<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// =============================================================
// WishlistController.php — pindahkan ke file terpisah
// =============================================================

class WishlistController extends Controller
{
    public function index()
    {
        $items = Auth::user()->wishlists()
            ->with(['product.images', 'product.store'])
            ->latest()
            ->paginate(20);
        return view('pages.wishlist.index', compact('items'));
    }

    public function toggle(string $productId)
    {
        $existing = Auth::user()->wishlists()->where('product_id', $productId)->first();
        if ($existing) {
            $existing->delete();
            $wishlisted = false;
        } else {
            Auth::user()->wishlists()->create(['product_id' => $productId]);
            $wishlisted = true;
        }
        return response()->json(['wishlisted' => $wishlisted]);
    }
}
