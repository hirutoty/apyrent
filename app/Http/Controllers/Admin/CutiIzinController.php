<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CutiIzin;
use Illuminate\Http\Request;

class CutiIzinController extends Controller
{
    public function index()
    {
        $data = CutiIzin::latest()->get();

        $totalPengajuan = $data->count();
        $totalDisetujui = $data->where('status', 'Disetujui')->count();
        $totalPending   = $data->where('status', 'Pending')->count();
        $totalDitolak   = $data->where('status', 'Ditolak')->count();

        return view('admin.hrd.cuti.index', compact(
            'data', 'totalPengajuan', 'totalDisetujui', 'totalPending', 'totalDitolak'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pegawai'    => 'required|string|max:255',
            'jenis_cuti_izin' => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'lama_hari'       => 'required|integer|min:1',
            'alasan'          => 'required|string',
            'status'          => 'required|in:Pending,Disetujui,Ditolak',
        ]);

        CutiIzin::create($validated);

        return redirect()->route('cuti.index')
            ->with('success', 'Pengajuan cuti/izin berhasil ditambahkan.');
    }

    public function update(Request $request, CutiIzin $cuti)
    {
        $validated = $request->validate([
            'nama_pegawai'    => 'required|string|max:255',
            'jenis_cuti_izin' => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'lama_hari'       => 'required|integer|min:1',
            'alasan'          => 'required|string',
            'status'          => 'required|in:Pending,Disetujui,Ditolak',
        ]);

        $cuti->update($validated);

        return redirect()->route('cuti.index')
            ->with('success', 'Pengajuan cuti/izin berhasil diperbarui.');
    }

    public function destroy(CutiIzin $cuti)
    {
        $cuti->delete();

        return redirect()->route('cuti.index')
            ->with('success', 'Pengajuan cuti/izin berhasil dihapus.');
    }
}
