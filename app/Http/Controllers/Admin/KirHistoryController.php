<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KirHistory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KirHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', 'semua');
        $tahun = $request->input('tahun', 'semua');

        $data = $this->filteredQuery($bulan, $tahun)->paginate(15)->withQueryString();

        // daftar tahun untuk dropdown, diambil dari data yang ada
        $tahunList = KirHistory::selectRaw('YEAR(masa_berlaku) as tahun')
            ->whereNotNull('masa_berlaku')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('admin.kir.history', compact('data', 'bulan', 'tahun', 'tahunList'));
    }

    public function destroy($id)
    {
        $data = KirHistory::findOrFail($id);
        $data->delete();

        return back()->with('success', 'Data history KIR berhasil dihapus!');
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
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.kir.history-pdf', compact('data', 'namaBulan', 'namaTahun', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('history-kir-' . $namaBulan . '-' . $namaTahun . '.pdf');
    }

    private function filteredQuery($bulan, $tahun)
    {
        $query = KirHistory::with(['kendaraan', 'attachments']);

        if ($bulan !== 'semua') {
            $query->whereMonth('masa_berlaku', $bulan);
        }

        if ($tahun !== 'semua') {
            $query->whereYear('masa_berlaku', $tahun);
        }

        return $query->latest();
    }
}