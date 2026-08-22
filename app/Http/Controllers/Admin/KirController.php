<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kir;
use App\Models\Kendaraan;
use App\Models\Setting;
use App\Models\KirHistory;
use App\Models\Attachment;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class KirController extends Controller
{
    /**
     * AJAX: Ambil data pemilik & jenis kendaraan berdasarkan kendaraan_id
     */
    public function getKendaraanDetail($id)
    {
        $kendaraan = Kendaraan::with(['member', 'jenis'])->findOrFail($id);

        return response()->json([
            'nama_pemilik'    => $kendaraan->member->nama ?? '-',
            'jenis_kendaraan' => $kendaraan->jenis->nama_jenis ?? '-',
        ]);
    }

    public function index(Request $request)    {
        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $satuanReminder = $setting->satuan_reminder ?? 'hari';
        $batasReminder  = $setting->batas_reminder  ?? 0;

        $reminder = match ($satuanReminder) {
            'hari'   => $batasReminder,
            'minggu' => $batasReminder * 7,
            'bulan'  => $batasReminder * 30,
            'tahun'  => $batasReminder * 365,
            default  => $batasReminder,
        };

        $query = Kir::with(['kendaraan', 'attachments'])->latest();

        // Server-side search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_uji', 'like', "%{$s}%")
                  ->orWhereHas('kendaraan', fn($k) =>
                      $k->where('nopol', 'like', "%{$s}%")
                        ->orWhere('merk', 'like', "%{$s}%")
                  );
            });
        }

        // Filter status (aktif / nonaktif)
        if ($request->filled('status') && in_array($request->status, ['aktif', 'nonaktif'])) {
            if ($request->status === 'aktif') {
                $query->where('masa_berlaku', '>=', now()->toDateString());
            } else {
                $query->where('masa_berlaku', '<', now()->toDateString());
            }
        }

        // Filter tahun masa berlaku
        if ($request->filled('tahun')) {
            $query->whereYear('masa_berlaku', $request->tahun);
        }

        // Filter bulan masa berlaku (format Y-m dari input type="month")
        if ($request->filled('bulan')) {
            [$y, $m] = array_pad(explode('-', $request->bulan), 2, null);
            if ($y) $query->whereYear('masa_berlaku', $y);
            if ($m) $query->whereMonth('masa_berlaku', $m);
        }

        // Filter hari masa berlaku
        if ($request->filled('hari')) {
            $query->whereDay('masa_berlaku', $request->hari);
        }

        $data = $query->paginate(15)->withQueryString();

        return view('admin.kir.index', [
            'data'      => $data,
            'kendaraan' => Kendaraan::all(),
            'reminder'  => $reminder,
        ]);
    }

    /**
     * Helper: simpan banyak attachment sekaligus.
     *
     * @param  array   $files        Array file dari $request->file(...)
     * @param  int     $relationId   ID record target (kir aktif atau history)
     * @param  string  $relationType Tipe relasi, default 'kir'
     */
    private function simpanAttachments($files, $relationId, $relationType = 'kir', $historyId = null)
    {
        $pathDir = public_path('kir/attachments');
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
                'file_path'     => 'kir/attachments/' . $filename,
                'file_type'     => $extension,
                'file_size'     => $size,
            ]);

            if ($historyId) {
                Attachment::create([
                    'relation_type' => $relationType . '_history',
                    'relation_id'   => $historyId,
                    'file_name'     => $originalName,
                    'file_path'     => 'kir/attachments/' . $filename,
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
            'no_ktp'        => 'required|string|max:255',
            'nama_ktp'      => 'required|string|max:255',
            'lokasi_uji'    => 'required|string|max:255',
            'penguji'       => 'nullable|string|max:255',
            'status_uji'    => 'required|in:uji berkala,uji pertama',
            'no_uji'        => 'required|string|max:255',
            'tanggal_bayar' => 'required|date',
            'masa_berlaku'  => 'required|date',
            'biaya'         => 'required|numeric|min:0',
            'image'         => 'nullable|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ], [
            'kendaraan_id.required' => 'Kendaraan wajib dipilih',
            'no_ktp.required'       => 'No KTP wajib diisi',
            'nama_ktp.required'     => 'Nama KTP wajib diisi',
            'lokasi_uji.required'   => 'Lokasi uji wajib diisi',
            'status_uji.required'   => 'Status uji wajib dipilih',
            'status_uji.in'         => 'Status uji tidak valid',
            'no_uji.required'       => 'Nomor uji wajib diisi',
            'tanggal_bayar.required'=> 'Tanggal bayar wajib diisi',
            'masa_berlaku.required' => 'Masa berlaku wajib diisi',
            'biaya.required'        => 'Biaya KIR wajib diisi',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        $exists = Kir::where('kendaraan_id', $kendaraan->id)->exists();
        if ($exists) {
            return back()->with('error', 'Kendaraan ' . $kendaraan->nopol . ' sudah memiliki data KIR');
        }

        $data = $request->except(['bukti_attachment', '_token']);

        if ($request->hasFile('image')) {
            $file        = $request->file('image');
            $filename    = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('kir/dokumen');
            if (!file_exists($destination)) mkdir($destination, 0777, true);
            $file->move($destination, $filename);
            $data['image'] = 'kir/dokumen/' . $filename;
        }

        $kir = Kir::create($data);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $kir, $kendaraan) {
            // --- Catat ke Keuangan ---
            $lastSaldo   = (float) \Illuminate\Support\Facades\DB::table('keuangans')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
            $pengeluaran = (float) $request->biaya;
            $kodeJurnal  = 'KIR-' . $kir->id . '-' . now()->timestamp;

            Keuangan::create([
                'tanggal'     => now(),
                'reference'   => $kodeJurnal,
                'user_id'     => auth()->id(),
                'kategori'    => 'Pengeluaran',
                'metode'      => 'Cash',
                'keterangan'  => 'Pembayaran KIR baru: ' . $kendaraan->nopol . ' - No Uji: ' . $request->no_uji,
                'pemasukan'   => 0,
                'pengeluaran' => $pengeluaran,
                'saldo'       => $lastSaldo - $pengeluaran,
                'sumber'      => 'auto',
            ]);

            // --- Auto-posting ke Buku Besar ---
            $saldoBBTerakhir = (float) \Illuminate\Support\Facades\DB::table('bukubesars')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
            Bukubesar::create([
                'kode_jurnal' => $kodeJurnal,
                'transaksi'   => 'Beban KIR - ' . $kendaraan->nopol,
                'kategori'    => 'Beban',
                'tanggal'     => now()->toDateString(),
                'debit'       => $pengeluaran,
                'kredit'      => 0,
                'saldo'       => $saldoBBTerakhir - $pengeluaran,
                'aktivitas'   => 'Operasi',
                'keterangan'  => 'Auto-posting: Pembayaran KIR baru ' . $kendaraan->nopol,
            ]);
        });

        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $kir->id);
        }

        return back()->with('success', 'Data KIR berhasil ditambahkan');
    }


    /**
     * Update hanya status_uji
     */
    public function updateStatusUji(Request $request, $id)
    {
        $request->validate([
            'status_uji' => 'required|in:uji berkala,uji pertama',
        ], [
            'status_uji.required' => 'Status uji wajib dipilih',
            'status_uji.in'       => 'Status uji tidak valid',
        ]);

        $kir = Kir::findOrFail($id);
        $kir->update(['status_uji' => $request->status_uji]);

        return back()->with('success', 'Status uji berhasil diperbarui');
    }

    public function update(Request $request, $id)
    {
        $kir = Kir::findOrFail($id);

        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'no_ktp'       => 'required|string|max:255',
            'nama_ktp'     => 'required|string|max:255',
            'lokasi_uji'   => 'required|string|max:255',
            'penguji'      => 'nullable|string|max:255',
            'status_uji'   => 'required|in:uji berkala,uji pertama',
            'no_uji'       => 'required',
            'masa_berlaku' => 'required|date',
            'biaya' => 'required|numeric|min:0',
            'image' => 'nullable|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $kendaraan = \App\Models\Kendaraan::findOrFail($request->kendaraan_id);

        // 🔥 CEK DUPLIKAT (EXCLUDE DATA SENDIRI)
        $exists = Kir::where('id', '!=', $id)
            ->whereHas('kendaraan', function ($q) use ($kendaraan) {
                $q->where('nopol', $kendaraan->nopol);
            })
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kendaraan dengan nopol ini sudah memiliki data KIR');
        }

        $data = $request->except(['bukti_attachment', '_token', '_method']); // ✅ jangan ikut masuk mass-assign

        if ($request->hasFile('image')) {

            // hapus file lama
            if ($kir->image && file_exists(public_path($kir->image))) {
                unlink(public_path($kir->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('kir/dokumen');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $data['image'] = 'kir/dokumen/' . $filename;
        }

        $kir->update($data);

        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $kir->id);
        }

        return back()->with('success', 'Data KIR berhasil diupdate');
    }

    public function destroy($id)
    {
        $kir = Kir::findOrFail($id);

        if ($kir->image && file_exists(public_path($kir->image))) {
            unlink(public_path($kir->image));
        }

        // hapus semua file attachment terkait
        foreach ($kir->attachments as $att) {
            if (file_exists(public_path($att->file_path))) {
                unlink(public_path($att->file_path));
            }
            $att->delete();
        }

        $kir->delete();

        return back()->with(
            'success',
            'Data KIR berhasil dihapus'
        );
    }

    /**
     * Hapus 1 attachment tertentu
     */
    public function destroyAttachment($id)
    {
        $attachment = Attachment::where('relation_type', 'kir')->findOrFail($id);

        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }

        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus');
    }




    /**
     * AJAX: detail per record KIR + chart perpanjangan Jan-Des
     */
    public function detail(Request $request, $id)
    {
        $kir   = Kir::with(['kendaraan','attachments'])->findOrFail($id);
        $tahun = (int) $request->input('tahun', now()->year);

        $histories = KirHistory::where('kir_id', $id)
            ->orderBy('diperpanjang_pada', 'desc')
            ->get()
            ->map(fn($h) => [
                'id'               => $h->id,
                'no_uji'           => $h->no_uji,
                'biaya'            => $h->biaya,
                'masa_berlaku'     => $h->masa_berlaku ? \Carbon\Carbon::parse($h->masa_berlaku)->format('d M Y') : '-',
                'tanggal_bayar'    => isset($h->tanggal_bayar) ? \Carbon\Carbon::parse($h->tanggal_bayar)->format('d M Y') : '-',
                'image'            => $h->image ? asset($h->image) : null,
                'diperpanjang_pada'=> $h->diperpanjang_pada ? \Carbon\Carbon::parse($h->diperpanjang_pada)->format('d M Y') : '-',
            ]);

        $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        $chartData   = [];
        for ($b = 1; $b <= 12; $b++) {
            $chartData[] = (float) KirHistory::where('kir_id', $id)
                ->whereYear('diperpanjang_pada', $tahun)
                ->whereMonth('diperpanjang_pada', $b)
                ->sum('biaya');
        }

        $availableYears = KirHistory::where('kir_id', $id)
            ->selectRaw('YEAR(diperpanjang_pada) as yr')
            ->whereNotNull('diperpanjang_pada')
            ->distinct()
            ->orderBy('yr', 'desc')
            ->pluck('yr');

        return response()->json([
            'success' => true,
            'record'  => [
                'id'          => $kir->id,
                'nopol'       => $kir->kendaraan->nopol ?? '-',
                'merk'        => $kir->kendaraan->merk ?? '-',
                'no_uji'      => $kir->no_uji,
                'no_ktp'      => $kir->no_ktp,
                'nama_ktp'    => $kir->nama_ktp,
                'lokasi_uji'  => $kir->lokasi_uji,
                'penguji'     => $kir->penguji,
                'status_uji'  => $kir->status_uji,
                'biaya'       => $kir->biaya,
                'masa_berlaku'=> $kir->masa_berlaku ? \Carbon\Carbon::parse($kir->masa_berlaku)->format('d M Y') : '-',
                'tanggal_bayar'=> $kir->tanggal_bayar ? \Carbon\Carbon::parse($kir->tanggal_bayar)->format('d M Y') : '-',
                'image'       => $kir->image ? asset($kir->image) : null,
            ],
            'histories'       => $histories,
            'chart'           => [
                'labels' => $bulanLabels,
                'data'   => $chartData,
                'tahun'  => $tahun,
            ],
            'available_years' => $availableYears,
        ]);
    }

    public function pdf(Request $request)
    {
        $search = $request->search;

        $data = Kir::with(['kendaraan', 'attachments'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('kendaraan', function ($q) use ($search) {
                    $q->where('nopol', 'like', "%$search%")
                        ->orWhere('merk', 'like', "%$search%");
                })
                    ->orWhere('no_uji', 'like', "%$search%");
            })
            ->get();

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.kir.pdf', compact('data', 'search', 'setting', 'logoSrc'));

        return $pdf->stream('data-kir.pdf');
    }

    /**
     * Perpanjang semua KIR sekaligus (bulk)
     * Setiap record: simpan history lama, update masa_berlaku += 1 tahun
     */
    public function perpanjangSemua(Request $request)
    {
        // Hanya superadmin yang boleh melakukan bulk perpanjangan
        if (auth()->user()->role !== 'superadmin') {
            return back()->with('error', 'Anda tidak memiliki akses untuk fitur ini.');
        }

        $request->validate([
            'tanggal_bayar' => 'nullable|date',
            'biaya_default' => 'nullable|numeric|min:0',
        ]);

        $kirList = Kir::with('kendaraan')->get();

        if ($kirList->isEmpty()) {
            return back()->with('error', 'Tidak ada data KIR untuk diperpanjang.');
        }

        $tanggalBayar = $request->filled('tanggal_bayar')
            ? Carbon::parse($request->tanggal_bayar)->toDateString()
            : now()->toDateString();

        $count = 0;

        foreach ($kirList as $kir) {
            // Simpan data lama ke history
            KirHistory::create([
                'kir_id'            => $kir->id,
                'kendaraan_id'      => $kir->kendaraan_id,
                'no_uji'            => $kir->no_uji,
                'masa_berlaku'      => $kir->masa_berlaku,
                'biaya'             => $kir->biaya,
                'image'             => $kir->image,
                'diperpanjang_pada' => $tanggalBayar,
            ]);
            $masaBerlakuBaru = Carbon::parse($kir->masa_berlaku)->addYear()->toDateString();

            $kir->update([
                'masa_berlaku'  => $masaBerlakuBaru,
                'tanggal_bayar' => $tanggalBayar,
                // gunakan biaya_default kalau diisi, atau pertahankan biaya lama
                'biaya'         => $request->filled('biaya_default') ? $request->biaya_default : $kir->biaya,
            ]);

            $count++;
        }

        return back()->with('success', "Berhasil memperpanjang {$count} data KIR.");
    }

    public function perpanjang(Request $request, $id)
    {
        $request->validate([
            'no_uji'         => 'required',
            'biaya'          => 'required|numeric|min:0',
            'tanggal_bayar'  => 'nullable|date',
            'image'          => 'required|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $kir = Kir::findOrFail($id);

        // Cek: masa berlaku masih > 30 hari ke depan, perpanjangan belum diperlukan
        if ($kir->masa_berlaku && Carbon::parse($kir->masa_berlaku)->diffInDays(now(), false) < -30) {
            return back()->with('error', 'Masa berlaku KIR masih panjang (> 30 hari), perpanjangan belum diperlukan.');
        }

        // --- Hitung tanggal standar ---
        $tanggalBayar    = $request->filled('tanggal_bayar')
            ? Carbon::parse($request->tanggal_bayar)->toDateString()
            : now()->toDateString();
        // masa_berlaku_baru = masa_berlaku LAMA + 1 tahun (dari DB)
        $masaBerlakuBaru = Carbon::parse($kir->masa_berlaku)->addYear()->toDateString();

        // --- Simpan path image LAMA sebelum upload ---
        $imageLama = $kir->image;

        // --- Upload image BARU ---
        $destination = public_path('kir/dokumen');
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $imageBaru = $imageLama; // fallback jika tidak ada upload baru
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($destination, $filename);
            $imageBaru = 'kir/dokumen/' . $filename;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $request, $kir, $imageLama, $imageBaru, $tanggalBayar, $masaBerlakuBaru
        ) {
            // --- Simpan data BARU ke history (sebagai log perpanjangan) ---
            $history = KirHistory::create([
                'kir_id'            => $kir->id,
                'kendaraan_id'      => $kir->kendaraan_id,
                'no_uji'            => $request->no_uji,
                'masa_berlaku'      => $masaBerlakuBaru,
                'biaya'             => $request->biaya,
                'image'             => $imageBaru,
                'tanggal_bayar'     => $tanggalBayar,
                'diperpanjang_pada' => now(),
            ]);

            // --- Catat ke Keuangan ---
            $lastSaldo = (float) \Illuminate\Support\Facades\DB::table('keuangans')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
            $pengeluaran = $request->biaya;
            // Kode jurnal unik per transaksi
            $kodeJurnal  = 'KIR-' . $kir->id . '-' . now()->timestamp;

            Keuangan::create([
                'tanggal'     => now(),
                'reference'   => $kodeJurnal,
                'user_id'     => auth()->id(),
                'kategori'    => 'Pengeluaran',
                'metode'      => 'Cash',
                'keterangan'  => 'Pembayaran KIR kendaraan: ' . ($kir->kendaraan->nopol ?? '-'),
                'pemasukan'   => 0,
                'pengeluaran' => $pengeluaran,
                'saldo'       => $lastSaldo - $pengeluaran,
                'sumber'      => 'auto',
            ]);

            // --- Auto-posting ke Buku Besar (kode jurnal unik, tanpa pengecekan duplikat) ---
            $saldoBBTerakhir = (float) \Illuminate\Support\Facades\DB::table('bukubesars')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
            Bukubesar::create([
                'kode_jurnal' => $kodeJurnal,
                'transaksi'   => 'Beban KIR - ' . ($kir->kendaraan->nopol ?? '-'),
                'kategori'    => 'Beban',
                'tanggal'     => now()->toDateString(),
                'debit'       => $pengeluaran,
                'kredit'      => 0,
                'saldo'       => $saldoBBTerakhir - $pengeluaran,
                'aktivitas'   => 'Operasi',
                'keterangan'  => 'Auto-posting: Pembayaran KIR kendaraan ' . ($kir->kendaraan->nopol ?? '-'),
            ]);

            // --- Update record aktif dengan data BARU ---
            $kir->update([
                'no_uji'        => $request->no_uji,
                'masa_berlaku'  => $masaBerlakuBaru,
                'biaya'         => $request->biaya,
                'image'         => $imageBaru,
                'tanggal_bayar' => $tanggalBayar,
            ]);

            // --- Pindahkan lampiran LAMA ke history ---
            // (Dihapus, karena history sekarang mencatat log baru)

            // --- Upload attachment tambahan BARU — masuk ke record aktif (halaman utama) & history ---
            if ($request->hasFile('bukti_attachment')) {
                Attachment::where('relation_type', 'kir')->where('relation_id', $kir->id)->delete();
                $this->simpanAttachments($request->file('bukti_attachment'), $kir->id, 'kir', $history->id);
            } else {
                $oldAttachments = Attachment::where('relation_type', 'kir')->where('relation_id', $kir->id)->get();
                foreach ($oldAttachments as $att) {
                    Attachment::create([
                        'relation_type' => 'kir_history',
                        'relation_id'   => $history->id,
                        'file_name'     => $att->file_name,
                        'file_path'     => $att->file_path,
                        'file_type'     => $att->file_type,
                        'file_size'     => $att->file_size,
                    ]);
                }
            }
        });

        return back()->with('success', 'KIR berhasil diperpanjang!');
    }
}