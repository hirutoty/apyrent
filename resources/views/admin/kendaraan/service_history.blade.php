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
    <x-chart-filter id="kendaraanServiceChartFilter" defaultFilter="month" :showCustomRange="true"
        :showCategoryFilter="true" :categories="$categories" />

    {{-- CHART CONTAINER --}}
    <x-chart-container
        id="kendaraanServiceChartContainer"
        layout="stacked"
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
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800">Riwayat Service</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $serviceHistory->total() }} service records</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="expandAllRows()"
                    class="px-3 py-1.5 text-xs font-medium text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                    <i class="fa fa-chevron-down text-xs mr-1"></i> Buka Semua
                </button>
                <button onclick="collapseAllRows()"
                    class="px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa fa-chevron-right text-xs mr-1"></i> Tutup Semua
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="w-8 px-3 py-3"></th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Keluhan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">KM</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Parts</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Total Biaya</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Approval</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($serviceHistory as $service)
                        @php
                            $partCount = $service->parts->count();
                            $partLimit = $service->parts->where('status', 'Limit')->count();
                            $rowId     = 'detail-parts-row-' . $service->id;
                        @endphp

                        {{-- Main row --}}
                        <tr class="border-t border-gray-100 hover:bg-blue-50/30 transition-colors cursor-pointer"
                            onclick="togglePartsRow('{{ $rowId }}', this)">
                            <td class="px-3 py-4 text-center">
                                <span id="chevron-{{ $service->id }}"
                                    class="inline-block text-gray-400 transition-transform duration-200 text-xs">
                                    <i class="fa fa-chevron-right"></i>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($service->tanggal_service)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-4 text-xs text-gray-600 max-w-[200px]">
                                {{ $service->keluhan ?: '-' }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap">
                                {{ number_format($service->kilometer ?? 0, 0, ',', '.') }} km
                            </td>
                            <td class="px-4 py-4">
                                @if($partCount > 0)
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            <i class="fa fa-cogs text-[10px]"></i> {{ $partCount }} Part
                                        </span>
                                        @if($partLimit > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                <i class="fa fa-exclamation text-[10px]"></i> {{ $partLimit }} Limit
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($service->total_biaya, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-4">
                                @if($service->status === 'limit')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Limit
                                    </span>
                                @elseif($service->status === 'proses')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Proses
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($service->is_request)
                                    @if($service->status_approval === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-300">
                                            <i class="fa fa-clock text-[10px]"></i> Pending
                                        </span>
                                    @elseif($service->status_approval === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-300">
                                            <i class="fa fa-check text-[10px]"></i> Approved
                                        </span>
                                    @elseif($service->status_approval === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-300">
                                            <i class="fa fa-times text-[10px]"></i> Rejected
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>

                        {{-- EXPANDABLE PARTS ROW --}}
                        <tr id="{{ $rowId }}" style="display:none;" class="bg-slate-50 border-t border-slate-100">
                            <td colspan="8" class="px-6 py-4">
                                @if($partCount > 0)
                                    @if($service->keluhan)
                                        <div class="mb-3 flex items-start gap-2">
                                            <span class="text-xs font-semibold text-gray-500 shrink-0 mt-0.5">Keluhan:</span>
                                            <span class="text-xs text-gray-700">{{ $service->keluhan }}</span>
                                        </div>
                                    @endif
                                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="bg-slate-100 text-gray-500">
                                                    <th class="text-left px-3 py-2 font-semibold">Part</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Kategori</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Part No.</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Serial No.</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Posisi</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Tgl Pasang</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Interval</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Tgl Limit</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Kondisi</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Status</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Pengeluaran</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Keterangan</th>
                                                    <th class="text-right px-3 py-2 font-semibold">Biaya</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($service->parts as $part)
                                                    @php
                                                        $rowBgClass = match($part->status) {
                                                            'Limit'   => 'bg-red-50',
                                                            'Diganti' => 'bg-gray-100',
                                                            'Proses'  => 'bg-amber-50',
                                                            default   => 'bg-white'
                                                        };
                                                    @endphp
                                                    <tr class="border-t border-slate-100 {{ $rowBgClass }}">
                                                        <td class="px-3 py-2 font-medium {{ $part->status === 'Diganti' ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                                                            {{ $part->nama_part }}
                                                            @if($part->status === 'Diganti')
                                                                <span class="ml-1 px-1.5 py-0.5 text-[10px] bg-gray-200 text-gray-600 rounded" style="text-decoration:none !important">
                                                                    <i class="fa fa-exchange-alt"></i> Diganti
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-2 {{ $part->status === 'Diganti' ? 'text-gray-400' : 'text-gray-500' }}">{{ $part->category?->nama ?? '—' }}</td>
                                                        <td class="px-3 py-2 font-mono {{ $part->status === 'Diganti' ? 'text-gray-400' : 'text-gray-600' }}">{{ $part->part_number ?: '—' }}</td>
                                                        <td class="px-3 py-2 font-mono {{ $part->status === 'Diganti' ? 'text-gray-400' : 'text-gray-600' }}">{{ $part->serial_number ?: '—' }}</td>
                                                        <td class="px-3 py-2 {{ $part->status === 'Diganti' ? 'text-gray-400' : 'text-gray-600' }}">{{ $part->posisi ?: '—' }}</td>
                                                        <td class="px-3 py-2 whitespace-nowrap {{ $part->status === 'Diganti' ? 'text-gray-400' : 'text-gray-600' }}">
                                                            {{ $part->tgl_pasang ? \Carbon\Carbon::parse($part->tgl_pasang)->format('d M Y') : '—' }}
                                                        </td>
                                                        <td class="px-3 py-2 whitespace-nowrap {{ $part->status === 'Diganti' ? 'text-gray-400' : 'text-gray-600' }}">
                                                            {{ $part->interval_nilai }} {{ $part->interval_satuan }}
                                                        </td>
                                                        <td class="px-3 py-2 whitespace-nowrap {{ $part->status === 'Limit' ? 'text-red-600 font-semibold' : ($part->status === 'Diganti' ? 'text-gray-400' : 'text-gray-600') }}">
                                                            {{ $part->tanggal_limit ? \Carbon\Carbon::parse($part->tanggal_limit)->format('d M Y') : '—' }}
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            @php
                                                                $kondisiColor = match($part->kondisi) {
                                                                    'Rusak'       => 'bg-red-100 text-red-700',
                                                                    'Perlu Ganti' => 'bg-amber-100 text-amber-700',
                                                                    default       => 'bg-emerald-100 text-emerald-700',
                                                                };
                                                                if ($part->status === 'Diganti') $kondisiColor = 'bg-gray-200 text-gray-500';
                                                            @endphp
                                                            <span class="px-1.5 py-0.5 rounded text-xs font-medium {{ $kondisiColor }}">
                                                                {{ $part->kondisi }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            @if($part->status === 'Limit')
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">
                                                                    <i class="fa fa-exclamation-triangle text-[9px]"></i> Limit
                                                                </span>
                                                            @elseif($part->status === 'Diganti')
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-gray-200 text-gray-600">
                                                                    <i class="fa fa-history text-[9px]"></i> Diganti
                                                                </span>
                                                            @elseif($part->status === 'Proses')
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-700">
                                                                    <i class="fa fa-clock text-[9px]"></i> Proses
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                                    <i class="fa fa-check text-[9px]"></i> Terpasang
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            @if($part->status_pengeluaran === 'overservice')
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                                                    <i class="fa fa-triangle-exclamation text-[9px]"></i> Over
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                                    <i class="fa fa-check text-[9px]"></i> Stabil
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-2 {{ $part->status === 'Diganti' ? 'text-gray-400' : 'text-gray-600' }}">
                                                            {{ $part->keterangan ? \Str::limit($part->keterangan, 30) : '—' }}
                                                        </td>
                                                        <td class="px-3 py-2 text-right font-semibold whitespace-nowrap {{ $part->status === 'Diganti' ? 'text-gray-400' : 'text-gray-700' }}">
                                                            Rp {{ number_format($part->biaya, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-slate-100 border-t border-slate-200">
                                                    <td colspan="12" class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Total Biaya Parts:</td>
                                                    <td class="px-3 py-2 text-right text-xs font-bold text-gray-800">
                                                        Rp {{ number_format($service->parts->sum('biaya'), 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    {{-- Bukti & Lampiran --}}
                                    @if($service->bukti_pembayaran || $service->attachments->isNotEmpty())
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-gray-500">
                                            @if($service->bukti_pembayaran)
                                                <a href="{{ asset($service->bukti_pembayaran) }}" target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                                    <i class="bi bi-image"></i> Bukti Pembayaran
                                                </a>
                                            @endif
                                            @foreach($service->attachments as $att)
                                                <a href="{{ asset($att->file_path) }}" target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                                    <i class="bi bi-paperclip"></i> {{ $att->file_name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-4 text-xs text-gray-400">
                                        <i class="fa fa-cogs text-gray-300 text-2xl mb-2 block"></i>
                                        Belum ada data part untuk service ini
                                    </div>
                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-screwdriver-wrench text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada riwayat service untuk kendaraan ini</p>
                                </div>
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

// Expandable rows
function togglePartsRow(rowId, headerTr) {
    const row = document.getElementById(rowId);
    const id  = rowId.replace('detail-parts-row-', '');
    const ch  = document.getElementById('chevron-' + id);
    const isOpen = row.style.display !== 'none';
    row.style.display = isOpen ? 'none' : '';
    if (ch) ch.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
}

function expandAllRows() {
    document.querySelectorAll('[id^="detail-parts-row-"]').forEach(row => {
        row.style.display = '';
        const id = row.id.replace('detail-parts-row-', '');
        const ch = document.getElementById('chevron-' + id);
        if (ch) ch.style.transform = 'rotate(90deg)';
    });
}

function collapseAllRows() {
    document.querySelectorAll('[id^="detail-parts-row-"]').forEach(row => {
        row.style.display = 'none';
        const id = row.id.replace('detail-parts-row-', '');
        const ch = document.getElementById('chevron-' + id);
        if (ch) ch.style.transform = 'rotate(0deg)';
    });
}

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
                kendaraan_id: {{ $kendaraan->id }},
                category_id: e.detail.categoryId ?? ''
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
        }, filters, { accentLine: true });
    } catch (error) {
        console.error('Error loading kendaraan service charts:', error);
    }
}

async function updateKendaraanServiceCharts(filters) {
    try {
        const isScrollable = filters.filter_type === 'custom';
        const barOptions  = { scrollable: isScrollable, accentLine: true };
        const lineOptions = { scrollable: isScrollable };
        await chartManager.updateChartsFromAPI('service-history', {
            pie: 'kendaraanServicePieChart',
            bar: 'kendaraanServiceBarChart',
            line: 'kendaraanServiceLineChart'
        }, filters, barOptions, lineOptions);
    } catch (error) {
        console.error('Error updating kendaraan service charts:', error);
    }
}
</script>

@endsection
