<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function index()
    {
        $data = User::latest()->get();

        return view('admin.user.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',

            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',

            'password' => 'required|min:6',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'role' => 'required|in:superadmin,keuangan,produksi',
            'status' => 'required',
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan, silakan pakai yang lain',

            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar, gunakan email lain',

            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_telp' => $request->no_telp,
            'foto' => $request->hasFile('foto')
                ? $request->file('foto')->store('user', 'public')
                : null,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return back()->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',

            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,

            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'role' => 'required|in:superadmin,keuangan,produksi',
            'status' => 'required',
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan user lain',

            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan user lain',
        ]);

        $foto = $user->foto;

        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $foto = $request->file('foto')->store('user', 'public');
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'foto' => $foto,
            'role' => $request->role,
            'status' => $request->status,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }



    public function exportPdf(Request $request)
    {
        $query = User::query();

        // 🔎 FILTER SEARCH
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('no_telp', 'like', "%$search%")
                    ->orWhere('role', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            });
        }

        $data = $query->latest()->get();
        $setting = Setting::first();

        $filter = $request->search ?? 'Semua Data';

        $pdf = Pdf::loadView('admin.user.pdf', [
            'data' => $data,
            'filter' => $filter,
            'setting' => $setting,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('data-user.pdf');
    }
}
