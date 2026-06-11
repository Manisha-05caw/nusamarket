<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;

class StoreController extends Controller
{
    public function show(string $slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();
        $products = Product::where('store_id', $store->id)
            ->where('is_active', true)
            ->with('images')
            ->paginate(20);

        return view('pages.stores.show', compact('store', 'products'));
    }
}
