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
    public function index(Request $request)
    {
        GpsKendaraan::where('tanggal_habis', '<=', now())
            ->where('status_sewa', 'aktif')
            ->update(['status_sewa' => 'habis']);

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
            'status_gps'                      => 'required|in:aktif,nonaktif',
            'tanggal_bayar'                   => 'required|date',
            'tanggal_habis'                   => 'required|date',
            'keterangan'                      => 'nullable|string|max:1000',
            'gps_items'                       => 'required|array|min:1',
            'gps_items.*.gps_id'              => 'required|exists:gps,id',
            'gps_items.*.type'                => 'required|string|max:100',
            'gps_items.*.biaya_sewa'          => 'required|integer|min:0',
            'gps_items.*.bukti_bayar'         => 'nullable|file|max:5120',
            'gps_items.*.lampiran'            => 'nullable|array',
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
        // APPROVAL WORKFLOW: Intercept dan kirim ke Pembayaran
        // ===========================================================================
        
        try {
            // Check if this is a resubmit (from rejected pembayaran)
            if ($request->filled('edit_pembayaran')) {
                $pembayaranId = $request->input('edit_pembayaran');
                
                // Resubmit: Update existing pembayaran
                $pembayaran = $interceptor->resubmitToPembayaran($pembayaranId, $request, 'gps');
                
                return back()
                    ->with('success', 'Pengajuan GPS berhasil diajukan ulang. Menunggu approval dari Superadmin.');
            }
            
            // Step 1: Intercept data dari form
            $interceptedData = $interceptor->intercept($request, 'gps');
            
            // Step 2: Save ke Pembayaran
            $pembayaran = $interceptor->saveToPembayaran($interceptedData, 'gps');
            
            // Step 3: Upload temporary files
            $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $pembayaran->id);
            
            // Step 4: Update source_data dengan file info
            $sourceData = $pembayaran->source_data;
            $sourceData['temp_files'] = $uploadedFiles;
            $pembayaran->update(['source_data' => $sourceData]);

            // Step 5: Buat record gps_kendaraan per item dengan persetujuan=Pending
            $tanggalBayar = $request->tanggal_bayar;
            $tanggalHabis = $request->tanggal_habis;
            $durasiBulan  = $tanggalBayar && $tanggalHabis
                ? max((int) \Carbon\Carbon::parse($tanggalBayar)->diffInMonths(\Carbon\Carbon::parse($tanggalHabis)), 1)
                : 12;

            $lampiranDir = public_path('gps/attachments');
            if (!file_exists($lampiranDir)) mkdir($lampiranDir, 0777, true);

            foreach ($gpsItems as $idx => $item) {
                $gpsRecord = GpsKendaraan::create([
                    'pembayaran_id' => $pembayaran->id,
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

            return back()
                ->with('success', 'Pengajuan pengeluaran GPS berhasil dikirim. Menunggu approval dari Superadmin.');
                
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
            'tanggal_bayar'  => 'required|date',
            'biaya_sewa'     => 'required|integer|min:0',
            'gps_items'      => 'nullable|array',
        ]);

        $gpsKendaraan = GpsKendaraan::with(['kendaraan', 'gps'])->findOrFail($id);

        // Cek: masa berlaku masih > 30 hari ke depan
        if ($gpsKendaraan->tanggal_habis && Carbon::parse($gpsKendaraan->tanggal_habis)->diffInDays(now(), false) < -30) {
            return back()->with('error', 'Masa berlaku GPS masih panjang (> 30 hari), perpanjangan belum diperlukan.');
        }

        try {
            $tanggalBayar = $request->tanggal_bayar;
            $biayaSewa    = (int) $request->biaya_sewa;
            $tanggalHabis = Carbon::parse($gpsKendaraan->tanggal_habis)->addYear()->toDateString();
            $durasiBulan  = (int) Carbon::parse($tanggalBayar)->diffInMonths(Carbon::parse($tanggalHabis));
            $durasiBulan  = max($durasiBulan, 1);

            // Simpan ke Pembayaran
            $sourceData = [
                'is_perpanjang'       => true,
                'existing_record_id'  => $gpsKendaraan->id,
                'kendaraan_id'        => $gpsKendaraan->kendaraan_id,
                'kendaraan_nopol'     => $gpsKendaraan->kendaraan->nopol ?? '-',
                'tanggal_bayar'       => $tanggalBayar,
                'tanggal_habis'       => $tanggalHabis,
                'status_gps'          => $gpsKendaraan->status_gps,
                'gps_items'           => [[
                    'gps_id'       => $gpsKendaraan->gps_id,
                    'type'         => $gpsKendaraan->type,
                    'biaya_sewa'   => $biayaSewa,
                    'nama_bank'    => $gpsKendaraan->nama_bank,
                    'no_rekening'  => $gpsKendaraan->no_rekening,
                    'nama_pemilik' => $gpsKendaraan->nama_pemilik,
                ]],
            ];

            $user      = auth()->user();
            $pembayaran = \App\Models\Pembayaran::create([
                'no_pr'             => $interceptor->generateNoPR('gps_perpanjang'),
                'tanggal'           => now(),
                'departemen'        => $user->departemen ?? 'Umum',
                'tipe_pembayaran'   => 'service',
                'pemohon'           => $user->nama ?? $user->email,
                'alasan_permintaan' => 'Perpanjangan GPS - ' . ($gpsKendaraan->gps->nama_gps ?? '-') . ' (' . $gpsKendaraan->type . ')',
                'nominal'           => $biayaSewa,
                'nama_bank'         => $gpsKendaraan->nama_bank,
                'no_rekening'       => $gpsKendaraan->no_rekening,
                'nama_rekening'     => $gpsKendaraan->nama_pemilik,
                'status'            => 'Pending',
                'source_type'       => 'gps_perpanjang',
                'source_data'       => $sourceData,
                'target_id'         => null,
                'can_edit'          => false,
            ]);

            // Upload lampiran jika ada
            $lampiranDir = public_path('gps/attachments');
            if (!file_exists($lampiranDir)) mkdir($lampiranDir, 0777, true);

            // Buat record baru gps_kendaraan dengan persetujuan=Pending
            $newRecord = GpsKendaraan::create([
                'pembayaran_id' => $pembayaran->id,
                'kendaraan_id'  => $gpsKendaraan->kendaraan_id,
                'gps_id'        => $gpsKendaraan->gps_id,
                'type'          => $gpsKendaraan->type,
                'status_gps'    => 'nonaktif',
                'tanggal_pasang'=> $tanggalBayar,
                'tanggal_habis' => $tanggalHabis,
                'tanggal_bayar' => $tanggalBayar,
                'biaya_sewa'    => $biayaSewa,
                'durasi_bulan'  => $durasiBulan,
                'status_sewa'   => 'tidak_aktif',
                'bukti_bayar'   => null,
                'keterangan'    => 'Perpanjangan GPS',
                'nama_bank'     => $gpsKendaraan->nama_bank,
                'no_rekening'   => $gpsKendaraan->no_rekening,
                'nama_pemilik'  => $gpsKendaraan->nama_pemilik,
                'persetujuan'   => 'Pending',
            ]);

            // Lampiran
            if ($request->hasFile('gps_items.0.lampiran')) {
                foreach ($request->file('gps_items.0.lampiran') as $lampiranFile) {
                    if (!$lampiranFile->isValid()) continue;
                    $origName = $lampiranFile->getClientOriginalName();
                    $ext      = $lampiranFile->getClientOriginalExtension();
                    $fileSize = $lampiranFile->getSize();
                    $filename = time() . '_' . uniqid() . '.' . $ext;
                    $lampiranFile->move($lampiranDir, $filename);
                    Attachment::create([
                        'relation_type' => 'gps',
                        'relation_id'   => $newRecord->id,
                        'file_name'     => $origName,
                        'file_path'     => 'gps/attachments/' . $filename,
                        'file_type'     => $ext,
                        'file_size'     => $fileSize,
                    ]);
                }
            }

            return back()->with('success', 'Pengajuan perpanjangan GPS berhasil dikirim. Menunggu approval dari Superadmin.');

        } catch (\Exception $e) {
            \Log::error('Error perpanjang GPS: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengajukan perpanjangan. Silakan coba lagi.');
        }
    }

    /**
     * Perpanjang SEMUA GPS yang dimiliki satu kendaraan sekaligus.
     * Setiap GPS memiliki biaya & bukti bayar sendiri-sendiri.
     */
    public function perpanjangKendaraan(Request $request, $kendaraanId)
    {
        $request->validate([
            'tanggal_bayar'                    => 'required|date',
            'gps_items'                        => 'required|array|min:1',
            'gps_items.*.gps_kendaraan_id'     => 'required|exists:gps_kendaraan,id',
            'gps_items.*.biaya_sewa'           => 'required|integer|min:0',
            'gps_items.*.bukti_bayar'          => 'required|file|max:5120',
            'gps_items.*.lampiran'             => 'nullable|array',
            'gps_items.*.lampiran.*'           => 'file|max:5120',
        ], [
            'tanggal_bayar.required'                => 'Tanggal bayar wajib diisi.',
            'gps_items.required'                    => 'Data GPS tidak ditemukan.',
            'gps_items.*.gps_kendaraan_id.required' => 'ID GPS kendaraan wajib ada.',
            'gps_items.*.biaya_sewa.required'       => 'Biaya sewa wajib diisi untuk setiap GPS.',
            'gps_items.*.bukti_bayar.required'      => 'Bukti bayar wajib diupload untuk setiap GPS.',        ]);

        $tanggalBayar = Carbon::parse($request->tanggal_bayar)->toDateString();
        $gpsItems     = $request->input('gps_items');

        // Siapkan dir upload
        $buktiDir = public_path('gps/bukti_bayar');
        if (!file_exists($buktiDir)) mkdir($buktiDir, 0777, true);

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $request, $gpsItems, $tanggalBayar, $buktiDir
        ) {
            foreach ($gpsItems as $idx => $item) {
                $gpsKendaraan = GpsKendaraan::findOrFail($item['gps_kendaraan_id']);
                $tanggalHabis = Carbon::parse($gpsKendaraan->tanggal_habis)->addYear()->toDateString();

                // Upload bukti bayar per-GPS
                $buktiBayarBaru = $gpsKendaraan->bukti_bayar; // fallback
                if ($request->hasFile("gps_items.{$idx}.bukti_bayar")) {
                    $file     = $request->file("gps_items.{$idx}.bukti_bayar");
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($buktiDir, $filename);
                    $buktiBayarBaru = 'gps/bukti_bayar/' . $filename;
                }

                // Simpan ke history
                $history = GpsKendaraanHistory::create([
                    'gps_id'           => $gpsKendaraan->gps_id,
                    'gps_kendaraan_id' => $gpsKendaraan->id,
                    'kendaraan_id'     => $gpsKendaraan->kendaraan_id,
                    'type'             => $gpsKendaraan->type,
                    'tanggal_pasang'   => $tanggalBayar,
                    'tanggal_habis'    => $tanggalHabis,
                    'durasi_bulan'     => 12,
                    'biaya_sewa'       => $item['biaya_sewa'],
                    'status_sewa'      => 'aktif',
                    'status_gps'       => $gpsKendaraan->status_gps,
                    'bukti_bayar'      => $buktiBayarBaru,
                    'tanggal_bayar'    => $tanggalBayar,
                    'diperpanjang_pada'=> now(),
                ]);

                // Catat ke Keuangan
                $lastSaldo   = (float) \Illuminate\Support\Facades\DB::table('keuangans')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
                $pengeluaran = (int) $item['biaya_sewa'];
                $kodeJurnal  = 'GPS-' . $gpsKendaraan->id . '-' . now()->timestamp;

                Keuangan::create([
                    'tanggal'     => now(),
                    'reference'   => $kodeJurnal,
                    'user_id'     => auth()->id(),
                    'divisi'      => auth()->user() ? ucfirst(auth()->user()->role) : 'Keuangan',
                    'kategori'    => 'Pengeluaran',
                    'metode'      => 'Cash',
                    'keterangan'  => 'Perpanjangan GPS: ' . $gpsKendaraan->type . ' - ' . ($gpsKendaraan->kendaraan->nopol ?? '-'),
                    'pemasukan'   => 0,
                    'pengeluaran' => $pengeluaran,
                    'saldo'       => $lastSaldo - $pengeluaran,
                    'sumber'      => 'auto',
                ]);

                // Auto-posting Buku Besar
                $saldoBB = (float) \Illuminate\Support\Facades\DB::table('bukubesars')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
                Bukubesar::create([
                    'kode_jurnal' => $kodeJurnal,
                    'transaksi'   => 'Beban GPS - ' . $gpsKendaraan->type,
                    'kategori'    => 'Beban',
                    'tanggal'     => now()->toDateString(),
                    'debit'       => $pengeluaran,
                    'kredit'      => 0,
                    'saldo'       => $saldoBB - $pengeluaran,
                    'aktivitas'   => 'Operasi',
                    'keterangan'  => 'Perpanjangan GPS ' . ($gpsKendaraan->kendaraan->nopol ?? '-'),
                ]);

                // Update record aktif
                $gpsKendaraan->update([
                    'status_gps'     => 'aktif',
                    'tanggal_pasang' => $tanggalBayar,
                    'tanggal_habis'  => $tanggalHabis,
                    'durasi_bulan'   => 12,
                    'biaya_sewa'     => $item['biaya_sewa'],
                    'status_sewa'    => 'aktif',
                    'bukti_bayar'    => $buktiBayarBaru,
                    'tanggal_bayar'  => $tanggalBayar,
                ]);

                // Lampiran per-GPS
                if ($request->hasFile("gps_items.{$idx}.lampiran")) {
                    Attachment::where('relation_type', 'gps')->where('relation_id', $gpsKendaraan->id)->delete();
                    $this->simpanAttachments(
                        $request->file("gps_items.{$idx}.lampiran"),
                        $gpsKendaraan->id,
                        'gps',
                        $history->id
                    );
                } else {
                    // Salin lampiran lama ke history
                    $oldAtts = Attachment::where('relation_type', 'gps')->where('relation_id', $gpsKendaraan->id)->get();
                    foreach ($oldAtts as $att) {
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
            }
        });

        return back()->with('success', count($gpsItems) . ' GPS kendaraan berhasil diperpanjang.');
    }
}
