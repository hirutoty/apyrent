<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Jenis;
use App\Models\Member;
use App\Models\Setting;
use App\Models\ReminderService;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::selectRaw("
        merk,
        jenis_id,
        COUNT(*) as total_unit,
        SUM(CASE WHEN status_kendaraan = 'tersedia' THEN 1 ELSE 0 END) as tersedia_unit
    ")
            ->with('jenis')
            ->groupBy('merk', 'jenis_id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('merk', 'like', "%{$s}%")
                  ->orWhere('nopol', 'like', "%{$s}%");
            });
        }

        $data = $query->paginate(10)->withQueryString();

        $totalKendaraan  = Kendaraan::count();
        $totalTersedia   = Kendaraan::where('status_kendaraan', 'tersedia')->count();
        $totalHabis      = Kendaraan::where('status_kendaraan', '!=', 'tersedia')->count();

        $jenis = Jenis::all();
        $members = Member::orderBy('nama')->get();

        return view('admin.kendaraan.index', compact('data', 'jenis', 'members', 'totalKendaraan', 'totalTersedia', 'totalHabis'));
    }



    public function show($merk)
    {
        $data = Kendaraan::with(['user', 'jenis', 'member', 'rentals', 'rentals.member'])
            ->where('merk', $merk)
            ->latest()
            ->get();

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $jenis = Jenis::all();
        $members = Member::orderBy('nama')->get();

        $reminderHari = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        foreach ($data as $d) {

            $rental = $d->rentals->firstWhere('status', 'aktif');

            $d->reminder = false;
            $d->terlambat = false;
            $d->sisa = null;

            if ($rental && $rental->tanggal_selesai && $rental->tanggal_mulai) {

                $now = Carbon::now();
                $end = Carbon::parse($rental->tanggal_selesai);

                // 🔥 SELISIH DARI SEKARANG KE TANGGAL_SELESAI (real-time)
                $diffSeconds = $end->timestamp - $now->timestamp;

                if ($diffSeconds < 0) {
                    $d->terlambat = true;
                    $d->sisa = $this->formatSisa(abs($diffSeconds));
                } else {

                    if ($diffSeconds <= ($reminderHari * 86400)) {
                        $d->reminder = true;
                    }

                    $d->sisa = $this->formatSisa($diffSeconds);
                }
            }
        }

        // Ambil semua ID kendaraan pada merk ini
        $kendaraanIds = $data->pluck('id')->toArray();

        // Parts per kendaraan: ambil semua service_parts terbaru (status Terpasang/Limit)
        // grouped by kendaraan_id
        $partsPerKendaraan = \App\Models\ServicePart::with('category')
            ->whereIn('kendaraan_id', $kendaraanIds)
            ->whereIn('status', ['Terpasang', 'Limit'])
            ->orderByDesc('tgl_pasang')
            ->get()
            ->groupBy('kendaraan_id');

        return view('admin.kendaraan.show', compact('data', 'merk', 'jenis', 'setting', 'members', 'partsPerKendaraan'));
    }

    private function getMembers() {
        return Member::orderBy('nama')->get();
    }



    /**
     * Format sisa waktu: >= 1 hari → "X hari", < 1 hari → "X jam", < 1 jam → "< 1 jam"
     */
    private function formatSisa($seconds)
    {
        $seconds = (int) $seconds;
        if ($seconds <= 0)  return '0 jam';
        $hari = (int) floor($seconds / 86400);
        if ($hari >= 1)     return $hari . ' hari';
        $jam  = (int) floor($seconds / 3600);
        if ($jam  >= 1)     return $jam  . ' jam';
        return '< 1 jam';
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'jenis_id' => 'required|exists:jenis,id',
            'nopol' => 'required|unique:kendaraan,nopol',
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'dokumen' => 'required|array',
            'dokumen.*' => 'file|max:4096',
        ], [
            'member_id.required' => 'Pemilik kendaraan wajib dipilih dari data member',
            'member_id.exists' => 'Member tidak ditemukan, silakan pilih dari daftar',
            'jenis_id.required' => 'Jenis kendaraan wajib dipilih',
            'nopol.required' => 'Nomor polisi wajib diisi',
            'nopol.unique' => 'Nomor polisi sudah digunakan, tidak boleh sama',
            'foto.required' => 'Foto kendaraan wajib diupload',
            'dokumen.required' => 'Dokumen kendaraan wajib diupload',
        ]);

        $fotoFile = $request->file('foto');
        $fotoName = time() . '_' . $fotoFile->getClientOriginalName();
        $fotoFile->move(public_path('kendaraan/foto'), $fotoName);
        $foto = 'kendaraan/foto/' . $fotoName;

        $dokumenPaths = [];
        foreach ($request->file('dokumen') as $file) {
            $name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('kendaraan/dokumen'), $name);
            $dokumenPaths[] = 'kendaraan/dokumen/' . $name;
        }
        $dokumen = count($dokumenPaths) === 1 ? $dokumenPaths[0] : json_encode($dokumenPaths);

        $kendaraan = Kendaraan::create([
            'user_id' => Auth::id(), // 🔥 AUTO DARI SESSION
            'jenis_id' => $request->jenis_id,
            'member_id' => $request->member_id ?: null,
            'nopol' => $request->nopol,
            'foto' => $foto,
            'dokumen' => $dokumen,

            'harga_sewa_per_hari' => $request->harga_sewa_per_hari,
            'harga_sewa_per_jam' => $request->harga_sewa_per_jam,
            'nama_pemilik' => $request->nama_pemilik,
            'alamat' => $request->alamat,
            'merk' => $request->merk,
            'warna' => $request->warna,

            'tahun_pembuatan' => $request->tahun_pembuatan,
            'tahun_perakitan' => $request->tahun_perakitan,
            'isi_silinder' => $request->isi_silinder,
            'bahan_bakar' => $request->bahan_bakar,

            'no_rangka' => $request->no_rangka,
            'no_mesin' => $request->no_mesin,
            'no_bpkb' => $request->no_bpkb,

            'kode_lokasi' => $request->kode_lokasi,
            'no_urut_pendaftaran' => $request->no_urut_pendaftaran,

            'batas_biaya' => $request->batas_biaya,
            'masa_berlaku' => $request->masa_berlaku,

            'kilometer_sekarang' => $request->kilometer_sekarang,
            'limit_km_service' => $request->limit_km_service,
            'limit_biaya_bulanan_service' => $request->limit_biaya_bulanan_service,
            'limit_biaya_tahunan_service' => $request->limit_biaya_tahunan_service,
            'km_terakhir_service' => $request->km_terakhir_service,
            'tanggal_terakhir_service' => $request->tanggal_terakhir_service,

            'status_service' => $request->status_service,
            'status_kendaraan' => $request->status_kendaraan,
        ]);

        // 🔔 Auto-create Reminder Service +3 bulan dari tanggal_terakhir_service
        if ($request->filled('tanggal_terakhir_service')) {
            $this->buatAtauUpdateReminderService($kendaraan, $request->tanggal_terakhir_service);
        }

        return back()->with('success', 'Data kendaraan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $request->validate([
            'jenis_id' => 'required|exists:jenis,id',
            'nopol' => 'required|unique:kendaraan,nopol,' . $id,
        ], [
            'nopol.required' => 'Nomor polisi wajib diisi',
            'nopol.unique' => 'Nomor polisi sudah digunakan kendaraan lain',
        ]);

        $foto = $kendaraan->foto;
        $dokumen = $kendaraan->dokumen;

        if ($request->hasFile('foto')) {
            if ($kendaraan->foto && file_exists(public_path($kendaraan->foto))) {
                unlink(public_path($kendaraan->foto));
            }

            $fotoFile = $request->file('foto');
            $fotoName = time() . '_' . $fotoFile->getClientOriginalName();
            $fotoFile->move(public_path('kendaraan/foto'), $fotoName);

            $foto = 'kendaraan/foto/' . $fotoName;
        }

        if ($request->hasFile('dokumen')) {
            // Hapus file lama (bisa string tunggal atau JSON array)
            $oldDokumen = $kendaraan->dokumen;
            if ($oldDokumen) {
                $oldPaths = [];
                try { $oldPaths = json_decode($oldDokumen, true) ?: [$oldDokumen]; }
                catch (\Exception $e) { $oldPaths = [$oldDokumen]; }
                foreach ($oldPaths as $old) {
                    if ($old && file_exists(public_path($old))) unlink(public_path($old));
                }
            }

            $dokumenPaths = [];
            foreach ($request->file('dokumen') as $file) {
                $name = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('kendaraan/dokumen'), $name);
                $dokumenPaths[] = 'kendaraan/dokumen/' . $name;
            }
            $dokumen = count($dokumenPaths) === 1 ? $dokumenPaths[0] : json_encode($dokumenPaths);
        }

        $kendaraan->update([
            'user_id' => Auth::id(), // 🔥 AUTO SESSION
            'jenis_id' => $request->jenis_id,
            'member_id' => $request->member_id ?: null,
            'nopol' => $request->nopol,
            'foto' => $foto,
            'dokumen' => $dokumen,

            'harga_sewa_per_hari' => $request->harga_sewa_per_hari,
            'harga_sewa_per_jam' => $request->harga_sewa_per_jam,
            'nama_pemilik' => $request->nama_pemilik,
            'alamat' => $request->alamat,
            'merk' => $request->merk,
            'warna' => $request->warna,

            'tahun_pembuatan' => $request->tahun_pembuatan,
            'tahun_perakitan' => $request->tahun_perakitan,
            'isi_silinder' => $request->isi_silinder,
            'bahan_bakar' => $request->bahan_bakar,

            'no_rangka' => $request->no_rangka,
            'no_mesin' => $request->no_mesin,
            'no_bpkb' => $request->no_bpkb,

            'kode_lokasi' => $request->kode_lokasi,
            'no_urut_pendaftaran' => $request->no_urut_pendaftaran,

            'batas_biaya' => $request->batas_biaya,
            'masa_berlaku' => $request->masa_berlaku,

            'kilometer_sekarang' => $request->kilometer_sekarang,
            'limit_km_service' => $request->limit_km_service,
            'limit_biaya_bulanan_service' => $request->limit_biaya_bulanan_service,
            'limit_biaya_tahunan_service' => $request->limit_biaya_tahunan_service,
            'km_terakhir_service' => $request->km_terakhir_service,
            'tanggal_terakhir_service' => $request->tanggal_terakhir_service,

            'status_service' => $request->status_service,

        ]);

        // 🔔 Jika tanggal_terakhir_service berubah, update/create ReminderService
        if ($request->filled('tanggal_terakhir_service')) {
            $tanggalLama = $kendaraan->getOriginal('tanggal_terakhir_service');
            $tanggalBaru = $request->tanggal_terakhir_service;

            // Bandingkan: jika berubah atau belum ada reminder aktif
            if (
                Carbon::parse($tanggalLama)->toDateString() !== Carbon::parse($tanggalBaru)->toDateString()
                || !ReminderService::where('kendaraan_id', $kendaraan->id)->whereIn('status', ['aktif', 'jatuh_tempo'])->exists()
            ) {
                $this->buatAtauUpdateReminderService($kendaraan, $tanggalBaru);
            }
        }

        return back()->with('success', 'Data kendaraan berhasil diupdate');
    }

    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        // hapus foto
        if ($kendaraan->foto && file_exists(public_path($kendaraan->foto))) {
            unlink(public_path($kendaraan->foto));
        }

        // hapus dokumen
        if ($kendaraan->dokumen && file_exists(public_path($kendaraan->dokumen))) {
            unlink(public_path($kendaraan->dokumen));
        }

        $kendaraan->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function updateStatus(Request $request, Kendaraan $kendaraan)
    {
        $request->validate([
            'status_kendaraan' => 'required|in:tersedia,disewa,service,bermasalah',
            'foto_masalah.*'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'catatan_masalah'  => 'nullable|string|max:1000',
        ]);

        $data = [
            'status_kendaraan' => $request->status_kendaraan,
        ];

        // Jika bermasalah → simpan foto dan catatan
        if ($request->status_kendaraan === 'bermasalah') {
            // Upload foto-foto baru (append ke yang sudah ada, atau fresh)
            $fotoLama = $kendaraan->foto_masalah ?? [];

            if ($request->hasFile('foto_masalah')) {
                $fotos = [];
                foreach ($request->file('foto_masalah') as $file) {
                    $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('kendaraan/masalah'), $name);
                    $fotos[] = 'kendaraan/masalah/' . $name;
                }
                // Gabungkan dengan foto lama (jika ada)
                $data['foto_masalah'] = json_encode(array_merge($fotoLama, $fotos));
            }

            if ($request->filled('catatan_masalah')) {
                $data['catatan_masalah'] = $request->catatan_masalah;
            }
        } else {
            // Jika status bukan bermasalah, reset foto & catatan masalah
            // Hapus file fisik
            $fotoLama = $kendaraan->foto_masalah ?? [];
            foreach ($fotoLama as $path) {
                if ($path && file_exists(public_path($path))) {
                    unlink(public_path($path));
                }
            }
            $data['foto_masalah']    = null;
            $data['catatan_masalah'] = null;
        }

        $kendaraan->update($data);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    /**
     * Hapus satu foto masalah dari kendaraan
     */
    public function deleteFotoMasalah(Request $request, Kendaraan $kendaraan)
    {
        $request->validate([
            'foto_path' => 'required|string',
        ]);

        $path    = $request->foto_path;
        $fotos   = $kendaraan->foto_masalah ?? [];
        $fotos   = array_values(array_filter($fotos, fn($f) => $f !== $path));

        // Hapus file fisik
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }

        $kendaraan->update([
            'foto_masalah' => count($fotos) > 0 ? json_encode($fotos) : null,
        ]);

        return back()->with('success', 'Foto masalah berhasil dihapus.');
    }

    public function exportPdf($merk)
    {
        $data = Kendaraan::with(['user', 'jenis'])
            ->where('merk', $merk)
            ->get();
        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $pdf = Pdf::loadView(
            'admin.kendaraan.pdf',
            compact('data', 'merk', 'setting', 'logoSrc')
        )->setPaper('A4', 'landscape');

        return $pdf->stream(
            'laporan-kendaraan-' . str_replace(' ', '-', $merk) . '.pdf'
        );
    }

    public function exportMerkPdf()
    {
        $data = Kendaraan::selectRaw("
        merk,
        jenis_id,
        COUNT(*) as total_unit,
        SUM(CASE WHEN status_kendaraan = 'tersedia' THEN 1 ELSE 0 END) as tersedia_unit
    ")
            ->with('jenis')
            ->groupBy('merk', 'jenis_id')
            ->get();

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $pdf = Pdf::loadView(
            'admin.kendaraan.pdf_merk',
            compact('data', 'setting', 'logoSrc')
        )->setPaper('A4', 'potrait');

        return $pdf->stream('laporan-merk-kendaraan.pdf');
    }

    // ── HELPER ──────────────────────────────────────────────────────────────

    /**
     * Buat atau update ReminderService "Service Rutin" +3 bulan dari tanggal terakhir service.
     * - Jika sudah ada reminder aktif/jatuh_tempo → update tanggal_mulai & jatuh_tempo.
     * - Jika belum ada → buat baru.
     */
    private function buatAtauUpdateReminderService(Kendaraan $kendaraan, string $tanggalTerakhirService): void
    {
        $tanggalMulai  = Carbon::parse($tanggalTerakhirService);
        $jatuhTempo    = (clone $tanggalMulai)->addMonths(3);
        $statusReminder = Carbon::today()->gte($jatuhTempo) ? 'jatuh_tempo' : 'aktif';

        // Cari reminder aktif / jatuh_tempo yang sudah ada untuk kendaraan ini
        $existing = ReminderService::where('kendaraan_id', $kendaraan->id)
            ->where('nama_reminder', 'Service Rutin')
            ->whereIn('status', ['aktif', 'jatuh_tempo'])
            ->first();

        if ($existing) {
            $existing->update([
                'tanggal_mulai'        => $tanggalMulai->toDateString(),
                'interval_nilai'       => 3,
                'interval_satuan'      => 'bulan',
                'tanggal_jatuh_tempo'  => $jatuhTempo->toDateString(),
                'status'               => $statusReminder,
                'sudah_dibuat_masalah' => false,
            ]);
        } else {
            ReminderService::create([
                'kendaraan_id'         => $kendaraan->id,
                'nama_reminder'        => 'Service Rutin',
                'tanggal_mulai'        => $tanggalMulai->toDateString(),
                'interval_nilai'       => 3,
                'interval_satuan'      => 'bulan',
                'tanggal_jatuh_tempo'  => $jatuhTempo->toDateString(),
                'keterangan'           => 'Auto-generated dari tanggal terakhir service kendaraan',
                'status'               => $statusReminder,
                'sudah_dibuat_masalah' => false,
            ]);
        }
    }

    /**
     * Display service history for specific kendaraan
     */
    public function serviceHistory($id)
    {
        $kendaraan = Kendaraan::with(['jenis'])->findOrFail($id);
        
        $serviceHistory = \App\Models\ServiceHistory::with([
                'kendaraan.jenis',
                'attachments',
                'parts.category',
            ])
            ->where('kendaraan_id', $id)
            ->latest('tanggal_service')
            ->paginate(20);

        // Summary stats untuk kendaraan ini
        $totalService = \App\Models\ServiceHistory::where('kendaraan_id', $id)->count();
        $totalBiaya = \App\Models\ServiceHistory::where('kendaraan_id', $id)->sum('total_biaya');
        $lastService = \App\Models\ServiceHistory::where('kendaraan_id', $id)
            ->latest('tanggal_service')
            ->first();
        $avgBiaya = $totalService > 0 ? $totalBiaya / $totalService : 0;

        $categories = \App\Models\ServiceCategory::orderBy('nama')->get();

        return view('admin.kendaraan.service_history', compact(
            'kendaraan',
            'serviceHistory',
            'totalService',
            'totalBiaya',
            'lastService',
            'avgBiaya',
            'categories'
        ));
    }
}
