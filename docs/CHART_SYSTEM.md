# Chart & Filter System Documentation

## Overview

Sistem chart interaktif untuk aplikasi rental kendaraan dengan Chart.js v4.4.0. Setiap halaman keuangan memiliki 3 jenis visualisasi data (Pie, Bar, Line Chart) dengan filter waktu real-time dan stats cards.

## 📦 Komponen Utama

### 1. Frontend Components

#### **chart-manager.js** 
`public/js/chart-manager.js` (390 lines)

Class JavaScript untuk manage Chart.js instances:

**Methods:**
- `initPieChart(canvasId, data)` - Initialize pie/doughnut chart
- `initBarChart(canvasId, data)` - Initialize bar chart  
- `initLineChart(canvasId, data)` - Initialize line chart dengan gradient
- `updateChart(canvasId, newData)` - Update chart dengan data baru
- `destroyChart(canvasId)` - Destroy chart instance
- `fetchChartData(page, filters)` - Ajax fetch dari API
- `initChartsFromAPI(page, canvasIds, filters)` - Init semua charts
- `updateChartsFromAPI(page, canvasIds, filters)` - Update semua charts
- `updateStatsCards(statsData)` - Update statistics cards
- `showLoading(containerId)` - Show loading skeleton
- `showError(containerId, message)` - Show error state

#### **chart-styles.css**
`public/css/chart-styles.css`

Styling lengkap untuk chart components:
- Chart containers & responsive grid
- Filter buttons & date picker
- Stats cards
- Loading skeletons & animations
- Error states
- Mobile responsive (stack vertical)

#### **Blade Components**

**chart-filter.blade.php**
```blade
<x-chart-filter 
    id="myChartFilter" 
    defaultFilter="month" 
    :showCustomRange="true" 
/>
```

Props:
- `id` - Unique identifier untuk filter
- `defaultFilter` - Default filter (today/week/month/year)
- `showCustomRange` - Show/hide custom date range picker

**chart-container.blade.php**
```blade
<x-chart-container
    id="myChartContainer"
    pieTitle="Distribution" pieId="myPieChart"
    barTitle="Monthly Trend" barId="myBarChart"
    lineTitle="Growth Trend" lineId="myLineChart"
    :showStats="true" :statsData="[]"
/>
```

Props:
- `id` - Container ID
- `pieTitle, barTitle, lineTitle` - Chart titles
- `pieId, barId, lineId` - Canvas IDs untuk charts
- `showStats` - Show/hide stats cards
- `statsData` - Array data untuk stats cards

### 2. Backend Services

#### **ChartDataService**
`app/Services/ChartDataService.php` (533 lines)

Service untuk agregasi dan transformasi data chart.

**Methods:**
- `getPieChartData($query, $config)` - Generate pie chart data
- `getBarChartData($query, $config)` - Generate bar chart data
- `getLineChartData($query, $config)` - Generate line chart data
- `getStatsData($query, $config)` - Calculate statistics
- `applyDateFilter($query, $filterType, $dates, $dateColumn)` - Apply date filters
- `getPeriodLabel($filterType, $dates)` - Get period label text

**Aggregation Types:**
- `count` - Count records
- `sum` - Sum column values
- `avg` - Average values
- `min`, `max` - Min/max values
- `count_where` - Count with condition
- `sum_where` - Sum with condition
- `sum_where_not` - Sum excluding condition
- `custom` - Custom calculation (untuk Keuangan net income)
- `custom_saldo` - Custom saldo calculation

#### **ChartDataController**
`app/Http/Controllers/Admin/ChartDataController.php`

API endpoint controller untuk chart data.

**Endpoint:** `GET /admin/chart-data/{page}`

Query Params:
- `filter_type` - today/week/month/year/custom
- `start_date` - Start date (for custom filter)
- `end_date` - End date (for custom filter)

**Response:**
```json
{
    "success": true,
    "data": {
        "pie": { "labels": [...], "datasets": [...] },
        "bar": { "labels": [...], "datasets": [...] },
        "line": { "labels": [...], "datasets": [...] },
        "stats": [...]
    },
    "cached": true
}
```

**Features:**
- ✅ Laravel Cache (5 minutes TTL)
- ✅ Query cloning untuk prevent N+1
- ✅ DB-level aggregation
- ✅ Error handling & logging

## 🚀 Cara Implementasi di Halaman Baru

### Step 1: Tambahkan Components di View

```blade
{{-- Chart Filter --}}
<x-chart-filter id="myPageChartFilter" defaultFilter="month" :showCustomRange="true" />

{{-- Chart Container --}}
<x-chart-container
    id="myPageChartContainer"
    pieTitle="Data Distribution" pieId="myPagePieChart"
    barTitle="Monthly Comparison" barId="myPageBarChart"
    lineTitle="Trend Analysis" lineId="myPageLineChart"
    :showStats="true" :statsData="[]"
/>
```

