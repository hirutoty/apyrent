@extends('admin.layouts.app')

@section('title', 'History GPS Kendaraan')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">History Perpanjangan GPS Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Riwayat seluruh data GPS kendaraan yang telah diperpanjang.</p>
        </div>
        <a href="{{ route('gps-kendaraan-history.export', request()->query()) }}" target="_blank"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
            <i class="fa fa-file-pdf"></i> Export PDF
        </a>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="flex items-center gap-2 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
            <i class="fa fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Data</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $data->total() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">perpanjangan tercatat</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Biaya</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">
                @if(request('bulan') || request('tahun'))
                    periode yang dipilih
                @else
                    semua periode
                @endif
            </p>
        </div>
    </div>

    {{-- CHART FILTER --}}
    <x-chart-filter id="gpsHistoryChartFilter" defaultFilter="month" :showCustomRange="true" />

    {{-- CHART CONTAINER --}}
    <x-chart-container
        id="gpsHistoryChartContainer"
        layout="stacked"
        pieTitle="Distribusi Type GPS"       pieId="gpsHistoryPieChart"
        barTitle="Biaya GPS per Periode"     barId="gpsHistoryBarChart"
        lineTitle="Trend Biaya Sewa GPS"     lineId="gpsHistoryLineChart"
        :showStats="true" :statsData="[]"
    />

    {{-- FILTER + SEARCH BAR --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-5 py-4">
        <form method="GET" action="{{ route('gps-kendaraan-history.index') }}"
              class="flex flex-wrap items-end gap-3">

            <div class="flex flex-col gap-1 w-64">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cari</label>
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Kendaraan, GPS, type..."
                           class="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bulan</label>
                <select name="bulan"
                        class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm py-2 pl-3 pr-8">
                    <option value="">Semua Bulan</option>
                    @foreach([
                        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                        9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                    ] as $num => $nama)
                        <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tahun</label>
                <select name="tahun"
                        class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm py-2 pl-3 pr-8">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                    <i class="fa fa-filter"></i> Cari / Filter
                </button>
                @if(request('search') || request('bulan') || request('tahun'))
                    <a href="{{ route('gps-kendaraan-history.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium transition">
                        <i class="fa fa-times"></i> Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-10">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kendaraan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nopol</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">GPS</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Biaya Sewa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Tgl Habis Lama</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Diperpanjang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Bukti</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Lampiran</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide w-28">Aksi</th>
                    </tr>
                </thead>

                <tbody id="tableBody" class="divide-y divide-gray-100">
                    @forelse($data as $item)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-4 py-3 text-gray-400 text-xs">
                                {{ $data->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $item->kendaraan->merk ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-mono text-xs tracking-wider">
                                    {{ $item->kendaraan->nopol ?? '-' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $item->gps->nama_gps ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium">
                                    {{ $item->type }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right font-semibold text-gray-800 tabular-nums">
                                Rp {{ number_format($item->biaya_sewa, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ \Carbon\Carbon::parse($item->tanggal_habis)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                <div class="text-gray-800 font-medium">
                                    {{ \Carbon\Carbon::parse($item->diperpanjang_pada)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($item->diperpanjang_pada)->format('H:i') }}
                                </div>
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
                                        <a href="{{ route('kendaraan.gps-history', $item->kendaraan_id) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 transition">
                                            <i class="fa fa-eye text-[9px]"></i> Detail
                                        </a>
                                    @endif
                                    <form action="{{ route('history.gpskendaraan.destroy', $item->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus history GPS ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg
                                                       text-xs font-medium bg-red-50 text-red-600
                                                       hover:bg-red-100 transition">
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
                                    <p class="text-sm font-medium">Belum ada history perpanjangan GPS.</p>
                                    @if(request('bulan') || request('tahun'))
                                        <p class="text-xs">Coba ubah filter periode.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
            <div class="py-3 border-t border-gray-100 px-5">
                <x-pagination :paginator="$data" />
            </div>
        </div>

        @if($data->count())
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500">
            <span>Menampilkan <strong class="text-gray-700">{{ $data->count() }}</strong> data</span>
            <span>Total: <strong class="text-gray-700">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</strong></span>
        </div>
        @endif

    </div>

</div>

<script>
const chartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function () {
    initGpsHistoryCharts({ filter_type: 'month' });

    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'gpsHistoryChartFilter') {
            updateGpsHistoryCharts({
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
            });
        }
    });
});

async function initGpsHistoryCharts(filters) {
    try {
        await chartManager.initChartsFromAPI('gps-kendaraan-history', {
            pie:  'gpsHistoryPieChart',
            bar:  'gpsHistoryBarChart',
            line: 'gpsHistoryLineChart',
        }, filters, { accentLine: true });
    } catch (e) { console.error('Error loading GPS history charts:', e); }
}

async function updateGpsHistoryCharts(filters) {
    try {
        const scrollable = filters.filter_type === 'custom';
        await chartManager.updateChartsFromAPI('gps-kendaraan-history', {
            pie:  'gpsHistoryPieChart',
            bar:  'gpsHistoryBarChart',
            line: 'gpsHistoryLineChart',
        }, filters, { scrollable, accentLine: true }, { scrollable });
    } catch (e) { console.error('Error updating GPS history charts:', e); }
}
</script>

@endsection
