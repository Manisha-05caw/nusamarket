<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!in_array($user->role, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk penjual.');
        }

        if ($user->role === 'seller' && !$user->stores()->exists()) {
            if (!$request->routeIs('seller.store.setup*')) {
                return redirect()->route('seller.store.setup')
                    ->with('info', 'Lengkapi profil toko kamu terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
