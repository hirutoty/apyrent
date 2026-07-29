<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bukubesar;
use App\Models\Keuangan;
use App\Models\Purchasero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseroController extends Controller
{
    public function index(Request $request)
    {
        $role = auth()->user()->role;
        $sort = $request->input('sort', 'terbaru');

        $query = Purchasero::query();

        if ($role === 'superadmin') {
            // Superadmin: tab Pending / Diajukan / Disetujui / Ditolak
            $tab = $request->input('tab', 'Diajukan');
            if (in_array($tab, ['Pending', 'Diajukan', 'Disetujui', 'Ditolak'])) {
                $query->where('status', $tab);
            } else {
                $query->whereIn('status', ['Pending', 'Diajukan', 'Disetujui', 'Ditolak']);
            }
        } else {
            // Non-superadmin: filter per departemen sesuai role
            // Role name = nama departemen (keuangan → Keuangan, produksi → Produksi, dst.)
            $deptMap = [
                'keuangan'  => 'Keuangan',
                'produksi'  => 'Produksi',
                'hrd'       => 'HRD',
                'purchase'  => 'Purchase',
                'sales'     => 'Sales',
                'marketing' => 'Marketing',
                'it'        => 'IT',
            ];
            if (isset($deptMap[$role])) {
                $query->where('departemen', $deptMap[$role]);
            }

            $tab = $request->input('tab', 'semua');
            if ($tab !== 'semua') {
                $query->where('status', $tab);
            }
        }

        if ($sort === 'terlama') {
            $query->oldest('id');
        } else {
            $query->latest('id');
        }

        $data = $query->paginate(15)->withQueryString();

        // Hitung stats per departemen juga (untuk non-superadmin scope)
        $baseQuery = Purchasero::query();
        if ($role !== 'superadmin') {
            $deptMap = [
                'keuangan'  => 'Keuangan',
                'produksi'  => 'Produksi',
                'hrd'       => 'HRD',
                'purchase'  => 'Purchase',
                'sales'     => 'Sales',
                'marketing' => 'Marketing',
                'it'        => 'IT',
            ];
            if (isset($deptMap[$role])) {
                $baseQuery->where('departemen', $deptMap[$role]);
            }
        }

        $totalPR        = (clone $baseQuery)->count();
        $totalDisetujui = (clone $baseQuery)->where('status', 'Disetujui')->count();
        $totalPending   = (clone $baseQuery)->where('status', 'Pending')->count();
        $totalDitolak   = (clone $baseQuery)->where('status', 'Ditolak')->count();
        $totalDiajukan  = (clone $baseQuery)->where('status', 'Diajukan')->count();
        $totalNominal   = (clone $baseQuery)->whereIn('status', ['Diajukan', 'Disetujui'])->sum('nominal');

        // Departemen label untuk auto-fill di form store
        $deptLabel = match($role) {
            'keuangan'  => 'Keuangan',
            'produksi'  => 'Produksi',
            'hrd'       => 'HRD',
            'purchase'  => 'Purchase',
            'sales'     => 'Sales',
            'marketing' => 'Marketing',
            'it'        => 'IT',
            default     => ucfirst($role),
        };

        return view('admin.purchasero.index', compact(
            'data', 'role', 'tab', 'sort', 'deptLabel',
            'totalPR', 'totalDisetujui', 'totalPending', 'totalDitolak', 'totalDiajukan', 'totalNominal'
        ));
    }

    public function store(Request $request)
    {
        $role = auth()->user()->role;

        $deptMap = [
            'keuangan'   => 'Keuangan',
            'produksi'   => 'Produksi',
            'hrd'        => 'HRD',
            'purchase'   => 'Purchase',
            'sales'      => 'Sales',
            'marketing'  => 'Marketing',
            'it'         => 'IT',
            'superadmin' => 'Manajemen',
        ];
        $departemen = $deptMap[$role] ?? ucfirst($role);

        $request->validate([
            'tanggal'                        => 'required|date',
            'pemohon'                        => 'required|string|max:255',
            'items'                          => 'required|array|min:1',
            'items.*.barang_jasa'            => 'required|string|max:255',
            'items.*.kode_barang'            => 'required|string|max:255',
            'items.*.qty'                    => 'required|integer|min:1',
            'items.*.satuan'                 => 'required|string|max:255',
            'items.*.alasan_permintaan'      => 'required|string|max:255',
            'items.*.nominal'                => 'nullable|integer|min:0',
        ], [
            'tanggal.required'                   => 'Tanggal wajib diisi',
            'pemohon.required'                   => 'Pemohon wajib diisi',
            'items.required'                     => 'Minimal 1 item harus diisi',
            'items.*.barang_jasa.required'        => 'Barang/Jasa wajib diisi',
            'items.*.kode_barang.required'        => 'Kode Barang wajib diisi',
            'items.*.qty.required'               => 'Qty wajib diisi',
            'items.*.satuan.required'            => 'Satuan wajib diisi',
            'items.*.alasan_permintaan.required' => 'Alasan wajib diisi',
        ]);

        // Generate 1 No PR untuk semua item dalam 1 submit
        $last      = Purchasero::orderBy('id', 'desc')->first();
        $lastNum   = $last && preg_match('/(\d+)$/', $last->no_pr, $m) ? (int) $m[1] : 0;
        $noPr      = 'PR-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);

        $count = 0;
        foreach ($request->items as $item) {
            Purchasero::create([
                'no_pr'             => $noPr,
                'tanggal'           => $request->tanggal,
                'pemohon'           => $request->pemohon,
                'departemen'        => $departemen,
                'barang_jasa'       => $item['barang_jasa'],
                'kode_barang'       => $item['kode_barang'],
                'qty'               => $item['qty'],
                'satuan'            => $item['satuan'],
                'alasan_permintaan' => $item['alasan_permintaan'],
                'nominal'           => $item['nominal'] ?? null,
                'status'            => 'Pending',
            ]);
            $count++;
        }

        return redirect()->route('purchasero.index')
            ->with('success', "Pengadaan {$noPr} berhasil ditambahkan ({$count} item).");
    }

    public function update(Request $request, Purchasero $purchasero)
    {
        // Guard: PR yang sudah diajukan/disetujui tidak boleh diedit
        if (in_array($purchasero->status, ['Disetujui', 'Diajukan'])) {
            return redirect()->route('purchasero.index')
                ->with('error', 'Pengadaan yang sudah diajukan/disetujui tidak dapat diedit.');
        }

        // Update hanya field konten, departemen & status tidak berubah
        $validated = $this->validateDataStore($request);
        // Pertahankan departemen & status yang sudah ada
        unset($validated['departemen'], $validated['status']);

        $purchasero->update($validated);

        return redirect()->route('purchasero.index')
            ->with('success', 'Pengadaan berhasil diperbarui.');
    }

    public function destroy(Purchasero $purchasero)
    {
        // Guard: PR yang sudah diajukan/disetujui tidak boleh dihapus
        if (in_array($purchasero->status, ['Disetujui', 'Diajukan'])) {
            return redirect()->route('purchasero.index')
                ->with('error', 'Pengadaan yang sudah diajukan/disetujui tidak dapat dihapus.');
        }

        $purchasero->delete();

        return redirect()->route('purchasero.index')
            ->with('success', 'Pengadaan berhasil dihapus.');
    }

    /**
     * Tombol "Ajukan" — hanya untuk non-superadmin.
     */
    public function ajukan(Purchasero $purchasero)
    {
        $role = auth()->user()->role;

        if ($role === 'superadmin') {
            return redirect()->route('purchasero.index')
                ->with('error', 'Superadmin tidak dapat mengajukan pengadaan.');
        }

        if (in_array($purchasero->status, ['Diajukan', 'Disetujui'])) {
            return redirect()->route('purchasero.index')
                ->with('error', 'Pengadaan ini sudah diajukan atau disetujui.');
        }

        $purchasero->update([
            'status'            => 'Diajukan',
            'terakhir_diajukan' => now(),
        ]);

        return redirect()->route('purchasero.index')
            ->with('success', 'Pengadaan ' . $purchasero->no_pr . ' berhasil diajukan.');
    }

    /**
     * Superadmin: Setujui atau Tolak (dengan catatan wajib jika Ditolak).
     */
    public function updateStatusInline(Request $request, Purchasero $purchasero)
    {
        $role = auth()->user()->role;

        if ($role !== 'superadmin') {
            return redirect()->route('purchasero.index')
                ->with('error', 'Anda tidak memiliki izin untuk mengubah status ini.');
        }

        $request->validate([
            'status'  => 'required|in:Disetujui,Ditolak',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $statusLama = $purchasero->status;
        $updateData = ['status' => $request->status];

        if ($request->status === 'Ditolak') {
            $updateData['catatan']             = $request->catatan;
            $updateData['disetujui_oleh']      = null;
            $updateData['tanggal_persetujuan'] = null;
        } else {
            // Disetujui: catat siapa yang menyetujui & kapan
            $updateData['disetujui_oleh']      = auth()->user()->name;
            $updateData['tanggal_persetujuan'] = now()->toDateString();
            $updateData['catatan']             = null;
        }

        $purchasero->update($updateData);

        // ── Hapus jurnal lama jika sebelumnya Disetujui lalu di-Tolak ──
        if ($statusLama === 'Disetujui' && $request->status === 'Ditolak') {
            DB::transaction(function () use ($purchasero) {
                $kodeJurnal = 'PR-JRN-' . $purchasero->no_pr;

                // Hapus dari keuangans + recalculate saldo
                $keuangan = Keuangan::where('reference', $kodeJurnal)->first();
                if ($keuangan) {
                    $keuanganId = $keuangan->id;
                    $keuangan->delete();
                    PaymentsController::recalculateKeuanganSaldo($keuanganId);
                }

                // Hapus dari bukubesars + recalculate saldo
                $jurnal = Bukubesar::where('kode_jurnal', $kodeJurnal)
                    ->orWhere('referensi', $purchasero->no_pr)
                    ->first();
                if ($jurnal) {
                    $jurnalId = $jurnal->id;
                    $jurnal->delete();
                    PaymentsController::recalculateBukubesarSaldo($jurnalId);
                }
            });
        }

        // ── Buat jurnal otomatis saat status berubah ke Disetujui ──
        if ($request->status === 'Disetujui') {
            DB::transaction(function () use ($purchasero) {
                $kodeJurnal = 'PR-JRN-' . $purchasero->no_pr;
                $nominal    = (int) ($purchasero->nominal ?? 0);

                // ── Catat ke Keuangan (sebelumnya tidak ada) ──
                if (!Keuangan::where('reference', $kodeJurnal)->exists()) {
                    $lastSaldo = (float) DB::table('keuangans')
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->value('saldo') ?? 0;

                    Keuangan::create([
                        'tanggal'     => $purchasero->tanggal_persetujuan ?? now()->toDateString(),
                        'reference'   => $kodeJurnal,
                        'user_id'     => auth()->id(),
                        'kategori'    => 'Pengeluaran',
                        'metode'      => 'Cash',
                        'keterangan'  => 'PR #' . $purchasero->no_pr . ' - ' . $purchasero->barang_jasa,
                        'pemasukan'   => 0,
                        'pengeluaran' => $nominal,
                        'saldo'       => $lastSaldo - $nominal,
                        'sumber'      => 'auto',
                    ]);
                }

                // ── Catat ke Buku Besar (pakai lockForUpdate, cegah duplikat) ──
                if (!Bukubesar::where('kode_jurnal', $kodeJurnal)->exists()) {
                    $saldoBB = (float) DB::table('bukubesars')
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->value('saldo') ?? 0;

                    Bukubesar::create([
                        'kode_jurnal' => $kodeJurnal,
                        'transaksi'   => 'Pengadaan: ' . $purchasero->barang_jasa,
                        'kategori'    => 'Beban',
                        'tanggal'     => $purchasero->tanggal_persetujuan ?? now()->toDateString(),
                        'debit'       => $nominal,
                        'kredit'      => 0,
                        'saldo'       => $saldoBB - $nominal,
                        'aktivitas'   => 'pengadaan',
                        'keterangan'  => 'PR #' . $purchasero->no_pr . ' disetujui oleh ' . $purchasero->disetujui_oleh,
                        'referensi'   => $purchasero->no_pr,
                    ]);
                }
            });
        }

        $label = $request->status === 'Disetujui' ? 'disetujui' : 'ditolak';

        return redirect()->route('purchasero.index')
            ->with('success', 'Pengadaan ' . $purchasero->no_pr . ' berhasil ' . $label . '.');
    }

    /**
     * Validasi untuk store & update (tanpa departemen & status — di-handle controller).
     */
    private function validateDataStore(Request $request): array
    {
        return $request->validate([
            'tanggal'           => 'required|date',
            'pemohon'           => 'required|string|max:255',
            'barang_jasa'       => 'required|string|max:255',
            'kode_barang'       => 'required|string|max:255',
            'qty'               => 'required|integer|min:1',
            'satuan'            => 'required|string|max:255',
            'alasan_permintaan' => 'required|string|max:255',
            'nominal'           => 'nullable|integer|min:0',
        ]);
    }
}
