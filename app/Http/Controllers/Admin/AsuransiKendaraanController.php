<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Asuransi;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Models\AsuransiKendaraan;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\AsuransiHistory;
use App\Models\JenisAsuransi;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AsuransiKendaraanController extends Controller
{
    public function index()
    {
        // update otomatis status expired
        AsuransiKendaraan::where('tgl_berakhir', '<', now())
            ->where('status_kendaraan', 'aktif')
            ->update(['status_kendaraan' => 'expired']);

        $data = AsuransiKendaraan::with([
            'kendaraan',
            'asuransi',
            'jenisAsuransi',
            'attachments'
        ])->latest()->paginate(15)->withQueryString();

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $kendaraan = Kendaraan::all();
        $asuransi = Asuransi::all();
        $jenisAsuransi = JenisAsuransi::all();
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

        foreach ($data->getCollection() as $d) {

            $tglBerakhir = \Carbon\Carbon::parse($d->tgl_berakhir)->startOfDay();
            $hariIni = now()->startOfDay();

            $d->sisaHari = (int) $hariIni->diffInDays($tglBerakhir, false);

            $d->isExpired = $d->sisaHari <= 0;

            $d->isSoon = !$d->isExpired && $d->sisaHari <= $reminder;
        }

        return view('admin.asuransi.asuransi_kendaraan', compact(
            'data',
            'kendaraan',
            'asuransi',
            'jenisAsuransi',
            'reminder',
        ));
    }

    /**
     * Helper: simpan banyak attachment sekaligus untuk 1 asuransi
     * (konsisten dengan PajakController)
     */
    private function simpanAttachments($files, $asuransiId)
    {
        $pathDir = public_path('asuransi/attachments');
        if (!file_exists($pathDir)) mkdir($pathDir, 0777, true);

        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // ambil data SEBELUM file dipindah (biar getSize() tidak error)
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $size         = $file->getSize();

            $file->move($pathDir, $filename);

            Attachment::create([
                'relation_type' => 'asuransi',
                'relation_id'   => $asuransiId,
                'file_name'     => $originalName,
                'file_path'     => 'asuransi/attachments/' . $filename,
                'file_type'     => $extension,
                'file_size'     => $size,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'       => 'required|exists:kendaraan,id',
            'asuransi_id'        => 'required|exists:asuransi,id',
            'jenis_asuransi_id'  => 'required|exists:jenis_asuransi,id',
            'tgl_mulai'          => 'required|date',
            'tgl_berakhir'       => 'required|date|after_or_equal:tgl_mulai',

            'durasi_bulan'       => 'required|integer|min:1',
            'biaya'              => 'required|numeric|min:0',
            'bukti_bayar'        => 'required|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $kendaraan = \App\Models\Kendaraan::findOrFail($request->kendaraan_id);

        // 🔥 CEK DUPLIKAT FULL KOMBINASI
        $exists = AsuransiKendaraan::where('kendaraan_id', $request->kendaraan_id)
            ->exists();

        if ($exists) {
            return back()->with(
                'error',
                'Kendaraan / nopol ini sudah terdaftar pada data asuransi'
            );
        }

        $buktiBayar = null;

        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('asuransi/bukti_bayar'), $filename);

            $buktiBayar = 'asuransi/bukti_bayar/' . $filename;
        }

        $asuransiKendaraan = AsuransiKendaraan::create([
            'kendaraan_id'      => $request->kendaraan_id,
            'asuransi_id'       => $request->asuransi_id,
            'jenis_asuransi_id' => $request->jenis_asuransi_id,
            'tgl_mulai'         => $request->tgl_mulai,
            'tgl_berakhir'      => $request->tgl_berakhir,

            'durasi_bulan'      => $request->durasi_bulan,
            'biaya'             => $request->biaya,
            'bukti_bayar'       => $buktiBayar,

            'status_kendaraan'  => 'aktif',
        ]);

        // upload attachment tambahan (bisa lebih dari satu, SETELAH ADA ID)
        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $asuransiKendaraan->id);
        }

        return back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'kendaraan_id' => 'required|exists:kendaraan,id',
        'asuransi_id' => 'required|exists:asuransi,id',
        'jenis_asuransi_id' => 'required|exists:jenis_asuransi,id',
        'tgl_mulai' => 'required|date',
        'tgl_berakhir' => 'required|date',
        'status_kendaraan' => 'required',
        'durasi_bulan' => 'required|integer|min:1',
        'biaya'        => 'required|numeric|min:0',
        'bukti_bayar'        => 'nullable|file|max:5120', // ✅ diubah dari 'required' jadi 'nullable'
        'bukti_attachment'   => 'nullable|array',
        'bukti_attachment.*' => 'file|max:5120',
    ]);

    $data = AsuransiKendaraan::findOrFail($id);

    // 🔥 CEK DUPLIKAT SEBELUM UPDATE
    $exists = AsuransiKendaraan::where('kendaraan_id', $request->kendaraan_id)
        ->where('id', '!=', $id)
        ->exists();

    if ($exists) {
        return back()->with(
            'error',
            'Kendaraan / nopol ini sudah terdaftar pada data asuransi'
        );
    }

    // upload file (pakai bukti lama sebagai default)
    $buktiBayar = $data->bukti_bayar;

    if ($request->hasFile('bukti_bayar')) {

        if ($buktiBayar && file_exists(public_path($buktiBayar))) {
            unlink(public_path($buktiBayar));
        }

        $file = $request->file('bukti_bayar');

        $filename = time() . '_' . $file->getClientOriginalName();

        $file->move(public_path('asuransi/bukti_bayar'), $filename);

        $buktiBayar = 'asuransi/bukti_bayar/' . $filename;
    }
    // kalau tidak upload file baru, $buktiBayar tetap = data lama

    $data->update([
        'kendaraan_id'      => $request->kendaraan_id,
        'asuransi_id'       => $request->asuransi_id,
        'jenis_asuransi_id' => $request->jenis_asuransi_id,
        'tgl_mulai'         => $request->tgl_mulai,
        'tgl_berakhir'      => $request->tgl_berakhir,
        'status_kendaraan'  => $request->status_kendaraan,
        'durasi_bulan'      => $request->durasi_bulan,
        'biaya'             => $request->biaya,
        'bukti_bayar'       => $buktiBayar,
    ]);

    if ($request->hasFile('bukti_attachment')) {
        $this->simpanAttachments($request->file('bukti_attachment'), $data->id);
    }

    return back()->with('success', 'Data berhasil diupdate');
}

    public function destroy($id)
    {
        $data = AsuransiKendaraan::findOrFail($id);

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
        $attachment = Attachment::where('relation_type', 'asuransi')->findOrFail($id);

        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }

        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        $search = $request->search;

        $data = AsuransiKendaraan::with([
            'kendaraan',
            'asuransi',
            'jenisAsuransi',
            'attachments'
        ])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('kendaraan', function ($k) use ($search) {
                    $k->where('nopol', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%");
                })
                    ->orWhereHas('asuransi', function ($a) use ($search) {
                        $a->where('nama_asuransi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jenisAsuransi', function ($j) use ($search) {
                        $j->where('nama_jenis', 'like', "%{$search}%");
                    })
                    ->orWhere('status_kendaraan', 'like', "%{$search}%");
            })
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

        $pdf = Pdf::loadView(
            'admin.asuransi.pdf_asuransi_kendaraan',
            [
                'data'    => $data,
                'search'  => $search,
                'setting' => $setting,
                'logoSrc' => $logoSrc,
            ]
        )->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-asuransi-kendaraan.pdf');
    }

    public function perpanjang(Request $request, $id)
    {
        $request->validate([
            'asuransi_id'       => 'required|exists:asuransi,id',
            'jenis_asuransi_id' => 'required|exists:jenis_asuransi,id',
            'tgl_berakhir'      => 'required|date',
            'durasi_bulan'      => 'required|integer|min:1',
            'biaya'             => 'required|numeric|min:0',
            'bukti_bayar'       => 'required|file|max:5120', // ✅ typo 'require' diperbaiki
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $asuransi = AsuransiKendaraan::findOrFail($id);
        // Upload file baru terlebih dahulu
        $bukti = $asuransi->bukti_bayar;

        if ($request->hasFile('bukti_bayar')) {

            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . $file->getClientOriginalName();

            $path = public_path('asuransi/bukti_bayar');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file->move($path, $filename);

            $bukti = 'asuransi/bukti_bayar/' . $filename;
        }

        // Simpan history
        AsuransiHistory::create([
            'asuransi_kendaraan_id' => $asuransi->id,
            'kendaraan_id'          => $asuransi->kendaraan_id,
            'asuransi_id'           => $asuransi->asuransi_id,
            'jenis_asuransi_id'     => $asuransi->jenis_asuransi_id,
            'tgl_mulai'             => $asuransi->tgl_mulai,
            'tgl_berakhir'          => $asuransi->tgl_berakhir,
            'durasi_bulan'          => $asuransi->durasi_bulan,
            'biaya'                 => $asuransi->biaya,
            'bukti_bayar'           => $bukti,
            'status_kendaraan'      => 'aktif',
            'diperpanjang_pada'     => now(),
        ]);


        // 🔥 MASUK KE KEUANGAN (PENGELUARAN)
        $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;

        $pengeluaran = $request->biaya;

        Keuangan::create([
            'tanggal'      => now(),
            'reference'    => 'Asuransi-' . $asuransi->id,
            'user_id'      => auth()->id(),
            'kategori'     => 'Pengeluaran',
            'metode'       => '-',
            // ✅ jenis_pajak diganti ke jenisAsuransi->nama_jenis (field aslinya tidak ada di model Asuransi)
            'keterangan'   => 'Pembayaran asuransi kendaraan: ' . ($asuransi->jenisAsuransi->nama_jenis ?? '-') . ' - ' . $request->keterangan,
            'pemasukan'    => 0,
            'pengeluaran'  => $request->biaya,
            'saldo' => $lastSaldo - $pengeluaran,
        ]);


        // Update data aktif
        $asuransi->update([
            'asuransi_id'       => $request->asuransi_id,
            'jenis_asuransi_id' => $request->jenis_asuransi_id,
            'tgl_mulai'         => now()->toDateString(),
            'tgl_berakhir'      => $request->tgl_berakhir,
            'durasi_bulan'      => $request->durasi_bulan,
            'biaya'             => $request->biaya,
            'bukti_bayar'       => $bukti,
            'status_kendaraan'  => 'aktif',
        ]);

        // upload attachment tambahan (bukti pendukung perpanjangan)
        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $asuransi->id);
        }

        return back()->with('success', 'Asuransi berhasil diperpanjang!');
    }
}