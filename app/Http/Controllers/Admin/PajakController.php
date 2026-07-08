<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Models\PajakKendaraan;
use App\Models\Kendaraan;
use App\Models\Setting;
use App\Models\PajakHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Keuangan;


class PajakController extends Controller
{
    public function index(Request $request)
    {
        $query = PajakKendaraan::with(['kendaraan', 'attachments'])->latest();

        if ($request->filled('hari'))  $query->whereDay('jatuh_tempo', $request->hari);
        if ($request->filled('bulan')) $query->whereMonth('jatuh_tempo', $request->bulan);
        if ($request->filled('tahun')) $query->whereYear('jatuh_tempo', $request->tahun);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('jenis_pajak','like',"%$s%")
                  ->orWhere('status','like',"%$s%")
                  ->orWhereHas('kendaraan', fn($k) => $k->where('nopol','like',"%$s%")->orWhere('merk','like',"%$s%"));
            });
        }

        $data = $query->get();
        $kendaraan = Kendaraan::all();
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

        return view('admin.pajak_kendaraan.index', compact('data', 'kendaraan', 'reminder'));
    }

    /**
     * Helper: simpan banyak attachment sekaligus untuk 1 pajak
     */
    private function simpanAttachments($files, $pajakId)
{
    $pathDir = public_path('pajak/attachments');
    if (!file_exists($pathDir)) mkdir($pathDir, 0777, true);

    foreach ($files as $file) {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // ambil data SEBELUM file dipindah
        $originalName = $file->getClientOriginalName();
        $extension    = $file->getClientOriginalExtension();
        $size         = $file->getSize();

        $file->move($pathDir, $filename);

        Attachment::create([
            'relation_type' => 'pajak',
            'relation_id'   => $pajakId,
            'file_name'     => $originalName,
            'file_path'     => 'pajak/attachments/' . $filename,
            'file_type'     => $extension,
            'file_size'     => $size,
        ]);
    }
}

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'jenis_pajak' => 'required',
            'nominal' => 'required|numeric',
            'jatuh_tempo' => 'required|date',
            'tanggal_bayar' => 'nullable|date',
            'status' => 'required',
            'keterangan' => 'nullable',
            'bukti' => 'nullable|file|max:5120',
            'bukti_attachment' => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        $exists = PajakKendaraan::whereHas('kendaraan', function ($q) use ($kendaraan) {
            $q->where('nopol', $kendaraan->nopol);
        })->exists();

        if ($exists) {
            return back()->with('error', 'Nopol ini sudah memiliki data pajak');
        }

        // upload bukti utama
        $bukti = null;

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $path = public_path('pajak/bukti');
            if (!file_exists($path)) mkdir($path, 0777, true);

            $file->move($path, $filename);
            $bukti = 'pajak/bukti/' . $filename;
        }

        // simpan pajak dulu
        $pajak = PajakKendaraan::create([
            'kendaraan_id' => $request->kendaraan_id,
            'jenis_pajak' => $request->jenis_pajak,
            'nominal' => $request->nominal,
            'jatuh_tempo' => $request->jatuh_tempo,
            'tanggal_bayar' => $request->tanggal_bayar,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'bukti' => $bukti,
        ]);

        // upload attachment tambahan (bisa lebih dari satu, SETELAH ADA ID)
        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $pajak->id);
        }

        return back()->with('success', 'Data pajak berhasil ditambahkan');
    }

    public function perpanjang(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric',
            'jatuh_tempo' => 'required|date',
            'tanggal_bayar' => 'nullable|date',
            'status' => 'required',
            'keterangan' => 'nullable',
            'bukti' => 'nullable|file|max:5120',
            'bukti_attachment' => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $pajak = PajakKendaraan::findOrFail($id);
        $bukti = $pajak->bukti;

        $path = public_path('pajak/bukti');
        if (!file_exists($path)) mkdir($path, 0777, true);

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $filename);

            if ($bukti && file_exists(public_path($bukti))) {
                unlink(public_path($bukti));
            }

            $bukti = 'pajak/bukti/' . $filename;
        }

        $finalBukti = $bukti;

        // Simpan data lama ke history
        PajakHistory::create([
            'pajak_kendaraan_id' => $pajak->id,
            'kendaraan_id'       => $pajak->kendaraan_id,
            'jenis_pajak'        => $pajak->jenis_pajak,
            'nominal'            => $pajak->nominal,
            'jatuh_tempo'        => $pajak->jatuh_tempo,
            'tanggal_bayar'      => now(),
            'status'             => 'sudah_bayar',
            'keterangan'         => $pajak->keterangan,
            'bukti'              => $finalBukti,
            'diperpanjang_pada'  => now(),
        ]);

        // MASUK KE KEUANGAN (PENGELUARAN)
        $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;
        $pengeluaran = $request->nominal;

        Keuangan::create([
            'tanggal'      => now(),
            'reference'    => 'PAJAK-' . $pajak->id,
            'user_id'      => auth()->id(),
            'kategori'     => 'Pengeluaran',
            'metode'       => 'cash',
            'keterangan'   => 'Pembayaran pajak kendaraan: ' . $pajak->jenis_pajak . ' - ' . $request->keterangan,
            'pemasukan'    => 0,
            'pengeluaran'  => $request->nominal,
            'saldo' => $lastSaldo - $pengeluaran,
        ]);

        // Update data aktif
        $pajak->update([
            'nominal'        => $request->nominal,
            'jatuh_tempo'    => $request->jatuh_tempo,
            'tanggal_bayar'  => now()->toDateString(),
            'status'         => 'sudah_bayar',
            'keterangan'     => $request->keterangan,
            'bukti'          => $finalBukti,
        ]);

        // upload attachment tambahan (bukti pendukung perpanjangan)
        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $pajak->id);
        }

        return back()->with('success', 'Pajak berhasil diperpanjang');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'jenis_pajak' => 'required',
            'nominal' => 'required|numeric',
            'jatuh_tempo' => 'required|date',
            'tanggal_bayar' => 'nullable|date',
            'status' => 'required',
            'keterangan' => 'nullable',
            'bukti' => 'nullable|file|max:5120',
            'bukti_attachment' => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $pajak = PajakKendaraan::findOrFail($id);
        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        $exists = PajakKendaraan::whereHas('kendaraan', function ($q) use ($kendaraan) {
            $q->where('nopol', $kendaraan->nopol);
        })
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Nopol ini sudah memiliki data pajak');
        }

        $bukti = $pajak->bukti;

        if ($request->hasFile('bukti')) {
            if ($bukti && file_exists(public_path($bukti))) {
                unlink(public_path($bukti));
            }

            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('pajak/bukti'), $filename);
            $bukti = 'pajak/bukti/' . $filename;
        }

        $pajak->update([
            'kendaraan_id' => $request->kendaraan_id,
            'jenis_pajak' => $request->jenis_pajak,
            'nominal' => $request->nominal,
            'jatuh_tempo' => $request->jatuh_tempo,
            'tanggal_bayar' => $request->tanggal_bayar,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'bukti' => $bukti,
        ]);

        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $pajak->id);
        }

        return back()->with('success', 'Data pajak berhasil diupdate');
    }

    public function destroy($id)
    {
        $pajak = PajakKendaraan::findOrFail($id);

        if ($pajak->bukti && file_exists(public_path($pajak->bukti))) {
            unlink(public_path($pajak->bukti));
        }

        // hapus semua file attachment terkait
        foreach ($pajak->attachments as $att) {
            if (file_exists(public_path($att->file_path))) {
                unlink(public_path($att->file_path));
            }
            $att->delete();
        }

        $pajak->delete();

        return back()->with('success', 'Data pajak berhasil dihapus');
    }

    /**
     * Hapus 1 attachment tertentu (dipanggil dari tombol hapus di list attachment)
     */
    public function destroyAttachment($id)
    {
        $attachment = Attachment::where('relation_type', 'pajak')->findOrFail($id);

        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }

        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        $search = $request->search;

        $query = PajakKendaraan::with(['kendaraan', 'attachments']);

        if ($request->filled('hari'))  $query->whereDay('jatuh_tempo', $request->hari);
        if ($request->filled('bulan')) $query->whereMonth('jatuh_tempo', $request->bulan);
        if ($request->filled('tahun')) $query->whereYear('jatuh_tempo', $request->tahun);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('jenis_pajak', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('nominal', 'like', "%{$search}%")
                    ->orWhere('jatuh_tempo', 'like', "%{$search}%")
                    ->orWhere('tanggal_bayar', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('kendaraan', function ($k) use ($search) {
                        $k->where('nopol', 'like', "%{$search}%")
                            ->orWhere('merk', 'like', "%{$search}%");
                    });
            });
        }

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $data = $query->latest()->get();

        $pdf = Pdf::loadView(
            'admin.pajak_kendaraan.pdf_pajak',
            compact('data', 'search', 'setting', 'logoSrc')
        )->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-pajak-kendaraan.pdf');
    }
}