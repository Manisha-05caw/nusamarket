<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'    => DB::table('users')->count(),
            'total_sellers'  => DB::table('users')->where('role','seller')->count(),
            'total_buyers'   => DB::table('users')->where('role','buyer')->count(),
            'total_stores'   => DB::table('stores')->count(),
            'active_stores'  => DB::table('stores')->where('status','active')->count(),
            'pending_stores' => DB::table('stores')->where('status','pending_review')->count(),
            'total_products' => DB::table('products')->count(),
            'total_orders'   => DB::table('orders')->count(),
            'gmv'            => DB::table('orders')->where('status','completed')->sum('total_amount'),
            'revenue'        => DB::table('orders')->where('status','completed')->sum('platform_fee'),
            'pending_orders' => DB::table('orders')->where('status','pending_payment')->count(),
        ];
        $gmvChart     = collect();
        $pendingStores = DB::table('stores')->join('users','stores.owner_id','=','users.id')->where('stores.status','pending_review')->select('stores.*','users.name as owner_name','users.email as owner_email')->latest('stores.created_at')->take(5)->get();
        $recentOrders  = DB::table('orders')->join('users','orders.buyer_id','=','users.id')->select('orders.*','users.name as buyer_name')->orderByDesc('orders.created_at')->take(10)->get();
        return view('pages.admin.dashboard', compact('stats','gmvChart','pendingStores','recentOrders'));
    }
}
