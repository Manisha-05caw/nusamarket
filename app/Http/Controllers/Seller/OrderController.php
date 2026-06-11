<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $store  = Auth::user()->stores()->firstOrFail();
        $status = request('status','pending');
        $items  = OrderItem::where('store_id',$store->id)
            ->when($status!=='all',fn($q)=>$q->where('item_status',$status))
            ->with(['order.buyer','product.images','variant'])
            ->orderByDesc('created_at')->paginate(15);
        return view('pages.seller.orders.index', compact('store','items','status'));
    }

    public function show(string $id)
    {
        $store = Auth::user()->stores()->firstOrFail();
        $item  = OrderItem::where('store_id',$store->id)->with(['order.buyer','product','variant','review'])->findOrFail($id);
        return view('pages.seller.orders.show', compact('item'));
    }

    public function process(string $id)
    {
        $store = Auth::user()->stores()->firstOrFail();
        OrderItem::where('store_id',$store->id)->where('item_status','pending')->findOrFail($id)->update(['item_status'=>'processing']);
        return back()->with('success','Pesanan diproses.');
    }

    public function ship(Request $request, string $id)
    {
        $request->validate(['tracking_number'=>'required|string|max:100']);
        $store = Auth::user()->stores()->firstOrFail();
        $item  = OrderItem::where('store_id',$store->id)->where('item_status','processing')->findOrFail($id);
        $item->update(['item_status'=>'shipped']);
        $item->order->update(['status'=>'shipped','tracking_number'=>$request->tracking_number]);
        return back()->with('success','Pesanan dikirim!');
    }
}
