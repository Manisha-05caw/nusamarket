<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Auth::user()->cart()->with([
            'items.variant.product.images',
            'items.variant.product.store',
        ])->firstOrCreate(['user_id' => Auth::id()]);

        $groupedItems = $cart->items->groupBy(fn($item) => $item->variant->product->store_id);

        return view('pages.checkout.cart', compact('cart', 'groupedItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        if ($variant->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart = Auth::user()->cart()->firstOrCreate(['user_id' => Auth::id()]);
        $item = $cart->items()->where('variant_id', $variant->id)->first();

        if ($item) {
            $newQty = $item->quantity + $request->quantity;
            if ($newQty > $variant->stock) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }
            $item->update(['quantity' => $newQty]);
        } else {
            $cart->items()->create([
                'variant_id' => $variant->id,
                'quantity'   => $request->quantity,
            ]);
        }

        session(['cart_count' => $cart->items()->sum('quantity')]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Ditambahkan ke keranjang.', 'cart_count' => session('cart_count')]);
        }

        return back()->with('success', 'Produk ditambahkan ke keranjang!');
    }

    public function update(Request $request, string $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $item = CartItem::findOrFail($itemId);
        abort_unless($item->cart->user_id === Auth::id(), 403);

        if ($request->quantity > $item->variant->stock) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $item->update(['quantity' => $request->quantity]);
        session(['cart_count' => Auth::user()->cart->items()->sum('quantity')]);
        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(string $itemId)
    {
        $item = CartItem::findOrFail($itemId);
        abort_unless($item->cart->user_id === Auth::id(), 403);
        $item->delete();
        session(['cart_count' => Auth::user()->cart->items()->sum('quantity')]);
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear()
    {
        Auth::user()->cart?->items()->delete();
        session(['cart_count' => 0]);
        return back()->with('success', 'Keranjang dikosongkan.');
    }
}
