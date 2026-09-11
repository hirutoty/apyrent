<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\AsuransiKendaraan;
use App\Models\PajakKendaraan;
use App\Models\ServicePart;
use App\Models\GpsKendaraan;
use App\Models\GpsKendaraanHistory;
use App\Models\Kir;
use App\Models\KirHistory;
use App\Models\Stnk;
use App\Models\ServiceAsuransi;
use App\Models\PurchaseOrder;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\Attachment;
use App\Models\AsuransiHistory;
use App\Models\PajakHistory;
use App\Models\JenisAsuransi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PengeluaranTransferService
{
    /**
     * Main transfer method - route ke method spesifik berdasarkan source_type
     *
     * @param Pembayaran $pembayaran
     * @param array $approvalFiles Files yang diupload saat approval
     * @return int Target ID dari record yang dibuat
     */
    public function transfer(Pembayaran $pembayaran, array $approvalFiles, ?array $selectedItems = null): int
    {
        DB::beginTransaction();
        
        try {
            $targetId = match($pembayaran->source_type) {
                'asuransi_kendaraan'            => $this->transferAsuransi($pembayaran, $approvalFiles),
                'asuransi_kendaraan_perpanjang' => $this->transferAsuransiPerpanjang($pembayaran, $approvalFiles),
                'pajak'                         => $this->transferPajak($pembayaran, $approvalFiles),
                'pajak_perpanjang'              => $this->transferPajakPerpanjang($pembayaran, $approvalFiles),
                'service_part'                  => $this->transferServicePart($pembayaran, $approvalFiles),
                'gps'                           => $this->transferGps($pembayaran, $approvalFiles, $selectedItems),
                'gps_perpanjang'                => $this->transferGpsPerpanjang($pembayaran, $approvalFiles, $selectedItems),
                'kir'                           => $this->transferKir($pembayaran, $approvalFiles),
                'kir_perpanjang'                => $this->transferKirPerpanjang($pembayaran, $approvalFiles),
                'stnk'                          => $this->transferStnk($pembayaran, $approvalFiles),
                'service_asuransi'              => $this->transferServiceAsuransi($pembayaran, $approvalFiles),
                'purchase_order'                => $this->transferPurchaseOrder($pembayaran, $approvalFiles),
                default => throw new \Exception("Unknown source type: {$pembayaran->source_type}"),
            };
            
            DB::commit();
            
            return $targetId;
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Transfer failed for Pembayaran #{$pembayaran->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Transfer Asuransi Kendaraan (tambah baru)
     * Pola sama dengan GPS: record sudah dibuat saat store() dengan persetujuan=Pending,
     * saat approval cukup update record existing dengan bukti & aktifkan.
     */
    protected function transferAsuransi(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;

        // Copy bukti bayar dari approval ke final storage
        $buktiBayar = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'asuransi/bukti_bayar',
            $pembayaran->id
        );

        // Coba update record Pending yang sudah dibuat saat store()
        $existing = null;
        if (!empty($sourceData['existing_record_id'])) {
            $existing = AsuransiKendaraan::where('id', $sourceData['existing_record_id'])
                ->where(function ($q) {
                    $q->where('persetujuan', 'Pending')->orWhereNull('persetujuan');
                })
                ->first();
        }

        // Fallback: cari via pembayaran_id
        if (!$existing) {
            $existing = AsuransiKendaraan::where('pembayaran_id', $pembayaran->id)
                ->where(function ($q) {
                    $q->where('persetujuan', 'Pending')->orWhereNull('persetujuan');
                })
                ->first();
        }

        $updateData = [
            'bukti_bayar'       => $buktiBayar,
            'tanggal_bayar'     => $sourceData['tanggal_bayar'] ?? now()->toDateString(),
            'status_kendaraan'  => 'aktif',
            'pembayaran_id'     => $pembayaran->id,
            'persetujuan'       => 'Disetujui',
            'nama_rekening'     => $sourceData['nama_rekening'] ?? null,
            'nama_bank'         => $sourceData['nama_bank'] ?? null,
            'no_rekening'       => $sourceData['no_rekening'] ?? null,
        ];

        if ($existing) {
            $existing->update($updateData);
            $asuransi = $existing;
        } else {
            // Fallback: buat baru jika record Pending tidak ditemukan (alur lama)
            $asuransi = AsuransiKendaraan::create(array_merge($updateData, [
                'kendaraan_id'      => $sourceData['kendaraan_id'],
                'asuransi_id'       => $sourceData['asuransi_id'],
                'jenis_asuransi_id' => $sourceData['jenis_asuransi_id'],
                'tgl_mulai'         => $sourceData['tgl_mulai'],
                'tgl_berakhir'      => $sourceData['tgl_berakhir'],
                'durasi_bulan'      => $sourceData['durasi_bulan'] ?? 12,
                'biaya'             => $sourceData['biaya'],
            ]));
        }

        // Copy attachments approval ke storage final
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'asuransi/attachments',
            'asuransi',
            $asuransi->id,
            $pembayaran->id
        );

        $kendaraan     = Kendaraan::find($sourceData['kendaraan_id'] ?? $asuransi->kendaraan_id);
        $jenisAsuransi = JenisAsuransi::find($sourceData['jenis_asuransi_id'] ?? $asuransi->jenis_asuransi_id);

        $this->createKeuanganRecord(
            'ASURANSI',
            $asuransi->id,
            $sourceData['biaya'] ?? $asuransi->biaya,
            'Pembayaran asuransi kendaraan: ' . ($jenisAsuransi->nama_jenis ?? '-') . ' - ' . ($kendaraan->nopol ?? '-')
        );

        $this->createBukubesarRecord(
            'ASURANSI',
            $asuransi->id,
            $sourceData['biaya'] ?? $asuransi->biaya,
            'Beban Asuransi - ' . ($jenisAsuransi->nama_jenis ?? '-'),
            'Auto-posting: Pembayaran asuransi kendaraan ' . ($kendaraan->nopol ?? '-')
        );

        return $asuransi->id;
    }

    /**
     * Transfer Pajak Kendaraan (tambah baru)
     * Jika record pajak sudah ada (existing_record_id), update record tsb.
     * Jika tidak ada, buat baru.
     */
    protected function transferPajak(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;

        // Copy bukti dari approval
        $bukti = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'pajak/bukti',
            $pembayaran->id
        );

        // Jika record sudah ada (dibuat saat store() dengan Pending), update saja
        if (!empty($sourceData['existing_record_id'])) {
            $pajak = PajakKendaraan::findOrFail($sourceData['existing_record_id']);
            $pajak->update([
                'bukti'         => $bukti,
                'status'        => 'sudah_bayar',
                'status_aktif'  => 'aktif',
                'persetujuan'   => 'Disetujui',
                'pembayaran_id' => $pembayaran->id,
            ]);
        } else {
            // Fallback: buat baru (alur lama)
            $pajak = PajakKendaraan::create([
                'kendaraan_id'  => $sourceData['kendaraan_id'],
                'jenis_pajak'   => $sourceData['jenis_pajak'],
                'nominal'       => $sourceData['nominal'],
                'jatuh_tempo'   => $sourceData['jatuh_tempo'],
                'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? now()->toDateString(),
                'status'        => 'sudah_bayar',
                'status_aktif'  => 'aktif',
                'keterangan'    => $sourceData['keterangan'] ?? null,
                'nama_pemilik'  => $sourceData['nama_pemilik'] ?? null,
                'nama_bank'     => $sourceData['nama_bank'] ?? null,
                'no_rekening'   => $sourceData['no_rekening'] ?? null,
                'bukti'         => $bukti,
                'pembayaran_id' => $pembayaran->id,
                'persetujuan'   => 'Disetujui',
            ]);
        }

        // Copy attachments dari approval
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'pajak/attachments',
            'pajak',
            $pajak->id,
            $pembayaran->id
        );

        // Load kendaraan
        $kendaraan = Kendaraan::find($sourceData['kendaraan_id']);

        $this->createKeuanganRecord(
            'PAJAK',
            $pajak->id,
            $sourceData['nominal'],
            'Pembayaran pajak kendaraan: ' . $sourceData['jenis_pajak'] . ' - ' . ($kendaraan->nopol ?? '-')
        );

        $this->createBukubesarRecord(
            'PAJAK',
            $pajak->id,
            $sourceData['nominal'],
            'Beban Pajak - ' . $sourceData['jenis_pajak'],
            'Auto-posting: Pembayaran pajak kendaraan ' . ($kendaraan->nopol ?? '-')
        );

        return $pajak->id;
    }

    /**
     * Transfer Service Part — dijalankan saat keuangan approve di halaman Pembayaran.
     *
     * Alur baru (Task 3):
     * 1. Buat/update ServiceHistory untuk kendaraan ini
     * 2. Buat setiap ServicePart dari source_data['parts'] dengan:
     *    - status       = 'tidak_aktif'   (belum dipasang secara fisik)
     *    - persetujuan  = 'Disetujui'     (sudah disetujui keuangan)
     *    - status_approval = 'approved'
     * 3. Catat cashflow (Keuangan + BukuBesar) di sini — BUKAN di store()/requestStore()
     * 4. Copy attachment dari temp storage ke final storage
     */
    protected function transferServicePart(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;
        $kendaraanId = $sourceData['kendaraan_id'];
        $kendaraan   = Kendaraan::find($kendaraanId);

        // ── Hitung total biaya dari parts ─────────────────────────────
        $parts       = $sourceData['parts'] ?? [];
        $sumBiaya    = collect($parts)->sum(fn($p) => (int)($p['biaya'] ?? 0));
        $totalBiaya  = filled($sourceData['total_biaya_override'] ?? null) && (int)($sourceData['total_biaya_override'] ?? 0) > 0
            ? (int)$sourceData['total_biaya_override']
            : $sumBiaya;
        $totalBiaya  = max($totalBiaya, $sumBiaya > 0 ? $sumBiaya : 0);

        // ── Cari atau buat ServiceHistory ─────────────────────────────
        $existing = \App\Models\ServiceHistory::where('kendaraan_id', $kendaraanId)
            ->latest()
            ->first();

        if ($existing) {
            $existing->update([
                'keluhan'         => $sourceData['keluhan'] ?? $existing->keluhan,
                'kilometer'       => $sourceData['kilometer'] ?? $existing->kilometer,
                'total_biaya'     => $existing->total_biaya + $totalBiaya,
                'tanggal_service' => $sourceData['tanggal_service'] ?? $existing->tanggal_service,
                'status_approval' => 'approved',
                'persetujuan'     => 'Disetujui',
                'approval_by'     => auth()->id(),
                'approval_at'     => now(),
            ]);
            $service = $existing;
        } else {
            $service = \App\Models\ServiceHistory::create([
                'kendaraan_id'    => $kendaraanId,
                'keluhan'         => $sourceData['keluhan'] ?? null,
                'kilometer'       => $sourceData['kilometer'] ?? 0,
                'total_biaya'     => $totalBiaya,
                'status'          => 'proses',
                'tanggal_service' => $sourceData['tanggal_service'] ?? now()->toDateString(),
                'status_approval' => 'approved',
                'persetujuan'     => 'Disetujui',
                'is_request'      => (bool)($sourceData['is_request'] ?? true),
                'approval_by'     => auth()->id(),
                'approval_at'     => now(),
            ]);
        }

        // ── Resolve kategori baru (inline category) ───────────────────
        foreach ($parts as &$partData) {
            if (!empty($partData['nama_category_baru'])) {
                $cat = \App\Models\ServiceCategory::firstOrCreate(
                    ['nama' => trim($partData['nama_category_baru'])]
                );
                $partData['category_id'] = $cat->id;
            }
        }
        unset($partData);

        // ── Buat ServicePart untuk setiap part ────────────────────────
        $lastPartId = null;
        foreach ($parts as $idx => $partData) {
            $tglPasang    = \Carbon\Carbon::parse($partData['tgl_pasang'] ?? now());
            $intervalNilai = (int)($partData['interval_nilai'] ?? 12);
            $intervalSatuan = $partData['interval_satuan'] ?? 'bulan';

            $tanggalLimit = match ($intervalSatuan) {
                'hari'   => (clone $tglPasang)->addDays($intervalNilai),
                'minggu' => (clone $tglPasang)->addWeeks($intervalNilai),
                'tahun'  => (clone $tglPasang)->addYears($intervalNilai),
                default  => (clone $tglPasang)->addMonths($intervalNilai),
            };

            // Hitung status_pengeluaran
            $statusPengeluaran = 'stabil';
            $categoryId = $partData['category_id'] ?? null;
            $biaya      = (int)($partData['biaya'] ?? 0);
            if ($categoryId && $biaya > 0) {
                $limit = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $kendaraanId)
                    ->where('category_id', $categoryId)
                    ->whereNotNull('limit_price')
                    ->first();
                if ($limit && $biaya > $limit->limit_price) {
                    $statusPengeluaran = 'overservice';
                }
            }

            // Copy bukti files dari temp storage ke final storage
            $buktiFiles = [];
            $tempFiles  = $sourceData['temp_files'] ?? [];
            // Coba ambil dari temp_files per-part jika ada
            $partTempBukti = $tempFiles['parts'][$idx]['bukti'] ?? $tempFiles['bukti'] ?? [];
            foreach ((array)$partTempBukti as $tf) {
                if (!empty($tf['path'])) {
                    $finalPath = $this->copyFileToPublic($tf['path'], 'service-parts', $pembayaran->id);
                    $buktiFiles[] = [
                        'path' => $finalPath,
                        'name' => $tf['original_name'] ?? basename($tf['path']),
                        'type' => $tf['extension'] ?? pathinfo($tf['path'], PATHINFO_EXTENSION),
                    ];
                }
            }

            $part = \App\Models\ServicePart::create([
                'service_history_id'  => $service->id,
                'kendaraan_id'        => $kendaraanId,
                'category_id'         => $categoryId,
                'nama_part'           => $partData['nama_part'],
                'part_number'         => $partData['part_number'] ?? null,
                'serial_number'       => $partData['serial_number'] ?? null,
                'posisi'              => $partData['posisi'] ?? null,
                'tgl_pasang'          => $tglPasang->toDateString(),
                'kilometer_pasang'    => (int)($partData['kilometer_pasang'] ?? $sourceData['kilometer'] ?? 0),
                'kondisi'             => $partData['kondisi'] ?? 'Baik',
                // Status tidak_aktif: disetujui keuangan tapi belum dipasang fisik
                'status'              => 'tidak_aktif',
                'interval_nilai'      => $intervalNilai,
                'interval_satuan'     => $intervalSatuan,
                'tanggal_limit'       => $tanggalLimit->toDateString(),
                'biaya'               => $biaya,
                'status_pengeluaran'  => $statusPengeluaran,
                'bukti'               => !empty($buktiFiles) ? $buktiFiles : null,
                'keterangan'          => $partData['keterangan'] ?? null,
                'nama_rekening'       => $partData['nama_rekening'] ?? null,
                'nama_bank'           => $partData['nama_bank'] ?? null,
                'no_rekening'         => $partData['no_rekening'] ?? null,
                'is_request'          => (bool)($partData['is_request'] ?? true),
                'status_approval'     => 'approved',
                'approval_by'         => auth()->id(),
                'approval_at'         => now(),
                'persetujuan'         => 'Disetujui',
            ]);

            $lastPartId = $part->id;
        }

        // ── Copy lampiran (attachments) dari temp storage ─────────────
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'service/attachments',
            'service',
            $service->id,
            $pembayaran->id
        );

        // Juga copy attachment dari temp_files jika ada
        $tempAttachments = ($sourceData['temp_files'] ?? [])['attachments'] ?? [];
        foreach ($tempAttachments as $tf) {
            if (empty($tf['path'])) continue;
            try {
                $finalPath = $this->copyFileToPublic($tf['path'], 'service/attachments', $pembayaran->id);
                \App\Models\Attachment::create([
                    'relation_type' => 'service',
                    'relation_id'   => $service->id,
                    'file_name'     => $tf['original_name'] ?? basename($tf['path']),
                    'file_path'     => $finalPath,
                    'file_type'     => $tf['extension'] ?? pathinfo($tf['path'], PATHINFO_EXTENSION),
                    'file_size'     => $tf['size'] ?? null,
                ]);
            } catch (\Exception $e) {
                \Log::warning("Gagal copy attachment service part: " . $e->getMessage());
            }
        }

        // ── Catat cashflow — HANYA dicatat di sini (bukan di store/requestStore) ──
        $this->createKeuanganRecord(
            'SRV',
            $service->id,
            $totalBiaya,
            'Service Kendaraan - ' . ($kendaraan->merk ?? '-') . ' ' . ($kendaraan->nopol ?? '-')
        );

        $this->createBukubesarRecord(
            'SRV',
            $service->id,
            $totalBiaya,
            'Beban Service - ' . ($kendaraan->merk ?? '-') . ' ' . ($kendaraan->nopol ?? '-'),
            'Auto-posting: Service kendaraan ' . ($kendaraan->nopol ?? '-') . ' via PR #' . $pembayaran->no_pr
        );

        return $service->id;
    }

    /**
     * Transfer GPS Kendaraan
     * source_data berisi: kendaraan_id, status_gps, tanggal_bayar, tanggal_habis,
     *                     keterangan, gps_items[]{gps_id, type, biaya_sewa,
     *                     nama_bank, no_rekening, nama_pemilik}
     *
     * @param  array|null $selectedItems  Subset item yang di-approve. Jika null → semua item.
     *                                    Format: [ ['idx'=>0, 'bukti_file'=>UploadedFile|null, ...], ... ]
     */
    protected function transferGps(Pembayaran $pembayaran, array $approvalFiles, ?array $selectedItems = null): int
    {
        $sourceData   = $pembayaran->source_data;
        $allGpsItems  = $sourceData['gps_items'] ?? [];
        $kendaraanId  = $sourceData['kendaraan_id'];
        $tanggalBayar = $sourceData['tanggal_bayar'] ?? now()->toDateString();
        $tanggalHabis = $sourceData['tanggal_habis'] ?? now()->addYear()->toDateString();
        $statusGps    = $sourceData['status_gps'] ?? 'aktif';
        $keterangan   = $sourceData['keterangan'] ?? null;

        // Filter: jika selectedItems diberikan, hanya proses item yang ada di dalamnya
        if ($selectedItems !== null) {
            $selectedIdx = array_column($selectedItems, 'idx');
            $gpsItems    = array_values(array_filter(
                $allGpsItems,
                fn($item, $idx) => in_array($idx, $selectedIdx),
                ARRAY_FILTER_USE_BOTH
            ));
        } else {
            $gpsItems = $allGpsItems;
        }

        $kendaraan  = Kendaraan::find($kendaraanId);
        $totalBiaya = 0;
        $lastId     = null;

        $buktiDir = public_path('gps/bukti_bayar');
        if (!file_exists($buktiDir)) mkdir($buktiDir, 0777, true);

        // Update atau buat record GpsKendaraan per item yang diapprove
        foreach ($gpsItems as $loopIdx => $item) {
            $biayaSewa = (int) ($item['biaya_sewa'] ?? 0);
            $totalBiaya += $biayaSewa;

            // Hitung durasi bulan
            $durasiBulan = (int) \Carbon\Carbon::parse($tanggalBayar)
                ->diffInMonths(\Carbon\Carbon::parse($tanggalHabis));
            $durasiBulan = max($durasiBulan, 1);

            // Bukti per item: cocokkan berdasarkan gps_id + type dari selectedItems
            $buktiBayar = null;
            if ($selectedItems !== null) {
                foreach ($selectedItems as $sel) {
                    // Cari entry selectedItems yang cocok dengan item ini via gps_id+type atau idx
                    $selGpsId = $sel['gps_id'] ?? null;
                    $selType  = $sel['type'] ?? null;
                    $itemGpsId = $item['gps_id'] ?? null;
                    $itemType  = $item['type'] ?? null;

                    $matchByIdentity = $selGpsId && $selType
                        && $selGpsId == $itemGpsId && $selType == $itemType;

                    // Fallback: cocokkan by idx vs posisi di allGpsItems
                    $matchByIdx = false;
                    if (!$matchByIdentity) {
                        foreach ($allGpsItems as $ai => $ai_item) {
                            if (($ai_item['gps_id'] ?? null) == $itemGpsId
                                && ($ai_item['type'] ?? null) == $itemType
                                && $ai === (int) ($sel['idx'] ?? -1)) {
                                $matchByIdx = true;
                                break;
                            }
                        }
                    }

                    if (($matchByIdentity || $matchByIdx) && !empty($sel['bukti_path'])) {
                        $buktiBayar = $sel['bukti_path'];
                        break;
                    }
                }
            }

            // Cari GPS record yang terkait item ini via pembayaran_id + gps_id + type
            // Record sudah dibuat saat store() dengan persetujuan='Pending' atau 'Diajukan ke Pembayaran'
            $existing = GpsKendaraan::where('kendaraan_id', $kendaraanId)
                ->where('gps_id', $item['gps_id'] ?? null)
                ->where('type', $item['type'] ?? null)
                ->where('pembayaran_id', $pembayaran->id)
                ->whereIn('persetujuan', ['Pending', 'Diajukan ke Pembayaran'])
                ->first();

            // Fallback: cari by kendaraan_id tanpa pembayaran_id (alur PO lama sebelum pembayaran_id terisi)
            if (!$existing) {
                $existing = GpsKendaraan::where('kendaraan_id', $kendaraanId)
                    ->where('gps_id', $item['gps_id'] ?? null)
                    ->where('type', $item['type'] ?? null)
                    ->whereIn('persetujuan', ['Pending', 'Diajukan ke Pembayaran'])
                    ->first();
            }

            $updateData = [
                'pembayaran_id' => $pembayaran->id, // Link to Pembayaran (important for PO flow)
                'status_gps'    => 'aktif', // Pembayaran disetujui → GPS resmi aktif
                'tanggal_pasang'=> $tanggalBayar,
                'tanggal_habis' => $tanggalHabis,
                'tanggal_bayar' => $tanggalBayar,
                'biaya_sewa'    => $biayaSewa,
                'durasi_bulan'  => $durasiBulan,
                'status_sewa'   => now()->lte($tanggalHabis) ? 'aktif' : 'expired',
                'bukti_bayar'   => $buktiBayar,
                'keterangan'    => $keterangan,
                'nama_bank'     => $item['nama_bank'] ?? null,
                'no_rekening'   => $item['no_rekening'] ?? null,
                'nama_pemilik'  => $item['nama_pemilik'] ?? null,
                'persetujuan'   => 'Disetujui',
            ];

            if ($existing) {
                $existing->update($updateData);
                $gpsRecord = $existing;
            } else {
                // Fallback: buat baru jika record Pending tidak ditemukan (shouldn't happen in normal flow)
                \Log::warning("GPS record not found during transfer for kendaraan {$kendaraanId}, creating new record. This should not happen in normal flow.");
                $gpsRecord = GpsKendaraan::create(array_merge($updateData, [
                    'kendaraan_id'  => $kendaraanId,
                    'gps_id'        => $item['gps_id'] ?? null,
                    'type'          => $item['type'] ?? null,
                ]));
            }

            $lastId = $gpsRecord->id;

            // Copy lampiran per item dari temp_files ke tabel attachments
            $tempFiles    = $sourceData['temp_files'] ?? [];
            $itemLampiran = [];

            // Cari lampiran berdasarkan origIdx (posisi item di allGpsItems)
            $origIdxForLamp = null;
            foreach ($allGpsItems as $ai => $ai_item) {
                if (($ai_item['gps_id'] ?? null) == ($item['gps_id'] ?? null)
                    && ($ai_item['type'] ?? null) == ($item['type'] ?? null)) {
                    $origIdxForLamp = $ai;
                    break;
                }
            }
            if ($origIdxForLamp !== null) {
                $itemLampiran = $tempFiles['gps_items'][$origIdxForLamp]['lampiran'] ?? [];
            }

            // Copy lampiran dari temp storage (jika belum ada attachments)
            foreach ($itemLampiran as $lampFile) {
                if (empty($lampFile['path'])) continue;
                
                // Check if attachment already exists (untuk avoid duplicate)
                $attachmentExists = Attachment::where('relation_type', 'gps')
                    ->where('relation_id', $gpsRecord->id)
                    ->where('file_name', $lampFile['original_name'] ?? basename($lampFile['path']))
                    ->exists();
                
                if (!$attachmentExists) {
                    $finalPath = $this->copyFileToPublic($lampFile['path'], 'gps/attachments', $pembayaran->id);
                    Attachment::create([
                        'relation_type' => 'gps',
                        'relation_id'   => $gpsRecord->id,
                        'file_name'     => $lampFile['original_name'] ?? basename($lampFile['path']),
                        'file_path'     => $finalPath,
                        'file_type'     => $lampFile['extension'] ?? pathinfo($lampFile['path'], PATHINFO_EXTENSION),
                        'file_size'     => $lampFile['size'] ?? null,
                    ]);
                }
            }
        }

        // Jika tidak ada item sama sekali (edge case), return 0
        if (!$lastId) {
            throw new \Exception('GPS items kosong, tidak ada data yang ditransfer.');
        }

        // Catat Keuangan & Buku Besar hanya untuk item yang diapprove
        $this->createKeuanganRecord(
            'GPS',
            $lastId,
            $totalBiaya,
            'Pembayaran GPS kendaraan - ' . ($kendaraan->nopol ?? '-') .
            ' (' . count($gpsItems) . ' GPS)'
        );

        $this->createBukubesarRecord(
            'GPS',
            $lastId,
            $totalBiaya,
            'Beban GPS - ' . ($kendaraan->nopol ?? '-'),
            'Auto-posting: Pembayaran GPS kendaraan ' . ($kendaraan->nopol ?? '-') .
            ' via PR #' . $pembayaran->no_pr
        );

        // Attachments tambahan
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'gps/attachments',
            'gps',
            $lastId,
            $pembayaran->id
        );

        return $lastId;
    }

    /**
     * Simpan item GPS yang ditolak ke gps_kendaraan dengan persetujuan = Ditolak.
     *
     * @param  Pembayaran $pembayaran
     * @param  array      $rejectedItems  [ ['idx'=>int, 'catatan'=>string], ... ]
     */
    public function transferGpsRejected(Pembayaran $pembayaran, array $rejectedItems): void
    {
        $sourceData   = $pembayaran->source_data;
        $allGpsItems  = $sourceData['gps_items'] ?? [];
        $recordIds    = $sourceData['gps_record_ids'] ?? [];   // index → gps_kendaraan.id
        $kendaraanId  = $sourceData['kendaraan_id'];
        $keterangan   = $sourceData['keterangan'] ?? null;

        foreach ($rejectedItems as $entry) {
            $idx     = $entry['idx'];
            $item    = $allGpsItems[$idx] ?? null;
            if (!$item) continue;

            // Cara 1: langsung via gps_record_ids (paling akurat)
            $recordId = $recordIds[$idx] ?? null;
            $existing = null;

            if ($recordId) {
                $existing = GpsKendaraan::where('id', $recordId)
                    ->whereIn('persetujuan', ['Pending', 'Diajukan ke Pembayaran'])
                    ->first();
            }

            // Cara 2: fallback via pembayaran_id + gps_id + type
            if (!$existing) {
                $existing = GpsKendaraan::where('pembayaran_id', $pembayaran->id)
                    ->where('gps_id', $item['gps_id'] ?? null)
                    ->where('type', $item['type'] ?? null)
                    ->whereIn('persetujuan', ['Pending', 'Diajukan ke Pembayaran'])
                    ->first();
            }

            // Cara 3: fallback via kendaraan_id + gps_id + type
            if (!$existing) {
                $existing = GpsKendaraan::where('kendaraan_id', $kendaraanId)
                    ->where('gps_id', $item['gps_id'] ?? null)
                    ->where('type', $item['type'] ?? null)
                    ->whereIn('persetujuan', ['Pending', 'Diajukan ke Pembayaran'])
                    ->first();
            }

            if ($existing) {
                $existing->update([
                    'persetujuan' => 'Ditolak',
                    'keterangan'  => $entry['catatan'] ?: ($existing->keterangan ?? null),
                ]);
            } else {
                \Log::warning("transferGpsRejected: GPS record not found for pembayaran #{$pembayaran->id} idx:{$idx} gps_id:{$item['gps_id']} type:{$item['type']}");
            }
        }
    }

    /**
     * Transfer Perpanjangan GPS — update record existing via approval
     * Cari record via: 1) pembayaran_id + gps_id + type (alur perpanjang per unit)
     *                   2) gps_kendaraan_id langsung (alur perpanjangKendaraan)
     * Saat approved: snapshot data lama → history → update persetujuan=Disetujui
     */
    protected function transferGpsPerpanjang(Pembayaran $pembayaran, array $approvalFiles, ?array $selectedItems = null): int
    {
        $sourceData   = $pembayaran->source_data;
        $tanggalBayar = $sourceData['tanggal_bayar'] ?? now()->toDateString();
        $gpsItems     = $sourceData['gps_items'] ?? [];

        $lastId     = null;
        $totalBiaya = 0;

        $buktiDir = public_path('gps/bukti_bayar');
        if (!file_exists($buktiDir)) mkdir($buktiDir, 0777, true);

        foreach ($gpsItems as $loopIdx => $item) {
            $biayaSewa    = (int) ($item['biaya_sewa'] ?? 0);
            $totalBiaya  += $biayaSewa;
            $tanggalHabis = $item['tanggal_habis'] ?? ($sourceData['tanggal_habis'] ?? now()->addYear()->toDateString());
            $statusGps    = $item['status_gps'] ?? ($sourceData['status_gps'] ?? 'aktif');

            $durasiBulan = max((int) \Carbon\Carbon::parse($tanggalBayar)->diffInMonths(\Carbon\Carbon::parse($tanggalHabis)), 1);

            // Bukti dari selectedItems
            $buktiBayar = null;
            if ($selectedItems !== null) {
                foreach ($selectedItems as $sel) {
                    $selGpsId = $sel['gps_id'] ?? null;
                    $selType  = $sel['type'] ?? null;
                    if ($selGpsId == ($item['gps_id'] ?? null) && $selType == ($item['type'] ?? null)) {
                        $buktiBayar = $sel['bukti_path'] ?? null;
                        break;
                    }
                }
            }
            if (!$buktiBayar && !empty($approvalFiles['bukti'][0])) {
                $buktiBayar = $this->copyBuktiToFinalStorage(
                    $approvalFiles['bukti'][0],
                    'gps/bukti_bayar',
                    $pembayaran->id
                );
            }

            // ── Cari record existing ────────────────────────────────────
            $existing = null;

            // 1) Via gps_kendaraan_id (perpanjangKendaraan — record sudah ada, bukan Pending baru)
            if (!empty($item['gps_kendaraan_id'])) {
                $existing = GpsKendaraan::find($item['gps_kendaraan_id']);
            }

            // 2) Via pembayaran_id + gps_id + type (perpanjang per unit — record Pending dibuat saat perpanjang())
            if (!$existing) {
                $existing = GpsKendaraan::where('pembayaran_id', $pembayaran->id)
                    ->where('gps_id', $item['gps_id'] ?? null)
                    ->where('type', $item['type'] ?? null)
                    ->where(function($q) {
                        $q->where('persetujuan', 'Pending')->orWhereNull('persetujuan');
                    })
                    ->first();
            }

            // Snapshot data lama ke history SEBELUM update
            if ($existing) {
                \App\Models\GpsKendaraanHistory::create([
                    'gps_kendaraan_id'  => $existing->id,
                    'kendaraan_id'      => $existing->kendaraan_id,
                    'gps_id'            => $existing->gps_id,
                    'type'              => $existing->type,
                    'status_gps'        => $existing->status_gps,
                    'tanggal_pasang'    => $existing->tanggal_pasang,
                    'tanggal_habis'     => $existing->tanggal_habis,
                    'biaya_sewa'        => $existing->biaya_sewa,
                    'durasi_bulan'      => $existing->durasi_bulan,
                    'status_sewa'       => $existing->status_sewa,
                    'bukti_bayar'       => $existing->bukti_bayar,
                    'tanggal_bayar'     => $existing->tanggal_bayar ?? $tanggalBayar,
                    'diperpanjang_pada' => now(),
                ]);
            }

            $updateData = [
                'status_gps'    => 'aktif', // Pembayaran disetujui → GPS resmi aktif
                'tanggal_pasang'=> $tanggalBayar,
                'tanggal_habis' => $tanggalHabis,
                'tanggal_bayar' => $tanggalBayar,
                'biaya_sewa'    => $biayaSewa,
                'durasi_bulan'  => $durasiBulan,
                'status_sewa'   => now()->lte($tanggalHabis) ? 'aktif' : 'expired',
                'bukti_bayar'   => $buktiBayar,
                'nama_bank'     => $item['nama_bank'] ?? null,
                'no_rekening'   => $item['no_rekening'] ?? null,
                'nama_pemilik'  => $item['nama_pemilik'] ?? null,
                'persetujuan'   => 'Disetujui',
                'pembayaran_id' => $pembayaran->id,
            ];

            if ($existing) {
                $existing->update($updateData);
                $lastId = $existing->id;
            } else {
                // Fallback: buat baru jika record tidak ditemukan
                $new = GpsKendaraan::create(array_merge($updateData, [
                    'pembayaran_id' => $pembayaran->id,
                    'kendaraan_id'  => $sourceData['kendaraan_id'],
                    'gps_id'        => $item['gps_id'] ?? null,
                    'type'          => $item['type'] ?? null,
                    'keterangan'    => 'Perpanjangan GPS',
                ]));
                $lastId = $new->id;
            }
        }

        if (!$lastId) throw new \Exception('GPS perpanjang items kosong.');

        $kendaraan = Kendaraan::find($sourceData['kendaraan_id'] ?? GpsKendaraan::find($lastId)?->kendaraan_id);

        $this->createKeuanganRecord(
            'GPS-PERP',
            $lastId,
            $totalBiaya,
            'Perpanjangan GPS kendaraan - ' . ($kendaraan->nopol ?? '-') . ' (' . count($gpsItems) . ' GPS)'
        );

        $this->createBukubesarRecord(
            'GPS-PERP',
            $lastId,
            $totalBiaya,
            'Beban Perpanjangan GPS - ' . ($kendaraan->nopol ?? '-'),
            'Auto-posting: Perpanjangan GPS ' . ($kendaraan->nopol ?? '-') . ' via PR #' . $pembayaran->no_pr
        );

        return $lastId;
    }

    /**
     * Transfer KIR (tambah baru)
     * Pola sama dengan GPS: record sudah dibuat saat store() dengan persetujuan=Pending,
     * saat approval cukup update record existing dengan bukti & aktifkan.
     */
    protected function transferKir(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;

        // Copy bukti dari approval ke final storage
        $image = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'kir/dokumen',
            $pembayaran->id
        );

        // Coba update record Pending yang sudah dibuat saat store()
        $existing = null;
        if (!empty($sourceData['existing_record_id'])) {
            $existing = Kir::where('id', $sourceData['existing_record_id'])
                ->where(function ($q) {
                    $q->where('persetujuan', 'Pending')->orWhereNull('persetujuan');
                })
                ->first();
        }

        // Fallback: cari via pembayaran_id
        if (!$existing) {
            $existing = Kir::where('pembayaran_id', $pembayaran->id)
                ->where(function ($q) {
                    $q->where('persetujuan', 'Pending')->orWhereNull('persetujuan');
                })
                ->first();
        }

        $updateData = [
            'image'         => $image,
            'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? now()->toDateString(),
            'status'        => 'aktif',
            'pembayaran_id' => $pembayaran->id,
            'persetujuan'   => 'Disetujui',
        ];

        if ($existing) {
            $existing->update($updateData);
            $kir = $existing;
        } else {
            // Fallback: buat baru jika record Pending tidak ditemukan (alur lama)
            $kir = Kir::create(array_merge($updateData, [
                'kendaraan_id'  => $sourceData['kendaraan_id'],
                'no_ktp'        => $sourceData['no_ktp'],
                'nama_ktp'      => $sourceData['nama_ktp'],
                'lokasi_uji'    => $sourceData['lokasi_uji'],
                'penguji'       => $sourceData['penguji'] ?? null,
                'status_uji'    => $sourceData['status_uji'],
                'no_uji'        => $sourceData['no_uji'],
                'masa_berlaku'  => $sourceData['masa_berlaku'],
                'biaya'         => $sourceData['biaya'],
            ]));
        }

        // Copy attachments approval ke storage final
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'kir/attachments',
            'kir',
            $kir->id,
            $pembayaran->id
        );

        $kendaraan = Kendaraan::find($sourceData['kendaraan_id'] ?? $kir->kendaraan_id);

        $this->createKeuanganRecord(
            'KIR',
            $kir->id,
            $sourceData['biaya'] ?? $kir->biaya,
            'Pembayaran KIR kendaraan - ' . ($kendaraan->nopol ?? '-')
        );

        $this->createBukubesarRecord(
            'KIR',
            $kir->id,
            $sourceData['biaya'] ?? $kir->biaya,
            'Beban KIR',
            'Auto-posting: Pembayaran KIR kendaraan ' . ($kendaraan->nopol ?? '-')
        );

        return $kir->id;
    }

    /**
     * Transfer Perpanjangan Pajak Kendaraan
     * Snapshot data lama → pajak_history → update record aktif dengan data baru
     */
    protected function transferPajakPerpanjang(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;

        // Load record pajak lama
        $pajakLama = PajakKendaraan::findOrFail($sourceData['existing_record_id']);

        // Copy bukti baru dari approval
        $buktiBaru = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'pajak/bukti',
            $pembayaran->id
        );

        // Snapshot data lama ke pajak_history
        PajakHistory::create([
            'pajak_kendaraan_id' => $pajakLama->id,
            'kendaraan_id'       => $pajakLama->kendaraan_id,
            'jenis_pajak'        => $pajakLama->jenis_pajak,
            'nominal'            => $pajakLama->nominal,
            'jatuh_tempo'        => $pajakLama->jatuh_tempo,
            'tanggal_bayar'      => $pajakLama->tanggal_bayar,
            'status'             => $pajakLama->status,
            'keterangan'         => $pajakLama->keterangan,
            'bukti'              => $pajakLama->bukti,
            'diperpanjang_pada'  => now(),
        ]);

        // Update record aktif dengan data baru
        $pajakLama->update([
            'nominal'        => $sourceData['nominal'],
            'jatuh_tempo'    => $sourceData['jatuh_tempo'],
            'tanggal_bayar'  => $sourceData['tanggal_bayar'] ?? now()->toDateString(),
            'status'         => $sourceData['status'] ?? 'sudah_bayar',
            'status_aktif'   => 'aktif',
            'keterangan'     => $sourceData['keterangan'] ?? null,
            'bukti'          => $buktiBaru,
            'pembayaran_id'  => $pembayaran->id,
            'persetujuan'    => 'Disetujui',
        ]);

        // Copy attachments
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'pajak/attachments',
            'pajak',
            $pajakLama->id,
            $pembayaran->id
        );

        $kendaraan = Kendaraan::find($pajakLama->kendaraan_id);

        $this->createKeuanganRecord(
            'PAJAK-PERP',
            $pajakLama->id,
            $sourceData['nominal'],
            'Perpanjangan pajak kendaraan: ' . ($pajakLama->jenis_pajak) . ' - ' . ($kendaraan->nopol ?? '-')
        );

        $this->createBukubesarRecord(
            'PAJAK-PERP',
            $pajakLama->id,
            $sourceData['nominal'],
            'Beban Perpanjangan Pajak - ' . ($pajakLama->jenis_pajak),
            'Auto-posting: Perpanjangan pajak kendaraan ' . ($kendaraan->nopol ?? '-') . ' via PR #' . $pembayaran->no_pr
        );

        return $pajakLama->id;
    }

    /**
     * Transfer Perpanjangan Asuransi Kendaraan
     * Snapshot data lama → asuransi_history → update record aktif dengan data baru
     */
    protected function transferAsuransiPerpanjang(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;

        // Load record asuransi lama
        $asuransiLama = AsuransiKendaraan::findOrFail($sourceData['existing_record_id']);

        // Copy bukti baru dari approval
        $buktiBaru = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'asuransi/bukti_bayar',
            $pembayaran->id
        );

        // Snapshot data lama ke asuransi_history
        AsuransiHistory::create([
            'asuransi_kendaraan_id' => $asuransiLama->id,
            'kendaraan_id'          => $asuransiLama->kendaraan_id,
            'asuransi_id'           => $asuransiLama->asuransi_id,
            'jenis_asuransi_id'     => $asuransiLama->jenis_asuransi_id,
            'tgl_mulai'             => $asuransiLama->tgl_mulai,
            'tgl_berakhir'          => $asuransiLama->tgl_berakhir,
            'durasi_bulan'          => $asuransiLama->durasi_bulan,
            'biaya'                 => $asuransiLama->biaya,
            'bukti_bayar'           => $asuransiLama->bukti_bayar,
            'tanggal_bayar'         => $asuransiLama->tanggal_bayar,
            'diperpanjang_pada'     => now(),
        ]);

        // Update record aktif dengan data baru
        $asuransiLama->update([
            'asuransi_id'       => $sourceData['asuransi_id'] ?? $asuransiLama->asuransi_id,
            'jenis_asuransi_id' => $sourceData['jenis_asuransi_id'] ?? $asuransiLama->jenis_asuransi_id,
            'tgl_mulai'         => $sourceData['tgl_mulai'],
            'tgl_berakhir'      => $sourceData['tgl_berakhir'],
            'durasi_bulan'      => $sourceData['durasi_bulan'] ?? 12,
            'biaya'             => $sourceData['biaya'],
            'bukti_bayar'       => $buktiBaru,
            'tanggal_bayar'     => $sourceData['tanggal_bayar'] ?? now()->toDateString(),
            'pembayaran_id'     => $pembayaran->id,
            'persetujuan'       => 'Disetujui',
        ]);

        // Copy attachments
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'asuransi/attachments',
            'asuransi',
            $asuransiLama->id,
            $pembayaran->id
        );

        $kendaraan     = Kendaraan::find($asuransiLama->kendaraan_id);
        $jenisAsuransi = JenisAsuransi::find($asuransiLama->jenis_asuransi_id);

        $this->createKeuanganRecord(
            'ASURANSI-PERP',
            $asuransiLama->id,
            $sourceData['biaya'],
            'Perpanjangan asuransi kendaraan: ' . ($jenisAsuransi->nama_jenis ?? '-') . ' - ' . ($kendaraan->nopol ?? '-')
        );

        $this->createBukubesarRecord(
            'ASURANSI-PERP',
            $asuransiLama->id,
            $sourceData['biaya'],
            'Beban Perpanjangan Asuransi - ' . ($jenisAsuransi->nama_jenis ?? '-'),
            'Auto-posting: Perpanjangan asuransi ' . ($kendaraan->nopol ?? '-') . ' via PR #' . $pembayaran->no_pr
        );

        return $asuransiLama->id;
    }

    /**
     * Transfer Perpanjangan KIR
     * Snapshot data lama → kir_history → update record aktif
     * masa_berlaku baru = masa_berlaku lama + 6 bulan
     */
    protected function transferKirPerpanjang(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;

        // Load record kir lama
        $kirLama = Kir::findOrFail($sourceData['existing_record_id']);

        // Copy image baru dari approval
        $imageBaru = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'kir/dokumen',
            $pembayaran->id
        );

        // Hitung masa berlaku baru = masa_berlaku lama + 6 bulan
        $masaBerlakuBaru = \Carbon\Carbon::parse($kirLama->masa_berlaku)
            ->addMonths(6)
            ->toDateString();

        // Snapshot data lama ke kir_history
        KirHistory::create([
            'kir_id'            => $kirLama->id,
            'kendaraan_id'      => $kirLama->kendaraan_id,
            'no_uji'            => $kirLama->no_uji,
            'masa_berlaku'      => $kirLama->masa_berlaku,
            'biaya'             => $kirLama->biaya,
            'image'             => $kirLama->image,
            'tanggal_bayar'     => $kirLama->tanggal_bayar,
            'diperpanjang_pada' => now(),
        ]);

        // Update record aktif dengan data baru
        $kirLama->update([
            'no_uji'        => $sourceData['no_uji'],
            'masa_berlaku'  => $masaBerlakuBaru,
            'biaya'         => $sourceData['biaya'],
            'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? now()->toDateString(),
            'image'         => $imageBaru,
            'pembayaran_id' => $pembayaran->id,
            'persetujuan'   => 'Disetujui',
        ]);

        // Copy attachments
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'kir/attachments',
            'kir',
            $kirLama->id,
            $pembayaran->id
        );

        $kendaraan = Kendaraan::find($kirLama->kendaraan_id);

        $this->createKeuanganRecord(
            'KIR-PERP',
            $kirLama->id,
            $sourceData['biaya'],
            'Perpanjangan KIR kendaraan - ' . ($kendaraan->nopol ?? '-')
        );

        $this->createBukubesarRecord(
            'KIR-PERP',
            $kirLama->id,
            $sourceData['biaya'],
            'Beban Perpanjangan KIR',
            'Auto-posting: Perpanjangan KIR kendaraan ' . ($kendaraan->nopol ?? '-') . ' via PR #' . $pembayaran->no_pr
        );

        return $kirLama->id;
    }

    /**
     * Transfer STNK
     */
    protected function transferStnk(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;
        
        $bukti = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'stnk/dokumen',
            $pembayaran->id
        );
        
        $stnk = Stnk::create([
            'kendaraan_id'        => $sourceData['kendaraan_id'],
            'tanggal_perpanjang'  => $sourceData['tanggal_perpanjang'] ?? now(),
            'tanggal_berakhir'    => $sourceData['tanggal_berakhir'],
            'biaya'               => $sourceData['biaya'],
            'status'              => $sourceData['status'] ?? 'aktif',
            'keterangan'          => $sourceData['keterangan'] ?? null,
            'dokumen'             => $bukti,
        ]);
        
        $kendaraan = Kendaraan::find($sourceData['kendaraan_id']);
        
        $this->createKeuanganRecord(
            'STNK',
            $stnk->id,
            $sourceData['biaya'],
            'Pembayaran STNK kendaraan - ' . ($kendaraan->nopol ?? '-')
        );
        
        $this->createBukubesarRecord(
            'STNK',
            $stnk->id,
            $sourceData['biaya'],
            'Beban STNK',
            'Auto-posting: Pembayaran STNK kendaraan ' . ($kendaraan->nopol ?? '-')
        );
        
        return $stnk->id;
    }

    /**
     * Transfer Service Asuransi
     */
    protected function transferServiceAsuransi(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;
        
        $bukti = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'service-asuransi',
            $pembayaran->id
        );
        
        $serviceAsuransi = ServiceAsuransi::create([
            'kendaraan_id'      => $sourceData['kendaraan_id'],
            'service_history_id'=> $sourceData['service_history_id'] ?? null,
            'asuransi_id'       => $sourceData['asuransi_id'],
            'jenis_asuransi_id' => $sourceData['jenis_asuransi_id'],
            'no_polis'          => $sourceData['no_polis'] ?? null,
            'tanggal_klaim'     => $sourceData['tanggal_klaim'] ?? now(),
            'biaya'             => $sourceData['biaya'],
            'status'            => $sourceData['status'] ?? 'Disetujui',
            'keterangan'        => $sourceData['keterangan'] ?? null,
            'bukti'             => $bukti,
        ]);
        
        // Copy attachments
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'service-asuransi-attachment',
            'service_asuransi',
            $serviceAsuransi->id,
            $pembayaran->id
        );
        
        return $serviceAsuransi->id;
    }

    /**
     * Transfer Purchase Order - create record di purchase_orders setelah approved
     */
    protected function transferPurchaseOrder(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;

        $po = PurchaseOrder::create([
            'tanggal_po'     => $sourceData['tanggal_po'] ?? now()->toDateString(),
            'vendor'         => $sourceData['vendor'],
            'terkait_rfq'    => $sourceData['terkait_rfq'] ?? null,
            'total_barang'   => (int) ($sourceData['total_barang'] ?? 0),
            'total_harga'    => (int) ($sourceData['total_harga'] ?? 0),
            'status_po'      => 'Approved',
            'tanggal_kirim'  => $sourceData['tanggal_kirim'] ?? null,
            'tanggal_terima' => $sourceData['tanggal_terima'] ?? null,
            'catatan'        => $sourceData['catatan'] ?? null,
        ]);

        $kodeJurnal = 'PO-PR-' . $po->id;
        $nominal    = (int) ($sourceData['total_harga'] ?? 0);

        if ($nominal > 0) {
            $lastSaldo = (float) DB::table('keuangans')
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->value('saldo') ?? 0;

            Keuangan::create([
                'tanggal'     => now()->toDateString(),
                'reference'   => $kodeJurnal,
                'user_id'     => auth()->id(),
                'kategori'    => 'Pengeluaran',
                'metode'      => 'Transfer',
                'keterangan'  => 'Purchase Order #' . $po->po_id . ' - ' . $sourceData['vendor'],
                'pemasukan'   => 0,
                'pengeluaran' => $nominal,
                'saldo'       => $lastSaldo - $nominal,
                'sumber'      => 'auto',
            ]);

            $saldoBB = (float) DB::table('bukubesars')
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->value('saldo') ?? 0;

            Bukubesar::create([
                'kode_jurnal' => $kodeJurnal,
                'transaksi'   => 'Pembelian: ' . $sourceData['vendor'],
                'kategori'    => 'Beban',
                'tanggal'     => now()->toDateString(),
                'debit'       => $nominal,
                'kredit'      => 0,
                'saldo'       => $saldoBB - $nominal,
                'aktivitas'   => 'pembayaran',
                'keterangan'  => 'Auto-posting: PO #' . $po->po_id . ' disetujui via Pembayaran #' . $pembayaran->no_pr,
                'referensi'   => $pembayaran->no_pr,
            ]);
        }

        return $po->id;
    }

    /**
     * Copy bukti file dari approval storage ke final storage
     */
    protected function copyBuktiToFinalStorage(?array $buktiFile, string $targetFolder, int $pembayaranId): ?string
    {
        if (!$buktiFile || !isset($buktiFile['path'])) {
            return null;
        }
        
        return $this->copyFileToPublic($buktiFile['path'], $targetFolder, $pembayaranId);
    }

    /**
     * Copy file dari storage ke public folder
     */
    protected function copyFileToPublic(string $storagePath, string $publicFolder, int $pembayaranId): string
    {
        // Full path di storage
        $sourceFullPath = storage_path('app/public/' . $storagePath);
        
        // Generate nama file baru dengan prefix pembayaran
        $filename = 'pembayaran_' . $pembayaranId . '_' . time() . '_' . basename($storagePath);
        
        // Target path di public
        $targetFolder = public_path($publicFolder);
        if (!file_exists($targetFolder)) {
            File::makeDirectory($targetFolder, 0777, true);
        }
        
        $targetFullPath = $targetFolder . '/' . $filename;
        
        // Copy file
        File::copy($sourceFullPath, $targetFullPath);
        
        // Return relative path
        return $publicFolder . '/' . $filename;
    }

    /**
     * Copy attachments dan create Attachment records
     */
    protected function copyAttachmentsToFinalStorage(
        array $attachmentFiles,
        string $publicFolder,
        string $relationType,
        int $relationId,
        int $pembayaranId
    ): void {
        if (empty($attachmentFiles)) {
            return;
        }
        
        foreach ($attachmentFiles as $file) {
            $finalPath = $this->copyFileToPublic($file['path'], $publicFolder, $pembayaranId);
            
            Attachment::create([
                'relation_type' => $relationType,
                'relation_id'   => $relationId,
                'file_name'     => $file['original_name'],
                'file_path'     => $finalPath,
                'file_type'     => $file['extension'],
                'file_size'     => $file['size'],
            ]);
        }
    }

    /**
     * Create Keuangan record
     */
    protected function createKeuanganRecord(string $type, int $id, float $amount, string $description): void
    {
        $lastSaldo = (float) DB::table('keuangans')
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->value('saldo') ?? 0;
        
        $kodeJurnal = $type . '-' . $id . '-' . now()->timestamp;
        
        Keuangan::create([
            'tanggal'     => now(),
            'reference'   => $kodeJurnal,
            'user_id'     => auth()->id(),
            'divisi'      => auth()->user() ? ucfirst(auth()->user()->role) : 'Keuangan',
            'kategori'    => 'Pengeluaran',
            'metode'      => 'Cash',
            'keterangan'  => $description,
            'pemasukan'   => 0,
            'pengeluaran' => $amount,
            'saldo'       => $lastSaldo - $amount,
            'sumber'      => 'auto',
        ]);
    }

    /**
     * Create Bukubesar record
     */
    protected function createBukubesarRecord(string $type, int $id, float $amount, string $transaksi, string $description): void
    {
        $saldoBBTerakhir = (float) DB::table('bukubesars')
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->value('saldo') ?? 0;
        
        $kodeJurnal = $type . '-' . $id . '-' . now()->timestamp;
        
        Bukubesar::create([
            'kode_jurnal' => $kodeJurnal,
            'transaksi'   => $transaksi,
            'kategori'    => 'Beban',
            'tanggal'     => now()->toDateString(),
            'debit'       => $amount,
            'kredit'      => 0,
            'saldo'       => $saldoBBTerakhir - $amount,
            'aktivitas'   => 'Operasi',
            'keterangan'  => $description,
        ]);
    }
}
