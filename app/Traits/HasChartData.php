<?php

namespace App\Traits;

use App\Services\ChartDataService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * HasChartData Trait
 * 
 * Trait untuk menambahkan chart data capability ke controllers
 * 
 * Usage:
 * 1. use HasChartData; di controller
 * 2. Implement getChartConfig() method
 * 3. Call getChartDataResponse($request) untuk get chart data
 */
trait HasChartData
{
    protected $chartDataService;

    /**
     * Initialize chart data service
     */
    protected function initChartDataService()
    {
        if (!$this->chartDataService) {
            $this->chartDataService = new ChartDataService();
        }
    }

    /**
     * Get chart data response
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getChartDataResponse(Request $request): JsonResponse
    {
        $this->initChartDataService();

        try {
            // Get filter parameters
            $filterType = $request->input('filter_type', 'month');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $customDates = ($filterType === 'custom' && $startDate && $endDate) 
                ? [$startDate, $endDate] 
                : [];

            // Get chart configuration from controller
            $config = $this->getChartConfig();

            // Get base query
            $query = $this->getChartQuery();

            // Apply date filter
            $dateColumn = $config['dateColumn'] ?? 'created_at';
            $filteredQuery = $this->chartDataService->applyDateFilter(
                $query,
                $filterType,
                $customDates,
                $dateColumn
            );

            // Get chart data
            $pieData = $this->chartDataService->getPieChartData(
                clone $filteredQuery,
                $config['pie'] ?? []
            );

            $barData = $this->chartDataService->getBarChartData(
                clone $filteredQuery,
                $config['bar'] ?? []
            );

            $lineData = $this->chartDataService->getLineChartData(
                clone $filteredQuery,
                $config['line'] ?? []
            );

            $statsData = $this->chartDataService->getStatsData(
                clone $filteredQuery,
                $config['stats'] ?? []
            );

            // Get period label
            $periodLabel = $this->chartDataService->getPeriodLabel($filterType, $customDates);

            return response()->json([
                'success' => true,
                'data' => [
                    'pie' => $pieData,
                    'bar' => $barData,
                    'line' => $lineData,
                    'stats' => $statsData,
                    'period' => $periodLabel
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load chart data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get chart configuration
     * Must be implemented by controller
     * 
     * @return array
     */
    abstract protected function getChartConfig(): array;

    /**
     * Get base query for charts
     * Must be implemented by controller
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract protected function getChartQuery();

    /**
     * Helper: Prepare chart data for view
     * Use this in index() method to pass initial config to view
     * 
     * @param Request $request
     * @return array
     */
    protected function prepareChartDataForView(Request $request): array
    {
        $config = $this->getChartConfig();
        
        return [
            'chartConfig' => [
                'apiEndpoint' => $this->getChartApiEndpoint(),
                'defaultFilter' => $config['defaultFilter'] ?? 'month',
                'pieTitle' => $config['pie']['title'] ?? 'Distribution',
                'barTitle' => $config['bar']['title'] ?? 'Comparison',
                'lineTitle' => $config['line']['title'] ?? 'Trend',
            ]
        ];
    }

    /**
     * Get chart API endpoint URL
     * Override this if you have custom route
     * 
     * @return string
     */
    protected function getChartApiEndpoint(): string
    {
        // Default: assume route is {resource}.chartData
        $routeName = $this->getChartRouteName();
        return route($routeName);
    }

    /**
     * Get chart route name
     * Override this to customize route name
     * 
     * @return string
     */
    protected function getChartRouteName(): string
    {
        return 'chart.data';
    }
}
