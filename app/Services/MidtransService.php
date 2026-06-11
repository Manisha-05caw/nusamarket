<?php
// =============================================================
// app/Services/MidtransService.php
// =============================================================
namespace App\Services;

use App\Models\Order;
use App\Models\Payment;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool   $isProduction;
    protected string $snapUrl;
    protected string $apiUrl;

    public function __construct()
    {
        $this->serverKey    = config('services.midtrans.server_key');
        $this->clientKey    = config('services.midtrans.client_key');
        $this->isProduction = config('services.midtrans.is_production', false);
        $this->snapUrl      = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
        $this->apiUrl       = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Buat Snap Token untuk halaman pembayaran
     */
    public function createSnapToken(Order $order): array
    {
        $addr = $order->shipping_address;

        $payload = [
            'transaction_details' => [
                'order_id'     => $order->id,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->buyer->name,
                'email'      => $order->buyer->email,
                'phone'      => $order->buyer->phone ?? '',
                'shipping_address' => [
                    'first_name' => $addr['recipient'] ?? '',
                    'phone'      => $addr['phone'] ?? '',
                    'address'    => $addr['address_line'] ?? '',
                    'city'       => $addr['city'] ?? '',
                    'postal_code'=> $addr['postal_code'] ?? '',
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $this->buildItemDetails($order),
            'enabled_payments' => $this->getEnabledPayments($order->payment->method),
            'callbacks' => [
                'finish'  => route('checkout.success', $order->id),
                'error'   => route('checkout.payment', $order->id),
                'pending' => route('checkout.payment', $order->id),
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'hours',
                'duration'   => 24,
            ],
        ];

        $response = $this->post($this->snapUrl, $payload);

        if (isset($response['token'])) {
            $order->payment()->update([
                'gateway_ref'     => $order->id,
                'gateway_payload' => $response,
                'expired_at'      => now()->addHours(24),
            ]);
        }

        return $response;
    }

    /**
     * Cek status transaksi dari Midtrans
     */
    public function checkStatus(string $orderId): array
    {
        return $this->get("{$this->apiUrl}/{$orderId}/status");
    }

    /**
     * Verifikasi signature webhook Midtrans
     */
    public function verifySignature(array $payload): bool
    {
        $signature = hash('sha512',
            $payload['order_id'] .
            $payload['status_code'] .
            $payload['gross_amount'] .
            $this->serverKey
        );

        return $signature === ($payload['signature_key'] ?? '');
    }

    /**
     * Handle webhook notification dari Midtrans
     */
    public function handleNotification(array $payload): bool
    {
        if (!$this->verifySignature($payload)) {
            return false;
        }

        $payment = Payment::where('gateway_ref', $payload['order_id'])->first();
        if (!$payment) return false;

        $transactionStatus = $payload['transaction_status'];
        $fraudStatus       = $payload['fraud_status'] ?? null;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $this->markAsPaid($payment);
            }
        } elseif ($transactionStatus === 'settlement') {
            $this->markAsPaid($payment);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $this->markAsFailed($payment, $transactionStatus);
        } elseif ($transactionStatus === 'pending') {
            $payment->update(['status' => 'pending']);
        }

        return true;
    }

    // ─── Private helpers ────────────────────────────────────────

    private function markAsPaid(Payment $payment): void
    {
        $payment->update(['status' => 'paid', 'paid_at' => now()]);

        $order = $payment->order;
        $order->update(['status' => 'paid', 'paid_at' => now()]);

        // Notifikasi ke tiap seller
        foreach ($order->items->groupBy('store_id') as $storeId => $items) {
            $store = \App\Models\Store::find($storeId);
            \App\Models\Notification::create([
                'user_id' => $store->owner_id,
                'type'    => 'new_order',
                'title'   => 'Pesanan Baru! 🎉',
                'body'    => 'Pesanan baru senilai Rp ' . number_format($items->sum('subtotal'), 0, ',', '.') . ' telah masuk.',
                'data'    => ['order_id' => $order->id],
            ]);
        }

        // Notifikasi ke buyer
        \App\Models\Notification::create([
            'user_id' => $order->buyer_id,
            'type'    => 'payment_success',
            'title'   => 'Pembayaran Berhasil ✓',
            'body'    => 'Pembayaran pesanan #' . strtoupper(substr($order->id, 0, 8)) . ' berhasil dikonfirmasi.',
            'data'    => ['order_id' => $order->id],
        ]);
    }

    private function markAsFailed(Payment $payment, string $reason): void
    {
        $status = $reason === 'expire' ? 'expired' : 'failed';
        $payment->update(['status' => $status]);
        $payment->order->update(['status' => 'cancelled']);

        // Kembalikan stok
        foreach ($payment->order->items as $item) {
            $item->variant->increment('stock', $item->quantity);
        }
    }

    private function buildItemDetails(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id'       => $item->product_id,
                'price'    => (int) $item->unit_price,
                'quantity' => $item->quantity,
                'name'     => substr($item->product_name, 0, 50),
            ];
        }

        // Ongkos kirim
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id'       => 'SHIPPING',
                'price'    => (int) $order->shipping_cost,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim (' . $order->courier . ')',
            ];
        }

        // Biaya platform
        if ($order->platform_fee > 0) {
            $items[] = [
                'id'       => 'PLATFORM_FEE',
                'price'    => (int) $order->platform_fee,
                'quantity' => 1,
                'name'     => 'Biaya Layanan',
            ];
        }

        return $items;
    }

    private function getEnabledPayments(string $method): array
    {
        return match($method) {
            'gopay'       => ['gopay'],
            'ovo'         => ['other_qris'],
            'dana'        => ['other_qris'],
            'qris'        => ['other_qris', 'gopay'],
            'credit_card' => ['credit_card'],
            'debit_card'  => ['credit_card'],
            default       => ['bca_va', 'bni_va', 'bri_va', 'mandiri_bill', 'permata_va'],
        };
    }

    private function post(string $url, array $data): array
    {
        $response = \Http::withBasicAuth($this->serverKey, '')
            ->acceptJson()
            ->post($url, $data);

        return $response->json();
    }

    private function get(string $url): array
    {
        $response = \Http::withBasicAuth($this->serverKey, '')
            ->acceptJson()
            ->get($url);

        return $response->json();
    }
}
