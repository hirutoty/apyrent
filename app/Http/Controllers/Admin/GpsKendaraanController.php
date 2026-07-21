<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GpsKendaraan;
use App\Models\Gps;
use App\Models\Kendaraan;
use App\Models\Setting;
use App\Models\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\GpsKendaraanHistory;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\User;

class GpsKendaraanController extends Controller
{
    public function index()
    {
        GpsKendaraan::where('tanggal_habis', '<=', now())
            ->where('status_sewa', 'aktif')
            ->update(['status_sewa' => 'habis']);

        $data = GpsKendaraan::with(['kendaraan', 'gps', 'attachments'])->latest()->paginate(15)->withQueryString();
        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $reminder = match ($setting->satuan_reminder) {
            'hari'    => $setting->batas_reminder,
            'minggu'  => $setting->batas_reminder * 7,
            'bulan'   => $setting->batas_reminder * 30,
            'tahun'   => $setting->batas_reminder * 365,
            default   => $setting->batas_reminder,
        };

        return view('admin.gps.gps_kendaraan', [
            'data'      => $data,
            'reminder'  => $reminder,
            'gps'       => Gps::all(),
            'kendaraan' => Kendaraan::all(),
        ]);
    }

    /**
     * Helper: simpan banyak attachment sekaligus.
     *
     * @param  array   $files        Array file dari $request->file(...)
     * @param  int     $relationId   ID record target (gps aktif atau history)
     * @param  string  $relationType Tipe relasi, default 'gps'
     */
    private function simpanAttachments($files, $relationId, $relationType = 'gps', $historyId = null)
    {
        $pathDir = public_path('gps/attachments');
        if (!file_exists($pathDir)) mkdir($pathDir, 0777, true);

        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // ambil data SEBELUM file dipindah
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $size         = $file->getSize();

            $file->move($pathDir, $filename);

            Attachment::create([
                'relation_type' => $relationType,
                'relation_id'   => $relationId,
                'file_name'     => $originalName,
                'file_path'     => 'gps/attachments/' . $filename,
                'file_type'     => $extension,
                'file_size'     => $size,
            ]);

            if ($historyId) {
                Attachment::create([
                    'relation_type' => $relationType . '_history',
                    'relation_id'   => $historyId,
                    'file_name'     => $originalName,
                    'file_path'     => 'gps/attachments/' . $filename,
                    'file_type'     => $extension,
                    'file_size'     => $size,
                ]);
            }
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'  => 'required|exists:kendaraan,id',
            'gps_id'        => 'required|exists:gps,id',
            'type'          => 'required',
            'status_gps'    => 'required',
            'tanggal_pasang' => 'required|date',
            'tanggal_habis' => 'required|date',
            'biaya_sewa'    => 'required|integer',
            'durasi_bulan'  => 'required|integer',
            'bukti_bayar' => 'nullable|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);
        $exists = GpsKendaraan::where('kendaraan_id', $request->kendaraan_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kendaraan ini sudah terdaftar pada GPS Kendaraan');
        }

        $status = now()->lte($request->tanggal_habis) ? 'aktif' : 'habis';

        // Upload bukti bayar
        $buktiPath = null;

        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('gps/bukti_bayar'), $filename);

