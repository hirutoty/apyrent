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

    return view('admin.pajak_kendaraan.index', compact(
        'data',
        'kendaraan',
        'reminder'
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
        // Validation tetap lengkap
        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'jenis_pajak' => 'required',
            'nominal' => 'required|numeric',
            'jatuh_tempo' => 'required|date',
            'tanggal_bayar' => 'nullable|date',
            'status' => 'required',
            'keterangan' => 'nullable',
            'bukti' => 'nullable|file|max:5120',  // Changed to nullable karena upload saat approval
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

        // ===========================================================================
        // APPROVAL WORKFLOW: Intercept dan kirim ke Pembayaran
        // ===========================================================================
        
        try {
            // Check if this is a resubmit (from rejected pembayaran)
            if ($request->filled('edit_pembayaran')) {
                $pembayaranId = $request->input('edit_pembayaran');
                
                // Resubmit: Update existing pembayaran
                $pembayaran = $interceptor->resubmitToPembayaran($pembayaranId, $request, 'pajak');
                
                return redirect()
                    ->route('pembayaran.index', ['tab' => 'Pending'])
                    ->with('success', 'Pengajuan pajak berhasil diajukan ulang. Menunggu approval dari Superadmin.');
            }
            
            // Step 1: Intercept data dari form
            $interceptedData = $interceptor->intercept($request, 'pajak');
            
            // Step 2: Save ke Pembayaran
            $pembayaran = $interceptor->saveToPembayaran($interceptedData, 'pajak');
            
            // Step 3: Upload temporary files
            $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $pembayaran->id);
            
            // Step 4: Update source_data dengan file info
            $sourceData = $pembayaran->source_data;
            $sourceData['temp_files'] = $uploadedFiles;
            $pembayaran->update(['source_data' => $sourceData]);
            
            return redirect()
                ->route('pembayaran.index', ['tab' => 'Pending'])
                ->with('success', 'Pengajuan pengeluaran pajak berhasil dikirim. Menunggu approval dari Superadmin.');
                
        } catch (\Exception $e) {
            \Log::error('Error intercepting pajak submission: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan pengeluaran. Silakan coba lagi.');
        }
    }

    public function perpanjang(Request $request, $id, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'nominal'        => 'required|numeric',
            'tanggal_bayar'  => 'nullable|date',
            'keterangan'     => 'nullable',
            'bukti'          => 'nullable|file|max:5120',  // Changed to nullable - upload saat approval
            'bukti_attachment' => 'nullable|array',
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