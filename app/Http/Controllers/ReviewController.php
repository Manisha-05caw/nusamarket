<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create(string $orderId, string $itemId)
    {
        $order = Order::where('buyer_id', Auth::id())->findOrFail($orderId);
        $item  = $order->items()->with(['product.images','review'])->findOrFail($itemId);
        if ($item->hasReview()) return redirect()->route('orders.show', $orderId)->with('error', 'Sudah diulas.');
        return view('pages.reviews.create', compact('order', 'item'));
    }

    public function store(Request $request, string $orderId, string $itemId)
    {
        $request->validate([
            'rating_product'  => 'required|integer|between:1,5',
            'rating_delivery' => 'required|integer|between:1,5',
            'rating_service'  => 'required|integer|between:1,5',
            'comment'         => 'nullable|string|max:1000',
        ]);
        $order = Order::where('buyer_id', Auth::id())->findOrFail($orderId);
        $item  = $order->items()->findOrFail($itemId);
        Review::create([
            'order_item_id'   => $item->id,
            'buyer_id'        => Auth::id(),
            'product_id'      => $item->product_id,
            'store_id'        => $item->store_id,
            'rating_product'  => $request->rating_product,
            'rating_delivery' => $request->rating_delivery,
            'rating_service'  => $request->rating_service,
            'comment'         => $request->comment,
        ]);
        return redirect()->route('orders.show', $orderId)->with('success', 'Ulasan berhasil dikirim!');
    }
}
