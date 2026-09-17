<?php

namespace App\Services;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengeluaranInterceptorService
{
    /**
     * Intercept form submit dan extract data untuk disimpan ke Pembayaran
     *
     * @param Request $request
     * @param string $sourceType (asuransi_kendaraan, pajak, service_part, gps, kir, stnk, service_asuransi)
     * @return array Data yang sudah diformat untuk Pembayaran
     */
    public function intercept(Request $request, string $sourceType): array
    {
        // Extract semua data dari request
        $data = $request->except(['_token', '_method', 'bukti', 'bukti_attachment', 'attachment', 'attachments']);

        // Jika dari Ganti Baru (from_part), inject replace_part_id ke setiap part di source_data
        if ($request->filled('from_part') && $sourceType === 'service_part') {
            $replacePartId = (int) $request->input('from_part');
            if (!empty($data['parts']) && is_array($data['parts'])) {
                foreach ($data['parts'] as &$partData) {
                    $partData['replace_part_id'] = $replacePartId;
                }
                unset($partData);
            }
        }

        // Inject keterangan_limit, kondisi, dan status otomatis untuk service_part
        // karena ketiga field tersebut sudah dihapus dari form dan di-generate server-side
        if ($sourceType === 'service_part' && !empty($data['parts']) && is_array($data['parts'])) {
            $kendaraanId = (int) ($data['kendaraan_id'] ?? 0);
            $kmInput     = (int) ($data['kilometer'] ?? 0);

            // Pre-load limit rules sekali
            $limitRules = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $kendaraanId)
                ->get()
                ->keyBy('category_id');

            foreach ($data['parts'] as &$partData) {
                // Status — selalu Proses saat diajukan
                $partData['status'] = 'Proses';

                // Kondisi — Perlu Ganti saat baru diinput
                $partData['kondisi'] = 'Perlu Ganti';

                // Keterangan limit otomatis
                $categoryId = $partData['category_id'] ?? null;
                $limitRule  = ($categoryId && isset($limitRules[$categoryId]))
                    ? $limitRules[$categoryId]
                    : null;

                $partData['keterangan_limit'] = $this->buildKeteranganLimitForIntercept(
                    $partData,
                    $kmInput,
                    $limitRule,
                    $data['tanggal_service'] ?? null
                );
            }
            unset($partData);
        }

        // Get user info
        $user = Auth::user();
        
        // Format data berdasarkan source type
        $formattedData = [
            'source_type' => $sourceType,
            'source_data' => $data,
            'departemen' => $user->departemen ?? 'Umum',
            'pemohon' => $user->nama ?? $user->email,
            'alasan_permintaan' => $this->getAlasanPermintaan($sourceType, $data),
            'nominal' => $this->extractNominal($sourceType, $data),
            'nama_bank' => $request->input('nama_bank'),
            'no_rekening' => $request->input('no_rekening'),
            'nama_rekening' => $request->input('nama_rekening'),
            'informasi' => $request->input('informasi') ?? $request->input('keterangan'),
        ];
        
