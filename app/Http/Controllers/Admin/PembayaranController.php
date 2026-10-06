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
                // Tampilkan PR Disetujui + PR Diajukan yang punya item approved
                // (partial approval: satu item sudah disetujui, satunya masih diajukan ulang)
                $query->where(function ($q) {
                    $q->where('status', 'Disetujui')
                      ->orWhere(function ($q2) {
                          $q2->where('status', 'Diajukan')
                             ->whereRaw("JSON_SEARCH(JSON_EXTRACT(source_data, '$.item_decisions[*].action'), 'one', 'approved') IS NOT NULL");
                      });
                });
            } elseif ($tab === 'Ditolak') {
                // Tampilkan: PR yang Ditolak sepenuhnya,
                // ATAU PR yang Disetujui tapi punya item_decisions dengan action=rejected
                $query->where(function ($q) {
                    $q->where('status', 'Ditolak')
                      ->orWhere(function ($q2) {
                          $q2->where('status', 'Disetujui')
                             ->whereRaw("JSON_SEARCH(JSON_EXTRACT(source_data, '$.item_decisions[*].action'), 'one', 'rejected') IS NOT NULL");
                      });
                });
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

        $data = $query->with(['items', 'approvals', 'serviceParts'])->paginate(15)->withQueryString();

        // ── Task 1: Attach badge_stats per-item untuk source_type yang support per-item decision ──
        $supportedTypes = ['service_asuransi', 'service_part', 'service_incident', 'gps', 'gps_perpanjang'];
        foreach ($data as $item) {
            if (!in_array($item->source_type, $supportedTypes)) {
                continue;
            }

            $sourceData    = is_array($item->source_data) ? $item->source_data : (json_decode($item->source_data, true) ?? []);
            // PENTING: gunakan integer key agar in_array() dan has() konsisten
            $itemDecisions = collect($sourceData['item_decisions'] ?? [])->keyBy(fn($dec) => (int)($dec['idx'] ?? -1));

            // Ambil item list sesuai source_type
            $itemList = match($item->source_type) {
                'service_part', 'service_incident' => $sourceData['parts'] ?? [],
                'service_asuransi'                 => $sourceData['kejadians'] ?? [],
                'gps', 'gps_perpanjang'            => $sourceData['gps_items'] ?? [],
                default => [],
            };

            $approvedCount = 0;
            $rejectedCount = 0;
            $pendingCount  = 0;

            if (!empty($itemDecisions) && !empty($itemList)) {
                // Ada keputusan per-item → hitung approved/rejected
                foreach ($itemDecisions as $idx => $decision) {
                    if (($decision['action'] ?? '') === 'approved') {
                        $approvedCount++;
                    } else {
                        $rejectedCount++;
                    }
                }

                // Sisa item yang belum diputuskan = pending (integer key lookup)
                foreach ($itemList as $itemIdx => $itemData) {
                    if (!$itemDecisions->has((int)$itemIdx)) {
                        $pendingCount++;
                    }
                }
            } elseif (!empty($itemList)) {
                // Belum ada keputusan → semua item pending/diajukan (tergantung status PR)
                $totalItems = count($itemList);
                if (in_array($item->status, ['Pending', 'Diajukan'])) {
                    $pendingCount = $totalItems;
                } elseif ($item->status === 'Disetujui') {
                    $approvedCount = $totalItems;
                } elseif ($item->status === 'Ditolak') {
                    $rejectedCount = $totalItems;
                }
            }

            // ── Filter badge sesuai tab aktif untuk PR partial ──────────────
            // PR partial (Diajukan + ada approved decisions) muncul di 2 tab:
            // - Tab Diajukan  → tampilkan hanya badge pending (item yang belum diputuskan)
            // - Tab Disetujui → tampilkan hanya badge approved (item yang sudah disetujui)
            if ($item->status === 'Diajukan' && !$itemDecisions->isEmpty()) {
                if ($tab === 'Disetujui') {
                    // Di tab Disetujui: hanya tampilkan item approved
                    $pendingCount  = 0;
                    $rejectedCount = 0;
                } elseif ($tab === 'Diajukan') {
                    // Di tab Diajukan: hanya tampilkan item pending (belum diputuskan)
                    $approvedCount = 0;
                    $rejectedCount = 0;
                }
            }

            // Attach badge_stats ke item
            $item->badge_stats = [
                'approved' => $approvedCount,
                'rejected' => $rejectedCount,
                'pending'  => $pendingCount,
            ];
        }

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

        // Stats per item — loop semua PR dan hitung dari item_decisions
        $allPRsForStats = (clone $baseQuery)->get(['id', 'status', 'nominal', 'nominal_original', 'source_type', 'source_data']);

        $totalItemDisetujui = 0;
        $totalItemDitolak   = 0;
        $totalItemPending   = 0;
        $totalItemDiajukan  = 0;
        $nominalDisetujui   = 0;
        $nominalDitolak     = 0;
        $nominalPending     = 0;
        $nominalDiajukan    = 0;

        foreach ($allPRsForStats as $_pr) {
            $_sd       = is_array($_pr->source_data) ? $_pr->source_data : (json_decode($_pr->source_data, true) ?? []);
            // PENTING: integer key agar has() dan lookup konsisten
            $_dec      = collect($_sd['item_decisions'] ?? [])->keyBy(fn($d) => (int)($d['idx'] ?? -1));
            $_srcType  = $_pr->source_type ?? '';
            $_status   = $_pr->status ?? '';

            // Ambil item list sesuai source_type
            $_itemList = match(true) {
                in_array($_srcType, ['service_part', 'service_incident']) => $_sd['parts'] ?? [],
                $_srcType === 'service_asuransi'                          => $_sd['kejadians'] ?? [],
                in_array($_srcType, ['gps', 'gps_perpanjang'])            => $_sd['gps_items'] ?? [],
                default => [],
            };
            $_nomField = in_array($_srcType, ['gps', 'gps_perpanjang']) ? 'biaya_sewa' : 'biaya';

            if (!empty($_dec) && !empty($_itemList)) {
                // Punya item_decisions → hitung per item
                foreach ($_dec as $_idx => $_d) {
                    $_iNom = (int)(($_itemList[(int)($_d['idx'] ?? $_idx)][$_nomField] ?? 0));
                    if (($_d['action'] ?? '') === 'approved') {
                        $totalItemDisetujui++;
                        $nominalDisetujui += $_iNom;
                    } else {
                        $totalItemDitolak++;
                        $nominalDitolak += $_iNom;
                    }
                }
                // Sisa item yang belum ada keputusan (integer key lookup)
                foreach ($_itemList as $_iIdx => $_iItem) {
                    if (!$_dec->has((int)$_iIdx)) {
                        $_iNom = (int)($_iItem[$_nomField] ?? 0);
                        if ($_status === 'Pending') {
                            $totalItemPending++;
                            $nominalPending += $_iNom;
                        } elseif ($_status === 'Diajukan') {
                            $totalItemDiajukan++;
                            $nominalDiajukan += $_iNom;
                        } elseif ($_status === 'Disetujui') {
                            // Partial approval: PR sudah Disetujui tapi masih ada item
                            // yang belum diputuskan → tetap hitung sebagai pending
                            $totalItemPending++;
                            $nominalPending += $_iNom;
                        }
                    }
                }
            } elseif (!empty($_itemList)) {
                // Belum ada item_decisions → seluruh item ikut status PR
                $cnt    = count($_itemList);
                $_prNom = (int)($_pr->nominal_original ?? $_pr->nominal ?? 0);
                if ($_status === 'Pending') {
                    $totalItemPending  += $cnt;
                    $nominalPending    += $_prNom;
                } elseif ($_status === 'Diajukan') {
                    $totalItemDiajukan += $cnt;
                    $nominalDiajukan   += $_prNom;
                } elseif ($_status === 'Disetujui') {
                    $totalItemDisetujui += $cnt;
                    $nominalDisetujui   += $_prNom;
                } elseif ($_status === 'Ditolak') {
                    $totalItemDitolak  += $cnt;
                    $nominalDitolak    += $_prNom;
                }
            } else {
                // Fallback: 1 PR = 1 item (pajak, asuransi, kir, stnk, belanja biasa)
                $_prNom = (int)($_pr->nominal_original ?? $_pr->nominal ?? 0);
                if ($_status === 'Pending') {
                    $totalItemPending++;
                    $nominalPending += $_prNom;
                } elseif ($_status === 'Diajukan') {
                    $totalItemDiajukan++;
                    $nominalDiajukan += $_prNom;
                } elseif ($_status === 'Disetujui') {
                    $totalItemDisetujui++;
                    $nominalDisetujui += $_prNom;
                } elseif ($_status === 'Ditolak') {
                    $totalItemDitolak++;
                    $nominalDitolak += $_prNom;
                }
            }
        }

        // Alias untuk view (tetap kompatibel dengan nama lama)
        $totalDisetujui = $totalItemDisetujui;
        $totalDitolak   = $totalItemDitolak;
        $totalPending   = $totalItemPending;
        $totalDiajukan  = $totalItemDiajukan;

        // Nominal total — pakai nominal_original (total sebelum partial approval) jika ada,
        // fallback ke nominal untuk record lama atau yang tidak partial
        $totalNominal = (clone $baseQuery)->get(['nominal', 'nominal_original'])
            ->sum(fn($p) => $p->nominal_original ?? $p->nominal ?? 0);

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

        // Mapping email → nama user untuk kolom pemohon
        $pemohonEmails = $data->pluck('pemohon')->filter()->unique()->values();
        $userNames = \App\Models\User::whereIn('email', $pemohonEmails)
            ->pluck('name', 'email');

        // Supplier list untuk dropdown di modal resubmit rejected items
        $suppliers = \App\Models\Supplier::orderBy('nama_supplier')
            ->get(['id', 'nama_supplier']);

        return view('admin.pembayaran.index', compact(
            'data', 'role', 'tab', 'sort', 'deptLabel', 'bulan', 'deptFilter',
            'sourceFilter', 'sourceTypes', 'userNames', 'suppliers',
            'totalPR', 'totalDisetujui', 'totalPending', 'totalDitolak', 'totalDiajukan', 'totalNominal',
            'nominalDisetujui', 'nominalPending', 'nominalDitolak', 'nominalDiajukan'
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
            $pembayaran = Pembayaran::with(['items', 'approvals', 'supplier'])->findOrFail($id);

            // Nama pemohon dari tabel users berdasarkan email
            $pemohonNama = \App\Models\User::where('email', $pembayaran->pemohon)->value('name');

            // Hitung total nominal yang relevan — sama persis dengan logika showApprovalModal:
            // Exclude item yang sudah rejected, include yang pending/approved
            $sourceData    = $pembayaran->source_data ?? [];
            $itemDecisions = $sourceData['item_decisions'] ?? [];
            $totalNominalDisplay = $pembayaran->total_nominal;

            if (!empty($itemDecisions)) {
                $srcType = $pembayaran->source_type;
                // Integer key agar get($idx) konsisten dengan loop array integer index
                $decMap  = collect($itemDecisions)->keyBy(fn($d) => (int)($d['idx'] ?? -1));

                if (in_array($srcType, ['gps', 'gps_perpanjang'])) {
                    $gpsItems = $sourceData['gps_items'] ?? [];
                    $totalNominalDisplay = collect($gpsItems)->reduce(function ($sum, $item, $idx) use ($decMap) {
                        $action = $decMap->get((int)$idx)['action'] ?? null;
                        if ($action !== 'rejected') $sum += (int) ($item['biaya_sewa'] ?? 0);
                        return $sum;
                    }, 0);
                } elseif (in_array($srcType, ['service_part', 'service_incident'])) {
                    $parts = $sourceData['parts'] ?? [];
                    $totalNominalDisplay = collect($parts)->reduce(function ($sum, $part, $idx) use ($decMap) {
                        $action = $decMap->get((int)$idx)['action'] ?? null;
                        if ($action !== 'rejected') $sum += (int) ($part['biaya'] ?? 0);
                        return $sum;
                    }, 0);
                } elseif ($srcType === 'service_asuransi') {
                    $kejadians = $sourceData['kejadians'] ?? [];
                    $totalNominalDisplay = collect($kejadians)->reduce(function ($sum, $kej, $idx) use ($decMap) {
                        $action = $decMap->get((int)$idx)['action'] ?? null;
                        if ($action !== 'rejected') $sum += (int) ($kej['biaya'] ?? 0);
                        return $sum;
                    }, 0);
                }

                // Fallback jika hasil 0 (semua pending belum ada keputusan)
                if ($totalNominalDisplay === 0) {
                    $totalNominalDisplay = $pembayaran->total_nominal;
                }
            }

            // ── Source type label ───────────────────────────────────
            $sourceTypeName = match($pembayaran->source_type) {
                'pajak'                          => 'Pajak Kendaraan',
                'pajak_perpanjang'               => 'Perpanjangan Pajak',
                'asuransi_kendaraan'             => 'Asuransi Kendaraan',
                'asuransi_kendaraan_perpanjang'  => 'Perpanjangan Asuransi',
                'service_part'                   => 'Service Part',
                'service_asuransi'               => 'Service Asuransi',
                'service_incident'               => 'Service Incident',
                'gps'                            => 'GPS Kendaraan',
                'gps_perpanjang'                 => 'Perpanjangan GPS',
                'kir'                            => 'KIR',
                'kir_perpanjang'                 => 'Perpanjangan KIR',
                'stnk'                           => 'STNK',
                'purchase_order'                 => 'Purchase Order',
                default                          => $pembayaran->source_type ? ucwords(str_replace('_', ' ', $pembayaran->source_type)) : 'Belanja',
            };

            // ── Info kendaraan & service dari source_data ────────────
            $sd        = $pembayaran->source_data ?? [];
            $kendaraan = isset($sd['kendaraan_id']) ? \App\Models\Kendaraan::find($sd['kendaraan_id']) : null;
            $kendaraanInfo = null;
            if ($kendaraan) {
                $kendaraanInfo = [
                    'nopol' => $kendaraan->nopol,
                    'merk'  => $kendaraan->merk,
                    'label' => $kendaraan->nopol . ' — ' . $kendaraan->merk,
                ];
            }

            $serviceInfo = null;
            if (in_array($pembayaran->source_type, ['service_part', 'service_incident', 'service_asuransi'])) {
                $serviceInfo = [
                    'tanggal_service'  => isset($sd['tanggal_service']) ? \Carbon\Carbon::parse($sd['tanggal_service'])->format('d M Y') : null,
                    'kilometer'        => $sd['kilometer'] ?? null,
                    'keluhan'          => $sd['keluhan'] ?? null,
                    'nama_asuransi'    => $sd['nama_asuransi'] ?? null,
                    'keterangan'       => $sd['keterangan'] ?? $pembayaran->keterangan ?? null,
                ];
            }

            // ── Info approval dari approvals relation ────────────────
            $approvalInfo = null;
            $approvedRec  = $pembayaran->approvals->where('action', 'approved')->sortByDesc('created_at')->first();
            $rejectedRec  = $pembayaran->approvals->where('action', 'rejected')->sortByDesc('created_at')->first();
            $latestRec    = $pembayaran->approvals->sortByDesc('created_at')->first();
            if ($latestRec) {
                $approvalInfo = [
                    'action'         => $latestRec->action,
                    'oleh'           => $latestRec->approved_by ?? $pembayaran->disetujui_oleh,
                    'tanggal'        => $latestRec->created_at ? $latestRec->created_at->format('d M Y') : null,
                    'catatan'        => $latestRec->catatan ?? $pembayaran->catatan,
                ];
            } elseif ($pembayaran->disetujui_oleh || $pembayaran->tanggal_persetujuan) {
                $approvalInfo = [
                    'action'  => $pembayaran->status === 'Disetujui' ? 'approved' : ($pembayaran->status === 'Ditolak' ? 'rejected' : null),
                    'oleh'    => $pembayaran->disetujui_oleh,
                    'tanggal' => $pembayaran->tanggal_persetujuan ? \Carbon\Carbon::parse($pembayaran->tanggal_persetujuan)->format('d M Y') : null,
                    'catatan' => $pembayaran->catatan,
                ];
            }

            // Format data untuk response
            $data = [
                'id' => $pembayaran->id,
                'no_pr' => $pembayaran->no_pr,
                'source_type' => $pembayaran->source_type,
                'source_type_name' => $sourceTypeName,
                'tanggal_formatted' => $pembayaran->tanggal ? \Carbon\Carbon::parse($pembayaran->tanggal)->format('d M Y') : '-',
                'departemen' => $pembayaran->departemen ?? '-',
                'pemohon' => $pembayaran->pemohon ?? '-',
                'pemohon_nama' => $pemohonNama,
                'vendor' => $pembayaran->supplier ? $pembayaran->supplier->nama_supplier : null,
                'alasan_permintaan' => $pembayaran->alasan_permintaan ?? $sd['keluhan'] ?? $sd['alasan_permintaan'] ?? '-',
                'keterangan' => $pembayaran->keterangan ?? $sd['keterangan'] ?? null,
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
                'total_nominal' => $totalNominalDisplay,
                'total_nominal_formatted' => number_format($totalNominalDisplay, 0, ',', '.'),
                'total_items' => $pembayaran->items->count() > 0 ? $pembayaran->items->count() : 1,
                // Rekening bank & informasi tambahan (level PR — untuk non service_part/service_incident)
                'nama_bank'     => $pembayaran->nama_bank,
                'no_rekening'   => $pembayaran->no_rekening,
                'nama_rekening' => $pembayaran->nama_rekening,
                'informasi'     => $pembayaran->informasi,
                // Extra
                'kendaraan'    => $kendaraanInfo,
                'service_info' => $serviceInfo,
                'approval'     => $approvalInfo,
            ];

            // Items data (new structure — belanja/purchase order)
            if ($pembayaran->items->count() > 0) {
                $data['items'] = $pembayaran->items->map(function($item) {
                    // Resolve bukti file
                    $buktiFinal = null;
                    if ($item->bukti) {
                        $b = is_array($item->bukti) ? $item->bukti : (json_decode($item->bukti, true) ?? []);
                        if (!empty($b)) {
                            $bFirst = $b[0] ?? $b;
                            $bPath  = is_array($bFirst) ? ($bFirst['path'] ?? '') : $bFirst;
                            $bName  = is_array($bFirst) ? ($bFirst['original_name'] ?? basename($bPath)) : basename($bFirst);
                            if ($bPath) $buktiFinal = ['url' => asset('storage/'.$bPath), 'name' => $bName];
                        }
                    }
                    return [
                        'nama_barang'            => $item->nama_barang,
                        'kategori'               => $item->kategori,
                        'posisi'                 => $item->posisi,
                        'part_number'            => $item->part_number,
                        'serial_number'          => $item->serial_number,
                        'qty'                    => $item->qty,
                        'satuan'                 => $item->satuan,
                        'harga_satuan_formatted' => $item->harga_satuan ? number_format($item->harga_satuan, 0, ',', '.') : null,
                        'subtotal'               => $item->subtotal,
                        'subtotal_formatted'     => $item->subtotal ? number_format($item->subtotal, 0, ',', '.') : null,
                        'spesifikasi'            => $item->spesifikasi,
                        'merk'                   => $item->merk,
                        'keterangan'             => $item->keterangan,
                        'bukti'                  => $buktiFinal,
                    ];
                });
            } elseif ($pembayaran->source_type) {
                // Source_type dari kendaraan — bangun items dari source_data
                $srcType = $pembayaran->source_type;
                // $sd sudah di-set di atas
                // Integer key agar get($idx) konsisten dengan loop array integer index
                $decMap  = collect($sd['item_decisions'] ?? [])->keyBy(fn($d) => (int)($d['idx'] ?? -1));

                $kendaraanLabel = $kendaraan
                    ? ($kendaraan->nopol . ' — ' . $kendaraan->merk)
                    : null;

                // Helper: resolve lampiran array menjadi [{url, name}]
                $resolveLampiran = function(array $files) {
                    return collect($files)->map(function($f) {
                        $path = is_array($f) ? ($f['path'] ?? '') : $f;
                        $name = is_array($f) ? ($f['original_name'] ?? basename($path)) : basename($f);
                        if (!$path) return null;
                        return ['url' => asset('storage/'.$path), 'name' => $name];
                    })->filter()->values()->all();
                };

                if (in_array($srcType, ['gps', 'gps_perpanjang'])) {
                    $gpsItems = $sd['gps_items'] ?? [];
                    $data['items'] = collect($gpsItems)->map(function ($item, $idx) use ($decMap, $sd, $resolveLampiran) {
                        $action  = $decMap->get($idx)['action'] ?? null;
                        $gpsRaw  = $sd['temp_files']['gps_items'][$idx]['lampiran'] ?? [];
                        return [
                            'nama_barang'        => $item['nama_gps'] ?? ('GPS #' . ($idx + 1)),
                            'kategori'           => $item['type'] ?? '-',
                            'qty'                => 1,
                            'satuan'             => 'unit',
                            'subtotal'           => $item['biaya_sewa'] ?? 0,
                            'subtotal_formatted' => number_format($item['biaya_sewa'] ?? 0, 0, ',', '.'),
                            'nama_bank'          => $item['nama_bank'] ?? null,
                            'no_rekening'        => $item['no_rekening'] ?? null,
                            'nama_rekening'      => $item['nama_pemilik'] ?? null,
                            'lampiran'           => $resolveLampiran($gpsRaw),
                            'bukti'              => null,
                            'status_item'        => $action,
                        ];
                    })->values()->all();

                } elseif (in_array($srcType, ['service_part', 'service_incident'])) {
                    $parts = $sd['parts'] ?? [];
                    $data['items'] = collect($parts)->map(function ($part, $idx) use ($decMap, $sd, $resolveLampiran) {
                        $action  = $decMap->get($idx)['action'] ?? null;
                        $cat     = isset($part['category_id'])
                            ? \App\Models\ServiceCategory::find($part['category_id'])
                            : null;
                        // Lampiran yang diupload saat buat PR
                        $lampiranRaw = $sd['temp_files']['parts'][$idx]['bukti'] ?? [];
                        // Bukti bayar admin (diupload saat approve)
                        $buktiFinal  = null;
                        if (!empty($part['bukti_bayar_admin'])) {
                            $bv   = $part['bukti_bayar_admin'];
                            $bArr = is_array($bv) ? $bv : [$bv];
                            $b0   = $bArr[0];
                            $bPath = is_array($b0) ? ($b0['path'] ?? '') : $b0;
                            $bName = is_array($b0) ? ($b0['original_name'] ?? basename($bPath)) : basename($b0);
                            if ($bPath) $buktiFinal = ['url' => asset('storage/'.$bPath), 'name' => $bName];
                            if (!$buktiFinal && !is_array($b0) && $b0) $buktiFinal = ['url' => asset($b0), 'name' => basename($b0)];
                        }
                        // Cek juga di item_decisions[idx].bukti
                        if (!$buktiFinal && isset($decMap[$idx]['bukti']['path'])) {
                            $dp = $decMap[$idx]['bukti']['path'];
                            $dn = $decMap[$idx]['bukti']['original_name'] ?? basename($dp);
                            $buktiFinal = ['url' => asset($dp), 'name' => $dn];
                        }
                        return [
                            'nama_barang'        => $part['nama_part'] ?? '-',
                            'part_number'        => !empty($part['part_number']) && $part['part_number'] !== '-' ? $part['part_number'] : null,
                            'kategori'           => $cat ? $cat->nama : ($part['category_nama'] ?? '-'),
                            'kondisi'            => $part['kondisi'] ?? null,
                            'qty'                => 1,
                            'satuan'             => 'unit',
                            'subtotal'           => $part['biaya'] ?? 0,
                            'subtotal_formatted' => number_format($part['biaya'] ?? 0, 0, ',', '.'),
                            'nama_bank'          => $part['nama_bank'] ?? null,
                            'no_rekening'        => $part['no_rekening'] ?? null,
                            'nama_rekening'      => $part['nama_rekening'] ?? null,
                            'supplier'           => isset($part['supplier_id'])
                                ? optional(\App\Models\Supplier::find($part['supplier_id']))->nama_supplier
                                : null,
                            'limit_snapshot'     => $part['limit_snapshot'] ?? null,
                            'keterangan_limit'   => $part['keterangan_limit'] ?? $part['keterangan'] ?? null,
                            'lampiran'           => $resolveLampiran($lampiranRaw),
                            'bukti'              => $buktiFinal,
                            'status_item'        => $action,
                        ];
                    })->values()->all();

                } elseif ($srcType === 'service_asuransi') {
                    $kejadians = $sd['kejadians'] ?? [];
                    $decMap2   = collect($sd['item_decisions'] ?? [])->keyBy(fn($d) => (int)($d['idx'] ?? -1));
                    $data['items'] = collect($kejadians)->map(function ($kej, $idx) use ($decMap2, $sd, $resolveLampiran) {
                        $action  = $decMap2->get((int)$idx)['action'] ?? null;
                        $lampiranRaw = $kej['lampiran'] ?? [];
                        // Bukti dari item_decisions
                        $buktiFinal = null;
                        if (isset($decMap2[(int)$idx]['bukti']['path'])) {
                            $dp = $decMap2[(int)$idx]['bukti']['path'];
                            $dn = $decMap2[(int)$idx]['bukti']['original_name'] ?? basename($dp);
                            $buktiFinal = ['url' => asset($dp), 'name' => $dn];
                        }
                        return [
                            'nama_barang'        => $kej['nama_kejadian'] ?? '-',
                            'kategori'           => 'Kejadian',
                            'qty'                => 1,
                            'satuan'             => 'kejadian',
                            'subtotal'           => $kej['biaya'] ?? 0,
                            'subtotal_formatted' => number_format($kej['biaya'] ?? 0, 0, ',', '.'),
                            'lampiran'           => $resolveLampiran($lampiranRaw),
                            'bukti'              => $buktiFinal,
                            'status_item'        => $action,
                        ];
                    })->values()->all();

                } elseif (in_array($srcType, ['pajak', 'pajak_perpanjang'])) {
                    $lampiranRaw = $sd['temp_files']['attachments'] ?? [];
                    $data['items'] = [[
                        'nama_barang'        => ($sd['jenis_pajak'] ?? 'Pajak Kendaraan') . ($sd['tahun_pajak'] ? ' ' . $sd['tahun_pajak'] : ''),
                        'kategori'           => 'Pajak',
                        'qty'                => 1,
                        'satuan'             => 'tahun',
                        'subtotal'           => $sd['nominal'] ?? $pembayaran->nominal ?? 0,
                        'subtotal_formatted' => number_format($sd['nominal'] ?? $pembayaran->nominal ?? 0, 0, ',', '.'),
                        'keterangan'         => isset($sd['tanggal_jatuh_tempo']) ? 'Jatuh tempo: ' . \Carbon\Carbon::parse($sd['tanggal_jatuh_tempo'])->format('d M Y') : null,
                        'lampiran'           => $resolveLampiran($lampiranRaw),
                        'bukti'              => null,
                        'nama_bank'          => $pembayaran->nama_bank,
                        'no_rekening'        => $pembayaran->no_rekening,
                        'nama_rekening'      => $pembayaran->nama_rekening,
                    ]];

                } elseif (in_array($srcType, ['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'])) {
                    $asr = isset($sd['asuransi_id']) ? \App\Models\Asuransi::find($sd['asuransi_id']) : null;
                    $lampiranRaw = $sd['temp_files']['attachments'] ?? [];
                    $data['items'] = [[
                        'nama_barang'        => $asr ? $asr->nama_asuransi : ($sd['nama_asuransi'] ?? 'Asuransi Kendaraan'),
                        'kategori'           => 'Asuransi',
                        'qty'                => 1,
                        'satuan'             => 'polis',
                        'subtotal'           => $sd['premi'] ?? $sd['biaya'] ?? $pembayaran->nominal ?? 0,
                        'subtotal_formatted' => number_format($sd['premi'] ?? $sd['biaya'] ?? $pembayaran->nominal ?? 0, 0, ',', '.'),
                        'keterangan'         => implode(' | ', array_filter([
                            isset($sd['no_polis']) ? 'No Polis: ' . $sd['no_polis'] : null,
                            isset($sd['tanggal_habis']) ? 'Berlaku s/d: ' . \Carbon\Carbon::parse($sd['tanggal_habis'])->format('d M Y') : null,
                        ])) ?: null,
                        'lampiran'           => $resolveLampiran($lampiranRaw),
                        'bukti'              => null,
                        'nama_bank'          => $pembayaran->nama_bank,
                        'no_rekening'        => $pembayaran->no_rekening,
                        'nama_rekening'      => $pembayaran->nama_rekening,
                    ]];

                } elseif (in_array($srcType, ['kir', 'kir_perpanjang'])) {
                    $lampiranRaw = $sd['temp_files']['attachments'] ?? [];
                    $data['items'] = [[
                        'nama_barang'        => 'KIR Kendaraan' . (!empty($sd['no_kir']) ? ' (' . $sd['no_kir'] . ')' : ''),
                        'kategori'           => 'KIR',
                        'qty'                => 1,
                        'satuan'             => 'kali',
                        'subtotal'           => $sd['biaya'] ?? $pembayaran->nominal ?? 0,
                        'subtotal_formatted' => number_format($sd['biaya'] ?? $pembayaran->nominal ?? 0, 0, ',', '.'),
                        'keterangan'         => isset($sd['tanggal_habis_kir']) ? 'Berlaku s/d: ' . \Carbon\Carbon::parse($sd['tanggal_habis_kir'])->format('d M Y') : null,
                        'lampiran'           => $resolveLampiran($lampiranRaw),
                        'bukti'              => null,
                        'nama_bank'          => $pembayaran->nama_bank,
                        'no_rekening'        => $pembayaran->no_rekening,
                        'nama_rekening'      => $pembayaran->nama_rekening,
                    ]];

                } elseif ($srcType === 'stnk') {
                    $lampiranRaw = $sd['temp_files']['attachments'] ?? [];
                    $data['items'] = [[
                        'nama_barang'        => 'STNK' . (!empty($sd['tahun_stnk']) ? ' ' . $sd['tahun_stnk'] : ''),
                        'kategori'           => 'STNK',
                        'qty'                => 1,
                        'satuan'             => 'tahun',
                        'subtotal'           => $sd['biaya'] ?? $pembayaran->nominal ?? 0,
                        'subtotal_formatted' => number_format($sd['biaya'] ?? $pembayaran->nominal ?? 0, 0, ',', '.'),
                        'lampiran'           => $resolveLampiran($lampiranRaw),
                        'bukti'              => null,
                        'nama_bank'          => $pembayaran->nama_bank,
                        'no_rekening'        => $pembayaran->no_rekening,
                        'nama_rekening'      => $pembayaran->nama_rekening,
                    ]];

                } else {
                    // Fallback legacy
                    $data['barang_jasa'] = $pembayaran->barang_jasa;
                    $data['nominal']     = $pembayaran->nominal;
                    $data['nominal_formatted'] = $pembayaran->nominal ? number_format($pembayaran->nominal, 0, ',', '.') : null;
                }
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
                        'kondisi'          => $part['kondisi'] ?? 'Perlu Ganti',
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
                        'aktivitas'   => 'pembayaran',
                        'keterangan'  => 'PR #' . $pembayaran->no_pr . ' disetujui oleh ' . $pembayaran->disetujui_oleh,
                        'referensi'   => $pembayaran->no_pr,
                    ]);
                }

                // ── Aktivasi service_history saat tipe_pembayaran = service ──
                // Selalu update status dari tidak_aktif → aktif (tidak pernah create baru)
                if ($pembayaran->tipe_pembayaran === 'service') {
                    $existingDraft = \App\Models\ServiceHistory::where('pembayaran_id', $pembayaran->id)->first();

                    if ($existingDraft) {
                        $existingDraft->update(['status' => 'aktif']);

                        // Update semua parts tidak_aktif → aktif
                        $existingDraft->parts()
                            ->where('status', 'tidak_aktif')
                            ->update(['status' => 'aktif']);
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

            // Aktivasi service_history yang sudah ada — hanya update status, tidak create baru
            $existingDraft = \App\Models\ServiceHistory::where('pembayaran_id', $pembayaran->id)->first();

            $serviceHistory = null;
            if ($existingDraft) {
                $existingDraft->update([
                    'status'           => 'aktif',
                    'bukti_pembayaran' => $buktiPath ?? $existingDraft->bukti_pembayaran,
                ]);

                $existingDraft->parts()
                    ->where('status', 'tidak_aktif')
                    ->update(['status' => 'aktif']);

                $serviceHistory = $existingDraft;
            }

            // Simpan lampiran ke tabel attachments jika ada
            if ($lampiranPath && $serviceHistory) {
                \App\Models\Attachment::create([
                    'relation_type' => 'service',
                    'relation_id'   => $serviceHistory->id,
                    'file_path'     => $lampiranPath,
                    'file_name'     => basename($lampiranPath),
                    'file_type'     => 'lampiran',
                    'file_size'     => $request->file('lampiran_tambahan') ? $request->file('lampiran_tambahan')->getSize() : null,
                ]);
            }

            // (kilometer kendaraan diupdate saat tombol Terpasang diklik)

            // ── Recalculate keterangan_limit untuk parts yang baru diaktifkan ──
            // Setelah approve, parts berubah status tidak_aktif → aktif.
            // keterangan_limit diupdate tapi aktifCount hanya hitung Terpasang (sudah dipasang fisik).
            // aktif & tidak_aktif = sudah disetujui keuangan tapi belum dipasang → TIDAK dihitung.
            if ($serviceHistory) {
                $shController = app(\App\Http\Controllers\Admin\ServiceHistoryController::class);
                $activatedParts = $serviceHistory->parts()->where('status', 'aktif')->get();
                foreach ($activatedParts as $activatedPart) {
                    if (!$activatedPart->category_id) continue;
                    $limitRule = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $activatedPart->kendaraan_id)
                        ->where('category_id', $activatedPart->category_id)
                        ->first();
                    if (!$limitRule || !$limitRule->jumlah) continue;

                    $aktifCountNow = \App\Models\ServicePart::where('kendaraan_id', $activatedPart->kendaraan_id)
                        ->where('category_id', $activatedPart->category_id)
                        ->whereIn('status', ['Terpasang'])
                        ->where(fn($q) => $q->whereNull('persetujuan')->orWhere('persetujuan', '!=', 'Ditolak Pembayaran'))
                        ->count();

                    $partArray = [
                        'biaya'            => 0,
                        'tgl_pasang'       => $activatedPart->tgl_pasang,
                        'interval_nilai'   => $activatedPart->interval_nilai,
                        'interval_satuan'  => $activatedPart->interval_satuan,
                        'kilometer_pasang' => $activatedPart->kilometer_pasang,
                    ];
                    $tanggalServis = optional($serviceHistory)->tanggal_service ?? now()->toDateString();
                    $kmInput       = (int) ($serviceHistory->kilometer ?? $activatedPart->kilometer_pasang);

                    $newKet = $shController->generateKeteranganLimitPublic(
                        $partArray, $kmInput, $limitRule, $tanggalServis,
                        null, $aktifCountNow, (int) $limitRule->jumlah
                    );
                    $activatedPart->update(['keterangan_limit' => $newKet]);
                }
            }

            // ── Catat cashflow per item ke tabel keuangans ───────────────────
            if ($serviceHistory) {
                $sdParts     = $pembayaran->source_data['parts'] ?? [];
                $namaKend    = optional($pembayaran->kendaraan)->nopol ?? '-';
                $tanggalAppr = now()->toDateString();
                foreach ($sdParts as $idx => $part) {
                    $biaya = (int) ($part['biaya'] ?? 0);
                    if ($biaya <= 0) continue;
                    $namaPart  = $part['nama_part'] ?? ('Part #' . ($idx + 1));
                    $reference = 'PR-SVC-' . $pembayaran->no_pr . '-' . ($idx + 1);
                    if (Keuangan::where('reference', $reference)->exists()) continue;

                    $lastSaldo = (float) DB::table('keuangans')->orderBy('id', 'desc')->value('saldo') ?? 0;

                    Keuangan::create([
                        'tanggal'     => $tanggalAppr,
                        'reference'   => $reference,
                        'user_id'     => auth()->id(),
                        'kategori'    => 'Service Kendaraan',
                        'metode'      => 'Cash',
                        'keterangan'  => 'Service Part: ' . $namaPart . ' — ' . $namaKend . ' (PR #' . $pembayaran->no_pr . ')',
                        'pemasukan'   => 0,
                        'pengeluaran' => $biaya,
                        'saldo'       => $lastSaldo - $biaya,
                        'sumber'      => 'auto',
                    ]);
                }
            }

            // ── Update keterangan_limit & snapshot di source_data ─────────────
            $this->updateSourceDataKeteranganLimit($pembayaran);

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

            // Update data kendaraan berdasarkan service history
            $kendaraan = \App\Models\Kendaraan::find($pembayaran->kendaraan_id);
            if ($kendaraan) {
                $kilometer = null;
                $tglService = null;

                // Ambil dari service_history yang baru saja diselesaikan, atau cari yang terkait
                if ($sh) {
                    $kilometer  = $sh->kilometer;
                    $tglService = $sh->tanggal_service;
                } else {
                    // Fallback: ambil dari source_data pembayaran
                    $sourceData = $pembayaran->source_data ?? [];
                    $kilometer  = $sourceData['kilometer'] ?? null;
                    $tglService = $pembayaran->tanggal_service ?? null;
                }

                $updateKendaraan = [];

                // Update kilometer hanya jika nilai baru lebih besar dari yang tersimpan
                if ($kilometer && (int)$kilometer > (int)($kendaraan->kilometer_sekarang ?? 0)) {
                    $updateKendaraan['kilometer_sekarang']  = (int)$kilometer;
                    $updateKendaraan['km_terakhir_service'] = (int)$kilometer;
                }

                // Update tanggal terakhir service
                if ($tglService) {
                    $tglCarbon = \Carbon\Carbon::parse($tglService);
                    $tglExisting = $kendaraan->tanggal_terakhir_service
                        ? \Carbon\Carbon::parse($kendaraan->tanggal_terakhir_service)
                        : null;

                    if (!$tglExisting || $tglCarbon->gte($tglExisting)) {
                        $updateKendaraan['tanggal_terakhir_service'] = $tglCarbon->toDateString();
                    }
                }

                if (!empty($updateKendaraan)) {
                    $kendaraan->update($updateKendaraan);
                }
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
            ])
            ->sortByDesc(fn($k) => \App\Models\ServiceHistory::where('kendaraan_id', $k['id'])
                ->max('tanggal_service'))
            ->values();

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
        try {
        $pembayaran = Pembayaran::with(['kendaraan', 'approvals.user', 'serviceParts', 'items'])
            ->findOrFail($id);
        
        // Nama pemohon dari tabel users berdasarkan email
        $pemohonNama = \App\Models\User::where('email', $pembayaran->pemohon)->value('name');

        // Decode source_data dan load related data
        $sourceData = $pembayaran->source_data ?? [];

        // Kirimkan source_data tanpa temp_files yang bisa sangat besar,
        // temp_files dikirim terpisah
        $sourceDataForClient = $sourceData;

        // Normalisasi biaya/biaya_sewa ke integer agar tidak ada string concatenation di JS
        if (!empty($sourceDataForClient['parts']) && is_array($sourceDataForClient['parts'])) {
            foreach ($sourceDataForClient['parts'] as &$p) {
                if (isset($p['biaya'])) $p['biaya'] = (int) $p['biaya'];
            }
            unset($p);
        }
        if (!empty($sourceDataForClient['kejadians']) && is_array($sourceDataForClient['kejadians'])) {
            foreach ($sourceDataForClient['kejadians'] as &$k) {
                if (isset($k['biaya'])) $k['biaya'] = (int) $k['biaya'];
            }
            unset($k);
        }
        if (!empty($sourceDataForClient['gps_items']) && is_array($sourceDataForClient['gps_items'])) {
            foreach ($sourceDataForClient['gps_items'] as &$g) {
                if (isset($g['biaya_sewa'])) $g['biaya_sewa'] = (int) $g['biaya_sewa'];
            }
            unset($g);
        }

        $relatedData = [];
        
        // Load related data berdasarkan source_type
        if ($pembayaran->source_type) {
            $relatedData = $this->loadRelatedData($pembayaran->source_type, $sourceData);
        }

        // Hitung nominal yang relevan untuk ditampilkan di modal:
        // Jika ada item_decisions tersimpan, hitung hanya item yang belum diputuskan
        // (pending) atau yang approved — exclude yang sudah rejected
        $existingDecisions = collect($sourceData['item_decisions'] ?? [])->keyBy(fn($d) => (int)($d['idx'] ?? -1));
        $nominalDisplay = (int) ($pembayaran->nominal ?? 0);

        if ($existingDecisions->isNotEmpty()) {
            $srcType = $pembayaran->source_type;
            if (in_array($srcType, ['gps', 'gps_perpanjang'])) {
                $gpsItems = $sourceData['gps_items'] ?? [];
                $nominalDisplay = collect($gpsItems)->reduce(function ($sum, $item, $idx) use ($existingDecisions) {
                    $dec = $existingDecisions->get((int)$idx);
                    // Tampilkan jika belum ada keputusan atau approved
                    if (!$dec || ($dec['action'] ?? '') !== 'rejected') {
                        $sum += (int) ($item['biaya_sewa'] ?? 0);
                    }
                    return $sum;
                }, 0);
            } elseif (in_array($srcType, ['service_part', 'service_incident'])) {
                $parts = $sourceData['parts'] ?? [];
                $nominalDisplay = collect($parts)->reduce(function ($sum, $part, $idx) use ($existingDecisions) {
                    $dec = $existingDecisions->get((int)$idx);
                    if (!$dec || ($dec['action'] ?? '') !== 'rejected') {
                        $sum += (int) ($part['biaya'] ?? 0);
                    }
                    return $sum;
                }, 0);
            } elseif ($srcType === 'service_asuransi') {
                $kejadians = $sourceData['kejadians'] ?? [];
                $nominalDisplay = collect($kejadians)->reduce(function ($sum, $kej, $idx) use ($existingDecisions) {
                    $dec = $existingDecisions->get((int)$idx);
                    if (!$dec || ($dec['action'] ?? '') !== 'rejected') {
                        $sum += (int) ($kej['biaya'] ?? 0);
                    }
                    return $sum;
                }, 0);
            }
        }
        
        // Bangun data pembayaran secara manual agar tidak ada masalah serialisasi
        // dari accessor atau relasi yang tidak diharapkan
        $pembayaranData = [
            'id'                  => $pembayaran->id,
            'no_pr'               => $pembayaran->no_pr,
            'tanggal'             => $pembayaran->tanggal,
            'departemen'          => $pembayaran->departemen,
            'pemohon'             => $pembayaran->pemohon,
            'pemohon_nama'        => $pemohonNama,
            'alasan_permintaan'   => $pembayaran->alasan_permintaan,
            'keterangan'          => $pembayaran->keterangan,
            'status'              => $pembayaran->status,
            'nominal'             => (int) ($pembayaran->nominal ?? 0),
            'nominal_display'     => (int) $nominalDisplay,
            'source_type'         => $pembayaran->source_type,
            'source_type_name'    => $pembayaran->source_type_name,
            'can_edit'            => $pembayaran->can_edit,
            'target_id'           => $pembayaran->target_id,
            'disetujui_oleh'      => $pembayaran->disetujui_oleh,
            'tanggal_persetujuan' => $pembayaran->tanggal_persetujuan,
            'catatan'             => $pembayaran->catatan,
            'terakhir_diajukan'   => $pembayaran->terakhir_diajukan,
            'tipe_pembayaran'     => $pembayaran->tipe_pembayaran,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'pembayaran' => $pembayaranData,
                'source_data' => $sourceDataForClient,
                'related_data' => $relatedData,
                'temp_files' => $sourceData['temp_files'] ?? [],
            ],
        ]);

        } catch (\Throwable $e) {
            \Log::error('showApprovalModal error for id=' . $id . ': ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data approval: ' . $e->getMessage(),
            ], 500);
        }
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
                    // service_asuransi menyimpan nama_asuransi (string), bukan asuransi_id
                    $data['nama_asuransi'] = $sourceData['nama_asuransi'] ?? null;
                    $data['jenis_asuransi'] = \App\Models\JenisAsuransi::find($sourceData['jenis_asuransi_id'] ?? null);
                    break;

                case 'service_incident':
                    $data['kendaraan'] = \App\Models\Kendaraan::find($sourceData['kendaraan_id']);
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
            'catatan' => 'required|string|min:3|max:500',
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

            // Untuk service_part: update keterangan_limit part lama ke 'pembayaran ditolak' jika ada replace_part_id
            if ($pembayaran->source_type === 'service_part') {
                $sourceData = $pembayaran->source_data ?? [];
                $parts      = $sourceData['parts'] ?? [];
                foreach ($parts as $partData) {
                    if (!empty($partData['replace_part_id'])) {
                        \App\Models\ServicePart::where('id', (int) $partData['replace_part_id'])
                            ->update(['keterangan_limit' => 'pembayaran ditolak']);
                    }
                }
            }

            // Untuk service_part & service_incident: update persetujuan parts dan service_history
            // menjadi 'Ditolak Pembayaran' agar status terlihat di halaman service history
            if (in_array($pembayaran->source_type, ['service_part', 'service_incident'])) {
                $sdReject    = $pembayaran->source_data ?? [];
                $kendaraanId = $sdReject['kendaraan_id'] ?? null;

                // Update service_history yang terkait pembayaran ini (via pembayaran_id)
                \App\Models\ServiceHistory::where('pembayaran_id', $pembayaran->id)
                    ->update(['persetujuan' => 'Ditolak Pembayaran']);

                // Update service_parts via service_history_id
                $sh = \App\Models\ServiceHistory::where('pembayaran_id', $pembayaran->id)->first();
                if ($sh) {
                    \App\Models\ServicePart::where('service_history_id', $sh->id)
                        ->whereIn('persetujuan', ['Pending', 'Diajukan ke Pembayaran'])
                        ->update(['persetujuan' => 'Ditolak Pembayaran']);
                }

                // Selalu jalankan fallback via kendaraan_id — menangani kasus
                // di mana pembayaran_id di service_history sudah tertimpa pembayaran lain
                if ($kendaraanId) {
                    \App\Models\ServicePart::where('kendaraan_id', $kendaraanId)
                        ->whereIn('persetujuan', ['Pending', 'Diajukan ke Pembayaran'])
                        ->where('status', 'tidak_aktif')
                        ->update(['persetujuan' => 'Ditolak Pembayaran']);
                }

                // Update service_incidents yang terkait pembayaran ini
                if ($pembayaran->source_type === 'service_incident') {
                    $siId = $sdReject['service_incident_id'] ?? null;
                    if ($siId) {
                        \App\Models\ServiceIncident::where('id', $siId)
                            ->update(['persetujuan' => 'Ditolak Pembayaran']);
                    }
                    // Fallback via pembayaran_id yang tersimpan di service_incidents
                    \App\Models\ServiceIncident::where('pembayaran_id', $pembayaran->id)
                        ->whereIn('persetujuan', ['Pending', 'Diajukan ke Pembayaran', 'Disetujui'])
                        ->update(['persetujuan' => 'Ditolak Pembayaran']);
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
        } elseif (in_array($sourceType, ['service_part', 'service_incident'])) {
            return $this->approveItemsServicePart($request, $pembayaran, $items);
        } elseif ($sourceType === 'service_asuransi') {
            return $this->approveItemsServiceAsuransi($request, $pembayaran, $items);
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
                $newStatus = 'Disetujui'; // partial approval → tetap Disetujui, item_decisions tracks rejected
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

            // Tentukan direktori bukti berdasarkan source_type
            $buktiDirName = $pembayaran->source_type === 'service_incident'
                ? 'service-incident-parts'
                : 'gps/bukti_bayar';
            $buktiDir = public_path($buktiDirName);
            if (!file_exists($buktiDir)) mkdir($buktiDir, 0777, true);

            foreach ($items as $idx => $item) {
                $action  = $item['action'];
                $catatan = $item['catatan'] ?? null;

                // Upload bukti per item jika ada
                $buktiBayarPath         = null;
                $buktiBayarOriginalName = null;
                if ($request->hasFile("items.{$idx}.bukti")) {
                    $file                   = $request->file("items.{$idx}.bukti");
                    $buktiBayarOriginalName = $file->getClientOriginalName();
                    $filename               = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $buktiBayarOriginalName);
                    $file->move($buktiDir, $filename);
                    $buktiBayarPath = $buktiDirName . '/' . $filename;
                }

                // Catat ke approval history
                \App\Models\PembayaranApproval::create([
                    'pembayaran_id'    => $pembayaran->id,
                    'user_id'          => auth()->id(),
                    'action'           => $action,
                    'catatan'          => $catatan,
                    'bukti_files'      => $buktiBayarPath ? [[
                        'path'          => $buktiBayarPath,
                        'original_name' => $buktiBayarOriginalName,
                    ]] : null,
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
                // Untuk service_incident: kirim array lengkap (idx + bukti_path) agar bukti_bayar per-part tersimpan
                $transferArg = ($pembayaran->source_type === 'service_incident')
                    ? $approvedItems
                    : array_column($approvedItems, 'idx');
                $targetId = $transferService->transfer($pembayaran, [], $transferArg);
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
                $newStatus = 'Disetujui'; // partial approval → tetap Disetujui
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

            // Simpan bukti_bayar_admin per part ke source_data
            $buktiToSave = array_filter(array_column($approvedItems, 'bukti_path', 'idx'));
            if (!empty($buktiToSave)) {
                $sourceData = $pembayaran->fresh()->source_data ?? [];
                foreach ($buktiToSave as $idx => $buktiPath) {
                    if (isset($sourceData['parts'][$idx])) {
                        $sourceData['parts'][$idx]['bukti_bayar_admin'] = $buktiPath;
                    }
                }
                $pembayaran->update(['source_data' => $sourceData]);
            }

            // ── Task 3: Recalculate keterangan_limit untuk approved parts ──────
            // Parts yang diapprove statusnya masih tidak_aktif (aktif setelah PengeluaranTransferService).
            // aktifCount hanya hitung Terpasang — aktif & tidak_aktif belum dipasang fisik.
            if (!empty($approvedItems)) {
                $sh = \App\Models\ServiceHistory::where('pembayaran_id', $pembayaran->id)->first();
                if ($sh) {
                    $shController    = app(\App\Http\Controllers\Admin\ServiceHistoryController::class);
                    $approvedIdxList = array_column($approvedItems, 'idx');
                    $activatedParts  = $sh->parts()
                        ->whereIn('status', ['aktif', 'tidak_aktif'])
                        ->get();

                    foreach ($activatedParts as $aPart) {
                        if (!$aPart->category_id) continue;
                        $limitRule = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $aPart->kendaraan_id)
                            ->where('category_id', $aPart->category_id)
                            ->first();
                        if (!$limitRule || !$limitRule->jumlah) continue;

                        $aktifCountNow = \App\Models\ServicePart::where('kendaraan_id', $aPart->kendaraan_id)
                            ->where('category_id', $aPart->category_id)
                            ->whereIn('status', ['Terpasang'])
                            ->where(fn($q) => $q->whereNull('persetujuan')->orWhere('persetujuan', '!=', 'Ditolak Pembayaran'))
                            ->count();

                        $partArray = [
                            'biaya'            => 0,
                            'tgl_pasang'       => $aPart->tgl_pasang,
                            'interval_nilai'   => $aPart->interval_nilai,
                            'interval_satuan'  => $aPart->interval_satuan,
                            'kilometer_pasang' => $aPart->kilometer_pasang,
                        ];
                        $kmInput = (int) ($sh->kilometer ?? $aPart->kilometer_pasang);

                        $newKet = $shController->generateKeteranganLimitPublic(
                            $partArray, $kmInput, $limitRule,
                            $sh->tanggal_service ?? now()->toDateString(),
                            null, $aktifCountNow, (int) $limitRule->jumlah
                        );
                        $aPart->update(['keterangan_limit' => $newKet]);
                    }
                }
            }

            // ── Task 4: Catat cashflow per item ke tabel keuangans ───────────
            if (!empty($approvedItems)) {
                $sdParts     = $pembayaran->source_data['parts'] ?? [];
                $tanggalAppr = now()->toDateString();
                foreach ($approvedItems as $approvedItem) {
                    $idx   = (int) $approvedItem['idx'];
                    $part  = $sdParts[$idx] ?? [];
                    $biaya = (int) ($part['biaya'] ?? 0);
                    if ($biaya <= 0) continue;

                    $namaKendaraan = isset($pembayaran->source_data['kendaraan_id'])
                        ? (optional(\App\Models\Kendaraan::find($pembayaran->source_data['kendaraan_id']))->nopol ?? '-')
                        : '-';
                    $namaPart = $part['nama_part'] ?? ('Part #' . ($idx + 1));

                    $lastSaldo = (float) DB::table('keuangans')->orderBy('id', 'desc')->value('saldo') ?? 0;

                    Keuangan::create([
                        'tanggal'     => $tanggalAppr,
                        'reference'   => 'PR-SVC-' . $pembayaran->no_pr . '-' . ($idx + 1),
                        'user_id'     => auth()->id(),
                        'kategori'    => 'Service Kendaraan',
                        'metode'      => 'Cash',
                        'keterangan'  => 'Service Part: ' . $namaPart . ' — ' . $namaKendaraan . ' (PR #' . $pembayaran->no_pr . ')',
                        'pemasukan'   => 0,
                        'pengeluaran' => $biaya,
                        'saldo'       => $lastSaldo - $biaya,
                        'sumber'      => 'auto',
                    ]);
                }
            }

            // ── Update keterangan_limit & snapshot di source_data ─────────────
            $this->updateSourceDataKeteranganLimit($pembayaran);

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
     * Handle service_asuransi-specific per-item (per-kejadian) approval
     */
    private function approveItemsServiceAsuransi(Request $request, Pembayaran $pembayaran, array $items)
    {
        DB::beginTransaction();
        try {
            $approvedItems   = [];
            $rejectedItems   = [];
            $buktiDir        = public_path('service-asuransi/bukti_bayar');
            if (!file_exists($buktiDir)) mkdir($buktiDir, 0777, true);

            $sourceKejadians = $pembayaran->source_data['kejadians'] ?? [];

            foreach ($items as $idx => $item) {
                $action  = $item['action'];
                $catatan = $item['catatan'] ?? null;

                // Upload bukti per kejadian
                $buktiBayarPath         = null;
                $buktiBayarOriginalName = null;
                if ($request->hasFile("items.{$idx}.bukti")) {
                    $file                   = $request->file("items.{$idx}.bukti");
                    $buktiBayarOriginalName = $file->getClientOriginalName();
                    $filename               = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $buktiBayarOriginalName);
                    $file->move($buktiDir, $filename);
                    $buktiBayarPath = 'service-asuransi/bukti_bayar/' . $filename;
                }

                // Catat ke approval history per item
                \App\Models\PembayaranApproval::create([
                    'pembayaran_id'    => $pembayaran->id,
                    'user_id'          => auth()->id(),
                    'action'           => $action,
                    'catatan'          => $catatan,
                    'bukti_files'      => $buktiBayarPath ? [[
                        'path'          => $buktiBayarPath,
                        'original_name' => $buktiBayarOriginalName,
                        'extension'     => pathinfo($buktiBayarPath, PATHINFO_EXTENSION),
                    ]] : null,
                    'attachment_files' => null,
                ]);

                if ($action === 'approved') {
                    $approvedItems[] = [
                        'idx'        => (int) $idx,
                        'bukti_path' => $buktiBayarPath,
                        'bukti_name' => $buktiBayarOriginalName,
                    ];
                } else {
                    $rejectedItems[] = ['idx' => (int) $idx, 'catatan' => $catatan];
                }
            }

            // Tentukan status PR
            $totalItems    = count($items);
            $approvedCount = count($approvedItems);
            $rejectedCount = count($rejectedItems);

            // Hitung nominal dan bangun item_decisions
            // PENTING: gabungkan dengan item_decisions lama yang sudah approved
            // agar siklus approval berikutnya tidak kehilangan keputusan sebelumnya
            $existingDecisions = collect($pembayaran->source_data['item_decisions'] ?? [])
                ->where('action', 'approved')
                ->keyBy(fn($d) => (int)($d['idx'] ?? -1));

            $nominalApproved = 0;
            $nominalRejected = 0;
            $itemDecisions   = [];

            // Masukkan keputusan lama yang approved terlebih dahulu
            foreach ($existingDecisions as $existingIdx => $existingDec) {
                $kej   = $sourceKejadians[(int) $existingIdx] ?? [];
                $biaya = (int) ($kej['biaya'] ?? 0);
                $nominalApproved += $biaya;
                $itemDecisions[] = $existingDec; // pakai data lama as-is (termasuk bukti)
            }

            // Tambahkan keputusan baru dari form saat ini
            foreach ($items as $idx => $decision) {
                $kej   = $sourceKejadians[(int) $idx] ?? [];
                $biaya = (int) ($kej['biaya'] ?? 0);
                if ($decision['action'] === 'approved') {
                    $nominalApproved += $biaya;
                } else {
                    $nominalRejected += $biaya;
                }
                // Cari bukti path untuk item ini
                $buktiBayarForItem = null;
                foreach ($approvedItems as $ai) {
                    if ($ai['idx'] === (int) $idx) {
                        $buktiBayarForItem = $ai['bukti_path'] ? [
                            'path'          => $ai['bukti_path'],
                            'original_name' => $ai['bukti_name'],
                        ] : null;
                        break;
                    }
                }
                $itemDecisions[] = [
                    'idx'           => (int) $idx,
                    'nama_kejadian' => $kej['nama_kejadian'] ?? '-',
                    'biaya'         => $biaya,
                    'action'        => $decision['action'],
                    'catatan'       => $decision['catatan'] ?? null,
                    'bukti'         => $buktiBayarForItem,
                ];
            }

            // Tentukan status PR berdasarkan semua item (lama + baru)
            $allKejadians   = $sourceKejadians;
            $allDecisions   = collect($itemDecisions)->keyBy(fn($d) => (int)($d['idx'] ?? -1));
            $totalAllItems  = count($allKejadians);
            $totalDecided   = $allDecisions->count();
            $allApproved    = $allDecisions->where('action', 'approved')->count();
            $allRejected    = $allDecisions->where('action', 'rejected')->count();

            // Semua item sudah diputuskan
            if ($totalDecided >= $totalAllItems) {
                if ($allRejected === $totalAllItems) {
                    $newStatus = 'Ditolak';
                } else {
                    $newStatus = 'Disetujui'; // semua atau sebagian approved
                }
            } else {
                // Masih ada item pending → tetap Diajukan agar muncul di tab Diajukan
                $newStatus = 'Diajukan';
            }

            // Simpan bukti per kejadian ke source_data['kejadians'] agar transfer bisa ambil
            $sourceData = $pembayaran->source_data ?? [];
            foreach ($approvedItems as $ai) {
                if (isset($sourceData['kejadians'][$ai['idx']]) && $ai['bukti_path']) {
                    $sourceData['kejadians'][$ai['idx']]['bukti_bayar_admin'] = [
                        'path'          => $ai['bukti_path'],
                        'original_name' => $ai['bukti_name'],
                        'extension'     => pathinfo($ai['bukti_path'], PATHINFO_EXTENSION),
                    ];
                }
            }

            // Jalankan transfer hanya untuk yang disetujui
            $transferService = app(\App\Services\PengeluaranTransferService::class);
            $targetId        = null;
            if (!empty($approvedItems)) {
                $approvedIndices = array_column($approvedItems, 'idx');
                // Update source_data dulu agar transfer bisa baca bukti per kejadian
                $pembayaran->update(['source_data' => $sourceData]);
                $targetId = $transferService->transfer(
                    $pembayaran->fresh(),
                    ['bukti' => [], 'attachments' => []],
                    $approvedIndices
                );
            }

            $pembayaran->update([
                'status'              => $newStatus,
                'target_id'           => $targetId,
                'can_edit'            => $allRejected > 0,
                'nominal'             => $nominalApproved,
                'disetujui_oleh'      => auth()->user()->nama ?? auth()->user()->email,
                'tanggal_persetujuan' => now(),
            ]);

            // Simpan keputusan per kejadian ke source_data
            $sourceData = $pembayaran->fresh()->source_data ?? [];
            $sourceData['item_decisions']   = $itemDecisions;
            $sourceData['nominal_approved'] = $nominalApproved;
            $sourceData['nominal_rejected'] = $nominalRejected;
            $pembayaran->update(['source_data' => $sourceData]);

            // Update ServiceAsuransi.persetujuan
            $saId = ($pembayaran->fresh()->source_data)['service_asuransi_id'] ?? null;
            if ($saId) {
                $saPersetujuan = match($newStatus) {
                    'Disetujui' => 'Disetujui',
                    'Ditolak'   => 'Ditolak',
                    default     => null,
                };
                if ($saPersetujuan) {
                    \App\Models\ServiceAsuransi::where('id', $saId)->update([
                        'persetujuan'   => $saPersetujuan,
                        'pembayaran_id' => $pembayaran->id,
                        'status'        => $newStatus === 'Ditolak' ? 'tidak_aktif' : 'bermasalah',
                    ]);
                }
            }

            // Cashflow per kejadian dicatat di dalam transferServiceAsuransi() —
            // tidak perlu catat ulang di sini agar tidak double.

            DB::commit();

            $msg = "Keputusan disimpan: {$approvedCount} kejadian disetujui, {$rejectedCount} ditolak. Status PR: {$newStatus}.";
            return redirect()->route('pembayaran.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("approveItems ServiceAsuransi failed for Pembayaran #{$pembayaran->id}: " . $e->getMessage());
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
    /**
     * Recalculate dan update keterangan_limit + limit_snapshot.sisa_pasang
     * di source_data Pembayaran dan PO terkait setelah approve.
     *
     * Dipanggil setelah parts berubah status tidak_aktif → aktif,
     * sehingga aktifCount sudah bertambah dan sisa_pasang berkurang.
     */
    public function updateSourceDataKeteranganLimit(\App\Models\Pembayaran $pembayaran): void
    {
        if (!in_array($pembayaran->source_type, ['service_part', 'service_incident'])) {
            return;
        }

        $sourceData  = $pembayaran->source_data ?? [];
        $parts       = $sourceData['parts'] ?? [];
        $kendaraanId = $sourceData['kendaraan_id'] ?? null;
        if (!$kendaraanId || empty($parts)) return;

        $shController = app(\App\Http\Controllers\Admin\ServiceHistoryController::class);
        $sh = \App\Models\ServiceHistory::where('pembayaran_id', $pembayaran->id)->first();
        $tanggalServis = $sh?->tanggal_service ?? now()->toDateString();
        $kmInput       = (int) ($sh?->kilometer ?? 0);

        // Track berapa item per kategori yang sudah diproses dalam batch ini.
        // Tujuannya: item ke-1 kategori A mendapat aktifCount total,
        // item ke-2 kategori A mendapat aktifCount-1, dst.
        // Ini memastikan keterangan_limit tiap item mencerminkan posisinya sendiri.
        $batchCountPerCategory = [];

        $changed = false;
        foreach ($parts as $idx => $part) {
            $categoryId = $part['category_id'] ?? null;
            if (!$categoryId) continue;

            $limitRule = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $kendaraanId)
                ->where('category_id', $categoryId)
                ->first();
            if (!$limitRule || !$limitRule->jumlah) continue;

            // Hitung aktifCount total dari DB — hanya yang sudah benar-benar terpasang fisik.
            // 'aktif' & 'tidak_aktif' = sudah disetujui keuangan tapi belum dipasang → TIDAK dihitung.
            // sisa_pasang baru berkurang saat user klik tombol Terpasang (status → Terpasang).
            // 'Ditolak Pembayaran' dikecualikan: part ditolak tidak mengurangi sisa pasang.
            $q = \App\Models\ServicePart::where('kendaraan_id', $kendaraanId)
                ->where('category_id', $categoryId)
                ->whereIn('status', ['Terpasang'])
                ->where(fn($q) => $q->whereNull('persetujuan')->orWhere('persetujuan', '!=', 'Ditolak Pembayaran'));
            if ($limitRule->reset_at) {
                $q->where('created_at', '>=', $limitRule->reset_at);
            }
            $aktifCountTotal = $q->count();

            // aktifCountTotal = semua part kategori ini yang sudah Terpasang fisik.
            // Tidak perlu batch offset — semua part baru masih tidak_aktif/aktif,
            // belum masuk hitungan. sisa_pasang = limitJumlah - aktifCountTotal.
            $aktifCountNow = $aktifCountTotal;
            $sisaPasang    = max(0, (int) $limitRule->jumlah - $aktifCountNow);

            // Recalculate keterangan_limit string
            // Biaya di-set 0 karena part sudah tersimpan di DB — getKumulatifBiayaKategori
            // sudah include biaya ini, jadi jangan tambahkan lagi (double-count)
            $partArray = [
                'biaya'            => 0,
                'tgl_pasang'       => $part['tgl_pasang'] ?? $tanggalServis,
                'interval_nilai'   => $part['interval_nilai'] ?? 0,
                'interval_satuan'  => $part['interval_satuan'] ?? 'bulan',
                'kilometer_pasang' => $part['kilometer_pasang'] ?? $kmInput,
            ];
            $newKet = $shController->generateKeteranganLimitPublic(
                $partArray, $kmInput, $limitRule, $tanggalServis,
                null, $aktifCountNow, (int) $limitRule->jumlah
            );

            // Update di source_data parts
            $parts[$idx]['keterangan_limit'] = $newKet;

            // Update limit_snapshot.sisa_pasang dan jumlah flags
            if (isset($parts[$idx]['limit_snapshot'])) {
                $parts[$idx]['limit_snapshot']['sisa_pasang']   = $sisaPasang;
                $parts[$idx]['limit_snapshot']['aktif_count']   = $aktifCountNow;
                $parts[$idx]['limit_snapshot']['jumlah_sama']   = $aktifCountNow === (int) $limitRule->jumlah;
                $parts[$idx]['limit_snapshot']['jumlah_lewat']  = $aktifCountNow > (int) $limitRule->jumlah;
            }

            $changed = true;
        }

        if (!$changed) return;

        $sourceData['parts'] = $parts;

        // Update di tabel pembayarans
        $pembayaran->updateQuietly(['source_data' => $sourceData]);

        // Update juga di tabel purchase_orders yang terkait (via pembayaran_id)
        \App\Models\PurchaseOrder::where('pembayaran_id', $pembayaran->id)
            ->each(function ($po) use ($sourceData) {
                $poSourceData = $po->source_data ?? [];
                $poSourceData['parts'] = $sourceData['parts'];
                $po->updateQuietly(['source_data' => $poSourceData]);
            });
    }

    /**
     * Sync persetujuan ke tabel sumber (pajak / asuransi / kir)
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
        
        // Validation: Only rejected (or partial-approved with rejected items) can be edited
        $hasRejectedItems = !empty(collect($pembayaran->source_data['item_decisions'] ?? [])->where('action', 'rejected')->all());
        if (!in_array($pembayaran->status, ['Ditolak']) && !($pembayaran->status === 'Disetujui' && $hasRejectedItems)) {
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

    /*
    |--------------------------------------------------------------------------
    | RESUBMIT REJECTED ITEMS (inline modal — service_part, service_incident, service_asuransi)
    |--------------------------------------------------------------------------
    */

    /**
     * AJAX GET — kembalikan semua item yang rejected dari satu PR.
     * Digunakan untuk prefill form di modal resubmit.
     */
    /**
     * GET: Load semua items dari pembayaran untuk modal per-item approval
     * Digunakan oleh modal Approve di halaman pembayaran untuk service_part/service_incident/service_asuransi
     */
    public function approvalItems(Pembayaran $pembayaran)
    {
        if (!in_array($pembayaran->status, ['Pending', 'Diajukan'])) {
            return response()->json(['success' => false, 'message' => 'PR tidak dalam status yang dapat diproses.'], 422);
        }

        $srcType    = $pembayaran->source_data['source_type'] ?? $pembayaran->source_type;
        $sourceData = $pembayaran->source_data ?? [];
        $items      = [];

        // ── Kumpulkan index item yang sudah approved di item_decisions ──────
        // Item-item ini TIDAK perlu muncul lagi di modal approval karena sudah
        // pernah disetujui sebelumnya (partial approval siklus sebelumnya).
        $alreadyApprovedIdx = collect($sourceData['item_decisions'] ?? [])
            ->where('action', 'approved')
            ->pluck('idx')
            ->map('intval')
            ->flip() // jadikan key untuk O(1) lookup
            ->all();

        // ── Fallback: cek juga dari bukti_bayar_admin per kejadian ──────────
        // Untuk data lama yang item_decisions-nya belum tersimpan dengan benar,
        // item yang sudah punya bukti_bayar_admin = sudah pernah diapprove.
        if ($srcType === 'service_asuransi') {
            foreach ($sourceData['kejadians'] ?? [] as $idx => $kej) {
                if (!empty($kej['bukti_bayar_admin'])) {
                    $alreadyApprovedIdx[(int)$idx] = (int)$idx;
                }
            }
        }

        if (in_array($srcType, ['service_part', 'service_incident'])) {
            $parts = $sourceData['parts'] ?? [];
            foreach ($parts as $idx => $part) {
                // Lewati item yang sudah approved sebelumnya
                if (isset($alreadyApprovedIdx[(int)$idx])) continue;
                $items[] = [
                    'idx'           => $idx,
                    'nama'          => $part['nama_part'] ?? '-',
                    'category'      => $part['category_nama'] ?? null,
                    'biaya'         => (int) ($part['biaya'] ?? 0),
                    'nama_bank'     => $part['nama_bank'] ?? null,
                    'no_rekening'   => $part['no_rekening'] ?? null,
                    'nama_rekening' => $part['nama_rekening'] ?? null,
                ];
            }
        } elseif ($srcType === 'service_asuransi') {
            $kejadians = $sourceData['kejadians'] ?? [];
            foreach ($kejadians as $idx => $kej) {
                // Lewati kejadian yang sudah approved sebelumnya
                if (isset($alreadyApprovedIdx[(int)$idx])) continue;
                $items[] = [
                    'idx'   => $idx,
                    'nama'  => $kej['nama_kejadian'] ?? '-',
                    'biaya' => (int) ($kej['biaya'] ?? 0),
                ];
            }
        } elseif (in_array($srcType, ['gps', 'gps_perpanjang'])) {
            $gpsItems = $sourceData['gps_items'] ?? [];
            foreach ($gpsItems as $idx => $gItem) {
                // Lewati item GPS yang sudah approved sebelumnya
                if (isset($alreadyApprovedIdx[(int)$idx])) continue;
                $gpsModel = isset($gItem['gps_id']) ? \App\Models\Gps::find($gItem['gps_id']) : null;
                $items[] = [
                    'idx'         => $idx,
                    'nama'        => ($gpsModel->nama_gps ?? '-') . ' (' . ($gItem['type'] ?? '-') . ')',
                    'biaya'       => (int) ($gItem['biaya_sewa'] ?? 0),
                    'nama_bank'   => $gItem['nama_bank'] ?? null,
                    'no_rekening' => $gItem['no_rekening'] ?? null,
                    'nama_pemilik'=> $gItem['nama_pemilik'] ?? null,
                ];
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Source type tidak didukung untuk per-item approval.'], 422);
        }

        // Kendaraan info
        $kendaraanId = $sourceData['kendaraan_id'] ?? null;
        $kendaraan   = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;

        return response()->json([
            'success'     => true,
            'no_pr'       => $pembayaran->no_pr,
            'source_type' => $srcType,
            'kendaraan'   => $kendaraan ? $kendaraan->nopol . ' — ' . $kendaraan->merk : '-',
            'items'       => $items,
        ]);
    }

    /**
     * GET: Load rejected items untuk modal resubmit
     */
    public function rejectedItems(Pembayaran $pembayaran)
    {
        // Guard: hanya PR yang punya item rejected yang bisa diakses
        $hasRejectedItems = !empty(collect($pembayaran->source_data['item_decisions'] ?? [])->where('action', 'rejected')->all());
        if ($pembayaran->status === 'Ditolak' || ($pembayaran->status === 'Disetujui' && $hasRejectedItems && $pembayaran->can_edit)) {
            // ok, lanjut
        } else {
            return response()->json([
                'success' => false,
                'message' => 'PR ini tidak dalam status yang dapat diajukan ulang.',
            ], 422);
        }

        if (!$pembayaran->can_edit) {
            return response()->json([
                'success' => false,
                'message' => 'PR ini tidak dapat diedit.',
            ], 403);
        }

        $supportedTypes = ['service_part', 'service_incident', 'service_asuransi'];
        if (!in_array($pembayaran->source_type, $supportedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Source type tidak didukung untuk resubmit inline.',
            ], 422);
        }

        $sourceData    = $pembayaran->source_data ?? [];
        $itemDecisions = collect($sourceData['item_decisions'] ?? [])->keyBy(fn($d) => (int)($d['idx'] ?? -1));

        // Ambil hanya item yang action = 'rejected'
        $rejectedDecisions = collect($sourceData['item_decisions'] ?? [])
            ->filter(fn($d) => ($d['action'] ?? '') === 'rejected');

        if ($rejectedDecisions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada item yang ditolak.',
            ], 422);
        }

        $srcType = $pembayaran->source_type;
        $items   = [];

        foreach ($rejectedDecisions as $decision) {
            $idx = (int) ($decision['idx'] ?? 0);

            if (in_array($srcType, ['service_part', 'service_incident'])) {
                $part = $sourceData['parts'][$idx] ?? [];
                $items[] = [
                    'idx'           => $idx,
                    'nama'          => $part['nama_part'] ?? '-',
                    'biaya'         => (int) ($part['biaya'] ?? 0),
                    'nama_bank'     => $part['nama_bank'] ?? '',
                    'no_rekening'   => $part['no_rekening'] ?? '',
                    'nama_rekening' => $part['nama_rekening'] ?? '',
                    'supplier_id'   => $part['supplier_id'] ?? null,
                    'keterangan'    => $part['keterangan'] ?? '',
                    'catatan_tolak' => $decision['catatan'] ?? '',
                ];
            } elseif ($srcType === 'service_asuransi') {
                $kej = $sourceData['kejadians'][$idx] ?? [];
                $items[] = [
                    'idx'           => $idx,
                    'nama'          => $kej['nama_kejadian'] ?? '-',
                    'biaya'         => (int) ($kej['biaya'] ?? 0),
                    'keterangan'    => $kej['keterangan'] ?? '',
                    'catatan_tolak' => $decision['catatan'] ?? '',
                ];
            }
        }

        // Catatan PR-level (dari approval history terbaru)
        $catatanPR = $pembayaran->approvals()
            ->where('action', 'rejected')
            ->latest()
            ->value('catatan') ?? $pembayaran->catatan ?? null;

        return response()->json([
            'success'      => true,
            'no_pr'        => $pembayaran->no_pr,
            'source_type'  => $srcType,
            'catatan_pr'   => $catatanPR,
            'status'       => $pembayaran->status,
            'items'        => array_values($items),
        ]);
    }

    /**
     * POST — update source_data item yang rejected, reset item_decisions,
     * dan ubah status PR ke Diajukan.
     */
    public function resubmitRejectedItems(Request $request, Pembayaran $pembayaran)
    {
        // Guard
        $hasRejectedItems = !empty(collect($pembayaran->source_data['item_decisions'] ?? [])->where('action', 'rejected')->all());
        if (!in_array($pembayaran->status, ['Ditolak']) && !($pembayaran->status === 'Disetujui' && $hasRejectedItems)) {
            return back()->with('error', 'PR ini tidak dalam status yang dapat diajukan ulang.');
        }

        if (!$pembayaran->can_edit) {
            return back()->with('error', 'PR ini tidak dapat diedit.');
        }

        $srcType = $pembayaran->source_type;

        // Validasi dasar
        $rules = [
            'items'         => 'required|array|min:1',
            'items.*.idx'   => 'required|integer|min:0',
            'items.*.biaya' => 'required|integer|min:0',
        ];

        if (in_array($srcType, ['service_part', 'service_incident'])) {
            $rules['items.*.nama_bank']     = 'nullable|string|max:100';
            $rules['items.*.no_rekening']   = 'nullable|string|max:50';
            $rules['items.*.nama_rekening'] = 'nullable|string|max:150';
            $rules['items.*.supplier_id']   = 'nullable|integer|exists:supplier,id';
            $rules['items.*.keterangan']    = 'nullable|string|max:500';
        } elseif ($srcType === 'service_asuransi') {
            $rules['items.*.keterangan'] = 'nullable|string|max:500';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $sourceData    = $pembayaran->source_data ?? [];
            $statusAwal    = $pembayaran->status; // simpan sebelum update

            // ── Kumpulkan index item yang sedang diresubmit ─────────────────
            $resubmittedIndices = collect($request->items)->pluck('idx')->map('intval')->all();

            // ── Bangun map item_decisions yang sudah ada, pakai integer key ─
            // PENTING: keyBy dengan string menyebabkan mismatch saat forget() dengan integer.
            // Gunakan array biasa dengan integer key agar konsisten.
            $existingDecisions = [];
            foreach ($sourceData['item_decisions'] ?? [] as $dec) {
                $existingDecisions[(int)($dec['idx'] ?? -1)] = $dec;
            }

            // Update tiap item yang diedit di source_data
            foreach ($request->items as $item) {
                $idx = (int) $item['idx'];

                if (in_array($srcType, ['service_part', 'service_incident'])) {
                    if (isset($sourceData['parts'][$idx])) {
                        $sourceData['parts'][$idx]['biaya']         = (int) $item['biaya'];
                        $sourceData['parts'][$idx]['nama_bank']     = $item['nama_bank'] ?? $sourceData['parts'][$idx]['nama_bank'] ?? null;
                        $sourceData['parts'][$idx]['no_rekening']   = $item['no_rekening'] ?? $sourceData['parts'][$idx]['no_rekening'] ?? null;
                        $sourceData['parts'][$idx]['nama_rekening'] = $item['nama_rekening'] ?? $sourceData['parts'][$idx]['nama_rekening'] ?? null;
                        $sourceData['parts'][$idx]['supplier_id']   = $item['supplier_id'] ?? $sourceData['parts'][$idx]['supplier_id'] ?? null;
                        $sourceData['parts'][$idx]['keterangan']    = $item['keterangan'] ?? $sourceData['parts'][$idx]['keterangan'] ?? null;
                        // Hapus flag rejection lama
                        unset($sourceData['parts'][$idx]['status_approval']);
                        unset($sourceData['parts'][$idx]['catatan_penolakan']);
                    }
                } elseif ($srcType === 'service_asuransi') {
                    if (isset($sourceData['kejadians'][$idx])) {
                        $sourceData['kejadians'][$idx]['biaya']      = (int) $item['biaya'];
                        $sourceData['kejadians'][$idx]['keterangan'] = $item['keterangan'] ?? $sourceData['kejadians'][$idx]['keterangan'] ?? null;
                    }
                }

                // Hapus entry item ini dari decisions — kembali ke "belum diputuskan"
                unset($existingDecisions[$idx]);
            }

            // Simpan hanya keputusan approved yang tersisa (integer key sudah aman)
            $sourceData['item_decisions'] = array_values(
                array_filter($existingDecisions, fn($d) => ($d['action'] ?? '') === 'approved')
            );

            $hasRejectedLeft = false; // tidak ada rejected tersisa setelah resubmit

            // ── Hitung ulang nominal ────────────────────────────────────────
            // nominal_approved = biaya item yang masih approved (tidak diresubmit)
            // nominal_resubmit = biaya item yang baru diresubmit (pakai nilai baru)
            // Gunakan integer key untuk lookup yang konsisten
            $approvedIdxMap  = array_flip(array_column($sourceData['item_decisions'], 'idx'));
            $nominalApproved = 0;
            $nominalResubmit = 0;

            if (in_array($srcType, ['service_part', 'service_incident'])) {
                foreach ($sourceData['parts'] ?? [] as $idx => $part) {
                    if (isset($approvedIdxMap[(int)$idx])) {
                        $nominalApproved += (int) ($part['biaya'] ?? 0);
                    } elseif (in_array((int)$idx, $resubmittedIndices)) {
                        $nominalResubmit += (int) ($part['biaya'] ?? 0);
                    }
                }
            } elseif ($srcType === 'service_asuransi') {
                foreach ($sourceData['kejadians'] ?? [] as $idx => $kej) {
                    if (isset($approvedIdxMap[(int)$idx])) {
                        $nominalApproved += (int) ($kej['biaya'] ?? 0);
                    } elseif (in_array((int)$idx, $resubmittedIndices)) {
                        $nominalResubmit += (int) ($kej['biaya'] ?? 0);
                    }
                }
            }

            $nominalBaru = $nominalApproved + $nominalResubmit;

            $sourceData['nominal_approved'] = $nominalApproved;
            $sourceData['nominal_rejected'] = 0;

            // ── Update PR: status → Diajukan, nominal diperbarui ───────────
            $pembayaran->update([
                'source_data'       => $sourceData,
                'status'            => 'Diajukan',
                'nominal'           => $nominalBaru,
                'can_edit'          => false,
                'catatan'           => null,
                'terakhir_diajukan' => now(),
            ]);

            // ── Sync PO terkait ─────────────────────────────────────────────
            // Update total_harga, source_data, dan status PO agar tampil nominal baru
            $po = \App\Models\PurchaseOrder::where('pembayaran_id', $pembayaran->id)->first();
            if ($po) {
                $poSourceData = $po->source_data ?? [];

                // Update bagian source_data PO sesuai source_type
                if (in_array($srcType, ['service_part', 'service_incident'])) {
                    $poSourceData['parts'] = $sourceData['parts'];
                } elseif ($srcType === 'service_asuransi') {
                    $poSourceData['kejadians'] = $sourceData['kejadians'];
                }

                // Reset item_decisions PO agar item yang diresubmit muncul kembali sebagai pending
                $poSourceData['item_decisions'] = $sourceData['item_decisions'];

                $po->update([
                    'source_data' => $poSourceData,
                    'total_harga' => $nominalBaru,
                    'can_edit'    => false,
                ]);
            }

            // ── Sync tabel service sesuai source_type ──────────────────────
            if ($srcType === 'service_asuransi') {
                // Update biaya kejadian yang diresubmit di tabel service_asuransi_kejadians
                $saId = $sourceData['service_asuransi_id'] ?? null;
                if (!$saId) {
                    // Fallback: cari via pembayaran_id
                    $sa = \App\Models\ServiceAsuransi::where('pembayaran_id', $pembayaran->id)->first();
                    $saId = $sa?->id;
                }
                if ($saId) {
                    // Update biaya total service_asuransi = approved + resubmit
                    \App\Models\ServiceAsuransi::where('id', $saId)->update([
                        'biaya'       => $nominalBaru,
                        'persetujuan' => 'Diajukan ke Pembayaran',
                    ]);

                    // Update biaya per kejadian yang diresubmit
                    foreach ($request->items as $item) {
                        $idx      = (int) $item['idx'];
                        $kej      = $sourceData['kejadians'][$idx] ?? [];
                        $namaKej  = $kej['nama_kejadian'] ?? null;
                        $biayaBaru = (int) $item['biaya'];

                        if ($namaKej) {
                            \App\Models\ServiceAsuransiKejadian::where('service_asuransi_id', $saId)
                                ->where('nama_kejadian', $namaKej)
                                ->whereIn('status', ['ditolak', 'diajukan'])
                                ->update([
                                    'biaya'             => $biayaBaru,
                                    'status'            => 'diajukan',
                                    'catatan_penolakan' => null,
                                ]);
                        }
                    }
                }

            } elseif (in_array($srcType, ['service_part', 'service_incident'])) {
                // Reset persetujuan service_history & parts agar muncul kembali di antrian
                \App\Models\ServiceHistory::where('pembayaran_id', $pembayaran->id)
                    ->update(['persetujuan' => 'Diajukan ke Pembayaran']);

                $sh = \App\Models\ServiceHistory::where('pembayaran_id', $pembayaran->id)->first();
                if ($sh) {
                    // Update biaya parts yang diresubmit
                    foreach ($request->items as $item) {
                        $idx      = (int) $item['idx'];
                        $part     = $sourceData['parts'][$idx] ?? [];
                        $namaPart = $part['nama_part'] ?? null;
                        if ($namaPart) {
                            \App\Models\ServicePart::where('service_history_id', $sh->id)
                                ->where('nama_part', $namaPart)
                                ->whereIn('persetujuan', ['Ditolak Pembayaran', 'Diajukan ke Pembayaran'])
                                ->update([
                                    'biaya'       => (int) $item['biaya'],
                                    'persetujuan' => 'Diajukan ke Pembayaran',
                                ]);
                        }
                    }

                    // Update total_biaya service_history
                    $totalBiayaBaru = $sh->parts()
                        ->whereNotIn('persetujuan', ['Ditolak Pembayaran'])
                        ->sum('biaya');
                    $sh->update(['total_biaya' => $totalBiayaBaru]);
                }

                // Khusus service_incident: reset persetujuan incident
                if ($srcType === 'service_incident') {
                    $siId = ($pembayaran->source_data ?? [])['service_incident_id'] ?? null;
                    if ($siId) {
                        \App\Models\ServiceIncident::where('id', $siId)
                            ->update(['persetujuan' => 'Diajukan ke Pembayaran']);
                    }
                    \App\Models\ServiceIncident::where('pembayaran_id', $pembayaran->id)
                        ->whereIn('persetujuan', ['Ditolak', 'Ditolak Pembayaran'])
                        ->update(['persetujuan' => 'Diajukan ke Pembayaran']);
                }
            }

            DB::commit();

            // Redirect: service_incident → halaman servis insiden
            // service_part → service history
            if ($srcType === 'service_incident') {
                return redirect()
                    ->route('service-incident.index')
                    ->with('success', 'Item berhasil diajukan ulang. Item yang ditolak pada PR ' . $pembayaran->no_pr . ' sudah kembali ke antrian approval.');
            }

            if ($srcType === 'service_part') {
                return redirect()
                    ->route('service-history.index', ['highlight_pembayaran' => $pembayaran->id])
                    ->with('success', 'Item berhasil diajukan ulang. Item yang ditolak pada PR ' . $pembayaran->no_pr . ' sudah kembali ke antrian approval.');
            }

            return redirect()
                ->route('pembayaran.index')
                ->with('success', 'Item ditolak berhasil diajukan ulang. Item yang ditolak pada PR ' . $pembayaran->no_pr . ' sudah kembali ke antrian approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("resubmitRejectedItems failed for Pembayaran #{$pembayaran->id}: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}



