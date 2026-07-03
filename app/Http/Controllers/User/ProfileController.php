<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('profil.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'no_telp'   => 'nullable|string|max:20',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'  => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'no_telp'   => $request->no_telp,
        ];

        if ($request->hasFile('foto')) {

    // hapus foto lama
    if ($user->foto && file_exists(public_path($user->foto))) {
        unlink(public_path($user->foto));
    }

    $file = $request->file('foto');

    $namaFile = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

    $tujuan = public_path('uploads/user');

    if (!file_exists($tujuan)) {
        mkdir($tujuan, 0755, true);
    }

    $file->move($tujuan, $namaFile);

    $data['foto'] = 'uploads/user/' . $namaFile;
}

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}