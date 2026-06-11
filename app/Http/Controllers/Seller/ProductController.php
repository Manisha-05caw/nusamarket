<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $store = Auth::user()->stores()->firstOrFail();
        $products = $store->products()->with('images')
            ->when(request('q'), fn($q,$kw)=>$q->where('name','like',"%$kw%"))
            ->latest()->paginate(20);
        return view('pages.seller.products.index', compact('store','products'));
    }

    public function create() { return view('pages.seller.products.form', ['product'=>null]); }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $store = Auth::user()->stores()->firstOrFail();
        $product = $store->products()->create(array_merge($data, ['slug'=>Str::slug($data['name']).'-'.Str::random(5)]));
        $product->variants()->create(['price'=>$data['base_price'],'stock'=>$request->input('variants.0.stock',0),'is_active'=>true]);
        return redirect()->route('seller.products.index')->with('success','Produk ditambahkan!');
    }

    public function edit(string $id)
    {
        $store = Auth::user()->stores()->firstOrFail();
        $product = $store->products()->with(['variants','images'])->findOrFail($id);
        return view('pages.seller.products.form', compact('product'));
    }

    public function update(Request $request, string $id)
    {
        $store = Auth::user()->stores()->firstOrFail();
        $store->products()->findOrFail($id)->update($this->validate($request));
        return redirect()->route('seller.products.index')->with('success','Produk diperbarui!');
    }

    public function destroy(string $id)
    {
        Auth::user()->stores()->firstOrFail()->products()->findOrFail($id)->delete();
        return back()->with('success','Produk dihapus.');
    }

    public function toggle(string $id)
    {
        $product = Auth::user()->stores()->firstOrFail()->products()->findOrFail($id);
        $product->update(['is_active'=>!$product->is_active]);
        return back()->with('success','Status produk diubah.');
    }

    private function validate(Request $request): array
    {
        return $request->validate([
            'name'=>'required|string|max:255',
            'description'=>'required|string',
            'category'=>'required',
            'base_price'=>'required|numeric|min:100',
            'discount_percent'=>'nullable|integer|between:0,90',
            'weight_gram'=>'required|integer|min:1',
        ]);
    }
}
