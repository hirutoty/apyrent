@extends('admin.layouts.app')

@section('title', 'Test Chart System')

@section('content')
<div class="space-y-6 p-5">
    
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Test Chart System</h1>
            <p class="text-sm text-gray-500 mt-0.5">Testing Chart.js implementation dengan dummy data</p>
        </div>
    </div>

    {{-- CHART FILTER --}}
    <x-chart-filter 
        id="testChartFilter" 
        defaultFilter="month"
        :showCustomRange="true"
    />

    {{-- CHART CONTAINER WITH 3 CHARTS --}}
    <x-chart-container
        id="testChartContainer"
        pieTitle="Distribusi Keuangan"
        pieId="testPieChart"
        barTitle="Perbandingan Bulanan"
        barId="testBarChart"
        lineTitle="Trend Pemasukan"
        lineId="testLineChart"
        :showStats="true"
        :statsData="[
            [
                'label' => 'Total Transaksi',
                'value' => '1,234',
                'color' => '#4f6ef7',
                'iconBg' => '#eef1ff',
                'icon' => 'fa fa-receipt',
                'trend' => 12.5
            ],
            [
                'label' => 'Total Pemasukan',
                'value' => 'Rp 45.6M',
                'color' => '#10b981',
                'iconBg' => '#d1fae5',
                'icon' => 'fa fa-arrow-trend-up',
                'trend' => 8.3
            ],
            [
                'label' => 'Total Pengeluaran',
                'value' => 'Rp 32.1M',
                'color' => '#ef4444',
                'iconBg' => '#fee2e2',
                'icon' => 'fa fa-arrow-trend-down',
                'trend' => -3.2
            ],
            [
                'label' => 'Saldo',
                'value' => 'Rp 13.5M',
                'color' => '#8b5cf6',
                'iconBg' => '#f3e8ff',
                'icon' => 'fa fa-wallet',
                'badge' => 'Healthy',
                'badgeType' => 'success'
            ]
        ]"
    />

    {{-- TEST CONTROLS --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Test Controls</h3>
        <div class="flex gap-3">
            <button onclick="updateTestCharts()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                <i class="fa fa-sync mr-2"></i> Update Charts
            </button>
            <button onclick="destroyTestCharts()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                <i class="fa fa-trash mr-2"></i> Destroy Charts
            </button>
            <button onclick="initTestCharts()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                <i class="fa fa-play mr-2"></i> Re-initialize
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', function() {
        initTestCharts();

        // Listen to filter changes
        document.addEventListener('chartFilterChange', function(e) {
            console.log('📊 Filter changed:', e.detail);
            
            // Show notification
            const filterInfo = e.detail.filterType === 'custom' 
                ? `Custom: ${e.detail.startDate} - ${e.detail.endDate}`
                : e.detail.filterType.toUpperCase();
            
            showNotification(`Filter diubah: ${filterInfo}. Charts akan di-update...`);
            
            // In real implementation, fetch new data and update charts
            setTimeout(() => {
                updateTestCharts();
                showNotification('Charts berhasil di-update!', 'success');
            }, 500);
        });
    });

    function showNotification(message, type = 'info') {
        const colors = {
            info: 'bg-blue-50 text-blue-700 border-blue-200',
            success: 'bg-green-50 text-green-700 border-green-200',
            warning: 'bg-yellow-50 text-yellow-700 border-yellow-200',
            error: 'bg-red-50 text-red-700 border-red-200'
        };

        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg border ${colors[type]} shadow-lg animate-fade-in`;
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fa fa-info-circle"></i>
                <span class="text-sm font-medium">${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            notification.style.transition = 'all 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    function initTestCharts() {
        // Pie Chart - Pemasukan vs Pengeluaran
        const pieData = {
            labels: ['Pemasukan', 'Pengeluaran', 'Pending'],
            datasets: [{
                data: [45600000, 32100000, 5200000],
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b']
            }]
        };

        window.chartManager.initPieChart('testPieChart', pieData, {
            plugins: {
                title: {
                    display: false
                }
            }
        });

        // Bar Chart - Trend Bulanan (Pemasukan vs Pengeluaran)
        const barData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [
                {
                    label: 'Pemasukan',
                    data: [8500000, 7200000, 9100000, 8800000, 10200000, 9500000],
                    backgroundColor: '#10b981'
                },
                {
                    label: 'Pengeluaran',
                    data: [5200000, 4800000, 6100000, 5500000, 6800000, 5900000],
                    backgroundColor: '#ef4444'
                }
            ]
        };

        window.chartManager.initBarChart('testBarChart', barData, {
            plugins: {
                title: {
                    display: false
                }
            }
        });

        // Line Chart - Trend Net Income (Data berbeda dari Bar)
        const lineData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ago', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [
                {
                    label: 'Net Income',
                    data: [3300000, 2400000, 3000000, 3300000, 3400000, 3600000, 3800000, 4200000, 3900000, 3700000, 4100000, 4500000],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)'
                }
            ]
        };

        window.chartManager.initLineChart('testLineChart', lineData, {
            plugins: {
                title: {
                    display: false
                }
            }
        });

        console.log('✅ All 3 test charts (Pie, Bar, Line) initialized successfully');
    }

    function updateTestCharts() {
        // Generate random data for testing
        const randomData = () => Math.floor(Math.random() * 10000000) + 5000000;

        // Update Pie Chart
        const newPieData = {
            labels: ['Pemasukan', 'Pengeluaran', 'Pending'],
            datasets: [{
                data: [randomData(), randomData(), randomData()],
            }]
        };
        window.chartManager.updateChart('testPieChart', newPieData);

        // Update Bar Chart
        const newBarData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [
                {
                    label: 'Pemasukan',
                    data: Array(6).fill(0).map(() => randomData()),
                },
                {
                    label: 'Pengeluaran',
                    data: Array(6).fill(0).map(() => randomData()),
                }
            ]
        };
        window.chartManager.updateChart('testBarChart', newBarData);

        // Update Line Chart
        const newLineData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ago', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [
                {
                    label: 'Net Income',
                    data: Array(12).fill(0).map(() => randomData()),
                }
            ]
        };
        window.chartManager.updateChart('testLineChart', newLineData);

        console.log('✅ All 3 charts updated with random data');
    }

    function destroyTestCharts() {
        window.chartManager.destroyAllCharts();
        console.log('✅ All charts destroyed');
    }
</script>
@endpush
