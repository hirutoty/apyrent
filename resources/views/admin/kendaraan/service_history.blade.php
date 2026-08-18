@extends('admin.layouts.app')

@section('title', 'Service History - ' . $kendaraan->nopol)

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('service-history.index') }}" 
                   class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <i class="fa fa-arrow-left text-sm"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Service History - {{ $kendaraan->nopol }}</h1>
            </div>
            <p class="text-sm text-gray-500">{{ $kendaraan->merk }} {{ $kendaraan->jenis->nama ?? '' }} - Detail riwayat service kendaraan</p>
        </div>
    </div>

    {{-- CHART FILTER --}}
    <x-chart-filter id="kendaraanServiceChartFilter" defaultFilter="month" :showCustomRange="true" />

    {{-- CHART CONTAINER --}}
    <x-chart-container
        id="kendaraanServiceChartContainer"
        pieTitle="Biaya per Kategori" pieId="kendaraanServicePieChart"
        barTitle="Timeline Biaya Service" barId="kendaraanServiceBarChart"
        lineTitle="Trend Biaya" lineId="kendaraanServiceLineChart"
        :showStats="true" :statsData="[]"
    />

    {{-- INFO KENDARAAN CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Informasi Kendaraan</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-500">Nopol</p>
                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $kendaraan->nopol }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Merk</p>
                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $kendaraan->merk }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Jenis</p>
                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $kendaraan->jenis->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Status</p>
                <p class="text-sm font-semibold mt-1">
                    <span class="px-2 py-1 rounded-full text-xs 
                        {{ $kendaraan->status_kendaraan === 'tersedia' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($kendaraan->status_kendaraan) }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- SUMMARY STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Service</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalService }}</h3>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Biaya</p>
            <h3 class="text-sm font-bold text-green-600 mt-1">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Last Service</p>
            <h3 class="text-xs font-bold text-blue-600 mt-1">
                {{ $lastService ? \Carbon\Carbon::parse($lastService->tanggal_service)->format('d M Y') : '-' }}
            </h3>
            @if($lastService)
                <p class="text-[10px] text-gray-500 mt-0.5">
                    {{ \Carbon\Carbon::parse($lastService->tanggal_service)->diffForHumans() }}
                </p>
            @endif
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Rata-rata Biaya</p>
            <h3 class="text-sm font-bold text-purple-600 mt-1">Rp {{ number_format($avgBiaya, 0, ',', '.') }}</h3>
        </div>
    </div>

    {{-- SERVICE HISTORY TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Riwayat Service</h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ $serviceHistory->total() }} service records</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Keluhan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">KM</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Biaya</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Parts</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($serviceHistory as $service)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-xs text-gray-700">
                                {{ \Carbon\Carbon::parse($service->tanggal_service)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-700">
                                {{ $service->keluhan ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-700">
                                {{ number_format($service->kilometer ?? 0) }} km
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-green-600">
                                Rp {{ number_format($service->total_biaya, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2 py-1 rounded-full text-[10px] font-medium
                                    {{ $service->status === 'selesai' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ $service->parts->count() }} part(s)
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                Belum ada riwayat service untuk kendaraan ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($serviceHistory->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $serviceHistory->links() }}
            </div>
        @endif
    </div>

</div>

<script>
const chartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts with default filter and kendaraan_id
    initKendaraanServiceCharts({ 
        filter_type: 'month',
        kendaraan_id: {{ $kendaraan->id }}
    });
    
    // Listen for filter changes
    document.addEventListener('chartFilterChange', function(e) {
        if (e.detail.filterId === 'kendaraanServiceChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date: e.detail.startDate,
                end_date: e.detail.endDate,
                kendaraan_id: {{ $kendaraan->id }}
            };
            updateKendaraanServiceCharts(filters);
        }
    });
});

async function initKendaraanServiceCharts(filters) {
    try {
        await chartManager.initChartsFromAPI('service-history', {
            pie: 'kendaraanServicePieChart',
            bar: 'kendaraanServiceBarChart',
            line: 'kendaraanServiceLineChart'
        }, filters);
    } catch (error) {
        console.error('Error loading kendaraan service charts:', error);
    }
}

async function updateKendaraanServiceCharts(filters) {
    try {
        await chartManager.updateChartsFromAPI('service-history', {
            pie: 'kendaraanServicePieChart',
            bar: 'kendaraanServiceBarChart',
            line: 'kendaraanServiceLineChart'
        }, filters);
    } catch (error) {
        console.error('Error updating kendaraan service charts:', error);
    }
}
</script>

@endsection
