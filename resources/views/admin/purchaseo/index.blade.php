@extends('admin.layouts.app')
@section('title', 'Purchase Order Approval')
@section('content')
<div class="space-y-6 p-5">

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <i class="fa fa-check-circle text-green-500"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <i class="fa fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
            <i class="fa fa-exclamation-triangle text-amber-500"></i> {{ session('warning') }}
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Purchase Order</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola approval purchase order dari pengeluaran kendaraan</p>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total PO</p>
            <h2 class="text-3xl font-bold text-indigo-600 mt-2">{{ $totalPO }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total Nilai (Pending + Disetujui)</p>
            <h2 class="text-2xl font-bold text-emerald-600 mt-2">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h2>
        </div>
    </div>

    {{-- CHART --}}
    @php
        $sourceList = $sourceTypes->keys()->map(fn($s) => ['id' => $s, 'nama' => ucfirst(str_replace('_', ' ', $s))]);
    @endphp
    <x-chart-filter id="poChartFilter" defaultFilter="month" :showCustomRange="true"
        :showCategoryFilter="true" :categories="$sourceList" />
    <x-chart-container id="poChartContainer" layout="stacked"
        pieTitle="Distribusi Status PO" pieId="poPieChart"
        barTitle="Total Harga PO per Bulan" barId="poBarChart"
        lineTitle="Trend PO" lineId="poLineChart"
        :showStats="true" :statsData="[]" />

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

        {{-- NAV TABS --}}
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 -mb-px overflow-x-auto">
                @foreach ([
                    ['label' => 'Semua',    'status' => 'semua',    'count' => $totalPO,       'color' => 'blue'],
                    ['label' => 'Pending',  'status' => 'Pending',  'count' => $totalPending,  'color' => 'yellow'],
                    ['label' => 'Disetujui','status' => 'Disetujui','count' => $totalApproved, 'color' => 'green'],
                    ['label' => 'Ditolak',  'status' => 'Ditolak',  'count' => $totalRejected, 'color' => 'red'],
                ] as $tab)
                    @php $isActive = $statusFilter === $tab['status']; @endphp
                    <a href="{{ route('purchase-order.index', array_merge(request()->except('status'), ['status' => $tab['status']])) }}"
                        class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                            {{ $isActive ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <span>{{ $tab['label'] }}</span>
                        @php $bc = match($tab['color']){'green'=>'bg-green-100 text-green-700','red'=>'bg-red-100 text-red-700','yellow'=>'bg-yellow-100 text-yellow-700','blue'=>'bg-blue-100 text-blue-700',default=>'bg-gray-100 text-gray-700'}; @endphp
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $bc }}">{{ $tab['count'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- TOOLBAR --}}
        <div class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <div class="flex-1 text-xs text-gray-500">
                Menampilkan <span class="font-semibold text-gray-700">{{ $data->total() }}</span> data
            </div>

            {{-- Filter Form (Jenis + Tahun) --}}
            <form method="GET" action="{{ route('purchase-order.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <input type="hidden" name="sort" value="{{ $sort }}">

                <span class="text-xs text-gray-500 whitespace-nowrap">Jenis:</span>
                <select name="source_type"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
                    <option value="">Semua Jenis</option>
                    @foreach($sourceTypes->keys() as $st)
                        <option value="{{ $st }}" {{ $sourceFilter === $st ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $st)) }}
                        </option>
                    @endforeach
                </select>

                <span class="text-xs text-gray-500 whitespace-nowrap">Tahun:</span>
                <select name="tahun"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ ($tahunFilter ?? '') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>

                <button type="submit"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    <i class="fa fa-filter text-xs"></i> Filter
                </button>
                @if($sourceFilter || $tahunFilter)
                    <a href="{{ route('purchase-order.index', ['status' => $statusFilter, 'sort' => $sort]) }}"
                        class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </form>

            {{-- Sort --}}
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 whitespace-nowrap">Urutkan:</span>
                <a href="{{ route('purchase-order.index', array_merge(request()->except('sort'), ['sort' => 'terbaru'])) }}"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                        {{ $sort === 'terbaru' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    <i class="fa fa-sort-down"></i> Terbaru
                </a>
                <a href="{{ route('purchase-order.index', array_merge(request()->except('sort'), ['sort' => 'terlama'])) }}"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                        {{ $sort === 'terlama' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    <i class="fa fa-sort-up"></i> Terlama
                </a>
            </div>
        </div>

        {{-- GROUPED ACCORDION BY JENIS --}}
        @php
            $grouped = $data->getCollection()->groupBy(function($po) {
                return ucwords(str_replace('_', ' ', $po->source_type ?? 'Lainnya'));
            });

            $jenisConfig = [
                'Gps'           => ['icon'=>'fa fa-satellite-dish', 'color'=>'green'],
                'Gps Perpanjang'=> ['icon'=>'fa fa-rotate-right',   'color'=>'green'],
                'Asuransi Kendaraan'         => ['icon'=>'fa fa-shield',      'color'=>'purple'],
                'Asuransi Kendaraan Perpanjang' => ['icon'=>'fa fa-shield',   'color'=>'purple'],
                'Pajak'         => ['icon'=>'fa fa-receipt',        'color'=>'blue'],
                'Pajak Perpanjang' => ['icon'=>'fa fa-rotate-right','color'=>'blue'],
                'Kir'           => ['icon'=>'fa fa-clipboard-check','color'=>'teal'],
                'Kir Perpanjang'=> ['icon'=>'fa fa-rotate-right',   'color'=>'teal'],
                'Stnk'          => ['icon'=>'fa fa-id-card',        'color'=>'indigo'],
                'Service Part'  => ['icon'=>'fa fa-wrench',         'color'=>'orange'],
                'Service Asuransi' => ['icon'=>'fa fa-tools',       'color'=>'orange'],
                'Service Incident' => ['icon'=>'fa fa-exclamation-triangle', 'color'=>'red'],
                'Lainnya'       => ['icon'=>'fa fa-file-invoice',   'color'=>'gray'],
            ];
            $colorMap = [
                'green'  => ['bg'=>'bg-green-50',  'border'=>'border-green-200',  'text'=>'text-green-700',  'hdr'=>'bg-green-50/40'],
                'blue'   => ['bg'=>'bg-blue-50',   'border'=>'border-blue-200',   'text'=>'text-blue-700',   'hdr'=>'bg-blue-50/40'],
                'purple' => ['bg'=>'bg-purple-50', 'border'=>'border-purple-200', 'text'=>'text-purple-700', 'hdr'=>'bg-purple-50/40'],
                'orange' => ['bg'=>'bg-orange-50', 'border'=>'border-orange-200', 'text'=>'text-orange-700', 'hdr'=>'bg-orange-50/40'],
                'teal'   => ['bg'=>'bg-teal-50',   'border'=>'border-teal-200',   'text'=>'text-teal-700',   'hdr'=>'bg-teal-50/40'],
                'indigo' => ['bg'=>'bg-indigo-50', 'border'=>'border-indigo-200', 'text'=>'text-indigo-700', 'hdr'=>'bg-indigo-50/40'],
                'gray'   => ['bg'=>'bg-gray-50',   'border'=>'border-gray-200',   'text'=>'text-gray-600',   'hdr'=>'bg-gray-50/40'],
                'red'    => ['bg'=>'bg-red-50',    'border'=>'border-red-200',    'text'=>'text-red-700',    'hdr'=>'bg-red-50/40'],
            ];
        @endphp

        @if($data->isEmpty())
            <div class="text-center py-16 text-gray-400 text-sm">
                <i class="fa fa-inbox text-4xl mb-3 block text-gray-300"></i>
                Belum ada Purchase Order
            </div>
        @else
            <div class="divide-y divide-gray-100">
            @foreach($grouped as $jenis => $items)
                @php
                    $cfg = $jenisConfig[$jenis] ?? ['icon'=>'fa fa-file-invoice','color'=>'gray'];
                    $clr = $colorMap[$cfg['color']] ?? $colorMap['gray'];
                    $gIdx = $loop->index;

                    // Hitung nominal & count per item (bukan per PO)
                    // GPS punya gps_items di source_data — hitung tiap sub-item
                    $totalGrp        = 0; // pending + approved (ditolak tidak masuk)
                    $nominalApproved = 0;
                    $nominalRejected = 0;
                    $nominalPending  = 0;
                    $grpPending  = 0;
                    $grpApproved = 0;
                    $grpRejected = 0;
                    foreach ($items as $_po) {
                        $_sd    = $_po->source_data ?? [];
                        $_gpsI  = $_sd['gps_items'] ?? [];
                        $_dec   = $_sd['item_decisions'] ?? [];
                        if (!empty($_gpsI)) {
                            if (!empty($_dec)) {
                                // Sudah ada keputusan per item
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
                                // Sisa yang belum diproses → Pending
                                $processedIdx = array_column($_dec, 'idx');
                                foreach ($_gpsI as $_gi => $_gitem) {
                                    if (!in_array($_gi, $processedIdx)) {
                                        $grpPending++;
                                        $nominalPending += (int)($_gitem['biaya_sewa'] ?? 0);
                                    }
                                }
                            } else {
                                // Belum diproses — semua item ikut status PO
                                $_poNom = (int)($_po->total_harga ?? 0);
                                $cnt    = count($_gpsI);
                                if ($_po->status === 'Pending') {
                                    $grpPending     += $cnt;
                                    $nominalPending += $_poNom;
                                } elseif ($_po->status === 'Disetujui') {
                                    $grpApproved     += $cnt;
                                    $nominalApproved += $_poNom;
                                } elseif ($_po->status === 'Ditolak') {
                                    $grpRejected     += $cnt;
                                    $nominalRejected += $_poNom;
                                }
                            }
                        } else {
                            // Non-GPS: 1 PO = 1 item
                            $_poNom = (int)($_po->total_harga ?? 0);
                            if ($_po->status === 'Pending') {
                                $grpPending++;
                                $nominalPending += $_poNom;
                            } elseif ($_po->status === 'Disetujui') {
                                $grpApproved++;
                                $nominalApproved += $_poNom;
                            } elseif ($_po->status === 'Ditolak') {
                                $grpRejected++;
                                $nominalRejected += $_poNom;
                            }
                        }
                    }
                    $totalGrp = $nominalApproved + $nominalPending;
                @endphp

                <div>
                    {{-- GROUP HEADER --}}
                    <div class="w-full flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors cursor-pointer {{ $clr['hdr'] }}"
                        onclick="togglePoGroup({{ $gIdx }})">
                        <i id="po-grp-chevron-{{ $gIdx }}"
                            class="fa fa-chevron-right text-[11px] text-gray-400 transition-transform duration-200 flex-shrink-0"></i>
                        <span class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 {{ $clr['bg'] }} border {{ $clr['border'] }}">
                            <i class="{{ $cfg['icon'] }} {{ $clr['text'] }} text-sm"></i>
                        </span>
                        <span class="text-sm font-bold text-gray-800">{{ $jenis }}</span>
                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $items->count() }} PO</span>

                        {{-- Breakdown status per item — hanya tampil di tab Semua --}}
                        @if(($statusFilter ?? '') === 'semua')
                        <span class="flex items-center gap-1">
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700">
                                Pending: {{ $grpPending }}
                            </span>
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">
                                Disetujui: {{ $grpApproved }}
                            </span>
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700">
                                Ditolak: {{ $grpRejected }}
                            </span>
                        </span>
                        @endif

                    </div>

                    {{-- GROUP BODY --}}
                    <div id="po-grp-body-{{ $gIdx }}" class="hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50/80 border-b border-gray-100">
                                        <th class="w-6 px-2 py-2.5"></th>
                                        <th class="text-left text-[11px] font-semibold uppercase text-gray-400 px-4 py-2.5">PO Number</th>
                                        <th class="text-left text-[11px] font-semibold uppercase text-gray-400 px-4 py-2.5">Vendor</th>
                                        <th class="text-left text-[11px] font-semibold uppercase text-gray-400 px-4 py-2.5">Keluhan</th>
                                        <th class="text-left text-[11px] font-semibold uppercase text-gray-400 px-4 py-2.5">Items</th>
                                        <th class="text-right text-[11px] font-semibold uppercase text-gray-400 px-4 py-2.5">Total Harga</th>
                                        <th class="text-left text-[11px] font-semibold uppercase text-gray-400 px-4 py-2.5">Tanggal</th>
                                        <th class="text-left text-[11px] font-semibold uppercase text-gray-400 px-4 py-2.5">Status</th>
                                        <th class="text-center text-[11px] font-semibold uppercase text-gray-400 px-4 py-2.5">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $poIdx => $po)
                                    @php
                                        $poRowId      = 'po-row-' . $gIdx . '-' . $poIdx;
                                        $sourceData   = $po->source_data ?? [];
                                        $allGpsItems  = $sourceData['gps_items'] ?? [];
                                        $poDecMap     = collect($sourceData['item_decisions'] ?? [])->keyBy('idx');

                                        // Filter GPS items sesuai tab — untuk data lama yang item_decisions-nya ada
                                        // Data baru sudah tersimpan terfilter di source_data
                                        if ($poDecMap->isNotEmpty()) {
                                            if ($statusFilter === 'Disetujui') {
                                                $gpsItems = collect($allGpsItems)
                                                    ->filter(fn($g, $i) => ($poDecMap[$i]['action'] ?? '') === 'approved')
                                                    ->values()->all();
                                            } elseif ($statusFilter === 'Ditolak') {
                                                $gpsItems = collect($allGpsItems)
                                                    ->filter(fn($g, $i) => ($poDecMap[$i]['action'] ?? '') !== 'approved' && $poDecMap->has($i))
                                                    ->values()->all();
                                            } else {
                                                $gpsItems = $allGpsItems;
                                            }
                                        } else {
                                            $gpsItems = $allGpsItems;
                                        }

                                        $hasItems = !empty($allGpsItems) || (in_array($po->source_type, ['service_part', 'service_incident']) && !empty($sourceData['parts'])) || ($po->source_type === 'service_asuransi' && !empty($sourceData['kejadians']));
                                    @endphp
                                    {{-- Baris utama --}}
                                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/40 hover:bg-blue-50/30 transition-colors {{ $hasItems ? 'cursor-pointer' : '' }}"
                                        @if($hasItems) onclick="togglePoRow('{{ $poRowId }}')" @endif>
                                        <td class="px-2 py-3 text-center">
                                            @if($hasItems)
                                                <i id="chv-{{ $poRowId }}" class="fa fa-chevron-right text-[10px] text-gray-300 transition-transform duration-200"></i>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="font-mono text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-lg border border-indigo-100">{{ $po->po_id }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-700">{{ $po->vendor ?? '-' }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            @php
                                                $ketPO = $po->keterangan
                                                    ?: ($po->source_data['keluhan'] ?? null)
                                                    ?: ($po->source_data['alasan_permintaan'] ?? null);
                                            @endphp
                                            @if($ketPO)
                                                <span>{{ $ketPO }}</span>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-700 text-center">
                                            @php
                                                $_poDec   = $po->source_data['item_decisions'] ?? [];
                                                $_poGpsI  = $po->source_data['gps_items'] ?? [];
                                                $_poParts = $po->source_data['parts'] ?? [];
                                                $_poKejad = $po->source_data['kejadians'] ?? [];
                                                if (!empty($_poDec)) {
                                                    // Ada keputusan per item — tampilkan sesuai status
                                                    $_poItemCount = collect($_poDec)
                                                        ->filter(fn($_d) => ($statusFilter === 'Ditolak')
                                                            ? ($_d['action'] ?? '') !== 'approved'
                                                            : ($_d['action'] ?? '') === 'approved')
                                                        ->count();
                                                } elseif (!empty($_poGpsI)) {
                                                    $_poItemCount = count($_poGpsI);
                                                } elseif ($po->source_type === 'service_asuransi' && !empty($_poKejad)) {
                                                    $_poItemCount = count($_poKejad);
                                                } elseif (in_array($po->source_type, ['service_part','service_incident']) && !empty($_poParts)) {
                                                    $_poItemCount = count($_poParts);
                                                } else {
                                                    $_poItemCount = $po->total_barang ?? 0;
                                                }
                                            @endphp
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-600">
                                                <i class="fa fa-boxes text-blue-400 text-[10px]"></i>
                                                {{ number_format($_poItemCount) }} item
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-xs font-semibold text-gray-800 text-right">
                                            @php
                                                $_poDec2  = $po->source_data['item_decisions'] ?? [];
                                                $_poGpsI2 = $po->source_data['gps_items'] ?? [];
                                                if (!empty($_poDec2) && !empty($_poGpsI2)) {
                                                    $_poNomAppr = collect($_poGpsI2)
                                                        ->filter(fn($g, $i) => ($_poDec2[$i]['action'] ?? '') === 'approved')
                                                        ->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                    $_poNomRej  = collect($_poGpsI2)
                                                        ->filter(fn($g, $i) => ($_poDec2[$i]['action'] ?? '') !== 'approved')
                                                        ->sum(fn($g) => $g['biaya_sewa'] ?? 0);
                                                    $_poNomShow = $statusFilter === 'Ditolak' ? $_poNomRej : $_poNomAppr;
                                                } else {
                                                    $_poNomShow = $po->total_harga ?? 0;
                                                }
                                            @endphp
                                            <span class="{{ $statusFilter === 'Ditolak' ? 'text-red-500' : 'text-emerald-600' }}">
                                                Rp {{ number_format($_poNomShow, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500">{{ $po->tanggal_po ? $po->tanggal_po->format('d M Y') : '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if($po->status === 'Pending')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-yellow-100 text-yellow-700">
                                                    <i class="fa fa-clock text-[8px]"></i> Pending
                                                </span>
                                            @elseif($po->status === 'Disetujui')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-green-100 text-green-700">
                                                    <i class="fa fa-check text-[8px]"></i> Disetujui
                                                </span>
                                            @elseif($po->status === 'Ditolak')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-red-100 text-red-700">
                                                    <i class="fa fa-times text-[8px]"></i> Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button onclick="viewDetail({{ $po->id }})"
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                                    <i class="fa fa-eye text-xs"></i> Detail
                                                </button>
                                                @if($po->status === 'Pending' && auth()->user()->role === 'superadmin')
                                                    @if(in_array($po->source_type, ['gps', 'gps_perpanjang', 'service_part', 'service_incident', 'service_asuransi']))
                                                        {{-- GPS / Service Part / Service Incident / Service Asuransi: per-item approval modal --}}
                                                        <button onclick="openApproveModal({{ $po->id }}, '{{ $po->po_id }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                                                            <i class="fa fa-check text-xs"></i> Approve
                                                        </button>
                                                    @elseif($po->source_type === 'stnk')
                                                        {{-- STNK: langsung confirm tanpa modal --}}
                                                        <button onclick="approveStnk({{ $po->id }}, '{{ $po->po_id }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                                                            <i class="fa fa-check text-xs"></i> Approve
                                                        </button>
                                                    @else
                                                        {{-- Pajak, Asuransi, KIR, dll: modal sederhana --}}
                                                        <button onclick="openApproveSimpleModal({{ $po->id }}, '{{ $po->po_id }}', '{{ $po->source_type }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                                                            <i class="fa fa-check text-xs"></i> Approve
                                                        </button>
                                                    @endif
                                                    @if(in_array($po->source_type, ['gps', 'gps_perpanjang', 'service_part', 'service_incident', 'service_asuransi']))
                                                        <button onclick="openRejectModal({{ $po->id }}, '{{ $po->po_id }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                                            <i class="fa fa-times text-xs"></i> Reject
                                                        </button>
                                                    @else
                                                        <button onclick="openRejectSimpleModal({{ $po->id }}, '{{ $po->po_id }}', '{{ $po->source_type }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                                            <i class="fa fa-times text-xs"></i> Reject
                                                        </button>
                                                    @endif
                                                @endif
                                                @if($po->status === 'Ditolak' && ($po->can_edit || in_array($po->source_type, ['service_part', 'service_asuransi', 'service_incident'])))
                                                    @if(in_array($po->source_type, ['gps', 'gps_perpanjang']))
                                                        <button onclick="openResubmitModal({{ $po->id }}, '{{ $po->po_id }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-colors">
                                                            <i class="fa fa-rotate-right text-xs"></i> Ulang
                                                        </button>
                                                    @elseif($po->source_type === 'stnk')
                                                        {{-- STNK: reset ke Pending langsung --}}
                                                        <button onclick="resubmitSimple({{ $po->id }}, '{{ $po->po_id }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-colors">
                                                            <i class="fa fa-rotate-right text-xs"></i> Ulang
                                                        </button>
                                                    @elseif($po->source_type === 'service_part')
                                                        {{-- Service Part: redirect ke form create dengan semua parts pre-filled --}}
                                                        <button onclick="resubmitViaPO({{ $po->id }})"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-colors">
                                                            <i class="fa fa-rotate-right text-xs"></i> Ulang
                                                        </button>
                                                    @elseif(in_array($po->source_type, ['service_asuransi', 'service_incident']))
                                                        {{-- Service Asuransi: modal ajukan ulang dengan pre-fill data lama --}}
                                                        <button onclick="openResubmitServiceAsuransiModal({{ $po->id }}, '{{ $po->po_id }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-colors">
                                                            <i class="fa fa-rotate-right text-xs"></i> Ulang
                                                        </button>
                                                    @else
                                                        {{-- Pajak, Asuransi, KIR & _perpanjang: modal inline --}}
                                                        <button onclick="openResubmitSimpleModal({{ $po->id }}, '{{ $po->po_id }}', '{{ $po->source_type }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-colors">
                                                            <i class="fa fa-rotate-right text-xs"></i> Ulang
                                                        </button>
                                                    @endif
                                                @endif
                                                @if(in_array($po->status, ['Pending', 'Ditolak']))
                                                    <form action="{{ route('purchase-order.destroy', $po->id) }}" method="POST" class="inline"
                                                        onsubmit="return confirm('Yakin menghapus PO ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-white bg-gray-500 rounded-lg hover:bg-gray-600 transition-colors">
                                                            <i class="fa fa-trash text-xs"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Baris detail items (hidden, toggle on click) --}}
                                    @if($hasItems)
                                    @php
                                        $kendaraanId  = $sourceData['kendaraan_id'] ?? null;
                                        $kendaraan    = $kendaraanId ? \App\Models\Kendaraan::find($kendaraanId) : null;
                                        $isServicePart     = $po->source_type === 'service_part';
                                        $isServiceIncident = $po->source_type === 'service_incident';
                                        $isServiceAsuransi = $po->source_type === 'service_asuransi';

                                        if ($isServicePart || $isServiceIncident) {
                                            $allParts    = $sourceData['parts'] ?? [];
                                            $partDecMap  = collect($sourceData['item_decisions'] ?? [])->keyBy('idx');

                                            if ($partDecMap->isNotEmpty()) {
                                                if ($statusFilter === 'Disetujui') {
                                                    $parts = collect($allParts)
                                                        ->filter(fn($p, $i) => ($partDecMap[$i]['action'] ?? '') === 'approved')
                                                        ->values()->all();
                                                } elseif ($statusFilter === 'Ditolak') {
                                                    $parts = collect($allParts)
                                                        ->filter(fn($p, $i) => ($partDecMap[$i]['action'] ?? '') !== 'approved' && $partDecMap->has($i))
                                                        ->values()->all();
                                                } else {
                                                    $parts = $allParts;
                                                }
                                            } else {
                                                $parts = $allParts;
                                            }

                                            $totalItems = collect($parts)->sum(fn($p) => $p['biaya'] ?? 0);
                                        } elseif ($isServiceAsuransi) {
                                            $asuransiKejadians = $sourceData['kejadians'] ?? [];
                                            $totalItems = collect($asuransiKejadians)->sum(fn($k) => $k['biaya'] ?? 0);
                                        } else {
                                            $tanggalBayar = $sourceData['tanggal_bayar'] ?? null;
                                            $tanggalHabis = $sourceData['tanggal_habis'] ?? null;
                                            $totalItems   = collect($gpsItems)->sum(fn($i) => $i['biaya_sewa'] ?? 0);
                                        }
                                    @endphp
                                    <tr id="{{ $poRowId }}" class="hidden">
                                        <td colspan="9" class="px-0 py-0">
                                            @if($isServicePart || $isServiceIncident)
                                            {{-- ── SERVICE PART / SERVICE INCIDENT EXPAND ── --}}
                                            @php $expandColor = $isServiceIncident ? 'red' : 'orange'; @endphp
                                            <div class="bg-{{ $expandColor }}-50/30 border-t border-{{ $expandColor }}-100 px-6 py-4">
                                                <div class="flex items-center gap-2 mb-3">
                                                    <i class="{{ $isServiceIncident ? 'fa fa-exclamation-triangle' : 'fa fa-tools' }} text-{{ $expandColor }}-600 text-xs"></i>
                                                    <span class="text-[11px] font-bold text-{{ $expandColor }}-700 uppercase tracking-wide">
                                                        Detail — {{ $isServiceIncident ? 'Service Incident' : 'Service Part' }}
                                                    </span>
                                                </div>
                                                {{-- Info kendaraan & service --}}
                                                <div class="grid grid-cols-4 gap-4 mb-3 text-xs">
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Kendaraan</p>
                                                        <p class="font-semibold text-gray-800">{{ $kendaraan ? $kendaraan->nopol . ' — ' . $kendaraan->merk : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Tgl Service</p>
                                                        <p class="text-gray-700">{{ isset($sourceData['tanggal_service']) ? \Carbon\Carbon::parse($sourceData['tanggal_service'])->format('d M Y') : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">KM / Keluhan</p>
                                                        <p class="text-gray-700">{{ $sourceData['kilometer'] ?? '-' }} {{ $sourceData['keluhan'] ? '— ' . \Illuminate\Support\Str::limit($sourceData['keluhan'], 30) : '' }}</p>
                                                    </div>
                                                    @if($isServiceIncident && !empty($sourceData['keterangan']))
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Keterangan</p>
                                                        <p class="font-mono text-[11px] text-red-700 bg-red-50 px-1.5 py-0.5 rounded">{{ $sourceData['keterangan'] }}</p>
                                                    </div>
                                                    @endif
                                                </div>

                                                {{-- Lampiran per-part (bukti dari form incident/service part) --}}
                                                @php
                                                    $siAllLampiran = [];
                                                    foreach (($sourceData['temp_files']['parts'] ?? []) as $pIdx2 => $pFiles) {
                                                        foreach (($pFiles['bukti'] ?? []) as $pf) {
                                                            $partName = $parts[$pIdx2]['nama_part'] ?? 'Part '.($pIdx2+1);
                                                            $siAllLampiran[] = array_merge($pf, ['_part' => $partName]);
                                                        }
                                                    }
                                                    // Lampiran umum
                                                    foreach (($sourceData['temp_files']['attachments'] ?? []) as $af) {
                                                        $siAllLampiran[] = $af;
                                                    }
                                                @endphp
                                                @if(!empty($siAllLampiran))
                                                <div class="mb-3 flex flex-wrap gap-1.5">
                                                    @foreach($siAllLampiran as $lf)
                                                        @php
                                                            $lfPath  = $lf['path'] ?? '';
                                                            $lfName  = $lf['original_name'] ?? basename($lfPath);
                                                            $lfExt   = strtolower($lf['extension'] ?? pathinfo($lfPath, PATHINFO_EXTENSION));
                                                            $lfIsImg = in_array($lfExt, ['jpg','jpeg','png','webp','gif']);
                                                            $lfColor = $isServiceIncident ? 'red' : 'orange';
                                                            $lfIcon  = $lfIsImg ? 'fa-image text-' . $lfColor . '-400' : ($lfExt === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                                                            $lfUrl   = $lfPath ? \Illuminate\Support\Facades\Storage::disk('public')->url($lfPath) : null;
                                                        @endphp
                                                        @if($lfUrl)
                                                            <a href="{{ $lfUrl }}" target="_blank"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium bg-white border border-{{ $lfColor }}-200 text-{{ $lfColor }}-600 hover:bg-{{ $lfColor }}-50 rounded-lg truncate max-w-[160px]"
                                                                title="{{ $lfName }}{{ isset($lf['_part']) ? ' ('.$lf['_part'].')' : '' }}">
                                                                <i class="fa {{ $lfIcon }} text-[9px]"></i>
                                                                <span class="truncate">{{ Str::limit($lfName, 20) }}</span>
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                @endif
                                                {{-- Tabel parts --}}
                                                <div class="bg-white rounded-xl border border-{{ $expandColor }}-100 overflow-hidden">
                                                    <table class="w-full text-xs">
                                                        <thead>
                                                            <tr class="bg-{{ $expandColor }}-50 border-b border-{{ $expandColor }}-100">
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">#</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Nama Part</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Kategori</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Kondisi</th>
                                                                @if($isServiceIncident)
                                                                    <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Supplier</th>
                                                                @endif
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Bank</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">No. Rekening</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Atas Nama</th>
                                                                <th class="text-center px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Lampiran</th>
                                                                <th class="text-right px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Biaya</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($parts as $pIdx => $part)
                                                                @php
                                                                    $category = isset($part['category_id']) ? \App\Models\ServiceCategory::find($part['category_id']) : null;
                                                                    $catNama  = $category ? $category->nama : ($part['nama_category_baru'] ?? '-');
                                                                    $supplier = isset($part['supplier_id']) ? \App\Models\Supplier::find($part['supplier_id']) : null;
                                                                @endphp
                                                                <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/40">
                                                                    <td class="px-3 py-2 text-gray-400">{{ $pIdx + 1 }}</td>
                                                                    <td class="px-3 py-2">
                                                                        <span class="font-semibold text-gray-800">{{ $part['nama_part'] ?? '-' }}</span>
                                                                        @if(!empty($part['part_number']) && $part['part_number'] !== '-')
                                                                            <span class="text-gray-400 ml-1 font-mono text-[10px]">({{ $part['part_number'] }})</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-3 py-2">
                                                                        <span class="bg-{{ $expandColor }}-100 text-{{ $expandColor }}-700 px-1.5 py-0.5 rounded text-[10px] font-semibold">{{ $catNama }}</span>
                                                                    </td>
                                                                    <td class="px-3 py-2 text-gray-600">{{ $part['kondisi'] ?? '-' }}</td>
                                                                    @if($isServiceIncident)
                                                                        <td class="px-3 py-2 text-gray-600">{{ $supplier?->nama_supplier ?? '-' }}</td>
                                                                    @endif
                                                                    <td class="px-3 py-2 text-gray-600">{{ $part['nama_bank'] ?? '-' }}</td>
                                                                    <td class="px-3 py-2 font-mono text-gray-600">{{ $part['no_rekening'] ?? '-' }}</td>
                                                                    <td class="px-3 py-2 text-gray-600">{{ $part['nama_rekening'] ?? '-' }}</td>
                                                                    <td class="px-3 py-2 text-center">
                                                                        @php
                                                                            $partLampiran = $sourceData['temp_files']['parts'][$pIdx]['bukti'] ?? [];
                                                                        @endphp
                                                                        @if(!empty($partLampiran))
                                                                            <div class="flex flex-col gap-0.5">
                                                                            @foreach($partLampiran as $pf)
                                                                                @php
                                                                                    $pfUrl = isset($pf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($pf['path']) : null;
                                                                                @endphp
                                                                                @if($pfUrl)
                                                                                    <a href="{{ $pfUrl }}" target="_blank"
                                                                                        class="text-[11px] text-blue-600 hover:underline truncate max-w-[120px]"
                                                                                        title="{{ $pf['original_name'] ?? '' }}">{{ $pf['original_name'] ?? 'file' }}</a>
                                                                                @endif
                                                                            @endforeach
                                                                            </div>
                                                                        @else
                                                                            <span class="text-gray-300 text-[10px]">—</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-3 py-2 text-right font-bold {{ $statusFilter === 'Ditolak' ? 'text-red-500' : 'text-emerald-600' }}">Rp {{ number_format($part['biaya'] ?? 0, 0, ',', '.') }}</td>
                                                                </tr>
                                                            @endforeach
                                                            <tr class="border-t-2 border-{{ $expandColor }}-200 bg-{{ $expandColor }}-50/50">
                                                                <td colspan="{{ $isServiceIncident ? 8 : 8 }}" class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Total</td>
                                                                <td class="px-3 py-2 text-right text-sm font-bold {{ $statusFilter === 'Ditolak' ? 'text-red-500' : 'text-emerald-600' }}">Rp {{ number_format($totalItems, 0, ',', '.') }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            @elseif($isServiceAsuransi)
                                            {{-- ── SERVICE ASURANSI EXPAND ── --}}
                                            <div class="bg-blue-50/30 border-t border-blue-100 px-6 py-4">
                                                <div class="flex items-center gap-2 mb-3">
                                                    <i class="fa fa-shield-alt text-blue-600 text-xs"></i>
                                                    <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wide">Detail — Service Asuransi</span>
                                                </div>
                                                <div class="grid grid-cols-4 gap-4 mb-3 text-xs">
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Kendaraan</p>
                                                        <p class="font-semibold text-gray-800">{{ $kendaraan ? $kendaraan->nopol . ' — ' . $kendaraan->merk : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Nama Asuransi</p>
                                                        <p class="text-gray-700">{{ $sourceData['nama_asuransi'] ?? '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Tgl Service</p>
                                                        <p class="text-gray-700">{{ isset($sourceData['tanggal_service']) ? \Carbon\Carbon::parse($sourceData['tanggal_service'])->format('d M Y') : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Keterangan</p>
                                                        <p class="font-mono text-[11px] text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded">{{ $sourceData['keterangan'] ?? ($po->keterangan ?? '-') }}</p>
                                                    </div>
                                                </div>

                                                {{-- Lampiran PO-level (temp_files) --}}
                                                @php
                                                    $saLampiran = $sourceData['temp_files']['attachments'] ?? [];
                                                    // Kumpulkan semua lampiran kejadian ke satu array flat
                                                    $saKejLampiran = [];
                                                    foreach (($sourceData['kejadians'] ?? []) as $kjIdx2 => $kj2) {
                                                        foreach (($kj2['lampiran'] ?? []) as $lf) {
                                                            $saKejLampiran[] = array_merge($lf, ['_kej' => $kj2['nama_kejadian'] ?? '#'.($kjIdx2+1)]);
                                                        }
                                                        // Juga cek temp_files per kejadian
                                                        $tempKejFiles = $sourceData['temp_files']['kejadians'][$kjIdx2] ?? [];
                                                        foreach ($tempKejFiles as $lf) {
                                                            $saKejLampiran[] = array_merge($lf, ['_kej' => $kj2['nama_kejadian'] ?? '#'.($kjIdx2+1)]);
                                                        }
                                                    }
                                                    $allSaLampiran = array_merge($saLampiran, $saKejLampiran);
                                                @endphp
                                                @if(!empty($allSaLampiran))
                                                    <div class="mb-3 flex flex-wrap gap-1.5">
                                                        @foreach($allSaLampiran as $lf)
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
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-lg truncate max-w-[160px]"
                                                                    title="{{ $lfName }}{{ isset($lf['_kej']) ? ' ('. $lf['_kej'].')' : '' }}">
                                                                    <i class="fa {{ $lfIcon }} text-[9px]"></i>
                                                                    <span class="truncate">{{ Str::limit($lfName, 20) }}</span>
                                                                </a>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <div class="bg-white rounded-xl border border-blue-100 overflow-hidden">
                                                    <table class="w-full text-xs">
                                                        <thead>
                                                            <tr class="bg-blue-50 border-b border-blue-100">
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">#</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Nama Kejadian</th>
                                                                <th class="text-center px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Lampiran</th>
                                                                <th class="text-right px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Biaya</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($asuransiKejadians as $kjIdx => $kj)
                                                                <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/40">
                                                                    <td class="px-3 py-2 text-gray-400">{{ $kjIdx + 1 }}</td>
                                                                    <td class="px-3 py-2 font-semibold text-gray-800">{{ $kj['nama_kejadian'] ?? '-' }}</td>
                                                                    <td class="px-3 py-2 text-center">
                                                                        @php $kjLampiran = $kj['lampiran'] ?? []; @endphp
                                                                        @if(!empty($kjLampiran))
                                                                            <div class="flex flex-col gap-0.5 items-center">
                                                                            @foreach($kjLampiran as $kjf)
                                                                                @php
                                                                                    $kjUrl = isset($kjf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($kjf['path']) : null;
                                                                                    $kjExt = strtolower($kjf['extension'] ?? pathinfo($kjf['path'] ?? '', PATHINFO_EXTENSION));
                                                                                    $kjIcon = in_array($kjExt, ['jpg','jpeg','png','gif']) ? 'fa-image text-blue-400'
                                                                                        : ($kjExt === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                                                                                @endphp
                                                                                @if($kjUrl)
                                                                                    <a href="{{ $kjUrl }}" target="_blank"
                                                                                        class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline truncate max-w-[140px]"
                                                                                        title="{{ $kjf['original_name'] ?? '' }}">
                                                                                        <i class="fa {{ $kjIcon }} text-[10px]"></i>
                                                                                        <span class="truncate">{{ $kjf['original_name'] ?? 'file' }}</span>
                                                                                    </a>
                                                                                @endif
                                                                            @endforeach
                                                                            </div>
                                                                        @else
                                                                            <span class="text-gray-300 text-[10px]">—</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-3 py-2 text-right font-bold text-emerald-600">Rp {{ number_format($kj['biaya'] ?? 0, 0, ',', '.') }}</td>
                                                                </tr>
                                                            @endforeach
                                                            <tr class="border-t-2 border-blue-200 bg-blue-50/50">
                                                                <td colspan="3" class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Total</td>
                                                                <td class="px-3 py-2 text-right text-sm font-bold text-emerald-600">Rp {{ number_format($totalItems, 0, ',', '.') }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            @else
                                            {{-- ── GPS EXPAND ── --}}
                                            <div class="bg-green-50/30 border-t border-green-100 px-6 py-4">
                                                <div class="flex items-center justify-between mb-3">
                                                    <div class="flex items-center gap-2">
                                                        <i class="fa fa-calendar-check text-green-600 text-xs"></i>
                                                        <span class="text-[11px] font-bold text-green-700 uppercase tracking-wide">Detail — GPS Kendaraan</span>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-3 gap-4 mb-3 text-xs">
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Kendaraan</p>
                                                        <p class="font-semibold text-gray-800">{{ $kendaraan ? $kendaraan->nopol . ' — ' . $kendaraan->merk : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Tgl Bayar</p>
                                                        <p class="text-gray-700">{{ $tanggalBayar ? \Carbon\Carbon::parse($tanggalBayar)->format('d M Y') : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Berlaku s/d</p>
                                                        <p class="text-gray-700">{{ $tanggalHabis ? \Carbon\Carbon::parse($tanggalHabis)->format('d M Y') : '-' }}</p>
                                                    </div>
                                                </div>
                                                <div class="bg-white rounded-xl border border-green-100 overflow-hidden">
                                                    <table class="w-full text-xs">
                                                        <thead>
                                                            <tr class="bg-green-50 border-b border-green-100">
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">#</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Type GPS</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Tgl Bayar</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Berlaku s/d</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Bank</th>
                                                                <th class="text-left px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">No. Rekening</th>
                                                                <th class="text-center px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Lampiran</th>
                                                                <th class="text-right px-3 py-2 text-[10px] font-semibold text-gray-500 uppercase">Biaya Sewa</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($gpsItems as $giIdx => $gItem)
                                                                @php
                                                                    $gpsModel = isset($gItem['gps_id']) ? \App\Models\Gps::find($gItem['gps_id']) : null;
                                                                @endphp
                                                                <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/40">
                                                                    <td class="px-3 py-2 text-gray-400">{{ $giIdx + 1 }}</td>
                                                                    <td class="px-3 py-2">
                                                                        <span class="font-semibold text-gray-800">{{ $gpsModel->nama_gps ?? '-' }}</span>
                                                                        <span class="text-gray-400 ml-1">({{ $gItem['type'] ?? '-' }})</span>
                                                                    </td>
                                                                    <td class="px-3 py-2 text-gray-600">{{ $tanggalBayar ? \Carbon\Carbon::parse($tanggalBayar)->format('d M Y') : '-' }}</td>
                                                                    <td class="px-3 py-2 text-gray-600">{{ $tanggalHabis ? \Carbon\Carbon::parse($tanggalHabis)->format('d M Y') : '-' }}</td>
                                                                    <td class="px-3 py-2 text-gray-600">{{ $gItem['nama_bank'] ?? '-' }}</td>
                                                                    <td class="px-3 py-2 font-mono text-gray-600">{{ $gItem['no_rekening'] ?? '-' }}</td>
                                                                    <td class="px-3 py-2 text-center">
                                                                        @php
                                                                            $gpsLampiran = $sourceData['temp_files']['gps_items'][$giIdx]['lampiran'] ?? [];
                                                                        @endphp
                                                                        @if(!empty($gpsLampiran))
                                                                            <div class="flex flex-col gap-0.5">
                                                                            @foreach($gpsLampiran as $gf)
                                                                                @php
                                                                                    $gfUrl  = isset($gf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($gf['path']) : null;
                                                                                @endphp
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
                                                                    <td class="px-3 py-2 text-right font-bold {{ $statusFilter === 'Ditolak' ? 'text-red-500' : 'text-emerald-600' }}">Rp {{ number_format($gItem['biaya_sewa'] ?? 0, 0, ',', '.') }}</td>
                                                                </tr>
                                                            @endforeach
                                                            <tr class="border-t-2 border-green-200 bg-green-50/50">
                                                                <td colspan="7" class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Total</td>
                                                                <td class="px-3 py-2 text-right text-sm font-bold {{ $statusFilter === 'Ditolak' ? 'text-red-500' : 'text-emerald-600' }}">Rp {{ number_format($totalItems, 0, ',', '.') }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            @endif
                                            @if(in_array($po->source_type, ['pajak', 'pajak_perpanjang', 'asuransi_kendaraan', 'asuransi_kendaraan_perpanjang', 'kir', 'kir_perpanjang', 'stnk']))
                                            {{-- ── LAMPIRAN PAJAK / ASURANSI / KIR ── --}}
                                            @php
                                                // Ambil dari temp_files (PO baru)
                                                $lampiranFiles = $sourceData['temp_files']['attachments'] ?? [];

                                                // Fallback: ambil dari tabel attachments DB (PO lama)
                                                if (empty($lampiranFiles)) {
                                                    $existingRecordId = $sourceData['existing_record_id'] ?? null;
                                                    $relType = match($po->source_type) {
                                                        'pajak', 'pajak_perpanjang'                                   => 'pajak',
                                                        'asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'         => 'asuransi',
                                                        'kir', 'kir_perpanjang'                                       => 'kir',
                                                        default                                                       => null,
                                                    };
                                                    if ($existingRecordId && $relType) {
                                                        $dbAttachments = \App\Models\Attachment::where('relation_type', $relType)
                                                            ->where('relation_id', $existingRecordId)
                                                            ->get();
                                                        foreach ($dbAttachments as $att) {
                                                            $lampiranFiles[] = [
                                                                'original_name' => $att->file_name,
                                                                'path'          => null,
                                                                'public_url'    => asset($att->file_path),
                                                            ];
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <div class="bg-blue-50/30 border-t border-blue-100 px-6 py-4">
                                                <div class="flex items-center gap-2 mb-3">
                                                    <i class="fa fa-paperclip text-blue-600 text-xs"></i>
                                                    <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wide">Lampiran</span>
                                                </div>
                                                @if(!empty($lampiranFiles))
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($lampiranFiles as $lf)
                                                            @php
                                                                $lfUrl = $lf['public_url']
                                                                    ?? (isset($lf['path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($lf['path']) : null);
                                                            @endphp
                                                            @if($lfUrl)
                                                                <a href="{{ $lfUrl }}" target="_blank"
                                                                    class="text-xs text-blue-600 hover:underline truncate max-w-[200px]"
                                                                    title="{{ $lf['original_name'] ?? '' }}">
                                                                    {{ $lf['original_name'] ?? 'file' }}
                                                                </a>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-xs text-gray-400 italic">Tidak ada lampiran</p>
                                                @endif
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif {{-- end hasItems --}}
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif

        @if($data->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $data->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="detailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Purchase Order Detail</h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6" id="detailContent">
            <div class="flex items-center justify-center py-12">
                <i class="fa fa-spinner fa-spin text-2xl text-gray-400"></i>
            </div>
        </div>
    </div>
</div>

{{-- MODAL APPROVE (per-item GPS) --}}
<div id="approveModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Approve Purchase Order</h3>
                <p class="text-sm text-gray-500 mt-0.5">No PO: <span id="approvePoId" class="font-mono font-semibold text-blue-600"></span></p>
            </div>
            <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div id="approveModalLoading" class="flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
                <p class="text-sm">Memuat data item...</p>
            </div>
        </div>
        <div id="approveModalContent" class="hidden flex-1 overflow-y-auto">
            <div id="approveKendaraanInfo" class="px-6 pt-4 pb-2"></div>
            <div class="px-6 pb-2">
                <div class="flex items-center justify-between mb-2">
                    <p id="approveItemListLabel" class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <i class="fa fa-list-ul mr-1 text-green-500"></i> Item GPS — Centang yang ingin disetujui
                    </p>
                    <div class="flex gap-2">
                        <button type="button" onclick="approveSelectAll(true)"
                            class="text-[11px] text-green-600 font-medium px-2 py-0.5 bg-green-50 rounded-md border border-green-200 hover:bg-green-100">
                            Pilih Semua
                        </button>
                        <button type="button" onclick="approveSelectAll(false)"
                            class="text-[11px] text-gray-500 font-medium px-2 py-0.5 bg-gray-50 rounded-md border border-gray-200 hover:bg-gray-100">
                            Hapus Pilihan
                        </button>
                    </div>
                </div>
                <div id="approveItemList" class="space-y-2"></div>
            </div>
            <div class="px-6 pb-4 pt-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Catatan Approval <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="approveCatatan" rows="2" placeholder="Catatan untuk approval ini..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-green-400"></textarea>
            </div>
            <div id="approveSummary" class="mx-6 mb-4 px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-gray-600 hidden">
                <span id="approveSummaryText"></span>
            </div>
        </div>
        <div id="approveModalFooter" class="hidden border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
            <button type="button" onclick="closeApproveModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" id="approveSubmitBtn" onclick="submitApproveItems()"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-check"></i> Konfirmasi Approval
            </button>
        </div>
    </div>
</div>

{{-- MODAL REJECT (per-item) --}}
<div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Tolak Purchase Order</h3>
                <p class="text-sm text-gray-500 mt-0.5">No PO: <span id="rejectPoId" class="font-mono font-semibold text-red-600"></span></p>
            </div>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div id="rejectModalLoading" class="flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
                <p class="text-sm">Memuat data item...</p>
            </div>
        </div>
        <div id="rejectModalContent" class="hidden flex-1 overflow-y-auto">
            <div id="rejectKendaraanInfo" class="px-6 pt-4 pb-2"></div>
            <div class="px-6 pb-2">
                <div class="flex items-center justify-between mb-2">
                    <p id="rejectItemListLabel" class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <i class="fa fa-list-ul mr-1 text-red-500"></i> Item — Centang yang ingin ditolak
                    </p>
                    <div class="flex gap-2">
                        <button type="button" onclick="rejectSelectAll(true)"
                            class="text-[11px] text-red-600 font-medium px-2 py-0.5 bg-red-50 rounded-md border border-red-200 hover:bg-red-100">
                            Tolak Semua
                        </button>
                        <button type="button" onclick="rejectSelectAll(false)"
                            class="text-[11px] text-gray-500 font-medium px-2 py-0.5 bg-gray-50 rounded-md border border-gray-200 hover:bg-gray-100">
                            Hapus Pilihan
                        </button>
                    </div>
                </div>
                <div id="rejectItemList" class="space-y-2"></div>
            </div>
            <div class="px-6 pb-4 pt-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Catatan Umum <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="rejectCatatan" rows="2" placeholder="Catatan umum untuk penolakan ini..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-400"></textarea>
            </div>
            <div id="rejectSummary" class="mx-6 mb-4 px-4 py-3 bg-red-50 rounded-xl border border-red-200 text-xs text-red-700 hidden">
                <span id="rejectSummaryText"></span>
            </div>
        </div>
        <div id="rejectModalFooter" class="hidden border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
            <button type="button" onclick="closeRejectModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" id="rejectSubmitBtn" onclick="submitRejectItems()"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-times"></i> Konfirmasi Penolakan
            </button>
        </div>
    </div>
</div>

{{-- MODAL RESUBMIT GPS --}}
<div id="resubmitModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Ajukan Ulang GPS</h3>
                <p class="text-sm text-gray-500 mt-0.5">PO: <span id="resubmitPoNumber" class="font-mono font-semibold text-amber-600"></span></p>
            </div>
            <button onclick="closeResubmitModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div id="resubmitLoading" class="flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
                <p class="text-sm">Memuat data...</p>
            </div>
        </div>
        <form id="resubmitForm" method="POST" enctype="multipart/form-data"
              action="{{ route('gps-kendaraan.store') }}"
              class="hidden flex-1 overflow-y-auto flex flex-col">
            @csrf
            <input type="hidden" name="edit_purchase_order" id="resubmitPoId">
            <input type="hidden" name="kendaraan_id" id="resubmitKendaraanId">
            <input type="hidden" name="tanggal_bayar" id="resubmitTanggalBayar">
            <input type="hidden" name="tanggal_habis" id="resubmitTanggalHabis">
            <div class="flex-1 overflow-y-auto px-6 pt-4 pb-2 space-y-4">
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm">
                    <p class="font-semibold text-gray-800" id="resubmitKendaraanInfo">-</p>
                    <p class="text-xs text-amber-600 mt-0.5" id="resubmitCatatan"></p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">GPS Items</p>
                    <div id="resubmitItemsContainer" class="space-y-3"></div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan</label>
                    <textarea name="keterangan" id="resubmitKeterangan" rows="2"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-amber-100 focus:border-amber-400"
                        placeholder="Keterangan tambahan..."></textarea>
                </div>
            </div>
            <div class="border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
                <button type="button" onclick="closeResubmitModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl py-2.5">
                    <i class="fa fa-rotate-right"></i> Ajukan Ulang
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

{{-- MODAL RESUBMIT PAJAK / ASURANSI_KENDARAAN / KIR --}}
<div id="resubmitSimpleModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-lg font-bold text-gray-800" id="resubmitSimpleTitle">Ajukan Ulang</h3>
                <p class="text-sm text-gray-500 mt-0.5">PO: <span id="resubmitSimplePoNumber" class="font-mono font-semibold text-amber-600"></span></p>
            </div>
            <button onclick="closeResubmitSimpleModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>

        {{-- Loading state --}}
        <div id="resubmitSimpleLoading" class="flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
                <p class="text-sm">Memuat data...</p>
            </div>
        </div>

        {{-- Form area (rendered dynamically) --}}
        <div id="resubmitSimpleBody" class="hidden flex-1 overflow-y-auto flex flex-col">
            {{-- Info kendaraan + alasan ditolak --}}
            <div class="px-6 pt-4 pb-2 space-y-4">
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm">
                    <p class="font-semibold text-gray-800" id="resubmitSimpleKendaraan">-</p>
                    <p class="text-xs text-amber-700 mt-0.5" id="resubmitSimpleCatatan"></p>
                </div>
            </div>

            {{-- Field container — diisi JS --}}
            <div id="resubmitSimpleFields" class="flex-1 overflow-y-auto px-6 pb-2 space-y-3"></div>

            <div class="border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
                <button type="button" onclick="closeResubmitSimpleModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="resubmitSimpleSubmitBtn" onclick="submitResubmitSimple()"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-rotate-right"></i> Ajukan Ulang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: APPROVE SIMPLE (non-GPS) --}}
<div id="approveSimpleModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-sm">
                        <i class="fa fa-check"></i>
                    </span>
                    Approve Purchase Order
                </h2>
                <p id="approveSimpleSubtitle" class="text-xs text-gray-500 mt-1 ml-10"></p>
            </div>
            <button onclick="closeApproveSimpleModal()" class="text-gray-400 hover:text-red-500 text-lg transition-colors">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700">
                <i class="fa fa-info-circle mr-1.5"></i>
                Setelah disetujui, pembayaran akan otomatis dibuat dan masuk ke daftar Pembayaran.
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea id="approveSimpleCatatan" rows="3" placeholder="Catatan persetujuan..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-100 focus:border-green-400"></textarea>
            </div>
        </div>
        <div class="border-t border-gray-100 px-6 py-4 flex gap-2">
            <button type="button" onclick="closeApproveSimpleModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" id="approveSimpleSubmitBtn" onclick="submitApproveSimple()"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-check"></i> Konfirmasi Approve
            </button>
        </div>
    </div>
</div>

{{-- MODAL: REJECT SIMPLE (non-GPS) --}}
<div id="rejectSimpleModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-sm">
                        <i class="fa fa-times"></i>
                    </span>
                    Tolak Purchase Order
                </h2>
                <p id="rejectSimpleSubtitle" class="text-xs text-gray-500 mt-1 ml-10"></p>
            </div>
            <button onclick="closeRejectSimpleModal()" class="text-gray-400 hover:text-red-500 text-lg transition-colors">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea id="rejectSimpleCatatan" rows="4" placeholder="Tuliskan alasan penolakan..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-400"></textarea>
            </div>
        </div>
        <div class="border-t border-gray-100 px-6 py-4 flex gap-2">
            <button type="button" onclick="closeRejectSimpleModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" id="rejectSimpleSubmitBtn" onclick="submitRejectSimple()"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-times-circle"></i> Konfirmasi Tolak
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── GROUPED ACCORDION ─────────────────────────────────────────
function togglePoGroup(idx) {
    const body    = document.getElementById('po-grp-body-' + idx);
    const chevron = document.getElementById('po-grp-chevron-' + idx);
    if (!body) return;
    const isOpen = !body.classList.contains('hidden');
    body.classList.toggle('hidden', isOpen);
    if (chevron) chevron.style.transform = isOpen ? '' : 'rotate(90deg)';
}

// ── ROW EXPAND (detail items) ──────────────────────────────────
function togglePoRow(rowId) {
    const row     = document.getElementById(rowId);
    const chevron = document.getElementById('chv-' + rowId);
    if (!row) return;
    const isOpen = !row.classList.contains('hidden');
    row.classList.toggle('hidden', isOpen);
    if (chevron) chevron.style.transform = isOpen ? '' : 'rotate(90deg)';
}

// Auto-buka grup pertama saat halaman load
document.addEventListener('DOMContentLoaded', function () {
    // Buka semua grup jika hanya 1, atau buka grup pertama
    const firstBody = document.getElementById('po-grp-body-0');
    const firstChev = document.getElementById('po-grp-chevron-0');
    if (firstBody) {
        firstBody.classList.remove('hidden');
        if (firstChev) firstChev.style.transform = 'rotate(90deg)';
    }
});

// ── CHART ─────────────────────────────────────────────────────
const chartManager = new ChartManager();
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'poChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
            };
            if (!chartManager.hasChart('poBarChart')) {
                initPoCharts(filters);
            } else {
                updatePoCharts(filters);
            }
        }
    });
});
async function initPoCharts(filters) {
    try {
        await chartManager.initChartsFromAPI('purchase-order', {
            pie: 'poPieChart', bar: 'poBarChart', line: 'poLineChart'
        }, filters, { accentLine: true });
    } catch (e) { console.error('Error loading PO charts:', e); }
}
async function updatePoCharts(filters) {
    try {
        const isScrollable = filters.filter_type === 'custom';
        await chartManager.updateChartsFromAPI('purchase-order', {
            pie: 'poPieChart', bar: 'poBarChart', line: 'poLineChart'
        }, filters, { scrollable: isScrollable, accentLine: true }, { scrollable: isScrollable });
    } catch (e) { console.error('Error updating PO charts:', e); }
}

// ── DETAIL MODAL ──────────────────────────────────────────────
function viewDetail(poId) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    modal.classList.remove('hidden'); modal.classList.add('flex');
    content.innerHTML = '<div class="flex items-center justify-center py-12"><i class="fa fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
    fetch('/admin/purchase-order/' + poId + '/detail')
        .then(r => r.json())
        .then(data => { content.innerHTML = data.success ? buildDetailContent(data) : '<p class="text-red-600 text-center py-8">' + (data.message || 'Error') + '</p>'; })
        .catch(err => { content.innerHTML = '<p class="text-red-600 text-center py-8">' + err.message + '</p>'; });
}
function buildDetailContent(data) {
    const po = data.po, details = data.details;
    let html = '<div class="space-y-5">'
        + '<div class="bg-gray-50 rounded-xl p-4 grid grid-cols-2 gap-4">'
        + '<div><p class="text-xs text-gray-500">PO Number</p><p class="font-mono font-bold text-indigo-700">' + po.po_id + '</p></div>'
        + '<div><p class="text-xs text-gray-500">Jenis</p><p class="font-semibold capitalize">' + (po.source_type || '').replace(/_/g, ' ') + '</p></div>'
        + '<div><p class="text-xs text-gray-500">Vendor</p><p class="font-medium">' + (po.vendor || '-') + '</p></div>'
        + '<div><p class="text-xs text-gray-500">Tanggal PO</p><p class="font-medium">' + (po.tanggal_po || '-') + '</p></div>'
        + '<div><p class="text-xs text-gray-500">Total Items</p><p class="font-medium">' + po.total_barang + '</p></div>'
        + '<div><p class="text-xs text-gray-500">Total Harga</p><p class="font-bold text-lg text-indigo-600">Rp ' + Number(po.total_harga).toLocaleString('id-ID') + '</p></div>'
        + '</div>'
        + (po.keterangan ? '<div class="mt-3 px-4 py-2.5 bg-gray-50 rounded-xl border border-gray-100"><p class="text-xs text-gray-400 mb-0.5">Keterangan</p><p class="text-sm text-gray-700">' + po.keterangan + '</p></div>' : '')
        + '<div class="flex items-center gap-2"><span class="text-sm text-gray-600">Status:</span>' + getStatusBadge(po.status) + '</div>';
    if (details.type === 'gps') {
        const k = details.kendaraan || {};
        html += '<div><h4 class="font-semibold text-gray-800 mb-2">Kendaraan</h4>'
            + '<div class="bg-blue-50 rounded-xl p-3 text-sm space-y-1">'
            + '<p><span class="text-gray-500">Nopol:</span> <b>' + (k.nopol || '-') + '</b></p>'
            + '<p><span class="text-gray-500">Merk:</span> ' + (k.merk || '-') + '</p>'
            + '<p><span class="text-gray-500">Tgl Bayar:</span> ' + (details.tanggal_bayar || '-') + '</p>'
            + '<p><span class="text-gray-500">Berlaku s/d:</span> ' + (details.tanggal_habis || '-') + '</p>'
            + '</div></div>';
        html += '<div><h4 class="font-semibold text-gray-800 mb-2">GPS Items (' + details.items.length + ')</h4><div class="space-y-2">';
        details.items.forEach(function(item) {
            const lampiran = item.lampiran || [];
            let lampiranHtml = lampiran.length
                ? '<div class="mt-2 pt-2 border-t border-gray-100"><p class="text-[10px] font-semibold text-gray-400 uppercase mb-1"><i class="fa fa-paperclip mr-1"></i>Lampiran (' + lampiran.length + ')</p><div class="flex flex-wrap gap-1.5">'
                    + lampiran.map(function(att) {
                        const ext = (att.file_type || '').toLowerCase();
                        const icon = ['jpg','jpeg','png'].includes(ext) ? 'fa-image text-blue-400' : (ext === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                        return '<a href="' + att.file_path + '" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 bg-gray-50 border border-gray-200 rounded-lg text-[11px] text-gray-600 hover:bg-blue-50 hover:text-blue-700 max-w-[180px]" title="' + att.file_name + '"><i class="fa ' + icon + ' text-[10px]"></i><span class="truncate">' + att.file_name + '</span></a>';
                    }).join('') + '</div></div>'
                : '<p class="mt-1 text-[11px] text-gray-300 italic">Tidak ada lampiran</p>';
            html += '<div class="border border-gray-200 rounded-xl p-3">'
                + '<div class="flex items-start justify-between mb-1"><div><p class="font-semibold text-gray-800">' + (item.gps_name || '-') + '</p><p class="text-xs text-gray-500">Type: ' + (item.type || '-') + '</p></div>'
                + '<p class="font-bold text-indigo-600">Rp ' + Number(item.biaya_sewa || 0).toLocaleString('id-ID') + '</p></div>'
                + '<div class="grid grid-cols-3 gap-2 text-xs text-gray-600"><div><span class="text-gray-400">Bank:</span> ' + (item.nama_bank || '-') + '</div><div><span class="text-gray-400">Rek:</span> ' + (item.no_rekening || '-') + '</div><div><span class="text-gray-400">A/n:</span> ' + (item.nama_pemilik || '-') + '</div></div>'
                + lampiranHtml + '</div>';
        });
        html += '</div></div>';
    }

    if (details.type === 'service_part') {
        const k = details.kendaraan || {};
        html += '<div><h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2"><i class="fa fa-tools text-orange-500 text-sm"></i> Kendaraan & Service</h4>'
            + '<div class="bg-orange-50 rounded-xl p-3 text-sm grid grid-cols-2 gap-2">'
            + '<div><span class="text-gray-500">Nopol:</span> <b>' + (k.nopol || '-') + '</b> <span class="text-gray-400">' + (k.merk || '') + '</span></div>'
            + '<div><span class="text-gray-500">Tgl Service:</span> ' + (details.tanggal_service || '-') + '</div>'
            + '<div><span class="text-gray-500">KM:</span> ' + (details.kilometer || '-') + '</div>'
            + (details.keluhan ? '<div><span class="text-gray-500">Keluhan:</span> ' + details.keluhan + '</div>' : '')
            + '</div></div>';
        const items = details.items || [];
        html += '<div><h4 class="font-semibold text-gray-800 mb-2">Service Parts (' + items.length + ')</h4><div class="space-y-2">';
        let totalBiaya = 0;
        items.forEach(function(item, idx) {
            totalBiaya += Number(item.biaya || 0);
            html += '<div class="border border-orange-200 rounded-xl p-3 bg-orange-50/20">'
                + '<div class="flex items-start justify-between mb-1.5">'
                + '<div><p class="font-semibold text-gray-800">' + (item.nama_part || '-') + '</p>'
                + '<div class="flex items-center gap-1.5 mt-0.5 flex-wrap">'
                + (item.category_nama ? '<span class="bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded text-[10px] font-semibold">' + item.category_nama + '</span>' : '')
                + (item.part_number && item.part_number !== '-' ? '<span class="font-mono text-gray-400 text-[10px]">' + item.part_number + '</span>' : '')
                + (item.kondisi && item.kondisi !== '-' ? '<span class="text-gray-400 text-[10px]">Kondisi: ' + item.kondisi + '</span>' : '')
                + (item.posisi && item.posisi !== '-' ? '<span class="text-gray-400 text-[10px]">Posisi: ' + item.posisi + '</span>' : '')
                + '</div></div>'
                + '<p class="font-bold text-emerald-600 text-sm">Rp ' + Number(item.biaya || 0).toLocaleString('id-ID') + '</p></div>'
                + '<div class="grid grid-cols-3 gap-2 text-xs text-gray-600 border-t border-orange-100 pt-2 mt-1">'
                + '<div><span class="text-gray-400">Bank:</span> ' + (item.nama_bank || '-') + '</div>'
                + '<div><span class="text-gray-400">Rek:</span> ' + (item.no_rekening || '-') + '</div>'
                + '<div><span class="text-gray-400">A/n:</span> ' + (item.nama_rekening || '-') + '</div>'
                + '</div>'
                + (function() {
                    const lamps = item.lampiran || [];
                    if (!lamps.length) return '<p class="mt-1.5 text-[10px] text-gray-300 italic border-t border-orange-100 pt-1"><i class="fa fa-paperclip mr-1"></i>Tidak ada lampiran</p>';
                    return '<div class="mt-2 pt-2 border-t border-orange-100">'
                        + '<p class="text-[10px] font-semibold text-gray-400 uppercase mb-1"><i class="fa fa-paperclip mr-1"></i>Lampiran (' + lamps.length + ')</p>'
                        + '<div class="flex flex-wrap gap-1.5">'
                        + lamps.map(function(f) {
                            const ext = (f.file_type || '').toLowerCase();
                            const isImg = ['jpg','jpeg','png','gif','webp'].includes(ext);
                            const icon = isImg ? 'fa-image text-blue-400' : (ext === 'pdf' ? 'fa-file-pdf text-red-400' : (['mp4','mov'].includes(ext) ? 'fa-file-video text-purple-400' : 'fa-paperclip text-gray-400'));
                            return '<a href="' + f.file_path + '" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-orange-200 rounded-lg text-[11px] text-gray-600 hover:bg-orange-50 hover:text-orange-700 max-w-[180px]" title="' + f.file_name + '">'
                                + '<i class="fa ' + icon + ' text-[10px]"></i>'
                                + '<span class="truncate">' + f.file_name + '</span>'
                                + '</a>';
                        }).join('')
                        + '</div></div>';
                })()
                + '</div>';
        });
        html += '</div>'
            + '<div class="mt-2 flex justify-end"><p class="text-sm font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5">Total: Rp ' + totalBiaya.toLocaleString('id-ID') + '</p></div>'
            + '</div>';
    }
    // ── LAMPIRAN (temp_files) ─────────────────────────────────
    var tempFiles = po.temp_files || {};
    var allLampiran = [].concat(tempFiles.bukti || [], tempFiles.attachments || []);
    if (tempFiles.parts) { Object.values(tempFiles.parts).forEach(function(p) { allLampiran = allLampiran.concat(p.bukti || []); }); }
    if (tempFiles.gps_items) { Object.values(tempFiles.gps_items).forEach(function(g) { allLampiran = allLampiran.concat(g.lampiran || []); }); }
    if (allLampiran.length > 0) {
        html += '<div><h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2"><i class="fa fa-paperclip text-blue-500 text-sm"></i> Lampiran (' + allLampiran.length + ')</h4>'
            + '<div class="flex flex-wrap gap-2">';
        allLampiran.forEach(function(f) {
            var name = f.original_name || f.stored_name || 'file';
            var ext  = (f.extension || '').toLowerCase();
            var path = f.path || null;
            var url  = path ? '/storage/' + path : null;
            var isImg = ['jpg','jpeg','png','gif','webp'].includes(ext);
            var icon  = isImg ? 'fa-image text-blue-400' : (ext === 'pdf' ? 'fa-file-pdf text-red-400' : (['mp4','mov'].includes(ext) ? 'fa-file-video text-purple-400' : 'fa-paperclip text-gray-400'));
            var size  = f.size ? Math.round(f.size / 1024) + ' KB' : '';
            if (url) {
                html += '<a href="' + url + '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-colors max-w-[200px]" title="' + name + '">'
                    + '<i class="fa ' + icon + ' text-[11px] flex-shrink-0"></i>'
                    + '<span class="truncate">' + name + '</span>'
                    + (size ? '<span class="text-gray-400 text-[10px] flex-shrink-0">' + size + '</span>' : '')
                    + '</a>';
            } else {
                html += '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-500 max-w-[200px]" title="' + name + '">'
                    + '<i class="fa ' + icon + ' text-[11px] flex-shrink-0"></i><span class="truncate">' + name + '</span></span>';
            }
        });
        html += '</div></div>';
    }

    if (po.status !== 'Pending') {
        html += '<div class="border-t pt-4"><h4 class="font-semibold text-gray-800 mb-2">Info Approval</h4>'
            + '<div class="bg-gray-50 rounded-xl p-3 space-y-1 text-sm">'
            + '<p><span class="text-gray-500">Oleh:</span> ' + (po.disetujui_oleh || '-') + '</p>'
            + '<p><span class="text-gray-500">Tanggal:</span> ' + (po.tanggal_persetujuan || '-') + '</p>'
            + (po.catatan_approval ? '<p><span class="text-gray-500">Catatan:</span> ' + po.catatan_approval + '</p>' : '')
            + (po.pembayaran_no_pr ? '<p><span class="text-gray-500">Pembayaran:</span> <b class="font-mono">' + po.pembayaran_no_pr + '</b></p>' : '')
            + '</div></div>';
    }
    html += '</div>';
    return html;
}
function getStatusBadge(status) {
    const map = {
        'Pending':   '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700"><i class="fa fa-clock text-[8px]"></i> Pending</span>',
        'Disetujui': '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700"><i class="fa fa-check text-[8px]"></i> Disetujui</span>',
        'Ditolak':   '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700"><i class="fa fa-times text-[8px]"></i> Ditolak</span>',
    };
    return map[status] || status;
}
function closeDetailModal() {
    document.getElementById('detailModal').classList.replace('flex','hidden');
    document.getElementById('detailModal').classList.add('hidden');
}

// ── APPROVE MODAL ─────────────────────────────────────────────
let currentApprovePoId = null, approveItemDecisions = [];
function openApproveModal(poId, poNumber) {
    currentApprovePoId = poId;
    document.getElementById('approvePoId').textContent = poNumber;
    document.getElementById('approveModalLoading').classList.remove('hidden');
    document.getElementById('approveModalContent').classList.add('hidden');
    document.getElementById('approveModalFooter').classList.add('hidden');
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveModal').classList.add('flex');
    fetch('/admin/purchase-order/' + poId + '/detail')
        .then(r => r.json())
        .then(function(data) {
            if (!data.success) throw new Error(data.message || 'Gagal memuat data');
            renderApproveItems(data);
            document.getElementById('approveModalLoading').classList.add('hidden');
            document.getElementById('approveModalContent').classList.remove('hidden');
            document.getElementById('approveModalFooter').classList.remove('hidden');
        })
        .catch(function(err) {
            document.getElementById('approveModalLoading').innerHTML = '<div class="text-center text-red-500 py-8"><p class="text-sm">' + err.message + '</p></div>';
        });
}
function renderApproveItems(data) {
    const details = data.details, items = details.items || [];
    approveItemDecisions = items.map(function() { return { action: null, buktiFile: null }; });
    const k = details.kendaraan || {};
    const isServicePart     = details.type === 'service_part';
    const isServiceIncident = details.type === 'service_incident';
    const isServiceAsuransi = details.type === 'service_asuransi';

    // ── Header info kendaraan ─────────────────────────────────
    if (isServicePart || isServiceIncident) {
        document.getElementById('approveKendaraanInfo').innerHTML =
            '<div class="bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 mb-3 flex items-center gap-3"><i class="fa fa-tools text-orange-600"></i>'
            + '<div class="text-sm flex-1"><div class="flex items-center gap-2 flex-wrap"><span class="font-bold text-gray-800">' + (k.nopol || '-') + '</span>'
            + '<span class="text-gray-500">' + (k.merk || '') + '</span></div>'
            + '<div class="flex gap-3 mt-0.5 text-xs text-gray-400 flex-wrap">'
            + '<span>Tgl Service: <b class="text-gray-600">' + (details.tanggal_service || '-') + '</b></span>'
            + '<span>KM: <b class="text-gray-600">' + (details.kilometer || '-') + '</b></span>'
            + (details.keluhan ? '<span>Keluhan: <b class="text-gray-600">' + details.keluhan + '</b></span>' : '')
            + '</div></div></div>';
    } else if (isServiceAsuransi) {
        document.getElementById('approveKendaraanInfo').innerHTML =
            '<div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-3 flex items-center gap-3"><i class="fa fa-shield-halved text-blue-600"></i>'
            + '<div class="text-sm flex-1"><div class="flex items-center gap-2 flex-wrap"><span class="font-bold text-gray-800">' + (k.nopol || '-') + '</span>'
            + '<span class="text-gray-500">' + (k.merk || '') + '</span>'
            + (details.nama_asuransi && details.nama_asuransi !== '-' ? '<span class="bg-blue-100 text-blue-700 text-[10px] font-semibold px-1.5 py-0.5 rounded">' + details.nama_asuransi + '</span>' : '')
            + '</div>'
            + '<div class="flex gap-3 mt-0.5 text-xs text-gray-400 flex-wrap">'
            + '<span>Tgl Service: <b class="text-gray-600">' + (details.tanggal_service || '-') + '</b></span>'
            + '<span>KM: <b class="text-gray-600">' + (details.kilometer || '-') + '</b></span>'
            + '</div></div></div>';
        // Update label list
        const lbl = document.getElementById('approveItemListLabel');
        if (lbl) lbl.innerHTML = '<i class="fa fa-list-ul mr-1 text-blue-500"></i> Kejadian — Centang yang ingin disetujui';
    } else {
        document.getElementById('approveKendaraanInfo').innerHTML =
            '<div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-3 flex items-center gap-3"><i class="fa fa-car text-green-600"></i>'
            + '<div class="text-sm"><span class="font-bold text-gray-800">' + (k.nopol || '-') + '</span>'
            + '<span class="text-gray-500 ml-2">' + (k.merk || '') + '</span>'
            + '<span class="ml-3 text-gray-400 text-xs">Tgl Bayar: <b>' + (details.tanggal_bayar || '-') + '</b></span>'
            + '<span class="ml-3 text-gray-400 text-xs">Berlaku s/d: <b>' + (details.tanggal_habis || '-') + '</b></span></div></div>';
    }

    const list = document.getElementById('approveItemList');
    list.innerHTML = '';
    items.forEach(function(item, idx) {
        // ── Item title, subtitle, nominal ──────────────────────
        let itemTitle, itemSubtitle, itemNominal, bankInfo = '';

        if (isServiceAsuransi) {
            // Kejadian asuransi: tampilkan nama kejadian + lampiran
            itemTitle    = item.nama_kejadian || '-';
            itemSubtitle = '';
            itemNominal  = item.biaya || 0;

            // Tampilkan lampiran per kejadian
            const lamps = item.lampiran || [];
            if (lamps.length > 0) {
                const lampHtml = lamps.map(function(lf) {
                    const ext    = (lf.file_type || '').toLowerCase();
                    const isImg  = ['jpg','jpeg','png','webp','gif'].includes(ext);
                    const icon   = isImg ? 'fa-image text-blue-400' : (ext === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                    return '<a href="' + lf.file_path + '" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-blue-600 hover:underline max-w-[150px] truncate">'
                        + '<i class="fa ' + icon + ' text-[9px]"></i><span class="truncate">' + (lf.file_name || 'file') + '</span></a>';
                }).join('');
                itemSubtitle = '<div class="flex flex-wrap gap-1.5 mt-1">' + lampHtml + '</div>';
            }
        } else if (isServicePart || isServiceIncident) {
            itemTitle    = item.nama_part || '-';
            itemSubtitle = [
                item.category_nama ? '<span class="bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded text-[10px] font-semibold">' + item.category_nama + '</span>' : '',
                item.part_number && item.part_number !== '-' ? '<span class="font-mono text-gray-400 text-[10px]">' + item.part_number + '</span>' : '',
                item.kondisi && item.kondisi !== '-' ? '<span class="text-gray-400 text-[10px]">Kondisi: ' + item.kondisi + '</span>' : '',
                item.posisi && item.posisi !== '-'   ? '<span class="text-gray-400 text-[10px]">Posisi: ' + item.posisi + '</span>' : '',
            ].filter(Boolean).join(' ');
            itemNominal  = item.biaya || 0;
            const bankName = item.nama_bank || '-';
            const bankRek  = item.no_rekening || '-';
            const bankAn   = item.nama_pemilik || item.nama_rekening || '-';
            bankInfo = [
                bankName !== '-' ? '<span><i class="fa fa-building text-[9px]"></i> ' + bankName + '</span>' : '',
                bankRek  !== '-' ? '<span class="font-mono">' + bankRek + '</span>' : '',
                bankAn   !== '-' ? '<span>a/n ' + bankAn + '</span>' : '',
            ].filter(Boolean).join(' ');
        } else {
            // GPS
            const bankName = item.nama_bank || '-';
            const bankRek  = item.no_rekening || '-';
            const bankAn   = item.nama_pemilik || item.nama_rekening || '-';
            bankInfo = [
                bankName !== '-' ? '<span><i class="fa fa-building text-[9px]"></i> ' + bankName + '</span>' : '',
                bankRek  !== '-' ? '<span class="font-mono">' + bankRek + '</span>' : '',
                bankAn   !== '-' ? '<span>a/n ' + bankAn + '</span>' : '',
            ].filter(Boolean).join(' ');
            itemTitle    = item.gps_name || '-';
            itemSubtitle = '<span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-mono">' + (item.type || '-') + '</span>';
            itemNominal  = item.biaya_sewa || 0;
        }

        // ── Warna aksen per type ───────────────────────────────
        const accentColor = isServiceAsuransi ? 'blue'
            : (isServicePart || isServiceIncident) ? 'orange'
            : 'green';

        const card = document.createElement('div');
        card.id = 'approve-item-card-' + idx;
        card.className = 'border border-red-200 rounded-xl overflow-hidden transition-all bg-red-50/10';

        const row = document.createElement('div');
        row.className = 'flex items-start gap-3 px-4 py-3';
        row.innerHTML = '<div class="flex-shrink-0 pt-0.5"><input type="checkbox" id="item-chk-' + idx + '" class="w-4 h-4 rounded text-green-600 cursor-pointer"></div>'
            + '<div class="flex-1 min-w-0"><label for="item-chk-' + idx + '" class="cursor-pointer">'
            + '<div class="flex items-center gap-2 flex-wrap">'
            + '<span class="text-xs text-gray-400">#' + (idx+1) + '</span>'
            + '<span class="font-semibold text-gray-800 text-sm">' + itemTitle + '</span>'
            + (itemSubtitle && !isServiceAsuransi ? itemSubtitle : '')
            + '<span class="ml-auto text-xs font-bold text-emerald-600">Rp ' + formatNumber(itemNominal) + '</span>'
            + '</div>'
            + (isServiceAsuransi && itemSubtitle ? itemSubtitle : '')
            + (bankInfo ? '<div class="mt-1 flex flex-wrap gap-x-3 text-[11px] text-gray-400">' + bankInfo + '</div>' : '')
            + (isServicePart && (item.keterangan_limit || item.keterangan) && (item.keterangan_limit || item.keterangan) !== '-' ? '<p class="mt-1 text-[10px] text-gray-400 italic">' + (item.keterangan_limit || item.keterangan) + '</p>' : '')
            + '</label></div>'
            + '<div id="approve-item-badge-' + idx + '" class="flex-shrink-0 self-center"><span class="text-[10px] font-semibold text-red-600 bg-red-100 px-1.5 py-0.5 rounded-full"><i class="fa fa-times text-[8px]"></i> Ditolak</span></div>';

        card.appendChild(row);

        const rejectPanel = document.createElement('div');
        rejectPanel.id = 'reject-item-panel-' + idx;
        rejectPanel.className = 'px-4 pb-3 pt-2 border-t border-red-100 bg-red-50/20';
        rejectPanel.innerHTML = '<label class="text-[11px] font-semibold text-red-500 mb-1.5 block">Alasan Penolakan <span class="font-normal text-red-400">(opsional)</span></label>'
            + '<textarea id="reject-catatan-' + idx + '" rows="2" placeholder="Tulis alasan penolakan item ini..." class="w-full text-xs px-3 py-2 border border-red-200 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-red-100 bg-white"></textarea>';
        card.appendChild(rejectPanel);

        const chk = row.querySelector('input[type=checkbox]');
        chk.addEventListener('change', function() { toggleApproveItem(idx, this.checked); });
        list.appendChild(card);
    });
    updateApproveSummary();
}
function toggleApproveItem(idx, checked) {
    approveItemDecisions[idx].action = checked ? 'approved' : null;
    const card        = document.getElementById('approve-item-card-' + idx);
    const rejectPanel = document.getElementById('reject-item-panel-' + idx);
    const badge       = document.getElementById('approve-item-badge-' + idx);
    if (checked) {
        card.className = 'border border-green-300 rounded-xl overflow-hidden transition-all bg-green-50/20';
        rejectPanel.classList.add('hidden');
        badge.innerHTML = '<span class="text-[10px] font-semibold text-green-700 bg-green-100 px-1.5 py-0.5 rounded-full"><i class="fa fa-check text-[8px]"></i> Disetujui</span>';
    } else {
        card.className = 'border border-red-200 rounded-xl overflow-hidden transition-all bg-red-50/10';
        rejectPanel.classList.remove('hidden');
        badge.innerHTML = '<span class="text-[10px] font-semibold text-red-600 bg-red-100 px-1.5 py-0.5 rounded-full"><i class="fa fa-times text-[8px]"></i> Ditolak</span>';
    }
    updateApproveSummary();
}
function approveSelectAll(select) {
    approveItemDecisions.forEach(function(d, idx) {
        const chk = document.getElementById('item-chk-' + idx);
        if (chk) { chk.checked = select; toggleApproveItem(idx, select); }
    });
}
function handleApproveBuktiFile(event, idx) {
    const file = event.target.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) { alert('File terlalu besar. Max 5MB.'); event.target.value = ''; return; }
    approveItemDecisions[idx].buktiFile = file;
    document.getElementById('approve-bukti-label-' + idx).textContent = '✓ ' + file.name;
    // Sembunyikan warning saat file sudah dipilih
    const warn = document.getElementById('approve-bukti-warn-' + idx);
    if (warn) { warn.classList.add('hidden'); }
    const wrap = document.getElementById('approve-bukti-label-wrap-' + idx);
    if (wrap) { wrap.className = 'flex items-center gap-2 px-3 py-2 border border-dashed border-green-400 bg-green-50 rounded-lg cursor-pointer'; }
}
function updateApproveSummary() {
    const approved = approveItemDecisions.filter(d => d.action === 'approved').length;
    const total = approveItemDecisions.length;
    const summary = document.getElementById('approveSummary');
    const text = document.getElementById('approveSummaryText');
    if (total > 0) {
        summary.classList.remove('hidden');
        const rejected = total - approved;
        text.innerHTML = '<i class="fa fa-check-circle text-green-500 mr-1"></i><b>' + approved + '</b> item disetujui'
            + (rejected > 0 ? ', <i class="fa fa-times-circle text-red-400 ml-2 mr-1"></i><b>' + rejected + '</b> item akan ditolak' : '');
    }
}
function allApprovedHaveBukti() {
    let allOk = true;
    approveItemDecisions.forEach(function(d, idx) {
        if (d.action !== 'approved') return;
        const warn = document.getElementById('approve-bukti-warn-' + idx);
        const wrap = document.getElementById('approve-bukti-label-wrap-' + idx);
        if (!d.buktiFile) {
            allOk = false;
            if (warn) warn.classList.remove('hidden');
            if (wrap) wrap.className = 'flex items-center gap-2 px-3 py-2 border border-dashed border-red-400 bg-red-50/30 rounded-lg cursor-pointer';
        }
    });
    return allOk;
}
async function submitApproveItems() {
    const approved = approveItemDecisions.filter(d => d.action === 'approved');
    if (approved.length === 0) { alert('Pilih minimal 1 item yang ingin disetujui.'); return; }
    const total = approveItemDecisions.length, rejected = total - approved.length;
    if (!confirm('Approve ' + approved.length + ' item' + (rejected > 0 ? ', tolak ' + rejected + ' item?' : '?'))) return;
    const btn = document.getElementById('approveSubmitBtn');
    btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
    const formData = new FormData();
    const token = document.querySelector('meta[name="csrf-token"]');
    formData.append('_token', token ? token.content : '');
    formData.append('catatan', document.getElementById('approveCatatan').value);
    approveItemDecisions.forEach(function(d, idx) {
        const action = d.action === 'approved' ? 'approved' : 'rejected';
        const catatan = action === 'rejected' ? ((document.getElementById('reject-catatan-' + idx) || {}).value || '') : '';
        formData.append('items[' + idx + '][action]', action);
        formData.append('items[' + idx + '][catatan]', catatan);
        if (action === 'approved' && d.buktiFile) formData.append('items[' + idx + '][bukti]', d.buktiFile);
    });
    try {
        const res = await fetch('/admin/purchase-order/' + currentApprovePoId + '/approve-items', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.success) { window.location.href = result.redirect || window.location.href; }
        else { alert(result.message || 'Terjadi kesalahan.'); btn.disabled = false; btn.innerHTML = '<i class="fa fa-check"></i> Konfirmasi Approval'; }
    } catch (e) { alert('Terjadi kesalahan jaringan.'); btn.disabled = false; btn.innerHTML = '<i class="fa fa-check"></i> Konfirmasi Approval'; }
}
function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden'); document.getElementById('approveModal').classList.remove('flex');
    document.getElementById('approveCatatan').value = ''; currentApprovePoId = null; approveItemDecisions = [];
}

// ── REJECT MODAL (per-item) ───────────────────────────────────
let currentRejectPoId = null, rejectItemDecisions = [];
function openRejectModal(poId, poNumber) {
    currentRejectPoId = poId;
    document.getElementById('rejectPoId').textContent = poNumber;
    document.getElementById('rejectModalLoading').classList.remove('hidden');
    document.getElementById('rejectModalContent').classList.add('hidden');
    document.getElementById('rejectModalFooter').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
    fetch('/admin/purchase-order/' + poId + '/detail')
        .then(r => r.json())
        .then(function(data) {
            if (!data.success) throw new Error(data.message || 'Gagal memuat data');
            renderRejectItems(data);
            document.getElementById('rejectModalLoading').classList.add('hidden');
            document.getElementById('rejectModalContent').classList.remove('hidden');
            document.getElementById('rejectModalFooter').classList.remove('hidden');
        })
        .catch(function(err) {
            document.getElementById('rejectModalLoading').innerHTML = '<div class="text-center text-red-500 py-8"><p class="text-sm">' + err.message + '</p></div>';
        });
}
function renderRejectItems(data) {
    const details = data.details, items = details.items || [];
    rejectItemDecisions = items.map(function() { return { action: 'rejected', catatan: '' }; });
    const k = details.kendaraan || {};
    const isServicePart     = details.type === 'service_part';
    const isServiceIncident = details.type === 'service_incident';
    const isServiceAsuransi = details.type === 'service_asuransi';

    let headerBg = 'bg-red-50 border-red-200';
    let headerIcon = 'fa-car text-red-500';
    document.getElementById('rejectKendaraanInfo').innerHTML =
        '<div class="' + headerBg + ' border rounded-xl px-4 py-3 mb-3 flex items-center gap-3"><i class="fa ' + headerIcon + '"></i>'
        + '<div class="text-sm"><span class="font-bold text-gray-800">' + (k.nopol || '-') + '</span>'
        + '<span class="text-gray-500 ml-2">' + (k.merk || '') + '</span></div></div>';

    // Update label
    const rejectLabel = document.getElementById('rejectItemListLabel');
    if (rejectLabel) {
        let labelText = 'Item GPS — Centang yang ingin ditolak';
        if (isServicePart)     labelText = 'Part — Centang yang ingin ditolak';
        if (isServiceIncident) labelText = 'Part Incident — Centang yang ingin ditolak';
        if (isServiceAsuransi) labelText = 'Kejadian — Centang yang ingin ditolak';
        rejectLabel.innerHTML = '<i class="fa fa-list-ul mr-1 text-red-500"></i> ' + labelText;
    }

    const list = document.getElementById('rejectItemList');
    list.innerHTML = '';
    items.forEach(function(item, idx) {
        const card = document.createElement('div');
        card.id = 'reject-item-card-' + idx;
        card.className = 'border border-red-300 rounded-xl overflow-hidden transition-all bg-red-50/20';
        const row = document.createElement('div');
        row.className = 'flex items-start gap-3 px-4 py-3';

        let itemName, itemBadge, itemBiaya;
        if (isServiceAsuransi) {
            itemName  = item.nama_kejadian || '-';
            itemBadge = '<span class="text-xs bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded">Kejadian</span>';
            itemBiaya = item.biaya || 0;
        } else if (isServicePart || isServiceIncident) {
            itemName  = item.nama_part     || '-';
            itemBadge = item.category_nama ? '<span class="text-xs bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded font-semibold">' + item.category_nama + '</span>' : '-';
            itemBiaya = item.biaya         ||  0;
        } else {
            // GPS
            itemName  = item.gps_name  || '-';
            itemBadge = (item.type || '-');
            itemBiaya = item.biaya_sewa ||  0;
        }

        const badgeHtml = (isServicePart || isServiceIncident)
            ? itemBadge
            : (isServiceAsuransi
                ? itemBadge
                : '<span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-mono">' + itemBadge + '</span>');

        row.innerHTML = '<div class="flex-shrink-0 pt-0.5"><input type="checkbox" id="reject-chk-' + idx + '" checked class="w-4 h-4 rounded text-red-600 cursor-pointer"></div>'
            + '<div class="flex-1 min-w-0"><label for="reject-chk-' + idx + '" class="cursor-pointer"><div class="flex items-center gap-2 flex-wrap">'
            + '<span class="text-xs text-gray-400">#' + (idx+1) + '</span>'
            + '<span class="font-semibold text-gray-800 text-sm">' + itemName + '</span>'
            + badgeHtml
            + '<span class="ml-auto text-xs font-bold text-emerald-600">Rp ' + formatNumber(itemBiaya) + '</span>'
            + '</div></label></div>'
            + '<div id="reject-item-badge-' + idx + '" class="flex-shrink-0 self-center"><span class="text-[10px] font-semibold text-red-600 bg-red-100 px-1.5 py-0.5 rounded-full"><i class="fa fa-times text-[8px]"></i> Ditolak</span></div>';
        card.appendChild(row);

        const reasonPanel = document.createElement('div');
        reasonPanel.id = 'reject-reason-panel-' + idx;
        reasonPanel.className = 'px-4 pb-3 pt-2 border-t border-red-100 bg-red-50/30';
        reasonPanel.innerHTML = '<label class="text-[11px] font-semibold text-red-500 mb-1.5 block">Alasan Penolakan <span class="text-red-400 font-normal">(wajib)</span></label>'
            + '<textarea id="reject-reason-' + idx + '" rows="2" placeholder="Tulis alasan penolakan item ini..." class="w-full text-xs px-3 py-2 border border-red-200 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-red-100 bg-white" oninput="rejectItemDecisions[' + idx + '].catatan = this.value; updateRejectSummary()"></textarea>';
        card.appendChild(reasonPanel);

        const chk = row.querySelector('input[type=checkbox]');
        chk.addEventListener('change', function() { toggleRejectItem(idx, this.checked); });
        list.appendChild(card);
    });
    updateRejectSummary();
}
function toggleRejectItem(idx, checked) {
    rejectItemDecisions[idx].action = checked ? 'rejected' : 'skip';
    const card = document.getElementById('reject-item-card-' + idx);
    const reasonPanel = document.getElementById('reject-reason-panel-' + idx);
    const badge = document.getElementById('reject-item-badge-' + idx);
    if (checked) {
        card.className = 'border border-red-300 rounded-xl overflow-hidden transition-all bg-red-50/20';
        reasonPanel.classList.remove('hidden');
        badge.innerHTML = '<span class="text-[10px] font-semibold text-red-600 bg-red-100 px-1.5 py-0.5 rounded-full"><i class="fa fa-times text-[8px]"></i> Ditolak</span>';
    } else {
        card.className = 'border border-gray-200 rounded-xl overflow-hidden transition-all bg-gray-50/10';
        reasonPanel.classList.add('hidden');
        badge.innerHTML = '<span class="text-[10px] font-semibold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded-full"><i class="fa fa-minus text-[8px]"></i> Dilewati</span>';
    }
    updateRejectSummary();
}
function rejectSelectAll(select) {
    rejectItemDecisions.forEach(function(d, idx) {
        const chk = document.getElementById('reject-chk-' + idx);
        if (chk) { chk.checked = select; toggleRejectItem(idx, select); }
    });
}
function updateRejectSummary() {
    const rejected = rejectItemDecisions.filter(d => d.action === 'rejected').length;
    const total = rejectItemDecisions.length;
    const summary = document.getElementById('rejectSummary');
    const text = document.getElementById('rejectSummaryText');
    summary.classList.remove('hidden');
    text.innerHTML = '<i class="fa fa-times-circle text-red-500 mr-1"></i><b>' + rejected + '</b> item akan ditolak'
        + (total - rejected > 0 ? ' &nbsp;·&nbsp; <b>' + (total - rejected) + '</b> item dilewati' : '');
}
async function submitRejectItems() {
    const toReject = rejectItemDecisions.filter(d => d.action === 'rejected');
    if (toReject.length === 0) { alert('Pilih minimal 1 item yang ingin ditolak.'); return; }
    for (let idx = 0; idx < rejectItemDecisions.length; idx++) {
        if (rejectItemDecisions[idx].action !== 'rejected') continue;
        const val = ((document.getElementById('reject-reason-' + idx) || {}).value || '').trim();
        if (!val) { alert('Item #' + (idx + 1) + ': Alasan penolakan wajib diisi.'); document.getElementById('reject-reason-' + idx).focus(); return; }
        rejectItemDecisions[idx].catatan = val;
    }
    if (!confirm('Tolak ' + toReject.length + ' item' + (rejectItemDecisions.length - toReject.length > 0 ? ', ' + (rejectItemDecisions.length - toReject.length) + ' item dilewati?' : '?'))) return;
    const btn = document.getElementById('rejectSubmitBtn');
    btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
    const formData = new FormData();
    const token = document.querySelector('meta[name="csrf-token"]');
    formData.append('_token', token ? token.content : '');
    formData.append('catatan', document.getElementById('rejectCatatan').value);
    rejectItemDecisions.forEach(function(d, idx) {
        const action = d.action === 'rejected' ? 'rejected' : 'approved';
        formData.append('items[' + idx + '][action]', action);
        formData.append('items[' + idx + '][catatan]', d.action === 'rejected' ? (d.catatan || '') : '');
    });
    try {
        const res = await fetch('/admin/purchase-order/' + currentRejectPoId + '/approve-items', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.success) { window.location.href = result.redirect || window.location.href; }
        else { alert(result.message || 'Terjadi kesalahan.'); btn.disabled = false; btn.innerHTML = '<i class="fa fa-times"></i> Konfirmasi Penolakan'; }
    } catch (e) { alert('Terjadi kesalahan jaringan.'); btn.disabled = false; btn.innerHTML = '<i class="fa fa-times"></i> Konfirmasi Penolakan'; }
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden'); document.getElementById('rejectModal').classList.remove('flex');
    document.getElementById('rejectCatatan').value = ''; currentRejectPoId = null; rejectItemDecisions = [];
}

// ── RESUBMIT MODAL ────────────────────────────────────────────
let resubmitGpsData = [];
function openResubmitModal(poId, poNumber) {
    document.getElementById('resubmitPoNumber').textContent = poNumber;
    document.getElementById('resubmitLoading').classList.remove('hidden');
    document.getElementById('resubmitForm').classList.add('hidden');
    document.getElementById('resubmitModal').classList.remove('hidden');
    document.getElementById('resubmitModal').classList.add('flex');
    fetch('/admin/purchase-order/' + poId + '/resubmit', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({}),
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) throw new Error(data.message || 'Gagal memuat data');
        renderResubmitForm(data);
        document.getElementById('resubmitLoading').classList.add('hidden');
        document.getElementById('resubmitForm').classList.remove('hidden');
    })
    .catch(function(err) {
        document.getElementById('resubmitLoading').innerHTML = '<div class="text-center text-red-500 py-8"><p class="text-sm">' + err.message + '</p></div>';
    });
}
function renderResubmitForm(data) {
    document.getElementById('resubmitPoId').value        = data.po_id;
    document.getElementById('resubmitKendaraanId').value = data.kendaraan_id;
    // Set hidden values langsung dari data — tidak perlu sync dari visible input
    document.getElementById('resubmitTanggalBayar').value = data.tanggal_bayar;
    document.getElementById('resubmitTanggalHabis').value  = data.tanggal_habis;
    document.getElementById('resubmitKeterangan').value    = data.keterangan || '';
    document.getElementById('resubmitKendaraanInfo').textContent = (data.nopol || '-') + ' — ' + (data.merk || '');
    if (data.catatan) document.getElementById('resubmitCatatan').textContent = 'Alasan ditolak: ' + data.catatan;
    resubmitGpsData = data.gps_items || [];
    const container = document.getElementById('resubmitItemsContainer');
    container.innerHTML = '';
    resubmitGpsData.forEach(function(item, idx) {
        const div = document.createElement('div');
        div.className = 'border border-gray-200 rounded-xl p-4 space-y-2 bg-gray-50/50';
        // Hidden inputs untuk field yang tidak diubah user
        div.innerHTML = '<input type="hidden" name="gps_items[' + idx + '][gps_id]"      value="' + (item.gps_id      || '') + '">'
            + '<input type="hidden" name="gps_items[' + idx + '][type]"       value="' + (item.type       || '') + '">'
            + '<input type="hidden" name="gps_items[' + idx + '][nama_bank]"  value="' + (item.nama_bank  || '') + '">'
            + '<input type="hidden" name="gps_items[' + idx + '][no_rekening]" value="' + (item.no_rekening || '') + '">'
            + '<input type="hidden" name="gps_items[' + idx + '][nama_pemilik]" value="' + (item.nama_pemilik || '') + '">'
            // Label nama GPS
            + '<div class="flex items-center gap-2">'
            + '<span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-600 text-[10px] font-bold flex items-center justify-center">' + (idx + 1) + '</span>'
            + '<span class="text-xs font-semibold text-gray-700">' + (item.nama_gps || '-') + '</span>'
            + '<span class="font-mono text-[10px] bg-gray-200 text-gray-500 px-1.5 py-0.5 rounded">' + (item.type || '') + '</span>'
            + '</div>'
            // Hanya Biaya Sewa yang visible
            + '<div><label class="text-xs font-semibold text-gray-600">Biaya Sewa <span class="text-red-500">*</span></label>'
            + '<input type="number" name="gps_items[' + idx + '][biaya_sewa]" value="' + (item.biaya_sewa || 0) + '" required min="0"'
            + ' class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-100 focus:border-amber-400"></div>';
        container.appendChild(div);
    });
}
function closeResubmitModal() {
    document.getElementById('resubmitModal').classList.replace('flex','hidden');
    document.getElementById('resubmitModal').classList.add('hidden');
}

// ── BACKDROP CLICK ────────────────────────────────────────────
document.getElementById('detailModal')?.addEventListener('click',   function(e) { if (e.target === this) closeDetailModal(); });
document.getElementById('approveModal')?.addEventListener('click',  function(e) { if (e.target === this) closeApproveModal(); });
document.getElementById('rejectModal')?.addEventListener('click',   function(e) { if (e.target === this) closeRejectModal(); });
document.getElementById('resubmitModal')?.addEventListener('click', function(e) { if (e.target === this) closeResubmitModal(); });

function formatNumber(n) { return Number(n).toLocaleString('id-ID'); }
// ── RESUBMIT via PO (pajak/asuransi/KIR → redirect ke form edit) ─
async function resubmitViaPO(poId) {
    try {
        const formData = new FormData();
        const token = document.querySelector('meta[name="csrf-token"]');
        formData.append('_token', token ? token.content : '');
        const res = await fetch('/admin/purchase-order/' + poId + '/resubmit', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.success && result.redirect) {
            window.location.href = result.redirect;
        } else {
            alert(result.message || 'Terjadi kesalahan.');
        }
    } catch (e) { alert('Terjadi kesalahan jaringan.'); }
}

// ── RESUBMIT SIMPLE MODAL (pajak / asuransi_kendaraan / kir) ─────
let _resubmitSimplePoId   = null;
let _resubmitSimpleType   = null;

const _resubmitSimpleTitles = {
    pajak:                          'Ajukan Ulang — Pajak Kendaraan',
    pajak_perpanjang:               'Ajukan Ulang — Perpanjangan Pajak',
    asuransi_kendaraan:             'Ajukan Ulang — Asuransi Kendaraan',
    asuransi_kendaraan_perpanjang:  'Ajukan Ulang — Perpanjangan Asuransi',
    kir:                            'Ajukan Ulang — KIR',
    kir_perpanjang:                 'Ajukan Ulang — Perpanjangan KIR',
};

function openResubmitSimpleModal(poId, poNumber, sourceType) {
    _resubmitSimplePoId = poId;
    _resubmitSimpleType = sourceType;

    document.getElementById('resubmitSimplePoNumber').textContent = poNumber;
    document.getElementById('resubmitSimpleTitle').textContent = _resubmitSimpleTitles[sourceType] || 'Ajukan Ulang';
    document.getElementById('resubmitSimpleLoading').classList.remove('hidden');
    document.getElementById('resubmitSimpleBody').classList.add('hidden');
    document.getElementById('resubmitSimpleModal').classList.remove('hidden');
    document.getElementById('resubmitSimpleModal').classList.add('flex');

    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    fetch('/admin/purchase-order/' + poId + '/resubmit-modal', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({}),
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) throw new Error(data.message || 'Gagal memuat data');
        renderResubmitSimpleForm(data);
        document.getElementById('resubmitSimpleLoading').classList.add('hidden');
        document.getElementById('resubmitSimpleBody').classList.remove('hidden');
    })
    .catch(function(err) {
        document.getElementById('resubmitSimpleLoading').innerHTML =
            '<div class="text-center text-red-500 py-8 px-6"><i class="fa fa-exclamation-triangle text-xl mb-2"></i><p class="text-sm">' + err.message + '</p></div>';
    });
}

function renderResubmitSimpleForm(data) {
    document.getElementById('resubmitSimpleKendaraan').textContent = (data.nopol || '-') + ' — ' + (data.merk || '');
    const catatanEl = document.getElementById('resubmitSimpleCatatan');
    catatanEl.textContent = data.catatan ? 'Alasan ditolak: ' + data.catatan : '';

    const container = document.getElementById('resubmitSimpleFields');
    container.innerHTML = '';

    function makeField(label, id, type, value, required, extra) {
        required = required ? '<span class="text-red-500">*</span>' : '';
        extra = extra || '';
        return '<div>'
            + '<label class="block text-xs font-semibold text-gray-600 mb-1">' + label + ' ' + required + '</label>'
            + '<input type="' + type + '" id="rsf_' + id + '" value="' + (value || '') + '" ' + extra
            + ' class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-100 focus:border-amber-400">'
            + '</div>';
    }

    let html = '';
    if (['pajak', 'pajak_perpanjang'].includes(data.source_type)) {
        html += makeField('Nominal (Rp)', 'nominal', 'number', data.nominal, true, 'min="0"');
        html += makeField('Nama Pemilik', 'nama_pemilik', 'text', data.nama_pemilik, false);
        html += '<div class="grid grid-cols-2 gap-3">';
        html += makeField('Nama Bank', 'nama_bank', 'text', data.nama_bank, false);
        html += makeField('No. Rekening', 'no_rekening', 'text', data.no_rekening, false);
        html += '</div>';

    } else if (['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'].includes(data.source_type)) {
        html += '<div class="grid grid-cols-2 gap-3">';
        html += makeField('Tgl Mulai', 'tgl_mulai', 'date', data.tgl_mulai, true);
        html += makeField('Tgl Berakhir', 'tgl_berakhir', 'date', data.tgl_berakhir, true);
        html += '</div>';
        html += '<div class="grid grid-cols-2 gap-3">';
        html += makeField('Durasi (bulan)', 'durasi_bulan', 'number', data.durasi_bulan, false, 'min="0"');
        html += makeField('Biaya (Rp)', 'biaya', 'number', data.biaya, true, 'min="0"');
        html += '</div>';
        html += makeField('Nama Bank', 'nama_bank', 'text', data.nama_bank, false);
        html += '<div class="grid grid-cols-2 gap-3">';
        html += makeField('No. Rekening', 'no_rekening', 'text', data.no_rekening, false);
        html += makeField('Nama Pemilik Rekening', 'nama_rekening', 'text', data.nama_rekening, false);
        html += '</div>';

    } else {
        // kir / kir_perpanjang
        html += '<div class="grid grid-cols-2 gap-3">';
        html += makeField('No. Uji', 'no_uji', 'text', data.no_uji, false);
        html += makeField('Tanggal Bayar', 'tanggal_bayar', 'date', data.tanggal_bayar, true);
        html += '</div>';
        html += '<div class="grid grid-cols-2 gap-3">';
        html += makeField('Masa Berlaku s/d', 'masa_berlaku', 'date', data.masa_berlaku, true);
        html += makeField('Biaya (Rp)', 'biaya', 'number', data.biaya, true, 'min="0"');
        html += '</div>';
        html += makeField('Nama Bank', 'nama_bank', 'text', data.nama_bank, false);
        html += makeField('No. Rekening', 'no_rekening', 'text', data.no_rekening, false);
    }

    container.innerHTML = html;
}

function closeResubmitSimpleModal() {
    document.getElementById('resubmitSimpleModal').classList.replace('flex', 'hidden');
    document.getElementById('resubmitSimpleModal').classList.add('hidden');
    _resubmitSimplePoId = null;
    _resubmitSimpleType = null;
}

async function submitResubmitSimple() {
    if (!_resubmitSimplePoId) return;

    const btn   = document.getElementById('resubmitSimpleSubmitBtn');
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function getVal(id) {
        const el = document.getElementById('rsf_' + id);
        return el ? el.value : '';
    }

    let payload = {};
    if (['pajak', 'pajak_perpanjang'].includes(_resubmitSimpleType)) {
        payload = {
            nominal       : getVal('nominal'),
            nama_pemilik  : getVal('nama_pemilik'),
            nama_bank     : getVal('nama_bank'),
            no_rekening   : getVal('no_rekening'),
        };
        if (!payload.nominal) { alert('Nominal wajib diisi.'); return; }
    } else if (['asuransi_kendaraan', 'asuransi_kendaraan_perpanjang'].includes(_resubmitSimpleType)) {
        payload = {
            tgl_mulai     : getVal('tgl_mulai'),
            tgl_berakhir  : getVal('tgl_berakhir'),
            durasi_bulan  : getVal('durasi_bulan'),
            biaya         : getVal('biaya'),
            nama_bank     : getVal('nama_bank'),
            no_rekening   : getVal('no_rekening'),
            nama_rekening : getVal('nama_rekening'),
        };
        if (!payload.tgl_mulai || !payload.tgl_berakhir || !payload.biaya) { alert('Tgl Mulai, Tgl Berakhir, dan Biaya wajib diisi.'); return; }
    } else {
        // kir / kir_perpanjang
        payload = {
            no_uji        : getVal('no_uji'),
            tanggal_bayar : getVal('tanggal_bayar'),
            masa_berlaku  : getVal('masa_berlaku'),
            biaya         : getVal('biaya'),
            nama_bank     : getVal('nama_bank'),
            no_rekening   : getVal('no_rekening'),
        };
        if (!payload.tanggal_bayar || !payload.masa_berlaku || !payload.biaya) { alert('Tanggal Bayar, Masa Berlaku, dan Biaya wajib diisi.'); return; }
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res = await fetch('/admin/purchase-order/' + _resubmitSimplePoId + '/resubmit-update', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        const result = await res.json();
        if (result.success) {
            closeResubmitSimpleModal();
            window.location.href = result.redirect || window.location.href;
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

document.getElementById('resubmitSimpleModal')?.addEventListener('click', function(e) { if (e.target === this) closeResubmitSimpleModal(); });

// ── RESUBMIT SERVICE ASURANSI MODAL ───────────────────────────────────────
let _rsaPoId = null;

function openResubmitServiceAsuransiModal(poId, poNumber) {
    _rsaPoId = poId;
    document.getElementById('rsaPoNumber').textContent = poNumber;
    document.getElementById('rsaLoading').classList.remove('hidden');
    document.getElementById('rsaBody').classList.add('hidden');
    document.getElementById('modalResubmitServiceAsuransi').classList.remove('hidden');
    document.getElementById('modalResubmitServiceAsuransi').classList.add('flex');

    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    fetch('/admin/purchase-order/' + poId + '/resubmit', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({}),
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) throw new Error(data.message || 'Gagal memuat data');
        renderRsaForm(data);
        document.getElementById('rsaLoading').classList.add('hidden');
        document.getElementById('rsaBody').classList.remove('hidden');
    })
    .catch(function(err) {
        document.getElementById('rsaLoading').innerHTML =
            '<div class="text-center text-red-500 py-8 px-6"><i class="fa fa-exclamation-triangle text-xl mb-2 block"></i><p class="text-sm">' + err.message + '</p></div>';
    });
}

function renderRsaForm(data) {
    // Info kendaraan + alasan
    document.getElementById('rsaKendaraan').textContent = (data.nopol || '-') + ' — ' + (data.merk || '');
    const catatanEl = document.getElementById('rsaCatatan');
    if (data.catatan) {
        catatanEl.textContent = 'Alasan penolakan: ' + data.catatan;
        catatanEl.classList.remove('hidden');
    } else {
        catatanEl.classList.add('hidden');
    }

    // Set form action
    document.getElementById('rsaForm').action = '/admin/purchase-order/' + _rsaPoId + '/resubmit-service-asuransi';

    // Hidden fields
    document.getElementById('rsa_kendaraan_id').value   = data.kendaraan_id   || '';
    document.getElementById('rsa_nama_asuransi').value  = data.nama_asuransi  || '';
    document.getElementById('rsa_tanggal_service').value= data.tanggal_service|| '';
    document.getElementById('rsa_periode_mulai').value  = data.periode_mulai  || '';
    document.getElementById('rsa_periode_selesai').value= data.periode_selesai|| '';
    document.getElementById('rsa_kilometer').value      = data.kilometer      || '';

    // Render kejadian rows
    const container = document.getElementById('rsaKejadianContainer');
    container.innerHTML = '';
    (data.kejadians || []).forEach(function(kej, idx) {
        renderRsaKejadian(container, idx, kej);
    });
    updateRsaTotalBiaya();
}

function renderRsaKejadian(container, idx, kej) {
    const div = document.createElement('div');
    div.id = 'rsa-kej-' + idx;
    div.className = 'bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3';

    // Lampiran lama
    const lampiranLama = (kej.lampiran_existing || kej.lampiran || []);

    // Hidden inputs untuk lampiran lama (agar ikut terkirim ke server)
    let hiddenLampiranInputs = lampiranLama.map(function(lf, li) {
        const path = lf.path || '';
        const name = lf.original_name || path.split('/').pop();
        const ext  = lf.extension || path.split('.').pop();
        const size = lf.size || 0;
        if (!path) return '';
        return '<input type="hidden" name="kejadians[' + idx + '][lampiran_lama][' + li + '][path]"          value="' + path.replace(/"/g, '&quot;') + '">'
             + '<input type="hidden" name="kejadians[' + idx + '][lampiran_lama][' + li + '][original_name]" value="' + name.replace(/"/g, '&quot;') + '">'
             + '<input type="hidden" name="kejadians[' + idx + '][lampiran_lama][' + li + '][extension]"     value="' + ext + '">'
             + '<input type="hidden" name="kejadians[' + idx + '][lampiran_lama][' + li + '][size]"          value="' + size + '">';
    }).join('');

    let lampiranLamaHtml = '';
    if (lampiranLama.length > 0) {
        lampiranLamaHtml = '<div class="mt-1 space-y-1">'
            + lampiranLama.map(function(lf) {
                const path = lf.path || '';
                const name = lf.original_name || path.split('/').pop();
                const ext  = (lf.extension || '').toLowerCase();
                const isImg = ['jpg','jpeg','png','webp'].includes(ext);
                const icon  = isImg ? 'fa-image text-blue-400' : (ext === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                const url   = path ? '/storage/' + path : null;
                if (!url) return '';
                return '<a href="' + url + '" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline">'
                    + '<i class="fa ' + icon + ' text-[9px]"></i><span class="truncate max-w-[200px]">' + name + '</span></a>';
            }).join('') + '</div>';
    }

    div.innerHTML = `
        ${hiddenLampiranInputs}
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-gray-600">Kejadian #${idx + 1}</span>
            <button type="button" onclick="removeRsaKejadian(${idx})"
                class="w-6 h-6 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center text-xs">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                    onchange="updateRsaTotalBiaya()" oninput="updateRsaTotalBiaya()"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
            </div>
        </div>
        ${lampiranLama.length ? `
        <div>
            <label class="text-xs font-semibold text-gray-500 mb-1 block">Lampiran</label>
            ${lampiranLamaHtml}
        </div>` : ''}
    `;
    container.appendChild(div);
}

function removeRsaKejadian(idx) {
    document.getElementById('rsa-kej-' + idx)?.remove();
    updateRsaTotalBiaya();
}

let rsaKejadianCount = 0;
function addRsaKejadian() {
    const container = document.getElementById('rsaKejadianContainer');
    const idx = container.children.length + rsaKejadianCount++;
    renderRsaKejadian(container, idx, {});
}

function updateRsaTotalBiaya() {
    let total = 0;
    document.querySelectorAll('#rsaKejadianContainer [name$="[biaya]"]').forEach(function(inp) {
        total += parseInt(inp.value || 0);
    });
    const el = document.getElementById('rsaTotalBiaya');
    if (el) el.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function updateRsaLampiranList(idx, input) {
    const list = document.getElementById('rsa-lampiran-list-' + idx);
    if (!list) return;
    list.innerHTML = Array.from(input.files).map(f =>
        '<div class="flex items-center gap-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded px-2 py-1">'
        + '<i class="fa fa-paperclip text-[10px] text-gray-400"></i>'
        + '<span class="truncate">' + f.name + '</span>'
        + '<span class="ml-auto text-[10px] text-gray-400">' + (f.size/1024).toFixed(0) + ' KB</span>'
        + '</div>'
    ).join('');
}

function closeResubmitServiceAsuransiModal() {
    document.getElementById('modalResubmitServiceAsuransi').classList.add('hidden');
    document.getElementById('modalResubmitServiceAsuransi').classList.remove('flex');
    _rsaPoId = null;
}

async function submitResubmitServiceAsuransi() {
    if (!_rsaPoId) return;
    const btn   = document.getElementById('rsaSubmitBtn');
    const form  = document.getElementById('rsaForm');
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';

    const formData = new FormData(form);
    formData.append('_token', token);

    try {
        const res    = await fetch('/admin/purchase-order/' + _rsaPoId + '/resubmit-service-asuransi', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.success) {
            closeResubmitServiceAsuransiModal();
            window.location.href = result.redirect || window.location.href;
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

document.getElementById('modalResubmitServiceAsuransi')?.addEventListener('click', function(e) {
    if (e.target === this) closeResubmitServiceAsuransiModal();
});

// ── RESUBMIT SIMPLE (STNK → reset ke Pending langsung) ───────
async function resubmitSimple(poId, poNumber) {
    if (!confirm('Ajukan ulang PO ' + poNumber + '?')) return;
    try {
        const formData = new FormData();
        const token = document.querySelector('meta[name="csrf-token"]');
        formData.append('_token', token ? token.content : '');
        const res = await fetch('/admin/purchase-order/' + poId + '/resubmit-simple', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.success) {
            window.location.href = result.redirect || window.location.href;
        } else {
            alert(result.message || 'Terjadi kesalahan.');
        }
    } catch (e) { alert('Terjadi kesalahan jaringan.'); }
}

// ── REJECT SIMPLE MODAL (non-GPS: pajak, asuransi, KIR, dll) ──
let currentRejectSimplePoId = null;

function openRejectSimpleModal(poId, poNumber, sourceType) {
    currentRejectSimplePoId = poId;
    const labels = {
        'pajak': 'Pajak Kendaraan', 'pajak_perpanjang': 'Perpanjangan Pajak',
        'asuransi_kendaraan': 'Asuransi Kendaraan', 'asuransi_kendaraan_perpanjang': 'Perpanjangan Asuransi',
        'kir': 'KIR', 'kir_perpanjang': 'Perpanjangan KIR', 'stnk': 'STNK',
    };
    const label = labels[sourceType] || sourceType;
    document.getElementById('rejectSimpleSubtitle').textContent = poNumber + ' — ' + label;
    document.getElementById('rejectSimpleCatatan').value = '';
    const btn = document.getElementById('rejectSimpleSubmitBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-times-circle"></i> Konfirmasi Tolak';
    const m = document.getElementById('rejectSimpleModal');
    m.classList.remove('hidden'); m.classList.add('flex');
    setTimeout(() => document.getElementById('rejectSimpleCatatan').focus(), 100);
}

function closeRejectSimpleModal() {
    const m = document.getElementById('rejectSimpleModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    currentRejectSimplePoId = null;
}

async function submitRejectSimple() {
    if (!currentRejectSimplePoId) return;
    const catatan = document.getElementById('rejectSimpleCatatan').value.trim();
    if (!catatan) {
        document.getElementById('rejectSimpleCatatan').focus();
        document.getElementById('rejectSimpleCatatan').classList.add('border-red-400');
        return;
    }
    document.getElementById('rejectSimpleCatatan').classList.remove('border-red-400');
    const btn = document.getElementById('rejectSimpleSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
    const formData = new FormData();
    const token = document.querySelector('meta[name="csrf-token"]');
    formData.append('_token', token ? token.content : '');
    formData.append('catatan', catatan);
    try {
        const res = await fetch('/admin/purchase-order/' + currentRejectSimplePoId + '/reject-simple', {
            method: 'POST', body: formData
        });
        const result = await res.json();
        if (result.success) {
            window.location.href = result.redirect || window.location.href;
        } else {
            alert(result.message || 'Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-times-circle"></i> Konfirmasi Tolak';
        }
    } catch (e) {
        alert('Terjadi kesalahan jaringan.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-times-circle"></i> Konfirmasi Tolak';
    }
}

document.getElementById('rejectSimpleModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRejectSimpleModal();
});

// ── APPROVE SIMPLE MODAL (non-GPS: pajak, asuransi, KIR, dll) ─
let currentApproveSimplePoId = null;

function openApproveSimpleModal(poId, poNumber, sourceType) {
    currentApproveSimplePoId = poId;
    const labels = {
        'pajak': 'Pajak Kendaraan', 'pajak_perpanjang': 'Perpanjangan Pajak',
        'asuransi_kendaraan': 'Asuransi Kendaraan', 'asuransi_kendaraan_perpanjang': 'Perpanjangan Asuransi',
        'kir': 'KIR', 'kir_perpanjang': 'Perpanjangan KIR',
    };
    const label = labels[sourceType] || sourceType;
    document.getElementById('approveSimpleSubtitle').textContent = poNumber + ' — ' + label;
    document.getElementById('approveSimpleCatatan').value = '';
    const btn = document.getElementById('approveSimpleSubmitBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-check"></i> Konfirmasi Approve';
    const m = document.getElementById('approveSimpleModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}

function closeApproveSimpleModal() {
    const m = document.getElementById('approveSimpleModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    currentApproveSimplePoId = null;
}

async function submitApproveSimple() {
    if (!currentApproveSimplePoId) return;
    const btn = document.getElementById('approveSimpleSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';

    const formData = new FormData();
    const token = document.querySelector('meta[name="csrf-token"]');
    formData.append('_token', token ? token.content : '');
    formData.append('catatan', document.getElementById('approveSimpleCatatan').value);

    try {
        const res = await fetch('/admin/purchase-order/' + currentApproveSimplePoId + '/approve-simple', {
            method: 'POST', body: formData
        });
        const result = await res.json();
        if (result.success) {
            window.location.href = result.redirect || window.location.href;
        } else {
            alert(result.message || 'Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check"></i> Konfirmasi Approve';
        }
    } catch (e) {
        alert('Terjadi kesalahan jaringan.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-check"></i> Konfirmasi Approve';
    }
}

// Close on backdrop click
document.getElementById('approveSimpleModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeApproveSimpleModal();
});

// ── STNK: langsung approve tanpa modal ────────────────────────
async function approveStnk(poId, poNumber) {
    if (!confirm('Approve PO ' + poNumber + ' (STNK)?')) return;
    const formData = new FormData();
    const token = document.querySelector('meta[name="csrf-token"]');
    formData.append('_token', token ? token.content : '');
    try {
        const res = await fetch('/admin/purchase-order/' + poId + '/approve-simple', {
            method: 'POST', body: formData
        });
        const result = await res.json();
        if (result.success) {
            window.location.href = result.redirect || window.location.href;
        } else {
            alert(result.message || 'Terjadi kesalahan.');
        }
    } catch (e) {
        alert('Terjadi kesalahan jaringan.');
    }
}

</script>
@endpush

{{-- MODAL: RESUBMIT SERVICE ASURANSI --}}
<div id="modalResubmitServiceAsuransi" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-base font-bold text-gray-800">Ajukan Ulang — Service Asuransi</h3>
                <p class="text-sm text-gray-500 mt-0.5">PO: <span id="rsaPoNumber" class="font-mono font-semibold text-amber-600"></span></p>
            </div>
            <button onclick="closeResubmitServiceAsuransiModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>

        {{-- Loading --}}
        <div id="rsaLoading" class="flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
                <p class="text-sm">Memuat data...</p>
            </div>
        </div>

        {{-- Body --}}
        <div id="rsaBody" class="hidden flex-1 overflow-y-auto flex flex-col">
            {{-- Info kendaraan + alasan --}}
            <div class="px-6 pt-4 pb-2 space-y-2">
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm">
                    <p class="font-semibold text-gray-800" id="rsaKendaraan">-</p>
                </div>
                <div id="rsaCatatan" class="hidden bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-xs text-red-700"></div>
            </div>

            <form id="rsaForm" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto px-6 pb-4 space-y-4">
                @csrf
                <input type="hidden" name="kendaraan_id"    id="rsa_kendaraan_id">
                <input type="hidden" name="nama_asuransi"   id="rsa_nama_asuransi">
                <input type="hidden" name="tanggal_service" id="rsa_tanggal_service">
                <input type="hidden" name="periode_mulai"   id="rsa_periode_mulai">
                <input type="hidden" name="periode_selesai" id="rsa_periode_selesai">
                <input type="hidden" name="kilometer"       id="rsa_kilometer">

                {{-- Total biaya auto-sum --}}
                <div class="flex items-center gap-3 px-3 py-2 bg-blue-50 border border-blue-100 rounded-xl text-xs text-gray-600">
                    Total Biaya (auto-sum dari kejadian):
                    <span id="rsaTotalBiaya" class="font-bold text-blue-700 ml-1">Rp 0</span>
                </div>

                {{-- Kejadian container --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-gray-600">Daftar Kejadian</label>
                        <button type="button" onclick="addRsaKejadian()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fa fa-plus text-xs"></i> Tambah Kejadian
                        </button>
                    </div>
                    <div id="rsaKejadianContainer" class="space-y-3"></div>
                </div>
            </form>

            <div class="border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
                <button type="button" onclick="closeResubmitServiceAsuransiModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="rsaSubmitBtn" onclick="submitResubmitServiceAsuransi()"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-rotate-right"></i> Ajukan Ulang
                </button>
            </div>
        </div>
    </div>
</div>
