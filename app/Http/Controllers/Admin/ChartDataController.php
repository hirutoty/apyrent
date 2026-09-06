<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ChartDataService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Chart Data Controller
 * Handle AJAX requests for chart data from all pages
 */
class ChartDataController extends Controller
{
    protected $chartDataService;

    public function __construct(ChartDataService $chartDataService)
    {
        $this->chartDataService = $chartDataService;
    }

    /**
     * Get chart data for specific page
     * 
     * @param Request $request
     * @param string $page Page identifier (keuangan, payments, invoice, etc)
     * @return JsonResponse
     */
    public function getData(Request $request, string $page): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'filter_type'   => 'nullable|in:today,week,month,year,specific_year,custom',
            'start_date'    => 'nullable|string',
            'end_date'      => 'nullable|string',
            'specific_year' => 'nullable|integer|min:2000|max:2099',
            'kendaraan_id'  => 'nullable|integer|exists:kendaraan,id',
            'category_id'   => 'nullable|integer',
            'departemen'    => 'nullable|string',
            'kontrak_id'    => 'nullable|integer',
        ]);

        try {
            // Get filter parameters
            $filterType  = $request->input('filter_type', 'month');
            $startDate   = $request->input('start_date');
            $endDate     = $request->input('end_date');
            $specificYear = $request->input('specific_year') ? (int) $request->input('specific_year') : null;
            $kendaraanId = $request->input('kendaraan_id');
            $categoryId  = $request->input('category_id');
            $departemen  = $request->input('departemen');
            $kontrakId   = $request->input('kontrak_id');
            $customDates = ($filterType === 'custom' && $startDate && $endDate)
                ? [$startDate, $endDate]
                : ($filterType === 'specific_year' && $specificYear ? [$specificYear] : []);

            // Get chart config and query based on page
            $config = $this->getPageChartConfig($page);
            $query  = $this->getPageQuery($page);

            if (!$query) {
                throw new \Exception('Invalid page identifier');
            }

            // Apply kendaraan filter if provided
            if ($kendaraanId) {
                $query->where('kendaraan_id', $kendaraanId);
            }

            // Apply category filter for service-history page
            if ($categoryId && $page === 'service-history') {
                $query->whereHas('parts', fn($p) => $p->where('category_id', $categoryId));
            }

            // Apply departemen filter for purchasero page
            if ($departemen && $page === 'purchasero') {
                $query->where('departemen', $departemen);
            }

            // Apply kontrak_id filter for summary page
            if ($kontrakId && $page === 'summary') {
                $query->where('kontrak_id', $kontrakId);
            }

            // Apply date filter
            $dateColumn = $config['dateColumn'] ?? 'created_at';
            $filteredQuery = $this->chartDataService->applyDateFilter(
                $query,
                $filterType,
                $customDates,
                $dateColumn
            );

            // Get chart data
            $pieConfig     = $config['pie'] ?? [];
            $pieQueryToUse = ($pieConfig['ignoreFilter'] ?? false) ? clone $query : clone $filteredQuery;
            $pieData = $this->chartDataService->getPieChartData(
                $pieQueryToUse,
                $pieConfig
            );

            $barData = $this->chartDataService->getBarChartData(
                clone $filteredQuery,
                array_merge($config['bar'] ?? [], [
                    'filter_type'   => $filterType,
                    'start_date'    => $startDate,
                    'end_date'      => $endDate,
                    'specific_year' => $specificYear,
                ])
            );

            $lineData = $this->chartDataService->getLineChartData(
                clone $filteredQuery,
                array_merge($config['line'] ?? [], [
                    'filter_type'   => $filterType,
                    'start_date'    => $startDate,
                    'end_date'      => $endDate,
                    'specific_year' => $specificYear,
                ])
            );

            $statsData = $this->chartDataService->getStatsData(
                clone $filteredQuery,
                $config['stats'] ?? [],
                clone $query  // Pass base query (unfiltered) for stats with ignoreFilter flag
            );

            // Get period label
            $periodLabel = $this->chartDataService->getPeriodLabel($filterType, $customDates);

            $chartData = [
                'pie'    => $pieData,
                'bar'    => $barData,
                'line'   => $lineData,
                'stats'  => $statsData,
                'period' => $periodLabel,
            ];

            return response()->json([
                'success' => true,
                'data'    => $chartData,
                'cached'  => false,
            ]);

        } catch (\Exception $e) {
            \Log::error('Chart data error: ' . $e->getMessage(), [
                'page' => $page,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load chart data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get chart configuration for specific page
     * 
     * @param string $page
     * @return array
     */
    protected function getPageChartConfig(string $page): array
    {
        // This will be extended as we integrate each page
        // For now, return basic config
        $configs = [
            'keuangan' => $this->getKeuanganConfig(),
            'payments' => $this->getPaymentsConfig(),
            'invoice' => $this->getInvoiceConfig(),
            'aging-ar' => $this->getAgingArConfig(),
            'aging-ap' => $this->getAgingApConfig(),
            'hutang-vendor' => $this->getHutangVendorConfig(),
            'budgeting' => $this->getBudgetingConfig(),
            'rekonsiliasi' => $this->getRekonsiliasiConfig(),
            'bukubesar' => $this->getBukubesarConfig(),
            'bupot' => $this->getBupotConfig(),
            'efaktur' => $this->getEfakturConfig(),
            'konsolidasi' => $this->getKonsolidasiConfig(),
            'virtual-account' => $this->getVirtualAccountConfig(),
            'summary' => $this->getSummaryConfig(),
            'rental' => $this->getRentalConfig(),
            'service' => $this->getServiceConfig(),
            'history' => $this->getHistoryConfig(),
            'data-leasing' => $this->getDataLeasingConfig(),
            'asuransi' => $this->getAsuransiConfig(),
            'gps' => $this->getGpsConfig(),
            'kir' => $this->getKirConfig(),
            'pajak-kendaraan' => $this->getPajakKendaraanConfig(),
            'stnk' => $this->getStnkConfig(),
            'service-history' => $this->getServiceHistoryConfig(),
            'purchasero'      => $this->getPurchaseroConfig(),
            'kendaraan' => $this->getKendaraanConfig(),
            'kendaraan-show' => $this->getKendaraanShowConfig(),
            'asuransi-kendaraan' => $this->getAsuransiKendaraanConfig(),
            'gps-kendaraan' => $this->getGpsKendaraanConfig(),
            // Member & Pelanggan
            'member' => $this->getMemberConfig(),
            'pelanggan' => $this->getPelangganConfig(),
            // Penawaran & Kontrak
            'penawaran' => $this->getPenawaranConfig(),
            'kontrak'   => $this->getKontrakConfig(),
            // Master Data
            'jenis-asuransi'  => $this->getJenisAsuransiConfig(),
            'supplier'        => $this->getSupplierConfig(),
            'jenis-kendaraan' => $this->getJenisKendaraanConfig(),
            // History list pages
            'pajak-history' => $this->getPajakHistoryConfig(),
            'asuransi-history' => $this->getAsuransiHistoryConfig(),
            'kir-history' => $this->getKirHistoryConfig(),
            'gps-kendaraan-history' => $this->getGpsKendaraanHistoryConfig(),
            // History per-kendaraan detail pages
            'pajak-history-kendaraan' => $this->getPajakHistoryConfig(),
            'asuransi-history-kendaraan' => $this->getAsuransiHistoryConfig(),
            'kir-history-kendaraan' => $this->getKirHistoryConfig(),
            'gps-history-kendaraan' => $this->getGpsKendaraanHistoryConfig(),
            // Add more as needed
        ];

        return $configs[$page] ?? [];
    }

    /**
     * Get query builder for specific page
     * 
     * @param string $page
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    protected function getPageQuery(string $page)
    {
        // Map page to model
        $models = [
            'keuangan' => \App\Models\Keuangan::query(),
            'payments' => \App\Models\InvoicePayment::query(),
            'invoice' => \App\Models\Invoice::query(),
            'aging-ar' => \App\Models\AgingAr::query(),
            'aging-ap' => \App\Models\aging_aps::query(),
            'hutang-vendor' => \App\Models\HutangVendor::query(),
            'budgeting' => \App\Models\AnggaranProyek::query(),
            'rekonsiliasi' => \App\Models\RekonsiliasiBank::query(),
            'bukubesar' => \App\Models\Bukubesar::query(),
            'bupot' => \App\Models\Bupot::query(),
            'efaktur' => \App\Models\Efaktur::query(),
            'konsolidasi' => \App\Models\LaporanKeuangan::query(),
            'virtual-account' => \App\Models\VirtualAccount::query(),
            'summary' => \App\Models\InvSummary::query(),
            'rental' => \App\Models\Rental::query(),
            'service' => \App\Models\Service::query(),
            'history' => \App\Models\Rental::query(),
            'data-leasing' => \App\Models\DataLeasing::query(),
            'asuransi' => \App\Models\Asuransi::query(),
            'gps' => \App\Models\Gps::query(),
            'kir' => \App\Models\Kir::query(),
            'pajak-kendaraan' => \App\Models\PajakKendaraan::query(),
            'stnk' => \App\Models\StnkHistory::query(),
            'service-history' => \App\Models\ServiceHistory::query(),
            'purchasero'      => \App\Models\Purchasero::query(),
            'kendaraan' => \App\Models\Kendaraan::query(),
            'kendaraan-show' => \App\Models\ServiceHistory::query(),
            'asuransi-kendaraan' => \App\Models\AsuransiKendaraan::query(),
            'gps-kendaraan' => \App\Models\GpsKendaraan::query(),
            // Member & Pelanggan
            'member' => \App\Models\Member::query(),
            'pelanggan' => \App\Models\Pelanggan::query(),
            // Penawaran & Kontrak
            'penawaran' => \App\Models\InvPenawaran::query(),
            'kontrak'   => \App\Models\InvKontrak::query(),
            // Master Data
            'jenis-asuransi'  => \App\Models\JenisAsuransi::query(),
            'supplier'        => \App\Models\Supplier::query(),
            'jenis-kendaraan' => \App\Models\Jenis::query(),
            // History list pages
            'pajak-history' => \App\Models\PajakHistory::query(),
            'asuransi-history' => \App\Models\AsuransiHistory::query(),
            'kir-history' => \App\Models\KirHistory::query(),
            'gps-kendaraan-history' => \App\Models\GpsKendaraanHistory::query(),
            // History per-kendaraan detail pages
            'pajak-history-kendaraan' => \App\Models\PajakHistory::query(),
            'asuransi-history-kendaraan' => \App\Models\AsuransiHistory::query(),
            'kir-history-kendaraan' => \App\Models\KirHistory::query(),
            'gps-history-kendaraan' => \App\Models\GpsKendaraanHistory::query(),
            // Add more as needed
        ];

        return $models[$page] ?? null;
    }

    /**
     * Chart config for Keuangan page
     */
    protected function getKeuanganConfig(): array
    {
        return [
            'dateColumn' => 'tanggal',
            'pie' => [
                'title' => 'Distribusi Keuangan',
                'groupBy' => 'jenis',
                'valueColumn' => 'pemasukan',
                'aggregation' => 'custom',
                'labels' => ['Pemasukan', 'Pengeluaran'],
                'colors' => ['#10b981', '#ef4444']
            ],
            'bar' => [
                'title' => 'Perbandingan Bulanan',
                'groupBy' => 'month',
                'valueColumns' => [
                    'pemasukan',
                    'pengeluaran',
                    ['computed' => ['op' => 'subtract', 'a' => 'pemasukan', 'b' => 'pengeluaran', 'allowNegative' => true]],
                ],
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal',
                'limit' => 6,
                'labels' => ['Pemasukan', 'Pengeluaran', 'Sisa Saldo'],
                'colors' => ['#10b981', '#ef4444', '#3b82f6'],
            ],
            'line' => [
                'title'       => 'Trend Saldo',
                'groupBy'     => 'month',
                'valueColumn' => 'last_value:saldo',
                'aggregation' => 'last_value',
                'dateColumn'  => 'tanggal',
                'limit'       => 12,
                'label'       => 'Saldo Akhir',
                'color'       => '#8b5cf6'
            ],
            'stats' => [
                [
                    'label' => 'Total Transaksi',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-receipt',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'tanggal'
                ],
                [
                    'label' => 'Total Pemasukan',
                    'type' => 'sum',
                    'column' => 'pemasukan',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-arrow-trend-up',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'tanggal'
                ],
                [
                    'label' => 'Total Pengeluaran',
                    'type' => 'sum',
                    'column' => 'pengeluaran',
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-arrow-trend-down',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'tanggal'
                ],
                [
                    'label' => 'Saldo',
                    'type' => 'custom_saldo',
                    'format' => 'currency',
                    'color' => '#8b5cf6',
                    'iconBg' => '#f3e8ff',
                    'icon' => 'fa fa-wallet',
                    'badge' => 'Healthy',
                    'badgeType' => 'success'
                ]
            ]
        ];
    }

    /**
     * Chart config for Payments page
     */
    protected function getPaymentsConfig(): array
    {
        return [
            'dateColumn' => 'payment_date',
            'pie' => [
                'title' => 'Status Pembayaran',
                'ignoreFilter' => true,
                'groupBy' => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [
                    'Verified' => 'Verified',
                    'Pending' => 'Pending',
                    'Rejected' => 'Rejected'
                ],
                'colors' => ['#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title' => 'Pembayaran per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['amount'],
                'aggregation' => 'sum',
                'dateColumn' => 'payment_date',
                'limit' => 6,
                'labels' => ['Total Pembayaran']
            ],
            'line' => [
                'title' => 'Trend Pembayaran',
                'groupBy' => 'month',
                'valueColumn' => 'amount',
                'aggregation' => 'sum',
                'dateColumn' => 'payment_date',
                'limit' => 12,
                'label' => 'Amount',
                'color' => '#3b82f6'
            ],
            'stats' => [
                [
                    'label' => 'Total Transaksi',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-receipt',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'payment_date'
                ],
                [
                    'label' => 'Total Amount',
                    'type' => 'sum',
                    'column' => 'amount',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-money-bill-wave',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'payment_date'
                ],
                [
                    'label' => 'Verified',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'Verified'],
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle'
                ],
                [
                    'label' => 'Pending',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'Pending'],
                    'format' => 'number',
                    'color' => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon' => 'fa fa-clock'
                ]
            ]
        ];
    }

    /**
     * Chart config for Invoice page
     */
    protected function getInvoiceConfig(): array
    {
        return [
            'dateColumn' => 'invoice_date',
            'pie' => [
                'title' => 'Status Invoice',
                'ignoreFilter' => true,
                'groupBy' => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [
                    'lunas' => 'Lunas',
                    'partial' => 'Partial',
                    'overdue' => 'Overdue',
                    'unpaid' => 'Unpaid'
                ],
                'colors' => ['#10b981', '#f59e0b', '#ef4444', '#6b7280']
            ],
            'bar' => [
                'title' => 'Invoice per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['total'],
                'aggregation' => 'sum',
                'dateColumn' => 'invoice_date',
                'limit' => 6,
                'labels' => ['Total Invoice']
            ],
            'line' => [
                'title' => 'Trend Invoice',
                'groupBy' => 'month',
                'valueColumn' => 'total',
                'aggregation' => 'sum',
                'dateColumn' => 'invoice_date',
                'limit' => 12,
                'label' => 'Amount',
                'color' => '#10b981'
            ],
            'stats' => [
                [
                    'label' => 'Total Invoice',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-file-invoice',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'invoice_date'
                ],
                [
                    'label' => 'Total Amount',
                    'type' => 'sum',
                    'column' => 'total',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-money-bill-wave',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'invoice_date'
                ],
                [
                    'label' => 'Lunas',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'lunas'],
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle'
                ],
                [
                    'label' => 'Overdue',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'overdue'],
                    'format' => 'number',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-exclamation-triangle'
                ]
            ]
        ];
    }

    /**
     * Chart config for Aging AR page
     */
    protected function getAgingArConfig(): array
    {
        return [
            'dateColumn' => 'jatuh_tempo',
            'pie' => [
                'title' => 'Kategori Aging',
                'groupBy' => 'kategori',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [
                    'Current' => 'Current',
                    '1-30' => '1-30 Hari',
                    '31-60' => '31-60 Hari',
                    '61-90' => '61-90 Hari',
                    '>90' => '>90 Hari'
                ],
                'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#991b1b']
            ],
            'bar' => [
                'title' => 'Outstanding per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['total'],
                'aggregation' => 'sum',
                'dateColumn' => 'jatuh_tempo',
                'limit' => 6,
                'labels' => ['Outstanding']
            ],
            'line' => [
                'title' => 'Trend AR',
                'groupBy' => 'month',
                'valueColumn' => 'total',
                'aggregation' => 'sum',
                'dateColumn' => 'jatuh_tempo',
                'limit' => 12,
                'label' => 'Amount',
                'color' => '#ef4444'
            ],
            'stats' => [
                [
                    'label' => 'Total Outstanding',
                    'type' => 'sum',
                    'column' => 'total',
                    'format' => 'currency',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-file-invoice-dollar',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'jatuh_tempo'
                ],
                [
                    'label' => 'Overdue Amount',
                    'type' => 'sum_where',
                    'column' => 'total',
                    'where' => ['status' => 'overdue'],
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-exclamation-triangle'
                ],
                [
                    'label' => 'Total Items',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-list'
                ],
                [
                    'label' => 'Current',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['kategori' => 'Current'],
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle'
                ]
            ]
        ];
    }

    /**
     * Chart config for Aging AP page
     */
    protected function getAgingApConfig(): array
    {
        return [
            'dateColumn' => 'jatuh_tempo',
            'pie' => [
                'title' => 'Kategori Aging',
                'groupBy' => 'kategori',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [
                    'Current' => 'Current',
                    '1-30' => '1-30 Hari',
                    '31-60' => '31-60 Hari',
                    '61-90' => '61-90 Hari',
                    '>90' => '>90 Hari'
                ],
                'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#991b1b']
            ],
            'bar' => [
                'title' => 'Hutang per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['jumlah'],
                'aggregation' => 'sum',
                'dateColumn' => 'jatuh_tempo',
                'limit' => 6,
                'labels' => ['Hutang']
            ],
            'line' => [
                'title' => 'Trend AP',
                'groupBy' => 'month',
                'valueColumn' => 'jumlah',
                'aggregation' => 'sum',
                'dateColumn' => 'jatuh_tempo',
                'limit' => 12,
                'label' => 'Amount',
                'color' => '#ef4444'
            ],
            'stats' => [
                [
                    'label' => 'Total Hutang',
                    'type' => 'sum',
                    'column' => 'jumlah',
                    'format' => 'currency',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-file-invoice',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'jatuh_tempo'
                ],
                [
                    'label' => 'Overdue',
                    'type' => 'sum_where_not',
                    'column' => 'jumlah',
                    'where' => ['kategori' => 'Current'],
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-exclamation-triangle'
                ],
                [
                    'label' => 'Total Items',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-list'
                ],
                [
                    'label' => 'Lunas',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_lunas' => 1],
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle'
                ]
            ]
        ];
    }

    /**
     * Chart config for Hutang Vendor page
     */
    protected function getHutangVendorConfig(): array
    {
        return [
            'dateColumn' => 'jatuh_tempo',
            'pie' => [
                'title' => 'Status Hutang',
                'groupBy' => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title' => 'Hutang per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['nominal'],
                'aggregation' => 'sum',
                'dateColumn' => 'jatuh_tempo',
                'limit' => 6,
                'labels' => ['Nominal Hutang']
            ],
            'line' => [
                'title' => 'Trend Hutang',
                'groupBy' => 'month',
                'valueColumn' => 'sisa',
                'aggregation' => 'sum',
                'dateColumn' => 'jatuh_tempo',
                'limit' => 12,
                'label' => 'Sisa Hutang',
                'color' => '#ef4444'
            ],
            'stats' => [
                [
                    'label' => 'Total Hutang',
                    'type' => 'sum',
                    'column' => 'nominal',
                    'format' => 'currency',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-file-invoice'
                ],
                [
                    'label' => 'Sudah Dibayar',
                    'type' => 'sum',
                    'column' => 'dibayar',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle'
                ],
                [
                    'label' => 'Sisa Hutang',
                    'type' => 'sum',
                    'column' => 'sisa',
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-exclamation-triangle'
                ],
                [
                    'label' => 'Total Vendor',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#8b5cf6',
                    'iconBg' => '#f3e8ff',
                    'icon' => 'fa fa-building'
                ]
            ]
        ];
    }

    /**
     * Chart config for Budgeting page
     */
    protected function getBudgetingConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie' => [
                'title' => 'Budget Allocation',
                'groupBy' => 'kategori',
                'valueColumn' => 'budget',
                'aggregation' => 'sum',
                'labels' => [],
                'colors' => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            ],
            'bar' => [
                'title' => 'Budget vs Realisasi',
                'groupBy' => 'month',
                'valueColumns' => ['budget', 'realisasi'],
                'aggregation' => 'sum',
                'dateColumn' => 'created_at',
                'limit' => 6,
                'labels' => ['Budget', 'Realisasi']
            ],
            'line' => [
                'title' => 'Trend Budget',
                'groupBy' => 'month',
                'valueColumn' => 'budget',
                'aggregation' => 'sum',
                'dateColumn' => 'created_at',
                'limit' => 12,
                'label' => 'Budget',
                'color' => '#4f6ef7'
            ],
            'stats' => [
                [
                    'label' => 'Total Budget',
                    'type' => 'sum',
                    'column' => 'budget',
                    'format' => 'currency',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-coins'
                ],
                [
                    'label' => 'Total Realisasi',
                    'type' => 'sum',
                    'column' => 'realisasi',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle'
                ],
                [
                    'label' => 'Variance',
                    'type' => 'sum',
                    'column' => 'sisa',
                    'format' => 'currency',
                    'color' => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon' => 'fa fa-balance-scale'
                ],
                [
                    'label' => 'Total Proyek',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#8b5cf6',
                    'iconBg' => '#f3e8ff',
                    'icon' => 'fa fa-project-diagram'
                ]
            ]
        ];
    }

    /**
     * Chart config for Rekonsiliasi page
     */
    protected function getRekonsiliasiConfig(): array
    {
        return [
            'dateColumn' => 'tanggal',
            'pie' => [
                'title' => 'Status Rekonsiliasi',
                'groupBy' => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title' => 'Transaksi per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['saldo_buku', 'saldo_bank'],
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal',
                'limit' => 6,
                'labels' => ['Saldo Buku', 'Saldo Bank']
            ],
            'line' => [
                'title' => 'Trend Selisih',
                'groupBy' => 'month',
                'valueColumn' => 'selisih',
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal',
                'limit' => 12,
                'label' => 'Selisih',
                'color' => '#ef4444'
            ],
            'stats' => [
                [
                    'label' => 'Total Transaksi',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-exchange-alt'
                ],
                [
                    'label' => 'Saldo Buku',
                    'type' => 'sum',
                    'column' => 'saldo_buku',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-book'
                ],
                [
                    'label' => 'Saldo Bank',
                    'type' => 'sum',
                    'column' => 'saldo_bank',
                    'format' => 'currency',
                    'color' => '#3b82f6',
                    'iconBg' => '#dbeafe',
                    'icon' => 'fa fa-university'
                ],
                [
                    'label' => 'Selisih',
                    'type' => 'sum',
                    'column' => 'selisih',
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-exclamation-triangle'
                ]
            ]
        ];
    }

    /**
     * Chart config for Bukubesar page
     */
    protected function getBukubesarConfig(): array
    {
        return [
            'dateColumn' => 'tanggal',
            'pie' => [
                'title' => 'Debit vs Kredit',
                'groupBy' => 'aktivitas',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#10b981', '#ef4444', '#3b82f6']
            ],
            'bar' => [
                'title' => 'Debit & Kredit per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['debit', 'kredit'],
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal',
                'limit' => 6,
                'labels' => ['Debit', 'Kredit']
            ],
            'line' => [
                'title' => 'Trend Saldo',
                'groupBy' => 'month',
                'valueColumn' => 'saldo',
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal',
                'limit' => 12,
                'label' => 'Saldo',
                'color' => '#4f6ef7'
            ],
            'stats' => [
                [
                    'label' => 'Total Debit',
                    'type' => 'sum',
                    'column' => 'debit',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-arrow-up'
                ],
                [
                    'label' => 'Total Kredit',
                    'type' => 'sum',
                    'column' => 'kredit',
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-arrow-down'
                ],
                [
                    'label' => 'Balance',
                    'type' => 'sum',
                    'column' => 'saldo',
                    'format' => 'currency',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-balance-scale'
                ],
                [
                    'label' => 'Total Transaksi',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#8b5cf6',
                    'iconBg' => '#f3e8ff',
                    'icon' => 'fa fa-exchange-alt'
                ]
            ]
        ];
    }

    /**
     * Chart config for Bupot page
     */
    protected function getBupotConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_bukti',
            'pie' => [
                'title' => 'Jenis Pajak',
                'groupBy' => 'tipe',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title' => 'Pajak per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['jumlah_potong'],
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal_bukti',
                'limit' => 6,
                'labels' => ['Jumlah Pajak']
            ],
            'line' => [
                'title' => 'Trend Pemotongan',
                'groupBy' => 'month',
                'valueColumn' => 'jumlah_potong',
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal_bukti',
                'limit' => 12,
                'label' => 'Jumlah Potong',
                'color' => '#ef4444'
            ],
            'stats' => [
                [
                    'label' => 'Total Pajak',
                    'type' => 'sum',
                    'column' => 'jumlah_potong',
                    'format' => 'currency',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-file-invoice-dollar'
                ],
                [
                    'label' => 'Total Dipotong',
                    'type' => 'sum',
                    'column' => 'jumlah_bruto',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-money-bill-wave'
                ],
                [
                    'label' => 'Avg Rate',
                    'type' => 'avg',
                    'column' => 'tarif_pajak',
                    'format' => 'percentage',
                    'color' => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon' => 'fa fa-percentage'
                ],
                [
                    'label' => 'Total Bukti',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#8b5cf6',
                    'iconBg' => '#f3e8ff',
                    'icon' => 'fa fa-file-alt'
                ]
            ]
        ];
    }

    /**
     * Chart config for E-faktur page
     */
    protected function getEfakturConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_faktur',
            'pie' => [
                'title' => 'Status E-Faktur',
                'groupBy' => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title' => 'E-Faktur per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['dpp', 'ppn'],
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal_faktur',
                'limit' => 6,
                'labels' => ['DPP', 'PPN']
            ],
            'line' => [
                'title' => 'Trend PPN',
                'groupBy' => 'month',
                'valueColumn' => 'ppn',
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal_faktur',
                'limit' => 12,
                'label' => 'PPN',
                'color' => '#10b981'
            ],
            'stats' => [
                [
                    'label' => 'Total Faktur',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-file-invoice'
                ],
                [
                    'label' => 'Total DPP',
                    'type' => 'sum',
                    'column' => 'dpp',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-money-bill-wave'
                ],
                [
                    'label' => 'Total PPN',
                    'type' => 'sum',
                    'column' => 'ppn',
                    'format' => 'currency',
                    'color' => '#3b82f6',
                    'iconBg' => '#dbeafe',
                    'icon' => 'fa fa-coins'
                ],
                [
                    'label' => 'Reject Count',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'reject'],
                    'format' => 'number',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-exclamation-triangle'
                ]
            ]
        ];
    }

    /**
     * Chart config for Konsolidasi page
     */
    protected function getKonsolidasiConfig(): array
    {
        return [
            'dateColumn' => 'periode',
            'pie' => [
                'title' => 'Revenue by Business Unit',
                'groupBy' => 'nama_perusahaan',
                'valueColumn' => 'pendapatan',
                'aggregation' => 'sum',
                'labels' => [],
                'colors' => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            ],
            'bar' => [
                'title' => 'Consolidated Revenue',
                'groupBy' => 'month',
                'valueColumns' => ['pendapatan', 'beban'],
                'aggregation' => 'sum',
                'dateColumn' => 'periode',
                'limit' => 6,
                'labels' => ['Pendapatan', 'Beban']
            ],
            'line' => [
                'title' => 'Profit Trend',
                'groupBy' => 'month',
                'valueColumn' => 'laba',
                'aggregation' => 'sum',
                'dateColumn' => 'periode',
                'limit' => 12,
                'label' => 'Laba',
                'color' => '#10b981'
            ],
            'stats' => [
                [
                    'label' => 'Total Revenue',
                    'type' => 'sum',
                    'column' => 'pendapatan',
                    'format' => 'currency',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-chart-line'
                ],
                [
                    'label' => 'Total Expense',
                    'type' => 'sum',
                    'column' => 'beban',
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-money-bill-wave'
                ],
                [
                    'label' => 'Net Income',
                    'type' => 'sum',
                    'column' => 'laba',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-coins'
                ],
                [
                    'label' => 'Business Units',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#8b5cf6',
                    'iconBg' => '#f3e8ff',
                    'icon' => 'fa fa-building'
                ]
            ]
        ];
    }

    /**
     * Chart config for Virtual Account page
     */
    protected function getVirtualAccountConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie' => [
                'title' => 'VA Status',
                'groupBy' => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title' => 'VA Transactions',
                'groupBy' => 'month',
                'valueColumns' => ['amount'],
                'aggregation' => 'sum',
                'dateColumn' => 'created_at',
                'limit' => 6,
                'labels' => ['Amount']
            ],
            'line' => [
                'title' => 'VA Trend',
                'groupBy' => 'month',
                'valueColumn' => 'amount',
                'aggregation' => 'sum',
                'dateColumn' => 'created_at',
                'limit' => 12,
                'label' => 'Amount',
                'color' => '#3b82f6'
            ],
            'stats' => [
                [
                    'label' => 'Active VA',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'active'],
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle'
                ],
                [
                    'label' => 'Total Amount',
                    'type' => 'sum',
                    'column' => 'amount',
                    'format' => 'currency',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-money-bill-wave'
                ],
                [
                    'label' => 'Success Rate',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'success'],
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-percent'
                ],
                [
                    'label' => 'Pending',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'pending'],
                    'format' => 'number',
                    'color' => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon' => 'fa fa-clock'
                ]
            ]
        ];
    }

    /**
     * Chart config for Summary page
     */
    protected function getSummaryConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie' => [
                'title' => 'Distribusi Status Pembayaran',
                'ignoreFilter' => true,
                'groupBy' => 'payment_status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [
                    'Paid'    => 'Paid',
                    'Partial' => 'Partial',
                    'Unpaid'  => 'Unpaid',
                ],
                'colors' => ['#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title' => 'Total Tagihan per Periode (tgl. dibuat)',
                'groupBy' => 'month',
                'valueColumns' => ['total_amount', 'paid_amount', 'remaining_amount'],
                'aggregation' => 'sum',
                'dateColumn' => 'created_at',
                'limit' => 12,
                'labels' => ['Total Tagihan', 'Dibayar', 'Sisa Bayar'],
                'colors' => ['#3b82f6', '#10b981', '#ef4444'],
            ],
            'line' => [
                'title' => 'Trend Tagihan (tgl. dibuat)',
                'groupBy' => 'month',
                'valueColumn' => 'total_amount',
                'aggregation' => 'sum',
                'dateColumn' => 'created_at',
                'limit' => 12,
                'label' => 'Total Tagihan',
                'color' => '#10b981'
            ],
            'stats' => [
                [
                    'label' => 'Total Tagihan',
                    'type' => 'sum',
                    'column' => 'total_amount',
                    'format' => 'currency',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-chart-line',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'created_at'
                ],
                [
                    'label' => 'Total Data',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-list',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'created_at'
                ],
                [
                    'label' => 'Sudah Dibayar',
                    'type' => 'sum',
                    'column' => 'paid_amount',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle'
                ],
                [
                    'label' => 'Sisa Tagihan',
                    'type' => 'sum',
                    'column' => 'remaining_amount',
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-clock'
                ]
            ]
        ];
    }

    /**
     * Chart config for Rental page
     */
    protected function getRentalConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_mulai',
            'pie' => [
                'title' => 'Payment Status',
                'groupBy' => 'status_pembayaran',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title'        => 'Pendapatan Rental per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => [
                    'total_biaya',
                    ['where' => ['status_pembayaran' => 'lunas'],                                    'column' => 'total_biaya', 'label' => 'Lunas'],
                    ['where' => ['status_pembayaran' => ['belum_bayar', 'dp', 'partial']], 'column' => 'total_biaya', 'label' => 'Belum Lunas'],
                ],
                'aggregation'  => 'sum',
                'dateColumn'   => 'tanggal_mulai',
                'limit'        => 6,
                'labels'       => ['Total', 'Lunas', 'Belum Lunas'],
                'colors'       => ['#3b82f6', '#10b981', '#ef4444'],
            ],
            'line' => [
                'title'       => 'Trend Pendapatan Rental',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'total_biaya',
                'aggregation' => 'sum',
                'dateColumn'  => 'tanggal_mulai',
                'limit'       => 12,
                'label'       => 'Pendapatan',
                'color'       => '#3b82f6'
            ],
            'stats' => [
                [
                    'label' => 'Total Rentals',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-car'
                ],
                [
                    'label' => 'Total Revenue',
                    'type' => 'sum',
                    'column' => 'total_biaya',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-money-bill-wave'
                ],
                [
                    'label' => 'Active Rentals',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'active'],
                    'format' => 'number',
                    'color' => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon' => 'fa fa-clock'
                ],
                [
                    'label' => 'Avg. Revenue',
                    'type' => 'avg',
                    'column' => 'total_biaya',
                    'format' => 'currency',
                    'color' => '#8b5cf6',
                    'iconBg' => '#f3e8ff',
                    'icon' => 'fa fa-calculator'
                ]
            ]
        ];
    }

    /**
     * Chart config for History Rental page
     */
    protected function getHistoryConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_mulai',
            'pie' => [
                'title'       => 'Distribusi Status Rental',
                'groupBy'     => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels'      => [
                    'aktif'   => 'Aktif',
                    'selesai' => 'Selesai',
                    'booking' => 'Booking',
                    'Pending' => 'Pending',
                    'batal'   => 'Batal',
                ],
                'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#6b7280', '#ef4444'],
            ],
            'bar' => [
                'title'        => 'Pendapatan History Rental per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => [
                    'total_biaya',
                    ['where' => ['status_pembayaran' => 'lunas'],                                    'column' => 'total_biaya', 'label' => 'Lunas'],
                    ['where' => ['status_pembayaran' => ['belum_bayar', 'dp', 'partial']], 'column' => 'total_biaya', 'label' => 'Belum Lunas'],
                ],
                'aggregation' => 'sum',
                'dateColumn'  => 'tanggal_mulai',
                'limit'       => 6,
                'labels'      => ['Total', 'Lunas', 'Belum Lunas'],
                'colors'      => ['#3b82f6', '#10b981', '#ef4444'],
            ],
            'line' => [
                'title'       => 'Trend Pendapatan History Rental',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'total_biaya',
                'aggregation' => 'sum',
                'dateColumn'  => 'tanggal_mulai',
                'limit'       => 12,
                'label'       => 'Pendapatan',
                'color'       => '#3b82f6',
            ],
            'stats' => [
                [
                    'label'  => 'Total Rental',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon'   => 'fa fa-car',
                ],
                [
                    'label'  => 'Total Pendapatan',
                    'type'   => 'sum',
                    'column' => 'total_biaya',
                    'format' => 'currency',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-money-bill-wave',
                ],
                [
                    'label'  => 'Sudah Lunas',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status_pembayaran' => 'lunas'],
                    'format' => 'number',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-check-circle',
                ],
                [
                    'label'  => 'Belum Lunas',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status_pembayaran' => ['belum_bayar', 'dp', 'partial']],
                    'format' => 'number',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-clock',
                ],
            ],
        ];
    }

    /**
     * Chart config for Data Leasing page
     */
    protected function getDataLeasingConfig(): array
    {
        return [
            'dateColumn' => 'periode_mulai',
            'pie' => [
                'title'       => 'Distribusi Cara Bayar',
                'groupBy'     => 'cara_bayar',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels'      => [],
                'colors'      => ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
            ],
            'bar' => [
                'title'        => 'Total Angsuran per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => [
                    'angsuran_per_bulan',
                    ['where' => ['cara_bayar' => 'Auto Debit'], 'column' => 'angsuran_per_bulan', 'label' => 'Auto Debit'],
                    ['where' => ['cara_bayar' => 'Transfer'],   'column' => 'angsuran_per_bulan', 'label' => 'Transfer'],
                ],
                'aggregation' => 'sum',
                'dateColumn'  => 'periode_mulai',
                'limit'       => 6,
                'labels'      => ['Total Angsuran', 'Auto Debit', 'Transfer'],
                'colors'      => ['#3b82f6', '#10b981', '#f59e0b'],
            ],
            'line' => [
                'title'       => 'Trend Total Angsuran',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'angsuran_per_bulan',
                'aggregation' => 'sum',
                'dateColumn'  => 'periode_mulai',
                'limit'       => 12,
                'label'       => 'Angsuran',
                'color'       => '#3b82f6',
            ],
            'stats' => [
                [
                    'label'  => 'Total Data',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon'   => 'fa fa-bank',
                ],
                [
                    'label'  => 'Total Angsuran/Bln',
                    'type'   => 'sum',
                    'column' => 'angsuran_per_bulan',
                    'format' => 'currency',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-money-bill-wave',
                ],
                [
                    'label'  => 'Avg Angsuran',
                    'type'   => 'avg',
                    'column' => 'angsuran_per_bulan',
                    'format' => 'currency',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-calculator',
                ],
                [
                    'label'  => 'Total Cicilan',
                    'type'   => 'sum',
                    'column' => 'jumlah_cicilan',
                    'format' => 'number',
                    'color'  => '#8b5cf6',
                    'iconBg' => '#f3e8ff',
                    'icon'   => 'fa fa-list-ol',
                ],
            ],
        ];
    }

    /**
     * Chart config for Service page
     */
    protected function getServiceConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie' => [
                'title' => 'Service Distribution',
                'groupBy' => 'nama_service',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            ],
            'bar' => [
                'title' => 'Service Cost',
                'groupBy' => 'month',
                'valueColumns' => ['biaya_default'],
                'aggregation' => 'sum',
                'dateColumn' => 'created_at',
                'limit' => 6,
                'labels' => ['Cost']
            ],
            'line' => [
                'title' => 'Service Trend',
                'groupBy' => 'month',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'dateColumn' => 'created_at',
                'limit' => 12,
                'label' => 'Services',
                'color' => '#10b981'
            ],
            'stats' => [
                [
                    'label' => 'Total Services',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-wrench'
                ],
                [
                    'label' => 'Total Cost',
                    'type' => 'sum',
                    'column' => 'biaya_default',
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-money-bill-wave'
                ],
                [
                    'label' => 'Avg. Cost',
                    'type' => 'avg',
                    'column' => 'biaya_default',
                    'format' => 'currency',
                    'color' => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon' => 'fa fa-calculator'
                ],
                [
                    'label' => 'Service Types',
                    'type' => 'count',
                    'column' => 'nama_service',
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-list'
                ]
            ]
        ];
    }

    /**
     * Chart config for Asuransi page
     */
    protected function getAsuransiConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie' => [
                'title' => 'Distribusi Asuransi',
                'groupBy' => 'nama_asuransi',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title' => 'Penambahan Asuransi per Periode',
                'groupBy' => 'month',
                'valueColumns' => [['count' => true, 'label' => 'Jumlah Asuransi']],
                'aggregation' => 'count',
                'dateColumn' => 'created_at',
                'limit' => 12,
                'labels' => ['Jumlah Asuransi'],
                'colors' => ['#4f6ef7'],
            ],
            'line' => [
                'title' => 'Trend Asuransi',
                'groupBy' => 'month',
                'valueColumn' => '_count',
                'aggregation' => 'count',
                'dateColumn' => 'created_at',
                'limit' => 12,
                'label' => 'Total',
                'color' => '#4f6ef7'
            ],
            'stats' => [
                [
                    'label' => 'Total Asuransi',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-shield-alt',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'created_at',
                ],
            ]
        ];
    }

    /**
     * Chart config for GPS page
     */
    protected function getGpsConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie' => [
                'title' => 'Distribusi GPS',
                'groupBy' => 'nama_gps',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title' => 'Penambahan GPS per Periode',
                'groupBy' => 'month',
                'valueColumns' => [['count' => true, 'label' => 'Jumlah GPS']],
                'aggregation' => 'count',
                'dateColumn' => 'created_at',
                'limit' => 12,
                'labels' => ['Jumlah GPS'],
                'colors' => ['#4f6ef7'],
            ],
            'line' => [
                'title' => 'Trend GPS',
                'groupBy' => 'month',
                'valueColumn' => '_count',
                'aggregation' => 'count',
                'dateColumn' => 'created_at',
                'limit' => 12,
                'label' => 'Total',
                'color' => '#4f6ef7'
            ],
            'stats' => [
                [
                    'label' => 'Total GPS',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-map-marker-alt',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'created_at',
                ],
            ]
        ];
    }

    /**
     * Chart config for KIR page
     */
    protected function getKirConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_bayar',
            'pie' => [
                'title' => 'Distribusi Status KIR',
                'groupBy' => 'status_uji',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [
                    'uji berkala' => 'Uji Berkala',
                    'uji pertama' => 'Uji Pertama',
                ],
                'colors' => ['#3b82f6', '#8b5cf6', '#f59e0b']
            ],
            'bar' => [
                'title' => 'Biaya KIR per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['biaya'],
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal_bayar',
                'limit' => 12,
                'labels' => ['Biaya KIR'],
                'colors' => ['#f59e0b'],
            ],
            'line' => [
                'title' => 'Trend KIR',
                'groupBy' => 'month',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'dateColumn' => 'tanggal_bayar',
                'limit' => 12,
                'label' => 'Jumlah KIR',
                'color' => '#3b82f6'
            ],
            'stats' => [
                [
                    'label' => 'Total KIR',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-clipboard-check'
                ],
                [
                    'label' => 'Total Biaya',
                    'type' => 'sum',
                    'column' => 'biaya',
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-money-bill-wave'
                ],
                [
                    'label' => 'Uji Berkala',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_uji' => 'uji berkala'],
                    'format' => 'number',
                    'color' => '#3b82f6',
                    'iconBg' => '#dbeafe',
                    'icon' => 'fa fa-rotate'
                ],
                [
                    'label' => 'Uji Pertama',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_uji' => 'uji pertama'],
                    'format' => 'number',
                    'color' => '#8b5cf6',
                    'iconBg' => '#ede9fe',
                    'icon' => 'fa fa-star'
                ],
            ]
        ];
    }

    /**
     * Chart config for Pajak Kendaraan page
     */
    protected function getPajakKendaraanConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_bayar',
            'pie' => [
                'title' => 'Tax Status',
                'groupBy' => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [],
                'colors' => ['#10b981', '#f59e0b', '#ef4444']
            ],
            'bar' => [
                'title' => 'Tax Payment',
                'groupBy' => 'month',
                'valueColumns' => ['nominal'],
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal_bayar',
                'limit' => 6,
                'labels' => ['Amount']
            ],
            'line' => [
                'title' => 'Tax Trend',
                'groupBy' => 'month',
                'valueColumn' => 'nominal',
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal_bayar',
                'limit' => 12,
                'label' => 'Payment',
                'color' => '#ef4444'
            ],
            'stats' => [
                [
                    'label' => 'Total Taxes',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-file-invoice-dollar',
                    'ignoreFilter' => true,
                ],
                [
                    'label' => 'Total Payment',
                    'type' => 'sum',
                    'column' => 'nominal',
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-money-bill-wave',
                    'ignoreFilter' => true,
                ],
                [
                    'label' => 'Paid',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'sudah_bayar'],
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle',
                    'ignoreFilter' => true,
                ],
                [
                    'label' => 'Overdue',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status' => 'belum_bayar'],
                    'format' => 'number',
                    'color' => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon' => 'fa fa-exclamation-triangle',
                    'ignoreFilter' => true,
                ]
            ]
        ];
    }

    /**
     * Chart config for STNK page
     */
    protected function getStnkConfig(): array
    {
        return [
            'dateColumn' => 'diperpanjang_pada',
            'pie' => [
                'title'       => 'Distribusi Biaya per Merk',
                'groupBy'     => 'merk',
                'valueColumn' => 'biaya',
                'aggregation' => 'sum',
                'labels'      => [],
                'colors'      => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
            ],
            'bar' => [
                'title'        => 'Biaya Perpanjangan STNK per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => ['biaya'],
                'aggregation'  => 'sum',
                'dateColumn'   => 'diperpanjang_pada',
                'limit'        => 12,
                'labels'       => ['Biaya STNK'],
                'colors'       => ['#4f6ef7'],
                'format'       => 'currency',
            ],
            'line' => [
                'title'       => 'Trend Perpanjangan STNK',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'biaya',
                'aggregation' => 'sum',
                'dateColumn'  => 'diperpanjang_pada',
                'limit'       => 12,
                'label'       => 'Biaya',
                'color'       => '#10b981',
                'format'      => 'currency',
            ],
            'stats' => [
                [
                    'label'  => 'Total Perpanjangan',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon'   => 'fa fa-id-card',
                ],
                [
                    'label'  => 'Total Biaya',
                    'type'   => 'sum',
                    'column' => 'biaya',
                    'format' => 'currency',
                    'color'  => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon'   => 'fa fa-money-bill-wave',
                ],
                [
                    'label'  => 'Rata-rata Biaya',
                    'type'   => 'avg',
                    'column' => 'biaya',
                    'format' => 'currency',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-calculator',
                ],
                [
                    'label'  => 'Kendaraan Terdaftar',
                    'type'   => 'count',
                    'column' => 'kendaraan_id',
                    'format' => 'number',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-car',
                ],
            ],
        ];
    }

    /**
     * Chart config for Service History page
     */
    protected function getServiceHistoryConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_service',
            'pie' => [
                'title' => 'Biaya per Status',
                'groupBy' => 'status',
                'valueColumn' => 'total_biaya',
                'aggregation' => 'sum',
                'labels' => [],
                'colors' => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']
            ],
            'bar' => [
                'title'        => 'Biaya Service per Bulan',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => [
                    'total_biaya',
                    'maks_bulanan',
                    ['computed' => ['op' => 'subtract', 'a' => 'maks_bulanan', 'b' => 'total_biaya']],
                ],
                'aggregation'  => 'sum',
                'dateColumn'   => 'tanggal_service',
                'limit'        => 6,
                'labels'       => ['Biaya', 'Limit', 'Sisa'],
                'colors'       => ['#ef4444', '#f59e0b', '#10b981'],
            ],
            'line' => [
                'title'       => 'Trend Biaya Service',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'total_biaya',
                'aggregation' => 'sum',
                'dateColumn'  => 'tanggal_service',
                'limit'       => 12,
                'label'       => 'Biaya',
                'color'       => '#ef4444'
            ],
            'stats' => [
                [
                    'label' => 'Total Service',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-wrench'
                ],
                [
                    'label' => 'Total Biaya',
                    'type' => 'sum',
                    'column' => 'total_biaya',
                    'format' => 'currency',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-money-bill-wave'
                ],
                [
                    'label' => 'Service Bulan Ini',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-calendar-check',
                    'dateColumn' => 'tanggal_service',
                    'dateFilter' => 'current_month'
                ],
                [
                    'label' => 'Service Pending',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_approval' => 'pending'],
                    'format' => 'number',
                    'color' => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon' => 'fa fa-clock'
                ]
            ]
        ];
    }

    /**
     * Chart config for Purchasero (Pengadaan) page
     */
    protected function getPurchaseroConfig(): array
    {
        return [
            'dateColumn' => 'tanggal',
            'pie' => [
                'title'       => 'Distribusi Status',
                'groupBy'     => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels'      => [],
                'colors'      => ['#3b82f6', '#f59e0b', '#10b981', '#ef4444'],
            ],
            'bar' => [
                'title'        => 'Nominal Pengadaan per Bulan',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => ['nominal'],
                'aggregation'  => 'sum',
                'dateColumn'   => 'tanggal',
                'limit'        => 6,
                'labels'       => ['Nominal'],
                'colors'       => ['#3b82f6'],
            ],
            'line' => [
                'title'       => 'Trend Nominal Pengadaan',
                'groupBy'     => 'month',
                'valueColumn' => 'nominal',
                'aggregation' => 'sum',
                'dateColumn'  => 'tanggal',
                'limit'       => 12,
                'label'       => 'Nominal',
                'color'       => '#8b5cf6',
            ],
            'stats' => [
                [
                    'label'  => 'Total Pengadaan',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#3b82f6',
                    'iconBg' => '#dbeafe',
                    'icon'   => 'fa fa-shopping-cart',
                ],
                [
                    'label'  => 'Total Nominal',
                    'type'   => 'sum',
                    'column' => 'nominal',
                    'format' => 'currency',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-money-bill-wave',
                ],
                [
                    'label'  => 'Disetujui',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status' => 'Disetujui'],
                    'format' => 'number',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-check-circle',
                ],
                [
                    'label'  => 'Pending',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status' => 'Pending'],
                    'format' => 'number',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-clock',
                ],
            ],
        ];
    }

    /**
     * Chart config for Kendaraan Index page (per-merk grouping)
     */
    protected function getKendaraanConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie' => [
                'title' => 'Distribusi Status Kendaraan',
                'groupBy' => 'status_kendaraan',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [
                    'tersedia'   => 'Tersedia',
                    'disewa'     => 'Disewa',
                    'service'    => 'Service',
                    'bermasalah' => 'Bermasalah',
                ],
                'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
            ],
            'bar' => [
                'title' => 'Jumlah Kendaraan per Merk',
                'groupBy' => 'merk',
                'valueColumns' => ['id'],
                'aggregation' => 'count',
                'dateColumn' => 'created_at',
                'limit' => 10,
                'labels' => ['Unit'],
                'colors' => ['#4f6ef7'],
            ],
            'line' => [
                'title' => 'Trend Penambahan Kendaraan',
                'groupBy' => 'month',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'dateColumn' => 'created_at',
                'limit' => 12,
                'label' => 'Kendaraan Baru',
                'color' => '#8b5cf6',
            ],
            'stats' => [
                [
                    'label' => 'Total Kendaraan',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-car',
                ],
                [
                    'label' => 'Tersedia',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_kendaraan' => 'tersedia'],
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle',
                ],
                [
                    'label' => 'Disewa',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_kendaraan' => 'disewa'],
                    'format' => 'number',
                    'color' => '#3b82f6',
                    'iconBg' => '#dbeafe',
                    'icon' => 'fa fa-key',
                ],
                [
                    'label' => 'Bermasalah',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_kendaraan' => 'bermasalah'],
                    'format' => 'number',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-exclamation-triangle',
                ],
            ],
        ];
    }

    /**
     * Chart config for Kendaraan Show page (per-unit dari merk tertentu)
     */
    protected function getKendaraanShowConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_service',
            'pie' => [
                'title'       => 'Biaya per Status Service',
                'groupBy'     => 'status',
                'valueColumn' => 'total_biaya',
                'aggregation' => 'sum',
                'labels'      => [
                    'proses'  => 'Proses',
                    'selesai' => 'Selesai',
                    'limit'   => 'Limit',
                ],
                'colors' => ['#f59e0b', '#10b981', '#ef4444'],
            ],
            'bar' => [
                'title'        => 'Biaya Service Aktual per Bulan',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => [
                    'total_biaya',
                    'maks_bulanan',
                    ['computed' => ['op' => 'subtract', 'a' => 'maks_bulanan', 'b' => 'total_biaya']],
                ],
                'aggregation'  => 'sum',
                'dateColumn'   => 'tanggal_service',
                'limit'        => 6,
                'labels'       => ['Biaya Aktual', 'Limit', 'Sisa'],
                'colors'       => ['#ef4444', '#f59e0b', '#10b981'],
                'format'       => 'currency',
            ],
            'line' => [
                'title'       => 'Trend Biaya Service',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'total_biaya',
                'aggregation' => 'sum',
                'dateColumn'  => 'tanggal_service',
                'limit'       => 12,
                'label'       => 'Biaya Aktual',
                'color'       => '#ef4444',
                'format'      => 'currency',
            ],
            'stats' => [
                [
                    'label'  => 'Total Service',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon'   => 'fa fa-wrench',
                ],
                [
                    'label'  => 'Total Biaya',
                    'type'   => 'sum',
                    'column' => 'total_biaya',
                    'format' => 'currency',
                    'color'  => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon'   => 'fa fa-money-bill-wave',
                ],
                [
                    'label'  => 'Selesai',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status' => 'selesai'],
                    'format' => 'number',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-check-circle',
                ],
                [
                    'label'  => 'Proses',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status' => 'proses'],
                    'format' => 'number',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-rotate',
                ],
            ],
        ];
    }

    /**
     * Chart config for Asuransi Kendaraan page
     */
    protected function getAsuransiKendaraanConfig(): array
    {
        return [
            'dateColumn' => 'tgl_mulai',
            'pie' => [
                'title' => 'Distribusi Status Asuransi',
                'groupBy' => 'status_kendaraan',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [
                    'aktif'   => 'Aktif',
                    'expired' => 'Expired',
                ],
                'colors' => ['#10b981', '#ef4444'],
            ],
            'bar' => [
                'title' => 'Nominal Asuransi per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['biaya'],
                'aggregation' => 'sum',
                'dateColumn' => 'tgl_mulai',
                'limit' => 6,
                'labels' => ['Nominal'],
                'colors' => ['#4f6ef7'],
            ],
            'line' => [
                'title' => 'Trend Nominal Asuransi',
                'groupBy' => 'month',
                'valueColumn' => 'biaya',
                'aggregation' => 'sum',
                'dateColumn' => 'tgl_mulai',
                'limit' => 12,
                'label' => 'Total Nominal',
                'color' => '#4f6ef7',
            ],
            'stats' => [
                [
                    'label' => 'Total Asuransi',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon' => 'fa fa-shield-halved',
                ],
                [
                    'label' => 'Total Nominal',
                    'type' => 'sum',
                    'column' => 'biaya',
                    'format' => 'currency',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-money-bill-wave',
                ],
                [
                    'label' => 'Aktif',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_kendaraan' => 'aktif'],
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle',
                ],
                [
                    'label' => 'Expired',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_kendaraan' => 'expired'],
                    'format' => 'number',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-times-circle',
                ],
            ],
        ];
    }

    /**
     * Chart config for Pajak History list/detail page
     */
    protected function getPajakHistoryConfig(): array
    {
        return [
            'dateColumn' => 'diperpanjang_pada',
            'pie' => [
                'title'       => 'Distribusi Jenis Pajak',
                'groupBy'     => 'jenis_pajak',
                'valueColumn' => 'nominal',
                'aggregation' => 'sum',
                'labels'      => [],
                'colors'      => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
            ],
            'bar' => [
                'title'        => 'Biaya Pajak per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => ['nominal'],
                'aggregation'  => 'sum',
                'dateColumn'   => 'diperpanjang_pada',
                'limit'        => 12,
                'labels'       => ['Nominal Pajak'],
                'colors'       => ['#4f6ef7'],
            ],
            'line' => [
                'title'       => 'Trend Biaya Pajak',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'nominal',
                'aggregation' => 'sum',
                'dateColumn'  => 'diperpanjang_pada',
                'limit'       => 12,
                'label'       => 'Nominal',
                'color'       => '#4f6ef7',
            ],
            'stats' => [
                [
                    'label'  => 'Total Perpanjangan',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon'   => 'fa fa-file-invoice-dollar',
                ],
                [
                    'label'  => 'Total Biaya',
                    'type'   => 'sum',
                    'column' => 'nominal',
                    'format' => 'currency',
                    'color'  => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon'   => 'fa fa-money-bill-wave',
                ],
                [
                    'label'  => 'Rata-rata Biaya',
                    'type'   => 'avg',
                    'column' => 'nominal',
                    'format' => 'currency',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-calculator',
                ],
                [
                    'label'  => 'Kendaraan Terdaftar',
                    'type'   => 'count',
                    'column' => 'kendaraan_id',
                    'format' => 'number',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-car',
                ],
            ],
        ];
    }

    /**
     * Chart config for Asuransi History list/detail page
     */
    protected function getAsuransiHistoryConfig(): array
    {
        return [
            'dateColumn' => 'diperpanjang_pada',
            'pie' => [
                'title'       => 'Distribusi Status Asuransi',
                'groupBy'     => 'status_kendaraan',
                'valueColumn' => 'biaya',
                'aggregation' => 'sum',
                'labels'      => [
                    'aktif'   => 'Aktif',
                    'expired' => 'Expired',
                ],
                'colors'      => ['#10b981', '#ef4444'],
            ],
            'bar' => [
                'title'        => 'Biaya Asuransi per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => ['biaya'],
                'aggregation'  => 'sum',
                'dateColumn'   => 'diperpanjang_pada',
                'limit'        => 12,
                'labels'       => ['Biaya Asuransi'],
                'colors'       => ['#3b82f6'],
            ],
            'line' => [
                'title'       => 'Trend Biaya Asuransi',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'biaya',
                'aggregation' => 'sum',
                'dateColumn'  => 'diperpanjang_pada',
                'limit'       => 12,
                'label'       => 'Biaya',
                'color'       => '#3b82f6',
            ],
            'stats' => [
                [
                    'label'  => 'Total Perpanjangan',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon'   => 'fa fa-shield-halved',
                ],
                [
                    'label'  => 'Total Biaya',
                    'type'   => 'sum',
                    'column' => 'biaya',
                    'format' => 'currency',
                    'color'  => '#3b82f6',
                    'iconBg' => '#dbeafe',
                    'icon'   => 'fa fa-money-bill-wave',
                ],
                [
                    'label'  => 'Rata-rata Biaya',
                    'type'   => 'avg',
                    'column' => 'biaya',
                    'format' => 'currency',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-calculator',
                ],
                [
                    'label'  => 'Kendaraan Terdaftar',
                    'type'   => 'count',
                    'column' => 'kendaraan_id',
                    'format' => 'number',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-car',
                ],
            ],
        ];
    }

    /**
     * Chart config for KIR History list/detail page
     */
    protected function getKirHistoryConfig(): array
    {
        return [
            'dateColumn' => 'diperpanjang_pada',
            'pie' => [
                'title'       => 'Distribusi Status KIR',
                'groupBy'     => 'no_uji',
                'valueColumn' => 'biaya',
                'aggregation' => 'sum',
                'labels'      => [],
                'colors'      => ['#f59e0b', '#3b82f6', '#10b981', '#8b5cf6'],
            ],
            'bar' => [
                'title'        => 'Biaya KIR per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => ['biaya'],
                'aggregation'  => 'sum',
                'dateColumn'   => 'diperpanjang_pada',
                'limit'        => 12,
                'labels'       => ['Biaya KIR'],
                'colors'       => ['#f59e0b'],
            ],
            'line' => [
                'title'       => 'Trend Biaya KIR',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'biaya',
                'aggregation' => 'sum',
                'dateColumn'  => 'diperpanjang_pada',
                'limit'       => 12,
                'label'       => 'Biaya',
                'color'       => '#f59e0b',
            ],
            'stats' => [
                [
                    'label'  => 'Total Perpanjangan',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon'   => 'fa fa-clipboard-check',
                ],
                [
                    'label'  => 'Total Biaya',
                    'type'   => 'sum',
                    'column' => 'biaya',
                    'format' => 'currency',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-money-bill-wave',
                ],
                [
                    'label'  => 'Rata-rata Biaya',
                    'type'   => 'avg',
                    'column' => 'biaya',
                    'format' => 'currency',
                    'color'  => '#8b5cf6',
                    'iconBg' => '#ede9fe',
                    'icon'   => 'fa fa-calculator',
                ],
                [
                    'label'  => 'Kendaraan Terdaftar',
                    'type'   => 'count',
                    'column' => 'kendaraan_id',
                    'format' => 'number',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-car',
                ],
            ],
        ];
    }

    /**
     * Chart config for GPS Kendaraan History list/detail page
     */
    protected function getGpsKendaraanHistoryConfig(): array
    {
        return [
            'dateColumn' => 'diperpanjang_pada',
            'pie' => [
                'title'       => 'Distribusi Type GPS',
                'groupBy'     => 'type',
                'valueColumn' => 'biaya_sewa',
                'aggregation' => 'sum',
                'labels'      => [],
                'colors'      => ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
            ],
            'bar' => [
                'title'        => 'Biaya GPS per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => ['biaya_sewa'],
                'aggregation'  => 'sum',
                'dateColumn'   => 'diperpanjang_pada',
                'limit'        => 12,
                'labels'       => ['Biaya Sewa GPS'],
                'colors'       => ['#6366f1'],
            ],
            'line' => [
                'title'       => 'Trend Biaya GPS',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'biaya_sewa',
                'aggregation' => 'sum',
                'dateColumn'  => 'diperpanjang_pada',
                'limit'       => 12,
                'label'       => 'Biaya Sewa',
                'color'       => '#6366f1',
            ],
            'stats' => [
                [
                    'label'  => 'Total Perpanjangan',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#6366f1',
                    'iconBg' => '#ede9fe',
                    'icon'   => 'fa fa-satellite-dish',
                ],
                [
                    'label'  => 'Total Biaya',
                    'type'   => 'sum',
                    'column' => 'biaya_sewa',
                    'format' => 'currency',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-money-bill-wave',
                ],
                [
                    'label'  => 'Rata-rata Biaya',
                    'type'   => 'avg',
                    'column' => 'biaya_sewa',
                    'format' => 'currency',
                    'color'  => '#8b5cf6',
                    'iconBg' => '#ede9fe',
                    'icon'   => 'fa fa-calculator',
                ],
                [
                    'label'  => 'Kendaraan Terdaftar',
                    'type'   => 'count',
                    'column' => 'kendaraan_id',
                    'format' => 'number',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-car',
                ],
            ],
        ];
    }

    /**
     * Chart config for GPS Kendaraan page
     */
    protected function getGpsKendaraanConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_pasang',
            'pie' => [
                'title' => 'Distribusi Status GPS',
                'groupBy' => 'status_gps',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels' => [
                    'aktif'    => 'Aktif',
                    'nonaktif' => 'Nonaktif',
                ],
                'colors' => ['#10b981', '#ef4444'],
            ],
            'bar' => [
                'title' => 'Biaya GPS per Bulan',
                'groupBy' => 'month',
                'valueColumns' => ['biaya_sewa'],
                'aggregation' => 'sum',
                'dateColumn' => 'tanggal_pasang',
                'limit' => 6,
                'labels' => ['Biaya Sewa'],
                'colors' => ['#6366f1'],
            ],
            'line' => [
                'title' => 'Trend GPS Aktif',
                'groupBy' => 'month',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'dateColumn' => 'tanggal_pasang',
                'limit' => 12,
                'label' => 'Jumlah GPS',
                'color' => '#6366f1',
            ],
            'stats' => [
                [
                    'label' => 'Total GPS',
                    'type' => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color' => '#6366f1',
                    'iconBg' => '#ede9fe',
                    'icon' => 'fa fa-satellite-dish',
                ],
                [
                    'label' => 'Total Biaya',
                    'type' => 'sum',
                    'column' => 'biaya_sewa',
                    'format' => 'currency',
                    'color' => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon' => 'fa fa-wallet',
                ],
                [
                    'label' => 'Aktif',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_gps' => 'aktif'],
                    'format' => 'number',
                    'color' => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon' => 'fa fa-check-circle',
                ],
                [
                    'label' => 'Nonaktif',
                    'type' => 'count_where',
                    'column' => 'id',
                    'where' => ['status_gps' => 'nonaktif'],
                    'format' => 'number',
                    'color' => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon' => 'fa fa-times-circle',
                ],
            ],
        ];
    }

    protected function getMemberConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie'  => ['title' => 'Jenis Member', 'groupBy' => 'jenis_member', 'valueColumn' => 'id', 'aggregation' => 'count', 'labels' => ['perorangan' => 'Perorangan', 'perusahaan' => 'Perusahaan'], 'colors' => ['#4f6ef7', '#10b981']],
            'bar'  => ['title' => 'Pendaftaran Member per Periode', 'groupBy' => 'month', 'valueColumns' => ['id'], 'aggregation' => 'count', 'dateColumn' => 'created_at', 'limit' => 12, 'labels' => ['Jumlah Member'], 'colors' => ['#4f6ef7'], 'format' => 'number'],
            'line' => ['title' => 'Trend Member', 'groupBy' => 'month', 'valueColumn' => 'id', 'aggregation' => 'count', 'dateColumn' => 'created_at', 'limit' => 12, 'label' => 'Member', 'color' => '#4f6ef7', 'format' => 'number'],
            'stats' => [
                ['label' => 'Total Member',   'type' => 'count',               'column' => 'id', 'format' => 'number', 'color' => '#4f6ef7', 'iconBg' => '#eef1ff', 'icon' => 'fa fa-users'],
                ['label' => 'Perorangan',     'type' => 'count_where',         'column' => 'id', 'where' => ['jenis_member' => 'perorangan'], 'format' => 'number', 'color' => '#10b981', 'iconBg' => '#d1fae5', 'icon' => 'fa fa-user'],
                ['label' => 'Perusahaan',     'type' => 'count_where',         'column' => 'id', 'where' => ['jenis_member' => 'perusahaan'], 'format' => 'number', 'color' => '#f59e0b', 'iconBg' => '#fef3c7', 'icon' => 'fa fa-building'],
                ['label' => 'Baru Bulan Ini', 'type' => 'count_current_month', 'dateColumn' => 'created_at', 'format' => 'number', 'color' => '#8b5cf6', 'iconBg' => '#ede9fe', 'icon' => 'fa fa-user-plus'],
            ],
        ];
    }

    protected function getPelangganConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie'  => ['title' => 'Jenis Pelanggan', 'groupBy' => 'jenis_pelanggan', 'valueColumn' => 'id', 'aggregation' => 'count', 'labels' => ['perorangan' => 'Perorangan', 'perusahaan' => 'Perusahaan'], 'colors' => ['#3b82f6', '#f59e0b']],
            'bar'  => ['title' => 'Pendaftaran Pelanggan per Periode', 'groupBy' => 'month', 'valueColumns' => ['id'], 'aggregation' => 'count', 'dateColumn' => 'created_at', 'limit' => 12, 'labels' => ['Jumlah Pelanggan'], 'colors' => ['#3b82f6'], 'format' => 'number'],
            'line' => ['title' => 'Trend Pelanggan', 'groupBy' => 'month', 'valueColumn' => 'id', 'aggregation' => 'count', 'dateColumn' => 'created_at', 'limit' => 12, 'label' => 'Pelanggan', 'color' => '#3b82f6', 'format' => 'number'],
            'stats' => [
                ['label' => 'Total Pelanggan', 'type' => 'count',               'column' => 'id', 'format' => 'number', 'color' => '#3b82f6', 'iconBg' => '#dbeafe', 'icon' => 'fa fa-users'],
                ['label' => 'Perorangan',      'type' => 'count_where',         'column' => 'id', 'where' => ['jenis_pelanggan' => 'perorangan'], 'format' => 'number', 'color' => '#10b981', 'iconBg' => '#d1fae5', 'icon' => 'fa fa-user'],
                ['label' => 'Perusahaan',      'type' => 'count_where',         'column' => 'id', 'where' => ['jenis_pelanggan' => 'perusahaan'], 'format' => 'number', 'color' => '#f59e0b', 'iconBg' => '#fef3c7', 'icon' => 'fa fa-building'],
                ['label' => 'Baru Bulan Ini',  'type' => 'count_current_month', 'dateColumn' => 'created_at', 'format' => 'number', 'color' => '#8b5cf6', 'iconBg' => '#ede9fe', 'icon' => 'fa fa-user-plus'],
            ],
        ];
    }

    /**
     * Chart config for Penawaran page
     */
    protected function getPenawaranConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_penawaran',
            'pie' => [
                'title'       => 'Distribusi Status Penawaran',
                'ignoreFilter' => true,
                'groupBy'     => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels'      => [
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'active'   => 'Active',
                    'rejected' => 'Rejected',
                    'expired'  => 'Expired',
                ],
                'colors' => ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#6b7280'],
            ],
            'bar' => [
                'title'        => 'Jumlah Penawaran per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'format'       => 'number',
                'valueColumns' => [
                    ['count' => true, 'label' => 'Total'],
                    ['where' => ['status' => 'pending'],  'column' => 'id', 'label' => 'Pending',  'aggregation' => 'count'],
                    ['where' => ['status' => 'approved'], 'column' => 'id', 'label' => 'Approved', 'aggregation' => 'count'],
                    ['where' => ['status' => 'expired'],  'column' => 'id', 'label' => 'Expired',  'aggregation' => 'count'],
                    ['where' => ['status' => 'rejected'], 'column' => 'id', 'label' => 'Rejected', 'aggregation' => 'count'],
                ],
                'aggregation'  => 'count',
                'dateColumn'   => 'tanggal_penawaran',
                'limit'        => 12,
                'labels'       => ['Total', 'Pending', 'Approved', 'Expired', 'Rejected'],
                'colors'       => ['#4f6ef7', '#f59e0b', '#10b981', '#6b7280', '#ef4444'],
            ],
            'line' => [
                'title'       => 'Trend Penawaran',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'format'      => 'number',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'dateColumn'  => 'tanggal_penawaran',
                'limit'       => 12,
                'label'       => 'Penawaran',
                'color'       => '#4f6ef7',
            ],
            'stats' => [
                [
                    'label'  => 'Total Penawaran',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon'   => 'fa fa-file-alt',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'tanggal_penawaran',
                ],
                [
                    'label'  => 'Approved',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status' => 'approved'],
                    'format' => 'number',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-check-circle',
                ],
                [
                    'label'  => 'Pending',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status' => 'pending'],
                    'format' => 'number',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-clock',
                ],
                [
                    'label'  => 'Expired / Rejected',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status' => 'expired'],
                    'format' => 'number',
                    'color'  => '#ef4444',
                    'iconBg' => '#fee2e2',
                    'icon'   => 'fa fa-times-circle',
                ],
            ],
        ];
    }

    /**
     * Chart config for Kontrak page
     */
    protected function getKontrakConfig(): array
    {
        return [
            'dateColumn' => 'tanggal_kontrak',
            'pie' => [
                'title'       => 'Distribusi Status Kontrak',
                'ignoreFilter' => true,
                'groupBy'     => 'status',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels'      => [
                    'pending'             => 'Pending',
                    'approved'            => 'Approved',
                    'active'              => 'Active',
                    'completed'           => 'Completed',
                    'selesai-belum lunas' => 'Selesai-Belum Lunas',
                    'rejected'            => 'Rejected',
                    'expired'             => 'Expired',
                    'terminated'          => 'Terminated',
                ],
                'colors' => ['#f59e0b', '#6366f1', '#10b981', '#3b82f6', '#f97316', '#ef4444', '#6b7280', '#1e293b'],
            ],
            'bar' => [
                'title'        => 'Jumlah Kontrak per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'format'       => 'number',
                'valueColumns' => [
                    ['count' => true, 'label' => 'Total'],
                    ['where' => ['status' => 'active'],   'column' => 'id', 'label' => 'Active',   'aggregation' => 'count'],
                    ['where' => ['status' => 'pending'],  'column' => 'id', 'label' => 'Pending',  'aggregation' => 'count'],
                    ['where' => ['status' => 'expired'],  'column' => 'id', 'label' => 'Expired',  'aggregation' => 'count'],
                ],
                'aggregation'  => 'count',
                'dateColumn'   => 'tanggal_kontrak',
                'limit'        => 12,
                'labels'       => ['Total', 'Active', 'Pending', 'Expired'],
                'colors'       => ['#6366f1', '#10b981', '#f59e0b', '#6b7280'],
            ],
            'line' => [
                'title'       => 'Trend Kontrak',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'format'      => 'number',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'dateColumn'  => 'tanggal_kontrak',
                'limit'       => 12,
                'label'       => 'Kontrak',
                'color'       => '#6366f1',
            ],
            'stats' => [
                [
                    'label'  => 'Total Kontrak',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#6366f1',
                    'iconBg' => '#ede9fe',
                    'icon'   => 'fa fa-file-contract',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'tanggal_kontrak',
                ],
                [
                    'label'  => 'Active',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status' => 'active'],
                    'format' => 'number',
                    'color'  => '#10b981',
                    'iconBg' => '#d1fae5',
                    'icon'   => 'fa fa-check-circle',
                ],
                [
                    'label'  => 'Pending',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status' => 'pending'],
                    'format' => 'number',
                    'color'  => '#f59e0b',
                    'iconBg' => '#fef3c7',
                    'icon'   => 'fa fa-clock',
                ],
                [
                    'label'  => 'Selesai-Belum Lunas',
                    'type'   => 'count_where',
                    'column' => 'id',
                    'where'  => ['status' => 'selesai-belum lunas'],
                    'format' => 'number',
                    'color'  => '#f97316',
                    'iconBg' => '#ffedd5',
                    'icon'   => 'fa fa-exclamation-triangle',
                ],
            ],
        ];
    }

    /**
     * Chart config for Jenis Asuransi master data page
     */
    protected function getJenisAsuransiConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie' => [
                'title'       => 'Distribusi Jenis Asuransi',
                'groupBy'     => 'nama_jenis',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels'      => [],
                'colors'      => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
            ],
            'bar' => [
                'title'        => 'Penambahan Jenis Asuransi per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => [['count' => true, 'label' => 'Jumlah Jenis']],
                'aggregation'  => 'count',
                'dateColumn'   => 'created_at',
                'limit'        => 12,
                'labels'       => ['Jumlah Jenis'],
                'colors'       => ['#4f6ef7'],
            ],
            'line' => [
                'title'       => 'Trend Jenis Asuransi',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'dateColumn'  => 'created_at',
                'limit'       => 12,
                'label'       => 'Total',
                'color'       => '#4f6ef7',
            ],
            'stats' => [
                [
                    'label'  => 'Total Jenis Asuransi',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon'   => 'fa fa-tag',
                ],
            ],
        ];
    }

    /**
     * Chart config for Supplier master data page
     */
    protected function getSupplierConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie' => [
                'title'       => 'Distribusi Supplier per Nama',
                'groupBy'     => 'nama_supplier',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels'      => [],
                'colors'      => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#f97316'],
            ],
            'bar' => [
                'title'        => 'Penambahan Supplier per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => ['id'],
                'aggregation'  => 'count',
                'dateColumn'   => 'created_at',
                'limit'        => 12,
                'labels'       => ['Jumlah Supplier'],
                'colors'       => ['#4f6ef7'],
                'format'       => 'number',
            ],
            'line' => [
                'title'       => 'Trend Penambahan Supplier',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'dateColumn'  => 'created_at',
                'limit'       => 12,
                'label'       => 'Supplier Baru',
                'color'       => '#10b981',
                'format'      => 'number',
            ],
            'stats' => [
                [
                    'label'  => 'Total Supplier',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#4f6ef7',
                    'iconBg' => '#eef1ff',
                    'icon'   => 'fa fa-truck',
                ],
            ],
        ];
    }

    /**
     * Chart config for Jenis Kendaraan master data page
     */
    protected function getJenisKendaraanConfig(): array
    {
        return [
            'dateColumn' => 'created_at',
            'pie' => [
                'title'       => 'Distribusi Jenis Kendaraan',
                'groupBy'     => 'nama_jenis',
                'valueColumn' => 'id',
                'aggregation' => 'count',
                'labels'      => [],
                'colors'      => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
            ],
            'bar' => [
                'title'        => 'Penambahan Jenis Kendaraan per Periode',
                'groupBy'      => 'month',
                'autoDaily'    => true,
                'valueColumns' => [['count' => true, 'label' => 'Jumlah Jenis']],
                'aggregation'  => 'count',
                'dateColumn'   => 'created_at',
                'limit'        => 12,
                'labels'       => ['Jumlah Jenis'],
                'colors'       => ['#3b82f6'],
            ],
            'line' => [
                'title'       => 'Trend Jenis Kendaraan',
                'groupBy'     => 'month',
                'autoDaily'   => true,
                'valueColumn' => '_count',
                'aggregation' => 'count',
                'dateColumn'  => 'created_at',
                'limit'       => 12,
                'label'       => 'Total',
                'color'       => '#3b82f6',
            ],
            'stats' => [
                [
                    'label'  => 'Total Jenis Kendaraan',
                    'type'   => 'count',
                    'column' => 'id',
                    'format' => 'number',
                    'color'  => '#3b82f6',
                    'iconBg' => '#dbeafe',
                    'icon'   => 'fa fa-car',
                    'trendComparison' => 'last_month',
                    'dateColumn' => 'created_at',
                ],
            ],
        ];
    }

}