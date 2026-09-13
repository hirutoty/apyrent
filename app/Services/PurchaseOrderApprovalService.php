<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderApprovalService
{
    protected $interceptor;

    public function __construct(PengeluaranInterceptorService $interceptor)
    {
        $this->interceptor = $interceptor;
    }

    /**
     * Approve dengan subset items (partial approval per-item)
     * Digunakan saat admin memilih item mana yang disetujui vs ditolak
     *
     * @param PurchaseOrder $po        PO yang sudah di-update status-nya
     * @param array $approvedSourceData source_data yang hanya berisi gps_items/parts yang approved
     * @param array $perItemBukti       ['idx' => 'path/to/bukti'] mapping index item ke path file
     * @param string|null $catatan
     * @return Pembayaran
     */
    public function approveWithItems(PurchaseOrder $po, array $approvedSourceData, array $perItemBukti = [], ?string $catatan = null, bool $hasRejected = false): Pembayaran
    {
        $sourceType = $po->source_type;

        // Generate no_pr
        $noPR = $this->generateNoPRFromSourceType($sourceType);

        $alasanPermintaan = $this->buildAlasanPermintaan($sourceType, $approvedSourceData, $po);

        $pemohon    = $approvedSourceData['pemohon'] ?? Auth::user()->nama ?? Auth::user()->email ?? 'N/A';
        $departemen = $approvedSourceData['departemen'] ?? Auth::user()->departemen ?? 'Umum';
        
        // Calculate nominal based on source_type
        if ($sourceType === 'gps' || $sourceType === 'gps_perpanjang') {
            $nominal = collect($approvedSourceData['gps_items'] ?? [])->sum(fn($i) => $i['biaya_sewa'] ?? 0);
        } elseif ($sourceType === 'service_part') {
            $nominal = collect($approvedSourceData['parts'] ?? [])->sum(fn($p) => $p['biaya'] ?? 0);
        } elseif (in_array($sourceType, ['pajak', 'pajak_perpanjang'])) {
            $nominal = floatval($approvedSourceData['nominal'] ?? 0);
        } elseif (in_array($sourceType, ['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'])) {
            $nominal = floatval($approvedSourceData['premi'] ?? $approvedSourceData['biaya'] ?? 0);
        } elseif (in_array($sourceType, ['kir', 'kir_perpanjang', 'stnk'])) {
            $nominal = floatval($approvedSourceData['biaya'] ?? 0);
        } else {
            $nominal = floatval($approvedSourceData['nominal'] ?? $approvedSourceData['biaya'] ?? 0);
        }

        // Sematkan bukti per item ke dalam source_data
        if (!empty($perItemBukti)) {
            if ($sourceType === 'gps') {
                foreach ($perItemBukti as $idx => $path) {
                    if (isset($approvedSourceData['gps_items'][$idx])) {
                        $approvedSourceData['gps_items'][$idx]['bukti_bayar_admin'] = $path;
                    }
                }
            } elseif ($sourceType === 'service_part') {
                foreach ($perItemBukti as $idx => $path) {
                    if (isset($approvedSourceData['parts'][$idx])) {
                        $approvedSourceData['parts'][$idx]['bukti_bayar_admin'] = $path;
                    }
                }
            }
        }

        // Extract bank info based on source_type (for service_part: first part's bank, for GPS: source level)
        $namaBank = null;
        $noRekening = null;
        $namaPemilik = null;
        
        if ($sourceType === 'service_part') {
            // Service part: bank info is per-part, take from first approved part
            $firstPart = ($approvedSourceData['parts'] ?? [])[0] ?? null;
            if ($firstPart) {
                $namaBank = $firstPart['nama_bank'] ?? null;
                $noRekening = $firstPart['no_rekening'] ?? null;
                $namaPemilik = $firstPart['nama_rekening'] ?? $firstPart['nama_pemilik'] ?? null;
            }
        } else {
            // GPS: bank info at source level
            $namaBank = $approvedSourceData['nama_bank'] ?? null;
            $noRekening = $approvedSourceData['no_rekening'] ?? null;
            $namaPemilik = $approvedSourceData['nama_pemilik'] ?? $approvedSourceData['nama_rekening'] ?? null;
        }

        // Status: Pembayaran dibuat dengan status 'Diajukan' (bukan langsung Disetujui)
        // agar masih perlu approval di halaman Pembayaran
        $status = 'Diajukan';

        $pembayaran = Pembayaran::create([
            'no_pr'               => $noPR,
            'tanggal'             => now(),
            'departemen'          => $departemen,
            'tipe_pembayaran'     => 'service',
            'pemohon'             => $pemohon,
            'alasan_permintaan'   => $alasanPermintaan,
            'nominal'             => $nominal,
            'nama_bank'           => $namaBank,
            'no_rekening'         => $noRekening,
            'nama_pemilik'        => $namaPemilik,
            'keterangan'          => $catatan ?: ($approvedSourceData['keterangan'] ?? null),
            'status'              => $status,
            'disetujui_oleh'      => Auth::user()->nama ?? Auth::user()->email,
            'tanggal_persetujuan' => now(),
            'source_type'         => $sourceType,
            'source_data'         => $approvedSourceData,
            'target_id'           => null,
            'can_edit'            => false,
        ]);

        $po->update(['pembayaran_id' => $pembayaran->id]);

        // Update GpsKendaraan approved items ke 'Diajukan ke Pembayaran'
        if ($po->source_type === 'gps') {
            $origSourceData = $po->source_data ?? [];
            $allGpsItems    = $origSourceData['gps_items'] ?? [];
            $approvedItems  = $approvedSourceData['gps_items'] ?? [];
            $kendaraanId    = $origSourceData['kendaraan_id'] ?? null;
            $recordIds      = $origSourceData['gps_record_ids'] ?? [];

            // Kumpulkan gps_id+type dari approved items
            $approvedKeys = array_map(fn($i) => ($i['gps_id'] ?? '') . '_' . ($i['type'] ?? ''), $approvedItems);

            if (!empty($recordIds)) {
                foreach ($allGpsItems as $idx => $item) {
                    $key      = ($item['gps_id'] ?? '') . '_' . ($item['type'] ?? '');
                    $recordId = $recordIds[$idx] ?? null;
                    if ($recordId && in_array($key, $approvedKeys)) {
                        \App\Models\GpsKendaraan::where('id', $recordId)
                            ->where('persetujuan', 'Pending')
                            ->update([
                                'persetujuan'   => 'Diajukan ke Pembayaran',
                                'pembayaran_id' => $pembayaran->id,
                            ]);
                    }
                }
            } elseif ($kendaraanId) {
                foreach ($approvedItems as $item) {
                    \App\Models\GpsKendaraan::where('kendaraan_id', $kendaraanId)
                        ->where('gps_id', $item['gps_id'] ?? null)
                        ->where('type', $item['type'] ?? null)
                        ->where('persetujuan', 'Pending')
                        ->update([
                            'persetujuan'   => 'Diajukan ke Pembayaran',
                            'pembayaran_id' => $pembayaran->id,
                        ]);
                }
            }
        }
        // service_part: no external records to update (parts are embedded in source_data)

        return $pembayaran;
    }

    /**
     * Approve Purchase Order dan auto-create Pembayaran
     *
     * @param PurchaseOrder $po
     * @param array $approvalData ['catatan' => optional notes]
     * @return Pembayaran
     * @throws \Exception
     */
    public function approve(PurchaseOrder $po, array $approvalData = []): Pembayaran
    {
        if (!$po->isPending()) {
            throw new \Exception('Hanya Purchase Order dengan status Pending yang dapat disetujui.');
        }

        DB::beginTransaction();

        try {
            // Update Purchase Order status
            $po->update([
                'status' => 'Disetujui',
                'disetujui_oleh' => Auth::id(),
                'tanggal_persetujuan' => now(),
                'catatan_approval' => $approvalData['catatan'] ?? null,
            ]);

            // Auto-create Pembayaran from PO source_data
            $pembayaran = $this->createPembayaranFromPO($po);

            // Link Pembayaran to PO
            $po->update(['pembayaran_id' => $pembayaran->id]);

            // Update semua GpsKendaraan terkait PO ini ke 'Diajukan ke Pembayaran'
            // sehingga record muncul di tabel gps_kendaraan dengan status tersebut
            if ($po->source_type === 'gps') {
                $sourceData  = $po->source_data ?? [];
                $recordIds   = $sourceData['gps_record_ids'] ?? [];
                $gpsItems    = $sourceData['gps_items'] ?? [];

                if (!empty($recordIds)) {
                    \App\Models\GpsKendaraan::whereIn('id', $recordIds)
                        ->where('persetujuan', 'Pending')
                        ->update([
                            'persetujuan'   => 'Diajukan ke Pembayaran',
                            'pembayaran_id' => $pembayaran->id,
                        ]);
                } else {
                    // Fallback: cari by kendaraan_id + type dari gps_items
                    $kendaraanId = $sourceData['kendaraan_id'] ?? null;
                    if ($kendaraanId && !empty($gpsItems)) {
                        foreach ($gpsItems as $item) {
                            \App\Models\GpsKendaraan::where('kendaraan_id', $kendaraanId)
                                ->where('gps_id', $item['gps_id'] ?? null)
                                ->where('type', $item['type'] ?? null)
                                ->where('persetujuan', 'Pending')
                                ->update([
                                    'persetujuan'   => 'Diajukan ke Pembayaran',
                                    'pembayaran_id' => $pembayaran->id,
                                ]);
                        }
                    }
                }
            }

            // Update record Asuransi terkait PO ini ke 'Diajukan ke Pembayaran'
            if ($po->source_type === 'asuransi_kendaraan') {
                $sourceData = $po->source_data ?? [];
                $existingId = $sourceData['existing_record_id'] ?? null;
                if ($existingId) {
                    \App\Models\AsuransiKendaraan::where('id', $existingId)
                        ->where('persetujuan', 'Pending')
                        ->update([
                            'persetujuan'   => 'Diajukan ke Pembayaran',
                            'pembayaran_id' => $pembayaran->id,
                        ]);
                }
            }

            // Update record Pajak terkait PO ini ke 'Diajukan ke Pembayaran'
            if ($po->source_type === 'pajak') {
                $sourceData = $po->source_data ?? [];
                $existingId = $sourceData['existing_record_id'] ?? null;
                if ($existingId) {
                    \App\Models\PajakKendaraan::where('id', $existingId)
                        ->where('persetujuan', 'Pending')
                        ->update([
                            'persetujuan'   => 'Diajukan ke Pembayaran',
                            'pembayaran_id' => $pembayaran->id,
                        ]);
                }
            }

            // Update record KIR terkait PO ini ke 'Diajukan ke Pembayaran'
            if ($po->source_type === 'kir') {
                $sourceData = $po->source_data ?? [];
                $existingId = $sourceData['existing_record_id'] ?? null;
                if ($existingId) {
                    \App\Models\Kir::where('id', $existingId)
                        ->where('persetujuan', 'Pending')
                        ->update([
                            'persetujuan'   => 'Diajukan ke Pembayaran',
                            'pembayaran_id' => $pembayaran->id,
                        ]);
                }
            }

            DB::commit();

            return $pembayaran;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving PO #' . $po->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reject Purchase Order
     *
     * @param PurchaseOrder $po
     * @param string $catatan
     * @return void
     * @throws \Exception
     */
    public function reject(PurchaseOrder $po, string $catatan): void
    {
        if (!$po->isPending()) {
            throw new \Exception('Hanya Purchase Order dengan status Pending yang dapat ditolak.');
        }

        DB::beginTransaction();

        try {
            $po->update([
                'status' => 'Ditolak',
                'disetujui_oleh' => Auth::id(),
                'tanggal_persetujuan' => now(),
                'catatan_approval' => $catatan,
                'can_edit' => true, // Allow resubmit
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting PO #' . $po->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Resubmit rejected Purchase Order dengan data baru
     *
     * @param PurchaseOrder $po
     * @param array $newData
     * @param Request $request
     * @return PurchaseOrder
     * @throws \Exception
     */
    public function resubmit(PurchaseOrder $po, array $newData, Request $request): PurchaseOrder
    {
        if (!$po->isRejected()) {
            throw new \Exception('Hanya Purchase Order yang ditolak yang dapat diajukan ulang.');
        }

        if (!$po->can_edit) {
            throw new \Exception('Purchase Order ini tidak dapat diedit.');
        }

        DB::beginTransaction();

        try {
            // Delete old temp files
            $this->deleteTemporaryFiles($po->id);

            // Upload new temp files
            $uploadedFiles = $this->interceptor->uploadTemporaryFiles($request, $po->id, 'purchase_order');

            // Extract nominal total dari data baru
            $sourceType = $po->source_type;
            $nominal = $this->extractNominalFromData($sourceType, $newData);

            // Extract vendor (opsional)
            $vendor = $newData['vendor'] ?? $po->vendor ?? 'Vendor ' . ucfirst(str_replace('_', ' ', $sourceType));

            // Extract total items
            $totalItems = $this->extractTotalItemsFromData($sourceType, $newData);

            // Untuk GPS: hapus record Ditolak lama, buat records Pending baru
            $newGpsRecordIds = [];
            if ($sourceType === 'gps') {
                $oldSourceData = $po->source_data ?? [];
                $oldRecordIds  = $oldSourceData['gps_record_ids'] ?? [];

                // Untuk GPS: ambil lampiran lama SEBELUM hapus record, lalu hapus record Ditolak
                $oldAttachmentsByIdx = [];
                foreach ($oldRecordIds as $idx => $oldId) {
                    $oldAttachmentsByIdx[$idx] = \App\Models\Attachment::where('relation_type', 'gps')
                        ->where('relation_id', $oldId)
                        ->get();
                }

                // Hapus GPS records lama yang Ditolak terkait PO ini
                if (!empty($oldRecordIds)) {
                    \App\Models\GpsKendaraan::whereIn('id', $oldRecordIds)
                        ->where('persetujuan', 'Ditolak')
                        ->delete();
                }

                // Buat GPS records baru dengan status Pending
                // Lampiran lama dipindahkan ke record baru (bukan dihapus)
                $gpsItems    = $newData['gps_items'] ?? [];
                $kendaraanId = $newData['kendaraan_id'] ?? $oldSourceData['kendaraan_id'] ?? null;
                $tanggalBayar = $newData['tanggal_bayar'] ?? null;
                $tanggalHabis = $newData['tanggal_habis'] ?? null;
                $durasiBulan  = ($tanggalBayar && $tanggalHabis)
                    ? max((int) \Carbon\Carbon::parse($tanggalBayar)->diffInMonths(\Carbon\Carbon::parse($tanggalHabis)), 1)
                    : 12;

                foreach ($gpsItems as $itemIdx => $item) {
                    // Ambil lampiran lama yang sudah dikumpulkan sebelum record dihapus
                    $oldAttachments = $oldAttachmentsByIdx[$itemIdx] ?? collect();

                    $gpsRecord = \App\Models\GpsKendaraan::create([
                        'pembayaran_id' => null,
                        'kendaraan_id'  => $kendaraanId,
                        'gps_id'        => $item['gps_id'] ?? null,
                        'type'          => $item['type'] ?? null,
                        'status_gps'    => 'nonaktif',
                        'tanggal_pasang'=> $tanggalBayar,
                        'tanggal_habis' => $tanggalHabis,
                        'tanggal_bayar' => $tanggalBayar,
                        'biaya_sewa'    => (int) ($item['biaya_sewa'] ?? 0),
                        'durasi_bulan'  => $durasiBulan,
                        'status_sewa'   => 'tidak_aktif',
                        'bukti_bayar'   => null,
                        'keterangan'    => $newData['keterangan'] ?? null,
                        'nama_bank'     => $item['nama_bank'] ?? null,
                        'no_rekening'   => $item['no_rekening'] ?? null,
                        'nama_pemilik'  => $item['nama_pemilik'] ?? null,
                        'persetujuan'   => 'Pending',
                    ]);
                    $newGpsRecordIds[] = $gpsRecord->id;

                    // Pindahkan lampiran lama ke record baru (update relation_id)
                    if ($oldAttachments->isNotEmpty()) {
                        \App\Models\Attachment::whereIn('id', $oldAttachments->pluck('id'))
                            ->update(['relation_id' => $gpsRecord->id]);
                    }

                    // Simpan lampiran baru yang diupload saat resubmit (ditambahkan, bukan mengganti)
                    if ($request->hasFile("gps_items.{$itemIdx}.lampiran")) {
                        $lampiranDir = public_path('gps/attachments');
                        if (!file_exists($lampiranDir)) mkdir($lampiranDir, 0777, true);

                        foreach ($request->file("gps_items.{$itemIdx}.lampiran") as $lampiranFile) {
                            if (!$lampiranFile->isValid()) continue;
                            $origName = $lampiranFile->getClientOriginalName();
                            $ext      = $lampiranFile->getClientOriginalExtension();
                            $fileSize = $lampiranFile->getSize();
                            $filename = time() . '_' . uniqid() . '.' . $ext;
                            $lampiranFile->move($lampiranDir, $filename);
                            \App\Models\Attachment::create([
                                'relation_type' => 'gps',
                                'relation_id'   => $gpsRecord->id,
                                'file_name'     => $origName,
                                'file_path'     => 'gps/attachments/' . $filename,
                                'file_type'     => $ext,
                                'file_size'     => $fileSize,
                            ]);
                        }
                    }
                }

                $newData['gps_record_ids'] = $newGpsRecordIds;
            }

            // Update PO dengan data baru
            $po->update([
                'source_data' => array_merge($newData, ['temp_files' => $uploadedFiles]),
                'vendor' => $vendor,
                'total_barang' => $totalItems,
                'total_harga' => $nominal,
                'tanggal_po' => now()->toDateString(),
                'status' => 'Pending',
                'disetujui_oleh' => null,
                'tanggal_persetujuan' => null,
                'catatan_approval' => null,
                'can_edit' => false,
                'terakhir_diajukan' => now(),
            ]);

            DB::commit();

            return $po;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error resubmitting PO #' . $po->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create Pembayaran from approved PurchaseOrder
     *
     * @param PurchaseOrder $po
     * @return Pembayaran
     */
    protected function createPembayaranFromPO(PurchaseOrder $po): Pembayaran
    {
        $sourceData = $po->source_data;
        $sourceType = $po->source_type;

        // Generate no_pr
        $noPR = $this->generateNoPRFromSourceType($sourceType);

        // Build alasan permintaan
        $alasanPermintaan = $this->buildAlasanPermintaan($sourceType, $sourceData, $po);

        // Get user info (pemohon dari source_data atau current user)
        $pemohon = $sourceData['pemohon'] ?? Auth::user()->nama ?? Auth::user()->email ?? 'N/A';
        $departemen = $sourceData['departemen'] ?? Auth::user()->departemen ?? 'Umum';

        // Create Pembayaran
        $pembayaran = Pembayaran::create([
            'no_pr' => $noPR,
            'tanggal' => now(),
            'departemen' => $departemen,
            'tipe_pembayaran' => 'service', // All vehicle expenses use 'service' type
            'pemohon' => $pemohon,
            'alasan_permintaan' => $alasanPermintaan,
            'nominal' => $po->total_harga,
            'nama_bank' => $sourceData['nama_bank'] ?? null,
            'no_rekening' => $sourceData['no_rekening'] ?? null,
            'nama_pemilik' => $sourceData['nama_pemilik'] ?? $sourceData['nama_rekening'] ?? $sourceData['nama_rekening'] ?? null,
            'keterangan' => $sourceData['keterangan'] ?? $sourceData['informasi'] ?? null,
            'status' => 'Diajukan', // PO sudah diapprove superadmin → langsung Diajukan ke Pembayaran
            'terakhir_diajukan' => now(),
            'source_type' => $sourceType,
            'source_data' => $sourceData,
            'target_id' => null, // Will be filled after Pembayaran approved
            'can_edit' => false,
        ]);

        return $pembayaran;
    }

    /**
     * Generate No PR based on source type
     */
    protected function generateNoPRFromSourceType(string $sourceType): string
    {
        $typeMap = [
            'gps' => 'GPS',
            'asuransi_kendaraan' => 'ASR',
            'pajak' => 'PJK',
            'kir' => 'KIR',
            'stnk' => 'STN',
            'service_part' => 'SVC',
            'service_asuransi' => 'SAS',
        ];

        $typeCode = $typeMap[$sourceType] ?? 'PGL';

        // Get last PR for this type
        $lastPR = Pembayaran::where('source_type', $sourceType)
            ->where('no_pr', 'like', "PR-{$typeCode}-%")
            ->orderBy('id', 'desc')
            ->first();

        $increment = 1;

        if ($lastPR) {
            preg_match('/PR-' . $typeCode . '-(\d+)/', $lastPR->no_pr, $matches);
            if (isset($matches[1])) {
                $increment = intval($matches[1]) + 1;
            }
        }

        return sprintf('PR-%s-%03d', $typeCode, $increment);
    }

    /**
     * Build alasan permintaan description
     */
    protected function buildAlasanPermintaan(string $sourceType, array $sourceData, PurchaseOrder $po): string
    {
        return match ($sourceType) {
            'gps' => 'Pembayaran GPS Kendaraan (via PO #' . $po->po_id . ') - ' . ($sourceData['keterangan'] ?? 'N/A'),
            'asuransi_kendaraan' => 'Pembayaran Asuransi Kendaraan (via PO #' . $po->po_id . ') - ' . ($sourceData['keterangan'] ?? 'N/A'),
            'pajak' => 'Pembayaran Pajak Kendaraan (via PO #' . $po->po_id . ') - ' . ($sourceData['jenis_pajak'] ?? 'N/A'),
            'kir' => 'Pembayaran KIR Kendaraan (via PO #' . $po->po_id . ')',
            'stnk' => 'Pembayaran STNK Kendaraan (via PO #' . $po->po_id . ')',
            'service_part' => 'Pembelian Service Part (via PO #' . $po->po_id . ')',
            'service_asuransi' => 'Klaim Asuransi Service (via PO #' . $po->po_id . ')',
            default => 'Pengeluaran Kendaraan (via PO #' . $po->po_id . ')',
        };
    }

    /**
     * Extract nominal from source data based on type
     */
    protected function extractNominalFromData(string $sourceType, array $data): float
    {
        return match ($sourceType) {
            'gps' => collect($data['gps_items'] ?? [])->sum(fn($item) => floatval($item['biaya_sewa'] ?? 0)),
            'asuransi_kendaraan' => floatval($data['biaya'] ?? $data['premi'] ?? 0),
            'pajak' => floatval($data['nominal'] ?? 0),
            'kir' => floatval($data['biaya'] ?? 0),
            'stnk' => floatval($data['biaya'] ?? 0),
            'service_part' => collect($data['parts'] ?? [])->sum(fn($p) => floatval($p['biaya'] ?? 0)),
            'service_asuransi' => floatval($data['biaya'] ?? 0),
            default => 0,
        };
    }

    /**
     * Extract total items count from source data
     */
    protected function extractTotalItemsFromData(string $sourceType, array $data): int
    {
        return match ($sourceType) {
            'gps' => count($data['gps_items'] ?? []),
            'service_part' => count($data['parts'] ?? []),
            default => 1,
        };
    }

    /**
     * Delete temporary files for PO
     */
    protected function deleteTemporaryFiles(int $poId): void
    {
        $tempDir = "purchase_order/temp/{$poId}";

        if (Storage::disk('public')->exists($tempDir)) {
            Storage::disk('public')->deleteDirectory($tempDir);
        }
    }
}
