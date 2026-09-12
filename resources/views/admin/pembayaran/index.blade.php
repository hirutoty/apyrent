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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total Pembayaran</p>
            <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $totalPR }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
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

                        if (!empty($_gpsI)) {
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
                                } elseif (in_array($_pr->status, ['Disetujui','Disetujui Sebagian'])) {
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
                            } elseif (in_array($_pr->status, ['Disetujui','Disetujui Sebagian'])) {
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
                            } elseif (in_array($_pr->status, ['Disetujui','Disetujui Sebagian'])) {
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

                        {{-- Breakdown status per item — hanya tampil di tab Semua --}}
                        @if(($tab ?? '') === 'semua')
                        <span class="flex items-center gap-1">
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700">
                                Pending: {{ $grpPending }}
                            </span>
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                Diajukan: {{ $grpDiajukan }}
                            </span>
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">
                                Disetujui: {{ $grpApproved }}
                            </span>
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700">
                                Ditolak: {{ $grpRejected }}
                            </span>
                        </span>
                        @endif

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
                                        // Hitung approved/rejected dari approval history untuk GPS
                                        $approvedCount = 0;
                                        $rejectedCount = 0;
                                        $isGpsPartial  = false;
                                        if ($d->status === 'Disetujui Sebagian' && in_array($d->source_type, ['gps','gps_perpanjang'])) {
                                            $approvedCount = $d->approvals->where('action','approved')->count();
                                            $rejectedCount = $d->approvals->where('action','rejected')->count();
                                            $isGpsPartial  = true;
                                        }

                                        // Status color: Disetujui Sebagian diperlakukan berbeda per tab
                                        $statusColor = match($d->status) {
                                            'Disetujui'          => 'bg-green-100 text-green-700',
                                            'Ditolak'            => 'bg-red-100 text-red-700',
                                            'Diajukan'           => 'bg-indigo-100 text-indigo-600',
                                            'Pending'            => 'bg-yellow-100 text-yellow-600',
                                            'Disetujui Sebagian' => $tab === 'Ditolak'
                                                                        ? 'bg-red-100 text-red-700'
                                                                        : 'bg-green-100 text-green-700',
                                            default              => 'bg-gray-100 text-gray-500',
                                        };
                                        $statusIcon = match($d->status) {
                                            'Disetujui'          => 'fa-check-circle',
                                            'Ditolak'            => 'fa-times-circle',
                                            'Diajukan'           => 'fa-paper-plane',
                                            'Pending'            => 'fa-clock',
                                            'Disetujui Sebagian' => $tab === 'Ditolak' ? 'fa-times-circle' : 'fa-check-circle',
                                            default              => 'fa-circle',
                                        };
                                        $statusLabel = match($d->status) {
                                            'Disetujui Sebagian' => $tab === 'Ditolak' ? 'Ditolak' : 'Disetujui',
                                            default              => $d->status ?? '-',
                                        };

                                        $rowUid = 'r'.$gIdx.'i'.$di;
                                        $_sd_items = is_array($d->source_data) ? $d->source_data : (json_decode($d->source_data, true) ?? []);
                                        $_dec = $_sd_items['item_decisions'] ?? [];
                                        $_gpsItemsAll = $_sd_items['gps_items'] ?? [];

                                        if (!empty($_gpsItemsAll)) {
                                            // GPS: hitung dari gps_items actual sesuai tab
                                            if (!empty($_dec)) {
                                                $_decMapCount = collect($_dec)->keyBy('idx');
                                                if (in_array($tab ?? '', ['Disetujui'])) {
                                                    $itemCount = collect($_gpsItemsAll)
                                                        ->filter(fn($g, $i) => ($_decMapCount[$i]['action'] ?? '') === 'approved')
                                                        ->count();
                                                } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                    $itemCount = collect($_gpsItemsAll)
                                                        ->filter(fn($g, $i) => ($_decMapCount[$i]['action'] ?? '') !== 'approved' && $_decMapCount->has($i))
                                                        ->count();
                                                } else {
                                                    // Semua/Pending/Diajukan: total gps_items
                                                    $itemCount = count($_gpsItemsAll);
                                                }
                                            } else {
                                                $itemCount = count($_gpsItemsAll);
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
                                        <td class="px-3 py-3 text-xs text-gray-700">{{ $d->pemohon ?? '-' }}</td>
                                        <td class="px-3 py-3">
                                            @if($d->keterangan)
                                                <span class="text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded max-w-[130px] block truncate"
                                                    title="{{ $d->keterangan }}">{{ $d->keterangan }}</span>
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
                                            @php
                                                $_sd     = is_array($d->source_data) ? $d->source_data : (json_decode($d->source_data, true) ?? []);
                                                $_decMap = collect($_sd['item_decisions'] ?? [])->keyBy('idx');

                                                if (($tab ?? '') === 'semua') {
                                                    if (!$_decMap->isEmpty()) {
                                                        $_nomApprRow = (int) ($_sd['nominal_approved'] ?? 0);
                                                        $_nomRejRow  = (int) ($_sd['nominal_rejected'] ?? 0);
                                                        if ($_nomApprRow === 0 && $_nomRejRow === 0) {
                                                            // nominal_approved/rejected tidak ada → pakai total semua gps_items
                                                            $_rowNominal = collect($_sd['gps_items'] ?? [])->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                            if ($_rowNominal == 0) $_rowNominal = (int)($d->nominal ?? 0);
                                                        } else {
                                                            $_rowNominal = $_nomApprRow + $_nomRejRow;
                                                        }
                                                    } else {
                                                        $_gpsItemsTotal = collect($_sd['gps_items'] ?? [])->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                        $_rowNominal = $_gpsItemsTotal > 0 ? $_gpsItemsTotal : ($d->nominal ?? 0);
                                                    }
                                                } else {
                                                    if (!$_decMap->isEmpty()) {
                                                        $_nomApprRow = collect($_sd['gps_items'] ?? [])
                                                            ->filter(fn($g, $i) => ($_decMap[$i]['action'] ?? '') === 'approved')
                                                            ->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                        $_nomRejRow  = collect($_sd['gps_items'] ?? [])
                                                            ->filter(fn($g, $i) => ($_decMap[$i]['action'] ?? '') !== 'approved' && $_decMap->has($i))
                                                            ->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                        if (in_array($tab ?? '', ['Ditolak'])) {
                                                            $_rowNominal = $_nomRejRow;
                                                        } elseif (in_array($tab ?? '', ['Disetujui'])) {
                                                            $_rowNominal = $_nomApprRow;
                                                        } else {
                                                            $_rowNominal = $_nomApprRow + $_nomRejRow;
                                                        }
                                                    } else {
                                                        $_gpsItemsTotal = collect($_sd['gps_items'] ?? [])->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                        $_rowNominal = $_gpsItemsTotal > 0 ? $_gpsItemsTotal : ($d->nominal ?? 0);
                                                    }
                                                }
                                            @endphp
                                            {{-- DEBUG TEMP: hapus setelah fix --}}
                                            @if(($_rowNominal ?? 0) == 0)
                                                <div class="text-[9px] text-gray-400 text-left">
                                                    id:{{ $d->id }}|tab:{{ $tab }}|gpsTotal:{{ collect($_sd['gps_items'] ?? [])->sum(fn($g) => $g['biaya_sewa'] ?? 0) }}|nom:{{ $d->nominal }}|decmap:{{ $_decMap->count() }}
                                                </div>
                                            @endif
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

                                                <button type="button" onclick="openDetailModal({{ $d->id }})"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition-colors">
                                                    <i class="fa fa-eye text-[10px]"></i> Detail
                                                </button>

                                                @if($role === 'superadmin')
                                                    @if(in_array($d->status, ['Pending', 'Diajukan']))
                                                        @if($d->source_type)
                                                            @if(in_array($d->source_type, ['gps', 'gps_perpanjang', 'service_part']))
                                                                {{-- GPS/Service Part: pakai approval modal dengan per-item approve/reject --}}
                                                                <button type="button"
                                                                    onclick="openApprovalModal({{ $d->id }})"
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 transition-colors">
                                                                    <i class="fa fa-check text-[10px]"></i> Approve
                                                                </button>
                                                                <button type="button"
                                                                    onclick="openRejectModal({{ $d->id }})"
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition-colors">
                                                                    <i class="fa fa-times text-[10px]"></i> Reject
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
                                                        <a href="{{ route('pembayaran.edit-rejected', $d->id) }}"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition-colors">
                                                            <i class="fa fa-edit text-[10px]"></i> Edit & Ajukan Ulang
                                                        </a>
                                                    @elseif($d->status === 'Disetujui' && $d->source_type && $d->target_id)
                                                        <a href="{{ route(match($d->source_type){'asuransi_kendaraan'=>'asuransi-kendaraan.index','pajak'=>'pajak.index',default=>'pembayaran.index'}) }}" target="_blank"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                                            <i class="bi bi-box-arrow-up-right text-[10px]"></i> Lihat Data
                                                        </a>
                                                    @endif
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
                                                    @if($kend)
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
                                                    {{-- Asuransi --}}
                                                    @elseif(in_array($d->source_type,['asuransi_kendaraan','asuransi_kendaraan_perpanjang']))
                                                    @php $asr = isset($sd['asuransi_id']) ? \App\Models\Asuransi::find($sd['asuransi_id']) : null; $jAsr = isset($sd['jenis_asuransi_id']) ? \App\Models\JenisAsuransi::find($sd['jenis_asuransi_id']) : null; @endphp
                                                    <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Perusahaan</p><p class="text-xs font-semibold text-gray-700">{{ $asr->nama_asuransi ?? '-' }}</p></div>
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Jenis</p><p class="text-xs text-gray-700">{{ $jAsr->nama_jenis ?? '-' }}</p></div>
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
                                                    {{-- STNK --}}
                                                    @elseif($d->source_type === 'stnk')
                                                    <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                                                        @if(isset($sd['tahun_stnk']))<div><p class="text-[10px] text-gray-400 uppercase">Tahun STNK</p><p class="text-xs font-semibold text-gray-700">{{ $sd['tahun_stnk'] }}</p></div>@endif
                                                        @if(isset($sd['tanggal_stnk']))<div><p class="text-[10px] text-gray-400 uppercase">Tgl STNK</p><p class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($sd['tanggal_stnk'])->format('d M Y') }}</p></div>@endif
                                                        <div><p class="text-[10px] text-gray-400 uppercase">Biaya</p><p class="text-xs font-bold text-emerald-600">Rp {{ number_format($sd['biaya']??$d->nominal??0,0,',','.') }}</p></div>
                                                    </div>
                                                    {{-- GPS --}}
                                                    @elseif(in_array($d->source_type, ['gps', 'gps_perpanjang']))
                                                    @php
                                                        $gpsItems    = $sd['gps_items'] ?? [];
                                                        $itemDecMap  = collect($sd['item_decisions'] ?? [])->keyBy('idx');
                                                        // Filter item sesuai tab: approved → tampil di Disetujui, rejected → Ditolak
                                                        // Jika belum ada keputusan, tampilkan semua
                                                        if ($itemDecMap->isNotEmpty()) {
                                                            if (in_array($tab ?? '', ['Disetujui'])) {
                                                                $filteredGpsItems = collect($gpsItems)
                                                                    ->filter(fn($g, $i) => ($itemDecMap[$i]['action'] ?? '') === 'approved')
                                                                    ->all();
                                                            } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                                $filteredGpsItems = collect($gpsItems)
                                                                    ->filter(fn($g, $i) => ($itemDecMap[$i]['action'] ?? '') !== 'approved' && $itemDecMap->has($i))
                                                                    ->all();
                                                            } else {
                                                                $filteredGpsItems = $gpsItems; // semua (tab Semua/lainnya)
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
                                                                <td colspan="{{ $itemDecMap->isNotEmpty() ? 7 : 6 }}" class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Total</td>
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
                                                        $spParts    = $sd['parts'] ?? [];
                                                        $spDecMap   = collect($sd['item_decisions'] ?? [])->keyBy('idx');
                                                        if ($spDecMap->isNotEmpty()) {
                                                            if (in_array($tab ?? '', ['Disetujui'])) {
                                                                $filteredParts = collect($spParts)->filter(fn($p, $i) => ($spDecMap[$i]['action'] ?? '') === 'approved')->values()->all();
                                                            } elseif (in_array($tab ?? '', ['Ditolak'])) {
                                                                $filteredParts = collect($spParts)->filter(fn($p, $i) => ($spDecMap[$i]['action'] ?? '') !== 'approved' && $spDecMap->has($i))->values()->all();
                                                            } else {
                                                                $filteredParts = $spParts;
                                                            }
                                                        } else {
                                                            $filteredParts = $spParts;
                                                        }
                                                        $spTotal = collect($filteredParts)->sum(fn($p) => $p['biaya'] ?? 0);
                                                    @endphp
                                                    @if(count($filteredParts) > 0)
                                                    <table class="w-full text-xs">
                                                        <thead>
                                                            <tr class="bg-orange-50/60 border-y border-orange-100">
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">#</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Nama Part</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Kategori</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Kondisi</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">Bank</th>
                                                                <th class="text-left px-4 py-2 font-semibold text-gray-500">No. Rekening</th>
                                                                <th class="text-right px-4 py-2 font-semibold text-gray-500">Biaya</th>
                                                                @if($spDecMap->isNotEmpty())
                                                                    <th class="text-center px-4 py-2 font-semibold text-gray-500">Status</th>
                                                                @endif
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($filteredParts as $spi => $spart)
                                                            @php
                                                                $spCat   = isset($spart['category_id']) ? \App\Models\ServiceCategory::find($spart['category_id']) : null;
                                                                $spCatNm = $spCat ? $spCat->nama : ($spart['nama_category_baru'] ?? '-');
                                                                $spDec   = $spDecMap[$spi] ?? null;
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
                                                                <td class="px-4 py-2 text-gray-600">{{ $spart['nama_bank'] ?? '-' }}</td>
                                                                <td class="px-4 py-2 font-mono text-gray-600">{{ $spart['no_rekening'] ?? '-' }}</td>
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
                                                        @endforeach
                                                            <tr class="border-t-2 border-gray-200 bg-gray-50">
                                                                <td colspan="{{ $spDecMap->isNotEmpty() ? 7 : 6 }}" class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Total</td>
                                                                <td class="px-4 py-2 text-right text-sm font-bold {{ in_array($tab ?? '', ['Ditolak']) ? 'text-red-500' : 'text-emerald-600' }}">
                                                                    Rp {{ number_format($spTotal, 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    @else
                                                    <div class="px-4 py-3 text-xs text-gray-400 italic">Tidak ada part yang ditampilkan.</div>
                                                    @endif
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
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan (opsional)</label>
                <textarea name="catatan" rows="2" placeholder="Catatan persetujuan..."
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
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa fa-file-lines text-blue-500"></i> Detail Pembayaran
                </h2>
                <p id="d_no_pr" class="text-xs text-gray-400 mt-0.5 font-mono"></p>
            </div>
            <button onclick="closeDetailModal()"
                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="px-6 py-4 space-y-3">
            <div class="grid grid-cols-4 gap-3">
                <div class="bg-gray-50 rounded-xl px-3 py-2.5"><p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Tanggal</p><p id="d_tanggal" class="text-sm font-medium text-gray-700"></p></div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5"><p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Pemohon</p><p id="d_pemohon" class="text-sm font-medium text-gray-700"></p></div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5"><p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Departemen</p><p id="d_departemen" class="text-sm font-medium text-gray-700"></p></div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5"><p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Status</p><span id="d_status_badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"></span></div>
            </div>
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-100"><p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">Detail Items</p></div>
                <div id="d_items_container"></div>
                <div id="d_old_structure" class="hidden grid grid-cols-3 divide-x divide-y divide-gray-100">
                    <div class="px-4 py-2.5 col-span-2"><p class="text-[10px] text-gray-400 mb-0.5">Barang/Jasa</p><p id="d_barang_jasa" class="text-sm font-medium text-gray-700"></p></div>
                    <div class="px-4 py-2.5"><p class="text-[10px] text-gray-400 mb-0.5">Nominal</p><p id="d_nominal_old" class="text-sm font-semibold text-emerald-700"></p></div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-emerald-50 rounded-xl px-3 py-2.5 border border-emerald-100"><p class="text-[10px] text-emerald-400 font-semibold uppercase tracking-wide mb-0.5">Total Nominal</p><p id="d_total_nominal" class="text-base font-bold text-emerald-700"></p></div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5"><p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Total Items</p><p id="d_total_items" class="text-base font-semibold text-gray-700"></p></div>
            </div>
            <div class="bg-blue-50 rounded-xl px-4 py-3 border border-blue-100"><p class="text-[10px] text-blue-400 font-semibold uppercase tracking-wide mb-1">Alasan Permintaan</p><p id="d_alasan" class="text-sm text-blue-700"></p></div>
            <div id="d_catatan_section" class="hidden bg-red-50 border border-red-100 rounded-xl px-4 py-2.5"><p class="text-[10px] text-red-400 font-semibold uppercase tracking-wide mb-0.5">Catatan Penolakan</p><p id="d_catatan" class="text-sm text-red-700"></p></div>
        </div>
        <div class="px-6 pb-4">
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
function openDetailModal(id) {
    const modal = document.getElementById('detailModal');
    modal.classList.remove('hidden'); modal.classList.add('flex');
    document.getElementById('d_no_pr').innerText = 'Loading...';
    document.getElementById('d_items_container').innerHTML =
        '<div class="px-4 py-6 text-center text-gray-400"><i class="fa fa-spinner fa-spin mr-2"></i>Loading...</div>';

    fetch('/admin/pembayaran/' + id + '/details')
        .then(r => r.json())
        .then(data => { if (data.success) populateDetailModal(data.pembayaran); else closeDetailModal(); })
        .catch(() => closeDetailModal());
}

function populateDetailModal(pr) {
    document.getElementById('d_no_pr').innerText = pr.no_pr;
    document.getElementById('d_tanggal').innerText = pr.tanggal_formatted;
    document.getElementById('d_pemohon').innerText = pr.pemohon;
    document.getElementById('d_departemen').innerText = pr.departemen;
    document.getElementById('d_alasan').innerText = pr.alasan_permintaan || '-';
    const sb = document.getElementById('d_status_badge');
    sb.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium ' + pr.status_class;
    sb.innerHTML = '<i class="fa fa-circle text-[6px]"></i> ' + pr.status;
    document.getElementById('d_total_nominal').innerText = 'Rp ' + pr.total_nominal_formatted;
    document.getElementById('d_total_items').innerText = pr.total_items + ' item' + (pr.total_items > 1 ? 's' : '');

    const container = document.getElementById('d_items_container');
    const oldStruct = document.getElementById('d_old_structure');
    if (pr.items && pr.items.length > 0) {
        container.innerHTML = '';
        oldStruct.classList.add('hidden');
        pr.items.forEach((item, idx) => {
            const div = document.createElement('div');
            div.className = 'p-4' + (idx > 0 ? ' border-t border-gray-100' : '');
            div.innerHTML = `<div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Item #${idx+1}</span>
                ${item.subtotal ? '<span class="text-sm font-semibold text-emerald-600">Rp '+item.subtotal_formatted+'</span>' : ''}
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                <div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Nama Barang</p><p class="font-medium text-gray-700">${item.nama_barang}</p></div>
                <div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Kategori</p><p class="text-gray-600">${item.kategori||'-'}</p></div>
                <div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Qty</p><p class="font-medium text-gray-700">${item.qty} ${item.satuan||''}</p></div>
                <div><p class="text-[10px] text-gray-400 uppercase mb-0.5">Harga Satuan</p><p class="text-gray-600">${item.harga_satuan ? 'Rp '+item.harga_satuan_formatted : '-'}</p></div>
            </div>`;
            container.appendChild(div);
        });
    } else {
        oldStruct.classList.remove('hidden');
        container.innerHTML = '';
        document.getElementById('d_barang_jasa').innerText = pr.barang_jasa || '-';
        document.getElementById('d_nominal_old').innerText = pr.nominal ? 'Rp ' + pr.nominal_formatted : '-';
    }

    const catatanSection = document.getElementById('d_catatan_section');
    if (pr.catatan && pr.catatan.trim()) {
        document.getElementById('d_catatan').innerText = pr.catatan;
        catatanSection.classList.remove('hidden');
    } else {
        catatanSection.classList.add('hidden');
    }
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
document.addEventListener('DOMContentLoaded', function() {
    initPembayaranCharts({ filter_type: 'month' });
    document.addEventListener('chartFilterChange', function(e) {
        if (e.detail.filterId === 'pembayaranChartFilter') {
            updatePembayaranCharts({
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
                departemen:  e.detail.categoryId ?? '',
            });
        }
    });
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
</script>
@endpush

@endsection
