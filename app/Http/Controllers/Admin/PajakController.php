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
        // Auto-update: record aktif yang jatuh_tempo sudah lewat → expired
        PajakKendaraan::where('status_aktif', 'aktif')
            ->where('jatuh_tempo', '<', now()->toDateString())
            ->update(['status_aktif' => 'expired']);

        // Tampilkan Pajak yang:
        // - Sudah melewati PO (bukan Pending = menunggu PO approval)
        // - Jika Ditolak: hanya tampilkan yang sudah sampai tahap Pembayaran (punya pembayaran_id)
        $query = PajakKendaraan::with(['kendaraan', 'attachments'])
            ->whereNotNull('persetujuan')
            ->where('persetujuan', '!=', 'Pending')
            ->where(function ($q) {
                $q->where('persetujuan', '!=', 'Ditolak')
                  ->orWhereNotNull('pembayaran_id'); // Ditolak di Pembayaran → tampilkan
            })
            ->latest();

    if ($request->filled('status') && in_array($request->status, ['sudah_bayar', 'belum_bayar'])) {
        $query->where('status', $request->status);
    }

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

    // Cek jika ada edit_pembayaran di URL (dari halaman Pembayaran Ditolak)
    $editPembayaranId = request('edit_pembayaran');
    $pajakDitolak     = null;
    $rejectionReason  = request('rejection_reason');

    if ($editPembayaranId) {
        $pembayaranDitolak = \App\Models\Pembayaran::find($editPembayaranId);
        if ($pembayaranDitolak && $pembayaranDitolak->status === 'Ditolak') {
            $sourceData   = $pembayaranDitolak->source_data ?? [];
            $existingId   = $sourceData['existing_record_id'] ?? null;
            if ($existingId) {
                $pajakDitolak = PajakKendaraan::find($existingId);
            }
        }
    }

    return view('admin.pajak_kendaraan.index', compact(
        'data',
        'kendaraan',
        'reminder',
        'editPembayaranId',
        'pajakDitolak',
        'rejectionReason'
    ));
}

    /**
     * Helper: simpan banyak attachment sekaligus.
     *
     * @param  array   $files        Array file dari $request->file(...)
     * @param  int     $relationId   ID record target (pajak aktif atau history)
     * @param  string  $relationType Tipe relasi, default 'pajak'
     */
    private function simpanAttachments($files, $relationId, $relationType = 'pajak', $historyId = null)
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
                'relation_type' => $relationType,
                'relation_id'   => $relationId,
                'file_name'     => $originalName,
                'file_path'     => 'pajak/attachments/' . $filename,
                'file_type'     => $extension,
                'file_size'     => $size,
            ]);

            if ($historyId) {
                Attachment::create([
                    'relation_type' => $relationType . '_history',
                    'relation_id'   => $historyId,
                    'file_name'     => $originalName,
                    'file_path'     => 'pajak/attachments/' . $filename,
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
            'jenis_pajak'        => 'required',
            'nominal'            => 'required|numeric',
            'jatuh_tempo'        => 'nullable|date',
            'tanggal_bayar'      => 'nullable|date',
            'keterangan'         => 'nullable',
            'nama_pemilik'       => 'nullable|string|max:255',
            'nama_bank'          => 'nullable|string|max:255',
            'no_rekening'        => 'nullable|string|max:100',
            'bukti_attachment'   => 'required|array|min:1',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        // ── RESUBMIT PO: edit_purchase_order diisi (dari halaman Ajukan Ulang setelah PO ditolak) ──
        if ($request->filled('edit_purchase_order')) {
            $poId = (int) $request->input('edit_purchase_order');

            try {
                $po = \App\Models\PurchaseOrder::findOrFail($poId);

                if ($po->status !== 'Ditolak') {
                    return redirect()
                        ->route('pajak.index')
                        ->with('error', 'Purchase Order ini tidak dapat diajukan ulang (status: ' . $po->status . ').');
                }

                $sourceData = $po->source_data ?? [];
                $existingId = $sourceData['existing_record_id'] ?? null;

                // Update record pajak dengan data baru
                if ($existingId) {
                    PajakKendaraan::where('id', $existingId)->update([
                        'persetujuan'   => 'Pending',
                        'kendaraan_id'  => $request->kendaraan_id,
                        'jenis_pajak'   => $request->jenis_pajak,
                        'nominal'       => $request->nominal,
                        'jatuh_tempo'   => $request->jatuh_tempo,
                        'tanggal_bayar' => $request->tanggal_bayar,
                        'keterangan'    => $request->keterangan,
                        'nama_pemilik'  => $request->nama_pemilik,
                        'nama_bank'     => $request->nama_bank,
                        'no_rekening'   => $request->no_rekening,
                    ]);

                    // Simpan lampiran baru (akumulatif — lampiran lama TIDAK dihapus)
                    if ($request->hasFile('bukti_attachment')) {
                        $this->simpanAttachments($request->file('bukti_attachment'), $existingId);
                    }
                }

                // Update source_data PO dengan data baru
                $newSourceData = array_merge($sourceData, [
                    'kendaraan_id'  => $request->kendaraan_id,
                    'jenis_pajak'   => $request->jenis_pajak,
                    'nominal'       => $request->nominal,
                    'jatuh_tempo'   => $request->jatuh_tempo,
                    'tanggal_bayar' => $request->tanggal_bayar,
                    'keterangan'    => $request->keterangan,
                    'nama_pemilik'  => $request->nama_pemilik,
                    'nama_bank'     => $request->nama_bank,
                    'no_rekening'   => $request->no_rekening,
                    'existing_record_id' => $existingId,
                ]);

                $po->update([
                    'source_data'         => $newSourceData,
                    'total_harga'         => $request->nominal,
                    'status'              => 'Pending',
                    'catatan_approval'    => null,
                    'disetujui_oleh'      => null,
                    'tanggal_persetujuan' => null,
                    'can_edit'            => false,
                    'terakhir_diajukan'   => now(),
                ]);

                return redirect()
                    ->route('purchase-order.index', ['status' => 'Pending'])
                    ->with('success', 'Pajak berhasil diajukan ulang ke Purchase Order. Menunggu approval Superadmin.');

            } catch (\Exception $e) {
                \Log::error('Error resubmit PO pajak: ' . $e->getMessage());
                return redirect()
                    ->route('pajak.ajukan-ulang', $poId)
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan saat mengajukan ulang: ' . $e->getMessage());
            }
        }

        // ── RESUBMIT PEMBAYARAN: edit_pembayaran diisi (dari halaman Ajukan Ulang setelah Pembayaran ditolak) ──
        if ($request->filled('edit_pembayaran')) {
            $pembayaranId = (int) $request->input('edit_pembayaran');

            try {
                $pembayaran = \App\Models\Pembayaran::findOrFail($pembayaranId);

                \Log::info("Resubmit pajak: pembayaran #{$pembayaranId} status={$pembayaran->status}");

                // Hanya izinkan dari status Ditolak atau Pending (refresh / double-submit)
                if (!in_array($pembayaran->status, ['Ditolak', 'Pending'])) {
                    return redirect()
                        ->route('pajak.index')
                        ->with('error', 'Pengajuan ini tidak dapat diajukan ulang (status: ' . $pembayaran->status . ').');
                }

                // Gabungkan source_data lama dengan data baru dari form
                $sourceData = $pembayaran->source_data ?? [];
                $sourceData = array_merge($sourceData, [
                    'kendaraan_id'  => $request->kendaraan_id,
                    'jenis_pajak'   => $request->jenis_pajak,
                    'nominal'       => $request->nominal,
                    'jatuh_tempo'   => $request->jatuh_tempo,
                    'tanggal_bayar' => $request->tanggal_bayar,
                    'keterangan'    => $request->keterangan,
                    'nama_pemilik'  => $request->nama_pemilik,
                    'nama_bank'     => $request->nama_bank,
                    'no_rekening'   => $request->no_rekening,
                    'status'        => 'belum_bayar',
                ]);

                $pembayaran->update([
                    'source_data' => $sourceData,
                    'nominal'     => $request->nominal,
                    'nama_bank'   => $request->nama_bank,
                    'no_rekening' => $request->no_rekening,
                    'status'      => 'Pending',
                    'can_edit'    => false,
                ]);

                // Update persetujuan record pajak → Pending
                $existingId = $sourceData['existing_record_id'] ?? null;
                if ($existingId) {
                    PajakKendaraan::where('id', $existingId)
                        ->update(['persetujuan' => 'Pending']);
                }

                // Simpan lampiran baru jika ada
                if ($request->hasFile('bukti_attachment') && $existingId) {
                    $this->simpanAttachments($request->file('bukti_attachment'), $existingId);
                }

                return redirect()
                    ->route('pajak.index')
                    ->with('success', 'Pengajuan pajak berhasil diajukan ulang. Menunggu approval dari Superadmin.');

            } catch (\Exception $e) {
                \Log::error('Error resubmit pajak: ' . $e->getMessage());
                return redirect()
                    ->route('pajak.ajukan-ulang', $pembayaranId)
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan saat mengajukan ulang: ' . $e->getMessage());
            }
        }

        $exists = PajakKendaraan::where('kendaraan_id', $kendaraan->id)
            ->whereIn('persetujuan', ['Pending', 'Diajukan ke Pembayaran', 'Disetujui'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kendaraan ini sudah memiliki data pajak aktif atau sedang dalam proses approval.');
        }

        try {
            // ===========================================================================
            // NEW FLOW: Kirim ke Purchase Order (bukan langsung Pembayaran)
            // ===========================================================================

            // Step 1: Buat record pajak dengan persetujuan=Pending
            // (belum muncul di tabel utama sampai PO disetujui)
            $pajak = PajakKendaraan::create([
                'pembayaran_id' => null, // Diisi setelah PO disetujui & Pembayaran dibuat
                'kendaraan_id'  => $request->kendaraan_id,
                'jenis_pajak'   => $request->jenis_pajak,
                'nominal'       => $request->nominal,
                'jatuh_tempo'   => $request->jatuh_tempo,
                'tanggal_bayar' => $request->tanggal_bayar,
                'status'        => 'belum_bayar',
                'status_aktif'  => 'tidak_aktif',
                'keterangan'    => $request->keterangan,
                'nama_pemilik'  => $request->nama_pemilik,
                'nama_bank'     => $request->nama_bank,
                'no_rekening'   => $request->no_rekening,
                'persetujuan'   => 'Pending',
            ]);

            // Step 2: Simpan lampiran
            if ($request->hasFile('bukti_attachment')) {
                $this->simpanAttachments($request->file('bukti_attachment'), $pajak->id);
            }

            // Step 3: Intercept & buat Purchase Order
            $request->merge([
                'nama_rekening' => $request->nama_pemilik,
            ]);
            $interceptedData = $interceptor->intercept($request, 'pajak');

            // Sematkan existing_record_id agar PO approval bisa update record yang sudah ada
            $interceptedData['source_data']['existing_record_id'] = $pajak->id;

            $po = $interceptor->saveToPurchaseOrder($interceptedData, 'pajak');

            // Step 4: Update source_data PO
            $sourceData = $po->source_data;
            $sourceData['existing_record_id'] = $pajak->id;
            $po->update(['source_data' => $sourceData]);

            return redirect()
                ->route('purchase-order.index', ['status' => 'Pending'])
                ->with('success', 'Pengajuan pajak berhasil dikirim ke Purchase Order. Menunggu approval dari Superadmin.');

        } catch (\Exception $e) {
            \Log::error('Error creating pajak: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman ajukan ulang pajak yang ditolak (dedicated page, bukan modal)
     * Handles both: PO ditolak dan Pembayaran ditolak
     */
    public function ajukanUlangForm($id)
    {
        // Coba sebagai pembayaran_id dulu
        $pembayaran = \App\Models\Pembayaran::find($id);
        $isPo = false;
        $po   = null;

        if (!$pembayaran) {
            $po   = \App\Models\PurchaseOrder::find($id);
            $isPo = true;
        }

        if (!$pembayaran && !$po) {
            return redirect()->route('pajak.index')->with('error', 'Data tidak ditemukan.');
        }

        if ($isPo) {
            if ($po->status !== 'Ditolak') {
                return redirect()->route('pajak.index')->with('error', 'Hanya pengajuan yang ditolak yang dapat diedit.');
            }
            $sourceData      = $po->source_data ?? [];
            $existingId      = $sourceData['existing_record_id'] ?? null;
            $pajak           = $existingId ? PajakKendaraan::with(['kendaraan', 'attachments'])->findOrFail($existingId) : null;
            $rejectionReason = $po->catatan_approval ?? null;
            $editPoId        = $po->id;
            $editPembayaranId = null;
        } else {
            if ($pembayaran->status !== 'Ditolak') {
                return redirect()->route('pajak.index')->with('error', 'Hanya pengajuan yang ditolak yang dapat diedit.');
            }
            $sourceData      = $pembayaran->source_data ?? [];
            $existingId      = $sourceData['existing_record_id'] ?? null;
            $pajak           = $existingId ? PajakKendaraan::with(['kendaraan', 'attachments'])->findOrFail($existingId) : null;
            $rejectionReason = $pembayaran->latestApproval?->catatan ?? null;
            $editPoId        = null;
            $editPembayaranId = $pembayaran->id;
        }

        if (!$pajak) {
            return redirect()->route('pajak.index')->with('error', 'Data pajak tidak ditemukan.');
        }

        $kendaraan = \App\Models\Kendaraan::all();

        return view('admin.pajak_kendaraan.ajukan-ulang', compact(
            'pajak',
            'editPoId',
            'editPembayaranId',
            'rejectionReason',
            'kendaraan'
        ));
    }

    public function perpanjang(Request $request, $id, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'nominal'        => 'required|numeric',
            'tanggal_bayar'  => 'nullable|date',
            'keterangan'     => 'nullable',
            'bukti'          => 'nullable|file|max:5120',
            'bukti_attachment' => 'required|array|min:1',
            'bukti_attachment.*' => 'file|max:5120',
            'nama_bank'      => 'nullable|string|max:255',
            'no_rekening'    => 'nullable|string|max:100',
            'nama_rekening'  => 'nullable|string|max:255',
            'informasi'      => 'nullable|string',
        ]);

        $pajak = PajakKendaraan::findOrFail($id);

        // Cek: masa berlaku masih > 30 hari ke depan, perpanjangan belum diperlukan
        if ($pajak->jatuh_tempo && Carbon::parse($pajak->jatuh_tempo)->diffInDays(now(), false) < -30) {
            return back()->with('error', 'Masa berlaku pajak masih panjang (> 30 hari), perpanjangan belum diperlukan.');
        }

        // ===========================================================================
        // APPROVAL WORKFLOW: Perpanjang melalui Pembayaran untuk approval
        // ===========================================================================
        
        try {
            $pembayaran = $interceptor->perpanjangViaPembayaran($request, 'pajak', $pajak);
            
            return redirect()
                ->route('pembayaran.index', ['tab' => 'Pending'])
                ->with('success', 'Pengajuan perpanjangan pajak berhasil dikirim. Menunggu approval dari Superadmin.');
                
        } catch (\Exception $e) {
            \Log::error('Error perpanjang Pajak via Pembayaran: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan perpanjangan. Silakan coba lagi.');
        }
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

    /**
     * AJAX: detail per record pajak + data chart perpanjangan Jan-Des
     */
    public function detail(Request $request, $id)
    {
        $pajak = PajakKendaraan::with(['kendaraan', 'attachments'])->findOrFail($id);
        $tahun = (int) $request->input('tahun', now()->year);

        // History perpanjangan untuk tabel
        $histories = PajakHistory::where('pajak_kendaraan_id', $id)
            ->orderBy('diperpanjang_pada', 'desc')
            ->get()
            ->map(fn($h) => [
                'id'               => $h->id,
                'jenis_pajak'      => $h->jenis_pajak,
                'nominal'          => $h->nominal,
                'tanggal_bayar'    => $h->tanggal_bayar?->format('d M Y'),
                'jatuh_tempo'      => $h->jatuh_tempo?->format('d M Y'),
                'status'           => $h->status,
                'keterangan'       => $h->keterangan,
                'bukti'            => $h->bukti ? asset($h->bukti) : null,
                'diperpanjang_pada'=> $h->diperpanjang_pada?->format('d M Y'),
            ]);

        // Chart: nominal perpanjangan per bulan (Jan-Des) tahun terpilih
        $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        $chartData   = [];
        for ($b = 1; $b <= 12; $b++) {
            $chartData[] = (float) PajakHistory::where('pajak_kendaraan_id', $id)
                ->whereYear('diperpanjang_pada', $tahun)
                ->whereMonth('diperpanjang_pada', $b)
                ->sum('nominal');
        }

        // Tahun-tahun yang ada di history untuk dropdown filter
        $availableYears = PajakHistory::where('pajak_kendaraan_id', $id)
            ->selectRaw('YEAR(diperpanjang_pada) as yr')
            ->whereNotNull('diperpanjang_pada')
            ->distinct()
            ->orderBy('yr', 'desc')
            ->pluck('yr');

        return response()->json([
            'success' => true,
            'record'  => [
                'id'           => $pajak->id,
                'nopol'        => $pajak->kendaraan->nopol ?? '-',
                'merk'         => $pajak->kendaraan->merk ?? '-',
                'jenis_pajak'  => $pajak->jenis_pajak,
                'nominal'      => $pajak->nominal,
                'tanggal_bayar'=> $pajak->tanggal_bayar ? \Carbon\Carbon::parse($pajak->tanggal_bayar)->format('d M Y') : '-',
                'jatuh_tempo'  => $pajak->jatuh_tempo ? \Carbon\Carbon::parse($pajak->jatuh_tempo)->format('d M Y') : '-',
                'status'       => $pajak->status,
                'keterangan'   => $pajak->keterangan,
                'bukti'        => $pajak->bukti ? asset($pajak->bukti) : null,
            ],
            'histories'      => $histories,
            'chart'          => [
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