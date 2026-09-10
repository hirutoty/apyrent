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
     * @param array $approvedSourceData source_data yang hanya berisi gps_items yang approved
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
        $nominal    = collect($approvedSourceData['gps_items'] ?? [])->sum(fn($i) => $i['biaya_sewa'] ?? 0);

        // Sematkan bukti per item ke dalam source_data
        if (!empty($perItemBukti)) {
            foreach ($perItemBukti as $idx => $path) {
                if (isset($approvedSourceData['gps_items'][$idx])) {
                    $approvedSourceData['gps_items'][$idx]['bukti_bayar_admin'] = $path;
                }
            }
        }

        // Status: kalau ada item ditolak → Disetujui Sebagian, kalau semua approved → Disetujui
        $status = $hasRejected ? 'Disetujui Sebagian' : 'Disetujui';

        $pembayaran = Pembayaran::create([
            'no_pr'               => $noPR,
            'tanggal'             => now(),
            'departemen'          => $departemen,
            'tipe_pembayaran'     => 'service',
            'pemohon'             => $pemohon,
            'alasan_permintaan'   => $alasanPermintaan,
            'nominal'             => $nominal,
            'nama_bank'           => $approvedSourceData['nama_bank'] ?? null,
            'no_rekening'         => $approvedSourceData['no_rekening'] ?? null,
            'nama_pemilik'        => $approvedSourceData['nama_pemilik'] ?? $approvedSourceData['nama_rekening'] ?? null,
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
            'nama_pemilik' => $sourceData['nama_pemilik'] ?? $sourceData['nama_rekening'] ?? null,
            'keterangan' => $sourceData['keterangan'] ?? $sourceData['informasi'] ?? null,
            'status' => 'Pending',
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