### Step 2: Tambahkan JavaScript Initialization

```blade
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartManager = new ChartManager();
    
    // Initial load dengan filter default
    initMyPageCharts({ filter_type: 'month' });
    
    // Listen filter change event
    document.addEventListener('chartFilterChange', function(e) {
        if (e.detail.filterId === 'myPageChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date: e.detail.startDate,
                end_date: e.detail.endDate
            };
            updateMyPageCharts(filters);
        }
    });
    
    // Initialize charts
    async function initMyPageCharts(filters) {
        try {
            await chartManager.initChartsFromAPI('my-page', {
                pie: 'myPagePieChart',
                bar: 'myPageBarChart',
                line: 'myPageLineChart'
            }, filters);
        } catch (error) {
            console.error('Error loading charts:', error);
        }
    }
    
    // Update charts on filter change
    async function updateMyPageCharts(filters) {
        try {
            await chartManager.updateChartsFromAPI('my-page', {
                pie: 'myPagePieChart',
                bar: 'myPageBarChart',
                line: 'myPageLineChart'
            }, filters);
        } catch (error) {
            console.error('Error updating charts:', error);
        }
    }
});
</script>
@endpush
```

### Step 3: Tambahkan Backend Config

Edit `app/Http/Controllers/Admin/ChartDataController.php`:

#### 3a. Tambahkan ke config map

```php
protected function getPageChartConfig(string $page): array
{
    $configs = [
        // ... existing configs
        'my-page' => $this->getMyPageConfig(),
    ];
    
    return $configs[$page] ?? [];
}
```

#### 3b. Tambahkan ke model map

```php
protected function getPageQuery(string $page)
{
    $models = [
        // ... existing models
        'my-page' => \App\Models\MyModel::query(),
    ];
    
    return $models[$page] ?? null;
}
```

#### 3c. Buat config method

```php
protected function getMyPageConfig(): array
{
    return [
        'dateColumn' => 'created_at', // Kolom tanggal untuk filter
        
        // Pie Chart Config
        'pie' => [
            'title' => 'Distribution by Status',
            'groupBy' => 'status', // Kolom untuk group
            'valueColumn' => 'id', // Kolom untuk aggregation
            'aggregation' => 'count', // count/sum/avg
            'labels' => [], // Optional: custom labels
            'colors' => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444']
        ],
        
        // Bar Chart Config
        'bar' => [
            'title' => 'Monthly Comparison',
            'groupBy' => 'month', // month/week/day/category
            'valueColumns' => ['amount'], // Kolom untuk compare
            'aggregation' => 'sum',
            'dateColumn' => 'created_at',
            'limit' => 6, // Jumlah periode
            'labels' => ['Total Amount']
        ],
        
        // Line Chart Config
        'line' => [
            'title' => 'Trend Analysis',
            'groupBy' => 'month',
            'valueColumn' => 'amount',
            'aggregation' => 'sum',
            'dateColumn' => 'created_at',
            'limit' => 12,
            'label' => 'Growth',
            'color' => '#10b981'
        ],
        
        // Stats Cards Config
        'stats' => [
            [
                'label' => 'Total Records',
                'type' => 'count',
                'column' => 'id',
                'format' => 'number', // number/currency/percent
                'color' => '#4f6ef7',
                'iconBg' => '#eef1ff',
                'icon' => 'fa fa-list'
            ],
            [
                'label' => 'Total Amount',
                'type' => 'sum',
                'column' => 'amount',
                'format' => 'currency',
                'color' => '#10b981',
                'iconBg' => '#d1fae5',
                'icon' => 'fa fa-money-bill-wave'
            ],
            [
                'label' => 'Active Items',
                'type' => 'count_where',
                'column' => 'id',
                'where' => ['status' => 'active'], // Condition
                'format' => 'number',
                'color' => '#f59e0b',
                'iconBg' => '#fef3c7',
                'icon' => 'fa fa-check-circle'
            ],
            [
                'label' => 'Average Value',
                'type' => 'avg',
                'column' => 'amount',
                'format' => 'currency',
                'color' => '#8b5cf6',
                'iconBg' => '#f3e8ff',
                'icon' => 'fa fa-calculator'
            ]
        ]
    ];
}
```

## 📊 Pages dengan Chart System

