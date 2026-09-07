<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bukubesar;
use App\Models\Keuangan;
use App\Models\PurchaseOrder;
use App\Services\PengeluaranInterceptorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $data = PurchaseOrder::latest()->paginate(15)->withQueryString();

        $statusStats = PurchaseOrder::selectRaw('status_po, count(*) as total')->groupBy('status_po')->pluck('total', 'status_po');

        $totalPO       = PurchaseOrder::count();
        $totalApproved = PurchaseOrder::where('status_po', 'Approved')->count();
        $totalPending  = PurchaseOrder::where('status_po', 'Pending')->count();
        $totalClosed   = PurchaseOrder::where('status_po', 'Closed')->count();

        return view('admin.purchaseo.index', compact(
            'data', 'statusStats',
            'totalPO', 'totalApproved', 'totalPending', 'totalClosed'
        ));
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

        // Resubmit dari penolakan
        if ($request->filled('edit_pembayaran')) {
            $pembayaranId = $request->input('edit_pembayaran');
            $interceptor->resubmitToPembayaran($pembayaranId, $request, 'purchase_order');

            return redirect()
                ->route('pembayaran.index')
                ->with('success', 'Pengajuan Purchase Order berhasil diajukan ulang. Menunggu approval.');
        }

        // Intercept & kirim ke Pembayaran
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
        // Hapus jurnal terkait jika PO berstatus Closed
        if ($purchaseOrder->status_po === 'Closed') {
            DB::transaction(function () use ($purchaseOrder) {
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

                $purchaseOrder->delete();
            });
        } else {
            $purchaseOrder->delete();
        }

        return redirect()->route('purchase-order.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
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
