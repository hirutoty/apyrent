<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryController extends Controller
{
    public function index()
{
    $kendaraans = Kendaraan::withCount('rentals')

        // TOTAL PENDAPATAN RENTAL
        ->withSum('rentals', 'total_biaya')

        // TOTAL SERVICE
        ->withSum('serviceHistories', 'total_biaya')

        ->orderBy('rentals_count', 'desc')

        ->get();

    /*
    |--------------------------------------------------------------------------
    | TOTAL OMSET BERSIH
    |--------------------------------------------------------------------------
    */

    $totalOmset = $kendaraans->sum(function ($k) {

        $rental = $k->rentals_sum_total_biaya ?? 0;

        $service = $k->service_histories_sum_total_biaya ?? 0;

        return $rental - $service;
    });

    return view('admin.history.index', compact(
        'kendaraans',
        'totalOmset'
    ));
}
    
      public function show($id)
{
    $kendaraan = Kendaraan::with(['rentals' => function ($query) {
        $query->with('member')
              ->orderBy('created_at', 'desc'); // data terbaru di atas
    }])->findOrFail($id);

    $rentals = $kendaraan->rentals;

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