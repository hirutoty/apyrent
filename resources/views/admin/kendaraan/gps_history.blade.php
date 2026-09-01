@extends('admin.layouts.app')

@section('title', 'History GPS - ' . $kendaraan->nopol)

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('gps-kendaraan-history.index') }}"
                   class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <i class="fa fa-arrow-left text-sm"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">History GPS - {{ $kendaraan->nopol }}</h1>
            </div>
            <p class="text-sm text-gray-500">{{ $kendaraan->merk }} {{ $kendaraan->jenis->nama ?? '' }} — Riwayat perpanjangan GPS kendaraan</p>
        </div>
    </div>

    {{-- CHART FILTER --}}
    <x-chart-filter id="gpsKendaraanChartFilter" defaultFilter="month" :showCustomRange="true" />

    {{-- CHART CONTAINER --}}
    <x-chart-container
        id="gpsKendaraanChartContainer"
        layout="stacked"
        pieTitle="Distribusi Type GPS"        pieId="gpsKendaraanPieChart"
        barTitle="Biaya GPS per Periode"      barId="gpsKendaraanBarChart"
        lineTitle="Trend Biaya Sewa GPS"      lineId="gpsKendaraanLineChart"
        :showStats="true" :statsData="[]"
    />

    {{-- INFO KENDARAAN --}}
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
            <p class="text-xs text-slate-500">Total Perpanjangan</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalData }}</h3>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Biaya Sewa</p>
            <h3 class="text-sm font-bold text-indigo-600 mt-1">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Perpanjangan Terakhir</p>
            <h3 class="text-xs font-bold text-green-600 mt-1">
                {{ $lastHistory ? \Carbon\Carbon::parse($lastHistory->diperpanjang_pada)->format('d M Y') : '-' }}
            </h3>
            @if($lastHistory)
                <p class="text-[10px] text-gray-500 mt-0.5">
                    {{ \Carbon\Carbon::parse($lastHistory->diperpanjang_pada)->diffForHumans() }}
                </p>
            @endif
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Rata-rata Biaya</p>
            <h3 class="text-sm font-bold text-purple-600 mt-1">Rp {{ number_format($avgBiaya, 0, ',', '.') }}</h3>
        </div>
    </div>

    {{-- HISTORY TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800">Riwayat GPS</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $history->total() }} record perpanjangan</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">GPS</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Type</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Biaya Sewa</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Tgl Pasang</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Tgl Habis</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Diperpanjang</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Bukti</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Lampiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                        <tr class="border-t border-gray-100 hover:bg-indigo-50/30 transition-colors">
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $history->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $item->gps->nama_gps ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium">
                                    {{ $item->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 tabular-nums">
                                Rp {{ number_format($item->biaya_sewa, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap">
                                {{ $item->tanggal_pasang ? \Carbon\Carbon::parse($item->tanggal_pasang)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap">
                                {{ $item->tanggal_habis ? \Carbon\Carbon::parse($item->tanggal_habis)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap">
                                <div class="font-medium text-gray-800">
                                    {{ $item->diperpanjang_pada ? \Carbon\Carbon::parse($item->diperpanjang_pada)->format('d M Y') : '-' }}
                                </div>
                                @if($item->diperpanjang_pada)
                                    <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($item->diperpanjang_pada)->format('H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($item->status_gps === 'aktif')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->bukti_bayar)
                                    <a href="{{ asset($item->bukti_bayar) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition">
                                        <i class="fa fa-image text-[9px]"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->attachments->isNotEmpty())
                                    <div class="flex flex-col gap-1">
                                        @foreach($item->attachments as $att)
                                            <a href="{{ asset($att->file_path) }}" target="_blank"
                                               class="text-blue-600 underline text-xs hover:text-blue-800">
                                                {{ $att->file_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-satellite-dish text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada riwayat GPS untuk kendaraan ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($history->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $history->links() }}
            </div>
        @endif
    </div>

</div>

<script>
const chartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'gpsKendaraanChartFilter') {
            const filters = {
                filter_type:  e.detail.filterType,
                start_date:   e.detail.startDate,
                end_date:     e.detail.endDate,
                kendaraan_id: {{ $kendaraan->id }},
            };
            if (!chartManager.hasChart('gpsKendaraanBarChart')) {
                initGpsKendaraanCharts(filters);
            } else {
                updateGpsKendaraanCharts(filters);
            }
        }
    });
});

async function initGpsKendaraanCharts(filters) {
    try {
        await chartManager.initChartsFromAPI('gps-history-kendaraan', {
            pie:  'gpsKendaraanPieChart',
            bar:  'gpsKendaraanBarChart',
            line: 'gpsKendaraanLineChart',
        }, filters, { accentLine: true });
    } catch (e) { console.error('Error loading GPS charts:', e); }
}

async function updateGpsKendaraanCharts(filters) {
    try {
        const scrollable = filters.filter_type === 'custom';
        await chartManager.updateChartsFromAPI('gps-history-kendaraan', {
            pie:  'gpsKendaraanPieChart',
            bar:  'gpsKendaraanBarChart',
            line: 'gpsKendaraanLineChart',
        }, filters, { scrollable, accentLine: true }, { scrollable });
    } catch (e) { console.error('Error updating GPS charts:', e); }
}
</script>

@endsection
