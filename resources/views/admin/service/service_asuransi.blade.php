@extends('admin.layouts.app')

@section('title', 'Service Asuransi')

@section('content')

    @php
        $jumlahService = $data->count();
        $totalBiaya = $data->sum('biaya');
        $jumlahBermasalah = $data->where('status', 'bermasalah')->count();
        $jumlahSelesai    = $data->where('status', 'selesai')->count();
        $jumlahTidakAktif = $data->where('status', 'tidak_aktif')->count();
    @endphp

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Service Asuransi</h1>
                <p class="text-sm text-gray-500 mt-0.5">Data Service & Asuransi Kendaraan</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150 mt-2 sm:mt-0">
                <i class="fa fa-plus text-sm"></i>
                Tambah
            </button>
        </div>

        {{-- NAV TABS --}}
        <div>
            <nav class="inline-flex gap-1 bg-gray-100 rounded-xl p-1">
                @php
                    $navItems = [
                        ['label' => 'Service History',  'url' => '/admin/service-history',    'icon' => 'bi bi-clock-history'],
                        ['label' => 'Service Asuransi', 'url' => '/admin/service-asuransi',   'icon' => 'bi bi-shield-fill-check'],
                        ['label' => 'Service Incident', 'url' => '/admin/service-incident',   'icon' => 'bi bi-exclamation-triangle-fill'],
                        ['label' => 'Reminder Service', 'url' => '/admin/reminder-service',   'icon' => 'bi bi-bell-fill'],
                        ['label' => 'Kategori Service', 'url' => '/admin/service-categories', 'icon' => 'bi bi-tags-fill'],
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

        {{-- CHART FILTER --}}
        <x-chart-filter id="serviceAsuransiChartFilter" defaultFilter="month" :showCustomRange="true"
            :showCategoryFilter="false" :categories="collect()" />

        {{-- CHART CONTAINER --}}
        <x-chart-container
            id="serviceAsuransiChartContainer"
            layout="stacked"
            pieTitle="Biaya per Status" pieId="serviceAsuransiPieChart"
            barTitle="Biaya Service Asuransi per Periode" barId="serviceAsuransiBarChart"
            lineTitle="Trend Biaya Service Asuransi" lineId="serviceAsuransiLineChart"
            :showStats="true" :statsData="[]"
        />

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-shield-halved text-blue-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Service Asuransi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $jumlahService }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Bermasalah</p>
                    <p class="text-2xl font-bold text-red-600">{{ $jumlahBermasalah }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Selesai</p>
                    <p class="text-2xl font-bold text-green-600">{{ $jumlahSelesai }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-wallet text-green-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Biaya</p>
                    <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- TOOLBAR --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800">Service Asuransi</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->total() }} total data</p>
                </div>
                <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap items-center gap-2">

                    {{-- Filter Status --}}
                    <div class="inline-flex rounded-lg border border-gray-200 overflow-hidden text-xs">
                        <a href="{{ request()->fullUrlWithQuery(['status' => '', 'page' => 1]) }}"
                            class="px-3 py-1.5 font-medium transition-colors {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                            Semua
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'bermasalah', 'page' => 1]) }}"
                            class="px-3 py-1.5 font-medium transition-colors border-l border-gray-200 {{ request('status') === 'bermasalah' ? 'bg-red-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                            Bermasalah
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'selesai', 'page' => 1]) }}"
                            class="px-3 py-1.5 font-medium transition-colors border-l border-gray-200 {{ request('status') === 'selesai' ? 'bg-green-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                            Selesai
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'tidak_aktif', 'page' => 1]) }}"
                            class="px-3 py-1.5 font-medium transition-colors border-l border-gray-200 {{ request('status') === 'tidak_aktif' ? 'bg-slate-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                            Tidak Aktif
                        </a>
                    </div>

                    {{-- Search --}}
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" placeholder="Cari kendaraan..."
                            value="{{ request('search') }}"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                    </div>

                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fa fa-search text-xs"></i> Cari
                    </button>

                    @if(request('search') || request('status'))
                        <a href="{{ route('service-asuransi.index') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fa fa-rotate-left text-xs"></i> Reset
                        </a>
                    @endif

                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="w-8 px-3 py-4"></th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4 w-10">No</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Kendaraan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Asuransi</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Keterangan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">KM</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Tanggal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Periode</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Biaya</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Persetujuan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Kejadian</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Bukti Bayar</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Lampiran</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="serviceTableBody">
                        @forelse($data as $d)
                            @php
                                $kejadianCount = $d->kejadians->count();
                                $totalKejadian = $d->kejadians->sum('biaya');
                            @endphp
                            <tr class="border-t border-gray-100 odd:bg-white even:bg-gray-50 hover:bg-blue-50/40 transition-colors duration-100 cursor-pointer"
                                onclick="toggleKejadianRow('kejadian-row-{{ $d->id }}', this)">

                                {{-- CHEVRON --}}
                                <td class="px-3 py-4 text-center">
                                    <span id="chevron-{{ $d->id }}" class="inline-block text-gray-400 transition-transform duration-200 text-xs">
                                        <i class="fa fa-chevron-right"></i>
                                    </span>
                                </td>

                                {{-- NO --}}
                                <td class="px-5 py-4 text-xs text-gray-400 font-semibold">{{ $data->firstItem() + $loop->index }}</td>

                                {{-- KENDARAAN --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="fa fa-car text-blue-500 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $d->kendaraan->merk ?? '-' }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $d->kendaraan->nopol ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- ASURANSI --}}
                                <td class="px-5 py-4">
                                    @if($d->nama_asuransi)
                                        <div class="flex flex-col gap-1">
                                            <p class="text-sm font-semibold text-gray-800">{{ $d->nama_asuransi }}</p>
                                            @if($d->jenisAsuransi)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-purple-50 text-purple-600 border border-purple-200 w-fit">
                                                        
                                                    {{ $d->jenisAsuransi->nama_jenis }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-300 text-sm">—</span>
                                    @endif
                                </td>

                                {{-- KELUHAN --}}
                                    <td class="px-5 py-4 text-sm text-gray-700 max-w-xs">
                                        <span class="line-clamp-2">{{ $d->keterangan ?? '-' }}</span>
                                    </td>

                                    {{-- KM --}}
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ number_format($d->kilometer ?? 0, 0, ',', '.') }} km
                                    </td>

                                    {{-- TANGGAL --}}
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $d->tanggal_service ? \Carbon\Carbon::parse($d->tanggal_service)->format('d M Y') : '-' }}
                                    </td>

                                    {{-- PERIODE --}}
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        @if($d->periode_mulai && $d->periode_selesai)
                                            <div class="flex flex-col gap-0.5">
                                                <span class="text-xs text-gray-400">Mulai:</span>
                                                <span class="font-medium">{{ \Carbon\Carbon::parse($d->periode_mulai)->format('d M Y') }}</span>
                                                <span class="text-xs text-gray-400 mt-1">Selesai:</span>
                                                <span class="font-medium">{{ \Carbon\Carbon::parse($d->periode_selesai)->format('d M Y') }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>

                                    {{-- BIAYA --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-800">
                                            Rp {{ number_format($d->biaya ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php $st = $d->status ?? 'bermasalah'; @endphp
                                        @if($st === 'tidak_aktif')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border bg-slate-100 text-slate-500 border-slate-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Tidak Aktif
                                            </span>
                                        @else
                                        <button type="button"
                                            onclick="openStatusModal({{ $d->id }}, '{{ $st }}', '{{ addslashes(($d->kendaraan->merk ?? '') . ' - ' . ($d->kendaraan->nopol ?? '')) }}')"
                                            title="Klik untuk ubah status"
                                            class="status-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border cursor-pointer transition-all hover:shadow-sm
                                                {{ $st === 'selesai'
                                                    ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100'
                                                    : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $st === 'selesai' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                            {{ $st === 'selesai' ? 'Selesai' : 'Bermasalah' }}
                                        </button>
                                        @endif
                                    </td>

                                    {{-- PERSETUJUAN --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php $prst = $d->persetujuan ?? null; @endphp
                                        @if($prst === 'Disetujui')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                <i class="fa fa-check-circle text-[10px]"></i> Disetujui
                                            </span>
                                        @elseif($prst === 'Diajukan ke Pembayaran')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                <i class="fa fa-paper-plane text-[10px]"></i> Di Pembayaran
                                            </span>
                                        @elseif($prst === 'Ditolak')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                <i class="fa fa-times-circle text-[10px]"></i> Ditolak
                                            </span>
                                        @elseif($prst === 'Pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                                <i class="fa fa-clock text-[10px]"></i> Pending
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>

                                    {{-- KEJADIAN (badge count) --}}
                                    <td class="px-5 py-4" onclick="event.stopPropagation()">
                                        @if($kejadianCount > 0)
                                            <button type="button"
                                                onclick="toggleKejadianRow('kejadian-row-{{ $d->id }}', this.closest('tr').previousElementSibling ?? document.querySelector('tr:has(#chevron-{{ $d->id }})'))"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
                                                <i class="fa fa-list text-[10px]"></i> {{ $kejadianCount }} Kejadian
                                            </button>
                                        @else
                                            <span class="text-gray-300 text-sm">—</span>
                                        @endif
                                    </td>

                                    {{-- BUKTI BAYAR dari Pembayaran (per kejadian) --}}
                                    <td class="px-5 py-4" onclick="event.stopPropagation()">
                                        @php
                                            $approvalBukti = [];
                                            if ($d->pembayaran) {
                                                $latestAppr = $d->pembayaran->approvals
                                                    ->where('action', 'approved')->first()
                                                    ?? $d->pembayaran->latestApproval;
                                                if ($latestAppr && !empty($latestAppr->bukti_files)) {
                                                    $approvalBukti = is_array($latestAppr->bukti_files)
                                                        ? $latestAppr->bukti_files
                                                        : (json_decode($latestAppr->bukti_files, true) ?? []);
                                                }
                                            }
                                        @endphp
                                        @if(!empty($approvalBukti))
                                            <div class="flex flex-col gap-1">
                                                @foreach($approvalBukti as $bi => $bf)
                                                    @php
                                                        $bfPath = $bf['path'] ?? '';
                                                        $bfName = $bf['original_name'] ?? basename($bfPath);
                                                        $bfExt  = strtolower($bf['extension'] ?? pathinfo($bfPath, PATHINFO_EXTENSION));
                                                        $bfIsImg = in_array($bfExt, ['jpg','jpeg','png','webp','gif']);
                                                        $bfIcon  = $bfIsImg ? 'fa-image text-emerald-400' : ($bfExt === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                                                        $bfUrl   = $bfPath ? \Illuminate\Support\Facades\Storage::disk('public')->url($bfPath) : null;
                                                        // Cari label kejadian yang sesuai index
                                                        $bfLabel = $d->kejadians->get($bi)?->nama_kejadian ?? null;
                                                    @endphp
                                                    @if($bfUrl)
                                                        <div class="flex flex-col gap-0.5">
                                                            @if($bfLabel)
                                                                <span class="text-[10px] text-gray-400 font-medium truncate max-w-[130px]">{{ Str::limit($bfLabel, 16) }}</span>
                                                            @endif
                                                            <a href="{{ $bfUrl }}" target="_blank"
                                                                class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline truncate max-w-[130px]"
                                                                title="{{ $bfName }}">
                                                                <i class="fa {{ $bfIcon }} text-[9px]"></i>
                                                                {{ Str::limit($bfName, 16) }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @elseif($d->pembayaran && $d->pembayaran->bukti_pembayaran)
                                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($d->pembayaran->bukti_pembayaran) }}" target="_blank"
                                                class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline">
                                                <i class="fa fa-paperclip text-[10px]"></i> Lihat Bukti
                                            </a>
                                        @elseif($d->pembayaran && in_array($d->pembayaran->status, ['Diajukan', 'Pending']))
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-yellow-50 text-yellow-600 border border-yellow-200">
                                                <i class="fa fa-clock text-[9px]"></i> Menunggu
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-sm">—</span>
                                        @endif
                                    </td>

                                    {{-- LAMPIRAN per Kejadian (dari temp_files / lampiran kejadian) --}}
                                    <td class="px-5 py-4" onclick="event.stopPropagation()">
                                        @php
                                            // Kumpulkan lampiran per kejadian dari relasi kejadians
                                            $hasAnyLampiran = false;
                                        @endphp
                                        @if($d->kejadians->count() > 0)
                                            <div class="flex flex-col gap-2">
                                                @foreach($d->kejadians as $kej)
                                                    @php
                                                        $kejLampiran = is_array($kej->lampiran)
                                                            ? $kej->lampiran
                                                            : (json_decode($kej->getRawOriginal('lampiran') ?? '[]', true) ?? []);
                                                    @endphp
                                                    @if(!empty($kejLampiran))
                                                        @php $hasAnyLampiran = true; @endphp
                                                        <div class="flex flex-col gap-0.5">
                                                            <span class="text-[10px] font-semibold text-gray-500 truncate max-w-[130px]" title="{{ $kej->nama_kejadian }}">
                                                                {{ Str::limit($kej->nama_kejadian, 16) }}
                                                            </span>
                                                            @foreach($kejLampiran as $lf)
                                                                @php
                                                                    $lfPath  = $lf['path'] ?? '';
                                                                    $lfName  = $lf['original_name'] ?? $lf['name'] ?? basename($lfPath);
                                                                    $lfExt   = strtolower($lf['extension'] ?? pathinfo($lfPath, PATHINFO_EXTENSION));
                                                                    $lfIsImg = in_array($lfExt, ['jpg','jpeg','png','webp','gif']);
                                                                    $lfIcon  = $lfIsImg ? 'fa-image text-blue-400' : ($lfExt === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                                                                    // path bisa dari storage atau public
                                                                    $lfUrl   = $lfPath
                                                                        ? (str_starts_with($lfPath, 'http') ? $lfPath : \Illuminate\Support\Facades\Storage::disk('public')->url($lfPath))
                                                                        : null;
                                                                @endphp
                                                                @if($lfUrl)
                                                                    <a href="{{ $lfUrl }}" target="_blank"
                                                                        class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline truncate max-w-[130px]"
                                                                        title="{{ $lfName }}">
                                                                        <i class="fa {{ $lfIcon }} text-[9px]"></i>
                                                                        {{ Str::limit($lfName, 16) }}
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endforeach
                                                @if(!$hasAnyLampiran)
                                                    <span class="text-gray-300 text-sm">—</span>
                                                @endif
                                            </div>
                                        @else
                                            {{-- Fallback: lampiran lama dari kolom attachment --}}
                                            @php
                                                $lampiranList = is_array($d->attachment) ? $d->attachment : (json_decode($d->getRawOriginal('attachment'), true) ?? []);
                                            @endphp
                                            @if(!empty($lampiranList))
                                                <div class="flex flex-col gap-1">
                                                    @foreach($lampiranList as $lamp)
                                                        @php
                                                            $lPath  = $lamp['path'] ?? '';
                                                            $lName  = $lamp['name'] ?? basename($lPath);
                                                            $lExt   = strtolower(pathinfo($lPath, PATHINFO_EXTENSION));
                                                            $lIsImg = in_array($lExt, ['jpg','jpeg','png','webp']);
                                                            $lIcon  = $lIsImg ? 'fa-image' : ($lExt === 'pdf' ? 'fa-file-pdf' : 'fa-file');
                                                        @endphp
                                                        <a href="{{ asset($lPath) }}" target="_blank"
                                                            class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline truncate max-w-[130px]"
                                                            title="{{ $lName }}">
                                                            <i class="fa {{ $lIcon }} text-[9px]"></i>
                                                            {{ Str::limit($lName, 18) }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-300 text-sm">—</span>
                                            @endif
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="px-5 py-4" onclick="event.stopPropagation()">
                                        <div class="flex items-center justify-center gap-1.5">
                                            {{-- Ajukan Ulang: hanya untuk Ditolak di Pembayaran --}}
                                            @if($d->persetujuan === 'Ditolak' && $d->pembayaran_id)
                                            <button type="button"
                                                onclick="openSaAjukanUlangModal({{ $d->id }}, '{{ ($d->kendaraan->nopol ?? '-') . ' — ' . ($d->kendaraan->merk ?? '') }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-amber-100 text-amber-700 hover:bg-amber-200 transition-colors">
                                                <i class="fa fa-rotate-right text-xs"></i> Ajukan Ulang
                                            </button>
                                            @endif

                                            <button
                                                class="btn-edit inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                                data-id="{{ $d->id }}"
                                                data-kendaraan_id="{{ $d->kendaraan_id }}"
                                                data-nama_asuransi="{{ $d->nama_asuransi ?? '' }}"
                                                data-jenis_asuransi_id="{{ $d->jenis_asuransi_id ?? '' }}"
                                                data-tanggal_service="{{ $d->tanggal_service }}"
                                                data-periode_mulai="{{ $d->periode_mulai }}"
                                                data-periode_selesai="{{ $d->periode_selesai }}"
                                                data-kilometer="{{ $d->kilometer }}"
                                                data-biaya="{{ $d->biaya }}"
                                                data-keterangan="{{ $d->keterangan }}">
                                                <i class="fa fa-edit text-xs"></i> Edit
                                            </button>

                                            <form action="{{ route('service-asuransi.destroy', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                                    <i class="fa fa-trash text-xs"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- EXPANDABLE KEJADIAN ROW --}}
                                <tr id="kejadian-row-{{ $d->id }}" style="display:none;" class="bg-slate-50 border-t border-slate-100">
                                    <td colspan="15" class="px-6 py-4">
                                        @if($kejadianCount > 0)
                                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                                <table class="w-full text-xs">
                                                    <thead>
                                                        <tr class="bg-slate-100 text-gray-500">
                                                            <th class="text-left px-3 py-2 font-semibold w-8">No</th>
                                                            <th class="text-left px-3 py-2 font-semibold">Nama Kejadian</th>
                                                            <th class="text-right px-3 py-2 font-semibold">Biaya</th>
                                                            <th class="text-left px-3 py-2 font-semibold">Lampiran</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($d->kejadians as $i => $kej)
                                                            <tr class="border-t border-slate-100 {{ $loop->even ? 'bg-white' : 'bg-slate-50' }}">
                                                                <td class="px-3 py-2 text-gray-400">{{ $i + 1 }}</td>
                                                                <td class="px-3 py-2 font-medium text-gray-800">{{ $kej->nama_kejadian }}</td>
                                                                <td class="px-3 py-2 text-right font-semibold text-gray-700">
                                                                    Rp {{ number_format($kej->biaya, 0, ',', '.') }}
                                                                </td>
                                                                <td class="px-3 py-2">
                                                                    @if($kej->lampiran && count($kej->lampiran) > 0)
                                                                        <div class="flex flex-wrap gap-1">
                                                                            @foreach($kej->lampiran as $lamp)
                                                                                @php
                                                                                    $lPath = $lamp['path'] ?? '';
                                                                                    $lName = $lamp['name'] ?? basename($lPath);
                                                                                    $lExt  = strtolower(pathinfo($lPath, PATHINFO_EXTENSION));
                                                                                    $lIsImg = in_array($lExt, ['jpg','jpeg','png','webp']);
                                                                                @endphp
                                                                                <a href="{{ asset($lPath) }}" target="_blank"
                                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200"
                                                                                    title="{{ $lName }}">
                                                                                    <i class="fa {{ $lIsImg ? 'fa-image' : ($lExt === 'pdf' ? 'fa-file-pdf' : 'fa-file') }} text-[9px]"></i>
                                                                                    {{ Str::limit($lName, 20) }}
                                                                                </a>
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <span class="text-gray-300">—</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="bg-slate-100 border-t border-slate-200">
                                                            <td colspan="2" class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Total:</td>
                                                            <td class="px-3 py-2 text-right text-xs font-bold text-gray-800">
                                                                Rp {{ number_format($d->kejadians->sum('biaya'), 0, ',', '.') }}
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4 text-xs text-gray-400">
                                                <i class="fa fa-list text-gray-300 text-xl mb-1 block"></i>
                                                Belum ada kejadian untuk record ini
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                        @empty
                            <tr>
                                <td colspan="13" class="px-5 py-12 text-center">
                                    <p class="text-sm text-gray-500">Belum ada data service asuransi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="py-3 border-t border-gray-100"><x-pagination :paginator="$data" /></div>
                <div id="noResultRow" class="hidden px-5 py-12 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fa fa-search text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-500">Tidak ada hasil yang cocok</p>
                        <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian atau filter</p>
                    </div>
                </div>
            </div>

            {{-- Tidak perlu tableFooter JS karena pagination sudah server-side --}}

        </div>
    </div>


    {{-- ============================================================
    MODAL TAMBAH / EDIT
    ============================================================ --}}
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 overflow-y-auto py-6"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 flex flex-col">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 shrink-0">
                <div>
                    <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Data Service Asuransi</h2>
                    <p id="modalDesc" class="text-xs text-gray-500 mt-0.5">Isi data service & asuransi kendaraan</p>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="form" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 space-y-4 overflow-y-auto max-h-[80vh]">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan</label>
                    <select name="kendaraan_id" id="kendaraan_id"
                        onchange="onKendaraanChange(this)"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}" data-nopol="{{ $k->nopol }}">{{ $k->merk }} - {{ $k->nopol }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Asuransi</label>
                    <select name="nama_asuransi" id="nama_asuransi" onchange="handleNamaAsuransiChange()"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">-- Pilih Nama Asuransi --</option>
                        @foreach ($asuransi as $a)
                            <option value="{{ $a->nama_asuransi }}">{{ $a->nama_asuransi }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Pilih nama asuransi, jenis akan otomatis terisi</p>
                </div>

                <div id="jenisAsuransiWrapper" style="display: none;">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Asuransi</label>
                    <select name="jenis_asuransi_id" id="jenis_asuransi_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">-- Pilih Jenis --</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Jika ada beberapa jenis, pilih yang sesuai</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Service</label>
                    <input type="date" name="tanggal_service" id="tanggal_service"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Periode Mulai</label>
                        <input type="date" name="periode_mulai" id="periode_mulai"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Periode Selesai</label>
                        <input type="date" name="periode_selesai" id="periode_selesai"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kilometer</label>
                    <div class="relative">
                        <input type="number" name="kilometer" id="kilometer" placeholder="Otomatis dari kendaraan"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-blue-50">
                        <span id="km_loading" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-xs text-blue-500">
                            <i class="fa fa-spinner fa-spin"></i>
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Otomatis terisi dari kilometer sekarang kendaraan</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya</label>
                    <div class="flex items-center gap-3 px-3 py-2 bg-blue-50 border border-blue-100 rounded-xl text-xs text-gray-600">
                        Total Biaya (auto-sum dari kejadian):
                        <span id="total-biaya-display" class="font-bold text-blue-700 ml-1">Rp 0</span>
                    </div>
                    <input type="hidden" name="biaya" id="biaya" value="0">
                </div>

                {{-- SECTION KEJADIAN --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-gray-600">Kejadian</label>
                        <button type="button" onclick="addKejadian()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fa fa-plus text-xs"></i> Tambah Kejadian
                        </button>
                    </div>
                    <div id="kejadian-container" class="space-y-3"></div>
                    <div id="kejadian-empty" class="text-center py-4 text-xs text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                        <i class="fa fa-list text-gray-300 text-xl mb-1 block"></i>
                        Klik "+ Tambah Kejadian" untuk menambahkan kejadian
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl">
                    <i class="fa fa-save text-sm"></i> Simpan
                </button>
            </form>
        </div>
    </div>


    {{-- ============================================================
    MODAL DETAIL BUKTI
    ============================================================ --}}
    <div id="modalDetailBukti" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-4"
        style="backdrop-filter:blur(3px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Detail Bukti</h2>
                    <p id="detailBuktiSubtitle" class="text-xs text-gray-500 mt-0.5"></p>
                </div>
                <button onclick="closeDetailBukti()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-xl leading-none">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div id="detailBuktiContent" class="overflow-y-auto p-6 flex-1">
                {{-- Diisi via JS --}}
            </div>

        </div>
    </div>

    {{-- ============================================================
    MODAL DETAIL ATTACHMENT
    ============================================================ --}}
    <div id="modalDetailAttachment" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-4"
        style="backdrop-filter:blur(3px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Detail Attachment</h2>
                    <p id="detailAttachmentSubtitle" class="text-xs text-gray-500 mt-0.5"></p>
                </div>
                <button onclick="closeDetailAttachment()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-xl leading-none">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div id="detailAttachmentContent" class="overflow-y-auto p-6 flex-1">
                {{-- Diisi via JS --}}
            </div>

        </div>
    </div>

    {{-- Lightbox overlay untuk zoom gambar --}}
    <div id="lightbox" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/80"
        onclick="closeLightbox()">
        <img id="lightboxImg" src="" alt="" class="max-w-[90vw] max-h-[90vh] rounded-xl shadow-2xl object-contain">
        <button onclick="closeLightbox()"
            class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center text-lg transition-colors">
            <i class="fa fa-times"></i>
        </button>
    </div>


    {{-- ============================================================
    POPUP ALERT
    ============================================================ --}}
    @if (session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
            <div id="alertBox"
                class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">
                @if (session('success'))
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @elseif (session('error'))
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <button onclick="closeAlert()"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%239ca3af'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            padding-right: 24px !important;
        }
    </style>

    <script>
    // =================================================================
    // MODAL TAMBAH / EDIT
    // =================================================================
    const modal = document.getElementById('modal');

    function setModalMode(mode) {
        document.getElementById('modalTitle').textContent = mode === 'edit' ? 'Edit Data Service Asuransi' : 'Tambah Data Service Asuransi';
        document.getElementById('modalDesc').textContent  = mode === 'edit' ? 'Ubah data service & asuransi kendaraan' : 'Isi data service & asuransi kendaraan';
    }

    function openModal() {
        setModalMode('add');
        document.getElementById('form').reset();
        document.getElementById('form').action = '/admin/service-asuransi';
        const mp = document.getElementById('method-put');
        if (mp) mp.remove();
        // Reset jenis asuransi dropdown
        document.getElementById('jenisAsuransiWrapper').style.display = 'none';
        document.getElementById('jenis_asuransi_id').innerHTML = '<option value="">-- Pilih Jenis Asuransi --</option>';
        // Reset kejadian dan total biaya
        resetKejadian();
        // Auto-fill km dari kendaraan pertama
        const firstKendaraan = document.getElementById('kendaraan_id');
        if (firstKendaraan && firstKendaraan.value) {
            fetchKendaraanData(firstKendaraan.value);
        }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    // =================================================================
    // AJAX KILOMETER + SLUG KETERANGAN
    // =================================================================
    function onKendaraanChange(sel) {
        const id = sel.value;
        if (!id) return;
        fetchKendaraanData(id);
    }

    function fetchKendaraanData(kendaraanId) {
        const kmInput   = document.getElementById('kilometer');
        const ketInput  = document.getElementById('keterangan');
        const loading   = document.getElementById('km_loading');
        const sel       = document.getElementById('kendaraan_id');

        if (loading) loading.classList.remove('hidden');

        fetch('/admin/service-asuransi/kendaraan/' + kendaraanId + '/data')
            .then(r => r.json())
            .then(function(data) {
                if (kmInput) kmInput.value = data.kilometer_sekarang || 0;
                if (ketInput) ketInput.value = 'servis asuransi-' + (data.nopol || '');
                if (loading) loading.classList.add('hidden');
            })
            .catch(function() {
                if (loading) loading.classList.add('hidden');
            });
    }

    // -- EDIT BUTTON --------------------------------------------------
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit');
        if (!btn) return;

        setModalMode('edit');

        const form = document.getElementById('form');
        form.action = '/admin/service-asuransi/' + btn.dataset.id;

        const existingMp = document.getElementById('method-put');
        if (existingMp) existingMp.remove();

        const input = document.createElement('input');
        input.type = 'hidden'; input.name = '_method'; input.value = 'PUT'; input.id = 'method-put';
        form.appendChild(input);

        document.getElementById('biaya').value          = btn.dataset.biaya;
        document.getElementById('keterangan').value     = btn.dataset.keterangan;
        document.getElementById('kendaraan_id').value   = btn.dataset.kendaraan_id;        document.getElementById('tanggal_service').value = btn.dataset.tanggal_service;
        document.getElementById('periode_mulai').value  = btn.dataset.periode_mulai || '';
        document.getElementById('periode_selesai').value = btn.dataset.periode_selesai || '';
        document.getElementById('kilometer').value      = btn.dataset.kilometer;
        
        // Set nama asuransi dan render jenis dropdown
        const namaAsuransi    = btn.dataset.nama_asuransi    || '';
        const jenisAsuransiId = btn.dataset.jenis_asuransi_id || '';

        document.getElementById('nama_asuransi').value = namaAsuransi;
        renderJenisDropdown(namaAsuransi, jenisAsuransiId);

        // Reset kejadian container (edit mode tidak pre-fill kejadian)
        resetKejadian();
        // Update total display dari biaya tersimpan
        const savedBiaya = parseInt(btn.dataset.biaya || 0);
        document.getElementById('total-biaya-display').textContent = 'Rp ' + savedBiaya.toLocaleString('id-ID');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });


    // =================================================================
    // HANDLE NAMA ASURANSI & JENIS ASURANSI
    // Data jenis asuransi di-embed langsung dari Blade (tidak perlu AJAX)
    // =================================================================
    const ALL_JENIS = @json($jenisAsuransi->map(fn($j) => ['id' => $j->id, 'nama_jenis' => $j->nama_jenis]));

    function handleNamaAsuransiChange() {
        renderJenisDropdown(
            document.getElementById('nama_asuransi').value,
            null
        );
    }

    function renderJenisDropdown(namaAsuransi, selectedId) {
        const wrapper = document.getElementById('jenisAsuransiWrapper');
        const select  = document.getElementById('jenis_asuransi_id');

        if (!namaAsuransi) {
            wrapper.style.display = 'none';
            select.innerHTML = '<option value="">-- Pilih Jenis --</option>';
            return;
        }

        // Tampilkan semua jenis dari master (bukan filter per nama asuransi)
        let options = '<option value="">-- Pilih Jenis Asuransi --</option>';
        ALL_JENIS.forEach(function(j) {
            const sel = (selectedId && String(selectedId) === String(j.id)) ? ' selected' : '';
            options += `<option value="${j.id}"${sel}>${j.nama_jenis}</option>`;
        });
        select.innerHTML = options;
        wrapper.style.display = 'block';
    }


    // =================================================================
    // KEJADIAN DYNAMIC ROWS
    // =================================================================
    let kejadianIndex = 0;

    function resetKejadian() {
        kejadianIndex = 0;
        document.getElementById('kejadian-container').innerHTML = '';
        document.getElementById('kejadian-empty').style.display = '';
        document.getElementById('biaya').value = '0';
        document.getElementById('total-biaya-display').textContent = 'Rp 0';
    }

    function addKejadian() {
        const idx       = kejadianIndex++;
        const container = document.getElementById('kejadian-container');
        document.getElementById('kejadian-empty').style.display = 'none';

        const row = document.createElement('div');
        row.id        = 'kejadian-row-' + idx;
        row.className = 'bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3';
        row.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-600">Kejadian #${idx + 1}</span>
                <button type="button" onclick="removeKejadian(${idx})"
                    class="w-6 h-6 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center text-xs transition-colors">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Kejadian <span class="text-red-400">*</span></label>
                    <input type="text" name="kejadians[${idx}][nama_kejadian]" required
                        placeholder="cth: Ganti Kaca Depan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 mb-1 block">Biaya (Rp)</label>
                    <input type="number" name="kejadians[${idx}][biaya]" min="0" value="0"
                        onchange="recalcTotalBiaya()" oninput="recalcTotalBiaya()"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">
                    Lampiran <span class="text-red-400">*</span> <span class="text-gray-400 font-normal text-[10px]">(wajib — foto/dokumen bukti kejadian)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer border border-dashed border-blue-200 hover:border-blue-400 bg-white hover:bg-blue-50/40 rounded-lg px-3 py-2.5 transition-colors">
                    <i class="fa fa-paperclip text-blue-400 text-sm"></i>
                    <span class="text-xs text-gray-500">Klik untuk pilih file...</span>
                    <input type="file" name="kejadians[${idx}][lampiran][]" multiple required
                        accept="image/*,.pdf,.doc,.docx"
                        onchange="updateLampiranList(${idx}, this)"
                        class="hidden">
                </label>
                <div id="lampiran-list-${idx}" class="mt-1 space-y-1"></div>
                <p class="text-[10px] text-red-500 mt-1"><i class="fa fa-circle-exclamation"></i> Wajib upload minimal 1 lampiran per kejadian</p>
            </div>
        `;
        container.appendChild(row);
        recalcTotalBiaya();
    }

    function removeKejadian(idx) {
        document.getElementById('kejadian-row-' + idx)?.remove();
        recalcTotalBiaya();
        if (!document.querySelector('[id^="kejadian-row-"]')) {
            document.getElementById('kejadian-empty').style.display = '';
        }
    }

    function recalcTotalBiaya() {
        let total = 0;
        document.querySelectorAll('#kejadian-container [name$="[biaya]"]').forEach(inp => {
            total += parseInt(inp.value || 0);
        });
        document.getElementById('biaya').value = total;
        document.getElementById('total-biaya-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function updateLampiranList(idx, input) {
        const list = document.getElementById('lampiran-list-' + idx);
        if (!list) return;
        list.innerHTML = Array.from(input.files).map(f =>
            `<div class="flex items-center gap-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded px-2 py-1">
                <i class="fa fa-paperclip text-[10px] text-gray-400"></i>
                <span class="truncate">${f.name}</span>
                <span class="ml-auto text-[10px] text-gray-400">${(f.size/1024).toFixed(0)} KB</span>
            </div>`
        ).join('');
    }

    // =================================================================
    // TOGGLE KEJADIAN ROW (EXPANDABLE)
    // =================================================================
    function toggleKejadianRow(rowId, headerTr) {
        const row = document.getElementById(rowId);
        if (!row) return;
        const id    = rowId.replace('kejadian-row-', '');
        const ch    = document.getElementById('chevron-' + id);
        const isOpen = row.style.display !== 'none';
        row.style.display = isOpen ? 'none' : '';
        if (ch) ch.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
    }


    // =================================================================
    // MODAL DETAIL ATTACHMENT (kept for backward compat lightbox)
    // =================================================================
    function openDetailAttachment(recordId, attachmentArr, label) {
        document.getElementById('detailAttachmentSubtitle').textContent = label || '';

        const content = document.getElementById('detailAttachmentContent');
        content.innerHTML = '';

        if (!Array.isArray(attachmentArr) || attachmentArr.length === 0) {
            content.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Tidak ada file attachment</p>';
        } else {
            const files = attachmentArr.map(function(f) {
                const path     = (typeof f === 'object') ? (f.path || '') : f;
                const origName = (typeof f === 'object' && f.name) ? f.name : path.split('/').pop();
                return { path, name: origName };
            }).filter(f => f.path);

            const images = files.filter(f => /\.(jpg|jpeg|png|webp)$/i.test(f.path));
            const docs   = files.filter(f => !/\.(jpg|jpeg|png|webp)$/i.test(f.path));

            if (images.length > 0) {
                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4';

                images.forEach(function(f) {
                    const wrap = document.createElement('div');
                    wrap.className = 'group relative rounded-xl overflow-hidden border border-purple-100 bg-purple-50 cursor-pointer';
                    wrap.onclick   = function() { openLightbox('/' + f.path); };
                    wrap.innerHTML = `
                        <img src="/${f.path}" alt="${f.name}"
                            class="w-full h-36 object-cover transition-transform duration-200 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                            <i class="fa fa-expand text-white opacity-0 group-hover:opacity-100 text-xl transition-opacity drop-shadow"></i>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 px-2 py-1.5 bg-gradient-to-t from-black/60 to-transparent">
                            <p class="text-[10px] text-white/90 truncate font-medium" title="${f.name}">${f.name}</p>
                            <a href="/${f.path}" download="${f.name}" onclick="event.stopPropagation()"
                                class="text-[10px] text-white/60 hover:text-white flex items-center gap-1 mt-0.5">
                                <i class="fa fa-download text-[9px]"></i> Unduh
                            </a>
                        </div>`;
                    grid.appendChild(wrap);
                });

                content.appendChild(grid);
            }

            if (docs.length > 0) {
                if (images.length > 0) {
                    const hr = document.createElement('hr');
                    hr.className = 'border-gray-100 mb-4';
                    content.appendChild(hr);
                }

                const docList = document.createElement('div');
                docList.className = 'space-y-2';

                docs.forEach(function(f) {
                    const ext   = f.path.split('.').pop().toLowerCase();
                    const isPdf = ext === 'pdf';

                    const item = document.createElement('a');
                    item.href   = '/' + f.path;
                    item.target = '_blank';
                    item.className = `flex items-center gap-3 px-4 py-3 rounded-xl border transition-colors hover:bg-gray-50 ${isPdf ? 'border-red-100 bg-red-50/40 text-red-600' : 'border-purple-100 bg-purple-50/40 text-purple-600'}`;
                    item.innerHTML = `
                        <i class="fa ${isPdf ? 'fa-file-pdf' : 'fa-file-word'} text-xl flex-shrink-0"></i>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold truncate" title="${f.name}">${f.name}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Klik untuk buka</p>
                        </div>
                        <i class="fa fa-external-link-alt text-xs text-gray-400 flex-shrink-0"></i>`;
                    docList.appendChild(item);
                });

                content.appendChild(docList);
            }
        }

        const detailModal = document.getElementById('modalDetailAttachment');
        detailModal.classList.remove('hidden');
        detailModal.classList.add('flex');
    }

    function closeDetailAttachment() {
        const m = document.getElementById('modalDetailAttachment');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    document.getElementById('modalDetailAttachment').addEventListener('click', function(e) {
        if (e.target === this) closeDetailAttachment();
    });


    // =================================================================
    // MODAL DETAIL BUKTI
    // =================================================================
    function openDetailBukti(recordId, buktiArr, label) {
        document.getElementById('detailBuktiSubtitle').textContent = label || '';

        const content = document.getElementById('detailBuktiContent');
        content.innerHTML = '';

        if (!Array.isArray(buktiArr) || buktiArr.length === 0) {
            content.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Tidak ada file bukti</p>';
        } else {
            // Normalise ke {path, name}
            const files = buktiArr.map(function(f) {
                const path     = (typeof f === 'object') ? (f.path || '') : f;
                const origName = (typeof f === 'object' && f.name) ? f.name : path.split('/').pop();
                return { path, name: origName };
            }).filter(f => f.path);

            const images = files.filter(f => /\.(jpg|jpeg|png|webp)$/i.test(f.path));
            const docs   = files.filter(f => !/\.(jpg|jpeg|png|webp)$/i.test(f.path));

            // --- GRID GAMBAR ---
            if (images.length > 0) {
                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4';

                images.forEach(function(f) {
                    const wrap = document.createElement('div');
                    wrap.className = 'group relative rounded-xl overflow-hidden border border-gray-100 bg-gray-50 cursor-pointer';
                    wrap.onclick   = function() { openLightbox('/' + f.path); };
                    wrap.innerHTML = `
                        <img src="/${f.path}" alt="${f.name}"
                            class="w-full h-36 object-cover transition-transform duration-200 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                            <i class="fa fa-expand text-white opacity-0 group-hover:opacity-100 text-xl transition-opacity drop-shadow"></i>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 px-2 py-1.5 bg-gradient-to-t from-black/60 to-transparent">
                            <p class="text-[10px] text-white/90 truncate font-medium" title="${f.name}">${f.name}</p>
                            <a href="/${f.path}" download="${f.name}" onclick="event.stopPropagation()"
                                class="text-[10px] text-white/60 hover:text-white flex items-center gap-1 mt-0.5">
                                <i class="fa fa-download text-[9px]"></i> Unduh
                            </a>
                        </div>`;
                    grid.appendChild(wrap);
                });

                content.appendChild(grid);
            }

            // --- LIST DOKUMEN ---
            if (docs.length > 0) {
                if (images.length > 0) {
                    const hr = document.createElement('hr');
                    hr.className = 'border-gray-100 mb-4';
                    content.appendChild(hr);
                }

                const docList = document.createElement('div');
                docList.className = 'space-y-2';

                docs.forEach(function(f) {
                    const ext   = f.path.split('.').pop().toLowerCase();
                    const isPdf = ext === 'pdf';

                    const item = document.createElement('a');
                    item.href   = '/' + f.path;
                    item.target = '_blank';
                    item.className = `flex items-center gap-3 px-4 py-3 rounded-xl border transition-colors hover:bg-gray-50 ${isPdf ? 'border-red-100 bg-red-50/40 text-red-600' : 'border-blue-100 bg-blue-50/40 text-blue-600'}`;
                    item.innerHTML = `
                        <i class="fa ${isPdf ? 'fa-file-pdf' : 'fa-file-word'} text-xl flex-shrink-0"></i>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold truncate" title="${f.name}">${f.name}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Klik untuk buka</p>
                        </div>
                        <i class="fa fa-external-link-alt text-xs text-gray-400 flex-shrink-0"></i>`;
                    docList.appendChild(item);
                });

                content.appendChild(docList);
            }
        }

        const detailModal = document.getElementById('modalDetailBukti');
        detailModal.classList.remove('hidden');
        detailModal.classList.add('flex');
    }

    function closeDetailBukti() {
        const m = document.getElementById('modalDetailBukti');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    document.getElementById('modalDetailBukti').addEventListener('click', function(e) {
        if (e.target === this) closeDetailBukti();
    });

    // =================================================================
    // LIGHTBOX
    // =================================================================
    function openLightbox(src) {
        document.getElementById('lightboxImg').src = src;
        const lb = document.getElementById('lightbox');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
    }

    function closeLightbox() {
        const lb = document.getElementById('lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
        document.getElementById('lightboxImg').src = '';
    }


    // =================================================================
    // POPUP ALERT
    // =================================================================
    (function() {
        const overlay = document.getElementById('alertOverlay');
        const box     = document.getElementById('alertBox');
        if (!overlay) return;
        setTimeout(function() {
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
            box.style.transform = 'translateY(0)';
        }, 80);
        const timer = setTimeout(closeAlert, 4500);
        overlay.addEventListener('click', function(e) { if (e.target === overlay) closeAlert(); });
        function closeAlert() {
            clearTimeout(timer);
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            box.style.transform = 'translateY(-16px)';
        }
        window.closeAlert = closeAlert;
    })();

    // =================================================================
    // STATUS MODAL
    // =================================================================
    </script>

    {{-- ============================================================
    MODAL UBAH STATUS
    ============================================================ --}}
    <div id="modalStatus" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40 p-4"
        style="backdrop-filter:blur(3px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-auto" style="animation:slideUp .18s ease">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Ubah Status</h3>
                    <p class="text-xs text-gray-500 mt-0.5" id="statusModalDesc"></p>
                </div>
                <button onclick="closeStatusModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formStatus" method="POST" action="" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Penanganan</label>
                    <select name="status" id="statusSelect"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="bermasalah"> Bermasalah — mobil masih bermasalah</option>
                        <option value="selesai"> Selesai — masalah sudah ditangani</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="closeStatusModal()"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-500 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors">
                        <i class="fa fa-save text-xs mr-1"></i> Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
    // =================================================================
    // STATUS MODAL HANDLER
    // =================================================================
    function openStatusModal(id, currentStatus, label) {
        document.getElementById('statusModalDesc').textContent = label || '';
        document.getElementById('statusSelect').value = currentStatus || 'bermasalah';
        document.getElementById('formStatus').action = '/admin/service-asuransi/' + id + '/status';

        const m = document.getElementById('modalStatus');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }

    function closeStatusModal() {
        const m = document.getElementById('modalStatus');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    document.getElementById('modalStatus').addEventListener('click', function(e) {
        if (e.target === this) closeStatusModal();
    });
    </script>

    <script>
    // ========================================
    // CHART INITIALIZATION — Service Asuransi
    // ========================================
    const chartManager = new ChartManager();

    document.addEventListener('DOMContentLoaded', function() {
        initServiceAsuransiCharts({ filter_type: 'month' });

        document.addEventListener('chartFilterChange', function(e) {
            if (e.detail.filterId === 'serviceAsuransiChartFilter') {
                const filters = {
                    filter_type: e.detail.filterType,
                    start_date:  e.detail.startDate,
                    end_date:    e.detail.endDate,
                };
                updateServiceAsuransiCharts(filters);
            }
        });
    });

    async function initServiceAsuransiCharts(filters) {
        try {
            await chartManager.initChartsFromAPI('service-asuransi', {
                pie:  'serviceAsuransiPieChart',
                bar:  'serviceAsuransiBarChart',
                line: 'serviceAsuransiLineChart',
            }, filters, { accentLine: true });
        } catch (error) {
            console.error('Error loading service asuransi charts:', error);
        }
    }

    async function updateServiceAsuransiCharts(filters) {
        try {
            const isScrollable = filters.filter_type === 'custom';
            await chartManager.updateChartsFromAPI('service-asuransi', {
                pie:  'serviceAsuransiPieChart',
                bar:  'serviceAsuransiBarChart',
                line: 'serviceAsuransiLineChart',
            }, filters,
            { scrollable: isScrollable, accentLine: true },
            { scrollable: isScrollable });
        } catch (error) {
            console.error('Error updating service asuransi charts:', error);
        }
    }
    </script>

{{-- MODAL: AJUKAN ULANG SERVICE ASURANSI (dari halaman Service Asuransi) --}}
<div id="modalSaAjukanUlang" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-base font-bold text-gray-800">Ajukan Ulang — Service Asuransi</h3>
                <p class="text-sm text-gray-500 mt-0.5" id="saAuKendaraan">-</p>
            </div>
            <button onclick="closeSaAjukanUlangModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div id="saAuLoading" class="flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
                <p class="text-sm">Memuat data...</p>
            </div>
        </div>

        <div id="saAuBody" class="hidden flex-1 overflow-y-auto flex flex-col">
            <div class="px-6 pt-4 pb-2">
                <div id="saAuCatatan" class="hidden bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-xs text-red-700 mb-2"></div>
            </div>

            <form id="saAuForm" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto px-6 pb-4 space-y-4">
                @csrf
                <input type="hidden" name="kendaraan_id"    id="saAuKendaraanId">
                <input type="hidden" name="nama_asuransi"   id="saAuNamaAsuransi">
                <input type="hidden" name="tanggal_service" id="saAuTglService">
                <input type="hidden" name="periode_mulai"   id="saAuPeriodeMulai">
                <input type="hidden" name="periode_selesai" id="saAuPeriodeSelesai">
                <input type="hidden" name="kilometer"       id="saAuKilometer">

                <div class="flex items-center gap-3 px-3 py-2 bg-blue-50 border border-blue-100 rounded-xl text-xs text-gray-600">
                    Total Biaya:
                    <span id="saAuTotalBiaya" class="font-bold text-blue-700 ml-1">Rp 0</span>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-gray-600">Daftar Kejadian</label>
                        <button type="button" onclick="addSaAuKejadian()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fa fa-plus text-xs"></i> Tambah
                        </button>
                    </div>
                    <div id="saAuKejadianContainer" class="space-y-3"></div>
                </div>
            </form>

            <div class="border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
                <button type="button" onclick="closeSaAjukanUlangModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">
                    Batal
                </button>
                <button type="button" id="saAuSubmitBtn" onclick="submitSaAjukanUlang()"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl py-2.5">
                    <i class="fa fa-rotate-right"></i> Ajukan Ulang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let _saAuId = null, _saAuKejIdx = 0;

function openSaAjukanUlangModal(saId, kendaraan) {
    _saAuId = saId;
    document.getElementById('saAuKendaraan').textContent = kendaraan;
    document.getElementById('saAuLoading').classList.remove('hidden');
    document.getElementById('saAuBody').classList.add('hidden');
    const m = document.getElementById('modalSaAjukanUlang');
    m.classList.remove('hidden'); m.classList.add('flex');

    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    fetch('/admin/service-asuransi/' + saId + '/ajukan-ulang', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) throw new Error(data.message || 'Gagal memuat data');
        renderSaAuForm(data);
        document.getElementById('saAuLoading').classList.add('hidden');
        document.getElementById('saAuBody').classList.remove('hidden');
    })
    .catch(function(err) {
        document.getElementById('saAuLoading').innerHTML =
            '<div class="text-center text-red-500 py-8 px-6"><i class="fa fa-exclamation-triangle text-xl mb-2 block"></i><p class="text-sm">' + err.message + '</p></div>';
    });
}

function renderSaAuForm(data) {
    const catatanEl = document.getElementById('saAuCatatan');
    if (data.catatan_penolakan) {
        catatanEl.textContent = 'Alasan penolakan: ' + data.catatan_penolakan;
        catatanEl.classList.remove('hidden');
    } else {
        catatanEl.classList.add('hidden');
    }

    document.getElementById('saAuKendaraanId').value    = data.kendaraan_id   || '';
    document.getElementById('saAuNamaAsuransi').value   = data.nama_asuransi  || '';
    document.getElementById('saAuTglService').value     = data.tanggal_service|| '';
    document.getElementById('saAuPeriodeMulai').value   = data.periode_mulai  || '';
    document.getElementById('saAuPeriodeSelesai').value = data.periode_selesai|| '';
    document.getElementById('saAuKilometer').value      = data.kilometer      || '';

    const container = document.getElementById('saAuKejadianContainer');
    container.innerHTML = '';
    _saAuKejIdx = 0;
    (data.kejadians || []).forEach(function(kej, idx) {
        renderSaAuKejadian(container, idx, kej);
        _saAuKejIdx = idx + 1;
    });
    updateSaAuTotal();
}

function renderSaAuKejadian(container, idx, kej) {
    const lampiranLama = kej.lampiran_existing || [];
    let lampiranHtml = '';
    if (lampiranLama.length) {
        lampiranHtml = '<div class="mt-1 space-y-0.5">' +
            lampiranLama.map(function(lf) {
                const path = lf.path || ''; const name = lf.original_name || path.split('/').pop();
                const ext  = (lf.extension || '').toLowerCase();
                const icon = ['jpg','jpeg','png','webp'].includes(ext) ? 'fa-image text-blue-400' : (ext === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                const url  = path ? '/storage/' + path : null;
                return url ? '<a href="' + url + '" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline"><i class="fa ' + icon + ' text-[9px]"></i><span class="truncate max-w-[200px]">' + name + '</span></a><br>' : '';
            }).join('') + '</div>';
    }
    const div = document.createElement('div');
    div.id = 'sa-au-kej-' + idx;
    div.className = 'bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3';
    div.innerHTML = `
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-gray-600">Kejadian #${idx + 1}</span>
            <button type="button" onclick="removeSaAuKejadian(${idx})" class="w-6 h-6 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center text-xs"><i class="fa fa-times"></i></button>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Kejadian <span class="text-red-400">*</span></label>
                <input type="text" name="kejadians[${idx}][nama_kejadian]" required value="${(kej.nama_kejadian||'').replace(/"/g,'&quot;')}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Biaya (Rp)</label>
                <input type="number" name="kejadians[${idx}][biaya]" min="0" value="${kej.biaya||0}"
                    onchange="updateSaAuTotal()" oninput="updateSaAuTotal()"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 bg-white">
            </div>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 mb-1">Lampiran Lama</p>
            ${lampiranHtml || '<p class="text-xs text-gray-400">Tidak ada lampiran lama</p>'}
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500 mb-1 block">Tambah Lampiran Baru <span class="text-gray-400 font-normal text-[10px]">(opsional)</span></label>
            <label class="flex items-center gap-2 cursor-pointer border border-dashed border-blue-200 hover:border-blue-400 bg-white rounded-lg px-3 py-2.5 transition-colors">
                <i class="fa fa-paperclip text-blue-400"></i><span class="text-xs text-gray-500">Klik untuk pilih file...</span>
                <input type="file" name="kejadians[${idx}][lampiran][]" multiple accept="image/*,.pdf,.doc,.docx"
                    onchange="updateSaAuLampiranList(${idx}, this)" class="hidden">
            </label>
            <div id="sa-au-lampiran-${idx}" class="mt-1 space-y-1"></div>
        </div>`;
    container.appendChild(div);
}

function removeSaAuKejadian(idx) { document.getElementById('sa-au-kej-' + idx)?.remove(); updateSaAuTotal(); }

function addSaAuKejadian() {
    const c = document.getElementById('saAuKejadianContainer');
    renderSaAuKejadian(c, _saAuKejIdx++, {});
}

function updateSaAuTotal() {
    let t = 0;
    document.querySelectorAll('#saAuKejadianContainer [name$="[biaya]"]').forEach(i => t += parseInt(i.value||0));
    document.getElementById('saAuTotalBiaya').textContent = 'Rp ' + t.toLocaleString('id-ID');
}

function updateSaAuLampiranList(idx, input) {
    const list = document.getElementById('sa-au-lampiran-' + idx);
    if (!list) return;
    list.innerHTML = Array.from(input.files).map(f =>
        '<div class="flex items-center gap-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded px-2 py-1"><i class="fa fa-paperclip text-[10px] text-gray-400"></i><span class="truncate">' + f.name + '</span></div>'
    ).join('');
}

function closeSaAjukanUlangModal() {
    document.getElementById('modalSaAjukanUlang').classList.add('hidden');
    document.getElementById('modalSaAjukanUlang').classList.remove('flex');
    _saAuId = null; _saAuKejIdx = 0;
}

async function submitSaAjukanUlang() {
    if (!_saAuId) return;
    const btn   = document.getElementById('saAuSubmitBtn');
    const form  = document.getElementById('saAuForm');
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
    const formData = new FormData(form);
    formData.append('_token', token);
    try {
        const res    = await fetch('/admin/service-asuransi/' + _saAuId + '/ajukan-ulang-submit', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.success) { closeSaAjukanUlangModal(); window.location.reload(); }
        else { alert(result.message || 'Terjadi kesalahan.'); btn.disabled = false; btn.innerHTML = '<i class="fa fa-rotate-right"></i> Ajukan Ulang'; }
    } catch (e) { alert('Terjadi kesalahan jaringan.'); btn.disabled = false; btn.innerHTML = '<i class="fa fa-rotate-right"></i> Ajukan Ulang'; }
}

document.getElementById('modalSaAjukanUlang')?.addEventListener('click', function(e) {
    if (e.target === this) closeSaAjukanUlangModal();
});
</script>

@endsection
