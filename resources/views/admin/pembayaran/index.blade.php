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
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i class="fa fa-shopping-cart text-indigo-600 text-sm"></i>
                </span>
                <p class="text-xs text-gray-500 font-medium">Total PR</p>
            </div>
            <h2 class="text-2xl font-bold text-indigo-600">{{ $totalPR }}</h2>
            <p class="text-xs text-gray-400 mt-1">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-blue-100 p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fa fa-paper-plane text-blue-600 text-sm"></i>
                </span>
                <p class="text-xs text-gray-500 font-medium">Diajukan</p>
            </div>
            <h2 class="text-2xl font-bold text-blue-600">{{ $totalDiajukan }}</h2>
            <p class="text-xs text-gray-400 mt-1">Rp {{ number_format($nominalDiajukan, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-300 mt-0.5">item</p>
        </div>
        <div class="bg-white rounded-2xl border border-yellow-100 p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-8 h-8 rounded-xl bg-yellow-100 flex items-center justify-center">
                    <i class="fa fa-clock text-yellow-600 text-sm"></i>
                </span>
                <p class="text-xs text-gray-500 font-medium">Pending</p>
            </div>
            <h2 class="text-2xl font-bold text-yellow-600">{{ $totalPending }}</h2>
            <p class="text-xs text-gray-400 mt-1">Rp {{ number_format($nominalPending, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-300 mt-0.5">item</p>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-100 p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i class="fa fa-check-circle text-emerald-600 text-sm"></i>
                </span>
                <p class="text-xs text-gray-500 font-medium">Disetujui</p>
            </div>
            <h2 class="text-2xl font-bold text-emerald-600">{{ $totalDisetujui }}</h2>
            <p class="text-xs text-gray-400 mt-1">Rp {{ number_format($nominalDisetujui, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-300 mt-0.5">item</p>
        </div>
        <div class="bg-white rounded-2xl border border-red-100 p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="fa fa-times-circle text-red-600 text-sm"></i>
                </span>
                <p class="text-xs text-gray-500 font-medium">Ditolak</p>
            </div>
            <h2 class="text-2xl font-bold text-red-600">{{ $totalDitolak }}</h2>
            <p class="text-xs text-gray-400 mt-1">Rp {{ number_format($nominalDitolak, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-300 mt-0.5">item</p>
        </div>
    </div>

    {{-- CHART FILTER --}}
    @php
        $deptList = collect(['Keuangan','Produksi','HRD','Purchase','Sales','Marketing','IT'])
            ->map(fn($d) => ['id' => $d, 'nama' => $d]);
    @endphp
    <x-chart-filter id="pembayaranChartFilter" defaultFilter="year" :showCustomRange="true"
        :showCategoryFilter="true" :categories="$deptList" />

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
                        ['key'=>'semua',    'label'=>'Semua',     'icon'=>'bi bi-grid-3x3-gap','count'=>$totalPR,        'color'=>'blue'],
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
                            @php $bc = match($t['color']){'green'=>'bg-green-100 text-green-700','red'=>'bg-red-100 text-red-700','indigo'=>'bg-indigo-100 text-indigo-700','yellow'=>'bg-yellow-100 text-yellow-700','blue'=>'bg-blue-100 text-blue-700',default=>'bg-gray-100 text-gray-700'}; @endphp
                            <span class="ml-1 text-xs font-bold px-2 py-0.5 rounded-full {{ $bc }}">{{ $t['count'] }}</span>
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

                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 whitespace-nowrap">Urutkan:</span>
                    <a href="{{ route('pembayaran.index', array_merge(request()->except('sort'), ['sort'=>'terbaru'])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors {{ $sort==='terbaru' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        <i class="bi bi-sort-down"></i> Terbaru
                    </a>
                    <a href="{{ route('pembayaran.index', array_merge(request()->except('sort'), ['sort'=>'terlama'])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors {{ $sort==='terlama' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
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
                <select name="source_type" class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">Semua Jenis</option>
                    @foreach($sourceTypes->keys() as $st)
                        <option value="{{ $st }}" {{ ($sourceFilter??'') === $st ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $st)) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    <i class="fa fa-filter text-xs mr-1"></i> Filter
                </button>
                @if($bulan || $deptFilter || $sourceFilter)
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
                return $d->source_type ? ($d->source_type_name ?? ucwords(str_replace('_',' ',$d->source_type))) : 'Belanja';
            });

            $jenisConfig = [
                'Pajak Kendaraan'        => ['icon'=>'bi bi-card-checklist',     'color'=>'blue'],
                'Perpanjangan Pajak'     => ['icon'=>'bi bi-arrow-repeat',        'color'=>'blue'],
                'Asuransi Kendaraan'     => ['icon'=>'bi bi-shield-check',        'color'=>'purple'],
                'Perpanjangan Asuransi'  => ['icon'=>'bi bi-shield-plus',         'color'=>'purple'],
                'Service Part'           => ['icon'=>'bi bi-tools',               'color'=>'orange'],
                'Service Asuransi'       => ['icon'=>'bi bi-wrench-adjustable',   'color'=>'orange'],
                'GPS Kendaraan'          => ['icon'=>'bi bi-geo-alt-fill',        'color'=>'green'],
                'Perpanjangan GPS'       => ['icon'=>'bi bi-arrow-repeat',        'color'=>'green'],
                'KIR'                    => ['icon'=>'bi bi-patch-check',         'color'=>'teal'],
                'Perpanjangan KIR'       => ['icon'=>'bi bi-arrow-repeat',        'color'=>'teal'],
                'STNK'                   => ['icon'=>'bi bi-file-earmark-text',   'color'=>'indigo'],
                'Purchase Order'         => ['icon'=>'bi bi-bag-check',           'color'=>'cyan'],
                'Belanja'                => ['icon'=>'bi bi-cart3',               'color'=>'gray'],
            ];
            $colorMap = [
                'blue'   => ['bg'=>'bg-blue-50',   'border'=>'border-blue-200',   'text'=>'text-blue-700',   'badge'=>'bg-blue-100 text-blue-700',    'hdr'=>'bg-blue-50/40'],
                'purple' => ['bg'=>'bg-purple-50', 'border'=>'border-purple-200', 'text'=>'text-purple-700', 'badge'=>'bg-purple-100 text-purple-700', 'hdr'=>'bg-purple-50/40'],
                'orange' => ['bg'=>'bg-orange-50', 'border'=>'border-orange-200', 'text'=>'text-orange-700', 'badge'=>'bg-orange-100 text-orange-700', 'hdr'=>'bg-orange-50/40'],
                'green'  => ['bg'=>'bg-green-50',  'border'=>'border-green-200',  'text'=>'text-green-700',  'badge'=>'bg-green-100 text-green-700',   'hdr'=>'bg-green-50/40'],
                'teal'   => ['bg'=>'bg-teal-50',   'border'=>'border-teal-200',   'text'=>'text-teal-700',   'badge'=>'bg-teal-100 text-teal-700',     'hdr'=>'bg-teal-50/40'],
                'indigo' => ['bg'=>'bg-indigo-50', 'border'=>'border-indigo-200', 'text'=>'text-indigo-700', 'badge'=>'bg-indigo-100 text-indigo-700', 'hdr'=>'bg-indigo-50/40'],
                'cyan'   => ['bg'=>'bg-cyan-50',   'border'=>'border-cyan-200',   'text'=>'text-cyan-700',   'badge'=>'bg-cyan-100 text-cyan-700',     'hdr'=>'bg-cyan-50/40'],
                'gray'   => ['bg'=>'bg-gray-50',   'border'=>'border-gray-200',   'text'=>'text-gray-600',   'badge'=>'bg-gray-100 text-gray-600',     'hdr'=>'bg-gray-50/40'],
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
                    $cfg  = $jenisConfig[$jenis] ?? ['icon'=>'bi bi-wallet2','color'=>'gray'];
                    $clr  = $colorMap[$cfg['color']] ?? $colorMap['gray'];
                    $gIdx = $loop->index;
                    $pendingIds  = $items->where('status','Pending')->pluck('id')->values()->toArray();

                    // Hitung nominal & count per item (bukan per PR)
                    // GPS punya gps_items di source_data — hitung tiap sub-item
                    // Pembayaran belanja punya relasi items
                    $nominalApproved = 0;
                    $nominalRejected = 0;
                    $nominalPending  = 0;
                    $nominalDiajukan = 0;
                    $totalGrp    = 0;
                    $grpPending  = 0;
                    $grpDiajukan = 0;
                    $grpApproved = 0;
                    $grpRejected = 0;
                    foreach ($items as $_pr) {
                        $_sd       = $_pr->source_data ?? [];
                        $_gpsI     = $_sd['gps_items'] ?? [];
                        $_dec      = $_sd['item_decisions'] ?? [];
                        $_relItems = $_pr->items ?? collect([]);
                        $_srcType  = $_pr->source_type ?? '';

                        // ── Service Asuransi dengan item_decisions ──
                        if (in_array($_srcType, ['service_asuransi', 'service_part', 'service_incident']) && !empty($_dec)) {
                            $_allItems = [];
                            if ($_srcType === 'service_asuransi') {
                                $_allItems = $_sd['kejadians'] ?? [];
                            } else {
                                $_allItems = $_sd['parts'] ?? [];
                            }
                            foreach ($_dec as $_dIdx => $_d) {
                                $_iNom = (int)(($_allItems[(int)($_d['idx'] ?? $_dIdx)]['biaya'] ?? 0));
                                if (($_d['action'] ?? '') === 'approved') {
                                    $grpApproved++;
                                    $nominalApproved += $_iNom;
                                } else {
                                    $grpRejected++;
                                    $nominalRejected += $_iNom;
                                }
                            }
                        } elseif (!empty($_gpsI)) {
                            // GPS multi-item
                            if (!empty($_dec)) {
                                foreach ($_dec as $_dIdx => $_d) {
                                    $_iNom = (int)(($_gpsI[(int)($_d['idx'] ?? $_dIdx)]['biaya_sewa'] ?? 0));
                                    if (($_d['action'] ?? '') === 'approved') {
                                        $grpApproved++;
                                        $nominalApproved += $_iNom;
                                    } else {
                                        $grpRejected++;
                                        $nominalRejected += $_iNom;
                                    }
                                }
                                // Sisa belum diproses → ikut status PR
                                $processedIdx = array_column($_dec, 'idx');
                                foreach ($_gpsI as $_gi => $_gitem) {
                                    if (!in_array($_gi, $processedIdx)) {
                                        $_iNom = (int)($_gitem['biaya_sewa'] ?? 0);
                                        if ($_pr->status === 'Pending') {
                                            $grpPending++;
                                            $nominalPending += $_iNom;
                                        } elseif ($_pr->status === 'Diajukan') {
                                            $grpDiajukan++;
                                            $nominalDiajukan += $_iNom;
                                        }
                                    }
                                }
                            } else {
                                // Belum diproses
                                $_prNom = (int)($_pr->total_nominal ?? 0);
                                $cnt    = count($_gpsI);
                                if ($_pr->status === 'Pending') {
                                    $grpPending     += $cnt;
                                    $nominalPending += $_prNom;
                                } elseif ($_pr->status === 'Diajukan') {
                                    $grpDiajukan     += $cnt;
                                    $nominalDiajukan += $_prNom;
                                } elseif ($_pr->status === 'Disetujui') {
                                    $grpApproved     += $cnt;
                                    $nominalApproved += $_prNom;
                                } elseif ($_pr->status === 'Ditolak') {
                                    $grpRejected     += $cnt;
                                    $nominalRejected += $_prNom;
                                }
                            }
                        } elseif ($_relItems->count() > 0) {
                            // Belanja/service — relasi items, hitung subtotal per item
                            $_prNom  = (int)($_pr->total_nominal ?? 0);
                            $cnt     = $_relItems->count();
                            if ($_pr->status === 'Pending') {
                                $grpPending     += $cnt;
                                $nominalPending += $_prNom;
                            } elseif ($_pr->status === 'Diajukan') {
                                $grpDiajukan     += $cnt;
                                $nominalDiajukan += $_prNom;
                            } elseif ($_pr->status === 'Disetujui') {
                                $grpApproved     += $cnt;
                                $nominalApproved += $_prNom;
                            } elseif ($_pr->status === 'Ditolak') {
                                $grpRejected     += $cnt;
                                $nominalRejected += $_prNom;
                            }
                        } else {
                            // Fallback 1 PR = 1 item
                            $_prNom = (int)($_pr->total_nominal ?? 0);
                            if ($_pr->status === 'Pending') {
                                $grpPending++;
                                $nominalPending += $_prNom;
                            } elseif ($_pr->status === 'Diajukan') {
                                $grpDiajukan++;
                                $nominalDiajukan += $_prNom;
                            } elseif ($_pr->status === 'Disetujui') {
                                $grpApproved++;
                                $nominalApproved += $_prNom;
                            } elseif ($_pr->status === 'Ditolak') {
                                $grpRejected++;
                                $nominalRejected += $_prNom;
                            }
                        }
                    }
                    // totalGrp = approved + diajukan + pending (ditolak tidak masuk)
                    $totalGrp = $nominalApproved + $nominalDiajukan + $nominalPending;
                @endphp

                {{-- ── GROUP HEADER ── --}}
                <div>
                    <div class="w-full flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors cursor-pointer {{ $clr['hdr'] }}"
                        onclick="toggleGroup({{ $gIdx }})">

                        <i id="grp-chevron-{{ $gIdx }}"
                            class="fa fa-chevron-right text-[11px] text-gray-400 transition-transform duration-200 flex-shrink-0"></i>

                        <span class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 {{ $clr['bg'] }} border {{ $clr['border'] }}">
                            <i class="{{ $cfg['icon'] }} {{ $clr['text'] }} text-sm"></i>
                        </span>

                        <span class="text-sm font-bold text-gray-800">{{ $jenis }}</span>

                        {{-- Count PR --}}
                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $items->count() }} PR</span>
                        {{-- Per-group approve/reject — sebelah kanan nama jenis --}}
                        @if($role === 'superadmin' && $tab === 'Pending' && count($pendingIds) > 0)
                            <div class="flex gap-1.5 flex-shrink-0 ml-auto" onclick="event.stopPropagation()">
                                <button type="button"
                                    onclick="openBulkApproveModal('{{ addslashes($jenis) }}', {!! json_encode($pendingIds) !!})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                    <i class="fa fa-check text-[10px]"></i> Approve All
                                </button>
                                <button type="button"
                                    onclick="openBulkRejectModal('{{ addslashes($jenis) }}', {!! json_encode($pendingIds) !!})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                    <i class="fa fa-times text-[10px]"></i> Reject All
                                </button>
                            </div>
                        @else
                            {{-- nominal tidak ditampilkan di header group --}}
                        @endif

                    </div>{{-- end group header --}}

                    {{-- ── GROUP BODY ── --}}
                    <div id="grp-body-{{ $gIdx }}" class="hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50/80 border-b border-gray-100">
                                        <th class="w-6 px-2 py-2.5"></th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">No PR</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Tanggal</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Departemen</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Pemohon</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Keluhan</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Items</th>
                                        <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Nominal</th>
                                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Status</th>

                                        <th class="text-center text-[11px] font-semibold uppercase tracking-wide text-gray-400 px-3 py-2.5">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $di => $d)
                                    @php
                                        // Hitung approved/rejected dari item_decisions (berlaku untuk semua tipe per-item)
                                        $_decRaw   = (is_array($d->source_data) ? $d->source_data : (json_decode($d->source_data, true) ?? []))['item_decisions'] ?? [];
                                        $_decColl  = collect($_decRaw);
                                        $approvedCount = $_decColl->where('action','approved')->count();
                                        $rejectedCount = $_decColl->where('action','rejected')->count();
                                        $totalDecCount = $approvedCount + $rejectedCount;
                                        $_prHasRejected = $rejectedCount > 0;

                                        // Status per tab — PR partial muncul di beberapa tab:
                                        // tampilkan status yang sesuai tab aktif, bukan selalu status PR
                                        $statusForDisplay = $d->status ?? '-';
                                        if (!empty($_decRaw)) {
                                            // PR partial: tentukan status badge berdasarkan tab aktif
                                            if (in_array($tab ?? '', ['Disetujui'])) {
                                                $statusForDisplay = 'Disetujui';
                                            } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                $statusForDisplay = 'Ditolak';
                                            } elseif (in_array($tab ?? '', ['Diajukan', 'Pending'])) {
                                                $statusForDisplay = $d->status ?? 'Diajukan';
                                            }
                                        }
                                        $statusColor = match($statusForDisplay) {
                                            'Disetujui' => 'bg-green-100 text-green-700',
                                            'Ditolak'   => 'bg-red-100 text-red-700',
                                            'Diajukan'  => 'bg-indigo-100 text-indigo-600',
                                            'Pending'   => 'bg-yellow-100 text-yellow-600',
                                            default     => 'bg-gray-100 text-gray-500',
                                        };
                                        $statusIcon = match($statusForDisplay) {
                                            'Disetujui' => 'fa-check-circle',
                                            'Ditolak'   => 'fa-times-circle',
                                            'Diajukan'  => 'fa-paper-plane',
                                            'Pending'   => 'fa-clock',
                                            default     => 'fa-circle',
                                        };
                                        $statusLabel = $statusForDisplay;

                                        $rowUid = 'r'.$gIdx.'i'.$di;
                                        $_sd_items = is_array($d->source_data) ? $d->source_data : (json_decode($d->source_data, true) ?? []);
                                        $_dec = $_sd_items['item_decisions'] ?? [];
                                        $_gpsItemsAll = $_sd_items['gps_items'] ?? [];
                                        $_sdParts  = $_sd_items['parts'] ?? [];
                                        $_sdKejad  = $_sd_items['kejadians'] ?? [];

                                        if (!empty($_gpsItemsAll)) {
                                            // GPS: hitung dari gps_items actual sesuai tab
                                            if (!empty($_dec)) {
                                                $_decMapCount = collect($_dec)->keyBy(fn($dec) => (int)($dec['idx'] ?? -1));
                                                if (in_array($tab ?? '', ['Disetujui'])) {
                                                    $itemCount = collect($_gpsItemsAll)
                                                        ->filter(fn($g, $i) => ($_decMapCount[(int)$i]['action'] ?? '') === 'approved')
                                                        ->count();
                                                } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                    $itemCount = collect($_gpsItemsAll)
                                                        ->filter(fn($g, $i) => ($_decMapCount[(int)$i]['action'] ?? '') !== 'approved' && $_decMapCount->has((int)$i))
                                                        ->count();
                                                } else {
                                                    $itemCount = count($_gpsItemsAll);
                                                }
                                            } else {
                                                $itemCount = count($_gpsItemsAll);
                                            }
                                        } elseif (in_array($d->source_type, ['service_part', 'service_incident']) && !empty($_sdParts)) {
                                            // Service part/incident: filter per tab jika ada item_decisions
                                            if (!empty($_dec)) {
                                                $_decMapCount = collect($_dec)->keyBy(fn($dec) => (int)($dec['idx'] ?? -1));
                                                if (in_array($tab ?? '', ['Disetujui'])) {
                                                    $itemCount = collect($_sdParts)
                                                        ->filter(fn($p, $i) => ($_decMapCount[(int)$i]['action'] ?? '') === 'approved')
                                                        ->count();
                                                } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                    $itemCount = collect($_sdParts)
                                                        ->filter(fn($p, $i) => ($_decMapCount[(int)$i]['action'] ?? '') === 'rejected')
                                                        ->count();
                                                } else {
                                                    $itemCount = count($_sdParts);
                                                }
                                            } else {
                                                $itemCount = count($_sdParts);
                                            }
                                        } elseif ($d->source_type === 'service_asuransi' && !empty($_sdKejad)) {
                                            // Service asuransi: filter per tab jika ada item_decisions
                                            if (!empty($_dec)) {
                                                $_decMapCount = collect($_dec)->keyBy(fn($dec) => (int)($dec['idx'] ?? -1));
                                                if (in_array($tab ?? '', ['Disetujui'])) {
                                                    $itemCount = collect($_sdKejad)
                                                        ->filter(fn($k, $i) => ($_decMapCount[(int)$i]['action'] ?? '') === 'approved')
                                                        ->count();
                                                } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                    $itemCount = collect($_sdKejad)
                                                        ->filter(fn($k, $i) => ($_decMapCount[(int)$i]['action'] ?? '') === 'rejected')
                                                        ->count();
                                                } elseif (in_array($tab ?? '', ['Diajukan'])) {
                                                    // Tab Diajukan PR partial: hanya item yang belum diputuskan (pending)
                                                    $itemCount = collect($_sdKejad)
                                                        ->filter(fn($k, $i) => !$_decMapCount->has((int)$i))
                                                        ->count();
                                                } else {
                                                    $itemCount = count($_sdKejad);
                                                }
                                            } else {
                                                $itemCount = count($_sdKejad);
                                            }
                                        } elseif ($d->items->count() > 0) {
                                            $itemCount = $d->items->count();
                                        } else {
                                            $itemCount = 1;
                                        }
                                    @endphp

                                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/40 hover:bg-blue-50/30 transition-colors cursor-pointer"
                                        onclick="toggleRowExpand('{{ $rowUid }}')">

                                        <td class="px-2 py-3 text-center">
                                            <i id="rowchv-{{ $rowUid }}"
                                                class="fa fa-chevron-right text-[10px] text-gray-300 transition-transform duration-200"></i>
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
                                        <td class="px-3 py-3 text-xs text-gray-700">
                                            @php $pemohonNama = $userNames[$d->pemohon] ?? null; @endphp
                                            @if($pemohonNama)
                                                <span class="font-medium text-gray-800">{{ $pemohonNama }}</span>
                                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $d->pemohon ?? '-' }}</p>
                                            @else
                                                {{ $d->pemohon ?? '-' }}
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">
                                            @php
                                                $_sd_ket = is_array($d->source_data) ? $d->source_data : (json_decode($d->source_data, true) ?? []);
                                                $ketPmb = $d->keterangan
                                                    ?: ($_sd_ket['keluhan'] ?? null)
                                                    ?: ($_sd_ket['alasan_permintaan'] ?? null);
                                            @endphp
                                            @if($ketPmb)
                                                <span class="text-xs text-gray-600">{{ $ketPmb }}</span>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">
                                            @if(!empty($d->badge_stats))
                                                {{-- Task 2: Render badge per-item untuk source_type yang support per-item decision --}}
                                                <div class="flex flex-col gap-1">
                                                    @if($d->badge_stats['approved'] > 0)
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">
                                                            <i class="fa fa-check text-[8px]"></i> {{ $d->badge_stats['approved'] }}
                                                        </span>
                                                    @endif
                                                    @if($d->badge_stats['pending'] > 0)
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700">
                                                            <i class="fa fa-clock text-[8px]"></i> {{ $d->badge_stats['pending'] }}
                                                        </span>
                                                    @endif
                                                    @if($d->badge_stats['rejected'] > 0)
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700">
                                                            <i class="fa fa-times text-[8px]"></i> {{ $d->badge_stats['rejected'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                {{-- Fallback: tampilkan item count biasa --}}
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-600">
                                                    <i class="fa fa-boxes text-blue-400 text-[10px]"></i>
                                                    {{ $itemCount }} item{{ $itemCount > 1 ? 's' : '' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            @php
                                                $_sd       = is_array($d->source_data) ? $d->source_data : (json_decode($d->source_data, true) ?? []);
                                                // PENTING: gunakan integer key agar filter index array konsisten
                                                $_decMap   = collect($_sd['item_decisions'] ?? [])->keyBy(fn($dec) => (int)($dec['idx'] ?? -1));
                                                $_srcType  = $d->source_type ?? '';

                                                // Helper: hitung total item berdasarkan source_type
                                                $_itemsTotal = match(true) {
                                                    $_srcType === 'service_part'
                                                        => collect($_sd['parts'] ?? [])->sum(fn($p) => $p['biaya'] ?? 0),
                                                    $_srcType === 'service_asuransi'
                                                        => collect($_sd['kejadians'] ?? [])->sum(fn($k) => $k['biaya'] ?? 0),
                                                    $_srcType === 'service_incident'
                                                        => collect($_sd['parts'] ?? [])->sum(fn($p) => $p['biaya'] ?? 0),
                                                    in_array($_srcType, ['gps', 'gps_perpanjang'])
                                                        => collect($_sd['gps_items'] ?? [])->sum(fn($g) => $g['biaya_sewa'] ?? 0),
                                                    // pajak, asuransi_kendaraan, kir, stnk: ambil dari field langsung di source_data
                                                    in_array($_srcType, ['pajak', 'pajak_perpanjang'])
                                                        => (int)($_sd['nominal'] ?? 0),
                                                    in_array($_srcType, ['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'])
                                                        => (int)($_sd['biaya'] ?? $_sd['premi'] ?? 0),
                                                    in_array($_srcType, ['kir', 'kir_perpanjang'])
                                                        => (int)($_sd['biaya'] ?? 0),
                                                    default => 0,
                                                };

                                                // Fallback bertingkat jika _itemsTotal masih 0:
                                                // 1. Coba nominal_approved + nominal_rejected dari source_data (tersimpan saat approval)
                                                // 2. Coba total_nominal accessor (yang mungkin juga 0 kalau DB sudah ditimpa)
                                                // 3. Coba kolom nominal di DB
                                                if ($_itemsTotal == 0) {
                                                    $_nomApp = (int)($_sd['nominal_approved'] ?? 0);
                                                    $_nomRej = (int)($_sd['nominal_rejected'] ?? 0);
                                                    if ($_nomApp + $_nomRej > 0) {
                                                        $_itemsTotal = $_nomApp + $_nomRej;
                                                    } else {
                                                        $_itemsTotal = (int)($d->total_nominal ?? $d->nominal ?? 0);
                                                    }
                                                }

                                                if (($tab ?? '') === 'semua') {
                                                    if (!$_decMap->isEmpty()) {
                                                        $_nomApprRow = (int) ($_sd['nominal_approved'] ?? 0);
                                                        $_nomRejRow  = (int) ($_sd['nominal_rejected'] ?? 0);
                                                        $_rowNominal = ($_nomApprRow + $_nomRejRow) > 0
                                                            ? $_nomApprRow + $_nomRejRow
                                                            : ($_itemsTotal > 0 ? $_itemsTotal : (int)($d->nominal ?? 0));
                                                    } else {
                                                        $_rowNominal = $_itemsTotal > 0 ? $_itemsTotal : (int)($d->nominal ?? 0);
                                                    }
                                                } else {
                                                    if (!$_decMap->isEmpty() && in_array($_srcType, ['gps', 'gps_perpanjang'])) {
                                                        // GPS dengan item_decisions: filter per approved/rejected/pending
                                                        $_nomApprRow = collect($_sd['gps_items'] ?? [])
                                                            ->filter(fn($g, $i) => ($_decMap[(int)$i]['action'] ?? '') === 'approved')
                                                            ->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                        $_nomRejRow  = collect($_sd['gps_items'] ?? [])
                                                            ->filter(fn($g, $i) => ($_decMap[(int)$i]['action'] ?? '') !== 'approved' && $_decMap->has((int)$i))
                                                            ->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                        // Item pending = belum ada entry di item_decisions
                                                        $_nomPendRow = collect($_sd['gps_items'] ?? [])
                                                            ->filter(fn($g, $i) => !$_decMap->has((int)$i))
                                                            ->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                        if (in_array($tab ?? '', ['Ditolak'])) {
                                                            $_rowNominal = $_nomRejRow;
                                                        } elseif (in_array($tab ?? '', ['Disetujui'])) {
                                                            $_rowNominal = $_nomApprRow;
                                                        } elseif (in_array($tab ?? '', ['Diajukan', 'Pending']) && $_nomPendRow > 0) {
                                                            // Tab Diajukan/Pending (PR partial GPS resubmit): hanya harga item pending
                                                            $_rowNominal = $_nomPendRow;
                                                        } else {
                                                            $_rowNominal = $_nomApprRow + $_nomRejRow + $_nomPendRow;
                                                        }
                                                        // Fallback: item_decisions mungkin tidak punya key 'idx' (data lama)
                                                        if ($_rowNominal == 0) {
                                                            $_rowNominal = $_itemsTotal > 0 ? $_itemsTotal : (int)($d->total_nominal ?? 0);
                                                        }
                                                    } else {
                                                        // service_part / service_incident / service_asuransi: filter per item_decisions
                                                        if (!$_decMap->isEmpty() && in_array($_srcType, ['service_part', 'service_incident'])) {
                                                            $_nomApprRow = collect($_sd['parts'] ?? [])
                                                                ->filter(fn($p, $i) => ($_decMap[(int)$i]['action'] ?? '') === 'approved')
                                                                ->sum(fn($p) => $p['biaya'] ?? 0);
                                                            $_nomRejRow = collect($_sd['parts'] ?? [])
                                                                ->filter(fn($p, $i) => ($_decMap[(int)$i]['action'] ?? '') === 'rejected')
                                                                ->sum(fn($p) => $p['biaya'] ?? 0);
                                                            $_nomPendRow = collect($_sd['parts'] ?? [])
                                                                ->filter(fn($p, $i) => !$_decMap->has((int)$i))
                                                                ->sum(fn($p) => $p['biaya'] ?? 0);
                                                            if (in_array($tab ?? '', ['Ditolak'])) {
                                                                $_rowNominal = $_nomRejRow ?: ($_itemsTotal > 0 ? $_itemsTotal : (int)($d->total_nominal ?? 0));
                                                            } elseif (in_array($tab ?? '', ['Disetujui'])) {
                                                                $_rowNominal = $_nomApprRow ?: ($_itemsTotal > 0 ? $_itemsTotal : (int)($d->total_nominal ?? 0));
                                                            } elseif (in_array($tab ?? '', ['Diajukan']) && $d->status === 'Diajukan' && $_nomPendRow > 0) {
                                                                // Tab Diajukan (PR partial resubmit): hanya harga item yang belum diputuskan (pending)
                                                                $_rowNominal = $_nomPendRow;
                                                            } else {
                                                                $_rowNominal = ($_nomApprRow + $_nomRejRow) > 0 ? ($_nomApprRow + $_nomRejRow) : ($_itemsTotal > 0 ? $_itemsTotal : (int)($d->nominal ?? 0));
                                                            }
                                                        } elseif (!$_decMap->isEmpty() && $_srcType === 'service_asuransi') {
                                                            $_nomApprRow = collect($_sd['kejadians'] ?? [])
                                                                ->filter(fn($k, $i) => ($_decMap[(int)$i]['action'] ?? '') === 'approved')
                                                                ->sum(fn($k) => $k['biaya'] ?? 0);
                                                            $_nomRejRow = collect($_sd['kejadians'] ?? [])
                                                                ->filter(fn($k, $i) => ($_decMap[(int)$i]['action'] ?? '') === 'rejected')
                                                                ->sum(fn($k) => $k['biaya'] ?? 0);
                                                            // Item pending = belum ada entry di item_decisions
                                                            $_nomPendRow = collect($_sd['kejadians'] ?? [])
                                                                ->filter(fn($k, $i) => !$_decMap->has((int)$i))
                                                                ->sum(fn($k) => $k['biaya'] ?? 0);
                                                            if (in_array($tab ?? '', ['Ditolak'])) {
                                                                $_rowNominal = $_nomRejRow ?: ($_itemsTotal > 0 ? $_itemsTotal : (int)($d->total_nominal ?? 0));
                                                            } elseif (in_array($tab ?? '', ['Disetujui'])) {
                                                                // Tab Disetujui: hanya harga item yang sudah approved
                                                                $_rowNominal = $_nomApprRow ?: ($_itemsTotal > 0 ? $_itemsTotal : (int)($d->total_nominal ?? 0));
                                                            } elseif (in_array($tab ?? '', ['Diajukan']) && $d->status === 'Diajukan' && $_nomPendRow > 0) {
                                                                // Tab Diajukan (PR partial): hanya harga item yang belum diputuskan
                                                                $_rowNominal = $_nomPendRow;
                                                            } else {
                                                                $_rowNominal = ($_nomApprRow + $_nomRejRow) > 0 ? ($_nomApprRow + $_nomRejRow) : ($_itemsTotal > 0 ? $_itemsTotal : (int)($d->nominal ?? 0));
                                                            }
                                                        } else {
                                                            // pajak, asuransi_kendaraan, kir, stnk, dll: pakai nominal langsung
                                                            $_rowNominal = $_itemsTotal > 0 ? $_itemsTotal : (int)($d->total_nominal ?? 0);
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <span class="{{ in_array($tab ?? '', ['Ditolak']) ? 'text-red-500' : 'text-emerald-600' }} text-xs font-semibold">
                                                Rp {{ number_format($_rowNominal, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                                <i class="fa {{ $statusIcon }} text-[8px]"></i> {{ $statusLabel }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-3" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-center gap-1 flex-wrap">

                                                <button type="button" onclick="openDetailModal({{ $d->id }}, '{{ $tab }}')"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition-colors">
                                                    <i class="fa fa-eye text-[10px]"></i> Detail
                                                </button>

                                                @if($role === 'superadmin')
                                                    @php
                                                        // PR partial muncul di 2 tab. Di tab Disetujui, tombol Approve/Reject
                                                        // tidak ditampilkan karena tab itu menampilkan item yang sudah approved.
                                                        // Tombol Approve hanya relevan di tab Diajukan/Pending/semua.
                                                        $_showApproveBtn = in_array($d->status, ['Pending', 'Diajukan'])
                                                            && !in_array($tab ?? '', ['Disetujui', 'Ditolak']);
                                                    @endphp
                                                    @if($_showApproveBtn)
                                                        @if($d->source_type)
                                                            @if(in_array($d->source_type, ['gps', 'gps_perpanjang', 'service_part', 'service_incident', 'service_asuransi']))
                                                                {{-- GPS/Service Part/Service Asuransi: pakai approval modal dengan per-item approve/reject --}}
                                                                <button type="button"
                                                                    onclick="openApprovalModal({{ $d->id }})"
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 transition-colors">
                                                                    <i class="fa fa-check text-[10px]"></i> Aksi
                                                                </button>
                                                                <button type="button"
                                                                    onclick="openRejectModal({{ $d->id }})"
                                                                    class="hidden inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition-colors">
                                                                    <i class="fa fa-times text-[10px]"></i> Reject kontoo
                                                                </button>
                                                            @else
                                                                <button type="button"
                                                                    onclick="openSingleApproveModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 transition-colors">
                                                                    <i class="fa fa-check text-[10px]"></i> Approve
                                                                </button>
                                                                <button type="button"
                                                                    onclick="openSingleRejectModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition-colors">
                                                                    <i class="fa fa-times text-[10px]"></i> Reject
                                                                </button>
                                                            @endif
                                                        @else
                                                            @if($d->tipe_pembayaran === 'service')
                                                                <button type="button"
                                                                    onclick="openApproveServiceModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 transition-colors">
                                                                    <i class="fa fa-check text-[10px]"></i> Setujui
                                                                </button>
                                                            @else
                                                                <form action="{{ route('pembayaran.status', $d->id) }}" method="POST" class="inline">
                                                                    @csrf <input type="hidden" name="status" value="Disetujui">
                                                                    <button type="submit"
                                                                        onclick="return confirm('Setujui pembayaran {{ $d->no_pr }}?')"
                                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 transition-colors">
                                                                        <i class="fa fa-check text-[10px]"></i> Setujui
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <button type="button"
                                                                onclick="openTolakModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition-colors">
                                                                <i class="fa fa-times text-[10px]"></i> Tolak
                                                            </button>
                                                        @endif
                                                    @elseif($d->status === 'Ditolak' && $d->source_type && $d->can_edit)
                                                        @php
                                                            $useInlineModal = $d->source_type === 'service_incident';
                                                            $resubmitRoute = match($d->source_type) {
                                                                'pajak'              => route('pajak.index', ['highlight_pembayaran' => $d->id]),
                                                                'asuransi_kendaraan' => route('asuransi-kendaraan.index', ['highlight_pembayaran' => $d->id]),
                                                                'kir'                => route('kir.index', ['highlight_pembayaran' => $d->id]),
                                                                default              => route('pembayaran.edit-rejected', $d->id),
                                                            };
                                                        @endphp
                                                        @if(in_array($d->source_type, ['service_asuransi', 'service_part', 'service_incident', 'gps', 'gps_perpanjang']))
                                                            {{-- service_asuransi, service_part, service_incident, gps Ditolak penuh: modal inline per-item --}}
                                                            <button type="button"
                                                                onclick="openResubmitRejectedModal({{ $d->id }})"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition-colors">
                                                                <i class="fa fa-rotate-right text-[10px]"></i> Edit & Ajukan Ulang
                                                            </button>
                                                        @else
                                                        <a href="{{ $resubmitRoute }}"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition-colors">
                                                            <i class="fa fa-edit text-[10px]"></i> Edit & Ajukan Ulang
                                                        </a>
                                                        @endif
                                                    @elseif($tab === 'Ditolak' && $d->status === 'Disetujui' && $_prHasRejected && $d->can_edit && in_array($d->source_type, ['service_part','service_incident','service_asuransi','gps','gps_perpanjang']))
                                                        {{-- PR Disetujui tapi ada item yang ditolak (muncul di tab Ditolak): bisa ajukan ulang --}}
                                                        <button type="button"
                                                            onclick="openResubmitRejectedModal({{ $d->id }})"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition-colors">
                                                            <i class="fa fa-rotate-right text-[10px]"></i> Edit & Ajukan Ulang
                                                        </button>
                                                    @endif
                                                @else
                                                    {{-- Tombol Edit & Ajukan Ulang untuk item rejected (modal inline) --}}
                                                    @php
                                                        $_resubmitSupported = in_array($d->source_type, ['service_part', 'service_incident', 'service_asuransi']);
                                                    @endphp

                                                    {{-- Kondisi A: PR Disetujui dengan item rejected (muncul di tab Ditolak) — bisa ajukan ulang --}}
                                                    @if($tab === 'Ditolak' && $d->status === 'Disetujui' && $_prHasRejected && $d->can_edit && in_array($d->source_type, ['service_part','service_incident','service_asuransi','gps','gps_perpanjang']))
                                                        <button type="button"
                                                            onclick="openResubmitRejectedModal({{ $d->id }})"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition-colors">
                                                            <i class="fa fa-rotate-right text-[10px]"></i> Edit & Ajukan Ulang
                                                        </button>

                                                    {{-- Kondisi B: service_part / service_asuransi / gps Ditolak penuh — modal inline --}}
                                                    @elseif($d->status === 'Ditolak' && in_array($d->source_type, ['service_part', 'service_asuransi', 'gps', 'gps_perpanjang']) && $d->can_edit)
                                                        <button type="button"
                                                            onclick="openResubmitRejectedModal({{ $d->id }})"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition-colors">
                                                            <i class="fa fa-rotate-right text-[10px]"></i> Edit & Ajukan Ulang
                                                        </button>

                                                    @else
                                                        @if(!in_array($d->status, ['Diajukan','Disetujui']))
                                                            <a href="{{ route('pembayaran.edit', $d->id) }}"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-yellow-50 text-yellow-700 hover:bg-yellow-100 border border-yellow-200 transition-colors">
                                                                <i class="fa fa-edit text-[10px]"></i> Edit
                                                            </a>
                                                            <button type="button"
                                                                data-action="{{ route('pembayaran.destroy', $d->id) }}"
                                                                data-name="{{ $d->no_pr }}"
                                                                onclick="triggerDelete(this)"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition-colors">
                                                                <i class="fa fa-trash text-[10px]"></i> Hapus
                                                            </button>
                                                        @endif
                                                        @if(in_array($d->status, ['Pending','Ditolak']))
                                                            <form action="{{ route('pembayaran.ajukan', $d->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit"
                                                                    onclick="return confirm('Ajukan pembayaran {{ $d->no_pr }}?')"
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                                                    <i class="fa fa-paper-plane text-[10px]"></i> Ajukan
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                @endif

                                            </div>
                                        </td>
                                    </tr>

                                    {{-- ROW EXPAND --}}
                                    <tr id="rowexpand-{{ $rowUid }}" class="hidden bg-blue-50/20">
                                        <td colspan="10" class="px-6 pb-4 pt-1">
                                            @php $sd = $d->source_data ?? []; @endphp
                                            <div class="rounded-xl border border-blue-100 bg-white overflow-hidden shadow-sm">

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
                                                                <td class="px-4 py-2 text-gray-400 max-w-[150px] truncate">{{ $item->keterangan ?: '-' }}</td>
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
                                                        <p class="text-[10px] font-semibold text-purple-500 uppercase tracking-wider">
                                                            <i class="bi bi-wallet2 mr-1"></i> Detail — {{ $d->source_type_name }}
                                                        </p>
                                                    </div>
                                                    @php $kend = isset($sd['kendaraan_id']) ? \App\Models\Kendaraan::find($sd['kendaraan_id']) : null; @endphp
                                                    @if($kend && !in_array($d->source_type, ['service_asuransi', 'service_part', 'service_incident']))
                                                    <div class="px-4 py-2.5 grid grid-cols-2 md:grid-cols-4 gap-3 bg-gray-50/50 border-b border-gray-100">
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Kendaraan</p><p class="text-xs font-semibold text-gray-700">{{ $kend->nopol }} — {{ $kend->merk }}</p></div>
                                                        @if(isset($sd['tanggal_bayar']))<div><p class="text-[10px] text-gray-400 uppercase">Tgl Bayar</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_bayar'])->format('d M Y') }}</p></div>@endif
                                                        @if(isset($sd['tanggal_habis']))<div><p class="text-[10px] text-gray-400 uppercase">Berlaku s/d</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_habis'])->format('d M Y') }}</p></div>@endif
                                                    </div>
                                                    @endif
                                                    {{-- Pajak --}}
                                                    @if(in_array($d->source_type,['pajak','pajak_perpanjang']))
                                                    <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Jenis Pajak</p><p class="text-xs font-semibold text-gray-700">{{ $sd['jenis_pajak'] ?? '-' }}</p></div>
                                                        @if(isset($sd['tahun_pajak']))<div><p class="text-[10px] text-gray-400 uppercase">Tahun</p><p class="text-xs text-gray-700">{{ $sd['tahun_pajak'] }}</p></div>@endif
                                                        @if(isset($sd['tanggal_jatuh_tempo']))<div><p class="text-[10px] text-gray-400 uppercase">Jatuh Tempo</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_jatuh_tempo'])->format('d M Y') }}</p></div>@endif
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Nominal</p><p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['nominal']??$d->nominal??0,0,',','.') }}</p></div>
                                                    </div>
                                                    @php
                                                        // Lampiran dari temp_files (baru) atau DB attachments (lama)
                                                        $pajakLampiran = $sd['temp_files']['attachments'] ?? [];
                                                        if (empty($pajakLampiran)) {
                                                            $pajakExistingId = $sd['existing_record_id'] ?? null;
                                                            if ($pajakExistingId) {
                                                                $pajakAttachments = \App\Models\Attachment::where('relation_type', 'pajak')
                                                                    ->where('relation_id', $pajakExistingId)->get();
                                                                foreach ($pajakAttachments as $att) {
                                                                    $pajakLampiran[] = [
                                                                        'original_name' => $att->file_name,
                                                                        'public_url'    => asset($att->file_path),
                                                                    ];
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if(!empty($pajakLampiran))
                                                    <div class="px-4 pb-3 flex flex-wrap gap-2 border-t border-blue-100 pt-2">
                                                        <p class="w-full text-[10px] font-semibold text-gray-400 uppercase mb-0.5"><i class="fa fa-paperclip mr-1"></i>Lampiran</p>
                                                        @foreach($pajakLampiran as $plf)
                                                            @php $plfUrl = $plf['public_url'] ?? (isset($plf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($plf['path']) : null); @endphp
                                                            @if($plfUrl)
                                                                <a href="{{ $plfUrl }}" target="_blank"
                                                                    class="text-xs text-blue-600 hover:underline truncate max-w-[200px]"
                                                                    title="{{ $plf['original_name'] ?? '' }}">{{ $plf['original_name'] ?? 'file' }}</a>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                    {{-- Asuransi --}}
                                                    @elseif(in_array($d->source_type,['asuransi_kendaraan','asuransi_kendaraan_perpanjang']))
                                                    @php $asr = isset($sd['asuransi_id']) ? \App\Models\Asuransi::find($sd['asuransi_id']) : null; $jAsr = isset($sd['jenis_asuransi_id']) ? \App\Models\JenisAsuransi::find($sd['jenis_asuransi_id']) : null; @endphp
                                                    <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Perusahaan</p><p class="text-xs font-semibold text-gray-700">{{ $asr->nama_asuransi ?? '-' }}</p></div>
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Jenis</p><p class="text-xs text-gray-700">{{ $jAsr->nama_jenis ?? '-' }}</p></div>
                                                        @if(isset($sd['no_polis']))<div><p class="text-[10px] text-gray-400 uppercase">No. Polis</p><p class="text-xs font-mono text-gray-700">{{ $sd['no_polis'] }}</p></div>@endif
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Premi</p><p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['premi']??$d->nominal??0,0,',','.') }}</p></div>
                                                    </div>
                                                    @php
                                                        $asrLampiran = $sd['temp_files']['attachments'] ?? [];
                                                        if (empty($asrLampiran)) {
                                                            $asrExistingId = $sd['existing_record_id'] ?? null;
                                                            if ($asrExistingId) {
                                                                foreach (\App\Models\Attachment::where('relation_type','asuransi')->where('relation_id',$asrExistingId)->get() as $att) {
                                                                    $asrLampiran[] = ['original_name'=>$att->file_name,'public_url'=>asset($att->file_path)];
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if(!empty($asrLampiran))
                                                    <div class="px-4 pb-3 flex flex-wrap gap-2 border-t border-purple-100 pt-2">
                                                        <p class="w-full text-[10px] font-semibold text-gray-400 uppercase mb-0.5"><i class="fa fa-paperclip mr-1"></i>Lampiran</p>
                                                        @foreach($asrLampiran as $alf)
                                                            @php $alfUrl = $alf['public_url'] ?? (isset($alf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($alf['path']) : null); @endphp
                                                            @if($alfUrl)<a href="{{ $alfUrl }}" target="_blank" class="text-xs text-blue-600 hover:underline truncate max-w-[200px]" title="{{ $alf['original_name']??'' }}">{{ $alf['original_name']??'file' }}</a>@endif
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                    {{-- KIR --}}
                                                    @elseif(in_array($d->source_type,['kir','kir_perpanjang']))
                                                    <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                        @if(isset($sd['no_kir']))<div><p class="text-[10px] text-gray-400 uppercase">No. KIR</p><p class="text-xs font-mono text-gray-700">{{ $sd['no_kir'] }}</p></div>@endif
                                                        @if(isset($sd['tanggal_kir']))<div><p class="text-[10px] text-gray-400 uppercase">Tgl KIR</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_kir'])->format('d M Y') }}</p></div>@endif
                                                        @if(isset($sd['tanggal_habis_kir']))<div><p class="text-[10px] text-gray-400 uppercase">Berlaku s/d</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_habis_kir'])->format('d M Y') }}</p></div>@endif
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Biaya</p><p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['biaya']??$d->nominal??0,0,',','.') }}</p></div>
                                                    </div>
                                                    @php
                                                        $kirLampiran = $sd['temp_files']['attachments'] ?? [];
                                                        if (empty($kirLampiran)) {
                                                            $kirExistingId = $sd['existing_record_id'] ?? null;
                                                            if ($kirExistingId) {
                                                                foreach (\App\Models\Attachment::where('relation_type','kir')->where('relation_id',$kirExistingId)->get() as $att) {
                                                                    $kirLampiran[] = ['original_name'=>$att->file_name,'public_url'=>asset($att->file_path)];
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if(!empty($kirLampiran))
                                                    <div class="px-4 pb-3 flex flex-wrap gap-2 border-t border-teal-100 pt-2">
                                                        <p class="w-full text-[10px] font-semibold text-gray-400 uppercase mb-0.5"><i class="fa fa-paperclip mr-1"></i>Lampiran</p>
                                                        @foreach($kirLampiran as $klf)
                                                            @php $klfUrl = $klf['public_url'] ?? (isset($klf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($klf['path']) : null); @endphp
                                                            @if($klfUrl)<a href="{{ $klfUrl }}" target="_blank" class="text-xs text-blue-600 hover:underline truncate max-w-[200px]" title="{{ $klf['original_name']??'' }}">{{ $klf['original_name']??'file' }}</a>@endif
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                    {{-- STNK --}}
                                                    @elseif($d->source_type === 'stnk')
                                                    <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                        @if(isset($sd['tahun_stnk']))<div><p class="text-[10px] text-gray-400 uppercase">Tahun STNK</p><p class="text-xs font-semibold text-gray-700">{{ $sd['tahun_stnk'] }}</p></div>@endif
                                                        @if(isset($sd['tanggal_stnk']))<div><p class="text-[10px] text-gray-400 uppercase">Tgl STNK</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_stnk'])->format('d M Y') }}</p></div>@endif
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Biaya</p><p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['biaya']??$d->nominal??0,0,',','.') }}</p></div>
                                                    </div>
                                                    @php
                                                        $stnkLampiran = $sd['temp_files']['attachments'] ?? [];
                                                        if (empty($stnkLampiran)) {
                                                            $stnkExistingId = $sd['existing_record_id'] ?? null;
                                                            if ($stnkExistingId) {
                                                                foreach (\App\Models\Attachment::where('relation_type','stnk')->where('relation_id',$stnkExistingId)->get() as $att) {
                                                                    $stnkLampiran[] = ['original_name'=>$att->file_name,'public_url'=>asset($att->file_path)];
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if(!empty($stnkLampiran))
                                                    <div class="px-4 pb-3 flex flex-wrap gap-2 border-t border-indigo-100 pt-2">
                                                        <p class="w-full text-[10px] font-semibold text-gray-400 uppercase mb-0.5"><i class="fa fa-paperclip mr-1"></i>Lampiran</p>
                                                        @foreach($stnkLampiran as $slf)
                                                            @php $slfUrl = $slf['public_url'] ?? (isset($slf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($slf['path']) : null); @endphp
                                                            @if($slfUrl)<a href="{{ $slfUrl }}" target="_blank" class="text-xs text-blue-600 hover:underline truncate max-w-[200px]" title="{{ $slf['original_name']??'' }}">{{ $slf['original_name']??'file' }}</a>@endif
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                    {{-- GPS --}}
                                                    @elseif(in_array($d->source_type, ['gps', 'gps_perpanjang']))
                                                    @php
                                                        $gpsItems    = $sd['gps_items'] ?? [];
                                                        $itemDecMap  = collect($sd['item_decisions'] ?? [])->keyBy(fn($dec) => (int)($dec['idx'] ?? -1));
                                                        // Filter item sesuai tab: approved → tampil di Disetujui, rejected → Ditolak
                                                        // Jika belum ada keputusan, tampilkan semua
                                                        if ($itemDecMap->isNotEmpty()) {
                                                            if (in_array($tab ?? '', ['Disetujui'])) {
                                                                $filteredGpsItems = collect($gpsItems)
                                                                    ->filter(fn($g, $i) => ($itemDecMap[(int)$i]['action'] ?? '') === 'approved')
                                                                    ->all();
                                                            } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                                $filteredGpsItems = collect($gpsItems)
                                                                    ->filter(fn($g, $i) => ($itemDecMap[(int)$i]['action'] ?? '') !== 'approved' && $itemDecMap->has((int)$i))
                                                                    ->all();
                                                            } else {
                                                                // Tab Diajukan/Pending: hanya tampilkan item yang BELUM diputuskan
                                                                // Item yang sudah approved di item_decisions tidak boleh muncul di sini
                                                                $filteredGpsItems = collect($gpsItems)
                                                                    ->filter(fn($g, $i) => ($itemDecMap[(int)$i]['action'] ?? '') !== 'approved')
                                                                    ->all();
                                                            }
                                                        } else {
                                                            $filteredGpsItems = $gpsItems;
                                                        }
                                                        $totalFiltered = collect($filteredGpsItems)->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                    @endphp
                                                    @if(count($filteredGpsItems) > 0)
                                                    <table class="w-full text-xs">
                                                        <thead>
                                                            <tr class="bg-green-50/60 border-y border-green-100">
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">#</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Type GPS</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Tgl Bayar</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Berlaku s/d</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Bank</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">No. Rekening</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Lampiran</th>
                                                                <th class="text-right px-4 py-2 font-semibold text-gray-500">Biaya Sewa</th>
                                                                @if($itemDecMap->isNotEmpty())
                                                                    <th class="text-center px-4 py-2 font-semibold text-gray-500">Status</th>
                                                                @endif
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($filteredGpsItems as $gi => $gitem)
                                                            @php
                                                                $gps     = isset($gitem['gps_id']) ? \App\Models\Gps::find($gitem['gps_id']) : null;
                                                                $itemDec = $itemDecMap[$gi] ?? null;
                                                            @endphp
                                                            <tr class="border-t border-gray-50 {{ ($loop->index ?? 0)%2===0 ? 'bg-white' : 'bg-gray-50/40' }}">
                                                                <td class="px-4 py-2 text-gray-400">{{ $loop->iteration }}</td>
                                                                <td class="px-4 py-2 font-medium text-gray-700">
                                                                    @if($gps)
                                                                        <span class="font-semibold">{{ $gps->nama_gps ?? '-' }}</span>
                                                                        <span class="text-gray-400 ml-1">({{ $gitem['type'] ?? '-' }})</span>
                                                                    @else
                                                                        {{ $gitem['type'] ?? '-' }}
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-2 text-gray-600">
                                                                    {{ isset($sd['tanggal_bayar']) ? \Carbon\Carbon::parse($sd['tanggal_bayar'])->format('d M Y') : '-' }}
                                                                </td>
                                                                <td class="px-4 py-2 text-gray-600">
                                                                    {{ isset($sd['tanggal_habis']) ? \Carbon\Carbon::parse($sd['tanggal_habis'])->format('d M Y') : '-' }}
                                                                </td>
                                                                <td class="px-4 py-2 text-gray-600">{{ $gitem['nama_bank'] ?? '-' }}</td>
                                                                <td class="px-4 py-2 font-mono text-gray-600">{{ $gitem['no_rekening'] ?? '-' }}</td>
                                                                <td class="px-4 py-2">
                                                                    @php
                                                                        $gpsLamp = $sd['temp_files']['gps_items'][$gi]['lampiran'] ?? [];
                                                                    @endphp
                                                                    @if(!empty($gpsLamp))
                                                                        <div class="flex flex-col gap-0.5">
                                                                        @foreach($gpsLamp as $gf)
                                                                            @php $gfUrl = isset($gf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($gf['path']) : null; @endphp
                                                                            @if($gfUrl)
                                                                                <a href="{{ $gfUrl }}" target="_blank"
                                                                                    class="text-[11px] text-blue-600 hover:underline truncate max-w-[120px]"
                                                                                    title="{{ $gf['original_name'] ?? '' }}">{{ $gf['original_name'] ?? 'file' }}</a>
                                                                            @endif
                                                                        @endforeach
                                                                        </div>
                                                                    @else
                                                                        <span class="text-gray-300 text-[10px]">—</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-2 text-right font-semibold {{ in_array($tab ?? '', ['Ditolak']) ? 'text-red-500' : 'text-emerald-600' }}">
                                                                    Rp {{ number_format($gitem['biaya_sewa'] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                @if($itemDecMap->isNotEmpty())
                                                                    <td class="px-4 py-2 text-center">
                                                                        @if($itemDec && $itemDec['action'] === 'approved')
                                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">
                                                                                <i class="fa fa-check text-[8px]"></i> Disetujui
                                                                            </span>
                                                                        @elseif($itemDec)
                                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700">
                                                                                <i class="fa fa-times text-[8px]"></i> Ditolak
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                            <tr class="border-t-2 border-gray-200 bg-gray-50">
                                                                <td colspan="{{ $itemDecMap->isNotEmpty() ? 8 : 7 }}" class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Total</td>
                                                                <td class="px-4 py-2 text-right text-sm font-bold {{ in_array($tab ?? '', ['Ditolak']) ? 'text-red-500' : 'text-emerald-600' }}">
                                                                    Rp {{ number_format($totalFiltered, 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    @else
                                                    <div class="px-4 py-3 grid grid-cols-2 gap-3">
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Alasan</p><p class="text-xs text-gray-600">{{ $d->alasan_permintaan ?: '-' }}</p></div>
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Total</p><p class="text-xs font-bold text-emerald-600">Rp {{ number_format($d->nominal??0,0,',','.') }}</p></div>
                                                    </div>
                                                    @endif
                                                    {{-- Fallback --}}
                                                    @elseif($d->source_type === 'service_part')
                                                    @php
                                                        $spKendaraan = isset($sd['kendaraan_id']) ? \App\Models\Kendaraan::find($sd['kendaraan_id']) : null;
                                                        $spParts    = $sd['parts'] ?? [];
                                                        $spDecMap   = collect($sd['item_decisions'] ?? [])->keyBy(fn($dec) => (int)($dec['idx'] ?? -1));
                                                        if ($spDecMap->isNotEmpty()) {
                                                            if (in_array($tab ?? '', ['Disetujui'])) {
                                                                $filteredParts = collect($spParts)->filter(fn($p, $i) => ($spDecMap[(int)$i]['action'] ?? '') === 'approved')->values()->all();
                                                            } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                                $filteredParts = collect($spParts)->filter(fn($p, $i) => ($spDecMap[(int)$i]['action'] ?? '') !== 'approved' && $spDecMap->has((int)$i))->values()->all();
                                                            } else {
                                                                $filteredParts = $spParts;
                                                            }
                                                        } else {
                                                            $filteredParts = $spParts;
                                                        }
                                                        $spTotal = collect($filteredParts)->sum(fn($p) => $p['biaya'] ?? 0);
                                                    @endphp
                                                    {{-- Header info kendaraan, tgl service, km --}}
                                                    <div class="px-4 py-3 grid grid-cols-3 gap-3 bg-orange-50/40 border-b border-orange-100 text-xs">
                                                        <div>
                                                            <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Kendaraan</p>
                                                            <p class="font-semibold text-gray-800">{{ $spKendaraan ? $spKendaraan->nopol . ' — ' . $spKendaraan->merk : '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Tgl Service</p>
                                                            <p class="text-gray-700">{{ isset($sd['tanggal_service']) ? \Carbon\Carbon::parse($sd['tanggal_service'])->format('d M Y') : '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">KM / Keluhan</p>
                                                            <p class="text-gray-700">{{ ($sd['kilometer'] ?? '-') . ($sd['keluhan'] ? ' — ' . \Illuminate\Support\Str::limit($sd['keluhan'], 25) : '') }}</p>
                                                        </div>
                                                    </div>
                                                    @if(count($filteredParts) > 0)
                                                    <table class="w-full text-xs">
                                                        <thead>
                                                            <tr class="bg-orange-50/60 border-y border-orange-100">
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">#</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Nama Part</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Kategori</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Kondisi</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Service</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Keterangan Limit</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Bank</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">No. Rekening</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Atas Nama</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Lampiran</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Bukti</th>
                                                                <th class="text-right px-4 py-2 font-semibold text-gray-500">Biaya</th>
                                                                @if($spDecMap->isNotEmpty())
                                                                    <th class="text-center px-4 py-2 font-semibold text-gray-500">Status</th>
                                                                @endif
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        @php
                                                            // Akumulasi biaya per kategori dalam satu Pembayaran ini
                                                            // untuk menghitung sisa limit kumulatif yang benar per item
                                                            $pembBatchBiayaPerCategory = [];
                                                        @endphp
                                                        @foreach($filteredParts as $spi => $spart)
                                                            @php
                                                                $spCat   = isset($spart['category_id']) ? \App\Models\ServiceCategory::find($spart['category_id']) : null;
                                                                $spCatNm = $spCat ? $spCat->nama : ($spart['nama_category_baru'] ?? '-');
                                                                $spDec   = $spDecMap[$spi] ?? null;
                                                                // Lampiran: file dari input part (temp_files)
                                                                $spLampiran = $sd['temp_files']['parts'][$spi]['bukti'] ?? [];
                                                                // Bukti: file yang diupload saat approve di halaman pembayaran
                                                                $spBukti = isset($spart['bukti_bayar_admin']) && $spart['bukti_bayar_admin']
                                                                    ? [$spart['bukti_bayar_admin']]
                                                                    : [];
                                                            @endphp
                                                            <tr class="border-t border-gray-50 {{ ($spi%2===0)?'bg-white':'bg-gray-50/40' }}">
                                                                <td class="px-4 py-2 text-gray-400">{{ $spi + 1 }}</td>
                                                                <td class="px-4 py-2 font-medium text-gray-700">
                                                                    {{ $spart['nama_part'] ?? '-' }}
                                                                    @if(!empty($spart['part_number']) && $spart['part_number'] !== '-')
                                                                        <span class="text-gray-400 text-[10px] font-mono ml-1">({{ $spart['part_number'] }})</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-2">
                                                                    <span class="bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded text-[10px] font-semibold">{{ $spCatNm }}</span>
                                                                </td>
                                                                <td class="px-4 py-2 text-gray-600">{{ $spart['kondisi'] ?? '-' }}</td>
                                                                {{-- KOLOM STATUS LIMIT: Service vs Limit --}}
                                                                @php $snap = $spart['limit_snapshot'] ?? null; @endphp
                                                                <td class="px-4 py-2">
                                                                    @if($snap)
                                                                    <div class="flex flex-col gap-y-1 text-[11px]">
                                                                        {{-- Biaya --}}
                                                                        <span class="{{ $snap['biaya_lewat'] ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                                                            Rp {{ number_format($snap['service_biaya'], 0, ',', '.') }}
                                                                        </span>
                                                                        {{-- Tanggal --}}
                                                                        <span class="{{ $snap['tanggal_lewat'] ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                                                            {{ $snap['service_tanggal'] ?? '—' }}
                                                                        </span>
                                                                        {{-- KM --}}
                                                                        <span class="{{ $snap['km_lewat'] ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                                                            KM {{ number_format($snap['service_km'], 0, ',', '.') }}
                                                                        </span>
                                                                    </div>
                                                                    @else
                                                                        <span class="text-gray-300 text-[10px]">—</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-2 text-gray-500">
                                                                    @if(!empty($snap))
                                                                        @php
                                                                            // Gunakan nilai dari snapshot yang tersimpan — sudah diperbaiki via
                                                                            // command limit:refresh-snapshot. Live recalculate dihapus karena
                                                                            // part dari pembayaran Diajukan bisa sudah masuk DB via jalur lain
                                                                            // sehingga kumulatif double-count dan hasilnya tidak akurat.
                                                                            $ketBiayaLewatLive = $snap['biaya_lewat'] ?? false;
                                                                            $ketBiayaSamaLive  = $snap['biaya_sama']  ?? false;

                                                                            // Render per dimensi secara terurut: Biaya → Jangka Waktu → KM
                                                                            $ketDimensi = [];

                                                                            // Helper: format sisa hari → string
                                                                            $fmtSisaWaktu = function(int $h): string {
                                                                                if ($h <= 0)  return 'sisa 0 hari';
                                                                                if ($h <= 30) return 'sisa ' . $h . ' hari';
                                                                                if ($h < 360) {
                                                                                    $b = (int)floor($h/30); $s = $h - $b*30;
                                                                                    return 'sisa ' . $b . ' bulan' . ($s > 0 ? ' ' . $s . ' hari' : '');
                                                                                }
                                                                                $t = (int)floor($h/365); $b = (int)floor(($h - $t*365)/30);
                                                                                return 'sisa ' . $t . ' tahun' . ($b > 0 ? ' ' . $b . ' bulan' : '');
                                                                            };

                                                                            // 1. Biaya
                                                                            if (!empty($snap['limit_biaya'])) {
                                                                                if ($ketBiayaLewatLive) {
                                                                                    $ketDimensi[] = ['label' => 'Sudah melebihi limit biaya', 'color' => 'bg-red-100 text-red-700'];
                                                                                } elseif ($ketBiayaSamaLive) {
                                                                                    $ketDimensi[] = ['label' => 'Sudah mencapai batas limit biaya', 'color' => 'bg-yellow-100 text-yellow-700'];
                                                                                } else {
                                                                                    // Hitung biaya item sebelumnya sekategori dalam Pembayaran ini
                                                                                    $pembCatIdBiaya      = (int) ($spart['category_id'] ?? 0);
                                                                                    $pembBatchExtraBiaya = $pembCatIdBiaya ? (int) ($pembBatchBiayaPerCategory[$pembCatIdBiaya] ?? 0) : 0;

                                                                                    $sisaBiayaLabel = '';
                                                                                    // Prioritas 1: parse dari keterangan_limit yang sudah benar (data baru via Task 1)
                                                                                    $ketLimitStr    = $spart['keterangan_limit'] ?? '';
                                                                                    if (preg_match('/Belum mencapai limit biaya\s*(\([^)]+\))/i', $ketLimitStr, $m)) {
                                                                                        $sisaBiayaLabel = ' ' . $m[1];
                                                                                    } elseif (isset($snap['sisa_limit_biaya'])) {
                                                                                        // Fallback: sisa_limit_biaya dari snapshot (Task 1 sudah benar untuk data baru)
                                                                                        // Untuk data lama, kurangi manual dengan batch extra
                                                                                        $sisaSetelah = (int) $snap['sisa_limit_biaya'] - $pembBatchExtraBiaya;
                                                                                        if ($sisaSetelah >= 0) {
                                                                                            $sisaBiayaLabel = ' (sisa Rp ' . number_format($sisaSetelah, 0, ',', '.') . ')';
                                                                                        }
                                                                                    }
                                                                                    $ketDimensi[] = ['label' => 'Belum mencapai limit biaya' . $sisaBiayaLabel, 'color' => 'bg-green-100 text-green-700'];
                                                                                }
                                                                            }

                                                                            // 2. Jangka Waktu
                                                                            if (!empty($snap['limit_interval_label'])) {
                                                                                if (!empty($snap['tanggal_lewat'])) {
                                                                                    $ketDimensi[] = ['label' => 'Sudah melebihi batas limit jangka waktu', 'color' => 'bg-red-100 text-red-700'];
                                                                                } elseif (!empty($snap['tanggal_sama'])) {
                                                                                    $ketDimensi[] = ['label' => 'Sudah mencapai batas limit jangka waktu', 'color' => 'bg-yellow-100 text-yellow-700'];
                                                                                } else {
                                                                                    $sisaWaktuLabel = '';
                                                                                    if (!empty($snap['tgl_limit_interval'])) {
                                                                                        $hariSisa = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($snap['tgl_limit_interval'])->startOfDay(), false);
                                                                                        if ($hariSisa > 0) $sisaWaktuLabel = ' (' . $fmtSisaWaktu($hariSisa) . ')';
                                                                                    }
                                                                                    $ketDimensi[] = ['label' => 'Belum mencapai limit jangka waktu' . $sisaWaktuLabel, 'color' => 'bg-green-100 text-green-700'];
                                                                                }
                                                                            }

                                                                            // 3. KM
                                                                            if (!empty($snap['limit_km_target'])) {
                                                                                if (!empty($snap['km_lewat'])) {
                                                                                    $ketDimensi[] = ['label' => 'Sudah melebihi batas limit KM', 'color' => 'bg-red-100 text-red-700'];
                                                                                } elseif (!empty($snap['km_sama'])) {
                                                                                    $ketDimensi[] = ['label' => 'Sudah mencapai batas limit KM', 'color' => 'bg-yellow-100 text-yellow-700'];
                                                                                } else {
                                                                                    $sisaKmLabel = isset($snap['sisa_limit_km']) ? ' (sisa ' . number_format($snap['sisa_limit_km'], 0, ',', '.') . ' km)' : '';
                                                                                    $ketDimensi[] = ['label' => 'Belum mencapai batas limit KM' . $sisaKmLabel, 'color' => 'bg-green-100 text-green-700'];
                                                                                }
                                                                            }

                                                                            // 4. Jumlah pasang
                                                                            if (!empty($snap['limit_jumlah'])) {
                                                                                if (!empty($snap['jumlah_lewat'])) {
                                                                                    $ketDimensi[] = ['label' => 'Sudah melebihi batas pemasangan part', 'color' => 'bg-red-100 text-red-700'];
                                                                                } elseif (!empty($snap['jumlah_sama'])) {
                                                                                    $ketDimensi[] = ['label' => 'Sudah mencapai batas pemasangan part', 'color' => 'bg-yellow-100 text-yellow-700'];
                                                                                } else {
                                                                                    $sisaPasangLabel = isset($snap['sisa_pasang']) ? ' (sisa ' . $snap['sisa_pasang'] . ' pcs)' : '';
                                                                                    $ketDimensi[] = ['label' => 'Belum mencapai batas pemasangan part' . $sisaPasangLabel, 'color' => 'bg-green-100 text-green-700'];
                                                                                }
                                                                            }

                                                                            // Jika tidak ada dimensi limit sama sekali, tampilkan "Belum mencapai limit"
                                                                            if (empty($ketDimensi)) {
                                                                                $ketDimensi[] = ['label' => 'Belum mencapai limit', 'color' => 'bg-green-100 text-green-700'];
                                                                            }
                                                                        @endphp
                                                                        <div class="flex flex-col gap-0.5">
                                                                            @foreach($ketDimensi as $dim)
                                                                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-medium {{ $dim['color'] }}">{{ $dim['label'] }}</span>
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <span class="text-gray-300">—</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-2 text-gray-600">{{ $spart['nama_bank'] ?? '-' }}</td>
                                                                <td class="px-4 py-2 font-mono text-gray-600">{{ $spart['no_rekening'] ?? '-' }}</td>
                                                                <td class="px-4 py-2 text-gray-600">{{ $spart['nama_rekening'] ?? '-' }}</td>

                                                                {{-- Kolom LAMPIRAN: dari input part (temp_files) --}}
                                                                <td class="px-4 py-2">
                                                                    @if(count($spLampiran) > 0)
                                                                        <div class="flex flex-col gap-1">
                                                                            @foreach($spLampiran as $bf)
                                                                                @php
                                                                                    $bPath = $bf['path'] ?? '';
                                                                                    $bName = $bf['original_name'] ?? basename($bPath);
                                                                                    $bUrl  = asset('storage/' . $bPath);
                                                                                @endphp
                                                                                <a href="{{ $bUrl }}" target="_blank"
                                                                                    class="text-blue-500 hover:text-blue-700 hover:underline text-[11px] truncate max-w-[150px]"
                                                                                    title="{{ $bName }}">{{ $bName }}</a>
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <span class="text-gray-300 text-[10px]">—</span>
                                                                    @endif
                                                                </td>

                                                                {{-- Kolom BUKTI: diupload saat approve di halaman pembayaran --}}
                                                                <td class="px-4 py-2">
                                                                    @if(!empty($spBukti))
                                                                        @php
                                                                            $buktiVal = $spBukti[0];
                                                                            if (is_array($buktiVal)) {
                                                                                $bPath = $buktiVal['path'] ?? '';
                                                                                $bName = $buktiVal['original_name'] ?? basename($bPath);
                                                                                $bExt  = strtolower($buktiVal['extension'] ?? pathinfo($bPath, PATHINFO_EXTENSION));
                                                                                $bUrl  = asset('storage/' . $bPath);
                                                                            } else {
                                                                                $bPath = $buktiVal;
                                                                                $bName = basename($bPath);
                                                                                $bExt  = strtolower(pathinfo($bPath, PATHINFO_EXTENSION));
                                                                                $bUrl  = asset($bPath);
                                                                            }
                                                                            $bIcon = in_array($bExt, ['jpg','jpeg','png','gif','webp'])
                                                                                ? 'fa-image'
                                                                                : ($bExt === 'pdf' ? 'fa-file-pdf' : 'fa-paperclip');
                                                                        @endphp
                                                                        <a href="{{ $bUrl }}" target="_blank"
                                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 text-[10px]"
                                                                            title="{{ $bName }}">
                                                                            <i class="fa {{ $bIcon }} text-[9px]"></i>
                                                                            {{ Str::limit($bName, 20) }}
                                                                        </a>
                                                                    @else
                                                                        <span class="text-gray-300 text-[10px]">—</span>
                                                                    @endif
                                                                </td>

                                                                <td class="px-4 py-2 text-right font-semibold {{ in_array($tab ?? '', ['Ditolak']) ? 'text-red-500' : 'text-emerald-600' }}">
                                                                    Rp {{ number_format($spart['biaya'] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                @if($spDecMap->isNotEmpty())
                                                                    <td class="px-4 py-2 text-center">
                                                                        @if($spDec && $spDec['action'] === 'approved')
                                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-green-100 text-green-700"><i class="fa fa-check text-[8px]"></i> Disetujui</span>
                                                                        @elseif($spDec)
                                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700"><i class="fa fa-times text-[8px]"></i> Ditolak</span>
                                                                        @endif
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            @php
                                                                // Akumulasikan biaya item ini untuk item berikutnya sekategori dalam batch yang sama
                                                                $pembCatIdAkum = (int) ($spart['category_id'] ?? 0);
                                                                if ($pembCatIdAkum) {
                                                                    $pembBatchBiayaPerCategory[$pembCatIdAkum] = ($pembBatchBiayaPerCategory[$pembCatIdAkum] ?? 0) + (int) ($spart['biaya'] ?? 0);
                                                                }
                                                            @endphp
                                                        @endforeach
                                                            <tr class="border-t-2 border-gray-200 bg-gray-50">
                                                                <td colspan="{{ $spDecMap->isNotEmpty() ? 11 : 10 }}" class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Total</td>
                                                                <td class="px-4 py-2 text-right text-sm font-bold {{ in_array($tab ?? '', ['Ditolak']) ? 'text-red-500' : 'text-emerald-600' }}">
                                                                    Rp {{ number_format($spTotal, 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    @else
                                                    <div class="px-4 py-3 text-xs text-gray-400 italic">Tidak ada part yang ditampilkan.</div>
                                                    @endif
                                                    {{-- Service Asuransi --}}
                                                    @elseif($d->source_type === 'service_asuransi')
                                                    @php
                                                        $saAllKejadians = $sd['kejadians'] ?? [];
                                                        $saKeterangan = $sd['keterangan'] ?? $d->keterangan ?? null;
                                                        $saKend  = isset($sd['kendaraan_id']) ? \App\Models\Kendaraan::find($sd['kendaraan_id']) : null;
                                                        $saDecMap = collect($sd['item_decisions'] ?? [])->keyBy(fn($dec) => (int)($dec['idx'] ?? -1));

                                                        // Filter kejadian berdasarkan tab aktif — identik dengan pola service_part
                                                        // PENTING: jangan pakai ->values() agar original index dipertahankan
                                                        // sehingga lookup $saDecMap[$i] tetap akurat
                                                        if ($saDecMap->isNotEmpty()) {
                                                            if (in_array($tab ?? '', ['Disetujui'])) {
                                                                $saKejadians = collect($saAllKejadians)->filter(fn($k, $i) => ($saDecMap[(int)$i]['action'] ?? '') === 'approved')->all();
                                                            } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                                $saKejadians = collect($saAllKejadians)->filter(fn($k, $i) => ($saDecMap[(int)$i]['action'] ?? '') !== 'approved' && $saDecMap->has((int)$i))->all();
                                                            } elseif (in_array($tab ?? '', ['Diajukan'])) {
                                                                // Tab Diajukan PR partial: hanya kejadian yang belum diputuskan
                                                                $saKejadians = collect($saAllKejadians)->filter(fn($k, $i) => !$saDecMap->has((int)$i))->all();
                                                            } else {
                                                                $saKejadians = $saAllKejadians;
                                                            }
                                                        } else {
                                                            $saKejadians = $saAllKejadians;
                                                        }
                                                        $saTotal = collect($saKejadians)->sum(fn($k) => $k['biaya'] ?? 0);
                                                    @endphp
                                                    <div class="px-4 py-3">
                                                        {{-- Info header --}}
                                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3 text-xs">
                                                            <div>
                                                                <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Kendaraan</p>
                                                                <p class="font-semibold text-gray-800">{{ $saKend ? $saKend->nopol . ' — ' . $saKend->merk : '-' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Nama Asuransi</p>
                                                                <p class="text-gray-700">{{ $sd['nama_asuransi'] ?? '-' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Tgl Service</p>
                                                                <p class="text-gray-700">{{ isset($sd['tanggal_service']) ? \Carbon\Carbon::parse($sd['tanggal_service'])->format('d M Y') : '-' }}</p>
                                                            </div>
                                                            @if($saKeterangan)
                                                            <div>
                                                                <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Keterangan</p>
                                                                <p class="font-mono text-[11px] text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded">{{ $saKeterangan }}</p>
                                                            </div>
                                                            @endif
                                                        </div>

                                                        {{-- Lampiran dari temp_files --}}
                                                        @php
                                                            $saLampiran = [];
                                                            foreach (($saAllKejadians) as $kjIdx => $kj) {
                                                                foreach (($kj['lampiran'] ?? []) as $lf) {
                                                                    $saLampiran[] = array_merge($lf, ['_label' => $kj['nama_kejadian'] ?? '#'.($kjIdx+1)]);
                                                                }
                                                            }
                                                            foreach (($sd['temp_files']['attachments'] ?? []) as $af) {
                                                                $saLampiran[] = $af;
                                                            }
                                                        @endphp
                                                        @if(!empty($saLampiran))
                                                        <div class="mb-3 flex flex-wrap gap-1.5">
                                                            @foreach($saLampiran as $lf)
                                                                @php
                                                                    $lfPath = $lf['path'] ?? '';
                                                                    $lfName = $lf['original_name'] ?? basename($lfPath);
                                                                    $lfExt  = strtolower($lf['extension'] ?? pathinfo($lfPath, PATHINFO_EXTENSION));
                                                                    $lfIsImg = in_array($lfExt, ['jpg','jpeg','png','webp','gif']);
                                                                    $lfIcon  = $lfIsImg ? 'fa-image text-blue-400' : ($lfExt === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                                                                    $lfUrl   = $lfPath ? \Illuminate\Support\Facades\Storage::disk('public')->url($lfPath) : null;
                                                                @endphp
                                                                @if($lfUrl)
                                                                    <a href="{{ $lfUrl }}" target="_blank"
                                                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-lg"
                                                                        title="{{ $lfName }}">
                                                                        <i class="fa {{ $lfIcon }} text-[9px]"></i>
                                                                        <span class="truncate max-w-[140px]">{{ Str::limit($lfName, 20) }}</span>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        @endif

                                                        {{-- Bukti bayar dari approval --}}
                                                        @php
                                                            $saBukti = null;
                                                            $saApproval = $d->approvals->where('action','approved')->first();
                                                            if ($saApproval && !empty($saApproval->bukti_files)) {
                                                                $saBuktiFiles = is_array($saApproval->bukti_files) ? $saApproval->bukti_files : json_decode($saApproval->bukti_files, true);
                                                                $saBukti = $saBuktiFiles[0] ?? null;
                                                            }
                                                        @endphp
                                                        @if($saBukti)
                                                        @php
                                                            $sbPath = $saBukti['path'] ?? '';
                                                            $sbName = $saBukti['original_name'] ?? basename($sbPath);
                                                            $sbUrl  = $sbPath ? \Illuminate\Support\Facades\Storage::disk('public')->url($sbPath) : null;
                                                        @endphp
                                                        @if($sbUrl)
                                                        <div class="mb-3">
                                                            <p class="text-[10px] text-gray-400 uppercase font-semibold mb-1">Bukti Pembayaran</p>
                                                            <a href="{{ $sbUrl }}" target="_blank"
                                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg hover:bg-emerald-100">
                                                                <i class="fa fa-paperclip text-[9px]"></i> {{ Str::limit($sbName, 25) }}
                                                            </a>
                                                        </div>
                                                        @endif
                                                        @endif

                                                        {{-- Tabel kejadian --}}
                                                        @if(!empty($saKejadians))
                                                        <div class="rounded-xl border border-blue-100 overflow-hidden">
                                                            <table class="w-full text-xs">
                                                                <thead>
                                                                    <tr class="bg-blue-50 border-b border-blue-100">
                                                                        <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">#</th>
                                                                        <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Nama Kejadian</th>
                                                                        <th class="text-center px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Lampiran</th>
                                                                        <th class="text-right px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Biaya</th>
                                                                        @if($saDecMap->isNotEmpty())
                                                                            <th class="text-center px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Bukti Bayar</th>
                                                                            <th class="text-center px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Status</th>
                                                                        @endif
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $saKjCounter = 0; @endphp
                                                                    @foreach($saKejadians as $saKjIdx => $saKj)
                                                                    @php $saKjDec = $saDecMap[$saKjIdx] ?? null; $saKjCounter++; @endphp
                                                                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/40">
                                                                        <td class="px-3 py-2 text-gray-400">{{ $saKjCounter }}</td>
                                                                        <td class="px-3 py-2 font-semibold text-gray-800">{{ $saKj['nama_kejadian'] ?? '-' }}</td>
                                                                        <td class="px-3 py-2 text-center">
                                                                            @php $saKjLamp = $saKj['lampiran'] ?? []; @endphp
                                                                            @if(!empty($saKjLamp))
                                                                                <div class="flex flex-wrap gap-0.5 justify-center">
                                                                                @foreach($saKjLamp as $kjf)
                                                                                    @php
                                                                                        $kjfUrl  = isset($kjf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($kjf['path']) : null;
                                                                                        $kjfExt  = strtolower($kjf['extension'] ?? pathinfo($kjf['path'] ?? '', PATHINFO_EXTENSION));
                                                                                        $kjfIcon = in_array($kjfExt, ['jpg','jpeg','png','gif']) ? 'fa-image text-blue-400' : ($kjfExt === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                                                                                    @endphp
                                                                                    @if($kjfUrl)
                                                                                        <a href="{{ $kjfUrl }}" target="_blank"
                                                                                            class="inline-flex items-center gap-0.5 text-[11px] text-blue-600 hover:underline truncate max-w-[120px]"
                                                                                            title="{{ $kjf['original_name'] ?? '' }}">
                                                                                            <i class="fa {{ $kjfIcon }} text-[9px]"></i>
                                                                                            <span class="truncate">{{ Str::limit($kjf['original_name'] ?? 'file', 16) }}</span>
                                                                                        </a>
                                                                                    @endif
                                                                                @endforeach
                                                                                </div>
                                                                            @else
                                                                                <span class="text-gray-300">—</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="px-3 py-2 text-right font-bold {{ ($saKjDec && ($saKjDec['action'] ?? '') === 'rejected') ? 'text-red-500' : 'text-emerald-600' }}">Rp {{ number_format($saKj['biaya'] ?? 0, 0, ',', '.') }}</td>
                                                                        @if($saDecMap->isNotEmpty())
                                                                            {{-- Kolom Bukti Bayar --}}
                                                                            <td class="px-3 py-2 text-center">
                                                                                @php
                                                                                    $saKjBukti = $saKjDec['bukti'] ?? null;
                                                                                    $saKjBuktiUrl = null;
                                                                                    $saKjBuktiName = null;
                                                                                    $saKjBuktiExt = null;
                                                                                    if ($saKjBukti && isset($saKjBukti['path'])) {
                                                                                        $saKjBuktiUrl  = asset($saKjBukti['path']);
                                                                                        $saKjBuktiName = $saKjBukti['original_name'] ?? basename($saKjBukti['path']);
                                                                                        $saKjBuktiExt  = strtolower(pathinfo($saKjBukti['path'], PATHINFO_EXTENSION));
                                                                                    }
                                                                                @endphp
                                                                                @if($saKjBuktiUrl)
                                                                                    @php
                                                                                        $bkIcon = in_array($saKjBuktiExt, ['jpg','jpeg','png','gif','webp'])
                                                                                            ? 'fa-image text-blue-400'
                                                                                            : ($saKjBuktiExt === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                                                                                    @endphp
                                                                                    <a href="{{ $saKjBuktiUrl }}" target="_blank"
                                                                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors"
                                                                                        title="{{ $saKjBuktiName }}">
                                                                                        <i class="fa {{ $bkIcon }} text-[9px]"></i>
                                                                                        <span class="truncate max-w-[90px]">{{ Str::limit($saKjBuktiName, 14) }}</span>
                                                                                    </a>
                                                                                @else
                                                                                    <span class="text-gray-300 text-[10px]">—</span>
                                                                                @endif
                                                                            </td>
                                                                            {{-- Kolom Status --}}
                                                                            <td class="px-3 py-2 text-center">
                                                                                @if($saKjDec && ($saKjDec['action'] ?? '') === 'approved')
                                                                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">
                                                                                        <i class="fa fa-check text-[8px]"></i> Disetujui
                                                                                    </span>
                                                                                @elseif($saKjDec)
                                                                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700"
                                                                                        title="{{ $saKjDec['catatan'] ?? '' }}">
                                                                                        <i class="fa fa-times text-[8px]"></i> Ditolak
                                                                                    </span>
                                                                                    @if(!empty($saKjDec['catatan']))
                                                                                        <p class="text-[9px] text-red-400 mt-0.5 truncate max-w-[100px]" title="{{ $saKjDec['catatan'] }}">{{ Str::limit($saKjDec['catatan'], 20) }}</p>
                                                                                    @endif
                                                                                @else
                                                                                    <span class="text-gray-300 text-[10px]">—</span>
                                                                                @endif
                                                                            </td>
                                                                        @endif
                                                                    </tr>
                                                                    @endforeach
                                                                    <tr class="border-t-2 border-blue-200 bg-blue-50/50">
                                                                        <td colspan="{{ $saDecMap->isNotEmpty() ? 5 : 3 }}" class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Total</td>
                                                                        <td class="px-3 py-2 text-right text-sm font-bold {{ in_array($tab ?? '', ['Ditolak']) ? 'text-red-500' : 'text-emerald-600' }}">Rp {{ number_format($saTotal, 0, ',', '.') }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        @endif
                                                    </div>

                                                    {{-- Service Incident --}}
                                                    @elseif($d->source_type === 'service_incident')
                                                    @php
                                                        $siAllParts   = $sd['parts'] ?? [];
                                                        $siKeterangan = $sd['keterangan'] ?? $d->keterangan ?? null;
                                                        $siKend       = isset($sd['kendaraan_id']) ? \App\Models\Kendaraan::find($sd['kendaraan_id']) : null;
                                                        $siDecMap     = collect($sd['item_decisions'] ?? [])->keyBy(fn($dec) => (int)($dec['idx'] ?? -1));

                                                        // Filter parts per tab aktif (sama dengan service_part)
                                                        if ($siDecMap->isNotEmpty()) {
                                                            if (in_array($tab ?? '', ['Disetujui'])) {
                                                                $siParts = collect($siAllParts)->filter(fn($p, $i) => ($siDecMap[(int)$i]['action'] ?? '') === 'approved')->all();
                                                            } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                                $siParts = collect($siAllParts)->filter(fn($p, $i) => ($siDecMap[(int)$i]['action'] ?? '') === 'rejected')->all();
                                                            } elseif (in_array($tab ?? '', ['Diajukan', 'Pending'])) {
                                                                // Hanya item yang belum diputuskan (pending / resubmit)
                                                                $siParts = collect($siAllParts)->filter(fn($p, $i) => !$siDecMap->has((int)$i))->all();
                                                            } else {
                                                                $siParts = $siAllParts;
                                                            }
                                                        } else {
                                                            $siParts = $siAllParts;
                                                        }
                                                        $siTotal = collect($siParts)->sum(fn($p) => $p['biaya'] ?? 0);
                                                    @endphp
                                                    <div class="px-4 py-3">
                                                        {{-- Info header --}}
                                                        <div class="grid grid-cols-4 gap-3 mb-3 text-xs">
                                                            <div>
                                                                <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Kendaraan</p>
                                                                <p class="font-semibold text-gray-800">{{ $siKend ? $siKend->nopol . ' — ' . $siKend->merk : '-' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Tgl Service</p>
                                                                <p class="text-gray-700">{{ isset($sd['tanggal_service']) ? \Carbon\Carbon::parse($sd['tanggal_service'])->format('d M Y') : '-' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">KM / Keluhan</p>
                                                                <p class="text-gray-700">{{ $sd['kilometer'] ?? '-' }}{{ isset($sd['keluhan']) ? ' — ' . Str::limit($sd['keluhan'], 25) : '' }}</p>
                                                            </div>
                                                            @if($siKeterangan)
                                                            <div>
                                                                <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Keterangan</p>
                                                                <p class="font-mono text-[11px] text-red-700 bg-red-50 px-1.5 py-0.5 rounded">{{ $siKeterangan }}</p>
                                                            </div>
                                                            @endif
                                                        </div>

                                                        {{-- Tabel parts (lampiran & bukti per item) --}}
                                                        @if(!empty($siParts))
                                                        <div class="rounded-xl border border-red-100 overflow-hidden">
                                                            <table class="w-full text-xs">
                                                                <thead>
                                                                    <tr class="bg-red-50 border-b border-red-100">
                                                                        <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">#</th>
                                                                        <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Nama Part</th>
                                                                        <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Kategori</th>
                                                                        <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Bank</th>
                                                                        <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">No. Rekening</th>
                                                                        <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Lampiran</th>
                                                                        <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Bukti</th>
                                                                        <th class="text-right px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Biaya</th>
                                                                        @if($siDecMap->isNotEmpty())
                                                                            <th class="text-center px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Status</th>
                                                                        @endif
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($siParts as $siPIdx => $siPart)
                                                                    @php
                                                                        $siCat = isset($siPart['category_id']) ? \App\Models\ServiceCategory::find($siPart['category_id']) : null;
                                                                        // Lampiran: file yang diupload saat buat service incident
                                                                        $siPartLampiran = $sd['temp_files']['parts'][$siPIdx]['bukti'] ?? [];
                                                                        // Bukti bayar admin: diupload saat approve
                                                                        $siPartBukti = isset($siPart['bukti_bayar_admin']) && $siPart['bukti_bayar_admin']
                                                                            ? (is_array($siPart['bukti_bayar_admin']) ? $siPart['bukti_bayar_admin'] : [$siPart['bukti_bayar_admin']])
                                                                            : [];
                                                                        $siPartDec = $siDecMap->get((int)$siPIdx);
                                                                    @endphp
                                                                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/40">
                                                                        <td class="px-3 py-2 text-gray-400">{{ $siPIdx + 1 }}</td>
                                                                        <td class="px-3 py-2 font-semibold text-gray-800">{{ $siPart['nama_part'] ?? '-' }}</td>
                                                                        <td class="px-3 py-2">
                                                                            @if($siCat)
                                                                                <span class="bg-red-100 text-red-700 px-1.5 py-0.5 rounded text-[10px] font-semibold">{{ $siCat->nama }}</span>
                                                                            @elseif(!empty($siPart['category_nama']))
                                                                                <span class="bg-red-100 text-red-700 px-1.5 py-0.5 rounded text-[10px] font-semibold">{{ $siPart['category_nama'] }}</span>
                                                                            @else
                                                                                <span class="text-gray-400">—</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="px-3 py-2 text-gray-600">{{ $siPart['nama_bank'] ?? '-' }}</td>
                                                                        <td class="px-3 py-2 font-mono text-gray-600">{{ $siPart['no_rekening'] ?? '-' }}</td>

                                                                        {{-- Kolom LAMPIRAN: file yang diupload saat buat service incident --}}
                                                                        <td class="px-3 py-2">
                                                                            @if(count($siPartLampiran) > 0)
                                                                                <div class="flex flex-col gap-1">
                                                                                    @foreach($siPartLampiran as $siLf)
                                                                                        @php
                                                                                            $siLfPath = $siLf['path'] ?? '';
                                                                                            $siLfName = $siLf['original_name'] ?? basename($siLfPath);
                                                                                            $siLfExt  = strtolower($siLf['extension'] ?? pathinfo($siLfPath, PATHINFO_EXTENSION));
                                                                                            $siLfIsImg = in_array($siLfExt, ['jpg','jpeg','png','webp','gif']);
                                                                                            $siLfIcon  = $siLfIsImg ? 'fa-image text-blue-400' : ($siLfExt === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                                                                                            $siLfUrl   = $siLfPath ? asset('storage/' . $siLfPath) : null;
                                                                                        @endphp
                                                                                        @if($siLfUrl)
                                                                                            <a href="{{ $siLfUrl }}" target="_blank"
                                                                                                class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:text-blue-800 hover:underline truncate max-w-[150px]"
                                                                                                title="{{ $siLfName }}">
                                                                                                <i class="fa {{ $siLfIcon }} text-[9px] flex-shrink-0"></i>
                                                                                                <span class="truncate">{{ Str::limit($siLfName, 18) }}</span>
                                                                                            </a>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                            @else
                                                                                <span class="text-gray-300 text-[10px]">—</span>
                                                                            @endif
                                                                        </td>

                                                                        {{-- Kolom BUKTI: diupload saat approve --}}
                                                                        <td class="px-3 py-2">
                                                                            @if(!empty($siPartBukti))
                                                                                @php
                                                                                    $siBVal = $siPartBukti[0];
                                                                                    $siBPath = is_array($siBVal) ? ($siBVal['path'] ?? '') : $siBVal;
                                                                                    $siBName = is_array($siBVal) ? ($siBVal['original_name'] ?? basename($siBPath)) : basename($siBPath);
                                                                                    $siBUrl  = $siBPath ? asset($siBPath) : null;
                                                                                @endphp
                                                                                @if($siBUrl)
                                                                                    <a href="{{ $siBUrl }}" target="_blank"
                                                                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg hover:bg-emerald-100 max-w-[150px]"
                                                                                        title="{{ $siBName }}">
                                                                                        <i class="fa fa-paperclip text-[9px]"></i>
                                                                                        <span class="truncate">{{ Str::limit($siBName, 18) }}</span>
                                                                                    </a>
                                                                                @endif
                                                                            @else
                                                                                <span class="text-gray-300 text-[10px]">—</span>
                                                                            @endif
                                                                        </td>

                                                                        <td class="px-3 py-2 text-right font-bold {{ in_array($tab ?? '', ['Ditolak']) ? 'text-red-500' : 'text-emerald-600' }}">
                                                                            Rp {{ number_format($siPart['biaya'] ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                        @if($siDecMap->isNotEmpty())
                                                                            <td class="px-3 py-2 text-center">
                                                                                @if($siPartDec && ($siPartDec['action'] ?? '') === 'approved')
                                                                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">
                                                                                        <i class="fa fa-check text-[8px]"></i> Disetujui
                                                                                    </span>
                                                                                @elseif($siPartDec && ($siPartDec['action'] ?? '') === 'rejected')
                                                                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700">
                                                                                        <i class="fa fa-times text-[8px]"></i> Ditolak
                                                                                    </span>
                                                                                @else
                                                                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700">
                                                                                        <i class="fa fa-clock text-[8px]"></i> Diajukan
                                                                                    </span>
                                                                                @endif
                                                                            </td>
                                                                        @endif
                                                                    </tr>
                                                                    @endforeach
                                                                    <tr class="border-t-2 border-red-200 bg-red-50/50">
                                                                        <td colspan="{{ $siDecMap->isNotEmpty() ? 8 : 7 }}" class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Total</td>
                                                                        <td class="px-3 py-2 text-right text-sm font-bold {{ in_array($tab ?? '', ['Ditolak']) ? 'text-red-500' : 'text-emerald-600' }}">
                                                                            Rp {{ number_format($siTotal, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        @endif
                                                    </div>

                                                    {{-- Fallback --}}
                                                    @else
                                                    <div class="px-4 py-3 grid grid-cols-2 gap-3">
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

                                                {{-- Rekening Bank --}}
                                                {{-- Untuk service_part/service_incident: info bank sudah tampil per-part di tabel di atas --}}
                                                @if(!in_array($d->source_type, ['service_part', 'service_incident']))
                                                @if($d->nama_bank || $d->no_rekening || $d->nama_rekening)
                                                <div class="mx-4 mb-3 mt-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                                                    <p class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider mb-2"><i class="bi bi-bank mr-1"></i> Rekening Bank</p>
                                                    <div class="grid grid-cols-3 gap-3">
                                                        @if($d->nama_bank)<div><p class="text-[10px] text-amber-500 uppercase">Bank</p><p class="text-xs font-medium text-gray-700">{{ $d->nama_bank }}</p></div>@endif
                                                        @if($d->no_rekening)<div><p class="text-[10px] text-amber-500 uppercase">No. Rek</p><p class="text-xs font-medium font-mono text-gray-700">{{ $d->no_rekening }}</p></div>@endif
                                                        @if($d->nama_rekening)<div><p class="text-[10px] text-amber-500 uppercase">Atas Nama</p><p class="text-xs font-medium text-gray-700">{{ $d->nama_rekening }}</p></div>@endif
                                                    </div>
                                                </div>
                                                @endif
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

{{-- ================================================================
     MODAL: BULK APPROVE
================================================================ --}}
@if($role === 'superadmin')
<div id="modalBulkApprove" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 overflow-auto" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 my-6" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-sm"><i class="fa fa-check"></i></span>
                    Approve Pembayaran
                </h2>
                <p id="bulkApproveSubtitle" class="text-xs text-gray-500 mt-1 ml-10">Pilih item yang ingin disetujui</p>
            </div>
            <button type="button" onclick="closeBulkApproveModal()"
                class="text-gray-400 hover:text-red-500 text-lg transition-colors"><i class="fa fa-times"></i></button>
        </div>

        <div class="px-6 py-4 space-y-4">
            {{-- Toggle All --}}
            <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-xl">
                <input type="checkbox" id="approveSelectAll" class="rounded text-green-600 cursor-pointer"
                    onchange="toggleApproveAll(this)">
                <label for="approveSelectAll" class="text-sm font-semibold text-green-700 cursor-pointer flex-1">
                    Approve All — Setujui semua item
                </label>
                <span id="approveSelectedCount" class="text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">0 dipilih</span>
            </div>

            {{-- Item list — tiap item punya upload bukti sendiri --}}
            <div id="approveItemList" class="space-y-3 max-h-[28rem] overflow-y-auto pr-1">
                {{-- diisi JS --}}
            </div>

            {{-- Catatan global opsional --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan (opsional)</label>
                <textarea id="approveCatatan" rows="2" placeholder="Catatan persetujuan untuk semua item..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-green-400"></textarea>
            </div>
        </div>

        <div class="border-t border-gray-100 px-6 py-4 flex gap-2">
            <button type="button" onclick="closeBulkApproveModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
            <button type="button" onclick="submitBulkApprove()"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-check-double"></i> Konfirmasi Approve
            </button>
        </div>
    </div>
</div>

{{-- ================================================================
     MODAL: BULK REJECT
================================================================ --}}
<div id="modalBulkReject" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 overflow-auto" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 my-6" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-sm"><i class="fa fa-times"></i></span>
                    Reject Pembayaran
                </h2>
                <p id="bulkRejectSubtitle" class="text-xs text-gray-500 mt-1 ml-10">Pilih item yang ingin ditolak beserta alasannya</p>
            </div>
            <button type="button" onclick="closeBulkRejectModal()"
                class="text-gray-400 hover:text-red-500 text-lg transition-colors"><i class="fa fa-times"></i></button>
        </div>

        <div class="px-6 py-4 space-y-4">
            {{-- Toggle All + alasan global --}}
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl space-y-2.5">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="rejectSelectAll" class="rounded text-red-500 cursor-pointer"
                        onchange="toggleRejectAll(this)">
                    <label for="rejectSelectAll" class="text-sm font-semibold text-red-700 cursor-pointer flex-1">
                        Reject All — Tolak semua item
                    </label>
                    <span id="rejectSelectedCount" class="text-xs font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">0 dipilih</span>
                </div>
                <textarea id="rejectAllAlasan" rows="2" placeholder="Alasan penolakan untuk semua item..."
                    class="w-full border border-red-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-400 bg-white"
                    oninput="syncRejectAllAlasan(this.value)"></textarea>
            </div>

            {{-- Per-item list --}}
            <div id="rejectItemList" class="space-y-3 max-h-72 overflow-y-auto pr-1">
                {{-- diisi JS --}}
            </div>
        </div>

        <div class="border-t border-gray-100 px-6 py-4 flex gap-2">
            <button type="button" onclick="closeBulkRejectModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
            <button type="button" onclick="submitBulkReject()"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-times-circle"></i> Konfirmasi Reject
            </button>
        </div>
    </div>
</div>

{{-- MODAL: SINGLE APPROVE (pengeluaran kendaraan) --}}
<div id="modalSingleApprove" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 overflow-auto" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 my-6" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-sm"><i class="fa fa-check"></i></span>
                    Approve Pengeluaran
                </h2>
                <p id="singleApproveSubtitle" class="text-xs text-gray-500 mt-1 ml-10"></p>
            </div>
            <button type="button" onclick="closeSingleApproveModal()"
                class="text-gray-400 hover:text-red-500 text-lg transition-colors"><i class="fa fa-times"></i></button>
        </div>
        <form id="formSingleApprove" action="" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Bukti Pembayaran <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal">(jpg/png/pdf maks 5MB)</span>
                </label>
                <input type="file" name="bukti[]" multiple required
                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600
                        file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                        file:text-xs file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan <span class="text-red-500">*</span></label>
                <textarea name="catatan" rows="2" required placeholder="Catatan persetujuan (wajib diisi)..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-green-400"></textarea>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeSingleApproveModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-check"></i> Konfirmasi Approve
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: SINGLE REJECT --}}
<div id="modalSingleReject" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 overflow-auto" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 my-6" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-sm"><i class="fa fa-times"></i></span>
                    Reject Pengeluaran
                </h2>
                <p id="singleRejectSubtitle" class="text-xs text-gray-500 mt-1 ml-10"></p>
            </div>
            <button type="button" onclick="closeSingleRejectModal()"
                class="text-gray-400 hover:text-red-500 text-lg transition-colors"><i class="fa fa-times"></i></button>
        </div>
        <form id="formSingleReject" action="" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="catatan" rows="4" required placeholder="Tuliskan alasan penolakan..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-400"></textarea>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeSingleRejectModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-times-circle"></i> Konfirmasi Reject
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: APPROVE SERVICE --}}
<div id="approveServiceModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-sm"><i class="fa fa-check"></i></span>
                    Setujui Pembayaran Service
                </h2>
                <p id="approveServiceSubtitle" class="text-xs text-gray-500 mt-1 ml-10"></p>
            </div>
            <button onclick="closeApproveServiceModal()" class="text-gray-400 hover:text-red-500 text-lg"><i class="fa fa-times"></i></button>
        </div>
        <form id="approveServiceForm" action="" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bukti Pembayaran <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="file" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lampiran Tambahan <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="file" name="lampiran_tambahan" accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
            </div>
            <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                <i class="fa fa-info-circle mr-1"></i> Setelah disetujui, data service otomatis masuk ke riwayat service kendaraan.
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

{{-- MODAL: TOLAK (non-pengeluaran) --}}
@if($role === 'superadmin')
<div id="tolakModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-sm"><i class="fa fa-times"></i></span>
                    Tolak Pembayaran
                </h2>
                <p id="tolakSubtitle" class="text-xs text-gray-500 mt-1 ml-10"></p>
            </div>
            <button onclick="closeTolakModal()" class="text-gray-400 hover:text-red-500 text-lg"><i class="fa fa-times"></i></button>
        </div>
        <form id="tolakForm" action="" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="status" value="Ditolak">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="catatan" id="catatanTolak" rows="4" required placeholder="Tuliskan alasan penolakan..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-400"></textarea>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeTolakModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-times-circle"></i> Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- MODAL: DETAIL --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 max-h-[92vh] flex flex-col" style="animation:slideUp .2s ease">
        {{-- Header --}}
        <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa fa-file-invoice text-blue-500"></i> Detail Pembayaran
                </h2>
                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                    <span id="d_no_pr" class="text-xs font-mono font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded"></span>
                    <span id="d_jenis_badge" class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded"></span>
                </div>
            </div>
            <button onclick="closeDetailModal()"
                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center flex-shrink-0">
                <i class="fa fa-xmark text-sm"></i>
            </button>
        </div>
        {{-- Scrollable body --}}
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4" id="d_body">
            {{-- Loading --}}
            <div id="d_loading" class="flex items-center justify-center py-12 text-gray-400">
                <i class="fa fa-spinner fa-spin text-xl mr-2"></i> Memuat data...
            </div>
            {{-- Content (diisi JS) --}}
            <div id="d_content" class="hidden space-y-4"></div>
        </div>
        <div class="px-6 pb-4 flex-shrink-0">
            <button onclick="closeDetailModal()" class="w-full text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2 hover:bg-gray-50 transition-colors">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL: HAPUS --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl"><i class="fa fa-triangle-exclamation"></i></div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Pembayaran?</h2>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">Kamu akan menghapus <strong id="deleteName" class="text-gray-700"></strong>. Tindakan ini tidak dapat dibatalkan.</p>
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

{{-- POPUP ALERT --}}
@if(session('success') || session('error') || session('info') || session('warning') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @elseif(session('info'))
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-600 text-xl"><i class="fa fa-info-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Info</p><p class="text-xs text-gray-500 mt-0.5">{{ session('info') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
            <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4 space-y-0.5">
                @if(session('error'))<li>{{ session('error') }}</li>@endif
                @if(session('warning'))<li>{{ session('warning') }}</li>@endif
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg flex-shrink-0"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
</style>

@php
    $allPendingJson = $data->getCollection()
        ->where('status','Pending')
        ->map(function($d) {
            return [
                'id'     => $d->id,
                'no_pr'  => $d->no_pr,
                'jenis'  => $d->source_type
                    ? ($d->source_type_name ?? $d->source_type)
                    : 'Belanja',
            ];
        })
        ->values();
@endphp

{{-- MODAL: AJUKAN ULANG SERVICE ASURANSI (dari halaman Pembayaran) --}}
<div id="modalAjukanUlangSA" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-base font-bold text-gray-800">Ajukan Ulang — Service Asuransi</h3>
                <p class="text-sm text-gray-500 mt-0.5">PR: <span id="saAjukanPoNumber" class="font-mono font-semibold text-amber-600"></span></p>
            </div>
            <button onclick="closeAjukanUlangSAModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>

        {{-- Loading --}}
        <div id="saAjukanLoading" class="flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
                <p class="text-sm">Memuat data...</p>
            </div>
        </div>

        {{-- Body --}}
        <div id="saAjukanBody" class="hidden flex-1 overflow-y-auto flex flex-col">
            <div class="px-6 pt-4 pb-2 space-y-2">
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm">
                    <p class="font-semibold text-gray-800" id="saAjukanKendaraan">-</p>
                    <p class="text-xs text-gray-500 mt-0.5" id="saAjukanNamaAsuransi"></p>
                </div>
                <div id="saAjukanCatatan" class="hidden bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-xs text-red-700"></div>
            </div>

            <form id="saAjukanForm" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto px-6 pb-4 space-y-4">
                @csrf
                <input type="hidden" name="kendaraan_id"    id="saAjukanKendaraanId">
                <input type="hidden" name="nama_asuransi"   id="saAjukanNamaAsuransiHidden">
                <input type="hidden" name="tanggal_service" id="saAjukanTglService">
                <input type="hidden" name="periode_mulai"   id="saAjukanPeriodeMulai">
                <input type="hidden" name="periode_selesai" id="saAjukanPeriodeSelesai">
                <input type="hidden" name="kilometer"       id="saAjukanKilometer">

                {{-- Total biaya auto-sum --}}
                <div class="flex items-center gap-3 px-3 py-2 bg-blue-50 border border-blue-100 rounded-xl text-xs text-gray-600">
                    Total Biaya (auto-sum dari kejadian):
                    <span id="saTotalBiaya" class="font-bold text-blue-700 ml-1">Rp 0</span>
                </div>

                {{-- Kejadian container --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-gray-600">Daftar Kejadian</label>
                        <button type="button" onclick="addSaKejadian()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fa fa-plus text-xs"></i> Tambah Kejadian
                        </button>
                    </div>
                    <div id="saAjukanKejadianContainer" class="space-y-3"></div>
                </div>
            </form>

            <div class="border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
                <button type="button" onclick="closeAjukanUlangSAModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="saAjukanSubmitBtn" onclick="submitAjukanUlangSA()"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-rotate-right"></i> Ajukan Ulang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL: EDIT & AJUKAN ULANG ITEM DITOLAK
     Digunakan untuk: service_part (Ditolak / Disetujui+rejected),
                      service_incident (Disetujui+rejected),
                      service_asuransi (Disetujui+rejected),
                      gps / gps_perpanjang (Disetujui+rejected)
============================================================ --}}
<div id="resubmitRejectedModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
     onclick="if(event.target===this) closeResubmitRejectedModal()">

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 flex flex-col max-h-[90vh]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center">
                        <i class="fa fa-rotate-right text-amber-600 text-sm"></i>
                    </span>
                    Edit &amp; Ajukan Ulang Item Ditolak
                </h2>
                <p class="text-xs text-gray-400 mt-0.5 ml-10">
                    No PR: <span id="rrm-no-pr" class="font-mono font-semibold text-gray-600">—</span>
                </p>
            </div>
            <button type="button" onclick="closeResubmitRejectedModal()"
                class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>

        {{-- Loading state --}}
        <div id="rrm-loading" class="flex-1 flex items-center justify-center py-16">
            <div class="text-center text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl mb-3 block text-blue-400"></i>
                <p class="text-sm">Memuat data item...</p>
            </div>
        </div>

        {{-- Error state --}}
        <div id="rrm-error" class="hidden flex-1 flex items-center justify-center py-16 px-6">
            <div class="text-center text-red-500">
                <i class="fa fa-exclamation-triangle text-2xl mb-3 block"></i>
                <p class="text-sm" id="rrm-error-msg">Gagal memuat data.</p>
            </div>
        </div>

        {{-- Body + Footer (setelah data dimuat) --}}
        <form id="rrm-form" method="POST" action="" class="hidden flex-1 flex flex-col overflow-hidden">
            @csrf

            {{-- Catatan penolakan PR-level --}}
            <div id="rrm-catatan-wrap" class="hidden px-6 pt-4 flex-shrink-0">
                <div class="flex items-start gap-2 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                    <i class="fa fa-exclamation-circle text-red-500 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-semibold text-red-700 mb-0.5">Catatan Penolakan Admin</p>
                        <p id="rrm-catatan-pr" class="text-xs text-red-600"></p>
                    </div>
                </div>
            </div>

            {{-- Items list --}}
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4" id="rrm-items-container">
                {{-- Diisi via JS --}}
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex-shrink-0 rounded-b-2xl">
                <button type="button" onclick="closeResubmitRejectedModal()"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" id="rrm-submit-btn"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-colors shadow-sm">
                    <i class="fa fa-paper-plane text-xs"></i>
                    Ajukan Ulang
                </button>
            </div>
        </form>

    </div>
</div>

{{-- Supplier data untuk JS dropdown --}}
<script>
window.__rrmSuppliers = @json($suppliers->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama_supplier]));
</script>

@push('scripts')
<script>
// ── DATA PENDING (untuk modal bulk) ──────────────────────────
const ALL_PENDING = {!! json_encode($allPendingJson) !!};

// ── ACCORDION GROUP ───────────────────────────────────────────
function toggleGroup(idx) {
    const body    = document.getElementById('grp-body-' + idx);
    const chevron = document.getElementById('grp-chevron-' + idx);
    if (!body) return;
    const hidden = body.classList.contains('hidden');
    body.classList.toggle('hidden', !hidden);
    chevron.style.transform = hidden ? 'rotate(90deg)' : '';
    chevron.classList.toggle('text-blue-500', hidden);
    chevron.classList.toggle('text-gray-400', !hidden);
}

// ── ROW EXPAND ────────────────────────────────────────────────
function toggleRowExpand(uid) {
    const row     = document.getElementById('rowexpand-' + uid);
    const chevron = document.getElementById('rowchv-' + uid);
    if (!row) return;
    const hidden = row.classList.contains('hidden');
    row.classList.toggle('hidden', !hidden);
    if (chevron) {
        chevron.style.transform = hidden ? 'rotate(90deg)' : '';
        chevron.classList.toggle('text-blue-500', hidden);
        chevron.classList.toggle('text-gray-300', !hidden);
    }
}

// ── CHECKBOX GROUP SELECT-ALL ─────────────────────────────────
function toggleGroupCheck(masterCb, gIdx) {
    document.querySelectorAll('.grp-' + gIdx + '-item').forEach(cb => {
        cb.checked = masterCb.checked;
    });
    updateApproveCount();
    updateRejectCount();
}

// ── BULK APPROVE MODAL ────────────────────────────────────────
let bulkApproveItems = []; // [{id, no_pr, jenis}]

function openBulkApproveModal(jenis, ids) {
    // jenis & ids optional — jika tidak ada, tampilkan semua pending
    if (ids && ids.length > 0) {
        bulkApproveItems = ALL_PENDING.filter(i => ids.includes(i.id));
        document.getElementById('bulkApproveSubtitle').textContent =
            jenis ? 'Jenis: ' + jenis + ' — ' + bulkApproveItems.length + ' pengajuan' : bulkApproveItems.length + ' pengajuan';
    } else {
        bulkApproveItems = [...ALL_PENDING];
        document.getElementById('bulkApproveSubtitle').textContent = 'Semua ' + bulkApproveItems.length + ' pengajuan pending';
    }

    renderApproveItemList();
    document.getElementById('approveSelectAll').checked = false;
    document.getElementById('approveCatatan').value = '';
    updateApproveCount();

    const m = document.getElementById('modalBulkApprove');
    m.classList.remove('hidden'); m.classList.add('flex');
}

function closeBulkApproveModal() {
    const m = document.getElementById('modalBulkApprove');
    m.classList.add('hidden'); m.classList.remove('flex');
}

function renderApproveItemList() {
    const list = document.getElementById('approveItemList');
    list.innerHTML = '';
    bulkApproveItems.forEach(item => {
        const div = document.createElement('div');
        div.className = 'border border-gray-200 rounded-xl overflow-hidden';
        div.innerHTML = `
            <div class="flex items-center gap-3 px-3 py-2.5 bg-gray-50 border-b border-gray-100">
                <input type="checkbox" class="approve-item-cb rounded text-green-600 cursor-pointer flex-shrink-0"
                    value="${item.id}" onchange="updateApproveCount()">
                <span class="font-mono text-xs font-semibold text-gray-700 flex-1">${item.no_pr}</span>
                <span class="text-[11px] text-gray-400">${item.jenis}</span>
            </div>
            <div class="px-3 py-2.5">
                <label class="block text-[11px] font-semibold text-gray-500 mb-1">
                    Bukti Pembayaran <span class="text-red-400">*</span>
                    <span class="text-gray-400 font-normal">(jpg/png/pdf maks 5MB)</span>
                </label>
                <input type="file" id="bukti-approve-${item.id}"
                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip"
                    class="approve-bukti-input w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs text-gray-600
                        file:mr-2 file:py-1 file:px-2 file:rounded file:border-0
                        file:text-[11px] file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                    data-id="${item.id}">
            </div>`;
        list.appendChild(div);
    });
}

function toggleApproveAll(masterCb) {
    document.querySelectorAll('.approve-item-cb').forEach(cb => cb.checked = masterCb.checked);
    updateApproveCount();
}

function updateApproveCount() {
    const count = document.querySelectorAll('.approve-item-cb:checked').length;
    document.getElementById('approveSelectedCount').textContent = count + ' dipilih';
    const all = document.querySelectorAll('.approve-item-cb').length;
    document.getElementById('approveSelectAll').checked = count > 0 && count === all;
    document.getElementById('approveSelectAll').indeterminate = count > 0 && count < all;
}

function submitBulkApprove() {
    const checkedCbs = [...document.querySelectorAll('.approve-item-cb:checked')];
    if (checkedCbs.length === 0) { alert('Pilih minimal 1 item untuk disetujui.'); return; }

    // Validasi bukti per item
    let missing = false;
    checkedCbs.forEach(cb => {
        const fileInput = document.getElementById('bukti-approve-' + cb.value);
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            missing = true;
            if (fileInput) fileInput.classList.add('border-red-400', 'bg-red-50');
        } else {
            if (fileInput) fileInput.classList.remove('border-red-400', 'bg-red-50');
        }
    });
    if (missing) { alert('Bukti pembayaran wajib diupload untuk setiap item yang dipilih.'); return; }
    if (!confirm('Setujui ' + checkedCbs.length + ' pembayaran?')) return;

    const catatan = document.getElementById('approveCatatan').value;
    const ids     = checkedCbs.map(cb => cb.value);

    // Kirim semua sekaligus: FormData dengan bukti per item sebagai bukti_{id}
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    ids.forEach(id => formData.append('ids[]', id));
    formData.append('catatan', catatan);

    // Tambahkan file per item dengan key bukti_{id}[]
    checkedCbs.forEach(cb => {
        const fileInput = document.getElementById('bukti-approve-' + cb.value);
        if (fileInput && fileInput.files.length > 0) {
            [...fileInput.files].forEach(f => formData.append('bukti_per_item[' + cb.value + '][]', f));
        }
    });

    // Fallback: juga kirim sebagai bukti[] untuk endpoint lama
    checkedCbs.forEach(cb => {
        const fileInput = document.getElementById('bukti-approve-' + cb.value);
        if (fileInput && fileInput.files.length > 0) {
            [...fileInput.files].forEach(f => formData.append('bukti[]', f));
        }
    });

    fetch('{{ route("pembayaran.bulk-approve") }}', { method: 'POST', body: formData })
        .then(r => { if (r.redirected) { window.location.href = r.url; } else { window.location.reload(); } })
        .catch(() => window.location.reload());
}

// ── BULK REJECT MODAL ─────────────────────────────────────────
let bulkRejectItems = [];

function openBulkRejectModal(jenis, ids) {
    if (ids && ids.length > 0) {
        bulkRejectItems = ALL_PENDING.filter(i => ids.includes(i.id));
        document.getElementById('bulkRejectSubtitle').textContent =
            jenis ? 'Jenis: ' + jenis + ' — ' + bulkRejectItems.length + ' pengajuan' : bulkRejectItems.length + ' pengajuan';
    } else {
        bulkRejectItems = [...ALL_PENDING];
        document.getElementById('bulkRejectSubtitle').textContent = 'Semua ' + bulkRejectItems.length + ' pengajuan pending';
    }

    renderRejectItemList();
    document.getElementById('rejectSelectAll').checked = false;
    document.getElementById('rejectAllAlasan').value = '';
    updateRejectCount();

    const m = document.getElementById('modalBulkReject');
    m.classList.remove('hidden'); m.classList.add('flex');
}

function closeBulkRejectModal() {
    const m = document.getElementById('modalBulkReject');
    m.classList.add('hidden'); m.classList.remove('flex');
}

function renderRejectItemList() {
    const list = document.getElementById('rejectItemList');
    list.innerHTML = '';
    bulkRejectItems.forEach(item => {
        const div = document.createElement('div');
        div.className = 'border border-gray-200 rounded-xl overflow-hidden';
        div.innerHTML = `
            <div class="flex items-center gap-3 px-3 py-2.5 bg-gray-50">
                <input type="checkbox" class="reject-item-cb rounded text-red-500 cursor-pointer flex-shrink-0"
                    value="${item.id}" data-idx="${item.id}" onchange="updateRejectCount()">
                <span class="font-mono text-xs font-semibold text-gray-700 flex-1">${item.no_pr}</span>
                <span class="text-[11px] text-gray-400">${item.jenis}</span>
            </div>
            <div class="px-3 pb-2.5 pt-1.5">
                <textarea class="reject-alasan-input w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs resize-none focus:outline-none focus:ring-1 focus:ring-red-200"
                    rows="2" placeholder="Alasan penolakan untuk item ini *"
                    data-id="${item.id}"></textarea>
            </div>`;
        list.appendChild(div);
    });
}

function toggleRejectAll(masterCb) {
    document.querySelectorAll('.reject-item-cb').forEach(cb => cb.checked = masterCb.checked);
    updateRejectCount();
}

function syncRejectAllAlasan(val) {
    document.querySelectorAll('.reject-alasan-input').forEach(ta => ta.value = val);
}

function updateRejectCount() {
    const count = document.querySelectorAll('.reject-item-cb:checked').length;
    document.getElementById('rejectSelectedCount').textContent = count + ' dipilih';
    const all = document.querySelectorAll('.reject-item-cb').length;
    document.getElementById('rejectSelectAll').checked = count > 0 && count === all;
    document.getElementById('rejectSelectAll').indeterminate = count > 0 && count < all;
}

function submitBulkReject() {
    const checkedCbs = [...document.querySelectorAll('.reject-item-cb:checked')];
    if (checkedCbs.length === 0) { alert('Pilih minimal 1 item untuk ditolak.'); return; }

    // Validasi alasan per item
    let missing = false;
    checkedCbs.forEach(cb => {
        const ta = document.querySelector('.reject-alasan-input[data-id="' + cb.value + '"]');
        if (!ta || !ta.value.trim()) { missing = true; ta && ta.classList.add('border-red-400'); }
        else { ta && ta.classList.remove('border-red-400'); }
    });
    if (missing) { alert('Alasan penolakan wajib diisi untuk setiap item yang dipilih.'); return; }
    if (!confirm('Tolak ' + checkedCbs.length + ' pembayaran?')) return;

    // Kirim satu per satu menggunakan fetch (setiap item mungkin punya alasan beda)
    // Kita pakai endpoint bulk-reject dengan catatan global (ambil dari item pertama atau alasan global)
    const globalAlasan = document.getElementById('rejectAllAlasan').value.trim();
    const firstAlasan  = document.querySelector('.reject-alasan-input[data-id="' + checkedCbs[0].value + '"]')?.value.trim() || globalAlasan;
    const catatan = globalAlasan || firstAlasan;

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    checkedCbs.forEach(cb => formData.append('ids[]', cb.value));
    formData.append('catatan', catatan || '-');

    fetch('{{ route("pembayaran.bulk-reject") }}', { method: 'POST', body: formData })
        .then(r => { if (r.redirected) { window.location.href = r.url; } else { window.location.reload(); } })
        .catch(() => window.location.reload());
}

// ── SINGLE APPROVE (pengeluaran kendaraan) ────────────────────
function openSingleApproveModal(id, noPr) {
    const form = document.getElementById('formSingleApprove');
    form.action = '/admin/pembayaran/' + id + '/approve';
    document.getElementById('singleApproveSubtitle').textContent = 'No PR: ' + noPr;
    form.reset();
    const m = document.getElementById('modalSingleApprove');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeSingleApproveModal() {
    const m = document.getElementById('modalSingleApprove');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('modalSingleApprove')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeSingleApproveModal(); });

// ── SINGLE REJECT ─────────────────────────────────────────────
function openSingleRejectModal(id, noPr) {
    const form = document.getElementById('formSingleReject');
    form.action = '/admin/pembayaran/' + id + '/reject';
    document.getElementById('singleRejectSubtitle').textContent = 'No PR: ' + noPr;
    form.reset();
    const m = document.getElementById('modalSingleReject');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeSingleRejectModal() {
    const m = document.getElementById('modalSingleReject');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('modalSingleReject')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeSingleRejectModal(); });

// ── APPROVE SERVICE ───────────────────────────────────────────
function openApproveServiceModal(id, noPr) {
    const form = document.getElementById('approveServiceForm');
    form.action = '/admin/pembayaran/' + id + '/approve-service';
    document.getElementById('approveServiceSubtitle').textContent = 'No PR: ' + noPr;
    form.reset();
    const m = document.getElementById('approveServiceModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeApproveServiceModal() {
    const m = document.getElementById('approveServiceModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('approveServiceModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeApproveServiceModal(); });

// ── TOLAK MODAL ───────────────────────────────────────────────
function openTolakModal(id, noPr) {
    document.getElementById('tolakForm').action = '/admin/pembayaran/' + id + '/status';
    document.getElementById('tolakSubtitle').textContent = 'No PR: ' + noPr;
    document.getElementById('catatanTolak').value = '';
    const m = document.getElementById('tolakModal');
    m.classList.remove('hidden'); m.classList.add('flex');
    setTimeout(() => document.getElementById('catatanTolak').focus(), 100);
}
function closeTolakModal() {
    const m = document.getElementById('tolakModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('tolakModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeTolakModal(); });

// ── BULK MODAL: close on backdrop ─────────────────────────────
document.getElementById('modalBulkApprove')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeBulkApproveModal(); });
document.getElementById('modalBulkReject')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeBulkRejectModal(); });

// ── DETAIL MODAL ──────────────────────────────────────────────
function openDetailModal(id, currentTab) {
    const modal = document.getElementById('detailModal');
    modal.classList.remove('hidden'); modal.classList.add('flex');
    document.getElementById('d_no_pr').textContent = '';
    document.getElementById('d_jenis_badge').textContent = '';
    document.getElementById('d_loading').classList.remove('hidden');
    document.getElementById('d_content').classList.add('hidden');
    document.getElementById('d_content').innerHTML = '';

    fetch('/admin/pembayaran/' + id + '/details')
        .then(r => r.json())
        .then(data => {
            if (data.success) populateDetailModal(data.pembayaran, currentTab || 'semua');
            else closeDetailModal();
        })
        .catch(() => closeDetailModal());
}

function populateDetailModal(pr, activeTab) {
    activeTab = activeTab || 'semua';
    document.getElementById('d_no_pr').textContent = pr.no_pr || '-';
    document.getElementById('d_jenis_badge').textContent = pr.source_type_name || 'Belanja';
    document.getElementById('d_loading').classList.add('hidden');

    const content = document.getElementById('d_content');
    content.classList.remove('hidden');

    // ── Helper: file chip ────────────────────────────────────
    function fileChip(url, name, colorClass) {
        colorClass = colorClass || 'bg-blue-50 border-blue-200 text-blue-700';
        if (!url) return '';
        const ext = (name || '').split('.').pop().toLowerCase();
        const isImg = ['jpg','jpeg','png','gif','webp'].includes(ext);
        const icon = isImg ? 'fa-image' : (ext === 'pdf' ? 'fa-file-pdf' : 'fa-paperclip');
        return '<a href="' + url + '" target="_blank" title="' + name + '"'
            + ' class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-[11px] font-medium hover:opacity-80 transition ' + colorClass + ' max-w-[180px]">'
            + '<i class="fa ' + icon + ' text-[10px] flex-shrink-0"></i>'
            + '<span class="truncate">' + (name || 'file') + '</span></a>';
    }

    // ── Helper: info row ──────────────────────────────────────
    function infoCell(label, value) {
        if (!value) return '';
        return '<div class="bg-gray-50 rounded-xl px-3 py-2.5">'
            + '<p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">' + label + '</p>'
            + '<p class="text-sm font-medium text-gray-700">' + value + '</p>'
            + '</div>';
    }

    // ── Filter items berdasarkan activeTab + item_decisions ───
    function filterItemsByTab(items) {
        if (!items || !items.length) return items || [];
        // Cek apakah ada item yang punya status_item (dari item_decisions)
        const hasDecisions = items.some(function(it) { return it.status_item !== null && it.status_item !== undefined; });
        if (!hasDecisions) return items; // belum ada keputusan, tampilkan semua
        if (activeTab === 'Disetujui') {
            return items.filter(function(it) { return it.status_item === 'approved'; });
        }
        if (activeTab === 'Ditolak') {
            return items.filter(function(it) { return it.status_item === 'rejected'; });
        }
        if (activeTab === 'Diajukan' || activeTab === 'Pending') {
            // PR partial (ada item_decisions) yang muncul di tab Diajukan/Pending:
            // hanya tampilkan item yang BELUM diputuskan (status_item null/undefined).
            // Item yang sudah approved tidak boleh muncul di sini karena PR-nya masih
            // berstatus Diajukan (menunggu keputusan untuk item-item pending).
            return items.filter(function(it) { return it.status_item === null || it.status_item === undefined; });
        }
        return items; // tab 'semua' atau nilai lain → tampilkan semua
    }

    let html = '';

    // ── 1. Info utama PR ─────────────────────────────────────
    html += '<div class="grid grid-cols-2 sm:grid-cols-4 gap-3">'
        + infoCell('Tanggal', pr.tanggal_formatted)
        + infoCell('Pemohon', pr.pemohon_nama ? '<span class="font-semibold text-gray-800">' + pr.pemohon_nama + '</span><br><span class="text-[11px] text-gray-400">' + pr.pemohon + '</span>' : (pr.pemohon || '-'))
        + infoCell('Departemen', pr.departemen)
        + infoCell('Vendor', pr.vendor)
        + '</div>';
    // Tanggal ajukan sebagai baris terpisah (hanya jika ada)
    if (pr.terakhir_diajukan_formatted) {
        html += '<div class="grid grid-cols-1">' + infoCell('Tanggal Diajukan', pr.terakhir_diajukan_formatted) + '</div>';
    }

    // ── 2. Info Kendaraan & Service (jika ada) ────────────────
    if (pr.kendaraan || pr.service_info) {
        const k = pr.kendaraan || {};
        const s = pr.service_info || {};
        const isAsuransi = pr.source_type === 'service_asuransi';
        const color = isAsuransi ? 'blue' : 'orange';
        html += '<div class="bg-' + color + '-50/50 border border-' + color + '-100 rounded-xl px-4 py-3">'
            + '<p class="text-[10px] font-bold text-' + color + '-600 uppercase tracking-wide mb-2">'
            + '<i class="fa fa-car mr-1"></i> Kendaraan & Service</p>'
            + '<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">';
        if (k.label)             html += '<div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Kendaraan</p><p class="font-semibold text-gray-800">' + k.label + '</p></div>';
        if (s.tanggal_service)   html += '<div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Tgl Service</p><p class="text-gray-700">' + s.tanggal_service + '</p></div>';
        if (s.kilometer)         html += '<div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Kilometer</p><p class="text-gray-700">' + s.kilometer + ' km</p></div>';
        if (s.keluhan)           html += '<div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Keluhan</p><p class="text-gray-700">' + s.keluhan + '</p></div>';
        if (s.nama_asuransi)     html += '<div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Asuransi</p><p class="text-gray-700">' + s.nama_asuransi + '</p></div>';
        if (s.keterangan)        html += '<div class="col-span-2"><p class="text-[10px] text-gray-400 uppercase mb-0.5">Keterangan</p><p class="text-gray-700">' + s.keterangan + '</p></div>';
        html += '</div></div>';
    }

    // ── 3. Alasan & Keterangan ────────────────────────────────
    if (pr.alasan_permintaan && pr.alasan_permintaan !== '-') {
        html += '<div class="bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">'
            + '<p class="text-[10px] text-blue-500 font-semibold uppercase tracking-wide mb-1">Alasan Permintaan</p>'
            + '<p class="text-sm text-blue-800">' + pr.alasan_permintaan + '</p></div>';
    }

    // ── 4. Items / Daftar Part ────────────────────────────────
    const allItems = pr.items || [];
    const items = filterItemsByTab(allItems);
    const isTabRejected = activeTab === 'Ditolak';
    if (items.length > 0) {
        // Hitung total dari items yang ditampilkan
        const visibleTotal = items.reduce(function(s, it) {
            return s + (parseInt((it.subtotal || '').toString().replace(/\D/g,'')) || 0);
        }, 0);
        const totalDisplay = visibleTotal > 0
            ? visibleTotal.toLocaleString('id-ID')
            : pr.total_nominal_formatted;
        html += '<div class="border border-gray-100 rounded-xl overflow-hidden">'
            + '<div class="bg-gray-50 px-4 py-2 border-b border-gray-100 flex items-center justify-between">'
            + '<p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">Detail Items (' + items.length + ')</p>'
            + '<p class="text-sm font-bold ' + (isTabRejected ? 'text-red-500' : 'text-emerald-600') + '">Total: Rp ' + totalDisplay + '</p>'
            + '</div>';

        items.forEach(function(item, idx) {
            const hasBank = item.nama_bank || item.no_rekening;
            const lampiranArr = item.lampiran || [];
            const bukti = item.bukti || null;
            const hasFiles = lampiranArr.length > 0 || bukti;

            html += '<div class="px-4 py-3' + (idx > 0 ? ' border-t border-gray-100' : '') + '">'
                // Row atas: nama + harga
                + '<div class="flex items-start justify-between gap-2 mb-2">'
                + '<div class="flex-1 min-w-0">'
                + '<div class="flex items-center gap-1.5 flex-wrap">';
            if (item.kategori) html += '<span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700">' + item.kategori + '</span>';
            html += '<span class="text-sm font-semibold text-gray-800">' + (item.nama_barang || '-') + '</span>';
            if (item.part_number) html += '<span class="font-mono text-gray-400 text-[10px]">(' + item.part_number + ')</span>';
            if (item.kondisi)     html += '<span class="text-[10px] text-gray-400">· ' + item.kondisi + '</span>';
            html += '</div>'
                + (item.keterangan ? '<p class="text-[11px] text-gray-400 mt-0.5">' + item.keterangan + '</p>' : '')
                + '</div>'
                + '<span class="text-sm font-bold text-emerald-600 flex-shrink-0">Rp ' + (item.subtotal_formatted || '0') + '</span>'
                + '</div>';

            // ── Limit Snapshot (Service grid) ──────────────────────
            const snap = item.limit_snapshot || null;
            if (snap) {
                html += '<div class="mt-1.5 mb-2 flex flex-col gap-0.5 text-[11px]">'
                    + '<span class="' + (snap.biaya_lewat ? 'text-red-600 font-bold' : 'text-gray-700') + '">Rp ' + Number(snap.service_biaya || 0).toLocaleString('id-ID') + '</span>'
                    + '<span class="' + (snap.tanggal_lewat ? 'text-red-600 font-bold' : 'text-gray-700') + '">' + (snap.service_tanggal || '—') + '</span>'
                    + '<span class="' + (snap.km_lewat ? 'text-red-600 font-bold' : 'text-gray-700') + '">KM ' + Number(snap.service_km || 0).toLocaleString('id-ID') + '</span>'
                    + '</div>';
            }

            // ── Keterangan Limit badges ──────────────────────────────
            const ketLimitStr = item.keterangan_limit || null;
            if (ketLimitStr && ketLimitStr !== '-') {
                const badges = ketLimitStr.split(',').map(function(s){ return s.trim(); }).filter(Boolean);
                if (badges.length > 0) {
                    html += '<div class="flex flex-wrap gap-1 mb-2">';
                    badges.forEach(function(badge) {
                        const bl = badge.toLowerCase();
                        let bc = 'bg-green-100 text-green-700';
                        if (bl.includes('melebihi')) {
                            bc = 'bg-red-100 text-red-700';
                        } else if (!bl.includes('belum') && bl.includes('sudah mencapai')) {
                            bc = 'bg-yellow-100 text-yellow-700';
                        }
                        html += '<span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-medium ' + bc + '">' + badge.charAt(0).toUpperCase() + badge.slice(1) + '</span>';
                    });
                    html += '</div>';
                }
            }

            // Info Supplier per item (jika ada)
            if (item.supplier) {
                html += '<div class="flex items-center gap-1.5 text-[11px] text-gray-600 font-medium mb-1.5 px-1">'
                    + '<i class="fa fa-store text-[9px] text-indigo-400"></i>' + item.supplier
                    + '</div>';
            }

            // Info Bank per item
            if (hasBank) {
                html += '<div class="flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-gray-500 mb-2 px-1">'
                    + '<span class="flex items-center gap-1"><i class="fa fa-building text-[9px] text-amber-500"></i>'
                    + (item.nama_bank || '-') + '</span>'
                    + '<span class="flex items-center gap-1"><i class="fa fa-credit-card text-[9px] text-amber-500"></i>'
                    + (item.no_rekening || '-') + '</span>'
                    + (item.nama_rekening ? '<span class="flex items-center gap-1"><i class="fa fa-user text-[9px] text-amber-500"></i>' + item.nama_rekening + '</span>' : '')
                    + '</div>';
            }

            // Lampiran + Bukti per item
            if (hasFiles) {
                html += '<div class="flex flex-wrap gap-1.5">';
                if (lampiranArr.length > 0) {
                    lampiranArr.forEach(function(f) {
                        html += fileChip(f.url, f.name, 'bg-blue-50 border-blue-200 text-blue-700');
                    });
                }
                if (bukti) {
                    html += fileChip(bukti.url, bukti.name, 'bg-emerald-50 border-emerald-200 text-emerald-700');
                }
                html += '</div>';
            }
            html += '</div>';
        });

        // Total row
        html += '<div class="border-t-2 border-gray-200 bg-gray-50 px-4 py-2.5 flex justify-end">'
            + '<span class="text-sm font-bold ' + (isTabRejected ? 'text-red-500' : 'text-emerald-600') + '">Rp ' + totalDisplay + '</span>'
            + '</div>';
        html += '</div>';

    } else if (pr.barang_jasa) {
        html += '<div class="border border-gray-100 rounded-xl px-4 py-3">'
            + '<p class="text-[10px] text-gray-400 mb-0.5 uppercase">Barang/Jasa</p>'
            + '<p class="text-sm font-medium text-gray-700">' + pr.barang_jasa + '</p>'
            + (pr.nominal_formatted ? '<p class="text-sm font-bold text-emerald-600 mt-1">Rp ' + pr.nominal_formatted + '</p>' : '')
            + '</div>';
    }

    // ── 5. Info Rekening bank level PR (untuk non service_part/incident) ─
    if (!pr.source_type || !['service_part','service_incident'].includes(pr.source_type)) {
        if (pr.nama_bank || pr.no_rekening) {
            html += '<div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">'
                + '<p class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider mb-2"><i class="bi bi-bank mr-1"></i> Rekening Bank</p>'
                + '<div class="flex flex-wrap gap-x-6 gap-y-1 text-sm">';
            if (pr.nama_bank)     html += '<div><span class="text-[10px] text-gray-400 uppercase block">Bank</span><span class="font-medium text-gray-700">' + pr.nama_bank + '</span></div>';
            if (pr.no_rekening)   html += '<div><span class="text-[10px] text-gray-400 uppercase block">No. Rekening</span><span class="font-mono font-medium text-gray-700">' + pr.no_rekening + '</span></div>';
            if (pr.nama_rekening) html += '<div><span class="text-[10px] text-gray-400 uppercase block">Atas Nama</span><span class="font-medium text-gray-700">' + pr.nama_rekening + '</span></div>';
            html += '</div></div>';
        }
    }

    // ── 6. Info Approval ──────────────────────────────────────
    const ap = pr.approval;
    if (ap && (ap.oleh || ap.tanggal || ap.catatan)) {
        const isApproved = ap.action === 'approved';
        const apColor = isApproved ? 'green' : 'red';
        const apIcon  = isApproved ? 'fa-check-circle' : 'fa-times-circle';
        const apLabel = isApproved ? 'Disetujui' : 'Ditolak';
        html += '<div class="bg-' + apColor + '-50 border border-' + apColor + '-200 rounded-xl px-4 py-3">'
            + '<p class="text-[10px] font-semibold text-' + apColor + '-600 uppercase tracking-wide mb-2">'
            + '<i class="fa ' + apIcon + ' mr-1"></i> Info Approval — ' + apLabel + '</p>'
            + '<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">';
        if (ap.oleh)    html += '<div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Oleh</p><p class="font-semibold text-gray-700">' + ap.oleh + '</p></div>';
        if (ap.tanggal) html += '<div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Tanggal</p><p class="text-gray-700">' + ap.tanggal + '</p></div>';
        if (ap.catatan) html += '<div class="col-span-2 sm:col-span-3"><p class="text-[10px] text-gray-400 uppercase mb-0.5">Catatan</p><p class="text-gray-700">' + ap.catatan + '</p></div>';
        html += '</div></div>';
    } else if (pr.catatan) {
        html += '<div class="bg-red-50 border border-red-100 rounded-xl px-4 py-2.5">'
            + '<p class="text-[10px] text-red-400 font-semibold uppercase tracking-wide mb-0.5">Catatan</p>'
            + '<p class="text-sm text-red-700">' + pr.catatan + '</p></div>';
    }

    content.innerHTML = html;
}

function closeDetailModal() {
    const m = document.getElementById('detailModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('detailModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeDetailModal(); });

// ── DELETE MODAL ──────────────────────────────────────────────
function triggerDelete(btn) {
    document.getElementById('deleteForm').action = btn.dataset.action;
    document.getElementById('deleteName').innerText = btn.dataset.name || 'ini';
    const m = document.getElementById('deleteModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeDeleteModal() {
    const m = document.getElementById('deleteModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('deleteModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeDeleteModal(); });

// ── POPUP ALERT ───────────────────────────────────────────────
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

// ── APPROVAL MODAL (component) ────────────────────────────────
function openApprovalModal(id) {
    window.dispatchEvent(new CustomEvent('open-approval-modal', { detail: { id } }));
}
function openRejectModal(id) {
    window.dispatchEvent(new CustomEvent('open-approval-modal', { detail: { id, action: 'reject' } }));
}

// ── CHARTS ────────────────────────────────────────────────────
const pembayaranChartManager = new ChartManager();
document.addEventListener('chartFilterChange', function(e) {
    if (e.detail.filterId === 'pembayaranChartFilter') {
        const filters = {
            filter_type:   e.detail.filterType,
            start_date:    e.detail.startDate,
            end_date:      e.detail.endDate,
            specific_year: e.detail.specificYear,
            departemen:    e.detail.categoryId ?? '',
        };
        if (!pembayaranChartManager.hasChart('pembayaranBarChart')) {
            initPembayaranCharts(filters);
        } else {
            updatePembayaranCharts(filters);
        }
    }
});
async function initPembayaranCharts(f) {
    try { await pembayaranChartManager.initChartsFromAPI('pembayaran', {pie:'pembayaranPieChart',bar:'pembayaranBarChart',line:'pembayaranLineChart'}, f); } catch(e) {}
}
async function updatePembayaranCharts(f) {
    try { await pembayaranChartManager.updateChartsFromAPI('pembayaran', {pie:'pembayaranPieChart',bar:'pembayaranBarChart',line:'pembayaranLineChart'}, f, {scrollable:f.filter_type==='custom'}); } catch(e) {}
}

// Prevent row click propagation from action buttons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('td button, td form, td a').forEach(el => {
        el.addEventListener('click', e => e.stopPropagation());
    });
});

// ── AJUKAN ULANG SERVICE ASURANSI MODAL (dari halaman Pembayaran) ──
let _saAjukanId = null;

function openAjukanUlangSAModal(saId, noPr) {
    _saAjukanId = saId;
    document.getElementById('saAjukanPoNumber').textContent = noPr;
    document.getElementById('saAjukanLoading').classList.remove('hidden');
    document.getElementById('saAjukanBody').classList.add('hidden');
    const m = document.getElementById('modalAjukanUlangSA');
    m.classList.remove('hidden'); m.classList.add('flex');

    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    fetch('/admin/service-asuransi/' + saId + '/ajukan-ulang', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) throw new Error(data.message || 'Gagal memuat data');
        renderSaAjukanForm(data);
        document.getElementById('saAjukanLoading').classList.add('hidden');
        document.getElementById('saAjukanBody').classList.remove('hidden');
    })
    .catch(function(err) {
        document.getElementById('saAjukanLoading').innerHTML =
            '<div class="text-center text-red-500 py-8 px-6"><i class="fa fa-exclamation-triangle text-xl mb-2 block"></i><p class="text-sm">' + err.message + '</p></div>';
    });
}

function renderSaAjukanForm(data) {
    document.getElementById('saAjukanKendaraan').textContent = data.kendaraan || '-';
    document.getElementById('saAjukanNamaAsuransi').textContent = (data.nama_asuransi || '') + (data.tanggal_service ? ' • ' + data.tanggal_service : '');
    const catatanEl = document.getElementById('saAjukanCatatan');
    if (data.catatan_penolakan) {
        catatanEl.textContent = 'Alasan penolakan: ' + data.catatan_penolakan;
        catatanEl.classList.remove('hidden');
    } else {
        catatanEl.classList.add('hidden');
    }

    // Set hidden fields
    document.getElementById('saAjukanKendaraanId').value   = data.kendaraan_id   || '';
    document.getElementById('saAjukanTglService').value    = data.tanggal_service || '';
    document.getElementById('saAjukanKilometer').value     = data.kilometer       || '';
    document.getElementById('saAjukanPeriodeMulai').value  = data.periode_mulai   || '';
    document.getElementById('saAjukanPeriodeSelesai').value= data.periode_selesai || '';
    document.getElementById('saAjukanNamaAsuransiHidden').value = data.nama_asuransi || '';

    // Render kejadian
    const container = document.getElementById('saAjukanKejadianContainer');
    container.innerHTML = '';
    (data.kejadians || []).forEach(function(kej, idx) {
        renderSaKejadian(container, idx, kej);
    });
    updateSaTotalBiaya();
}

let _saKejCount = 0;
function renderSaKejadian(container, idx, kej) {
    const div = document.createElement('div');
    div.id = 'sa-kej-' + idx;
    div.className = 'bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3';

    // Lampiran lama — tampil read-only, kirim via hidden inputs
    const lampiranLama = kej.lampiran_existing || [];

    // Hidden inputs agar lampiran lama ikut terkirim ke server
    const hiddenLampiranInputs = lampiranLama.map(function(lf, li) {
        const path = lf.path || '';
        const name = (lf.original_name || path.split('/').pop()).replace(/"/g, '&quot;');
        const ext  = lf.extension || path.split('.').pop();
        const size = lf.size || 0;
        if (!path) return '';
        return '<input type="hidden" name="kejadians[' + idx + '][lampiran_lama][' + li + '][path]"          value="' + path.replace(/"/g, '&quot;') + '">'
             + '<input type="hidden" name="kejadians[' + idx + '][lampiran_lama][' + li + '][original_name]" value="' + name + '">'
             + '<input type="hidden" name="kejadians[' + idx + '][lampiran_lama][' + li + '][extension]"     value="' + ext + '">'
             + '<input type="hidden" name="kejadians[' + idx + '][lampiran_lama][' + li + '][size]"          value="' + size + '">';
    }).join('');

    let lampiranLamaHtml = '';
    if (lampiranLama.length > 0) {
        lampiranLamaHtml = '<div class="flex flex-wrap gap-1.5">'
            + lampiranLama.map(function(lf) {
                const path = lf.path || '';
                const name = lf.original_name || path.split('/').pop();
                const ext  = (lf.extension || '').toLowerCase();
                const isImg = ['jpg','jpeg','png','webp'].includes(ext);
                const icon  = isImg ? 'fa-image text-blue-400' : (ext === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                const url   = path ? '/storage/' + path : null;
                if (!url) return '';
                return '<a href="' + url + '" target="_blank"'
                    + ' class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-white border border-gray-200 text-blue-600 hover:bg-blue-50"'
                    + ' title="' + name + '">'
                    + '<i class="fa ' + icon + ' text-[9px]"></i>'
                    + '<span class="truncate max-w-[160px]">' + name + '</span></a>';
            }).join('') + '</div>';
    } else {
        lampiranLamaHtml = '<p class="text-xs text-gray-400 italic">Tidak ada lampiran</p>';
    }

    div.innerHTML = `
        ${hiddenLampiranInputs}
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-gray-600">Kejadian #${idx + 1}</span>
            <button type="button" onclick="removeSaKejadian(${idx})"
                class="w-6 h-6 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center text-xs">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Kejadian <span class="text-red-400">*</span></label>
                <input type="text" name="kejadians[${idx}][nama_kejadian]" required
                    value="${(kej.nama_kejadian || '').replace(/"/g, '&quot;')}"
                    placeholder="cth: Ganti Kaca Depan"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Biaya (Rp)</label>
                <input type="number" name="kejadians[${idx}][biaya]" min="0" value="${kej.biaya || 0}"
                    onchange="updateSaTotalBiaya()" oninput="updateSaTotalBiaya()"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
            </div>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 mb-1.5">
                <i class="fa fa-paperclip text-gray-400 mr-1"></i>Lampiran
            </p>
            ${lampiranLamaHtml}
        </div>
    `;
    container.appendChild(div);
}

function removeSaKejadian(idx) {
    document.getElementById('sa-kej-' + idx)?.remove();
    updateSaTotalBiaya();
}

function addSaKejadian() {
    const container = document.getElementById('saAjukanKejadianContainer');
    const idx = container.children.length + (_saKejCount++);
    renderSaKejadian(container, idx, {});
}

function updateSaTotalBiaya() {
    let total = 0;
    document.querySelectorAll('#saAjukanKejadianContainer [name$="[biaya]"]').forEach(function(inp) {
        total += parseInt(inp.value || 0);
    });
    const el = document.getElementById('saTotalBiaya');
    if (el) el.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function closeAjukanUlangSAModal() {
    document.getElementById('modalAjukanUlangSA').classList.add('hidden');
    document.getElementById('modalAjukanUlangSA').classList.remove('flex');
    _saAjukanId = null;
    _saKejCount = 0;
}

async function submitAjukanUlangSA() {
    if (!_saAjukanId) return;
    const btn   = document.getElementById('saAjukanSubmitBtn');
    const form  = document.getElementById('saAjukanForm');
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';

    const formData = new FormData(form);
    formData.append('_token', token);

    try {
        const res    = await fetch('/admin/service-asuransi/' + _saAjukanId + '/ajukan-ulang-submit', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.success) {
            closeAjukanUlangSAModal();
            window.location.reload();
        } else {
            alert(result.message || 'Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-rotate-right"></i> Ajukan Ulang';
        }
    } catch (e) {
        alert('Terjadi kesalahan jaringan.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-rotate-right"></i> Ajukan Ulang';
    }
}

document.getElementById('modalAjukanUlangSA')?.addEventListener('click', function(e) {
    if (e.target === this) closeAjukanUlangSAModal();
});

// ── RESUBMIT REJECTED ITEMS MODAL ────────────────────────────
// Digunakan untuk: service_part (Ditolak / Disetujui+rejected),
//                 service_incident (Disetujui+rejected),
//                 service_asuransi (Disetujui+rejected),
//                 gps / gps_perpanjang (Disetujui+rejected)

let _rrmPembayaranId = null;

function openResubmitRejectedModal(pembayaranId) {
    _rrmPembayaranId = pembayaranId;

    // Reset state modal
    document.getElementById('rrm-no-pr').textContent = '—';
    document.getElementById('rrm-loading').classList.remove('hidden');
    document.getElementById('rrm-error').classList.add('hidden');
    document.getElementById('rrm-form').classList.add('hidden');
    document.getElementById('rrm-catatan-wrap').classList.add('hidden');
    document.getElementById('rrm-items-container').innerHTML = '';

    // Buka modal
    const modal = document.getElementById('resubmitRejectedModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Fetch data item rejected
    fetch('/admin/pembayaran/' + pembayaranId + '/rejected-items', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) throw new Error(data.message || 'Gagal memuat data.');

        // Set no_pr di header
        document.getElementById('rrm-no-pr').textContent = data.no_pr || '—';

        // Set form action
        const form = document.getElementById('rrm-form');
        form.action = '/admin/pembayaran/' + pembayaranId + '/resubmit-rejected-items';

        // Tampilkan catatan penolakan PR-level jika ada
        if (data.catatan_pr) {
            document.getElementById('rrm-catatan-pr').textContent = data.catatan_pr;
            document.getElementById('rrm-catatan-wrap').classList.remove('hidden');
        }

        // Render items
        const container = document.getElementById('rrm-items-container');
        const srcType   = data.source_type;
        const isServicePart     = srcType === 'service_part';
        const isServiceIncident = srcType === 'service_incident';
        const isGps             = srcType === 'gps' || srcType === 'gps_perpanjang';
        const hasBank           = isServicePart || isServiceIncident || isGps;

        data.items.forEach(function(item, i) {
            const div = document.createElement('div');
            div.className = 'border border-gray-200 rounded-xl overflow-hidden';

            // Item header
            let headerHtml = `
                <div class="flex items-center gap-2 px-4 py-3 bg-red-50/60 border-b border-red-100">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">
                        <i class="fa fa-times-circle text-[9px]"></i> Ditolak
                    </span>
                    <span class="text-sm font-semibold text-gray-800">${_escHtml(item.nama)}</span>
                </div>`;

            // Alasan tolak per item
            if (item.catatan_tolak) {
                headerHtml += `
                <div class="px-4 py-2 bg-yellow-50 border-b border-yellow-100 flex items-start gap-2">
                    <i class="fa fa-comment-dots text-yellow-500 text-xs mt-0.5 flex-shrink-0"></i>
                    <p class="text-xs text-yellow-700"><span class="font-semibold">Alasan:</span> ${_escHtml(item.catatan_tolak)}</p>
                </div>`;
            }

            // Form fields
            headerHtml += `<div class="px-4 py-3 grid grid-cols-1 gap-3">`;

            // Hidden idx
            headerHtml += `<input type="hidden" name="items[${i}][idx]" value="${item.idx}">`;

            // Biaya — service_part juga tampilkan supplier di kolom kanan
            if (isServicePart) {
                // Supplier dropdown di kolom kanan biaya
                let supplierOptions = '<option value="">— Pilih Supplier —</option>';
                (window.__rrmSuppliers || []).forEach(function(s) {
                    const sel = s.id == item.supplier_id ? 'selected' : '';
                    supplierOptions += `<option value="${s.id}" ${sel}>${_escHtml(s.nama)}</option>`;
                });
                headerHtml += `
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Biaya (Rp)</label>
                        <input type="number" name="items[${i}][biaya]" value="${item.biaya}"
                            min="0" required
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Supplier</label>
                        <select name="items[${i}][supplier_id]"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400">
                            ${supplierOptions}
                        </select>
                    </div>
                </div>`;
            } else {
                // service_incident / service_asuransi — biaya saja (full width)
                headerHtml += `
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Biaya (Rp)</label>
                    <input type="number" name="items[${i}][biaya]" value="${item.biaya}"
                        min="0" required
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400">
                </div>`;
            }

            // Bank / Rekening — service_part, service_incident, dan gps
            if (hasBank) {
                // GPS pakai field nama_pemilik; service_part/incident pakai nama_rekening
                const atasNamaField = isGps ? 'nama_pemilik' : 'nama_rekening';
                const atasNamaValue = isGps ? (item.nama_pemilik || '') : (item.nama_rekening || '');
                headerHtml += `
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama Bank</label>
                            <input type="text" name="items[${i}][nama_bank]" value="${_escAttr(item.nama_bank || '')}"
                                maxlength="100"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">No. Rekening</label>
                            <input type="text" name="items[${i}][no_rekening]" value="${_escAttr(item.no_rekening || '')}"
                                maxlength="50"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Atas Nama</label>
                            <input type="text" name="items[${i}][${atasNamaField}]" value="${_escAttr(atasNamaValue)}"
                                maxlength="150"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400">
                        </div>
                    </div>`;
            }

            // Keterangan — hanya untuk service_part (service_incident & service_asuransi tidak)
            if (isServicePart) {
                headerHtml += `
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Keterangan</label>
                    <textarea name="items[${i}][keterangan]" rows="2" maxlength="500"
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 resize-none">${_escHtml(item.keterangan || '')}</textarea>
                </div>`;
            }

            headerHtml += `</div>`; // end grid
            div.innerHTML = headerHtml;
            container.appendChild(div);
        });

        // Tampilkan form, sembunyikan loading
        document.getElementById('rrm-loading').classList.add('hidden');
        document.getElementById('rrm-form').classList.remove('hidden');
    })
    .catch(function(err) {
        document.getElementById('rrm-loading').classList.add('hidden');
        document.getElementById('rrm-error-msg').textContent = err.message || 'Terjadi kesalahan.';
        document.getElementById('rrm-error').classList.remove('hidden');
    });
}

function closeResubmitRejectedModal() {
    const modal = document.getElementById('resubmitRejectedModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    _rrmPembayaranId = null;
}

// Handle submit: disable tombol saat loading
document.getElementById('rrm-form')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('rrm-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin text-xs"></i> Mengajukan...';
});

// Helper escape HTML
function _escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
function _escAttr(str) {
    return String(str).replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
</script>
@endpush

{{-- ── MODAL PER-ITEM APPROVAL (service_part / service_incident / GPS / service_asuransi) ── --}}
<div id="modalPerItemApproval" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-base font-bold text-gray-800">Approve / Reject Items</h3>
                <p class="text-sm text-gray-500 mt-0.5">PR: <span id="pia-no-pr" class="font-mono font-semibold text-blue-600"></span></p>
            </div>
            <button onclick="closePerItemApprovalModal()" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center">
                <i class="fa fa-times text-sm"></i>
            </button>
        </div>

        {{-- Loading --}}
        <div id="pia-loading" class="flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
                <p class="text-sm">Memuat data items...</p>
            </div>
        </div>

        {{-- Content --}}
        <form id="pia-form" method="POST" enctype="multipart/form-data" class="hidden flex-1 overflow-y-auto flex flex-col">
            @csrf
            <div class="px-6 pt-4 pb-2">
                <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-2.5 text-xs text-blue-700 mb-3">
                    <i class="fa fa-info-circle mr-1"></i>
                    <span id="pia-kendaraan"></span>
                </div>
                <div id="pia-items-container" class="space-y-3"></div>
            </div>
            <div class="border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
                <button type="button" onclick="closePerItemApprovalModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">
                    Batal
                </button>
                <button type="button" id="pia-submit-btn" onclick="submitPerItemApproval()"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl py-2.5">
                    <i class="fa fa-check"></i> Konfirmasi Keputusan
                </button>
            </div>
        </form>

        <div id="pia-error" class="hidden px-6 py-8 text-center text-red-500 text-sm">
            <i class="fa fa-exclamation-triangle text-2xl mb-2 block"></i>
            <span id="pia-error-msg"></span>
        </div>
    </div>
</div>

<script>
let _piaPembayaranId = null;
let _piaItems = [];
let _piaDecisions = {}; // { idx: 'approved'|'rejected' }

function openApprovalModal(id) {
    _piaPembayaranId = id;
    _piaItems = [];
    _piaDecisions = {};

    document.getElementById('pia-no-pr').textContent = '—';
    document.getElementById('pia-loading').classList.remove('hidden');
    document.getElementById('pia-form').classList.add('hidden');
    document.getElementById('pia-error').classList.add('hidden');
    document.getElementById('pia-items-container').innerHTML = '';

    const m = document.getElementById('modalPerItemApproval');
    m.classList.remove('hidden'); m.classList.add('flex');

    fetch('/admin/pembayaran/' + id + '/approval-items', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) throw new Error(data.message || 'Gagal memuat data.');

        document.getElementById('pia-no-pr').textContent = data.no_pr || '—';
        document.getElementById('pia-kendaraan').textContent = data.kendaraan || '-';

        // Set form action
        document.getElementById('pia-form').action = '/admin/pembayaran/' + id + '/approve-items';

        _piaItems = data.items || [];
        const isServicePart = ['service_part', 'service_incident'].includes(data.source_type);
        const container = document.getElementById('pia-items-container');

        _piaItems.forEach(function(item, i) {
            const idx = item.idx;
            _piaDecisions[idx] = 'approved'; // default: semua approved

            const card = document.createElement('div');
            card.id = 'pia-card-' + idx;
            card.className = 'border border-green-200 rounded-xl overflow-hidden bg-green-50/20';

            card.innerHTML = `
                <input type="hidden" name="items[${i}][idx]" value="${idx}">
                <input type="hidden" id="pia-action-${idx}" name="items[${i}][action]" value="approved">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-semibold text-gray-800">${_escH(item.nama)}</span>
                            ${item.category ? '<span class="text-[10px] bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded font-semibold">' + _escH(item.category) + '</span>' : ''}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-3">
                            <span class="font-semibold text-emerald-600">Rp ${parseInt(item.biaya).toLocaleString('id-ID')}</span>
                            ${item.nama_bank ? '<span>' + _escH(item.nama_bank) + ' · ' + _escH(item.no_rekening || '-') + '</span>' : ''}
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0 ml-3">
                        <button type="button" onclick="setPiaDecision(${idx}, 'approved')"
                            id="pia-btn-approve-${idx}"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-green-600 text-white">
                            <i class="fa fa-check text-[10px]"></i> Approve
                        </button>
                        <button type="button" onclick="setPiaDecision(${idx}, 'rejected')"
                            id="pia-btn-reject-${idx}"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-600 border border-gray-200">
                            <i class="fa fa-times text-[10px]"></i> Reject
                        </button>
                    </div>
                </div>
                ${isServicePart ? `
                {{-- Bukti bayar upload (wajib jika approved) --}}
                <div id="pia-bukti-wrap-${idx}" class="px-4 pb-3 pt-1 border-t border-green-100 bg-green-50/40">
                    <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Bukti Pembayaran <span class="text-red-400">*</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer border border-dashed border-green-300 hover:border-green-400 bg-white rounded-lg px-3 py-2 transition-colors">
                        <i class="fa fa-paperclip text-green-500 text-sm"></i>
                        <span id="pia-bukti-label-${idx}" class="text-xs text-gray-500 flex-1">Klik untuk pilih file...</span>
                        <input type="file" name="items[${i}][bukti]" id="pia-bukti-input-${idx}"
                            accept="image/*,application/pdf" class="hidden"
                            onchange="updatePiaBuktiLabel(${idx})">
                    </label>
                </div>
                ` : ''}
                {{-- Alasan tolak (tampil saat rejected) --}}
                <div id="pia-reject-panel-${idx}" class="hidden px-4 pb-3 pt-1 border-t border-red-100 bg-red-50/30">
                    <label class="block text-[10px] font-semibold text-red-500 uppercase tracking-wide mb-1.5">
                        Alasan Penolakan <span class="text-red-400">*</span>
                    </label>
                    <textarea name="items[${i}][catatan]" id="pia-catatan-${idx}" rows="2"
                        placeholder="Tulis alasan penolakan..."
                        class="w-full text-xs px-3 py-2 border border-red-200 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-red-100 bg-white"></textarea>
                </div>`;

            container.appendChild(card);
        });

        document.getElementById('pia-loading').classList.add('hidden');
        document.getElementById('pia-form').classList.remove('hidden');
    })
    .catch(function(err) {
        document.getElementById('pia-loading').classList.add('hidden');
        document.getElementById('pia-error-msg').textContent = err.message || 'Terjadi kesalahan.';
        document.getElementById('pia-error').classList.remove('hidden');
    });
}

function setPiaDecision(idx, action) {
    _piaDecisions[idx] = action;
    const card = document.getElementById('pia-card-' + idx);
    const actionInput = document.getElementById('pia-action-' + idx);
    const btnApprove = document.getElementById('pia-btn-approve-' + idx);
    const btnReject = document.getElementById('pia-btn-reject-' + idx);
    const buktiWrap = document.getElementById('pia-bukti-wrap-' + idx);
    const rejectPanel = document.getElementById('pia-reject-panel-' + idx);

    if (actionInput) actionInput.value = action;

    if (action === 'approved') {
        card.className = 'border border-green-200 rounded-xl overflow-hidden bg-green-50/20';
        btnApprove.className = 'inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-green-600 text-white';
        btnReject.className = 'inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-600 border border-gray-200';
        if (buktiWrap) buktiWrap.classList.remove('hidden');
        if (rejectPanel) rejectPanel.classList.add('hidden');
    } else {
        card.className = 'border border-red-200 rounded-xl overflow-hidden bg-red-50/20';
        btnApprove.className = 'inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-500 hover:bg-green-100 hover:text-green-600 border border-gray-200';
        btnReject.className = 'inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-red-600 text-white';
        if (buktiWrap) buktiWrap.classList.add('hidden');
        if (rejectPanel) rejectPanel.classList.remove('hidden');
    }
}

function updatePiaBuktiLabel(idx) {
    const input = document.getElementById('pia-bukti-input-' + idx);
    const label = document.getElementById('pia-bukti-label-' + idx);
    if (input && input.files.length > 0) {
        label.textContent = '✓ ' + input.files[0].name;
        label.classList.add('text-green-700');
    }
}

async function submitPerItemApproval() {
    if (!_piaPembayaranId) return;

    // Validasi: approved harus ada bukti, rejected harus ada catatan
    let valid = true;
    for (const item of _piaItems) {
        const idx = item.idx;
        const action = _piaDecisions[idx];
        if (action === 'approved') {
            const buktiInput = document.getElementById('pia-bukti-input-' + idx);
            if (buktiInput && buktiInput.files.length === 0) {
                alert('Item "' + item.nama + '" disetujui tapi bukti pembayaran belum diupload.');
                valid = false; break;
            }
        } else {
            const catatanEl = document.getElementById('pia-catatan-' + idx);
            if (catatanEl && !catatanEl.value.trim()) {
                alert('Item "' + item.nama + '" ditolak tapi alasan penolakan belum diisi.');
                catatanEl.focus(); valid = false; break;
            }
        }
    }
    if (!valid) return;

    const btn = document.getElementById('pia-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';

    const form = document.getElementById('pia-form');
    const formData = new FormData(form);

    try {
        const res = await fetch('/admin/pembayaran/' + _piaPembayaranId + '/approve-items', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        // Controller merespon dengan redirect — ikuti redirect
        if (res.redirected) {
            window.location.href = res.url;
            return;
        }

        // Coba parse sebagai JSON (jika ada error response)
        const contentType = res.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            const result = await res.json();
            if (!result.success) {
                alert(result.message || 'Terjadi kesalahan.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-check"></i> Konfirmasi Keputusan';
                return;
            }
        }

        // Jika response adalah HTML (redirect di-follow), reload
        window.location.reload();
    } catch(e) {
        alert('Terjadi kesalahan jaringan: ' + e.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-check"></i> Konfirmasi Keputusan';
    }
}

function closePerItemApprovalModal() {
    const m = document.getElementById('modalPerItemApproval');
    m.classList.add('hidden'); m.classList.remove('flex');
    _piaPembayaranId = null;
}

document.getElementById('modalPerItemApproval')?.addEventListener('click', function(e) {
    if (e.target === this) closePerItemApprovalModal();
});

function _escH(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

@endsection
