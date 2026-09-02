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
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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

        return view('admin.service.service_history_create', compact('kendaraan', 'categories', 'prefill'));
    }

    /**
     * Show Request Part form
     */
    public function requestCreate(Request $request)
    {
        $kendaraan  = Kendaraan::orderBy('merk')->get();
        $categories = ServiceCategory::orderBy('nama')->get();
        return view('admin.service.service_history_request', compact('kendaraan', 'categories'));
    }

    /**
     * Store Request Part — semua parts masuk sebagai pending (no duplicate check)
     */
    public function requestStore(Request $request)
    {
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
                $request, $kendaraan, $totalBiaya, $partStatuses, $buktiBayarMeta, $attachmentsMeta,
                $resolvedParts, &$movedFiles
            ) {
                $buktiBayar = null;
                if ($buktiBayarMeta) {
                    if (!file_exists($buktiBayarMeta['destination'])) mkdir($buktiBayarMeta['destination'], 0777, true);
                    $buktiBayarMeta['file']->move($buktiBayarMeta['destination'], $buktiBayarMeta['filename']);
                    $buktiBayar   = $buktiBayarMeta['path'];
                    $movedFiles[] = public_path($buktiBayar);
                }

                // Merge ke service history yang ada, atau buat baru jika belum ada
                $existing = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
                    ->latest()
                    ->first();

                if ($existing) {
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
                    // Buat service history baru dengan status_approval = pending
                    $service = ServiceHistory::create([
                        'kendaraan_id'    => $request->kendaraan_id,
                        'keluhan'         => $request->keluhan,
                        'kilometer'       => $request->kilometer,
                        'total_biaya'     => $totalBiaya,
                        'status'          => $this->deriveServiceStatus($resolvedParts),
                        'tanggal_service' => $request->tanggal_service,
                        'bukti_pembayaran' => $buktiBayar,
                        'status_approval' => 'pending',
                        'is_request'      => true,
                    ]);
                }

                // Simpan semua parts sebagai pending (no duplicate check)
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
                        'is_request'          => true,
                        'status_approval'     => 'pending',
                    ]);
                }

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
            ->with('success', 'Request part berhasil dikirim dan menunggu approval.');
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

    public function store(Request $request)
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
        ]);

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
        $movedFiles      = [];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $request, $kendaraan, $totalBiaya, $partStatuses, $buktiBayarMeta, $attachmentsMeta,
                $resolvedParts, &$movedFiles
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
            });
        } catch (\Throwable $e) {
            foreach ($movedFiles as $f) { if (file_exists($f)) unlink($f); }
            throw $e;
        }

        // Build success message - include duplicate warning if any
        $successMsg = 'Data service berhasil ditambahkan.';
        if (!empty($duplicateParts)) {
            $duplicateList = collect($duplicateParts)->pluck('label')->join(', ');
            $count = count($duplicateParts);
            $successMsg .= " {$count} part tidak ditambahkan karena sudah ada: {$duplicateList}. Gunakan Request Part untuk part tersebut.";
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
     * Update status per-part (Proses ↔ Terpasang), lalu recalculate header ServiceHistory.
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

        // Jika part ini adalah request part, wajib sudah di-approve terlebih dahulu
        if ($part->is_request && $part->status_approval !== 'approved') {
            return back()->with('error', 'Status part tidak bisa diubah sebelum part ini disetujui oleh superadmin.');
        }

        $part->update(['status' => $request->status]);

        // Recalculate header status dari semua parts di service history ini
        $sh = ServiceHistory::with('parts')->find($part->service_history_id);
        if ($sh) {
            $adaProses = $sh->parts->contains('status', 'Proses');
            $newStatus = $adaProses ? 'proses' : 'selesai';
            // Jangan override jika ada part Limit dan tidak ada Proses
            if (!$adaProses && $sh->parts->contains('status', 'Limit')) {
                $newStatus = 'limit';
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
     * Tandai service_history dari pengadaan sebagai Selesai (Terpasang).
     * Hanya bisa dilakukan superadmin pada service yang status_approval = approved dan status = proses.
     */
    public function terpasang(int $id)
    {
        if (auth()->user()->role !== 'superadmin') {
            return redirect()->back()->with('error', 'Tidak memiliki izin.');
        }

        $sh = ServiceHistory::findOrFail($id);

        if ($sh->status !== 'proses' || $sh->status_approval !== 'approved') {
            return redirect()->back()->with('error', 'Service tidak bisa ditandai Terpasang dalam kondisi saat ini.');
        }

        $sh->update(['status' => 'selesai']);

        return redirect()->back()->with('success', 'Service berhasil ditandai sebagai Selesai (Terpasang).');
    }
}
