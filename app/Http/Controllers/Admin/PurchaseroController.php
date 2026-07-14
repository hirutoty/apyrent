<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bukubesar;
use App\Models\Purchasero;
use Illuminate\Http\Request;

class PurchaseroController extends Controller
{
    public function index(Request $request)
    {
        $role = auth()->user()->role;
        $sort = $request->input('sort', 'terbaru');

        $query = Purchasero::query();

        if ($role === 'superadmin') {
            // Superadmin: tab Diajukan / Disetujui / Ditolak
            $tab = $request->input('tab', 'Diajukan');
            if (in_array($tab, ['Diajukan', 'Disetujui', 'Ditolak'])) {
                $query->where('status', $tab);
            } else {
                $query->whereIn('status', ['Diajukan', 'Disetujui', 'Ditolak']);
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

        // Departemen otomatis dari role
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

        $validated = $this->validateDataStore($request);
        $validated['departemen'] = $departemen;
        $validated['status']     = 'Pending';

        Purchasero::create($validated);

        return redirect()->route('purchasero.index')
            ->with('success', 'Pengadaan berhasil ditambahkan.');
    }

    public function update(Request $request, Purchasero $purchasero)
    {
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

        // ── Task 3: Hapus jurnal lama jika sebelumnya Disetujui lalu di-Tolak ──
        if ($statusLama === 'Disetujui' && $request->status === 'Ditolak') {
            Bukubesar::where('referensi', $purchasero->no_pr)->delete();
        }

        // ── Task 2: Buat jurnal otomatis saat status berubah ke Disetujui ──
        if ($request->status === 'Disetujui') {
            // Ambil saldo terakhir dari buku besar (0 jika belum ada data)
            $saldoTerakhir = Bukubesar::latest('id')->value('saldo') ?? 0;
            $nominal       = (int) ($purchasero->nominal ?? 0);
            $saldoBaru     = $saldoTerakhir - $nominal;

            Bukubesar::create([
                'kode_jurnal' => 'PR-JRN-' . $purchasero->no_pr,
                'transaksi'   => 'Pengadaan: ' . $purchasero->barang_jasa,
                'kategori'    => 'Beban',
                'tanggal'     => $purchasero->tanggal_persetujuan,
                'debit'       => $nominal,   // Beban/pengeluaran = sisi DEBIT
                'kredit'      => 0,
                'saldo'       => $saldoBaru,
                'aktivitas'   => 'pengadaan',
                'keterangan'  => 'PR #' . $purchasero->no_pr . ' disetujui oleh ' . $purchasero->disetujui_oleh,
                'referensi'   => $purchasero->no_pr,
            ]);
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
