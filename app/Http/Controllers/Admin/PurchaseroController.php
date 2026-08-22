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
        $bulan = $request->input('bulan'); // format: Y-m
        $deptFilter = $request->input('departemen'); // hanya untuk superadmin

        $query = Purchasero::query();

        if ($role === 'superadmin') {
            $tab = $request->input('tab', 'Diajukan');
            if (in_array($tab, ['Pending', 'Diajukan', 'Disetujui', 'Ditolak'])) {
                $query->where('status', $tab);
            } else {
                $query->whereIn('status', ['Pending', 'Diajukan', 'Disetujui', 'Ditolak']);
            }
            // Filter departemen (superadmin saja)
            if ($deptFilter) {
                $query->where('departemen', $deptFilter);
            }
        } else {
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

        // Filter bulan
        if ($bulan) {
            [$y, $m] = explode('-', $bulan);
            $query->whereYear('tanggal', $y)->whereMonth('tanggal', $m);
        }

        if ($sort === 'terlama') {
            $query->oldest('id');
        } else {
            $query->latest('id');
        }

        $data = $query->with('items')->paginate(15)->withQueryString();

        // Stats (scope sama dengan query utama tapi tanpa pagination)
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
        if ($role === 'superadmin' && $deptFilter) {
            $baseQuery->where('departemen', $deptFilter);
        }
        if ($bulan) {
            [$y, $m] = explode('-', $bulan);
            $baseQuery->whereYear('tanggal', $y)->whereMonth('tanggal', $m);
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
            'data', 'role', 'tab', 'sort', 'deptLabel', 'bulan', 'deptFilter',
            'totalPR', 'totalDisetujui', 'totalPending', 'totalDitolak', 'totalDiajukan', 'totalNominal'
        ));
    }

    public function create()
    {
        $role = auth()->user()->role;
        
        // Generate No PR preview
        $last = Purchasero::orderBy('id', 'desc')->first();
        $lastNum = $last && preg_match('/(\d+)$/', $last->no_pr, $m) ? (int) $m[1] : 0;
        $noPrPreview = 'PR-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        
        // Departemen options untuk superadmin
        $departemenOptions = [];
        if ($role === 'superadmin') {
            $departemenOptions = [
                'Keuangan', 'Produksi', 'HRD', 'Purchase', 
                'Sales', 'Marketing', 'IT', 'Manajemen'
            ];
        }
        
        // Departemen label untuk auto-fill
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

        return view('admin.purchasero.create', compact('role', 'noPrPreview', 'departemenOptions', 'deptLabel'));
    }

    public function edit(Purchasero $purchasero)
    {
        // Guard: hanya status Pending & Diajukan yang bisa edit
        if (!in_array($purchasero->status, ['Pending', 'Diajukan'])) {
            return redirect()->route('purchasero.index')
                ->with('error', 'Pengadaan dengan status ' . $purchasero->status . ' tidak dapat diedit.');
        }

        // Load items relation
        $purchasero->load('items');

        return view('admin.purchasero.edit', compact('purchasero'));
    }

    public function details($id)
    {
        try {
            $purchasero = Purchasero::with('items')->findOrFail($id);
            
            // Format data untuk response
            $data = [
                'id' => $purchasero->id,
                'no_pr' => $purchasero->no_pr,
                'tanggal_formatted' => $purchasero->tanggal ? \Carbon\Carbon::parse($purchasero->tanggal)->format('d M Y') : '-',
                'departemen' => $purchasero->departemen ?? '-',
                'pemohon' => $purchasero->pemohon ?? '-',
                'alasan_permintaan' => $purchasero->alasan_permintaan ?? '-',
                'status' => $purchasero->status ?? '-',
                'status_class' => match($purchasero->status) {
                    'Disetujui' => 'bg-green-100 text-green-600',
                    'Ditolak'   => 'bg-red-100 text-red-600',
                    'Diajukan'  => 'bg-indigo-100 text-indigo-600',
                    'Pending'   => 'bg-yellow-100 text-yellow-600',
                    default     => 'bg-gray-100 text-gray-500',
                },
                'disetujui_oleh' => $purchasero->disetujui_oleh,
                'tanggal_persetujuan_formatted' => $purchasero->tanggal_persetujuan ? \Carbon\Carbon::parse($purchasero->tanggal_persetujuan)->format('d M Y') : null,
                'catatan' => $purchasero->catatan,
                'terakhir_diajukan_formatted' => $purchasero->terakhir_diajukan ? \Carbon\Carbon::parse($purchasero->terakhir_diajukan)->format('d M Y H:i') : null,
                'total_nominal' => $purchasero->total_nominal,
                'total_nominal_formatted' => number_format($purchasero->total_nominal, 0, ',', '.'),
                'total_items' => $purchasero->items->count() > 0 ? $purchasero->items->count() : 1,
            ];

            // Items data (new structure)
            if ($purchasero->items->count() > 0) {
                $data['items'] = $purchasero->items->map(function($item) {
                    return [
                        'nama_barang' => $item->nama_barang,
                        'kategori' => $item->kategori,
                        'posisi' => $item->posisi,
                        'part_number' => $item->part_number,
                        'serial_number' => $item->serial_number,
                        'qty' => $item->qty,
                        'satuan' => $item->satuan,
                        'harga_satuan' => $item->harga_satuan,
                        'harga_satuan_formatted' => $item->harga_satuan ? number_format($item->harga_satuan, 0, ',', '.') : null,
                        'subtotal' => $item->subtotal,
                        'subtotal_formatted' => $item->subtotal ? number_format($item->subtotal, 0, ',', '.') : null,
                        'spesifikasi' => $item->spesifikasi,
                        'merk' => $item->merk,
                        'keterangan' => $item->keterangan,
                        'bukti' => $item->bukti,
                    ];
                });
            } else {
                // Legacy data (old structure) - for backward compatibility
                $data['barang_jasa'] = $purchasero->barang_jasa;
                $data['kode_barang'] = $purchasero->kode_barang;
                $data['qty'] = $purchasero->qty;
                $data['satuan'] = $purchasero->satuan;
                $data['nominal'] = $purchasero->nominal;
                $data['nominal_formatted'] = $purchasero->nominal ? number_format($purchasero->nominal, 0, ',', '.') : null;
            }

            return response()->json(['success' => true, 'purchasero' => $data]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
    }

    public function store(Request $request)
    {
        $role = auth()->user()->role;

        // Departemen mapping
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
        
        // Untuk superadmin, ambil dari input; untuk user lain, set sesuai role
        if ($role === 'superadmin') {
            $departemen = $request->departemen;
        } else {
            $departemen = $deptMap[$role] ?? ucfirst($role);
        }

        // Validation rules untuk header + items
        $request->validate([
            'tanggal'                => 'required|date',
            'departemen'             => $role === 'superadmin' ? 'required|string|max:255' : 'nullable',
            'pemohon'                => 'required|string|max:255',
            'alasan_permintaan'      => 'required|string',
            'items'                  => 'required|array|min:1',
            'items.*.nama_barang'    => 'required|string|max:255',
            'items.*.kategori'       => 'nullable|string|max:255',
            'items.*.posisi'         => 'nullable|string|max:255',
            'items.*.part_number'    => 'nullable|string|max:255',
            'items.*.serial_number'  => 'nullable|string|max:255',
            'items.*.qty'            => 'required|numeric|min:0.01',
            'items.*.satuan'         => 'nullable|string|max:50',
            'items.*.harga_satuan'   => 'nullable|numeric|min:0',
            'items.*.subtotal'       => 'nullable|numeric|min:0',
            'items.*.spesifikasi'    => 'nullable|string',
            'items.*.merk'           => 'nullable|string|max:255',
            'items.*.keterangan'     => 'nullable|string',
            'items.*.bukti'          => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ], [
            'tanggal.required'           => 'Tanggal wajib diisi',
            'departemen.required'        => 'Departemen wajib dipilih',
            'pemohon.required'           => 'Pemohon wajib diisi',
            'alasan_permintaan.required' => 'Alasan permintaan wajib diisi',
            'items.required'             => 'Minimal 1 item harus diisi',
            'items.*.nama_barang.required' => 'Nama barang wajib diisi',
            'items.*.qty.required'       => 'Quantity wajib diisi',
            'items.*.qty.min'            => 'Quantity minimal 0.01',
            'items.*.bukti.mimes'        => 'File harus berformat: jpg, jpeg, png, pdf, doc, docx',
            'items.*.bukti.max'          => 'Ukuran file maksimal 2MB',
        ]);

        // Generate No PR
        $last    = Purchasero::orderBy('id', 'desc')->first();
        $lastNum = $last && preg_match('/(\d+)$/', $last->no_pr, $m) ? (int) $m[1] : 0;
        $noPr    = 'PR-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);

        // Calculate total nominal dari sum subtotal items
        $totalNominal = 0;
        foreach ($request->items as $item) {
            $subtotal = isset($item['subtotal']) && is_numeric($item['subtotal']) ? (float) $item['subtotal'] : 0;
            $totalNominal += $subtotal;
        }

        DB::beginTransaction();
        try {
            // Create Purchasero header
            $purchasero = Purchasero::create([
                'no_pr'             => $noPr,
                'tanggal'           => $request->tanggal,
                'departemen'        => $departemen,
                'pemohon'           => $request->pemohon,
                'alasan_permintaan' => $request->alasan_permintaan,
                'nominal'           => $totalNominal,
                'status'            => 'Diajukan', // Langsung status "Diajukan"
                'barang_jasa'       => null, // Deprecated field untuk backward compatibility
                'kode_barang'       => null, // Deprecated field untuk backward compatibility
                'qty'               => null, // Deprecated field untuk backward compatibility
                'satuan'            => null, // Deprecated field untuk backward compatibility
            ]);

            // Create PurchaseroItem untuk setiap item
            foreach ($request->items as $index => $item) {
                $buktiFiles = [];
                
                // Handle file upload untuk bukti per item
                if ($request->hasFile("items.{$index}.bukti")) {
                    $file = $request->file("items.{$index}.bukti");
                    $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('purchasero/bukti', $filename, 'public');
                    $buktiFiles[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path'     => $path,
                        'size'     => $file->getSize(),
                        'type'     => $file->getClientOriginalExtension(),
                    ];
                }

                $purchasero->items()->create([
                    'nama_barang'   => $item['nama_barang'],
                    'kategori'      => $item['kategori'] ?? null,
                    'posisi'        => $item['posisi'] ?? null,
                    'part_number'   => $item['part_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'qty'           => $item['qty'],
                    'satuan'        => $item['satuan'] ?? null,
                    'harga_satuan'  => isset($item['harga_satuan']) && is_numeric($item['harga_satuan']) ? (float) $item['harga_satuan'] : null,
                    'subtotal'      => isset($item['subtotal']) && is_numeric($item['subtotal']) ? (float) $item['subtotal'] : null,
                    'spesifikasi'   => $item['spesifikasi'] ?? null,
                    'merk'          => $item['merk'] ?? null,
                    'keterangan'    => $item['keterangan'] ?? null,
                    'bukti'         => !empty($buktiFiles) ? $buktiFiles : null,
                ]);
            }

            DB::commit();

            return redirect()->route('purchasero.index')
                ->with('success', "Pengadaan {$noPr} berhasil diajukan dengan " . count($request->items) . " item.");

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Purchasero $purchasero)
    {
        // Guard: hanya status Pending & Diajukan yang bisa edit
        if (!in_array($purchasero->status, ['Pending', 'Diajukan'])) {
            return redirect()->route('purchasero.index')
                ->with('error', 'Pengadaan dengan status ' . $purchasero->status . ' tidak dapat diedit.');
        }

        $role = auth()->user()->role;

        // Departemen logic
        if ($role === 'superadmin') {
            $departemen = $request->departemen;
        } else {
            $departemen = $purchasero->departemen; // Keep existing departemen
        }

        // Validation rules
        $request->validate([
            'tanggal'                => 'required|date',
            'departemen'             => $role === 'superadmin' ? 'required|string|max:255' : 'nullable',
            'pemohon'                => 'required|string|max:255',
            'alasan_permintaan'      => 'required|string',
            'items'                  => 'required|array|min:1',
            'items.*.nama_barang'    => 'required|string|max:255',
            'items.*.kategori'       => 'nullable|string|max:255',
            'items.*.posisi'         => 'nullable|string|max:255',
            'items.*.part_number'    => 'nullable|string|max:255',
            'items.*.serial_number'  => 'nullable|string|max:255',
            'items.*.qty'            => 'required|numeric|min:0.01',
            'items.*.satuan'         => 'nullable|string|max:50',
            'items.*.harga_satuan'   => 'nullable|numeric|min:0',
            'items.*.subtotal'       => 'nullable|numeric|min:0',
            'items.*.spesifikasi'    => 'nullable|string',
            'items.*.merk'           => 'nullable|string|max:255',
            'items.*.keterangan'     => 'nullable|string',
            'items.*.bukti'          => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        // Calculate total nominal
        $totalNominal = 0;
        foreach ($request->items as $item) {
            $subtotal = isset($item['subtotal']) && is_numeric($item['subtotal']) ? (float) $item['subtotal'] : 0;
            $totalNominal += $subtotal;
        }

        DB::beginTransaction();
        try {
            // Update Purchasero header
            $purchasero->update([
                'tanggal'           => $request->tanggal,
                'departemen'        => $departemen,
                'pemohon'           => $request->pemohon,
                'alasan_permintaan' => $request->alasan_permintaan,
                'nominal'           => $totalNominal,
            ]);

            // Delete existing items
            $purchasero->items()->delete();

            // Create new items
            foreach ($request->items as $index => $item) {
                $buktiFiles = [];
                
                // Handle file upload untuk bukti per item
                if ($request->hasFile("items.{$index}.bukti")) {
                    $file = $request->file("items.{$index}.bukti");
                    $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('purchasero/bukti', $filename, 'public');
                    $buktiFiles[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path'     => $path,
                        'size'     => $file->getSize(),
                        'type'     => $file->getClientOriginalExtension(),
                    ];
                }

                $purchasero->items()->create([
                    'nama_barang'   => $item['nama_barang'],
                    'kategori'      => $item['kategori'] ?? null,
                    'posisi'        => $item['posisi'] ?? null,
                    'part_number'   => $item['part_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'qty'           => $item['qty'],
                    'satuan'        => $item['satuan'] ?? null,
                    'harga_satuan'  => isset($item['harga_satuan']) && is_numeric($item['harga_satuan']) ? (float) $item['harga_satuan'] : null,
                    'subtotal'      => isset($item['subtotal']) && is_numeric($item['subtotal']) ? (float) $item['subtotal'] : null,
                    'spesifikasi'   => $item['spesifikasi'] ?? null,
                    'merk'          => $item['merk'] ?? null,
                    'keterangan'    => $item['keterangan'] ?? null,
                    'bukti'         => !empty($buktiFiles) ? $buktiFiles : null,
                ]);
            }

            DB::commit();

            return redirect()->route('purchasero.index')
                ->with('success', "Pengadaan {$purchasero->no_pr} berhasil diperbarui dengan " . count($request->items) . " item.");

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat update data: ' . $e->getMessage());
        }
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
