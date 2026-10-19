<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keuangan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\KeuanganExport;
use App\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class KeuanganController extends Controller
{
    /**
     * LIST PENGELUARAN
     */
   public function index(Request $request)
  {
      // FIX MEDIUM #8: gunakan latest('id') agar ordering konsisten berdasarkan PK
      $query = Keuangan::with('user')->latest('id');
  
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
  
      // ── TOTAL ALL-TIME ─────────────────────────────────
      $totalPemasukan   = Keuangan::sum('pemasukan');
      $totalPengeluaran = Keuangan::sum('pengeluaran');
      $saldo            = $totalPemasukan - $totalPengeluaran;

      // ── TOTAL FILTERED (mengikuti filter aktif) ────────
      $filteredQuery = Keuangan::query();

      if ($request->filled('jenis')) {
          if ($request->jenis == 'Pemasukan') {
              $filteredQuery->where('pemasukan', '>', 0);
          } elseif ($request->jenis == 'Pengeluaran') {
              $filteredQuery->where('pengeluaran', '>', 0);
          }
      }
      if ($request->filled('hari'))   $filteredQuery->whereDay('tanggal', $request->hari);
      if ($request->filled('bulan'))  $filteredQuery->whereMonth('tanggal', $request->bulan);
      if ($request->filled('tahun'))  $filteredQuery->whereYear('tanggal', $request->tahun);
      if ($request->filled('search')) {
          $keyword = $request->search;
          $filteredQuery->where(function ($q) use ($keyword) {
              $q->where('kategori',   'like', "%$keyword%")
                ->orWhere('keterangan', 'like', "%$keyword%")
                ->orWhere('reference',  'like', "%$keyword%")
                ->orWhereHas('user', function ($u) use ($keyword) {
                    $u->where('name', 'like', "%$keyword%");
                });
          });
      }

      $filteredPemasukan   = (clone $filteredQuery)->sum('pemasukan');
      $filteredPengeluaran = (clone $filteredQuery)->sum('pengeluaran');
      $filteredSaldo       = $filteredPemasukan - $filteredPengeluaran;

      // Tandai apakah filter sedang aktif
      $hasFilter = $request->filled('jenis') || $request->filled('hari')
                || $request->filled('bulan') || $request->filled('tahun')
                || $request->filled('search');
  
      // ── AGING AP ──────────────────────────────────────
      $setting  = \App\Models\Setting::first();
      $reminderAp = $setting->satuan_reminder ?? 30;

      $query_ap = \App\Models\aging_aps::query();
      if ($request->filled('hari_ap'))  $query_ap->whereDay('jatuh_tempo', $request->hari_ap);
      if ($request->filled('bulan_ap')) $query_ap->whereMonth('jatuh_tempo', $request->bulan_ap);
      if ($request->filled('tahun_ap')) $query_ap->whereYear('jatuh_tempo', $request->tahun_ap);
      $dataAp = $query_ap->latest()->get();

      // ── AGING AR ──────────────────────────────────────
      $batasReminder = $setting?->batas_reminder ?? 7;
      $reminderAr = match ($setting?->satuan_reminder) {
          'hari'   => $batasReminder,
          'minggu' => $batasReminder * 7,
          'bulan'  => $batasReminder * 30,
          'tahun'  => $batasReminder * 365,
          default  => $batasReminder,
      };

      $query_ar = \App\Models\AgingAr::with(['member', 'invoice']);
      if ($request->filled('hari_ar'))  $query_ar->whereDay('jatuh_tempo', $request->hari_ar);
      if ($request->filled('bulan_ar')) $query_ar->whereMonth('jatuh_tempo', $request->bulan_ar);
      if ($request->filled('tahun_ar')) $query_ar->whereYear('jatuh_tempo', $request->tahun_ar);
      $dataAr = $query_ar->latest()->get();

      $q_reminder = \App\Models\AgingAr::with(['member', 'invoice'])->where('status', 'Belum Bayar');
      if ($request->filled('hari_ar'))  $q_reminder->whereDay('jatuh_tempo', $request->hari_ar);
      if ($request->filled('bulan_ar')) $q_reminder->whereMonth('jatuh_tempo', $request->bulan_ar);
      if ($request->filled('tahun_ar')) $q_reminder->whereYear('jatuh_tempo', $request->tahun_ar);
      $dataReminder = $q_reminder->latest()->get();

      $q_lunas = \App\Models\AgingAr::with(['member', 'invoice'])->where('status', 'Bayar');
      if ($request->filled('hari_ar'))  $q_lunas->whereDay('jatuh_tempo', $request->hari_ar);
      if ($request->filled('bulan_ar')) $q_lunas->whereMonth('jatuh_tempo', $request->bulan_ar);
      if ($request->filled('tahun_ar')) $q_lunas->whereYear('jatuh_tempo', $request->tahun_ar);
      $dataLunas = $q_lunas->latest()->get();
      // ────────────────────────────────────────
  
      return view('admin.keuangan.index', compact(
          'keuangans', 'totalPemasukan', 'totalPengeluaran', 'saldo',
          'filteredPemasukan', 'filteredPengeluaran', 'filteredSaldo', 'hasFilter',
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

        // P0 #3 FIX RACE CONDITION: wrap in transaction with row-level lock
        DB::transaction(function () use ($request) {
            // MEDIUM #8 FIX LATEST: gunakan lockForUpdate + orderBy id desc untuk mencegah race condition
            $lastSaldo = (float) DB::table('keuangans')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;

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

            // MEDIUM #11 KOLOM SUMBER: tambahkan 'sumber' => 'manual'
            Keuangan::create([
                'tanggal'      => now(),
                'reference'    => $reference,
                'user_id'      => auth()->id(),
                'divisi'       => auth()->user() ? ucfirst(auth()->user()->role) : 'Keuangan',
                'kategori'     => $request->kategori,
                'metode'       => $request->metode,
                'keterangan'   => $request->keterangan,
                'pemasukan'    => $pemasukan,
                'pengeluaran'  => $pengeluaran,
                'saldo'        => $saldoBaru,
                'sumber'       => 'manual',
            ]);
        });

        return back()->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    /**
     * HAPUS
     */
    public function destroy($id)
    {
        $keuangan = Keuangan::findOrFail($id);

        // SOFT GUARD: entri dengan sumber='auto' dibuat oleh sistem (STNK, GPS,
        // Asuransi, Service, HutangVendor, dsb.) dan TIDAK boleh dihapus secara
        // manual — hapus dari sumber asalnya agar saldo tetap konsisten.
        if ($keuangan->sumber === 'auto') {
            return back()->with('error',
                'Entri ini dibuat otomatis oleh sistem dan tidak dapat dihapus secara manual. '
                . 'Hapus atau batalkan dari sumber asalnya (Service / GPS / STNK / Asuransi / Hutang Vendor / dll).'
            );
        }

        $keuanganId = $keuangan->id;
        $keuangan->delete();

        // Recalculate saldo semua baris setelah baris yang dihapus
        PaymentsController::recalculateKeuanganSaldo($keuanganId);

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

    /**
     * Get detail cashflow untuk modal popup
     */
    public function getDetail($id)
    {
        $keuangan = Keuangan::findOrFail($id);
        
        $detail = [
            'id' => $keuangan->id,
            'tanggal' => $keuangan->tanggal->format('d M Y'),
            'reference' => $keuangan->reference,
            'kategori' => $keuangan->kategori,
            'keterangan' => $keuangan->keterangan,
            'nominal' => $keuangan->pengeluaran > 0 ? $keuangan->pengeluaran : $keuangan->pemasukan,
            'type' => $keuangan->pengeluaran > 0 ? 'pengeluaran' : 'pemasukan',
            'metode' => $keuangan->metode,
            'user' => $keuangan->user->nama ?? $keuangan->user->email ?? '-',
            'divisi' => $keuangan->divisi,
        ];
        
        // Load detail from source if available
        if ($keuangan->source_type && $keuangan->source_id) {
            $detail['source'] = $this->loadSourceDetail($keuangan->source_type, $keuangan->source_id);
        }
        
        return response()->json($detail);
    }
    
    /**
     * Load detail dari source record (service_part, service_incident, dll)
     */
    private function loadSourceDetail(string $sourceType, int $sourceId)
    {
        switch ($sourceType) {
            case 'service_part':
                $part = \App\Models\ServicePart::with(['kendaraan', 'serviceHistory', 'category', 'supplier'])->find($sourceId);
                if (!$part) return null;
                
                return [
                    'type' => 'Service Kendaraan',
                    'kendaraan' => $part->kendaraan->merk . ' - ' . $part->kendaraan->nopol,
                    'nama_part' => $part->nama_part,
                    'kategori' => $part->category->nama ?? '-',
                    'serial_number' => $part->serial_number ?? '-',
                    'tgl_pasang' => $part->tgl_pasang ? \Carbon\Carbon::parse($part->tgl_pasang)->format('d M Y') : '-',
                    'kilometer_pasang' => number_format($part->kilometer_pasang ?? 0, 0, ',', '.') . ' km',
                    'biaya' => number_format($part->biaya, 0, ',', '.'),
                    'kondisi' => $part->kondisi,
                    'supplier' => $part->supplier->nama ?? '-',
                    'nama_bank' => $part->nama_bank ?? '-',
                    'no_rekening' => $part->no_rekening ?? '-',
                    'nama_rekening' => $part->nama_rekening ?? '-',
                    'bukti' => $part->bukti_bayar ?? [],
                ];
                
            case 'service_asuransi_kejadian':
                $kejadian = \App\Models\ServiceAsuransiKejadian::with(['serviceAsuransi.kendaraan', 'serviceAsuransi.jenisAsuransi'])->find($sourceId);
                if (!$kejadian) return null;
                
                return [
                    'type' => 'Service Asuransi',
                    'kendaraan' => $kejadian->serviceAsuransi->kendaraan->merk . ' - ' . $kejadian->serviceAsuransi->kendaraan->nopol,
                    'nama_asuransi' => $kejadian->serviceAsuransi->nama_asuransi ?? '-',
                    'jenis_asuransi' => $kejadian->serviceAsuransi->jenisAsuransi->nama ?? '-',
                    'nama_kejadian' => $kejadian->nama_kejadian,
                    'biaya' => number_format($kejadian->biaya, 0, ',', '.'),
                    'tanggal_service' => $kejadian->serviceAsuransi->tanggal_service ? \Carbon\Carbon::parse($kejadian->serviceAsuransi->tanggal_service)->format('d M Y') : '-',
                    'periode' => ($kejadian->serviceAsuransi->periode_mulai ? \Carbon\Carbon::parse($kejadian->serviceAsuransi->periode_mulai)->format('d M Y') : '-') . ' - ' . ($kejadian->serviceAsuransi->periode_selesai ? \Carbon\Carbon::parse($kejadian->serviceAsuransi->periode_selesai)->format('d M Y') : '-'),
                    'lampiran' => $kejadian->lampiran ?? [],
                    'bukti' => $kejadian->bukti_bayar ?? [],
                ];
                
            case 'service_incident_part':
                $part = \App\Models\ServiceIncidentPart::with(['serviceIncident.kendaraan', 'category', 'supplier'])->find($sourceId);
                if (!$part) return null;
                
                return [
                    'type' => 'Service Insiden',
                    'kendaraan' => $part->serviceIncident->kendaraan->merk . ' - ' . $part->serviceIncident->kendaraan->nopol,
                    'tanggal_service' => $part->serviceIncident->tanggal_service ? \Carbon\Carbon::parse($part->serviceIncident->tanggal_service)->format('d M Y') : '-',
                    'keluhan' => $part->serviceIncident->keluhan ?? '-',
                    'nama_part' => $part->nama_part,
                    'kategori' => $part->category->nama ?? '-',
                    'biaya' => number_format($part->biaya, 0, ',', '.'),
                    'kondisi' => $part->kondisi,
                    'supplier' => $part->supplier->nama ?? '-',
                    'nama_bank' => $part->nama_bank ?? '-',
                    'no_rekening' => $part->no_rekening ?? '-',
                    'nama_rekening' => $part->nama_rekening ?? '-',
                    'lampiran' => $part->lampiran ?? [],
                    'bukti' => $part->bukti_bayar ?? [],
                ];
                
            case 'gps_kendaraan':
                $gps = \App\Models\GpsKendaraan::with(['kendaraan', 'gps'])->find($sourceId);
                if (!$gps) return null;
                
                return [
                    'type' => 'GPS',
                    'kendaraan' => $gps->kendaraan->merk . ' - ' . $gps->kendaraan->nopol,
                    'nama_gps' => $gps->gps->nama_gps ?? '-',
                    'type_gps' => $gps->type,
                    'tanggal_pasang' => $gps->tanggal_pasang ? \Carbon\Carbon::parse($gps->tanggal_pasang)->format('d M Y') : '-',
                    'tanggal_habis' => $gps->tanggal_habis ? \Carbon\Carbon::parse($gps->tanggal_habis)->format('d M Y') : '-',
                    'durasi_bulan' => $gps->durasi_bulan . ' bulan',
                    'biaya_sewa' => number_format($gps->biaya_sewa, 0, ',', '.'),
                    'status_sewa' => $gps->status_sewa,
                    'nama_bank' => $gps->nama_bank ?? '-',
                    'no_rekening' => $gps->no_rekening ?? '-',
                    'nama_pemilik' => $gps->nama_pemilik ?? '-',
                    'bukti' => $gps->bukti_bayar ? [$gps->bukti_bayar] : [],
                ];
                
            default:
                return null;
        }
    }
}
