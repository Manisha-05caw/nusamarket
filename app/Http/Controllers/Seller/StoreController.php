<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function setup()    { return view('pages.seller.store.setup'); }

    public function storeSetup(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:120','description'=>'nullable|string','city'=>'nullable|string','province'=>'nullable|string']);
        Auth::user()->stores()->create(array_merge($data, ['slug' => Str::slug($data['name']).'-'.Str::random(5), 'status'=>'pending_review']));
        return redirect()->route('seller.dashboard')->with('success', 'Toko berhasil dibuat!');
    }

    public function edit()
    {
        $store = Auth::user()->stores()->firstOrFail();
        return view('pages.seller.store.edit', compact('store'));
    }

    public function update(Request $request)
    {
        $store = Auth::user()->stores()->firstOrFail();
        $store->update($request->validate(['name'=>'required','description'=>'nullable','city'=>'nullable','province'=>'nullable']));
        return back()->with('success', 'Toko diperbarui.');
    }
}
