@extends('admin.layouts.app')

@section('title', 'Service Kendaraan')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

@php
    $totalService = $data->total();
    $totalBiaya   = $data->sum('total_biaya');
    $totalProses  = $data->where('status', 'proses')->count();
    $totalSelesai = $data->where('status', 'selesai')->count();
@endphp

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Service Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Riwayat Service & Tracking Part Terpasang</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('service-history.request.create') }}"
                class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-paper-plane text-sm"></i> Request Part
            </a>
            <a href="{{ route('service-history.create') }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i> Tambah Service
            </a>
        </div>
    </div>

    {{-- NAV TABS --}}
    <div>
        <nav class="inline-flex gap-1 bg-gray-100 rounded-xl p-1">
            @php
                $navItems = [
                    ['label' => 'Service History',  'url' => '/admin/service-history', 'icon' => 'bi bi-clock-history'],
                    ['label' => 'Service Asuransi', 'url' => '/admin/service-asuransi','icon' => 'bi bi-shield-fill-check'],
                    ['label' => 'Reminder Service', 'url' => '/admin/reminder-service','icon' => 'bi bi-bell-fill'],
                ];
            @endphp
            @foreach ($navItems as $item)
                @php $isActiveTab = request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*'); @endphp
                <a href="{{ $item['url'] }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-semibold whitespace-nowrap rounded-lg transition-all duration-150
                        {{ $isActiveTab ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-white/60' }}">
                    <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Service</p>
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
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Hampir Limit</p>
            <h3 class="text-2xl font-bold text-yellow-600 mt-1">{{ $hampir }}</h3>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Limit Habis</p>
            <h3 class="text-2xl font-bold text-red-600 mt-1">{{ $habis }}</h3>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- TOOLBAR --}}
        <div class="flex flex-col gap-3 px-5 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-gray-800">Riwayat Service</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->total() }} data</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('service-history.pdf', ['bulan' => request('bulan'), 'search' => request('search')]) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>
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

            {{-- APPROVAL FILTER TABS --}}
            <div class="flex items-center gap-2 border-b border-gray-200 pb-3 mb-4">
                <a href="{{ route('service-history.index', array_merge(request()->except('approval_status'), ['bulan' => request('bulan'), 'kendaraan_id' => request('kendaraan_id'), 'search' => request('search')])) }}"
                    class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors {{ !request('approval_status') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa fa-list-ul text-[10px] mr-1"></i> Semua
                </a>
                <a href="{{ route('service-history.index', array_merge(request()->all(), ['approval_status' => 'pending'])) }}"
                    class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors {{ request('approval_status') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa fa-clock text-[10px] mr-1"></i> Pending Request
                </a>
                <a href="{{ route('service-history.index', array_merge(request()->all(), ['approval_status' => 'approved'])) }}"
                    class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors {{ request('approval_status') === 'approved' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa fa-check text-[10px] mr-1"></i> Approved
                </a>
                <a href="{{ route('service-history.index', array_merge(request()->all(), ['approval_status' => 'rejected'])) }}"
                    class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors {{ request('approval_status') === 'rejected' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="fa fa-times text-[10px] mr-1"></i> Rejected
                </a>
            </div>

            {{-- FILTER FORM --}}
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="approval_status" value="{{ request('approval_status') }}">
                <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

                <select name="kendaraan_id"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">Semua Kendaraan</option>
                    @foreach ($kendaraan as $k)
                        <option value="{{ $k->id }}" {{ request('kendaraan_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->merk }} - {{ $k->nopol }}
                        </option>
                    @endforeach
                </select>

                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nopol, part, kategori, serial..."
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-56">
                </div>

                <button type="submit"
                    class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Filter
                </button>
                @if(request('bulan') || request('search') || request('kendaraan_id'))
                    <a href="{{ url('/admin/service-history') }}"
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Jenis</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">KM</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Parts</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Total Biaya</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Pengeluaran</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $d)
                        @php
                            $partCount     = $d->parts->count();
                            $partLimit     = $d->parts->where('status', 'Limit')->count();
                            $rowId         = 'parts-row-' . $d->id;
                        @endphp

                        {{-- Main row --}}
                        <tr class="border-t border-gray-100 hover:bg-blue-50/30 transition-colors cursor-pointer"
                            onclick="togglePartsRow('{{ $rowId }}', this)">
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
                            <td class="px-4 py-4 text-xs text-gray-500">
                                {{ $d->kendaraan?->jenis?->nama ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($d->tanggal_service)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap">
                                {{ number_format($d->kilometer, 0, ',', '.') }} km
                            </td>
                            <td class="px-4 py-4">
                                @if ($partCount > 0)
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            <i class="fa fa-cogs text-[10px]"></i> {{ $partCount }} Part
                                        </span>
                                        @if ($partLimit > 0)
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
                                <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($d->total_biaya, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <button type="button" onclick="event.stopPropagation(); ubahStatus({{ $d->id }}, '{{ $d->status }}')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition-colors hover:opacity-80
                                            {{ $d->status == 'proses' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $d->status == 'proses' ? 'bg-amber-400' : 'bg-emerald-500' }}"></span>
                                        {{ $d->status == 'proses' ? 'Proses' : 'Selesai' }}
                                    </button>
                                    @if ($d->status_approval === 'pending')
                                        <button type="button" onclick="event.stopPropagation(); openApprovalModal({{ $d->id }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-300 hover:bg-yellow-100 transition-colors">
                                            <i class="fa fa-clock text-[10px]"></i> Pending
                                        </button>
                                    @elseif ($d->status_approval === 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-300">
                                            <i class="fa fa-check text-[10px]"></i> Approved
                                        </span>
                                    @elseif ($d->status_approval === 'rejected')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-300">
                                            <i class="fa fa-times text-[10px]"></i> Rejected
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                @if ($d->status_pengeluaran === 'overservice')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                        <i class="fa fa-triangle-exclamation text-[10px]"></i> Over
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa fa-check text-[10px]"></i> Stabil
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($d->status_approval === 'pending')
                                        <a href="{{ route('service-history.request.edit', $d->id) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                            <i class="fa fa-pencil text-xs"></i> Edit
                                        </a>
                                    @endif
                                    <form action="{{ route('service-history.destroy', $d->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
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
                        <tr id="{{ $rowId }}" style="display:none;" class="bg-slate-50 border-t border-slate-100">
                            <td colspan="10" class="px-6 py-4">
                                @if ($partCount > 0)
                                    {{-- Keluhan header --}}
                                    @if ($d->keluhan)
                                        <div class="mb-3 flex items-start gap-2">
                                            <span class="text-xs font-semibold text-gray-500 shrink-0 mt-0.5">Keluhan:</span>
                                            <span class="text-xs text-gray-700">{{ $d->keluhan }}</span>
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
                                                    <th class="text-left px-3 py-2 font-semibold">Keterangan</th>
                                                    <th class="text-left px-3 py-2 font-semibold">Bukti</th>
                                                    <th class="text-right px-3 py-2 font-semibold">Biaya</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($d->parts as $part)
                                                    @php
                                                        $rowBgClass = match($part->status) {
                                                            'Limit' => 'bg-red-50',
                                                            'Diganti' => 'bg-gray-100',
                                                            default => 'bg-white'
                                                        };
                                                    @endphp
                                                    <tr class="border-t border-slate-100 {{ $rowBgClass }}">
                                                        <td class="px-3 py-2 font-medium {{ $part->status === 'Diganti' ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                                                            {{ $part->nama_part }}
                                                            @if($part->status === 'Diganti')
                                                                <span class="ml-2 px-1.5 py-0.5 text-[10px] bg-gray-200 text-gray-600 rounded no-underline" 
                                                                      style="text-decoration: none !important;"
                                                                      title="Diganti pada {{ $part->replaced_at?->format('d M Y H:i') }} → Part #{{ $part->replaced_by_part_id }}">
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
                                                                if ($part->status === 'Diganti') {
                                                                    $kondisiColor = 'bg-gray-200 text-gray-500';
                                                                }
                                                            @endphp
                                                            <span class="px-1.5 py-0.5 rounded text-xs font-medium {{ $kondisiColor }}">
                                                                {{ $part->kondisi }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            @if ($part->status === 'Limit')
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">
                                                                    <i class="fa fa-exclamation-triangle text-[9px]"></i> Limit
                                                                </span>
                                                            @elseif ($part->status === 'Diganti')
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-gray-200 text-gray-600">
                                                                    <i class="fa fa-history text-[9px]"></i> Diganti
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                                    <i class="fa fa-check text-[9px]"></i> Terpasang
                                                                </span>
                                                            @endif
                                                        </td>
                                                        {{-- Keterangan Column --}}
                                                        <td class="px-3 py-2 {{ $part->status === 'Diganti' ? 'text-gray-400' : 'text-gray-600' }}">
                                                            @if($part->keterangan)
                                                                <span class="text-xs" title="{{ $part->keterangan }}">
                                                                    {{ Str::limit($part->keterangan, 30) }}
                                                                </span>
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        {{-- Bukti Column --}}
                                                        <td class="px-3 py-2">
                                                            @php
                                                                $buktiData = $part->bukti;
                                                                if (is_string($buktiData)) {
                                                                    $buktiData = json_decode($buktiData, true);
                                                                }
                                                            @endphp
                                                            @if($buktiData && is_array($buktiData) && count($buktiData) > 0)
                                                                <div class="flex gap-1">
                                                                    @foreach($buktiData as $file)
                                                                        @php
                                                                            $filePath = $file['path'] ?? '';
                                                                            $fileName = $file['name'] ?? basename($filePath);
                                                                            $fileType = $file['type'] ?? pathinfo($filePath, PATHINFO_EXTENSION);
                                                                            $isImage = in_array(strtolower($fileType), ['jpg', 'jpeg', 'png', 'gif']);
                                                                        @endphp
                                                                        @if($isImage)
                                                                            <a href="{{ asset($filePath) }}" target="_blank" title="{{ $fileName }}">
                                                                                <img src="{{ asset($filePath) }}" 
                                                                                    alt="{{ $fileName }}"
                                                                                    class="w-12 h-12 object-cover rounded border border-gray-300 hover:border-blue-400">
                                                                            </a>
                                                                        @else
                                                                            <a href="{{ asset($filePath) }}" target="_blank" title="{{ $fileName }}"
                                                                                class="w-12 h-12 flex items-center justify-center bg-gray-100 rounded border border-gray-300 hover:border-blue-400">
                                                                                <i class="fa fa-file text-gray-400"></i>
                                                                            </a>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                —
                                                            @endif
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
                                                        Rp {{ number_format($d->parts->sum('biaya'), 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    {{-- Bukti & Lampiran --}}
                                    <div class="mt-3 flex flex-wrap gap-4 text-xs text-gray-500">
                                        @if ($d->bukti_pembayaran)
                                            <a href="{{ asset($d->bukti_pembayaran) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-blue-600 hover:underline">
                                                <i class="fa fa-file"></i> Bukti Pembayaran
                                            </a>
                                        @endif
                                        @foreach ($d->attachments as $att)
                                            <a href="{{ asset($att->file_path) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-blue-600 hover:underline">
                                                <i class="fa fa-paperclip"></i> {{ $att->file_name }}
                                            </a>
                                        @endforeach
                                    </div>
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
                            <td colspan="10" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-screwdriver-wrench text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data Service History</p>
                                    <a href="{{ route('service-history.create') }}"
                                        class="text-xs text-blue-600 hover:underline">Tambah service pertama</a>
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

{{-- MODAL UBAH STATUS --}}
<div id="modalStatus" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
        <div class="px-6 pt-6 pb-4 flex items-center gap-4">
            <div id="msvc-icon" class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl bg-blue-100 text-blue-600">
                <i class="fa fa-gear"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-800">Ubah Status Service</h2>
                <p id="msvc-desc" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
        </div>
        <div class="px-6 pb-2 space-y-2">
            <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer hover:border-amber-200 hover:bg-amber-50 has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50">
                <input type="radio" name="msvc_status" value="proses" class="hidden">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="fa fa-clock text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Proses</p>
                    <p class="text-xs text-gray-400">Service sedang berjalan</p>
                </div>
            </label>
            <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer hover:border-emerald-200 hover:bg-emerald-50 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50">
                <input type="radio" name="msvc_status" value="selesai" class="hidden">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fa fa-check-circle text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Selesai</p>
                    <p class="text-xs text-gray-400">Service telah diselesaikan</p>
                </div>
            </label>
        </div>
        <div class="px-6 py-4 flex gap-3">
            <button onclick="closeStatusModal()" class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50">Batal</button>
            <button onclick="submitStatus()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl flex items-center justify-center gap-2">
                <i class="fa fa-check text-xs"></i> Simpan
            </button>
        </div>
    </div>
</div>

{{-- MODAL APPROVAL --}}
<div id="modalApproval" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
        <div class="px-6 pt-6 pb-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 text-xl">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-800">Approval Request Part</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pilih tindakan untuk request ini</p>
            </div>
        </div>

        {{-- Approve section --}}
        <div class="px-6 pb-2 space-y-2">
            <p class="text-xs font-semibold text-gray-600 mb-2">Jika disetujui, set status service:</p>
            <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer hover:border-amber-200 hover:bg-amber-50 has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50">
                <input type="radio" name="approval_status_service" value="proses" class="hidden">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="fa fa-clock text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Proses</p>
                    <p class="text-xs text-gray-400">Service sedang berjalan</p>
                </div>
            </label>
            <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer hover:border-emerald-200 hover:bg-emerald-50 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50">
                <input type="radio" name="approval_status_service" value="selesai" class="hidden">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fa fa-check-circle text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Selesai</p>
                    <p class="text-xs text-gray-400">Service telah diselesaikan</p>
                </div>
            </label>
        </div>

        <div class="px-6 py-4 flex gap-2">
            <button onclick="closeApprovalModal()" class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50">Batal</button>
            <button onclick="submitReject()" class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2.5 rounded-xl flex items-center justify-center gap-1">
                <i class="fa fa-times text-xs"></i> Tolak
            </button>
            <button onclick="submitApprove()" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2.5 rounded-xl flex items-center justify-center gap-1">
                <i class="fa fa-check text-xs"></i> Setuju
            </button>
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
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Validasi Error!</p><ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
</style>

<script>
// Expandable rows
function togglePartsRow(rowId, headerTr) {
    const row    = document.getElementById(rowId);
    const id     = rowId.replace('parts-row-', '');
    const ch     = document.getElementById('chevron-' + id);
    const isOpen = row.style.display !== 'none';

    row.style.display  = isOpen ? 'none' : '';
    if (ch) ch.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
}

function expandAllRows() {
    document.querySelectorAll('[id^="parts-row-"]').forEach(row => {
        row.style.display = '';
        const id = row.id.replace('parts-row-', '');
        const ch = document.getElementById('chevron-' + id);
        if (ch) ch.style.transform = 'rotate(90deg)';
    });
}

function collapseAllRows() {
    document.querySelectorAll('[id^="parts-row-"]').forEach(row => {
        row.style.display = 'none';
        const id = row.id.replace('parts-row-', '');
        const ch = document.getElementById('chevron-' + id);
        if (ch) ch.style.transform = 'rotate(0deg)';
    });
}

// Status modal
let _statusId = null;
function ubahStatus(id, current) {
    _statusId = id;
    document.getElementById('msvc-desc').textContent = 'Status saat ini: ' + (current === 'proses' ? 'Proses' : 'Selesai');
    document.querySelectorAll('input[name="msvc_status"]').forEach(r => r.checked = r.value === current);
    const m = document.getElementById('modalStatus');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeStatusModal() {
    const m = document.getElementById('modalStatus');
    m.classList.add('hidden'); m.classList.remove('flex');
}
function submitStatus() {
    const sel = document.querySelector('input[name="msvc_status"]:checked');
    if (!sel) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/service-history/' + _statusId + '/status';
    form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
        + '<input type="hidden" name="_method" value="PUT">'
        + '<input type="hidden" name="status" value="' + sel.value + '">';
    document.body.appendChild(form);
    form.submit();
}
document.getElementById('modalStatus').addEventListener('click', e => { if (e.target === document.getElementById('modalStatus')) closeStatusModal(); });

// Toggle Part Detail (Keterangan & Bukti)
function togglePartDetail(partId) {
    const detailRow = document.getElementById('detail-' + partId);
    const icon = document.getElementById('icon-' + partId);
    
    if (detailRow.classList.contains('hidden')) {
        detailRow.classList.remove('hidden');
        if (icon) icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
    } else {
        detailRow.classList.add('hidden');
        if (icon) icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
    }
}

// Approval Modal
let _approvalId = null;

function openApprovalModal(id) {
    _approvalId = id;
    document.querySelectorAll('input[name="approval_status_service"]').forEach(r => r.checked = false);
    const m = document.getElementById('modalApproval');
    m.classList.remove('hidden'); m.classList.add('flex');
}

function closeApprovalModal() {
    const m = document.getElementById('modalApproval');
    m.classList.add('hidden'); m.classList.remove('flex');
}

function submitApprove() {
    const sel = document.querySelector('input[name="approval_status_service"]:checked');
    if (!sel) {
        alert('Pilih status service terlebih dahulu.');
        return;
    }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/service-history/${_approvalId}/approve`;
    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <input type="hidden" name="status" value="${sel.value}">`;
    document.body.appendChild(form);
    form.submit();
}

function submitReject() {
    if (!confirm('Yakin ingin menolak request ini?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/service-history/${_approvalId}/reject`;
    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
    document.body.appendChild(form);
    form.submit();
}

document.getElementById('modalApproval').addEventListener('click', e => {
    if (e.target === document.getElementById('modalApproval')) closeApprovalModal();
});

// Delete Part Bukti
function deletePartBukti(partId, filePath) {
    if (!confirm('Hapus file bukti ini?')) return;
    
    fetch(`/admin/service-parts/${partId}/bukti`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ file_path: filePath })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal menghapus file: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan saat menghapus file');
    });
}

// Alert
(function() {
    const overlay = document.getElementById('alertOverlay');
    const box = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(() => { overlay.style.opacity='1'; overlay.style.pointerEvents='auto'; box.style.transform='translateY(0)'; }, 80);
    const timer = setTimeout(closeAlert, 4500);
    overlay.addEventListener('click', e => { if (e.target===overlay) closeAlert(); });
    function closeAlert() { clearTimeout(timer); overlay.style.opacity='0'; overlay.style.pointerEvents='none'; box.style.transform='translateY(-16px)'; }
    window.closeAlert = closeAlert;
})();
</script>

@endsection
