<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RemoveBgService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.remove.bg/v1.0/removebg';

    public function __construct()
    {
        $this->apiKey = config('services.removebg.key', '');
    }

    /**
     * Hapus background gambar dan simpan ke storage.
     * Kalau API key kosong atau API gagal, simpan file asli tanpa remove bg.
     *
     * @param  UploadedFile  $file      File yang diupload
     * @param  string        $folder    Folder tujuan di storage/public (misal: uploads/ttd)
     * @return string                   Path relatif yang disimpan ke DB
     */
    public function uploadAndRemoveBg(UploadedFile $file, string $folder = 'uploads/ttd'): string
    {
        // Jika API key belum diset, simpan langsung tanpa remove bg
        if (empty($this->apiKey)) {
            return $file->store($folder, 'public');
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(['X-Api-Key' => $this->apiKey])
                ->attach('image_file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post($this->apiUrl, [
                    'size'   => 'auto',
                    'format' => 'png',  // selalu PNG agar transparan
                ]);

            if ($response->successful()) {
                // Simpan hasil PNG transparan
                $filename = $folder . '/' . Str::uuid() . '.png';
                Storage::disk('public')->put($filename, $response->body());
                return $filename;
            }

            // API error — log dan fallback ke file asli
            Log::warning('RemoveBG API error: ' . $response->status() . ' ' . $response->body());

        } catch (\Exception $e) {
            Log::warning('RemoveBG exception: ' . $e->getMessage());
        }

        // Fallback: simpan file asli
        return $file->store($folder, 'public');
    }
}
