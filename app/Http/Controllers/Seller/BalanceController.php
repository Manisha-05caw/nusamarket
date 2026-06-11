<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    public function index()
    {
        $store        = Auth::user()->stores()->firstOrFail();
        $balance      = $store->balance;
        $transactions = BalanceTransaction::where('store_id',$store->id)->latest()->paginate(20);
        return view('pages.seller.balance', compact('store','balance','transactions'));
    }

    public function withdraw(Request $request)
    {
        $request->validate(['amount'=>'required|numeric|min:50000','bank_name'=>'required','account_number'=>'required','account_name'=>'required']);
        $store   = Auth::user()->stores()->firstOrFail();
        $balance = $store->balance;
        abort_if(!$balance||$balance->available<$request->amount,422,'Saldo tidak mencukupi.');
        $balance->decrement('available',$request->amount);
        return back()->with('success','Permintaan penarikan berhasil.');
    }
}
