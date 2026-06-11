<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Notification;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::query()
            ->when(request('q'),fn($q,$kw)=>$q->where('name','like',"%$kw%"))
            ->when(request('status'),fn($q,$s)=>$q->where('status',$s))
            ->with('owner')->withCount('products')->orderByDesc('created_at')->paginate(20);
        $counts = ['all'=>Store::count(),'active'=>Store::where('status','active')->count(),'pending_review'=>Store::where('status','pending_review')->count(),'suspended'=>Store::where('status','suspended')->count()];
        return view('pages.admin.stores', compact('stores','counts'));
    }

    public function approve(string $id)
    {
        $store = Store::findOrFail($id);
        $store->update(['status'=>'active']);
        return back()->with('success',"Toko {$store->name} disetujui.");
    }

    public function suspend(string $id)
    {
        $store = Store::findOrFail($id);
        $store->update(['status'=>'suspended']);
        return back()->with('success',"Toko {$store->name} ditangguhkan.");
    }
}
