<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\PenanggungJawab;
use Illuminate\Http\Request;

class PenanggungJawabController extends Controller
{
    public function index()
    {
        $data        = PenanggungJawab::latest()->paginate(15)->withQueryString();
        $total       = PenanggungJawab::count();
        $totalAktif  = PenanggungJawab::where('status', 'Aktif')->count();
        $totalNonaktif= PenanggungJawab::where('status', 'Nonaktif')->count();

        return view('admin.asset.pj.index', compact(
            'data', 'total', 'totalAktif', 'totalNonaktif'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_aset'          => 'required|string|max:255',
            'nama_aset'          => 'required|string|max:255',
            'pic'                => 'required|string|max:255',
            'tanggal_penempatan' => 'required|date',
            'divisi'             => 'required|string|max:255',
            'status'             => 'required|string|max:255',
        ]);

        PenanggungJawab::create($request->all());

        return redirect()->route('asset.pj.index')
            ->with('success', 'Data Penanggung Jawab berhasil ditambahkan.');
    }

    public function update(Request $request, PenanggungJawab $pj)
    {
        $request->validate([
            'kode_aset'          => 'required|string|max:255',
            'nama_aset'          => 'required|string|max:255',
            'pic'                => 'required|string|max:255',
            'tanggal_penempatan' => 'required|date',
            'divisi'             => 'required|string|max:255',
            'status'             => 'required|string|max:255',
        ]);

        $pj->update($request->all());

        return redirect()->route('asset.pj.index')
            ->with('success', 'Data Penanggung Jawab berhasil diperbarui.');
    }

    public function destroy(PenanggungJawab $pj)
    {
        $pj->delete();

        return redirect()->route('asset.pj.index')
            ->with('success', 'Data Penanggung Jawab berhasil dihapus.');
    }
}
