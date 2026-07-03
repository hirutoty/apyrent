<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\StnkHistory;
use Barryvdh\DomPDF\Facade\Pdf;

class StnkHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan; // format dari <input type="month">: YYYY-MM

        $data = StnkHistory::with('kendaraan')
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(diperpanjang_pada, '%Y-%m') = ?", [$bulan]))
            ->latest('diperpanjang_pada')
            ->get();

        return view('admin.stnk.history', [
            'data'  => $data,
            'bulan' => $bulan,
        ]);
    }

    public function destroy($id)
    {
        $history = StnkHistory::findOrFail($id);

        // Hapus file bukti jika ada
        if ($history->bukti && file_exists(public_path($history->bukti))) {
            unlink(public_path($history->bukti));
        }

        // Hapus data
        $history->delete();

        return back()->with('success', 'Riwayat STNK berhasil dihapus.');
    }



    public function exportPdf(Request $request)
    {
        $bulan = $request->bulan;

        $query = StnkHistory::with('kendaraan');

        if ($bulan) {
            $query->whereRaw(
                "DATE_FORMAT(diperpanjang_pada, '%Y-%m') = ?",
                [$bulan]
            );
        }

        $data = $query->latest('diperpanjang_pada')->get();
        $setting = Setting::first();

        $pdf = Pdf::loadView('admin.stnk.history_pdf', [
            'data' => $data,
            'bulan' => $bulan,
            'setting' => $setting,
        ]);

        return $pdf->stream('History-STNK.pdf');
    }
}
