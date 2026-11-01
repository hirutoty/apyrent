<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * FORM REGISTER
     */
    public function index()
    {
        return view('register');
    }

    /**
     * PROSES REGISTER
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:users,username',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6|confirmed',
            'no_telp'    => 'required|string|max:20',
            'role'       => 'required|in:keuangan,produksi,hrd,purchase,sales,marketing,it', // WAJIB PILIH ROLE
            'foto'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload foto menggunakan Storage (sama seperti UserController)
        $foto = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('user', 'public');
        }

        $user = User::create([
            'name'       => $validated['name'],
            'username'   => $validated['username'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'no_telp'    => $validated['no_telp'],
            'foto'       => $foto,
            'role'       => $validated['role'], // Role yang dipilih user
            'status'     => 'regis',           // Status default: menunggu approval
        ]);

        // ❌ TIDAK ADA AUTO LOGIN LAGI - User harus menunggu approval admin

        return redirect('/login')
            ->with('success', 'Register berhasil! Akun Anda menunggu persetujuan dari admin. Terima kasih.');
    }
}
