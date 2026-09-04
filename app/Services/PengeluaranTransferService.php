<?php

namespace App\Services;

use App\Models\Purchasero;
use App\Models\AsuransiKendaraan;
use App\Models\PajakKendaraan;
use App\Models\ServicePart;
use App\Models\GpsKendaraan;
use App\Models\Kir;
use App\Models\Stnk;
use App\Models\ServiceAsuransi;
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
     * @param Purchasero $purchasero
     * @param array $approvalFiles Files yang diupload saat approval
     * @return int Target ID dari record yang dibuat
     */
    public function transfer(Purchasero $purchasero, array $approvalFiles): int
    {
        DB::beginTransaction();
        
        try {
            $targetId = match($purchasero->source_type) {
                'asuransi_kendaraan' => $this->transferAsuransi($purchasero, $approvalFiles),
                'pajak' => $this->transferPajak($purchasero, $approvalFiles),
                'service_part' => $this->transferServicePart($purchasero, $approvalFiles),
                'gps' => $this->transferGps($purchasero, $approvalFiles),
                'kir' => $this->transferKir($purchasero, $approvalFiles),
                'stnk' => $this->transferStnk($purchasero, $approvalFiles),
                'service_asuransi' => $this->transferServiceAsuransi($purchasero, $approvalFiles),
                default => throw new \Exception("Unknown source type: {$purchasero->source_type}"),
            };
            
            DB::commit();
            
            return $targetId;
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Transfer failed for Purchasero #{$purchasero->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Transfer Asuransi Kendaraan
     */
    protected function transferAsuransi(Purchasero $purchasero, array $approvalFiles): int
    {
        $sourceData = $purchasero->source_data;
        
        // Copy bukti bayar dari approval ke final storage
        $buktiBayar = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'asuransi/bukti_bayar',
            $purchasero->id
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
            $purchasero->id
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
    protected function transferPajak(Purchasero $purchasero, array $approvalFiles): int
    {
        $sourceData = $purchasero->source_data;
        
        // Copy bukti dari approval
        $bukti = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'pajak/bukti',
            $purchasero->id
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
            $purchasero->id
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
    protected function transferServicePart(Purchasero $purchasero, array $approvalFiles): int
    {
        $sourceData = $purchasero->source_data;
        
        // Copy bukti
        $buktiFiles = [];
        if (!empty($approvalFiles['bukti'])) {
            foreach ($approvalFiles['bukti'] as $file) {
                $finalPath = $this->copyFileToPublic(
                    $file['path'],
                    'service-parts',
                    $purchasero->id
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
     */
    protected function transferGps(Purchasero $purchasero, array $approvalFiles): int
    {
        $sourceData = $purchasero->source_data;
        
        $bukti = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'gps/bukti',
            $purchasero->id
        );
        
        $gps = GpsKendaraan::create([
            'kendaraan_id'         => $sourceData['kendaraan_id'],
            'gps_id'               => $sourceData['gps_id'] ?? null,
            'nomor_imei'           => $sourceData['nomor_imei'] ?? null,
            'tanggal_pemasangan'   => $sourceData['tanggal_pemasangan'] ?? now(),
            'tanggal_berakhir'     => $sourceData['tanggal_berakhir'],
            'tanggal_bayar'        => $sourceData['tanggal_bayar'] ?? now(),
            'biaya'                => $sourceData['biaya'],
            'status'               => $sourceData['status'] ?? 'aktif',
            'keterangan'           => $sourceData['keterangan'] ?? null,
            'bukti'                => $bukti,
        ]);
        
        $kendaraan = Kendaraan::find($sourceData['kendaraan_id']);
        
        $this->createKeuanganRecord(
            'GPS',
            $gps->id,
            $sourceData['biaya'],
            'Pembayaran GPS kendaraan - ' . ($kendaraan->nopol ?? '-')
        );
        
        $this->createBukubesarRecord(
            'GPS',
            $gps->id,
            $sourceData['biaya'],
            'Beban GPS',
            'Auto-posting: Pembayaran GPS kendaraan ' . ($kendaraan->nopol ?? '-')
        );
        
        return $gps->id;
    }

    /**
     * Transfer KIR
     */
    protected function transferKir(Purchasero $purchasero, array $approvalFiles): int
    {
        $sourceData = $purchasero->source_data;
        
        $bukti = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'kir/dokumen',
            $purchasero->id
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
    protected function transferStnk(Purchasero $purchasero, array $approvalFiles): int
    {
        $sourceData = $purchasero->source_data;
        
        $bukti = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'stnk/dokumen',
            $purchasero->id
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
    protected function transferServiceAsuransi(Purchasero $purchasero, array $approvalFiles): int
    {
        $sourceData = $purchasero->source_data;
        
        $bukti = $this->copyBuktiToFinalStorage(
            $approvalFiles['bukti'][0] ?? null,
            'service-asuransi',
            $purchasero->id
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
            $purchasero->id
        );
        
        return $serviceAsuransi->id;
    }

    /**
     * Copy bukti file dari approval storage ke final storage
     */
    protected function copyBuktiToFinalStorage(?array $buktiFile, string $targetFolder, int $purchaseroId): ?string
    {
        if (!$buktiFile || !isset($buktiFile['path'])) {
            return null;
        }
        
        return $this->copyFileToPublic($buktiFile['path'], $targetFolder, $purchaseroId);
    }

    /**
     * Copy file dari storage ke public folder
     */
    protected function copyFileToPublic(string $storagePath, string $publicFolder, int $purchaseroId): string
    {
        // Full path di storage
        $sourceFullPath = storage_path('app/public/' . $storagePath);
        
        // Generate nama file baru dengan prefix purchasero
        $filename = 'purchasero_' . $purchaseroId . '_' . time() . '_' . basename($storagePath);
        
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
        int $purchaseroId
    ): void {
        if (empty($attachmentFiles)) {
            return;
        }
        
        foreach ($attachmentFiles as $file) {
            $finalPath = $this->copyFileToPublic($file['path'], $publicFolder, $purchaseroId);
            
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
