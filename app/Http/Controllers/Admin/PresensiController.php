<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index()
    {
        $data = Presensi::latest()->get();

        $totalPresensi  = $data->count();
        $totalHadir     = $data->where('status', 'Hadir')->count();
        $totalAlpa      = $data->where('status', 'Alpa')->count();
        $totalIzin      = $data->where('status', 'Izin')->count();
        $totalTerlambat = $data->where('status', 'Terlambat')->count();

        return view('admin.hrd.presensi.index', compact(
            'data', 'totalPresensi', 'totalHadir', 'totalAlpa', 'totalIzin', 'totalTerlambat'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pegawai'    => 'required|string|max:255',
            'tanggal'         => 'required|date',
            'jam_masuk'       => 'required',
            'jam_pulang'      => 'required',
            'metode_presensi' => 'required|string|max:255',
            'lokasi_presensi' => 'required|string|max:255',
            'status'          => 'required|in:Hadir,Alpa,Izin,Terlambat',
        ]);

        Presensi::create($validated);

        return redirect()->route('presensi.index')
            ->with('success', 'Data presensi berhasil ditambahkan.');
    }

    public function update(Request $request, Presensi $presensi)
    {
        $validated = $request->validate([
            'nama_pegawai'    => 'required|string|max:255',
            'tanggal'         => 'required|date',
            'jam_masuk'       => 'required',
            'jam_pulang'      => 'required',
            'metode_presensi' => 'required|string|max:255',
            'lokasi_presensi' => 'required|string|max:255',
            'status'          => 'required|in:Hadir,Alpa,Izin,Terlambat',
        ]);

        $presensi->update($validated);

        return redirect()->route('presensi.index')
            ->with('success', 'Data presensi berhasil diperbarui.');
    }

    public function destroy(Presensi $presensi)
    {
        $presensi->delete();

        return redirect()->route('presensi.index')
            ->with('success', 'Data presensi berhasil dihapus.');
    }
}
