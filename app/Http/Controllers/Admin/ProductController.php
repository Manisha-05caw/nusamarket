<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->when(request('q'),fn($q,$kw)=>$q->where('name','like',"%$kw%"))
            ->when(request('category'),fn($q,$c)=>$q->where('category',$c))
            ->with(['store','images'])->orderByDesc('created_at')->paginate(20);
        return view('pages.admin.products', compact('products'));
    }

    public function toggle(string $id)
    {
        $p = Product::findOrFail($id);
        $p->update(['is_active'=>!$p->is_active]);
        return back()->with('success','Status produk diubah.');
    }

    public function destroy(string $id)
    {
        Product::findOrFail($id)->delete();
        return back()->with('success','Produk dihapus.');
    }
}
