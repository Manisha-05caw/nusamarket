<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $store = Auth::user()->stores()->firstOrFail();
        $stats = [
            'total_orders'      => DB::table('order_items')->where('store_id',$store->id)->distinct('order_id')->count('order_id'),
            'pending_orders'    => DB::table('order_items')->where('store_id',$store->id)->where('item_status','pending')->count(),
            'total_products'    => $store->products()->count(),
            'active_products'   => $store->products()->where('is_active',true)->count(),
            'total_revenue'     => $store->balance?->total_earned ?? 0,
            'available_balance' => $store->balance?->available ?? 0,
            'rating_avg'        => $store->rating_avg,
            'total_reviews'     => $store->total_reviews,
        ];
        $salesChart  = collect();
        $topProducts = $store->products()->with('images')->orderByDesc('total_sold')->take(5)->get();
        $recentOrders = DB::table('order_items')
            ->where('order_items.store_id',$store->id)
            ->join('orders','order_items.order_id','=','orders.id')
            ->join('users','orders.buyer_id','=','users.id')
            ->select('orders.id','users.name as buyer_name','order_items.product_name','order_items.subtotal','order_items.item_status','orders.created_at')
            ->orderByDesc('orders.created_at')->take(10)->get();
        return view('pages.seller.dashboard', compact('store','stats','salesChart','topProducts','recentOrders'));
    }
}
