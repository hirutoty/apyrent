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

        $data = ServiceIncident::with([
                'kendaraan.jenis',
                'attachments',
                'parts.category',
                'parts.supplier',
            ])
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service,'%Y-%m') = ?", [$bulan]))
            ->when($kendaraanId, fn($q) => $q->where('kendaraan_id', $kendaraanId))
            ->latest()
            ->paginate($request->per_page ?? 50)
            ->withQueryString();

        $kendaraan  = Kendaraan::orderBy('merk')->get();
        $categories = ServiceCategory::orderBy('nama')->get();

        $totalService = $data->total();
        $totalBiaya   = ServiceIncident::when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service,'%Y-%m') = ?", [$bulan]))
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
            'parts.*.interval_nilai'       => 'required|integer|min:1',
            'parts.*.interval_satuan'      => 'required|in:hari,minggu,bulan,tahun',
            'parts.*.biaya'                => 'nullable|numeric|min:0',
            'parts.*.bukti'                => 'nullable|array',
            'parts.*.bukti.*'              => 'file|mimes:jpg,jpeg,png,mp4,mov',
            'parts.*.keterangan'           => 'nullable|string|max:1000',
            'parts.*.supplier_id'          => 'nullable|exists:supplier,id',
            'parts.*.nama_rekening'        => 'nullable|string|max:150',
            'parts.*.nama_bank'            => 'nullable|string|max:100',
            'parts.*.no_rekening'          => 'nullable|string|max:50',
            'bukti_attachment'             => 'nullable|array',
            'bukti_attachment.*'           => 'file|max:5120',
        ]);

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
            'kondisi'            => $p['kondisi'] ?? 'Baik',
            'status'             => 'Proses',
            'interval_nilai'     => (int)($p['interval_nilai'] ?? 12),
            'interval_satuan'    => $p['interval_satuan'] ?? 'bulan',
            'biaya'              => (int)($p['biaya'] ?? 0),
            'keterangan'         => $p['keterangan'] ?? null,
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
            'total_biaya_override' => $totalBiaya,
            'parts'                => $partsForPO,
        ];

        // ── Simpan ke PurchaseOrder via interceptor ────────────────────────
        $interceptor = app(PengeluaranInterceptorService::class);

        $kendaraan  = Kendaraan::find($request->kendaraan_id);
        $partNames  = collect($partsForPO)->pluck('nama_part')->filter()->take(3)->implode(', ');
        $nopol      = $kendaraan?->nopol ?? '-';
        $merk       = $kendaraan?->merk  ?? '-';

        $po = PurchaseOrder::create([
            'tanggal_po'          => now()->toDateString(),
            'vendor'              => $merk . ' ' . $nopol,
            'total_barang'        => count($partsForPO),
            'total_harga'         => $totalBiaya,
            'status_po'           => 'Pending',
            'catatan'             => $request->keluhan,
            'source_type'         => 'service_incident',
            'source_data'         => $sourceData,
            'status'              => 'Pending',
            'can_edit'            => false,
            'terakhir_diajukan'   => now(),
        ]);

        // ── Upload temporary files (bukti per-part + attachments) ──────────
        $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $po->id, 'purchase_order');

        // Juga proses bukti per-part dari parts[idx][bukti][]
        $partTempFiles = [];
        foreach ($resolvedParts as $idx => $p) {
            $fileKey = "parts.{$idx}.bukti";
            if ($request->hasFile($fileKey)) {
                $files = $request->file($fileKey);
                if (!is_array($files)) $files = [$files];
                foreach ($files as $fi => $file) {
                    if (!$file->isValid()) continue;
                    $ts          = time();
                    $storedName  = "{$ts}_{$idx}_{$fi}_{$file->getClientOriginalName()}";
                    $tempDir     = "purchase_order/temp/{$po->id}/parts/{$idx}/bukti";
                    $path        = $file->storeAs($tempDir, $storedName, 'public');
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
        $sourceData['temp_files'] = $uploadedFiles;
        $po->update(['source_data' => $sourceData]);

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
            $part->update(['status' => 'Terpasang', 'kondisi' => 'Baik']);
        }

        $incident->refresh();
        $adaProses = $incident->parts->contains(fn($p) => $p->status === 'Proses');
        $incident->update(['status' => $adaProses ? 'proses' : 'selesai']);

        return back()->with('success', 'Part berhasil ditandai sebagai Terpasang.');
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
