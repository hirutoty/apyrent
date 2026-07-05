<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Jenis;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KendaraanController extends Controller
{
    public function index()
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

        $jenis = Jenis::all();
        $kendaraanDetail = Kendaraan::with(['user', 'jenis', 'member'])->latest()->get();

        return view('admin.kendaraan.index', compact('data', 'jenis', 'kendaraanDetail'));
    }



    public function show($merk)
    {
        $data = Kendaraan::with(['user', 'jenis', 'member', 'rentals'])
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

        $reminderHari = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        foreach ($data as $d) {

            $rental = $d->rentals->first();

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

        return view('admin.kendaraan.show', compact('data', 'merk', 'jenis', 'setting'));
    }



    /**
     * 🔥 FORMAT SISA WAKTU: HARI KALAU >= 1 HARI, JAM KALAU < 1 HARI
     */
    private function formatSisa($seconds)
    {
        if ($seconds >= 86400) {
            return floor($seconds / 86400) . ' hari';
        }

        if ($seconds >= 3600) {
            return floor($seconds / 3600) . ' jam';
        }

        if ($seconds >= 60) {
            return floor($seconds / 60) . ' menit';
        }

        return $seconds . ' detik';
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_id' => 'required|exists:jenis,id',
            'nopol' => 'required|unique:kendaraan,nopol',
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'dokumen' => 'required|file|max:4096',
        ], [
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

        $dokumenFile = $request->file('dokumen');
        $dokumenName = time() . '_' . $dokumenFile->getClientOriginalName();
        $dokumenFile->move(public_path('kendaraan/dokumen'), $dokumenName);
        $dokumen = 'kendaraan/dokumen/' . $dokumenName;

        Kendaraan::create([
            'user_id' => Auth::id(), // 🔥 AUTO DARI SESSION
            'jenis_id' => $request->jenis_id,
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

            'batas_biaya' => $request->batas_biaya,
            'masa_berlaku' => $request->masa_berlaku,

            'kilometer_sekarang' => $request->kilometer_sekarang,
            'limit_km_service' => $request->limit_km_service,
            'limit_bulan_service' => $request->limit_bulan_service,
            'km_terakhir_service' => $request->km_terakhir_service,
            'tanggal_terakhir_service' => $request->tanggal_terakhir_service,

            'status_service' => $request->status_service,
            'status_kendaraan' => $request->status_kendaraan,
        ]);

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
            if ($kendaraan->dokumen && file_exists(public_path($kendaraan->dokumen))) {
                unlink(public_path($kendaraan->dokumen));
            }

            $dokumenFile = $request->file('dokumen');
            $dokumenName = time() . '_' . $dokumenFile->getClientOriginalName();
            $dokumenFile->move(public_path('kendaraan/dokumen'), $dokumenName);

            $dokumen = 'kendaraan/dokumen/' . $dokumenName;
        }

        $kendaraan->update([
            'user_id' => Auth::id(), // 🔥 AUTO SESSION
            'jenis_id' => $request->jenis_id,
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

            'batas_biaya' => $request->batas_biaya,
            'masa_berlaku' => $request->masa_berlaku,

            'kilometer_sekarang' => $request->kilometer_sekarang,
            'limit_km_service' => $request->limit_km_service,
            'limit_bulan_service' => $request->limit_bulan_service,
            'km_terakhir_service' => $request->km_terakhir_service,
            'tanggal_terakhir_service' => $request->tanggal_terakhir_service,

            'status_service' => $request->status_service,

        ]);

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
        ]);

        $kendaraan->update([
            'status_kendaraan' => $request->status_kendaraan,
        ]);

        return back()->with('success', 'Status berhasil diperbarui.');
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
}
