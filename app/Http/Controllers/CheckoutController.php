<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Auth::user()->cart()->with(['items.variant.product.store','items.variant.product.images'])->firstOrFail();
        if ($cart->items->isEmpty()) return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        $addresses = Auth::user()->addresses()->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
        return view('pages.checkout.index', compact('cart', 'addresses', 'defaultAddress'));
    }

    public function direct(Request $request)
    {
        $request->validate(['variant_id' => 'required|exists:product_variants,id', 'quantity' => 'required|integer|min:1']);
        session(['direct_buy' => ['variant_id' => $request->variant_id, 'quantity' => $request->quantity]]);
        return redirect()->route('checkout.index');
    }

    public function process(Request $request)
    {
        $request->validate([
            'address_id'     => 'required|exists:addresses,id',
            'courier'        => 'required|string',
            'courier_service'=> 'required|string',
            'payment_method' => 'required|in:bank_transfer,gopay,ovo,dana,qris,credit_card,debit_card',
        ]);
        $address = Auth::user()->addresses()->findOrFail($request->address_id);
        $cart    = Auth::user()->cart()->with('items.variant.product')->firstOrFail();
        $order   = null;
        DB::transaction(function() use ($request, $address, $cart, &$order) {
            $subtotal = $cart->total;
            $shipping = 15000;
            $fee      = round($subtotal * 0.02);
            $total    = $subtotal + $shipping + $fee;
            $order = Order::create([
                'buyer_id'       => Auth::id(),
                'status'         => 'pending_payment',
                'subtotal'       => $subtotal,
                'shipping_cost'  => $shipping,
                'platform_fee'   => $fee,
                'total_amount'   => $total,
                'shipping_address' => ['recipient'=>$address->recipient,'phone'=>$address->phone,'address_line'=>$address->address_line,'city'=>$address->city,'province'=>$address->province,'postal_code'=>$address->postal_code],
                'courier'        => $request->courier,
                'courier_service'=> $request->courier_service,
            ]);
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'variant_id'  => $item->variant_id,
                    'store_id'    => $item->variant->product->store_id,
                    'product_id'  => $item->variant->product_id,
                    'product_name'=> $item->variant->product->name,
                    'variant_info'=> ['size'=>$item->variant->size,'color'=>$item->variant->color],
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->variant->price,
                    'subtotal'    => $item->variant->price * $item->quantity,
                ]);
            }
            Payment::create(['order_id'=>$order->id,'method'=>$request->payment_method,'amount'=>$total,'status'=>'pending','expired_at'=>now()->addHours(24)]);
            $cart->items()->delete();
            session(['cart_count' => 0]);
        });
        return redirect()->route('checkout.payment', $order->id);
    }

    public function payment(string $orderId)
    {
        $order = Order::with(['items.product','payment'])->where('buyer_id', Auth::id())->findOrFail($orderId);
        $snapToken = null;
        $clientKey = config('services.midtrans.client_key');
        return view('pages.checkout.payment', compact('order', 'snapToken', 'clientKey'));
    }

    public function pay(Request $request, string $orderId)
    {
        $order = Order::where('buyer_id', Auth::id())->findOrFail($orderId);
        $order->payment()->update(['status'=>'paid','paid_at'=>now(),'gateway_ref'=>'INV-'.strtoupper(\Str::random(10))]);
        $order->update(['status'=>'paid','paid_at'=>now()]);
        return redirect()->route('checkout.success', $orderId);
    }

    public function success(string $orderId)
    {
        $order = Order::with(['items.product.images','payment'])->where('buyer_id', Auth::id())->findOrFail($orderId);
        return view('pages.checkout.success', compact('order'));
    }

    public function webhook(Request $request)
    {
        return response()->json(['message' => 'OK']);
    }
}
