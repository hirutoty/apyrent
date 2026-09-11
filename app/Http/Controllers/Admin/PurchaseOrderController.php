<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bukubesar;
use App\Models\Keuangan;
use App\Models\PurchaseOrder;
use App\Services\PengeluaranInterceptorService;
use App\Services\PurchaseOrderApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    protected $approvalService;

    public function __construct(PurchaseOrderApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index(Request $request)
    {
        // Tab filter
        $statusFilter = $request->input('status', 'Pending');
        
        $query = PurchaseOrder::with(['approver', 'pembayaran'])
            ->where('status', $statusFilter)
            ->latest();

        $data = $query->paginate(15)->withQueryString();

        // Statistics
        $statusStats = PurchaseOrder::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalPO       = PurchaseOrder::count();
        $totalApproved = PurchaseOrder::where('status', 'Disetujui')->count();
        $totalPending  = PurchaseOrder::where('status', 'Pending')->count();
        $totalRejected = PurchaseOrder::where('status', 'Ditolak')->count();

        // Legacy stats (for old status_po field)
        $totalClosed   = PurchaseOrder::where('status_po', 'Closed')->count();

        return view('admin.purchaseo.index', compact(
            'data',
            'statusStats',
            'statusFilter',
            'totalPO',
            'totalApproved',
            'totalPending',
            'totalRejected',
            'totalClosed'
        ));
    }

    /**
     * Get PO detail untuk modal (AJAX)
     */
    public function detail($id)
    {
        try {
            $po = PurchaseOrder::with(['approver', 'pembayaran'])->findOrFail($id);
            $sourceData = $po->source_data ?? [];
            
            // Build detail berdasarkan source_type
            $details = $this->buildDetailBySourceType($po->source_type, $sourceData);
            
            return response()->json([
                'success' => true,
                'po' => [
                    'po_id' => $po->po_id,
                    'source_type' => $po->source_type,
                    'vendor' => $po->vendor,
                    'total_barang' => $po->total_barang,
                    'total_harga' => $po->total_harga,
                    'tanggal_po' => $po->tanggal_po ? $po->tanggal_po->format('d M Y') : '-',
                    'status' => $po->status,
                    'catatan' => $po->catatan,
                    'catatan_approval' => $po->catatan_approval,
                    'disetujui_oleh' => $po->approver ? $po->approver->nama : null,
                    'tanggal_persetujuan' => $po->tanggal_persetujuan ? $po->tanggal_persetujuan->format('d M Y H:i') : null,
                    'pembayaran_no_pr' => $po->pembayaran ? $po->pembayaran->no_pr : null,
                ],
                'details' => $details,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PO tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Build detail content berdasarkan source type
     */
    protected function buildDetailBySourceType($sourceType, $sourceData)
    {
        if ($sourceType === 'gps') {
            return $this->buildGpsDetails($sourceData);
        }
        
        // For other types, return raw data
        return [
            'type' => 'raw',
            'data' => $sourceData,
        ];
    }

    /**
     * Build GPS-specific details
     */
    protected function buildGpsDetails($sourceData)
    {
        $gpsItems    = $sourceData['gps_items'] ?? [];
        $recordIds   = $sourceData['gps_record_ids'] ?? [];
        $kendaraanId = $sourceData['kendaraan_id'] ?? null;
        $kendaraan   = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;

        $items = [];
        foreach ($gpsItems as $idx => $item) {
            $gps = isset($item['gps_id']) ? \App\Models\Gps::find($item['gps_id']) : null;

            // Ambil lampiran dari DB berdasarkan gps_record_id
            $recordId   = $recordIds[$idx] ?? null;
            $lampiran   = [];
            if ($recordId) {
                $attachments = \App\Models\Attachment::where('relation_type', 'gps')
                    ->where('relation_id', $recordId)
                    ->get();
                foreach ($attachments as $att) {
                    $lampiran[] = [
                        'id'        => $att->id,
                        'file_name' => $att->file_name,          // nama asli
                        'file_path' => asset($att->file_path),   // URL publik
                        'file_type' => $att->file_type,
                        'file_size' => $att->file_size,
                    ];
                }
            }

            $items[] = [
                'gps_name'    => $gps ? $gps->nama_gps : '-',
                'type'        => $item['type'] ?? '-',
                'biaya_sewa'  => $item['biaya_sewa'] ?? 0,
                'nama_bank'   => $item['nama_bank'] ?? '-',
                'no_rekening' => $item['no_rekening'] ?? '-',
                'nama_pemilik'=> $item['nama_pemilik'] ?? '-',
                'lampiran'    => $lampiran,
            ];
        }

        return [
            'type'         => 'gps',
            'kendaraan'    => [
                'nopol' => $kendaraan ? $kendaraan->nopol : '-',
                'merk'  => $kendaraan ? $kendaraan->merk  : '-',
            ],
            'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? '-',
            'tanggal_habis' => $sourceData['tanggal_habis'] ?? '-',
            'keterangan'    => $sourceData['keterangan'] ?? '-',
            'items'         => $items,
        ];
    }

    /**
     * Approve Purchase Order dan auto-create Pembayaran
     */
    public function approve(Request $request, $id)
    {
        // Only Superadmin can approve
        if (auth()->user()->role !== 'superadmin') {
            return back()->with('error', 'Hanya Superadmin yang dapat menyetujui Purchase Order.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            $po = PurchaseOrder::findOrFail($id);
            
            // Approve PO and auto-create Pembayaran
            $pembayaran = $this->approvalService->approve($po, [
                'catatan' => $request->catatan,
            ]);

            return redirect()
                ->route('purchase-order.index', ['status' => 'Disetujui'])
                ->with('success', "Purchase Order {$po->po_id} telah disetujui. Pembayaran {$pembayaran->no_pr} otomatis dibuat dan menunggu approval.");

        } catch (\Exception $e) {
            \Log::error('Error approving PO #' . $id . ': ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject Purchase Order
     */
    public function reject(Request $request, $id)
    {
        // Only Superadmin can reject
        if (auth()->user()->role !== 'superadmin') {
            return back()->with('error', 'Hanya Superadmin yang dapat menolak Purchase Order.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ], [
            'catatan.required' => 'Catatan penolakan wajib diisi.',
        ]);

        try {
            $po = PurchaseOrder::findOrFail($id);
            
            // Reject PO
            $this->approvalService->reject($po, $request->catatan);

            return redirect()
                ->route('purchase-order.index', ['status' => 'Ditolak'])
                ->with('success', "Purchase Order {$po->po_id} telah ditolak. User dapat melakukan edit dan mengajukan ulang.");

        } catch (\Exception $e) {
            \Log::error('Error rejecting PO #' . $id . ': ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Approve GPS Purchase Order per-item (pilih item mana yang disetujui/ditolak + bukti per item)
     */
    public function approveItems(Request $request, $id)
    {
        if (auth()->user()->role !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Tidak ada akses.'], 403);
        }

        $po = PurchaseOrder::findOrFail($id);

        if (!$po->isPending()) {
            return response()->json(['success' => false, 'message' => 'Purchase Order bukan status Pending.'], 422);
        }

        $items   = $request->input('items', []);
        $catatan = $request->input('catatan', '');

        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada item yang diputuskan.'], 422);
        }

        $sourceData  = $po->source_data ?? [];
        $gpsItems    = $sourceData['gps_items'] ?? [];
        $approvedIdx = [];
        $rejectedIdx = [];

        // Validasi: item yang ditolak wajib ada catatannya
        foreach ($items as $idx => $decision) {
            $action = $decision['action'] ?? null;
            if ($action === 'rejected' && empty(trim($decision['catatan'] ?? ''))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item #' . ((int)$idx + 1) . ' yang ditolak wajib ada alasan penolakan.',
                ], 422);
            }
            if ($action === 'approved') $approvedIdx[] = (int) $idx;
            if ($action === 'rejected') $rejectedIdx[] = (int) $idx;
        }

        if (empty($approvedIdx) && empty($rejectedIdx)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada keputusan yang diberikan.'], 422);
        }

        try {
            \DB::beginTransaction();

            // Simpan bukti bayar per item yang diapprove ke disk
            $buktiDir = public_path('gps/bukti_bayar');
            if (!file_exists($buktiDir)) mkdir($buktiDir, 0777, true);

            $perItemBukti = [];
            foreach ($approvedIdx as $idx) {
                $fileInput = $request->file("items.{$idx}.bukti");
                if ($fileInput) {
                    $filename = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileInput->getClientOriginalName());
                    $fileInput->move($buktiDir, $filename);
                    $perItemBukti[$idx] = 'gps/bukti_bayar/' . $filename;
                }
            }

            $hasApproved = !empty($approvedIdx);
            $hasRejected = !empty($rejectedIdx);

            // Jika semua ditolak → reject PO
            if (!$hasApproved) {
                $po->update([
                    'status'              => 'Ditolak',
                    'disetujui_oleh'      => auth()->id(),
                    'tanggal_persetujuan' => now(),
                    'catatan_approval'    => $catatan ?: collect($items)->pluck('catatan')->filter()->implode('; '),
                    'can_edit'            => true,
                ]);

                // Hapus semua record GPS Pending terkait PO ini
                // Record IDs disimpan di source_data['gps_record_ids'], bukan di dalam tiap item
                $recordIds = $sourceData['gps_record_ids'] ?? [];
                if (!empty($recordIds)) {
                    \App\Models\GpsKendaraan::whereIn('id', $recordIds)
                        ->where('persetujuan', 'Pending')
                        ->delete();
                }

                \DB::commit();
                return response()->json([
                    'success'  => true,
                    'message'  => 'Semua item ditolak. Purchase Order ditolak.',
                    'redirect' => route('purchase-order.index', ['status' => 'Ditolak']),
                ]);
            }

            // Ada item yang diapprove → proses hanya approved items
            $approvedGpsItems = array_values(
                array_filter($gpsItems, fn($item, $idx) => in_array($idx, $approvedIdx), ARRAY_FILTER_USE_BOTH)
            );

            $nominalApproved = collect($approvedGpsItems)->sum(fn($i) => $i['biaya_sewa'] ?? 0);

            $po->update([
                'status'              => 'Disetujui',
                'disetujui_oleh'      => auth()->id(),
                'tanggal_persetujuan' => now(),
                'catatan_approval'    => $catatan,
                'total_harga'         => $nominalApproved,
                'total_barang'        => count($approvedGpsItems),
            ]);

            // Buat Pembayaran hanya dari approved items
            // Simpan item_decisions lengkap (approved + rejected) untuk ditampilkan di UI
            $allGpsItemNames = [];
            foreach ($gpsItems as $idx => $gpsItem) {
                $gpsModel = isset($gpsItem['gps_id']) ? \App\Models\Gps::find($gpsItem['gps_id']) : null;
                $allGpsItemNames[$idx] = [
                    'nama_gps' => $gpsModel->nama_gps ?? '-',
                    'type'     => $gpsItem['type'] ?? '-',
                    'action'   => in_array($idx, $approvedIdx) ? 'approved' : 'rejected',
                    'catatan'  => $items[$idx]['catatan'] ?? null,
                ];
            }

            $approvedSourceData = array_merge($sourceData, [
                'gps_items'      => $approvedGpsItems,
                'item_decisions' => array_values($allGpsItemNames),
            ]);
            $pembayaran = $this->approvalService->approveWithItems($po, $approvedSourceData, $perItemBukti, $catatan, $hasRejected);

            // Item yang ditolak → buat PO baru terpisah dengan status Ditolak
            // agar user dapat melihat di tab "Ditolak" dan mengajukan ulang
            $allRecordIds     = $sourceData['gps_record_ids'] ?? [];
            $rejectedGpsItems = array_values(
                array_filter($gpsItems, fn($item, $idx) => in_array($idx, $rejectedIdx), ARRAY_FILTER_USE_BOTH)
            );

            if (!empty($rejectedGpsItems)) {
                // Kumpulkan GPS record IDs untuk item yang ditolak (pertahankan urutan)
                $rejectedRecordIds = [];
                foreach ($rejectedIdx as $idx) {
                    $recordId = $allRecordIds[$idx] ?? null;
                    if ($recordId) $rejectedRecordIds[] = $recordId;
                }

                // Kumpulkan catatan penolakan per item
                $rejectedCatatan = collect($rejectedIdx)
                    ->map(fn($idx) => $items[$idx]['catatan'] ?? null)
                    ->filter()
                    ->implode('; ');

                // Buat PO baru untuk item yang ditolak (status Ditolak, can_edit=true)
                $rejectedSourceData = array_merge($sourceData, [
                    'gps_items'      => $rejectedGpsItems,
                    'gps_record_ids' => $rejectedRecordIds,
                ]);
                $nominalRejected = collect($rejectedGpsItems)->sum(fn($i) => $i['biaya_sewa'] ?? 0);

                PurchaseOrder::create([
                    'tanggal_po'          => now()->toDateString(),
                    'vendor'              => $po->vendor,
                    'source_type'         => $po->source_type,
                    'source_data'         => $rejectedSourceData,
                    'total_barang'        => count($rejectedGpsItems),
                    'total_harga'         => $nominalRejected,
                    'status'              => 'Ditolak',
                    'status_po'           => 'Pending',
                    'disetujui_oleh'      => auth()->id(),
                    'tanggal_persetujuan' => now(),
                    'catatan_approval'    => $catatan ?: $rejectedCatatan ?: 'Item ditolak dari PO ' . $po->po_id,
                    'can_edit'            => true,
                    'terakhir_diajukan'   => now(),
                ]);

                // Update GPS record yang ditolak: tandai persetujuan = Ditolak
                // Record tetap ada di DB — tidak dihapus, supaya tidak menghalangi resubmit
                if (!empty($rejectedRecordIds)) {
                    \App\Models\GpsKendaraan::whereIn('id', $rejectedRecordIds)
                        ->where('persetujuan', 'Pending')
                        ->update(['persetujuan' => 'Ditolak']);
                }
            }

            \DB::commit();

            $msg = "PO {$po->po_id}: " . count($approvedIdx) . " item disetujui";
            if ($hasRejected) $msg .= ", " . count($rejectedIdx) . " item ditolak";
            $msg .= ". Pembayaran {$pembayaran->no_pr} otomatis dibuat.";

            return response()->json([
                'success'  => true,
                'message'  => $msg,
                'redirect' => route('purchase-order.index', ['status' => 'Disetujui']),
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approveItems PO #' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Resubmit GPS Purchase Order yang ditolak — load data lama ke modal GPS,
     * user edit lalu submit ulang lewat GpsKendaraanController@store dengan edit_purchase_order
     * Route ini hanya mengembalikan data PO untuk diisi ke modal di view.
     */
    public function resubmit(Request $request, $id)
    {
        try {
            $po = PurchaseOrder::findOrFail($id);

            if (!$po->isRejected()) {
                return response()->json(['success' => false, 'message' => 'Hanya PO yang ditolak yang dapat diajukan ulang.'], 422);
            }

            if (!$po->can_edit) {
                return response()->json(['success' => false, 'message' => 'PO ini tidak dapat diedit.'], 422);
            }

            $sourceData = $po->source_data ?? [];
            $gpsItems   = $sourceData['gps_items'] ?? [];
            $kendaraanId = $sourceData['kendaraan_id'] ?? null;
            $kendaraan  = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;

            // Enrich items dengan nama GPS
            $enrichedItems = array_map(function ($item) {
                $gps = isset($item['gps_id']) ? \App\Models\Gps::find($item['gps_id']) : null;
                return array_merge($item, ['nama_gps' => $gps ? $gps->nama_gps : '-']);
            }, $gpsItems);

            return response()->json([
                'success'     => true,
                'po_id'       => $po->id,
                'po_number'   => $po->po_id,
                'catatan'     => $po->catatan_approval,
                'kendaraan_id'   => $kendaraanId,
                'nopol'          => $kendaraan ? $kendaraan->nopol : '-',
                'merk'           => $kendaraan ? $kendaraan->merk : '-',
                'tanggal_bayar'  => $sourceData['tanggal_bayar'] ?? '',
                'tanggal_habis'  => $sourceData['tanggal_habis'] ?? '',
                'keterangan'     => $sourceData['keterangan'] ?? '',
                'gps_items'      => $enrichedItems,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request, PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'tanggal_po'     => 'required|date',
            'vendor'         => 'required|string|max:255',
            'terkait_rfq'    => 'nullable|string|max:255',
            'total_barang'   => 'required|integer|min:1',
            'total_harga'    => 'required|integer|min:0',
            'tanggal_kirim'  => 'nullable|date',
            'tanggal_terima' => 'nullable|date',
            'catatan'        => 'nullable|string',
            'nama_bank'      => 'nullable|string|max:255',
            'no_rekening'    => 'nullable|string|max:100',
            'nama_rekening'  => 'nullable|string|max:255',
            'informasi'      => 'nullable|string',
        ]);

        // Resubmit dari penolakan (untuk manual PO entry, not GPS)
        if ($request->filled('edit_pembayaran')) {
            $pembayaranId = $request->input('edit_pembayaran');
            $interceptor->resubmitToPembayaran($pembayaranId, $request, 'purchase_order');

            return redirect()
                ->route('pembayaran.index')
                ->with('success', 'Pengajuan Purchase Order berhasil diajukan ulang. Menunggu approval.');
        }

        // Intercept & kirim ke Pembayaran (legacy flow for manual PO)
        try {
            $interceptedData = $interceptor->intercept($request, 'purchase_order');
            $pembayaran      = $interceptor->saveToPembayaran($interceptedData, 'purchase_order');

            // Upload temp files jika ada
            $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $pembayaran->id);
            if (!empty($uploadedFiles)) {
                $sourceData               = $pembayaran->source_data;
                $sourceData['temp_files'] = $uploadedFiles;
                $pembayaran->update(['source_data' => $sourceData]);
            }

            return redirect()
                ->route('pembayaran.index')
                ->with('success', 'Purchase Order ' . $pembayaran->no_pr . ' berhasil diajukan ke Pembayaran. Menunggu approval Superadmin.');

        } catch (\Exception $e) {
            \Log::error('Error intercepting Purchase Order: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $this->validateData($request);

        $statusLama = $purchaseOrder->status_po;
        $poId       = $purchaseOrder->id;           // simpan SEBELUM update

        // po_id sengaja tidak diubah saat update
        $purchaseOrder->update($validated);

        // Ambil nilai terbaru dari validated (sudah tersimpan ke DB)
        $poVendor = $validated['vendor'];
        $poHarga  = (int) $validated['total_harga'];

        // Auto-posting ke Keuangan & Buku Besar saat PO pertama kali di-Closed
        if ($validated['status_po'] === 'Closed' && $statusLama !== 'Closed') {
            DB::transaction(function () use ($poId, $poVendor, $poHarga) {
                $kodeJurnal = 'PO-' . $poId;

                if (!Keuangan::where('reference', $kodeJurnal)->exists()) {
                    $lastSaldo = (float) DB::table('keuangans')
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->value('saldo') ?? 0;

                    Keuangan::create([
                        'tanggal'     => now()->toDateString(),
                        'reference'   => $kodeJurnal,
                        'user_id'     => auth()->id(),
                        'kategori'    => 'Pengeluaran',
                        'metode'      => 'Cash',
                        'keterangan'  => 'Purchase Order: ' . $poVendor,
                        'pemasukan'   => 0,
                        'pengeluaran' => $poHarga,
                        'saldo'       => $lastSaldo - $poHarga,
                        'sumber'      => 'auto',
                    ]);
                }

                if (!Bukubesar::where('kode_jurnal', $kodeJurnal)->exists()) {
                    $saldoBB = (float) DB::table('bukubesars')
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->value('saldo') ?? 0;

                    Bukubesar::create([
                        'kode_jurnal' => $kodeJurnal,
                        'transaksi'   => 'Pembelian: ' . $poVendor,
                        'kategori'    => 'Beban',
                        'tanggal'     => now()->toDateString(),
                        'debit'       => $poHarga,
                        'kredit'      => 0,
                        'saldo'       => $saldoBB - $poHarga,
                        'aktivitas'   => 'Operasi',
                        'keterangan'  => 'Auto-posting: PO #' . $poId . ' - ' . $poVendor . ' (Closed)',
                    ]);
                }
            });
        }

        // Jika PO di-reopen dari Closed ke status lain, hapus jurnal & recalculate
        if ($statusLama === 'Closed' && $validated['status_po'] !== 'Closed') {
            DB::transaction(function () use ($poId) {
                $kodeJurnal = 'PO-' . $poId;

                $keuangan = Keuangan::where('reference', $kodeJurnal)->first();
                if ($keuangan) {
                    $keuanganId = $keuangan->id;
                    $keuangan->delete();
                    PaymentsController::recalculateKeuanganSaldo($keuanganId);
                }

                $jurnal = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();
                if ($jurnal) {
                    $jurnalId = $jurnal->id;
                    $jurnal->delete();
                    PaymentsController::recalculateBukubesarSaldo($jurnalId);
                }
            });
        }

        return redirect()->route('purchase-order.index')
            ->with('success', 'Purchase Order berhasil diperbarui.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        // Hanya PO dengan status Pending atau Ditolak yang bisa dihapus
        if (!in_array($purchaseOrder->status, ['Pending', 'Ditolak'])) {
            return back()->with('error', 'Hanya Purchase Order dengan status Pending atau Ditolak yang dapat dihapus.');
        }

        try {
            DB::transaction(function () use ($purchaseOrder) {
                // Delete temp files jika ada
                if (!empty($purchaseOrder->source_data['temp_files'])) {
                    $tempDir = "purchase_order/temp/{$purchaseOrder->id}";
                    \Storage::disk('public')->deleteDirectory($tempDir);
                }

                // Delete GPS records yang linked (jika ada) dengan persetujuan Pending
                if ($purchaseOrder->source_type === 'gps' && !empty($purchaseOrder->source_data['gps_record_ids'])) {
                    \App\Models\GpsKendaraan::whereIn('id', $purchaseOrder->source_data['gps_record_ids'])
                        ->where('persetujuan', 'Pending')
                        ->delete();
                }

                // Hapus jurnal terkait jika PO berstatus Closed (legacy)
                if ($purchaseOrder->status_po === 'Closed') {
                    $kodeJurnal = 'PO-' . $purchaseOrder->id;

                    $keuangan = Keuangan::where('reference', $kodeJurnal)->first();
                    if ($keuangan) {
                        $keuanganId = $keuangan->id;
                        $keuangan->delete();
                        PaymentsController::recalculateKeuanganSaldo($keuanganId);
                    }

                    $jurnal = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();
                    if ($jurnal) {
                        $jurnalId = $jurnal->id;
                        $jurnal->delete();
                        PaymentsController::recalculateBukubesarSaldo($jurnalId);
                    }
                }

                $purchaseOrder->delete();
            });

            return redirect()->route('purchase-order.index')
                ->with('success', 'Purchase Order berhasil dihapus.');

        } catch (\Exception $e) {
            \Log::error('Error deleting PO #' . $purchaseOrder->id . ': ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tanggal_po'     => 'required|date',
            'vendor'         => 'required|string|max:255',
            'terkait_rfq'    => 'nullable|string|max:255',
            'total_barang'   => 'required|integer|min:1',
            'total_harga'    => 'required|integer|min:0',
            'status_po'      => 'required|in:Pending,Approved,Closed',
            'tanggal_kirim'  => 'nullable|date',
            'tanggal_terima' => 'nullable|date',
            'catatan'        => 'nullable|string',
        ]);
    }
}
