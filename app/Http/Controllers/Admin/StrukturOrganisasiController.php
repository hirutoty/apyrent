<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;

class StrukturOrganisasiController extends Controller
{
    public function index()
    {
        $data = StrukturOrganisasi::latest()->paginate(15)->withQueryString();

        $totalPegawai    = StrukturOrganisasi::count();
        $totalTetap      = StrukturOrganisasi::where('status_jabatan', 'Tetap')->count();
        $totalKontrak    = StrukturOrganisasi::where('status_jabatan', 'Kontrak')->count();
        $totalDepartemen = StrukturOrganisasi::distinct('departemen')->count('departemen');

        return view('admin.hrd.struktur.index', compact(
            'data', 'totalPegawai', 'totalTetap', 'totalKontrak', 'totalDepartemen'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jabatan'    => 'required|string|max:255',
            'nama_pegawai'    => 'required|string|max:255',
            'nip_id'          => 'required|string|max:255',
            'departemen'      => 'required|string|max:255',
            'atasan_langsung' => 'nullable|string|max:255',
            'lokasi'          => 'required|string|max:255',
            'status_jabatan'  => 'required|in:Tetap,Kontrak',
            'tanggal_mulai'   => 'required|date',
        ]);

        StrukturOrganisasi::create($validated);

        return redirect()->route('struktur.index')
            ->with('success', 'Data struktur organisasi berhasil ditambahkan.');
    }

    public function update(Request $request, StrukturOrganisasi $struktur)
    {
        $validated = $request->validate([
            'nama_jabatan'    => 'required|string|max:255',
            'nama_pegawai'    => 'required|string|max:255',
            'nip_id'          => 'required|string|max:255',
            'departemen'      => 'required|string|max:255',
            'atasan_langsung' => 'nullable|string|max:255',
            'lokasi'          => 'required|string|max:255',
            'status_jabatan'  => 'required|in:Tetap,Kontrak',
            'tanggal_mulai'   => 'required|date',
        ]);

        $struktur->update($validated);

        return redirect()->route('struktur.index')
            ->with('success', 'Data struktur organisasi berhasil diperbarui.');
    }

    public function destroy(StrukturOrganisasi $struktur)
    {
        $struktur->delete();

        return redirect()->route('struktur.index')
            ->with('success', 'Data struktur organisasi berhasil dihapus.');
    }
}
