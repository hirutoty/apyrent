<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PajakHistory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PajakHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', 'semua');
        $tahun = $request->input('tahun', 'semua');

        $data = $this->filteredQuery($bulan, $tahun)->get();

        // daftar tahun untuk dropdown, diambil dari data yang ada
        $tahunList = PajakHistory::selectRaw('YEAR(jatuh_tempo) as tahun')
            ->whereNotNull('jatuh_tempo')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('admin.pajak_kendaraan.history', compact('data', 'bulan', 'tahun', 'tahunList'));
    }

    public function destroy($id)
    {
        $data = PajakHistory::findOrFail($id);
        $data->delete();

        return back()->with('success', 'Data history pajak berhasil dihapus!');
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->input('bulan', 'semua');
        $tahun = $request->input('tahun', 'semua');

        $data = $this->filteredQuery($bulan, $tahun)->get();

        $namaBulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $namaBulan = $bulan !== 'semua' ? ($namaBulanList[(int) $bulan] ?? $bulan) : 'Semua Bulan';
        $namaTahun = $tahun !== 'semua' ? $tahun : 'Semua Tahun';
        $setting = Setting::first();
        $pdf = Pdf::loadView('admin.pajak_kendaraan.history-pdf', compact('data', 'namaBulan', 'namaTahun', 'setting'))
            ->setPaper('a4', 'landscape');
            

        return $pdf->stream('history-pajak-' . $namaBulan . '-' . $namaTahun . '.pdf');
    }

    private function filteredQuery($bulan, $tahun)
    {
        $query = PajakHistory::with('kendaraan');

        if ($bulan !== 'semua') {
            $query->whereMonth('jatuh_tempo', $bulan);
        }

        if ($tahun !== 'semua') {
            $query->whereYear('jatuh_tempo', $tahun);
        }

        return $query->latest();
    }
}