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

        $pemohon    = $approvedSourceData['pemohon'] ?? Auth::user()->name ?? Auth::user()->email ?? 'N/A';
        $departemen = $approvedSourceData['departemen'] ?? Auth::user()->departemen ?? '-';
        
        // Calculate nominal based on source_type (hanya approved items)
        if ($sourceType === 'gps' || $sourceType === 'gps_perpanjang') {
            $nominal = collect($approvedSourceData['gps_items'] ?? [])->sum(fn($i) => $i['biaya_sewa'] ?? 0);
        } elseif ($sourceType === 'service_part') {
            $nominal = collect($approvedSourceData['parts'] ?? [])->sum(fn($p) => $p['biaya'] ?? 0);
        } elseif ($sourceType === 'service_incident') {
            $nominal = floatval($approvedSourceData['total_biaya_override'] ?? 0) > 0
                ? floatval($approvedSourceData['total_biaya_override'])
                : collect($approvedSourceData['parts'] ?? [])->sum(fn($p) => $p['biaya'] ?? 0);
        } elseif (in_array($sourceType, ['pajak', 'pajak_perpanjang'])) {
            $nominal = floatval($approvedSourceData['nominal'] ?? 0);
        } elseif (in_array($sourceType, ['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'])) {
            $nominal = floatval($approvedSourceData['premi'] ?? $approvedSourceData['biaya'] ?? 0);
        } elseif (in_array($sourceType, ['kir', 'kir_perpanjang', 'stnk'])) {
            $nominal = floatval($approvedSourceData['biaya'] ?? 0);
        } elseif ($sourceType === 'service_asuransi') {
            // Hitung hanya dari kejadians yang ada di approvedSourceData (sudah difilter approved saja)
            $nominal = collect($approvedSourceData['kejadians'] ?? [])->sum(fn($k) => $k['biaya'] ?? 0);
        } else {
            $nominal = floatval($approvedSourceData['nominal'] ?? $approvedSourceData['biaya'] ?? 0);
        }

        // nominal_original = total semua item dari PO asli (approved + rejected)
        // Ambil dari source_data PO original sebelum difilter
        $poOriginalData = $po->source_data ?? [];
        if ($sourceType === 'gps' || $sourceType === 'gps_perpanjang') {
            $nominalOriginal = collect($poOriginalData['gps_items'] ?? [])->sum(fn($i) => $i['biaya_sewa'] ?? 0);
        } elseif (in_array($sourceType, ['service_part', 'service_incident'])) {
            $nominalOriginal = collect($poOriginalData['parts'] ?? [])->sum(fn($p) => $p['biaya'] ?? 0);
        } elseif ($sourceType === 'service_asuransi') {
            $nominalOriginal = collect($poOriginalData['kejadians'] ?? [])->sum(fn($k) => $k['biaya'] ?? 0);
        } else {
            $nominalOriginal = $nominal; // non-partial: sama dengan nominal
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
        } elseif ($sourceType === 'service_incident') {
            // Service incident: bank info is per-part, take from first approved part
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

        // Hapus item_decisions dari source_data Pembayaran — audit trail keputusan
        // per-item (approved/rejected) hanya relevan di PO, bukan di Pembayaran.
        // Jika ikut tersimpan, summary card Pembayaran akan salah menghitung
        // item "Ditolak" dari entries rejected milik PO.
        $sourceDataForPembayaran = $approvedSourceData;
        unset($sourceDataForPembayaran['item_decisions']);

        // ── Inject linked record IDs dari PO ke source_data Pembayaran ──────────
        // service_asuransi_id dan service_incident_id hanya ada di PO source_data,
        // tapi dibutuhkan oleh showApprovalModal (fallback DB filter) dan
        // transferServiceAsuransi/Incident (lookup record lama). Inject di sini agar
        // Pembayaran selalu membawa referensi ke record yang benar.
        $poSourceDataForInject = $po->source_data ?? [];
        if ($sourceType === 'service_asuransi' && !empty($poSourceDataForInject['service_asuransi_id'])) {
            $sourceDataForPembayaran['service_asuransi_id'] = $poSourceDataForInject['service_asuransi_id'];
        }
        if ($sourceType === 'service_incident' && !empty($poSourceDataForInject['service_incident_id'])) {
            $sourceDataForPembayaran['service_incident_id'] = $poSourceDataForInject['service_incident_id'];
        }

        // ── Jika PO sudah punya Pembayaran (resubmit partial round ke-2+), UPDATE PR lama ──
        // Berlaku untuk service_part dan service_incident saja.
        // service_asuransi selalu buat Pembayaran baru per round agar setiap batch
        // kejadian punya PR sendiri — penggabungan ke 1 row dilakukan di view service_asuransi,
        // bukan di level Pembayaran.
        $existingPembayaran = null;
        if (
            in_array($sourceType, ['service_part', 'service_incident'])
            && !empty($po->pembayaran_id)
        ) {
            $existingPembayaran = Pembayaran::find($po->pembayaran_id);
        }

        if ($existingPembayaran) {
            // ── UPDATE Pembayaran lama ──────────────────────────────────────
            // Merge items baru yang approved ke dalam source_data yang sudah ada.
            // Items lama (yang sudah approved sebelumnya) tetap dipertahankan.
            $existingSourceData = $existingPembayaran->source_data ?? [];

            // Gabungkan parts: pakai index original dari approvedSourceData agar
            // tidak ada duplikat dan index tetap konsisten dengan PO.
            $existingParts = $existingSourceData['parts'] ?? [];
            foreach ($approvedSourceData['parts'] ?? [] as $idx => $part) {
                // Hapus flag rejection lama jika ada
                unset($part['status_approval'], $part['catatan_penolakan']);
                $existingParts[$idx] = $part;
            }
            $existingSourceData['parts'] = $existingParts;

            // Nominal: akumulasi dari semua parts yang ada di PR (approved lama + approved baru)
            $nominalAkumulasi = collect($existingParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));

            $existingPembayaran->update([
                'source_data'         => $existingSourceData,
                'nominal'             => $nominalAkumulasi,
                'nominal_original'    => $nominalOriginal,
                'status'              => 'Diajukan',
                'can_edit'            => false,
                'catatan'             => null,
                'terakhir_diajukan'   => now(),
            ]);

            $pembayaran = $existingPembayaran;
            // pembayaran_id PO tidak perlu diupdate — sudah menunjuk ke PR yang sama

        } else {
            // ── CREATE Pembayaran baru (first approval atau source type non-partial) ──
            $pembayaran = Pembayaran::create([
                'no_pr'               => $noPR,
                'tanggal'             => now(),
                'departemen'          => $departemen,
                'tipe_pembayaran'     => 'service',
                'pemohon'             => $pemohon,
                'alasan_permintaan'   => $alasanPermintaan,
                'nominal'             => $nominal,
                'nominal_original'    => $nominalOriginal,
                'nama_bank'           => $namaBank,
                'no_rekening'         => $noRekening,
                'nama_pemilik'        => $namaPemilik,
                'keterangan'          => $catatan ?: ($approvedSourceData['keterangan'] ?? null),
                'status'              => $status,
                'disetujui_oleh'      => Auth::user()->nama ?? Auth::user()->email,
                'tanggal_persetujuan' => now(),
                'source_type'         => $sourceType,
                'source_data'         => $sourceDataForPembayaran,
                'target_id'           => null,
                'can_edit'            => false,
            ]);

            // ── Populate item_pr_numbers: map each item index → PR number ──────────
            $this->populateItemPrNumbers($pembayaran, $sourceType, $sourceDataForPembayaran);

            $po->update(['pembayaran_id' => $pembayaran->id]);
        }

        // ── CREATE ServiceHistory draft BEFORE updateLinkedRecord untuk service_part ──
        // Sequence penting: ServiceHistory harus sudah ada sebelum updateLinkedRecord
        // dipanggil, karena updateLinkedRecord akan mencari ServiceHistory dengan
        // pembayaran_id dan mengupdate parts-nya.
        if ($sourceType === 'service_part') {
            $poController = app(\App\Http\Controllers\Admin\PurchaseOrderController::class);
            $poController->createServiceHistoryDraft($pembayaran, $sourceDataForPembayaran);
        }

        // Update linked records (GPS / pajak / asuransi_kendaraan / kir)
        // For GPS via approveWithItems, pass approvedSourceData so only approved items are updated
        $this->updateLinkedRecord($po, $pembayaran, $approvedSourceData);

        // service_incident: ServiceHistory tidak dibuat di sini (incident langsung buat parts)

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

            // Update semua linked records (GPS / pajak / asuransi_kendaraan / kir)
            $this->updateLinkedRecord($po, $pembayaran);

            // Update keterangan ServicePart lama ke 'diajukan ke pembayaran' jika ada replace_part_id
            if ($po->source_type === 'service_part') {
                $sourceData = $po->source_data ?? [];
                $parts      = $sourceData['parts'] ?? [];
                foreach ($parts as $partData) {
                    if (!empty($partData['replace_part_id'])) {
                        \App\Models\ServicePart::where('id', (int) $partData['replace_part_id'])
                            ->update(['keterangan' => 'diajukan ke pembayaran']);
                    }
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

            // Update keterangan ServicePart lama ke 'request ditolak' jika ada replace_part_id
            if ($po->source_type === 'service_part') {
                $sourceData = $po->source_data ?? [];
                $parts      = $sourceData['parts'] ?? [];
                foreach ($parts as $partData) {
                    if (!empty($partData['replace_part_id'])) {
                        \App\Models\ServicePart::where('id', (int) $partData['replace_part_id'])
                            ->update(['keterangan' => 'request ditolak']);
                    }
                }
            }

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

            // ── SERVICE INCIDENT: tidak perlu hapus/buat ulang record ──────────
            // Dengan alur baru, record ServiceIncident sudah ada sejak PO sebelumnya disetujui.
            // Saat resubmit, PurchaseOrderController sudah handle reset parts + header di resubmitServiceAsuransi/incident.
            // Di sini cukup pastikan service_incident_id tersimpan di data baru jika ada.
            if ($sourceType === 'service_incident') {
                $oldSourceData = $po->source_data ?? [];
                $incidentId    = $oldSourceData['service_incident_id'] ?? null;
                if ($incidentId) {
                    $newData['service_incident_id'] = $incidentId;
                }
            }

            // ── GPS: tidak perlu hapus/buat ulang record ────────────────────────
            // Dengan alur baru, record GPS dibuat saat PO disetujui (updateLinkedRecord).
            // Saat resubmit PO yang Ditolak (sebelum ada Pembayaran), belum ada record GPS.
            // Cukup upload lampiran baru ke temp storage, record dibuat ulang saat PO approve berikutnya.
            if ($sourceType === 'gps' && $request->hasAny(['gps_items'])) {
                $gpsItems = $newData['gps_items'] ?? [];
                foreach ($gpsItems as $itemIdx => $item) {
                    if ($request->hasFile("gps_items.{$itemIdx}.lampiran")) {
                        // Lampiran baru diunggah ke temp_files — sudah ditangani oleh uploadTemporaryFiles() di atas
                        // Tidak perlu buat record GPS di sini
                    }
                }
                // Hapus gps_record_ids lama dari source_data karena record sudah tidak ada
                unset($newData['gps_record_ids']);
            }

            // ── Preserve locked_approved_idx dan linked record IDs dari partial approval sebelumnya ──
            // Saat PO partial approved → resubmit, field-field berikut harus tetap ada:
            // - locked_approved_idx   : agar items yang sudah approved tidak muncul lagi di approval modal
            // - service_asuransi_id   : agar updateLinkedRecord tidak buat record ServiceAsuransi baru
            // - service_incident_id   : agar updateLinkedRecord tidak buat record ServiceIncident baru
            // item_decisions di-clear karena cycle approval baru dimulai
            $oldSourceData = $po->source_data ?? [];
            $preservedFields = [
                'locked_approved_idx'  => $oldSourceData['locked_approved_idx'] ?? [],
                'service_asuransi_id'  => $oldSourceData['service_asuransi_id'] ?? null,
                'service_incident_id'  => $oldSourceData['service_incident_id'] ?? null,
            ];
            // Hapus null values agar tidak mengotori source_data dengan key kosong
            $preservedFields = array_filter($preservedFields, fn($v) => $v !== null);

            // Update PO dengan data baru + preserve locked items
            $po->update([
                'source_data' => array_merge($newData, ['temp_files' => $uploadedFiles], $preservedFields),
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
        $pemohon = $sourceData['pemohon'] ?? Auth::user()->name ?? Auth::user()->email ?? 'N/A';
        $departemen = $sourceData['departemen'] ?? Auth::user()->departemen ?? '-';

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

        // ── Populate item_pr_numbers per item ───────────────────────────────────
        $this->populateItemPrNumbers($pembayaran, $sourceType, $sourceData);

        return $pembayaran;
    }

    /**
     * Populate item_pr_numbers di source_data Pembayaran.
     * Map setiap index item → no_pr dokumen Pembayaran.
     * Digunakan saat Pembayaran baru dibuat agar setiap item bisa dilacak PR number-nya.
     */
    protected function populateItemPrNumbers(Pembayaran $pembayaran, string $sourceType, array $sourceData): void
    {
        $multiItemTypes = ['gps', 'gps_perpanjang', 'service_part', 'service_asuransi', 'service_incident'];
        if (!in_array($sourceType, $multiItemTypes)) {
            return; // single-item types tidak perlu
        }

        $itemCount = match ($sourceType) {
            'gps', 'gps_perpanjang'             => count($sourceData['gps_items'] ?? []),
            'service_part', 'service_incident'  => count($sourceData['parts'] ?? []),
            'service_asuransi'                  => count($sourceData['kejadians'] ?? []),
            default                             => 0,
        };

        if ($itemCount === 0) return;

        $itemPrNumbers = [];
        for ($i = 0; $i < $itemCount; $i++) {
            $itemPrNumbers[$i] = $pembayaran->no_pr;
        }

        $sd = $pembayaran->source_data ?? [];
        $sd['item_pr_numbers'] = $itemPrNumbers;
        $pembayaran->update(['source_data' => $sd]);
    }

    /**
     * Generate No PR based on source type
     */
    protected function generateNoPRFromSourceType(string $sourceType): string
    {
        $typeMap = [
            'gps'               => 'GPS',
            'asuransi_kendaraan'=> 'ASR',
            'pajak'             => 'PJK',
            'kir'               => 'KIR',
            'stnk'              => 'STN',
            'service_part'      => 'SVC',
            'service_asuransi'  => 'SAS',
            'service_incident'  => 'SIN',
        ];

        $typeCode = $typeMap[$sourceType] ?? 'PGL';

        // Ambil nomor terbesar dari SEMUA Pembayaran dengan prefix ini,
        // tanpa filter source_type agar tidak ada nomor yang terlewat/duplikat.
        // Gunakan orderBy id desc sebagai proxy untuk nomor terbesar —
        // nomor PR selalu increment seiring id, sehingga id terbesar = nomor terbesar.
        $lastPR = Pembayaran::where('no_pr', 'like', "PR-{$typeCode}-%")
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
            'gps'               => 'Pembayaran GPS Kendaraan (via PO #' . $po->po_id . ') - ' . ($sourceData['keterangan'] ?? 'N/A'),
            'asuransi_kendaraan'=> 'Pembayaran Asuransi Kendaraan (via PO #' . $po->po_id . ') - ' . ($sourceData['keterangan'] ?? 'N/A'),
            'pajak'             => 'Pembayaran Pajak Kendaraan (via PO #' . $po->po_id . ') - ' . ($sourceData['jenis_pajak'] ?? 'N/A'),
            'kir'               => 'Pembayaran KIR Kendaraan (via PO #' . $po->po_id . ')',
            'stnk'              => 'Pembayaran STNK Kendaraan (via PO #' . $po->po_id . ')',
            'service_part'      => 'Pembelian Service Part (via PO #' . $po->po_id . ')',
            'service_asuransi'  => 'Klaim Asuransi Service (via PO #' . $po->po_id . ')',
            'service_incident'  => 'Service Insiden Kendaraan (via PO #' . $po->po_id . ') - ' . ($sourceData['keterangan'] ?? 'N/A'),
            default             => 'Pengeluaran Kendaraan (via PO #' . $po->po_id . ')',
        };
    }

    /**
     * Extract nominal from source data based on type
     */
    protected function extractNominalFromData(string $sourceType, array $data): float
    {
        return match ($sourceType) {
            'gps'               => collect($data['gps_items'] ?? [])->sum(fn($item) => floatval($item['biaya_sewa'] ?? 0)),
            'asuransi_kendaraan'=> floatval($data['biaya'] ?? $data['premi'] ?? 0),
            'pajak'             => floatval($data['nominal'] ?? 0),
            'kir'               => floatval($data['biaya'] ?? 0),
            'stnk'              => floatval($data['biaya'] ?? 0),
            'service_part'      => collect($data['parts'] ?? [])->sum(fn($p) => floatval($p['biaya'] ?? 0)),
            'service_asuransi'  => collect($data['kejadians'] ?? [])->sum(fn($k) => floatval($k['biaya'] ?? 0)),
            'service_incident'  => floatval($data['total_biaya_override'] ?? 0) > 0
                                    ? floatval($data['total_biaya_override'])
                                    : collect($data['parts'] ?? [])->sum(fn($p) => floatval($p['biaya'] ?? 0)),
            default             => 0,
        };
    }

    /**
     * Extract total items count from source data
     */
    protected function extractTotalItemsFromData(string $sourceType, array $data): int
    {
        return match ($sourceType) {
            'gps'              => count($data['gps_items'] ?? []),
            'service_part'     => count($data['parts'] ?? []),
            'service_incident' => count($data['parts'] ?? []),
            default            => 1,
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

    /**
     * Create a new PO document for resubmitted items.
     *
     * Saat item di-reject dan di-resubmit, alih-alih mengupdate PO lama,
     * sistem membuat PO document baru dengan po_id baru (increment) yang
     * hanya berisi item yang di-resubmit. PO lama tetap Disetujui dengan
     * item yang sudah approved.
     *
     * @param PurchaseOrder $oldPo        PO lama (status Disetujui, ada item rejected)
     * @param array         $newSourceData  source_data untuk PO baru (hanya berisi item rejected)
     * @param string        $sourceType     source_type PO
     * @return PurchaseOrder               PO baru yang dibuat
     */
    public function createResubmitPO(PurchaseOrder $oldPo, array $newSourceData, string $sourceType): PurchaseOrder
    {
        $vendor = $newSourceData['vendor'] ?? $oldPo->vendor ?? 'Vendor ' . ucfirst(str_replace('_', ' ', $sourceType));

        // Determine item count for new PO
        $totalItems = match ($sourceType) {
            'gps', 'gps_perpanjang' => count($newSourceData['gps_items'] ?? []),
            'service_part', 'service_incident' => count($newSourceData['parts'] ?? []),
            'service_asuransi' => count($newSourceData['kejadians'] ?? []),
            default => 1,
        };

        // Calculate nominal
        $nominal = match ($sourceType) {
            'gps', 'gps_perpanjang' => collect($newSourceData['gps_items'] ?? [])->sum(fn($i) => $i['biaya_sewa'] ?? 0),
            'service_part', 'service_incident' => collect($newSourceData['parts'] ?? [])->sum(fn($p) => $p['biaya'] ?? 0),
            'service_asuransi' => collect($newSourceData['kejadians'] ?? [])->sum(fn($k) => $k['biaya'] ?? 0),
            default => $newSourceData['nominal'] ?? $newSourceData['biaya'] ?? 0,
        };

        // Create new PO document — po_id auto-generated via boot() with increment
        $newPo = PurchaseOrder::create([
            'tanggal_po'        => now()->toDateString(),
            'vendor'            => $vendor,
            'pemohon'           => $newSourceData['pemohon'] ?? $oldPo->pemohon ?? null,
            'departemen'        => $newSourceData['departemen'] ?? $oldPo->departemen ?? null,
            'terkait_rfq'       => $newSourceData['terkait_rfq'] ?? null,
            'total_barang'      => $totalItems,
            'total_harga'       => (int) $nominal,
            'status_po'         => 'Pending',
            'catatan'           => $newSourceData['keterangan'] ?? null,
            'keterangan'        => $newSourceData['keterangan'] ?? null,
            'source_type'       => $sourceType,
            'source_data'       => $newSourceData,
            'status'            => 'Pending',
            'can_edit'          => false,
            'terakhir_diajukan' => now(),
        ]);

        // Populate item_po_numbers for new PO — all items get new PO number
        $multiItemTypes = ['gps', 'gps_perpanjang', 'service_part', 'service_asuransi', 'service_incident'];
        if (in_array($sourceType, $multiItemTypes)) {
            $itemPoNumbers = [];
            for ($i = 0; $i < $totalItems; $i++) {
                $itemPoNumbers[$i] = $newPo->po_id;
            }
            $sd = $newPo->source_data;
            $sd['item_po_numbers'] = $itemPoNumbers;
            $newPo->update(['source_data' => $sd]);
        }

        return $newPo;
    }

    /**
     * Update linked record (PajakKendaraan / AsuransiKendaraan / Kir / GpsKendaraan)
     * setelah PO disetujui — ubah persetujuan ke 'Diajukan ke Pembayaran' dan simpan pembayaran_id.
     *
     * @param PurchaseOrder $po
     * @param Pembayaran    $pembayaran
     * @param array|null    $overrideSourceData  Jika diberikan, digunakan sebagai source_data
     *                                            (berguna untuk partial-approve GPS di approveWithItems).
     */
    protected function updateLinkedRecord(PurchaseOrder $po, Pembayaran $pembayaran, ?array $overrideSourceData = null): void
    {
        $sourceType = $po->source_type;
        $sourceData = $po->source_data ?? [];

        // ── GPS ────────────────────────────────────────────────────────────────
        if (in_array($sourceType, ['gps', 'gps_perpanjang'])) {
            // Alur baru: tidak ada record GPS sebelum PO disetujui.
            // Buat record baru di sini dengan persetujuan = 'Diajukan ke Pembayaran'.
            // Untuk gps_perpanjang: record existing sudah ada, cukup update persetujuan-nya.

            $allGpsItems = $sourceType === 'gps'
                ? ($overrideSourceData['gps_items'] ?? $sourceData['gps_items'] ?? [])
                : ($sourceData['gps_items'] ?? []);

            $kendaraanId  = $sourceData['kendaraan_id'] ?? null;
            $tanggalBayar = $sourceData['tanggal_bayar'] ?? now()->toDateString();
            $tanggalHabis = $sourceData['tanggal_habis'] ?? now()->addYear()->toDateString();
            $keterangan   = $sourceData['keterangan'] ?? null;

            $durasiBulan = max(
                (int) \Carbon\Carbon::parse($tanggalBayar)->diffInMonths(\Carbon\Carbon::parse($tanggalHabis)),
                1
            );

            if ($sourceType === 'gps') {
                // Tambah baru: buat record GpsKendaraan per item
                $newRecordIds = [];
                foreach ($allGpsItems as $idx => $item) {
                    // Skip item yang tidak ada di overrideSourceData (partial approve)
                    if ($overrideSourceData !== null) {
                        $approvedKeys = array_map(
                            fn($i) => ($i['gps_id'] ?? '') . '_' . ($i['type'] ?? ''),
                            $overrideSourceData['gps_items'] ?? []
                        );
                        $itemKey = ($item['gps_id'] ?? '') . '_' . ($item['type'] ?? '');
                        if (!in_array($itemKey, $approvedKeys)) continue;
                    }

                    $gpsRecord = \App\Models\GpsKendaraan::create([
                        'pembayaran_id'  => $pembayaran->id,
                        'kendaraan_id'   => $kendaraanId,
                        'gps_id'         => $item['gps_id'] ?? null,
                        'type'           => $item['type'] ?? null,
                        'status_gps'     => 'nonaktif',
                        'tanggal_pasang' => $tanggalBayar,
                        'tanggal_habis'  => $tanggalHabis,
                        'tanggal_bayar'  => $tanggalBayar,
                        'tanggal_buat'   => $tanggalBayar,
                        'biaya_sewa'     => (int) ($item['biaya_sewa'] ?? 0),
                        'durasi_bulan'   => $durasiBulan,
                        'status_sewa'    => 'tidak_aktif',
                        'bukti_bayar'    => null,
                        'keterangan'     => $keterangan,
                        'nama_bank'      => $item['nama_bank'] ?? null,
                        'no_rekening'    => $item['no_rekening'] ?? null,
                        'nama_pemilik'   => $item['nama_pemilik'] ?? null,
                        'persetujuan'    => 'Diajukan ke Pembayaran',
                    ]);
                    $newRecordIds[] = $gpsRecord->id;

                    // Pindahkan lampiran dari temp_files ke attachments (relation_id = record baru)
                    $tempFiles    = $sourceData['temp_files'] ?? [];
                    $itemLampiran = $tempFiles['gps_items'][$idx]['lampiran'] ?? [];
                    $lampiranDir  = public_path('gps/attachments');
                    if (!file_exists($lampiranDir)) mkdir($lampiranDir, 0777, true);

                    foreach ($itemLampiran as $tf) {
                        $storagePath = $tf['path'] ?? null;
                        if (!$storagePath) continue;
                        $srcFullPath = storage_path('app/public/' . $storagePath);
                        if (!file_exists($srcFullPath)) continue;

                        $ext      = $tf['extension'] ?? pathinfo($storagePath, PATHINFO_EXTENSION);
                        $filename = time() . '_' . uniqid() . '.' . $ext;
                        copy($srcFullPath, $lampiranDir . '/' . $filename);

                        \App\Models\Attachment::create([
                            'relation_type' => 'gps',
                            'relation_id'   => $gpsRecord->id,
                            'file_name'     => $tf['original_name'] ?? basename($storagePath),
                            'file_path'     => 'gps/attachments/' . $filename,
                            'file_type'     => $ext,
                            'file_size'     => $tf['size'] ?? null,
                        ]);
                    }
                }

                // Simpan record IDs ke source_data PO untuk lookup saat transfer
                $updatedSource                  = $po->source_data;
                $updatedSource['gps_record_ids'] = $newRecordIds;
                $po->update(['source_data' => $updatedSource]);

            } else {
                // gps_perpanjang: record existing sudah ada, update persetujuan saja
                foreach ($allGpsItems as $item) {
                    if (!empty($item['gps_kendaraan_id'])) {
                        \App\Models\GpsKendaraan::where('id', $item['gps_kendaraan_id'])
                            ->update([
                                'persetujuan'   => 'Diajukan ke Pembayaran',
                                'pembayaran_id' => $pembayaran->id,
                            ]);
                    }
                }
            }
            return;
        }

        // ── PAJAK ──────────────────────────────────────────────────────────────
        if (in_array($sourceType, ['pajak', 'pajak_perpanjang'])) {
            $existingId = $sourceData['existing_record_id'] ?? null;
            if ($existingId) {
                \App\Models\PajakKendaraan::where('id', $existingId)
                    ->where('persetujuan', 'Pending')
                    ->update([
                        'persetujuan'   => 'Diajukan ke Pembayaran',
                        'pembayaran_id' => $pembayaran->id,
                        'status_aktif'  => 'tidak_aktif', // Set status tidak aktif saat diajukan ke Pembayaran
                        'tanggal_approval' => null, // Reset tanggal approval, nanti diisi saat Pembayaran approved
                    ]);
            }
            return;
        }

        // ── ASURANSI KENDARAAN ─────────────────────────────────────────────────
        if (in_array($sourceType, ['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'])) {
            $existingId = $sourceData['existing_record_id'] ?? null;
            if ($existingId) {
                \App\Models\AsuransiKendaraan::where('id', $existingId)
                    ->where('persetujuan', 'Pending')
                    ->update([
                        'persetujuan'   => 'Diajukan ke Pembayaran',
                        'pembayaran_id' => $pembayaran->id,
                        'status_aktif'  => 'tidak_aktif', // Set status tidak aktif saat diajukan ke Pembayaran
                        'tanggal_approval' => null, // Reset tanggal approval, nanti diisi saat Pembayaran approved
                    ]);
            }
            return;
        }

        // ── KIR ────────────────────────────────────────────────────────────────
        if (in_array($sourceType, ['kir', 'kir_perpanjang'])) {
            $existingId = $sourceData['existing_record_id'] ?? null;
            if ($existingId) {
                \App\Models\Kir::where('id', $existingId)
                    ->where('persetujuan', 'Pending')
                    ->update([
                        'persetujuan'   => 'Diajukan ke Pembayaran',
                        'pembayaran_id' => $pembayaran->id,
                        'status_aktif'  => 'tidak_aktif', // Set status tidak aktif saat diajukan ke Pembayaran
                        'tanggal_approval' => null, // Reset tanggal approval, nanti diisi saat Pembayaran approved
                    ]);
            }
            return;
        }

        // ── SERVICE INCIDENT ──────────────────────────────────────────────────
        if ($sourceType === 'service_incident') {
            $kendaraanId    = $sourceData['kendaraan_id'] ?? null;
            $tanggalService = $sourceData['tanggal_service'] ?? now()->toDateString();
            $kilometer      = $sourceData['kilometer'] ?? 0;
            $keluhan        = $sourceData['keluhan'] ?? null;
            $keterangan     = $sourceData['keterangan'] ?? null;
            $parts          = $sourceData['parts'] ?? [];

            // Filter parts jika ada partial approve (overrideSourceData)
            if ($overrideSourceData !== null) {
                $approvedPartKeys = array_map(
                    fn($p) => strtolower(trim($p['nama_part'] ?? '')) . '||' . ((int)($p['biaya'] ?? 0)),
                    $overrideSourceData['parts'] ?? []
                );
                $parts = array_values(array_filter($parts, function ($p) use ($approvedPartKeys) {
                    $key = strtolower(trim($p['nama_part'] ?? '')) . '||' . ((int)($p['biaya'] ?? 0));
                    return in_array($key, $approvedPartKeys);
                }));
            }

            $totalBiaya = collect($parts)->sum(fn($p) => (int)($p['biaya'] ?? 0));

            // Resolve kategori inline
            foreach ($parts as &$partData) {
                if (!empty($partData['nama_category_baru'])) {
                    $cat = \App\Models\ServiceCategory::firstOrCreate(
                        ['nama' => trim($partData['nama_category_baru'])]
                    );
                    $partData['category_id'] = $cat->id;
                }
            }
            unset($partData);

            // ── Guard: jika service_incident_id sudah ada di source_data PO,
            // ini adalah partial approval berikutnya (resubmit di-approve).
            // Cukup tambah parts baru ke record yang sudah ada — jangan buat record baru.
            $existingIncidentId = $sourceData['service_incident_id'] ?? null;

            if ($existingIncidentId) {
                $existingIncident = \App\Models\ServiceIncident::find($existingIncidentId);
                if ($existingIncident) {
                    // Akumulasi total_biaya (locked + baru)
                    $biayaBaru = $existingIncident->total_biaya + $totalBiaya;
                    $existingIncident->update([
                        'total_biaya'     => $biayaBaru,
                        'pembayaran_id'   => $pembayaran->id,
                        'purchase_order_id' => $po->id,
                        'persetujuan'     => 'Diajukan ke Pembayaran',
                    ]);

                    // Tambahkan HANYA parts baru (jangan hapus yang sudah ada)
                    foreach ($parts as $idx => $partData) {
                        \App\Models\ServiceIncidentPart::create([
                            'service_incident_id' => $existingIncident->id,
                            'kendaraan_id'        => $kendaraanId,
                            'category_id'         => $partData['category_id'] ?? null,
                            'nama_part'           => $partData['nama_part'] ?? '',
                            'part_number'         => $partData['part_number'] ?? null,
                            'serial_number'       => $partData['serial_number'] ?? null,
                            'posisi'              => $partData['posisi'] ?? null,
                            'tgl_pasang'          => $partData['tgl_pasang'] ?? $tanggalService,
                            'kilometer_pasang'    => $partData['kilometer_pasang'] ?? $kilometer,
                            'kondisi'             => $partData['kondisi'] ?? 'Perlu Ganti',
                            'status'              => 'tidak_aktif',
                            'biaya'               => (int)($partData['biaya'] ?? 0),
                            'supplier_id'         => $partData['supplier_id'] ?? null,
                            'nama_bank'           => $partData['nama_bank'] ?? null,
                            'no_rekening'         => $partData['no_rekening'] ?? null,
                            'nama_rekening'       => $partData['nama_rekening'] ?? null,
                            'persetujuan'         => 'Pending',
                            'purchase_order_id'   => $po->id,
                        ]);
                    }

                    return;
                }
            }

            // ── Tidak ada record sebelumnya: buat baru (alur pertama kali) ──
            $incident = \App\Models\ServiceIncident::create([
                'kendaraan_id'    => $kendaraanId,
                'keluhan'         => $keluhan,
                'keterangan'      => $keterangan,
                'kilometer'       => $kilometer,
                'total_biaya'     => $totalBiaya,
                'status'          => 'tidak_aktif',
                'tanggal_service' => $tanggalService,
                'status_approval' => 'pending',
                'pembayaran_id'   => $pembayaran->id,
                'purchase_order_id' => $po->id,
                'persetujuan'     => 'Diajukan ke Pembayaran',
            ]);

            // Buat ServiceIncidentPart per item yang disetujui
            foreach ($parts as $idx => $partData) {
                \App\Models\ServiceIncidentPart::create([
                    'service_incident_id' => $incident->id,
                    'kendaraan_id'        => $kendaraanId,
                    'category_id'         => $partData['category_id'] ?? null,
                    'nama_part'           => $partData['nama_part'] ?? '',
                    'part_number'         => $partData['part_number'] ?? null,
                    'serial_number'       => $partData['serial_number'] ?? null,
                    'posisi'              => $partData['posisi'] ?? null,
                    'tgl_pasang'          => $partData['tgl_pasang'] ?? $tanggalService,
                    'kilometer_pasang'    => $partData['kilometer_pasang'] ?? $kilometer,
                    'kondisi'             => $partData['kondisi'] ?? 'Perlu Ganti',
                    'status'              => 'tidak_aktif',
                    'biaya'               => (int)($partData['biaya'] ?? 0),
                    'supplier_id'         => $partData['supplier_id'] ?? null,
                    'nama_bank'           => $partData['nama_bank'] ?? null,
                    'no_rekening'         => $partData['no_rekening'] ?? null,
                    'nama_rekening'       => $partData['nama_rekening'] ?? null,
                    'persetujuan'         => 'Pending', // Part baru dibuat, pending approval di Pembayaran
                    'purchase_order_id'   => $po->id,
                ]);
            }

            // Simpan service_incident_id ke source_data PO agar transferServiceIncident bisa lookup
            $updatedSource = $po->source_data;
            $updatedSource['service_incident_id'] = $incident->id;
            $po->update(['source_data' => $updatedSource]);

            // Pindahkan lampiran dari temp storage ke attachments
            $tempAttachments = ($sourceData['temp_files'] ?? [])['attachments'] ?? [];
            $attDir = public_path('service-incident/attachments');
            if (!file_exists($attDir)) mkdir($attDir, 0777, true);
            foreach ($tempAttachments as $tf) {
                if (empty($tf['path'])) continue;
                $srcPath = storage_path('app/public/' . $tf['path']);
                if (!file_exists($srcPath)) continue;
                $ext      = $tf['extension'] ?? pathinfo($tf['path'], PATHINFO_EXTENSION);
                $filename = time() . '_' . uniqid() . '.' . $ext;
                copy($srcPath, $attDir . '/' . $filename);
                \App\Models\Attachment::create([
                    'relation_type' => 'service_incident',
                    'relation_id'   => $incident->id,
                    'file_name'     => $tf['original_name'] ?? basename($tf['path']),
                    'file_path'     => 'service-incident/attachments/' . $filename,
                    'file_type'     => $ext,
                    'file_size'     => $tf['size'] ?? null,
                ]);
            }

            return;
        }

        // ── SERVICE ASURANSI ──────────────────────────────────────────────────
        if ($sourceType === 'service_asuransi') {
            $kendaraanId  = $sourceData['kendaraan_id'] ?? null;
            $kejadians    = $overrideSourceData['kejadians'] ?? $sourceData['kejadians'] ?? [];
            $namaAsuransi = $sourceData['nama_asuransi'] ?? null;
            $keterangan   = $sourceData['keterangan'] ?? null;
            $totalBiaya   = collect($kejadians)->sum(fn($k) => (int)($k['biaya'] ?? 0));

            // Ekstrak date fields dari parent untuk diisi ke setiap kejadian
            // Priority: gunakan dari kejadian individual jika ada, fallback ke parent sourceData
            $tanggalService   = $sourceData['tanggal_service'] ?? now()->toDateString();
            $periodeMulai     = $sourceData['periode_mulai'] ?? null;
            $periodeSelesai   = $sourceData['periode_selesai'] ?? null;

            // ── Guard: jika service_asuransi_id sudah ada di source_data PO,
            // ini adalah partial approval kedua (resubmit). Cukup tambah kejadian baru
            // ke record yang sudah ada — jangan buat record baru.
            $existingId = $sourceData['service_asuransi_id'] ?? null;

            if ($existingId) {
                $existing = \App\Models\ServiceAsuransi::find($existingId);
                if ($existing) {
                    // Akumulasi biaya (locked + baru)
                    $biayaBaru = $existing->biaya + $totalBiaya;
                    $existing->update([
                        'biaya'         => $biayaBaru,
                        'pembayaran_id' => $pembayaran->id, // update ke pembayaran terbaru
                        'persetujuan'   => 'Diajukan ke Pembayaran',
                    ]);

                    // Tambahkan HANYA kejadian baru (jangan hapus yang sudah ada)
                    // Include tanggal, periode_mulai, periode_selesai dari parent
                    foreach ($kejadians as $kej) {
                        \App\Models\ServiceAsuransiKejadian::create([
                            'service_asuransi_id' => $existing->id,
                            'nama_kejadian'       => $kej['nama_kejadian'] ?? '-',
                            'tanggal'             => $kej['tanggal'] ?? $tanggalService,
                            'periode_mulai'       => $kej['periode_mulai'] ?? $periodeMulai,
                            'periode_selesai'     => $kej['periode_selesai'] ?? $periodeSelesai,
                            'biaya'               => (int)($kej['biaya'] ?? 0),
                            'lampiran'            => !empty($kej['lampiran']) ? $kej['lampiran'] : null,
                            'status'              => 'diajukan',
                        ]);
                    }

                    // Tidak perlu update source_data PO — service_asuransi_id sudah benar
                    return;
                }
            }

            // ── Tidak ada record sebelumnya: buat baru (alur pertama kali) ──
            $serviceAsuransi = \App\Models\ServiceAsuransi::create([
                'kendaraan_id'      => $kendaraanId,
                'nama_asuransi'     => $namaAsuransi,
                'jenis_asuransi_id' => $sourceData['jenis_asuransi_id'] ?? null,
                'tanggal_service'   => $tanggalService,
                'periode_mulai'     => $periodeMulai,
                'periode_selesai'   => $periodeSelesai,
                'kilometer'         => $sourceData['kilometer'] ?? 0,
                'biaya'             => $totalBiaya,
                'keterangan'        => $keterangan,
                'status'            => 'tidak_aktif',
                'pembayaran_id'     => $pembayaran->id,
                'purchase_order_id' => $po->id,
                'persetujuan'       => 'Diajukan ke Pembayaran',
            ]);

            foreach ($kejadians as $idx => $kej) {
                \App\Models\ServiceAsuransiKejadian::create([
                    'service_asuransi_id' => $serviceAsuransi->id,
                    'nama_kejadian'       => $kej['nama_kejadian'] ?? '-',
                    'tanggal'             => $kej['tanggal'] ?? $tanggalService,
                    'periode_mulai'       => $kej['periode_mulai'] ?? $periodeMulai,
                    'periode_selesai'     => $kej['periode_selesai'] ?? $periodeSelesai,
                    'biaya'               => (int)($kej['biaya'] ?? 0),
                    'lampiran'            => !empty($kej['lampiran']) ? $kej['lampiran'] : null,
                    'status'              => 'diajukan',
                ]);
            }

            // Simpan service_asuransi_id ke source_data PO agar approval berikutnya bisa lookup
            $updatedSource = $po->source_data;
            $updatedSource['service_asuransi_id'] = $serviceAsuransi->id;
            $po->update(['source_data' => $updatedSource]);

            return;
        }

        // ── SERVICE PART ───────────────────────────────────────────────────────
        // Parts sudah dibuat di createServiceHistoryDraft dengan persetujuan = 'Diajukan ke Pembayaran'
        // Tidak perlu update lagi, tapi kita log untuk debugging
        if ($sourceType === 'service_part') {
            $serviceHistory = \App\Models\ServiceHistory::where('pembayaran_id', $pembayaran->id)->first();
            if ($serviceHistory) {
                $partsCount = $serviceHistory->parts()->count();
                \Log::info('ServicePart updateLinkedRecord', [
                    'pembayaran_id' => $pembayaran->id,
                    'pembayaran_no_pr' => $pembayaran->no_pr,
                    'service_history_id' => $serviceHistory->id,
                    'parts_count' => $partsCount,
                    'parts_data' => $serviceHistory->parts()->get(['id', 'nama_part', 'persetujuan', 'status'])->toArray(),
                ]);
            } else {
                \Log::warning('ServiceHistory NOT FOUND for pembayaran', [
                    'pembayaran_id' => $pembayaran->id,
                    'pembayaran_no_pr' => $pembayaran->no_pr,
                ]);
            }
            return;
        }

        // Tipe lain (stnk, dll) tidak punya linked record eksternal — tidak ada aksi
    }
}
