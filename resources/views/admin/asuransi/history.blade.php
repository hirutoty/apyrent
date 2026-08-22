@extends('admin.layouts.app')

@section('title', 'History Asuransi Kendaraan')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">History Perpanjangan Asuransi</h1>
            <p class="text-sm text-gray-500 mt-1">Riwayat seluruh data asuransi kendaraan yang telah diperpanjang.</p>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Data</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $data->total() }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">perpanjangan tercatat</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Biaya</p>
            <h3 class="text-sm font-bold text-blue-600 mt-1">
                Rp {{ number_format(\App\Models\AsuransiHistory::when($bulan !== 'semua', fn($q) => $q->whereMonth('tgl_mulai', $bulan))->when($tahun !== 'semua', fn($q) => $q->whereYear('tgl_mulai', $tahun))->sum('biaya'), 0, ',', '.') }}
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">periode terpilih</p>
        </div>
    </div>

    {{-- CHART FILTER --}}
    <x-chart-filter id="asuransiHistoryChartFilter" defaultFilter="month" :showCustomRange="true" />

    {{-- CHART CONTAINER --}}
    <x-chart-container
        id="asuransiHistoryChartContainer"
        layout="stacked"
        pieTitle="Distribusi Status Asuransi"  pieId="asuransiHistoryPieChart"
        barTitle="Biaya Asuransi per Periode"  barId="asuransiHistoryBarChart"
        lineTitle="Trend Biaya Asuransi"       lineId="asuransiHistoryLineChart"
        :showStats="true" :statsData="[]"
    />

    {{-- FILTER + SEARCH + EXPORT --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-5 py-4">
        <form method="GET" action="{{ route('history.asuransi.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500 uppercase">Cari</label>
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Kendaraan, asuransi..."
                        class="pl-8 pr-3 py-2 w-56 rounded-lg border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            @php
                $namaBulanList = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                ];
            @endphp
            <select name="bulan" onchange="this.form.submit()"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="semua" {{ $bulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach($namaBulanList as $no => $nama)
                    <option value="{{ $no }}" {{ (string)$bulan === (string)$no ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>

            <select name="tahun" onchange="this.form.submit()"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="semua" {{ $tahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunList as $thn)
                    <option value="{{ $thn }}" {{ (string)$tahun === (string)$thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>

            <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fa fa-search text-xs"></i> Cari
            </button>

            @if(request('search') || $bulan != 'semua' || $tahun != 'semua')
                <a href="{{ route('history.asuransi.index') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="fa fa-rotate-left text-xs"></i> Reset
                </a>
            @endif

            <a href="{{ route('history.asuransi.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf"></i> Export PDF
            </a>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Kendaraan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Nopol</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Asuransi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">Jenis</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-400">Biaya</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-400">Jatuh Tempo Lama</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-400">Diperpanjang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-400">Bukti</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-400">Lampiran</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $data->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $item->kendaraan->merk ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-mono text-xs tracking-wider">
                                    {{ $item->kendaraan->nopol ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $item->asuransi->nama_asuransi ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->jenisAsuransi->nama_jenis ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 tabular-nums">
                                Rp {{ number_format($item->biaya, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tgl_berakhir)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->diperpanjang_pada)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->bukti_bayar)
                                    <button type="button"
                                        onclick="openSlideshow([{path:'{{ asset($item->bukti_bayar) }}', name:'{{ addslashes(basename($item->bukti_bayar)) }}'}], 0)"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                        <i class="bi bi-image text-sm"></i>
                                        {{ basename($item->bukti_bayar) }}
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->attachments->isNotEmpty())
                                    <div class="flex flex-col gap-1">
                                        @foreach($item->attachments as $att)
                                            <a href="{{ asset($att->file_path) }}" target="_blank"
                                                class="text-blue-600 underline text-xs hover:text-blue-800">
                                                *{{ $att->file_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($item->kendaraan_id)
                                        <a href="{{ route('kendaraan.asuransi-history', $item->kendaraan_id) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 transition">
                                            <i class="fa fa-eye text-[9px]"></i> Detail
                                        </a>
                                    @endif
                                    <form action="{{ route('history.asuransi.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus history ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400">
                                    <i class="fa fa-inbox text-4xl"></i>
                                    <p class="text-sm font-medium">Belum ada history perpanjangan asuransi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="py-3 border-t border-gray-100 px-5">
            <x-pagination :paginator="$data" />
        </div>
    </div>

</div>

<script>
const chartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function () {
    initAsuransiHistoryCharts({ filter_type: 'month' });

    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'asuransiHistoryChartFilter') {
            updateAsuransiHistoryCharts({
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
            });
        }
    });
});

async function initAsuransiHistoryCharts(filters) {
    try {
        await chartManager.initChartsFromAPI('asuransi-history', {
            pie:  'asuransiHistoryPieChart',
            bar:  'asuransiHistoryBarChart',
            line: 'asuransiHistoryLineChart',
        }, filters, { accentLine: true });
    } catch (e) { console.error('Error loading asuransi history charts:', e); }
}

async function updateAsuransiHistoryCharts(filters) {
    try {
        const scrollable = filters.filter_type === 'custom';
        await chartManager.updateChartsFromAPI('asuransi-history', {
            pie:  'asuransiHistoryPieChart',
            bar:  'asuransiHistoryBarChart',
            line: 'asuransiHistoryLineChart',
        }, filters, { scrollable, accentLine: true }, { scrollable });
    } catch (e) { console.error('Error updating asuransi history charts:', e); }
}
</script>

@endsection
