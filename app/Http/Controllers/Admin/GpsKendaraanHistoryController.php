<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GpsKendaraanHistory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class GpsKendaraanHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = GpsKendaraanHistory::with(['kendaraan', 'gps'])
            ->latest('diperpanjang_pada');

        if ($request->filled('bulan')) {
            $query->whereMonth('diperpanjang_pada', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('diperpanjang_pada', $request->tahun);
        }

        $data        = $query->get();
        $totalBiaya  = $data->sum('biaya_sewa');

        // Build year options from existing data (± current year)
        $tahunList = GpsKendaraanHistory::selectRaw('YEAR(diperpanjang_pada) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');
        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        return view('admin.gps.gps_kendaraan_history', compact('data', 'totalBiaya', 'tahunList', 'setting', 'logoSrc'));
    }

    public function exportPdf(Request $request)
    {
        $query = GpsKendaraanHistory::with(['kendaraan', 'gps']);

        if ($request->filled('bulan')) {
            $query->whereMonth('diperpanjang_pada', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('diperpanjang_pada', $request->tahun);
        }

        $data = $query->orderBy('diperpanjang_pada', 'desc')->get();

        $totalBiaya = $data->sum('biaya_sewa');
        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.gps.history_pdf', compact('data', 'totalBiaya', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        $filename = 'history-gps-kendaraan-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->stream($filename);
    }

    public function destroy($id)
    {
        $data = GpsKendaraanHistory::findOrFail($id);
        $data->delete();

        return back()->with('success', 'Data history GPS berhasil dihapus.');
    }
}