### Financial Pages (13)
1. ✅ **Keuangan** - Pemasukan vs Pengeluaran, Net Income trend
2. ✅ **Payments** - Payment status, monthly trend
3. ✅ **Invoice** - Invoice status, amount trend
4. ✅ **Aging AR** - Receivables aging analysis
5. ✅ **Aging AP** - Payables aging analysis
6. ✅ **Hutang Vendor** - Vendor debt tracking
7. ✅ **Budgeting** - Budget vs realisasi
8. ✅ **Rekonsiliasi** - Bank reconciliation
9. ✅ **Buku Besar** - Ledger debit/kredit
10. ✅ **Bupot** - Tax withholding
11. ✅ **E-faktur** - Electronic invoicing
12. ✅ **Konsolidasi** - Consolidated financial
13. ✅ **Virtual Account** - VA transactions
14. ✅ **Summary** - Revenue summary

### Operational Pages (7)
15. ✅ **Rental** - Rental revenue & trends
16. ✅ **Service** - Service cost analysis
17. ✅ **Asuransi** - Insurance distribution
18. ✅ **GPS** - GPS unit tracking
19. ✅ **KIR** - Vehicle testing status
20. ✅ **Pajak Kendaraan** - Tax payment tracking
21. ✅ **STNK** - Vehicle registration

## 🔧 Configuration Options

### Filter Types
- `today` - Hari ini
- `week` - 7 hari terakhir
- `month` - 30 hari terakhir
- `year` - 1 tahun terakhir
- `custom` - Custom date range

### Aggregation Types
- `count` - Count records
- `sum` - Sum values
- `avg` - Average values
- `min` - Minimum value
- `max` - Maximum value
- `count_where` - Count with WHERE condition
- `sum_where` - Sum with WHERE condition
- `sum_where_not` - Sum excluding condition

### Chart GroupBy Options
- `month` - Group by month
- `week` - Group by week
- `day` - Group by day
- `category` - Group by category field

### Data Formats
- `number` - Format: 1,234
- `currency` - Format: Rp 1,234,567
- `percent` - Format: 45%

## 🎨 Customization

### Custom Colors
Edit dalam config method:
```php
'colors' => ['#4f6ef7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
```

### Custom Chart Options
Override di JavaScript:
```javascript
chartManager.initPieChart('myChart', data, {
    plugins: {
        legend: {
            position: 'bottom'
        }
    }
});
```

### Custom Stats Calculation
Untuk logic kompleks, gunakan `custom` type dan implement di ChartDataService:
```php
if ($stat['type'] === 'custom_mystype') {
    // Your custom calculation logic
    $value = $this->calculateMyCustomValue($query, $stat);
}
```

## 🐛 Troubleshooting

### Chart tidak muncul
1. Cek console browser untuk error
2. Verify API endpoint response: `/admin/chart-data/{page}`
3. Cek apakah Chart.js CDN loaded
4. Verify canvas IDs match di view dan JavaScript

### Data tidak update saat filter change
1. Cek event listener untuk 'chartFilterChange'
2. Verify filterId match dengan component ID
3. Cek network tab untuk Ajax requests

### Error "Page configuration not found"
1. Pastikan page sudah di-add ke `getPageChartConfig()`
2. Verify method config exists (misal: `getMyPageConfig()`)

### Cache issue
Clear Laravel cache:
```bash
php artisan cache:clear
```

## 📈 Performance

- **Caching**: 5 minutes TTL, cache key based on page + filters
- **Query Optimization**: DB-level aggregation, query cloning
- **Lazy Loading**: Charts loaded on-demand via Ajax
- **Loading States**: Skeleton screens untuk better UX

## 🔒 Security

- CSRF token included dalam Ajax requests
- Input validation di controller
- SQL injection prevention via Eloquent
- Error messages sanitized

## 📝 Best Practices

1. **Selalu gunakan clone** untuk query saat multiple aggregations
2. **Cache busting**: Filter changes auto-refresh data
3. **Error handling**: Gunakan try-catch di JavaScript
4. **Loading states**: Show loading saat fetch data
5. **Mobile-first**: Test responsive di mobile view
6. **Semantic colors**: Hijau = positive, Merah = negative
7. **Consistent naming**: Use descriptive IDs dan methods

## 🎓 Examples

Lihat implementasi reference:
- **Simple**: `resources/views/admin/payments/index.blade.php`
- **Complex**: `resources/views/admin/keuangan/index.blade.php` (dengan tabs)
- **Test Page**: `resources/views/admin/test-chart.blade.php`

## 📚 Dependencies

- Chart.js v4.4.0 (CDN)
- Laravel 10.x
- Tailwind CSS (optional, untuk styling)
- Flatpickr (untuk date picker)

## 🆘 Support

Untuk pertanyaan atau issue:
1. Check dokumentasi ini
2. Review example implementations
3. Check Laravel logs: `storage/logs/laravel.log`
4. Browser console untuk JavaScript errors

---

**Last Updated:** 2026-08-18
**Version:** 1.0.0
**Maintainer:** Development Team
