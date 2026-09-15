@extends('admin.layouts.app')

@section('title', 'Service Incident')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Service Incident</h1>
            <p class="text-sm text-gray-500 mt-0.5">Data incident kendaraan yang sudah disetujui PO-nya</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('purchase-order.index', ['status' => 'Pending']) }}"
                class="inline-flex items-center gap-2 border border-orange-300 text-orange-600 hover:bg-orange-50 text-sm font-medium px-3 py-2 rounded-xl transition-colors">
                <i class="fa fa-clock text-sm"></i> Lihat PO Pending
            </a>
            <a href="{{ route('service-incident.create') }}"
                class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i> Tambah Service Incident
            </a>
        </div>
    </div>

    {{-- NAV TABS --}}
    <div>
        <nav class="inline-flex gap-1 bg-gray-100 rounded-xl p-1">
            @php
                $navItems = [
                    ['label' => 'Service History',  'url' => '/admin/service-history',    'icon' => 'bi bi-clock-history',              'role' => null],
                    ['label' => 'Service Asuransi', 'url' => '/admin/service-asuransi',   'icon' => 'bi bi-shield-fill-check',          'role' => null],
                    ['label' => 'Service Incident', 'url' => '/admin/service-incident',   'icon' => 'bi bi-exclamation-triangle-fill',  'role' => null],
                    ['label' => 'Reminder Service', 'url' => '/admin/reminder-service',   'icon' => 'bi bi-bell-fill',                  'role' => null],
                    ['label' => 'Kategori Service', 'url' => '/admin/service-categories', 'icon' => 'bi bi-tags-fill',                  'role' => 'superadmin'],
                ];
            @endphp
            @foreach ($navItems as $item)
                @if(!isset($item['role']) || auth()->user()->role === $item['role'])
                    @php $isActiveTab = request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*'); @endphp
                    <a href="{{ $item['url'] }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-semibold whitespace-nowrap rounded-lg transition-all duration-150
                            {{ $isActiveTab ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-white/60' }}">
                        <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>
    </div>

    {{-- CHART FILTER --}}
    <x-chart-filter id="serviceIncidentChartFilter" defaultFilter="month" :showCustomRange="true"
        :showCategoryFilter="false" :categories="collect()" />

    {{-- CHART CONTAINER --}}
    <x-chart-container
        id="serviceIncidentChartContainer"
        layout="stacked"
        pieTitle="Biaya per Status" pieId="serviceIncidentPieChart"
        barTitle="Biaya Service Incident per Periode" barId="serviceIncidentBarChart"
        lineTitle="Trend Biaya Service Incident" lineId="serviceIncidentLineChart"
        :showStats="true" :statsData="[]"
    />

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Incident</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalService }}</h3>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Biaya</p>
            <h3 class="text-sm font-bold text-green-600 mt-1">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Proses</p>
            <h3 class="text-2xl font-bold text-yellow-600 mt-1">{{ $totalProses }}</h3>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Selesai</p>
            <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalSelesai }}</h3>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- TOOLBAR --}}
        <div class="flex flex-col gap-3 px-5 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-gray-800">Riwayat Service Incident</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->total() }} data (sudah disetujui PO)</p>
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

            {{-- FILTER FORM --}}
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-400">

                <select name="kendaraan_id"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-400">
                    <option value="">Semua Kendaraan</option>
                    @foreach ($kendaraan as $k)
                        <option value="{{ $k->id }}" {{ request('kendaraan_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->merk }} - {{ $k->nopol }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="px-3 py-1.5 text-xs font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors">
                    Filter
                </button>
                @if(request('bulan') || request('kendaraan_id'))
                    <a href="{{ url('/admin/service-incident') }}"
                        class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="w-8 px-3 py-3"></th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Kendaraan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">KM</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Parts</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Total Biaya</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $d)
                        @php
                            $partCount = $d->parts->count();
                            $rowId     = 'incident-parts-row-' . $d->id;
                        @endphp

                        {{-- Main row --}}
                        <tr class="border-t border-gray-100 hover:bg-orange-50/20 transition-colors cursor-pointer"
                            onclick="togglePartsRow('{{ $rowId }}', '{{ $d->id }}')">
                            <td class="px-3 py-4 text-center">
                                <span id="chevron-{{ $d->id }}"
                                    class="inline-block text-gray-400 transition-transform duration-200 text-xs">
                                    <i class="fa fa-chevron-right"></i>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-800 text-sm">{{ $d->kendaraan?->merk ?? '-' }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $d->kendaraan?->nopol ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($d->tanggal_service)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap">
                                {{ number_format($d->kilometer, 0, ',', '.') }} km
                            </td>
                            <td class="px-4 py-4">
                                @if ($partCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                        <i class="fa fa-cogs text-[10px]"></i> {{ $partCount }} Part
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($d->total_biaya, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-4">
                                @if($d->status === 'proses')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border bg-amber-50 text-amber-700 border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Proses
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border bg-emerald-50 text-emerald-700 border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center gap-1.5">
                                    <form action="{{ route('service-incident.destroy', $d->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data incident ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                            <i class="fa fa-trash text-xs"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- EXPANDABLE PARTS ROW --}}
                        <tr id="{{ $rowId }}" style="display:none;" class="bg-orange-50/10 border-t border-orange-100">
                            <td colspan="8" class="px-6 py-4">
                                @if ($d->keluhan)
                                    <div class="mb-3 flex items-start gap-2 bg-orange-50 rounded-xl px-3 py-2">
                                        <i class="fa fa-exclamation-circle text-orange-500 mt-0.5 text-xs flex-shrink-0"></i>
                                        <span class="text-xs text-gray-700">{{ $d->keluhan }}</span>
                                    </div>
                                @endif

                                @if ($partCount > 0)
                                    <div class="overflow-x-auto rounded-xl border border-orange-100">
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="bg-orange-50 border-b border-orange-100 text-gray-500">
                                                    <th class="text-left px-3 py-2 font-semibold">Part</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Kategori</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Supplier</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Part No.</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Posisi</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Tgl Pasang</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Interval</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Tgl Limit</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Kondisi</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Info Pembayaran</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Bukti</th>
                                                    <th class="text-right px-3 py-2 font-semibold">Biaya</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($d->parts as $part)
                                                    <tr class="border-t border-orange-50 odd:bg-white even:bg-orange-50/20">
                                                        <td class="px-3 py-2 font-medium text-gray-800">
                                                            {{ $part->nama_part }}
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            @if($part->category)
                                                                <span class="bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded text-[10px] font-semibold">
                                                                    {{ $part->category->nama }}
                                                                </span>
                                                            @else
                                                                <span class="text-gray-400">—</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-2 text-gray-700">
                                                            {{ $part->supplier?->nama_supplier ?? '—' }}
                                                        </td>
                                                        <td class="px-3 py-2 font-mono text-gray-600">
                                                            {{ $part->part_number ?: '—' }}
                                                        </td>
                                                        <td class="px-3 py-2 text-gray-600">
                                                            {{ $part->posisi ?: '—' }}
                                                        </td>
                                                        <td class="px-3 py-2 whitespace-nowrap text-gray-600">
                                                            {{ $part->tgl_pasang ? \Carbon\Carbon::parse($part->tgl_pasang)->format('d M Y') : '—' }}
                                                        </td>
                                                        <td class="px-3 py-2 whitespace-nowrap text-gray-600">
                                                            {{ $part->interval_nilai }} {{ $part->interval_satuan }}
                                                        </td>
                                                        <td class="px-3 py-2 whitespace-nowrap text-gray-600">
                                                            {{ $part->tanggal_limit ? \Carbon\Carbon::parse($part->tanggal_limit)->format('d M Y') : '—' }}
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            @php
                                                                $kondisiColor = match($part->kondisi) {
                                                                    'Rusak'       => 'bg-red-100 text-red-700',
                                                                    'Perlu Ganti' => 'bg-amber-100 text-amber-700',
                                                                    default       => 'bg-emerald-100 text-emerald-700',
                                                                };
                                                            @endphp
                                                            <span class="px-1.5 py-0.5 rounded text-xs font-medium {{ $kondisiColor }}">
                                                                {{ $part->kondisi }}
                                                            </span>
                                                        </td>
                                                        {{-- Info Pembayaran --}}
                                                        <td class="px-3 py-2">
                                                            @if($part->nama_rekening || $part->nama_bank || $part->no_rekening)
                                                                <div class="flex flex-col gap-0.5">
                                                                    @if($part->nama_rekening)
                                                                        <span class="text-xs text-gray-700 font-medium">
                                                                            <i class="fa fa-user text-[9px] text-gray-400 mr-1"></i>{{ $part->nama_rekening }}
                                                                        </span>
                                                                    @endif
                                                                    @if($part->nama_bank)
                                                                        <span class="text-xs text-gray-500">
                                                                            <i class="fa fa-university text-[9px] text-gray-400 mr-1"></i>{{ $part->nama_bank }}
                                                                        </span>
                                                                    @endif
                                                                    @if($part->no_rekening)
                                                                        <span class="text-xs font-mono text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded text-[10px]">
                                                                            {{ $part->no_rekening }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-gray-300">—</span>
                                                            @endif
                                                        </td>
                                                        {{-- Bukti --}}
                                                        <td class="px-3 py-2">
                                                            @php
                                                                $buktiData = $part->bukti;
                                                                if (is_string($buktiData)) $buktiData = json_decode($buktiData, true);
                                                            @endphp
                                                            @if($buktiData && is_array($buktiData) && count($buktiData) > 0)
                                                                <div class="flex flex-col gap-1">
                                                                    @foreach($buktiData as $file)
                                                                        <a href="{{ asset($file['path'] ?? '') }}" target="_blank"
                                                                            class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 hover:underline truncate max-w-[130px]"
                                                                            title="{{ $file['name'] ?? '' }}">
                                                                            <i class="fa fa-paperclip text-[10px] flex-shrink-0"></i>
                                                                            <span class="truncate">{{ $file['name'] ?? basename($file['path'] ?? '') }}</span>
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <span class="text-gray-300">—</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-2 text-right font-semibold text-gray-700 whitespace-nowrap">
                                                            Rp {{ number_format($part->biaya, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-orange-50 border-t-2 border-orange-200">
                                                    <td colspan="11" class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Total Biaya Parts:</td>
                                                    <td class="px-3 py-2 text-right text-xs font-bold text-emerald-700">
                                                        Rp {{ number_format($d->parts->sum('biaya'), 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    {{-- Lampiran --}}
                                    @if ($d->attachments->isNotEmpty())
                                        @php
                                            $siAtts = $d->attachments->map(fn($a) => ['path' => asset($a->file_path), 'name' => $a->file_name])->values()->toArray();
                                        @endphp
                                        <div class="mt-3">
                                            <button type="button"
                                                onclick="openSlideshow(JSON.parse(this.dataset.imgs), 0)"
                                                data-imgs="{!! json_encode($siAtts, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_UNESCAPED_SLASHES) !!}"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                                <i class="bi bi-images"></i> Lampiran ({{ $d->attachments->count() }})
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-4 text-xs text-gray-400">
                                        <i class="fa fa-cogs text-gray-300 text-2xl mb-2 block"></i>
                                        Belum ada data part untuk incident ini
                                    </div>
                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-orange-50 flex items-center justify-center">
                                        <i class="fa fa-exclamation-triangle text-2xl text-orange-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data Service Incident</p>
                                    <p class="text-xs text-gray-400">Data akan muncul setelah PO disetujui oleh Superadmin</p>
                                    <a href="{{ route('service-incident.create') }}" class="text-xs text-orange-600 hover:underline">Tambah & ajukan incident pertama</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-gray-100">
            <x-pagination :paginator="$data" />
        </div>
    </div>
</div>

{{-- ALERT --}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if (session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @elseif (session('error'))
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Error!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('error') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Validasi Error!</p>
                <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
</style>

<script>
function togglePartsRow(rowId, incidentId) {
    const row    = document.getElementById(rowId);
    const ch     = document.getElementById('chevron-' + incidentId);
    const isOpen = row.style.display !== 'none';
    row.style.display = isOpen ? 'none' : '';
    if (ch) ch.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
}

function expandAllRows() {
    document.querySelectorAll('[id^="incident-parts-row-"]').forEach(row => {
        row.style.display = '';
        const id = row.id.replace('incident-parts-row-', '');
        const ch = document.getElementById('chevron-' + id);
        if (ch) ch.style.transform = 'rotate(90deg)';
    });
}

function collapseAllRows() {
    document.querySelectorAll('[id^="incident-parts-row-"]').forEach(row => {
        row.style.display = 'none';
        const id = row.id.replace('incident-parts-row-', '');
        const ch = document.getElementById('chevron-' + id);
        if (ch) ch.style.transform = 'rotate(0deg)';
    });
}

// Alert auto-dismiss
(function() {
    const overlay = document.getElementById('alertOverlay');
    const box     = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(() => { overlay.style.opacity='1'; overlay.style.pointerEvents='auto'; box.style.transform='translateY(0)'; }, 80);
    const timer = setTimeout(closeAlert, 4500);
    overlay.addEventListener('click', e => { if (e.target === overlay) closeAlert(); });
    function closeAlert() { clearTimeout(timer); overlay.style.opacity='0'; overlay.style.pointerEvents='none'; box.style.transform='translateY(-16px)'; }
    window.closeAlert = closeAlert;
})();

// ========================================
// CHART INITIALIZATION — Service Incident
// ========================================
const chartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function() {
    initServiceIncidentCharts({ filter_type: 'month' });

    document.addEventListener('chartFilterChange', function(e) {
        if (e.detail.filterId === 'serviceIncidentChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
            };
            updateServiceIncidentCharts(filters);
        }
    });
});

async function initServiceIncidentCharts(filters) {
    try {
        await chartManager.initChartsFromAPI('service-incident', {
            pie:  'serviceIncidentPieChart',
            bar:  'serviceIncidentBarChart',
            line: 'serviceIncidentLineChart',
        }, filters, { accentLine: true });
    } catch (error) {
        console.error('Error loading service incident charts:', error);
    }
}

async function updateServiceIncidentCharts(filters) {
    try {
        const isScrollable = filters.filter_type === 'custom';
        await chartManager.updateChartsFromAPI('service-incident', {
            pie:  'serviceIncidentPieChart',
            bar:  'serviceIncidentBarChart',
            line: 'serviceIncidentLineChart',
        }, filters,
        { scrollable: isScrollable, accentLine: true },
        { scrollable: isScrollable });
    } catch (error) {
        console.error('Error updating service incident charts:', error);
    }
}
</script>

@endsection
