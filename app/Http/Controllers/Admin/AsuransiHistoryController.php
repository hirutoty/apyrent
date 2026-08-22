<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsuransiHistory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AsuransiHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bulan  = $request->input('bulan', 'semua');
        $tahun  = $request->input('tahun', 'semua');
        $search = $request->input('search');

        $data = $this->filteredQuery($bulan, $tahun, $search)->paginate(15)->withQueryString();

        // daftar tahun untuk dropdown, diambil dari data yang ada
        $tahunList = AsuransiHistory::selectRaw('YEAR(tgl_mulai) as tahun')
            ->whereNotNull('tgl_mulai')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('admin.asuransi.history', compact('data', 'bulan', 'tahun', 'tahunList'));
    }

    public function kendaraan($id)
    {
        $kendaraan = \App\Models\Kendaraan::with(['jenis'])->findOrFail($id);

        $history = AsuransiHistory::with(['kendaraan', 'asuransi', 'jenisAsuransi', 'attachments'])
            ->where('kendaraan_id', $id)
            ->latest('diperpanjang_pada')
            ->paginate(20);

        $totalData  = AsuransiHistory::where('kendaraan_id', $id)->count();
        $totalBiaya = AsuransiHistory::where('kendaraan_id', $id)->sum('biaya');
        $lastHistory = AsuransiHistory::where('kendaraan_id', $id)
            ->latest('diperpanjang_pada')
            ->first();
        $avgBiaya = $totalData > 0 ? $totalBiaya / $totalData : 0;

        return view('admin.kendaraan.asuransi_history', compact(
            'kendaraan',
            'history',
            'totalData',
            'totalBiaya',
            'lastHistory',
            'avgBiaya'
        ));
    }

    public function destroy($id)
    {
        $data = AsuransiHistory::findOrFail($id);
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
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.asuransi.history-pdf', compact('data', 'namaBulan', 'namaTahun', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('history-asuransi-' . $namaBulan . '-' . $namaTahun . '.pdf');
    }

    private function filteredQuery($bulan, $tahun, $search = null)
    {
        $query = AsuransiHistory::with(['kendaraan', 'asuransi', 'jenisAsuransi', 'attachments']);

        if ($bulan !== 'semua') {
            $query->whereMonth('tgl_mulai', $bulan);
        }

        if ($tahun !== 'semua') {
            $query->whereYear('tgl_mulai', $tahun);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('kendaraan', fn($k) =>
                        $k->where('nopol', 'like', "%{$search}%")
                          ->orWhere('merk', 'like', "%{$search}%")
                  )
                  ->orWhereHas('asuransi', fn($a) =>
                        $a->where('nama_asuransi', 'like', "%{$search}%")
                  );
            });
        }

        return $query->latest();
    }
}