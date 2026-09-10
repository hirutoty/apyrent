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

class GpsKendaraanController extends Controller
{
    public function index(Request $request)
    {
        GpsKendaraan::where('tanggal_habis', '<=', now())
            ->where('status_sewa', 'aktif')
            ->update(['status_sewa' => 'expired']);

        $query = GpsKendaraan::with(['kendaraan', 'gps', 'attachments'])->latest();

        // Server-side search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('type', 'like', "%{$s}%")
                  ->orWhere('status_gps', 'like', "%{$s}%")
                  ->orWhereHas('kendaraan', fn($k) =>
                      $k->where('merk', 'like', "%{$s}%")
                        ->orWhere('nopol', 'like', "%{$s}%")
                  )
                  ->orWhereHas('gps', fn($g) =>
                      $g->where('nama_gps', 'like', "%{$s}%")
                  );
            });
        }

        // Server-side filter hari/bulan/tahun berdasarkan tanggal_habis
        if ($request->filled('hari')) {
            $query->whereDay('tanggal_habis', $request->hari);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_habis', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_habis', $request->tahun);
        }

        $data = $query->paginate(15)->withQueryString();

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
            'data'           => $data,
            'reminder'       => $reminder,
            'gps'            => Gps::all(),
            'kendaraan'      => Kendaraan::all(),
            // Semua GPS per kendaraan (tidak ter-paginate) untuk modal perpanjang
            'gpsPerKendaraan'=> GpsKendaraan::with('gps')
                ->get()
                ->groupBy('kendaraan_id')
                ->map(fn($items) => $items->map(fn($x) => [
                    'id'               => $x->id,
                    'nama_gps'         => $x->gps->nama_gps ?? '-',
                    'type'             => $x->type,
                    'status_gps'       => $x->status_gps,
                    'biaya_sewa'       => $x->biaya_sewa,
                    'tanggal_habis'    => $x->tanggal_habis
                                            ? Carbon::parse($x->tanggal_habis)->format('Y-m-d')
                                            : '',
                    'tanggal_habis_baru' => $x->tanggal_habis
                                            ? Carbon::parse($x->tanggal_habis)->addYear()->format('Y-m-d')
                                            : '',
                ])->values()),
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

    public function store(Request $request, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        // Validasi shared fields + gps_items array
        $request->validate([
            'kendaraan_id'                    => 'required|exists:kendaraan,id',
            'status_gps'                      => 'nullable|in:aktif,nonaktif',
            'tanggal_bayar'                   => 'required|date',
            'tanggal_habis'                   => 'required|date',
            'keterangan'                      => 'nullable|string|max:1000',
            'vendor'                          => 'nullable|string|max:255',
            'gps_items'                       => 'required|array|min:1',
            'gps_items.*.gps_id'              => 'required|exists:gps,id',
            'gps_items.*.type'                => 'required|string|max:100',
            'gps_items.*.biaya_sewa'          => 'required|integer|min:0',
            'gps_items.*.bukti_bayar'         => 'nullable|file|max:5120',
            'gps_items.*.lampiran'            => 'required|array|min:1',
            'gps_items.*.lampiran.*'          => 'file|max:5120',
            'gps_items.*.nama_bank'           => 'nullable|string|max:255',
            'gps_items.*.no_rekening'         => 'nullable|string|max:100',
            'gps_items.*.nama_pemilik'        => 'nullable|string|max:255',
        ], [
            'kendaraan_id.required'            => 'Kendaraan wajib dipilih.',
            'tanggal_bayar.required'           => 'Tanggal bayar wajib diisi.',
            'gps_items.required'               => 'Minimal satu GPS wajib ditambahkan.',
            'gps_items.*.gps_id.required'      => 'GPS wajib dipilih di setiap baris.',
            'gps_items.*.type.required'        => 'Type GPS wajib diisi di setiap baris.',
            'gps_items.*.biaya_sewa.required'  => 'Biaya sewa wajib diisi di setiap baris.',
        ]);

        $gpsItems    = $request->input('gps_items');
        $kendaraanId = $request->kendaraan_id;

        // --- Cek duplikat type antar baris dalam satu form ---
        $seenInForm = [];
        foreach ($gpsItems as $idx => $item) {
            $key = strtolower(trim($item['type']));
            if (isset($seenInForm[$key])) {
                return back()->withInput()
                    ->with('error', 'Baris ' . ($idx + 1) . ': type "' . $item['type'] . '" sudah dipakai di baris ' . ($seenInForm[$key] + 1) . '. Type tidak boleh sama.');
            }
            $seenInForm[$key] = $idx;
        }

        // --- Cek duplikat type vs database (kendaraan yang sama) ---
        foreach ($gpsItems as $idx => $item) {
            $exists = GpsKendaraan::where('kendaraan_id', $kendaraanId)
                ->whereRaw('LOWER(type) = ?', [strtolower(trim($item['type']))])
                ->exists();

            if ($exists) {
                $kendaraan = Kendaraan::find($kendaraanId);
                $nopol = $kendaraan ? $kendaraan->nopol : 'ID ' . $kendaraanId;
                return back()->withInput()
                    ->with('error', 'Baris ' . ($idx + 1) . ': Kendaraan ' . $nopol . ' sudah memiliki GPS dengan type "' . $item['type'] . '".');
            }
        }

        // ===========================================================================
        // NEW FLOW: Intercept dan kirim ke Purchase Order (bukan langsung Pembayaran)
        // ===========================================================================
        
        try {
            // Check if this is a resubmit (from rejected PO)
            if ($request->filled('edit_purchase_order')) {
                $poId = $request->input('edit_purchase_order');
                $approvalService = app(\App\Services\PurchaseOrderApprovalService::class);
                $po = \App\Models\PurchaseOrder::findOrFail($poId);
                
                // Resubmit: Update existing PO
                $po = $approvalService->resubmit($po, $request->all(), $request);
                
                return back()
                    ->with('success', 'Pengajuan GPS berhasil diajukan ulang ke Purchase Order. Menunggu approval dari Superadmin.');
            }
            
            // Step 1: Intercept data dari form
            $interceptedData = $interceptor->intercept($request, 'gps');
            
            // Step 2: Save ke Purchase Order (bukan Pembayaran)
            $po = $interceptor->saveToPurchaseOrder($interceptedData, 'gps');
            
            // Step 3: Upload temporary files ke PO temp storage
            $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $po->id, 'purchase_order');
            
            // Step 4: Update source_data dengan file info
            $sourceData = $po->source_data;
            $sourceData['temp_files'] = $uploadedFiles;
            $po->update(['source_data' => $sourceData]);

            // Step 5: Buat record gps_kendaraan per item dengan persetujuan=Pending
            // GPS records linked ke PO lewat source_data (belum ada pembayaran_id)
            $tanggalBayar = $request->tanggal_bayar;
            $tanggalHabis = $request->tanggal_habis;
            $durasiBulan  = $tanggalBayar && $tanggalHabis
                ? max((int) \Carbon\Carbon::parse($tanggalBayar)->diffInMonths(\Carbon\Carbon::parse($tanggalHabis)), 1)
                : 12;

            $lampiranDir = public_path('gps/attachments');
            if (!file_exists($lampiranDir)) mkdir($lampiranDir, 0777, true);

            $gpsRecordIds = [];
            foreach ($gpsItems as $idx => $item) {
                $gpsRecord = GpsKendaraan::create([
                    'pembayaran_id' => null, // Akan diisi setelah PO approved & Pembayaran created
                    'kendaraan_id'  => $kendaraanId,
                    'gps_id'        => $item['gps_id'] ?? null,
                    'type'          => $item['type'] ?? null,
                    'status_gps'    => 'nonaktif',
                    'tanggal_pasang'=> $tanggalBayar,
                    'tanggal_habis' => $tanggalHabis,
                    'tanggal_bayar' => $tanggalBayar,
                    'biaya_sewa'    => (int) ($item['biaya_sewa'] ?? 0),
                    'durasi_bulan'  => $durasiBulan,
                    'status_sewa'   => 'tidak_aktif',
                    'bukti_bayar'   => null,
                    'keterangan'    => $request->keterangan ?? null,
                    'nama_bank'     => $item['nama_bank'] ?? null,
                    'no_rekening'   => $item['no_rekening'] ?? null,
                    'nama_pemilik'  => $item['nama_pemilik'] ?? null,
                    'persetujuan'   => 'Pending',
                ]);

                $gpsRecordIds[] = $gpsRecord->id;

                // Simpan lampiran per item langsung ke attachments
                if ($request->hasFile("gps_items.{$idx}.lampiran")) {
                    foreach ($request->file("gps_items.{$idx}.lampiran") as $lampiranFile) {
                        if (!$lampiranFile->isValid()) continue;
                        $origName  = $lampiranFile->getClientOriginalName();
                        $ext       = $lampiranFile->getClientOriginalExtension();
                        $fileSize  = $lampiranFile->getSize(); // ambil SEBELUM move
                        $filename  = time() . '_' . uniqid() . '.' . $ext;
                        $lampiranFile->move($lampiranDir, $filename);
                        Attachment::create([
                            'relation_type' => 'gps',
                            'relation_id'   => $gpsRecord->id,
                            'file_name'     => $origName,
                            'file_path'     => 'gps/attachments/' . $filename,
                            'file_type'     => $ext,
                            'file_size'     => $fileSize,
                        ]);
                    }
                }
            }

            // Store GPS record IDs di PO source_data untuk tracking
            $sourceData = $po->source_data;
            $sourceData['gps_record_ids'] = $gpsRecordIds;
            $po->update(['source_data' => $sourceData]);

            return back()
                ->with('success', 'Pengajuan GPS berhasil dikirim ke Purchase Order. Menunggu approval dari Superadmin.');
                
        } catch (\Exception $e) {
            \Log::error('Error intercepting GPS kendaraan submission: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan pengeluaran. Silakan coba lagi.');
        }
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
            ->where('type', $request->type)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kendaraan ini sudah memiliki GPS dengan tipe yang sama');
        }

        $data   = GpsKendaraan::findOrFail($id);
        $status = now()->lte($request->tanggal_habis) ? 'aktif' : 'expired';

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

    /**
     * AJAX: detail per record GPS kendaraan + chart perpanjangan Jan-Des
     */
    public function detail(Request $request, $id)
    {
        $gps   = GpsKendaraan::with(['kendaraan','gps','attachments'])->findOrFail($id);
        $tahun = (int) $request->input('tahun', now()->year);

        $histories = GpsKendaraanHistory::with('gps')
            ->where('gps_kendaraan_id', $id)
            ->orderBy('diperpanjang_pada', 'desc')
            ->get()
            ->map(fn($h) => [
                'id'               => $h->id,
                'nama_gps'         => $h->gps->nama_gps ?? '-',
                'type'             => $h->type,
                'biaya_sewa'       => $h->biaya_sewa,
                'durasi_bulan'     => $h->durasi_bulan,
                'tanggal_pasang'   => $h->tanggal_pasang?->format('d M Y'),
                'tanggal_habis'    => $h->tanggal_habis?->format('d M Y'),
                'status_sewa'      => $h->status_sewa,
                'bukti_bayar'      => $h->bukti_bayar ? asset($h->bukti_bayar) : null,
                'diperpanjang_pada'=> $h->diperpanjang_pada?->format('d M Y'),
            ]);

        $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        $chartData   = [];
        for ($b = 1; $b <= 12; $b++) {
            $chartData[] = (float) GpsKendaraanHistory::where('gps_kendaraan_id', $id)
                ->whereYear('diperpanjang_pada', $tahun)
                ->whereMonth('diperpanjang_pada', $b)
                ->sum('biaya_sewa');
        }

        $availableYears = GpsKendaraanHistory::where('gps_kendaraan_id', $id)
            ->selectRaw('YEAR(diperpanjang_pada) as yr')
            ->whereNotNull('diperpanjang_pada')
            ->distinct()
            ->orderBy('yr', 'desc')
            ->pluck('yr');

        return response()->json([
            'success' => true,
            'record'  => [
                'id'           => $gps->id,
                'nopol'        => $gps->kendaraan->nopol ?? '-',
                'merk'         => $gps->kendaraan->merk ?? '-',
                'nama_gps'     => $gps->gps->nama_gps ?? '-',
                'type'         => $gps->type,
                'biaya_sewa'   => $gps->biaya_sewa,
                'durasi_bulan' => $gps->durasi_bulan,
                'tanggal_pasang'=> $gps->tanggal_pasang ? \Carbon\Carbon::parse($gps->tanggal_pasang)->format('d M Y') : '-',
                'tanggal_habis' => $gps->tanggal_habis ? \Carbon\Carbon::parse($gps->tanggal_habis)->format('d M Y') : '-',
                'status_gps'   => $gps->status_gps,
                'status_sewa'  => $gps->status_sewa,
                'bukti_bayar'  => $gps->bukti_bayar ? asset($gps->bukti_bayar) : null,
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

    public function perpanjang(Request $request, $id, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'tanggal_bayar' => 'required|date',
            'biaya_sewa'    => 'required|integer|min:0',
            'lampiran'      => 'required|array|min:1',
            'lampiran.*'    => 'file|max:5120',
        ]);

        $gpsKendaraan = GpsKendaraan::with(['kendaraan', 'gps'])->findOrFail($id);

        // Guard: masa berlaku masih > 30 hari ke depan
        if ($gpsKendaraan->tanggal_habis && Carbon::parse($gpsKendaraan->tanggal_habis)->diffInDays(now(), false) < -30) {
            return back()->with('error', 'Masa berlaku GPS masih panjang (> 30 hari), perpanjangan belum diperlukan.');
        }

        try {
            $tanggalBayar = $request->tanggal_bayar;
            $biayaSewa    = (int) $request->biaya_sewa;
            $tanggalHabis = Carbon::parse($gpsKendaraan->tanggal_habis)->addYear()->toDateString();

            // Merge data konteks GPS ke dalam request agar perpanjangViaPembayaran bisa memakai
            $request->merge([
                'biaya_sewa'   => $biayaSewa,
                'tanggal_bayar'=> $tanggalBayar,
                'tanggal_habis'=> $tanggalHabis,
                'status_gps'   => $gpsKendaraan->status_gps,
                'gps_items'    => [[
                    'gps_kendaraan_id' => $gpsKendaraan->id,
                    'gps_id'           => $gpsKendaraan->gps_id,
                    'type'             => $gpsKendaraan->type,
                    'biaya_sewa'       => $biayaSewa,
                    'nama_bank'        => $gpsKendaraan->nama_bank,
                    'no_rekening'      => $gpsKendaraan->no_rekening,
                    'nama_pemilik'     => $gpsKendaraan->nama_pemilik,
                ]],
            ]);

            $pembayaran = $interceptor->perpanjangViaPembayaran($request, 'gps', $gpsKendaraan);

            return redirect()
                ->route('pembayaran.index', ['tab' => 'Pending'])
                ->with('success', 'Pengajuan perpanjangan GPS berhasil dikirim. Menunggu approval dari Superadmin.');

        } catch (\Exception $e) {
            \Log::error('Error perpanjang GPS via Pembayaran: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengajukan perpanjangan. Silakan coba lagi.');
        }
    }

    /**
     * Perpanjang SEMUA GPS yang dimiliki satu kendaraan sekaligus via approval workflow.
     */
    public function perpanjangKendaraan(Request $request, $kendaraanId, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'tanggal_bayar'                    => 'required|date',
            'gps_items'                        => 'required|array|min:1',
            'gps_items.*.gps_kendaraan_id'     => 'required|exists:gps_kendaraan,id',
            'gps_items.*.biaya_sewa'           => 'required|integer|min:0',
            'gps_items.*.lampiran'             => 'nullable|array',
            'gps_items.*.lampiran.*'           => 'file|max:5120',
        ], [
            'tanggal_bayar.required'                => 'Tanggal bayar wajib diisi.',
            'gps_items.required'                    => 'Data GPS tidak ditemukan.',
            'gps_items.*.gps_kendaraan_id.required' => 'ID GPS kendaraan wajib ada.',
            'gps_items.*.biaya_sewa.required'       => 'Biaya sewa wajib diisi untuk setiap GPS.',
        ]);

        $tanggalBayar = Carbon::parse($request->tanggal_bayar)->toDateString();
        $inputItems   = $request->input('gps_items');

        // Bangun gps_items array yang lengkap (termasuk data dari record existing)
        $gpsItemsMerged = [];
        foreach ($inputItems as $item) {
            $gpsKendaraan = GpsKendaraan::with(['kendaraan', 'gps'])->findOrFail($item['gps_kendaraan_id']);
            $tanggalHabis = Carbon::parse($gpsKendaraan->tanggal_habis)->addYear()->toDateString();

            $gpsItemsMerged[] = [
                'gps_kendaraan_id' => $gpsKendaraan->id,
                'gps_id'           => $gpsKendaraan->gps_id,
                'type'             => $gpsKendaraan->type,
                'biaya_sewa'       => (int) $item['biaya_sewa'],
                'tanggal_habis'    => $tanggalHabis,
                'nama_bank'        => $gpsKendaraan->nama_bank,
                'no_rekening'      => $gpsKendaraan->no_rekening,
                'nama_pemilik'     => $gpsKendaraan->nama_pemilik,
                'status_gps'       => $gpsKendaraan->status_gps,
            ];
        }

        // Nominal total untuk header Pembayaran
        $totalBiaya = array_sum(array_column($gpsItemsMerged, 'biaya_sewa'));

        // Ambil kendaraan dari item pertama (semua item milik kendaraan yang sama)
        $firstGps  = GpsKendaraan::with('kendaraan')->findOrFail($inputItems[0]['gps_kendaraan_id']);
        $kendaraan = $firstGps->kendaraan;

        // Merge ke request agar perpanjangViaPembayaran bisa membaca data lengkap
        $request->merge([
            'kendaraan_id'  => $kendaraanId,
            'tanggal_bayar' => $tanggalBayar,
            'tanggal_habis' => $gpsItemsMerged[0]['tanggal_habis'],   // representatif (transfer service handles per-item)
            'status_gps'    => 'aktif',
            'gps_items'     => $gpsItemsMerged,
            'biaya_sewa'    => $totalBiaya,   // nominal total (dipakai extractNominal)
        ]);

        // Buat dummy existingRecord agar perpanjangViaPembayaran bisa mengisi kendaraan_id & existing_record_id
        // Untuk multi-item kita pakai firstGps sebagai anchor; transfer service akan loop gps_items
        try {
            $pembayaran = $interceptor->perpanjangViaPembayaran($request, 'gps', $firstGps);

            return redirect()
                ->route('pembayaran.index', ['tab' => 'Pending'])
                ->with('success', count($gpsItemsMerged) . ' GPS kendaraan berhasil diajukan perpanjangan. Menunggu approval dari Superadmin.');

        } catch (\Exception $e) {
            \Log::error('Error perpanjangKendaraan GPS via Pembayaran: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengajukan perpanjangan. Silakan coba lagi.');
        }
    }
}
