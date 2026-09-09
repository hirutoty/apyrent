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
        $request->validate([
            'kendaraan_id'       => 'required|exists:kendaraan,id',
            'asuransi_id'        => 'required|exists:asuransi,id',
            'jenis_asuransi_id'  => 'required|exists:jenis_asuransi,id',
            'tgl_mulai'          => 'required|date',
            'tgl_berakhir'       => 'required|date|after_or_equal:tgl_mulai',
            'durasi_bulan'       => 'required|integer|min:1',
            'biaya'              => 'required|numeric|min:0',
            'nama_rekening'      => 'nullable|string|max:255',
            'nama_bank'          => 'nullable|string|max:255',
            'no_rekening'        => 'nullable|string|max:100',
            'bukti_attachment'   => 'required|array|min:1',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        // ── RESUBMIT: edit_pembayaran diisi (dari halaman Ajukan Ulang setelah ditolak) ──
        if ($request->filled('edit_pembayaran')) {
            $pembayaranId = (int) $request->input('edit_pembayaran');

            try {
                $pembayaran = \App\Models\Pembayaran::findOrFail($pembayaranId);

                \Log::info("Resubmit asuransi kendaraan: pembayaran #{$pembayaranId} status={$pembayaran->status}");

                if (!in_array($pembayaran->status, ['Ditolak', 'Pending'])) {
                    return redirect()
                        ->route('asuransi-kendaraan.index')
                        ->with('error', 'Pengajuan ini tidak dapat diajukan ulang (status: ' . $pembayaran->status . ').');
                }

                // Gabungkan source_data lama dengan data baru dari form
                $sourceData = $pembayaran->source_data ?? [];
                $sourceData = array_merge($sourceData, [
                    'kendaraan_id'      => $request->kendaraan_id,
                    'asuransi_id'       => $request->asuransi_id,
                    'jenis_asuransi_id' => $request->jenis_asuransi_id,
                    'tgl_mulai'         => $request->tgl_mulai,
                    'tgl_berakhir'      => $request->tgl_berakhir,
                    'durasi_bulan'      => $request->durasi_bulan,
                    'biaya'             => $request->biaya,
                    'nama_rekening'     => $request->nama_rekening,
                    'nama_bank'         => $request->nama_bank,
                    'no_rekening'       => $request->no_rekening,
                ]);

                $pembayaran->update([
                    'source_data' => $sourceData,
                    'nominal'     => $request->biaya,
                    'nama_bank'   => $request->nama_bank,
                    'no_rekening' => $request->no_rekening,
                    'status'      => 'Pending',
                    'can_edit'    => false,
                ]);

                // Update persetujuan & data asuransi record → Pending
                $existingId = $sourceData['existing_record_id'] ?? null;
                if ($existingId) {
                    AsuransiKendaraan::where('id', $existingId)->update([
                        'persetujuan'       => 'Pending',
                        'asuransi_id'       => $request->asuransi_id,
                        'jenis_asuransi_id' => $request->jenis_asuransi_id,
                        'tgl_mulai'         => $request->tgl_mulai,
                        'tgl_berakhir'      => $request->tgl_berakhir,
                        'durasi_bulan'      => $request->durasi_bulan,
                        'biaya'             => $request->biaya,
                        'nama_rekening'     => $request->nama_rekening,
                        'nama_bank'         => $request->nama_bank,
                        'no_rekening'       => $request->no_rekening,
                    ]);
                }

                // Simpan lampiran baru jika ada
                if ($request->hasFile('bukti_attachment') && $existingId) {
                    $this->simpanAttachments($request->file('bukti_attachment'), $existingId);
                }

                return redirect()
                    ->route('asuransi-kendaraan.index')
                    ->with('success', 'Pengajuan asuransi berhasil diajukan ulang. Menunggu approval dari Superadmin.');

            } catch (\Exception $e) {
                \Log::error('Error resubmit asuransi kendaraan: ' . $e->getMessage());
                return redirect()
                    ->route('asuransi-kendaraan.ajukan-ulang', $pembayaranId)
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan saat mengajukan ulang: ' . $e->getMessage());
            }
        }

        // Cek duplikat: hanya blokir jika sudah ada data Pending atau Disetujui
        // (data Ditolak boleh daftar ulang)
        $exists = AsuransiKendaraan::where('kendaraan_id', $request->kendaraan_id)
            ->whereIn('persetujuan', ['Pending', 'Disetujui'])
            ->exists();
        if ($exists) {
            return back()->with('error', 'Kendaraan ini sudah memiliki data asuransi aktif atau sedang dalam proses approval.');
        }

        try {
            // Step 1: Intercept & buat Pembayaran untuk approval
            $interceptedData = $interceptor->intercept($request, 'asuransi_kendaraan');
            $pembayaran      = $interceptor->saveToPembayaran($interceptedData, 'asuransi_kendaraan');

            // Step 2: Simpan record ke asuransi_kendaraan dengan status tidak_aktif & Pending
            // Bukti bayar diisi saat Superadmin approve di halaman Pembayaran
            $asuransi = AsuransiKendaraan::create([
                'pembayaran_id'     => $pembayaran->id,
                'kendaraan_id'      => $request->kendaraan_id,
                'asuransi_id'       => $request->asuransi_id,
                'jenis_asuransi_id' => $request->jenis_asuransi_id,
                'tgl_mulai'         => $request->tgl_mulai,
                'tgl_berakhir'      => $request->tgl_berakhir,
                'durasi_bulan'      => $request->durasi_bulan,
                'biaya'             => $request->biaya,
                'bukti_bayar'       => null,
                'status_kendaraan'  => 'tidak_aktif',
                'persetujuan'       => 'Pending',
                'nama_rekening'     => $request->nama_rekening,
                'nama_bank'         => $request->nama_bank,
                'no_rekening'       => $request->no_rekening,
            ]);

            // Step 3: Simpan lampiran
            if ($request->hasFile('bukti_attachment')) {
                $this->simpanAttachments($request->file('bukti_attachment'), $asuransi->id);
            }

            // Step 4: Tandai existing_record_id di source_data pembayaran
            $sourceData = $pembayaran->source_data;
            $sourceData['existing_record_id'] = $asuransi->id;
            $pembayaran->update(['source_data' => $sourceData]);

            return redirect()
                ->route('asuransi-kendaraan.index')
                ->with('success', 'Pengajuan asuransi berhasil dikirim. Menunggu approval dari Superadmin.');

        } catch (\Exception $e) {
            \Log::error('Error store asuransi kendaraan: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Halaman ajukan ulang asuransi yang ditolak (dedicated page)
     */
    public function ajukanUlangForm($pembayaranId)
    {
        $pembayaran = \App\Models\Pembayaran::findOrFail($pembayaranId);

        if ($pembayaran->status !== 'Ditolak') {
            return redirect()->route('asuransi-kendaraan.index')
                ->with('error', 'Hanya pengajuan yang ditolak yang dapat diedit.');
        }

        $sourceData = $pembayaran->source_data ?? [];
        $existingId = $sourceData['existing_record_id'] ?? null;

        if (!$existingId) {
            return redirect()->route('asuransi-kendaraan.index')
                ->with('error', 'Data asuransi tidak ditemukan.');
        }

        $asuransi = AsuransiKendaraan::with(['kendaraan', 'asuransi', 'jenisAsuransi', 'attachments'])
            ->findOrFail($existingId);

        $rejectionReason  = $pembayaran->latestApproval?->catatan ?? null;
        $kendaraan        = Kendaraan::all();
        $allAsuransi      = Asuransi::all();
        $jenisAsuransi    = JenisAsuransi::all();

        return view('admin.asuransi.ajukan-ulang', compact(
            'asuransi',
            'pembayaranId',
            'rejectionReason',
            'kendaraan',
            'allAsuransi',
            'jenisAsuransi'
        ));
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
            'bukti_bayar'       => 'nullable|file|max:5120',
            'bukti_attachment'   => 'required|array|min:1',
            'bukti_attachment.*' => 'file|max:5120',
            'nama_bank'      => 'nullable|string|max:255',
            'no_rekening'    => 'nullable|string|max:100',
            'nama_rekening'  => 'nullable|string|max:255',
            'informasi'      => 'nullable|string',
        ]);

        $asuransi = AsuransiKendaraan::findOrFail($id);

        // Guard: masa berlaku masih > 30 hari ke depan, perpanjangan belum diperlukan
        if ($asuransi->tgl_berakhir && Carbon::parse($asuransi->tgl_berakhir)->diffInDays(now(), false) < -30) {
            return back()->with('error', 'Masa berlaku asuransi masih panjang (> 30 hari), perpanjangan belum diperlukan.');
        }

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
