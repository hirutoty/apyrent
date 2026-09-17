<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceIncident;
use App\Models\ServiceIncidentPart;
use App\Models\ServiceCategory;
use App\Models\Kendaraan;
use App\Models\Attachment;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Services\PengeluaranInterceptorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ServiceIncidentController extends Controller
{
    // =========================================================================
    // INDEX
    // =========================================================================

    public function index(Request $request)
    {
        $bulan       = $request->bulan ?? now()->format('Y-m');
        $kendaraanId = $request->kendaraan_id;

        // Hanya tampilkan yang sudah melewati PO (bukan Pending di PO)
        // Sama dengan logika GpsKendaraanController::index()
        $data = ServiceIncident::with([
                'kendaraan.jenis',
                'attachments',
                'parts.category',
                'parts.supplier',
            ])
            ->whereNotNull('persetujuan')
            ->where('persetujuan', '!=', 'Pending')
            ->where(function ($q) {
                $q->where('persetujuan', '!=', 'Ditolak')
                  ->orWhereNotNull('pembayaran_id');
            })
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service,'%Y-%m') = ?", [$bulan]))
            ->when($kendaraanId, fn($q) => $q->where('kendaraan_id', $kendaraanId))
            ->latest()
            ->paginate($request->per_page ?? 50)
            ->withQueryString();

        $kendaraan  = Kendaraan::orderBy('merk')->get();
        $categories = ServiceCategory::orderBy('nama')->get();

        $totalService = $data->total();
        $totalBiaya   = ServiceIncident::whereNotNull('persetujuan')
            ->where('persetujuan', '!=', 'Pending')
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service,'%Y-%m') = ?", [$bulan]))
            ->when($kendaraanId, fn($q) => $q->where('kendaraan_id', $kendaraanId))
            ->sum('total_biaya');
        $totalProses  = $data->where('status', 'proses')->count();
        $totalSelesai = $data->where('status', 'selesai')->count();

        return view('admin.service.service_incident', compact(
            'data', 'kendaraan', 'categories', 'bulan',
            'totalService', 'totalBiaya', 'totalProses', 'totalSelesai'
        ));
    }

    // =========================================================================
    // CREATE
    // =========================================================================

    public function create()
    {
        $kendaraan  = Kendaraan::orderBy('merk')->get();
        $categories = ServiceCategory::orderBy('nama')->get();
        $suppliers  = Supplier::orderBy('nama_supplier')->get();

        return view('admin.service.service_incident_create', compact('kendaraan', 'categories', 'suppliers'));
    }

    // =========================================================================
    // STORE — simpan ke PurchaseOrder (alur PO → Pembayaran)
    // =========================================================================

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'                 => 'required|exists:kendaraan,id',
            'tanggal_service'              => 'required|date',
            'kilometer'                    => 'required|integer|min:0',
            'keluhan'                      => 'nullable|string',
            'total_biaya_override'         => 'nullable|numeric|min:0',
            'parts'                        => 'required|array|min:1',
            'parts.*.nama_part'            => 'required|string|max:255',
            'parts.*.category_id'          => 'nullable',
            'parts.*.nama_category_baru'   => 'nullable|string|max:100',
            'parts.*.part_number'          => 'nullable|string|max:100',
            'parts.*.serial_number'        => 'nullable|string|max:100',
            'parts.*.posisi'               => 'nullable|string|max:100',
            'parts.*.tgl_pasang'           => 'required|date',
            'parts.*.kilometer_pasang'     => 'nullable|integer|min:0',
            'parts.*.kondisi'              => 'nullable|in:Baik,Rusak,Perlu Ganti',
            'parts.*.biaya'                => 'nullable|numeric|min:0',
            'parts.*.bukti'                => 'nullable|array',
            'parts.*.bukti.*'              => 'file|mimes:jpg,jpeg,png,mp4,mov',
            'parts.*.supplier_id'          => 'nullable|exists:supplier,id',
            'parts.*.nama_rekening'        => 'nullable|string|max:150',
            'parts.*.nama_bank'            => 'nullable|string|max:100',
            'parts.*.no_rekening'          => 'nullable|string|max:50',
            'bukti_attachment'             => 'nullable|array',
            'bukti_attachment.*'           => 'file|max:5120',
        ]);

        $kendaraan    = Kendaraan::findOrFail($request->kendaraan_id);
        $nopol        = $kendaraan->nopol ?? '-';
        $merk         = $kendaraan->merk  ?? '-';

        // ── Slug keterangan otomatis: servis insiden-nopol ────────────────
        $keterangan = 'servis insiden-' . $nopol;

        $resolvedParts = $this->resolvePartsCategory($request->parts ?? []);

        $sumBiaya   = collect($resolvedParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));
        $totalBiaya = filled($request->total_biaya_override) && (int)$request->total_biaya_override > 0
            ? (int)$request->total_biaya_override
            : $sumBiaya;

        // ── Siapkan source_data untuk PurchaseOrder ────────────────────────
        $partsForPO = array_map(fn($p) => [
            'nama_part'          => $p['nama_part'] ?? '',
            'category_id'        => $p['category_id'] ?? null,
            'nama_category_baru' => $p['nama_category_baru'] ?? null,
            'part_number'        => $p['part_number'] ?? null,
            'serial_number'      => $p['serial_number'] ?? null,
            'posisi'             => $p['posisi'] ?? null,
            'tgl_pasang'         => $p['tgl_pasang'] ?? now()->toDateString(),
            'kilometer_pasang'   => $p['kilometer_pasang'] ?? $request->kilometer,
            'kondisi'            => $p['kondisi'] ?? 'Perlu Ganti',
            'status'             => 'Proses',
            'biaya'              => (int)($p['biaya'] ?? 0),
            'supplier_id'        => $p['supplier_id'] ?? null,
            'nama_rekening'      => $p['nama_rekening'] ?? null,
            'nama_bank'          => $p['nama_bank'] ?? null,
            'no_rekening'        => $p['no_rekening'] ?? null,
        ], $resolvedParts);

        $sourceData = [
            'kendaraan_id'         => $request->kendaraan_id,
            'tanggal_service'      => $request->tanggal_service,
            'kilometer'            => $request->kilometer,
            'keluhan'              => $request->keluhan,
            'keterangan'           => $keterangan,
            'total_biaya_override' => $totalBiaya,
            'parts'                => $partsForPO,
        ];

        // ── Buat record ServiceIncident dengan status Pending ──────────────
        $serviceIncident = ServiceIncident::create([
            'kendaraan_id'    => $request->kendaraan_id,
            'keluhan'         => $request->keluhan,
            'keterangan'      => $keterangan,
            'kilometer'       => $request->kilometer,
            'total_biaya'     => $totalBiaya,
            'status'          => 'tidak_aktif',
            'tanggal_service' => $request->tanggal_service,
            'persetujuan'     => 'Pending',
        ]);

        // ── Simpan ke PurchaseOrder via interceptor ────────────────────────
        $interceptor = app(PengeluaranInterceptorService::class);

        $po = PurchaseOrder::create([
            'tanggal_po'              => now()->toDateString(),
            'vendor'                  => $merk . ' ' . $nopol,
            'total_barang'            => count($partsForPO),
            'total_harga'             => $totalBiaya,
            'status_po'               => 'Pending',
            'catatan'                 => $request->keluhan,
            'keterangan'              => $keterangan,
            'source_type'             => 'service_incident',
            'source_data'             => array_merge($sourceData, [
                'service_incident_id' => $serviceIncident->id,
            ]),
            'status'                  => 'Pending',
            'can_edit'                => false,
            'terakhir_diajukan'       => now(),
        ]);

        // Link PO ke record incident
        $serviceIncident->update(['purchase_order_id' => $po->id]);

        // ── Upload temporary files (bukti per-part + attachments) ──────────
        $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $po->id, 'purchase_order');

        // Proses bukti per-part dari parts[idx][bukti][]
        $partTempFiles = [];
        foreach ($resolvedParts as $idx => $p) {
            $fileKey = "parts.{$idx}.bukti";
            if ($request->hasFile($fileKey)) {
                $files = $request->file($fileKey);
                if (!is_array($files)) $files = [$files];
                foreach ($files as $fi => $file) {
                    if (!$file->isValid()) continue;
                    $ts         = time();
                    $storedName = "{$ts}_{$idx}_{$fi}_{$file->getClientOriginalName()}";
                    $tempDir    = "purchase_order/temp/{$po->id}/parts/{$idx}/bukti";
                    $path       = $file->storeAs($tempDir, $storedName, 'public');
                    $partTempFiles[$idx]['bukti'][] = [
                        'original_name' => $file->getClientOriginalName(),
                        'stored_name'   => $storedName,
                        'path'          => $path,
                        'full_path'     => storage_path('app/public/' . $path),
                        'size'          => $file->getSize(),
                        'extension'     => $file->getClientOriginalExtension(),
                    ];
                }
            }
        }

        if (!empty($partTempFiles)) {
            $uploadedFiles['parts'] = $partTempFiles;
        }

        // Update source_data dengan info temp files
        $updatedSource              = $po->source_data;
        $updatedSource['temp_files'] = $uploadedFiles;
        $po->update(['source_data' => $updatedSource]);

        return redirect()
            ->route('purchase-order.index', ['status' => 'Pending'])
            ->with('success', "Service Incident ({$nopol}) berhasil diajukan ke Purchase Order. Menunggu approval Superadmin.");
    }

    // =========================================================================
    // UPDATE STATUS SERVICE
    // =========================================================================

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:proses,selesai']);

        $incident = ServiceIncident::findOrFail($id);
        $incident->update(['status' => $request->status]);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    // =========================================================================
    // UPDATE STATUS PER-PART
    // =========================================================================

    public function updatePartStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Terpasang,Proses']);

        $part = ServiceIncidentPart::findOrFail($id);

        if ($part->status === 'Terpasang') {
            return back()->with('error', 'Part yang sudah Terpasang tidak bisa diubah statusnya.');
        }

        if ($part->persetujuan !== 'Disetujui') {
            return back()->with('error', 'Status part tidak bisa diubah sebelum disetujui.');
        }

        $updateData = ['status' => $request->status];
        if ($request->status === 'Terpasang' && $part->status === 'tidak_aktif') {
            $updateData['kondisi'] = 'Baik';
        }

        $part->update($updateData);

        // Recalculate header status
        $incident = ServiceIncident::with('parts')->find($part->service_incident_id);
        if ($incident) {
            $adaProses = $incident->parts->contains(fn($p) => $p->status === 'Proses');
            $incident->update(['status' => $adaProses ? 'proses' : 'selesai']);
        }

        return back()->with('success', 'Status part berhasil diperbarui.');
    }

    // =========================================================================
    // APPROVE PART (superadmin only)
    // =========================================================================

    public function approvePart($id)
    {
        $part = ServiceIncidentPart::findOrFail($id);

        if ($part->persetujuan !== 'Pending') {
            return back()->with('error', 'Part ini sudah diproses sebelumnya.');
        }

        $part->update([
            'persetujuan' => 'Disetujui',
        ]);

        return back()->with('success', 'Part "' . $part->nama_part . '" berhasil disetujui.');
    }

    // =========================================================================
    // REJECT PART (superadmin only)
    // =========================================================================

    public function rejectPart($id)
    {
        $part = ServiceIncidentPart::findOrFail($id);

        if ($part->persetujuan !== 'Pending') {
            return back()->with('error', 'Part ini sudah diproses sebelumnya.');
        }

        $part->update([
            'persetujuan' => 'Ditolak',
        ]);

        return back()->with('success', 'Part "' . $part->nama_part . '" telah ditolak.');
    }

    // =========================================================================
    // DELETE PART BUKTI
    // =========================================================================

    public function deletePartBukti(Request $request, $id)
    {
        $request->validate(['file_path' => 'required|string']);

        $part     = ServiceIncidentPart::findOrFail($id);
        $path     = $request->file_path;
        $buktiList = $part->bukti ?? [];

        $buktiList = array_values(array_filter($buktiList, fn($f) => ($f['path'] ?? '') !== $path));

        $fullPath = public_path($path);
        if ($path && file_exists($fullPath)) {
            unlink($fullPath);
        }

        $part->update(['bukti' => !empty($buktiList) ? $buktiList : null]);

        return response()->json(['success' => true, 'message' => 'File bukti berhasil dihapus']);
    }

    // =========================================================================
    // DESTROY ATTACHMENT
    // =========================================================================

    public function destroyAttachment($id)
    {
        $attachment = Attachment::where('relation_type', 'service_incident')->findOrFail($id);
        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }
        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus.');
    }

    // =========================================================================
    // DESTROY
    // =========================================================================

    public function destroy($id)
    {
        $incident = ServiceIncident::findOrFail($id);

        foreach ($incident->attachments as $att) {
            if (file_exists(public_path($att->file_path))) unlink(public_path($att->file_path));
            $att->delete();
        }

        foreach ($incident->parts as $part) {
            if ($part->bukti) {
                foreach ($part->bukti as $file) {
                    $filePath = public_path($file['path'] ?? '');
                    if (file_exists($filePath)) unlink($filePath);
                }
            }
        }

        if ($incident->bukti_pembayaran && file_exists(public_path($incident->bukti_pembayaran))) {
            unlink(public_path($incident->bukti_pembayaran));
        }

        $incident->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }

    // =========================================================================
    // TERPASANG (bulk — semua part Disetujui di service ini)
    // =========================================================================

    public function terpasang($id)
    {
        $incident = ServiceIncident::with('parts')->findOrFail($id);

        $partsTidakAktif = $incident->parts
            ->where('persetujuan', 'Disetujui')
            ->where('status', 'tidak_aktif');

        if ($partsTidakAktif->isEmpty()) {
            return back()->with('error', 'Tidak ada part yang perlu ditandai Terpasang.');
        }

        foreach ($partsTidakAktif as $part) {
            $part->update(['status' => 'Terpasang', 'kondisi' => 'Aktif']);
        }

        $incident->refresh();
        $adaProses = $incident->parts->contains(fn($p) => $p->status === 'Proses');
        $incident->update(['status' => $adaProses ? 'proses' : 'selesai']);

        return back()->with('success', 'Part berhasil ditandai sebagai Terpasang.');
    }

    // =========================================================================
    // RESUBMIT — ajukan ulang dari PO yang ditolak
    // POST /admin/service-incident/{poId}/resubmit
    // =========================================================================

    public function resubmit(Request $request, $poId)
    {
        $po = PurchaseOrder::where('source_type', 'service_incident')
            ->where('can_edit', true)
            ->where('status', 'Ditolak')
            ->findOrFail($poId);

        $request->validate([
            'kendaraan_id'                 => 'required|exists:kendaraan,id',
            'tanggal_service'              => 'required|date',
            'kilometer'                    => 'required|integer|min:0',
            'keluhan'                      => 'nullable|string',
            'total_biaya_override'         => 'nullable|numeric|min:0',
            'parts'                        => 'required|array|min:1',
            'parts.*.nama_part'            => 'required|string|max:255',
            'parts.*.category_id'          => 'nullable',
            'parts.*.nama_category_baru'   => 'nullable|string|max:100',
            'parts.*.part_number'          => 'nullable|string|max:100',
            'parts.*.serial_number'        => 'nullable|string|max:100',
            'parts.*.posisi'               => 'nullable|string|max:100',
            'parts.*.tgl_pasang'           => 'required|date',
            'parts.*.kilometer_pasang'     => 'nullable|integer|min:0',
            'parts.*.kondisi'              => 'nullable|in:Baik,Rusak,Perlu Ganti',
            'parts.*.biaya'                => 'nullable|numeric|min:0',
            'parts.*.bukti'                => 'nullable|array',
            'parts.*.bukti.*'              => 'file|mimes:jpg,jpeg,png,mp4,mov',
            'parts.*.supplier_id'          => 'nullable|exists:supplier,id',
            'parts.*.nama_rekening'        => 'nullable|string|max:150',
            'parts.*.nama_bank'            => 'nullable|string|max:100',
            'parts.*.no_rekening'          => 'nullable|string|max:50',
            'bukti_attachment'             => 'nullable|array',
            'bukti_attachment.*'           => 'file|max:5120',
        ]);

        $kendaraan  = Kendaraan::findOrFail($request->kendaraan_id);
        $nopol      = $kendaraan->nopol ?? '-';
        $keterangan = 'servis insiden-' . $nopol;

        $resolvedParts = $this->resolvePartsCategory($request->parts ?? []);

        $sumBiaya   = collect($resolvedParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));
        $totalBiaya = filled($request->total_biaya_override) && (int)$request->total_biaya_override > 0
            ? (int)$request->total_biaya_override
            : $sumBiaya;

        $partsForPO = array_map(fn($p) => [
            'nama_part'          => $p['nama_part'] ?? '',
            'category_id'        => $p['category_id'] ?? null,
            'nama_category_baru' => $p['nama_category_baru'] ?? null,
            'part_number'        => $p['part_number'] ?? null,
            'serial_number'      => $p['serial_number'] ?? null,
            'posisi'             => $p['posisi'] ?? null,
            'tgl_pasang'         => $p['tgl_pasang'] ?? now()->toDateString(),
            'kilometer_pasang'   => $p['kilometer_pasang'] ?? $request->kilometer,
            'kondisi'            => $p['kondisi'] ?? 'Perlu Ganti',
            'status'             => 'Proses',
            'biaya'              => (int)($p['biaya'] ?? 0),
            'supplier_id'        => $p['supplier_id'] ?? null,
            'nama_rekening'      => $p['nama_rekening'] ?? null,
            'nama_bank'          => $p['nama_bank'] ?? null,
            'no_rekening'        => $p['no_rekening'] ?? null,
        ], $resolvedParts);

        // Ambil service_incident_id lama dari source_data PO
        $oldSourceData   = $po->source_data ?? [];
        $incidentId      = $oldSourceData['service_incident_id'] ?? null;

        $newData = [
            'kendaraan_id'         => $request->kendaraan_id,
            'tanggal_service'      => $request->tanggal_service,
            'kilometer'            => $request->kilometer,
            'keluhan'              => $request->keluhan,
            'keterangan'           => $keterangan,
            'total_biaya_override' => $totalBiaya,
            'parts'                => $partsForPO,
            'service_incident_id'  => $incidentId, // preserve agar tidak kehilangan link
        ];

        $approvalService = app(\App\Services\PurchaseOrderApprovalService::class);

        try {
            $po = $approvalService->resubmit($po, $newData, $request);

            // Update juga record ServiceIncident header agar data terbaru tersimpan
            if ($incidentId) {
                ServiceIncident::where('id', $incidentId)->update([
                    'kendaraan_id'    => $request->kendaraan_id,
                    'keluhan'         => $request->keluhan,
                    'kilometer'       => $request->kilometer,
                    'total_biaya'     => $totalBiaya,
                    'tanggal_service' => $request->tanggal_service,
                    'keterangan'      => $keterangan,
                    'persetujuan'     => 'Pending',
                    'pembayaran_id'   => null,
                ]);
            }

            // Upload bukti per-part ke temp storage (sama seperti store())
            $interceptor   = app(PengeluaranInterceptorService::class);
            $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $po->id, 'purchase_order');

            $partTempFiles = [];
            foreach ($resolvedParts as $idx => $p) {
                $fileKey = "parts.{$idx}.bukti";
                if ($request->hasFile($fileKey)) {
                    $files = $request->file($fileKey);
                    if (!is_array($files)) $files = [$files];
                    foreach ($files as $fi => $file) {
                        if (!$file->isValid()) continue;
                        $ts         = time();
                        $storedName = "{$ts}_{$idx}_{$fi}_{$file->getClientOriginalName()}";
                        $tempDir    = "purchase_order/temp/{$po->id}/parts/{$idx}/bukti";
                        $path       = $file->storeAs($tempDir, $storedName, 'public');
                        $partTempFiles[$idx]['bukti'][] = [
                            'original_name' => $file->getClientOriginalName(),
                            'stored_name'   => $storedName,
                            'path'          => $path,
                            'full_path'     => storage_path('app/public/' . $path),
                            'size'          => $file->getSize(),
                            'extension'     => $file->getClientOriginalExtension(),
                        ];
                    }
                }
            }

            if (!empty($partTempFiles)) {
                $uploadedFiles['parts'] = $partTempFiles;
            }

            // Merge temp_files ke source_data PO
            $updatedSource               = $po->source_data;
            $updatedSource['temp_files'] = $uploadedFiles;
            $po->update(['source_data' => $updatedSource]);

            return redirect()
                ->route('purchase-order.index', ['status' => 'Pending'])
                ->with('success', "Service Incident ({$nopol}) berhasil diajukan ulang. Menunggu approval Superadmin.");

        } catch (\Exception $e) {
            \Log::error('Error resubmit service incident PO #' . $poId . ': ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // AJUKAN ULANG — load data untuk modal (dari Pembayaran yang ditolak)
    // GET /admin/service-incident/{id}/ajukan-ulang
    // =========================================================================

    public function ajukanUlang(Request $request, $id)
    {
        $incident = ServiceIncident::with(['kendaraan', 'parts.category', 'parts.supplier'])->findOrFail($id);

        if ($incident->persetujuan !== 'Ditolak' || !$incident->pembayaran_id) {
            return response()->json(['success' => false, 'message' => 'Record ini tidak dapat diajukan ulang.'], 422);
        }

        $pembayaran = \App\Models\Pembayaran::with('latestApproval')->find($incident->pembayaran_id);
        $catatan    = $pembayaran?->latestApproval?->catatan ?? $pembayaran?->catatan ?? null;

        $sourceData = $pembayaran ? ($pembayaran->source_data ?? []) : [];
        $tempFiles  = $sourceData['temp_files'] ?? [];

        // Kumpulkan info parts dari model + temp files
        $parts = $incident->parts->map(function ($part, $idx) use ($tempFiles) {
            $partTempBukti = $tempFiles['parts'][$idx]['bukti'] ?? [];
            $buktiExisting = [];
            foreach ($partTempBukti as $tf) {
                $url = isset($tf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($tf['path']) : null;
                if ($url) {
                    $buktiExisting[] = [
                        'path'          => $tf['path'],
                        'original_name' => $tf['original_name'] ?? basename($tf['path']),
                        'extension'     => $tf['extension'] ?? '',
                        'url'           => $url,
                    ];
                }
            }
            return [
                'id'               => $part->id,
                'nama_part'        => $part->nama_part,
                'category_id'      => $part->category_id,
                'category_nama'    => $part->category?->nama ?? '-',
                'biaya'            => $part->biaya,
                'supplier_id'      => $part->supplier_id,
                'supplier_nama'    => $part->supplier?->nama_supplier ?? '-',
                'nama_bank'        => $part->nama_bank,
                'no_rekening'      => $part->no_rekening,
                'nama_rekening'    => $part->nama_rekening,
                'bukti_existing'   => $buktiExisting,
            ];
        })->values();

        return response()->json([
            'success'            => true,
            'id'                 => $incident->id,
            'pembayaran_id'      => $incident->pembayaran_id,
            'kendaraan'          => ($incident->kendaraan->nopol ?? '-') . ' — ' . ($incident->kendaraan->merk ?? ''),
            'tanggal_service'    => $incident->tanggal_service,
            'kilometer'          => $incident->kilometer,
            'keluhan'            => $incident->keluhan,
            'catatan_penolakan'  => $catatan,
            'parts'              => $parts,
        ]);
    }

    // =========================================================================
    // AJUKAN ULANG SUBMIT — proses resubmit dari Pembayaran yang ditolak
    // POST /admin/service-incident/{id}/ajukan-ulang
    // =========================================================================

    public function ajukanUlangSubmit(Request $request, $id)
    {
        $incident = ServiceIncident::findOrFail($id);

        if ($incident->persetujuan !== 'Ditolak' || !$incident->pembayaran_id) {
            return response()->json(['success' => false, 'message' => 'Record ini tidak dapat diajukan ulang.'], 422);
        }

        $pembayaran = \App\Models\Pembayaran::find($incident->pembayaran_id);
        if (!$pembayaran || $pembayaran->status !== 'Ditolak') {
            return response()->json(['success' => false, 'message' => 'Pembayaran bukan status Ditolak.'], 422);
        }

        $request->validate([
            'parts'                    => 'nullable|array',
            'parts.*.nama_part'        => 'nullable|string|max:255',
            'parts.*.biaya'            => 'nullable|numeric|min:0',
            'parts.*.supplier_id'      => 'nullable|exists:supplier,id',
            'parts.*.nama_bank'        => 'nullable|string|max:100',
            'parts.*.no_rekening'      => 'nullable|string|max:50',
            'parts.*.nama_rekening'    => 'nullable|string|max:150',
            'catatan'                  => 'nullable|string|max:1000',
        ]);

        try {
            // Update source_data Pembayaran dengan data parts terbaru
            $sourceData = $pembayaran->source_data ?? [];
            $parts      = $sourceData['parts'] ?? [];

            if ($request->filled('parts')) {
                foreach ($request->input('parts') as $idx => $partInput) {
                    if (isset($parts[$idx])) {
                        $parts[$idx]['biaya']         = (int)($partInput['biaya'] ?? $parts[$idx]['biaya'] ?? 0);
                        $parts[$idx]['supplier_id']   = $partInput['supplier_id'] ?? $parts[$idx]['supplier_id'] ?? null;
                        $parts[$idx]['nama_bank']      = $partInput['nama_bank'] ?? $parts[$idx]['nama_bank'] ?? null;
                        $parts[$idx]['no_rekening']    = $partInput['no_rekening'] ?? $parts[$idx]['no_rekening'] ?? null;
                        $parts[$idx]['nama_rekening']  = $partInput['nama_rekening'] ?? $parts[$idx]['nama_rekening'] ?? null;
                    }
                }
                $sourceData['parts'] = $parts;
            }

            // Hitung ulang nominal
            $nominalBaru = collect($parts)->sum(fn($p) => (int)($p['biaya'] ?? 0));
            if (!empty($sourceData['total_biaya_override']) && (int)$sourceData['total_biaya_override'] > 0) {
                // Jika ada override, ikuti override; jika parts berubah, update override juga
                $sourceData['total_biaya_override'] = $nominalBaru;
            }

            // Update Pembayaran → kembali ke Diajukan
            $pembayaran->update([
                'source_data'       => $sourceData,
                'nominal'           => $nominalBaru,
                'status'            => 'Diajukan',
                'catatan'           => $request->catatan ?? $pembayaran->catatan,
                'terakhir_diajukan' => now(),
            ]);

            // Reset ServiceIncident ke Pending agar kelihatan sedang diproses lagi
            $incident->update([
                'persetujuan' => 'Diajukan ke Pembayaran',
                'total_biaya' => $nominalBaru,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service Incident berhasil diajukan ulang ke Pembayaran.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error ajukanUlangSubmit service incident #' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

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

    private function deriveStatus(array $parts): string
    {
        foreach ($parts as $part) {
            if (($part['status'] ?? '') === 'Proses') return 'proses';
        }
        return 'selesai';
    }

    private function hitungTanggalLimit(Carbon $tglPasang, int $nilai, string $satuan): Carbon
    {
        return match ($satuan) {
            'hari'   => (clone $tglPasang)->addDays($nilai),
            'minggu' => (clone $tglPasang)->addWeeks($nilai),
            'tahun'  => (clone $tglPasang)->addYears($nilai),
            default  => (clone $tglPasang)->addMonths($nilai),
        };
    }

    private function prepBuktiBayar(Request $request): ?array
    {
        if (!$request->hasFile('bukti_pembayaran')) return null;
        $file     = $request->file('bukti_pembayaran');
        $filename = time() . '_' . $file->getClientOriginalName();
        return [
            'file'        => $file,
            'filename'    => $filename,
            'destination' => public_path('bukti_pembayaran'),
            'path'        => 'bukti_pembayaran/' . $filename,
        ];
    }

    private function prepAttachments(Request $request): array
    {
        if (!$request->hasFile('bukti_attachment')) return [];
        $pathDir = public_path('service-incident/attachments');
        $result  = [];
        foreach ($request->file('bukti_attachment') as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $result[] = [
                'file'        => $file,
                'filename'    => $filename,
                'destination' => $pathDir,
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => 'service-incident/attachments/' . $filename,
                'file_type'   => $file->getClientOriginalExtension(),
                'file_size'   => $file->getSize(),
            ];
        }
        return $result;
    }

    private function uploadPartBukti(Request $request, int $partIndex): array
    {
        $items       = [];
        $fileKey     = "parts.{$partIndex}.bukti";

        if (!$request->hasFile($fileKey)) return $items;

        $destination = public_path('service-incident-parts');
        if (!file_exists($destination)) mkdir($destination, 0777, true);

        $files = $request->file($fileKey);
        if (!is_array($files)) $files = [$files];

        foreach ($files as $file) {
            if (!$file->isValid()) continue;
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $filename);
            $items[] = [
                'path' => 'service-incident-parts/' . $filename,
                'name' => $file->getClientOriginalName(),
                'type' => $file->getClientOriginalExtension(),
            ];
        }

        return $items;
    }
}
