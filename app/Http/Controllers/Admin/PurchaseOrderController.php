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
use Illuminate\Support\Facades\Storage;

class PurchaseOrderController extends Controller
{
    protected $approvalService;

    public function __construct(PurchaseOrderApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index(Request $request)
    {
        // Tab filter — 'semua' = tidak filter status
        $statusFilter  = $request->input('status', 'Pending');
        $sourceFilter  = $request->input('source_type', '');
        $tahunFilter   = $request->input('tahun', '');
        $sort          = $request->input('sort', 'terbaru');

        $query = PurchaseOrder::with(['approver', 'pembayaran']);

        if ($statusFilter === 'Ditolak') {
            // Tampilkan: PO Ditolak sepenuhnya ATAU PO Disetujui yang punya item rejected
            $query->where(function ($q) {
                $q->where('status', 'Ditolak')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'Disetujui')
                         ->whereRaw("JSON_SEARCH(JSON_EXTRACT(source_data, '$.item_decisions[*].action'), 'one', 'rejected') IS NOT NULL");
                  });
            });
        } elseif ($statusFilter === 'Disetujui') {
            // Tampilkan: PO Disetujui ATAU PO Pending yang punya item locked (partial resubmit)
            // PO partial yang item-nya sebagian pending muncul di dua tab sekaligus:
            // - Di Disetujui: hanya expand item approved
            // - Di Pending: hanya expand item yang belum di-approve
            $query->where(function ($q) {
                $q->where('status', 'Disetujui')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'Pending')
                         ->whereRaw("JSON_LENGTH(JSON_EXTRACT(source_data, '$.locked_approved_idx')) > 0");
                  });
            });
        } elseif ($statusFilter === 'Pending') {
            // Tab Pending: exclude PO yang total_barang = 0 (semua item sudah locked/approved)
            $query->where('status', 'Pending')->where('total_barang', '>', 0);
        } elseif ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        if ($sourceFilter) {
            $query->where('source_type', $sourceFilter);
        }

        if ($tahunFilter) {
            $query->whereYear('tanggal_po', $tahunFilter);
        }

        if ($sort === 'terlama') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $data = $query->paginate(15)->withQueryString();

        // Statistics — ikut filter tahun jika ada
        $statsQuery = PurchaseOrder::query();
        if ($tahunFilter) {
            $statsQuery->whereYear('tanggal_po', $tahunFilter);
        }

        $totalClosed   = (clone $statsQuery)->where('status_po', 'Closed')->count();
        $totalNominal  = (clone $statsQuery)->whereIn('status', ['Disetujui', 'Pending'])->sum('total_harga');

        // ── Hitung statistik PER ITEM (bukan per PO) ──────────────────────────────
        // Ambil semua PO untuk dihitung item-nya di PHP
        $allStatsPos = (clone $statsQuery)->get(['id', 'status', 'source_type', 'total_harga', 'source_data']);

        $totalPO       = $allStatsPos->count();
        $totalPending  = 0;
        $totalApproved = 0;
        $totalRejected = 0;
        $nominalPending  = 0;
        $nominalApproved = 0;
        $nominalRejected = 0;

        // Source types yang mendukung per-item counting
        $perItemTypes = ['gps', 'gps_perpanjang', 'service_part', 'service_incident', 'service_asuransi'];

        foreach ($allStatsPos as $_sPo) {
            $_sd          = $_sPo->source_data ?? [];
            $_gpsI        = $_sd['gps_items'] ?? [];
            $_parts       = $_sd['parts'] ?? [];
            $_kejad       = $_sd['kejadians'] ?? [];
            $_dec         = $_sd['item_decisions'] ?? [];
            $_locked      = array_map('intval', $_sd['locked_approved_idx'] ?? []);
            $_decColl     = collect($_dec)->keyBy(fn($d) => (int)($d['idx'] ?? -1));

            if (!empty($_gpsI) && in_array($_sPo->source_type, ['gps', 'gps_perpanjang'])) {
                // GPS: hitung per gps_item
                foreach ($_gpsI as $_gi => $_gitem) {
                    $_iNom = (int)($_gitem['biaya_sewa'] ?? 0);
                    if ($_decColl->has((int)$_gi)) {
                        $act = $_decColl[(int)$_gi]['action'] ?? '';
                        if ($act === 'approved') { $totalApproved++; $nominalApproved += $_iNom; }
                        elseif ($act === 'rejected') { $totalRejected++; $nominalRejected += $_iNom; }
                        else { $totalPending++; $nominalPending += $_iNom; }
                    } elseif (in_array((int)$_gi, $_locked)) {
                        $totalApproved++; $nominalApproved += $_iNom;
                    } elseif ($_sPo->status === 'Disetujui') {
                        $totalApproved++; $nominalApproved += $_iNom;
                    } elseif ($_sPo->status === 'Ditolak') {
                        $totalRejected++; $nominalRejected += $_iNom;
                    } else {
                        $totalPending++; $nominalPending += $_iNom;
                    }
                }
            } elseif (!empty($_parts) && in_array($_sPo->source_type, ['service_part', 'service_incident'])) {
                // Service Part / Incident: hitung per part
                // Debug: Log untuk PO yang memiliki parts
                \Log::info("Processing PO #{$_sPo->id} ({$_sPo->source_type})", [
                    'status' => $_sPo->status,
                    'parts_count' => count($_parts),
                    'decisions_count' => count($_dec),
                    'locked_count' => count($_locked),
                    'dec_has_idx' => !empty($_dec) && isset($_dec[0]['idx']),
                ]);
                
                foreach ($_parts as $_pi => $_part) {
                    $_iNom = (int)($_part['biaya'] ?? 0);
                    if ($_decColl->has((int)$_pi)) {
                        $act = $_decColl[(int)$_pi]['action'] ?? '';
                        if ($act === 'approved') { $totalApproved++; $nominalApproved += $_iNom; }
                        elseif ($act === 'rejected') { $totalRejected++; $nominalRejected += $_iNom; }
                        else { $totalPending++; $nominalPending += $_iNom; }
                    } elseif (in_array((int)$_pi, $_locked)) {
                        $totalApproved++; $nominalApproved += $_iNom;
                    } elseif ($_sPo->status === 'Disetujui') {
                        $totalApproved++; $nominalApproved += $_iNom;
                    } elseif ($_sPo->status === 'Ditolak') {
                        $totalRejected++; $nominalRejected += $_iNom;
                    } else {
                        $totalPending++; $nominalPending += $_iNom;
                    }
                }
            } elseif (!empty($_kejad) && $_sPo->source_type === 'service_asuransi') {
                // Service Asuransi: hitung per kejadian
                foreach ($_kejad as $_ki => $_kej) {
                    $_iNom = (int)($_kej['biaya'] ?? 0);
                    if ($_decColl->has((int)$_ki)) {
                        $act = $_decColl[(int)$_ki]['action'] ?? '';
                        if ($act === 'approved') { $totalApproved++; $nominalApproved += $_iNom; }
                        elseif ($act === 'rejected') { $totalRejected++; $nominalRejected += $_iNom; }
                        else { $totalPending++; $nominalPending += $_iNom; }
                    } elseif (in_array((int)$_ki, $_locked)) {
                        $totalApproved++; $nominalApproved += $_iNom;
                    } elseif ($_sPo->status === 'Disetujui') {
                        $totalApproved++; $nominalApproved += $_iNom;
                    } elseif ($_sPo->status === 'Ditolak') {
                        $totalRejected++; $nominalRejected += $_iNom;
                    } else {
                        $totalPending++; $nominalPending += $_iNom;
                    }
                }
            } else {
                // Non-per-item types (pajak, asuransi_kendaraan, kir, stnk, dll): 1 PO = 1 item
                $_poNom = (int)($_sPo->total_harga ?? 0);
                if ($_sPo->status === 'Pending') {
                    $totalPending++; $nominalPending += $_poNom;
                } elseif ($_sPo->status === 'Disetujui') {
                    $totalApproved++; $nominalApproved += $_poNom;
                } elseif ($_sPo->status === 'Ditolak') {
                    $totalRejected++; $nominalRejected += $_poNom;
                }
            }
        }

        // Debug: Log hasil perhitungan per-item
        \Log::info('PO Per-Item Stats', [
            'totalPending' => $totalPending,
            'totalApproved' => $totalApproved,
            'totalRejected' => $totalRejected,
            'nominalPending' => $nominalPending,
            'nominalApproved' => $nominalApproved,
            'nominalRejected' => $nominalRejected,
            'total_pos_processed' => $allStatsPos->count(),
        ]);

        // ── Count per PO untuk badge di tab nav (terpisah dari per-item stats) ──────
        $totalPoForTab      = (clone $statsQuery)->count();
        $totalPendingPoTab  = (clone $statsQuery)->where('status', 'Pending')->where('total_barang', '>', 0)->count();
        $totalApprovedPoTab = (clone $statsQuery)->where(function ($q) {
            $q->where('status', 'Disetujui')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'Pending')
                     ->whereRaw("JSON_LENGTH(JSON_EXTRACT(source_data, '$.locked_approved_idx')) > 0");
              });
        })->count();
        $totalRejectedPoTab = (clone $statsQuery)->where(function ($q) {
            $q->where('status', 'Ditolak')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'Disetujui')
                     ->whereRaw("JSON_SEARCH(JSON_EXTRACT(source_data, '$.item_decisions[*].action'), 'one', 'rejected') IS NOT NULL");
              });
        })->count();

        // Source types untuk dropdown filter
        $sourceTypes = PurchaseOrder::selectRaw('source_type, count(*) as total')
            ->whereNotNull('source_type')
            ->groupBy('source_type')
            ->pluck('total', 'source_type');

        // Tahun tersedia berdasarkan tanggal_po
        $availableYears = PurchaseOrder::selectRaw('YEAR(tanggal_po) as yr')
            ->whereNotNull('tanggal_po')
            ->distinct()
            ->orderBy('yr', 'desc')
            ->pluck('yr')
            ->filter()
            ->values();

        return view('admin.purchaseo.index', compact(
            'data',
            'statusFilter',
            'sourceFilter',
            'tahunFilter',
            'availableYears',
            'sort',
            'totalPO',
            'totalApproved',
            'totalPending',
            'totalRejected',
            'totalClosed',
            'totalNominal',
            'nominalPending',
            'nominalApproved',
            'nominalRejected',
            'sourceTypes',
            'totalPoForTab',
            'totalPendingPoTab',
            'totalApprovedPoTab',
            'totalRejectedPoTab'
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

            // Tab context untuk filter items di detail modal
            $tab = request()->input('tab', 'semua');

            // Inject PO status ke sourceData agar buildDetailBySourceType bisa akses
            $sourceData['po_status'] = $po->status;

            // Build detail berdasarkan source_type — filter items sesuai tab
            $details = $this->buildDetailBySourceType($po->source_type, $sourceData, $tab);
            
            // Resolve source_type label
            $sourceTypeName = match($po->source_type) {
                'pajak'                         => 'Pajak Kendaraan',
                'pajak_perpanjang'              => 'Perpanjangan Pajak',
                'asuransi_kendaraan'            => 'Asuransi Kendaraan',
                'asuransi_kendaraan_perpanjang' => 'Perpanjangan Asuransi',
                'service_part'                  => 'Service Part',
                'service_asuransi'              => 'Service Asuransi',
                'service_incident'              => 'Service Incident',
                'gps'                           => 'GPS Kendaraan',
                'gps_perpanjang'                => 'Perpanjangan GPS',
                'kir'                           => 'KIR',
                'kir_perpanjang'                => 'Perpanjangan KIR',
                'stnk'                          => 'STNK',
                default                         => $po->source_type ? ucwords(str_replace('_', ' ', $po->source_type)) : 'Lainnya',
            };

            return response()->json([
                'success' => true,
                'po' => [
                    'po_id'              => $po->po_id,
                    'source_type'        => $po->source_type,
                    'source_type_name'   => $sourceTypeName,
                    'vendor'             => $po->vendor,
                    'pemohon'            => $po->pemohon,
                    'departemen'         => $po->departemen,
                    'total_barang'       => $po->total_barang,
                    'total_harga'        => $po->total_harga,
                    'tanggal_po'         => $po->tanggal_po ? $po->tanggal_po->format('d M Y') : '-',
                    'status'             => $po->status,
                    'catatan'            => $po->catatan,
                    'keterangan'         => $po->keterangan,
                    'catatan_approval'   => $po->catatan_approval,
                    'disetujui_oleh'     => $po->approver ? $po->approver->name : null,
                    'tanggal_persetujuan'=> $po->tanggal_persetujuan ? $po->tanggal_persetujuan->format('d M Y H:i') : null,
                    'pembayaran_no_pr'   => $po->pembayaran ? $po->pembayaran->no_pr : null,
                    'temp_files'         => $sourceData['temp_files'] ?? [],
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
    /**
     * Helper: resolve bukti_bayar_admin field ke {url, name} atau null
     */
    protected function resolveBuktiAdmin($bukti): ?array
    {
        if (!$bukti) return null;
        // bisa berupa path string, atau array [{path, original_name}], atau [[path, ...]]
        $arr = is_array($bukti) ? $bukti : [$bukti];
        $b0  = $arr[0] ?? null;
        if (!$b0) return null;
        if (is_array($b0)) {
            $path = $b0['path'] ?? '';
            $name = $b0['original_name'] ?? basename($path);
            if ($path) return ['url' => asset('storage/'.$path), 'name' => $name];
            // fallback: public path
            $path = $b0['path'] ?? '';
            return $path ? ['url' => asset($path), 'name' => $name] : null;
        }
        // string path
        return ['url' => asset($b0), 'name' => basename($b0)];
    }

    protected function buildDetailBySourceType($sourceType, $sourceData, $tab = 'semua')
    {
        if ($sourceType === 'gps') {
            return $this->buildGpsDetails($sourceData, $tab);
        }

        if ($sourceType === 'service_part') {
            return $this->buildServicePartDetails($sourceData, $tab);
        }

        if ($sourceType === 'service_asuransi') {
            return $this->buildServiceAsuransiDetails($sourceData, $tab);
        }

        if ($sourceType === 'service_incident') {
            return $this->buildServiceIncidentDetails($sourceData, $tab);
        }

        if ($sourceType === 'asuransi_kendaraan') {
            return $this->buildAsuransiDetails($sourceData);
        }

        if ($sourceType === 'pajak') {
            return $this->buildPajakDetails($sourceData);
        }

        if ($sourceType === 'kir') {
            return $this->buildKirDetails($sourceData);
        }

        // For other types, return raw data
        return [
            'type' => 'raw',
            'data' => $sourceData,
        ];
    }

    /**
     * Build Asuransi Kendaraan-specific details
     */
    protected function buildAsuransiDetails($sourceData)
    {
        $kendaraanId    = $sourceData['kendaraan_id'] ?? null;
        $kendaraan      = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;
        $asuransi       = isset($sourceData['asuransi_id']) ? \App\Models\Asuransi::find($sourceData['asuransi_id']) : null;
        $jenisAsuransi  = isset($sourceData['jenis_asuransi_id']) ? \App\Models\JenisAsuransi::find($sourceData['jenis_asuransi_id']) : null;

        $existingId = $sourceData['existing_record_id'] ?? null;
        $lampiran   = [];
        if ($existingId) {
            $attachments = \App\Models\Attachment::where('relation_type', 'asuransi')
                ->where('relation_id', $existingId)
                ->get();
            foreach ($attachments as $att) {
                $lampiran[] = [
                    'id'        => $att->id,
                    'file_name' => $att->file_name,
                    'file_path' => asset($att->file_path),
                    'file_type' => $att->file_type,
                    'file_size' => $att->file_size,
                ];
            }
        }

        return [
            'type'            => 'asuransi_kendaraan',
            'kendaraan'       => [
                'nopol' => $kendaraan?->nopol ?? '-',
                'merk'  => $kendaraan?->merk  ?? '-',
            ],
            'perusahaan'      => $asuransi?->nama_asuransi ?? '-',
            'jenis_asuransi'  => $jenisAsuransi?->nama_jenis ?? '-',
            'tgl_mulai'       => $sourceData['tgl_mulai'] ?? '-',
            'tgl_berakhir'    => $sourceData['tgl_berakhir'] ?? '-',
            'durasi_bulan'    => $sourceData['durasi_bulan'] ?? '-',
            'biaya'           => $sourceData['biaya'] ?? 0,
            'nama_bank'       => $sourceData['nama_bank'] ?? '-',
            'no_rekening'     => $sourceData['no_rekening'] ?? '-',
            'nama_rekening'   => $sourceData['nama_rekening'] ?? '-',
            'lampiran'        => $lampiran,
        ];
    }

    /**
     * Build Pajak Kendaraan-specific details
     */
    protected function buildPajakDetails($sourceData)
    {
        $kendaraanId = $sourceData['kendaraan_id'] ?? null;
        $kendaraan   = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;

        $existingId = $sourceData['existing_record_id'] ?? null;
        $lampiran   = [];
        if ($existingId) {
            $attachments = \App\Models\Attachment::where('relation_type', 'pajak')
                ->where('relation_id', $existingId)
                ->get();
            foreach ($attachments as $att) {
                $lampiran[] = [
                    'id'        => $att->id,
                    'file_name' => $att->file_name,
                    'file_path' => asset($att->file_path),
                    'file_type' => $att->file_type,
                    'file_size' => $att->file_size,
                ];
            }
        }

        return [
            'type'          => 'pajak',
            'kendaraan'     => [
                'nopol' => $kendaraan?->nopol ?? '-',
                'merk'  => $kendaraan?->merk  ?? '-',
            ],
            'jenis_pajak'   => $sourceData['jenis_pajak'] ?? '-',
            'nominal'       => $sourceData['nominal'] ?? 0,
            'jatuh_tempo'   => $sourceData['jatuh_tempo'] ?? '-',
            'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? '-',
            'nama_bank'     => $sourceData['nama_bank'] ?? '-',
            'no_rekening'   => $sourceData['no_rekening'] ?? '-',
            'nama_pemilik'  => $sourceData['nama_pemilik'] ?? '-',
            'keterangan'    => $sourceData['keterangan'] ?? '-',
            'lampiran'      => $lampiran,
        ];
    }

    /**
     * Build KIR-specific details
     */
    protected function buildKirDetails($sourceData)
    {
        $kendaraanId = $sourceData['kendaraan_id'] ?? null;
        $kendaraan   = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;

        $existingId = $sourceData['existing_record_id'] ?? null;
        $lampiran   = [];
        if ($existingId) {
            $attachments = \App\Models\Attachment::where('relation_type', 'kir')
                ->where('relation_id', $existingId)
                ->get();
            foreach ($attachments as $att) {
                $lampiran[] = [
                    'id'        => $att->id,
                    'file_name' => $att->file_name,
                    'file_path' => asset($att->file_path),
                    'file_type' => $att->file_type,
                    'file_size' => $att->file_size,
                ];
            }
        }

        return [
            'type'          => 'kir',
            'kendaraan'     => [
                'nopol' => $kendaraan?->nopol ?? '-',
                'merk'  => $kendaraan?->merk  ?? '-',
            ],
            'no_uji'        => $sourceData['no_uji'] ?? '-',
            'no_ktp'        => $sourceData['no_ktp'] ?? '-',
            'nama_ktp'      => $sourceData['nama_ktp'] ?? '-',
            'lokasi_uji'    => $sourceData['lokasi_uji'] ?? '-',
            'penguji'       => $sourceData['penguji'] ?? '-',
            'status_uji'    => $sourceData['status_uji'] ?? '-',
            'masa_berlaku'  => $sourceData['masa_berlaku'] ?? '-',
            'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? '-',
            'biaya'         => $sourceData['biaya'] ?? 0,
            'nama_bank'     => $sourceData['nama_bank'] ?? '-',
            'no_rekening'   => $sourceData['no_rekening'] ?? '-',
            'nama_rekening' => $sourceData['nama_rekening'] ?? '-',
            'lampiran'      => $lampiran,
        ];
    }

    /**
     * Build Service Asuransi-specific details (per-kejadian)
     */
    protected function buildServiceAsuransiDetails($sourceData, $tab = 'semua')
    {
        $kendaraanId      = $sourceData['kendaraan_id'] ?? null;
        $kendaraan        = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;
        $allKejadians     = $sourceData['kejadians'] ?? [];
        $tempFiles        = $sourceData['temp_files'] ?? [];
        $itemDecisions    = $sourceData['item_decisions'] ?? [];
        $lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx'] ?? []);
        $decMap = collect($itemDecisions)->keyBy('idx');

        // Filter kejadians berdasarkan tab jika ada item_decisions
        if ($decMap->isNotEmpty()) {
            if ($tab === 'Disetujui') {
                // Include item yang approved di item_decisions PLUS item yang locked dari partial sebelumnya
                $kejadians = collect($allKejadians)
                    ->filter(fn($k, $i) =>
                        ($decMap[$i]['action'] ?? '') === 'approved'
                        || in_array($i, $lockedApprovedIdx)
                    )
                    ->all();
            } elseif ($tab === 'Ditolak') {
                $kejadians = collect($allKejadians)
                    ->filter(fn($k, $i) => ($decMap[$i]['action'] ?? '') === 'rejected')
                    ->all();
            } else {
                $kejadians = $allKejadians;
            }
        } else {
            // decMap kosong: jika tab Disetujui dan ada locked items, tampilkan hanya yang locked
            if ($tab === 'Disetujui' && !empty($lockedApprovedIdx)) {
                $kejadians = collect($allKejadians)
                    ->filter(fn($k, $i) => in_array($i, $lockedApprovedIdx))
                    ->all();
            } elseif (in_array($tab, ['Pending', 'semua']) && !empty($lockedApprovedIdx)) {
                // Tab Pending/semua untuk PO resubmit: tampilkan kejadian yang tidak locked (belum diapprove)
                $kejadians = collect($allKejadians)
                    ->filter(fn($k, $i) => !in_array($i, $lockedApprovedIdx))
                    ->all();
            } elseif ($tab === 'Ditolak') {
                // FIX: Tab Ditolak + decMap kosong = PO fully rejected
                // Check PO status: jika "Ditolak", tampilkan semua kejadians
                $poStatus = $sourceData['po_status'] ?? 'Pending';
                if ($poStatus === 'Ditolak') {
                    // PO fully rejected: tampilkan semua items dengan action = 'rejected'
                    $kejadians = $allKejadians;
                } else {
                    // Edge case: tab Ditolak tapi PO tidak berstatus Ditolak dan tidak ada item_decisions
                    $kejadians = [];
                }
            } else {
                $kejadians = $allKejadians;
            }
        }

        $items = [];
        foreach ($kejadians as $idx => $kej) {
            // PRESERVE ORIGINAL INDEX: track index asli kejadian di allKejadians
            // Cari index asli kejadian ini dengan matching nama + biaya
            $origIdx = null;
            foreach ($allKejadians as $origIndex => $origKej) {
                if (($origKej['nama_kejadian'] ?? '') === ($kej['nama_kejadian'] ?? '')
                    && (int)($origKej['biaya'] ?? 0) === (int)($kej['biaya'] ?? 0)) {
                    $origIdx = $origIndex;
                    break;
                }
            }
            if ($origIdx === null) {
                $origIdx = $idx; // fallback
            }

            // Ambil lampiran dari temp_files per kejadian
            $tempKejFiles = $tempFiles['kejadians'][$origIdx] ?? [];
            $lampiran = [];
            foreach ($tempKejFiles as $tf) {
                $ext = strtolower($tf['extension'] ?? '');
                $lampiran[] = [
                    'file_name' => $tf['original_name'] ?? basename($tf['path'] ?? ''),
                    'file_path' => Storage::disk('public')->url($tf['path'] ?? ''),
                    'file_type' => $ext,
                    'file_size' => $tf['size'] ?? 0,
                ];
            }
            // Juga cek lampiran yang tersimpan langsung di kejadian
            foreach ($kej['lampiran'] ?? [] as $lf) {
                $ext = strtolower($lf['extension'] ?? '');
                $lampiran[] = [
                    'file_name' => $lf['original_name'] ?? basename($lf['path'] ?? ''),
                    'file_path' => Storage::disk('public')->url($lf['path'] ?? ''),
                    'file_type' => $ext,
                    'file_size' => $lf['size'] ?? 0,
                ];
            }

            // Bukti bayar dari item_decisions
            $itemDecisions = $sourceData['item_decisions'] ?? [];
            $decMap = collect($itemDecisions)->keyBy('idx');
            $buktiAdmin = $decMap->get($origIdx)['bukti'] ?? null;

            // Ambil action dari item_decisions
            $decMapForAction = collect($itemDecisions)->keyBy('idx');
            $decEntry = $decMapForAction->get($origIdx);
            
            // FIX: Fallback action berdasarkan PO status jika item_decisions kosong
            $itemAction = $decEntry['action'] ?? null;
            if ($itemAction === null) {
                $poStatus = $sourceData['po_status'] ?? 'Pending';
                if ($poStatus === 'Ditolak' && $tab === 'Ditolak') {
                    // PO fully rejected: semua items dianggap rejected
                    $itemAction = 'rejected';
                } elseif ($poStatus === 'Disetujui' && $tab === 'Disetujui') {
                    // PO fully approved: semua items dianggap approved
                    $itemAction = 'approved';
                } elseif ($poStatus === 'Pending') {
                    // PO pending: semua items pending
                    $itemAction = 'pending';
                }
            }

            $items[] = [
                'po_number'        => $sourceData['item_po_numbers'][$origIdx] ?? null,
                'nama_kejadian'    => $kej['nama_kejadian'] ?? '-',
                'biaya'            => (int) ($kej['biaya'] ?? 0),
                // FIX: Fallback dari 'tanggal_service' ke 'tanggal' (key asli di source_data)
                'tanggal_service'  => $kej['tanggal_service'] ?? ($kej['tanggal'] ?? null),
                'periode_mulai'    => $kej['periode_mulai'] ?? null,
                'periode_selesai'  => $kej['periode_selesai'] ?? null,
                'lampiran'         => $lampiran,
                'action'           => $itemAction,
                'catatan_penolakan'=> $decEntry['catatan'] ?? null,
                'bukti'            => $buktiAdmin && isset($buktiAdmin['path'])
                    ? ['url' => asset($buktiAdmin['path']), 'name' => $buktiAdmin['original_name'] ?? basename($buktiAdmin['path'])]
                    : null,
                '_original_idx'    => $origIdx, // CRITICAL: preserve original index untuk approval
            ];
        }

        return [
            'type'                => 'service_asuransi',
            'kendaraan'           => [
                'nopol' => $kendaraan?->nopol ?? '-',
                'merk'  => $kendaraan?->merk  ?? '-',
            ],
            'nama_asuransi'       => $sourceData['nama_asuransi']   ?? '-',
            'keterangan'          => $sourceData['keterangan']       ?? '-',
            'tanggal_service'     => $sourceData['tanggal_service']  ?? '-',
            'kilometer'           => $sourceData['kilometer']        ?? '-',
            'locked_approved_idx' => $sourceData['locked_approved_idx'] ?? [],
            'items'               => $items,
        ];
    }

    /**
     * Build Service Incident-specific details (per-part seperti service_part)
     */
    protected function buildServiceIncidentDetails($sourceData, $tab = 'semua')
    {
        // Service incident punya struktur sama dengan service_part
        $details = $this->buildServicePartDetails($sourceData, $tab);
        $details['type'] = 'service_incident';
        return $details;
    }

    /**
     * Build Service Part-specific details
     */
    protected function buildServicePartDetails($sourceData, $tab = 'semua')
    {
        $allParts    = $sourceData['parts'] ?? [];
        $kendaraanId = $sourceData['kendaraan_id'] ?? null;
        $kendaraan   = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;

        // Filter parts berdasarkan tab jika ada item_decisions
        $itemDecisions     = $sourceData['item_decisions'] ?? [];
        $lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx'] ?? []);
        $decMap = collect($itemDecisions)->keyBy('idx');
        
        // Check: apakah PO ini fully approved tanpa rejected?
        $hasRejected = $decMap->where('action', 'rejected')->isNotEmpty();
        
        if ($decMap->isNotEmpty()) {
            if ($tab === 'Disetujui') {
                // Fix untuk PO fully approved: jika tidak ada rejected, tampilkan SEMUA parts
                if (!$hasRejected) {
                    $parts = $allParts;
                } else {
                    // Tampilkan: part yang approved di item_decisions PLUS part yang locked dari partial sebelumnya
                    $parts = collect($allParts)
                        ->filter(fn($p, $i) =>
                            ($decMap[$i]['action'] ?? '') === 'approved'
                            || in_array($i, $lockedApprovedIdx)
                        )
                        ->all();
                }
            } elseif ($tab === 'Ditolak') {
                $parts = collect($allParts)
                    ->filter(fn($p, $i) => ($decMap[$i]['action'] ?? '') === 'rejected')
                    ->all();
            } else {
                // Tab "semua" atau "Pending": tampilkan hanya yang belum diputuskan (exclude approved & rejected)
                // Ini mencegah locked items muncul di tab Pending saat PO partial resubmit
                $parts = collect($allParts)
                    ->filter(fn($p, $i) => 
                        !in_array($i, $lockedApprovedIdx) && 
                        !isset($decMap[$i])
                    )
                    ->all();
            }
        } else {
            // decMap kosong: jika tab Disetujui dan ada locked items, tampilkan hanya yang locked
            if ($tab === 'Disetujui' && !empty($lockedApprovedIdx)) {
                $parts = collect($allParts)
                    ->filter(fn($p, $i) => in_array($i, $lockedApprovedIdx))
                    ->all();
            } elseif ($tab === 'Ditolak' && !empty($lockedApprovedIdx)) {
                // Tab Ditolak + ada locked: tampilkan yang bukan locked (sedang pending resubmit)
                $parts = collect($allParts)
                    ->filter(fn($p, $i) => !in_array($i, $lockedApprovedIdx))
                    ->all();
            } elseif (($tab === 'Pending' || $tab === 'semua') && !empty($lockedApprovedIdx)) {
                // Tab Pending/semua + ada locked: exclude locked items, show only pending items
                $parts = collect($allParts)
                    ->filter(fn($p, $i) => !in_array($i, $lockedApprovedIdx))
                    ->all();
            } else {
                $parts = $allParts;
            }
        }

        // Ambil lampiran per-part dari temp_files yang disimpan saat upload
        $tempFiles = $sourceData['temp_files'] ?? [];
        $tempParts = $tempFiles['parts'] ?? [];

        $items = [];
        foreach ($parts as $origIdx => $part) {
            $category = isset($part['category_id']) ? \App\Models\ServiceCategory::find($part['category_id']) : null;

            // Ambil lampiran untuk part ini dari temp_files (menggunakan origIdx dari allParts)
            $lampiranFiles = $tempParts[$origIdx]['bukti'] ?? [];
            $lampiran = [];
            foreach ($lampiranFiles as $file) {
                $ext = strtolower($file['extension'] ?? '');
                $lampiran[] = [
                    'file_name' => $file['original_name'],
                    'file_path' => Storage::disk('public')->url($file['path']),
                    'file_type' => $ext,
                    'file_size' => $file['size'] ?? 0,
                ];
            }

            $items[] = [
                '_original_idx'    => $origIdx, // Track original index untuk submit
                'po_number'        => $sourceData['item_po_numbers'][$origIdx] ?? null,
                'nama_part'        => $part['nama_part'] ?? '-',
                'category_nama'    => $category ? $category->nama : ($part['nama_category_baru'] ?? '-'),
                'part_number'      => $part['part_number'] ?? '-',
                'kondisi'          => $part['kondisi'] ?? '-',
                'posisi'           => $part['posisi'] ?? '-',
                'serial_number'    => $part['serial_number'] ?? '-',
                'tanggal_pasang'   => $part['tanggal_pasang'] ?? $part['tgl_pasang'] ?? null,
                'biaya'            => $part['biaya'] ?? 0,
                'keterangan'       => $part['keterangan_limit'] ?? $part['keterangan'] ?? '-',
                'nama_bank'        => $part['nama_bank'] ?? '-',
                'no_rekening'      => $part['no_rekening'] ?? '-',
                'nama_rekening'    => $part['nama_rekening'] ?? '-',
                'supplier'         => isset($part['supplier_id'])
                    ? optional(\App\Models\Supplier::find($part['supplier_id']))->nama_supplier
                    : null,
                'limit_snapshot'   => $part['limit_snapshot'] ?? null,
                'keterangan_limit' => $part['keterangan_limit'] ?? $part['keterangan'] ?? null,
                'lampiran'         => $lampiran,
                'bukti'            => $this->resolveBuktiAdmin($part['bukti_bayar_admin'] ?? null),
                'action'           => $decMap->get($origIdx)['action'] ?? null,
                'catatan_penolakan'=> $decMap->get($origIdx)['catatan'] ?? null,
            ];
        }

        return [
            'type'                => 'service_part',
            'kendaraan'           => [
                'nopol' => $kendaraan ? $kendaraan->nopol : '-',
                'merk'  => $kendaraan ? $kendaraan->merk  : '-',
            ],
            'tanggal_service'     => $sourceData['tanggal_service'] ?? '-',
            'kilometer'           => $sourceData['kilometer'] ?? '-',
            'keluhan'             => $sourceData['keluhan'] ?? '-',
            'locked_approved_idx' => $sourceData['locked_approved_idx'] ?? [],
            'items'               => $items,
        ];
    }

    /**
     * Build GPS-specific details
     */
    protected function buildGpsDetails($sourceData, $tab = 'semua')
    {
        $allGpsItems = $sourceData['gps_items'] ?? [];
        $recordIds   = $sourceData['gps_record_ids'] ?? [];
        $kendaraanId = $sourceData['kendaraan_id'] ?? null;
        $kendaraan   = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;

        // Filter gps_items berdasarkan tab jika ada item_decisions
        $itemDecisions     = $sourceData['item_decisions'] ?? [];
        $lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx'] ?? []);
        $decMap = collect($itemDecisions)->keyBy('idx');
        if ($decMap->isNotEmpty()) {
            if ($tab === 'Disetujui') {
                // Tampilkan: item yang approved di item_decisions PLUS item yang locked dari partial sebelumnya
                $gpsItems = collect($allGpsItems)
                    ->filter(fn($g, $i) =>
                        ($decMap[$i]['action'] ?? '') === 'approved'
                        || in_array($i, $lockedApprovedIdx)
                    )
                    ->map(fn($g, $i) => array_merge($g, ['_orig_idx' => $i])) // ← INJECT ORIGINAL INDEX
                    ->values()
                    ->all();
            } elseif ($tab === 'Ditolak') {
                $gpsItems = collect($allGpsItems)
                    ->filter(fn($g, $i) => ($decMap[$i]['action'] ?? '') === 'rejected')
                    ->map(fn($g, $i) => array_merge($g, ['_orig_idx' => $i])) // ← INJECT ORIGINAL INDEX
                    ->values()
                    ->all();
            } else {
                // Tab semua/Pending: exclude item yang locked (sudah approved) DAN yang sudah
                // ada keputusan di item_decisions — hanya tampilkan yang belum diputuskan.
                // Ini mencegah item approved lama muncul di tab Pending.
                $gpsItems = collect($allGpsItems)
                    ->filter(fn($g, $i) =>
                        !in_array($i, $lockedApprovedIdx)
                        && !$decMap->has($i)
                    )
                    ->map(fn($g, $i) => array_merge($g, ['_orig_idx' => $i])) // ← INJECT ORIGINAL INDEX
                    ->values()
                    ->all();
            }
        } else {
            // decMap kosong: jika tab Disetujui dan ada locked items, tampilkan hanya yang locked
            if ($tab === 'Disetujui' && !empty($lockedApprovedIdx)) {
                $gpsItems = collect($allGpsItems)
                    ->filter(fn($g, $i) => in_array($i, $lockedApprovedIdx))
                    ->map(fn($g, $i) => array_merge($g, ['_orig_idx' => $i])) // ← INJECT ORIGINAL INDEX
                    ->values()
                    ->all();
            } elseif (in_array($tab, ['Pending', 'semua']) && !empty($lockedApprovedIdx)) {
                // Tab Pending/semua untuk PO resubmit: tampilkan item yang tidak locked (belum diapprove)
                $gpsItems = collect($allGpsItems)
                    ->filter(fn($g, $i) => !in_array($i, $lockedApprovedIdx))
                    ->map(fn($g, $i) => array_merge($g, ['_orig_idx' => $i])) // ← INJECT ORIGINAL INDEX
                    ->values()
                    ->all();
            } elseif ($tab === 'Ditolak') {
                // FIX: Tab Ditolak + decMap kosong = PO fully rejected
                // Check PO status: jika "Ditolak", tampilkan semua gps items
                $poStatus = $sourceData['po_status'] ?? 'Pending';
                if ($poStatus === 'Ditolak') {
                    // PO fully rejected: tampilkan semua items dengan action = 'rejected'
                    $gpsItems = collect($allGpsItems)
                        ->map(fn($g, $i) => array_merge($g, ['_orig_idx' => $i]))
                        ->values()
                        ->all();
                } else {
                    // Edge case: tab Ditolak tapi PO tidak berstatus Ditolak dan tidak ada item_decisions
                    $gpsItems = [];
                }
            } else {
                $gpsItems = collect($allGpsItems)
                    ->map(fn($g, $i) => array_merge($g, ['_orig_idx' => $i])) // ← INJECT ORIGINAL INDEX
                    ->values()
                    ->all();
            }
        }

        $items = [];
        foreach ($gpsItems as $idx => $item) {
            // ── FIX CRITICAL: Gunakan ARRAY_KEYS untuk preserve original index ──
            // Problem: $gpsItems dari filter() tidak preserve numeric key asli.
            // Solution: Pass original index via _orig_idx dari filter stage.
            
            $origIdx = $item['_orig_idx'] ?? null;
            
            // Fallback: coba match by gps_id + biaya_sewa
            if ($origIdx === null) {
                foreach ($allGpsItems as $origIndex => $origItem) {
                    if (($origItem['gps_id'] ?? null) === ($item['gps_id'] ?? null)
                        && (float)($origItem['biaya_sewa'] ?? 0) === (float)($item['biaya_sewa'] ?? 0)
                        && !in_array($origIndex, array_column($items, '_original_idx'))) { // avoid duplicate match
                        $origIdx = $origIndex;
                        break;
                    }
                }
            }
            
            // Ultimate fallback
            if ($origIdx === null) {
                $origIdx = $idx;
                \Log::warning('GPS Item Index Fallback', [
                    'loop_idx' => $idx,
                    'gps_id' => $item['gps_id'] ?? null,
                    'biaya' => $item['biaya_sewa'] ?? 0,
                ]);
            }

            $gps = isset($item['gps_id']) ? \App\Models\Gps::find($item['gps_id']) : null;

            // Ambil lampiran dari DB berdasarkan gps_record_id
            $recordId   = $recordIds[$origIdx] ?? null;
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

            // ── FIX: Tentukan action untuk item ini ────────────────────────────────
            // Logika priority:
            // 1. Jika ada di item_decisions → pakai action dari sana (approved/rejected)
            // 2. Jika ada di locked_approved_idx → action = 'approved' (locked dari cycle sebelumnya)
            // 3. Jika PO status "Pending" dan tidak ada decisions → action = 'pending' (belum diputuskan)
            // 4. Jika PO status "Disetujui" tapi item tidak ada di decisions/locked → action = null (seharusnya tidak terjadi)
            
            $itemAction = null;
            $itemCatatan = null;
            
            if ($decMap->has($origIdx)) {
                // Item sudah ada keputusan (approved/rejected)
                $itemAction = $decMap->get($origIdx)['action'] ?? null;
                $itemCatatan = $decMap->get($origIdx)['catatan'] ?? null;
            } elseif (in_array($origIdx, $lockedApprovedIdx)) {
                // Item di-lock approved dari cycle sebelumnya
                $itemAction = 'approved';
            } else {
                // Item belum ada keputusan
                // Cek status PO dari sourceData parent untuk determine default action
                $poStatus = $sourceData['po_status'] ?? 'Pending'; // fallback ke Pending
                if ($poStatus === 'Pending' && empty($itemDecisions)) {
                    // PO fresh (belum pernah di-approve) → semua item pending
                    $itemAction = 'pending';
                } elseif ($poStatus === 'Disetujui' && empty($itemDecisions) && empty($lockedApprovedIdx)) {
                    // PO full approved (legacy atau simple approval) → semua item approved
                    $itemAction = 'approved';
                } elseif ($poStatus === 'Ditolak' && empty($itemDecisions)) {
                    // FIX: PO fully rejected → semua item rejected
                    $itemAction = 'rejected';
                }
                // else: $itemAction tetap null (edge case, seharusnya tidak terjadi)
            }
            
            // DEBUG: Hanya log jika ada issue
            if ($origIdx !== $idx && $origIdx !== null) {
                \Log::debug('GPS Item Index Mismatch', [
                    'loop_idx' => $idx,
                    'orig_idx' => $origIdx,
                    'gps_id' => $item['gps_id'] ?? null,
                ]);
            }

            $items[] = [
                '_original_idx' => $origIdx, // CRITICAL: preserve original index untuk approval
                'po_number'   => $sourceData['item_po_numbers'][$origIdx] ?? null,
                'gps_name'    => $gps ? $gps->nama_gps : '-',
                'type'        => $item['type'] ?? '-',
                'biaya_sewa'  => $item['biaya_sewa'] ?? 0,
                'nama_bank'   => $item['nama_bank'] ?? '-',
                'no_rekening' => $item['no_rekening'] ?? '-',
                'nama_pemilik'=> $item['nama_pemilik'] ?? '-',
                'lampiran'    => $lampiran,
                // ── FIX: Tambahkan action dan catatan_penolakan untuk badge status ──
                'action'           => $itemAction,
                'catatan_penolakan'=> $itemCatatan,
            ];
        }

        return [
            'type'         => 'gps',
            'kendaraan'    => [
                'nopol' => $kendaraan ? $kendaraan->nopol : '-',
                'merk'  => $kendaraan ? $kendaraan->merk  : '-',
            ],
            'tanggal_bayar'       => $sourceData['tanggal_bayar'] ?? '-',
            'tanggal_habis'       => $sourceData['tanggal_habis'] ?? '-',
            'keterangan'          => $sourceData['keterangan'] ?? '-',
            'locked_approved_idx' => $sourceData['locked_approved_idx'] ?? [],
            'items'               => $items,
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
     * Approve GPS/Service Part Purchase Order per-item (pilih item mana yang disetujui/ditolak + bukti per item)
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
        $sourceType  = $po->source_type;
        $approvedIdx = [];
        $rejectedIdx = [];

        // ── Ambil locked items yang sudah approved sebelumnya ──
        $lockedApprovedIdx = collect($sourceData['locked_approved_idx'] ?? [])->map('intval')->toArray();

        // Validasi: item yang ditolak wajib ada catatannya
        foreach ($items as $idx => $decision) {
            $action = $decision['action'] ?? null;
            
            // ── SKIP locked items: sudah approved permanent, tidak boleh diubah ──
            if (in_array((int)$idx, $lockedApprovedIdx)) {
                continue;
            }
            
            if ($action === 'rejected' && empty(trim($decision['catatan'] ?? ''))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item #' . ((int)$idx + 1) . ' yang ditolak wajib ada alasan penolakan.',
                ], 422);
            }
            if ($action === 'approved') $approvedIdx[] = (int) $idx;
            if ($action === 'rejected') $rejectedIdx[] = (int) $idx;
        }

        // ── CRITICAL FIX: Jika tidak ada keputusan baru DAN tidak ada locked items ──
        // Artinya form kosong / bug frontend
        if (empty($approvedIdx) && empty($rejectedIdx) && empty($lockedApprovedIdx)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada keputusan yang diberikan.'], 422);
        }
        
        // ── Jika ada locked items tapi tidak ada keputusan baru, anggap PO sudah fully approved ──
        // Ini seharusnya tidak terjadi (modal tidak muncul untuk PO fully approved), 
        // tapi jika terjadi, tolak request
        if (empty($approvedIdx) && empty($rejectedIdx) && !empty($lockedApprovedIdx)) {
            return response()->json(['success' => false, 'message' => 'PO ini sudah fully approved sebelumnya.'], 422);
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

            // Route based on source_type
            if ($sourceType === 'gps') {
                return $this->approveItemsGps($po, $sourceData, $items, $approvedIdx, $rejectedIdx, $perItemBukti, $catatan, $hasApproved, $hasRejected);
            } elseif ($sourceType === 'service_part') {
                return $this->approveItemsServicePart($po, $sourceData, $items, $approvedIdx, $rejectedIdx, $perItemBukti, $catatan, $hasApproved, $hasRejected);
            } elseif ($sourceType === 'service_asuransi') {
                return $this->approveItemsServiceAsuransi($po, $sourceData, $items, $approvedIdx, $rejectedIdx, $catatan, $hasApproved, $hasRejected);
            } elseif ($sourceType === 'service_incident') {
                return $this->approveItemsServicePart($po, $sourceData, $items, $approvedIdx, $rejectedIdx, $perItemBukti, $catatan, $hasApproved, $hasRejected);
            } else {
                throw new \Exception('Unsupported source_type: ' . $sourceType);
            }

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approveItems PO #' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Approve simple — untuk PO non-GPS/non-service_part (pajak, asuransi, KIR, STNK, dll)
     * Tidak ada per-item decision, langsung approve seluruh PO dan buat Pembayaran otomatis
     */
    public function approveSimple(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'Pending') {
            return response()->json(['success' => false, 'message' => 'Hanya PO dengan status Pending yang dapat disetujui.'], 422);
        }

        // GPS dan service_part harus pakai approve-items (per-item decision)
        if (in_array($po->source_type, ['gps', 'gps_perpanjang', 'service_part', 'service_asuransi', 'service_incident'])) {
            return response()->json(['success' => false, 'message' => 'Tipe ini harus menggunakan approve per-item.'], 422);
        }

        $catatan    = $request->input('catatan', '');
        $sourceData = $po->source_data ?? [];

        try {
            \DB::beginTransaction();

            // Update PO status
            $po->update([
                'status'              => 'Disetujui',
                'disetujui_oleh'      => auth()->id(),
                'tanggal_persetujuan' => now(),
                'catatan_approval'    => $catatan ?: null,
                'source_data'         => $sourceData,
            ]);

            // Buat Pembayaran otomatis (approveWithItems akan buat ServiceHistory draft jika service_part)
            $pembayaran = $this->approvalService->approveWithItems($po, $sourceData, [], $catatan, false);

            // Link pembayaran ke PO
            $po->update(['pembayaran_id' => $pembayaran->id]);

            \DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'PO ' . $po->po_id . ' disetujui. Pembayaran ' . $pembayaran->no_pr . ' otomatis dibuat.',
                'redirect' => route('purchase-order.index', ['status' => 'Disetujui']),
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approveSimple PO #' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reject simple — untuk PO non-GPS/non-service_part (pajak, asuransi, KIR, STNK, dll)
     */
    public function rejectSimple(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'Pending') {
            return response()->json(['success' => false, 'message' => 'Hanya PO dengan status Pending yang dapat ditolak.'], 422);
        }

        if (in_array($po->source_type, ['gps', 'gps_perpanjang', 'service_part', 'service_asuransi', 'service_incident'])) {
            return response()->json(['success' => false, 'message' => 'Tipe ini harus menggunakan reject per-item.'], 422);
        }

        $catatan = $request->input('catatan', '');
        if (empty(trim($catatan))) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.'], 422);
        }

        try {
            \DB::beginTransaction();

            $po->update([
                'status'              => 'Ditolak',
                'disetujui_oleh'      => auth()->id(),
                'tanggal_persetujuan' => now(),
                'catatan_approval'    => $catatan,
                'can_edit'            => true,
            ]);

            \DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'PO ' . $po->po_id . ' ditolak.',
                'redirect' => route('purchase-order.index', ['status' => 'Ditolak']),
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error rejectSimple PO #' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle GPS-specific approval logic
     */
    private function approveItemsGps($po, $sourceData, $items, $approvedIdx, $rejectedIdx, $perItemBukti, $catatan, $hasApproved, $hasRejected)
    {
        $gpsItems = $sourceData['gps_items'] ?? [];
        $lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx'] ?? []);

        // Jika semua ditolak DAN tidak ada locked items → reject PO sepenuhnya
        if (!$hasApproved && empty($lockedApprovedIdx)) {
            $po->update([
                'status'              => 'Ditolak',
                'disetujui_oleh'      => auth()->id(),
                'tanggal_persetujuan' => now(),
                'catatan_approval'    => $catatan ?: collect($items)->pluck('catatan')->filter()->implode('; '),
                'can_edit'            => true,
            ]);

            // Dengan alur baru, record GPS belum ada saat PO Pending — tidak ada yang perlu dihapus.
            // Record baru hanya dibuat saat PO disetujui (updateLinkedRecord).

            \DB::commit();
            return response()->json([
                'success'  => true,
                'message'  => 'Semua item ditolak. Purchase Order ditolak.',
                'redirect' => route('purchase-order.index', ['status' => 'Ditolak']),
            ]);
        }

        // ── FIX: Case reject-only dengan locked items ──────────────────────────
        // Jika hanya ada rejection (tidak ada approval baru) DAN ada locked items:
        // → PO tetap "Disetujui" karena ada item yang sudah approved sebelumnya
        // → Item yang di-reject saat ini bisa di-resubmit
        
        // Build approved & rejected GPS item subsets (hanya yang baru diproses)
        $approvedGpsItems = array_values(
            array_filter($gpsItems, fn($item, $idx) => in_array($idx, $approvedIdx), ARRAY_FILTER_USE_BOTH)
        );
        
        // Locked GPS items (yang sudah approved sebelumnya)
        $lockedGpsItems = array_values(
            array_filter($gpsItems, fn($item, $idx) => in_array($idx, $lockedApprovedIdx), ARRAY_FILTER_USE_BOTH)
        );

        $nominalApprovedNew = collect($approvedGpsItems)->sum(fn($i) => $i['biaya_sewa'] ?? 0);

        // Nominal rejected items (yang baru di-reject)
        $nominalRejected = collect($gpsItems)
            ->filter(fn($item, $idx) => in_array($idx, $rejectedIdx), ARRAY_FILTER_USE_BOTH)
            ->sum(fn($i) => $i['biaya_sewa'] ?? 0);

        // Nominal approved yang sudah locked dari partial sebelumnya
        $nominalApprovedLocked = collect($lockedGpsItems)->sum(fn($i) => $i['biaya_sewa'] ?? 0);

        // Build item_decisions hanya untuk items yang diproses (approved + rejected), skip locked
        $itemDecisions = [];
        foreach ($items as $idx => $decision) {
            // Skip locked items — sudah diputuskan sebelumnya
            if (in_array((int)$idx, $lockedApprovedIdx)) continue;
            
            $gpsItem  = $gpsItems[$idx] ?? null;
            if (!$gpsItem) {
                \Log::warning('GPS Item not found for decision', ['idx' => $idx, 'total_gps_items' => count($gpsItems)]);
                continue;
            }
            
            $gpsModel = isset($gpsItem['gps_id']) ? \App\Models\Gps::find($gpsItem['gps_id']) : null;
            $itemDecisions[] = [
                'idx'      => (int)$idx,
                'nama_gps' => $gpsModel->nama_gps ?? '-',
                'type'     => $gpsItem['type'] ?? '-',
                'action'   => $decision['action'] ?? 'rejected',
                'catatan'  => $decision['catatan'] ?? null,
            ];
        }
        
        // ── DEBUG: Log final decisions ──────────────────────────────────────────
        \Log::info('GPS Approval - Item Decisions Built', [
            'po_id' => $po->id,
            'po_number' => $po->po_id,
            'total_decisions' => count($itemDecisions),
            'approved_idx' => $approvedIdx,
            'rejected_idx' => $rejectedIdx,
            'locked_idx' => $lockedApprovedIdx,
            'decisions' => $itemDecisions,
            'request_items' => array_map(fn($i) => [
                'idx' => $i,
                'action' => $items[$i]['action'] ?? 'N/A'
            ], array_keys($items)),
        ]);

        // ── FIX: locked_approved_idx harus sudah terisi SEBELUM approval cycle ini ──
        // Jika ada approved baru, merge. Jika hanya reject, tetap gunakan locked lama.
        $newLockedIdx = $hasApproved 
            ? array_values(array_unique(array_merge($lockedApprovedIdx, $approvedIdx)))
            : $lockedApprovedIdx;

        // ── FIX: Total barang/harga harus dari locked + approved baru SAJA ──
        // Jangan hitung item yang rejected ke dalam total
        $totalApprovedItems = count($lockedGpsItems) + count($approvedGpsItems);
        $totalNominalApproved = $nominalApprovedLocked + $nominalApprovedNew;

        // ── FIX: Status PO logic ──────────────────────────────────────────────
        // Jika ada item approved (baru atau locked) → status "Disetujui"
        // Jika SEMUA item rejected → status "Ditolak" (sudah handle di atas)
        $finalStatus = ($totalApprovedItems > 0) ? 'Disetujui' : 'Ditolak';

        $po->update([
            'status'              => $finalStatus,
            'disetujui_oleh'      => auth()->id(),
            'tanggal_persetujuan' => now(),
            'catatan_approval'    => $catatan,
            'total_harga'         => $totalNominalApproved, // total approved (locked + baru)
            'total_barang'        => $totalApprovedItems,   // total approved items
            'can_edit'            => $hasRejected, // dapat diajukan ulang jika ada yang ditolak
            'source_data'         => array_merge($sourceData, [
                'gps_items'               => $gpsItems, // keep ALL items untuk history
                'item_decisions'          => $itemDecisions, // hanya yang diproses cycle ini
                'locked_approved_idx'     => $newLockedIdx,
                'nominal_approved_locked' => $totalNominalApproved,
                'nominal_rejected'        => $nominalRejected,
            ]),
        ]);

        // Jika ada approved baru, buat/update Pembayaran
        if ($hasApproved) {
            // Build approvedSourceData for Pembayaran — hanya approved items baru
            $approvedSourceData = array_merge($sourceData, [
                'gps_items'      => $approvedGpsItems,
                'item_decisions' => $itemDecisions,
            ]);
            $pembayaran = $this->approvalService->approveWithItems($po, $approvedSourceData, $perItemBukti, $catatan, $hasRejected);
            
            \DB::commit();
            
            $msg = "PO {$po->po_id}: " . count($approvedIdx) . " item disetujui";
            if ($hasRejected) $msg .= ", " . count($rejectedIdx) . " item ditolak";
            $msg .= ". Pembayaran {$pembayaran->no_pr} otomatis dibuat.";
        } else {
            // Reject-only: tidak ada pembayaran baru, hanya update PO
            \DB::commit();
            
            $msg = "PO {$po->po_id}: " . count($rejectedIdx) . " item ditolak. "
                 . count($lockedGpsItems) . " item sebelumnya tetap disetujui.";
        }

        return response()->json([
            'success'  => true,
            'message'  => $msg,
            'redirect' => route('purchase-order.index', ['status' => 'Disetujui']),
        ]);
    }

    /**
     * Handle service_part-specific approval logic
     */
    private function approveItemsServicePart($po, $sourceData, $items, $approvedIdx, $rejectedIdx, $perItemBukti, $catatan, $hasApproved, $hasRejected)
    {
        $parts = $sourceData['parts'] ?? [];
        
        // ── Index yang sudah locked approved dari partial sebelumnya — tidak boleh diproses ulang ──
        // Pattern copied from approveItemsServiceAsuransi untuk consistency
        $lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx'] ?? []);

        // Jika semua ditolak → cek apakah ada locked items sebelumnya
        if (!$hasApproved) {
            // Jika ada locked_approved_idx: sebagian item sudah approved di Pembayaran sebelumnya.
            // PO tidak boleh jadi 'Ditolak' penuh — set ke 'Disetujui' dengan item pending sebagai rejected.
            if (!empty($lockedApprovedIdx)) {
                // Semua item yang sedang di-review (bukan locked) di-reject
                $allRejectedIdx = array_values(array_filter(
                    array_keys($parts),
                    fn($i) => !in_array((int)$i, $lockedApprovedIdx)
                ));
                $allPartDecisions = [];
                foreach ($parts as $idx => $part) {
                    if (in_array($idx, $lockedApprovedIdx)) continue;
                    $allPartDecisions[] = [
                        'idx'       => $idx,
                        'nama_part' => $part['nama_part'] ?? '-',
                        'category'  => $part['category_nama'] ?? '-',
                        'action'    => 'rejected',
                        'catatan'   => $items[$idx]['catatan'] ?? $catatan,
                    ];
                }
                $nominalRejected = collect($allRejectedIdx)->sum(fn($i) => (int)($parts[$i]['biaya'] ?? 0));
                $nominalApprovedLockedPrev = (int)($sourceData['nominal_approved_locked'] ?? 0);

                $po->update([
                    'status'              => 'Disetujui',
                    'disetujui_oleh'      => auth()->id(),
                    'tanggal_persetujuan' => now(),
                    'catatan_approval'    => $catatan,
                    'total_harga'         => $nominalApprovedLockedPrev,
                    'total_barang'        => 0,
                    'can_edit'            => true, // bisa diajukan ulang
                    'source_data'         => array_merge($sourceData, [
                        'parts'                   => $parts,
                        'item_decisions'          => $allPartDecisions,
                        'locked_approved_idx'     => array_values($lockedApprovedIdx),
                        'nominal_approved_locked' => $nominalApprovedLockedPrev,
                        'nominal_rejected'        => $nominalRejected,
                    ]),
                ]);

                \DB::commit();
                return response()->json([
                    'success'  => true,
                    'message'  => 'Semua part baru ditolak. Part yang sudah disetujui sebelumnya tetap terkunci.',
                    'redirect' => route('purchase-order.index', ['status' => 'Ditolak']),
                ]);
            }

            // Tidak ada locked → reject PO penuh
            $po->update([
                'status'              => 'Ditolak',
                'disetujui_oleh'      => auth()->id(),
                'tanggal_persetujuan' => now(),
                'catatan_approval'    => $catatan ?: collect($items)->pluck('catatan')->filter()->implode('; '),
                'can_edit'            => true,
            ]);

            \DB::commit();
            return response()->json([
                'success'  => true,
                'message'  => 'Semua part ditolak. Purchase Order ditolak.',
                'redirect' => route('purchase-order.index', ['status' => 'Ditolak']),
            ]);
        }

        // ── Ada part yang diapprove → ambil hanya approved ──
        // Exclude locked_approved_idx — item tersebut sudah diproses di Pembayaran sebelumnya
        // Pattern copied from approveItemsServiceAsuransi (line 1342-1343)
        $approvedIdx = array_values(array_filter($approvedIdx, fn($i) => !in_array($i, $lockedApprovedIdx)));
        $rejectedIdx = array_values(array_filter($rejectedIdx, fn($i) => !in_array($i, $lockedApprovedIdx)));

        // Pertahankan original index dari PO (JANGAN pakai array_values) agar saat merge
        // ke Pembayaran existing, index-nya konsisten dan tidak menimpa part lain.
        // Contoh: approved idx=1 (Bantol) tetap key 1, bukan di-reset ke key 0.
        $approvedParts = array_filter(
            $parts, fn($part, $idx) => in_array($idx, $approvedIdx), ARRAY_FILTER_USE_BOTH
        );

        $nominalApproved = collect($approvedParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));

        // ── Build item_decisions for non-locked parts only ──
        // Item locked sudah selesai di Pembayaran sebelumnya — tidak perlu keputusan baru
        // Pattern copied from approveItemsServiceAsuransi (line 1353-1362)
        $allPartDecisions = [];
        foreach ($parts as $idx => $part) {
            if (in_array($idx, $lockedApprovedIdx)) continue; // skip locked
            $allPartDecisions[] = [
                'idx'       => $idx,
                'nama_part' => $part['nama_part'] ?? '-',
                'category'  => $part['category_nama'] ?? '-',
                'action'    => in_array($idx, $approvedIdx) ? 'approved' : 'rejected',
                'catatan'   => $items[$idx]['catatan'] ?? null,
            ];
        }

        // Hitung nominal_rejected dari $allPartDecisions yang sudah di-build dengan benar.
        // Menggunakan $allPartDecisions (bukan $rejectedParts dari array_filter) agar
        // konsisten dengan apa yang benar-benar tersimpan ke item_decisions, dan menghindari
        // potensi key mismatch antara $rejectedIdx dan key array $parts.
        $nominalRejected = 0;
        foreach ($allPartDecisions as $decision) {
            if (($decision['action'] ?? '') === 'rejected') {
                $idx = $decision['idx'];
                $nominalRejected += (int)(($parts[$idx]['biaya'] ?? $parts[(int)$idx]['biaya'] ?? 0));
            }
        }

        // Ambil nominal_approved_locked dari partial sebelumnya (jika ada)
        $nominalApprovedLockedPrev = (int)($sourceData['nominal_approved_locked'] ?? 0);

        // locked_approved_idx: gabung dari sebelumnya + yang baru diapprove
        // Pattern copied from approveItemsServiceAsuransi (line 1369-1370)
        $prevLockedIdx = array_map('intval', $sourceData['locked_approved_idx'] ?? []);
        $newLockedIdx  = array_unique(array_merge($prevLockedIdx, $approvedIdx));

        // Keep ALL parts in source_data. item_decisions tracks which are approved/rejected.
        // Pattern copied from approveItemsServiceAsuransi (line 1372-1385)
        $po->update([
            'status'              => 'Disetujui',
            'disetujui_oleh'      => auth()->id(),
            'tanggal_persetujuan' => now(),
            'catatan_approval'    => $catatan,
            'total_harga'         => $nominalApprovedLockedPrev + $nominalApproved, // akumulasi semua approved
            'total_barang'        => count($approvedParts),
            'can_edit'            => $hasRejected, // dapat diajukan ulang jika ada yang ditolak
            'source_data'         => array_merge($sourceData, [
                'parts'                   => $parts, // keep ALL parts
                'item_decisions'          => $allPartDecisions,
                'locked_approved_idx'     => array_values($newLockedIdx),
                'nominal_approved_locked' => $nominalApprovedLockedPrev + $nominalApproved,
                'nominal_rejected'        => $nominalRejected,
            ]),
        ]);

        // Build approvedSourceData for Pembayaran — only approved parts
        // PENTING: Pembayaran hanya dapat parts yang approved, TAPI kita perlu
        // pastikan index parts tetap sama dengan PO agar tidak ada mismatch.
        $approvedSourceData = array_merge($sourceData, [
            'parts'          => $approvedParts, // Hanya approved
            'item_decisions' => array_values($allPartDecisions),
        ]);

        // service_incident: reset total_biaya_override agar nominal Pembayaran
        // hanya hitungan parts yang diapprove, bukan semua parts original
        if ($po->source_type === 'service_incident') {
            $approvedSourceData['total_biaya_override'] = $nominalApproved;
            // Pastikan service_incident_id selalu ada di source_data Pembayaran
            // agar transferServiceIncident bisa menemukan incident yang tepat
            if (empty($approvedSourceData['service_incident_id']) && !empty($sourceData['service_incident_id'])) {
                $approvedSourceData['service_incident_id'] = $sourceData['service_incident_id'];
            }
        }

        $pembayaran = $this->approvalService->approveWithItems($po, $approvedSourceData, $perItemBukti, $catatan, $hasRejected);

        // ServiceHistory draft sudah dibuat dari dalam approveWithItems sebelum updateLinkedRecord
        // (tidak perlu panggil createServiceHistoryDraft di sini lagi untuk service_part biasa)

        \DB::commit();

        $msg = "PO {$po->po_id}: " . count($approvedIdx) . " part disetujui";
        if ($hasRejected) $msg .= ", " . count($rejectedIdx) . " part ditolak";
        $msg .= ". Pembayaran {$pembayaran->no_pr} otomatis dibuat.";

        return response()->json([
            'success'  => true,
            'message'  => $msg,
            'redirect' => route('purchase-order.index', ['status' => 'Disetujui']),
        ]);
    }

    /**
     * Handle service_asuransi-specific approval logic (per-kejadian)
     */
    private function approveItemsServiceAsuransi($po, $sourceData, $items, $approvedIdx, $rejectedIdx, $catatan, $hasApproved, $hasRejected)
    {
        $kejadians         = $sourceData['kejadians'] ?? [];
        $serviceAsuransiId = $sourceData['service_asuransi_id'] ?? null;

        // Index yang sudah locked approved dari partial sebelumnya — tidak boleh diproses ulang
        $lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx'] ?? []);

        // Jika semua ditolak → cek apakah ada locked items sebelumnya
        if (!$hasApproved) {
            // Jika ada locked_approved_idx: sebagian kejadian sudah approved di Pembayaran sebelumnya.
            // PO tidak boleh jadi 'Ditolak' penuh — set ke 'Disetujui' dengan kejadian pending sebagai rejected.
            if (!empty($lockedApprovedIdx)) {
                $allRejectedIdx = array_values(array_filter(
                    array_keys($kejadians),
                    fn($i) => !in_array((int)$i, $lockedApprovedIdx)
                ));
                $allDecisions = [];
                foreach ($kejadians as $idx => $kej) {
                    if (in_array($idx, $lockedApprovedIdx)) continue;
                    $allDecisions[] = [
                        'idx'           => $idx,
                        'nama_kejadian' => $kej['nama_kejadian'] ?? '-',
                        'action'        => 'rejected',
                        'catatan'       => $items[$idx]['catatan'] ?? $catatan,
                    ];
                }
                $nominalRejected = collect($allRejectedIdx)->sum(fn($i) => (int)($kejadians[$i]['biaya'] ?? 0));
                $nominalApprovedLockedPrev = (int)($sourceData['nominal_approved_locked'] ?? 0);

                $po->update([
                    'status'              => 'Disetujui',
                    'disetujui_oleh'      => auth()->id(),
                    'tanggal_persetujuan' => now(),
                    'catatan_approval'    => $catatan,
                    'total_harga'         => $nominalApprovedLockedPrev,
                    'total_barang'        => 0,
                    'can_edit'            => true,
                    'source_data'         => array_merge($sourceData, [
                        'kejadians'               => $kejadians,
                        'item_decisions'          => $allDecisions,
                        'locked_approved_idx'     => array_values($lockedApprovedIdx),
                        'nominal_approved_locked' => $nominalApprovedLockedPrev,
                        'nominal_rejected'        => $nominalRejected,
                    ]),
                ]);

                if ($serviceAsuransiId) {
                    \App\Models\ServiceAsuransi::where('id', $serviceAsuransiId)
                        ->where('persetujuan', 'Diajukan ke Pembayaran')
                        ->update(['persetujuan' => 'Ditolak Pembayaran']);
                }

                \DB::commit();
                return response()->json([
                    'success'  => true,
                    'message'  => 'Semua kejadian baru ditolak. Kejadian yang sudah disetujui sebelumnya tetap terkunci.',
                    'redirect' => route('purchase-order.index', ['status' => 'Ditolak']),
                ]);
            }

            // Tidak ada locked → reject PO penuh
            $po->update([
                'status'              => 'Ditolak',
                'disetujui_oleh'      => auth()->id(),
                'tanggal_persetujuan' => now(),
                'catatan_approval'    => $catatan ?: collect($items)->pluck('catatan')->filter()->implode('; '),
                'can_edit'            => true,
            ]);

            if ($serviceAsuransiId) {
                \App\Models\ServiceAsuransi::where('id', $serviceAsuransiId)
                    ->where('persetujuan', 'Diajukan ke Pembayaran')
                    ->update(['persetujuan' => 'Ditolak Pembayaran']);
            }

            \DB::commit();
            return response()->json([
                'success'  => true,
                'message'  => 'Semua kejadian ditolak. Purchase Order ditolak.',
                'redirect' => route('purchase-order.index', ['status' => 'Ditolak']),
            ]);
        }

        // Ada kejadian yang diapprove → ambil hanya approved
        // Exclude locked_approved_idx — item tersebut sudah diproses di Pembayaran sebelumnya
        $approvedIdx = array_values(array_filter($approvedIdx, fn($i) => !in_array($i, $lockedApprovedIdx)));
        $rejectedIdx = array_values(array_filter($rejectedIdx, fn($i) => !in_array($i, $lockedApprovedIdx)));

        $approvedKejadians = array_values(
            array_filter($kejadians, fn($kej, $idx) => in_array($idx, $approvedIdx), ARRAY_FILTER_USE_BOTH)
        );

        $nominalApproved = collect($approvedKejadians)->sum(fn($k) => $k['biaya'] ?? 0);

        // Hitung rejected kejadians dan nominalnya
        $rejectedKejadians = array_values(
            array_filter($kejadians, fn($kej, $idx) => in_array($idx, $rejectedIdx), ARRAY_FILTER_USE_BOTH)
        );
        $nominalRejected = collect($rejectedKejadians)->sum(fn($k) => $k['biaya'] ?? 0);

        // Build item_decisions for non-locked kejadians only
        // Item locked sudah selesai di Pembayaran sebelumnya — tidak perlu keputusan baru
        $allDecisions = [];
        foreach ($kejadians as $idx => $kej) {
            if (in_array($idx, $lockedApprovedIdx)) continue; // skip locked
            $allDecisions[] = [
                'idx'           => $idx,
                'nama_kejadian' => $kej['nama_kejadian'] ?? '-',
                'action'        => in_array($idx, $approvedIdx) ? 'approved' : 'rejected',
                'catatan'       => $items[$idx]['catatan'] ?? null,
            ];
        }

        // Keep ALL kejadians in source_data. item_decisions tracks which are approved/rejected.
        // Simpan nominal_rejected agar query summary card & chart PO bisa menampilkan nilai yang benar.
        // Ambil nominal_approved_locked dari partial sebelumnya (jika ada)
        $nominalApprovedLockedPrev = (int)($sourceData['nominal_approved_locked'] ?? 0);

        // locked_approved_idx: gabung dari sebelumnya + yang baru diapprove
        $prevLockedIdxSA = array_map('intval', $sourceData['locked_approved_idx'] ?? []);
        $newLockedIdxSA  = array_unique(array_merge($prevLockedIdxSA, $approvedIdx));

        $po->update([
            'status'              => 'Disetujui',
            'disetujui_oleh'      => auth()->id(),
            'tanggal_persetujuan' => now(),
            'catatan_approval'    => $catatan,
            'total_harga'         => $nominalApprovedLockedPrev + $nominalApproved, // akumulasi semua approved
            'total_barang'        => count($approvedKejadians),
            'can_edit'            => $hasRejected, // dapat diajukan ulang jika ada yang ditolak
            'source_data'         => array_merge($sourceData, [
                'kejadians'               => $kejadians, // keep ALL kejadians
                'item_decisions'          => $allDecisions,
                'locked_approved_idx'     => array_values($newLockedIdxSA),
                'nominal_rejected'        => $nominalRejected,
                'nominal_approved_locked' => $nominalApprovedLockedPrev + $nominalApproved,
            ]),
        ]);

        // Build approvedSourceData for Pembayaran — only approved kejadians
        // Inject service_asuransi_id agar updateLinkedRecord bisa lookup record yang sudah ada
        $approvedSourceData = array_merge($sourceData, [
            'kejadians'      => $approvedKejadians,
            'item_decisions' => $allDecisions,
        ]);

        // Buat Pembayaran dari approved kejadians.
        // approveWithItems → updateLinkedRecord sudah mengurus:
        //   - Jika service_asuransi_id ada di source_data PO: update record + tambah kejadian baru
        //   - Jika belum ada: buat record baru
        // Tidak perlu blok update ServiceAsuransi/Kejadian di sini lagi.
        $pembayaran = $this->approvalService->approveWithItems($po, $approvedSourceData, [], $catatan, $hasRejected);

        \DB::commit();

        $msg = "PO {$po->po_id}: " . count($approvedIdx) . " kejadian disetujui";
        if ($hasRejected) $msg .= ", " . count($rejectedIdx) . " kejadian ditolak";
        $msg .= ". Pembayaran {$pembayaran->no_pr} otomatis dibuat.";

        return response()->json([
            'success'  => true,
            'message'  => $msg,
            'redirect' => route('purchase-order.index', ['status' => 'Disetujui']),
        ]);
    }

    /**
     * Buat ServiceHistory + ServicePart draft dengan status 'tidak_aktif'.
     * Dipanggil setelah PO service_part disetujui dan Pembayaran otomatis dibuat.
     * Record ini akan diaktifkan (status → proses, parts → Terpasang) saat pembayaran disetujui.
     *
     * @param \App\Models\Pembayaran $pembayaran  Pembayaran yang baru dibuat dari PO
     * @param array                  $sourceData  source_data PO yang sudah berisi approved parts
     */
    public function createServiceHistoryDraft(\App\Models\Pembayaran $pembayaran, array $sourceData): void
    {
        $kendaraanId    = $sourceData['kendaraan_id'] ?? null;
        $parts          = $sourceData['parts'] ?? [];

        if (!$kendaraanId || empty($parts)) {
            return;
        }

        // Guard: cek apakah ServiceHistory untuk pembayaran ini sudah ada (hindari double-create)
        // Jika sudah ada, JANGAN return — tetap lanjut untuk menambahkan ServicePart baru
        // yang belum ada (kasus resubmit partial: round ke-2 tambah part ke SH yang sama).
        $existingByPembayaran = \App\Models\ServiceHistory::lockForUpdate()
            ->where('pembayaran_id', $pembayaran->id)
            ->first();

        $tanggalService = $sourceData['tanggal_service'] ?? now()->toDateString();
        $kilometer      = (int) ($sourceData['kilometer'] ?? 0);
        $keluhan        = $sourceData['keluhan'] ?? null;
        $totalBiaya     = collect($parts)->sum(fn($p) => (int) ($p['biaya'] ?? 0));

        // Cek apakah ada part yang melebihi limit → status_pengeluaran = overservice
        $hasOverLimit = false;
        foreach ($parts as $part) {
            $categoryId = $part['category_id'] ?? null;
            $biaya      = (int) ($part['biaya'] ?? 0);
            if ($categoryId && $biaya > 0) {
                $limit = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $kendaraanId)
                    ->where('category_id', $categoryId)
                    ->first();
                if ($limit && $biaya > $limit->limit_price) {
                    $hasOverLimit = true;
                    break;
                }
            }
        }

        // ── Merge ke ServiceHistory yang sudah ada (1 kendaraan = 1 SH) ──────
        // Perpanjang/tambah part harus masuk ke SH yang sama, bukan buat baru.
        // Prioritas: SH yang sudah terkait dengan Pembayaran ini (resubmit round 2+),
        // fallback ke SH terbaru untuk kendaraan ini (round pertama).
        $existingSH = $existingByPembayaran
            ?? \App\Models\ServiceHistory::where('kendaraan_id', $kendaraanId)->latest()->first();

        // FIX: Kumpulkan nama_part dari $existingSH (bukan $existingByPembayaran).
        // Bug sebelumnya: existingPartNames diambil dari existingByPembayaran yang null
        // saat resubmit PO baru, padahal existingSH fallback ke SH lama yang sudah
        // punya parts — akibatnya guard duplikasi tidak pernah bekerja.
        $existingPartNames = $existingSH
            ? \App\Models\ServicePart::where('service_history_id', $existingSH->id)
                ->pluck('nama_part')
                ->map(fn($n) => strtolower(trim($n)))
                ->toArray()
            : [];

        if ($existingSH) {
            // Update header SH: tambah total_biaya, perbarui tanggal & kilometer jika lebih baru
            $existingSH->update([
                'total_biaya'     => $existingSH->total_biaya + $totalBiaya,
                'tanggal_service' => $tanggalService,
                'kilometer'       => max($kilometer, (int) $existingSH->kilometer),
                'keluhan'         => $keluhan ?: $existingSH->keluhan,
                // FIX: Jangan downgrade SH yang sudah aktif/Terpasang ke tidak_aktif.
                // Kasus: Pembayaran #1 sudah disetujui (SH = aktif), lalu PO #2 diapprove
                // → SH harus tetap aktif, bukan balik tidak_aktif.
                'status'          => in_array($existingSH->status, ['aktif', 'Terpasang'])
                                        ? $existingSH->status
                                        : 'tidak_aktif',
                'pembayaran_id'   => $pembayaran->id,
            ]);
            $serviceHistory = $existingSH;
        } else {
            // Belum ada SH sama sekali untuk kendaraan ini → buat baru
            $serviceHistory = \App\Models\ServiceHistory::create([
                'kendaraan_id'       => $kendaraanId,
                'tanggal_service'    => $tanggalService,
                'kilometer'          => $kilometer,
                'keluhan'            => $keluhan,
                'total_biaya'        => $totalBiaya,
                'status'             => 'tidak_aktif',
                'status_approval'    => 'approved',
                'status_pengeluaran' => $hasOverLimit ? 'overservice' : 'stabil',
                'approval_by'        => auth()->id(),
                'approval_at'        => now(),
                'is_request'         => true,
                'pembayaran_id'      => $pembayaran->id,
            ]);
        }

        // ── Update kilometer_sekarang kendaraan jika nilai baru lebih besar ──
        // Berlaku untuk semua approval PO service_part, termasuk hasil ajukan ulang.
        // tgl_pasang per-part sudah tersimpan di source_data dari form ajukan ulang.
        if ($kilometer > 0) {
            $kendaraanModel = \App\Models\Kendaraan::find($kendaraanId);
            if ($kendaraanModel && $kilometer > (int) ($kendaraanModel->kilometer_sekarang ?? 0)) {
                $kendaraanModel->update([
                    'kilometer_sekarang'  => $kilometer,
                    'km_terakhir_service' => $kilometer,
                ]);
            }
        }

        foreach ($parts as $idx => $part) {
            // Guard duplikasi: skip part yang sudah ada di SH ini (nama_part sama).
            // Kasus: resubmit round ke-2, part round ke-1 sudah tersimpan — jangan buat ulang.
            $namaPartNorm = strtolower(trim($part['nama_part'] ?? ''));
            if ($namaPartNorm && in_array($namaPartNorm, $existingPartNames)) {
                \Log::info('ServicePart SKIPPED (duplicate)', [
                    'nama_part' => $part['nama_part'],
                    'normalized' => $namaPartNorm,
                    'existing_names' => $existingPartNames,
                    'service_history_id' => $serviceHistory->id,
                ]);
                continue;
            }

            // Hitung tanggal_limit
            $tglPasang = \Carbon\Carbon::parse($part['tgl_pasang'] ?? $tanggalService);
            $interval  = (int) ($part['interval_nilai'] ?? 1);
            $satuan    = $part['interval_satuan'] ?? 'bulan';
            $tanggalLimit = match ($satuan) {
                'hari'   => (clone $tglPasang)->addDays($interval),
                'minggu' => (clone $tglPasang)->addWeeks($interval),
                'tahun'  => (clone $tglPasang)->addYears($interval),
                default  => (clone $tglPasang)->addMonths($interval),
            };

            // Copy temp_files bukti ke public path supaya tampil di kolom Lampiran
            $buktiFiles = [];
            $tempFiles  = $sourceData['temp_files'] ?? [];
            $partTempBukti = $tempFiles['parts'][$idx]['bukti'] ?? [];
            foreach ((array) $partTempBukti as $tf) {
                $storagePath = $tf['path'] ?? '';
                if (!$storagePath) continue;

                $sourceFull = storage_path('app/public/' . $storagePath);
                if (!file_exists($sourceFull)) {
                    $sourceFull = public_path($storagePath);
                }
                if (file_exists($sourceFull)) {
                    $destDir  = public_path('service-parts');
                    if (!file_exists($destDir)) mkdir($destDir, 0777, true);
                    $filename = time() . '_' . uniqid() . '_' . basename($storagePath);
                    \Illuminate\Support\Facades\File::copy($sourceFull, $destDir . '/' . $filename);
                    $buktiFiles[] = [
                        'path' => 'service-parts/' . $filename,
                        'name' => $tf['original_name'] ?? basename($storagePath),
                        'type' => $tf['extension'] ?? pathinfo($storagePath, PATHINFO_EXTENSION),
                    ];
                } elseif ($storagePath) {
                    $buktiFiles[] = [
                        'path' => $storagePath,
                        'name' => $tf['original_name'] ?? basename($storagePath),
                        'type' => $tf['extension'] ?? pathinfo($storagePath, PATHINFO_EXTENSION),
                    ];
                }
            }

            $newPart = \App\Models\ServicePart::create([
                'service_history_id' => $serviceHistory->id,
                'kendaraan_id'       => $kendaraanId,
                'category_id'        => $part['category_id'] ?? null,
                'supplier_id'        => !empty($part['supplier_id']) ? (int) $part['supplier_id'] : null,
                'nama_part'          => $part['nama_part'] ?? '',
                'part_number'        => $part['part_number'] ?? null,
                'serial_number'      => $part['serial_number'] ?? null,
                'posisi'             => $part['posisi'] ?? null,
                'tgl_pasang'         => $tglPasang->toDateString(),
                'kilometer_pasang'   => (int) ($part['kilometer_pasang'] ?? $kilometer),
                'kondisi'            => $part['kondisi'] ?? 'Perlu Ganti',
                'status'             => 'tidak_aktif',
                'interval_nilai'     => $interval,
                'interval_satuan'    => $satuan,
                'tanggal_limit'      => $tanggalLimit->toDateString(),
                'biaya'              => (int) ($part['biaya'] ?? 0),
                'keterangan_limit'   => $part['keterangan_limit'] ?? $part['keterangan'] ?? null,
                'nama_bank'          => $part['nama_bank'] ?? null,
                'no_rekening'        => $part['no_rekening'] ?? null,
                'nama_rekening'      => $part['nama_rekening'] ?? null,
                'bukti'              => !empty($buktiFiles) ? $buktiFiles : null,
                'is_request'         => true,
                'status_approval'    => 'approved',
                'approval_by'        => auth()->id(),
                'approval_at'        => now(),
                'persetujuan'        => 'Diajukan ke Pembayaran',
            ]);

            \Log::info('ServicePart CREATED', [
                'id' => $newPart->id,
                'nama_part' => $newPart->nama_part,
                'service_history_id' => $serviceHistory->id,
                'pembayaran_id' => $pembayaran->id,
                'persetujuan' => $newPart->persetujuan,
                'status' => $newPart->status,
            ]);

            // Recalculate keterangan_limit setelah part tersimpan ke DB.
            // Part baru sudah ada di DB dengan status tidak_aktif, sehingga:
            //   - aktifCount (jumlah) sudah termasuk part ini
            //   - total biaya dalam periode sudah termasuk biaya part ini
            // Keduanya harus di-recalculate agar keterangan akurat.
            $categoryId = $part['category_id'] ?? null;
            if ($categoryId) {
                $limitRule = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $kendaraanId)
                    ->where('category_id', $categoryId)
                    ->first();

                if ($limitRule) {
                    $shController = app(\App\Http\Controllers\Admin\ServiceHistoryController::class);

                    // ── Dimensi Jumlah ──────────────────────────────────────────────
                    $aktifCountNow = null;
                    $limitJumlahVal = null;
                    if ($limitRule->jumlah) {
                        $aktifCountNow  = \App\Models\ServicePart::where('kendaraan_id', $kendaraanId)
                            ->where('category_id', $categoryId)
                            ->whereIn('status', ['Terpasang', 'aktif'])
                            ->count();
                        $limitJumlahVal = (int) $limitRule->jumlah;
                    }

                    // ── Regenerate keterangan lengkap dari DB ───────────────────────
                    // Panggil generateKeteranganLimit dengan data terbaru dari DB.
                    // Part sudah tersimpan → total biaya periode & aktifCount sudah include part ini.
                    // Namun biaya di $partData adalah biaya part ini sendiri, dan
                    // getKumulatifBiayaKategori akan menjumlahkan SEMUA part di periode (termasuk ini).
                    // Untuk menghindari double-count, kita pass biaya = 0 sehingga
                    // kumulatif = total DB (sudah include part ini), tanpa tambah lagi.
                    $partDataForRecalc = array_merge($part, ['biaya' => 0]);
                    $tglPasangRecalc   = \Carbon\Carbon::parse($part['tgl_pasang'] ?? $tanggalService);

                    $keteranganBaru = $shController->generateKeteranganLimitPublic(
                        $partDataForRecalc,
                        $kilometer,
                        $limitRule,
                        $tanggalService,
                        null,
                        $aktifCountNow,
                        $limitJumlahVal
                    );

                    $newPart->update(['keterangan_limit' => $keteranganBaru]);
                }
            }
            // Part lama (replace_part_id) TIDAK di-archive di sini.
            // Archive hanya terjadi saat user klik tombol Pasang (updatePartStatus).
            // Selama menunggu pemasangan, part lama dan baru keduanya tampil di Tabel Aktif.
        }
    }

    /**
     * Resubmit GPS/Asuransi/Pajak/KIR Purchase Order yang ditolak
     * Untuk GPS: load data lama ke modal
     * Untuk Asuransi/Pajak/KIR: redirect ke dedicated ajukan-ulang page
     */
    public function resubmit(Request $request, $id)
    {
        try {
            $po = PurchaseOrder::findOrFail($id);

            if (!$po->isRejected()) {
                return response()->json(['success' => false, 'message' => 'Hanya PO yang ditolak yang dapat diajukan ulang.'], 422);
            }

            // service_part punya form standalone — tidak bergantung can_edit
            // PO partial (status Disetujui dengan item rejected) juga boleh resubmit
            // PO Pending dengan locked_approved_idx (resubmit setelah partial) juga boleh
            $isPartialResubmit = $po->status === 'Disetujui' && $po->isRejected();
            $isPendingPartial  = $po->status === 'Pending'
                && !empty($po->source_data['locked_approved_idx'] ?? [])
                && in_array($po->source_type, ['service_incident', 'service_asuransi', 'gps', 'gps_perpanjang']);
            if (!$po->can_edit && $po->source_type !== 'service_part' && !$isPartialResubmit && !$isPendingPartial) {
                return response()->json(['success' => false, 'message' => 'PO ini tidak dapat diedit.'], 422);
            }

            // Untuk Asuransi/Pajak/KIR: gunakan modal (Pattern 2, konsisten dengan GPS)
            if (in_array($po->source_type, ['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang', 'pajak', 'pajak_perpanjang', 'kir', 'kir_perpanjang'])) {
                // Delegate ke resubmitModal untuk return data ke modal form
                return $this->resubmitModal($request, $id);
            }

            // Service Part: redirect ke form create dengan edit_po param
            if ($po->source_type === 'service_part') {
                return response()->json([
                    'success'  => true,
                    'redirect' => route('service-history.create', ['edit_po' => $po->id]),
                ]);
            }

            // Service Asuransi: return data ke modal form
            if ($po->source_type === 'service_asuransi') {
                $sourceData  = $po->source_data ?? [];
                $kendaraanId = $sourceData['kendaraan_id'] ?? null;
                $kendaraan   = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;
                $allKejadians = $sourceData['kejadians'] ?? [];
                $itemDecisions = $sourceData['item_decisions'] ?? [];
                $decMap = collect($itemDecisions)->keyBy('idx');

                // Hanya kirim kejadian yang ditolak ke modal (partial) atau semua (Ditolak penuh)
                if ($decMap->isNotEmpty()) {
                    $kejadians = collect($allKejadians)
                        ->filter(fn($k, $i) => ($decMap[$i]['action'] ?? '') === 'rejected')
                        ->values()->all();
                } else {
                    $kejadians = $allKejadians;
                }

                // Enrich kejadian dengan lampiran dari temp_files
                $tempFiles = $sourceData['temp_files'] ?? [];
                $enrichedKejadians = array_map(function ($kej, $idx) use ($tempFiles) {
                    $tempKejFiles = $tempFiles['kejadians'][$idx] ?? [];
                    $lampiranExisting = [];
                    foreach ($tempKejFiles as $tf) {
                        $lampiranExisting[] = [
                            'path'          => $tf['path'] ?? '',
                            'original_name' => $tf['original_name'] ?? basename($tf['path'] ?? ''),
                            'extension'     => $tf['extension'] ?? '',
                            'size'          => $tf['size'] ?? 0,
                        ];
                    }
                    foreach ($kej['lampiran'] ?? [] as $lf) {
                        $lampiranExisting[] = $lf;
                    }
                    // PENTING: Kirim field dengan nama 'tanggal_service' ke frontend
                    // (form input pakai name tersebut), tapi source_data menyimpan dengan key 'tanggal'
                    return array_merge($kej, [
                        'lampiran_existing' => $lampiranExisting,
                        'tanggal_service'   => $kej['tanggal'] ?? ($kej['tanggal_service'] ?? null),
                        'periode_mulai'     => $kej['periode_mulai'] ?? null,
                        'periode_selesai'   => $kej['periode_selesai'] ?? null,
                    ]);
                }, $kejadians, array_keys($kejadians));

                return response()->json([
                    'success'        => true,
                    'po_id'          => $po->id,
                    'po_number'      => $po->po_id,
                    'catatan'        => $po->catatan_approval,
                    'kendaraan_id'   => $kendaraanId,
                    'nopol'          => $kendaraan ? $kendaraan->nopol : '-',
                    'merk'           => $kendaraan ? $kendaraan->merk  : '-',
                    'nama_asuransi'  => $sourceData['nama_asuransi']  ?? '',
                    'tanggal_service'=> $sourceData['tanggal_service'] ?? '',
                    'periode_mulai'  => $sourceData['periode_mulai']  ?? '',
                    'periode_selesai'=> $sourceData['periode_selesai'] ?? '',
                    'kilometer'      => $sourceData['kilometer']      ?? '',
                    'keterangan'     => $sourceData['keterangan']     ?? '',
                    'kejadians'      => $enrichedKejadians,
                ]);
            }

            // Service Incident: return data ke modal form (pakai modal yang sama dgn service_asuransi)
            if ($po->source_type === 'service_incident') {
                $sourceData  = $po->source_data ?? [];
                $kendaraanId = $sourceData['kendaraan_id'] ?? null;
                $kendaraan   = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;
                $allParts    = $sourceData['parts'] ?? [];
                
                // Filter: hanya parts yang rejected
                // Jika item_decisions kosong → PO ditolak penuh → semua parts perlu diajukan ulang
                $rawDecisions  = $sourceData['item_decisions'] ?? [];
                $itemDecisions = collect($rawDecisions)->keyBy('idx');

                if ($itemDecisions->isEmpty()) {
                    // PO Ditolak penuh: semua parts diajukan ulang
                    $rejectedParts = $allParts;
                } else {
                    // Partial rejection: hanya parts dengan action === 'rejected'
                    $rejectedParts = [];
                    foreach ($allParts as $idx => $part) {
                        $decision = $itemDecisions->get($idx);
                        if ($decision && ($decision['action'] ?? '') === 'rejected') {
                            $rejectedParts[$idx] = $part;
                        }
                    }
                    // Jika tidak ada satupun yang rejected (semua approved), fallback ke semua parts
                    if (empty($rejectedParts)) {
                        $rejectedParts = $allParts;
                    }
                }

                // Enrich rejected parts dengan lampiran dari temp_files
                $tempFiles = $sourceData['temp_files'] ?? [];
                $enrichedKejadians = array_map(function ($part, $idx) use ($tempFiles) {
                    $tempPartBukti = $tempFiles['parts'][$idx]['bukti'] ?? [];
                    $lampiranExisting = [];
                    $seenPaths = [];
                    
                    // Ambil dari model part (jika ada field lampiran/bukti)
                    foreach (($part['lampiran'] ?? []) as $lf) {
                        $path = $lf['path'] ?? '';
                        if (!$path || in_array($path, $seenPaths)) continue;
                        $seenPaths[] = $path;
                        $lampiranExisting[] = [
                            'path'          => $path,
                            'original_name' => $lf['original_name'] ?? basename($path),
                            'extension'     => $lf['extension'] ?? pathinfo($path, PATHINFO_EXTENSION),
                        ];
                    }
                    
                    // Fallback: ambil dari temp_files
                    foreach ($tempPartBukti as $tf) {
                        $path = $tf['path'] ?? '';
                        if (!$path || in_array($path, $seenPaths)) continue;
                        $seenPaths[] = $path;
                        $lampiranExisting[] = [
                            'path'          => $path,
                            'original_name' => $tf['original_name'] ?? basename($path),
                            'extension'     => $tf['extension'] ?? pathinfo($path, PATHINFO_EXTENSION),
                        ];
                    }
                    
                    return [
                        // Map part fields ke format yang expected di modal
                        'id'                => $part['service_part_id'] ?? $part['id'] ?? null, // PENTING: kirim id untuk edit mode detection
                        'service_part_id'   => $part['service_part_id'] ?? $part['id'] ?? null,
                        'nama_kejadian'     => $part['nama_part'] ?? '-',
                        'nama_part'         => $part['nama_part'] ?? '-',
                        'biaya'             => $part['biaya'] ?? 0,
                        'part_number'       => $part['part_number'] ?? '',
                        'serial_number'     => $part['serial_number'] ?? '',
                        'posisi'            => $part['posisi'] ?? '',
                        'tgl_pasang'        => $part['tgl_pasang'] ?? '',
                        'kilometer_pasang'  => $part['kilometer_pasang'] ?? 0,
                        'kondisi'           => $part['kondisi'] ?? 'Baik',
                        'supplier_id'       => $part['supplier_id'] ?? null,
                        'category_id'       => $part['category_id'] ?? null,
                        'nama_bank'         => $part['nama_bank'] ?? '',
                        'no_rekening'       => $part['no_rekening'] ?? '',
                        'nama_rekening'     => $part['nama_rekening'] ?? '',
                        'keterangan'        => $part['keterangan'] ?? '',
                        'lampiran_existing' => $lampiranExisting,
                    ];
                }, $rejectedParts, array_keys($rejectedParts));

                return response()->json([
                    'success'        => true,
                    'po_id'          => $po->id,
                    'po_number'      => $po->po_id,
                    'source_type'    => 'service_incident',
                    'catatan'        => $po->catatan_approval,
                    'kendaraan_id'   => $kendaraanId,
                    'nopol'          => $kendaraan ? $kendaraan->nopol : '-',
                    'merk'           => $kendaraan ? $kendaraan->merk  : '-',
                    'tanggal_service'=> $sourceData['tanggal_service'] ?? '',
                    'kilometer'      => $sourceData['kilometer']      ?? '',
                    'keterangan'     => $sourceData['keluhan']        ?? $sourceData['keterangan'] ?? '',
                    'kejadians'      => array_values($enrichedKejadians), // Re-index untuk modal
                ]);
            }

            // GPS: return data ke modal form
            $sourceData  = $po->source_data ?? [];
            $allGpsItems = $sourceData['gps_items'] ?? [];
            $kendaraanId = $sourceData['kendaraan_id'] ?? null;
            $kendaraan   = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;

            // Partial GPS: hanya kirim item yang rejected ke form, item locked tidak diubah
            $itemDecisions    = $sourceData['item_decisions'] ?? [];
            $lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx'] ?? []);
            $decMap = collect($itemDecisions)->keyBy('idx');

            if ($decMap->isNotEmpty()) {
                // Partial rejection: hanya GPS items dengan action = rejected
                $gpsItems = collect($allGpsItems)
                    ->filter(fn($item, $i) => ($decMap[$i]['action'] ?? '') === 'rejected')
                    ->all();
            } elseif (!empty($lockedApprovedIdx)) {
                // PO sudah Pending dengan locked (resubmit kedua kali): semua yang bukan locked = rejected
                $gpsItems = collect($allGpsItems)
                    ->filter(fn($item, $i) => !in_array($i, $lockedApprovedIdx))
                    ->all();
            } else {
                // PO ditolak penuh: semua items diajukan ulang
                $gpsItems = $allGpsItems;
            }

            // Enrich items dengan nama GPS dan lampiran existing
            $recordIds     = $sourceData['gps_record_ids'] ?? [];
            $enrichedItems = array_map(function ($item, $idx) use ($recordIds) {
                $gps = isset($item['gps_id']) ? \App\Models\Gps::find($item['gps_id']) : null;
                $recordId = $recordIds[$idx] ?? null;
                $lampiran = [];
                if ($recordId) {
                    $attachments = \App\Models\Attachment::where('relation_type', 'gps')
                        ->where('relation_id', $recordId)
                        ->get();
                    foreach ($attachments as $att) {
                        $lampiran[] = [
                            'id'        => $att->id,
                            'file_name' => $att->file_name,
                            'url'       => asset($att->file_path),
                        ];
                    }
                }
                return array_merge($item, [
                    'idx'               => $idx, // kirim original index agar partial resubmit bisa remap
                    'nama_gps'          => $gps ? $gps->nama_gps : '-',
                    'lampiran_existing' => $lampiran,
                ]);
            }, $gpsItems, array_keys($gpsItems));

            return response()->json([
                'success'        => true,
                'po_id'          => $po->id,
                'po_number'      => $po->po_id,
                'catatan'        => $po->catatan_approval,
                'kendaraan_id'   => $kendaraanId,
                'nopol'          => $kendaraan ? $kendaraan->nopol : '-',
                'merk'           => $kendaraan ? $kendaraan->merk : '-',
                'tanggal_bayar'  => $sourceData['tanggal_bayar'] ?? '',
                'tanggal_habis'  => $sourceData['tanggal_habis'] ?? '',
                'keterangan'     => $sourceData['keterangan'] ?? '',
                'vendor'         => $po->vendor ?? '',
                'gps_items'      => $enrichedItems,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Resubmit Modal — load data pajak/asuransi_kendaraan/kir ke JSON untuk modal inline
     */
    public function resubmitModal(Request $request, $id)
    {
        try {
            $po = PurchaseOrder::findOrFail($id);

            if (!$po->isRejected()) {
                return response()->json(['success' => false, 'message' => 'Hanya PO yang ditolak yang dapat diajukan ulang.'], 422);
            }

            if (!$po->can_edit) {
                return response()->json(['success' => false, 'message' => 'PO ini tidak dapat diedit.'], 422);
            }

            $allowed = ['pajak', 'pajak_perpanjang', 'asuransi_kendaraan', 'asuransi_kendaraan_perpanjang', 'kir', 'kir_perpanjang'];
            if (!in_array($po->source_type, $allowed)) {
                return response()->json(['success' => false, 'message' => 'Tipe PO tidak didukung oleh modal ini.'], 422);
            }

            $sourceData  = $po->source_data ?? [];
            $kendaraanId = $sourceData['kendaraan_id'] ?? null;
            $kendaraan   = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;

            $base = [
                'success'            => true,
                'po_id'              => $po->id,
                'po_number'          => $po->po_id,
                'source_type'        => $po->source_type,
                'catatan'            => $po->catatan_approval,
                'nopol'              => $kendaraan ? $kendaraan->nopol : '-',
                'merk'               => $kendaraan ? $kendaraan->merk  : '-',
                'kendaraan_id'       => $kendaraanId,
                'existing_record_id' => $sourceData['existing_record_id'] ?? $po->id, // Flag untuk detect edit mode
                'id'                 => $po->id, // Alternatif flag untuk detect edit mode
            ];

            if (in_array($po->source_type, ['pajak', 'pajak_perpanjang'])) {
                return response()->json(array_merge($base, [
                    'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? '',
                    'jatuh_tempo'   => $sourceData['jatuh_tempo']   ?? '',
                    'nominal'       => $sourceData['nominal']       ?? '',
                    'nama_pemilik'  => $sourceData['nama_pemilik']  ?? '',
                    'nama_bank'     => $sourceData['nama_bank']     ?? '',
                    'no_rekening'   => $sourceData['no_rekening']   ?? '',
                ]));
            }

            if (in_array($po->source_type, ['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'])) {
                return response()->json(array_merge($base, [
                    'tgl_mulai'     => $sourceData['tgl_mulai']     ?? '',
                    'tgl_berakhir'  => $sourceData['tgl_berakhir']  ?? '',
                    'durasi_bulan'  => $sourceData['durasi_bulan']  ?? '',
                    'biaya'         => $sourceData['biaya']         ?? '',
                    'nama_bank'     => $sourceData['nama_bank']     ?? '',
                    'no_rekening'   => $sourceData['no_rekening']   ?? '',
                    'nama_rekening' => $sourceData['nama_rekening'] ?? '',
                ]));
            }

            // kir / kir_perpanjang
            return response()->json(array_merge($base, [
                'no_uji'        => $sourceData['no_uji']        ?? '',
                'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? '',
                'masa_berlaku'  => $sourceData['masa_berlaku']  ?? '',
                'biaya'         => $sourceData['biaya']         ?? '',
                'nama_bank'     => $sourceData['nama_bank']     ?? '',
                'no_rekening'   => $sourceData['no_rekening']   ?? '',
            ]));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Resubmit Update — terima data dari modal, update source_data, kembalikan ke Pending
     */
    public function resubmitUpdate(Request $request, $id)
    {
        try {
            $po = PurchaseOrder::findOrFail($id);

            if (!$po->isRejected()) {
                return response()->json(['success' => false, 'message' => 'Hanya PO yang ditolak yang dapat diajukan ulang.'], 422);
            }

            if (!$po->can_edit) {
                return response()->json(['success' => false, 'message' => 'PO ini tidak dapat diedit.'], 422);
            }

            $allowed = ['pajak', 'pajak_perpanjang', 'asuransi_kendaraan', 'asuransi_kendaraan_perpanjang', 'kir', 'kir_perpanjang'];
            if (!in_array($po->source_type, $allowed)) {
                return response()->json(['success' => false, 'message' => 'Tipe PO tidak didukung.'], 422);
            }

            $sourceData = $po->source_data ?? [];

            if (in_array($po->source_type, ['pajak', 'pajak_perpanjang'])) {
                $request->validate([
                    'nominal' => 'required|numeric|min:0',
                ]);
                $sourceData = array_merge($sourceData, [
                    'nominal'      => $request->nominal,
                    'nama_pemilik' => $request->nama_pemilik ?? $sourceData['nama_pemilik'] ?? null,
                    'nama_bank'    => $request->nama_bank    ?? $sourceData['nama_bank']    ?? null,
                    'no_rekening'  => $request->no_rekening  ?? $sourceData['no_rekening']  ?? null,
                ]);
            } elseif (in_array($po->source_type, ['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'])) {
                $request->validate([
                    'tgl_mulai'    => 'required|date',
                    'tgl_berakhir' => 'required|date|after_or_equal:tgl_mulai',
                    'biaya'        => 'required|numeric|min:0',
                ]);
                $sourceData = array_merge($sourceData, [
                    'tgl_mulai'     => $request->tgl_mulai,
                    'tgl_berakhir'  => $request->tgl_berakhir,
                    'durasi_bulan'  => $request->durasi_bulan  ?? $sourceData['durasi_bulan']  ?? null,
                    'biaya'         => $request->biaya,
                    'nama_bank'     => $request->nama_bank     ?? $sourceData['nama_bank']     ?? null,
                    'no_rekening'   => $request->no_rekening   ?? $sourceData['no_rekening']   ?? null,
                    'nama_rekening' => $request->nama_rekening ?? $sourceData['nama_rekening'] ?? null,
                ]);
            } else {
                // kir / kir_perpanjang
                $request->validate([
                    'tanggal_bayar' => 'required|date',
                    'masa_berlaku'  => 'required|date',
                    'biaya'         => 'required|numeric|min:0',
                ]);
                $sourceData = array_merge($sourceData, [
                    'no_uji'        => $request->no_uji        ?? $sourceData['no_uji']        ?? null,
                    'tanggal_bayar' => $request->tanggal_bayar,
                    'masa_berlaku'  => $request->masa_berlaku,
                    'biaya'         => $request->biaya,
                    'nama_bank'     => $request->nama_bank     ?? $sourceData['nama_bank']     ?? null,
                    'no_rekening'   => $request->no_rekening   ?? $sourceData['no_rekening']   ?? null,
                ]);
            }

            // Hitung ulang total_harga dari source_data yang baru
            $totalHarga = match ($po->source_type) {
                'pajak', 'pajak_perpanjang'                                     => $sourceData['nominal'] ?? $po->total_harga,
                'asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'           => $sourceData['biaya']   ?? $po->total_harga,
                'kir', 'kir_perpanjang'                                         => $sourceData['biaya']   ?? $po->total_harga,
                default                                                         => $po->total_harga,
            };

            // Clear item_decisions when resubmitting — fresh approval cycle
            $sourceData['item_decisions'] = [];

            $po->update([
                'source_data'         => $sourceData,
                'total_harga'         => $totalHarga,
                'status'              => 'Pending',
                'catatan_approval'    => null,
                'disetujui_oleh'      => null,
                'tanggal_persetujuan' => null,
                'can_edit'            => false,
                'terakhir_diajukan'   => now(),
            ]);

            // ── CASCADE UPDATE: Update tabel induk sesuai source_type ─────────────
            $existingId = $sourceData['existing_record_id'] ?? null;
            
            if ($existingId) {
                if (in_array($po->source_type, ['pajak', 'pajak_perpanjang'])) {
                    \App\Models\PajakKendaraan::where('id', $existingId)->update([
                        'persetujuan'   => 'Pending',
                        'nominal'       => $sourceData['nominal'] ?? 0,
                        'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? null,
                        'jatuh_tempo'   => $sourceData['jatuh_tempo']   ?? null,
                        'nama_bank'     => $sourceData['nama_bank']     ?? null,
                        'no_rekening'   => $sourceData['no_rekening']   ?? null,
                        'nama_pemilik'  => $sourceData['nama_pemilik']  ?? null,
                    ]);
                } elseif (in_array($po->source_type, ['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'])) {
                    \App\Models\AsuransiKendaraan::where('id', $existingId)->update([
                        'persetujuan'   => 'Pending',
                        'biaya'         => $sourceData['biaya'] ?? 0,
                        'tgl_mulai'     => $sourceData['tgl_mulai']    ?? null,
                        'tgl_berakhir'  => $sourceData['tgl_berakhir'] ?? null,
                        'durasi_bulan'  => $sourceData['durasi_bulan'] ?? null,
                        'nama_bank'     => $sourceData['nama_bank']     ?? null,
                        'no_rekening'   => $sourceData['no_rekening']   ?? null,
                        'nama_rekening' => $sourceData['nama_rekening'] ?? null,
                    ]);
                } elseif (in_array($po->source_type, ['kir', 'kir_perpanjang'])) {
                    \App\Models\Kir::where('id', $existingId)->update([
                        'persetujuan'   => 'Pending',
                        'biaya'         => $sourceData['biaya'] ?? 0,
                        'tanggal_bayar' => $sourceData['tanggal_bayar'] ?? null,
                        'masa_berlaku'  => $sourceData['masa_berlaku']  ?? null,
                        'no_uji'        => $sourceData['no_uji']        ?? null,
                        'nama_bank'     => $sourceData['nama_bank']     ?? null,
                        'no_rekening'   => $sourceData['no_rekening']   ?? null,
                        'nama_rekening' => $sourceData['nama_rekening'] ?? null,
                    ]);
                }
                
                \Log::info("PO resubmit: Tabel induk updated", [
                    'po_id'        => $po->id,
                    'source_type'  => $po->source_type,
                    'existing_id'  => $existingId,
                    'new_amount'   => $totalHarga,
                ]);
            } else {
                \Log::warning("PO resubmit: existing_record_id not found", [
                    'po_id'       => $po->id,
                    'source_type' => $po->source_type,
                ]);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'PO ' . $po->po_id . ' berhasil diajukan ulang.',
                'redirect' => route('purchase-order.index', ['status' => 'Pending']),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => implode(' ', array_merge(...array_values($e->errors())))], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Resubmit simple — untuk STNK dan non-GPS yang tidak punya form edit tersendiri
     * Reset status PO kembali ke Pending
     */
    public function resubmitSimple(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if (!$po->isRejected()) {
            return response()->json(['success' => false, 'message' => 'Hanya PO yang ditolak yang dapat diajukan ulang.'], 422);
        }

        // service_asuransi dan service_incident selalu bisa resubmit (can_edit di-set true saat ditolak)
        if (!$po->can_edit && !in_array($po->source_type, ['service_asuransi', 'service_incident'])) {
            return response()->json(['success' => false, 'message' => 'PO ini tidak dapat diedit.'], 422);
        }

        try {
            // Clear item_decisions dari source_data agar badge status lama tidak muncul.
            // Juga bersihkan field partial approval agar PO full-reject tidak membawa sisa
            // state dari partial sebelumnya (locked_approved_idx, nominal_approved_locked, dll).
            $sourceData = $po->source_data ?? [];
            $sourceData['item_decisions']          = [];
            $sourceData['locked_approved_idx']     = [];
            $sourceData['nominal_approved_locked'] = 0;
            $sourceData['nominal_rejected']        = 0;

            $po->update([
                'source_data'         => $sourceData,
                'status'              => 'Pending',
                'catatan_approval'    => null,
                'disetujui_oleh'      => null,
                'tanggal_persetujuan' => null,
                'can_edit'            => false,
                'terakhir_diajukan'   => now(),
            ]);

            // Untuk service_asuransi/service_incident: reset record terkait ke Pending
            if (in_array($po->source_type, ['service_asuransi', 'service_incident'])) {
                $serviceAsuransiId = $po->source_data['service_asuransi_id'] ?? null;
                if ($serviceAsuransiId) {
                    // Dengan alur baru: record sudah ada dengan 'Ditolak Pembayaran', reset ke 'Diajukan ke Pembayaran'
                    \App\Models\ServiceAsuransi::where('id', $serviceAsuransiId)
                        ->whereIn('persetujuan', ['Ditolak', 'Ditolak Pembayaran'])
                        ->update(['persetujuan' => 'Diajukan ke Pembayaran', 'purchase_order_id' => $po->id]);
                }

                // service_incident: hapus parts lama dan reset header
                if ($po->source_type === 'service_incident') {
                    $siId = $po->source_data['service_incident_id'] ?? null;
                    if ($siId) {
                        // Hapus hanya parts yang terkait PO yang ditolak ini (via purchase_order_id).
                        // Parts dari PO sebelumnya yang sudah approved TIDAK dihapus.
                        $deletedCount = \App\Models\ServiceIncidentPart::where('service_incident_id', $siId)
                            ->where('purchase_order_id', $po->id)
                            ->delete();

                        // Fallback untuk data lama tanpa purchase_order_id
                        if ($deletedCount === 0) {
                            \App\Models\ServiceIncidentPart::where('service_incident_id', $siId)
                                ->where('persetujuan', 'Diajukan ke Pembayaran')
                                ->whereNull('purchase_order_id')
                                ->delete();
                        }

                        // Reset incident header ke Diajukan ke Pembayaran
                        \App\Models\ServiceIncident::where('id', $siId)->update([
                            'persetujuan'       => 'Diajukan ke Pembayaran',
                            'purchase_order_id' => $po->id,
                        ]);
                    }
                }
            }

            return response()->json([
                'success'  => true,
                'message'  => 'PO ' . $po->po_id . ' berhasil diajukan ulang.',
                'redirect' => route('purchase-order.index', ['status' => 'Pending']),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Resubmit Service Asuransi PO — update data kejadian dari modal ajukan ulang.
     * Hanya menangani source_type = 'service_asuransi'. Service Incident ditangani
     * oleh resubmitServiceIncident() tersendiri.
     */
    public function resubmitServiceAsuransi(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->source_type !== 'service_asuransi') {
            return response()->json(['success' => false, 'message' => 'PO bukan tipe service_asuransi.'], 422);
        }

        if (!$po->isRejected()) {
            return response()->json(['success' => false, 'message' => 'Hanya PO yang ditolak yang dapat diajukan ulang.'], 422);
        }

        try {
            $sourceData  = $po->source_data ?? [];
            $kejadians   = $request->input('kejadians', []);
            $biayaTotal  = collect($kejadians)->sum(fn($k) => (int)($k['biaya'] ?? 0));

            // Simpan lampiran baru per kejadian ke temp storage
            $tempFiles = $sourceData['temp_files'] ?? [];
            foreach ($kejadians as $idx => $kej) {
                $fileKey = "kejadians.{$idx}.lampiran";
                if ($request->hasFile($fileKey)) {
                    $files = $request->file($fileKey);
                    if (!is_array($files)) $files = [$files];
                    foreach ($files as $li => $file) {
                        if (!$file->isValid()) continue;
                        $storedName = time() . "_{$idx}_{$li}_" . $file->getClientOriginalName();
                        $tempDir    = "purchase_order/temp/{$po->id}/kejadians/{$idx}";
                        $path       = $file->storeAs($tempDir, $storedName, 'public');
                        $tempFiles['kejadians'][$idx][] = [
                            'path'          => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'size'          => $file->getSize(),
                            'extension'     => $file->getClientOriginalExtension(),
                        ];
                    }
                }
            }

            // Bangun ulang kejadians dengan lampiran lama dipertahankan + field baru
            $newKejadians = array_map(function ($kej, $idx) use ($tempFiles) {
                $lampiranLama = array_values(array_filter(
                    array_map(function ($lf) {
                        $path = $lf['path'] ?? '';
                        if (!$path) return null;
                        return [
                            'path'          => $path,
                            'original_name' => $lf['original_name'] ?? basename($path),
                            'extension'     => $lf['extension'] ?? pathinfo($path, PATHINFO_EXTENSION),
                            'size'          => (int)($lf['size'] ?? 0),
                        ];
                    }, $kej['lampiran_lama'] ?? [])
                ));
                $newLampiran = $tempFiles['kejadians'][$idx] ?? [];
                return [
                    'nama_kejadian'   => $kej['nama_kejadian'] ?? '',
                    'biaya'           => (int)($kej['biaya'] ?? 0),
                    // PENTING: simpan dengan key 'tanggal' agar konsisten dengan tabel DB (service_asuransi_kejadians.tanggal)
                    // Fallback dari tanggal_service untuk backward compatibility dengan data lama
                    'tanggal'         => $kej['tanggal_service'] ?? $kej['tanggal'] ?? null,
                    'periode_mulai'   => $kej['periode_mulai'] ?? null,
                    'periode_selesai' => $kej['periode_selesai'] ?? null,
                    'lampiran'        => array_merge($lampiranLama, $newLampiran),
                ];
            }, $kejadians, array_keys($kejadians));

            // ── source_data untuk full-reject (service_asuransi saja) ────────────────
            $newSourceData = array_merge($sourceData, [
                'kejadians'       => $newKejadians,
                'item_decisions'  => [],
                'locked_approved_idx'     => [],
                'nominal_approved_locked' => 0,
                'nominal_rejected'        => 0,
                'tanggal_service' => $request->input('tanggal_service', $sourceData['tanggal_service'] ?? null),
                'periode_mulai'   => $request->input('periode_mulai',   $sourceData['periode_mulai']   ?? null),
                'periode_selesai' => $request->input('periode_selesai', $sourceData['periode_selesai'] ?? null),
                'kilometer'       => $request->input('kilometer',       $sourceData['kilometer']       ?? null),
                'nama_asuransi'   => $request->input('nama_asuransi',   $sourceData['nama_asuransi']   ?? null),
                'temp_files'      => $tempFiles,
            ]);

            // ── PARTIAL: PO status Disetujui dengan item rejected, atau Pending + locked ──
            // ALUR BARU: Buat PO document baru untuk item yang rejected, PO lama tetap Disetujui
            $isPartial = $po->status === 'Disetujui'
                || ($po->status === 'Pending' && !empty($sourceData['locked_approved_idx'] ?? []));

            if ($isPartial) {
                $allKejadians = $sourceData['kejadians'] ?? [];
                $oldDecisions = $sourceData['item_decisions'] ?? [];

                if (!empty($sourceData['locked_approved_idx'] ?? []) && empty($oldDecisions)) {
                    $lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx']);
                    $rejectedIdx       = collect(array_keys($allKejadians))
                        ->map('intval')
                        ->filter(fn($i) => !in_array($i, $lockedApprovedIdx))
                        ->values()->all();
                } else {
                    $lockedApprovedIdx = collect($oldDecisions)
                        ->filter(fn($d) => ($d['action'] ?? '') === 'approved')
                        ->pluck('idx')->map('intval')->values()->all();
                    $rejectedIdx       = collect($oldDecisions)
                        ->filter(fn($d) => ($d['action'] ?? '') === 'rejected')
                        ->pluck('idx')->map('intval')->values()->all();
                    $existingLocked    = array_map('intval', $sourceData['locked_approved_idx'] ?? []);
                    $lockedApprovedIdx = array_values(array_unique(array_merge($lockedApprovedIdx, $existingLocked)));
                }

                // Build new kejadians array with updated data from form for rejected items
                $newKejadiansForNewPO = [];
                foreach ($newKejadians as $newIdx => $newKej) {
                    $origIdx = $rejectedIdx[$newIdx] ?? null;
                    if ($origIdx !== null && isset($allKejadians[$origIdx])) {
                        // PENTING: Gunakan data BARU dari form, jangan fallback ke lama
                        // Fallback hanya jika field baru NULL (tidak diisi sama sekali)
                        $oldKej = $allKejadians[$origIdx];
                        $newKejadiansForNewPO[] = array_merge($oldKej, [
                            'nama_kejadian'   => $newKej['nama_kejadian'],
                            'biaya'           => $newKej['biaya'],
                            // Prioritaskan data baru. Fallback ke tanggal_service (data lama) hanya jika null
                            'tanggal'         => $newKej['tanggal'] ?? ($oldKej['tanggal'] ?? ($oldKej['tanggal_service'] ?? null)),
                            'periode_mulai'   => $newKej['periode_mulai'] ?? ($oldKej['periode_mulai'] ?? null),
                            'periode_selesai' => $newKej['periode_selesai'] ?? ($oldKej['periode_selesai'] ?? null),
                            'lampiran'        => $newKej['lampiran'] ?? ($oldKej['lampiran'] ?? []),
                        ]);
                    }
                }

                // ── Update PO lama: hapus item rejected, keep only approved items ──
                $approvedKejadians = collect($allKejadians)
                    ->filter(fn($k, $i) => in_array((int)$i, $lockedApprovedIdx))
                    ->values()->all();
                $approvedItemPoNumbers = [];
                foreach ($lockedApprovedIdx as $i => $origIdx) {
                    $approvedItemPoNumbers[$i] = $sourceData['item_po_numbers'][$origIdx] ?? $po->po_id;
                }
                $nominalApprovedLocked = collect($approvedKejadians)->sum(fn($k) => (int)($k['biaya'] ?? 0));

                $oldPoSourceData = array_merge($sourceData, [
                    'kejadians'               => $approvedKejadians,
                    'item_decisions'          => [],
                    'locked_approved_idx'     => [],
                    'nominal_approved_locked' => $nominalApprovedLocked,
                    'nominal_rejected'        => 0,
                    'item_po_numbers'         => $approvedItemPoNumbers,
                    'temp_files'              => $sourceData['temp_files'] ?? [],
                ]);

                $po->update([
                    'source_data'         => $oldPoSourceData,
                    'total_harga'         => $nominalApprovedLocked,
                    'total_barang'        => count($approvedKejadians),
                    'can_edit'            => false,
                ]);

                // ── Create new PO document for rejected items ──
                $newPoSourceData = array_merge($sourceData, [
                    'kejadians'       => $newKejadiansForNewPO,
                    'item_decisions'  => [],
                    'locked_approved_idx'     => [],
                    'nominal_approved_locked' => 0,
                    'nominal_rejected'        => 0,
                    'tanggal_service' => $request->input('tanggal_service', $sourceData['tanggal_service'] ?? null),
                    'periode_mulai'   => $request->input('periode_mulai',   $sourceData['periode_mulai']   ?? null),
                    'periode_selesai' => $request->input('periode_selesai', $sourceData['periode_selesai'] ?? null),
                    'kilometer'       => $request->input('kilometer',       $sourceData['kilometer']       ?? null),
                    'nama_asuransi'   => $request->input('nama_asuransi',   $sourceData['nama_asuransi']   ?? null),
                    'temp_files'      => $tempFiles,
                    // Preserve service_asuransi_id agar updateLinkedRecord menambah kejadian ke record yang sama
                    'service_asuransi_id' => $sourceData['service_asuransi_id'] ?? null,
                ]);
                unset($newPoSourceData['biaya']);

                $newPo = $this->approvalService->createResubmitPO($po, $newPoSourceData, 'service_asuransi');

                return response()->json([
                    'success'  => true,
                    'message'  => 'PO baru ' . $newPo->po_id . ' dibuat untuk item yang diajukan ulang.',
                    'redirect' => route('purchase-order.index', ['status' => 'Pending']),
                ]);
            }

            // ── FULL REJECT: reset seluruh PO ke Pending ─────────────────────────────
            $po->update([
                'source_data'         => $newSourceData,
                'total_harga'         => $biayaTotal,
                'total_barang'        => count($newKejadians),
                'status'              => 'Pending',
                'catatan_approval'    => null,
                'disetujui_oleh'      => null,
                'tanggal_persetujuan' => null,
                'can_edit'            => false,
                'terakhir_diajukan'   => now(),
            ]);

            // Update record ServiceAsuransi ke Diajukan ke Pembayaran
            $serviceAsuransiId = $sourceData['service_asuransi_id'] ?? null;
            if ($serviceAsuransiId) {
                \App\Models\ServiceAsuransi::where('id', $serviceAsuransiId)->update([
                    'persetujuan'       => 'Diajukan ke Pembayaran',
                    'biaya'             => $biayaTotal,
                    'purchase_order_id' => $po->id,
                ]);
                \App\Models\ServiceAsuransiKejadian::where('service_asuransi_id', $serviceAsuransiId)->delete();
                foreach ($newKejadians as $kej) {
                    \App\Models\ServiceAsuransiKejadian::create([
                        'service_asuransi_id' => $serviceAsuransiId,
                        'nama_kejadian'       => $kej['nama_kejadian'] ?? '',
                        'biaya'               => (int)($kej['biaya'] ?? 0),
                        'lampiran'            => !empty($kej['lampiran']) ? $kej['lampiran'] : null,
                    ]);
                }
            }

            return response()->json([
                'success'  => true,
                'message'  => 'PO ' . $po->po_id . ' berhasil diajukan ulang.',
                'redirect' => route('purchase-order.index', ['status' => 'Pending']),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error resubmitServiceAsuransi PO #' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Resubmit Service Incident PO — terima data parts yang rejected dari modal ajukan ulang.
     * Logika terpisah dari resubmitServiceAsuransi agar tidak saling mempengaruhi.
     *
     * Dua skenario:
     *  - PARTIAL: PO status Disetujui (ada item approved + rejected) atau Pending dengan locked_approved_idx
     *             → hanya update parts yang rejected, pertahankan locked items
     *  - FULL REJECT: PO status Ditolak (semua parts ditolak)
     *             → reset seluruh PO ke Pending, hapus ServiceIncidentPart lama, reset header
     */
    public function resubmitServiceIncident(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->source_type !== 'service_incident') {
            return response()->json(['success' => false, 'message' => 'PO bukan tipe service_incident.'], 422);
        }

        if (!$po->isRejected()) {
            return response()->json(['success' => false, 'message' => 'Hanya PO yang ditolak yang dapat diajukan ulang.'], 422);
        }

        try {
            $sourceData = $po->source_data ?? [];
            $allParts   = $sourceData['parts'] ?? [];
            $formParts  = $request->input('kejadians', []); // form mengirim sebagai 'kejadians' (modal shared)
            $tempFiles  = $sourceData['temp_files'] ?? [];

            // ── Upload lampiran baru ke temp storage ──────────────────────────────────
            foreach ($formParts as $formIdx => $part) {
                $fileKey = "kejadians.{$formIdx}.lampiran";
                if ($request->hasFile($fileKey)) {
                    $files = $request->file($fileKey);
                    if (!is_array($files)) $files = [$files];
                    foreach ($files as $li => $file) {
                        if (!$file->isValid()) continue;
                        $storedName = time() . "_{$formIdx}_{$li}_" . $file->getClientOriginalName();
                        $tempDir    = "purchase_order/temp/{$po->id}/parts/{$formIdx}";
                        $path       = $file->storeAs($tempDir, $storedName, 'public');
                        $tempFiles['parts'][$formIdx]['bukti'][] = [
                            'path'          => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'size'          => $file->getSize(),
                            'extension'     => $file->getClientOriginalExtension(),
                        ];
                    }
                }
            }

            // ── Bangun newParts dari form (pertahankan lampiran lama) ─────────────────
            $newParts = array_map(function ($part, $formIdx) use ($tempFiles) {
                $lampiranLama = array_values(array_filter(
                    array_map(function ($lf) {
                        $path = $lf['path'] ?? '';
                        if (!$path) return null;
                        return [
                            'path'          => $path,
                            'original_name' => $lf['original_name'] ?? basename($path),
                            'extension'     => $lf['extension'] ?? pathinfo($path, PATHINFO_EXTENSION),
                            'size'          => (int)($lf['size'] ?? 0),
                        ];
                    }, $part['lampiran_lama'] ?? [])
                ));
                $newLampiran = $tempFiles['parts'][$formIdx]['bukti'] ?? [];
                return [
                    'nama_kejadian' => $part['nama_kejadian'] ?? '',  // nama dari form (dipetakan ke nama_part saat merge)
                    'biaya'         => (int)($part['biaya'] ?? 0),
                    'lampiran'      => array_merge($lampiranLama, $newLampiran),
                ];
            }, $formParts, array_keys($formParts));

            // ── Tentukan apakah PARTIAL atau FULL REJECT ─────────────────────────────
            $oldDecisions = $sourceData['item_decisions'] ?? [];
            $isPartial    = $po->status === 'Disetujui'
                || ($po->status === 'Pending' && !empty($sourceData['locked_approved_idx'] ?? []));

            if ($isPartial) {
                // ── PARTIAL RESUBMIT — ALUR BARU: Buat PO document baru ──────────────
                // Pisahkan index locked (approved) vs rejected
                if (!empty($sourceData['locked_approved_idx'] ?? []) && empty($oldDecisions)) {
                    $lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx']);
                    $rejectedIdx       = collect(array_keys($allParts))
                        ->map('intval')
                        ->filter(fn($i) => !in_array($i, $lockedApprovedIdx))
                        ->values()->all();
                } else {
                    $lockedApprovedIdx = collect($oldDecisions)
                        ->filter(fn($d) => ($d['action'] ?? '') === 'approved')
                        ->pluck('idx')->map('intval')->values()->all();
                    $rejectedIdx       = collect($oldDecisions)
                        ->filter(fn($d) => ($d['action'] ?? '') === 'rejected')
                        ->pluck('idx')->map('intval')->values()->all();
                    $existingLocked    = array_map('intval', $sourceData['locked_approved_idx'] ?? []);
                    $lockedApprovedIdx = array_values(array_unique(array_merge($lockedApprovedIdx, $existingLocked)));
                }

                // Build new parts array for new PO (only rejected items, updated with form data)
                $newPartsForNewPO = [];
                foreach ($newParts as $newIdx => $newPart) {
                    $origIdx = $rejectedIdx[$newIdx] ?? null;
                    if ($origIdx !== null && isset($allParts[$origIdx])) {
                        $newPartsForNewPO[] = array_merge($allParts[$origIdx], [
                            'nama_part' => $newPart['nama_kejadian'] ?? ($allParts[$origIdx]['nama_part'] ?? '-'),
                            'biaya'     => $newPart['biaya'],
                            'lampiran'  => $newPart['lampiran'] ?? ($allParts[$origIdx]['lampiran'] ?? []),
                        ]);
                    }
                }

                // ── Update PO lama: hapus item rejected, keep only approved items ──
                $approvedParts = collect($allParts)
                    ->filter(fn($p, $i) => in_array((int)$i, $lockedApprovedIdx))
                    ->values()->all();
                $approvedItemPoNumbers = [];
                foreach ($lockedApprovedIdx as $i => $origIdx) {
                    $approvedItemPoNumbers[$i] = $sourceData['item_po_numbers'][$origIdx] ?? $po->po_id;
                }
                $nominalApprovedLocked = collect($approvedParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));

                $oldPoSourceData = array_merge($sourceData, [
                    'parts'                   => $approvedParts,
                    'item_decisions'          => [],
                    'locked_approved_idx'     => [],
                    'nominal_approved_locked' => $nominalApprovedLocked,
                    'nominal_rejected'        => 0,
                    'item_po_numbers'         => $approvedItemPoNumbers,
                    'temp_files'              => $sourceData['temp_files'] ?? [],
                ]);

                $po->update([
                    'source_data'         => $oldPoSourceData,
                    'total_harga'         => $nominalApprovedLocked,
                    'total_barang'        => count($approvedParts),
                    'can_edit'            => false,
                ]);

                // ── Create new PO document for rejected items ──
                $newPoSourceData = array_merge($sourceData, [
                    'parts'           => $newPartsForNewPO,
                    'item_decisions'  => [],
                    'locked_approved_idx'     => [],
                    'nominal_approved_locked' => 0,
                    'nominal_rejected'        => 0,
                    'tanggal_service' => $request->input('tanggal_service', $sourceData['tanggal_service'] ?? null),
                    'kilometer'       => $request->input('kilometer',       $sourceData['kilometer']       ?? null),
                    'temp_files'      => $tempFiles,
                    // Preserve service_incident_id agar updateLinkedRecord menambah parts ke record yang sama
                    'service_incident_id' => $sourceData['service_incident_id'] ?? null,
                ]);
                unset($newPoSourceData['total_biaya_override']);

                $newPo = $this->approvalService->createResubmitPO($po, $newPoSourceData, 'service_incident');

                // Reset ServiceIncidentPart yang rejected milik PO lama
                $serviceIncidentId = $sourceData['service_incident_id'] ?? null;
                if ($serviceIncidentId) {
                    foreach ($rejectedIdx as $origIdx) {
                        \App\Models\ServiceIncidentPart::where('service_incident_id', $serviceIncidentId)
                            ->where('purchase_order_id', $po->id)
                            ->where('nama_part', $allParts[$origIdx]['nama_part'] ?? '')
                            ->delete();
                    }

                    // Reset header incident ke Diajukan ke Pembayaran (untuk PO baru)
                    \App\Models\ServiceIncident::where('id', $serviceIncidentId)->update([
                        'persetujuan'       => 'Diajukan ke Pembayaran',
                        'purchase_order_id' => $newPo->id,
                    ]);
                }

                return response()->json([
                    'success'  => true,
                    'message'  => 'PO baru ' . $newPo->po_id . ' dibuat untuk item yang diajukan ulang.',
                    'redirect' => route('purchase-order.index', ['status' => 'Pending']),
                ]);
            }

            // ── FULL REJECT — reset seluruh PO ke Pending ───────────────────────────
            // Remap newParts ke format parts[] dengan mempertahankan field lain dari originalParts.
            // Gunakan $rejectedIdx dari $oldDecisions jika ada, atau fallback ke semua index.
            $allRejectedIdx = collect($oldDecisions)->filter(fn($d) => ($d['action'] ?? '') === 'rejected')
                ->pluck('idx')->map('intval')->values()->all();
            if (empty($allRejectedIdx)) {
                // PO full rejected (status Ditolak, tidak ada item_decisions) → semua parts di-resubmit
                $allRejectedIdx = array_keys($allParts);
            }

            $remappedParts = [];
            foreach ($newParts as $newIdx => $newPart) {
                $origIdx = $allRejectedIdx[$newIdx] ?? $newIdx;
                $orig    = $allParts[$origIdx] ?? [];
                $remappedParts[$origIdx] = array_merge($orig, [
                    'nama_part' => $newPart['nama_kejadian'] ?? ($orig['nama_part'] ?? '-'),
                    'biaya'     => $newPart['biaya'],
                    'lampiran'  => $newPart['lampiran'] ?? ($orig['lampiran'] ?? []),
                ]);
            }
            // Tambahkan parts yang tidak dikirim ulang (seharusnya tidak ada di full reject, tapi safety)
            foreach ($allParts as $origIdx => $part) {
                if (!isset($remappedParts[$origIdx])) {
                    $remappedParts[$origIdx] = $part;
                }
            }
            ksort($remappedParts);

            $biayaTotal    = collect($remappedParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));
            $newSourceData = array_merge($sourceData, [
                'parts'           => array_values($remappedParts),
                'item_decisions'  => [],
                'locked_approved_idx'     => [],
                'nominal_approved_locked' => 0,
                'nominal_rejected'        => 0,
                'tanggal_service' => $request->input('tanggal_service', $sourceData['tanggal_service'] ?? null),
                'kilometer'       => $request->input('kilometer',       $sourceData['kilometer']       ?? null),
                'temp_files'      => $tempFiles,
            ]);

            $po->update([
                'source_data'         => $newSourceData,
                'total_harga'         => $biayaTotal,
                'total_barang'        => count($remappedParts),
                'status'              => 'Pending',
                'catatan_approval'    => null,
                'disetujui_oleh'      => null,
                'tanggal_persetujuan' => null,
                'can_edit'            => false,
                'terakhir_diajukan'   => now(),
            ]);

            // Reset ServiceIncident + hapus ServiceIncidentPart lama milik PO ini
            $serviceIncidentId = $sourceData['service_incident_id'] ?? null;
            if ($serviceIncidentId) {
                $deletedCount = \App\Models\ServiceIncidentPart::where('service_incident_id', $serviceIncidentId)
                    ->where('purchase_order_id', $po->id)
                    ->delete();

                if ($deletedCount === 0) {
                    // Fallback untuk data lama tanpa purchase_order_id
                    \App\Models\ServiceIncidentPart::where('service_incident_id', $serviceIncidentId)
                        ->where('persetujuan', 'Diajukan ke Pembayaran')
                        ->whereNull('purchase_order_id')
                        ->delete();
                }

                // JANGAN reset pembayaran_id — parts dari PO sebelumnya yang sudah approved tetap valid
                \App\Models\ServiceIncident::where('id', $serviceIncidentId)->update([
                    'persetujuan'       => 'Diajukan ke Pembayaran',
                    'purchase_order_id' => $po->id,
                ]);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'PO ' . $po->po_id . ' berhasil diajukan ulang.',
                'redirect' => route('purchase-order.index', ['status' => 'Pending']),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error resubmitServiceIncident PO #' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Resubmit GPS Partial — terima data GPS items yang rejected dari form GPS,
     * pertahankan item yang sudah locked/approved, reset hanya item yang diajukan ulang ke Pending.
     * Dipanggil oleh GpsKendaraanController saat PO GPS partial.
     */
    public function resubmitGpsPartial(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if (!$po->isRejected()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Hanya PO yang ditolak yang dapat diajukan ulang.'], 422);
            }
            return back()->with('error', 'Hanya PO yang ditolak yang dapat diajukan ulang.');
        }

        try {
            $sourceData    = $po->source_data ?? [];
            $allGpsItems   = $sourceData['gps_items'] ?? [];
            $oldDecisions  = $sourceData['item_decisions'] ?? [];
            $tempFiles     = $sourceData['temp_files'] ?? [];

            // Pisahkan index locked (approved) vs yang ditolak (perlu resubmit)
            if (!empty($sourceData['locked_approved_idx'] ?? []) && empty($oldDecisions)) {
                // PO sudah Pending dengan locked (resubmit kedua kali)
                $lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx']);
                $rejectedIdx = collect(array_keys($allGpsItems))
                    ->map('intval')
                    ->filter(fn($i) => !in_array($i, $lockedApprovedIdx))
                    ->values()->all();
            } else {
                $lockedApprovedIdx = collect($oldDecisions)
                    ->filter(fn($d) => ($d['action'] ?? '') === 'approved')
                    ->pluck('idx')->map('intval')->values()->all();
                $rejectedIdx = collect($oldDecisions)
                    ->filter(fn($d) => ($d['action'] ?? '') === 'rejected')
                    ->pluck('idx')->map('intval')->values()->all();
                // FIX siklus ke-3: merge dengan locked_approved_idx yang sudah ada di source_data.
                // item_decisions siklus ke-2 hanya mencatat keputusan terbaru (rejected saja),
                // sehingga lockedApprovedIdx dari item_decisions bisa kosong meski ada item locked.
                $existingLocked    = array_map('intval', $sourceData['locked_approved_idx'] ?? []);
                $lockedApprovedIdx = array_values(array_unique(array_merge($lockedApprovedIdx, $existingLocked)));
            }

            // Ambil data GPS items baru dari request (hanya rejected items yang dikirim)
            $newGpsItems = $request->input('gps_items', []);

            // Upload lampiran baru ke temp storage
            foreach ($newGpsItems as $formIdx => $item) {
                $fileKey = "gps_items.{$formIdx}.lampiran";
                if ($request->hasFile($fileKey)) {
                    $files = $request->file($fileKey);
                    if (!is_array($files)) $files = [$files];
                    foreach ($files as $li => $file) {
                        if (!$file->isValid()) continue;
                        $origIdx = $item['original_idx'] ?? $rejectedIdx[$formIdx] ?? $formIdx;
                        $storedName = time() . "_{$origIdx}_{$li}_" . $file->getClientOriginalName();
                        $tempDir    = "purchase_order/temp/{$po->id}/gps/{$origIdx}";
                        $path       = $file->storeAs($tempDir, $storedName, 'public');
                        $tempFiles['gps'][$origIdx][] = [
                            'path'          => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'size'          => $file->getSize(),
                            'extension'     => $file->getClientOriginalExtension(),
                        ];
                    }
                }
            }

            // Update GPS items yang rejected dengan data baru dari form
            $newGpsItemsForNewPO = [];
            foreach ($newGpsItems as $formIdx => $newItem) {
                $origIdx = isset($newItem['original_idx'])
                    ? (int)$newItem['original_idx']
                    : ($rejectedIdx[$formIdx] ?? null);

                if ($origIdx !== null && isset($allGpsItems[$origIdx])) {
                    $newGpsItemsForNewPO[] = array_merge($allGpsItems[$origIdx], [
                        'biaya_sewa'   => (int)($newItem['biaya_sewa'] ?? $allGpsItems[$origIdx]['biaya_sewa'] ?? 0),
                        'nama_bank'    => $newItem['nama_bank']    ?? $allGpsItems[$origIdx]['nama_bank']    ?? null,
                        'no_rekening'  => $newItem['no_rekening']  ?? $allGpsItems[$origIdx]['no_rekening']  ?? null,
                        'nama_pemilik' => $newItem['nama_pemilik'] ?? $allGpsItems[$origIdx]['nama_pemilik'] ?? null,
                    ]);
                }
            }

            // ── Update PO lama: hapus item rejected, keep only approved items ──
            $approvedGpsItems = collect($allGpsItems)
                ->filter(fn($g, $i) => in_array((int)$i, $lockedApprovedIdx))
                ->values()->all();
            $approvedItemPoNumbers = [];
            foreach ($lockedApprovedIdx as $i => $origIdx) {
                $approvedItemPoNumbers[$i] = $sourceData['item_po_numbers'][$origIdx] ?? $po->po_id;
            }
            $nominalApprovedLocked = collect($approvedGpsItems)->sum(fn($g) => (int)($g['biaya_sewa'] ?? 0));

            $oldPoSourceData = array_merge($sourceData, [
                'gps_items'               => $approvedGpsItems,
                'item_decisions'          => [],
                'locked_approved_idx'     => [],
                'nominal_approved_locked' => $nominalApprovedLocked,
                'nominal_rejected'        => 0,
                'item_po_numbers'         => $approvedItemPoNumbers,
                'temp_files'              => $sourceData['temp_files'] ?? [],
            ]);

            $po->update([
                'source_data'         => $oldPoSourceData,
                'total_harga'         => $nominalApprovedLocked,
                'total_barang'        => count($approvedGpsItems),
                'can_edit'            => false,
            ]);

            // ── Create new PO document for rejected items ──
            $newPoSourceData = array_merge($sourceData, [
                'gps_items'       => $newGpsItemsForNewPO,
                'item_decisions'  => [],
                'locked_approved_idx'     => [],
                'nominal_approved_locked' => 0,
                'nominal_rejected'        => 0,
                'temp_files'              => $tempFiles,
                'tanggal_bayar'           => $request->input('tanggal_bayar', $sourceData['tanggal_bayar'] ?? null),
                'tanggal_habis'           => $request->input('tanggal_habis', $sourceData['tanggal_habis'] ?? null),
                'keterangan'              => $request->input('keterangan',    $sourceData['keterangan']    ?? null),
            ]);

            $newPo = $this->approvalService->createResubmitPO($po, $newPoSourceData, $po->source_type);

            if ($request->expectsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'PO baru ' . $newPo->po_id . ' dibuat untuk item yang diajukan ulang.',
                    'redirect' => route('purchase-order.index', ['status' => 'Pending']),
                ]);
            }

            return redirect('/admin/purchase-order?status=Pending')
                ->with('success', 'PO baru ' . $newPo->po_id . ' dibuat untuk item yang diajukan ulang.');

        } catch (\Exception $e) {
            \Log::error('Error resubmitGpsPartial PO #' . $id . ': ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
