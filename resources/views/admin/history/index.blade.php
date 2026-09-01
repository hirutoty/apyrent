@extends('admin.layouts.app')

@section('title', 'History Rental Kendaraan')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">History Rental Kendaraan</h1>
        <p class="text-sm text-gray-500 mt-0.5">Rekap total rental & pendapatan kendaraan</p>
    </div>

    {{-- CHART FILTER --}}
    <x-chart-filter id="historyChartFilter" defaultFilter="month" :showCustomRange="true" />

    {{-- CHART CONTAINER --}}
    <x-chart-container
        id="historyChartContainer"
        layout="stacked"
        pieTitle="Distribusi Status Rental" pieId="historyPieChart"
        barTitle="Pendapatan Rental per Periode" barId="historyBarChart"
        lineTitle="Trend Pendapatan Rental" lineId="historyLineChart"
        :showStats="true" :statsData="[]"
    />

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fa fa-history text-blue-500 text-lg"></i>
            </div>
            <div>
                <p class="text-[11px] uppercase font-semibold text-gray-400">History</p>
                <p class="text-2xl font-bold text-gray-800 leading-tight">{{ $kendaraans->sum('rentals_count') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Total History Rental</p>
            </div>
        </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-car text-purple-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-semibold text-gray-400">Rental</p>
                    <p class="text-sm font-bold text-purple-600 leading-tight">Rp {{ number_format($kendaraans->sum('rentals_sum_total_biaya'), 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Pendapatan Rental</p>
                </div>
            </div>

    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- TOOLBAR --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Kendaraan</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $kendaraans->total() }} total kendaraan</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-1.5">
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari mobil, plat..."
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-700 transition-colors">
                        Cari
                    </button>
                    @if(request('search'))
                    <a href="{{ request()->url() }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 transition-colors">
                        Reset
                    </a>
                    @endif
                </form>
                <button onclick="window.location.reload()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                    <i class="fa fa-sync text-xs"></i> Refresh
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Mobil</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Plat</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Total Rental</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Pendatapan Rental</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    @forelse($kendaraans as $k)
                        @php
                            $rentalAktifCount = $k->rentals->where('status', 'aktif')->count();
                            $isAktif = $rentalAktifCount > 0;
                        @endphp
                        <tr class="border-t border-gray-50 transition-colors duration-100 {{ $isAktif ? 'bg-blue-50/60 hover:bg-blue-50' : 'hover:bg-gray-50' }}">

                            {{-- NO --}}
                            <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $kendaraans->firstItem() + $loop->index }}</td>

                            {{-- MOBIL --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg {{ $isAktif ? 'bg-blue-100' : 'bg-blue-50' }} flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-car text-blue-400 text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-gray-800">{{ $k->merk }}</span>
                                        @if($isAktif)
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <span class="relative flex h-1.5 w-1.5">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-blue-500"></span>
                                                </span>
                                                <span class="text-[11px] font-medium text-blue-600">
                                                    Ada rental yang sedang aktif {{ $rentalAktifCount }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- PLAT --}}
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $k->nopol }}</span>
                            </td>

                            {{-- TOTAL RENTAL --}}
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-bold text-gray-700">{{ $k->rentals_count }}x</span>
                            </td>

                            {{-- BIAYA RENTAL --}}
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-semibold text-indigo-600">Rp {{ number_format($k->rentals_sum_total_biaya ?? 0) }}</span>
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center">
                                    <a href="{{ route('history.show', $k->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                        <i class="fa fa-eye text-xs"></i> Detail
                                    </a>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-car text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data rental</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="py-3 border-t border-gray-100">
                <x-pagination :paginator="$kendaraans" />
            </div>

        </div>

    </div>

</div>


{{-- ======================================
    POPUP ALERT (FIXED OVERLAY)
======================================--}}
@if (session('success') || $errors->any())
<div id="alertOverlay"
     class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
     style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

    <div id="alertBox"
         class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
         style="transform:translateY(-16px);transition:transform 0.25s">

        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <button onclick="closeAlert()"
                class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0"
                aria-label="Tutup">
            <i class="fa fa-times"></i>
        </button>

    </div>
</div>
@endif


<style>
@keyframes slideUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script>
// -- POPUP ALERT ------------------------------------
(function () {
    var overlay = document.getElementById('alertOverlay');
    var box     = document.getElementById('alertBox');
    if (!overlay) return;

    setTimeout(function () {
        overlay.style.opacity       = '1';
        overlay.style.pointerEvents = 'auto';
        box.style.transform         = 'translateY(0)';
    }, 80);

    var timer = setTimeout(closeAlert, 4500);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeAlert();
    });

    function closeAlert() {
        clearTimeout(timer);
        overlay.style.opacity       = '0';
        overlay.style.pointerEvents = 'none';
        box.style.transform         = 'translateY(-16px)';
    }
    window.closeAlert = closeAlert;
})();
</script>

<script>
// ========================================
// CHART INITIALIZATION
// ========================================
const historyChartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'historyChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
            };
            if (!historyChartManager.hasChart('historyBarChart')) {
                initHistoryCharts(filters);
            } else {
                updateHistoryCharts(filters);
            }
        }
    });
});

async function initHistoryCharts(filters) {
    try {
        await historyChartManager.initChartsFromAPI('history', {
            pie:  'historyPieChart',
            bar:  'historyBarChart',
            line: 'historyLineChart',
        }, filters, { accentLine: true });
    } catch (error) {
        console.error('Error loading history charts:', error);
    }
}

async function updateHistoryCharts(filters) {
    try {
        const isScrollable = filters.filter_type === 'custom';
        const barOptions   = { scrollable: isScrollable, accentLine: true };
        const lineOptions  = { scrollable: isScrollable };
        await historyChartManager.updateChartsFromAPI('history', {
            pie:  'historyPieChart',
            bar:  'historyBarChart',
            line: 'historyLineChart',
        }, filters, barOptions, lineOptions);
    } catch (error) {
        console.error('Error updating history charts:', error);
    }
}
</script>

@endsection