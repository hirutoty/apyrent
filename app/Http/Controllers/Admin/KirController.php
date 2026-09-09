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

        // Auto-update: record aktif yang masa berlakunya sudah lewat → expired
        Kir::where('status', 'aktif')
            ->where('masa_berlaku', '<', now()->toDateString())
            ->update(['status' => 'expired']);

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

    public function store(Request $request, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        // Backend fallback: hitung masa_berlaku dari tanggal_bayar + 6 bulan
        // (jika field kosong karena JS tidak berjalan)
        if (empty($request->masa_berlaku) && $request->filled('tanggal_bayar')) {
            $request->merge([
                'masa_berlaku' => Carbon::parse($request->tanggal_bayar)->addMonths(6)->format('Y-m-d'),
            ]);
        }

        $request->validate([
            'kendaraan_id'       => 'required|exists:kendaraan,id',
            'no_ktp'             => 'required|string|max:255',
            'nama_ktp'           => 'required|string|max:255',
            'lokasi_uji'         => 'required|string|max:255',
            'penguji'            => 'nullable|string|max:255',
            'status_uji'         => 'required|in:uji berkala,uji pertama',
            'no_uji'             => 'required|string|max:255',
            'tanggal_bayar'      => 'required|date',
            'masa_berlaku'       => 'required|date',
            'biaya'              => 'required|numeric|min:0',
            'bukti_attachment'   => 'required|array|min:1',
            'bukti_attachment.*' => 'file|max:5120',
        ], [
            'kendaraan_id.required'  => 'Kendaraan wajib dipilih',
            'no_ktp.required'        => 'No KTP wajib diisi',
            'nama_ktp.required'      => 'Nama KTP wajib diisi',
            'lokasi_uji.required'    => 'Lokasi uji wajib diisi',
            'status_uji.required'    => 'Status uji wajib dipilih',
            'status_uji.in'          => 'Status uji tidak valid',
            'no_uji.required'        => 'Nomor uji wajib diisi',
            'tanggal_bayar.required' => 'Tanggal ketentuan bayar wajib diisi',
            'masa_berlaku.required'  => 'Masa berlaku wajib diisi',
            'biaya.required'         => 'Biaya KIR wajib diisi',
            'bukti_attachment.required' => 'Lampiran wajib diupload minimal 1 file',
            'bukti_attachment.min'      => 'Lampiran wajib diupload minimal 1 file',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        $exists = Kir::where('kendaraan_id', $kendaraan->id)->exists();
        if ($exists) {
            return back()->with('error', 'Kendaraan ' . $kendaraan->nopol . ' sudah memiliki data KIR');
        }

        try {
            // Step 1: Intercept & buat Pembayaran (untuk pencatatan approval)
            $interceptedData = $interceptor->intercept($request, 'kir');
            $pembayaran      = $interceptor->saveToPembayaran($interceptedData, 'kir');

            // Step 2: Simpan record langsung ke kir dengan status Pending & tidak_aktif
            // (bukti/image hanya diisi saat approval oleh keuangan)
            $kir = Kir::create([
                'pembayaran_id' => $pembayaran->id,
                'kendaraan_id'  => $request->kendaraan_id,
                'no_ktp'        => $request->no_ktp,
                'nama_ktp'      => $request->nama_ktp,
                'lokasi_uji'    => $request->lokasi_uji,
                'penguji'       => $request->penguji,
                'status_uji'    => $request->status_uji,
                'no_uji'        => $request->no_uji,
                'masa_berlaku'  => $request->masa_berlaku,
                'biaya'         => $request->biaya,
                'tanggal_bayar' => $request->tanggal_bayar,
                'image'         => null,
                'status'        => 'tidak_aktif',
                'persetujuan'   => 'Pending',
            ]);

            // Step 3: Simpan attachment langsung
            if ($request->hasFile('bukti_attachment')) {
                $this->simpanAttachments($request->file('bukti_attachment'), $kir->id);
            }

            // Step 4: Tandai existing_record_id di source_data pembayaran
            $sourceData = $pembayaran->source_data;
            $sourceData['existing_record_id'] = $kir->id;
            $pembayaran->update(['source_data' => $sourceData]);

            return redirect()
                ->route('kir.index')
                ->with('success', 'Data KIR berhasil disimpan. Menunggu approval dari keuangan.');

        } catch (\Exception $e) {
            \Log::error('Error store KIR: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
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

        // Backend fallback: hitung masa_berlaku dari tanggal_bayar + 6 bulan
        if (empty($request->masa_berlaku) && $request->filled('tanggal_bayar')) {
            $request->merge([
                'masa_berlaku' => Carbon::parse($request->tanggal_bayar)->addMonths(6)->format('Y-m-d'),
            ]);
        }

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

    public function perpanjang(Request $request, $id, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'no_uji'         => 'required',
            'biaya'          => 'required|numeric|min:0',
            'tanggal_bayar'  => 'nullable|date',
            'image'          => 'nullable|file|max:5120',
            'bukti_attachment'   => 'required|array|min:1',
            'bukti_attachment.*' => 'file|max:5120',
            'nama_bank'      => 'nullable|string|max:255',
            'no_rekening'    => 'nullable|string|max:100',
            'nama_rekening'  => 'nullable|string|max:255',
            'informasi'      => 'nullable|string',
        ]);

        $kir = Kir::findOrFail($id);

        // Cek: masa berlaku masih > 30 hari ke depan, perpanjangan belum diperlukan
        if ($kir->masa_berlaku && Carbon::parse($kir->masa_berlaku)->diffInDays(now(), false) < -30) {
            return back()->with('error', 'Masa berlaku KIR masih panjang (> 30 hari), perpanjangan belum diperlukan.');
        }

        // ===========================================================================
        // APPROVAL WORKFLOW: Perpanjang melalui Pembayaran untuk approval
        // ===========================================================================
        
        try {
            $pembayaran = $interceptor->perpanjangViaPembayaran($request, 'kir', $kir);
            
            return redirect()
                ->route('pembayaran.index', ['tab' => 'Pending'])
                ->with('success', 'Pengajuan perpanjangan KIR berhasil dikirim. Menunggu approval dari Superadmin.');
                
        } catch (\Exception $e) {
            \Log::error('Error perpanjang KIR via Pembayaran: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan perpanjangan. Silakan coba lagi.');
        }
    }
}