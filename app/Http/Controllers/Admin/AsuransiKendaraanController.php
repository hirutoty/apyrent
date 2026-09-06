<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Asuransi;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Models\AsuransiKendaraan;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\AsuransiHistory;
use App\Models\JenisAsuransi;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AsuransiKendaraanController extends Controller
{
    public function index(Request $request)
    {
        // update otomatis status expired
        AsuransiKendaraan::where('tgl_berakhir', '<', now())
            ->where('status_kendaraan', 'aktif')
            ->update(['status_kendaraan' => 'expired']);

        $query = AsuransiKendaraan::with([
            'kendaraan',
            'asuransi',
            'jenisAsuransi',
            'attachments'
        ])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('kendaraan', fn($k) =>
                        $k->where('nopol', 'like', "%{$s}%")
                          ->orWhere('merk', 'like', "%{$s}%")
                  )
                  ->orWhereHas('asuransi', fn($a) =>
                        $a->where('nama_asuransi', 'like', "%{$s}%")
                  )
                  ->orWhereHas('jenisAsuransi', fn($j) =>
                        $j->where('nama_jenis', 'like', "%{$s}%")
                  )
                  ->orWhere('status_kendaraan', 'like', "%{$s}%");
            });
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tgl_berakhir', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tgl_berakhir', $request->tahun);
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
            $hariIni     = now()->startOfDay();

            $d->sisaHari   = (int) $hariIni->diffInDays($tglBerakhir, false);
            $d->sisaDetik  = (int) (\Carbon\Carbon::parse($d->tgl_berakhir)->endOfDay()->timestamp - now()->timestamp);

            $d->isExpired = $d->sisaHari <= 0;
            $d->isSoon    = !$d->isExpired && $d->sisaHari <= $reminder;
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
     * Helper: simpan banyak attachment sekaligus.
     *
     * @param  array   $files        Array file dari $request->file(...)
     * @param  int     $relationId   ID record target (asuransi aktif atau history)
     * @param  string  $relationType Tipe relasi, default 'asuransi'
     */
    private function simpanAttachments($files, $relationId, $relationType = 'asuransi', $historyId = null)
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
                'relation_type' => $relationType,
                'relation_id'   => $relationId,
                'file_name'     => $originalName,
                'file_path'     => 'asuransi/attachments/' . $filename,
                'file_type'     => $extension,
                'file_size'     => $size,
            ]);

            if ($historyId) {
                Attachment::create([
                    'relation_type' => $relationType . '_history',
                    'relation_id'   => $historyId,
                    'file_name'     => $originalName,
                    'file_path'     => 'asuransi/attachments/' . $filename,
                    'file_type'     => $extension,
                    'file_size'     => $size,
                ]);
            }
        }
    }

    public function store(Request $request, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        // Validation tetap lengkap
        $request->validate([
            'kendaraan_id'       => 'required|exists:kendaraan,id',
            'asuransi_id'        => 'required|exists:asuransi,id',
            'jenis_asuransi_id'  => 'required|exists:jenis_asuransi,id',
            'tgl_mulai'          => 'required|date',
            'tgl_berakhir'       => 'required|date|after_or_equal:tgl_mulai',
            'durasi_bulan'       => 'required|integer|min:1',
            'biaya'              => 'required|numeric|min:0',
            'bukti_bayar'        => 'nullable|file|max:5120',  // Changed to nullable karena upload saat approval
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

        // ===========================================================================
        // APPROVAL WORKFLOW: Intercept dan kirim ke Pembayaran
        // ===========================================================================
        
        try {
            // Check if this is a resubmit (from rejected pembayaran)
            if ($request->filled('edit_pembayaran')) {
                $pembayaranId = $request->input('edit_pembayaran');
                
                // Resubmit: Update existing pembayaran
                $pembayaran = $interceptor->resubmitToPembayaran($pembayaranId, $request, 'asuransi_kendaraan');
                
                return redirect()
                    ->route('pembayaran.index', ['tab' => 'Pending'])
                    ->with('success', 'Pengajuan asuransi berhasil diajukan ulang. Menunggu approval dari Superadmin.');
            }
            
            // Step 1: Intercept data dari form
            $interceptedData = $interceptor->intercept($request, 'asuransi_kendaraan');
            
            // Step 2: Save ke Pembayaran
            $pembayaran = $interceptor->saveToPembayaran($interceptedData, 'asuransi_kendaraan');
            
            // Step 3: Upload temporary files
            $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $pembayaran->id);
            
            // Step 4: Update source_data dengan file info
            $sourceData = $pembayaran->source_data;
            $sourceData['temp_files'] = $uploadedFiles;
            $pembayaran->update(['source_data' => $sourceData]);
            
            return redirect()
                ->route('pembayaran.index', ['tab' => 'Pending'])
                ->with('success', 'Pengajuan pengeluaran asuransi berhasil dikirim. Menunggu approval dari Superadmin.');
                
        } catch (\Exception $e) {
            \Log::error('Error intercepting asuransi kendaraan submission: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan pengeluaran. Silakan coba lagi.');
        }
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

    /**
     * AJAX: detail per record asuransi kendaraan + chart perpanjangan Jan-Des
     */
    public function detail(Request $request, $id)
    {
        $asuransi = AsuransiKendaraan::with(['kendaraan','asuransi','jenisAsuransi','attachments'])->findOrFail($id);
        $tahun    = (int) $request->input('tahun', now()->year);

        $histories = AsuransiHistory::with(['asuransi','jenisAsuransi'])
            ->where('asuransi_kendaraan_id', $id)
            ->orderBy('diperpanjang_pada', 'desc')
            ->get()
            ->map(fn($h) => [
                'id'               => $h->id,
                'perusahaan'       => $h->asuransi->nama_asuransi ?? '-',
                'jenis'            => $h->jenisAsuransi->nama_jenis ?? '-',
                'biaya'            => $h->biaya,
                'tgl_mulai'        => $h->tgl_mulai ? \Carbon\Carbon::parse($h->tgl_mulai)->format('d M Y') : '-',
                'tgl_berakhir'     => $h->tgl_berakhir ? \Carbon\Carbon::parse($h->tgl_berakhir)->format('d M Y') : '-',
                'durasi_bulan'     => $h->durasi_bulan,
                'tanggal_bayar'    => $h->tanggal_bayar ? \Carbon\Carbon::parse($h->tanggal_bayar)->format('d M Y') : '-',
                'bukti_bayar'      => $h->bukti_bayar ? asset($h->bukti_bayar) : null,
                'diperpanjang_pada'=> $h->diperpanjang_pada ? \Carbon\Carbon::parse($h->diperpanjang_pada)->format('d M Y') : '-',
            ]);

        $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        $chartData   = [];
        for ($b = 1; $b <= 12; $b++) {
            $chartData[] = (float) AsuransiHistory::where('asuransi_kendaraan_id', $id)
                ->whereYear('diperpanjang_pada', $tahun)
                ->whereMonth('diperpanjang_pada', $b)
                ->sum('biaya');
        }

        $availableYears = AsuransiHistory::where('asuransi_kendaraan_id', $id)
            ->selectRaw('YEAR(diperpanjang_pada) as yr')
            ->whereNotNull('diperpanjang_pada')
            ->distinct()
            ->orderBy('yr', 'desc')
            ->pluck('yr');

        return response()->json([
            'success' => true,
            'record'  => [
                'id'          => $asuransi->id,
                'nopol'       => $asuransi->kendaraan->nopol ?? '-',
                'merk'        => $asuransi->kendaraan->merk ?? '-',
                'perusahaan'  => $asuransi->asuransi->nama_asuransi ?? '-',
                'jenis'       => $asuransi->jenisAsuransi->nama_jenis ?? '-',
                'biaya'       => $asuransi->biaya,
                'tgl_mulai'   => $asuransi->tgl_mulai ? \Carbon\Carbon::parse($asuransi->tgl_mulai)->format('d M Y') : '-',
                'tgl_berakhir'=> $asuransi->tgl_berakhir ? \Carbon\Carbon::parse($asuransi->tgl_berakhir)->format('d M Y') : '-',
                'durasi_bulan'=> $asuransi->durasi_bulan,
                'status'      => $asuransi->status_kendaraan,
                'bukti_bayar' => $asuransi->bukti_bayar ? asset($asuransi->bukti_bayar) : null,
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

    public function perpanjang(Request $request, $id, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'asuransi_id'       => 'required|exists:asuransi,id',
            'jenis_asuransi_id' => 'required|exists:jenis_asuransi,id',
            'tgl_berakhir'      => 'required|date',
            'durasi_bulan'      => 'required|integer|min:1',
            'biaya'             => 'required|numeric|min:0',
            'tanggal_bayar'     => 'nullable|date',
            'bukti_bayar'       => 'nullable|file|max:5120',  // Changed to nullable - upload saat approval
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
            'nama_bank'      => 'nullable|string|max:255',
            'no_rekening'    => 'nullable|string|max:100',
            'nama_rekening'  => 'nullable|string|max:255',
            'informasi'      => 'nullable|string',
        ]);

        $asuransi = AsuransiKendaraan::findOrFail($id);

        // ===========================================================================
        // APPROVAL WORKFLOW: Perpanjang melalui Pembayaran untuk approval
        // ===========================================================================
        
        try {
            $pembayaran = $interceptor->perpanjangViaPembayaran($request, 'asuransi_kendaraan', $asuransi);
            
            return redirect()
                ->route('pembayaran.index', ['tab' => 'Pending'])
                ->with('success', 'Pengajuan perpanjangan asuransi berhasil dikirim. Menunggu approval dari Superadmin.');
                
        } catch (\Exception $e) {
            \Log::error('Error perpanjang Asuransi via Pembayaran: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan perpanjangan. Silakan coba lagi.');
        }
    }
}