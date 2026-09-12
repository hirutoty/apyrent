<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bukubesar;
use App\Models\Keuangan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $role = auth()->user()->role;
        $sort = $request->input('sort', 'terbaru');
        $bulan = $request->input('bulan'); // format: Y-m
        $deptFilter = $request->input('departemen'); // hanya untuk superadmin
        $sourceFilter = $request->input('source_type', ''); // filter jenis pengeluaran

        $query = Pembayaran::query();

        if ($role === 'superadmin') {
            $tab = $request->input('tab', 'Diajukan');
            if ($tab === 'Disetujui') {
                $query->whereIn('status', ['Disetujui', 'Disetujui Sebagian']);
            } elseif ($tab === 'Ditolak') {
                $query->whereIn('status', ['Ditolak', 'Disetujui Sebagian']);
            } elseif (in_array($tab, ['Pending', 'Diajukan'])) {
                $query->where('status', $tab);
            }
            // 'semua' atau nilai lain → tidak filter status (tampilkan semua)
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

        // Filter source_type (jenis pengeluaran)
        if ($sourceFilter) {
            $query->where('source_type', $sourceFilter);
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

        $data = $query->with(['items', 'approvals'])->paginate(15)->withQueryString();

        // Stats (scope sama dengan query utama tapi tanpa pagination)
        $baseQuery = Pembayaran::query();
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
        $totalDisetujui = (clone $baseQuery)->whereIn('status', ['Disetujui', 'Disetujui Sebagian'])->count();
        $totalPending   = (clone $baseQuery)->where('status', 'Pending')->count();
        $totalDitolak   = (clone $baseQuery)->whereIn('status', ['Ditolak', 'Disetujui Sebagian'])->count();
        $totalDiajukan  = (clone $baseQuery)->where('status', 'Diajukan')->count();
        $totalNominal   = (clone $baseQuery)->whereIn('status', ['Diajukan', 'Disetujui', 'Disetujui Sebagian'])->sum('nominal');

        // Source types untuk dropdown filter
        $sourceTypes = Pembayaran::selectRaw('source_type, count(*) as total')
            ->whereNotNull('source_type')
            ->where('source_type', '!=', '')
            ->groupBy('source_type')
            ->pluck('total', 'source_type');

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

        return view('admin.pembayaran.index', compact(
            'data', 'role', 'tab', 'sort', 'deptLabel', 'bulan', 'deptFilter',
            'sourceFilter', 'sourceTypes',
            'totalPR', 'totalDisetujui', 'totalPending', 'totalDitolak', 'totalDiajukan', 'totalNominal'
        ));
    }

    public function create()
    {
        $role = auth()->user()->role;
        
        // Generate No PR preview
        $last = Pembayaran::orderBy('id', 'desc')->first();
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

        return view('admin.pembayaran.create', compact('role', 'noPrPreview', 'departemenOptions', 'deptLabel'));
    }

    public function edit(Pembayaran $pembayaran)
    {
        // Guard: hanya status Pending & Diajukan yang bisa edit
        if (!in_array($pembayaran->status, ['Pending', 'Diajukan'])) {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Pembayaran dengan status ' . $pembayaran->status . ' tidak dapat diedit.');
        }

        // Load items relation
        $pembayaran->load('items');

        return view('admin.pembayaran.edit', compact('pembayaran'));
    }

    public function show(Pembayaran $pembayaran)
    {
        $role = auth()->user()->role;

        $pembayaran->load(['items', 'approvals.user', 'supplier', 'kendaraan', 'serviceParts']);

        return view('admin.pembayaran.show', compact('pembayaran', 'role'));
    }

    public function details($id)
    {
        try {
            $pembayaran = Pembayaran::with('items')->findOrFail($id);
            
            // Format data untuk response
            $data = [
                'id' => $pembayaran->id,
                'no_pr' => $pembayaran->no_pr,
                'tanggal_formatted' => $pembayaran->tanggal ? \Carbon\Carbon::parse($pembayaran->tanggal)->format('d M Y') : '-',
                'departemen' => $pembayaran->departemen ?? '-',
                'pemohon' => $pembayaran->pemohon ?? '-',
                'alasan_permintaan' => $pembayaran->alasan_permintaan ?? '-',
                'status' => $pembayaran->status ?? '-',
                'status_class' => match($pembayaran->status) {
                    'Disetujui' => 'bg-green-100 text-green-600',
                    'Ditolak'   => 'bg-red-100 text-red-600',
                    'Diajukan'  => 'bg-indigo-100 text-indigo-600',
                    'Pending'   => 'bg-yellow-100 text-yellow-600',
                    default     => 'bg-gray-100 text-gray-500',
                },
                'disetujui_oleh' => $pembayaran->disetujui_oleh,
                'tanggal_persetujuan_formatted' => $pembayaran->tanggal_persetujuan ? \Carbon\Carbon::parse($pembayaran->tanggal_persetujuan)->format('d M Y') : null,
                'catatan' => $pembayaran->catatan,
                'terakhir_diajukan_formatted' => $pembayaran->terakhir_diajukan ? \Carbon\Carbon::parse($pembayaran->terakhir_diajukan)->format('d M Y H:i') : null,
                'total_nominal' => $pembayaran->total_nominal,
                'total_nominal_formatted' => number_format($pembayaran->total_nominal, 0, ',', '.'),
                'total_items' => $pembayaran->items->count() > 0 ? $pembayaran->items->count() : 1,
                // Rekening bank & informasi tambahan
                'nama_bank'     => $pembayaran->nama_bank,
                'no_rekening'   => $pembayaran->no_rekening,
                'nama_rekening' => $pembayaran->nama_rekening,
                'informasi'     => $pembayaran->informasi,
            ];

            // Items data (new structure)
            if ($pembayaran->items->count() > 0) {
                $data['items'] = $pembayaran->items->map(function($item) {
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
                $data['barang_jasa'] = $pembayaran->barang_jasa;
                $data['kode_barang'] = $pembayaran->kode_barang;
                $data['qty'] = $pembayaran->qty;
                $data['satuan'] = $pembayaran->satuan;
                $data['nominal'] = $pembayaran->nominal;
                $data['nominal_formatted'] = $pembayaran->nominal ? number_format($pembayaran->nominal, 0, ',', '.') : null;
            }

            return response()->json(['success' => true, 'pembayaran' => $data]);
            
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

        $tipe = $request->input('tipe_pembayaran', 'belanja');

        // ── VALIDASI BERBEDA BERDASARKAN TIPE ──────────────────────
        if ($tipe === 'service') {
            $request->validate([
                'tanggal'                      => 'required|date',
                'departemen'                   => $role === 'superadmin' ? 'required|string|max:255' : 'nullable',
                'pemohon'                      => 'required|string|max:255',
                'alasan_permintaan'            => 'required|string',
                'keterangan'                   => 'nullable|string|max:500',
                'kendaraan_id'                 => 'required|exists:kendaraan,id',
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
                'keterangan'             => 'nullable|string|max:500',
                'items'                  => 'required|array|min:1',
                'items.*.nama_barang'    => 'required|string|max:255',
                'items.*.qty'            => 'required|numeric|min:0.01',
                'items.*.bukti'          => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            ]);
        }

        // Generate No PR
        $last    = Pembayaran::orderBy('id', 'desc')->first();
        $lastNum = $last && preg_match('/(\d+)$/', $last->no_pr, $m) ? (int) $m[1] : 0;
        $noPr    = 'PR-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            if ($tipe === 'service') {
                // ── SIMPAN PEMBAYARAN SERVICE ────────────────────────
                $totalNominal = 0;
                foreach ($request->parts as $part) {
                    $totalNominal += (int) ($part['biaya'] ?? 0);
                }

                $pembayaran = Pembayaran::create([
                    'no_pr'             => $noPr,
                    'tanggal'           => $request->tanggal,
                    'departemen'        => $departemen,
                    'tipe_pembayaran'    => 'service',
                    'pemohon'           => $request->pemohon,
                    'supplier_id'       => $request->supplier_id,
                    'alasan_permintaan' => $request->alasan_permintaan,
                    'keterangan'        => $request->keterangan,
                    'nominal'           => $totalNominal,
                    'status'            => $role === 'superadmin' ? 'Diajukan' : 'Pending',
                    'kendaraan_id'      => $request->kendaraan_id,
                    'tanggal_service'   => $request->tanggal, // sama dengan tanggal pengajuan
                    'kilometer'         => $request->kilometer,
                    'keluhan'           => $request->alasan_permintaan, // alasan permintaan/keluhan
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

                    $pembayaran->serviceParts()->create([
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
                // ── SIMPAN PEMBAYARAN BELANJA ────────────────────────
                $totalNominal = 0;
                foreach ($request->items as $item) {
                    $totalNominal += isset($item['subtotal']) && is_numeric($item['subtotal']) ? (float) $item['subtotal'] : 0;
                }

                $pembayaran = Pembayaran::create([
                    'no_pr'             => $noPr,
                    'tanggal'           => $request->tanggal,
                    'departemen'        => $departemen,
                    'tipe_pembayaran'    => 'belanja',
                    'pemohon'           => $request->pemohon,
                    'supplier_id'       => $request->supplier_id,
                    'alasan_permintaan' => $request->alasan_permintaan,
                    'keterangan'        => $request->keterangan,
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
                        $path = $file->storeAs('pembayaran/bukti', $filename, 'public');
                        $buktiFiles[] = [
                            'filename' => $file->getClientOriginalName(),
                            'path'     => $path,
                            'size'     => $file->getSize(),
                            'type'     => $file->getClientOriginalExtension(),
                        ];
                    }

                    $pembayaran->items()->create([
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
            return redirect()->route('pembayaran.index')
                ->with('success', "Pembayaran {$tipePesan} {$noPr} berhasil " . ($role === 'superadmin' ? 'diajukan' : 'disimpan sebagai Pending') . ".");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        // Guard: hanya status Pending & Diajukan yang bisa edit
        if (!in_array($pembayaran->status, ['Pending', 'Diajukan'])) {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Pembayaran dengan status ' . $pembayaran->status . ' tidak dapat diedit.');
        }

        $role = auth()->user()->role;

        // Departemen logic
        if ($role === 'superadmin') {
            $departemen = $request->departemen;
        } else {
            $departemen = $pembayaran->departemen; // Keep existing departemen
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
            // Update Pembayaran header
            $pembayaran->update([
                'tanggal'           => $request->tanggal,
                'departemen'        => $departemen,
                'pemohon'           => $request->pemohon,
                'supplier_id'       => $request->supplier_id,
                'alasan_permintaan' => $request->alasan_permintaan,
                'keterangan'        => $request->keterangan,
                'nominal'           => $totalNominal,
            ]);

            // Delete existing items
            $pembayaran->items()->delete();

            // Create new items
            foreach ($request->items as $index => $item) {
                $buktiFiles = [];
                
                // Handle file upload untuk bukti per item
                if ($request->hasFile("items.{$index}.bukti")) {
                    $file = $request->file("items.{$index}.bukti");
                    $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('pembayaran/bukti', $filename, 'public');
                    $buktiFiles[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path'     => $path,
                        'size'     => $file->getSize(),
                        'type'     => $file->getClientOriginalExtension(),
                    ];
                }

                $pembayaran->items()->create([
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

            return redirect()->route('pembayaran.index')
                ->with('success', "Pembayaran {$pembayaran->no_pr} berhasil diperbarui dengan " . count($request->items) . " item.");

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat update data: ' . $e->getMessage());
        }
    }

    public function destroy(Pembayaran $pembayaran)
    {
        // Guard: PR yang sudah diajukan/disetujui tidak boleh dihapus
        if (in_array($pembayaran->status, ['Disetujui', 'Diajukan'])) {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Pembayaran yang sudah diajukan/disetujui tidak dapat dihapus.');
        }

        $pembayaran->delete();

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    /**
     * Tombol "Ajukan" — hanya untuk non-superadmin.
     */
    public function ajukan(Pembayaran $pembayaran)
    {
        $role = auth()->user()->role;

        if ($role === 'superadmin') {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Superadmin tidak dapat mengajukan pembayaran.');
        }

        if (in_array($pembayaran->status, ['Diajukan', 'Disetujui'])) {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Pembayaran ini sudah diajukan atau disetujui.');
        }

        $pembayaran->update([
            'status'            => 'Diajukan',
            'terakhir_diajukan' => now(),
        ]);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran ' . $pembayaran->no_pr . ' berhasil diajukan.');
    }

    /**
     * Superadmin: Setujui atau Tolak (dengan catatan wajib jika Ditolak).
     */
    public function updateStatusInline(Request $request, Pembayaran $pembayaran)
    {
        $role = auth()->user()->role;

        if ($role !== 'superadmin') {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Anda tidak memiliki izin untuk mengubah status ini.');
        }

        $request->validate([
            'status'  => 'required|in:Disetujui,Ditolak',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $statusLama = $pembayaran->status;
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

        $pembayaran->update($updateData);

        // ── Hapus jurnal lama jika sebelumnya Disetujui lalu di-Tolak ──
        if ($statusLama === 'Disetujui' && $request->status === 'Ditolak') {
            DB::transaction(function () use ($pembayaran) {
                $kodeJurnal = 'PR-JRN-' . $pembayaran->no_pr;

                // Hapus dari keuangans + recalculate saldo
                $keuangan = Keuangan::where('reference', $kodeJurnal)->first();
                if ($keuangan) {
                    $keuanganId = $keuangan->id;
                    $keuangan->delete();
                    PaymentsController::recalculateKeuanganSaldo($keuanganId);
                }

                // Hapus dari bukubesars + recalculate saldo
                $jurnal = Bukubesar::where('kode_jurnal', $kodeJurnal)
                    ->orWhere('referensi', $pembayaran->no_pr)
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
            DB::transaction(function () use ($pembayaran) {
                $kodeJurnal = 'PR-JRN-' . $pembayaran->no_pr;
                $nominal    = (int) ($pembayaran->nominal ?? 0);

                // ── Catat ke Keuangan ──
                if (!Keuangan::where('reference', $kodeJurnal)->exists()) {
                    $lastSaldo = (float) DB::table('keuangans')
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->value('saldo') ?? 0;

                    Keuangan::create([
                        'tanggal'     => $pembayaran->tanggal_persetujuan ?? now()->toDateString(),
                        'reference'   => $kodeJurnal,
                        'user_id'     => auth()->id(),
                        'kategori'    => 'Pengeluaran',
                        'metode'      => 'Cash',
                        'keterangan'  => 'PR #' . $pembayaran->no_pr . ' - ' . $pembayaran->barang_jasa,
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
                        'transaksi'   => 'Pembayaran: ' . $pembayaran->barang_jasa,
                        'kategori'    => 'Beban',
                        'tanggal'     => $pembayaran->tanggal_persetujuan ?? now()->toDateString(),
                        'debit'       => $nominal,
                        'kredit'      => 0,
                        'saldo'       => $saldoBB - $nominal,
                        'aktivitas'            => 'pembayaran',
                        'keterangan'  => 'PR #' . $pembayaran->no_pr . ' disetujui oleh ' . $pembayaran->disetujui_oleh,
                        'referensi'   => $pembayaran->no_pr,
                    ]);
                }

        // ── Buat service_history jika tipe_pembayaran = service ──
                if ($pembayaran->tipe_pembayaran === 'service' && $pembayaran->kendaraan_id) {
                    $pembayaran->load('serviceParts');

                    $serviceHistory = \App\Models\ServiceHistory::create([
                        'kendaraan_id'      => $pembayaran->kendaraan_id,
                        'tanggal_service'   => $pembayaran->tanggal_service ?? now()->toDateString(),
                        'kilometer'         => $pembayaran->kilometer ?? 0,
                        'keluhan'           => $pembayaran->keluhan,
                        'total_biaya'       => $pembayaran->nominal,
                        'status'            => 'proses',
                        'status_approval'   => 'approved',
                        'approval_by'       => auth()->id(),
                        'approval_at'       => now(),
                        'is_request'        => true,
                    ]);

                    // Cek apakah ada part yang melebihi limit → status_pengeluaran = overservice
                    $hasOverLimit = $pembayaran->serviceParts->contains('is_over_limit', true);

                    if ($hasOverLimit) {
                        $serviceHistory->update(['status_pengeluaran' => 'overservice']);
                    }

                    // Buat ServicePart dari PR service parts
                    foreach ($pembayaran->serviceParts as $prPart) {
                        \App\Models\ServicePart::create([
                            'service_history_id' => $serviceHistory->id,
                            'kendaraan_id'       => $pembayaran->kendaraan_id,
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
                    if ($pembayaran->kilometer) {
                        \App\Models\Kendaraan::where('id', $pembayaran->kendaraan_id)
                            ->update(['kilometer_sekarang' => $pembayaran->kilometer]);
                    }
                }
            });
        }

        $label = $request->status === 'Disetujui' ? 'disetujui' : 'ditolak';

        // Jika Ditolak dan tipe service: set service_history yang pending ke rejected (jika ada)
        if ($request->status === 'Ditolak' && $pembayaran->tipe_pembayaran === 'service') {
            \App\Models\ServiceHistory::where('kendaraan_id', $pembayaran->kendaraan_id)
                ->where('is_request', true)
                ->where('status_approval', 'pending')
                ->update([
                    'status_approval' => 'rejected',
                    'approval_by'     => auth()->id(),
                    'approval_at'     => now(),
                ]);
        }

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran ' . $pembayaran->no_pr . ' berhasil ' . $label . '.');
    }

    /**
     * Setujui pembayaran SERVICE dengan upload bukti pembayaran + lampiran.
     * Dipanggil dari modal khusus di index pembayaran.
     */
    public function approveService(Request $request, Pembayaran $pembayaran)
    {
        if (auth()->user()->role !== 'superadmin') {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Tidak memiliki izin.');
        }

        if ($pembayaran->status !== 'Diajukan' || $pembayaran->tipe_pembayaran !== 'service') {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Pembayaran tidak valid untuk disetujui via modal ini.');
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
                    ->storeAs('pembayaran/bukti', time() . '_bukti_' . $request->file('bukti_pembayaran')->getClientOriginalName(), 'public');
            }

            $lampiranPath = null;
            if ($request->hasFile('lampiran_tambahan')) {
                $lampiranPath = $request->file('lampiran_tambahan')
                    ->storeAs('pembayaran/lampiran', time() . '_lamp_' . $request->file('lampiran_tambahan')->getClientOriginalName(), 'public');
            }

            // Update PR: status Disetujui + simpan path file
            $pembayaran->update([
                'status'               => 'Disetujui',
                'disetujui_oleh'       => auth()->user()->name,
                'tanggal_persetujuan'  => now()->toDateString(),
                'bukti_pembayaran'     => $buktiPath ?? $pembayaran->bukti_pembayaran,
                'lampiran_tambahan'    => $lampiranPath ?? $pembayaran->lampiran_tambahan,
            ]);

            // Buat service_history + parts
            $pembayaran->load('serviceParts');
            $hasOverLimit = $pembayaran->serviceParts->contains('is_over_limit', true);

            $serviceHistory = \App\Models\ServiceHistory::create([
                'kendaraan_id'        => $pembayaran->kendaraan_id,
                'tanggal_service'     => $pembayaran->tanggal_service ?? now()->toDateString(),
                'kilometer'           => $pembayaran->kilometer ?? 0,
                'keluhan'             => $pembayaran->keluhan,
                'total_biaya'         => $pembayaran->nominal,
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
                    'file_path'     => $lampiranPath,
                    'file_name'     => basename($lampiranPath),
                    'file_type'     => 'lampiran',
                    'file_size'     => $request->file('lampiran_tambahan') ? $request->file('lampiran_tambahan')->getSize() : null,
                ]);
            }

            foreach ($pembayaran->serviceParts as $prPart) {
                \App\Models\ServicePart::create([
                    'service_history_id' => $serviceHistory->id,
                    'kendaraan_id'       => $pembayaran->kendaraan_id,
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

            if ($pembayaran->kilometer) {
                \App\Models\Kendaraan::where('id', $pembayaran->kendaraan_id)
                    ->update(['kilometer_sekarang' => $pembayaran->kilometer]);
            }

            DB::commit();

            return redirect()->route('pembayaran.index')
                ->with('success', 'Pembayaran ' . $pembayaran->no_pr . ' berhasil disetujui dan data service tersimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }
    public function terpasang(Request $request, Pembayaran $pembayaran)
    {
        if (auth()->user()->role !== 'superadmin') {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Tidak memiliki izin.');
        }

        if ($pembayaran->status !== 'Disetujui' || $pembayaran->tipe_pembayaran !== 'service') {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Pembayaran tidak bisa diubah ke Terpasang.');
        }

        DB::transaction(function () use ($pembayaran) {
            // Ubah status PR menjadi Terpasang (selesai)
            $pembayaran->update(['status' => 'Terpasang']);

            // Update service_history terkait (via kendaraan + tanggal)
            $sh = \App\Models\ServiceHistory::where('kendaraan_id', $pembayaran->kendaraan_id)
                ->where('is_request', true)
                ->where('status', 'Approved')
                ->whereDate('tanggal_service', $pembayaran->tanggal_service)
                ->first();

            if ($sh) {
                $sh->update(['status' => 'selesai']);
            }
        });

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran ' . $pembayaran->no_pr . ' sudah ditandai Terpasang.');
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
        $pembayaran = Pembayaran::with(['kendaraan', 'approvals.user'])
            ->findOrFail($id);
        
        // Decode source_data dan load related data
        $sourceData = $pembayaran->source_data ?? [];
        $relatedData = [];
        
        // Load related data berdasarkan source_type
        if ($pembayaran->source_type) {
            $relatedData = $this->loadRelatedData($pembayaran->source_type, $sourceData);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'pembayaran' => $pembayaran,
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

                case 'asuransi_kendaraan_perpanjang':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    $data['asuransi_lama'] = \App\Models\AsuransiKendaraan::with(['asuransi', 'jenisAsuransi'])
                        ->find($sourceData['existing_record_id']);
                    $data['asuransi'] = \App\Models\Asuransi::find($sourceData['asuransi_id'] ?? $data['asuransi_lama']?->asuransi_id);
                    $data['jenis_asuransi'] = \App\Models\JenisAsuransi::find($sourceData['jenis_asuransi_id'] ?? $data['asuransi_lama']?->jenis_asuransi_id);
                    break;
                    
                case 'pajak':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    break;

                case 'pajak_perpanjang':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    $data['pajak_lama'] = \App\Models\PajakKendaraan::find($sourceData['existing_record_id']);
                    break;
                    
                case 'service_part':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    $data['category'] = \App\Models\ServiceCategory::find($sourceData['category_id'] ?? null);
                    break;
                    
                case 'gps':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    // Handle multi-item GPS (gps_items array)
                    if (!empty($sourceData['gps_items'])) {
                        $tempFiles = $sourceData['temp_files'] ?? [];
                        $data['gps_items'] = collect($sourceData['gps_items'])->map(function ($item, $idx) use ($tempFiles) {
                            $gpsModel    = \App\Models\Gps::find($item['gps_id'] ?? null);
                            $buktiBayar  = $tempFiles['gps_items'][$idx]['bukti_bayar'] ?? null;
                            $lampiranArr = $tempFiles['gps_items'][$idx]['lampiran'] ?? [];
                            return [
                                'gps_id'       => $item['gps_id'] ?? null,
                                'nama_gps'     => $gpsModel->nama_gps ?? '-',
                                'type'         => $item['type'] ?? '-',
                                'biaya_sewa'   => $item['biaya_sewa'] ?? 0,
                                'nama_bank'    => $item['nama_bank'] ?? null,
                                'no_rekening'  => $item['no_rekening'] ?? null,
                                'nama_pemilik' => $item['nama_pemilik'] ?? null,
                                'bukti_bayar'  => $buktiBayar,
                                'lampiran'     => $lampiranArr,
                            ];
                        })->values()->toArray();
                    } else {
                        $data['gps'] = \App\Models\Gps::find($sourceData['gps_id'] ?? null);
                    }
                    break;

                case 'gps_perpanjang':
                    // kendaraan_id mungkin tidak ada di source_data perpanjangan
                    // ambil dari existing GPS record jika perlu
                    $existingGpsRecord = \App\Models\GpsKendaraan::find($sourceData['existing_record_id'] ?? null);
                    $kendaraanId = $sourceData['kendaraan_id']
                        ?? $existingGpsRecord?->kendaraan_id
                        ?? null;
                    $data['kendaraan'] = \App\Models\Kendaraan::find($kendaraanId);

                    $tempFiles = $sourceData['temp_files'] ?? [];
                    $gpsItems  = $sourceData['gps_items'] ?? [];

                    if (!empty($gpsItems)) {
                        $data['gps_items'] = collect($gpsItems)->map(function ($item, $idx) use ($tempFiles) {
                            $gpsModel    = \App\Models\Gps::find($item['gps_id'] ?? null);
                            $buktiBayar  = $tempFiles['gps_items'][$idx]['bukti_bayar'] ?? null;
                            $lampiranArr = $tempFiles['gps_items'][$idx]['lampiran'] ?? [];
                            return [
                                'gps_id'           => $item['gps_id'] ?? null,
                                'gps_kendaraan_id' => $item['gps_kendaraan_id'] ?? null,
                                'nama_gps'         => $gpsModel->nama_gps ?? '-',
                                'type'             => $item['type'] ?? '-',
                                'biaya_sewa'       => $item['biaya_sewa'] ?? 0,
                                'nama_bank'        => $item['nama_bank'] ?? null,
                                'no_rekening'      => $item['no_rekening'] ?? null,
                                'nama_pemilik'     => $item['nama_pemilik'] ?? null,
                                'bukti_bayar'      => $buktiBayar,
                                'lampiran'         => $lampiranArr,
                            ];
                        })->values()->toArray();
                    } else {
                        // Fallback: single GPS item dari existing_record_id
                        if ($existingGpsRecord) {
                            $existingGpsRecord->load('gps');
                            $data['gps_items'] = [[
                                'gps_id'           => $existingGpsRecord->gps_id,
                                'gps_kendaraan_id' => $existingGpsRecord->id,
                                'nama_gps'         => $existingGpsRecord->gps->nama_gps ?? '-',
                                'type'             => $existingGpsRecord->type,
                                'biaya_sewa'       => $sourceData['biaya_sewa'] ?? $existingGpsRecord->biaya_sewa,
                                'nama_bank'        => $existingGpsRecord->nama_bank,
                                'no_rekening'      => $existingGpsRecord->no_rekening,
                                'nama_pemilik'     => $existingGpsRecord->nama_pemilik,
                                'bukti_bayar'      => null,
                                'lampiran'         => [],
                            ]];
                        }
                    }
                    break;
                    
                case 'kir':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    break;

                case 'kir_perpanjang':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
                    $data['kir_lama'] = \App\Models\Kir::find($sourceData['existing_record_id']);
                    break;

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
        
        // Load Pembayaran
        $pembayaran = Pembayaran::findOrFail($id);
        
        // Authorization check
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya Superadmin yang dapat menyetujui pengeluaran.');
        }
        
        // Status check — terima Pending dan Diajukan (GPS flow menggunakan status Diajukan)
        if (!in_array($pembayaran->status, ['Pending', 'Diajukan'])) {
            return back()->with('error', 'Pengeluaran ini tidak dalam status yang dapat diproses. Status: ' . $pembayaran->status);
        }
        
        DB::beginTransaction();
        
        try {
            // Upload approval files
            $approvalFiles = $this->uploadApprovalFiles($request, $pembayaran->id);
            
            // Create approval history
            \App\Models\PembayaranApproval::create([
                'pembayaran_id' => $pembayaran->id,
                'user_id' => auth()->id(),
                'action' => 'approved',
                'catatan' => $request->catatan,
                'bukti_files' => $approvalFiles['bukti'] ?? [],
                'attachment_files' => $approvalFiles['attachments'] ?? [],
            ]);
            
            // Transfer data ke tabel tujuan
            $transferService = app(\App\Services\PengeluaranTransferService::class);
            $targetId = $transferService->transfer($pembayaran, $approvalFiles);
            
            // Update Pembayaran status
            $pembayaran->update([
                'status' => 'Disetujui',
                'target_id' => $targetId,
                'can_edit' => false,
                'disetujui_oleh' => auth()->user()->nama ?? auth()->user()->email,
                'tanggal_persetujuan' => now(),
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('pembayaran.index')
                ->with('success', 'Pengeluaran berhasil disetujui! Data telah ditransfer ke sistem.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Approval failed for Pembayaran #{$id}: " . $e->getMessage());
            
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
        
        // Load Pembayaran
        $pembayaran = Pembayaran::findOrFail($id);
        
        // Authorization check
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya Superadmin yang dapat menolak pengeluaran.');
        }
        
        // Status check — terima Pending dan Diajukan (GPS flow menggunakan status Diajukan)
        if (!in_array($pembayaran->status, ['Pending', 'Diajukan'])) {
            return back()->with('error', 'Pengeluaran ini tidak dalam status yang dapat diproses. Status: ' . $pembayaran->status);
        }
        
        DB::beginTransaction();
        
        try {
            // Create rejection history
            \App\Models\PembayaranApproval::create([
                'pembayaran_id' => $pembayaran->id,
                'user_id' => auth()->id(),
                'action' => 'rejected',
                'catatan' => $request->catatan,
                'bukti_files' => null,
                'attachment_files' => null,
            ]);
            
            // Update status & allow edit
            $pembayaran->update([
                'status' => 'Ditolak',
                'can_edit' => true,  // User bisa edit & ajukan ulang
            ]);

            // Sync persetujuan ke tabel sumber jika ada existing_record_id
            $this->syncPersetujuanToSource($pembayaran, 'Ditolak');

            // Untuk GPS (tambah baru): update semua record yang terkait pembayaran ini ke 'Ditolak'
            if ($pembayaran->source_type === 'gps') {
                \App\Models\GpsKendaraan::where('pembayaran_id', $pembayaran->id)
                    ->where('persetujuan', 'Diajukan ke Pembayaran')
                    ->update(['persetujuan' => 'Ditolak']);
            }

            // Untuk GPS perpanjang: update via gps_kendaraan_id dari source_data
            if ($pembayaran->source_type === 'gps_perpanjang') {
                $sourceData = $pembayaran->source_data ?? [];
                $gpsItems   = $sourceData['gps_items'] ?? [];
                foreach ($gpsItems as $item) {
                    $gpsKendaraanId = $item['gps_kendaraan_id'] ?? null;
                    if ($gpsKendaraanId) {
                        \App\Models\GpsKendaraan::where('id', $gpsKendaraanId)
                            ->update([
                                'persetujuan' => 'Ditolak',
                                'keterangan'  => $request->catatan,
                            ]);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('pembayaran.index')
                ->with('info', 'Pengeluaran ditolak. User dapat melihat alasan dan mengajukan ulang.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Rejection failed for Pembayaran #{$id}: " . $e->getMessage());
            
            return back()
                ->with('error', 'Terjadi kesalahan saat menolak pengeluaran: ' . $e->getMessage());
        }
    }

    /**
     * Per-item approval untuk GPS multi-item.
     * Menerima keputusan approve/reject per baris GPS beserta bukti masing-masing.
     *
     * Request:
     *   items[{idx}][action]  = 'approved' | 'rejected'
     *   items[{idx}][catatan] = string (wajib jika rejected)
     *   items[{idx}][bukti]   = file (opsional)
     */
    public function approveItems(Request $request, Pembayaran $pembayaran)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya Superadmin yang dapat menyetujui pengeluaran.');
        }

        if (!in_array($pembayaran->status, ['Pending', 'Diajukan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran ini tidak dalam status yang dapat diproses (status: ' . $pembayaran->status . ').',
            ], 422);
        }

        // Validasi
        $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.action'         => 'required|in:approved,rejected',
            'items.*.catatan'        => 'nullable|string|max:500',
            'items.*.bukti'          => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip|max:5120',
        ]);

        $items = $request->input('items');

        // Pastikan setiap item rejected punya catatan
        foreach ($items as $idx => $item) {
            if ($item['action'] === 'rejected' && empty(trim($item['catatan'] ?? ''))) {
                return back()->with('error', "Item #" . ($idx + 1) . " ditolak tapi catatan alasan kosong. Wajib isi alasan penolakan.");
            }
        }

        // Pastikan setiap item approved punya bukti pembayaran
        foreach ($items as $idx => $item) {
            if ($item['action'] === 'approved' && !$request->hasFile("items.{$idx}.bukti")) {
                return response()->json([
                    'success' => false,
                    'message' => "Item #" . ($idx + 1) . " disetujui tapi bukti pembayaran belum diupload. Wajib upload bukti.",
                ], 422);
            }
        }

        // Route based on source_type
        $sourceType = $pembayaran->source_type;
        
        if (in_array($sourceType, ['gps', 'gps_perpanjang'])) {
            return $this->approveItemsGps($request, $pembayaran, $items);
        } elseif ($sourceType === 'service_part') {
            return $this->approveItemsServicePart($request, $pembayaran, $items);
        } else {
            return back()->with('error', 'Source type tidak didukung untuk per-item approval.');
        }
    }

    /**
     * Handle GPS-specific per-item approval
     */
    private function approveItemsGps(Request $request, Pembayaran $pembayaran, array $items)
    {
        DB::beginTransaction();
        try {
            $approvedItems  = [];
            $rejectedItems  = [];
            $buktiDir       = public_path('gps/bukti_bayar');
            if (!file_exists($buktiDir)) mkdir($buktiDir, 0777, true);

            foreach ($items as $idx => $item) {
                $action  = $item['action'];
                $catatan = $item['catatan'] ?? null;

                // Upload bukti per item jika ada
                $buktiBayarPath = null;
                if ($request->hasFile("items.{$idx}.bukti")) {
                    $file           = $request->file("items.{$idx}.bukti");
                    $filename       = time() . '_' . $idx . '_' . $file->getClientOriginalName();
                    $file->move($buktiDir, $filename);
                    $buktiBayarPath = 'gps/bukti_bayar/' . $filename;
                }

                // Catat ke approval history
                \App\Models\PembayaranApproval::create([
                    'pembayaran_id'    => $pembayaran->id,
                    'user_id'          => auth()->id(),
                    'action'           => $action,
                    'catatan'          => $catatan,
                    'bukti_files'      => $buktiBayarPath ? [['path' => $buktiBayarPath]] : null,
                    'attachment_files' => null,
                ]);

                if ($action === 'approved') {
                    // Sertakan gps_id + type dari source_data untuk matching di transferGps
                    $sourceGpsItems = $pembayaran->source_data['gps_items'] ?? [];
                    $sourceItem     = $sourceGpsItems[(int) $idx] ?? [];
                    $approvedItems[] = [
                        'idx'        => (int) $idx,
                        'bukti_path' => $buktiBayarPath,
                        'gps_id'     => $sourceItem['gps_id'] ?? null,
                        'type'       => $sourceItem['type'] ?? null,
                    ];
                } else {
                    $rejectedItems[] = ['idx' => (int) $idx, 'catatan' => $catatan];
                }
            }

            $transferService = app(\App\Services\PengeluaranTransferService::class);

            // Transfer item approved → gps_kendaraan (persetujuan = Disetujui)
            $targetId = null;
            if (!empty($approvedItems)) {
                $targetId = $transferService->transfer($pembayaran, [], $approvedItems);
            }

            // Simpan item rejected → gps_kendaraan (persetujuan = Ditolak)
            if (!empty($rejectedItems)) {
                $transferService->transferGpsRejected($pembayaran, $rejectedItems);
            }

            // Tentukan status PR induk
            $totalItems    = count($items);
            $approvedCount = count($approvedItems);
            $rejectedCount = count($rejectedItems);

            if ($approvedCount === $totalItems) {
                $newStatus = 'Disetujui';
            } elseif ($rejectedCount === $totalItems) {
                $newStatus = 'Ditolak';
            } else {
                $newStatus = 'Disetujui Sebagian';
            }

            // Hitung nominal approved-only dan rejected-only untuk update record
            $sourceGpsItems  = $pembayaran->source_data['gps_items'] ?? [];
            $nominalApproved = 0;
            $nominalRejected = 0;
            foreach ($items as $idx => $decision) {
                $biayaSewa = (int) ($sourceGpsItems[(int) $idx]['biaya_sewa'] ?? 0);
                if ($decision['action'] === 'approved') {
                    $nominalApproved += $biayaSewa;
                } else {
                    $nominalRejected += $biayaSewa;
                }
            }

            $pembayaran->update([
                'status'              => $newStatus,
                'target_id'           => $targetId,
                'can_edit'            => $rejectedCount > 0,
                'disetujui_oleh'      => auth()->user()->nama ?? auth()->user()->email,
                'tanggal_persetujuan' => now(),
                // Simpan nominal approved saja; nominal rejected akan tetap
                // tercatat di source_data agar view bisa menampilkan keduanya
                'nominal'             => $nominalApproved,
            ]);

            // Simpan keputusan per item ke source_data untuk ditampilkan di UI
            $itemDecisions = [];
            foreach ($items as $idx => $decision) {
                $gpsItem  = $sourceGpsItems[(int) $idx] ?? [];
                $gpsModel = isset($gpsItem['gps_id']) ? \App\Models\Gps::find($gpsItem['gps_id']) : null;
                $itemDecisions[] = [
                    'idx'        => (int) $idx,
                    'nama_gps'   => $gpsModel->nama_gps ?? '-',
                    'type'       => $gpsItem['type'] ?? '-',
                    'biaya_sewa' => (int) ($gpsItem['biaya_sewa'] ?? 0),
                    'action'     => $decision['action'],
                ];
            }
            $updatedSourceData = array_merge($pembayaran->source_data ?? [], [
                'item_decisions'   => $itemDecisions,
                'nominal_approved' => $nominalApproved,
                'nominal_rejected' => $nominalRejected,
            ]);
            $pembayaran->update(['source_data' => $updatedSourceData]);

            DB::commit();

            $msg = "Keputusan disimpan: {$approvedCount} disetujui, {$rejectedCount} ditolak. Status PR: {$newStatus}.";
            return redirect()->route('pembayaran.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("approveItems GPS failed for Pembayaran #{$pembayaran->id}: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Handle service_part-specific per-item approval
     */
    private function approveItemsServicePart(Request $request, Pembayaran $pembayaran, array $items)
    {
        DB::beginTransaction();
        try {
            $approvedItems  = [];
            $rejectedItems  = [];
            $buktiDir       = public_path('gps/bukti_bayar');
            if (!file_exists($buktiDir)) mkdir($buktiDir, 0777, true);

            foreach ($items as $idx => $item) {
                $action  = $item['action'];
                $catatan = $item['catatan'] ?? null;

                // Upload bukti per item jika ada
                $buktiBayarPath = null;
                if ($request->hasFile("items.{$idx}.bukti")) {
                    $file           = $request->file("items.{$idx}.bukti");
                    $filename       = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    $file->move($buktiDir, $filename);
                    $buktiBayarPath = 'gps/bukti_bayar/' . $filename;
                }

                // Catat ke approval history
                \App\Models\PembayaranApproval::create([
                    'pembayaran_id'    => $pembayaran->id,
                    'user_id'          => auth()->id(),
                    'action'           => $action,
                    'catatan'          => $catatan,
                    'bukti_files'      => $buktiBayarPath ? [['path' => $buktiBayarPath]] : null,
                    'attachment_files' => null,
                ]);

                if ($action === 'approved') {
                    $approvedItems[] = [
                        'idx'        => (int) $idx,
                        'bukti_path' => $buktiBayarPath,
                    ];
                } else {
                    $rejectedItems[] = ['idx' => (int) $idx, 'catatan' => $catatan];
                }
            }

            $transferService = app(\App\Services\PengeluaranTransferService::class);

            // Transfer item approved → service_history & service_parts
            $targetId = null;
            if (!empty($approvedItems)) {
                // Extract indices only for transfer method
                $approvedIndices = array_column($approvedItems, 'idx');
                $targetId = $transferService->transfer($pembayaran, [], $approvedIndices);
            }

            // Tentukan status PR induk
            $totalItems    = count($items);
            $approvedCount = count($approvedItems);
            $rejectedCount = count($rejectedItems);

            if ($approvedCount === $totalItems) {
                $newStatus = 'Disetujui';
            } elseif ($rejectedCount === $totalItems) {
                $newStatus = 'Ditolak';
            } else {
                $newStatus = 'Disetujui Sebagian';
            }

            $pembayaran->update([
                'status'              => $newStatus,
                'target_id'           => $targetId,
                'can_edit'            => $rejectedCount > 0,
                'disetujui_oleh'      => auth()->user()->nama ?? auth()->user()->email,
                'tanggal_persetujuan' => now(),
            ]);

            // Hitung nominal approved-only dan rejected-only
            $sourceParts     = $pembayaran->source_data['parts'] ?? [];
            $nominalApproved = 0;
            $nominalRejected = 0;
            $itemDecisions   = [];
            foreach ($items as $idx => $decision) {
                $part    = $sourceParts[(int) $idx] ?? [];
                $biaya   = (int) ($part['biaya'] ?? 0);
                if ($decision['action'] === 'approved') {
                    $nominalApproved += $biaya;
                } else {
                    $nominalRejected += $biaya;
                }
                $itemDecisions[] = [
                    'idx'       => (int) $idx,
                    'nama_part' => $part['nama_part'] ?? '-',
                    'category'  => $part['category_nama'] ?? '-',
                    'biaya'     => $biaya,
                    'action'    => $decision['action'],
                    'catatan'   => $decision['catatan'] ?? null,
                ];
            }

            // Update nominal ke approved-only saja
            $pembayaran->update(['nominal' => $nominalApproved]);

            // Simpan keputusan + nominal terpisah ke source_data
            $updatedSourceData = array_merge($pembayaran->source_data ?? [], [
                'item_decisions'   => $itemDecisions,
                'nominal_approved' => $nominalApproved,
                'nominal_rejected' => $nominalRejected,
            ]);
            $pembayaran->update(['source_data' => $updatedSourceData]);

            // Tandai part yang ditolak di source_data dengan status_approval = 'rejected'
            if (!empty($rejectedItems)) {
                $sourceData = $pembayaran->source_data ?? [];
                foreach ($rejectedItems as $rejected) {
                    $idx = $rejected['idx'];
                    if (isset($sourceData['parts'][$idx])) {
                        $sourceData['parts'][$idx]['status_approval'] = 'rejected';
                        $sourceData['parts'][$idx]['catatan_penolakan'] = $rejected['catatan'] ?? '';
                    }
                }
                $pembayaran->update(['source_data' => $sourceData]);
            }

            DB::commit();

            $msg = "Keputusan disimpan: {$approvedCount} part disetujui, {$rejectedCount} part ditolak. Status PR: {$newStatus}.";
            return redirect()->route('pembayaran.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("approveItems ServicePart failed for Pembayaran #{$pembayaran->id}: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve multiple pengeluaran
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|max:50',
            'ids.*' => 'required|integer|exists:pembayarans,id',
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
                $pembayaran = Pembayaran::find($id);
                
                if (!$pembayaran || $pembayaran->status !== 'Pending') {
                    $errorCount++;
                    $errors[] = "PR #{$pembayaran->no_pr}: Status bukan Pending";
                    continue;
                }
                
                DB::beginTransaction();
                
                // Upload files for this pembayaran
                $approvalFiles = $this->uploadApprovalFiles($request, $pembayaran->id);
                
                // Create approval
                \App\Models\PembayaranApproval::create([
                    'pembayaran_id' => $pembayaran->id,
                    'user_id' => auth()->id(),
                    'action' => 'approved',
                    'catatan' => $request->catatan,
                    'bukti_files' => $approvalFiles['bukti'] ?? [],
                    'attachment_files' => $approvalFiles['attachments'] ?? [],
                ]);
                
                // Transfer
                $transferService = app(\App\Services\PengeluaranTransferService::class);
                $targetId = $transferService->transfer($pembayaran, $approvalFiles);
                
                // Update
                $pembayaran->update([
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
                $errors[] = "PR #{$pembayaran->no_pr}: " . $e->getMessage();
                \Log::error("Bulk approve error for #{$id}: " . $e->getMessage());
            }
        }
        
        $message = "Bulk approve selesai. Berhasil: {$successCount}, Gagal: {$errorCount}";
        
        if ($errorCount > 0) {
            $message .= ". Errors: " . implode('; ', array_slice($errors, 0, 3));
        }
        
        return redirect()
            ->route('pembayaran.index')
            ->with($errorCount > 0 ? 'warning' : 'success', $message);
    }

    /**
     * Bulk reject multiple pengeluaran
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|max:50',
            'ids.*' => 'required|integer|exists:pembayarans,id',
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
                $pembayaran = Pembayaran::find($id);
                
                if (!$pembayaran || $pembayaran->status !== 'Pending') {
                    $errorCount++;
                    continue;
                }
                
                DB::beginTransaction();
                
                \App\Models\PembayaranApproval::create([
                    'pembayaran_id' => $pembayaran->id,
                    'user_id' => auth()->id(),
                    'action' => 'rejected',
                    'catatan' => $request->catatan,
                    'bukti_files' => null,
                    'attachment_files' => null,
                ]);
                
                $pembayaran->update([
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
            ->route('pembayaran.index')
            ->with('info', "Bulk reject selesai. Berhasil: {$successCount}, Gagal: {$errorCount}");
    }

    /**
     * Sync persetujuan ke tabel sumber (pajak_kendaraans, dll) berdasarkan existing_record_id
     * Dipanggil saat reject/approve agar badge persetujuan di halaman modul ikut update.
     */
    protected function syncPersetujuanToSource(\App\Models\Pembayaran $pembayaran, string $status): void
    {
        $sourceData = $pembayaran->source_data ?? [];
        $existingId = $sourceData['existing_record_id'] ?? null;

        if (!$existingId) return;

        try {
            match($pembayaran->source_type) {
                'pajak'               => \App\Models\PajakKendaraan::where('id', $existingId)
                    ->update(['persetujuan' => $status]),
                'asuransi_kendaraan'  => \App\Models\AsuransiKendaraan::where('id', $existingId)
                    ->update(['persetujuan' => $status]),
                'kir'                 => \App\Models\Kir::where('id', $existingId)
                    ->update(['persetujuan' => $status]),
                default => null,
            };
        } catch (\Exception $e) {
            \Log::warning("syncPersetujuanToSource failed for Pembayaran #{$pembayaran->id}: " . $e->getMessage());
        }
    }

    /**
     * Upload approval files (bukti & attachments)
     */
    protected function uploadApprovalFiles(Request $request, int $pembayaranId): array
    {
        $uploadedFiles = [
            'bukti' => [],
            'attachments' => [],
        ];
        
        $timestamp = time();
        $approvalDir = "pembayaran/approvals/{$pembayaranId}";
        
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
        $pembayaran = Pembayaran::findOrFail($id);
        
        // Check ownership atau superadmin
        if (auth()->user()->role !== 'superadmin' && $pembayaran->pemohon !== auth()->user()->nama) {
            abort(403, 'Anda tidak memiliki akses untuk membatalkan pengajuan ini.');
        }
        
        // Only Pending can be withdrawn
        if ($pembayaran->status !== 'Pending') {
            return back()->with('error', 'Hanya pengajuan dengan status Pending yang dapat dibatalkan.');
        }
        
        DB::beginTransaction();
        
        try {
            // Delete temp files
            $interceptor = app(\App\Services\PengeluaranInterceptorService::class);
            $interceptor->deleteTemporaryFiles($pembayaran->id);
            
            // Delete pembayaran
            $pembayaran->delete();
            
            DB::commit();
            
            return redirect()
                ->route('pembayaran.index')
                ->with('success', 'Pengajuan berhasil dibatalkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Withdraw failed for Pembayaran #{$id}: " . $e->getMessage());
            
            return back()->with('error', 'Gagal membatalkan pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Edit rejected pengeluaran - redirect to original form with pre-filled data
     */
    public function editRejected($id)
    {
        $pembayaran = Pembayaran::with('latestApproval')->findOrFail($id);
        
        // Validation: Only rejected pengeluaran can be edited
        if ($pembayaran->status !== 'Ditolak') {
            return back()->with('error', 'Hanya pengajuan yang ditolak yang dapat diedit.');
        }
        
        if (!$pembayaran->can_edit) {
            return back()->with('error', 'Pengajuan ini tidak dapat diedit.');
        }
        
        if (!$pembayaran->source_type) {
            return back()->with('error', 'Hanya pengeluaran yang dapat diedit melalui fitur ini.');
        }
        
        // Map source_type to route
        $routeMap = [
            'asuransi_kendaraan'            => 'asuransi-kendaraan.ajukan-ulang',
            'asuransi_kendaraan_perpanjang' => 'asuransi-kendaraan.index',
            'pajak'                         => 'pajak.ajukan-ulang',
            'pajak_perpanjang'              => 'pajak.index',
            'gps'                           => 'gps-kendaraan.index',
            'gps_perpanjang'                => 'gps-kendaraan.index',
            'kir'                           => 'kir.index',
            'kir_perpanjang'                => 'kir.index',
            'stnk'                          => 'stnk.index',
            'service_asuransi'              => 'service-asuransi.index',
            'service_part'                  => 'service-history.create',
            'purchase_order'                => 'purchase-order.index',
        ];

        $route = $routeMap[$pembayaran->source_type] ?? null;

        if (!$route) {
            return back()->with('error', 'Form untuk jenis pengeluaran ini tidak ditemukan.');
        }

        // Pajak pakai dedicated page dengan parameter ID pembayaran
        if ($pembayaran->source_type === 'pajak') {
            return redirect()->route('pajak.ajukan-ulang', $pembayaran->id)
                ->with('info', 'Silakan perbaiki data sesuai catatan penolakan, lalu ajukan ulang.');
        }

        // Asuransi kendaraan pakai dedicated page
        if ($pembayaran->source_type === 'asuransi_kendaraan') {
            return redirect()->route('asuransi-kendaraan.ajukan-ulang', $pembayaran->id)
                ->with('info', 'Silakan perbaiki data sesuai catatan penolakan, lalu ajukan ulang.');
        }

        // KIR pakai dedicated page
        if ($pembayaran->source_type === 'kir') {
            return redirect()->route('kir.ajukan-ulang', $pembayaran->id)
                ->with('info', 'Silakan perbaiki data sesuai catatan penolakan, lalu ajukan ulang.');
        }

        // Redirect to form with edit parameters
        return redirect()
            ->route($route, [
                'edit_pembayaran' => $id,
                'rejection_reason' => $pembayaran->latestApproval?->catatan ?? 'Tidak ada catatan',
            ])
            ->with('info', 'Silakan perbaiki data sesuai catatan penolakan, lalu submit ulang.');
    }
}



