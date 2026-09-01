<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::withCount('rentals')
            ->withSum('rentals', 'total_biaya')
            ->withSum('serviceHistories', 'total_biaya')
            ->orderBy('rentals_count', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('merk', 'like', "%{$s}%")
                  ->orWhere('nopol', 'like', "%{$s}%");
            });
        }

        $kendaraans = $query->paginate(15)->withQueryString();

        $totalOmset = $kendaraans->getCollection()->sum(function ($k) {
            return ($k->rentals_sum_total_biaya ?? 0) - ($k->service_histories_sum_total_biaya ?? 0);
        });

        return view('admin.history.index', compact('kendaraans', 'totalOmset'));
    }
    
    public function show(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $query = $kendaraan->rentals()->with('member')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('member', function ($q) use ($s) {
                $q->where('nama_pelanggan', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rentals = $query->paginate(15)->withQueryString();

        return view('admin.history.show', compact('kendaraan', 'rentals'));
    }

     public function exportPdf($id)
{
    $kendaraan = Kendaraan::with(['rentals.member'])->findOrFail($id);

    $rentals = $kendaraan->rentals;

    $totalRental     = $rentals->count();
    $totalPendapatan = $rentals->sum('total_biaya');

    $setting = Setting::first(); // sesuaikan cara ambil setting perusahaan kamu

    $pdf = Pdf::loadView('admin.history.export_pdf', compact(
        'kendaraan',
        'rentals',
        'totalRental',
        'totalPendapatan',
        'setting'
    ))->setPaper('a4', 'landscape');

    return $pdf->stream('history-rental-' . $kendaraan->nopol . '.pdf');
}


}