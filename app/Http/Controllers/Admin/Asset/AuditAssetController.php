<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\AuditAsset;
use Illuminate\Http\Request;

class AuditAssetController extends Controller
{
    public function index()
    {
        $data       = AuditAsset::latest()->paginate(15)->withQueryString();
        $total      = AuditAsset::count();
        $totalBaik  = AuditAsset::where('status_fisik', 'Baik')->count();
        $totalRusak = AuditAsset::where('status_fisik', 'Rusak')->count();
        $totalHilang= AuditAsset::where('status_fisik', 'Hilang')->count();

        return view('admin.asset.audit.index', compact(
            'data', 'total', 'totalBaik', 'totalRusak', 'totalHilang'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_aset'      => 'required|string|max:255',
            'tanggal_audit'  => 'required|date',
            'diperiksa_oleh' => 'required|string|max:255',
            'status_fisik'   => 'required|string|max:255',
            'temuan'         => 'required|string|max:255',
            'tindakan'       => 'nullable|string|max:255',
            'catatan'        => 'nullable|string',
        ]);

        AuditAsset::create($request->all());

        return redirect()->route('asset.audit.index')
            ->with('success', 'Data Audit Asset berhasil ditambahkan.');
    }

    public function update(Request $request, AuditAsset $audit)
    {
        $request->validate([
            'kode_aset'      => 'required|string|max:255',
            'tanggal_audit'  => 'required|date',
            'diperiksa_oleh' => 'required|string|max:255',
            'status_fisik'   => 'required|string|max:255',
            'temuan'         => 'required|string|max:255',
            'tindakan'       => 'nullable|string|max:255',
            'catatan'        => 'nullable|string',
        ]);

        $audit->update($request->all());

        return redirect()->route('asset.audit.index')
            ->with('success', 'Data Audit Asset berhasil diperbarui.');
    }

    public function destroy(AuditAsset $audit)
    {
        $audit->delete();

        return redirect()->route('asset.audit.index')
            ->with('success', 'Data Audit Asset berhasil dihapus.');
    }
}
