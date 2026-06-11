<?php
// =============================================================
// app/Http/Middleware/SellerMiddleware.php
// =============================================================
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

        // Kalau seller tapi belum punya toko, redirect ke setup
        if ($user->role === 'seller' && !$user->stores()->exists()) {
            if (!$request->routeIs('seller.store.setup*')) {
                return redirect()->route('seller.store.setup')
                    ->with('info', 'Lengkapi profil toko kamu terlebih dahulu.');
            }
        }

        if (!in_array($user->role, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk penjual.');
        }

        return $next($request);
    }
}


// =============================================================
// app/Http/Middleware/AdminMiddleware.php
// =============================================================

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
