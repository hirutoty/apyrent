@extends('admin.layouts.app')
@section('title', 'Pembayaran')
@section('content')

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
            <h2 class="text-2xl font-bold text-emerald-600 mt-2">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h2>
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
    <x-chart-container id="pembayaranChartContainer" layout="stacked"
        pieTitle="Distribusi Status" pieId="pembayaranPieChart"
        barTitle="Nominal Pembayaran per Bulan" barId="pembayaranBarChart"
        lineTitle="Trend Nominal Pembayaran" lineId="pembayaranLineChart"
        :showStats="true" :statsData="[]" />

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

        {{-- NAV TABS --}}
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 -mb-px overflow-x-auto">
                @if ($role === 'superadmin')
                    @foreach ([
                        ['key'=>'Pending',   'label'=>'Pending',   'icon'=>'bi bi-hourglass-split',  'count'=>$totalPending,   'color'=>'yellow'],
                        ['key'=>'Diajukan',  'label'=>'Diajukan',  'icon'=>'bi bi-paper-plane',       'count'=>$totalDiajukan,  'color'=>'indigo'],
                        ['key'=>'Disetujui', 'label'=>'Disetujui', 'icon'=>'bi bi-check-circle-fill', 'count'=>$totalDisetujui, 'color'=>'green'],
                        ['key'=>'Ditolak',   'label'=>'Ditolak',   'icon'=>'bi bi-x-circle-fill',     'count'=>$totalDitolak,   'color'=>'red'],
                    ] as $t)
                        @php $isActive = $tab === $t['key']; @endphp
                        <a href="{{ route('pembayaran.index', ['tab'=>$t['key'],'sort'=>$sort]) }}"
                            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                                {{ $isActive ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                            <i class="{{ $t['icon'] }}"></i> {{ $t['label'] }}
                            @php $badgeCls = match($t['color']) { 'green'=>'bg-green-100 text-green-700','red'=>'bg-red-100 text-red-700','indigo'=>'bg-indigo-100 text-indigo-700','yellow'=>'bg-yellow-100 text-yellow-700',default=>'bg-gray-100 text-gray-700' }; @endphp
                            <span class="ml-1 text-xs font-bold px-2 py-0.5 rounded-full {{ $badgeCls }}">{{ $t['count'] }}</span>
                        </a>
                    @endforeach
                @else
                    @foreach ([
                        ['key'=>'semua',    'label'=>'Semua',    'icon'=>'bi bi-list-ul',           'count'=>$totalPR,        'badge'=>'bg-blue-100 text-blue-700'],
                        ['key'=>'Pending',  'label'=>'Pending',  'icon'=>'bi bi-hourglass-split',   'count'=>$totalPending,   'badge'=>'bg-yellow-100 text-yellow-700'],
                        ['key'=>'Diajukan', 'label'=>'Diajukan', 'icon'=>'bi bi-paper-plane',       'count'=>$totalDiajukan,  'badge'=>'bg-indigo-100 text-indigo-700'],
                        ['key'=>'Disetujui','label'=>'Disetujui','icon'=>'bi bi-check-circle-fill', 'count'=>$totalDisetujui, 'badge'=>'bg-green-100 text-green-700'],
                        ['key'=>'Ditolak',  'label'=>'Ditolak',  'icon'=>'bi bi-x-circle-fill',    'count'=>$totalDitolak,   'badge'=>'bg-red-100 text-red-700'],
                    ] as $t)
                        @php $isActive = $tab === $t['key']; @endphp
                        <a href="{{ route('pembayaran.index', ['tab'=>$t['key'],'sort'=>$sort]) }}"
                            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                                {{ $isActive ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                            <i class="{{ $t['icon'] }}"></i> {{ $t['label'] }}
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
                {{-- Bulk action buttons superadmin --}}
                @if($role === 'superadmin' && $tab === 'Pending')
                    <button type="button" onclick="openBulkApproveModal()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fa fa-check-double text-xs"></i> Approve
                    </button>
                    <button type="button" onclick="openBulkRejectModal()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        <i class="fa fa-times-circle text-xs"></i> Reject
                    </button>
                @endif
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 whitespace-nowrap">Urutkan:</span>
                    <a href="{{ route('pembayaran.index', array_merge(request()->except('sort'), ['sort'=>'terbaru'])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors {{ $sort==='terbaru'?'bg-blue-600 text-white border-blue-600':'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        <i class="bi bi-sort-down"></i> Terbaru
                    </a>
                    <a href="{{ route('pembayaran.index', array_merge(request()->except('sort'), ['sort'=>'terlama'])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors {{ $sort==='terlama'?'bg-blue-600 text-white border-blue-600':'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        <i class="bi bi-sort-up"></i> Terlama
                    </a>
                </div>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="month" name="bulan" value="{{ $bulan ?? '' }}"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                @if($role === 'superadmin')
                    <select name="departemen" class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">Semua Departemen</option>
                        @foreach(['Keuangan','Produksi','HRD','Purchase','Sales','Marketing','IT'] as $dept)
                            <option value="{{ $dept }}" {{ ($deptFilter??'') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    <i class="fa fa-filter text-xs mr-1"></i> Filter
                </button>
                @if($bulan || $deptFilter)
                    <a href="{{ route('pembayaran.index', ['tab'=>$tab,'sort'=>$sort]) }}"
                        class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Reset</a>
                @endif
            </form>
        </div>

        {{-- ================================================================
             GROUPED ACCORDION BY JENIS
        ================================================================ --}}
        @php
            $grouped = $data->getCollection()->groupBy(function($d) {
                return $d->source_type ? $d->source_type_name : 'Belanja';
            });

            $jenisIcons = [
                'Pajak Kendaraan'       => ['icon'=>'bi bi-card-checklist',     'color'=>'blue'],
                'Perpanjangan Pajak'    => ['icon'=>'bi bi-arrow-repeat',        'color'=>'blue'],
                'Asuransi Kendaraan'    => ['icon'=>'bi bi-shield-check',         'color'=>'purple'],
                'Perpanjangan Asuransi' => ['icon'=>'bi bi-shield-plus',          'color'=>'purple'],
                'Service Part'          => ['icon'=>'bi bi-tools',                'color'=>'orange'],
                'Service Asuransi'      => ['icon'=>'bi bi-wrench-adjustable',    'color'=>'orange'],
                'GPS Kendaraan'         => ['icon'=>'bi bi-geo-alt-fill',         'color'=>'green'],
                'Perpanjangan GPS'      => ['icon'=>'bi bi-arrow-repeat',         'color'=>'green'],
                'KIR'                   => ['icon'=>'bi bi-patch-check',          'color'=>'teal'],
                'Perpanjangan KIR'      => ['icon'=>'bi bi-arrow-repeat',         'color'=>'teal'],
                'STNK'                  => ['icon'=>'bi bi-file-earmark-text',    'color'=>'indigo'],
                'Purchase Order'        => ['icon'=>'bi bi-bag-check',            'color'=>'cyan'],
                'Belanja'               => ['icon'=>'bi bi-cart3',                'color'=>'gray'],
            ];
            $colorMap = [
                'blue'   => ['bg'=>'bg-blue-50',   'border'=>'border-blue-200',   'text'=>'text-blue-700',   'badge'=>'bg-blue-100 text-blue-700',   'hdr'=>'bg-blue-50/60'],
                'purple' => ['bg'=>'bg-purple-50', 'border'=>'border-purple-200', 'text'=>'text-purple-700', 'badge'=>'bg-purple-100 text-purple-700','hdr'=>'bg-purple-50/60'],
                'orange' => ['bg'=>'bg-orange-50', 'border'=>'border-orange-200', 'text'=>'text-orange-700', 'badge'=>'bg-orange-100 text-orange-700','hdr'=>'bg-orange-50/60'],
                'green'  => ['bg'=>'bg-green-50',  'border'=>'border-green-200',  'text'=>'text-green-700',  'badge'=>'bg-green-100 text-green-700',  'hdr'=>'bg-green-50/60'],
                'teal'   => ['bg'=>'bg-teal-50',   'border'=>'border-teal-200',   'text'=>'text-teal-700',   'badge'=>'bg-teal-100 text-teal-700',    'hdr'=>'bg-teal-50/60'],
                'indigo' => ['bg'=>'bg-indigo-50', 'border'=>'border-indigo-200', 'text'=>'text-indigo-700', 'badge'=>'bg-indigo-100 text-indigo-700','hdr'=>'bg-indigo-50/60'],
                'cyan'   => ['bg'=>'bg-cyan-50',   'border'=>'border-cyan-200',   'text'=>'text-cyan-700',   'badge'=>'bg-cyan-100 text-cyan-700',    'hdr'=>'bg-cyan-50/60'],
                'gray'   => ['bg'=>'bg-gray-50',   'border'=>'border-gray-200',   'text'=>'text-gray-600',   'badge'=>'bg-gray-100 text-gray-600',    'hdr'=>'bg-gray-50/60'],
            ];
        @endphp

        @if($data->isEmpty())
            <div class="text-center py-16 text-gray-400 text-sm">
                <i class="fa fa-inbox text-4xl mb-3 block text-gray-300"></i>
                Belum ada data Pembayaran
            </div>
        @else
            <div class="divide-y divide-gray-100">
            @foreach($grouped as $jenis => $items)
                @php
                    $cfg   = $jenisIcons[$jenis] ?? ['icon'=>'bi bi-wallet2','color'=>'gray'];
                    $clr   = $colorMap[$cfg['color']] ?? $colorMap['gray'];
                    $gIdx  = $loop->index;
                    $total = $items->sum(fn($i) => $i->total_nominal ?? 0);
                    $pendingCount = $items->where('status','Pending')->count();
                    $grpPendingIds = $items->where('status','Pending')->pluck('id')->values()->toJson();
                @endphp

                {{-- GROUP HEADER --}}
                <div class="border-b border-gray-100">
                    <button type="button" onclick="toggleGroup({{ $gIdx }})"
                        class="w-full flex items-center gap-3 px-5 py-3.5 text-left hover:bg-gray-50/80 transition-colors {{ $clr['hdr'] }}">
                        <i id="grp-chevron-{{ $gIdx }}"
                            class="fa fa-chevron-right text-[11px] text-gray-400 transition-transform duration-200 flex-shrink-0"></i>
                        <span class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 {{ $clr['bg'] }} {{ $clr['border'] }} border">
                            <i class="{{ $cfg['icon'] }} {{ $clr['text'] }} text-sm"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <span class="text-sm font-bold text-gray-800">{{ $jenis }}</span>
                            <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full {{ $clr['badge'] }}">
                                {{ $items->count() }} pengajuan
                            </span>
                            @if($pendingCount > 0 && $role === 'superadmin' && $tab === 'Pending')
                                <span class="ml-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700">
                                    {{ $pendingCount }} pending
                                </span>
                            @endif
                        </div>
                        <span class="text-sm font-bold text-emerald-600 flex-shrink-0 mr-2">
                            Rp {{ number_format($total,0,',','.') }}
                        </span>
                        {{-- Quick approve/reject per grup --}}
                        @if($role === 'superadmin' && $tab === 'Pending' && $pendingCount > 0)
                            <div class="flex gap-1.5 flex-shrink-0" onclick="event.stopPropagation()">
                                <button type="button"
                                    onclick="openBulkApproveModal('{{ addslashes($jenis) }}', {{ $grpPendingIds }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                    <i class="fa fa-check text-[10px]"></i> Approve
                                </button>
                                <button type="button"
                                    onclick="openBulkRejectModal('{{ addslashes($jenis) }}', {{ $grpPendingIds }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                    <i class="fa fa-times text-[10px]"></i> Reject
                                </button>
                            </div>
                        @endif
                    </button>

                    {{-- GROUP BODY TABLE --}}
                    <div id="grp-body-{{ $gIdx }}" class="hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50/80 border-b border-gray-100">
                                        @if($role === 'superadmin' && $tab === 'Pending')
                                            <th class="w-9 px-3 py-2.5 text-center">
                                                <input type="checkbox" class="grp-check-all rounded text-blue-600 cursor-pointer"
                                                    data-gidx="{{ $gIdx }}" onchange="toggleGroupCheck(this,{{ $gIdx }})">
                                            </th>
                                        @endif
                                        <th class="w-6 px-2 py-2.5"></th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">No PR</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Tanggal</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Departemen</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Pemohon</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Keterangan</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Items</th>
                                        <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Nominal</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Status</th>
                                        <th class="text-center text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $di => $d)
                                    @php
                                        $bc = match($d->status) {
                                            'Disetujui'          => 'bg-green-100 text-green-600',
                                            'Ditolak'            => 'bg-red-100 text-red-600',
                                            'Diajukan'           => 'bg-indigo-100 text-indigo-600',
                                            'Pending'            => 'bg-yellow-100 text-yellow-600',
                                            'Disetujui Sebagian' => 'bg-teal-100 text-teal-700',
                                            default              => 'bg-gray-100 text-gray-500',
                                        };
                                        $rowId = 'row-'.$gIdx.'-'.$di;
                                        if($d->items->count()>0) { $itemCount=$d->items->count(); }
                                        elseif($d->source_type==='gps'&&!empty($d->source_data['gps_items'])) { $itemCount=count($d->source_data['gps_items']); }
                                        else { $itemCount=1; }
                                    @endphp
                                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/40 hover:bg-blue-50/30 transition-colors cursor-pointer"
                                        onclick="toggleRowExpand('{{ $rowId }}')">

                                        {{-- Checkbox --}}
                                        @if($role === 'superadmin' && $tab === 'Pending')
                                            <td class="px-3 py-3 text-center" onclick="event.stopPropagation()">
                                                @if($d->status === 'Pending')
                                                    <input type="checkbox"
                                                        class="item-check grp-{{ $gIdx }}-check rounded text-blue-600 cursor-pointer"
                                                        value="{{ $d->id }}"
                                                        data-no-pr="{{ $d->no_pr }}"
                                                        data-jenis="{{ $jenis }}">
                                                @endif
                                            </td>
                                        @endif

                                        <td class="px-2 py-3 text-center">
                                            <i id="row-chv-{{ $rowId }}" class="fa fa-chevron-right text-[10px] text-gray-300 transition-transform duration-200"></i>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->no_pr }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-xs text-gray-500 whitespace-nowrap">
                                            {{ $d->tanggal ? \Carbon\Carbon::parse($d->tanggal)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="text-xs font-semibold text-gray-700">
                                                <i class="fa fa-building text-blue-400 text-[10px] mr-1"></i>{{ $d->departemen ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-xs text-gray-700">{{ $d->pemohon ?? '-' }}</td>
                                        <td class="px-3 py-3">
                                            @if($d->keterangan)
                                                <span class="text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded max-w-[130px] block truncate" title="{{ $d->keterangan }}">{{ $d->keterangan }}</span>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-600">
                                                <i class="fa fa-boxes text-blue-400 text-[10px]"></i>
                                                {{ $itemCount }} item{{ $itemCount > 1 ? 's' : '' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <span class="text-xs font-semibold text-emerald-600">Rp {{ number_format($d->total_nominal??0,0,',','.') }}</span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $bc }}">
                                                <i class="fa fa-circle text-[6px]"></i> {{ $d->status ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-center gap-1 flex-wrap">

                                                {{-- Detail --}}
                                                <button type="button" onclick="openDetailModal({{ $d->id }})"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors border border-blue-200">
                                                    <i class="fa fa-eye text-[10px]"></i> Detail
                                                </button>

                                                @if($role === 'superadmin')
                                                    @if($d->status === 'Pending')
                                                        @if($d->source_type)
                                                            <button type="button"
                                                                onclick="openSingleApproveModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                                <i class="fa fa-check text-[10px]"></i> Approve
                                                            </button>
                                                            <button type="button"
                                                                onclick="openSingleRejectModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                                                <i class="fa fa-times text-[10px]"></i> Reject
                                                            </button>
                                                        @else
                                                            @if($d->tipe_pembayaran === 'service')
                                                                <button type="button" onclick="openApproveServiceModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                                    <i class="fa fa-check text-[10px]"></i> Setujui
                                                                </button>
                                                            @else
                                                                <form action="{{ route('pembayaran.status', $d->id) }}" method="POST" class="inline">
                                                                    @csrf <input type="hidden" name="status" value="Disetujui">
                                                                    <button type="submit" onclick="return confirm('Setujui pembayaran {{ $d->no_pr }}?')"
                                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                                        <i class="fa fa-check text-[10px]"></i> Setujui
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <button type="button" onclick="openTolakModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                                                <i class="fa fa-times text-[10px]"></i> Tolak
                                                            </button>
                                                        @endif
                                                    @elseif($d->status === 'Diajukan')
                                                        @if($d->tipe_pembayaran === 'service')
                                                            <button type="button" onclick="openApproveServiceModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                                <i class="fa fa-check text-[10px]"></i> Setujui
                                                            </button>
                                                        @else
                                                            <form action="{{ route('pembayaran.status', $d->id) }}" method="POST" class="inline">
                                                                @csrf <input type="hidden" name="status" value="Disetujui">
                                                                <button type="submit" onclick="return confirm('Setujui pembayaran {{ $d->no_pr }}?')"
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                                    <i class="fa fa-check text-[10px]"></i> Setujui
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <button type="button" onclick="openTolakModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                                            <i class="fa fa-times text-[10px]"></i> Tolak
                                                        </button>
                                                    @elseif($d->status === 'Ditolak' && $d->source_type && $d->can_edit)
                                                        <a href="{{ route('pembayaran.edit-rejected', $d->id) }}"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors border border-amber-200">
                                                            <i class="fa fa-edit text-[10px]"></i> Edit & Ajukan Ulang
                                                        </a>
                                                    @elseif($d->status === 'Disetujui' && $d->source_type && $d->target_id)
                                                        <a href="{{ route(match($d->source_type) { 'asuransi_kendaraan'=>'asuransi-kendaraan.index','pajak'=>'pajak.index',default=>'pembayaran.index' }) }}" target="_blank"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors border border-indigo-200">
                                                            <i class="bi bi-box-arrow-up-right text-[10px]"></i> Lihat Data
                                                        </a>
                                                    @endif
                                                @else
                                                    @if(!in_array($d->status, ['Diajukan','Disetujui']))
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
                                                    @if(in_array($d->status, ['Pending','Ditolak']))
                                                        <form action="{{ route('pembayaran.ajukan', $d->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" onclick="return confirm('Ajukan pembayaran {{ $d->no_pr }}?')"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors border border-indigo-200">
                                                                <i class="fa fa-paper-plane text-[10px]"></i> Ajukan
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                            </div>
                                        </td>
                                    </tr>

                                    {{-- ROW DETAIL EXPAND --}}
                                    <tr id="row-expand-{{ $rowId }}" class="hidden bg-blue-50/20 border-t-0">
                                        <td colspan="{{ ($role==='superadmin'&&$tab==='Pending') ? 11 : 10 }}" class="px-6 pb-4 pt-2">
                                            @php $sd = $d->source_data ?? []; @endphp
                                            <div class="rounded-xl border border-blue-100 bg-white overflow-hidden shadow-sm">

                                                {{-- Items --}}
                                                @if($d->items->count() > 0)
                                                    <div class="px-4 pt-3 pb-1">
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                                            <i class="fa fa-list-ul mr-1"></i> Detail Items ({{ $d->items->count() }})
                                                        </p>
                                                    </div>
                                                    <table class="w-full text-xs">
                                                        <thead><tr class="bg-gray-50 border-y border-gray-100">
                                                            <th class="text-left px-4 py-2 font-semibold text-gray-500">#</th>
                                                            <th class="text-left px-4 py-2 font-semibold text-gray-500">Nama Barang</th>
                                                            <th class="text-left px-4 py-2 font-semibold text-gray-500">Qty</th>
                                                            <th class="text-right px-4 py-2 font-semibold text-gray-500">Harga Satuan</th>
                                                            <th class="text-right px-4 py-2 font-semibold text-gray-500">Subtotal</th>
                                                            <th class="text-left px-4 py-2 font-semibold text-gray-500">Keterangan</th>
                                                        </tr></thead>
                                                        <tbody>
                                                            @foreach($d->items as $idx => $item)
                                                                <tr class="border-t border-gray-50 {{ $idx%2===0?'bg-white':'bg-gray-50/50' }}">
                                                                    <td class="px-4 py-2 text-gray-400">{{ $idx+1 }}</td>
                                                                    <td class="px-4 py-2 font-medium text-gray-700">{{ $item->nama_barang }}</td>
                                                                    <td class="px-4 py-2 text-gray-600">{{ $item->qty }} {{ $item->satuan }}</td>
                                                                    <td class="px-4 py-2 text-right text-gray-600">{{ $item->harga_satuan ? 'Rp '.number_format($item->harga_satuan,0,',','.') : '-' }}</td>
                                                                    <td class="px-4 py-2 text-right font-semibold text-emerald-600">{{ $item->subtotal ? 'Rp '.number_format($item->subtotal,0,',','.') : '-' }}</td>
                                                                    <td class="px-4 py-2 text-gray-400 max-w-[160px] truncate">{{ $item->keterangan ?: '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                            <tr class="border-t-2 border-gray-200 bg-gray-50">
                                                                <td colspan="4" class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Total</td>
                                                                <td class="px-4 py-2 text-right text-sm font-bold text-emerald-600">Rp {{ number_format($d->total_nominal,0,',','.') }}</td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                @elseif($d->source_type)
                                                    <div class="flex items-center justify-between px-4 pt-3 pb-2 border-b border-gray-100">
                                                        <p class="text-[10px] font-semibold text-purple-500 uppercase tracking-wider flex items-center gap-1.5">
                                                            <i class="bi bi-wallet2"></i> Detail Pengeluaran — {{ $d->source_type_name }}
                                                        </p>
                                                        <span class="text-xs font-bold text-emerald-600">Total: Rp {{ number_format($d->nominal??0,0,',','.') }}</span>
                                                    </div>
                                                    @php $kendaraan = isset($sd['kendaraan_id']) ? \App\Models\Kendaraan::find($sd['kendaraan_id']) : null; @endphp
                                                    @if($kendaraan || isset($sd['tanggal_bayar']) || isset($sd['tanggal_habis']))
                                                        <div class="px-4 py-2.5 grid grid-cols-2 md:grid-cols-4 gap-3 bg-gray-50/50 border-b border-gray-100">
                                                            @if($kendaraan)
                                                                <div><p class="text-[10px] text-gray-400 uppercase">Kendaraan</p><p class="text-xs font-semibold text-gray-700">{{ $kendaraan->nopol }} — {{ $kendaraan->merk }}</p></div>
                                                            @endif
                                                            @if(isset($sd['tanggal_bayar']))
                                                                <div><p class="text-[10px] text-gray-400 uppercase">Tgl Bayar</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_bayar'])->format('d M Y') }}</p></div>
                                                            @endif
                                                            @if(isset($sd['tanggal_habis']))
                                                                <div><p class="text-[10px] text-gray-400 uppercase">Berlaku s/d</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_habis'])->format('d M Y') }}</p></div>
                                                            @endif
                                                            @if(isset($sd['keterangan']) && $sd['keterangan'])
                                                                <div><p class="text-[10px] text-gray-400 uppercase">Keterangan</p><p class="text-xs text-gray-600">{{ $sd['keterangan'] }}</p></div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    {{-- Pajak --}}
                                                    @if($d->source_type === 'pajak')
                                                        <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                            <div><p class="text-[10px] text-gray-400 uppercase">Jenis Pajak</p><p class="text-xs font-semibold text-gray-700">{{ $sd['jenis_pajak'] ?? '-' }}</p></div>
                                                            @if(isset($sd['tahun_pajak']))<div><p class="text-[10px] text-gray-400 uppercase">Tahun Pajak</p><p class="text-xs text-gray-700">{{ $sd['tahun_pajak'] }}</p></div>@endif
                                                            @if(isset($sd['tanggal_jatuh_tempo']))<div><p class="text-[10px] text-gray-400 uppercase">Jatuh Tempo</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_jatuh_tempo'])->format('d M Y') }}</p></div>@endif
                                                            <div><p class="text-[10px] text-gray-400 uppercase">Nominal</p><p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['nominal']??$d->nominal??0,0,',','.') }}</p></div>
                                                        </div>
                                                    {{-- Asuransi --}}
                                                    @elseif($d->source_type === 'asuransi_kendaraan')
                                                        @php $asuransi = isset($sd['asuransi_id']) ? \App\Models\Asuransi::find($sd['asuransi_id']) : null; $jenisAsr = isset($sd['jenis_asuransi_id']) ? \App\Models\JenisAsuransi::find($sd['jenis_asuransi_id']) : null; @endphp
                                                        <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                            <div><p class="text-[10px] text-gray-400 uppercase">Perusahaan</p><p class="text-xs font-semibold text-gray-700">{{ $asuransi->nama_asuransi ?? '-' }}</p></div>
                                                            <div><p class="text-[10px] text-gray-400 uppercase">Jenis</p><p class="text-xs text-gray-700">{{ $jenisAsr->nama_jenis ?? '-' }}</p></div>
                                                            @if(isset($sd['no_polis']))<div><p class="text-[10px] text-gray-400 uppercase">No. Polis</p><p class="text-xs font-mono text-gray-700">{{ $sd['no_polis'] }}</p></div>@endif
                                                            <div><p class="text-[10px] text-gray-400 uppercase">Premi</p><p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['premi']??$d->nominal??0,0,',','.') }}</p></div>
                                                        </div>
                                                    {{-- KIR --}}
                                                    @elseif(in_array($d->source_type,['kir','kir_perpanjang']))
                                                        <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                            @if(isset($sd['no_kir']))<div><p class="text-[10px] text-gray-400 uppercase">No. KIR</p><p class="text-xs font-mono text-gray-700">{{ $sd['no_kir'] }}</p></div>@endif
                                                            @if(isset($sd['tanggal_kir']))<div><p class="text-[10px] text-gray-400 uppercase">Tgl KIR</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_kir'])->format('d M Y') }}</p></div>@endif
                                                            @if(isset($sd['tanggal_habis_kir']))<div><p class="text-[10px] text-gray-400 uppercase">Berlaku s/d</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_habis_kir'])->format('d M Y') }}</p></div>@endif
                                                            <div><p class="text-[10px] text-gray-400 uppercase">Biaya</p><p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['biaya']??$d->nominal??0,0,',','.') }}</p></div>
                                                        </div>
                                                    {{-- Fallback --}}
                                                    @else
                                                        <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-3 gap-3">
                                                            <div><p class="text-[10px] text-gray-400 uppercase">Alasan</p><p class="text-xs text-gray-600">{{ $d->alasan_permintaan ?: '-' }}</p></div>
                                                            <div><p class="text-[10px] text-gray-400 uppercase">Total</p><p class="text-xs font-bold text-emerald-600">Rp {{ number_format($d->nominal??0,0,',','.') }}</p></div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="px-4 py-3 text-xs text-gray-500">
                                                        <span class="font-medium text-gray-700">{{ $d->barang_jasa ?: '-' }}</span>
                                                        @if($d->qty) — {{ $d->qty }} {{ $d->satuan }} @endif
                                                        @if($d->nominal) — <span class="font-semibold text-emerald-600">Rp {{ number_format($d->nominal,0,',','.') }}</span> @endif
                                                    </div>
                                                @endif

                                                {{-- Rekening bank --}}
                                                @if($d->nama_bank || $d->no_rekening)
                                                    <div class="mx-4 mb-3 mt-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                                                        <p class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider mb-2"><i class="bi bi-bank mr-1"></i> Rekening Bank</p>
                                                        <div class="grid grid-cols-3 gap-3">
                                                            @if($d->nama_bank)<div><p class="text-[10px] text-amber-500 uppercase">Bank</p><p class="text-xs font-medium text-gray-700">{{ $d->nama_bank }}</p></div>@endif
                                                            @if($d->no_rekening)<div><p class="text-[10px] text-amber-500 uppercase">No. Rek</p><p class="text-xs font-medium font-mono text-gray-700">{{ $d->no_rekening }}</p></div>@endif
                                                            @if($d->nama_rekening)<div><p class="text-[10px] text-amber-500 uppercase">Atas Nama</p><p class="text-xs font-medium text-gray-700">{{ $d->nama_rekening }}</p></div>@endif
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif

        {{-- PAGINATION --}}
        <div class="py-3 border-t border-gray-100">
            <x-pagination :paginator="$data" />
        </div>

    </div>
</div>