        return $formattedData;
    }

    /**
     * Generate keterangan_limit otomatis untuk source_data PO/Pembayaran.
     * Sama logikanya dengan ServiceHistoryController::generateKeteranganLimit()
     * tapi sebagai method standalone di service ini.
     */
    private function buildKeteranganLimitForIntercept(array $partData, int $kmInput, ?\App\Models\ServiceCategoryLimit $limitRule, ?string $tanggalServis = null): string
    {
        if (!$limitRule) {
            return '-';
        }

        $biaya          = (int) ($partData['biaya'] ?? 0);
        $tglPasang      = \Carbon\Carbon::parse($partData['tgl_pasang'] ?? now());
        $intervalNilai  = (int) ($partData['interval_nilai'] ?? 0);
        $intervalSatuan = $partData['interval_satuan'] ?? 'bulan';
        $kmPasang       = (int) ($partData['kilometer_pasang'] ?? $kmInput);

        // Hitung tanggal limit
        $tglLimit = match ($intervalSatuan) {
            'hari'   => (clone $tglPasang)->addDays($intervalNilai),
            'minggu' => (clone $tglPasang)->addWeeks($intervalNilai),
            'tahun'  => (clone $tglPasang)->addYears($intervalNilai),
            default  => (clone $tglPasang)->addMonths($intervalNilai),
        };
        // Bandingkan dengan tanggal servis yang diinput (bukan hari ini)
        $refTanggal = \Carbon\Carbon::parse($tanggalServis ?? now())->startOfDay();

        $hargaLimit  = $limitRule->limit_price;
        $kmLimit     = $limitRule->limit_km;
        $intervalAda = $intervalNilai > 0;

        $biayaLewat  = $hargaLimit && $biaya > $hargaLimit;
        $biayaSama   = $hargaLimit && $biaya === $hargaLimit;
        $biayaAman   = !$hargaLimit || $biaya < $hargaLimit;

        $waktuLewat  = $intervalAda && $tglLimit->lt($refTanggal);
        $waktuSama   = $intervalAda && $tglLimit->eq($refTanggal);
        $waktuAman   = !$intervalAda || $tglLimit->gt($refTanggal);

        $kmAda      = $kmLimit && $kmLimit > 0;
        $kmSama     = $kmAda && $kmInput === $kmPasang + $kmLimit;
        $kmLewat    = $kmAda && $kmInput > $kmPasang + $kmLimit;
        $kmAman     = !$kmAda || $kmInput < $kmPasang + $kmLimit;

        $adaLimit   = $hargaLimit || $intervalAda || $kmAda;
        if (!$adaLimit || ($biayaAman && $waktuAman && $kmAman)) {
            return '-';
        }

        $parts = [];
        if ($biayaSama)       { $parts['biaya'] = 'mencapai batas limit biaya'; }
        elseif ($biayaLewat)  { $parts['biaya'] = 'sudah melebihi limit biaya'; }
        if ($waktuSama)       { $parts['waktu'] = 'mencapai batas limit jangka waktu'; }
        elseif ($waktuLewat)  { $parts['waktu'] = 'sudah melebihi batas waktu'; }
        if ($kmSama)          { $parts['km'] = 'mencapai batas limit KM'; }
        elseif ($kmLewat)     { $parts['km'] = 'sudah melebihi batas limit KM'; }

        $belumParts = [];
        if ($hargaLimit && $biayaAman && !isset($parts['biaya']))   { $belumParts[] = 'belum mencapai limit biaya'; }
        if ($intervalAda && $waktuAman && !isset($parts['waktu']))   { $belumParts[] = 'belum mencapai limit jangka waktu'; }
        if ($kmAda && $kmAman && !isset($parts['km']))               { $belumParts[] = 'belum mencapai limit KM'; }

        $kalimat = array_merge(array_values($parts), $belumParts);
        if (empty($kalimat)) { return '-'; }

        return ucfirst(implode(', ', $kalimat));
    }

    /**
     * Save data ke Pembayaran table
     *
     * @param array $data Data hasil intercept
     * @param string $sourceType
     * @return Pembayaran
     */
    public function saveToPembayaran(array $data, string $sourceType): Pembayaran
    {
        DB::beginTransaction();
        
        try {
            $pembayaran = Pembayaran::create([
                'no_pr' => $this->generateNoPR($sourceType),
                'tanggal' => now(),
                'departemen' => $data['departemen'],
                'tipe_pembayaran' => 'service', // All pengeluaran (vehicle expenses) use 'service' type
                'pemohon' => $data['pemohon'],
                'alasan_permintaan' => $data['alasan_permintaan'],
                'nominal' => $data['nominal'],
                'nama_bank' => $data['nama_bank'] ?? null,
                'no_rekening' => $data['no_rekening'] ?? null,
                'nama_rekening' => $data['nama_rekening'] ?? null,
                'informasi' => $data['informasi'] ?? null,
                'status' => 'Diajukan',        // Langsung Diajukan, tidak perlu klik Ajukan lagi
                'terakhir_diajukan' => now(),
                'source_type' => $sourceType,
                'source_data' => $data['source_data'],
                'target_id' => null,
                'can_edit' => false, // Tidak bisa edit setelah submit
            ]);
            
            DB::commit();
            
            return $pembayaran;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate No PR unik untuk pengeluaran
     *
     * @param string $sourceType
     * @return string
     */
    public function generateNoPR(string $sourceType): string
    {
        // Format: PR-PENGELUARAN-{TYPE}-{INCREMENT}
        $typeMap = [
            'asuransi_kendaraan'             => 'ASR',
            'asuransi_kendaraan_perpanjang'  => 'APP',
            'pajak'                          => 'PJK',
            'pajak_perpanjang'               => 'PJP',
            'service_part'                   => 'SVC',
            'service_incident'               => 'SIN',
            'gps'                            => 'GPS',
            'gps_perpanjang'                 => 'GPP',
            'kir'                            => 'KIR',
            'kir_perpanjang'                 => 'KRP',
            'stnk'                           => 'STN',
            'service_asuransi'               => 'SAS',
            'purchase_order'                 => 'PO',
        ];
        
        $typeCode = $typeMap[$sourceType] ?? 'PGL';
        
        // Get last PR for this type
        $lastPR = Pembayaran::where('source_type', $sourceType)
            ->where('no_pr', 'like', "PR-{$typeCode}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        $increment = 1;
        
        if ($lastPR) {
            // Extract increment from last PR
            preg_match('/PR-' . $typeCode . '-(\d+)/', $lastPR->no_pr, $matches);
            if (isset($matches[1])) {
                $increment = intval($matches[1]) + 1;
            }
        }
        
        return sprintf('PR-%s-%03d', $typeCode, $increment);
    }

    /**
     * Get alasan permintaan berdasarkan source type
     *
     * @param string $sourceType
     * @param array $data
     * @return string
     */
    protected function getAlasanPermintaan(string $sourceType, array $data): string
    {
        return match($sourceType) {
            'asuransi_kendaraan'            => 'Pembayaran Asuransi Kendaraan - ' . ($data['keterangan'] ?? 'N/A'),
            'asuransi_kendaraan_perpanjang' => 'Perpanjangan Asuransi Kendaraan',
            'pajak'                         => 'Pembayaran Pajak Kendaraan - ' . ($data['jenis_pajak'] ?? 'N/A'),
            'pajak_perpanjang'              => 'Perpanjangan Pajak Kendaraan',
            'service_part'                  => 'Pembelian Service Part - ' . (
                isset($data['parts']) && is_array($data['parts'])
                    ? collect($data['parts'])->pluck('nama_part')->filter()->take(3)->implode(', ')
                    : ($data['nama_part'] ?? 'N/A')
            ),
            'gps'                           => 'Pembayaran GPS Kendaraan - ' . ($data['keterangan'] ?? 'N/A'),
            'gps_perpanjang'                => 'Perpanjangan GPS Kendaraan',
            'kir'                           => 'Pembayaran KIR Kendaraan',
            'kir_perpanjang'                => 'Perpanjangan KIR Kendaraan',
            'stnk'                          => 'Pembayaran STNK Kendaraan',
            'service_asuransi'              => 'Klaim Asuransi Service - ' . ($data['keterangan'] ?? 'N/A'),
            'purchase_order'                => 'Purchase Order - ' . ($data['vendor'] ?? 'N/A'),
            default => 'Pengeluaran Kendaraan',
        };
    }

    /**
     * Extract nominal dari data berdasarkan source type
     *
     * @param string $sourceType
     * @param array $data
     * @return float
     */
    protected function extractNominal(string $sourceType, array $data): float
    {
        return match($sourceType) {
            'asuransi_kendaraan',
            'asuransi_kendaraan_perpanjang' => floatval($data['biaya'] ?? $data['premi'] ?? 0),
            'pajak',
            'pajak_perpanjang'              => floatval($data['nominal'] ?? 0),
            'service_part'                  => floatval(
                // Jika ada total_biaya_override, pakai itu; jika tidak, sum dari semua parts
                ($data['total_biaya_override'] ?? 0) > 0
                    ? $data['total_biaya_override']
                    : collect($data['parts'] ?? [])->sum(fn($p) => floatval($p['biaya'] ?? 0))
            ),
            'gps'                           => collect($data['gps_items'] ?? [])->sum(fn($item) => floatval($item['biaya_sewa'] ?? 0)),
            'gps_perpanjang'                => collect($data['gps_items'] ?? [])->sum(fn($item) => floatval($item['biaya_sewa'] ?? 0)),
            'kir',
            'kir_perpanjang'                => floatval($data['biaya'] ?? 0),
            'stnk'                          => floatval($data['biaya'] ?? 0),
            'service_asuransi'              => floatval($data['biaya'] ?? 0),
            'purchase_order'                => floatval($data['total_harga'] ?? 0),
            default => 0,
        };
    }

    /**
     * Resubmit rejected pembayaran dengan data baru
     * Update existing pembayaran record, reset status to Pending, update source_data
     *
     * @param int $pembayaranId
     * @param Request $request
     * @param string $sourceType
     * @return Pembayaran
     */
    public function resubmitToPembayaran(int $pembayaranId, Request $request, string $sourceType): Pembayaran
    {
        DB::beginTransaction();
        
        try {
            $pembayaran = Pembayaran::findOrFail($pembayaranId);
            
            // Validation: Only rejected pengeluaran can be resubmitted
            if (!in_array($pembayaran->status, ['Ditolak', 'Disetujui Sebagian'])) {
                throw new \Exception('Hanya pengajuan yang ditolak yang dapat diajukan ulang.');
            }
            
            if (!$pembayaran->can_edit) {
                throw new \Exception('Pengajuan ini tidak dapat diedit.');
            }
            
            // Intercept new data
            $interceptedData = $this->intercept($request, $sourceType);

            // Upload new temp files (jika ada)
            $newUploadedFiles = $this->uploadTemporaryFiles($request, $pembayaranId);

            // Ambil temp_files lama
            $oldTempFiles = $pembayaran->source_data['temp_files'] ?? [];

            // Merge: per part, pakai baru kalau ada; pertahankan lama kalau tidak
            $mergedTempFiles = $this->mergeTemporaryFiles($oldTempFiles, $newUploadedFiles, $interceptedData['source_data']['parts'] ?? []);

            // Hapus file lama HANYA yang sudah diganti
            $this->deleteReplacedTempFiles($oldTempFiles, $newUploadedFiles);

            // Update pembayaran
            $pembayaran->update([
                'source_data' => array_merge($interceptedData['source_data'], ['temp_files' => $mergedTempFiles]),
                'alasan_permintaan' => $interceptedData['alasan_permintaan'],
                'nominal' => $interceptedData['nominal'],
                'nama_bank' => $interceptedData['nama_bank'] ?? null,
                'no_rekening' => $interceptedData['no_rekening'] ?? null,
                'nama_rekening' => $interceptedData['nama_rekening'] ?? null,
                'informasi' => $interceptedData['informasi'] ?? null,
                'status' => 'Diajukan',
                'terakhir_diajukan' => now(),
                'can_edit' => false,
            ]);
            
            DB::commit();
            
            return $pembayaran;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Resubmit rejected Purchase Order dengan data baru
     * Update existing PO record, reset status to Pending, update source_data
     *
     * @param int $poId
     * @param Request $request
     * @param string $sourceType
     * @return \App\Models\PurchaseOrder
     */
    public function resubmitToPurchaseOrder(int $poId, Request $request, string $sourceType): \App\Models\PurchaseOrder
    {
        DB::beginTransaction();
        
        try {
            $po = \App\Models\PurchaseOrder::findOrFail($poId);
            
            // Validation: Only rejected PO can be resubmitted
            if ($po->status !== 'Ditolak') {
                throw new \Exception('Hanya Purchase Order yang ditolak yang dapat diajukan ulang.');
            }
            
            if (!$po->can_edit) {
                throw new \Exception('Purchase Order ini tidak dapat diedit.');
            }
            
            // Intercept new data
            $interceptedData = $this->intercept($request, $sourceType);

            // Upload new temp files (jika ada)
            $newUploadedFiles = $this->uploadTemporaryFiles($request, $poId, 'purchase_order');

            // Ambil temp_files lama dari PO yang ditolak
            $oldTempFiles = $po->source_data['temp_files'] ?? [];

            // Merge: per part, kalau user upload baru → pakai baru; kalau tidak → pakai lama
            $mergedTempFiles = $this->mergeTemporaryFiles($oldTempFiles, $newUploadedFiles, $interceptedData['source_data']['parts'] ?? []);

            // Hapus file lama HANYA yang sudah diganti dengan file baru
            $this->deleteReplacedTempFiles($oldTempFiles, $newUploadedFiles, 'purchase_order');

            // Extract vendor and total items
            $vendor = $interceptedData['source_data']['vendor'] ?? 'Vendor ' . ucfirst(str_replace('_', ' ', $sourceType));
            $totalItems = $this->extractTotalItems($sourceType, $interceptedData['source_data']);
            
            // Update PO
            $po->update([
                'source_data' => array_merge($interceptedData['source_data'], ['temp_files' => $mergedTempFiles]),
                'vendor' => $vendor,
                'total_barang' => $totalItems,
                'total_harga' => (int) $interceptedData['nominal'],
                'catatan' => $interceptedData['informasi'] ?? $interceptedData['source_data']['keterangan'] ?? null,
                'keterangan' => $interceptedData['source_data']['keterangan'] ?? null,
                'status' => 'Pending',
                'can_edit' => false,
                'terakhir_diajukan' => now(),
            ]);
            
            DB::commit();
            
            return $po;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Save data ke PurchaseOrder table (untuk tambah baru yang lewat PO)
     *
     * @param array $data Data hasil intercept
     * @param string $sourceType
     * @return \App\Models\PurchaseOrder
     */
    public function saveToPurchaseOrder(array $data, string $sourceType): \App\Models\PurchaseOrder
    {
        DB::beginTransaction();
        
        try {
            // Extract vendor (opsional, default jika kosong)
            $vendor = $data['source_data']['vendor'] ?? 'Vendor ' . ucfirst(str_replace('_', ' ', $sourceType));
            
            // Extract total items count
            $totalItems = $this->extractTotalItems($sourceType, $data['source_data']);
            
            // Create Purchase Order
            $po = \App\Models\PurchaseOrder::create([
                'tanggal_po' => now()->toDateString(),
                'vendor' => $vendor,
                'terkait_rfq' => $data['source_data']['terkait_rfq'] ?? null,
                'total_barang' => $totalItems,
                'total_harga' => (int) $data['nominal'],
                'status_po' => 'Pending', // Legacy field, not used in new flow
                'catatan' => $data['informasi'] ?? $data['source_data']['keterangan'] ?? null,
                'keterangan' => $data['source_data']['keterangan'] ?? null,
                // Approval workflow fields
                'source_type' => $sourceType,
                'source_data' => $data['source_data'],
                'status' => 'Pending',
                'can_edit' => false,
                'terakhir_diajukan' => now(),
            ]);
            
            DB::commit();
            
            return $po;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Extract total items count dari data berdasarkan source type
     */
    protected function extractTotalItems(string $sourceType, array $data): int
    {
        return match($sourceType) {
            'gps' => count($data['gps_items'] ?? []),
            'service_part'     => count($data['parts'] ?? []),
            'service_incident' => count($data['parts'] ?? []),
            default => 1,
        };
    }

    /**
     * Upload temporary files untuk pengeluaran yang menunggu approval
     * Support both Pembayaran and PurchaseOrder
     *
     * @param Request $request
     * @param int $entityId
     * @param string $entityType 'pembayaran' or 'purchase_order'
     * @return array Array of file metadata
     */
    public function uploadTemporaryFiles(Request $request, int $entityId, string $entityType = 'pembayaran'): array
    {
        $uploadedFiles = [];
        $timestamp = time();
        
        // Directory untuk temp files
        $tempDir = "{$entityType}/temp/{$entityId}";
        
        // Upload bukti files
        if ($request->hasFile('bukti')) {
            $files = is_array($request->file('bukti')) 
                ? $request->file('bukti') 
                : [$request->file('bukti')];
            
            foreach ($files as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $storedName = "{$timestamp}_{$index}_{$originalName}";
                
                $path = $file->storeAs($tempDir . '/bukti', $storedName, 'public');
                
                $uploadedFiles['bukti'][] = [
                    'original_name' => $originalName,
                    'stored_name' => $storedName,
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path),
                    'size' => $file->getSize(),
                    'extension' => $extension,
                ];
            }
        }
        
        // Upload attachment files (opsional)
        $attachmentFields = ['bukti_attachment', 'attachment', 'attachments', 'lampiran'];
        
        foreach ($attachmentFields as $field) {
            if ($request->hasFile($field)) {
                $files = is_array($request->file($field)) 
                    ? $request->file($field) 
                    : [$request->file($field)];
                
                foreach ($files as $index => $file) {
                    if (!$file->isValid()) continue;
                    
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $storedName = "{$timestamp}_{$index}_{$originalName}";
                    
                    $path = $file->storeAs($tempDir . '/attachments', $storedName, 'public');
                    
                    $uploadedFiles['attachments'][] = [
                        'original_name' => $originalName,
                        'stored_name' => $storedName,
                        'path' => $path,
                        'full_path' => storage_path('app/public/' . $path),
                        'size' => $file->getSize(),
                        'extension' => $extension,
                    ];
                }
            }
        }
        
        // Upload parts per-item bukti (service_part multi-item form)
        $partFiles = $request->file('parts');
        if (is_array($partFiles)) {
            foreach ($partFiles as $idx => $part) {
                if (empty($part['bukti']) || !is_array($part['bukti'])) continue;
                foreach ($part['bukti'] as $bi => $buktiFile) {
                    if (!$buktiFile || !$buktiFile->isValid()) continue;
                    $originalName = $buktiFile->getClientOriginalName();
                    $extension    = $buktiFile->getClientOriginalExtension();
                    $storedName   = "{$timestamp}_{$idx}_{$bi}_{$originalName}";
                    $path = $buktiFile->storeAs($tempDir . '/parts/' . $idx . '/bukti', $storedName, 'public');
                    $uploadedFiles['parts'][$idx]['bukti'][] = [
                        'original_name' => $originalName,
                        'stored_name'   => $storedName,
                        'path'          => $path,
                        'full_path'     => storage_path('app/public/' . $path),
                        'size'          => $buktiFile->getSize(),
                        'extension'     => $extension,
                    ];
                }
            }
        }

        // Upload gps_items per-item lampiran (GPS multi-item form)
        $gpsItemFiles = $request->file('gps_items');
        if (is_array($gpsItemFiles)) {
            foreach ($gpsItemFiles as $idx => $gpsItem) {
                // Lampiran per item GPS
                if (!empty($gpsItem['lampiran']) && is_array($gpsItem['lampiran'])) {
                    foreach ($gpsItem['lampiran'] as $li => $lampiranFile) {
                        if ($lampiranFile && $lampiranFile->isValid()) {
                            $originalName = $lampiranFile->getClientOriginalName();
                            $extension    = $lampiranFile->getClientOriginalExtension();
                            $storedName   = "{$timestamp}_{$idx}_{$li}_{$originalName}";

                            $path = $lampiranFile->storeAs($tempDir . '/gps_items/' . $idx . '/lampiran', $storedName, 'public');

                            $uploadedFiles['gps_items'][$idx]['lampiran'][] = [
                                'original_name' => $originalName,
                                'stored_name'   => $storedName,
                                'path'          => $path,
                                'full_path'     => storage_path('app/public/' . $path),
                                'size'          => $lampiranFile->getSize(),
                                'extension'     => $extension,
                            ];
                        }
                    }
                }
            }
        }
        
        return $uploadedFiles;
    }

    /**
     * Handle perpanjang flow - create pembayaran for renewal approval
     * 
     * @param Request $request
     * @param string $sourceType  Base source type (pajak, asuransi_kendaraan, kir, gps)
     * @param mixed $existingRecord
     * @return Pembayaran
     */
    public function perpanjangViaPembayaran(Request $request, string $sourceType, $existingRecord): Pembayaran
    {
        // Map base source_type ke perpanjang source_type
        $perpanjangTypeMap = [
            'pajak'              => 'pajak_perpanjang',
            'asuransi_kendaraan' => 'asuransi_kendaraan_perpanjang',
            'kir'                => 'kir_perpanjang',
            'gps'                => 'gps_perpanjang',
        ];
        $perpanjangSourceType = $perpanjangTypeMap[$sourceType] ?? $sourceType;

        DB::beginTransaction();
        
        try {
            // Build perpanjang data
            $perpanjangData = $request->all();
            $perpanjangData['is_perpanjang'] = true;
            $perpanjangData['existing_record_id'] = $existingRecord->id;
            
            // Add existing record info for context
            if (property_exists($existingRecord, 'kendaraan_id')) {
                $perpanjangData['kendaraan_id'] = $existingRecord->kendaraan_id;
            }
            if (method_exists($existingRecord, 'kendaraan') && $existingRecord->kendaraan) {
                $perpanjangData['kendaraan_nopol'] = $existingRecord->kendaraan->nopol ?? '-';
            }
            
            // Create fake request for intercept
            $fakeRequest = new Request($perpanjangData);
            $fakeRequest->merge($request->all());
            
            // Intercept data (menggunakan perpanjang source_type)
            $interceptedData = $this->intercept($fakeRequest, $perpanjangSourceType);
            
            // Alasan permintaan sudah di-set oleh getAlasanPermintaan untuk perpanjang types
            
            // Save to Pembayaran dengan perpanjang source_type
            $pembayaran = $this->saveToPembayaran($interceptedData, $perpanjangSourceType);
            
            // Upload files (file image/bukti dari form perpanjangan)
            $uploadedFiles = $this->uploadTemporaryFiles($fakeRequest, $pembayaran->id);
            
            // Sertakan lampiran lama dari GPS record ke source_data
            // supaya tampil di modal approval pembayaran
            $gpsItems = $interceptedData['source_data']['gps_items'] ?? [];
            if (!empty($gpsItems)) {
                foreach ($gpsItems as $idx => $item) {
                    $gpsKendaraanId = $item['gps_kendaraan_id'] ?? null;
                    if (!$gpsKendaraanId) continue;
                    $lampiranLama = \App\Models\Attachment::where('relation_type', 'gps')
                        ->where('relation_id', $gpsKendaraanId)
                        ->get()
                        ->map(fn($a) => [
                            'original_name' => $a->file_name,
                            'path'          => $a->file_path,
                            'size'          => $a->file_size,
                            'extension'     => $a->file_type,
                        ])
                        ->toArray();
                    if (!empty($lampiranLama)) {
                        $uploadedFiles['gps_items'][$idx]['lampiran'] = array_merge(
                            $uploadedFiles['gps_items'][$idx]['lampiran'] ?? [],
                            $lampiranLama
                        );
                    }
                }
            }

            // Update source_data
            $sourceData = $pembayaran->source_data;
            $sourceData['temp_files'] = $uploadedFiles;
            $pembayaran->update(['source_data' => $sourceData]);
            
            DB::commit();
            
            return $pembayaran;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Get human-readable source type name
     */
    private function getSourceTypeName(string $sourceType): string
    {
        return match($sourceType) {
            'gps'                            => 'GPS Kendaraan',
            'gps_perpanjang'                 => 'Perpanjangan GPS',
            'asuransi_kendaraan'             => 'Asuransi Kendaraan',
            'asuransi_kendaraan_perpanjang'  => 'Perpanjangan Asuransi Kendaraan',
            'pajak'                          => 'Pajak Kendaraan',
            'pajak_perpanjang'               => 'Perpanjangan Pajak Kendaraan',
            'kir'                            => 'KIR',
            'kir_perpanjang'                 => 'Perpanjangan KIR',
            'stnk'                           => 'STNK',
            'service_asuransi'               => 'Service Asuransi',
            'service_part'                   => 'Service Part',
            'purchase_order'                 => 'Purchase Order',
            default => ucfirst(str_replace('_', ' ', $sourceType)),
        };
    }

    /**
     * Delete temporary files untuk pembayaran yang dibatalkan
     *
     * @param int $pembayaranId
     * @return bool
     */
    public function deleteTemporaryFiles(int $entityId, string $entityType = 'pembayaran'): bool
    {
        $tempDir = match($entityType) {
            'purchase_order' => "purchase_order/temp/{$entityId}",
            default => "pembayaran/temp/{$entityId}",
        };
        
        try {
            Storage::disk('public')->deleteDirectory($tempDir);
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to delete temp files for {$entityType} {$entityId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Merge temp files lama dengan yang baru.
     * - Level bukti/attachments: kalau ada baru → pakai baru, kalau tidak → pakai lama
     * - Level parts[idx][bukti]: per-part, kalau user upload baru → pakai baru, kalau tidak → pakai lama
     */
    private function mergeTemporaryFiles(array $oldFiles, array $newFiles, array $parts = []): array
    {
        $merged = $oldFiles; // mulai dari lama

        // Override bukti & attachments kalau ada yang baru
        if (!empty($newFiles['bukti'])) {
            $merged['bukti'] = $newFiles['bukti'];
        }
        if (!empty($newFiles['attachments'])) {
            $merged['attachments'] = $newFiles['attachments'];
        }

        // Per-part bukti: override per index kalau ada file baru untuk index itu
        if (!empty($newFiles['parts'])) {
            foreach ($newFiles['parts'] as $idx => $partFiles) {
                if (!empty($partFiles['bukti'])) {
                    // User upload file baru untuk part ini → ganti
                    $merged['parts'][$idx]['bukti'] = $partFiles['bukti'];
                }
                // Kalau tidak ada file baru untuk part ini, data lama ($merged['parts'][$idx]) tetap dipertahankan
            }
        }

        // GPS items
        if (!empty($newFiles['gps_items'])) {
            foreach ($newFiles['gps_items'] as $idx => $gpsFiles) {
                if (!empty($gpsFiles['lampiran'])) {
                    $merged['gps_items'][$idx]['lampiran'] = $gpsFiles['lampiran'];
                }
            }
        }

        return $merged;
    }

    /**
     * Hapus hanya file lama yang sudah diganti dengan file baru.
     * File lama yang tidak diganti TIDAK dihapus.
     */
    private function deleteReplacedTempFiles(array $oldFiles, array $newFiles, string $entityType = 'pembayaran'): void
    {
        // Hapus bukti lama kalau ada bukti baru
        if (!empty($newFiles['bukti']) && !empty($oldFiles['bukti'])) {
            foreach ($oldFiles['bukti'] as $f) {
                if (!empty($f['path'])) {
                    Storage::disk('public')->delete($f['path']);
                }
            }
        }

        // Hapus attachments lama kalau ada baru
        if (!empty($newFiles['attachments']) && !empty($oldFiles['attachments'])) {
            foreach ($oldFiles['attachments'] as $f) {
                if (!empty($f['path'])) {
                    Storage::disk('public')->delete($f['path']);
                }
            }
        }

        // Hapus per-part bukti lama kalau ada yang baru per index
        if (!empty($newFiles['parts'])) {
            foreach ($newFiles['parts'] as $idx => $partNew) {
                if (!empty($partNew['bukti']) && !empty($oldFiles['parts'][$idx]['bukti'])) {
                    foreach ($oldFiles['parts'][$idx]['bukti'] as $f) {
                        if (!empty($f['path'])) {
                            Storage::disk('public')->delete($f['path']);
                        }
                    }
                }
            }
        }
    }
}
