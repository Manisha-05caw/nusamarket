<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;

class HomeController extends Controller
{
    public function index()
    {
        $banners = [
            ['image' => 'https://picsum.photos/seed/banner1/1200/400', 'title' => 'Promo Spesial Hari Ini', 'subtitle' => 'Diskon hingga 70% produk pilihan', 'cta' => 'Belanja Sekarang', 'url' => '/products'],
            ['image' => 'https://picsum.photos/seed/banner2/1200/400', 'title' => 'Batik Nusantara Terbaik', 'subtitle' => 'Koleksi eksklusif langsung dari pengrajin', 'cta' => 'Lihat Koleksi', 'url' => '/products?category=fashion_wanita'],
            ['image' => 'https://picsum.photos/seed/banner3/1200/400', 'title' => 'Gadget & Elektronik Murah', 'subtitle' => 'Produk original bergaransi resmi', 'cta' => 'Cek Produk', 'url' => '/products?category=elektronik'],
        ];

        $flashSaleProducts = Product::where('is_active', true)
            ->where('discount_percent', '>', 0)
            ->with(['images', 'store'])
            ->orderByDesc('total_sold')
            ->take(10)
            ->get()
            ->map(function ($p) {
                $p->sale_price   = $p->display_price;
                $p->sale_percent = min(round(($p->total_sold / max($p->total_sold + 20, 1)) * 100), 95);
                return $p;
            });

        $query = Product::where('is_active', true)->with(['images', 'store']);

        if (request('category'))  $query->where('category', request('category'));
        if (request('q'))         $query->where('name', 'like', '%' . request('q') . '%');
        if (request('min_price')) $query->where('base_price', '>=', request('min_price'));
        if (request('max_price')) $query->where('base_price', '<=', request('max_price'));
        if (request('rating'))    $query->where('rating_avg', '>=', request('rating'));

        $sortCol = match(request('sort', 'sold')) {
            'newest'     => 'created_at',
            'price_asc'  => 'base_price',
            'price_desc' => 'base_price',
            'rating'     => 'rating_avg',
            default      => 'total_sold',
        };
        $sortDir = request('sort') === 'price_asc' ? 'asc' : 'desc';

        $products         = $query->orderBy($sortCol, $sortDir)->paginate(20);
        $provinces        = Store::where('status', 'active')->distinct()->pluck('province')->filter()->sort()->values();
        $flashSaleSeconds = now()->secondsUntilEndOfDay();

        return view('pages.home.index', compact(
            'banners', 'flashSaleProducts', 'products', 'provinces', 'flashSaleSeconds'
        ));
    }

    public function flashSale()
    {
        $products = Product::where('is_active', true)
            ->where('discount_percent', '>', 0)
            ->with(['images', 'store'])
            ->orderByDesc('discount_percent')
            ->paginate(24);

        return view('pages.home.flash-sale', compact('products'));
    }
}
