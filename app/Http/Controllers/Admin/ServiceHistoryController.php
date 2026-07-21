<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceHistory;
use App\Models\ServiceDetail;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\Setting;
use App\Models\Attachment;
use App\Models\ReminderService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bulan  = $request->bulan ?? now()->format('Y-m');
        $search = $request->search;

        $data = ServiceHistory::with(['kendaraan', 'attachments'])
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service,'%Y-%m') = ?", [$bulan]))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('keluhan', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('kilometer', 'like', "%{$search}%")
                        ->orWhereHas(
                            'kendaraan',
                            fn($k) =>
                            $k->where('merk', 'like', "%{$search}%")
                                ->orWhere('nopol', 'like', "%{$search}%")
                        );
                });
            })
            ->latest()
            ->paginate(15)->withQueryString();

        // Semua kendaraan untuk dropdown form tambah service history
        // (bukan hanya yang Tidak Layak — kendaraan normal pun bisa di-service)
        $kendaraan = Kendaraan::whereNotIn('status_kendaraan', ['disewa'])
            ->orderBy('merk')
            ->get();

        // ID kendaraan yang punya service_detail Tidak Layak (untuk data akumulasi panel)
        $kendaraanIdsTidakLayak = ServiceDetail::where('status', 'Tidak Layak')
            ->pluck('kendaraan_id')
            ->unique();

        // Data akumulasi per kendaraan dari service_detail status 'Tidak Layak'
        $detailPerKendaraan = ServiceDetail::where('status', 'Tidak Layak')
            ->whereIn('kendaraan_id', $kendaraanIdsTidakLayak)
            ->orderBy('tanggal_service', 'desc')
            ->get()
            ->groupBy('kendaraan_id')
            ->map(function ($items) {
                return [
                    // Gabungan semua keluhan
                    'keluhan_gabungan' => $items
                        ->pluck('keterangan')
                        ->filter()
                        ->implode(', '),
                    // Total biaya akumulasi
                    'total_biaya' => $items->sum('biaya'),
                    // Kilometer dari record terbaru
                    'kilometer' => $items->first()->kilometer ?? 0,
                    // Rincian per item untuk ditampilkan
                    'rincian' => $items->map(fn($d) => [
                        'keluhan' => $d->keterangan ?? '-',
                        'biaya'   => $d->biaya ?? 0,
                        'tanggal' => $d->tanggal_service ?? '-',
                    ])->values()->toArray(),
                ];
            });

        return view('admin.service.service_history', [
            'data'               => $data,
            'kendaraan'          => $kendaraan,
            'bulan'              => $bulan,
            'detailPerKendaraan' => $detailPerKendaraan,
        ]);
    }

    /**
     * Helper: simpan banyak attachment sekaligus untuk 1 service history
     * (konsisten dengan Pajak, Asuransi, GPS, KIR)
     */
    private function simpanAttachments($files, $serviceId)
    {
        $pathDir = public_path('service/attachments');
        if (!file_exists($pathDir)) mkdir($pathDir, 0777, true);

        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // ambil data SEBELUM file dipindah
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $size         = $file->getSize();

            $file->move($pathDir, $filename);

            Attachment::create([
                'relation_type' => 'service',
                'relation_id'   => $serviceId,
                'file_name'     => $originalName,
                'file_path'     => 'service/attachments/' . $filename,
                'file_type'     => $extension,
                'file_size'     => $size,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'       => 'required|exists:kendaraan,id',
            'tanggal_service'    => 'required|date',
            'kilometer'          => 'required|integer|min:0',
            'total_biaya'        => 'required|numeric|min:0',
            'bukti_pembayaran'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        // Cek: kendaraan tidak boleh sedang disewa
        if ($kendaraan->status_kendaraan === 'disewa') {
            return back()->withErrors([
                'kendaraan_id' => 'Kendaraan sedang disewa, tidak bisa ditambahkan ke service.',
            ])->withInput();
        }

        // Cek: tidak boleh ada service proses aktif untuk kendaraan ini
        $serviceAktif = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->where('status', 'proses')
            ->exists();
        if ($serviceAktif) {
            return back()->withErrors([
                'kendaraan_id' => 'Kendaraan ini masih memiliki service yang sedang berjalan (proses). Selesaikan dulu sebelum tambah service baru.',
            ])->withInput();
        }

        // Cek: kilometer harus >= km_terakhir_service kendaraan
        $kmTerakhir = $kendaraan->km_terakhir_service ?? 0;
        if ((int) $request->kilometer < $kmTerakhir) {
            return back()->withErrors([
                'kilometer' => "Kilometer tidak valid. Harus lebih besar atau sama dengan km terakhir service ({$kmTerakhir} km).",
            ])->withInput();
        }
        $limitBulanan      = $kendaraan->limit_biaya_bulanan_service ?? 0;
        $limitTahunan      = $kendaraan->limit_biaya_tahunan_service ?? 0;
        $bulan             = date('Y-m', strtotime($request->tanggal_service));
        $tahun             = date('Y', strtotime($request->tanggal_service));

        // Hitung total biaya service bulan ini (exclude record baru)
        $totalBulanIni = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan])
            ->sum('total_biaya');

        // Hitung total biaya service tahun ini (exclude record baru)
        $totalTahunIni = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->whereYear('tanggal_service', $tahun)
            ->sum('total_biaya');

        // Sisa limit bulanan: negatif artinya sudah over
        $sisaLimit = $limitBulanan - ($totalBulanIni + $request->total_biaya);

        // Total biaya tahunan termasuk record baru
        $biayaTahunan = $totalTahunIni + $request->total_biaya;

        // maks_bulanan dari kendaraan
        $maksBulanan = $limitBulanan;

        // Status overservice: cek bulanan dulu, lalu tahunan
        $overBulanan       = $limitBulanan > 0 && ($totalBulanIni + $request->total_biaya) > $limitBulanan;
        $overTahunan       = $limitTahunan > 0 && $biayaTahunan > $limitTahunan;
        $statusPengeluaran = ($overBulanan || $overTahunan) ? 'overservice' : 'stabil';

        // Siapkan metadata file (move dilakukan di dalam transaction)
        $buktiBayarMeta    = null;
        $attachmentsMeta   = [];

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $buktiBayarMeta = [
                'file'        => $file,
                'filename'    => time() . '_' . $file->getClientOriginalName(),
                'destination' => public_path('bukti_pembayaran'),
                'path'        => 'bukti_pembayaran/' . time() . '_' . $file->getClientOriginalName(),
            ];
        }

        if ($request->hasFile('bukti_attachment')) {
            $pathDir = public_path('service/attachments');
            foreach ($request->file('bukti_attachment') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $attachmentsMeta[] = [
                    'file'          => $file,
                    'filename'      => $filename,
                    'destination'   => $pathDir,
                    'file_name'     => $file->getClientOriginalName(),
                    'file_path'     => 'service/attachments/' . $filename,
                    'file_type'     => $file->getClientOriginalExtension(),
                    'file_size'     => $file->getSize(),
                ];
            }
        }

        $movedFiles = []; // track file yang sudah di-move untuk rollback

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $request, $sisaLimit, $maksBulanan, $biayaTahunan, $statusPengeluaran,
                $buktiBayarMeta, $attachmentsMeta, $kendaraan, &$movedFiles
            ) {
                // ── Move bukti pembayaran di dalam transaction ──
                $buktiBayar = null;
                if ($buktiBayarMeta) {
                    if (!file_exists($buktiBayarMeta['destination'])) {
                        mkdir($buktiBayarMeta['destination'], 0777, true);
                    }
                    $buktiBayarMeta['file']->move($buktiBayarMeta['destination'], $buktiBayarMeta['filename']);
                    $buktiBayar = $buktiBayarMeta['path'];
                    $movedFiles[] = public_path($buktiBayar);
                }

                $service = ServiceHistory::create([
                    'kendaraan_id'       => $request->kendaraan_id,
                    'keluhan'            => $request->keluhan,
                    'kilometer'          => $request->kilometer,
                    'total_biaya'        => $request->total_biaya,
                    'status'             => $request->status,
                    'tanggal_service'    => $request->tanggal_service,
                    'sisa_limit'         => $sisaLimit,
                    'maks_bulanan'       => $maksBulanan,
                    'biaya_tahunan'      => $biayaTahunan,
                    'status_pengeluaran' => $statusPengeluaran,
                    'bukti_pembayaran'   => $buktiBayar,
                ]);

                // ── Move attachments di dalam transaction ──
                if (!empty($attachmentsMeta)) {
                    if (!file_exists($attachmentsMeta[0]['destination'])) {
                        mkdir($attachmentsMeta[0]['destination'], 0777, true);
                    }
                    foreach ($attachmentsMeta as $att) {
                        $att['file']->move($att['destination'], $att['filename']);
                        $movedFiles[] = public_path($att['file_path']);

                        Attachment::create([
                            'relation_type' => 'service',
                            'relation_id'   => $service->id,
                            'file_name'     => $att['file_name'],
                            'file_path'     => $att['file_path'],
                            'file_type'     => $att['file_type'],
                            'file_size'     => $att['file_size'],
                        ]);
                    }
                }

                // Update status kendaraan + tanggal_terakhir_service dalam satu call
                // agar tidak ada stale model instance yang menyebabkan update kedua ter-skip
                $updateKendaraan = [
                    'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
                ];
                if ($request->status === 'selesai') {
                    $updateKendaraan['tanggal_terakhir_service'] = $request->tanggal_service;
                    $updateKendaraan['km_terakhir_service']      = $request->kilometer;
                    $updateKendaraan['kilometer_sekarang']       = $request->kilometer;
                }
                $kendaraan->update($updateKendaraan);
                $kendaraan->refresh(); // sinkron instance lokal dengan DB

                // Reset semua ServiceDetail Tidak Layak → Layak, isi service_history_id
                ServiceDetail::where('kendaraan_id', $request->kendaraan_id)
                    ->where('status', 'Tidak Layak')
                    ->update([
                        'status'             => 'Layak',
                        'service_history_id' => $service->id,
                    ]);

                // 🔔 Jika status = selesai → reset ReminderService
                if ($request->status === 'selesai') {
                    $this->resetReminderService($kendaraan, $request->tanggal_service);
                }

                // ── Catat keuangan (lockForUpdate di dalam transaction) ──
                $kodeJurnal = 'SRV-' . $service->id;

                $lastSaldo = (float) (\Illuminate\Support\Facades\DB::table('keuangans')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->value('saldo') ?? 0);

                if (!Keuangan::where('reference', $kodeJurnal)->exists()) {
                    Keuangan::create([
                        'tanggal'     => $request->tanggal_service,
                        'reference'   => $kodeJurnal,
                        'user_id'     => auth()->id(),
                        'kategori'    => 'Pengeluaran',
                        'metode'      => 'Cash',
                        'keterangan'  => 'Service Kendaraan',
                        'pemasukan'   => 0,
                        'pengeluaran' => $request->total_biaya,
                        'saldo'       => $lastSaldo - $request->total_biaya,
                        'source_type' => 'service_history',
                        'source_id'   => $service->id,
                    ]);
                }

                // ── Auto-posting ke Buku Besar (lockForUpdate di dalam transaction) ──
                if (!Bukubesar::where('kode_jurnal', $kodeJurnal)->exists()) {
                    $saldoBBTerakhir = (float) (\Illuminate\Support\Facades\DB::table('bukubesars')
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->value('saldo') ?? 0);

                    Bukubesar::create([
                        'kode_jurnal' => $kodeJurnal,
                        'transaksi'   => 'Beban Service - ' . ($service->kendaraan->merk ?? '-') . ' ' . ($service->kendaraan->nopol ?? '-'),
                        'kategori'    => 'Beban',
                        'tanggal'     => $request->tanggal_service,
                        'debit'       => $request->total_biaya,
                        'kredit'      => 0,
                        'saldo'       => $saldoBBTerakhir - $request->total_biaya,
                        'aktivitas'   => 'Operasi',
                        'keterangan'  => 'Auto-posting: Service kendaraan ' . ($service->kendaraan->nopol ?? '-'),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            // Rollback: hapus file yang sudah terlanjur di-move
            foreach ($movedFiles as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            throw $e;
        }

        return back()->with('success', 'Data service berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id'       => 'required|exists:kendaraan,id',
            'tanggal_service'    => 'required|date',
            'total_biaya'        => 'required|numeric|min:0',
            'bukti_pembayaran'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $data              = ServiceHistory::findOrFail($id);
        $kendaraan         = Kendaraan::findOrFail($request->kendaraan_id);
        $limitBulanan      = $kendaraan->limit_biaya_bulanan_service ?? 0;
        $limitTahunan      = $kendaraan->limit_biaya_tahunan_service ?? 0;
        $bulan             = date('Y-m', strtotime($request->tanggal_service));
        $tahun             = date('Y', strtotime($request->tanggal_service));

        // Hitung total bulan ini (exclude record yang sedang diedit)
        $totalBulanIni = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->where('id', '!=', $id)
            ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan])
            ->sum('total_biaya');

        // Hitung total tahun ini (exclude record yang sedang diedit)
        $totalTahunIni = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->where('id', '!=', $id)
            ->whereYear('tanggal_service', $tahun)
            ->sum('total_biaya');

        // Sisa limit bulanan
        $sisaLimit = $limitBulanan - ($totalBulanIni + $request->total_biaya);

        // Total biaya tahunan termasuk record ini
        $biayaTahunan = $totalTahunIni + $request->total_biaya;

        $maksBulanan  = $limitBulanan;

        // Status overservice: cek bulanan dulu, lalu tahunan
        $overBulanan       = $limitBulanan > 0 && ($totalBulanIni + $request->total_biaya) > $limitBulanan;
        $overTahunan       = $limitTahunan > 0 && $biayaTahunan > $limitTahunan;
        $statusPengeluaran = ($overBulanan || $overTahunan) ? 'overservice' : 'stabil';

        // Siapkan metadata file baru (move dilakukan di dalam transaction)
        $buktiBayarMeta  = null;
        $attachmentsMeta = [];

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $buktiBayarMeta = [
                'file'        => $file,
                'filename'    => $filename,
                'destination' => public_path('bukti_pembayaran'),
                'path'        => 'bukti_pembayaran/' . $filename,
                'old_path'    => $data->bukti_pembayaran,
            ];
        }

        if ($request->hasFile('bukti_attachment')) {
            $pathDir = public_path('service/attachments');
            foreach ($request->file('bukti_attachment') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $attachmentsMeta[] = [
                    'file'        => $file,
                    'filename'    => $filename,
                    'destination' => $pathDir,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => 'service/attachments/' . $filename,
                    'file_type'   => $file->getClientOriginalExtension(),
                    'file_size'   => $file->getSize(),
                ];
            }
        }

        $movedFiles  = [];
        $deletedFiles = []; // track file lama yang dihapus untuk restore jika rollback

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $request, $id, $data, $kendaraan, $sisaLimit, $maksBulanan,
                $biayaTahunan, $statusPengeluaran, $buktiBayarMeta, $attachmentsMeta,
                &$movedFiles, &$deletedFiles
            ) {
                // ── Tangani bukti pembayaran ──
                $buktiBayar = $data->bukti_pembayaran;

                if ($buktiBayarMeta) {
                    // Hapus file lama
                    $oldPath = public_path($buktiBayarMeta['old_path'] ?? '');
                    if ($buktiBayarMeta['old_path'] && file_exists($oldPath)) {
                        // Simpan konten untuk restore jika rollback
                        $deletedFiles[$oldPath] = file_get_contents($oldPath);
                        unlink($oldPath);
                    }

                    if (!file_exists($buktiBayarMeta['destination'])) {
                        mkdir($buktiBayarMeta['destination'], 0777, true);
                    }
                    $buktiBayarMeta['file']->move($buktiBayarMeta['destination'], $buktiBayarMeta['filename']);
                    $buktiBayar = $buktiBayarMeta['path'];
                    $movedFiles[] = public_path($buktiBayar);
                }

                $data->update([
                    'kendaraan_id'       => $request->kendaraan_id,
                    'keluhan'            => $request->keluhan,
                    'kilometer'          => $request->kilometer,
                    'total_biaya'        => $request->total_biaya,
                    'status'             => $request->status,
                    'tanggal_service'    => $request->tanggal_service,
                    'sisa_limit'         => $sisaLimit,
                    'maks_bulanan'       => $maksBulanan,
                    'biaya_tahunan'      => $biayaTahunan,
                    'status_pengeluaran' => $statusPengeluaran,
                    'bukti_pembayaran'   => $buktiBayar,
                ]);

                // ── Move attachments baru di dalam transaction ──
                if (!empty($attachmentsMeta)) {
                    if (!file_exists($attachmentsMeta[0]['destination'])) {
                        mkdir($attachmentsMeta[0]['destination'], 0777, true);
                    }
                    foreach ($attachmentsMeta as $att) {
                        $att['file']->move($att['destination'], $att['filename']);
                        $movedFiles[] = public_path($att['file_path']);

                        Attachment::create([
                            'relation_type' => 'service',
                            'relation_id'   => $data->id,
                            'file_name'     => $att['file_name'],
                            'file_path'     => $att['file_path'],
                            'file_type'     => $att['file_type'],
                            'file_size'     => $att['file_size'],
                        ]);
                    }
                }

                // Update status kendaraan + tanggal_terakhir_service dalam satu call
                $updateKendaraan = [
                    'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
                ];
                if ($request->status === 'selesai') {
                    $updateKendaraan['tanggal_terakhir_service'] = $request->tanggal_service;
                    $updateKendaraan['km_terakhir_service']      = $request->kilometer;
                    $updateKendaraan['kilometer_sekarang']       = $request->kilometer;
                }
                $kendaraan->update($updateKendaraan);
                $kendaraan->refresh();

                // 🔔 Jika status = selesai → reset ReminderService
                if ($request->status === 'selesai') {
                    $this->resetReminderService($kendaraan, $request->tanggal_service);
                }

                // ── Sync keuangan (update atau buat baru) ──
                $kodeJurnal = 'SRV-' . $data->id;

                $lastSaldo = (float) (\Illuminate\Support\Facades\DB::table('keuangans')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->value('saldo') ?? 0);

                $keuangan = Keuangan::where('reference', $kodeJurnal)->first();

                if ($keuangan) {
                    // Hitung ulang saldo: kembalikan pengeluaran lama, kurangi pengeluaran baru
                    $selisih      = $request->total_biaya - $keuangan->pengeluaran;
                    $saldoBaru    = $lastSaldo - $selisih;

                    $keuangan->update([
                        'tanggal'     => $request->tanggal_service,
                        'pengeluaran' => $request->total_biaya,
                        'saldo'       => $saldoBaru,
                        'keterangan'  => 'Service Kendaraan',
                    ]);
                } else {
                    Keuangan::create([
                        'tanggal'     => $request->tanggal_service,
                        'reference'   => $kodeJurnal,
                        'user_id'     => auth()->id(),
                        'kategori'    => 'Pengeluaran',
                        'metode'      => 'Cash',
                        'keterangan'  => 'Service Kendaraan',
                        'pemasukan'   => 0,
                        'pengeluaran' => $request->total_biaya,
                        'saldo'       => $lastSaldo - $request->total_biaya,
                        'source_type' => 'service_history',
                        'source_id'   => $data->id,
                    ]);
                }

                // ── Sync Buku Besar ──
                $saldoBBTerakhir = (float) (\Illuminate\Support\Facades\DB::table('bukubesars')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->value('saldo') ?? 0);

                $bukubesar = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();

                if ($bukubesar) {
                    $selisihBB   = $request->total_biaya - $bukubesar->debit;
                    $saldoBBBaru = $saldoBBTerakhir - $selisihBB;

                    $bukubesar->update([
                        'tanggal'  => $request->tanggal_service,
                        'debit'    => $request->total_biaya,
                        'saldo'    => $saldoBBBaru,
                        'transaksi' => 'Beban Service - ' . ($kendaraan->merk ?? '-') . ' ' . ($kendaraan->nopol ?? '-'),
                    ]);
                } else {
                    Bukubesar::create([
                        'kode_jurnal' => $kodeJurnal,
                        'transaksi'   => 'Beban Service - ' . ($kendaraan->merk ?? '-') . ' ' . ($kendaraan->nopol ?? '-'),
                        'kategori'    => 'Beban',
                        'tanggal'     => $request->tanggal_service,
                        'debit'       => $request->total_biaya,
                        'kredit'      => 0,
                        'saldo'       => $saldoBBTerakhir - $request->total_biaya,
                        'aktivitas'   => 'Operasi',
                        'keterangan'  => 'Auto-posting: Service kendaraan ' . ($kendaraan->nopol ?? '-'),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            // Rollback: hapus file baru yang sudah terlanjur di-move
            foreach ($movedFiles as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            // Restore file lama yang sudah terhapus
            foreach ($deletedFiles as $filePath => $content) {
                file_put_contents($filePath, $content);
            }
            throw $e;
        }

        return back()->with('success', 'Data berhasil diupdate.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:proses,selesai',
        ]);

        $service = ServiceHistory::findOrFail($id);

        $service->update([
            'status' => $request->status,
        ]);

        // Sinkron status kendaraan + tanggal_terakhir_service dalam satu call
        $tanggalSelesai  = $service->tanggal_service
            ? Carbon::parse($service->tanggal_service)->toDateString()
            : now()->toDateString();

        $updateKendaraan = [
            'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
        ];
        if ($request->status === 'selesai') {
            $updateKendaraan['tanggal_terakhir_service'] = $tanggalSelesai;
            // Update km dari record service jika ada
            if ($service->kilometer > 0) {
                $updateKendaraan['km_terakhir_service'] = $service->kilometer;
                $updateKendaraan['kilometer_sekarang']  = $service->kilometer;
            }
        }

        $kendaraan = $service->kendaraan;
        $kendaraan->update($updateKendaraan);
        $kendaraan->refresh();

        // 🔔 Jika status berubah ke 'selesai' → reset ReminderService
        if ($request->status === 'selesai') {
            $this->resetReminderService($kendaraan, $tanggalSelesai);
        }

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $service   = ServiceHistory::findOrFail($id);
        $kendaraan = $service->kendaraan;

        // hapus semua file attachment terkait
        foreach ($service->attachments as $att) {
            if (file_exists(public_path($att->file_path))) {
                unlink(public_path($att->file_path));
            }
            $att->delete();
        }

        $service->delete();

        // Jika tidak ada service aktif lain yang masih proses, kembalikan status tersedia
        if ($kendaraan) {
            $masihProses = ServiceHistory::where('kendaraan_id', $kendaraan->id)
                ->where('status', 'proses')
                ->exists();

            if (!$masihProses) {
                $kendaraan->update(['status_kendaraan' => 'tersedia']);
            }
        }

        return back()->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Hapus 1 attachment tertentu
     */
    public function destroyAttachment($id)
    {
        $attachment = Attachment::where('relation_type', 'service')->findOrFail($id);

        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }

        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus');
    }

    public function pdf(Request $request)
    {
        $search = $request->search;
        $bulan  = $request->bulan;

        $data = ServiceHistory::with(['kendaraan', 'attachments'])
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan]))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('keluhan', 'like', "%$search%")
                        ->orWhere('status', 'like', "%$search%")
                        ->orWhereHas(
                            'kendaraan',
                            fn($k) =>
                            $k->where('merk', 'like', "%$search%")
                                ->orWhere('nopol', 'like', "%$search%")
                        );
                });
            })
            ->latest()
            ->get();

        $setting = Setting::first();

        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf     = Pdf::loadView('admin.service.pdf_history', compact('data', 'search', 'bulan', 'setting', 'logoSrc'));

        return $pdf->stream('service-history.pdf');
    }

    // ── HELPER ──────────────────────────────────────────────────────────────

    /**
     * Reset ReminderService "Service Rutin" untuk kendaraan setelah service selesai.
     * - Tandai reminder lama (aktif/jatuh_tempo) sebagai 'selesai'.
     * - Buat reminder baru berdasarkan interval dari kendaraan (limit_km_service atau
     *   setting default), fallback ke config('service.reminder_interval_bulan', 3).
     */
    private function resetReminderService(Kendaraan $kendaraan, string $tanggalSelesai): void
    {
        // Tandai semua reminder Service Rutin yang masih aktif/jatuh_tempo sebagai selesai
        ReminderService::where('kendaraan_id', $kendaraan->id)
            ->where('nama_reminder', 'Service Rutin')
            ->whereIn('status', ['aktif', 'jatuh_tempo'])
            ->update(['status' => 'selesai']);

        // Tentukan interval dari reminder aktif kendaraan (jika ada),
        // fallback ke config, fallback ke 3 bulan
        $reminderAktifTerakhir = ReminderService::where('kendaraan_id', $kendaraan->id)
            ->where('nama_reminder', 'Service Rutin')
            ->where('status', 'selesai')
            ->latest()
            ->first();

        if ($reminderAktifTerakhir && $reminderAktifTerakhir->interval_nilai > 0) {
            $intervalNilai  = $reminderAktifTerakhir->interval_nilai;
            $intervalSatuan = $reminderAktifTerakhir->interval_satuan;
        } else {
            $intervalNilai  = (int) config('service.reminder_interval_bulan', 3);
            $intervalSatuan = 'bulan';
        }

        $tanggalMulai = Carbon::parse($tanggalSelesai);

        $jatuhTempo = match ($intervalSatuan) {
            'hari'   => (clone $tanggalMulai)->addDays($intervalNilai),
            'minggu' => (clone $tanggalMulai)->addWeeks($intervalNilai),
            'tahun'  => (clone $tanggalMulai)->addYears($intervalNilai),
            default  => (clone $tanggalMulai)->addMonths($intervalNilai),
        };

        $statusReminder = Carbon::today()->gte($jatuhTempo) ? 'jatuh_tempo' : 'aktif';

        ReminderService::create([
            'kendaraan_id'         => $kendaraan->id,
            'nama_reminder'        => 'Service Rutin',
            'tanggal_mulai'        => $tanggalMulai->toDateString(),
            'interval_nilai'       => $intervalNilai,
            'interval_satuan'      => $intervalSatuan,
            'tanggal_jatuh_tempo'  => $jatuhTempo->toDateString(),
            'keterangan'           => 'Auto-reset setelah service selesai pada ' . $tanggalMulai->format('d/m/Y'),
            'status'               => $statusReminder,
            'sudah_dibuat_masalah' => false,
        ]);
    }
}