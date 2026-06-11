<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Notification;
use App\Models\SellerBalance;
use App\Models\BalanceTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $status = request('status');
        $orders = Order::where('buyer_id', Auth::id())
            ->when($status, fn($q) => $q->where('status', $status))
            ->with(['items.product.images', 'payment'])
            ->latest()->paginate(10);
        return view('pages.orders.index', compact('orders', 'status'));
    }

    public function show(string $id)
    {
        $order = Order::with(['items.product.images','items.product.store','items.variant','items.review','payment','buyer'])
            ->where('buyer_id', Auth::id())->findOrFail($id);
        $trackingHistory = [];
        return view('pages.orders.show', compact('order', 'trackingHistory'));
    }

    public function cancel(string $id)
    {
        $order = Order::where('buyer_id', Auth::id())->findOrFail($id);
        abort_unless(in_array($order->status, ['pending_payment','paid']), 403);
        DB::transaction(function() use ($order) {
            $order->update(['status' => 'cancelled']);
            foreach ($order->items as $item) {
                $item->variant->increment('stock', $item->quantity);
            }
        });
        return redirect()->route('orders.index')->with('success', 'Pesanan dibatalkan.');
    }

    public function confirmReceived(string $id)
    {
        $order = Order::where('buyer_id', Auth::id())->findOrFail($id);
        abort_unless($order->status === 'delivered', 403);
        DB::transaction(function() use ($order) {
            $order->update(['status' => 'completed', 'completed_at' => now()]);
            $order->items()->update(['item_status' => 'delivered']);
            foreach ($order->items->groupBy('store_id') as $storeId => $items) {
                $amount  = $items->sum('subtotal');
                $balance = SellerBalance::firstOrCreate(['store_id' => $storeId], ['available'=>0,'pending'=>0,'total_earned'=>0]);
                $balance->increment('available', $amount);
                $balance->increment('total_earned', $amount);
            }
        });
        return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan dikonfirmasi selesai!');
    }
}
