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

        if ($groupBy === 'month') {
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
     * Get data for Line Chart
     * 
     * @param Builder $query Base query
     * @param array $config Configuration for line chart
     * @return array Chart data
     */
    public function getLineChartData(Builder $query, array $config)
    {
        $groupBy = $config['groupBy'] ?? 'month';
        $valueColumn = $config['valueColumn'] ?? 'amount';
        $aggregation = $config['aggregation'] ?? 'sum';
        $dateColumn = $config['dateColumn'] ?? 'created_at';
        $limit = $config['limit'] ?? 12;
        $label = $config['label'] ?? 'Trend';
        $color = $config['color'] ?? '#8b5cf6';

        if ($groupBy === 'month') {
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
                    'label' => $label,
                    'data' => $data['values'],
                    'borderColor' => $color,
                    'backgroundColor' => $this->hexToRgba($color, 0.1)
                ]
            ]
        ];
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
        $datasets = [];

        // Generate last N months
        for ($i = $limit - 1; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->format('M Y');
        }

        // Get data for each value column (each column = one bar series)
        foreach ($valueColumns as $index => $column) {
            $columnData = [];

            // Support computed column: ['computed' => ['op' => 'subtract', 'a' => 'colA', 'b' => 'colB']]
            if (is_array($column) && isset($column['computed'])) {
                $op   = $column['computed']['op'] ?? 'subtract';
                $colA = $column['computed']['a'];
                $colB = $column['computed']['b'];

                for ($i = $limit - 1; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $valA = (float)(clone $query)
                        ->whereMonth($dateColumn, $date->month)
                        ->whereYear($dateColumn, $date->year)
                        ->sum($colA);
                    $valB = (float)(clone $query)
                        ->whereMonth($dateColumn, $date->month)
                        ->whereYear($dateColumn, $date->year)
                        ->sum($colB);
                    $columnData[] = $op === 'subtract' ? max(0, $valA - $valB) : $valA + $valB;
                }
            } else {
                for ($i = $limit - 1; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $value = (clone $query)
                        ->whereMonth($dateColumn, $date->month)
                        ->whereYear($dateColumn, $date->year)
                        ->sum($column);
                    $columnData[] = (float) $value;
                }
            }

            $labelFallback = is_array($column) ? ($column['label'] ?? 'Dataset') : ucfirst($column);
            $datasets[] = [
                'label' => $labels[$index] ?? $labelFallback,
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
            $days[] = Carbon::now()->subDays($i)->format('d M');
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
                $labels[] = $date->format('M');
                
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
            $labels[] = $date->format('M');
            
            $value = (clone $query)
                ->whereMonth($dateColumn, $date->month)
                ->whereYear($dateColumn, $date->year)
                ->sum($valueColumn);
            
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
            $labels[] = $date->format('d M');
            
            $value = (clone $query)
                ->whereDate($dateColumn, $date->toDateString())
                ->sum($valueColumn);
            
            $values[] = (float) $value;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function calculateStat($query, $config)
    {
        $type = $config['type'] ?? 'count'; // count, sum, avg, min, max, count_where, sum_where, sum_where_not, custom_saldo
        $column = $config['column'] ?? 'id';

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
