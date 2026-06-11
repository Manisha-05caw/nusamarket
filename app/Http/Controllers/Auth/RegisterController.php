<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('pages.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:120',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'phone'                 => 'nullable|string|max:20',
            'role'                  => 'required|in:buyer,seller',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'phone'    => $data['phone'] ?? null,
            'role'     => $data['role'],
        ]);

        $user->cart()->create();
        Auth::login($user);
        $request->session()->regenerate();

        if ($data['role'] === 'seller') {
            return redirect()->route('seller.store.setup')
                ->with('success', 'Akun berhasil dibuat! Setup toko kamu dulu.');
        }

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat!');
    }
}
