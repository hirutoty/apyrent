<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keuangan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\KeuanganExport;
use App\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;

class KeuanganController extends Controller
{
    /**
     * LIST PENGELUARAN
     */
   public function index(Request $request)
  {
      $query = Keuangan::with('user')->latest();
  
      if ($request->filled('jenis')) {
          if ($request->jenis == 'Pemasukan') {
              $query->where('pemasukan', '>', 0);
          } elseif ($request->jenis == 'Pengeluaran') {
              $query->where('pengeluaran', '>', 0);
          }
      }
  
      if ($request->filled('hari')) $query->whereDay('tanggal', $request->hari);
      if ($request->filled('bulan')) $query->whereMonth('tanggal', $request->bulan);
      if ($request->filled('tahun')) $query->whereYear('tanggal', $request->tahun);
  
      if ($request->filled('search')) {
          $keyword = $request->search;
          $query->where(function ($q) use ($keyword) {
              $q->where('kategori', 'like', "%$keyword%")
                  ->orWhere('keterangan', 'like', "%$keyword%")
                  ->orWhere('reference', 'like', "%$keyword%")
                  ->orWhereHas('user', function ($u) use ($keyword) {
                      $u->where('name', 'like', "%$keyword%");
                  });
          });
      }
  
      $keuangans = $query->paginate(15)->withQueryString();
  
      $totalPemasukan  = Keuangan::sum('pemasukan');
      $totalPengeluaran = Keuangan::sum('pengeluaran');
      $saldo = $totalPemasukan - $totalPengeluaran;
  
      // ── AGING AP ──────────────────────────────────────
      $dataAp   = \App\Models\Aging_aps::latest()->get();
      $setting  = \App\Models\Setting::first();
      $reminderAp = $setting->satuan_reminder ?? 30;
  
      // ── AGING AR ──────────────────────────────────────
      $dataAr = \App\Models\AgingAr::with(['member', 'invoice'])->latest()->get();
      $batasReminder = $setting?->batas_reminder ?? 7;
      $reminderAr = match ($setting?->satuan_reminder) {
          'hari'   => $batasReminder,
          'minggu' => $batasReminder * 7,
          'bulan'  => $batasReminder * 30,
          'tahun'  => $batasReminder * 365,
          default  => $batasReminder,
      };
   $dataReminder = \App\Models\AgingAr::with(['member', 'invoice'])
          ->where('status', 'Belum Bayar')
          ->latest()
          ->get();
  
      $dataLunas = \App\Models\AgingAr::with(['member', 'invoice'])
          ->where('status', 'Bayar')
          ->latest()
          ->get();
      // ────────────────────────────────────────
  
      return view('admin.keuangan.index', compact(
          'keuangans', 'totalPemasukan', 'totalPengeluaran', 'saldo',
          'dataAp', 'reminderAp',
          'dataAr', 'reminderAr',
          'setting',
          'dataReminder',  
          'dataLunas'      
      ));
  }

    /**
     * TAMBAH PENGELUARAN
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:Pemasukan,Pengeluaran',
            'kategori' => 'required|string|max:255',
            'metode' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;

        $pemasukan = 0;
        $pengeluaran = 0;

        if ($request->jenis == 'Pemasukan') {
            $pemasukan = $request->nominal;
            $saldoBaru = $lastSaldo + $pemasukan;
            $reference = 'INC-' . time();
        } else {
            $pengeluaran = $request->nominal;
            $saldoBaru = $lastSaldo - $pengeluaran;
            $reference = 'EXP-' . time();
        }

        Keuangan::create([
            'tanggal'      => now(),
            'reference'    => $reference,
            'user_id'      => auth()->id(),
            'kategori'     => $request->kategori,
            'metode'       => $request->metode,
            'keterangan'   => $request->keterangan,
            'pemasukan'    => $pemasukan,
            'pengeluaran'  => $pengeluaran,
            'saldo'        => $saldoBaru,
        ]);

        return back()->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    /**
     * HAPUS
     */
    public function destroy($id)
    {
        Keuangan::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        $query = Keuangan::with('user')->orderBy('tanggal');

        // Filter Jenis
if ($request->filled('jenis')) {
    if ($request->jenis == 'Pemasukan') {
        $query->where('pemasukan', '>', 0);
    } elseif ($request->jenis == 'Pengeluaran') {
        $query->where('pengeluaran', '>', 0);
    }
}

        // Filter hari
        if ($request->filled('hari')) {
            $query->whereDay('tanggal', $request->hari);
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Filter search (kategori / keterangan / reference / nama user)
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('kategori',   'like', "%{$keyword}%")
                    ->orWhere('keterangan', 'like', "%{$keyword}%")
                    ->orWhere('reference', 'like', "%{$keyword}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$keyword}%"));
            });
        }

        $keuangans = $query->get();

        // Kirim info filter aktif ke view PDF (opsional, untuk ditampilkan di header PDF)
        $filterInfo = [
            'hari'   => $request->hari,
            'bulan'  => $request->bulan,
            'tahun'  => $request->tahun,
            'jenis'  => $request->jenis,
            'search' => $request->search,
        ];
        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.keuangan.pdf', compact('keuangans', 'filterInfo', 'setting', 'logoSrc'));
        return $pdf->stream('laporan-keuangan.pdf');
    }



    public function exportExcel(Request $request)
    {
        $filename = 'keuangan_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new KeuanganExport(
                $request->hari,
                $request->bulan,
                $request->tahun,
                $request->jenis,
                $request->search
            ),
            $filename
        );
    }
}
