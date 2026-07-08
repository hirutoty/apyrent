<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PolicyCompliance;
use Illuminate\Http\Request;

class PolicyComplianceController extends Controller
{
    public function index()
    {
        $data           = PolicyCompliance::latest()->paginate(15)->withQueryString();
        $totalDokumen   = PolicyCompliance::count();
        $totalAktif     = PolicyCompliance::where('status', 'Aktif')->count();
        $totalDraft     = PolicyCompliance::where('status', 'Draft')->count();
        $totalReview    = PolicyCompliance::where('status', 'Review')->count();

        return view('admin.technology.policyc.index',
            compact('data','totalDokumen','totalAktif','totalDraft','totalReview'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_dokumen'        => 'required|string|max:255',
            'versi'               => 'required|string|max:20',
            'tanggal_berlaku'     => 'required|date',
            'tanggung_jawab'      => 'required|string|max:255',
            'status'              => 'required|string|max:50',
            'sertifikasi_terkait' => 'nullable|string|max:255',
        ]);

        PolicyCompliance::create($request->only([
            'nama_dokumen','versi','tanggal_berlaku','tanggung_jawab','status','sertifikasi_terkait'
        ]));

        return redirect()->route('policyc.index')->with('success', 'Policy & Compliance berhasil ditambahkan.');
    }

    public function update(Request $request, PolicyCompliance $policyc)
    {
        $request->validate([
            'nama_dokumen'        => 'required|string|max:255',
            'versi'               => 'required|string|max:20',
            'tanggal_berlaku'     => 'required|date',
            'tanggung_jawab'      => 'required|string|max:255',
            'status'              => 'required|string|max:50',
            'sertifikasi_terkait' => 'nullable|string|max:255',
        ]);

        $policyc->update($request->only([
            'nama_dokumen','versi','tanggal_berlaku','tanggung_jawab','status','sertifikasi_terkait'
        ]));

        return redirect()->route('policyc.index')->with('success', 'Policy & Compliance berhasil diperbarui.');
    }

    public function destroy(PolicyCompliance $policyc)
    {
        $policyc->delete();
        return redirect()->route('policyc.index')->with('success', 'Policy & Compliance berhasil dihapus.');
    }
}
