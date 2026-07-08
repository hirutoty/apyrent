<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index()
    {
        $data = Departemen::latest()->get();

        $totalDepartemen = $data->count();
        $totalAktif      = $data->where('status_aktif', 'Aktif')->count();
        $totalNonAktif   = $data->where('status_aktif', 'Non-Aktif')->count();
        $totalPosisi     = $data->sum('jumlah_posisi');

        return view('admin.hrd.departemen.index', compact(
            'data', 'totalDepartemen', 'totalAktif', 'totalNonAktif', 'totalPosisi'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_departemen'   => 'required|string|max:255',
            'kepala_departemen' => 'required|string|max:255',
            'tanggal_dibentuk'  => 'required|date',
            'jumlah_posisi'     => 'required|integer|min:1',
            'keterangan'        => 'nullable|string',
            'status_aktif'      => 'required|in:Aktif,Non-Aktif',
        ]);

        Departemen::create($validated);

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Departemen $departemen)
    {
        $validated = $request->validate([
            'nama_departemen'   => 'required|string|max:255',
            'kepala_departemen' => 'required|string|max:255',
            'tanggal_dibentuk'  => 'required|date',
            'jumlah_posisi'     => 'required|integer|min:1',
            'keterangan'        => 'nullable|string',
            'status_aktif'      => 'required|in:Aktif,Non-Aktif',
        ]);

        $departemen->update($validated);

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Departemen $departemen)
    {
        $departemen->delete();

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}
