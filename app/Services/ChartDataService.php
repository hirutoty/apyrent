<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Chart Data Service
 * Service untuk agregasi dan formatting data untuk charts
 */
class ChartDataService
{
    /**
     * Get data for Pie Chart
     * 
     * @param Builder $query Base query
     * @param array $config Configuration for pie chart
     * @return array Chart data in format: ['labels' => [], 'datasets' => []]
     */
    public function getPieChartData(Builder $query, array $config)
    {
        $groupBy = $config['groupBy'] ?? 'status';
        $valueColumn = $config['valueColumn'] ?? 'id';
        $aggregation = $config['aggregation'] ?? 'count'; // count, sum, avg, custom
        $labels = $config['labels'] ?? [];
        $colors = $config['colors'] ?? [];

        // Handle custom aggregation for Keuangan (Pemasukan vs Pengeluaran)
        if ($aggregation === 'custom' && $groupBy === 'jenis') {
            $pemasukan = $query->sum('pemasukan');
            $pengeluaran = $query->sum('pengeluaran');

            return [
                'labels' => ['Pemasukan', 'Pengeluaran'],
                'datasets' => [
                    [
                        'data' => [(float) $pemasukan, (float) $pengeluaran],
                        'backgroundColor' => $colors
                    ]
                ]
            ];
        }

        // Build aggregation query
        $results = $query
            ->select(
                $groupBy,
                DB::raw($this->getAggregationFunction($aggregation, $valueColumn) . ' as value')
            )
            ->groupBy($groupBy)
            ->orderBy('value', 'desc')
            ->get();

        // Format data
        $chartLabels = [];
        $chartData = [];

        foreach ($results as $result) {
            $label = $labels[$result->$groupBy] ?? $result->$groupBy;
            $chartLabels[] = $label;
            $chartData[] = (float) $result->value;
        }

        return [
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'data' => $chartData,
                    'backgroundColor' => $colors
                ]
            ]
        ];
    }

    /**
     * Get data for Bar Chart
     * 
     * @param Builder $query Base query
     * @param array $config Configuration for bar chart
     * @return array Chart data
     */
    public function getBarChartData(Builder $query, array $config)
    {
        $groupBy      = $config['groupBy'] ?? 'month';
        $valueColumns = $config['valueColumns'] ?? ['amount'];
        $aggregation  = $config['aggregation'] ?? 'sum';
        $dateColumn   = $config['dateColumn'] ?? 'created_at';
        $limit        = $config['limit'] ?? 12;
        $labels       = $config['labels'] ?? [];
        $colors       = $config['colors'] ?? [];
        $filterType   = $config['filter_type'] ?? null;
        $autoDaily    = $config['autoDaily'] ?? true; // default true agar semua chart responsive terhadap filter_type

        // Auto-switch ke daily per tanggal saat filter bulan ini
        if ($autoDaily && $filterType === 'today') {
            $data = $this->getTodayBarData($query, $valueColumns, $dateColumn, $labels);
        } elseif ($autoDaily && $filterType === 'week') {
            $data = $this->getCurrentWeekDailyBarData($query, $valueColumns, $dateColumn, $labels);
        } elseif ($autoDaily && $filterType === 'month' && $groupBy === 'month') {
            $data = $this->getCurrentMonthDailyBarData($query, $valueColumns, $dateColumn, $labels);
        } elseif ($autoDaily && $filterType === 'year' && $groupBy === 'month') {
            $data = $this->getCurrentYearMonthlyBarData($query, $valueColumns, $dateColumn, $labels);
        } elseif ($autoDaily && $filterType === 'custom' && !empty($config['start_date']) && !empty($config['end_date'])) {
            $data = $this->getCustomRangeBarData($query, $valueColumns, $dateColumn, $labels, $config['start_date'], $config['end_date']);
        } elseif ($groupBy === 'month') {
            $data = $this->getMonthlyBarData($query, $valueColumns, $aggregation, $dateColumn, $limit, $labels);
        } elseif ($groupBy === 'week') {
            $data = $this->getWeeklyBarData($query, $valueColumns, $aggregation, $dateColumn, $limit, $labels);
        } elseif ($groupBy === 'day') {
            $data = $this->getDailyBarData($query, $valueColumns, $aggregation, $dateColumn, $limit, $labels);
        } else {
            $data = $this->getCategoryBarData($query, $groupBy, $valueColumns, $aggregation, $labels);
        }

        // Pass colors to JS
        if (!empty($colors)) {
            $data['colors'] = $colors;
        }

        return $data;
    }

    /**
     * Get daily bar data for current month (tanggal 1 s/d akhir bulan)
     *
     * @param Builder $query  Query yang sudah difilter ke bulan ini
     * @param array   $valueColumns
     * @param string  $dateColumn
     * @param array   $labels
     * @return array
     */
    /**
     * Apply conditional where clauses to a query clone based on column config.
     * Supports: ['where' => ['col' => 'val']] and ['where' => ['col' => ['a','b']]] (whereIn).
     */
    private function applyColumnWhere(Builder $baseQuery, array $whereConditions): Builder
    {
        $q = clone $baseQuery;
        foreach ($whereConditions as $col => $val) {
            if (is_array($val)) {
                $q->whereIn($col, $val);
            } else {
                $q->where($col, $val);
            }
        }
        return $q;
    }

    /**
     * Resolve a single value from a query+column config for one period,
     * given an already date-scoped query clone.
     *
     * Supported column shapes:
     *   - string                            → sum(column)
     *   - ['count' => true]                 → count(*)
     *   - ['where'=>[...], 'column'=>'...'] → conditional sum
     *   - ['computed'=>['op','a','b']]      → a ± b
     */
    private function resolveColumnValue(Builder $periodQuery, $column): float
    {
        if (is_string($column)) {
            // If column contains SQL expression characters, use DB::raw
            if (str_contains($column, '*') || str_contains($column, '+') || str_contains($column, '-') || str_contains($column, '(')) {
                return (float)(clone $periodQuery)->selectRaw("SUM($column) as _val")->value('_val');
            }
            return (float)(clone $periodQuery)->sum($column);
        }

        // Support raw SQL expression: ['expr' => 'jumlah_barang * harga_barang']
        if (isset($column['expr'])) {
            $expr = $column['expr'];
            return (float)(clone $periodQuery)->selectRaw("SUM($expr) as _val")->value('_val');
        }

        // Support count per period
        if (isset($column['count']) && $column['count'] === true) {
            return (float)(clone $periodQuery)->count();
        }

        if (isset($column['where'])) {
            $col = $column['column'] ?? 'id';
            $agg = $column['aggregation'] ?? 'sum';
            $q   = $this->applyColumnWhere(clone $periodQuery, $column['where']);
            return $agg === 'count' ? (float)$q->count() : (float)$q->sum($col);
        }

        if (isset($column['computed'])) {
            $op           = $column['computed']['op'] ?? 'subtract';
            $allowNeg     = $column['computed']['allowNegative'] ?? false;
            $valA         = (float)(clone $periodQuery)->sum($column['computed']['a']);
            $valB         = (float)(clone $periodQuery)->sum($column['computed']['b']);
            $result       = $op === 'subtract' ? $valA - $valB : $valA + $valB;
            return ($op === 'subtract' && !$allowNeg) ? max(0, $result) : $result;
        }

        return 0.0;
    }

    /**
     * Get label fallback for a column config entry.
     */
    private function columnLabel($column, int $index, array $labels): string
    {
        if (isset($labels[$index])) return $labels[$index];
        if (is_array($column)) return $column['label'] ?? 'Dataset';
        return ucfirst($column);
    }

    private function getCurrentMonthDailyBarData($query, $valueColumns, $dateColumn, $labels)
    {
        $now         = Carbon::now();
        $daysInMonth = $now->daysInMonth;
        $year        = $now->year;
        $month       = $now->month;

        $datasets = [];

        foreach ($valueColumns as $index => $column) {
            $columnData = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date        = Carbon::create($year, $month, $d)->toDateString();
                $periodQuery = (clone $query)->whereDate($dateColumn, $date);
                $columnData[] = $this->resolveColumnValue($periodQuery, $column);
            }
            $datasets[] = [
                'label' => $this->columnLabel($column, $index, $labels),
                'data'  => $columnData,
            ];
        }

        return [
            'labels'   => range(1, $daysInMonth),
            'datasets' => $datasets,
        ];
    }

    /**
     * Get bar data untuk hari ini — satu label tanggal hari ini
     */
    private function getTodayBarData($query, $valueColumns, $dateColumn, $labels)
    {
        $today       = Carbon::now();
        $periodQuery = (clone $query)->whereDate($dateColumn, $today->toDateString());

        $datasets = [];
        foreach ($valueColumns as $index => $column) {
            $datasets[] = [
                'label' => $this->columnLabel($column, $index, $labels),
                'data'  => [$this->resolveColumnValue($periodQuery, $column)],
            ];
        }

        return [
            'labels'   => [$today->format('d M Y')],
            'datasets' => $datasets,
        ];
    }

    /**
     * Get bar data untuk minggu ini — Sen s/d Min (7 hari)
     */
    private function getCurrentWeekDailyBarData($query, $valueColumns, $dateColumn, $labels)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        $periodLabels = [];
        $periods      = [];
        $current = $startOfWeek->copy();
        while ($current->lte($endOfWeek)) {
            $periodLabels[] = $current->format('D, d M Y');
            $periods[]      = $current->toDateString();
            $current->addDay();
        }

        $datasets = [];
        foreach ($valueColumns as $index => $column) {
            $columnData = [];
            foreach ($periods as $date) {
                $periodQuery  = (clone $query)->whereDate($dateColumn, $date);
                $columnData[] = $this->resolveColumnValue($periodQuery, $column);
            }
            $datasets[] = [
                'label' => $this->columnLabel($column, $index, $labels),
                'data'  => $columnData,
            ];
        }

        return [
            'labels'   => $periodLabels,
            'datasets' => $datasets,
        ];
    }

    /**
     * @param Builder $query  Query yang sudah difilter ke tahun ini
     */
    private function getCurrentYearMonthlyBarData($query, $valueColumns, $dateColumn, $labels)
    {
        $year       = Carbon::now()->year;
        $monthNames = [
            "Jan $year","Feb $year","Mar $year","Apr $year",
            "Mei $year","Jun $year","Jul $year","Agu $year",
            "Sep $year","Okt $year","Nov $year","Des $year",
        ];

        $datasets = [];
        foreach ($valueColumns as $index => $column) {
            $columnData = [];
            for ($m = 1; $m <= 12; $m++) {
                $periodQuery  = (clone $query)->whereMonth($dateColumn, $m)->whereYear($dateColumn, $year);
                $columnData[] = $this->resolveColumnValue($periodQuery, $column);
            }
            $datasets[] = [
                'label' => $this->columnLabel($column, $index, $labels),
                'data'  => $columnData,
            ];
        }

        return [
            'labels'   => $monthNames,
            'datasets' => $datasets,
        ];
    }

    /**
     * Get bar data untuk custom date range — selalu per tanggal (scrollable di frontend)
     */
    private function getCustomRangeBarData($query, $valueColumns, $dateColumn, $labels, $startDateStr, $endDateStr)
    {
        $start = Carbon::createFromFormat('d-m-Y', $startDateStr)->startOfDay();
        $end   = Carbon::createFromFormat('d-m-Y', $endDateStr)->endOfDay();

        $periodLabels = [];
        $periods      = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $periodLabels[] = $current->format('d M Y');
            $periods[]      = $current->toDateString();
            $current->addDay();
        }

        $datasets = [];
        foreach ($valueColumns as $index => $column) {
            $columnData = [];
            foreach ($periods as $date) {
                $periodQuery  = (clone $query)->whereDate($dateColumn, $date);
                $columnData[] = $this->resolveColumnValue($periodQuery, $column);
            }
            $datasets[] = [
                'label' => $this->columnLabel($column, $index, $labels),
                'data'  => $columnData,
            ];
        }

        return ['labels' => $periodLabels, 'datasets' => $datasets];
    }

    /**
     * Get data for Line Chart
     * 
     * @param Builder $query Base query
     * @param array $config Configuration for line chart
     * @return array Chart data
     */
    public function getLineChartData(Builder $query, array $config)
    {
        $groupBy     = $config['groupBy'] ?? 'month';
        $valueColumn = $config['valueColumn'] ?? 'amount';
        $aggregation = $config['aggregation'] ?? 'sum';
        $dateColumn  = $config['dateColumn'] ?? 'created_at';
        $limit       = $config['limit'] ?? 12;
        $label       = $config['label'] ?? 'Trend';
        $color       = $config['color'] ?? '#8b5cf6';
        $autoDaily   = $config['autoDaily'] ?? true; // default true agar semua chart responsive terhadap filter_type
        $filterType  = $config['filter_type'] ?? null;
        $startDate   = $config['start_date'] ?? null;
        $endDate     = $config['end_date'] ?? null;

        // Auto-switch groupBy tergantung filter_type (sama seperti bar)
        if ($autoDaily && $filterType === 'today') {
            $data = $this->getTodayLineTrendData($query, $valueColumn, $dateColumn);
        } elseif ($autoDaily && $filterType === 'week') {
            $data = $this->getCurrentWeekDailyLineTrendData($query, $valueColumn, $dateColumn);
        } elseif ($autoDaily && $filterType === 'month' && $groupBy === 'month') {
            $data = $this->getCurrentMonthDailyLineTrendData($query, $valueColumn, $dateColumn);
        } elseif ($autoDaily && $filterType === 'year' && $groupBy === 'month') {
            $data = $this->getCurrentYearMonthlyLineTrendData($query, $valueColumn, $dateColumn);
        } elseif ($autoDaily && $filterType === 'custom' && $startDate && $endDate) {
            $data = $this->getCustomRangeLineTrendData($query, $valueColumn, $dateColumn, $startDate, $endDate);
        } elseif ($groupBy === 'month') {
            $data = $this->getMonthlyTrendData($query, $valueColumn, $aggregation, $dateColumn, $limit);
        } elseif ($groupBy === 'week') {
            $data = $this->getWeeklyTrendData($query, $valueColumn, $aggregation, $dateColumn, $limit);
        } else {
            $data = $this->getDailyTrendData($query, $valueColumn, $aggregation, $dateColumn, $limit);
        }

        return [
            'labels' => $data['labels'],
            'datasets' => [
                [
                    'label'           => $label,
                    'data'            => $data['values'],
                    'borderColor'     => $color,
                    'backgroundColor' => $this->hexToRgba($color, 0.1)
                ]
            ]
        ];
    }

    // ── Line autoDaily helpers ─────────────────────────────────────────────────

    /**
     * Resolve a single scalar value for line chart per period.
     * Handles count shortcuts, raw SQL expressions, and plain column sums.
     */
    private function resolveLineValue(Builder $periodQuery, string $valueColumn): float
    {
        if ($valueColumn === 'id' || $valueColumn === '_count') {
            return (float)(clone $periodQuery)->count();
        }
        // Detect SQL expression (contains *, +, -, or parentheses)
        if (str_contains($valueColumn, '*') || str_contains($valueColumn, '+')
            || str_contains($valueColumn, '(') || str_contains($valueColumn, '/')) {
            return (float)(clone $periodQuery)->selectRaw("SUM($valueColumn) as _val")->value('_val');
        }
        return (float)(clone $periodQuery)->sum($valueColumn);
    }

    /**
     * Line trend: hari ini — satu titik
     */
    private function getTodayLineTrendData($query, $valueColumn, $dateColumn): array
    {
        $today       = Carbon::now();
        $periodQuery = (clone $query)->whereDate($dateColumn, $today->toDateString());
        return [
            'labels' => [$today->format('d M Y')],
            'values' => [$this->resolveLineValue($periodQuery, $valueColumn)],
        ];
    }

    /**
     * Line trend: minggu ini — Sen s/d Min (7 titik)
     */
    private function getCurrentWeekDailyLineTrendData($query, $valueColumn, $dateColumn): array
    {
        $start  = Carbon::now()->startOfWeek();
        $end    = Carbon::now()->endOfWeek();
        $labels = [];
        $values = [];
        $cur    = $start->copy();
        while ($cur->lte($end)) {
            $labels[] = $cur->format('D, d M Y');
            $values[] = $this->resolveLineValue((clone $query)->whereDate($dateColumn, $cur->toDateString()), $valueColumn);
            $cur->addDay();
        }
        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Line trend: bulan ini — per tanggal (1 s/d akhir bulan)
     */
    private function getCurrentMonthDailyLineTrendData($query, $valueColumn, $dateColumn): array
    {
        $now         = Carbon::now();
        $daysInMonth = $now->daysInMonth;
        $labels      = [];
        $values      = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date     = Carbon::create($now->year, $now->month, $d)->toDateString();
            $labels[] = (string) $d;
            $values[] = $this->resolveLineValue((clone $query)->whereDate($dateColumn, $date), $valueColumn);
        }
        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Line trend: tahun ini — per bulan (12 titik)
     */
    private function getCurrentYearMonthlyLineTrendData($query, $valueColumn, $dateColumn): array
    {
        $year       = Carbon::now()->year;
        $monthNames = [
            "Jan $year","Feb $year","Mar $year","Apr $year",
            "Mei $year","Jun $year","Jul $year","Agu $year",
            "Sep $year","Okt $year","Nov $year","Des $year",
        ];
        $values = [];
        for ($m = 1; $m <= 12; $m++) {
            $values[] = $this->resolveLineValue(
                (clone $query)->whereMonth($dateColumn, $m)->whereYear($dateColumn, $year),
                $valueColumn
            );
        }
        return ['labels' => $monthNames, 'values' => $values];
    }

    /**
     * Line trend: custom range — per tanggal
     */
    private function getCustomRangeLineTrendData($query, $valueColumn, $dateColumn, $startDateStr, $endDateStr): array
    {
        $start  = Carbon::createFromFormat('d-m-Y', $startDateStr)->startOfDay();
        $end    = Carbon::createFromFormat('d-m-Y', $endDateStr)->endOfDay();
        $labels = [];
        $values = [];
        $cur    = $start->copy();
        while ($cur->lte($end)) {
            $labels[] = $cur->format('d M Y');
            $values[] = $this->resolveLineValue((clone $query)->whereDate($dateColumn, $cur->toDateString()), $valueColumn);
            $cur->addDay();
        }
        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Get statistics data
     * 
     * @param Builder $query Base query
     * @param array $config Configuration for stats
     * @return array Statistics data
     */
    public function getStatsData(Builder $query, array $config)
    {
        $stats = [];

        foreach ($config as $statConfig) {
            $value = $this->calculateStat($query, $statConfig);
            
            $stats[] = [
                'label' => $statConfig['label'],
                'value' => $this->formatValue($value, $statConfig['format'] ?? 'number'),
                'color' => $statConfig['color'] ?? '#4f6ef7',
                'iconBg' => $statConfig['iconBg'] ?? '#eef1ff',
                'icon' => $statConfig['icon'] ?? 'fa fa-chart-line',
                'trend' => $this->calculateTrend($query, $statConfig) ?? null,
                'badge' => $statConfig['badge'] ?? null,
                'badgeType' => $statConfig['badgeType'] ?? 'info'
            ];
        }

        return $stats;
    }

    /**
     * Apply date filter to query
     * 
     * @param Builder $query Query builder
     * @param string $filterType Filter type: today, week, month, year, custom
     * @param array $customDates Custom dates [start, end] for custom filter
     * @param string $dateColumn Date column name
     * @return Builder
     */
    public function applyDateFilter(Builder $query, string $filterType, array $customDates = [], string $dateColumn = 'created_at')
    {
        $now = Carbon::now();

        switch ($filterType) {
            case 'today':
                $query->whereDate($dateColumn, $now->toDateString());
                break;

            case 'week':
                $query->whereBetween($dateColumn, [
                    $now->startOfWeek()->toDateString(),
                    $now->endOfWeek()->toDateString()
                ]);
                break;

            case 'month':
                $query->whereMonth($dateColumn, $now->month)
                      ->whereYear($dateColumn, $now->year);
                break;

            case 'year':
                $query->whereYear($dateColumn, $now->year);
                break;

            case 'custom':
                if (!empty($customDates) && count($customDates) === 2) {
                    $startDate = Carbon::createFromFormat('d-m-Y', $customDates[0])->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', $customDates[1])->endOfDay();
                    $query->whereBetween($dateColumn, [$startDate, $endDate]);
                }
                break;
        }

        return $query;
    }

    /**
     * Get period label for display
     * 
     * @param string $filterType
     * @param array $customDates
     * @return string
     */
    public function getPeriodLabel(string $filterType, array $customDates = []): string
    {
        switch ($filterType) {
            case 'today':
                return 'Hari Ini - ' . Carbon::now()->format('d M Y');
            
            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                return 'Minggu Ini - ' . $start->format('d M') . ' s/d ' . $end->format('d M Y');
            
            case 'month':
                return 'Bulan Ini - ' . Carbon::now()->format('F Y');
            
            case 'year':
                return 'Tahun Ini - ' . Carbon::now()->format('Y');
            
            case 'custom':
                if (!empty($customDates) && count($customDates) === 2) {
                    return $customDates[0] . ' s/d ' . $customDates[1];
                }
                return 'Custom Period';
            
            default:
                return '';
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    private function getAggregationFunction(string $type, string $column): string
    {
        switch ($type) {
            case 'sum':
                return "SUM($column)";
            case 'avg':
                return "AVG($column)";
            case 'count':
            default:
                return "COUNT($column)";
        }
    }

    private function getMonthlyBarData($query, $valueColumns, $aggregation, $dateColumn, $limit, $labels)
    {
        $months = [];

        // Generate last N months labels
        for ($i = $limit - 1; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->format('M Y');
        }

        $datasets = [];
        foreach ($valueColumns as $index => $column) {
            $columnData = [];
            for ($i = $limit - 1; $i >= 0; $i--) {
                $date        = Carbon::now()->subMonths($i);
                $periodQuery = (clone $query)
                    ->whereMonth($dateColumn, $date->month)
                    ->whereYear($dateColumn, $date->year);
                $columnData[] = $this->resolveColumnValue($periodQuery, $column);
            }
            $datasets[] = [
                'label' => $this->columnLabel($column, $index, $labels),
                'data'  => $columnData,
            ];
        }

        return [
            'labels'   => $months,
            'datasets' => $datasets,
        ];
    }

    private function getWeeklyBarData($query, $valueColumns, $aggregation, $dateColumn, $limit, $labels)
    {
        $weeks = [];
        $datasets = [];

        for ($i = $limit - 1; $i >= 0; $i--) {
            $weeks[] = 'Week ' . Carbon::now()->subWeeks($i)->weekOfYear;
        }

        foreach ($valueColumns as $index => $column) {
            $columnData = [];
            
            for ($i = $limit - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subWeeks($i);
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                
                $value = (clone $query)
                    ->whereBetween($dateColumn, [$startOfWeek, $endOfWeek])
                    ->sum($column);
                
                $columnData[] = (float) $value;
            }

            $datasets[] = [
                'label' => $labels[$index] ?? ucfirst($column),
                'data' => $columnData
            ];
        }

        return [
            'labels' => $weeks,
            'datasets' => $datasets
        ];
    }

    private function getDailyBarData($query, $valueColumns, $aggregation, $dateColumn, $limit, $labels)
    {
        $days = [];
        $datasets = [];

        for ($i = $limit - 1; $i >= 0; $i--) {
            $days[] = Carbon::now()->subDays($i)->format('d M Y');
        }

        foreach ($valueColumns as $index => $column) {
            $columnData = [];
            
            for ($i = $limit - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->toDateString();
                
                $value = (clone $query)
                    ->whereDate($dateColumn, $date)
                    ->sum($column);
                
                $columnData[] = (float) $value;
            }

            $datasets[] = [
                'label' => $labels[$index] ?? ucfirst($column),
                'data' => $columnData
            ];
        }

        return [
            'labels' => $days,
            'datasets' => $datasets
        ];
    }

    private function getCategoryBarData($query, $groupBy, $valueColumns, $aggregation, $labels)
    {
        $categories = (clone $query)
            ->select($groupBy)
            ->distinct()
            ->pluck($groupBy)
            ->toArray();

        $datasets = [];

        foreach ($valueColumns as $index => $column) {
            $columnData = [];
            
            foreach ($categories as $category) {
                $value = (clone $query)
                    ->where($groupBy, $category)
                    ->sum($column);
                
                $columnData[] = (float) $value;
            }

            $datasets[] = [
                'label' => $labels[$index] ?? ucfirst($column),
                'data' => $columnData
            ];
        }

        return [
            'labels' => $categories,
            'datasets' => $datasets
        ];
    }

    private function getMonthlyTrendData($query, $valueColumn, $aggregation, $dateColumn, $limit)
    {
        $labels = [];
        $values = [];

        // Handle custom net income for Keuangan
        if ($valueColumn === 'net_income' && $aggregation === 'custom') {
            for ($i = $limit - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->format('M Y');
                
                $pemasukan = (clone $query)
                    ->whereMonth($dateColumn, $date->month)
                    ->whereYear($dateColumn, $date->year)
                    ->sum('pemasukan');
                
                $pengeluaran = (clone $query)
                    ->whereMonth($dateColumn, $date->month)
                    ->whereYear($dateColumn, $date->year)
                    ->sum('pengeluaran');
                
                $values[] = (float) ($pemasukan - $pengeluaran);
            }

            return ['labels' => $labels, 'values' => $values];
        }

        // Standard aggregation
        for ($i = $limit - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $periodQuery = (clone $query)
                ->whereMonth($dateColumn, $date->month)
                ->whereYear($dateColumn, $date->year);

            $value = $this->resolveLineValue($periodQuery, $valueColumn);
            
            $values[] = (float) $value;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getWeeklyTrendData($query, $valueColumn, $aggregation, $dateColumn, $limit)
    {
        $labels = [];
        $values = [];

        for ($i = $limit - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subWeeks($i);
            $labels[] = 'Week ' . $date->weekOfYear;
            
            $startOfWeek = $date->copy()->startOfWeek();
            $endOfWeek = $date->copy()->endOfWeek();
            
            $value = (clone $query)
                ->whereBetween($dateColumn, [$startOfWeek, $endOfWeek])
                ->sum($valueColumn);
            
            $values[] = (float) $value;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getDailyTrendData($query, $valueColumn, $aggregation, $dateColumn, $limit)
    {
        $labels = [];
        $values = [];

        for ($i = $limit - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M Y');
            
            $value = (clone $query)
                ->whereDate($dateColumn, $date->toDateString())
                ->sum($valueColumn);
            
            $values[] = (float) $value;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function calculateStat($query, $config)
    {
        $type = $config['type'] ?? 'count'; // count, sum, avg, min, max, count_where, sum_where, sum_where_not, custom_saldo, sum_expr
        $column = $config['column'] ?? 'id';

        // Handle raw expression sum: type='sum_expr', expr='col1 * col2'
        if ($type === 'sum_expr' && isset($config['expr'])) {
            $expr = $config['expr'];
            return (float)$query->selectRaw("SUM($expr) as _val")->value('_val');
        }

        // Handle custom saldo calculation for Keuangan
        if ($type === 'custom_saldo') {
            $pemasukan = $query->sum('pemasukan');
            $pengeluaran = $query->sum('pengeluaran');
            return $pemasukan - $pengeluaran;
        }

        // Handle count with where condition
        if ($type === 'count_where' && isset($config['where'])) {
            foreach ($config['where'] as $key => $value) {
                $query->where($key, $value);
            }
            return $query->count();
        }

        // Handle sum with where condition
        if ($type === 'sum_where' && isset($config['where'])) {
            foreach ($config['where'] as $key => $value) {
                $query->where($key, $value);
            }
            return $query->sum($column);
        }

        // Handle sum with where NOT condition
        if ($type === 'sum_where_not' && isset($config['where'])) {
            foreach ($config['where'] as $key => $value) {
                $query->where($key, '!=', $value);
            }
            return $query->sum($column);
        }

        switch ($type) {
            case 'sum':
                return $query->sum($column);
            case 'avg':
                return $query->avg($column);
            case 'min':
                return $query->min($column);
            case 'max':
                return $query->max($column);
            case 'count':
            default:
                return $query->count();
        }
    }

    private function calculateTrend($query, $config)
    {
        if (!isset($config['trendComparison'])) {
            return null;
        }

        $dateColumn = $config['dateColumn'] ?? 'created_at';
        $column = $config['column'] ?? 'id';
        $type = $config['type'] ?? 'count';

        // Get current period value
        $current = $this->calculateStat($query, $config);

        // Get previous period value
        $previousQuery = clone $query;
        $now = Carbon::now();

        switch ($config['trendComparison']) {
            case 'last_month':
                $previousQuery->whereMonth($dateColumn, $now->subMonth()->month)
                             ->whereYear($dateColumn, $now->year);
                break;
            case 'last_week':
                $start = $now->subWeek()->startOfWeek();
                $end = $now->subWeek()->endOfWeek();
                $previousQuery->whereBetween($dateColumn, [$start, $end]);
                break;
        }

        $previous = $this->calculateStat($previousQuery, $config);

        if ($previous == 0) {
            return 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function formatValue($value, $format)
    {
        switch ($format) {
            case 'currency':
                return 'Rp ' . number_format($value, 0, ',', '.');
            case 'percentage':
                return number_format($value, 1) . '%';
            case 'decimal':
                return number_format($value, 2, ',', '.');
            case 'number':
            default:
                return number_format($value, 0, ',', '.');
        }
    }

    private function hexToRgba($hex, $alpha = 1)
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba($r, $g, $b, $alpha)";
    }
}
