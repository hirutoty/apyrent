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
                'ratio' => 'nullable|string',
                
                // Foto wajib semua
                'foto_depan' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'foto_atas' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'foto_belakang' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'foto_sampingkiri' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'foto_sampingkanan' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                
                // Foto tambahan (multiple)
                'foto_tambahan' => 'required|array|min:1',
                'foto_tambahan.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'ket_tambahan' => 'nullable|array',
                'ket_tambahan.*' => 'nullable|string|max:255',
                
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
                'ban_depan_kanan' => 'nullable|in:B,R',
                'ban_depan_kiri' => 'nullable|in:B,R',
                'ban_belakang_kanan' => 'nullable|in:B,R',
                'ban_belakang_kiri' => 'nullable|in:B,R',
                'ban_ganda_depan_kanan' => 'nullable|in:B,R',
                'ban_ganda_depan_kiri' => 'nullable|in:B,R',
                'ban_ganda_belakang_kanan' => 'nullable|in:B,R',
                'ban_ganda_belakang_kiri' => 'nullable|in:B,R',
                'kondisi_ac' => 'nullable|in:B,R',
                'kondisi_lampu' => 'nullable|in:B,R',
                'lampu_sein' => 'nullable|in:B,R',
                'lampu_bik_plat' => 'nullable|in:B,R',
                'lampu_hazard' => 'nullable|in:B,R',
                'lampu_kota' => 'nullable|in:B,R',
                'lampu_besar_kecil' => 'nullable|in:B,R',
                'lampu_mundur' => 'nullable|in:B,R',
                'lampu_rem_belakang' => 'nullable|in:B,R',
                'lampu_rem_depan' => 'nullable|in:B,R',
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
                
                // Section B: Pemeriksaan Khusus Truck & Keselamatan (28 items)
                'wiper_washer' => 'nullable|in:B,R',
                'kaca_spion_kendaraan' => 'nullable|in:B,R',
                'sabuk_pengaman' => 'nullable|in:B,R',
                'ganjal_roda' => 'nullable|in:B,R',
                'apar' => 'nullable|in:B,R',
                
                // Section C: Kelengkapan Dokumen (5 items)
                'kir_berlaku' => 'nullable|in:B,R',
                'sim_pengemudi' => 'nullable|in:B,R',
                'sim_a' => 'nullable|in:B,R',
                'sim_b1' => 'nullable|in:B,R',
                'sim_b1_umum' => 'nullable|in:B,R',
                'sim_b2' => 'nullable|in:B,R',
                'sim_b2_umum' => 'nullable|in:B,R',
                'dokumen_surat_jalan' => 'nullable|in:B,R',
                'kesesuaian_muatan' => 'nullable|in:B,R',
                
                // Section B continued: Pemeriksaan Khusus Truck
                'sistem_rem_angin' => 'nullable|in:B,R',
                'tekanan_angin_tabung' => 'nullable|in:B,R',
                'kompresor_angin_saluran' => 'nullable|in:B,R',
                'brake_chamber_slack_adjuster' => 'nullable|in:B,R',
                'rem_parkir_tangan' => 'nullable|in:B,R',
                'transmisi_persneling' => 'nullable|in:B,R',
                'gardan_kebocoran_oli' => 'nullable|in:B,R',
                'kopel_propeller_universal' => 'nullable|in:B,R',
                'suspensi_per_daun' => 'nullable|in:B,R',
                'shock_absorber' => 'nullable|in:B,R',
                'chassis_rangka_baut' => 'nullable|in:B,R',
                'mur_baut_roda_velg' => 'nullable|in:B,R',
                'bak_box_karoseri_lantai' => 'nullable|in:B,R',
                'pintu_bak_sistem_pengunci' => 'nullable|in:B,R',
                'engsel_pengait_pengaman' => 'nullable|in:B,R',
                'terpal_tali_pengikat' => 'nullable|in:B,R',
                
                'sopir' => 'nullable|string',
                'ttd_supir' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'mekanis' => 'nullable|string',
                'ttd_mekanik' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'keterangan' => 'nullable|string',
            ]);

            // Upload foto kendaraan
            $fotoFields = ['foto_depan', 'foto_atas', 'foto_belakang', 'foto_sampingkiri', 'foto_sampingkanan'];
            foreach ($fotoFields as $field) {
                if ($request->hasFile($field)) {
                    $validated[$field] = $request->file($field)->store('ceklist_kondisi', 'public');
                }
            }

            // Upload foto tambahan (multiple)
            $fotoTambahanData = [];
            if ($request->hasFile('foto_tambahan')) {
                $fotoFiles = $request->file('foto_tambahan');
                $ketArray = $request->input('ket_tambahan', []);
                
                foreach ($fotoFiles as $index => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('ceklist_kondisi/tambahan', 'public');
                        $fotoTambahanData[] = [
                            'foto' => $path,
                            'keterangan' => $ketArray[$index] ?? null
                        ];
                    }
                }
            }
            $validated['foto_tambahan'] = $fotoTambahanData;

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
            \Log::error('Validation errors:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', array_map(fn($err) => is_array($err) ? implode(', ', $err) : $err, $e->errors())),
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
        
        // Hapus foto tambahan
        if ($ceklist->foto_tambahan && is_array($ceklist->foto_tambahan)) {
            foreach ($ceklist->foto_tambahan as $fotoData) {
                if (isset($fotoData['foto']) && Storage::disk('public')->exists($fotoData['foto'])) {
                    Storage::disk('public')->delete($fotoData['foto']);
                }
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
