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
use App\Models\Bukubesar;
use Carbon\Carbon;

class PajakController extends Controller
{
    public function index(Request $request)
{
    $query = PajakKendaraan::with(['kendaraan', 'attachments'])->latest();

    if ($request->filled('hari')) {
        $query->whereDay('jatuh_tempo', $request->hari);
    }

    if ($request->filled('bulan')) {
        $query->whereMonth('jatuh_tempo', $request->bulan);
    }

    if ($request->filled('tahun')) {
        $query->whereYear('jatuh_tempo', $request->tahun);
    }

    if ($request->filled('search')) {
        $s = $request->search;

        $query->where(function ($q) use ($s) {
            $q->where('jenis_pajak', 'like', "%{$s}%")
              ->orWhere('status', 'like', "%{$s}%")
              ->orWhereHas('kendaraan', function ($k) use ($s) {
                  $k->where('nopol', 'like', "%{$s}%")
                    ->orWhere('merk', 'like', "%{$s}%");
              });
        });
    }

    // Gunakan pagination + pertahankan parameter filter
    $data = $query->paginate(15)->withQueryString();

    $kendaraan = Kendaraan::all();
    $setting = Setting::first();

    // Base64 logo untuk DomPDF
    $logoPath = $setting?->logo
        ? public_path($setting->logo)
        : public_path('images/icon.png');

    $logoSrc = '';

    if (file_exists($logoPath)) {
        $mime = mime_content_type($logoPath) ?: 'image/png';
        $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }

    $reminder = match ($setting->satuan_reminder) {
        'hari'   => $setting->batas_reminder,
        'minggu' => $setting->batas_reminder * 7,
        'bulan'  => $setting->batas_reminder * 30,
        'tahun'  => $setting->batas_reminder * 365,
        default  => $setting->batas_reminder,
    };

    return view('admin.pajak_kendaraan.index', compact(
        'data',
        'kendaraan',
        'reminder'
    ));
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
            'nominal'        => 'required|numeric',
            'tanggal_bayar'  => 'nullable|date',
            'keterangan'     => 'nullable',
            'bukti'          => 'required|file|max:5120',
            'bukti_attachment' => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $pajak = PajakKendaraan::findOrFail($id);

        // --- Hitung tanggal standar ---
        $tanggalBayar = $request->filled('tanggal_bayar')
            ? Carbon::parse($request->tanggal_bayar)->toDateString()
            : now()->toDateString();
        // jatuh_tempo_baru = jatuh_tempo LAMA + 1 tahun (dari DB, bukan dari tanggal_bayar)
        $jatuhTempoBaru = Carbon::parse($pajak->jatuh_tempo)->addYear()->toDateString();

        // --- Simpan path bukti LAMA sebelum upload ---
        $buktiLama = $pajak->bukti;

        // --- Upload bukti BARU ---
        $path = public_path('pajak/bukti');
        if (!file_exists($path)) mkdir($path, 0777, true);

        $buktiBaru = $buktiLama; // fallback jika tidak ada upload baru
        if ($request->hasFile('bukti')) {
            $file     = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $filename);
            $buktiBaru = 'pajak/bukti/' . $filename;
        }

        // --- Simpan data LAMA ke history (pakai bukti LAMA) ---
        PajakHistory::create([
            'pajak_kendaraan_id' => $pajak->id,
            'kendaraan_id'       => $pajak->kendaraan_id,
            'jenis_pajak'        => $pajak->jenis_pajak,
            'nominal'            => $pajak->nominal,
            'jatuh_tempo'        => $pajak->jatuh_tempo,
            'tanggal_bayar'      => $tanggalBayar,
            'status'             => 'sudah_bayar',
            'keterangan'         => $pajak->keterangan,
            'bukti'              => $buktiLama,
            'diperpanjang_pada'  => $tanggalBayar,
        ]);

        // --- Catat ke Keuangan ---
        $lastSaldo   = Keuangan::latest()->value('saldo') ?? 0;
        $pengeluaran = $request->nominal;
        // Kode jurnal unik per transaksi — pakai timestamp agar perpanjangan ke-2, ke-3 dst tetap masuk
        $kodeJurnal  = 'PAJAK-' . $pajak->id . '-' . now()->timestamp;

        Keuangan::create([
            'tanggal'     => now(),
            'reference'   => $kodeJurnal,
            'user_id'     => auth()->id(),
            'kategori'    => 'Pengeluaran',
            'metode'      => 'cash',
            'keterangan'  => 'Pembayaran pajak kendaraan: ' . $pajak->jenis_pajak . ' - ' . $request->keterangan,
            'pemasukan'   => 0,
            'pengeluaran' => $request->nominal,
            'saldo'       => $lastSaldo - $pengeluaran,
        ]);

        // --- Auto-posting ke Buku Besar (kode jurnal unik, tanpa pengecekan duplikat) ---
        Bukubesar::create([
            'kode_jurnal' => $kodeJurnal,
            'transaksi'   => 'Beban Pajak - ' . $pajak->jenis_pajak,
            'kategori'    => 'Beban',
            'tanggal'     => now()->toDateString(),
            'debit'       => $request->nominal,
            'kredit'      => 0,
            'saldo'       => $request->nominal,
            'aktivitas'   => 'Operasi',
            'keterangan'  => 'Auto-posting: Pembayaran pajak kendaraan ' . ($pajak->kendaraan->nopol ?? '-'),
        ]);

        // --- Update record aktif dengan data BARU ---
        $pajak->update([
            'nominal'       => $request->nominal,
            'jatuh_tempo'   => $jatuhTempoBaru,
            'tanggal_bayar' => $tanggalBayar,
            'status'        => 'sudah_bayar',
            'keterangan'    => $request->keterangan,
            'bukti'         => $buktiBaru,
        ]);

        // upload attachment tambahan
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