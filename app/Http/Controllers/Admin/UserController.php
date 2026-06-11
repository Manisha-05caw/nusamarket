<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->when(request('q'),fn($q,$kw)=>$q->where('name','like',"%$kw%")->orWhere('email','like',"%$kw%"))
            ->when(request('role'),fn($q,$r)=>$q->where('role',$r))
            ->withCount(['orders','stores'])->latest()->paginate(20);
        return view('pages.admin.users', compact('users'));
    }

    public function show(string $id)
    {
        $user   = User::withCount(['orders','stores','reviews'])->findOrFail($id);
        $orders = $user->orders()->with('items')->latest()->take(10)->get();
        $stores = $user->stores()->withCount('products')->get();
        return view('pages.admin.users', compact('user','orders','stores'));
    }

    public function toggle(string $id)
    {
        $user = User::findOrFail($id);
        abort_if($user->role==='admin',403);
        $user->update(['is_active'=>!$user->is_active]);
        return back()->with('success',"Akun {$user->name} diperbarui.");
    }
}
