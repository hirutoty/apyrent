<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use Illuminate\Http\Request;

class UserAccessController extends Controller
{
    public function index()
    {
        $data         = UserAccess::latest()->paginate(15)->withQueryString();
        $totalAkses   = UserAccess::count();
        $totalAktif   = UserAccess::where('status_akses', 'Aktif')->count();
        $totalTidak   = UserAccess::where('status_akses', 'Nonaktif')->count();
        $totalSuspend = UserAccess::where('status_akses', 'Suspended')->count();

        return view('admin.technology.useraccess.index',
            compact('data','totalAkses','totalAktif','totalTidak','totalSuspend'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pengguna' => 'required|string|max:255',
            'divisi'        => 'required|string|max:100',
            'role_akses'    => 'required|string|max:100',
            'sistem'        => 'required|string|max:255',
            'status_akses'  => 'required|string|max:50',
            'catatan'       => 'nullable|string',
        ]);

        UserAccess::create($request->only([
            'nama_pengguna','divisi','role_akses','sistem','status_akses','catatan'
        ]));

        return redirect()->route('useraccess.index')->with('success', 'User Access berhasil ditambahkan.');
    }

    public function update(Request $request, UserAccess $useraccess)
    {
        $request->validate([
            'nama_pengguna' => 'required|string|max:255',
            'divisi'        => 'required|string|max:100',
            'role_akses'    => 'required|string|max:100',
            'sistem'        => 'required|string|max:255',
            'status_akses'  => 'required|string|max:50',
            'catatan'       => 'nullable|string',
        ]);

        $useraccess->update($request->only([
            'nama_pengguna','divisi','role_akses','sistem','status_akses','catatan'
        ]));

        return redirect()->route('useraccess.index')->with('success', 'User Access berhasil diperbarui.');
    }

    public function destroy(UserAccess $useraccess)
    {
        $useraccess->delete();
        return redirect()->route('useraccess.index')->with('success', 'User Access berhasil dihapus.');
    }
}
