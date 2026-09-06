@extends('admin.layouts.app')

@section('title', 'Pembayaran')

@section('content')

{{-- Include Approval Modal Component --}}
<x-approval-modal />

<div class="space-y-6 p-5">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pembayaran</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola pengajuan permintaan pembelian barang &amp; jasa</p>
        </div>
        <a href="{{ route('pembayaran.create') }}"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="fa fa-plus"></i> Tambah Pembayaran
        </a>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total Pembayaran</p>
            <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $totalPR }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Pending</p>
            <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $totalPending }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Diajukan</p>
            <h2 class="text-3xl font-bold text-indigo-600 mt-2">{{ $totalDiajukan }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Disetujui</p>
            <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $totalDisetujui }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Ditolak</p>
            <h2 class="text-3xl font-bold text-red-500 mt-2">{{ $totalDitolak }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 col-span-2 md:col-span-3">
            <p class="text-sm text-gray-500">Total Nominal (Diajukan + Disetujui)</p>
            <h2 class="text-2xl font-bold text-emerald-600 mt-2">
                Rp {{ number_format($totalNominal, 0, ',', '.') }}
            </h2>
        </div>
    </div>

    {{-- CHART FILTER --}}
    @php
        $deptList = collect(['Keuangan','Produksi','HRD','Purchase','Sales','Marketing','IT'])
            ->map(fn($d) => ['id' => $d, 'nama' => $d]);
    @endphp
    <x-chart-filter id="pembayaranChartFilter" defaultFilter="month" :showCustomRange="true"
        :showCategoryFilter="true" :categories="$deptList" />

    {{-- CHART CONTAINER --}}
    <x-chart-container
        id="pembayaranChartContainer"
        layout="stacked"
        pieTitle="Distribusi Status" pieId="pembayaranPieChart"
        barTitle="Nominal Pembayaran per Bulan" barId="pembayaranBarChart"
        lineTitle="Trend Nominal Pembayaran" lineId="pembayaranLineChart"
        :showStats="true" :statsData="[]"
    />

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

        {{-- NAV TABS --}}
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 -mb-px overflow-x-auto">
                @if ($role === 'superadmin')
                    @foreach ([
                        ['key' => 'Pending',   'label' => 'Pending',   'icon' => 'bi bi-hourglass-split',   'count' => $totalPending,   'color' => 'yellow'],
                        ['key' => 'Diajukan',  'label' => 'Diajukan',  'icon' => 'bi bi-paper-plane',        'count' => $totalDiajukan,  'color' => 'indigo'],
                        ['key' => 'Disetujui', 'label' => 'Disetujui', 'icon' => 'bi bi-check-circle-fill',  'count' => $totalDisetujui, 'color' => 'green'],
                        ['key' => 'Ditolak',   'label' => 'Ditolak',   'icon' => 'bi bi-x-circle-fill',      'count' => $totalDitolak,   'color' => 'red'],
                    ] as $t)
                        @php $isActive = $tab === $t['key']; @endphp
                        <a href="{{ route('pembayaran.index', ['tab' => $t['key'], 'sort' => $sort]) }}"
                            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                                {{ $isActive ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                            <i class="{{ $t['icon'] }}"></i>
                            {{ $t['label'] }}
                            @php
                                $badgeCls = match($t['color']) {
                                    'green'  => 'bg-green-100 text-green-700',
                                    'red'    => 'bg-red-100 text-red-700',
                                    'indigo' => 'bg-indigo-100 text-indigo-700',
                                    'yellow' => 'bg-yellow-100 text-yellow-700',
                                    default  => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="ml-1 text-xs font-bold px-2 py-0.5 rounded-full {{ $badgeCls }}">{{ $t['count'] }}</span>
                        </a>
                    @endforeach
                @else
                    @php
                        $navTabs = [
                            ['key' => 'semua',    'label' => 'Semua',    'icon' => 'bi bi-list-ul',           'count' => $totalPR,       'badge' => 'bg-blue-100 text-blue-700'],
                            ['key' => 'Pending',  'label' => 'Pending',  'icon' => 'bi bi-hourglass-split',   'count' => $totalPending,  'badge' => 'bg-yellow-100 text-yellow-700'],
                            ['key' => 'Diajukan', 'label' => 'Diajukan', 'icon' => 'bi bi-paper-plane',       'count' => $totalDiajukan, 'badge' => 'bg-indigo-100 text-indigo-700'],
                            ['key' => 'Disetujui','label' => 'Disetujui','icon' => 'bi bi-check-circle-fill', 'count' => $totalDisetujui,'badge' => 'bg-green-100 text-green-700'],
                            ['key' => 'Ditolak',  'label' => 'Ditolak',  'icon' => 'bi bi-x-circle-fill',    'count' => $totalDitolak,  'badge' => 'bg-red-100 text-red-700'],
                        ];
                    @endphp
                    @foreach ($navTabs as $t)
                        @php $isActive = $tab === $t['key']; @endphp
                        <a href="{{ route('pembayaran.index', ['tab' => $t['key'], 'sort' => $sort]) }}"
                            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                                {{ $isActive ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                            <i class="{{ $t['icon'] }}"></i>
                            {{ $t['label'] }}
                            <span class="ml-1 text-xs font-bold px-2 py-0.5 rounded-full {{ $t['badge'] }}">{{ $t['count'] }}</span>
                        </a>
                    @endforeach
                @endif
            </nav>
        </div>

        {{-- TOOLBAR --}}
        <div class="flex flex-col gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1 text-xs text-gray-500">
                    Menampilkan <span class="font-semibold text-gray-700">{{ $data->total() }}</span> data
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 whitespace-nowrap">Urutkan:</span>
                    <a href="{{ route('pembayaran.index', array_merge(request()->except('sort'), ['sort' => 'terbaru'])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                            {{ $sort === 'terbaru' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        <i class="bi bi-sort-down"></i> Terbaru
                    </a>
                    <a href="{{ route('pembayaran.index', array_merge(request()->except('sort'), ['sort' => 'terlama'])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                            {{ $sort === 'terlama' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        <i class="bi bi-sort-up"></i> Terlama
                    </a>
                </div>
            </div>

            {{-- FILTER FORM --}}
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="sort" value="{{ $sort }}">

                {{-- Filter Bulan --}}
                <input type="month" name="bulan" value="{{ $bulan ?? '' }}"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

                {{-- Filter Departemen (superadmin saja) --}}
                @if($role === 'superadmin')
                <select name="departemen"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">Semua Departemen</option>
                    @foreach(['Keuangan','Produksi','HRD','Purchase','Sales','Marketing','IT'] as $dept)
                        <option value="{{ $dept }}" {{ ($deptFilter ?? '') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
                @endif

                <button type="submit"
                    class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    <i class="fa fa-filter text-xs mr-1"></i> Filter
                </button>
                @if($bulan || $deptFilter)
                    <a href="{{ route('pembayaran.index', ['tab' => $tab, 'sort' => $sort]) }}"
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
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="w-8 px-2 py-3"></th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No PR</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jenis</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Departemen</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Pemohon</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Keterangan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Total Items</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Total Nominal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $d)
                        @php
                            $bc = match($d->status) {
                                'Disetujui' => 'bg-green-100 text-green-600',
                                'Ditolak'   => 'bg-red-100 text-red-600',
                                'Diajukan'  => 'bg-indigo-100 text-indigo-600',
                                'Pending'   => 'bg-yellow-100 text-yellow-600',
                                default     => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50 hover:bg-blue-50/50 transition-colors cursor-pointer" onclick="toggleExpand({{ $d->id }})">
                            <td class="px-2 py-3.5 text-center">
                                <i id="chevron-{{ $d->id }}" class="fa fa-chevron-right text-[10px] text-gray-400 transition-transform duration-200"></i>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-400">{{ $data->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->no_pr }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($d->source_type)
                                    {{-- Pengeluaran --}}
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                        <i class="bi bi-wallet2 text-[10px]"></i>
                                        {{ $d->source_type_name }}
                                    </span>
                                @else
                                    {{-- Belanja --}}
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="bi bi-cart3 text-[10px]"></i>
                                        Belanja
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-500 whitespace-nowrap">
                                {{ $d->tanggal ? \Carbon\Carbon::parse($d->tanggal)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-800">
                                    <i class="fa fa-building text-blue-400 text-xs"></i>{{ $d->departemen ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->pemohon ?? '-' }}</td>
                            <td class="px-4 py-3.5">
                                @if($d->keterangan)
                                    <span class="text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded max-w-[180px] block truncate" title="{{ $d->keterangan }}">
                                        {{ $d->keterangan }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @php
                                    if ($d->items->count() > 0) {
                                        $itemCount = $d->items->count();
                                    } elseif ($d->source_type === 'gps' && !empty($d->source_data['gps_items'])) {
                                        $itemCount = count($d->source_data['gps_items']);
                                    } else {
                                        $itemCount = 1;
                                    }
                                @endphp
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-gray-600">
                                    <i class="fa fa-boxes text-blue-400 text-xs"></i>
                                    {{ $itemCount }} item{{ $itemCount > 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <span class="text-sm font-semibold text-emerald-600">
                                    Rp {{ number_format($d->total_nominal ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $bc }}">
                                    <i class="fa fa-circle text-[6px]"></i> {{ $d->status ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">

                                    {{-- Detail selalu tampil --}}
                                    <button type="button"
                                        onclick="openDetailModal({{ $d->id }})"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors border border-blue-200">
                                        <i class="fa fa-eye text-[10px]"></i> Detail
                                    </button>

                                    @if ($role === 'superadmin')
                                        {{-- Superadmin Actions --}}
                                        @if($d->status === 'Pending')
                                            @if($d->source_type)
                                                {{-- PENGELUARAN: Approve/Reject dengan Modal --}}
                                                <button type="button"
                                                    onclick="openApprovalModal({{ $d->id }})"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                    <i class="fa fa-check text-[10px]"></i> Approve
                                                </button>
                                                <button type="button"
                                                    onclick="openRejectModal({{ $d->id }})"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                                    <i class="fa fa-times text-[10px]"></i> Reject
                                                </button>
                                            @else
                                                {{-- BELANJA REGULAR: Existing flow --}}
                                                @if($d->tipe_pembayaran === 'service')
                                                    <button type="button"
                                                        onclick="openApproveServiceModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                        <i class="fa fa-check text-[10px]"></i> Setujui
                                                    </button>
                                                @else
                                                    <form action="{{ route('pembayaran.status', $d->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="Disetujui">
                                                        <button type="submit"
                                                            onclick="return confirm('Setujui pembayaran {{ $d->no_pr }}?')"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                            <i class="fa fa-check text-[10px]"></i> Setujui
                                                        </button>
                                                    </form>
                                                @endif
                                                <button type="button"
                                                    onclick="openTolakModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                                    <i class="fa fa-times text-[10px]"></i> Tolak
                                                </button>
                                            @endif
                                        @elseif($d->status === 'Diajukan')
                                            {{-- Status Diajukan (Old logic) --}}
                                            @if($d->tipe_pembayaran === 'service')
                                                <button type="button"
                                                    onclick="openApproveServiceModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                    <i class="fa fa-check text-[10px]"></i> Setujui
                                                </button>
                                            @else
                                                <form action="{{ route('pembayaran.status', $d->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="Disetujui">
                                                    <button type="submit"
                                                        onclick="return confirm('Setujui pembayaran {{ $d->no_pr }}?')"
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                        <i class="fa fa-check text-[10px]"></i> Setujui
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button"
                                                onclick="openTolakModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                                <i class="fa fa-times text-[10px]"></i> Tolak
                                            </button>
                                        @elseif($d->status === 'Ditolak' && $d->source_type && $d->can_edit)
                                            {{-- PENGELUARAN DITOLAK: Button Edit & Ajukan Ulang --}}
                                            <a href="{{ route('pembayaran.edit-rejected', $d->id) }}"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors border border-amber-200">
                                                <i class="fa fa-edit text-[10px]"></i> Edit & Ajukan Ulang
                                            </a>
                                        @elseif($d->status === 'Disetujui' && $d->source_type && $d->target_id)
                                            {{-- Link to final table --}}
                                            <a href="{{ route(match($d->source_type) {
                                                'asuransi_kendaraan' => 'asuransi-kendaraan.index',
                                                'pajak' => 'pajak-kendaraan.index',
                                                default => 'pembayaran.index',
                                            }) }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors border border-indigo-200">
                                                <i class="bi bi-box-arrow-up-right text-[10px]"></i> Lihat Data
                                            </a>
                                        @endif

                                    @else
                                        {{-- Non-superadmin: Edit + Hapus (hanya jika belum diajukan/disetujui) --}}
                                        @if(!in_array($d->status, ['Diajukan', 'Disetujui']))
                                            <a href="{{ route('pembayaran.edit', $d->id) }}"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition-colors border border-yellow-200">
                                                <i class="fa fa-edit text-[10px]"></i> Edit
                                            </a>
                                            <button type="button"
                                                data-action="{{ route('pembayaran.destroy', $d->id) }}"
                                                data-name="{{ $d->no_pr }}"
                                                onclick="triggerDelete(this)"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                                <i class="fa fa-trash text-[10px]"></i> Hapus
                                            </button>
                                        @endif

                                        {{-- Ajukan: hanya saat Pending atau Ditolak --}}
                                        @if(in_array($d->status, ['Pending', 'Ditolak']))
                                            <form action="{{ route('pembayaran.ajukan', $d->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Ajukan pembayaran {{ $d->no_pr }}?')"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors border border-indigo-200">
                                                    <i class="fa fa-paper-plane text-[10px]"></i> Ajukan
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                </div>
                            </td>
                        </tr>

                        {{-- ===== EXPAND ROW ===== --}}
                        <tr id="expand-{{ $d->id }}" class="hidden bg-blue-50/30 border-t-0">
                            <td colspan="11" class="px-6 pb-4 pt-1">
                                <div class="rounded-xl border border-blue-100 bg-white overflow-hidden shadow-sm">

                                    {{-- Items --}}
                                    @if($d->items->count() > 0)
                                        <div class="px-4 pt-3 pb-1">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                                <i class="fa fa-list-ul mr-1"></i> Detail Items ({{ $d->items->count() }})
                                            </p>
                                        </div>
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="bg-gray-50 border-y border-gray-100">
                                                    <th class="text-left px-4 py-2 font-semibold text-gray-500">#</th>
                                                    <th class="text-left px-4 py-2 font-semibold text-gray-500">Nama Barang</th>
                                                    <th class="text-left px-4 py-2 font-semibold text-gray-500">Kategori</th>
                                                    <th class="text-left px-4 py-2 font-semibold text-gray-500">Qty</th>
                                                    <th class="text-right px-4 py-2 font-semibold text-gray-500">Harga Satuan</th>
                                                    <th class="text-right px-4 py-2 font-semibold text-gray-500">Subtotal</th>
                                                    <th class="text-left px-4 py-2 font-semibold text-gray-500">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($d->items as $idx => $item)
                                                    <tr class="border-t border-gray-50 {{ $idx % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                                                        <td class="px-4 py-2 text-gray-400">{{ $idx + 1 }}</td>
                                                        <td class="px-4 py-2 font-medium text-gray-700">{{ $item->nama_barang }}</td>
                                                        <td class="px-4 py-2 text-gray-500">{{ $item->kategori ?: '-' }}</td>
                                                        <td class="px-4 py-2 text-gray-600">{{ $item->qty }} {{ $item->satuan }}</td>
                                                        <td class="px-4 py-2 text-right text-gray-600">
                                                            {{ $item->harga_satuan ? 'Rp ' . number_format($item->harga_satuan, 0, ',', '.') : '-' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-right font-semibold text-emerald-600">
                                                            {{ $item->subtotal ? 'Rp ' . number_format($item->subtotal, 0, ',', '.') : '-' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-gray-400 max-w-[200px] truncate">{{ $item->keterangan ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="border-t-2 border-gray-200 bg-gray-50">
                                                    <td colspan="5" class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Total</td>
                                                    <td class="px-4 py-2 text-right text-sm font-bold text-emerald-600">
                                                        Rp {{ number_format($d->total_nominal, 0, ',', '.') }}
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @elseif($d->source_type)
                                        {{-- ===== PENGELUARAN KENDARAAN: detail per source_type ===== --}}
                                        @php $sd = $d->source_data ?? []; @endphp

                                        {{-- Header strip --}}
                                        <div class="flex items-center justify-between px-4 pt-3 pb-2 border-b border-gray-100">
                                            <p class="text-[10px] font-semibold text-purple-500 uppercase tracking-wider flex items-center gap-1.5">
                                                <i class="bi bi-wallet2"></i> Detail Pengeluaran — {{ $d->source_type_name }}
                                            </p>
                                            <span class="text-xs font-bold text-emerald-600">
                                                Total: Rp {{ number_format($d->nominal ?? 0, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- Info baris atas: kendaraan + tanggal --}}
                                        @php
                                            $kendaraan = isset($sd['kendaraan_id'])
                                                ? \App\Models\Kendaraan::find($sd['kendaraan_id'])
                                                : null;
                                        @endphp
                                        @if($kendaraan || isset($sd['tanggal_bayar']) || isset($sd['tanggal_habis']))
                                        <div class="px-4 py-2.5 grid grid-cols-2 md:grid-cols-4 gap-3 bg-gray-50/50 border-b border-gray-100">
                                            @if($kendaraan)
                                            <div>
                                                <p class="text-[10px] text-gray-400 uppercase">Kendaraan</p>
                                                <p class="text-xs font-semibold text-gray-700">{{ $kendaraan->nopol }} — {{ $kendaraan->merk }}</p>
                                            </div>
                                            @endif
                                            @if(isset($sd['tanggal_bayar']))
                                            <div>
                                                <p class="text-[10px] text-gray-400 uppercase">Tgl Bayar</p>
                                                <p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_bayar'])->format('d M Y') }}</p>
                                            </div>
                                            @endif
                                            @if(isset($sd['tanggal_habis']))
                                            <div>
                                                <p class="text-[10px] text-gray-400 uppercase">Berlaku s/d</p>
                                                <p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_habis'])->format('d M Y') }}</p>
                                            </div>
                                            @endif
                                            @if(isset($sd['keterangan']) && $sd['keterangan'])
                                            <div>
                                                <p class="text-[10px] text-gray-400 uppercase">Keterangan</p>
                                                <p class="text-xs text-gray-600">{{ $sd['keterangan'] }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        @endif

                                        {{-- ── GPS: tabel gps_items ── --}}
                                        @if($d->source_type === 'gps' && !empty($sd['gps_items']))
                                            @php $gpsItems = $sd['gps_items']; $totalBiaya = 0; @endphp
                                            <table class="w-full text-xs">
                                                <thead>
                                                    <tr class="bg-purple-50 border-y border-purple-100">
                                                        <th class="text-left px-4 py-2 font-semibold text-purple-500">#</th>
                                                        <th class="text-left px-4 py-2 font-semibold text-purple-500">Nama GPS</th>
                                                        <th class="text-left px-4 py-2 font-semibold text-purple-500">Type</th>
                                                        <th class="text-right px-4 py-2 font-semibold text-purple-500">Biaya Sewa</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($gpsItems as $gi => $gItem)
                                                        @php
                                                            $gpsModel = isset($gItem['gps_id']) ? \App\Models\Gps::find($gItem['gps_id']) : null;
                                                            $biaya = (int)($gItem['biaya_sewa'] ?? 0);
                                                            $totalBiaya += $biaya;
                                                        @endphp
                                                        <tr class="border-t border-gray-50 {{ $gi % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                                                            <td class="px-4 py-2 text-gray-400">{{ $gi + 1 }}</td>
                                                            <td class="px-4 py-2 font-medium text-gray-700">{{ $gpsModel->nama_gps ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-gray-600">{{ $gItem['type'] ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-right font-semibold text-emerald-600">
                                                                Rp {{ number_format($biaya, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr class="border-t-2 border-gray-200 bg-gray-50">
                                                        <td colspan="3" class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Total</td>
                                                        <td class="px-4 py-2 text-right text-sm font-bold text-emerald-600">
                                                            Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        {{-- ── Asuransi Kendaraan ── --}}
                                        @elseif($d->source_type === 'asuransi_kendaraan')
                                            @php
                                                $asuransi = isset($sd['asuransi_id']) ? \App\Models\Asuransi::find($sd['asuransi_id']) : null;
                                                $jenisAsr = isset($sd['jenis_asuransi_id']) ? \App\Models\JenisAsuransi::find($sd['jenis_asuransi_id']) : null;
                                            @endphp
                                            <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Perusahaan Asuransi</p>
                                                    <p class="text-xs font-semibold text-gray-700">{{ $asuransi->nama_asuransi ?? ($sd['asuransi_id'] ?? '-') }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Jenis Asuransi</p>
                                                    <p class="text-xs text-gray-700">{{ $jenisAsr->nama_jenis ?? ($sd['jenis_asuransi_id'] ?? '-') }}</p>
                                                </div>
                                                @if(isset($sd['no_polis']) && $sd['no_polis'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">No. Polis</p>
                                                    <p class="text-xs font-mono text-gray-700">{{ $sd['no_polis'] }}</p>
                                                </div>
                                                @endif
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Premi</p>
                                                    <p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['premi'] ?? $d->nominal ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                            </div>

                                        {{-- ── Pajak Kendaraan ── --}}
                                        @elseif($d->source_type === 'pajak')
                                            <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Jenis Pajak</p>
                                                    <p class="text-xs font-semibold text-gray-700">{{ $sd['jenis_pajak'] ?? '-' }}</p>
                                                </div>
                                                @if(isset($sd['tahun_pajak']) && $sd['tahun_pajak'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Tahun Pajak</p>
                                                    <p class="text-xs text-gray-700">{{ $sd['tahun_pajak'] }}</p>
                                                </div>
                                                @endif
                                                @if(isset($sd['tanggal_jatuh_tempo']) && $sd['tanggal_jatuh_tempo'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Jatuh Tempo</p>
                                                    <p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_jatuh_tempo'])->format('d M Y') }}</p>
                                                </div>
                                                @endif
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Nominal</p>
                                                    <p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['nominal'] ?? $d->nominal ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                            </div>

                                        {{-- ── KIR ── --}}
                                        @elseif($d->source_type === 'kir')
                                            <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                @if(isset($sd['no_kir']) && $sd['no_kir'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">No. KIR</p>
                                                    <p class="text-xs font-mono text-gray-700">{{ $sd['no_kir'] }}</p>
                                                </div>
                                                @endif
                                                @if(isset($sd['tanggal_kir']) && $sd['tanggal_kir'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Tgl KIR</p>
                                                    <p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_kir'])->format('d M Y') }}</p>
                                                </div>
                                                @endif
                                                @if(isset($sd['tanggal_habis_kir']) && $sd['tanggal_habis_kir'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Berlaku s/d</p>
                                                    <p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_habis_kir'])->format('d M Y') }}</p>
                                                </div>
                                                @endif
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Biaya</p>
                                                    <p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['biaya'] ?? $d->nominal ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                            </div>

                                        {{-- ── STNK ── --}}
                                        @elseif($d->source_type === 'stnk')
                                            <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                @if(isset($sd['tahun_stnk']) && $sd['tahun_stnk'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Tahun STNK</p>
                                                    <p class="text-xs font-semibold text-gray-700">{{ $sd['tahun_stnk'] }}</p>
                                                </div>
                                                @endif
                                                @if(isset($sd['tanggal_stnk']) && $sd['tanggal_stnk'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Tgl STNK</p>
                                                    <p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_stnk'])->format('d M Y') }}</p>
                                                </div>
                                                @endif
                                                @if(isset($sd['tanggal_habis_stnk']) && $sd['tanggal_habis_stnk'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Berlaku s/d</p>
                                                    <p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_habis_stnk'])->format('d M Y') }}</p>
                                                </div>
                                                @endif
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Biaya</p>
                                                    <p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['biaya'] ?? $d->nominal ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                            </div>

                                        {{-- ── Service Part ── --}}
                                        @elseif($d->source_type === 'service_part')
                                            <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-3 gap-3">
                                                @if(isset($sd['nama_part']) && $sd['nama_part'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Nama Part</p>
                                                    <p class="text-xs font-semibold text-gray-700">{{ $sd['nama_part'] }}</p>
                                                </div>
                                                @endif
                                                @if(isset($sd['jumlah']) && $sd['jumlah'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Jumlah</p>
                                                    <p class="text-xs text-gray-700">{{ $sd['jumlah'] }}</p>
                                                </div>
                                                @endif
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Biaya</p>
                                                    <p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['biaya'] ?? $d->nominal ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                            </div>

                                        {{-- ── Service Asuransi ── --}}
                                        @elseif($d->source_type === 'service_asuransi')
                                            <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-3 gap-3">
                                                @if(isset($sd['no_klaim']) && $sd['no_klaim'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">No. Klaim</p>
                                                    <p class="text-xs font-mono text-gray-700">{{ $sd['no_klaim'] }}</p>
                                                </div>
                                                @endif
                                                @if(isset($sd['keterangan']) && $sd['keterangan'])
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Keterangan</p>
                                                    <p class="text-xs text-gray-600">{{ $sd['keterangan'] }}</p>
                                                </div>
                                                @endif
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Biaya</p>
                                                    <p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['biaya'] ?? $d->nominal ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                            </div>

                                        {{-- ── Fallback: generic info ── --}}
                                        @else
                                            <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-3 gap-3">
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Alasan</p>
                                                    <p class="text-xs text-gray-600">{{ $d->alasan_permintaan ?: '-' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-gray-400 uppercase">Total</p>
                                                    <p class="text-xs font-bold text-emerald-600">Rp {{ number_format($d->nominal ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        {{-- Legacy single item --}}
                                        <div class="px-4 py-3 text-xs text-gray-500">
                                            <span class="font-medium text-gray-700">{{ $d->barang_jasa ?: '-' }}</span>
                                            @if($d->qty) — {{ $d->qty }} {{ $d->satuan }} @endif
                                            @if($d->nominal) — <span class="font-semibold text-emerald-600">Rp {{ number_format($d->nominal, 0, ',', '.') }}</span> @endif
                                        </div>
                                    @endif

                                    {{-- Rekening Bank (kalau ada) --}}
                                    @if($d->nama_bank || $d->no_rekening || $d->nama_rekening || $d->informasi)
                                        <div class="mx-4 mb-3 mt-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                                            <p class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider mb-2">
                                                <i class="bi bi-bank mr-1"></i> Informasi Rekening Bank
                                            </p>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                @if($d->nama_bank)
                                                    <div>
                                                        <p class="text-[10px] text-amber-500 uppercase">Bank</p>
                                                        <p class="text-xs font-medium text-gray-700">{{ $d->nama_bank }}</p>
                                                    </div>
                                                @endif
                                                @if($d->no_rekening)
                                                    <div>
                                                        <p class="text-[10px] text-amber-500 uppercase">No. Rekening</p>
                                                        <p class="text-xs font-medium text-gray-700 font-mono">{{ $d->no_rekening }}</p>
                                                    </div>
                                                @endif
                                                @if($d->nama_rekening)
                                                    <div>
                                                        <p class="text-[10px] text-amber-500 uppercase">Atas Nama</p>
                                                        <p class="text-xs font-medium text-gray-700">{{ $d->nama_rekening }}</p>
                                                    </div>
                                                @endif
                                                @if($d->informasi)
                                                    <div class="col-span-full md:col-span-1">
                                                        <p class="text-[10px] text-amber-500 uppercase">Informasi</p>
                                                        <p class="text-xs text-gray-600">{{ $d->informasi }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400 text-sm">
                                <i class="fa fa-inbox text-3xl mb-3 block text-gray-300"></i>
                                Belum ada data Pembayaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="py-3 border-t border-gray-100">
            <x-pagination :paginator="$data" />
        </div>

    </div>
</div>


{{-- ===== MODAL DETAIL ===== --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4" style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa fa-file-lines text-blue-500"></i> Detail Pembayaran
                </h2>
                <p id="d_no_pr" class="text-xs text-gray-400 mt-0.5 font-mono"></p>
            </div>
            <button onclick="closeDetailModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="px-6 py-4 space-y-3">

            {{-- Baris 1: info utama 4 kolom --}}
            <div class="grid grid-cols-4 gap-3">
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Tanggal</p>
                    <p id="d_tanggal" class="text-sm font-medium text-gray-700"></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Pemohon</p>
                    <p id="d_pemohon" class="text-sm font-medium text-gray-700"></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Departemen</p>
                    <p id="d_departemen" class="text-sm font-medium text-gray-700"></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Status</p>
                    <span id="d_status_badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"></span>
                </div>
            </div>

            {{-- Detail Barang - Multiple Items --}}
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-100">
                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">Detail Items</p>
                </div>
                <div id="d_items_container" class="divide-y divide-gray-100">
                    <!-- Items will be populated by JavaScript -->
                </div>
                
                <!-- Fallback for old structure (single item) -->
                <div id="d_old_structure" class="hidden grid grid-cols-3 divide-x divide-y divide-gray-100">
                    <div class="px-4 py-2.5 col-span-2">
                        <p class="text-[10px] text-gray-400 mb-0.5">Barang/Jasa</p>
                        <p id="d_barang_jasa" class="text-sm font-medium text-gray-700"></p>
                    </div>
                    <div class="px-4 py-2.5">
                        <p class="text-[10px] text-gray-400 mb-0.5">Kode Barang</p>
                        <p id="d_kode_barang" class="text-sm font-mono text-gray-700"></p>
                    </div>
                    <div class="px-4 py-2.5">
                        <p class="text-[10px] text-gray-400 mb-0.5">Qty</p>
                        <p id="d_qty" class="text-sm font-medium text-gray-700"></p>
                    </div>
                    <div class="px-4 py-2.5">
                        <p class="text-[10px] text-gray-400 mb-0.5">Satuan</p>
                        <p id="d_satuan" class="text-sm font-medium text-gray-700"></p>
                    </div>
                    <div class="px-4 py-2.5">
                        <p class="text-[10px] text-gray-400 mb-0.5">Nominal</p>
                        <p id="d_nominal_old" class="text-sm font-semibold text-emerald-700"></p>
                    </div>
                </div>
            </div>

            {{-- Total & Alasan Permintaan --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-emerald-50 rounded-xl px-3 py-2.5 border border-emerald-100">
                    <p class="text-[10px] text-emerald-400 font-semibold uppercase tracking-wide mb-0.5">Total Nominal</p>
                    <p id="d_total_nominal" class="text-base font-bold text-emerald-700"></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Total Items</p>
                    <p id="d_total_items" class="text-base font-semibold text-gray-700"></p>
                </div>
            </div>

            {{-- Alasan Permintaan --}}
            <div class="bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                <p class="text-[10px] text-blue-400 font-semibold uppercase tracking-wide mb-1">Alasan Permintaan</p>
                <p id="d_alasan" class="text-sm text-blue-700"></p>
            </div>

            {{-- Info tambahan: persetujuan + catatan + terakhir diajukan dalam 1 baris --}}
            <div class="grid grid-cols-3 gap-3">
                <div id="d_approval_section" class="col-span-2 border border-gray-100 rounded-xl overflow-hidden hidden">
                    <div class="bg-gray-50 px-3 py-1.5 border-b border-gray-100">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">Info Persetujuan</p>
                    </div>
                    <div class="grid grid-cols-2 divide-x divide-gray-100">
                        <div class="px-3 py-2.5"><p class="text-[10px] text-gray-400 mb-0.5">Disetujui Oleh</p><p id="d_disetujui_oleh" class="text-sm font-medium text-gray-700"></p></div>
                        <div class="px-3 py-2.5"><p class="text-[10px] text-gray-400 mb-0.5">Tgl Persetujuan</p><p id="d_tgl_persetujuan" class="text-sm font-medium text-gray-700"></p></div>
                    </div>
                </div>

                <div class="bg-indigo-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-indigo-400 font-semibold uppercase tracking-wide mb-0.5">Terakhir Diajukan</p>
                    <p id="d_terakhir_diajukan" class="text-sm text-indigo-700"></p>
                </div>
            </div>

            {{-- Rekening Bank Section --}}
            <div id="d_rekening_section" class="hidden bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                <div class="flex items-center gap-2 mb-2">
                    <i class="bi bi-bank text-amber-600"></i>
                    <p class="text-xs text-amber-700 font-semibold uppercase tracking-wide">Informasi Rekening Bank</p>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-2">
                    <div id="d_nama_bank_wrapper" class="hidden">
                        <p class="text-[10px] text-amber-500 uppercase mb-0.5">Nama Bank</p>
                        <p id="d_nama_bank" class="text-sm text-gray-700 font-medium"></p>
                    </div>
                    <div id="d_no_rekening_wrapper" class="hidden">
                        <p class="text-[10px] text-amber-500 uppercase mb-0.5">No. Rekening</p>
                        <p id="d_no_rekening" class="text-sm text-gray-700 font-medium"></p>
                    </div>
                    <div id="d_nama_rekening_wrapper" class="hidden col-span-2">
                        <p class="text-[10px] text-amber-500 uppercase mb-0.5">Nama Pemilik Rekening</p>
                        <p id="d_nama_rekening" class="text-sm text-gray-700 font-medium"></p>
                    </div>
                </div>
                <div id="d_informasi_wrapper" class="hidden pt-2 border-t border-amber-300">
                    <p class="text-[10px] text-amber-500 uppercase mb-0.5">Informasi Tambahan</p>
                    <p id="d_informasi" class="text-sm text-gray-700"></p>
                </div>
            </div>

            <div id="d_catatan_section" class="hidden bg-red-50 border border-red-100 rounded-xl px-4 py-2.5">
                <p class="text-[10px] text-red-400 font-semibold uppercase tracking-wide mb-0.5">Catatan Penolakan</p>
                <p id="d_catatan" class="text-sm text-red-700"></p>
            </div>

        </div>

        <div class="px-6 pb-4">
            <button onclick="closeDetailModal()" class="w-full text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2 hover:bg-gray-50 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ===== MODAL TOLAK (superadmin) ===== --}}
@if ($role === 'superadmin')
<div id="tolakModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-sm flex-shrink-0"><i class="fa fa-times"></i></span>
                    Tolak Pembayaran
                </h2>
                <p id="tolakSubtitle" class="text-xs text-gray-500 mt-1 ml-10"></p>
            </div>
            <button onclick="closeTolakModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none"><i class="fa fa-times"></i></button>
        </div>
        <form id="tolakForm" action="" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="status" value="Ditolak">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="catatan" id="catatanTolak" rows="4" required placeholder="Tuliskan alasan penolakan..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-400 resize-none"></textarea>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeTolakModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-times-circle"></i> Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ===== MODAL HAPUS ===== --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl">
                <i class="fa fa-triangle-exclamation"></i>
            </div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Pembayaran?</h2>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                Kamu akan menghapus <strong id="deleteName" class="text-gray-700"></strong>. Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">Batal</button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5">
                <i class="fa fa-trash"></i> Hapus
            </button>
        </form>
    </div>
</div>

{{-- ===== MODAL APPROVE SERVICE (dengan upload bukti) ===== --}}
@if ($role === 'superadmin')
<div id="approveServiceModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-sm flex-shrink-0"><i class="fa fa-check"></i></span>
                    Setujui Pembayaran Service
                </h2>
                <p id="approveServiceSubtitle" class="text-xs text-gray-500 mt-1 ml-10"></p>
            </div>
            <button onclick="closeApproveServiceModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none"><i class="fa fa-times"></i></button>
        </div>
        <form id="approveServiceForm" action="" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Bukti Pembayaran <span class="text-gray-400 text-[10px]">(opsional, jpg/png/pdf maks 5MB)</span>
                </label>
                <input type="file" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Lampiran Tambahan <span class="text-gray-400 text-[10px]">(opsional, jpg/png/pdf maks 5MB)</span>
                </label>
                <input type="file" name="lampiran_tambahan" accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
            </div>
            <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                <i class="fa fa-info-circle mr-1"></i> Setelah disetujui, data service akan otomatis masuk ke riwayat service kendaraan.
            </p>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeApproveServiceModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-check"></i> Konfirmasi Setujui
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ===== POPUP ALERT ===== --}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
            <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4 space-y-0.5">
                @if(session('error'))<li>{{ session('error') }}</li>@endif
                @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg flex-shrink-0"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
</style>


<script>
// ── Approve Service Modal ─────────────────────────────────────
function openApproveServiceModal(pembayaranId, noPr) {
    const modal = document.getElementById('approveServiceModal');
    const form  = document.getElementById('approveServiceForm');
    const subtitle = document.getElementById('approveServiceSubtitle');
    if (!modal || !form) return;
    form.action = '/admin/pembayaran/' + pembayaranId + '/approve-service';
    subtitle.textContent = 'No PR: ' + noPr;
    form.reset();
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeApproveServiceModal() {
    const modal = document.getElementById('approveServiceModal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
}
document.getElementById('approveServiceModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeApproveServiceModal();
});

// ── Detail Modal ──────────────────────────────────────────────
function openDetailModal(pembayaranId) {
    // Show modal immediately with loading state
    var modal = document.getElementById('detailModal');
    modal.classList.remove('hidden'); 
    modal.classList.add('flex');
    
    // Show loading state
    document.getElementById('d_no_pr').innerText = 'Loading...';
    document.getElementById('d_items_container').innerHTML = '<div class="px-4 py-6 text-center text-gray-400"><i class="fa fa-spinner fa-spin mr-2"></i>Loading items...</div>';
    
    // Fetch data via AJAX
    fetch('/admin/pembayaran/' + pembayaranId + '/details')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateDetailModal(data.pembayaran);
            } else {
                alert('Gagal memuat detail: ' + (data.message || 'Unknown error'));
                closeDetailModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat detail');
            closeDetailModal();
        });
}

function populateDetailModal(pr) {
    // Basic info
    document.getElementById('d_no_pr').innerText = pr.no_pr;
    document.getElementById('d_tanggal').innerText = pr.tanggal_formatted;
    document.getElementById('d_pemohon').innerText = pr.pemohon;
    document.getElementById('d_departemen').innerText = pr.departemen;
    document.getElementById('d_alasan').innerText = pr.alasan_permintaan || '-';
    document.getElementById('d_terakhir_diajukan').innerText = pr.terakhir_diajukan_formatted || '-';
    
    // Status badge
    var statusBadge = document.getElementById('d_status_badge');
    statusBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium ' + pr.status_class;
    statusBadge.innerHTML = '<i class="fa fa-circle text-[6px]"></i> ' + pr.status;
    
    // Total info
    document.getElementById('d_total_nominal').innerText = 'Rp ' + pr.total_nominal_formatted;
    document.getElementById('d_total_items').innerText = pr.total_items + ' item' + (pr.total_items > 1 ? 's' : '');
    
    // Items container
    var itemsContainer = document.getElementById('d_items_container');
    var oldStructure = document.getElementById('d_old_structure');
    
    if (pr.items && pr.items.length > 0) {
        // New structure: multiple items
        itemsContainer.innerHTML = '';
        oldStructure.classList.add('hidden');
        
        pr.items.forEach(function(item, index) {
            var itemDiv = document.createElement('div');
            itemDiv.className = 'p-4';
            
            itemDiv.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Item #${index + 1}</span>
                    ${item.subtotal ? '<span class="text-sm font-semibold text-emerald-600">Rp ' + item.subtotal_formatted + '</span>' : ''}
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                    <div>
                        <p class="text-[10px] text-gray-400 mb-0.5 uppercase">Nama Barang</p>
                        <p class="text-sm font-medium text-gray-700">${item.nama_barang}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 mb-0.5 uppercase">Kategori</p>
                        <p class="text-sm text-gray-600">${item.kategori || '-'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 mb-0.5 uppercase">Qty</p>
                        <p class="text-sm font-medium text-gray-700">${item.qty} ${item.satuan || ''}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 mb-0.5 uppercase">Harga Satuan</p>
                        <p class="text-sm text-gray-600">${item.harga_satuan ? 'Rp ' + item.harga_satuan_formatted : '-'}</p>
                    </div>
                </div>
                ${item.part_number || item.serial_number || item.spesifikasi || item.merk || item.keterangan ? `
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                    ${item.part_number ? '<div><span class="text-gray-400">Part#:</span> <span class="text-gray-600">' + item.part_number + '</span></div>' : ''}
                    ${item.serial_number ? '<div><span class="text-gray-400">Serial#:</span> <span class="text-gray-600">' + item.serial_number + '</span></div>' : ''}
                    ${item.spesifikasi ? '<div><span class="text-gray-400">Spesifikasi:</span> <span class="text-gray-600">' + item.spesifikasi + '</span></div>' : ''}
                    ${item.merk ? '<div><span class="text-gray-400">Merk:</span> <span class="text-gray-600">' + item.merk + '</span></div>' : ''}
                    ${item.posisi ? '<div><span class="text-gray-400">Posisi:</span> <span class="text-gray-600">' + item.posisi + '</span></div>' : ''}
                    ${item.keterangan ? '<div class="col-span-full"><span class="text-gray-400">Keterangan:</span> <span class="text-gray-600">' + item.keterangan + '</span></div>' : ''}
                </div>` : ''}
            `;
            
            itemsContainer.appendChild(itemDiv);
        });
    } else {
        // Old structure: fallback for legacy data
        oldStructure.classList.remove('hidden');
        itemsContainer.innerHTML = '';
        document.getElementById('d_barang_jasa').innerText = pr.barang_jasa || '-';
        document.getElementById('d_kode_barang').innerText = pr.kode_barang || '-';
        document.getElementById('d_qty').innerText = pr.qty || '-';
        document.getElementById('d_satuan').innerText = pr.satuan || '-';
        document.getElementById('d_nominal_old').innerText = pr.nominal ? 'Rp ' + pr.nominal_formatted : '-';
    }
    
    // Approval section
    var approvalSection = document.getElementById('d_approval_section');
    if (pr.disetujui_oleh) {
        document.getElementById('d_disetujui_oleh').innerText = pr.disetujui_oleh;
        document.getElementById('d_tgl_persetujuan').innerText = pr.tanggal_persetujuan_formatted || '-';
        approvalSection.classList.remove('hidden');
    } else {
        approvalSection.classList.add('hidden');
    }
    
    // Rekening bank section
    var rekeningSection = document.getElementById('d_rekening_section');
    var hasRekening = pr.nama_bank || pr.no_rekening || pr.nama_rekening || pr.informasi;
    
    if (hasRekening) {
        // Show/hide individual fields
        var namaBankWrapper = document.getElementById('d_nama_bank_wrapper');
        var noRekeningWrapper = document.getElementById('d_no_rekening_wrapper');
        var namaRekeningWrapper = document.getElementById('d_nama_rekening_wrapper');
        var informasiWrapper = document.getElementById('d_informasi_wrapper');
        
        if (pr.nama_bank) {
            document.getElementById('d_nama_bank').innerText = pr.nama_bank;
            namaBankWrapper.classList.remove('hidden');
        } else {
            namaBankWrapper.classList.add('hidden');
        }
        
        if (pr.no_rekening) {
            document.getElementById('d_no_rekening').innerText = pr.no_rekening;
            noRekeningWrapper.classList.remove('hidden');
        } else {
            noRekeningWrapper.classList.add('hidden');
        }
        
        if (pr.nama_rekening) {
            document.getElementById('d_nama_rekening').innerText = pr.nama_rekening;
            namaRekeningWrapper.classList.remove('hidden');
        } else {
            namaRekeningWrapper.classList.add('hidden');
        }
        
        if (pr.informasi) {
            document.getElementById('d_informasi').innerText = pr.informasi;
            informasiWrapper.classList.remove('hidden');
        } else {
            informasiWrapper.classList.add('hidden');
        }
        
        rekeningSection.classList.remove('hidden');
    } else {
        rekeningSection.classList.add('hidden');
    }
    
    // Catatan section
    var catatanSection = document.getElementById('d_catatan_section');
    if (pr.catatan && pr.catatan.trim()) {
        document.getElementById('d_catatan').innerText = pr.catatan;
        catatanSection.classList.remove('hidden');
    } else {
        catatanSection.classList.add('hidden');
    }
}
function closeDetailModal() {
    var m = document.getElementById('detailModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('detailModal').addEventListener('click', function(e) { if (e.target === this) closeDetailModal(); });

// ── Tolak Modal ───────────────────────────────────────────────
@if ($role === 'superadmin')
var tolakModal = document.getElementById('tolakModal');
var tolakForm  = document.getElementById('tolakForm');
function openTolakModal(id, noPr) {
    tolakForm.action = '/admin/pembayaran/' + id + '/status';
    document.getElementById('tolakSubtitle').innerText = 'No PR: ' + noPr;
    document.getElementById('catatanTolak').value = '';
    tolakModal.classList.remove('hidden'); tolakModal.classList.add('flex');
    setTimeout(function() { document.getElementById('catatanTolak').focus(); }, 100);
}
function closeTolakModal() {
    tolakModal.classList.add('hidden'); tolakModal.classList.remove('flex');
}
tolakModal.addEventListener('click', function(e) { if (e.target === this) closeTolakModal(); });
@endif

// ── Delete Modal ──────────────────────────────────────────────
var deleteModal = document.getElementById('deleteModal');
var deleteForm  = document.getElementById('deleteForm');
function triggerDelete(btn) {
    deleteForm.action = btn.dataset.action;
    document.getElementById('deleteName').innerText = btn.dataset.name || 'ini';
    deleteModal.classList.remove('hidden'); deleteModal.classList.add('flex');
}
function closeDeleteModal() {
    deleteModal.classList.add('hidden'); deleteModal.classList.remove('flex');
}
deleteModal.addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });

// ── Popup Alert ───────────────────────────────────────────────
(function() {
    var overlay = document.getElementById('alertOverlay');
    var box     = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(function() {
        overlay.style.opacity = '1'; overlay.style.pointerEvents = 'auto';
        box.style.transform = 'translateY(0)';
    }, 80);
    var timer = setTimeout(closeAlert, 4500);
    overlay.addEventListener('click', function(e) { if (e.target === overlay) closeAlert(); });
    function closeAlert() {
        clearTimeout(timer);
        overlay.style.opacity = '0'; overlay.style.pointerEvents = 'none';
        box.style.transform = 'translateY(-16px)';
    }
    window.closeAlert = closeAlert;
})();

// ========================================
// APPROVAL MODAL FUNCTIONS
// ========================================
function openApprovalModal(pembayaranId) {
    window.dispatchEvent(new CustomEvent('open-approval-modal', {
        detail: { id: pembayaranId }
    }));
}

function openRejectModal(pembayaranId) {
    window.dispatchEvent(new CustomEvent('open-approval-modal', {
        detail: { id: pembayaranId, action: 'reject' }
    }));
}

// ========================================
// CHART INITIALIZATION
// ========================================
const pembayaranChartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function() {
    initPembayaranCharts({ filter_type: 'month' });

    document.addEventListener('chartFilterChange', function(e) {
        if (e.detail.filterId === 'pembayaranChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
                departemen:  e.detail.categoryId ?? '',
            };
            updatePembayaranCharts(filters);
        }
    });
});

async function initPembayaranCharts(filters) {
    try {
        await pembayaranChartManager.initChartsFromAPI('pembayaran', {
            pie:  'pembayaranPieChart',
            bar:  'pembayaranBarChart',
            line: 'pembayaranLineChart',
        }, filters);
    } catch (error) {
        console.error('Error loading pembayaran charts:', error);
    }
}

async function updatePembayaranCharts(filters) {
    try {
        const barOptions = { scrollable: filters.filter_type === 'custom' };
        await pembayaranChartManager.updateChartsFromAPI('pembayaran', {
            pie:  'pembayaranPieChart',
            bar:  'pembayaranBarChart',
            line: 'pembayaranLineChart',
        }, filters, barOptions);
    } catch (error) {
        console.error('Error updating pembayaran charts:', error);
    }
}

// ── Expandable Rows ───────────────────────────────────────────
function toggleExpand(id) {
    var expandRow = document.getElementById('expand-' + id);
    var chevron   = document.getElementById('chevron-' + id);
    if (!expandRow) return;

    var isHidden = expandRow.classList.contains('hidden');

    if (isHidden) {
        expandRow.classList.remove('hidden');
        chevron.style.transform = 'rotate(90deg)';
        chevron.classList.remove('text-gray-400');
        chevron.classList.add('text-blue-500');
    } else {
        expandRow.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
        chevron.classList.remove('text-blue-500');
        chevron.classList.add('text-gray-400');
    }
}

// Prevent row click from firing when clicking action buttons/forms
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('td button, td form, td a').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
</script>

@endsection
