<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Rental;
use App\Models\User;
use App\Models\Keuangan;
use App\Models\ServiceHistory;
use App\Models\GpsKendaraan;
use App\Models\PajakKendaraan;
use App\Models\AsuransiKendaraan;
use App\Models\Kir;
use App\Models\Stnk;


class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $hari  = $request->hari;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $keuanganQuery = Keuangan::query();

        if ($tahun) {
            $keuanganQuery->whereYear('created_at', $tahun);
        }

        if ($bulan) {
            $keuanganQuery->whereMonth('created_at', $bulan);
        }

        if ($hari) {
            $keuanganQuery->whereDay('created_at', $hari);
        }
        /* =====================
        | MASTER DATA
        ======================*/
        $totalKendaraan = Kendaraan::count();
        $kendaraanDisewa = Kendaraan::where('status_kendaraan', 'disewa')->count();

        $totalUser = User::count();

        $totalGps = GpsKendaraan::query()
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($hari, fn($q) => $q->whereDay('created_at', $hari))
            ->count();

        $gpsAktif = GpsKendaraan::query()
            ->where('status_gps', 'aktif')
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($hari, fn($q) => $q->whereDay('created_at', $hari))
            ->count();

        $totalGps = GpsKendaraan::query()
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($hari, fn($q) => $q->whereDay('created_at', $hari))
            ->count();

        $totalAsuransi = AsuransiKendaraan::query()
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($hari, fn($q) => $q->whereDay('created_at', $hari))
            ->count();

        $gpsAktif = GpsKendaraan::query()
            ->where('status_gps', 'aktif')
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($hari, fn($q) => $q->whereDay('created_at', $hari))
            ->count();
        $totalKir = Kir::query()
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($hari, fn($q) => $q->whereDay('created_at', $hari))
            ->count();

        $totalPajak = PajakKendaraan::query()
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($hari, fn($q) => $q->whereDay('created_at', $hari))
            ->count();
        $totalRental = Rental::query()
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($hari, fn($q) => $q->whereDay('created_at', $hari))
            ->count();
        $totalService = ServiceHistory::query()
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($hari, fn($q) => $q->whereDay('created_at', $hari))
            ->count();

        /* =====================
        | KEUANGAN
        ======================*/
        $pemasukanBulanIni = (clone $keuanganQuery)->sum('pemasukan');

        $pengeluaranBulanIni = (clone $keuanganQuery)->sum('pengeluaran');

        $profitBulanIni = $pemasukanBulanIni - $pengeluaranBulanIni;

        /* =====================
        | RENTAL
        ======================*/
        $rentalQuery = Rental::with('kendaraan', 'member');

        if ($tahun) {
            $rentalQuery->whereYear('created_at', $tahun);
        }

        if ($bulan) {
            $rentalQuery->whereMonth('created_at', $bulan);
        }

        if ($hari) {
            $rentalQuery->whereDay('created_at', $hari);
        }

        $rentalTerbaru = $rentalQuery
            ->latest()
            ->take(5)
            ->get();

        /* =====================
        | SERVICE
        ======================*/
        $serviceBulanIni = ServiceHistory::whereMonth('created_at', now()->month)->count();

        /* =====================
| REMINDER STNK
======================*/
        $stnkHampirHabis = Stnk::with('kendaraan')
            ->whereDate('masa_berlaku', '<=', now()->addDays(30))
            ->get();

        /* =====================
| REMINDER GPS
======================*/
        $gpsHampirHabis = GpsKendaraan::with('kendaraan')
            ->whereDate('tanggal_habis', '<=', now()->addDays(30))
            ->get();

        /* =====================
        | REMINDER PAJAK
        ======================*/
        $pajakHampirHabis = PajakKendaraan::with('kendaraan')
            ->where('status', 'belum_bayar')
            ->whereDate('jatuh_tempo', '<=', now()->addDays(7))
            ->get();

        /* =====================
        | REMINDER ASURANSI
        ======================*/
        $asuransiHampirHabis = AsuransiKendaraan::with('kendaraan')
            ->whereDate('tgl_berakhir', '<=', now()->addDays(30))
            ->get();

        /* =====================
        | REMINDER KIR
        ======================*/
        $kirHampirHabis = Kir::with('kendaraan')
            ->whereDate('masa_berlaku', '<=', now()->addDays(30))
            ->get();

        /* =====================
        | GRAFIK PEMASUKAN
        ======================*/
        $chartPemasukan = [];

        $chartPemasukan = [];

        $tahunGrafik = $tahun ?: now()->year;

        for ($i = 1; $i <= 12; $i++) {

            $chartPemasukan[] = Keuangan::whereYear('created_at', $tahunGrafik)
                ->whereMonth('created_at', $i)
                ->sum('pemasukan');
        }

        return view('admin.dashboard.index', compact(
            /* master */
            'totalKendaraan',
            'kendaraanDisewa',
            'totalUser',

            /* gps */
            'totalGps',
            'gpsAktif',

            /* asuransi & kir */
            'totalAsuransi',
            'totalKir',

            /* pajak & rental */
            'totalPajak',
            'totalRental',

            /* service */
            'totalService',
            'serviceBulanIni',

            /* keuangan */
            'pemasukanBulanIni',
            'pengeluaranBulanIni',
            'profitBulanIni',

            /* data */
            'rentalTerbaru',

            /* reminder */
            'pajakHampirHabis',
            'asuransiHampirHabis',
            'kirHampirHabis',
            'stnkHampirHabis',   // baru
            'gpsHampirHabis',    // baru

            /* chart */
            'chartPemasukan'
        ));
    }
}