            $buktiPath = 'gps/bukti_bayar/' . $filename;
        }

        $gpsKendaraan = GpsKendaraan::create([
            'kendaraan_id'  => $request->kendaraan_id,
            'gps_id'        => $request->gps_id,
            'type'          => $request->type,
            'status_gps'    => $request->status_gps,
            'tanggal_pasang' => $request->tanggal_pasang,
            'tanggal_habis' => $request->tanggal_habis,
            'biaya_sewa'    => $request->biaya_sewa,
            'durasi_bulan'  => $request->durasi_bulan,
            'status_sewa'   => $status,
            'bukti_bayar'   => $buktiPath,
        ]);

        // upload attachment tambahan (bisa lebih dari satu, SETELAH ADA ID)
        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $gpsKendaraan->id);
        }

        return back()->with('success', 'Data GPS kendaraan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id'  => 'required|exists:kendaraan,id',
            'gps_id'        => 'required|exists:gps,id',
            'type'          => 'required',
            'status_gps'    => 'required',
            'tanggal_pasang' => 'required|date',
            'tanggal_habis' => 'required|date',
            'biaya_sewa'    => 'required|integer',
            'durasi_bulan'  => 'required|integer',
            'bukti_bayar' => 'nullable|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $exists = GpsKendaraan::where('kendaraan_id', $request->kendaraan_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kendaraan ini sudah terdaftar pada GPS Kendaraan');
        }

        $data   = GpsKendaraan::findOrFail($id);
        $status = now()->lte($request->tanggal_habis) ? 'aktif' : 'habis';

        // Upload bukti bayar baru, hapus yang lama
        $buktiPath = $data->bukti_bayar;
        if ($request->hasFile('bukti_bayar')) {
            if ($buktiPath && file_exists(public_path($buktiPath))) {
                unlink(public_path($buktiPath));
            }

            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('gps/bukti_bayar'), $filename);

            $buktiPath = 'gps/bukti_bayar/' . $filename;
        }

        $data->update([
            'kendaraan_id'  => $request->kendaraan_id,
            'gps_id'        => $request->gps_id,
            'type'          => $request->type,
            'status_gps'    => $request->status_gps,
            'tanggal_pasang' => $request->tanggal_pasang,
            'tanggal_habis' => $request->tanggal_habis,
            'biaya_sewa'    => $request->biaya_sewa,
            'durasi_bulan'  => $request->durasi_bulan,
            'status_sewa'   => $status,
            'bukti_bayar'   => $buktiPath,
        ]);

        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $data->id);
        }

        return back()->with('success', 'Data GPS kendaraan berhasil diupdate');
    }

    public function destroy($id)
    {
        $data = GpsKendaraan::findOrFail($id);

        if ($data->bukti_bayar && file_exists(public_path($data->bukti_bayar))) {
            unlink(public_path($data->bukti_bayar));
        }

        // hapus semua file attachment terkait
        foreach ($data->attachments as $att) {
            if (file_exists(public_path($att->file_path))) {
                unlink(public_path($att->file_path));
            }
            $att->delete();
        }

        $data->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    /**
     * Hapus 1 attachment tertentu
     */
    public function destroyAttachment($id)
    {
        $attachment = Attachment::where('relation_type', 'gps')->findOrFail($id);

        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }

        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        $search = $request->search;

        $query = GpsKendaraan::with(['kendaraan', 'gps', 'attachments']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                    ->orWhere('status_gps', 'like', "%{$search}%")
                    ->orWhereHas(
                        'kendaraan',
                        fn($k) =>
                        $k->where('merk', 'like', "%{$search}%")
                            ->orWhere('nopol', 'like', "%{$search}%")
                    )
                    ->orWhereHas(
                        'gps',
                        fn($g) =>
                        $g->where('nama_gps', 'like', "%{$search}%")
                    );
            });
        }

        $data    = $query->latest()->get();
        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView(
            'admin.gps.pdf_gps_kendaraan',
            compact('data', 'search', 'setting', 'logoSrc')
        )->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-gps-kendaraan.pdf');
    }

    public function perpanjang(Request $request, $id)
    {
        $request->validate([
            'durasi_bulan'   => 'required|integer|min:1',
            'biaya_sewa'     => 'required|integer',
            'tanggal_bayar'  => 'nullable|date',
            'bukti_bayar'    => 'required|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $gpsKendaraan = GpsKendaraan::findOrFail($id);

        // Cek: masa berlaku masih > 30 hari ke depan, perpanjangan belum diperlukan
        if ($gpsKendaraan->tanggal_habis && Carbon::parse($gpsKendaraan->tanggal_habis)->diffInDays(now(), false) < -30) {
            return back()->with('error', 'Masa berlaku GPS masih panjang (> 30 hari), perpanjangan belum diperlukan.');
        }

        // --- Hitung tanggal standar ---
        // tanggal_bayar (dari request, default hari ini) → tanggal_pasang baru
        $tanggalBayar  = $request->filled('tanggal_bayar')
            ? Carbon::parse($request->tanggal_bayar)->toDateString()
            : now()->toDateString();
        $tanggalPasang = $tanggalBayar;
        // tanggal_habis baru = tanggal_habis LAMA + 1 tahun (dari DB, bukan dari request)
        $tanggalHabis  = Carbon::parse($gpsKendaraan->tanggal_habis)->addYear()->toDateString();

        // --- Simpan path bukti LAMA sebelum upload ---
        $buktiBayarLama = $gpsKendaraan->bukti_bayar;

        // --- Upload bukti BARU ---
      $path = public_path('gps/bukti_bayar');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $buktiBayarBaru = $buktiBayarLama; // fallback jika tidak ada upload
        if ($request->hasFile('bukti_bayar')) {
            $file     = $request->file('bukti_bayar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $filename);
            $buktiBayarBaru = 'gps/bukti_bayar/' . $filename;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $request, $gpsKendaraan, $buktiBayarLama, $buktiBayarBaru, $tanggalBayar, $tanggalPasang, $tanggalHabis
        ) {
            // --- Simpan data BARU ke history (sebagai log perpanjangan) ---
            $history = GpsKendaraanHistory::create([
                'gps_id'           => $gpsKendaraan->gps_id,
                'gps_kendaraan_id' => $gpsKendaraan->id,
                'kendaraan_id'     => $gpsKendaraan->kendaraan_id,
                'type'             => $gpsKendaraan->type,
                'vendor_id'        => $gpsKendaraan->vendor_id,
                'tanggal_pasang'   => $tanggalPasang,
                'tanggal_habis'    => $tanggalHabis,
                'durasi_bulan'     => $request->durasi_bulan,
                'biaya_sewa'       => $request->biaya_sewa,
                'status_sewa'      => 'aktif',
                'status_gps'       => $gpsKendaraan->status_gps,
                'bukti_bayar'      => $buktiBayarBaru,
                'tanggal_bayar'    => $tanggalBayar,
                'diperpanjang_pada'=> now(),
            ]);

            // --- Catat ke Keuangan ---
            $lastSaldo = (float) \Illuminate\Support\Facades\DB::table('keuangans')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
            $pengeluaran = $request->biaya_sewa;
            // Kode jurnal unik per transaksi — pakai timestamp agar perpanjangan ke-2, ke-3 dst tetap masuk
            $kodeJurnal  = 'GPS-' . $gpsKendaraan->id . '-' . now()->timestamp;

            Keuangan::create([
                'tanggal'     => now(),
                'reference'   => $kodeJurnal,
                'user_id'     => auth()->id(),
                'kategori'    => 'Pengeluaran',
                'metode'      => '-',
                'keterangan'  => 'Perpanjangan GPS kendaraan: ' . $gpsKendaraan->type . ' - ' . ($gpsKendaraan->kendaraan->nopol ?? '-'),
                'pemasukan'   => 0,
                'pengeluaran' => $pengeluaran,
                'saldo'       => $lastSaldo - $pengeluaran,
            ]);

            // --- Auto-posting ke Buku Besar (tanpa pengecekan duplikat — kode jurnal sudah unik) ---
            $saldoBBTerakhir = (float) \Illuminate\Support\Facades\DB::table('bukubesars')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
            Bukubesar::create([
                'kode_jurnal' => $kodeJurnal,
                'transaksi'   => 'Beban GPS - ' . $gpsKendaraan->type,
                'kategori'    => 'Beban',
                'tanggal'     => now()->toDateString(),
                'debit'       => $pengeluaran,
                'kredit'      => 0,
                'saldo'       => $saldoBBTerakhir - $pengeluaran,
                'aktivitas'   => 'Operasi',
                'keterangan'  => 'Auto-posting: Perpanjangan GPS kendaraan ' . ($gpsKendaraan->kendaraan->nopol ?? '-'),
            ]);

            // --- Update record aktif dengan data BARU ---
            $gpsKendaraan->update([
                'status_gps'     => 'aktif',
                'tanggal_pasang' => $tanggalPasang,
                'tanggal_habis'  => $tanggalHabis,
                'durasi_bulan'   => $request->durasi_bulan,
                'biaya_sewa'     => $request->biaya_sewa,
                'status_sewa'    => 'aktif',
                'bukti_bayar'    => $buktiBayarBaru,
                'tanggal_bayar'  => $tanggalBayar,
            ]);

            // --- Pindahkan lampiran LAMA ke history ---
            // (Dihapus karena history sekarang mencatat log baru)

            // --- Upload attachment tambahan BARU — masuk ke record aktif (halaman utama) & History ---
            if ($request->hasFile('bukti_attachment')) {
                Attachment::where('relation_type', 'gps')->where('relation_id', $gpsKendaraan->id)->delete();
                $this->simpanAttachments($request->file('bukti_attachment'), $gpsKendaraan->id, 'gps', $history->id);
            } else {
                $oldAttachments = Attachment::where('relation_type', 'gps')->where('relation_id', $gpsKendaraan->id)->get();
                foreach ($oldAttachments as $att) {
                    Attachment::create([
                        'relation_type' => 'gps_history',
                        'relation_id'   => $history->id,
                        'file_name'     => $att->file_name,
                        'file_path'     => $att->file_path,
                        'file_type'     => $att->file_type,
                        'file_size'     => $att->file_size,
                    ]);
                }
            }
        });

        return back()->with('success', 'GPS kendaraan berhasil diperpanjang');
    }
}