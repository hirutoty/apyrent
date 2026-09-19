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
                'parts.supplier',
            ])
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service,'%Y-%m') = ?", [$bulan]))
            ->when($kendaraanId, fn($q) => $q->where('kendaraan_id', $kendaraanId))
            ->when($approvalStatus === 'pending', fn($q) => $q->where('status_approval', 'pending'))
            ->when($approvalStatus === 'approved', fn($q) => $q->where('status_approval', 'approved'))
            ->when($approvalStatus === 'rejected', fn($q) => $q->where('status_approval', 'rejected'))
            ->when($approvalStatus === 'limit', fn($q) => $q->where('status', 'limit'))
            ->when($approvalStatus === 'aktif', fn($q) => $q->where('status', 'aktif'))
            ->when($approvalStatus === 'terpasang', fn($q) => $q->where('status', 'terpasang'))
            ->when($approvalStatus === 'tidak_aktif', fn($q) => $q->where('status', 'tidak_aktif'))
            ->when($categoryId, fn($q) => $q->whereHas('parts', fn($p) => $p->where('category_id', $categoryId)))
            ->latest()
            ->paginate($request->per_page ?? 50)->withQueryString();

        $kendaraan  = Kendaraan::whereNotIn('status_kendaraan', ['disewa'])
            ->orderByDesc(
                \App\Models\ServiceHistory::select('tanggal_service')
                    ->whereColumn('kendaraan_id', 'kendaraan.id')
                    ->orderByDesc('tanggal_service')
                    ->limit(1)
            )
            ->orderBy('nopol')
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
            'batasReminder'      => (int) (Setting::first()?->batas_reminder ?? 30),
            'suppliers'          => Supplier::orderBy('nama_supplier')->get(),
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
        $kendaraan  = Kendaraan::orderByDesc(
                \App\Models\ServiceHistory::select('tanggal_service')
                    ->whereColumn('kendaraan_id', 'kendaraan.id')
                    ->orderByDesc('tanggal_service')
                    ->limit(1)
            )
            ->orderBy('nopol')
            ->get();
        $categories = ServiceCategory::orderBy('nama')->get();
        $suppliers  = Supplier::orderBy('nama_supplier')->get();
        $prefill    = null;

        if ($request->from_reminder) {
            $reminder = ReminderService::with(['servicePart.category', 'servicePart.serviceHistory', 'kendaraan'])->find($request->from_reminder);
            if ($reminder && $reminder->servicePart) {
                $part = $reminder->servicePart;
                $serviceHistory = $part->serviceHistory;
                
                $prefill = [
                    'source'            => 'reminder',
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

        if ($request->from_part) {
            $oldPart = ServicePart::with(['category', 'serviceHistory', 'kendaraan'])->find($request->from_part);
            if ($oldPart) {
                $kendaraanPart = $oldPart->kendaraan;
                $prefill = [
                    'source'          => 'part',
                    'replace_part_id' => $oldPart->id,
                    'kendaraan_id'    => $oldPart->kendaraan_id,
                    'kendaraan'       => $kendaraanPart,
                    'tanggal_service' => now()->format('Y-m-d'),
                    'kilometer'       => $kendaraanPart?->kilometer_sekarang ?? $oldPart->kilometer_pasang,
                    'keluhan'         => null,
                    'status'          => 'proses',
                    'part'            => [
                        'service_part_id' => $oldPart->id,
                        'nama_part'       => $oldPart->nama_part,
                        'part_number'     => $oldPart->part_number,
                        'posisi'          => $oldPart->posisi,
                        'category_id'     => $oldPart->category_id,
                        'category_nama'   => $oldPart->category?->nama,
                        'interval_nilai'  => $oldPart->interval_nilai,
                        'interval_satuan' => $oldPart->interval_satuan,
                        'biaya'           => $oldPart->biaya,
                        'kondisi'         => $oldPart->kondisi,
                    ],
                ];
            }
        }

        if ($request->edit_po) {
            $po = \App\Models\PurchaseOrder::find($request->edit_po);
            if ($po && $po->isRejected() && $po->source_type === 'service_part') {
                $sourceData    = $po->source_data ?? [];
                $kendaraanId   = $sourceData['kendaraan_id'] ?? null;
                $kendaraanPO   = $kendaraanId ? Kendaraan::find($kendaraanId) : null;
                $firstPart     = ($sourceData['parts'] ?? [])[0] ?? null;
                $prefill = [
                    'source'          => 'edit_po',
                    'edit_po_id'      => $po->id,
                    'kendaraan_id'    => $kendaraanId,
                    'kendaraan'       => $kendaraanPO,
                    'tanggal_service' => $sourceData['tanggal_service'] ?? now()->format('Y-m-d'),
                    'kilometer'       => $sourceData['kilometer'] ?? null,
                    'keluhan'         => $sourceData['keluhan'] ?? null,
                    'status'          => 'proses',
                    'catatan_tolak'   => $po->catatan_approval,
                    'part'            => $firstPart ? [
                        'service_part_id' => $firstPart['service_part_id'] ?? null,
                        'nama_part'       => $firstPart['nama_part'] ?? '',
                        'part_number'     => $firstPart['part_number'] ?? null,
                        'posisi'          => $firstPart['posisi'] ?? null,
                        'category_id'     => $firstPart['category_id'] ?? null,
                        'category_nama'   => $firstPart['category_nama'] ?? null,
                        'interval_nilai'  => $firstPart['interval_nilai'] ?? 12,
                        'interval_satuan' => $firstPart['interval_satuan'] ?? 'bulan',
                        'biaya'           => $firstPart['biaya'] ?? 0,
                        'kondisi'         => $firstPart['kondisi'] ?? 'Baik',
                    ] : null,
                    'all_parts'       => $sourceData['parts'] ?? [],
                    'temp_files'      => $sourceData['temp_files'] ?? [],
                ];
            }
        }

        // ── Ajukan Ulang dari Pembayaran yang Ditolak ──────────────────────────
        if ($request->filled('edit_pembayaran')) {
            $pembayaran = \App\Models\Pembayaran::find($request->edit_pembayaran);
            if ($pembayaran && in_array($pembayaran->status, ['Ditolak', 'Disetujui Sebagian']) && $pembayaran->source_type === 'service_part') {
                $sourceData    = $pembayaran->source_data ?? [];
                $kendaraanId   = $sourceData['kendaraan_id'] ?? null;
                $kendaraanPmb  = $kendaraanId ? Kendaraan::find($kendaraanId) : null;
                $allParts      = $sourceData['parts'] ?? [];
                $firstPart     = $allParts[0] ?? null;

                $prefill = [
                    'source'          => 'edit_pembayaran',
                    'edit_pembayaran_id' => $pembayaran->id,
                    'kendaraan_id'    => $kendaraanId,
                    'kendaraan'       => $kendaraanPmb,
                    'tanggal_service' => $sourceData['tanggal_service'] ?? now()->format('Y-m-d'),
                    'kilometer'       => $sourceData['kilometer'] ?? null,
                    'keluhan'         => $sourceData['keluhan'] ?? null,
                    'status'          => 'proses',
                    'catatan_tolak'   => $pembayaran->catatan ?? $request->rejection_reason ?? null,
                    'part'            => $firstPart ? [
                        'service_part_id' => $firstPart['service_part_id'] ?? null,
                        'nama_part'       => $firstPart['nama_part'] ?? '',
                        'part_number'     => $firstPart['part_number'] ?? null,
                        'posisi'          => $firstPart['posisi'] ?? null,
                        'category_id'     => $firstPart['category_id'] ?? null,
                        'category_nama'   => $firstPart['category_nama'] ?? null,
                        'interval_nilai'  => $firstPart['interval_nilai'] ?? 12,
                        'interval_satuan' => $firstPart['interval_satuan'] ?? 'bulan',
                        'biaya'           => $firstPart['biaya'] ?? 0,
                        'kondisi'         => $firstPart['kondisi'] ?? 'Baik',
                    ] : null,
                    'all_parts'       => $allParts,
                    'temp_files'      => $sourceData['temp_files'] ?? [],
                ];
            }
        }

        return view('admin.service.service_history_create', compact('kendaraan', 'categories', 'prefill', 'suppliers'));
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
            'parts.*.bukti'                => ($request->filled('edit_po') || $request->filled('edit_pembayaran'))
                                                ? 'nullable|array'
                                                : 'required_with:parts|array|min:1',
            'parts.*.bukti.*'              => 'file|mimes:jpg,jpeg,png,mp4,mov',
            'parts.*.keterangan_limit'     => 'nullable|string|max:1000',
            'parts.*.nama_rekening'        => 'nullable|string|max:150',
            'parts.*.nama_bank'            => 'nullable|string|max:100',
            'parts.*.no_rekening'          => 'nullable|string|max:50',
            'parts.*.supplier_id'          => 'nullable|exists:supplier,id',
        ]);

        // ===========================================================================
        // APPROVAL WORKFLOW: Intercept dan kirim ke Purchase Order
        // Skip intercept jika:
        // 1. Dari reminder (replacement part yang sudah approved)
        // 2. Dari edit existing service_history_id (update data existing)
        // ===========================================================================
        
        if (!$request->filled('from_reminder') && !$request->filled('service_history_id')) {
            try {
                // Check if this is a resubmit (from rejected PO)
                if ($request->filled('edit_po')) {
                    $poId = $request->input('edit_po');
                    
                    // Resubmit: Update existing PO
                    $po = $interceptor->resubmitToPurchaseOrder($poId, $request, 'service_part');
                    
                return redirect()
                    ->route('service-history.index')
                    ->with('success', 'Pengajuan service part berhasil diajukan ulang. Menunggu approval di Purchase Order.');
                }

                // Check if this is a resubmit (from rejected Pembayaran)
                if ($request->filled('edit_pembayaran')) {
                    $pembayaranId = (int) $request->input('edit_pembayaran');

                    $pembayaran = $interceptor->resubmitToPembayaran($pembayaranId, $request, 'service_part');

                    return redirect()
                        ->route('service-history.index')
                        ->with('success', 'Pengajuan service part berhasil diajukan ulang. Menunggu approval.');
                }
                
                // Step 1: Intercept data dari form
                $interceptedData = $interceptor->intercept($request, 'service_part');
                
                // Step 2: Save ke Purchase Order
                $po = $interceptor->saveToPurchaseOrder($interceptedData, 'service_part');
                
                // Step 3: Upload temporary files
                $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $po->id, 'purchase_order');
                
                // Step 4: Update source_data dengan file info
                $sourceData = $po->source_data;
                $sourceData['temp_files'] = $uploadedFiles;
                $po->update(['source_data' => $sourceData]);
                
                return redirect()
                    ->route('service-history.index')
                    ->with('success', 'Pengajuan service part berhasil dikirim. Menunggu approval dari Superadmin.');
                    
            } catch (\Exception $e) {
                \Log::error('Error intercepting service part submission: ' . $e->getMessage());
                
                return back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan saat mengajukan pengeluaran. Silakan coba lagi.');
            }
        }

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        $serviceAktif = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->whereIn('status', ['aktif', 'tidak_aktif'])->exists();
        if ($serviceAktif) {
            return back()->withErrors(['kendaraan_id' => 'Kendaraan masih punya service aktif atau menunggu persetujuan.'])->withInput();
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

                // Pre-load semua limit rules untuk kendaraan ini sekali saja (avoid N+1)
                $limitRulesMap = ServiceCategoryLimit::where('kendaraan_id', $request->kendaraan_id)
                    ->get()
                    ->keyBy('category_id');
                $kmInput = (int) $request->kilometer;
                
                foreach ($resolvedParts as $idx => $partData) {
                    $tglPasang     = Carbon::parse($partData['tgl_pasang']);
                    $tanggalLimit  = $this->hitungTanggalLimitPart(
                        $tglPasang,
                        (int)$partData['interval_nilai'],
                        $partData['interval_satuan']
                    );

                    // Ambil limit rule untuk kategori part ini
                    $limitRule = isset($partData['category_id']) && $partData['category_id']
                        ? ($limitRulesMap[$partData['category_id']] ?? null)
                        : null;

                    // Cari km_pasang part lama (Terpasang/aktif) dengan kategori+posisi sama
                    $partLamaKmPasang = null;
                    if (isset($partData['category_id']) && $partData['category_id']) {
                        $posisiVal = trim($partData['posisi'] ?? '');
                        $partLamaQuery = ServicePart::where('kendaraan_id', $request->kendaraan_id)
                            ->whereIn('status', ['Terpasang', 'aktif'])
                            ->where('category_id', $partData['category_id'])
                            ->whereNotNull('kilometer_pasang');
                        if ($posisiVal) {
                            $partLamaQuery->whereRaw('LOWER(TRIM(posisi)) = ?', [strtolower($posisiVal)]);
                        } else {
                            $partLamaQuery->where(fn($q) => $q->whereNull('posisi')->orWhereRaw("TRIM(posisi) = ''"));
                        }
                        $partLama = $partLamaQuery->orderByDesc('tgl_pasang')->first();
                        $partLamaKmPasang = $partLama ? (int) $partLama->kilometer_pasang : null;
                    }

                    // Auto-generate keterangan, kondisi, dan status
                    $keteranganOtomatis = $this->generateKeteranganLimit($partData, $kmInput, $limitRule, $request->tanggal_service, $partLamaKmPasang);
                    $statusOtomatis     = $this->deriveStatusPart();

                    // Upload bukti files untuk part ini
                    $buktiFiles = $this->uploadPartBuktiFiles($request, $idx);
                    foreach ($buktiFiles as $bf) {
                        $movedFiles[] = public_path($bf['path']);
                    }

                    $part = ServicePart::create([
                        'service_history_id'  => $service->id,
                        'kendaraan_id'        => $request->kendaraan_id,
                        'category_id'         => $partData['category_id'] ?? null,
                        'supplier_id'         => !empty($partData['supplier_id']) ? (int) $partData['supplier_id'] : null,
                        'nama_part'           => $partData['nama_part'],
                        'part_number'         => $partData['part_number'] ?? null,
                        'serial_number'       => $partData['serial_number'] ?? null,
                        'posisi'              => $partData['posisi'] ?? null,
                        'tgl_pasang'          => $tglPasang->toDateString(),
                        'kilometer_pasang'    => $partData['kilometer_pasang'] ?? $request->kilometer,
                        'kondisi'             => 'Perlu Ganti', // default saat baru diinput; akan diupdate saat Terpasang/Limit
                        'status'              => $statusOtomatis,
                        'interval_nilai'      => (int)$partData['interval_nilai'],
                        'interval_satuan'     => $partData['interval_satuan'],
                        'tanggal_limit'       => $tanggalLimit->toDateString(),
                        'biaya'               => (int)($partData['biaya'] ?? 0),
                        'status_pengeluaran'  => $partStatuses[$idx] ?? 'stabil',
                        'bukti'               => !empty($buktiFiles) ? json_encode($buktiFiles) : null,
                        'keterangan_limit'    => $keteranganOtomatis,
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
            'parts.*.keterangan_limit'     => 'nullable|string|max:1000',
            'parts.*.nama_rekening'        => 'nullable|string|max:150',
            'parts.*.nama_bank'            => 'nullable|string|max:100',
            'parts.*.no_rekening'          => 'nullable|string|max:50',
            'parts.*.supplier_id'          => 'nullable|exists:supplier,id',
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
                        'supplier_id'         => !empty($partData['supplier_id']) ? (int) $partData['supplier_id'] : null,
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
                        'keterangan_limit'    => $partData['keterangan_limit'] ?? $partData['keterangan'] ?? null,
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
        $request->validate(['status' => 'required|in:aktif,terpasang,limit']);

        $service   = ServiceHistory::findOrFail($id);
        $kendaraan = $service->kendaraan;

        $service->update(['status' => $request->status]);

        $tanggalSelesai  = Carbon::parse($service->tanggal_service)->toDateString();
        $updateKendaraan = [
            'status_kendaraan' => $request->status === 'aktif' ? 'service' : 'tersedia',
        ];
        if ($request->status === 'terpasang' && $service->kilometer > 0) {
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
            $masihAktif = ServiceHistory::where('kendaraan_id', $kendaraan->id)
                ->whereIn('status', ['aktif', 'tidak_aktif'])->exists();
            if (!$masihAktif) $kendaraan->update(['status_kendaraan' => 'tersedia']);
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
        $request->validate(['status' => 'required|in:aktif,selesai']);

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
                'status_kendaraan' => $request->status === 'aktif' ? 'service' : 'tersedia',
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

    /**
     * AJAX endpoint: return semua ServiceCategoryLimit untuk kendaraan tertentu.
     * Dipakai oleh form service_history_create untuk kalkulasi keterangan limit otomatis di JS.
     *
     * GET /admin/service-history/limit-rules/{kendaraan_id}
     */
    public function getLimitRules(int $kendaraanId): \Illuminate\Http\JsonResponse
    {
        $rules = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $kendaraanId)
            ->get()
            ->map(fn($r) => [
                'category_id'        => $r->category_id,
                'limit_price'        => $r->limit_price,
                'limit_km'           => $r->limit_km,
                'limit_km_interval'  => $r->limit_km_interval,
                'limit_nilai'        => $r->limit_nilai,
                'limit_satuan'       => $r->limit_satuan,
            ]);

        return response()->json($rules);
    }

    /**
     * AJAX endpoint: cari ServicePart Terpasang/aktif berdasarkan
     * kendaraan_id + category_id + posisi → return kilometer_pasang.
     *
     * Dipakai di form service untuk menghitung target KM limit:
     *   target = part_lama.kilometer_pasang + limit_km
     *
     * GET /admin/service-history/part-lama-km
     *   ?kendaraan_id=X&category_id=Y&posisi=Z
     */
    public function getPartLamaKm(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $kendaraanId = (int) $request->kendaraan_id;
        $categoryId  = $request->category_id ? (int) $request->category_id : null;
        $posisi      = $request->posisi ? trim($request->posisi) : null;

        if (!$kendaraanId) {
            return response()->json(['kilometer_pasang' => null]);
        }

        $query = ServicePart::where('kendaraan_id', $kendaraanId)
            ->whereIn('status', ['Terpasang', 'aktif'])
            ->whereNotNull('kilometer_pasang');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        } else {
            $query->whereNull('category_id');
        }

        if ($posisi) {
            $query->whereRaw('LOWER(TRIM(posisi)) = ?', [strtolower($posisi)]);
        } else {
            $query->where(fn($q) => $q->whereNull('posisi')->orWhereRaw("TRIM(posisi) = ''"));
        }

        $part = $query->orderByDesc('tgl_pasang')->first();

        return response()->json([
            'kilometer_pasang' => $part ? (int) $part->kilometer_pasang : null,
            'part_id'          => $part?->id,
            'nama_part'        => $part?->nama_part,
        ]);
    }

    /**
     * Perpanjang / Ganti Part dari modal di service history.
     *
     * Alur:
     * 1. Validasi request (nama_part, tgl_pasang, interval, lampiran wajib)
     * 2. Intercept → buat PurchaseOrder baru (service_part) dengan replace_part_id
     * 3. Upload file lampiran ke temp storage
     * 4. Redirect balik ke service history dengan pesan sukses
     *
     * POST /admin/service-history/perpanjang-part/{part_id}
     */
    public function perpanjangPart(Request $request, int $partId, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $oldPart = ServicePart::with(['kendaraan', 'category'])->findOrFail($partId);

        $request->validate([
            'kendaraan_id'     => 'required|exists:kendaraan,id',
            'nama_part'        => 'required|string|max:255',
            'category_id'      => 'nullable|exists:service_categories,id',
            'posisi'           => 'nullable|string|max:100',
            'tgl_pasang'       => 'required|date',
            'kilometer_pasang' => 'nullable|integer|min:0',
            'interval_nilai'   => 'required|integer|min:1',
            'interval_satuan'  => 'required|in:hari,minggu,bulan,tahun',
            'biaya'            => 'nullable|numeric|min:0',
            'part_number'      => 'nullable|string|max:100',
            'serial_number'    => 'nullable|string|max:100',
            'nama_rekening'    => 'nullable|string|max:150',
            'nama_bank'        => 'nullable|string|max:100',
            'no_rekening'      => 'nullable|string|max:50',
            'supplier_id'      => 'nullable|exists:supplier,id',
            'bukti'            => 'required|array|min:1',
            'bukti.*'          => 'file|mimes:jpg,jpeg,png,mp4,mov|max:10240',
        ], [
            'bukti.required'   => 'Lampiran wajib diisi.',
            'bukti.min'        => 'Minimal 1 file lampiran.',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        try {
            // Build synthetic request data agar kompatibel dengan intercept()
            // — wrap part baru dalam array parts[] seperti form service biasa
            $tanggalService = $request->tgl_pasang;
            $kmPasang       = (int) ($request->kilometer_pasang ?? $kendaraan->kilometer_sekarang ?? 0);

            // Resolve kategori jika ada
            $categoryId = $request->category_id ? (int) $request->category_id : ($oldPart->category_id);

            // Hitung tanggal_limit baru
            $tglPasangCarbon = Carbon::parse($tanggalService);
            $tanggalLimit    = $this->hitungTanggalLimitPart(
                $tglPasangCarbon,
                (int) $request->interval_nilai,
                $request->interval_satuan
            );

            // Ambil limit rule untuk keterangan otomatis
            $limitRule = $categoryId
                ? ServiceCategoryLimit::where('kendaraan_id', $request->kendaraan_id)
                    ->where('category_id', $categoryId)
                    ->first()
                : null;

            // Cari km_pasang part lama (old_part = $oldPart) — sudah diketahui karena ini perpanjang
            $partLamaKmPasang = $oldPart ? (int) $oldPart->kilometer_pasang : null;

            $partDataForKet = [
                'biaya'            => (int) ($request->biaya ?? 0),
                'tgl_pasang'       => $tanggalService,
                'interval_nilai'   => (int) $request->interval_nilai,
                'interval_satuan'  => $request->interval_satuan,
                'kilometer_pasang' => $kmPasang,
            ];
            $keteranganOtomatis = $this->generateKeteranganLimit(
                $partDataForKet, $kmPasang, $limitRule, $tanggalService, $partLamaKmPasang
            );

            // Buat source_data untuk PO
            $partsData = [[
                'nama_part'        => $request->nama_part,
                'category_id'      => $categoryId,
                'posisi'           => $request->posisi ?? $oldPart->posisi,
                'part_number'      => $request->part_number ?? null,
                'serial_number'    => $request->serial_number ?? null,
                'tgl_pasang'       => $tanggalService,
                'kilometer_pasang' => $kmPasang,
                'interval_nilai'   => (int) $request->interval_nilai,
                'interval_satuan'  => $request->interval_satuan,
                'biaya'            => (int) ($request->biaya ?? 0),
                'kondisi'          => 'Perlu Ganti',
                'status'           => 'Proses',
                'keterangan_limit' => $keteranganOtomatis,
                'nama_rekening'    => $request->nama_rekening ?? null,
                'nama_bank'        => $request->nama_bank ?? null,
                'no_rekening'      => $request->no_rekening ?? null,
                'supplier_id'      => $request->supplier_id ? (int) $request->supplier_id : null,
                'replace_part_id'  => $partId,   // tandai part mana yang diganti
            ]];

            $sourceData = [
                'kendaraan_id'    => (int) $request->kendaraan_id,
                'tanggal_service' => $tanggalService,
                'kilometer'       => $kmPasang,
                'keluhan'         => 'Perpanjang/Ganti: ' . $oldPart->nama_part
                                     . ($oldPart->posisi ? ' (' . $oldPart->posisi . ')' : ''),
                'parts'           => $partsData,
                'is_perpanjang'   => true,
                'old_part_id'     => $partId,
            ];

            // Buat PurchaseOrder langsung tanpa melalui intercept() karena request tidak punya
            // format multipart parts[0][nama_part] yang diharapkan intercept — kita buat manual
            $poModel = \App\Models\PurchaseOrder::create([
                'tanggal_po'        => now()->toDateString(),
                'vendor'            => $kendaraan->merk . ' — ' . $kendaraan->nopol,
                'source_type'       => 'service_part',
                'source_data'       => $sourceData,
                'total_barang'      => 1,
                'total_harga'       => (int) ($request->biaya ?? 0),
                'status'            => 'Pending',
                'status_po'         => 'Pending',
                'terakhir_diajukan' => now(),
            ]);

            // Upload lampiran ke temp storage
            $tempDir = storage_path('app/public/purchase_order/' . $poModel->id . '/parts/0/bukti');
            if (!file_exists($tempDir)) mkdir($tempDir, 0777, true);

            $tempFiles  = [];
            $buktiFiles = $request->file('bukti', []);
            foreach ($buktiFiles as $file) {
                if (!$file->isValid()) continue;
                $ext      = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $ext;
                $relPath  = 'purchase_order/' . $poModel->id . '/parts/0/bukti/' . $filename;
                $file->storeAs('public/' . 'purchase_order/' . $poModel->id . '/parts/0/bukti', $filename);
                $tempFiles[] = [
                    'path'          => $relPath,
                    'original_name' => $file->getClientOriginalName(),
                    'extension'     => $ext,
                    'size'          => $file->getSize(),
                ];
            }

            // Simpan temp_files ke source_data
            $updatedSource                        = $poModel->source_data;
            $updatedSource['temp_files']['parts'] = ['0' => ['bukti' => $tempFiles]];
            $poModel->update(['source_data' => $updatedSource]);

            return redirect()
                ->route('service-history.index')
                ->with('success', 'Pengajuan perpanjang part "' . $request->nama_part . '" berhasil dikirim. Menunggu approval di Purchase Order (PO: ' . $poModel->po_id . ').');

        } catch (\Throwable $e) {
            \Log::error('perpanjangPart error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Resolve category per part: buat category baru jika nama_category_baru diisi

    /**
     * Generate keterangan_limit otomatis per part berdasarkan kondisi limit.
     *
     * Logika:
     *   - Tidak ada limit sama sekali → "-"
     *   - Hanya limit biaya terlampaui (nilai == limit → "mencapai", > limit → "melebihi"),
     *     interval waktu & KM belum terlampaui
     *   - Hanya limit interval waktu terlampaui, biaya & KM belum
     *   - Hanya limit KM terlampaui, biaya & interval belum
     *   - Kombinasi keduanya / ketiganya → gabungan kalimat
     *
     * @param  array                        $partData    Data part dari form (biaya, tgl_pasang, interval_nilai, interval_satuan, kilometer_pasang)
     * @param  int                          $kmInput     KM header dari form (kilometer saat service)
     * @param  ServiceCategoryLimit|null    $limitRule   Rule limit kategori untuk kendaraan ini
     * @return string
     */
    private function generateKeteranganLimit(array $partData, int $kmInput, ?ServiceCategoryLimit $limitRule, ?string $tanggalServis = null, ?int $kmPasangLama = null): string
    {
        // Tidak ada rule → tidak ada limit
        if (!$limitRule) {
            return '-';
        }

        $biaya          = (int) ($partData['biaya'] ?? 0);
        $tglPasang      = \Carbon\Carbon::parse($partData['tgl_pasang'] ?? now());
        $intervalNilai  = (int) ($partData['interval_nilai'] ?? 0);
        $intervalSatuan = $partData['interval_satuan'] ?? 'bulan';
        $kmPasang       = (int) ($partData['kilometer_pasang'] ?? $kmInput);

        // Hitung tanggal limit (tgl_pasang + interval)
        $tglLimit   = $this->hitungTanggalLimitPart($tglPasang, $intervalNilai, $intervalSatuan);
        // Bandingkan dengan tanggal servis yang diinput (bukan hari ini)
        $refTanggal = \Carbon\Carbon::parse($tanggalServis ?? now())->startOfDay();

        // Cek masing-masing dimensi limit
        $hargaLimit  = $limitRule->limit_price;  // nullable
        $kmLimit     = $limitRule->limit_km;      // nullable
        $intervalAda = $intervalNilai > 0;

        // ── Dimensi biaya ─────────────────────────────────────────────────────
        $biayaLewat  = $hargaLimit && $biaya > $hargaLimit;
        $biayaSama   = $hargaLimit && $biaya === $hargaLimit;
        $biayaAman   = !$hargaLimit || $biaya < $hargaLimit;

        // ── Dimensi interval waktu ────────────────────────────────────────────
        $waktuLewat  = $intervalAda && $tglLimit->lt($refTanggal);
        $waktuSama   = $intervalAda && $tglLimit->eq($refTanggal);
        $waktuAman   = !$intervalAda || $tglLimit->gt($refTanggal);

        // ── Dimensi KM (km_pasang part LAMA + limit_km = target) ────────────
        // $kmPasangLama = kilometer_pasang dari part lama yang masih Terpasang
        //                 di kategori + posisi yang sama (dicari dari DB sebelum memanggil fungsi ini)
        // Jika tidak ada part lama, fallback ke km_pasang form baru
        $kmAda    = $limitRule->limit_km && $limitRule->limit_km > 0;
        $kmSama   = false;
        $kmLewat  = false;
        $kmAman   = true;
        if ($kmAda) {
            // Base KM: dari part lama jika tersedia, fallback ke km_pasang form baru
            $baseKm   = $kmPasangLama ?? $kmPasang;
            $targetKm = $baseKm + (int) $limitRule->limit_km;
            $kmSama  = $kmInput === $targetKm;
            $kmLewat = $kmInput  >  $targetKm;
            $kmAman  = $kmInput  <  $targetKm;
        }

        // Tidak ada satu pun limit yang dikonfigurasi → "-"
        $adaLimit = $hargaLimit || $intervalAda || $kmAda;
        if (!$adaLimit) {
            return '-';
        }

        // Jika tidak ada limit yang terlampaui atau tercapai
        if ($biayaAman && $waktuAman && $kmAman) {
            return '-';
        }

        // ── Bangun kalimat per dimensi ────────────────────────────────────────
        $parts = [];

        // Biaya
        if ($biayaSama) {
            $parts['biaya'] = 'mencapai batas limit biaya';
        } elseif ($biayaLewat) {
            $parts['biaya'] = 'sudah melebihi limit biaya';
        }

        // Interval waktu
        if ($waktuSama) {
            $parts['waktu'] = 'mencapai batas limit jangka waktu';
        } elseif ($waktuLewat) {
            $parts['waktu'] = 'sudah melebihi batas waktu';
        }

        // KM
        if ($kmSama) {
            $parts['km'] = 'mencapai batas limit KM';
        } elseif ($kmLewat) {
            $parts['km'] = 'sudah melebihi batas limit KM';
        }

        // Dimensi yang BELUM terlampaui (hanya dicantumkan jika ada rule-nya)
        $belumParts = [];
        if ($hargaLimit && $biayaAman && !isset($parts['biaya'])) {
            $belumParts[] = 'belum mencapai limit biaya';
        }
        if ($intervalAda && $waktuAman && !isset($parts['waktu'])) {
            $belumParts[] = 'belum mencapai limit jangka waktu';
        }
        if ($kmAda && $kmAman && !isset($parts['km'])) {
            $belumParts[] = 'belum mencapai limit KM';
        }

        // Gabung: yang terlampaui dulu, lalu yang belum
        $terlampaui = array_values($parts);
        $kalimat    = array_merge($terlampaui, $belumParts);

        if (empty($kalimat)) {
            return '-';
        }

        // Kapitalisasi kata pertama dan gabungkan
        $result = ucfirst(implode(', ', $kalimat));
        return $result;
    }

    /**
     * Generate status (kondisi) otomatis berdasarkan status part.
     * - Terpasang  → Aktif
     * - tidak_aktif (belum terpasang) / Proses → Perlu Ganti
     * - Limit      → Rusak
     * Dipanggil setelah part disimpan untuk update kondisi.
     */
    private function deriveKondisiFromStatus(string $status): string
    {
        return match ($status) {
            'Terpasang'   => 'Baik',
            'Limit'       => 'Rusak',
            default       => 'Perlu Ganti',
        };
    }

    /**
     * Generate status part otomatis (selalu 'Proses' saat pertama kali input —
     * akan berubah via approval flow dari PO).
     */
    private function deriveStatusPart(): string
    {
        return 'Proses';
    }
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

        // Kondisi otomatis berdasarkan status baru
        $updateData['kondisi'] = $this->deriveKondisiFromStatus($request->status);

        $part->update($updateData);

        // Recalculate header status dari semua parts di service history ini
        $sh = ServiceHistory::with('parts')->find($part->service_history_id);
        if ($sh) {
            $parts = $sh->parts;
            $adaAktif      = $parts->contains(fn($p) => $p->status === 'aktif');
            $adaTidakAktif = $parts->contains(fn($p) => $p->status === 'tidak_aktif');
            $adaLimit      = $parts->contains(fn($p) => $p->status === 'Limit');

            if ($adaLimit && !$adaAktif && !$adaTidakAktif) {
                $newStatus = 'limit';
            } elseif ($adaAktif || $adaTidakAktif) {
                $newStatus = 'aktif';
            } else {
                $newStatus = 'terpasang';
            }
            $sh->update(['status' => $newStatus]);
        }

        return back()->with('success', 'Status part berhasil diperbarui.');
    }

    /**
     * Turunkan status ServiceHistory dari komposisi status part-partnya.
     * Prioritas: ada aktif/tidak_aktif → 'aktif', semua Terpasang → 'terpasang'.
     * (Status 'limit' dihandle terpisah oleh cron CheckServicePartLimit)
     */
    private function deriveServiceStatus(array $parts): string
    {
        foreach ($parts as $part) {
            if (in_array($part['status'] ?? '', ['aktif', 'tidak_aktif', 'Proses'])) {
                return 'aktif';
            }
        }
        return 'terpasang';
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
                'kondisi' => $this->deriveKondisiFromStatus('Terpasang'), // otomatis: Aktif
            ]);
        }

        // Recalculate status service history
        $sh->refresh();
        $parts = $sh->parts;
        $adaAktif      = $parts->contains(fn($p) => $p->status === 'aktif');
        $adaTidakAktif = $parts->contains(fn($p) => $p->status === 'tidak_aktif');
        $adaLimit      = $parts->contains(fn($p) => $p->status === 'Limit');

        if ($adaLimit && !$adaAktif && !$adaTidakAktif) {
            $newStatus = 'limit';
        } elseif ($adaAktif || $adaTidakAktif) {
            $newStatus = 'aktif';
        } else {
            $newStatus = 'terpasang';
        }
        $sh->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Part berhasil ditandai sebagai Terpasang.');
    }

    // =========================================================================
    // TERPASANG AKTIF — aktif → terpasang + archive part lama
    // =========================================================================

    /**
     * Tandai ServiceHistory berstatus 'aktif' sebagai 'terpasang'.
     * Untuk setiap part aktif/tidak_aktif:
     *   1. Cari part lama kendaraan yg sama (nama_part + posisi + category_id) status Terpasang/Limit → Diganti
     *   2. Ubah part baru: aktif/tidak_aktif → Terpasang
     * Akses: superadmin & operasi
     */
    public function terpasangAktif(int $id)
    {
        $role = auth()->user()->role ?? '';
        if (!in_array($role, ['superadmin', 'operasi'])) {
            return redirect()->back()->with('error', 'Tidak memiliki izin.');
        }

        $sh = ServiceHistory::with('parts')->findOrFail($id);

        if ($sh->status !== 'aktif') {
            return redirect()->back()->with('error', 'Hanya service dengan status Aktif yang bisa ditandai Terpasang.');
        }

        $partsToActivate = $sh->parts->whereIn('status', ['aktif', 'tidak_aktif']);

        if ($partsToActivate->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada part yang perlu ditandai Terpasang.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($sh, $partsToActivate) {
            $now = now();

            foreach ($partsToActivate as $newPart) {
                // ── 1. Archive part lama (Opsi A: match by nama+posisi+category) ────
                \App\Models\ServicePart::where('kendaraan_id', $sh->kendaraan_id)
                    ->whereRaw('LOWER(TRIM(nama_part)) = ?', [strtolower(trim($newPart->nama_part ?? ''))])
                    ->where('category_id', $newPart->category_id)
                    ->where(function ($q) use ($newPart) {
                        $posisi = trim($newPart->posisi ?? '');
                        if ($posisi !== '') {
                            $q->whereRaw('LOWER(TRIM(posisi)) = ?', [strtolower($posisi)]);
                        } else {
                            $q->where(fn($q2) => $q2->whereNull('posisi')->orWhereRaw("TRIM(posisi) = ''"));
                        }
                    })
                    ->whereIn('status', ['Terpasang', 'Limit'])
                    ->where('id', '!=', $newPart->id) // jangan archive diri sendiri
                    ->update([
                        'status'             => 'Diganti',
                        'replaced_at'        => $now,
                        'replaced_by_part_id' => $newPart->id,
                    ]);

                // ── 2. Aktifkan part baru ──────────────────────────────────────────
                $newPart->update([
                    'status'  => 'Terpasang',
                    'kondisi' => $this->deriveKondisiFromStatus('Terpasang'), // otomatis: Aktif
                ]);
            }

            // ── 3. Update service history → terpasang ─────────────────────────────
            $sh->refresh();
            $parts         = $sh->parts;
            $adaLimit      = $parts->contains(fn($p) => $p->status === 'Limit');
            $adaAktif      = $parts->contains(fn($p) => $p->status === 'aktif');
            $adaTidakAktif = $parts->contains(fn($p) => $p->status === 'tidak_aktif');

            if ($adaLimit && !$adaAktif && !$adaTidakAktif) {
                $newStatus = 'limit';
            } elseif ($adaAktif || $adaTidakAktif) {
                $newStatus = 'aktif'; // masih ada yg belum terpasang
            } else {
                $newStatus = 'terpasang';
            }
            $sh->update(['status' => $newStatus]);

            // ── 4. Update status kendaraan ────────────────────────────────────────
            $kendaraan = $sh->kendaraan;
            if ($kendaraan) {
                $updateKendaraan = [
                    // KM selalu diupdate saat part dipasang (tidak tunggu status terpasang)
                    'km_terakhir_service'      => $sh->kilometer ?? $kendaraan->km_terakhir_service,
                    'kilometer_sekarang'       => $sh->kilometer ?? $kendaraan->kilometer_sekarang,
                    'tanggal_terakhir_service' => \Carbon\Carbon::parse($sh->tanggal_service)->toDateString(),
                ];
                if ($newStatus === 'terpasang') {
                    $updateKendaraan['status_kendaraan'] = 'tersedia';
                }
                $kendaraan->update($updateKendaraan);

                // Auto-close reminder aktif untuk parts yang baru terpasang
                foreach ($partsToActivate as $part) {
                    \App\Models\ReminderService::where('kendaraan_id', $sh->kendaraan_id)
                        ->whereIn('status', ['aktif', 'jatuh_tempo'])
                        ->whereHas('servicePart', fn($q) =>
                            $q->where('nama_part', $part->nama_part)
                              ->where('posisi', $part->posisi)
                        )
                        ->update(['status' => 'selesai']);
                }
            }
        });

        return redirect()->back()->with('success', 'Part berhasil ditandai Terpasang. Part lama diarsipkan sebagai Diganti.');
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
        // Pre-load limit rules sekali (avoid N+1 di dalam loop)
        $limitRulesMapPO = ServiceCategoryLimit::where('kendaraan_id', $service->kendaraan_id)
            ->get()
            ->keyBy('category_id');
        $kmInputPO = (int) ($service->kilometer ?? 0);

        foreach ($newParts as $partData) {
            $isOverLimit = false;
            $limitRulePO = isset($partData['category_id']) && $partData['category_id']
                ? ($limitRulesMapPO[$partData['category_id']] ?? null)
                : null;

            if ($limitRulePO && $limitRulePO->limit_price && (int)($partData['biaya'] ?? 0) > $limitRulePO->limit_price) {
                $isOverLimit = true;
            }

            // Cari km_pasang part lama (Terpasang/aktif) dengan kategori+posisi sama
            $partLamaKmPasangPO = null;
            if (isset($partData['category_id']) && $partData['category_id']) {
                $posisiValPO = trim($partData['posisi'] ?? '');
                $partLamaQueryPO = ServicePart::where('kendaraan_id', $service->kendaraan_id)
                    ->whereIn('status', ['Terpasang', 'aktif'])
                    ->where('category_id', $partData['category_id'])
                    ->whereNotNull('kilometer_pasang');
                if ($posisiValPO) {
                    $partLamaQueryPO->whereRaw('LOWER(TRIM(posisi)) = ?', [strtolower($posisiValPO)]);
                } else {
                    $partLamaQueryPO->where(fn($q) => $q->whereNull('posisi')->orWhereRaw("TRIM(posisi) = ''"));
                }
                $partLamaPO = $partLamaQueryPO->orderByDesc('tgl_pasang')->first();
                $partLamaKmPasangPO = $partLamaPO ? (int) $partLamaPO->kilometer_pasang : null;
            }

            // Keterangan otomatis
            $keteranganPartOtomatis = $this->generateKeteranganLimit($partData, $kmInputPO, $limitRulePO, $service->tanggal_service, $partLamaKmPasangPO);

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
                'kondisi'          => 'Perlu Ganti', // otomatis; diupdate jadi Aktif saat Terpasang
                'status_part'      => 'Proses',
                'interval_nilai'   => (int)($partData['interval_nilai'] ?? 1),
                'interval_satuan'  => $partData['interval_satuan'] ?? 'bulan',
                'biaya'            => (int)($partData['biaya'] ?? 0),
                'keterangan'       => $keteranganPartOtomatis,
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
