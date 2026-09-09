<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceHistory;
use App\Models\ServicePart;
use App\Models\ServiceCategory;
use App\Models\ServiceCategoryLimit;
use App\Models\ServiceDetail;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\Setting;
use App\Models\Attachment;
use App\Models\ReminderService;
use App\Models\Supplier;
use App\Models\Purchasero;
use App\Models\PurchaseroServicePart;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bulan           = $request->bulan ?? now()->format('Y-m');
        $categoryId      = $request->category_id;
        $kendaraanId     = $request->kendaraan_id;
        $approvalStatus  = $request->approval_status; // all, pending, approved, rejected

        $data = ServiceHistory::with([
                'kendaraan.jenis',
                'attachments',
                'parts.category',
            ])
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service,'%Y-%m') = ?", [$bulan]))
            ->when($kendaraanId, fn($q) => $q->where('kendaraan_id', $kendaraanId))
            ->when($approvalStatus === 'pending', fn($q) => $q->where('status_approval', 'pending'))
            ->when($approvalStatus === 'approved', fn($q) => $q->where('status_approval', 'approved'))
            ->when($approvalStatus === 'rejected', fn($q) => $q->where('status_approval', 'rejected'))
            ->when($approvalStatus === 'limit', fn($q) => $q->where('status', 'limit'))
            ->when($categoryId, fn($q) => $q->whereHas('parts', fn($p) => $p->where('category_id', $categoryId)))
            ->latest()
            ->paginate($request->per_page ?? 50)->withQueryString();

        $kendaraan  = Kendaraan::whereNotIn('status_kendaraan', ['disewa'])
            ->orderBy('merk')
            ->get();

        $categories = ServiceCategory::orderBy('nama')->get();

        // Summary: status limit per kendaraan (untuk cards)
        $allKendaraan = Kendaraan::all();
        $aman = 0; $hampir = 0; $habis = 0;
        foreach ($allKendaraan as $k) {
            $limit = $k->limit_biaya_bulanan_service ?? 0;
            if ($limit <= 0) continue;
            $total = ServiceHistory::where('kendaraan_id', $k->id)
                ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan])
                ->sum('total_biaya');
            $persen = ($total / $limit) * 100;
            if ($persen >= 100) $habis++;
            elseif ($persen >= 70) $hampir++;
            else $aman++;
        }

        return view('admin.service.service_history', [
            'data'               => $data,
            'kendaraan'          => $kendaraan,
            'categories'         => $categories,
            'bulan'              => $bulan,
            'aman'               => $aman,
            'hampir'             => $hampir,
            'habis'              => $habis,
            // Map limit price: "{kendaraan_id}_{category_id}" => limit_price
            // Dipakai di view untuk badge "Melebihi Limit" tanpa N+1 query
            'categoryLimitsMap'  => ServiceCategoryLimit::all()
                ->keyBy(fn($r) => $r->kendaraan_id . '_' . $r->category_id),
        ]);
    }

    /**
     * Show create form — pre-filled dari reminder jika ada ?from_reminder=ID
     */
    public function create(Request $request)
    {
        $kendaraan  = Kendaraan::orderBy('merk')->get();
        $categories = ServiceCategory::orderBy('nama')->get();
        $suppliers  = Supplier::orderBy('nama_supplier')->get();
        $prefill    = null;

        if ($request->from_reminder) {
            $reminder = ReminderService::with(['servicePart.category', 'servicePart.serviceHistory', 'kendaraan'])->find($request->from_reminder);
            if ($reminder && $reminder->servicePart) {
                $part = $reminder->servicePart;
                $serviceHistory = $part->serviceHistory;
                
                $prefill = [
                    'reminder_id'       => $reminder->id,
                    'kendaraan_id'      => $reminder->kendaraan_id,
                    'kendaraan'         => $reminder->kendaraan,
                    'service_history_id' => $serviceHistory?->id,
                    'tanggal_service'   => $serviceHistory?->tanggal_service,
                    'kilometer'         => $serviceHistory?->kilometer,
                    'keluhan'           => $serviceHistory?->keluhan,
                    'status'            => $serviceHistory?->status ?? 'proses',
                    'part'              => [
                        'service_part_id' => $part->id,
                        'nama_part'       => $part->nama_part,
                        'part_number'     => $part->part_number,
                        'posisi'          => $part->posisi,
                        'category_id'     => $part->category_id,
                        'category_nama'   => $part->category?->nama,
                        'interval_nilai'  => $part->interval_nilai,
                        'interval_satuan' => $part->interval_satuan,
                        'biaya'           => $part->biaya,
                        'kondisi'         => $part->kondisi,
                    ],
                ];
            }
        }

        return view('admin.service.service_history_create', compact('kendaraan', 'categories', 'prefill', 'suppliers'));
    }

    /**
     * Show Request Part form
     */
    public function requestCreate(Request $request)
    {
        $kendaraan  = Kendaraan::orderBy('merk')->get();
        $categories = ServiceCategory::orderBy('nama')->get();
        $suppliers  = Supplier::orderBy('nama_supplier')->get();
        $prefill    = null;
        return view('admin.service.service_history_request', compact('kendaraan', 'categories', 'suppliers', 'prefill'));
    }

    /**
     * Store Request Part — semua parts masuk melalui PengeluaranInterceptorService
     * (alur seragam dengan Tambah Service: tampil di pembayaran.index tab Pending)
     */
    public function requestStore(Request $request, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'kendaraan_id'                 => 'required|exists:kendaraan,id',
            'tanggal_service'              => 'required|date',
            'kilometer'                    => 'required|integer|min:0',
            'status'                       => 'nullable|in:proses,selesai',
            'keluhan'                      => 'nullable|string',
            'alasan_permintaan'            => 'nullable|string',
            'keterangan_pengadaan'         => 'nullable|string|max:500',
            'total_biaya_override'         => 'nullable|numeric|min:0',
            'bukti_attachment'             => 'nullable|array',
            'bukti_attachment.*'           => 'file|max:5120',
            'supplier_id'                  => 'nullable|exists:supplier,id',
            'parts'                        => 'required|array|min:1',
            'parts.*.nama_part'            => 'required_with:parts|string|max:255',
            'parts.*.category_id'          => 'nullable',
            'parts.*.nama_category_baru'   => 'nullable|string|max:100',
            'parts.*.part_number'          => 'nullable|string|max:100',
            'parts.*.serial_number'        => 'nullable|string|max:100',
            'parts.*.posisi'               => 'nullable|string|max:100',
            'parts.*.tgl_pasang'           => 'required_with:parts|date',
            'parts.*.kilometer_pasang'     => 'nullable|integer|min:0',
            'parts.*.kondisi'              => 'nullable|in:Baik,Rusak,Perlu Ganti',
            'parts.*.status'               => 'nullable|in:Terpasang,Proses',
            'parts.*.interval_nilai'       => 'required_with:parts|integer|min:1',
            'parts.*.interval_satuan'      => 'required_with:parts|in:hari,minggu,bulan,tahun',
            'parts.*.biaya'                => 'nullable|numeric|min:0',
            'parts.*.bukti'                => 'required|array|min:1',
            'parts.*.bukti.*'              => 'file|mimes:jpg,jpeg,png,mp4,mov',
            'parts.*.keterangan'           => 'nullable|string|max:1000',
            'parts.*.nama_rekening'        => 'nullable|string|max:150',
            'parts.*.nama_bank'            => 'nullable|string|max:100',
            'parts.*.no_rekening'          => 'nullable|string|max:50',
        ]);

        // =======================================================================
        // APPROVAL WORKFLOW: Intercept dan kirim ke Pembayaran (sama seperti store)
        // =======================================================================
        try {
            // Step 1: Intercept data dari form
            $interceptedData = $interceptor->intercept($request, 'service_part');

            // Step 2: Save ke Pembayaran
            $pembayaran = $interceptor->saveToPembayaran($interceptedData, 'service_part');

            // Step 3: Upload temporary files (attachment)
            $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $pembayaran->id);

            // Step 4: Update source_data dengan file info
            $sourceData = $pembayaran->source_data;
            $sourceData['temp_files'] = $uploadedFiles;
            $pembayaran->update(['source_data' => $sourceData]);

            return redirect()
                ->route('pembayaran.index', ['tab' => 'Pending'])
                ->with('success', 'Request part berhasil dikirim. Menunggu approval dari Superadmin.');

        } catch (\Exception $e) {
            \Log::error('Error intercepting request part submission: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan request part. Silakan coba lagi.');
        }
    }

    /**
     * Edit request pending
     */
    public function editRequest($id)
    {
        $service = ServiceHistory::with(['kendaraan', 'parts.category'])->findOrFail($id);

        if ($service->status_approval !== 'pending') {
            return redirect()->route('service-history.index')
                ->with('error', 'Hanya request dengan status pending yang bisa diedit.');
        }

        $kendaraans  = Kendaraan::orderBy('nomor_polisi')->get();
        $categories  = ServiceCategory::orderBy('nama')->get();

        return view('admin.service.service_history_edit_request', compact('service', 'kendaraans', 'categories'));
    }

    /**
     * Update request pending
     */
    public function updateRequest(Request $request, $id)
    {
        $service = ServiceHistory::findOrFail($id);

        if ($service->status_approval !== 'pending') {
            return redirect()->route('service-history.index')
                ->with('error', 'Hanya request dengan status pending yang bisa diupdate.');
        }

        $request->validate([
            'kendaraan_id'                 => 'required|exists:kendaraan,id',
            'tanggal_service'              => 'required|date',
            'kilometer'                    => 'required|integer|min:0',
            'status'                       => 'nullable|in:proses,selesai',
            'keluhan'                      => 'nullable|string',
            'total_biaya_override'         => 'nullable|numeric|min:0',
            'bukti_pembayaran'             => 'nullable|file|max:5120',
            'parts'                        => 'required|array|min:1',
            'parts.*.nama_part'            => 'required_with:parts|string|max:255',
            'parts.*.category_id'          => 'nullable',
            'parts.*.nama_category_baru'   => 'nullable|string|max:100',
            'parts.*.part_number'          => 'nullable|string|max:100',
            'parts.*.serial_number'        => 'nullable|string|max:100',
            'parts.*.posisi'               => 'nullable|string|max:100',
            'parts.*.tgl_pasang'           => 'required_with:parts|date',
            'parts.*.kilometer_pasang'     => 'nullable|integer|min:0',
            'parts.*.kondisi'              => 'nullable|in:Baik,Rusak,Perlu Ganti',
            'parts.*.status'               => 'nullable|in:Terpasang,Proses',
            'parts.*.interval_nilai'       => 'required_with:parts|integer|min:1',
            'parts.*.interval_satuan'      => 'required_with:parts|in:hari,minggu,bulan,tahun',
            'parts.*.biaya'                => 'nullable|numeric|min:0',
            'parts.*.bukti'                => 'nullable|array',
            'parts.*.bukti.*'              => 'file|mimes:jpg,jpeg,png,mp4,mov',
            'parts.*.keterangan'           => 'nullable|string|max:1000',
            'parts.*.nama_rekening'        => 'nullable|string|max:150',
            'parts.*.nama_bank'            => 'nullable|string|max:100',
            'parts.*.no_rekening'          => 'nullable|string|max:50',
        ]);

        $kendaraan     = Kendaraan::findOrFail($request->kendaraan_id);
        $resolvedParts = $this->resolvePartsCategory($request->parts ?? []);

        // Tentukan status_pengeluaran per-part berdasarkan limit_price kategori
        $partStatuses  = $this->resolvePartStatusPengeluaran($resolvedParts, $request->kendaraan_id);

        $sumBiayaParts = collect($resolvedParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));
        $totalBiaya    = filled($request->total_biaya_override) && (int)$request->total_biaya_override > 0
            ? (int)$request->total_biaya_override
            : $sumBiayaParts;

        $buktiBayarMeta  = $this->prepBuktiBayar($request);
        $attachmentsMeta = $this->prepAttachments($request);
        $movedFiles      = [];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $request, $service, $kendaraan, $totalBiaya, $partStatuses, $buktiBayarMeta, $attachmentsMeta,
                $resolvedParts, &$movedFiles
            ) {
                // Update bukti pembayaran jika ada
                $buktiBayar = $service->bukti_pembayaran;
                if ($buktiBayarMeta) {
                    // Hapus bukti lama
                    if ($buktiBayar && file_exists(public_path($buktiBayar))) {
                        unlink(public_path($buktiBayar));
                    }
                    if (!file_exists($buktiBayarMeta['destination'])) mkdir($buktiBayarMeta['destination'], 0777, true);
                    $buktiBayarMeta['file']->move($buktiBayarMeta['destination'], $buktiBayarMeta['filename']);
                    $buktiBayar   = $buktiBayarMeta['path'];
                    $movedFiles[] = public_path($buktiBayar);
                }

                // Update service history
                $service->update([
                    'kendaraan_id'    => $request->kendaraan_id,
                    'keluhan'         => $request->keluhan,
                    'kilometer'       => $request->kilometer,
                    'total_biaya'     => $totalBiaya,
                    'status'          => $this->deriveServiceStatus($resolvedParts),
                    'tanggal_service' => $request->tanggal_service,
                    'bukti_pembayaran' => $buktiBayar,
                ]);

                // Hapus parts lama
                foreach ($service->parts as $oldPart) {
                    if ($oldPart->bukti) {
                        $buktiArray = is_string($oldPart->bukti) ? json_decode($oldPart->bukti, true) : $oldPart->bukti;
                        if (is_array($buktiArray)) {
                            foreach ($buktiArray as $bf) {
                                if (isset($bf['path']) && file_exists(public_path($bf['path']))) {
                                    unlink(public_path($bf['path']));
                                }
                            }
                        }
                    }
                }
                $service->parts()->delete();

                // Insert parts baru
                foreach ($resolvedParts as $idx => $partData) {
                    $tglPasang    = Carbon::parse($partData['tgl_pasang']);
                    $tanggalLimit = $this->hitungTanggalLimitPart(
                        $tglPasang, (int)$partData['interval_nilai'], $partData['interval_satuan']
                    );

                    $buktiFiles = $this->uploadPartBuktiFiles($request, $idx);
                    foreach ($buktiFiles as $bf) {
                        $movedFiles[] = public_path($bf['path']);
                    }

                    ServicePart::create([
                        'service_history_id'  => $service->id,
                        'kendaraan_id'        => $request->kendaraan_id,
                        'category_id'         => $partData['category_id'] ?? null,
                        'nama_part'           => $partData['nama_part'],
                        'part_number'         => $partData['part_number'] ?? null,
                        'serial_number'       => $partData['serial_number'] ?? null,
                        'posisi'              => $partData['posisi'] ?? null,
                        'tgl_pasang'          => $tglPasang->toDateString(),
                        'kilometer_pasang'    => $partData['kilometer_pasang'] ?? $request->kilometer,
                        'kondisi'             => $partData['kondisi'] ?? 'Baik',
                        'status'              => in_array($partData['status'] ?? '', ['Terpasang','Proses','Diganti']) ? $partData['status'] : 'Proses',
                        'interval_nilai'      => (int)$partData['interval_nilai'],
                        'interval_satuan'     => $partData['interval_satuan'],
                        'tanggal_limit'       => $tanggalLimit->toDateString(),
                        'biaya'               => (int)($partData['biaya'] ?? 0),
                        'status_pengeluaran'  => $partStatuses[$idx] ?? 'stabil',
                        'bukti'               => !empty($buktiFiles) ? json_encode($buktiFiles) : null,
                        'keterangan'          => $partData['keterangan'] ?? null,
                        'nama_rekening'       => $partData['nama_rekening'] ?? null,
                        'nama_bank'           => $partData['nama_bank'] ?? null,
                        'no_rekening'         => $partData['no_rekening'] ?? null,
                    ]);
                }

                // Update attachments jika ada
                if (!empty($attachmentsMeta)) {
                    // Hapus attachments lama
                    foreach ($service->attachments as $oldAtt) {
                        if (file_exists(public_path($oldAtt->file_path))) {
                            unlink(public_path($oldAtt->file_path));
                        }
                        $oldAtt->delete();
                    }

                    if (!file_exists($attachmentsMeta[0]['destination'])) mkdir($attachmentsMeta[0]['destination'], 0777, true);
                    foreach ($attachmentsMeta as $att) {
                        $att['file']->move($att['destination'], $att['filename']);
                        $movedFiles[] = public_path($att['file_path']);
                        Attachment::create([
                            'relation_type' => 'service',
                            'relation_id'   => $service->id,
                            'file_name'     => $att['file_name'],
                            'file_path'     => $att['file_path'],
                            'file_type'     => $att['file_type'],
                            'file_size'     => $att['file_size'] ?? 0,
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            foreach ($movedFiles as $f) { if (file_exists($f)) unlink($f); }
            throw $e;
        }

        return redirect()->route('service-history.index')
            ->with('success', 'Request berhasil diupdate.');
    }

    /**
     * Tambah kategori baru secara inline dari form
     */
    public function storeCategory(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100|unique:service_categories,nama']);
        $cat = ServiceCategory::create(['nama' => $request->nama]);
        return response()->json(['id' => $cat->id, 'nama' => $cat->nama]);
    }

    /**
     * Ambil data kendaraan untuk auto-fill KM
     */
    public function getKendaraanData($id)
    {
        $k = Kendaraan::with('jenis')->findOrFail($id);
        return response()->json([
            'kilometer_sekarang' => $k->kilometer_sekarang ?? 0,
            'merk'               => $k->merk,
            'nopol'              => $k->nopol,
            'jenis'              => $k->jenis?->nama ?? '-',
        ]);
    }

    public function store(Request $request, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'kendaraan_id'                 => 'required|exists:kendaraan,id',
            'tanggal_service'              => 'required|date',
            'kilometer'                    => 'required|integer|min:0',
            'status'                       => 'nullable|in:proses,selesai',
            'keluhan'                      => 'nullable|string',
            'alasan_permintaan'            => 'nullable|string',
            'keterangan_pengadaan'         => 'nullable|string|max:500',
            'total_biaya_override'         => 'nullable|numeric|min:0',
            'bukti_pembayaran'             => 'nullable|file|max:5120',
            'bukti_attachment'             => 'nullable|array',
            'bukti_attachment.*'           => 'file|max:5120',
            'supplier_id'                  => 'nullable|exists:supplier,id',
            // Parts
            'parts'                        => 'nullable|array',
            'parts.*.nama_part'            => 'required_with:parts|string|max:255',
            'parts.*.category_id'          => 'nullable',
            'parts.*.nama_category_baru'   => 'nullable|string|max:100',
            'parts.*.part_number'          => 'nullable|string|max:100',
            'parts.*.serial_number'        => 'nullable|string|max:100',
            'parts.*.posisi'               => 'nullable|string|max:100',
            'parts.*.tgl_pasang'           => 'required_with:parts|date',
            'parts.*.kilometer_pasang'     => 'nullable|integer|min:0',
            'parts.*.kondisi'              => 'nullable|in:Baik,Rusak,Perlu Ganti',
            'parts.*.status'               => 'nullable|in:Terpasang,Proses',
            'parts.*.interval_nilai'       => 'required_with:parts|integer|min:1',
            'parts.*.interval_satuan'      => 'required_with:parts|in:hari,minggu,bulan,tahun',
            'parts.*.biaya'                => 'nullable|numeric|min:0',
            'parts.*.bukti'                => 'required_with:parts|array|min:1',
            'parts.*.bukti.*'              => 'file|mimes:jpg,jpeg,png,mp4,mov',
            'parts.*.keterangan'           => 'nullable|string|max:1000',
            'parts.*.nama_rekening'        => 'nullable|string|max:150',
            'parts.*.nama_bank'            => 'nullable|string|max:100',
            'parts.*.no_rekening'          => 'nullable|string|max:50',
        ]);

        // ===========================================================================
        // APPROVAL WORKFLOW: Intercept dan kirim ke Pembayaran
        // Skip intercept jika:
        // 1. Dari reminder (replacement part yang sudah approved)
        // 2. Dari edit existing service_history_id (update data existing)
        // ===========================================================================
        
        if (!$request->filled('from_reminder') && !$request->filled('service_history_id')) {
            try {
                // Check if this is a resubmit (from rejected pembayaran)
                if ($request->filled('edit_pembayaran')) {
                    $pembayaranId = $request->input('edit_pembayaran');
                    
                    // Resubmit: Update existing pembayaran
                    $pembayaran = $interceptor->resubmitToPembayaran($pembayaranId, $request, 'service_part');
                    
                    return redirect()
                        ->route('pembayaran.index', ['tab' => 'Pending'])
                        ->with('success', 'Pengajuan service part berhasil diajukan ulang. Menunggu approval dari Superadmin.');
                }
                
                // Step 1: Intercept data dari form
                $interceptedData = $interceptor->intercept($request, 'service_part');
                
                // Step 2: Save ke Pembayaran
                $pembayaran = $interceptor->saveToPembayaran($interceptedData, 'service_part');
                
                // Step 3: Upload temporary files
                $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $pembayaran->id);
                
                // Step 4: Update source_data dengan file info
                $sourceData = $pembayaran->source_data;
                $sourceData['temp_files'] = $uploadedFiles;
                $pembayaran->update(['source_data' => $sourceData]);
                
                return redirect()
                    ->route('pembayaran.index', ['tab' => 'Pending'])
                    ->with('success', 'Pengajuan pengeluaran service part berhasil dikirim. Menunggu approval dari Superadmin.');
                    
            } catch (\Exception $e) {
                \Log::error('Error intercepting service part submission: ' . $e->getMessage());
                
                return back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan saat mengajukan pengeluaran. Silakan coba lagi.');
            }
        }

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        $serviceAktif = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->where('status', 'proses')->exists();
        if ($serviceAktif) {
            return back()->withErrors(['kendaraan_id' => 'Kendaraan masih punya service proses aktif.'])->withInput();
        }

        // Resolusi kategori per part (buat baru jika inline)
        $resolvedParts = $this->resolvePartsCategory($request->parts ?? []);

        // Tentukan status_pengeluaran per-part berdasarkan limit_price kategori
        $partStatuses = $this->resolvePartStatusPengeluaran($resolvedParts, $request->kendaraan_id);

        // Check duplicate parts - only if not from service_history_id and not from reminder
        // Skip jika dari reminder karena konteksnya penggantian part (bukan tambah baru)
        if (!$request->filled('from_reminder')) {
            $duplicateCheck = $this->checkDuplicateParts($resolvedParts, $request->kendaraan_id);
            $validParts     = collect($duplicateCheck['valid'])->pluck('data')->toArray();
            $duplicateParts = $duplicateCheck['duplicate'];

            // If all parts are duplicate and no service_history_id, reject completely
            if (empty($validParts) && !$request->filled('service_history_id')) {
                $duplicateList = collect($duplicateParts)->pluck('label')->join(', ');
                return back()->with('error', "Semua part sudah ada: {$duplicateList}. Gunakan Request Part untuk menambahkan part yang sudah terpasang.")
                    ->withInput();
            }

            // Use only valid parts for processing
            $resolvedParts = $validParts;
        }

        // Total biaya: auto-sum dari parts, atau override jika diisi
        $sumBiayaParts = collect($resolvedParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));
        $totalBiaya    = filled($request->total_biaya_override) && (int)$request->total_biaya_override > 0
            ? (int)$request->total_biaya_override
            : $sumBiayaParts;

        // Siapkan metadata file
        $buktiBayarMeta  = $this->prepBuktiBayar($request);
        $attachmentsMeta = $this->prepAttachments($request);
        $movedFiles       = [];
        $purchaseroResult = ['created' => false, 'no_pr' => null, 'skipped_count' => 0];
        $duplicateParts   = [];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $request, $kendaraan, $totalBiaya, $partStatuses, $buktiBayarMeta, $attachmentsMeta,
                $resolvedParts, &$movedFiles, &$purchaseroResult
            ) {
                $buktiBayar = null;
                if ($buktiBayarMeta) {
                    if (!file_exists($buktiBayarMeta['destination'])) mkdir($buktiBayarMeta['destination'], 0777, true);
                    $buktiBayarMeta['file']->move($buktiBayarMeta['destination'], $buktiBayarMeta['filename']);
                    $buktiBayar   = $buktiBayarMeta['path'];
                    $movedFiles[] = public_path($buktiBayar);
                }

                // Check apakah update existing service history atau create baru
                if ($request->filled('service_history_id')) {
                    // UPDATE existing service history (dari edit request)
                    $service = ServiceHistory::findOrFail($request->service_history_id);
                    $service->update([
                        'kendaraan_id'    => $request->kendaraan_id,
                        'keluhan'         => $request->keluhan,
                        'kilometer'       => $request->kilometer,
                        'total_biaya'     => $totalBiaya,
                        'status'          => $this->deriveServiceStatus($resolvedParts),
                        'tanggal_service' => $request->tanggal_service,
                        'bukti_pembayaran' => $buktiBayar ?: $service->bukti_pembayaran,
                    ]);
                } else {
                    // Cek apakah kendaraan sudah punya service history (1 kendaraan = 1 service history)
                    $existing = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
                        ->latest()
                        ->first();

                    if ($existing) {
                        // MERGE ke service history yang sudah ada — update header dengan nilai terbaru
                        $existing->update([
                            'keluhan'         => $request->keluhan,
                            'kilometer'       => $request->kilometer,
                            'total_biaya'     => $existing->total_biaya + $totalBiaya,
                            'status'          => $this->deriveServiceStatus($resolvedParts),
                            'tanggal_service' => $request->tanggal_service,
                            'bukti_pembayaran' => $buktiBayar ?: $existing->bukti_pembayaran,
                        ]);
                        $service = $existing;
                    } else {
                        // CREATE service history baru (kendaraan belum punya)
                        $service = ServiceHistory::create([
                            'kendaraan_id'    => $request->kendaraan_id,
                            'keluhan'         => $request->keluhan,
                            'kilometer'       => $request->kilometer,
                            'total_biaya'     => $totalBiaya,
                            'status'          => $this->deriveServiceStatus($resolvedParts),
                            'tanggal_service' => $request->tanggal_service,
                            'bukti_pembayaran' => $buktiBayar,
                            'status_approval' => 'approved',
                        ]);
                    }
                }

                // Simpan parts
                $tglTerakhirPasang = null;
                $oldPartId = null;
                
                // Cek apakah service ini dari reminder (untuk replacement tracking)
                if ($request->filled('from_reminder')) {
                    $reminder = ReminderService::find($request->from_reminder);
                    if ($reminder && $reminder->service_part_id) {
                        $oldPartId = $reminder->service_part_id;
                    }
                }
                
                foreach ($resolvedParts as $idx => $partData) {
                    $tglPasang     = Carbon::parse($partData['tgl_pasang']);
                    $tanggalLimit  = $this->hitungTanggalLimitPart(
                        $tglPasang,
                        (int)$partData['interval_nilai'],
                        $partData['interval_satuan']
                    );

                    // Upload bukti files untuk part ini
                    $buktiFiles = $this->uploadPartBuktiFiles($request, $idx);
                    foreach ($buktiFiles as $bf) {
                        $movedFiles[] = public_path($bf['path']);
                    }

                    $part = ServicePart::create([
                        'service_history_id'  => $service->id,
                        'kendaraan_id'        => $request->kendaraan_id,
                        'category_id'         => $partData['category_id'] ?? null,
                        'nama_part'           => $partData['nama_part'],
                        'part_number'         => $partData['part_number'] ?? null,
                        'serial_number'       => $partData['serial_number'] ?? null,
                        'posisi'              => $partData['posisi'] ?? null,
                        'tgl_pasang'          => $tglPasang->toDateString(),
                        'kilometer_pasang'    => $partData['kilometer_pasang'] ?? $request->kilometer,
                        'kondisi'             => $partData['kondisi'] ?? 'Baik',
                        'status'              => in_array($partData['status'] ?? '', ['Terpasang','Proses','Diganti']) ? $partData['status'] : 'Proses',
                        'interval_nilai'      => (int)$partData['interval_nilai'],
                        'interval_satuan'     => $partData['interval_satuan'],
                        'tanggal_limit'       => $tanggalLimit->toDateString(),
                        'biaya'               => (int)($partData['biaya'] ?? 0),
                        'status_pengeluaran'  => $partStatuses[$idx] ?? 'stabil',
                        'bukti'               => !empty($buktiFiles) ? json_encode($buktiFiles) : null,
                        'keterangan'          => $partData['keterangan'] ?? null,
                        'nama_rekening'       => $partData['nama_rekening'] ?? null,
                        'nama_bank'           => $partData['nama_bank'] ?? null,
                        'no_rekening'         => $partData['no_rekening'] ?? null,
                    ]);

                    // Jika ini part pertama dari reminder (replacement), archive part lama
                    if ($oldPartId && $idx === 0) {
                        $oldPart = ServicePart::find($oldPartId);
                        if ($oldPart) {
                            $oldPart->update([
                                'status'             => 'Diganti',
                                'replaced_at'        => now(),
                                'replaced_by_part_id' => $part->id,
                            ]);
                        }
                    }

                    // Auto-close reminder aktif untuk part yang sama (kendaraan + posisi + nama)
                    $this->autoCloseReminderPart($kendaraan->id, $part, $oldPartId);

                    // Track tanggal pasang terbaru untuk update kendaraan
                    if (!$tglTerakhirPasang || $tglPasang->gt($tglTerakhirPasang)) {
                        $tglTerakhirPasang = $tglPasang;
                    }
                }

                // Recalculate total biaya - exclude parts dengan status "Diganti"
                $totalBiayaFinal = $service->parts()
                    ->whereIn('status', ['Terpasang', 'Limit'])
                    ->sum('biaya');
                $service->update(['total_biaya' => $totalBiayaFinal]);

                // Attachments
                if (!empty($attachmentsMeta)) {
                    if (!file_exists($attachmentsMeta[0]['destination'])) mkdir($attachmentsMeta[0]['destination'], 0777, true);
                    foreach ($attachmentsMeta as $att) {
                        $att['file']->move($att['destination'], $att['filename']);
                        $movedFiles[] = public_path($att['file_path']);
                        Attachment::create([
                            'relation_type' => 'service',
                            'relation_id'   => $service->id,
                            'file_name'     => $att['file_name'],
                            'file_path'     => $att['file_path'],
                            'file_type'     => $att['file_type'],
                            'file_size'     => $att['file_size'],
                        ]);
                    }
                }

                // Update kendaraan
                $updateKendaraan = [
                    'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
                ];
                if ($request->status === 'selesai') {
                    $updateKendaraan['km_terakhir_service']      = $request->kilometer;
                    $updateKendaraan['kilometer_sekarang']       = $request->kilometer;
                }
                // Update tanggal terakhir service dari tgl_pasang part terbaru
                if ($tglTerakhirPasang) {
                    $updateKendaraan['tanggal_terakhir_service'] = $tglTerakhirPasang->toDateString();
                }
                $kendaraan->update($updateKendaraan);

                // Jurnal keuangan
                $this->catatKeuangan($service, $kendaraan, $totalBiayaFinal, $request->tanggal_service, false);

                // Auto-submit ke Pengadaan (jika ada parts)
                if (!empty($resolvedParts)) {
                    $purchaseroResult = $this->createPurchaseroFromService(
                        $service,
                        $resolvedParts,
                        'Diajukan',
                        $request->supplier_id ? (int)$request->supplier_id : null,
                        $request->alasan_permintaan ?: null,
                        $request->keterangan_pengadaan ?: null
                    );
                }
            });
        } catch (\Throwable $e) {
            foreach ($movedFiles as $f) { if (file_exists($f)) unlink($f); }
            throw $e;
        }

        // Build success message - include duplicate warning if any
        $successMsg = 'Data service berhasil ditambahkan.';
        if ($purchaseroResult['created'] && $purchaseroResult['no_pr']) {
            $successMsg .= " Otomatis diajukan ke pengadaan (No PR: {$purchaseroResult['no_pr']}).";
        }
        if (!empty($duplicateParts)) {
            $duplicateList = collect($duplicateParts)->pluck('label')->join(', ');
            $count = count($duplicateParts);
            $successMsg .= " {$count} part tidak ditambahkan karena sudah ada: {$duplicateList}. Gunakan Request Part untuk part tersebut.";
        }
        if (($purchaseroResult['skipped_count'] ?? 0) > 0) {
            $skipped = $purchaseroResult['skipped_count'];
            $successMsg .= " {$skipped} part tidak diajukan ulang ke pengadaan karena sudah ada yang Pending/Diajukan.";
        }

        return redirect()->route('service-history.index')
            ->with('success', $successMsg);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id'                 => 'required|exists:kendaraan,id',
            'tanggal_service'              => 'required|date',
            'kilometer'                    => 'required|integer|min:0',
            'status'                       => 'nullable|in:proses,selesai',
            'keluhan'                      => 'nullable|string',
            'total_biaya_override'         => 'nullable|numeric|min:0',
            'bukti_pembayaran'             => 'nullable|file|max:5120',
            'bukti_attachment'             => 'nullable|array',
            'bukti_attachment.*'           => 'file|max:5120',
            // Parts
            'parts'                        => 'nullable|array',
            'parts.*.nama_part'            => 'required_with:parts|string|max:255',
            'parts.*.category_id'          => 'nullable',
            'parts.*.nama_category_baru'   => 'nullable|string|max:100',
            'parts.*.part_number'          => 'nullable|string|max:100',
            'parts.*.serial_number'        => 'nullable|string|max:100',
            'parts.*.posisi'               => 'nullable|string|max:100',
            'parts.*.tgl_pasang'           => 'required_with:parts|date',
            'parts.*.kilometer_pasang'     => 'nullable|integer|min:0',
            'parts.*.kondisi'              => 'nullable|in:Baik,Rusak,Perlu Ganti',
            'parts.*.status'               => 'nullable|in:Terpasang,Proses',
            'parts.*.interval_nilai'       => 'required_with:parts|integer|min:1',
            'parts.*.interval_satuan'      => 'required_with:parts|in:hari,minggu,bulan,tahun',
            'parts.*.biaya'                => 'nullable|numeric|min:0',
            'parts.*.bukti'                => 'nullable|array',
            'parts.*.bukti.*'              => 'file|mimes:jpg,jpeg,png,mp4,mov',
            'parts.*.keterangan'           => 'nullable|string|max:1000',
            'parts.*.nama_rekening'        => 'nullable|string|max:150',
            'parts.*.nama_bank'            => 'nullable|string|max:100',
            'parts.*.no_rekening'          => 'nullable|string|max:50',
        ]);

        $service   = ServiceHistory::findOrFail($id);
        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        $resolvedParts = $this->resolvePartsCategory($request->parts ?? []);

        // Tentukan status_pengeluaran per-part berdasarkan limit_price kategori
        $partStatuses  = $this->resolvePartStatusPengeluaran($resolvedParts, $request->kendaraan_id);

        $sumBiayaParts = collect($resolvedParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));
        $totalBiaya    = filled($request->total_biaya_override) && (int)$request->total_biaya_override > 0
            ? (int)$request->total_biaya_override
            : $sumBiayaParts;

        $buktiBayarMeta  = $this->prepBuktiBayar($request);
        $attachmentsMeta = $this->prepAttachments($request);
        $movedFiles      = [];
        $deletedFiles    = [];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $request, $id, $service, $kendaraan, $totalBiaya, $partStatuses, $buktiBayarMeta, $attachmentsMeta,
                $resolvedParts, &$movedFiles, &$deletedFiles
            ) {
                $buktiBayar = $service->bukti_pembayaran;
                if ($buktiBayarMeta) {
                    $oldPath = public_path($buktiBayarMeta['old_path'] ?? '');
                    if (!empty($buktiBayarMeta['old_path']) && file_exists($oldPath)) {
                        $deletedFiles[$oldPath] = file_get_contents($oldPath);
                        unlink($oldPath);
                    }
                    if (!file_exists($buktiBayarMeta['destination'])) mkdir($buktiBayarMeta['destination'], 0777, true);
                    $buktiBayarMeta['file']->move($buktiBayarMeta['destination'], $buktiBayarMeta['filename']);
                    $buktiBayar   = $buktiBayarMeta['path'];
                    $movedFiles[] = public_path($buktiBayar);
                }

                $service->update([
                    'kendaraan_id'    => $request->kendaraan_id,
                    'keluhan'         => $request->keluhan,
                    'kilometer'       => $request->kilometer,
                    'total_biaya'     => $totalBiaya,
                    'status'          => $this->deriveServiceStatus($resolvedParts),
                    'tanggal_service' => $request->tanggal_service,
                    'bukti_pembayaran' => $buktiBayar,
                ]);

                // Replace semua parts lama → hapus, buat ulang
                $service->parts()->delete();

                $tglTerakhirPasang = null;
                foreach ($resolvedParts as $idx => $partData) {
                    $tglPasang    = Carbon::parse($partData['tgl_pasang']);
                    $tanggalLimit = $this->hitungTanggalLimitPart(
                        $tglPasang, (int)$partData['interval_nilai'], $partData['interval_satuan']
                    );

                    // Upload bukti files untuk part ini
                    $buktiFiles = $this->uploadPartBuktiFiles($request, $idx);
                    foreach ($buktiFiles as $bf) {
                        $movedFiles[] = public_path($bf['path']);
                    }

                    $part = ServicePart::create([
                        'service_history_id'  => $service->id,
                        'kendaraan_id'        => $request->kendaraan_id,
                        'category_id'         => $partData['category_id'] ?? null,
                        'nama_part'           => $partData['nama_part'],
                        'part_number'         => $partData['part_number'] ?? null,
                        'serial_number'       => $partData['serial_number'] ?? null,
                        'posisi'              => $partData['posisi'] ?? null,
                        'tgl_pasang'          => $tglPasang->toDateString(),
                        'kilometer_pasang'    => $partData['kilometer_pasang'] ?? $request->kilometer,
                        'kondisi'             => $partData['kondisi'] ?? 'Baik',
                        'status'              => in_array($partData['status'] ?? '', ['Terpasang','Proses','Diganti']) ? $partData['status'] : 'Proses',
                        'interval_nilai'      => (int)$partData['interval_nilai'],
                        'interval_satuan'     => $partData['interval_satuan'],
                        'tanggal_limit'       => $tanggalLimit->toDateString(),
                        'biaya'               => (int)($partData['biaya'] ?? 0),
                        'status_pengeluaran'  => $partStatuses[$idx] ?? 'stabil',
                        'bukti'               => !empty($buktiFiles) ? json_encode($buktiFiles) : null,
                        'keterangan'          => $partData['keterangan'] ?? null,
                        'nama_rekening'       => $partData['nama_rekening'] ?? null,
                        'nama_bank'           => $partData['nama_bank'] ?? null,
                        'no_rekening'         => $partData['no_rekening'] ?? null,
                    ]);

                    $this->autoCloseReminderPart($kendaraan->id, $part);

                    if (!$tglTerakhirPasang || $tglPasang->gt($tglTerakhirPasang)) {
                        $tglTerakhirPasang = $tglPasang;
                    }
                }

                // Attachments baru
                if (!empty($attachmentsMeta)) {
                    if (!file_exists($attachmentsMeta[0]['destination'])) mkdir($attachmentsMeta[0]['destination'], 0777, true);
                    foreach ($attachmentsMeta as $att) {
                        $att['file']->move($att['destination'], $att['filename']);
                        $movedFiles[] = public_path($att['file_path']);
                        Attachment::create([
                            'relation_type' => 'service',
                            'relation_id'   => $service->id,
                            'file_name'     => $att['file_name'],
                            'file_path'     => $att['file_path'],
                            'file_type'     => $att['file_type'],
                            'file_size'     => $att['file_size'],
                        ]);
                    }
                }

                $updateKendaraan = [
                    'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
                ];
                if ($request->status === 'selesai') {
                    $updateKendaraan['km_terakhir_service'] = $request->kilometer;
                    $updateKendaraan['kilometer_sekarang']  = $request->kilometer;
                }
                if ($tglTerakhirPasang) {
                    $updateKendaraan['tanggal_terakhir_service'] = $tglTerakhirPasang->toDateString();
                }
                $kendaraan->update($updateKendaraan);

                $this->catatKeuangan($service, $kendaraan, $totalBiaya, $request->tanggal_service, true);
            });
        } catch (\Throwable $e) {
            foreach ($movedFiles as $f) { if (file_exists($f)) unlink($f); }
            foreach ($deletedFiles as $p => $c) { file_put_contents($p, $c); }
            throw $e;
        }

        return redirect()->route('service-history.index')
            ->with('success', 'Data service berhasil diupdate.');
    }

    public function updateStatus(Request $request, $id)
    {
        // 'limit' diizinkan agar cron bisa set via direct update,
        // tapi tidak ditampilkan sebagai opsi di modal admin
        $request->validate(['status' => 'required|in:proses,selesai,limit']);

        $service   = ServiceHistory::findOrFail($id);
        $kendaraan = $service->kendaraan;

        $service->update(['status' => $request->status]);

        $tanggalSelesai  = Carbon::parse($service->tanggal_service)->toDateString();
        $updateKendaraan = [
            'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
        ];
        if ($request->status === 'selesai' && $service->kilometer > 0) {
            $updateKendaraan['km_terakhir_service'] = $service->kilometer;
            $updateKendaraan['kilometer_sekarang']  = $service->kilometer;
            $updateKendaraan['tanggal_terakhir_service'] = $tanggalSelesai;
        }

        $kendaraan->update($updateKendaraan);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $service   = ServiceHistory::findOrFail($id);
        $kendaraan = $service->kendaraan;

        foreach ($service->attachments as $att) {
            if (file_exists(public_path($att->file_path))) unlink(public_path($att->file_path));
            $att->delete();
        }

        // Hapus bukti files dari semua parts sebelum cascade delete
        foreach ($service->parts as $part) {
            if ($part->bukti) {
                foreach ($part->bukti as $file) {
                    $filePath = public_path($file['path'] ?? '');
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }

        // Parts akan terhapus cascade (FK cascadeOnDelete)
        $service->delete();

        if ($kendaraan) {
            $masihProses = ServiceHistory::where('kendaraan_id', $kendaraan->id)
                ->where('status', 'proses')->exists();
            if (!$masihProses) $kendaraan->update(['status_kendaraan' => 'tersedia']);
        }

        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function destroyAttachment($id)
    {
        $attachment = Attachment::where('relation_type', 'service')->findOrFail($id);
        if (file_exists(public_path($attachment->file_path))) unlink(public_path($attachment->file_path));
        $attachment->delete();
        return back()->with('success', 'Lampiran berhasil dihapus');
    }

    /**
     * Approve a request
     */
    public function approve(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:proses,selesai']);

        $service = ServiceHistory::findOrFail($id);

        if ($service->status_approval !== 'pending') {
            return back()->with('error', 'Request sudah diproses.');
        }

        $service->update([
            'status_approval' => 'approved',
            'approval_by'     => auth()->id(),
            'approval_at'     => now(),
            'status'          => $request->status,
        ]);

        // Update kendaraan status
        $kendaraan = $service->kendaraan;
        if ($kendaraan) {
            $updateKendaraan = [
                'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
            ];
            if ($request->status === 'selesai' && $service->kilometer > 0) {
                $updateKendaraan['km_terakhir_service'] = $service->kilometer;
                $updateKendaraan['kilometer_sekarang']  = $service->kilometer;
            }
            $kendaraan->update($updateKendaraan);
        }

        return back()->with('success', 'Request berhasil disetujui.');
    }

    /**
     * Reject a request
     */
    public function reject($id)
    {
        $service = ServiceHistory::findOrFail($id);

        if ($service->status_approval !== 'pending') {
            return back()->with('error', 'Request sudah diproses.');
        }

        $service->update([
            'status_approval' => 'rejected',
            'approval_by'     => auth()->id(),
            'approval_at'     => now(),
        ]);

        return back()->with('success', 'Request telah ditolak.');
    }

    /**
     * Approve satu part (per-part approval, superadmin only)
     */
    public function approvePart(Request $request, $id)
    {
        $part = ServicePart::findOrFail($id);

        if (!$part->is_request) {
            return back()->with('error', 'Part ini bukan request part.');
        }

        if ($part->status_approval !== 'pending') {
            return back()->with('error', 'Part ini sudah diproses sebelumnya.');
        }

        $part->update([
            'status_approval' => 'approved',
            'approval_by'     => auth()->id(),
            'approval_at'     => now(),
        ]);

        return back()->with('success', 'Part "' . $part->nama_part . '" berhasil disetujui.');
    }

    /**
     * Reject satu part (per-part approval, superadmin only)
     */
    public function rejectPart($id)
    {
        $part = ServicePart::findOrFail($id);

        if (!$part->is_request) {
            return back()->with('error', 'Part ini bukan request part.');
        }

        if ($part->status_approval !== 'pending') {
            return back()->with('error', 'Part ini sudah diproses sebelumnya.');
        }

        $part->update([
            'status_approval' => 'rejected',
            'approval_by'     => auth()->id(),
            'approval_at'     => now(),
        ]);

        return back()->with('success', 'Part "' . $part->nama_part . '" telah ditolak.');
    }

    /**
     * Hapus satu file bukti dari service part
     */
    public function deletePartBukti(Request $request, $id)
    {
        $request->validate([
            'file_path' => 'required|string',
        ]);

        $part = ServicePart::findOrFail($id);
        $path = $request->file_path;

        $buktiList = $part->bukti ?? [];

        // Filter array bukti untuk remove file dengan path tersebut
        $buktiList = array_values(array_filter($buktiList, function ($f) use ($path) {
            return ($f['path'] ?? '') !== $path;
        }));

        // Hapus file fisik
        $fullPath = public_path($path);
        if ($path && file_exists($fullPath)) {
            unlink($fullPath);
        }

        $part->update([
            'bukti' => !empty($buktiList) ? $buktiList : null,
        ]);

        return response()->json(['success' => true, 'message' => 'File bukti berhasil dihapus']);
    }

    public function pdf(Request $request)
    {
        $categoryId = $request->category_id;
        $bulan      = $request->bulan;

        $data = ServiceHistory::with(['kendaraan.jenis', 'attachments', 'parts.category'])
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan]))
            ->when($categoryId, fn($q) => $q->whereHas('parts', fn($p) => $p->where('category_id', $categoryId)))
            ->latest()->get();

        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.service.pdf_history', compact('data', 'categoryId', 'bulan', 'setting', 'logoSrc'));
        return $pdf->stream('service-history.pdf');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Resolve category per part: buat category baru jika nama_category_baru diisi

    /**
     * Update status per-part (tidak_aktif → Terpasang, atau Proses ↔ Terpasang),
     * lalu recalculate header ServiceHistory.
     */
    public function updatePartStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Terpasang,Proses',
        ]);

        $part = ServicePart::findOrFail($id);

        // Terpasang tidak bisa diubah lagi
        if ($part->status === 'Terpasang') {
            return back()->with('error', 'Part yang sudah Terpasang tidak bisa diubah statusnya.');
        }

        // Jika status saat ini tidak_aktif, hanya boleh ke Terpasang (bukan Proses)
        if ($part->status === 'tidak_aktif' && $request->status !== 'Terpasang') {
            return back()->with('error', 'Part dengan status Tidak Aktif hanya bisa ditandai sebagai Terpasang.');
        }

        // Jika part ini sudah diapprove keuangan (persetujuan=Disetujui), boleh update
        // Jika belum diapprove (Pending/Ditolak), tidak boleh diubah
        if ($part->persetujuan !== 'Disetujui') {
            return back()->with('error', 'Status part tidak bisa diubah sebelum disetujui oleh keuangan.');
        }

        $updateData = ['status' => $request->status];

        // Saat ditandai Terpasang dari tidak_aktif, set kondisi ke Baik
        if ($request->status === 'Terpasang' && $part->status === 'tidak_aktif') {
            $updateData['kondisi'] = 'Baik';
        }

        $part->update($updateData);

        // Recalculate header status dari semua parts di service history ini
        $sh = ServiceHistory::with('parts')->find($part->service_history_id);
        if ($sh) {
            $parts = $sh->parts;
            // Tidak hitung part dengan status tidak_aktif sebagai "proses"
            $adaProses   = $parts->contains(fn($p) => $p->status === 'Proses');
            $adaTidakAktif = $parts->contains(fn($p) => $p->status === 'tidak_aktif');
            $adaLimit    = $parts->contains(fn($p) => $p->status === 'Limit');

            if ($adaLimit && !$adaProses) {
                $newStatus = 'limit';
            } elseif ($adaProses || $adaTidakAktif) {
                $newStatus = 'proses';
            } else {
                $newStatus = 'selesai';
            }
            $sh->update(['status' => $newStatus]);
        }

        return back()->with('success', 'Status part berhasil diperbarui.');
    }

    /**
     * Turunkan status ServiceHistory dari komposisi status part-partnya.
     * Prioritas: ada Proses → 'proses', tidak ada Proses → 'selesai'.
     * (Status 'limit' dihandle terpisah oleh cron CheckServicePartLimit)
     */
    private function deriveServiceStatus(array $parts): string
    {
        foreach ($parts as $part) {
            if (($part['status'] ?? '') === 'Proses') {
                return 'proses';
            }
        }
        return 'selesai';
    }


    private function resolvePartsCategory(array $parts): array
    {
        return array_map(function ($part) {
            if (!empty($part['nama_category_baru'])) {
                $cat = ServiceCategory::firstOrCreate(['nama' => trim($part['nama_category_baru'])]);
                $part['category_id'] = $cat->id;
            }
            return $part;
        }, $parts);
    }

    /**
     * Hitung tanggal limit part dari tgl_pasang + interval
     */

    /**
     * Tentukan status_pengeluaran per part berdasarkan limit_price kategori kendaraan.
     * Return array berindeks sama dengan $parts:
     *   ['stabil'|'overservice', ...]
     * Tidak pernah menolak — hanya memberi label.
     */
    private function resolvePartStatusPengeluaran(array $parts, int $kendaraanId): array
    {
        // Pre-load semua limit rules untuk kendaraan ini agar tidak N+1
        $rules = ServiceCategoryLimit::where('kendaraan_id', $kendaraanId)
            ->whereNotNull('limit_price')
            ->get()
            ->keyBy('category_id');

        $statuses = [];
        foreach ($parts as $part) {
            $categoryId = $part['category_id'] ?? null;
            $biaya      = (int)($part['biaya'] ?? 0);

            if ($categoryId && $biaya > 0 && isset($rules[$categoryId])) {
                $statuses[] = $biaya > $rules[$categoryId]->limit_price
                    ? 'overservice'
                    : 'stabil';
            } else {
                $statuses[] = 'stabil';
            }
        }

        return $statuses;
    }


    private function hitungTanggalLimitPart(Carbon $tglPasang, int $nilai, string $satuan): Carbon
    {
        return match ($satuan) {
            'hari'   => (clone $tglPasang)->addDays($nilai),
            'minggu' => (clone $tglPasang)->addWeeks($nilai),
            'tahun'  => (clone $tglPasang)->addYears($nilai),
            default  => (clone $tglPasang)->addMonths($nilai),
        };
    }

    /**
     * Auto-close reminder aktif untuk part yang sama (kendaraan + posisi + nama_part)
    /**
     * Auto-close reminder aktif untuk part yang sama (kendaraan + posisi + nama_part)
     * Jika $oldPartId diberikan, close semua reminder untuk part lama tersebut.
     */
    private function autoCloseReminderPart(int $kendaraanId, ServicePart $newPart, ?int $oldPartId = null): void
    {
        // Jika ada oldPartId (dari reminder replacement), close semua reminder untuk part lama
        if ($oldPartId) {
            ReminderService::where('service_part_id', $oldPartId)
                ->whereIn('status', ['aktif', 'jatuh_tempo'])
                ->update(['status' => 'selesai']);
        }
        
        // Juga close reminder yang match berdasarkan karakteristik (untuk backward compatibility)
        ReminderService::where('kendaraan_id', $kendaraanId)
            ->whereIn('status', ['aktif', 'jatuh_tempo'])
            ->whereHas('servicePart', fn($q) =>
                $q->where('nama_part', $newPart->nama_part)
                  ->where('posisi', $newPart->posisi)
            )
            ->update(['status' => 'selesai']);
    }

    /**
     * Kalkulasi sisa limit, biaya tahunan, dan status pengeluaran
     */
    private function hitungLimitStatus(Kendaraan $kendaraan, string $tanggal, int $totalBiaya, ?int $excludeId = null): array
    {
        $limitBulanan = $kendaraan->limit_biaya_bulanan_service ?? 0;
        $limitTahunan = $kendaraan->limit_biaya_tahunan_service ?? 0;
        $bulan        = date('Y-m', strtotime($tanggal));
        $tahun        = date('Y', strtotime($tanggal));

        $qBulan = ServiceHistory::where('kendaraan_id', $kendaraan->id)
            ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan]);
        $qTahun = ServiceHistory::where('kendaraan_id', $kendaraan->id)
            ->whereYear('tanggal_service', $tahun);

        if ($excludeId) {
            $qBulan->where('id', '!=', $excludeId);
            $qTahun->where('id', '!=', $excludeId);
        }

        $totalBulanIni = $qBulan->sum('total_biaya');
        $totalTahunIni = $qTahun->sum('total_biaya');
        $sisaLimit     = $limitBulanan - ($totalBulanIni + $totalBiaya);
        $biayaTahunan  = $totalTahunIni + $totalBiaya;
        $overBulanan   = $limitBulanan > 0 && ($totalBulanIni + $totalBiaya) > $limitBulanan;
        $overTahunan   = $limitTahunan > 0 && $biayaTahunan > $limitTahunan;

        return [$sisaLimit, $limitBulanan, $biayaTahunan, ($overBulanan || $overTahunan) ? 'overservice' : 'stabil'];
    }

    /**
     * Persiapkan metadata bukti pembayaran untuk di-move
     */
    private function prepBuktiBayar(Request $request, ?ServiceHistory $existing = null): ?array
    {
        if (!$request->hasFile('bukti_pembayaran')) return null;
        $file     = $request->file('bukti_pembayaran');
        $filename = time() . '_' . $file->getClientOriginalName();
        return [
            'file'        => $file,
            'filename'    => $filename,
            'destination' => public_path('bukti_pembayaran'),
            'path'        => 'bukti_pembayaran/' . $filename,
            'old_path'    => $existing?->bukti_pembayaran,
        ];
    }

    /**
     * Persiapkan metadata attachments untuk di-move
     */
    private function prepAttachments(Request $request): array
    {
        if (!$request->hasFile('bukti_attachment')) return [];
        $pathDir = public_path('service/attachments');
        $result  = [];
        foreach ($request->file('bukti_attachment') as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $result[] = [
                'file'        => $file,
                'filename'    => $filename,
                'destination' => $pathDir,
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => 'service/attachments/' . $filename,
                'file_type'   => $file->getClientOriginalExtension(),
                'file_size'   => $file->getSize(),
            ];
        }
        return $result;
    }

    /**
     * Catat / update jurnal keuangan dan buku besar
     */
    private function catatKeuangan(ServiceHistory $service, Kendaraan $kendaraan, int $totalBiaya, string $tanggal, bool $isUpdate): void
    {
        $kodeJurnal = 'SRV-' . $service->id;

        $lastSaldo = (float) (\Illuminate\Support\Facades\DB::table('keuangans')
            ->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0);

        $keuangan = Keuangan::where('reference', $kodeJurnal)->first();

        if ($isUpdate && $keuangan) {
            $selisih = $totalBiaya - $keuangan->pengeluaran;
            $keuangan->update([
                'tanggal'     => $tanggal,
                'pengeluaran' => $totalBiaya,
                'saldo'       => $lastSaldo - $selisih,
                'keterangan'  => 'Service Kendaraan',
            ]);
        } elseif (!$keuangan) {
            Keuangan::create([
                'tanggal'     => $tanggal,
                'reference'   => $kodeJurnal,
                'user_id'     => auth()->id(),
                'kategori'    => 'Pengeluaran',
                'metode'      => 'Cash',
                'keterangan'  => 'Service Kendaraan',
                'pemasukan'   => 0,
                'pengeluaran' => $totalBiaya,
                'saldo'       => $lastSaldo - $totalBiaya,
                'source_type' => 'service_history',
                'source_id'   => $service->id,
                'sumber'      => 'auto',
            ]);
        }

        $saldoBB = (float) (\Illuminate\Support\Facades\DB::table('bukubesars')
            ->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0);

        $bukubesar = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();

        if ($isUpdate && $bukubesar) {
            $selisihBB = $totalBiaya - $bukubesar->debit;
            $bukubesar->update([
                'tanggal'   => $tanggal,
                'debit'     => $totalBiaya,
                'saldo'     => $saldoBB - $selisihBB,
                'transaksi' => 'Beban Service - ' . ($kendaraan->merk ?? '-') . ' ' . ($kendaraan->nopol ?? '-'),
            ]);
        } elseif (!$bukubesar) {
            Bukubesar::create([
                'kode_jurnal' => $kodeJurnal,
                'transaksi'   => 'Beban Service - ' . ($kendaraan->merk ?? '-') . ' ' . ($kendaraan->nopol ?? '-'),
                'kategori'    => 'Beban',
                'tanggal'     => $tanggal,
                'debit'       => $totalBiaya,
                'kredit'      => 0,
                'saldo'       => $saldoBB - $totalBiaya,
                'aktivitas'   => 'Operasi',
                'keterangan'  => 'Auto-posting: Service kendaraan ' . ($kendaraan->nopol ?? '-'),
            ]);
        }
    }

    /**
     * Upload multiple file bukti untuk satu part.
     * Return array of objects: [{path, name, type, size}]
     */
    private function uploadPartBuktiFiles(Request $request, int $partIndex): array
    {
        $items = [];

        // Check apakah ada file untuk part ini
        $fileKey = "parts.{$partIndex}.bukti";
        if (!$request->hasFile($fileKey)) {
            return $items;
        }

        $destination = public_path('service-parts');
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $files = $request->file($fileKey);
        // Normalize to array jika single file
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if (!$file->isValid()) continue;

            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $filename     = time() . '_' . uniqid() . '.' . $extension;
            
            $file->move($destination, $filename);

            $items[] = [
                'path' => 'service-parts/' . $filename,
                'name' => $originalName,
                'type' => $extension,
            ];
        }

        return $items;
    }

    /**
     * Check duplicate parts - match by kendaraan_id, nama_part, category_id, posisi, status IN (Terpasang, Limit)
     * Return array: ['valid' => [], 'duplicate' => []]
     */
    private function checkDuplicateParts(array $parts, int $kendaraanId): array
    {
        $valid = [];
        $duplicate = [];

        foreach ($parts as $index => $partData) {
            // Normalize input for comparison (case-insensitive + trim)
            $namaPart = trim($partData['nama_part'] ?? '');
            $posisi = trim($partData['posisi'] ?? '');
            $categoryId = $partData['category_id'] ?? null;

            // Check if part already exists (case-insensitive)
            $exists = ServicePart::where('kendaraan_id', $kendaraanId)
                ->whereRaw('LOWER(TRIM(nama_part)) = ?', [strtolower($namaPart)])
                ->where('category_id', $categoryId)
                ->when($posisi, fn($q) => $q->whereRaw('LOWER(TRIM(posisi)) = ?', [strtolower($posisi)]))
                ->when(!$posisi, fn($q) => $q->whereNull('posisi'))
                ->whereIn('status', ['Terpasang', 'Limit'])
                ->exists();

            if ($exists) {
                $duplicate[] = [
                    'index' => $index,
                    'data' => $partData,
                    'label' => $namaPart . ($posisi ? ' - ' . $posisi : ''),
                ];
            } else {
                $valid[] = [
                    'index' => $index,
                    'data' => $partData,
                ];
            }
        }

        return ['valid' => $valid, 'duplicate' => $duplicate];
    }

    /**
     * Tandai semua part yang berstatus tidak_aktif+Disetujui di service history ini sebagai Terpasang.
     * Cashflow sudah dicatat saat approval keuangan, tidak dicatat lagi di sini.
     */
    public function terpasang(int $id)
    {
        $sh = ServiceHistory::with('parts')->findOrFail($id);

        $partsTidakAktif = $sh->parts
            ->where('persetujuan', 'Disetujui')
            ->where('status', 'tidak_aktif');

        if ($partsTidakAktif->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada part yang perlu ditandai Terpasang.');
        }

        foreach ($partsTidakAktif as $part) {
            $part->update([
                'status'  => 'Terpasang',
                'kondisi' => 'Baik',
            ]);
        }

        // Recalculate status service history
        $sh->refresh();
        $parts = $sh->parts;
        $adaProses     = $parts->contains(fn($p) => $p->status === 'Proses');
        $adaTidakAktif = $parts->contains(fn($p) => $p->status === 'tidak_aktif');
        $adaLimit      = $parts->contains(fn($p) => $p->status === 'Limit');

        if ($adaLimit && !$adaProses) {
            $newStatus = 'limit';
        } elseif ($adaProses || $adaTidakAktif) {
            $newStatus = 'proses';
        } else {
            $newStatus = 'selesai';
        }
        $sh->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Part berhasil ditandai sebagai Terpasang.');
    }

    // =========================================================================
    // PENGADAAN AUTO-SUBMIT HELPER
    // =========================================================================

    /**
     * Buat Purchasero otomatis dari ServiceHistory.
     *
     * @param  ServiceHistory  $service       Record service yang baru disimpan
     * @param  array           $resolvedParts Parts yang sudah di-resolve kategorinya
     * @param  string          $status        'Diajukan' (store) | 'Pending' (requestStore)
     * @param  int|null        $supplierId    Supplier/bengkel dari form
     * @return array  ['created' => bool, 'purchasero' => Purchasero|null,
     *                 'skipped_count' => int, 'no_pr' => string|null]
     */
    private function createPurchaseroFromService(
        ServiceHistory $service,
        array $resolvedParts,
        string $status,
        ?int $supplierId,
        ?string $alasanPermintaan = null,
        ?string $keteranganPengadaan = null
    ): array {
        if (empty($resolvedParts)) {
            return ['created' => false, 'purchasero' => null, 'skipped_count' => 0, 'no_pr' => null];
        }

        // Pisahkan part yang lolos cek duplikat vs yang di-skip
        $newParts     = [];
        $skippedCount = 0;

        foreach ($resolvedParts as $partData) {
            $namaPart     = strtolower(trim($partData['nama_part'] ?? ''));
            $categoryId   = $partData['category_id'] ?? null;
            $posisi       = strtolower(trim($partData['posisi'] ?? ''));
            $partNumber   = strtolower(trim($partData['part_number'] ?? ''));
            $serialNumber = strtolower(trim($partData['serial_number'] ?? ''));

            // Cek duplikat: 6 field sama DAN status purchasero masih Pending/Diajukan
            $isDuplicate = DB::table('purchaseros as pr')
                ->join('purchasero_service_parts as psp', 'pr.id', '=', 'psp.purchasero_id')
                ->where('psp.kendaraan_id', $service->kendaraan_id)
                ->whereRaw('LOWER(TRIM(psp.nama_part)) = ?', [$namaPart])
                ->where(function ($q) use ($categoryId) {
                    if ($categoryId) {
                        $q->where('psp.category_id', $categoryId);
                    } else {
                        $q->whereNull('psp.category_id');
                    }
                })
                ->where(function ($q) use ($posisi) {
                    if ($posisi) {
                        $q->whereRaw('LOWER(TRIM(psp.posisi)) = ?', [$posisi]);
                    } else {
                        $q->whereNull('psp.posisi')->orWhereRaw("TRIM(psp.posisi) = ''");
                    }
                })
                ->where(function ($q) use ($partNumber) {
                    if ($partNumber) {
                        $q->whereRaw('LOWER(TRIM(psp.part_number)) = ?', [$partNumber]);
                    } else {
                        $q->whereNull('psp.part_number')->orWhereRaw("TRIM(psp.part_number) = ''");
                    }
                })
                ->where(function ($q) use ($serialNumber) {
                    if ($serialNumber) {
                        $q->whereRaw('LOWER(TRIM(psp.serial_number)) = ?', [$serialNumber]);
                    } else {
                        $q->whereNull('psp.serial_number')->orWhereRaw("TRIM(psp.serial_number) = ''");
                    }
                })
                ->whereIn('pr.status', ['Pending', 'Diajukan'])
                ->exists();

            if ($isDuplicate) {
                $skippedCount++;
            } else {
                $newParts[] = $partData;
            }
        }

        // Jika semua part duplikat, tidak buat Purchasero baru
        if (empty($newParts)) {
            return ['created' => false, 'purchasero' => null, 'skipped_count' => $skippedCount, 'no_pr' => null];
        }

        // Generate No PR (lock untuk race condition)
        $last    = Purchasero::lockForUpdate()->orderBy('id', 'desc')->first();
        $lastNum = $last && preg_match('/(\d+)$/', $last->no_pr ?? '', $m) ? (int) $m[1] : 0;
        $noPr    = 'PR-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);

        $totalNominal = collect($newParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));

        // Generate keterangan slug otomatis jika tidak disediakan dari form
        if (empty($keteranganPengadaan)) {
            $kendaraan   = \App\Models\Kendaraan::find($service->kendaraan_id);
            $merkSlug    = strtolower(preg_replace('/[^a-z0-9]/i', '', $kendaraan?->merk ?? ''));
            $nopolSlug   = strtolower(preg_replace('/[^a-z0-9]/i', '', $kendaraan?->nopol ?? ''));
            $partSlugs   = collect($newParts)
                ->take(4)
                ->map(fn($p) => strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($p['nama_part'] ?? ''))))
                ->map(fn($s) => trim($s, '-'))
                ->filter()
                ->implode('-');
            $kendaraanSlug = $merkSlug . $nopolSlug;
            $parts = array_filter([$kendaraanSlug, $partSlugs]);
            $keteranganPengadaan = $nopolSlug
                ? substr('service-' . implode('-', $parts), 0, 500)
                : null;
        }

        // Buat Purchasero
        $purchasero = Purchasero::create([
            'no_pr'             => $noPr,
            'tanggal'           => $service->tanggal_service,
            'departemen'        => 'Produksi',
            'tipe_pengadaan'    => 'service',
            'pemohon'           => auth()->user()->name,
            'supplier_id'       => $supplierId,
            'alasan_permintaan' => $alasanPermintaan ?? $service->keluhan ?? '-',
            'keterangan'        => $keteranganPengadaan,
            'nominal'           => $totalNominal,
            'status'            => $status,
            'kendaraan_id'      => $service->kendaraan_id,
            'tanggal_service'   => $service->tanggal_service,
            'kilometer'         => $service->kilometer,
            'keluhan'           => $service->keluhan,
            'terakhir_diajukan' => $status === 'Diajukan' ? now() : null,
        ]);

        // Buat PurchaseroServicePart untuk tiap part yang lolos
        foreach ($newParts as $partData) {
            $isOverLimit = false;
            if (!empty($partData['category_id'])) {
                $limit = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $service->kendaraan_id)
                    ->where('category_id', $partData['category_id'])
                    ->first();
                if ($limit && (int)($partData['biaya'] ?? 0) > $limit->limit_price) {
                    $isOverLimit = true;
                }
            }

            PurchaseroServicePart::create([
                'purchasero_id'    => $purchasero->id,
                'kendaraan_id'     => $service->kendaraan_id,
                'category_id'      => $partData['category_id'] ?? null,
                'nama_part'        => $partData['nama_part'],
                'part_number'      => $partData['part_number'] ?? null,
                'serial_number'    => $partData['serial_number'] ?? null,
                'posisi'           => $partData['posisi'] ?? null,
                'merk'             => $partData['merk'] ?? null,
                'tgl_pasang'       => $partData['tgl_pasang'] ?? now()->toDateString(),
                'kilometer_pasang' => (int)($partData['kilometer_pasang'] ?? $service->kilometer ?? 0),
                'kondisi'          => $partData['kondisi'] ?? 'Baik',
                'status_part'      => 'Proses',
                'interval_nilai'   => (int)($partData['interval_nilai'] ?? 1),
                'interval_satuan'  => $partData['interval_satuan'] ?? 'bulan',
                'biaya'            => (int)($partData['biaya'] ?? 0),
                'keterangan'       => $partData['keterangan'] ?? null,
                'is_over_limit'    => $isOverLimit,
            ]);
        }

        return [
            'created'       => true,
            'purchasero'    => $purchasero,
            'skipped_count' => $skippedCount,
            'no_pr'         => $noPr,
        ];
    }
}
