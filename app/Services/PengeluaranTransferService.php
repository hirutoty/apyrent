<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\AsuransiKendaraan;
use App\Models\PajakKendaraan;
use App\Models\ServicePart;
use App\Models\GpsKendaraan;
use App\Models\Kir;
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
                'asuransi_kendaraan' => $this->transferAsuransi($pembayaran, $approvalFiles),
                'pajak' => $this->transferPajak($pembayaran, $approvalFiles),
                'service_part' => $this->transferServicePart($pembayaran, $approvalFiles),
                'gps' => $this->transferGps($pembayaran, $approvalFiles, $selectedItems),
                'gps_perpanjang' => $this->transferGpsPerpanjang($pembayaran, $approvalFiles, $selectedItems),
                'kir' => $this->transferKir($pembayaran, $approvalFiles),
                'stnk' => $this->transferStnk($pembayaran, $approvalFiles),
                'service_asuransi' => $this->transferServiceAsuransi($pembayaran, $approvalFiles),
                'purchase_order'   => $this->transferPurchaseOrder($pembayaran, $approvalFiles),
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
     * Transfer Asuransi Kendaraan
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
        
        // Create record di tabel asuransi_kendaraans
        $asuransi = AsuransiKendaraan::create([
            'kendaraan_id'      => $sourceData['kendaraan_id'],
            'asuransi_id'       => $sourceData['asuransi_id'],
            'jenis_asuransi_id' => $sourceData['jenis_asuransi_id'],
            'tgl_mulai'         => $sourceData['tgl_mulai'],
            'tgl_berakhir'      => $sourceData['tgl_berakhir'],
            'durasi_bulan'      => $sourceData['durasi_bulan'] ?? 12,
            'biaya'             => $sourceData['biaya'],
            'bukti_bayar'       => $buktiBayar,
            'status_kendaraan'  => 'aktif',
        ]);
        
        // Copy dan create attachments
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'asuransi/attachments',
            'asuransi',
            $asuransi->id,
            $pembayaran->id
        );
        
        // Load kendaraan untuk keterangan
        $kendaraan = Kendaraan::find($sourceData['kendaraan_id']);
        $jenisAsuransi = JenisAsuransi::find($sourceData['jenis_asuransi_id']);
        
        // Create Keuangan record
        $this->createKeuanganRecord(
            'ASURANSI',
            $asuransi->id,
            $sourceData['biaya'],
            'Pembayaran asuransi kendaraan: ' . ($jenisAsuransi->nama_jenis ?? '-') . ' - ' . ($kendaraan->nopol ?? '-')
        );
        
        // Create Bukubesar record
        $this->createBukubesarRecord(
            'ASURANSI',
            $asuransi->id,
            $sourceData['biaya'],
            'Beban Asuransi - ' . ($jenisAsuransi->nama_jenis ?? '-'),
            'Auto-posting: Pembayaran asuransi kendaraan ' . ($kendaraan->nopol ?? '-')
        );
        
        // Create history record
        AsuransiHistory::create([
            'asuransi_kendaraan_id' => $asuransi->id,
            'asuransi_id'           => $sourceData['asuransi_id'],
            'jenis_asuransi_id'     => $sourceData['jenis_asuransi_id'],
            'tgl_mulai'             => $sourceData['tgl_mulai'],
            'tgl_berakhir'          => $sourceData['tgl_berakhir'],
            'durasi_bulan'          => $sourceData['durasi_bulan'] ?? 12,
            'biaya'                 => $sourceData['biaya'],
            'tanggal_bayar'         => now(),
            'bukti_bayar'           => $buktiBayar,
            'diperpanjang_pada'     => now(),
        ]);
        
        return $asuransi->id;
    }

    /**
     * Transfer Pajak Kendaraan
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
        
        // Create record
        $pajak = PajakKendaraan::create([
            'kendaraan_id'  => $sourceData['kendaraan_id'],
            'jenis_pajak'   => $sourceData['jenis_pajak'],
            'nominal'       => $sourceData['nominal'],
            'jatuh_tempo'   => $sourceData['jatuh_tempo'],
            'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? now(),
            'status'        => $sourceData['status'] ?? 'sudah_bayar',
            'keterangan'    => $sourceData['keterangan'] ?? null,
            'bukti'         => $bukti,
        ]);
        
        // Copy attachments
        $this->copyAttachmentsToFinalStorage(
            $approvalFiles['attachments'] ?? [],
            'pajak/attachments',
            'pajak',
            $pajak->id,
            $pembayaran->id
        );
        
        // Load kendaraan
        $kendaraan = Kendaraan::find($sourceData['kendaraan_id']);
        
        // Create Keuangan (hanya jika sudah bayar)
        if (($sourceData['status'] ?? 'sudah_bayar') === 'sudah_bayar') {
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
        }
        
        // Create history
        PajakHistory::create([
            'pajak_kendaraan_id' => $pajak->id,
            'jenis_pajak'        => $sourceData['jenis_pajak'],
            'nominal'            => $sourceData['nominal'],
            'jatuh_tempo'        => $sourceData['jatuh_tempo'],
            'tanggal_bayar'      => $sourceData['tanggal_bayar'] ?? now(),
            'bukti'              => $bukti,
            'diperpanjang_pada'  => now(),
        ]);
        
        return $pajak->id;
    }

    /**
     * Transfer Service Part
     */
    protected function transferServicePart(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;
        
        // Copy bukti
        $buktiFiles = [];
        if (!empty($approvalFiles['bukti'])) {
            foreach ($approvalFiles['bukti'] as $file) {
                $finalPath = $this->copyFileToPublic(
                    $file['path'],
                    'service-parts',
                    $pembayaran->id
                );
                $buktiFiles[] = $finalPath;
            }
        }
        
        // Create service part
        $servicePart = ServicePart::create([
            'service_history_id' => $sourceData['service_history_id'] ?? null,
            'kendaraan_id'       => $sourceData['kendaraan_id'],
            'category_id'        => $sourceData['category_id'] ?? null,
            'nama_part'          => $sourceData['nama_part'],
            'part_number'        => $sourceData['part_number'] ?? null,
            'serial_number'      => $sourceData['serial_number'] ?? null,
            'posisi'             => $sourceData['posisi'] ?? null,
            'tgl_pasang'         => $sourceData['tgl_pasang'] ?? now(),
            'kilometer_pasang'   => $sourceData['kilometer_pasang'] ?? 0,
            'kondisi'            => $sourceData['kondisi'] ?? 'Terpasang',
            'status'             => $sourceData['status'] ?? 'Terpasang',
            'interval_nilai'     => $sourceData['interval_nilai'] ?? null,
            'interval_satuan'    => $sourceData['interval_satuan'] ?? 'bulan',
            'biaya'              => $sourceData['biaya'],
            'bukti'              => $buktiFiles,
            'keterangan'         => $sourceData['keterangan'] ?? null,
        ]);
        
        return $servicePart->id;
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

            // Coba update record Pending yang sudah ada (dibuat saat store())
            $existing = GpsKendaraan::where('pembayaran_id', $pembayaran->id)
                ->where('gps_id', $item['gps_id'] ?? null)
                ->where('type', $item['type'] ?? null)
                ->where(function($q) {
                    $q->where('persetujuan', 'Pending')
                      ->orWhereNull('persetujuan');
                })
                ->first();

            $updateData = [
                'status_gps'    => $statusGps,
                'tanggal_pasang'=> $tanggalBayar,
                'tanggal_habis' => $tanggalHabis,
                'tanggal_bayar' => $tanggalBayar,
                'biaya_sewa'    => $biayaSewa,
                'durasi_bulan'  => $durasiBulan,
                'status_sewa'   => now()->lte($tanggalHabis) ? 'aktif' : 'habis',
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
                // Fallback: buat baru jika record Pending tidak ditemukan
                $gpsRecord = GpsKendaraan::create(array_merge($updateData, [
                    'pembayaran_id' => $pembayaran->id,
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

            foreach ($itemLampiran as $lampFile) {
                if (empty($lampFile['path'])) continue;
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
        $kendaraanId  = $sourceData['kendaraan_id'];
        $tanggalBayar = $sourceData['tanggal_bayar'] ?? now()->toDateString();
        $tanggalHabis = $sourceData['tanggal_habis'] ?? now()->addYear()->toDateString();
        $statusGps    = $sourceData['status_gps'] ?? 'aktif';
        $keterangan   = $sourceData['keterangan'] ?? null;

        $durasiBulan = (int) \Carbon\Carbon::parse($tanggalBayar)
            ->diffInMonths(\Carbon\Carbon::parse($tanggalHabis));
        $durasiBulan = max($durasiBulan, 1);

        foreach ($rejectedItems as $entry) {
            $idx  = $entry['idx'];
            $item = $allGpsItems[$idx] ?? null;
            if (!$item) continue;

            $updateData = [
                'status_gps'    => 'nonaktif',
                'tanggal_pasang'=> $tanggalBayar,
                'tanggal_habis' => $tanggalHabis,
                'tanggal_bayar' => $tanggalBayar,
                'biaya_sewa'    => (int) ($item['biaya_sewa'] ?? 0),
                'durasi_bulan'  => $durasiBulan,
                'status_sewa'   => 'tidak_aktif',
                'bukti_bayar'   => null,
                'keterangan'    => $entry['catatan'] ?? $keterangan,
                'nama_bank'     => $item['nama_bank'] ?? null,
                'no_rekening'   => $item['no_rekening'] ?? null,
                'nama_pemilik'  => $item['nama_pemilik'] ?? null,
                'persetujuan'   => 'Ditolak',
            ];

            // Coba update record Pending yang dibuat saat store()
            $existing = GpsKendaraan::where('pembayaran_id', $pembayaran->id)
                ->where('gps_id', $item['gps_id'] ?? null)
                ->where('type', $item['type'] ?? null)
                ->where(function($q) {
                    $q->where('persetujuan', 'Pending')
                      ->orWhereNull('persetujuan');
                })
                ->first();

            if ($existing) {
                $existing->update($updateData);
            } else {
                // Fallback: buat baru jika record Pending tidak ditemukan
                GpsKendaraan::create(array_merge($updateData, [
                    'pembayaran_id' => $pembayaran->id,
                    'kendaraan_id'  => $kendaraanId,
                    'gps_id'        => $item['gps_id'] ?? null,
                    'type'          => $item['type'] ?? null,
                ]));
            }
        }
    }

    /**
     * Transfer Perpanjangan GPS — update record Pending yang dibuat saat perpanjang()
     * Saat approved: update persetujuan=Disetujui, status aktif, isi bukti_bayar
     */
    protected function transferGpsPerpanjang(Pembayaran $pembayaran, array $approvalFiles, ?array $selectedItems = null): int
    {
        $sourceData   = $pembayaran->source_data;
        $tanggalBayar = $sourceData['tanggal_bayar'] ?? now()->toDateString();
        $tanggalHabis = $sourceData['tanggal_habis'] ?? now()->addYear()->toDateString();
        $statusGps    = $sourceData['status_gps'] ?? 'aktif';
        $gpsItems     = $sourceData['gps_items'] ?? [];

        $lastId = null;
        $totalBiaya = 0;

        $buktiDir = public_path('gps/bukti_bayar');
        if (!file_exists($buktiDir)) mkdir($buktiDir, 0777, true);

        foreach ($gpsItems as $loopIdx => $item) {
            $biayaSewa = (int) ($item['biaya_sewa'] ?? 0);
            $totalBiaya += $biayaSewa;

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

            // Update record Pending yang dibuat saat perpanjang()
            $existing = GpsKendaraan::where('pembayaran_id', $pembayaran->id)
                ->where('gps_id', $item['gps_id'] ?? null)
                ->where('type', $item['type'] ?? null)
                ->where(function($q) {
                    $q->where('persetujuan', 'Pending')->orWhereNull('persetujuan');
                })
                ->first();

            $updateData = [
                'status_gps'    => $statusGps,
                'tanggal_pasang'=> $tanggalBayar,
                'tanggal_habis' => $tanggalHabis,
                'tanggal_bayar' => $tanggalBayar,
                'biaya_sewa'    => $biayaSewa,
                'durasi_bulan'  => $durasiBulan,
                'status_sewa'   => now()->lte($tanggalHabis) ? 'aktif' : 'habis',
                'bukti_bayar'   => $buktiBayar,
                'nama_bank'     => $item['nama_bank'] ?? null,
                'no_rekening'   => $item['no_rekening'] ?? null,
                'nama_pemilik'  => $item['nama_pemilik'] ?? null,
                'persetujuan'   => 'Disetujui',
            ];

            if ($existing) {
                $existing->update($updateData);
                $lastId = $existing->id;
            } else {
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

        $kendaraan = Kendaraan::find($sourceData['kendaraan_id']);

        $this->createKeuanganRecord(
            'GPS-PERP',
            $lastId,
            $totalBiaya,
            'Perpanjangan GPS kendaraan - ' . ($kendaraan->nopol ?? '-')
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
     * Transfer KIR
     */
    protected function transferKir(Pembayaran $pembayaran, array $approvalFiles): int
    {
        $sourceData = $pembayaran->source_data;
        
        $bukti = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'kir/dokumen',
            $pembayaran->id
        );
        
        $kir = Kir::create([
            'kendaraan_id'    => $sourceData['kendaraan_id'],
            'tanggal_uji'     => $sourceData['tanggal_uji'] ?? now(),
            'tanggal_berakhir'=> $sourceData['tanggal_berakhir'],
            'tanggal_bayar'   => $sourceData['tanggal_bayar'] ?? now(),
            'biaya'           => $sourceData['biaya'],
            'status'          => $sourceData['status'] ?? 'aktif',
            'keterangan'      => $sourceData['keterangan'] ?? null,
            'dokumen'         => $bukti,
        ]);
        
        $kendaraan = Kendaraan::find($sourceData['kendaraan_id']);
        
        $this->createKeuanganRecord(
            'KIR',
            $kir->id,
            $sourceData['biaya'],
            'Pembayaran KIR kendaraan - ' . ($kendaraan->nopol ?? '-')
        );
        
        $this->createBukubesarRecord(
            'KIR',
            $kir->id,
            $sourceData['biaya'],
            'Beban KIR',
            'Auto-posting: Pembayaran KIR kendaraan ' . ($kendaraan->nopol ?? '-')
        );
        
        return $kir->id;
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
