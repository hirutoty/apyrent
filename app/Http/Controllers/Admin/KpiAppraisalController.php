<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiAppraisal;
use Illuminate\Http\Request;

class KpiAppraisalController extends Controller
{
    public function index()
    {
        $data = KpiAppraisal::latest()->paginate(15)->withQueryString();

        $totalEvaluasi    = KpiAppraisal::count();
        $totalTerlampaui  = KpiAppraisal::where('status', 'Terlampaui')->count();
        $totalTercapai    = KpiAppraisal::where('status', 'Tercapai')->count();
        $totalBelum       = KpiAppraisal::where('status', 'Belum Tercapai')->count();
        $totalPegawai     = KpiAppraisal::distinct('nama_pegawai')->count('nama_pegawai');
        $rataNilai        = round(KpiAppraisal::avg('nilai_akhir') ?? 0, 2);
        $totalNilaiTinggi = KpiAppraisal::where('nilai_akhir', '>=', 80)->count();

        return view('admin.hrd.kpi.index', compact(
            'data', 'totalEvaluasi', 'totalPegawai', 'rataNilai', 'totalNilaiTinggi'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pegawai'     => 'required|string|max:255',
            'periode_evaluasi' => 'required|string|max:255',
            'disiplin'         => 'required|integer|min:0|max:100',
            'kolaborasi'       => 'required|integer|min:0|max:100',
            'produktivitas'    => 'required|integer|min:0|max:100',
            'nilai_akhir'      => 'required|numeric|min:0|max:100',
            'evaluator'        => 'required|string|max:255',
            'catatan'          => 'nullable|string',
        ]);

        KpiAppraisal::create($validated);

        return redirect()->route('kpi.index')
            ->with('success', 'Data KPI appraisal berhasil ditambahkan.');
    }

    public function update(Request $request, KpiAppraisal $kpi)
    {
        $validated = $request->validate([
            'nama_pegawai'     => 'required|string|max:255',
            'periode_evaluasi' => 'required|string|max:255',
            'disiplin'         => 'required|integer|min:0|max:100',
            'kolaborasi'       => 'required|integer|min:0|max:100',
            'produktivitas'    => 'required|integer|min:0|max:100',
            'nilai_akhir'      => 'required|numeric|min:0|max:100',
            'evaluator'        => 'required|string|max:255',
            'catatan'          => 'nullable|string',
        ]);

        $kpi->update($validated);

        return redirect()->route('kpi.index')
            ->with('success', 'Data KPI appraisal berhasil diperbarui.');
    }

    public function destroy(KpiAppraisal $kpi)
    {
        $kpi->delete();

        return redirect()->route('kpi.index')
            ->with('success', 'Data KPI appraisal berhasil dihapus.');
    }
}
