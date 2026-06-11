<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('pages.profile.index', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:120',
            'phone' => 'nullable|string|max:20',
        ]);
        Auth::user()->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        Auth::user()->update(['password' => bcrypt($request->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }

    public function addresses()
    {
        $addresses = Auth::user()->addresses()->get();
        return view('pages.profile.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $data = $request->validate([
            'label'        => 'required|string|max:60',
            'recipient'    => 'required|string|max:120',
            'phone'        => 'required|string|max:20',
            'address_line' => 'required|string',
            'city'         => 'required|string|max:100',
            'province'     => 'required|string|max:100',
            'postal_code'  => 'required|string|max:10',
            'is_default'   => 'boolean',
        ]);

        if (!empty($data['is_default'])) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        Auth::user()->addresses()->create($data);
        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function updateAddress(Request $request, string $id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $address->update($request->validate([
            'label'        => 'required|string|max:60',
            'recipient'    => 'required|string|max:120',
            'phone'        => 'required|string|max:20',
            'address_line' => 'required|string',
            'city'         => 'required|string|max:100',
            'province'     => 'required|string|max:100',
            'postal_code'  => 'required|string|max:10',
        ]));
        return back()->with('success', 'Alamat diperbarui.');
    }

    public function destroyAddress(string $id)
    {
        Auth::user()->addresses()->findOrFail($id)->delete();
        return back()->with('success', 'Alamat dihapus.');
    }

    public function setDefault(string $id)
    {
        Auth::user()->addresses()->update(['is_default' => false]);
        Auth::user()->addresses()->findOrFail($id)->update(['is_default' => true]);
        return back()->with('success', 'Alamat utama diubah.');
    }
}
