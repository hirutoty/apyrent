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

        if ($role === 'superadmin') {
            $departemen = $request->departemen;
        } else {
            $departemen = $deptMap[$role] ?? ucfirst($role);
        }

        $tipe = $request->input('tipe_pengadaan', 'belanja');

        // ── VALIDASI BERBEDA BERDASARKAN TIPE ──────────────────────
        if ($tipe === 'service') {
            $request->validate([
                'tanggal'                      => 'required|date',
                'departemen'                   => $role === 'superadmin' ? 'required|string|max:255' : 'nullable',
                'pemohon'                      => 'required|string|max:255',
                'alasan_permintaan'            => 'required|string',
                'kendaraan_id'                 => 'required|exists:kendaraan,id',
                'tanggal_service'              => 'required|date',
                'kilometer'                    => 'required|integer|min:0',
                'nama_penerima'                => 'required|string|max:255',
                'nama_bank'                    => 'required|string|max:255',
                'no_rekening'                  => 'required|string|max:100',
                'parts'                        => 'required|array|min:1',
                'parts.*.nama_part'            => 'required|string|max:255',
                'parts.*.category_id'          => 'nullable|exists:service_categories,id',
                'parts.*.tgl_pasang'           => 'required|date',
                'parts.*.kilometer_pasang'     => 'required|integer|min:0',
                'parts.*.interval_nilai'       => 'required|integer|min:1',
                'parts.*.interval_satuan'      => 'required|in:hari,minggu,bulan,tahun',
                'parts.*.biaya'                => 'required|integer|min:0',
            ], [
                'kendaraan_id.required'      => 'Kendaraan wajib dipilih',
                'tanggal_service.required'   => 'Tanggal service wajib diisi',
                'kilometer.required'         => 'Kilometer wajib diisi',
                'nama_penerima.required'     => 'Nama penerima wajib diisi',
                'nama_bank.required'         => 'Nama bank wajib diisi',
                'no_rekening.required'       => 'No rekening wajib diisi',
                'parts.required'             => 'Minimal 1 part harus diisi',
                'parts.*.nama_part.required' => 'Nama part wajib diisi',
                'parts.*.tgl_pasang.required'=> 'Tanggal pasang wajib diisi',
            ]);
        } else {
            $request->validate([
                'tanggal'                => 'required|date',
                'departemen'             => $role === 'superadmin' ? 'required|string|max:255' : 'nullable',
                'pemohon'                => 'required|string|max:255',
                'supplier_id'            => 'nullable|exists:supplier,id',
                'alasan_permintaan'      => 'required|string',
                'items'                  => 'required|array|min:1',
                'items.*.nama_barang'    => 'required|string|max:255',
                'items.*.qty'            => 'required|numeric|min:0.01',
                'items.*.bukti'          => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            ]);
        }

        // Generate No PR
        $last    = Purchasero::orderBy('id', 'desc')->first();
        $lastNum = $last && preg_match('/(\d+)$/', $last->no_pr, $m) ? (int) $m[1] : 0;
        $noPr    = 'PR-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            if ($tipe === 'service') {
                // ── SIMPAN PENGADAAN SERVICE ────────────────────────
                $totalNominal = 0;
                foreach ($request->parts as $part) {
                    $totalNominal += (int) ($part['biaya'] ?? 0);
                }

                $purchasero = Purchasero::create([
                    'no_pr'             => $noPr,
                    'tanggal'           => $request->tanggal,
                    'departemen'        => $departemen,
                    'tipe_pengadaan'    => 'service',
                    'pemohon'           => $request->pemohon,
                    'supplier_id'       => $request->supplier_id,
                    'alasan_permintaan' => $request->alasan_permintaan,
                    'nominal'           => $totalNominal,
                    'status'            => $role === 'superadmin' ? 'Diajukan' : 'Pending',
                    'kendaraan_id'      => $request->kendaraan_id,
                    'tanggal_service'   => $request->tanggal_service,
                    'kilometer'         => $request->kilometer,
                    'keluhan'           => $request->keluhan,
                    'nama_penerima'     => $request->nama_penerima,
                    'nama_bank'         => $request->nama_bank,
                    'no_rekening'       => $request->no_rekening,
                ]);

                // Simpan parts
                foreach ($request->parts as $part) {
                    // Cek apakah kendaraan sudah over limit untuk kategori ini
                    $isOverLimit = false;
                    if (!empty($part['category_id'])) {
                        $limit = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $request->kendaraan_id)
                            ->where('category_id', $part['category_id'])
                            ->first();
                        if ($limit && !empty($part['biaya']) && (int) $part['biaya'] > $limit->limit_price) {
                            $isOverLimit = true;
                        }
                    }

                    $purchasero->serviceParts()->create([
                        'kendaraan_id'     => $request->kendaraan_id,
                        'category_id'      => $part['category_id'] ?? null,
                        'nama_part'        => $part['nama_part'],
                        'part_number'      => $part['part_number'] ?? null,
                        'serial_number'    => $part['serial_number'] ?? null,
                        'posisi'           => $part['posisi'] ?? null,
                        'merk'             => $part['merk'] ?? null,
                        'tgl_pasang'       => $part['tgl_pasang'],
                        'kilometer_pasang' => $part['kilometer_pasang'] ?? 0,
                        'kondisi'          => $part['kondisi'] ?? 'Baik',
                        'status_part'      => 'Proses',
                        'interval_nilai'   => $part['interval_nilai'] ?? 1,
                        'interval_satuan'  => $part['interval_satuan'] ?? 'bulan',
                        'biaya'            => (int) ($part['biaya'] ?? 0),
                        'keterangan'       => $part['keterangan'] ?? null,
                        'is_over_limit'    => $isOverLimit,
                    ]);
                }
            } else {
                // ── SIMPAN PENGADAAN BELANJA ────────────────────────
                $totalNominal = 0;
                foreach ($request->items as $item) {
                    $totalNominal += isset($item['subtotal']) && is_numeric($item['subtotal']) ? (float) $item['subtotal'] : 0;
                }

                $purchasero = Purchasero::create([
                    'no_pr'             => $noPr,
                    'tanggal'           => $request->tanggal,
                    'departemen'        => $departemen,
                    'tipe_pengadaan'    => 'belanja',
                    'pemohon'           => $request->pemohon,
                    'supplier_id'       => $request->supplier_id,
                    'alasan_permintaan' => $request->alasan_permintaan,
                    'nominal'           => $totalNominal,
                    'status'            => $role === 'superadmin' ? 'Diajukan' : 'Pending',
                    'nama_penerima'     => $request->nama_penerima,
                    'nama_bank'         => $request->nama_bank,
                    'no_rekening'       => $request->no_rekening,
                    'barang_jasa'       => null,
                    'kode_barang'       => null,
                    'qty'               => null,
                    'satuan'            => null,
                ]);

                foreach ($request->items as $index => $item) {
                    $buktiFiles = [];
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
            }

            DB::commit();

            $tipePesan = $tipe === 'service' ? 'service' : 'belanja';
            return redirect()->route('purchasero.index')
                ->with('success', "Pengadaan {$tipePesan} {$noPr} berhasil " . ($role === 'superadmin' ? 'diajukan' : 'disimpan sebagai Pending') . ".");

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
            'supplier_id'            => 'nullable|exists:supplier,id',
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
                'supplier_id'       => $request->supplier_id,
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

                // ── Catat ke Keuangan ──
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

                // ── Catat ke Buku Besar ──
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

        // ── Buat service_history jika tipe_pengadaan = service ──
                if ($purchasero->tipe_pengadaan === 'service' && $purchasero->kendaraan_id) {
                    $purchasero->load('serviceParts');

                    $serviceHistory = \App\Models\ServiceHistory::create([
                        'kendaraan_id'      => $purchasero->kendaraan_id,
                        'tanggal_service'   => $purchasero->tanggal_service ?? now()->toDateString(),
                        'kilometer'         => $purchasero->kilometer ?? 0,
                        'keluhan'           => $purchasero->keluhan,
                        'total_biaya'       => $purchasero->nominal,
                        'status'            => 'proses',
                        'status_approval'   => 'approved',
                        'approval_by'       => auth()->id(),
                        'approval_at'       => now(),
                        'is_request'        => true,
                    ]);

                    // Cek apakah ada part yang melebihi limit → status_pengeluaran = overservice
                    $hasOverLimit = $purchasero->serviceParts->contains('is_over_limit', true);

                    if ($hasOverLimit) {
                        $serviceHistory->update(['status_pengeluaran' => 'overservice']);
                    }

                    // Buat ServicePart dari PR service parts
                    foreach ($purchasero->serviceParts as $prPart) {
                        \App\Models\ServicePart::create([
                            'service_history_id' => $serviceHistory->id,
                            'kendaraan_id'       => $purchasero->kendaraan_id,
                            'category_id'        => $prPart->category_id,
                            'nama_part'          => $prPart->nama_part,
                            'part_number'        => $prPart->part_number,
                            'serial_number'      => $prPart->serial_number,
                            'posisi'             => $prPart->posisi,
                            'tgl_pasang'         => $prPart->tgl_pasang,
                            'kilometer_pasang'   => $prPart->kilometer_pasang,
                            'kondisi'            => $prPart->kondisi,
                            'status'             => 'Terpasang',
                            'interval_nilai'     => $prPart->interval_nilai,
                            'interval_satuan'    => $prPart->interval_satuan,
                            'biaya'              => $prPart->biaya,
                            'keterangan'         => $prPart->keterangan,
                            'is_request'         => true,
                            'status_approval'    => 'approved',
                            'approval_by'        => auth()->id(),
                            'approval_at'        => now(),
                        ]);

                        // Update status_part di PR menjadi Terpasang
                        $prPart->update(['status_part' => 'Terpasang']);
                    }

                    // Update kilometer kendaraan
                    if ($purchasero->kilometer) {
                        \App\Models\Kendaraan::where('id', $purchasero->kendaraan_id)
                            ->update(['kilometer_sekarang' => $purchasero->kilometer]);
                    }
                }
            });
        }

        $label = $request->status === 'Disetujui' ? 'disetujui' : 'ditolak';

        // Jika Ditolak dan tipe service: set service_history yang pending ke rejected (jika ada)
        if ($request->status === 'Ditolak' && $purchasero->tipe_pengadaan === 'service') {
            \App\Models\ServiceHistory::where('kendaraan_id', $purchasero->kendaraan_id)
                ->where('is_request', true)
                ->where('status_approval', 'pending')
                ->update([
                    'status_approval' => 'rejected',
                    'approval_by'     => auth()->id(),
                    'approval_at'     => now(),
                ]);
        }

        return redirect()->route('purchasero.index')
            ->with('success', 'Pengadaan ' . $purchasero->no_pr . ' berhasil ' . $label . '.');
    }

    /**
     * Setujui pengadaan SERVICE dengan upload bukti pembayaran + lampiran.
     * Dipanggil dari modal khusus di index pengadaan.
     */
    public function approveService(Request $request, Purchasero $purchasero)
    {
        if (auth()->user()->role !== 'superadmin') {
            return redirect()->route('purchasero.index')
                ->with('error', 'Tidak memiliki izin.');
        }

        if ($purchasero->status !== 'Diajukan' || $purchasero->tipe_pengadaan !== 'service') {
            return redirect()->route('purchasero.index')
                ->with('error', 'Pengadaan tidak valid untuk disetujui via modal ini.');
        }

        $request->validate([
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'lampiran_tambahan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::beginTransaction();
        try {
            // Simpan file bukti
            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPath = $request->file('bukti_pembayaran')
                    ->storeAs('purchasero/bukti', time() . '_bukti_' . $request->file('bukti_pembayaran')->getClientOriginalName(), 'public');
            }

            $lampiranPath = null;
            if ($request->hasFile('lampiran_tambahan')) {
                $lampiranPath = $request->file('lampiran_tambahan')
                    ->storeAs('purchasero/lampiran', time() . '_lamp_' . $request->file('lampiran_tambahan')->getClientOriginalName(), 'public');
            }

            // Update PR: status Disetujui + simpan path file
            $purchasero->update([
                'status'               => 'Disetujui',
                'disetujui_oleh'       => auth()->user()->name,
                'tanggal_persetujuan'  => now()->toDateString(),
                'bukti_pembayaran'     => $buktiPath ?? $purchasero->bukti_pembayaran,
                'lampiran_tambahan'    => $lampiranPath ?? $purchasero->lampiran_tambahan,
            ]);

            // Buat service_history + parts
            $purchasero->load('serviceParts');
            $hasOverLimit = $purchasero->serviceParts->contains('is_over_limit', true);

            $serviceHistory = \App\Models\ServiceHistory::create([
                'kendaraan_id'        => $purchasero->kendaraan_id,
                'tanggal_service'     => $purchasero->tanggal_service ?? now()->toDateString(),
                'kilometer'           => $purchasero->kilometer ?? 0,
                'keluhan'             => $purchasero->keluhan,
                'total_biaya'         => $purchasero->nominal,
                'status'              => 'proses',
                'status_approval'     => 'approved',
                'status_pengeluaran'  => $hasOverLimit ? 'overservice' : 'stabil',
                'approval_by'         => auth()->id(),
                'approval_at'         => now(),
                'is_request'          => true,
                'bukti_pembayaran'    => $buktiPath,
            ]);

            // Simpan lampiran ke tabel attachments jika ada
            if ($lampiranPath) {
                \App\Models\Attachment::create([
                    'relation_type' => 'service',
                    'relation_id'   => $serviceHistory->id,
                    'path'          => $lampiranPath,
                    'filename'      => basename($lampiranPath),
                    'type'          => 'lampiran',
                ]);
            }

            foreach ($purchasero->serviceParts as $prPart) {
                \App\Models\ServicePart::create([
                    'service_history_id' => $serviceHistory->id,
                    'kendaraan_id'       => $purchasero->kendaraan_id,
                    'category_id'        => $prPart->category_id,
                    'nama_part'          => $prPart->nama_part,
                    'part_number'        => $prPart->part_number,
                    'serial_number'      => $prPart->serial_number,
                    'posisi'             => $prPart->posisi,
                    'tgl_pasang'         => $prPart->tgl_pasang,
                    'kilometer_pasang'   => $prPart->kilometer_pasang,
                    'kondisi'            => $prPart->kondisi,
                    'status'             => 'Terpasang',
                    'interval_nilai'     => $prPart->interval_nilai,
                    'interval_satuan'    => $prPart->interval_satuan,
                    'biaya'              => $prPart->biaya,
                    'keterangan'         => $prPart->keterangan,
                    'is_request'         => true,
                    'status_approval'    => 'approved',
                    'approval_by'        => auth()->id(),
                    'approval_at'        => now(),
                ]);
                $prPart->update(['status_part' => 'Terpasang']);
            }

            if ($purchasero->kilometer) {
                \App\Models\Kendaraan::where('id', $purchasero->kendaraan_id)
                    ->update(['kilometer_sekarang' => $purchasero->kilometer]);
            }

            DB::commit();

            return redirect()->route('purchasero.index')
                ->with('success', 'Pengadaan ' . $purchasero->no_pr . ' berhasil disetujui dan data service tersimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }
    public function terpasang(Request $request, Purchasero $purchasero)
    {
        if (auth()->user()->role !== 'superadmin') {
            return redirect()->route('purchasero.index')
                ->with('error', 'Tidak memiliki izin.');
        }

        if ($purchasero->status !== 'Disetujui' || $purchasero->tipe_pengadaan !== 'service') {
            return redirect()->route('purchasero.index')
                ->with('error', 'Pengadaan tidak bisa diubah ke Terpasang.');
        }

        DB::transaction(function () use ($purchasero) {
            // Ubah status PR menjadi Terpasang (selesai)
            $purchasero->update(['status' => 'Terpasang']);

            // Update service_history terkait (via kendaraan + tanggal)
            $sh = \App\Models\ServiceHistory::where('kendaraan_id', $purchasero->kendaraan_id)
                ->where('is_request', true)
                ->where('status', 'Approved')
                ->whereDate('tanggal_service', $purchasero->tanggal_service)
                ->first();

            if ($sh) {
                $sh->update(['status' => 'selesai']);
            }
        });

        return redirect()->route('purchasero.index')
            ->with('success', 'Pengadaan ' . $purchasero->no_pr . ' sudah ditandai Terpasang.');
    }

    /**
     * AJAX: daftar kendaraan untuk dropdown form service.
     * Kendaraan yang pernah service ditaruh paling atas.
     */
    public function apiKendaraan()
    {
        $kendaraan = \App\Models\Kendaraan::orderByDesc(
                \App\Models\ServiceHistory::select('tanggal_service')
                    ->whereColumn('kendaraan_id', 'kendaraan.id')
                    ->orderByDesc('tanggal_service')
                    ->limit(1)
            )
            ->orderBy('nopol')
            ->get(['id', 'nopol', 'merk', 'kilometer_sekarang'])
            ->map(fn($k) => [
                'id'                 => $k->id,
                'label'              => $k->nopol . ' — ' . $k->merk,
                'kilometer_sekarang' => $k->kilometer_sekarang ?? 0,
            ]);

        return response()->json(['success' => true, 'data' => $kendaraan]);
    }

    /**
     * AJAX: kategori beserta limit untuk kendaraan tertentu.
     * Digunakan untuk autofill interval di form service.
     */
    public function apiCategoryLimit(Request $request)
    {
        $kendaraanId = $request->integer('kendaraan_id');

        $categories = \App\Models\ServiceCategory::with([
            'limits' => fn($q) => $q->where('kendaraan_id', $kendaraanId)
        ])->get()->map(function ($cat) {
            $limit = $cat->limits->first();
            return [
                'id'              => $cat->id,
                'nama'            => $cat->nama,
                'limit_nilai'     => $limit?->limit_nilai ?? 1,
                'limit_satuan'    => $limit?->limit_satuan ?? 'tahun',
                'limit_price'     => $limit?->limit_price ?? 0,
            ];
        });

        return response()->json(['success' => true, 'data' => $categories]);
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

    /*
    |--------------------------------------------------------------------------
    | APPROVAL WORKFLOW METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Show approval modal data (AJAX)
     */
    public function showApprovalModal($id)
    {
        $purchasero = Purchasero::with(['kendaraan', 'approvals.user'])
            ->findOrFail($id);
        
        // Decode source_data dan load related data
        $sourceData = $purchasero->source_data ?? [];
        $relatedData = [];
        
        // Load related data berdasarkan source_type
        if ($purchasero->source_type) {
            $relatedData = $this->loadRelatedData($purchasero->source_type, $sourceData);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'purchasero' => $purchasero,
                'source_data' => $sourceData,
                'related_data' => $relatedData,
                'temp_files' => $sourceData['temp_files'] ?? [],
            ],
        ]);
    }

    /**
     * Load related data untuk ditampilkan di modal
     */
    protected function loadRelatedData(string $sourceType, array $sourceData): array
    {
        $data = [];
        
        try {
            switch ($sourceType) {
                case 'asuransi_kendaraan':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    $data['asuransi'] = \App\Models\Asuransi::find($sourceData['asuransi_id']);
                    $data['jenis_asuransi'] = \App\Models\JenisAsuransi::find($sourceData['jenis_asuransi_id']);
                    break;
                    
                case 'pajak':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    break;
                    
                case 'service_part':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    $data['category'] = \App\Models\ServiceCategory::find($sourceData['category_id'] ?? null);
                    break;
                    
                case 'gps':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    $data['gps'] = \App\Models\Gps::find($sourceData['gps_id'] ?? null);
                    break;
                    
                case 'kir':
                case 'stnk':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    break;
                    
                case 'service_asuransi':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    $data['asuransi'] = \App\Models\Asuransi::find($sourceData['asuransi_id']);
                    $data['jenis_asuransi'] = \App\Models\JenisAsuransi::find($sourceData['jenis_asuransi_id']);
                    break;
            }
        } catch (\Exception $e) {
            \Log::error("Error loading related data: " . $e->getMessage());
        }
        
        return $data;
    }

    /**
     * Approve pengeluaran
     */
    public function approve(Request $request, $id)
    {
        // Validation
        $request->validate([
            'bukti' => 'required|array|min:1',
            'bukti.*' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip|max:5120',
            'attachment' => 'nullable|array',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip|max:5120',
            'catatan' => 'nullable|string|max:500',
        ]);
        
        // Load Purchasero
        $purchasero = Purchasero::findOrFail($id);
        
        // Authorization check
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya Superadmin yang dapat menyetujui pengeluaran.');
        }
        
        // Status check
        if ($purchasero->status !== 'Pending') {
            return back()->with('error', 'Pengeluaran ini tidak dalam status Pending. Status: ' . $purchasero->status);
        }
        
        DB::beginTransaction();
        
        try {
            // Upload approval files
            $approvalFiles = $this->uploadApprovalFiles($request, $purchasero->id);
            
            // Create approval history
            \App\Models\PurchaseroApproval::create([
                'purchasero_id' => $purchasero->id,
                'user_id' => auth()->id(),
                'action' => 'approved',
                'catatan' => $request->catatan,
                'bukti_files' => $approvalFiles['bukti'] ?? [],
                'attachment_files' => $approvalFiles['attachments'] ?? [],
            ]);
            
            // Transfer data ke tabel tujuan
            $transferService = app(\App\Services\PengeluaranTransferService::class);
            $targetId = $transferService->transfer($purchasero, $approvalFiles);
            
            // Update Purchasero status
            $purchasero->update([
                'status' => 'Disetujui',
                'target_id' => $targetId,
                'can_edit' => false,
                'disetujui_oleh' => auth()->user()->nama ?? auth()->user()->email,
                'tanggal_persetujuan' => now(),
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('purchasero.index')
                ->with('success', 'Pengeluaran berhasil disetujui! Data telah ditransfer ke sistem.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Approval failed for Purchasero #{$id}: " . $e->getMessage());
            
            return back()
                ->with('error', 'Terjadi kesalahan saat menyetujui pengeluaran: ' . $e->getMessage());
        }
    }

    /**
     * Reject pengeluaran
     */
    public function reject(Request $request, $id)
    {
        // Validation - catatan WAJIB untuk reject
        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);
        
        // Load Purchasero
        $purchasero = Purchasero::findOrFail($id);
        
        // Authorization check
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya Superadmin yang dapat menolak pengeluaran.');
        }
        
        // Status check
        if ($purchasero->status !== 'Pending') {
            return back()->with('error', 'Pengeluaran ini tidak dalam status Pending. Status: ' . $purchasero->status);
        }
        
        DB::beginTransaction();
        
        try {
            // Create rejection history
            \App\Models\PurchaseroApproval::create([
                'purchasero_id' => $purchasero->id,
                'user_id' => auth()->id(),
                'action' => 'rejected',
                'catatan' => $request->catatan,
                'bukti_files' => null,
                'attachment_files' => null,
            ]);
            
            // Update status & allow edit
            $purchasero->update([
                'status' => 'Ditolak',
                'can_edit' => true,  // User bisa edit & ajukan ulang
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('purchasero.index')
                ->with('info', 'Pengeluaran ditolak. User dapat melihat alasan dan mengajukan ulang.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Rejection failed for Purchasero #{$id}: " . $e->getMessage());
            
            return back()
                ->with('error', 'Terjadi kesalahan saat menolak pengeluaran: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve multiple pengeluaran
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|max:50',
            'ids.*' => 'required|integer|exists:purchaseros,id',
            'bukti' => 'required|array',
            'bukti.*' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip|max:5120',
            'catatan' => 'nullable|string|max:500',
        ]);
        
        // Authorization check
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya Superadmin yang dapat bulk approve.');
        }
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($request->ids as $id) {
            try {
                $purchasero = Purchasero::find($id);
                
                if (!$purchasero || $purchasero->status !== 'Pending') {
                    $errorCount++;
                    $errors[] = "PR #{$purchasero->no_pr}: Status bukan Pending";
                    continue;
                }
                
                DB::beginTransaction();
                
                // Upload files for this purchasero
                $approvalFiles = $this->uploadApprovalFiles($request, $purchasero->id);
                
                // Create approval
                \App\Models\PurchaseroApproval::create([
                    'purchasero_id' => $purchasero->id,
                    'user_id' => auth()->id(),
                    'action' => 'approved',
                    'catatan' => $request->catatan,
                    'bukti_files' => $approvalFiles['bukti'] ?? [],
                    'attachment_files' => $approvalFiles['attachments'] ?? [],
                ]);
                
                // Transfer
                $transferService = app(\App\Services\PengeluaranTransferService::class);
                $targetId = $transferService->transfer($purchasero, $approvalFiles);
                
                // Update
                $purchasero->update([
                    'status' => 'Disetujui',
                    'target_id' => $targetId,
                    'can_edit' => false,
                    'disetujui_oleh' => auth()->user()->nama ?? auth()->user()->email,
                    'tanggal_persetujuan' => now(),
                ]);
                
                DB::commit();
                $successCount++;
                
            } catch (\Exception $e) {
                DB::rollBack();
                $errorCount++;
                $errors[] = "PR #{$purchasero->no_pr}: " . $e->getMessage();
                \Log::error("Bulk approve error for #{$id}: " . $e->getMessage());
            }
        }
        
        $message = "Bulk approve selesai. Berhasil: {$successCount}, Gagal: {$errorCount}";
        
        if ($errorCount > 0) {
            $message .= ". Errors: " . implode('; ', array_slice($errors, 0, 3));
        }
        
        return redirect()
            ->route('purchasero.index')
            ->with($errorCount > 0 ? 'warning' : 'success', $message);
    }

    /**
     * Bulk reject multiple pengeluaran
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|max:50',
            'ids.*' => 'required|integer|exists:purchaseros,id',
            'catatan' => 'required|string|max:500',  // WAJIB untuk reject
        ]);
        
        // Authorization check
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya Superadmin yang dapat bulk reject.');
        }
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($request->ids as $id) {
            try {
                $purchasero = Purchasero::find($id);
                
                if (!$purchasero || $purchasero->status !== 'Pending') {
                    $errorCount++;
                    continue;
                }
                
                DB::beginTransaction();
                
                \App\Models\PurchaseroApproval::create([
                    'purchasero_id' => $purchasero->id,
                    'user_id' => auth()->id(),
                    'action' => 'rejected',
                    'catatan' => $request->catatan,
                    'bukti_files' => null,
                    'attachment_files' => null,
                ]);
                
                $purchasero->update([
                    'status' => 'Ditolak',
                    'can_edit' => true,
                ]);
                
                DB::commit();
                $successCount++;
                
            } catch (\Exception $e) {
                DB::rollBack();
                $errorCount++;
                \Log::error("Bulk reject error for #{$id}: " . $e->getMessage());
            }
        }
        
        return redirect()
            ->route('purchasero.index')
            ->with('info', "Bulk reject selesai. Berhasil: {$successCount}, Gagal: {$errorCount}");
    }

    /**
     * Upload approval files (bukti & attachments)
     */
    protected function uploadApprovalFiles(Request $request, int $purchaseroId): array
    {
        $uploadedFiles = [
            'bukti' => [],
            'attachments' => [],
        ];
        
        $timestamp = time();
        $approvalDir = "purchasero/approvals/{$purchaseroId}";
        
        // Upload bukti files (REQUIRED)
        if ($request->hasFile('bukti')) {
            $files = is_array($request->file('bukti')) 
                ? $request->file('bukti') 
                : [$request->file('bukti')];
            
            foreach ($files as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $storedName = "{$timestamp}_{$index}_{$originalName}";
                
                $path = $file->storeAs($approvalDir . '/bukti', $storedName, 'public');
                
                $uploadedFiles['bukti'][] = [
                    'original_name' => $originalName,
                    'stored_name' => $storedName,
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path),
                    'size' => $file->getSize(),
                    'extension' => $extension,
                ];
            }
        }
        
        // Upload attachment files (OPTIONAL)
        if ($request->hasFile('attachment')) {
            $files = is_array($request->file('attachment')) 
                ? $request->file('attachment') 
                : [$request->file('attachment')];
            
            foreach ($files as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $storedName = "{$timestamp}_{$index}_{$originalName}";
                
                $path = $file->storeAs($approvalDir . '/attachments', $storedName, 'public');
                
                $uploadedFiles['attachments'][] = [
                    'original_name' => $originalName,
                    'stored_name' => $storedName,
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path),
                    'size' => $file->getSize(),
                    'extension' => $extension,
                ];
            }
        }
        
        return $uploadedFiles;
    }

    /**
     * Withdraw/cancel pengajuan pengeluaran (hanya untuk status Pending)
     */
    public function withdraw($id)
    {
        $purchasero = Purchasero::findOrFail($id);
        
        // Check ownership atau superadmin
        if (auth()->user()->role !== 'superadmin' && $purchasero->pemohon !== auth()->user()->nama) {
            abort(403, 'Anda tidak memiliki akses untuk membatalkan pengajuan ini.');
        }
        
        // Only Pending can be withdrawn
        if ($purchasero->status !== 'Pending') {
            return back()->with('error', 'Hanya pengajuan dengan status Pending yang dapat dibatalkan.');
        }
        
        DB::beginTransaction();
        
        try {
            // Delete temp files
            $interceptor = app(\App\Services\PengeluaranInterceptorService::class);
            $interceptor->deleteTemporaryFiles($purchasero->id);
            
            // Delete purchasero
            $purchasero->delete();
            
            DB::commit();
            
            return redirect()
                ->route('purchasero.index')
                ->with('success', 'Pengajuan berhasil dibatalkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Withdraw failed for Purchasero #{$id}: " . $e->getMessage());
            
            return back()->with('error', 'Gagal membatalkan pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Edit rejected pengeluaran - redirect to original form with pre-filled data
     */
    public function editRejected($id)
    {
        $purchasero = Purchasero::with('latestApproval')->findOrFail($id);
        
        // Validation: Only rejected pengeluaran can be edited
        if ($purchasero->status !== 'Ditolak') {
            return back()->with('error', 'Hanya pengajuan yang ditolak yang dapat diedit.');
        }
        
        if (!$purchasero->can_edit) {
            return back()->with('error', 'Pengajuan ini tidak dapat diedit.');
        }
        
        if (!$purchasero->source_type) {
            return back()->with('error', 'Hanya pengeluaran yang dapat diedit melalui fitur ini.');
        }
        
        // Map source_type to route
        $routeMap = [
            'asuransi_kendaraan' => 'asuransi-kendaraan.index',
            'pajak'              => 'pajak-kendaraan.index',
            'gps'                => 'gps-kendaraan.index',
            'kir'                => 'kir.index',
            'stnk'               => 'stnk.index',
            'service_asuransi'   => 'service-asuransi.index',
            'service_part'       => 'service-history.create',
        ];
        
        $route = $routeMap[$purchasero->source_type] ?? null;
        
        if (!$route) {
            return back()->with('error', 'Form untuk jenis pengeluaran ini tidak ditemukan.');
        }
        
        // Redirect to form with edit parameters
        return redirect()
            ->route($route, [
                'edit_purchasero' => $id,
                'rejection_reason' => $purchasero->latestApproval?->catatan ?? 'Tidak ada catatan',
            ])
            ->with('info', 'Silakan perbaiki data sesuai catatan penolakan, lalu submit ulang.');
    }
}
