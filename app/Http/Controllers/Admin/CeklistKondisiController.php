<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CeklistKondisi;
use App\Models\Kendaraan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CeklistKondisiController extends Controller
{
    /**
     * Display a listing per kendaraan (untuk tab)
     */
    public function index()
    {
        $kendaraans = Kendaraan::with(['ceklistKondisi' => function($query) {
            $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');
        }])->get();

        return view('admin.service.ceklist_kondisi.index', compact('kendaraans'));
    }

    /**
     * Show the form for creating a new resource
     */
    public function create($kendaraanId)
    {
        $kendaraan = Kendaraan::findOrFail($kendaraanId);
        return view('admin.service.ceklist_kondisi.create', compact('kendaraan'));
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kendaraan_id' => 'required|exists:kendaraan,id',
                'nama' => 'required|string|max:255',
                'perusahaan' => 'required|string|max:255',
                'alamat' => 'required|string',
                'no_telephone' => 'required|string|max:20',
                'tanggal' => 'required|date',
                'kilometer' => 'required|numeric',
                'jam' => 'required',
                'fuel' => 'required|in:E,1/8,1/4,1/2,3/4,F',
                'pemeriksa' => 'required|string|max:255',
                'status' => 'nullable|in:status,leasing,gs,replace,expedisi',
                'pemakaian_bbm' => 'nullable|string',
                'ratio' => 'nullable|string',
                
                // Foto wajib semua
                'foto_depan' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'foto_atas' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'foto_belakang' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'foto_sampingkiri' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'foto_sampingkanan' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                
                // Keterangan
                'ket_depan' => 'nullable|string',
                'ket_atas' => 'nullable|string',
                'ket_belakang' => 'nullable|string',
                'ket_sampingkiri' => 'nullable|string',
                'ket_sampingkanan' => 'nullable|string',
                'accecoris_tambahan' => 'nullable|string',
                
                // Kondisi (nullable, B atau R)
                'kondisi_mesin' => 'nullable|in:B,R',
                'kondisi_rem' => 'nullable|in:B,R',
                'kondisi_kopling' => 'nullable|in:B,R',
                'kondisi_ban' => 'nullable|in:B,R',
                'kondisi_ac' => 'nullable|in:B,R',
                'lampu_lampusen' => 'nullable|in:B,R',
                'lampu_lampubesarkecil' => 'nullable|in:B,R',
                'lampu_rembikplat' => 'nullable|in:B,R',
                'accu' => 'nullable|in:B,R',
                'airradiator' => 'nullable|in:B,R',
                'olimesin_remkopling' => 'nullable|in:B,R',
                'olipower_steering' => 'nullable|in:B,R',
                'kebersihan_interior' => 'nullable|in:B,R',
                'kerapihanbody_cat' => 'nullable|in:B,R',
                'perlengkapan' => 'nullable|in:B,R',
                'ban_cadang' => 'nullable|in:B,R',
                'dongkrak' => 'nullable|in:B,R',
                'kunciroda' => 'nullable|in:B,R',
                'radio_tape' => 'nullable|in:B,R',
                'merk' => 'nullable|in:B,R',
                'karpet_lantai' => 'nullable|in:B,R',
                'doproda' => 'nullable|in:B,R',
                'pematikapi' => 'nullable|in:B,R',
                'segitiga_pengaman' => 'nullable|in:B,R',
                'kotakobat' => 'nullable|in:B,R',
                'stnk' => 'nullable|in:B,R',
                'kuncipintu' => 'nullable|in:B,R',
                'spedoometer' => 'nullable|in:B,R',
                
                'sopir' => 'nullable|string',
                'ttd_supir' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'mekanis' => 'nullable|string',
                'ttd_mekanik' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'keterangan' => 'nullable|string',
            ]);

            // Upload foto kendaraan
            $fotoFields = ['foto_depan', 'foto_atas', 'foto_belakang', 'foto_sampingkiri', 'foto_sampingkanan'];
            foreach ($fotoFields as $field) {
                if ($request->hasFile($field)) {
                    $validated[$field] = $request->file($field)->store('ceklist_kondisi', 'public');
                }
            }

            // Upload TTD
            $ttdFields = ['ttd_supir', 'ttd_mekanik'];
            foreach ($ttdFields as $field) {
                if ($request->hasFile($field)) {
                    $validated[$field] = $request->file($field)->store('ceklist_kondisi/ttd', 'public');
                }
            }

            CeklistKondisi::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Ceklist kondisi berhasil disimpan!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving ceklist kondisi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource
     */
    public function show($id)
    {
        $ceklist = CeklistKondisi::with('kendaraan')->findOrFail($id);
        return view('admin.service.ceklist_kondisi.show', compact('ceklist'));
    }

    /**
     * Print ceklist kondisi (F4 format)
     */
    public function print($id)
    {
        $ceklist = CeklistKondisi::with('kendaraan')->findOrFail($id);
        $setting = \App\Models\Setting::first();
        
        // Convert logo to base64 for PDF
        $logoSrc = '';
        if ($setting && $setting->logo) {
            $logoPath = $this->resolveLogoPath($setting->logo);
            if ($logoPath && file_exists($logoPath)) {
                $mime = mime_content_type($logoPath) ?: 'image/png';
                $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }
        
        return view('admin.service.ceklist_kondisi.print', compact('ceklist', 'setting', 'logoSrc'));
    }
    
    /**
     * Resolve logo file path
     */
    private function resolveLogoPath($relativePath)
    {
        if (empty($relativePath)) {
            return '';
        }

        // Try storage/app/public/
        $storagePath = storage_path('app/public/' . ltrim($relativePath, '/'));
        if (file_exists($storagePath) && is_readable($storagePath)) {
            return $storagePath;
        }

        // Try public/
        $publicPath = public_path(ltrim($relativePath, '/'));
        if (file_exists($publicPath) && is_readable($publicPath)) {
            return $publicPath;
        }

        return '';
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy($id)
    {
        $ceklist = CeklistKondisi::findOrFail($id);
        
        // Hapus foto kendaraan
        $fotoFields = ['foto_depan', 'foto_atas', 'foto_belakang', 'foto_sampingkiri', 'foto_sampingkanan'];
        foreach ($fotoFields as $field) {
            if ($ceklist->$field && Storage::disk('public')->exists($ceklist->$field)) {
                Storage::disk('public')->delete($ceklist->$field);
            }
        }
        
        // Hapus TTD
        $ttdFields = ['ttd_supir', 'ttd_mekanik'];
        foreach ($ttdFields as $field) {
            if ($ceklist->$field && Storage::disk('public')->exists($ceklist->$field)) {
                Storage::disk('public')->delete($ceklist->$field);
            }
        }
        
        $ceklist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ceklist kondisi berhasil dihapus!'
        ]);
    }
}
