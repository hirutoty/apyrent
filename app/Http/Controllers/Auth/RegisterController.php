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
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
            'no_telp'   => 'required|string|max:20',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $foto = null;

        if ($request->hasFile('foto')) {

            $file = $request->file('foto');

            $fileName = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads/user'), $fileName);

            // SIMPAN PATH KE DB
            $foto = 'uploads/user/' . $fileName;
        }


        $user = User::create([
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'no_telp'   => $validated['no_telp'],
            'foto'      => $foto,
        ]);

        // 🔥 LOGIN OTOMATIS
        Auth::login($user);

        return redirect('/admin/dashboard')
            ->with('success', 'Register berhasil, selamat datang!');
    }
}
