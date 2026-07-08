<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResignOffboarding;
use Illuminate\Http\Request;

class ResignOffboardingController extends Controller
{
    public function index()
    {
        $data = ResignOffboarding::latest()->get();

        $totalResign      = $data->count();
        $totalSelesai     = $data->where('status_offboarding', 'Selesai')->count();
        $totalProses      = $data->where('status_offboarding', 'Proses')->count();
        $totalSerahTerima = $data->where('serah_terima', 'Sudah')->count();

        return view('admin.hrd.resign.index', compact(
            'data', 'totalResign', 'totalSelesai', 'totalProses', 'totalSerahTerima'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pegawai'       => 'required|string|max:255',
            'jabatan_terakhir'   => 'required|string|max:255',
            'tanggal_resign'     => 'required|date',
            'alasan'             => 'required|string|max:500',
            'status_offboarding' => 'required|in:Proses,Selesai',
            'serah_terima'       => 'required|in:Sudah,Belum',
            'keterangan'         => 'nullable|string',
        ]);

        ResignOffboarding::create($validated);

        return redirect()->route('resign.index')
            ->with('success', 'Data resign/offboarding berhasil ditambahkan.');
    }

    public function update(Request $request, ResignOffboarding $resign)
    {
        $validated = $request->validate([
            'nama_pegawai'       => 'required|string|max:255',
            'jabatan_terakhir'   => 'required|string|max:255',
            'tanggal_resign'     => 'required|date',
            'alasan'             => 'required|string|max:500',
            'status_offboarding' => 'required|in:Proses,Selesai',
            'serah_terima'       => 'required|in:Sudah,Belum',
            'keterangan'         => 'nullable|string',
        ]);

        $resign->update($validated);

        return redirect()->route('resign.index')
            ->with('success', 'Data resign/offboarding berhasil diperbarui.');
    }

    public function destroy(ResignOffboarding $resign)
    {
        $resign->delete();

        return redirect()->route('resign.index')
            ->with('success', 'Data resign/offboarding berhasil dihapus.');
    }
}
