<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemBackup;
use Illuminate\Http\Request;

class SystemBackupController extends Controller
{
    public function index()
    {
        $data          = SystemBackup::latest()->paginate(15)->withQueryString();
        $totalBackup   = SystemBackup::count();
        $totalAktif    = SystemBackup::where('status_backup', 'Aktif')->count();
        $totalGagal    = SystemBackup::where('status_backup', 'Gagal')->count();
        $totalNonaktif = SystemBackup::where('status_backup', 'Nonaktif')->count();

        return view('admin.technology.systemb.index',
            compact('data','totalBackup','totalAktif','totalGagal','totalNonaktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sistem'                 => 'required|string|max:255',
            'metode_backup'          => 'required|string|max:100',
            'frekuensi'              => 'required|string|max:100',
            'lokasi_backup'          => 'required|string|max:255',
            'status_backup'          => 'required|string|max:50',
            'uji_restore_terakhir'   => 'nullable|date',
        ]);

        SystemBackup::create($request->only([
            'sistem','metode_backup','frekuensi','lokasi_backup','status_backup','uji_restore_terakhir'
        ]));

        return redirect()->route('systemb.index')->with('success', 'System Backup berhasil ditambahkan.');
    }

    public function update(Request $request, SystemBackup $systemb)
    {
        $request->validate([
            'sistem'                 => 'required|string|max:255',
            'metode_backup'          => 'required|string|max:100',
            'frekuensi'              => 'required|string|max:100',
            'lokasi_backup'          => 'required|string|max:255',
            'status_backup'          => 'required|string|max:50',
            'uji_restore_terakhir'   => 'nullable|date',
        ]);

        $systemb->update($request->only([
            'sistem','metode_backup','frekuensi','lokasi_backup','status_backup','uji_restore_terakhir'
        ]));

        return redirect()->route('systemb.index')->with('success', 'System Backup berhasil diperbarui.');
    }

    public function destroy(SystemBackup $systemb)
    {
        $systemb->delete();
        return redirect()->route('systemb.index')->with('success', 'System Backup berhasil dihapus.');
    }
}
