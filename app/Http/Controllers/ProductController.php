<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return app(HomeController::class)->index();
    }

    public function search()
    {
        return app(HomeController::class)->index();
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['store', 'images', 'variants' => fn($q) => $q->where('is_active', true)])
            ->firstOrFail();

        $reviews = $product->reviews()
            ->with(['buyer', 'images'])
            ->latest()
            ->paginate(10);

        $relatedProducts = Product::where('is_active', true)
            ->where('store_id', $product->store_id)
            ->where('id', '!=', $product->id)
            ->with(['images', 'store'])
            ->take(5)
            ->get();

        return view('pages.products.show', compact('product', 'reviews', 'relatedProducts'));
    }
}
