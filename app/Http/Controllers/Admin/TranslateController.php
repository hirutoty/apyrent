<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TranslateController
{
    /**
     * Proxy endpoint: terima teks dari frontend, kirim ke DeepL, kembalikan hasil.
     * Dipanggil via POST /admin/translate
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text'        => 'required|string|max:50000',
            'source_lang' => 'nullable|string|size:2',
            'target_lang' => 'nullable|string|max:5',
        ]);

        $apiKey = config('services.deepl.key');

        if (empty($apiKey) || $apiKey === 'your-deepl-api-key-here') {
            return response()->json([
                'error' => 'DeepL API key belum dikonfigurasi. Isi DEEPL_API_KEY di file .env',
            ], 503);
        }

        $text       = $request->input('text');
        $sourceLang = strtoupper($request->input('source_lang', 'ID'));
        $targetLang = strtoupper($request->input('target_lang', 'EN-GB'));

        // DeepL Free API endpoint (note: api-free.deepl.com untuk free tier)
        $endpoint = 'https://api-free.deepl.com/v2/translate';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post($endpoint, [
                'text'        => [$text],
                'source_lang' => $sourceLang,
                'target_lang' => $targetLang,
                'preserve_formatting' => true,
            ]);

            \Log::info('DeepL response', [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
            ]);

            if ($response->failed()) {
                $status = $response->status();
                $msg = match($status) {
                    403 => 'API key tidak valid atau tidak aktif.',
                    456 => 'Kuota karakter DeepL habis untuk bulan ini.',
                    429 => 'Terlalu banyak request. Coba lagi sebentar.',
                    default => 'DeepL error: HTTP ' . $status,
                };
                return response()->json(['error' => $msg], $status);
            }

            $data        = $response->json();
            $translated  = $data['translations'][0]['text'] ?? '';

            return response()->json(['translated' => $translated]);

        } catch (\Exception $e) {
            \Log::error('DeepL exception', ['msg' => $e->getMessage()]);
            return response()->json([
                'error' => 'Gagal menghubungi DeepL: ' . $e->getMessage(),
            ], 500);
        }
    }
}
