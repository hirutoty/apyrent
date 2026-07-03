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
use App\Models\GpsKendaraanHistory;
use App\Models\Keuangan;
use App\Models\User;

class GpsKendaraanController extends Controller
{
    public function index()
    {
        GpsKendaraan::where('tanggal_habis', '<=', now())
            ->where('status_sewa', 'aktif')
            ->update(['status_sewa' => 'habis']);

        $data = GpsKendaraan::with(['kendaraan', 'gps', 'attachments'])->latest()->get();
        $setting = Setting::first();

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
     * Helper: simpan banyak attachment sekaligus untuk 1 GPS kendaraan
     * (konsisten dengan PajakController & AsuransiKendaraanController)
     */
    private function simpanAttachments($files, $gpsKendaraanId)
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
                'relation_type' => 'gps',
                'relation_id'   => $gpsKendaraanId,
                'file_name'     => $originalName,
                'file_path'     => 'gps/attachments/' . $filename,
                'file_type'     => $extension,
                'file_size'     => $size,
            ]);
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

        $pdf = Pdf::loadView(
            'admin.gps.pdf_gps_kendaraan',
            compact('data', 'search', 'setting')
        )->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-gps-kendaraan.pdf');
    }

    public function perpanjang(Request $request, $id)
    {
        $request->validate([
            'status_gps'     => 'required',
            'tanggal_pasang' => 'required|date',
            'durasi_bulan'   => 'required|integer|min:1',
            'tanggal_habis'  => 'required|date',
            'biaya_sewa'     => 'required|integer',
            'bukti_bayar'    => 'nullable|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $gpsKendaraan = GpsKendaraan::findOrFail($id);
        $bukti = $gpsKendaraan->bukti_bayar;

        $path = public_path('gps/bukti_bayar');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($request->hasFile('bukti_bayar')) {

            $file = $request->file('bukti_bayar');

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move($path, $filename);

            if ($bukti && file_exists(public_path($bukti))) {
                unlink(public_path($bukti));
            }

            $bukti = 'gps/bukti_bayar/' . $filename;
        }

        $finalBukti = $bukti;

        // Simpan data lama ke history
        GpsKendaraanHistory::create([
            'gps_kendaraan_id'  => $gpsKendaraan->id,
            'kendaraan_id'      => $gpsKendaraan->kendaraan_id,
            'gps_id'            => $gpsKendaraan->gps_id,
            'type'              => $gpsKendaraan->type,
            'status_gps'        => $gpsKendaraan->status_gps,
            'tanggal_pasang'    => $gpsKendaraan->tanggal_pasang,
            'tanggal_habis'     => $gpsKendaraan->tanggal_habis,
            'biaya_sewa'        => $gpsKendaraan->biaya_sewa,
            'durasi_bulan'      => $gpsKendaraan->durasi_bulan,
            'status_sewa'       => $gpsKendaraan->status_sewa,
            'bukti_bayar'       => $finalBukti,
            'diperpanjang_pada' => now(),
        ]);

        // 🔥 MASUK KE KEUANGAN (PENGELUARAN)
        $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;
        $pengeluaran = $request->biaya_sewa;

        Keuangan::create([
            'tanggal'      => now(),
            'reference'    => 'GPS-' . $gpsKendaraan->id,
            'user_id'      => auth()->id(),
            'kategori'     => 'gps_kendaraan',
            'metode'       => '-',
            'keterangan'   => 'Perpanjangan GPS kendaraan: ' . $gpsKendaraan->type . ' - ' . ($gpsKendaraan->kendaraan->nopol ?? '-'),
            'pemasukan'    => 0,
            'pengeluaran'  => $pengeluaran,
            'saldo'        => $lastSaldo - $pengeluaran,
        ]);

        // Update data aktif
        $gpsKendaraan->update([
            'status_gps'     => $request->status_gps,
            'tanggal_pasang' => $request->tanggal_pasang,
            'tanggal_habis'  => $request->tanggal_habis,
            'durasi_bulan'   => $request->durasi_bulan,
            'biaya_sewa'     => $request->biaya_sewa,
            'status_sewa'    => 'aktif',
            'bukti_bayar'    => $finalBukti,
        ]);

        // upload attachment tambahan (bukti pendukung perpanjangan)
        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $gpsKendaraan->id);
        }

        return back()->with('success', 'GPS kendaraan berhasil diperpanjang');
    }
}