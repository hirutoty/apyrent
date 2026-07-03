<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\Request;

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
        $kendaraan = Kendaraan::with(['rentals.member'])->findOrFail($id);

        $rentals = $kendaraan->rentals;

        return view('admin.history.show', compact('kendaraan', 'rentals'));
    }
}