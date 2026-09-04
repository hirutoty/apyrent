<?php

namespace App\Services;

use App\Models\Purchasero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengeluaranInterceptorService
{
    /**
     * Intercept form submit dan extract data untuk disimpan ke Purchasero
     *
     * @param Request $request
     * @param string $sourceType (asuransi_kendaraan, pajak, service_part, gps, kir, stnk, service_asuransi)
     * @return array Data yang sudah diformat untuk Purchasero
     */
    public function intercept(Request $request, string $sourceType): array
    {
        // Extract semua data dari request
        $data = $request->except(['_token', '_method', 'bukti', 'bukti_attachment', 'attachment', 'attachments']);
        
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
     * Save data ke Purchasero table
     *
     * @param array $data Data hasil intercept
     * @param string $sourceType
     * @return Purchasero
     */
    public function saveToPurchasero(array $data, string $sourceType): Purchasero
    {
        DB::beginTransaction();
        
        try {
            $purchasero = Purchasero::create([
                'no_pr' => $this->generateNoPR($sourceType),
                'tanggal' => now(),
                'departemen' => $data['departemen'],
                'tipe_pengadaan' => 'service', // All pengeluaran (vehicle expenses) use 'service' type
                'pemohon' => $data['pemohon'],
                'alasan_permintaan' => $data['alasan_permintaan'],
                'nominal' => $data['nominal'],
                'nama_bank' => $data['nama_bank'] ?? null,
                'no_rekening' => $data['no_rekening'] ?? null,
                'nama_rekening' => $data['nama_rekening'] ?? null,
                'informasi' => $data['informasi'] ?? null,
                'status' => 'Pending',
                'source_type' => $sourceType,
                'source_data' => $data['source_data'],
                'target_id' => null,
                'can_edit' => false, // Tidak bisa edit setelah submit
            ]);
            
            DB::commit();
            
            return $purchasero;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Upload temporary files untuk pengeluaran yang menunggu approval
     *
     * @param Request $request
     * @param int $purchaseroId
     * @return array Array of file metadata
     */
    public function uploadTemporaryFiles(Request $request, int $purchaseroId): array
    {
        $uploadedFiles = [];
        $timestamp = time();
        
        // Directory untuk temp files
        $tempDir = "purchasero/temp/{$purchaseroId}";
        
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
        $attachmentFields = ['bukti_attachment', 'attachment', 'attachments'];
        
        foreach ($attachmentFields as $field) {
            if ($request->hasFile($field)) {
                $files = is_array($request->file($field)) 
                    ? $request->file($field) 
                    : [$request->file($field)];
                
                foreach ($files as $index => $file) {
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
        
        return $uploadedFiles;
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
            'asuransi_kendaraan' => 'ASR',
            'pajak' => 'PJK',
            'service_part' => 'SVC',
            'gps' => 'GPS',
            'kir' => 'KIR',
            'stnk' => 'STN',
            'service_asuransi' => 'SAS',
        ];
        
        $typeCode = $typeMap[$sourceType] ?? 'PGL';
        
        // Get last PR for this type
        $lastPR = Purchasero::where('source_type', $sourceType)
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
            'asuransi_kendaraan' => 'Pembayaran Asuransi Kendaraan - ' . ($data['keterangan'] ?? 'N/A'),
            'pajak' => 'Pembayaran Pajak Kendaraan - ' . ($data['jenis_pajak'] ?? 'N/A'),
            'service_part' => 'Pembelian Service Part - ' . ($data['nama_part'] ?? 'N/A'),
            'gps' => 'Pembayaran GPS Kendaraan - ' . ($data['keterangan'] ?? 'N/A'),
            'kir' => 'Pembayaran KIR Kendaraan',
            'stnk' => 'Pembayaran STNK Kendaraan',
            'service_asuransi' => 'Klaim Asuransi Service - ' . ($data['keterangan'] ?? 'N/A'),
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
            'asuransi_kendaraan' => floatval($data['premi'] ?? 0),
            'pajak' => floatval($data['nominal'] ?? 0),
            'service_part' => floatval($data['biaya'] ?? 0),
            'gps' => floatval($data['biaya'] ?? 0),
            'kir' => floatval($data['biaya'] ?? 0),
            'stnk' => floatval($data['biaya'] ?? 0),
            'service_asuransi' => floatval($data['biaya'] ?? 0),
            default => 0,
        };
    }

    /**
     * Resubmit rejected purchasero dengan data baru
     * Update existing purchasero record, reset status to Pending, update source_data
     *
     * @param int $purchaseroId
     * @param Request $request
     * @param string $sourceType
     * @return Purchasero
     */
    public function resubmitToPurchasero(int $purchaseroId, Request $request, string $sourceType): Purchasero
    {
        DB::beginTransaction();
        
        try {
            $purchasero = Purchasero::findOrFail($purchaseroId);
            
            // Validation: Only rejected pengeluaran can be resubmitted
            if ($purchasero->status !== 'Ditolak') {
                throw new \Exception('Hanya pengajuan yang ditolak yang dapat diajukan ulang.');
            }
            
            if (!$purchasero->can_edit) {
                throw new \Exception('Pengajuan ini tidak dapat diedit.');
            }
            
            // Intercept new data
            $interceptedData = $this->intercept($request, $sourceType);
            
            // Delete old temp files
            $this->deleteTemporaryFiles($purchaseroId);
            
            // Upload new temp files
            $uploadedFiles = $this->uploadTemporaryFiles($request, $purchaseroId);
            
            // Update purchasero
            $purchasero->update([
                'source_data' => array_merge($interceptedData['source_data'], ['temp_files' => $uploadedFiles]),
                'alasan_permintaan' => $interceptedData['alasan_permintaan'],
                'nominal' => $interceptedData['nominal'],
                'nama_bank' => $interceptedData['nama_bank'] ?? null,
                'no_rekening' => $interceptedData['no_rekening'] ?? null,
                'nama_rekening' => $interceptedData['nama_rekening'] ?? null,
                'informasi' => $interceptedData['informasi'] ?? null,
                'status' => 'Pending',
                'can_edit' => false,
            ]);
            
            DB::commit();
            
            return $purchasero;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle perpanjang flow - create purchasero for renewal approval
     * 
     * @param Request $request
     * @param string $sourceType
     * @param mixed $existingRecord (GPS/Asuransi/Pajak/KIR/STNK record)
     * @return Purchasero
     */
    public function perpanjangViaPurchasero(Request $request, string $sourceType, $existingRecord): Purchasero
    {
        DB::beginTransaction();
        
        try {
            // Build perpanjang data
            $perpanjangData = $request->all();
            $perpanjangData['is_perpanjang'] = true;
            $perpanjangData['existing_record_id'] = $existingRecord->id;
            
            // Add existing record info for context
            if (method_exists($existingRecord, 'kendaraan') && $existingRecord->kendaraan) {
                $perpanjangData['kendaraan_id'] = $existingRecord->kendaraan_id;
                $perpanjangData['kendaraan_nopol'] = $existingRecord->kendaraan->nopol ?? '-';
            }
            
            // Create fake request for intercept
            $fakeRequest = new Request($perpanjangData);
            $fakeRequest->merge($request->all());
            
            // Intercept data
            $interceptedData = $this->intercept($fakeRequest, $sourceType);
            
            // Modify alasan_permintaan untuk perpanjang
            $interceptedData['alasan_permintaan'] = 'Perpanjangan ' . $this->getSourceTypeName($sourceType);
            
            // Save to Purchasero
            $purchasero = $this->saveToPurchasero($interceptedData, $sourceType);
            
            // Upload files
            $uploadedFiles = $this->uploadTemporaryFiles($fakeRequest, $purchasero->id);
            
            // Update source_data
            $sourceData = $purchasero->source_data;
            $sourceData['temp_files'] = $uploadedFiles;
            $purchasero->update(['source_data' => $sourceData]);
            
            DB::commit();
            
            return $purchasero;
            
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
            'gps' => 'GPS Kendaraan',
            'asuransi_kendaraan' => 'Asuransi Kendaraan',
            'pajak' => 'Pajak Kendaraan',
            'kir' => 'KIR',
            'stnk' => 'STNK',
            'service_asuransi' => 'Service Asuransi',
            'service_part' => 'Service Part',
            default => ucfirst($sourceType),
        };
    }

    /**
     * Delete temporary files untuk purchasero yang dibatalkan
     *
     * @param int $purchaseroId
     * @return bool
     */
    public function deleteTemporaryFiles(int $purchaseroId): bool
    {
        $tempDir = "purchasero/temp/{$purchaseroId}";
        
        try {
            Storage::disk('public')->deleteDirectory($tempDir);
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to delete temp files for purchasero {$purchaseroId}: " . $e->getMessage());
            return false;
        }
    }
}
