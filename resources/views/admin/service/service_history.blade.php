@extends('admin.layouts.app')

@section('title', 'Service Kendaraan')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    @php
        $totalService = $data->count();
        $totalBiaya = $data->sum('total_biaya');
        $totalProses = $data->where('status', 'proses')->count();
        $totalSelesai = $data->where('status', 'selesai')->count();
    @endphp

    @php
        $aman = 0;
        $hampir = 0;
        $habis = 0;

        foreach ($kendaraan as $k) {
            $limit = $k->limit_biaya_bulanan_service ?? 0;
            if ($limit <= 0) {
                continue;
            }

            $total = \App\Models\ServiceHistory::where('kendaraan_id', $k->id)
                ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan])
                ->whereYear('tanggal_service', now()->year)
                ->sum('total_biaya');

            $persen = ($total / $limit) * 100;

            if ($persen >= 100) {
                $habis++;
            } elseif ($persen >= 70) {
                $hampir++;
            } else {
                $aman++;
            }
        }
    @endphp

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Service Kendaraan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Riwayat Service & Data Mobil Bermasalah</p>
            </div>
            <button onclick="openModalTambah()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Data
            </button>
        </div>

        {{-- NAV TABS --}}
        <div>
            <nav class="inline-flex gap-1 bg-gray-100 rounded-xl p-1">
                @php
                    $navItems = [
                        ['label' => 'Service History',  'url' => '/admin/service-history', 'icon' => 'bi bi-clock-history'],
                        ['label' => 'Mobil Bermasalah', 'url' => '/admin/service-detail',  'icon' => 'bi bi-exclamation-triangle-fill'],
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
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-4">

            {{-- Total Service --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Service</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $totalService }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-screwdriver-wrench text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total Biaya --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Biaya</p>
                        <h3 class="text-lg font-bold text-green-600 mt-2 leading-tight">Rp
                            {{ number_format($totalBiaya, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Status Proses --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Status Proses</p>
                        <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $totalProses }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="fa-solid fa-gear text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Status Selesai --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Status Selesai</p>
                        <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $totalSelesai }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                    </div>
                </div>
            </div>



            {{-- Hampir Limit --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Hampir Limit</p>
                        <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $hampir }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Limit Habis --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Limit Habis</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $habis }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-ban text-2xl"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- TOOLBAR --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800">Riwayat Service</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $totalService }} data</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Filter bulan + search digabung dalam satu form --}}
                    <form method="GET" class="flex items-center gap-2">
                        <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}"
                            class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <div class="relative">
                            <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari kendaraan..."
                                class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                        </div>
                        <button type="submit"
                            class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            Cari
                        </button>
                        @if(request('bulan') || request('search'))
                        <a href="{{ url('/admin/service-history') }}"
                            class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            Reset
                        </a>
                        @endif
                    </form>
                    <a href="{{ route('service-history.pdf', ['bulan' => request('bulan'), 'search' => request('search')]) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>
                    <button id="btnExpandAll" onclick="expandAllAccordion()"
                        class="px-3 py-1.5 text-xs font-medium text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                        <i class="fa fa-chevron-down text-xs"></i> Buka Semua
                    </button>
                    <button id="btnCollapseAll" onclick="collapseAllAccordion()"
                        class="px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-chevron-right text-xs"></i> Tutup Semua
                    </button>
                </div>
            </div>



            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Keluhan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">KM</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Biaya</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4 col-budget">Maks Bulanan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4 col-budget">Sisa Budget</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Pengeluaran</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Tanggal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Bukti</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Lampiran</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-400 px-5 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @php
                            // Group $data by kendaraan_id for accordion display
                            $grouped = $data->groupBy('kendaraan_id');
                        @endphp

                        @forelse ($grouped as $kendaraanId => $rows)
                            @php
                                $firstRow        = $rows->first();
                                $groupMerk       = $firstRow->kendaraan->merk  ?? '-';
                                $groupNopol      = $firstRow->kendaraan->nopol ?? '-';
                                $groupCount      = $rows->count();
                                $groupTotalBiaya = $rows->sum('total_biaya');
                                $groupId         = 'group-' . $kendaraanId;

                                $limitBulan  = $firstRow->kendaraan->limit_biaya_bulanan_service ?? 0;
                                $limitTahun  = $firstRow->kendaraan->limit_biaya_tahunan_service ?? 0;
                                $sisaBulan   = $rows->sortByDesc('tanggal_service')->first()->sisa_limit ?? 0;
                                $pctSisa     = $limitBulan > 0 ? ($sisaBulan / $limitBulan) * 100 : 100;
                                $sisaColor   = $pctSisa <= 0  ? 'text-red-600 bg-red-50 border-red-200'
                                             : ($pctSisa <= 30 ? 'text-amber-600 bg-amber-50 border-amber-200'
                                             :                   'text-emerald-600 bg-emerald-50 border-emerald-200');
                            @endphp

                            {{-- -- GROUP HEADER ROW -- --}}
                            <tr class="group-header bg-blue-50/80 border-t-2 border-b border-blue-100 border-l-4 border-l-blue-500 cursor-pointer select-none hover:bg-blue-100 transition-colors duration-150"
                                onclick="toggleAccordion('{{ $groupId }}')"
                                data-group="{{ $groupId }}">
                                <td colspan="11" class="px-5 py-3.5">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        {{-- Car icon --}}
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-car text-sm"></i>
                                        </div>

                                        {{-- Merk & Nopol --}}
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span class="font-bold text-gray-800 text-sm">{{ $groupMerk }}</span>
                                            <span class="font-mono text-xs text-gray-600 bg-white border border-gray-200 px-2 py-0.5 rounded">{{ $groupNopol }}</span>
                                        </div>

                                        @if ($limitBulan > 0)
                                            <span class="text-gray-300 text-xs flex-shrink-0">|</span>

                                            {{-- Max Bulan / Tahun --}}
                                            <span class="text-xs text-gray-500 flex-shrink-0 whitespace-nowrap">
                                                Max Bulan/Tahun:
                                                <span class="font-semibold text-gray-700">
                                                    Rp {{ number_format($limitBulan, 0, ',', '.') }} / {{ number_format($limitTahun, 0, ',', '.') }}
                                                </span>
                                            </span>

                                            <span class="text-gray-300 text-xs flex-shrink-0">â€¢</span>

                                            {{-- Sisa Bulanan --}}
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-md border flex-shrink-0 whitespace-nowrap {{ $sisaColor }}">
                                                @if ($pctSisa <= 0)
                                                    <i class="fa-solid fa-ban text-[10px]"></i>
                                                @elseif ($pctSisa <= 30)
                                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                @else
                                                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                                                @endif
                                                Sisa Service Bulanan: Rp {{ number_format($sisaBulan, 0, ',', '.') }}
                                            </span>
                                        @endif

                                        <div class="flex-1"></div>

                                        {{-- Count badge --}}
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-600 text-white flex-shrink-0">
                                            {{ $groupCount }} Service
                                        </span>

                                        {{-- Total biaya sum --}}
                                        <span class="text-xs font-semibold text-green-700 bg-green-50 border border-green-100 px-3 py-1 rounded-full flex-shrink-0">
                                            Rp {{ number_format($groupTotalBiaya, 0, ',', '.') }}
                                        </span>

                                        {{-- Chevron icon --}}
                                        <i id="chevron-{{ $groupId }}"
                                           class="fa fa-chevron-right text-xs text-blue-400 transition-transform duration-300 flex-shrink-0"></i>
                                    </div>
                                </td>
                            </tr>

                            {{-- -- DETAIL / CHILD ROWS -- --}}
                            @foreach ($rows as $loopIndex => $d)
                                @php
                                    $limit = $d->kendaraan->limit_biaya_bulanan_service ?? 0;
                                    $sisa  = $d->sisa_limit ?? 0;

                                    $rowClass = '';
                                    if ($limit > 0) {
                                        if ($sisa <= 0) {
                                            $rowClass = 'bg-red-50 hover:bg-red-100';
                                        } elseif ($sisa <= $limit * 0.1) {
                                            $rowClass = 'bg-yellow-50 hover:bg-yellow-100';
                                        }
                                    }
                                @endphp

                                <tr class="group-child border-t border-gray-100 transition-all duration-200 {{ $rowClass ?: 'hover:bg-blue-50/30' }}"
                                    data-group="{{ $groupId }}"
                                    data-search="{{ strtolower(($d->kendaraan->merk ?? '') . ' ' . ($d->kendaraan->nopol ?? '') . ' ' . $d->keluhan . ' ' . $d->status) }}"
                                    style="display:none;">

                                    {{-- Keluhan --}}
                                    <td class="px-5 py-4 text-sm text-gray-700 max-w-xs">
                                        <span class="line-clamp-10">{{ $d->keluhan }}</span>
                                    </td>

                                    {{-- KM --}}
                                    <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                                        {{ number_format($d->kilometer, 0, ',', '.') }} km
                                    </td>

                                    {{-- Total Biaya --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($d->total_biaya, 0, ',', '.') }}</span>
                                    </td>

                                    {{-- Maks Bulanan (hidden by default) --}}
                                    <td class="col-budget px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        @if ($d->kendaraan->limit_biaya_bulanan_service ?? false)
                                            Rp {{ number_format($d->kendaraan->limit_biaya_bulanan_service, 0, ',', '.') }}
                                        @else
                                            <span class="text-gray-300">â€”</span>
                                        @endif
                                    </td>

                                    {{-- Sisa Budget (hidden by default) --}}
                                    @php
                                        $limit = $d->kendaraan->limit_biaya_bulanan_service ?? 0;
                                        $sisa  = $d->sisa_limit ?? 0;
                                    @endphp
                                    <td class="col-budget px-5 py-4 whitespace-nowrap">
                                        @if ($limit <= 0)
                                            <span class="text-gray-300 text-sm">â€”</span>
                                        @elseif ($sisa <= 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Habis
                                            </span>
                                        @elseif ($sisa <= $limit * 0.2)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Rp {{ number_format($sisa, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Rp {{ number_format($sisa, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status Service --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <button type="button"
                                            onclick="ubahStatusService({{ $d->id }}, '{{ $d->status }}')"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition-colors hover:opacity-80
                                                {{ $d->status == 'proses' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $d->status == 'proses' ? 'bg-amber-400' : 'bg-emerald-500' }}"></span>
                                            {{ $d->status == 'proses' ? 'Proses' : 'Selesai' }}
                                        </button>
                                    </td>

                                    {{-- Pengeluaran --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if ($d->status_pengeluaran === 'overservice')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                Over Service
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                                Stabil
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($d->tanggal_service)->format('d M Y') }}
                                    </td>

                                    {{-- Bukti --}}
                                    <td class="px-5 py-4">
                                        @if ($d->bukti_pembayaran)
                                            @php $filename = basename($d->bukti_pembayaran); @endphp
                                            <a href="{{ asset($d->bukti_pembayaran) }}" target="_blank"
                                                class="text-blue-600 underline text-xs hover:text-blue-800 block">
                                                {{ $filename }}
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>

                                    {{-- Lampiran --}}
                                    <td class="px-5 py-4">
                                        @if($d->attachments->isNotEmpty())
                                            <div class="flex flex-col gap-1">
                                                @foreach ($d->attachments as $att)
                                                    <div class="flex items-center gap-1">
                                                        <a href="{{ asset($att->file_path) }}" target="_blank"
                                                            class="text-blue-500 underline text-[11px] hover:text-blue-700">
                                                            {{ $att->file_name }}
                                                        </a>
                                                        <form action="{{ route('service-history.attachment.destroy', $att->id) }}"
                                                            method="POST" onsubmit="return confirm('Hapus lampiran ini?')"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-400 hover:text-red-600 text-[10px]">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <form action="{{ route('service-history.destroy', $d->id) }}" method="POST"
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
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="11" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fa fa-screwdriver-wrench text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data Service History</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Data" untuk menambahkan data baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
            </div>

            <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfoBottom"></div>

        </div>

    </div>

    {{-- ======================================
    MODAL TAMBAH
====================================== --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Data Service</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data riwayat service kendaraan</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formTambah" action="{{ route('service-history.store') }}" method="POST"
                enctype="multipart/form-data" class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                {{-- Kendaraan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select name="kendaraan_id" id="kendaraan_id_tambah" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Kendaraan --</option>
                        @forelse ($kendaraan as $k)
                            @php
                                $bulanAktif = $bulan ?? now()->format('Y-m');
                                $totalSvc   = \App\Models\ServiceHistory::where('kendaraan_id', $k->id)
                                    ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulanAktif])
                                    ->sum('total_biaya');
                                $limitK   = $k->limit_biaya_bulanan_service ?? 0;
                                $sisaK    = $limitK > 0 ? max($limitK - $totalSvc, 0) : 0;
                                $persen10 = $limitK * 0.1;

                                $detail        = $detailPerKendaraan[$k->id] ?? null;
                                $keluhanGabung = $detail['keluhan_gabungan'] ?? '';
                                $totalBiayaK   = $detail['total_biaya'] ?? 0;
                                $kilometerK    = $detail['kilometer'] ?? 0;
                                $rincianK      = $detail['rincian'] ?? [];
                                $rincianJson   = json_encode($rincianK, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_APOS);
                            @endphp
                            <option value="{{ $k->id }}"
                                data-sisa="{{ $sisaK }}"
                                data-limit="{{ $limitK }}"
                                data-status="{{ $k->status_kendaraan }}"
                                data-keluhan="{{ $keluhanGabung }}"
                                data-biaya="{{ $totalBiayaK }}"
                                data-kilometer="{{ $kilometerK }}"
                                data-rincian="{{ $rincianJson }}">
                                {{ $k->merk }} - {{ $k->nopol }}
                                @if ($limitK > 0)
                                    @if ($sisaK < 0)
                                        (? Over Rp {{ number_format(abs($sisaK), 0, ',', '.') }})
                                    @elseif ($sisaK <= 0)
                                        (Limit Habis)
                                    @elseif ($sisaK <= $persen10)
                                        (? Sisa Rp {{ number_format($sisaK, 0, ',', '.') }})
                                    @else
                                        (Sisa Rp {{ number_format($sisaK, 0, ',', '.') }})
                                    @endif
                                @else
                                    (Tidak ada limit)
                                @endif
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada kendaraan tersedia</option>
                        @endforelse
                    </select>

                    {{-- Panel info masalah dari Mobil Bermasalah --}}
                    <div id="infoMasalahPanel" class="hidden mt-2 p-3 bg-red-50 border border-red-100 rounded-lg space-y-2">
                        <div class="flex items-center gap-1.5">
                            <i class="fa fa-triangle-exclamation text-red-400 text-xs shrink-0"></i>
                            <span class="text-xs font-semibold text-red-700">Masalah dari tab Mobil Bermasalah:</span>
                        </div>
                        {{-- Rincian per keluhan --}}
                        <ul id="rincianMasalahList" class="space-y-1 pl-1"></ul>
                        {{-- Total akumulasi --}}
                        <div class="flex items-center justify-between pt-1 border-t border-red-200 mt-1">
                            <span class="text-xs font-semibold text-red-700">Total Biaya Akumulasi:</span>
                            <span id="totalBiayaAkumulasi" class="text-xs font-bold text-red-700"></span>
                        </div>
                    </div>
                </div>

                {{-- Info Maks Bulanan (read-only, dari kendaraan) --}}
                <div class="md:col-span-2" id="infoMaksBulananWrapper" style="display:none">
                    <div class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                        <i class="fa fa-info-circle text-blue-400 text-sm"></i>
                        <span class="text-xs text-blue-700">
                            Batas biaya bulanan kendaraan ini:
                            <strong id="infoMaksBulananNominal">-</strong>
                        </span>
                    </div>
                </div>

                {{-- Warning limit --}}
                <div id="warningLimitTambah" class="md:col-span-2 hidden p-3 rounded-lg text-xs font-medium"></div>

                {{-- Keluhan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Keluhan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keluhan" required rows="3" placeholder="Deskripsikan keluhan kendaraan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>

                {{-- Kilometer --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kilometer <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input readonly type="number" name="kilometer" id="tambah_kilometer" required placeholder="0"
                            class="disabled cursor-not-allowed w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 pr-24"
                            oninput="onKmTambahInput()">
                     
                    </div>
                </div>

                {{-- Total Biaya --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Total Biaya <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="total_biaya" id="total_biaya_tambah" required placeholder="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="proses">Proses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                {{-- Tanggal Service --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Service <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_service" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>


                {{-- Bukti Pembayaran --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Bukti Pembayaran
                    </label>

                    {{-- PREVIEW AREA --}}
                    <div id="previewWrap" class="hidden mb-3 relative">
                        <img id="previewImg" src=""
                            class="hidden h-32 w-full rounded-xl border border-gray-200 object-cover cursor-pointer"
                            onclick="window.open(this.src,'_blank')">

                        <a id="previewFileLink" href="#" target="_blank"
                            class="hidden items-center gap-3 p-4 border rounded-xl bg-gray-50 hover:bg-gray-100">
                            <i id="previewFileIcon" class="fa-solid fa-file text-2xl text-gray-500"></i>
                            <div>
                                <div id="previewFileName" class="font-medium text-sm text-gray-700">File Bukti Pembayaran
                                </div>
                            </div>
                        </a>

                        <button type="button" onclick="hapusPreviewTambah()"
                            class="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs flex items-center justify-center">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        </button>
                    </div>

                    {{-- UPLOAD AREA --}}
                    <label for="bukti_pembayaran"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-1"></i>

                        <span class="text-xs text-gray-500">Klik untuk upload file</span>
                        <span class="text-[10px] text-gray-400">
                            (max 5MB)
                        </span>
                    </label>

                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="hidden"
                        onchange="previewFileGPS(this)">
                </div>

                {{-- Lampiran Tambahan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Lampiran Tambahan (opsional, bisa lebih dari 1)
                    </label>

                    <label for="bukti_attachment"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <i class="fa-solid fa-paperclip text-xl text-gray-400 mb-1"></i>
                        <span class="text-xs text-gray-500">Klik untuk upload lampiran tambahan</span>
                        <span class="text-xs text-gray-400">(Maks 5MB per file)</span>
                    </label>

                    <input type="file" name="bukti_attachment[]" id="bukti_attachment" class="hidden" multiple
                        onchange="renderListAttachment(this, 'listAttachmentTambah')">

                    <ul id="listAttachmentTambah" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                </div>

                {{-- Tombol --}}
                <div class="md:col-span-2 flex gap-3 pt-1">
                    <button type="button" onclick="closeModalTambah()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                        <i class="fa fa-save text-sm"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- ======================================
    MODAL EDIT
====================================== --}}
    <div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Data Service</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data riwayat service kendaraan</p>
                </div>
                <button onclick="closeModalEdit()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')

                {{-- Kendaraan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select name="kendaraan_id" id="edit_kendaraan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        @foreach ($kendaraan as $k)
                            @php
                                $bulanAktif = $bulan ?? now()->format('Y-m');

                            $totalSvcEdit = \App\Models\ServiceHistory::where('kendaraan_id', $k->id)
                                ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulanAktif])
                                ->sum('total_biaya');

                            $limitEdit   = $k->limit_biaya_bulanan_service ?? 0;
                            $sisaEdit    = $limitEdit > 0 ? max($limitEdit - $totalSvcEdit, 0) : 0;
                            $detailEdit  = $detailPerKendaraan[$k->id] ?? null;
                            $keluhanEdit = $detailEdit['keluhan_gabungan'] ?? '';
                            $biayaEdit   = $detailEdit['total_biaya'] ?? 0;
                            $kmEdit      = $detailEdit['kilometer'] ?? 0;
                            $rincianEdit = $detailEdit['rincian'] ?? [];
                            $rincianEditJson = json_encode($rincianEdit, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_APOS);
                        @endphp
                        <option value="{{ $k->id }}"
                            data-limit="{{ $limitEdit }}"
                            data-sisa="{{ $sisaEdit }}"
                            data-keluhan="{{ $keluhanEdit }}"
                            data-biaya="{{ $biayaEdit }}"
                            data-kilometer="{{ $kmEdit }}"
                            data-rincian="{{ $rincianEditJson }}">
                            {{ $k->merk }} - {{ $k->nopol }}
                            @if ($limitEdit > 0)
                                @if ($sisaEdit <= 0)
                                    (Limit Habis)
                                @else
                                    (Sisa Rp {{ number_format($sisaEdit, 0, ',', '.') }})
                                @endif
                            @else
                                (Tidak ada limit)
                            @endif
                        </option>
                    @endforeach
                    </select>

                    {{-- Panel rincian masalah (edit) --}}
                    <div id="editInfoMasalahPanel" class="hidden mt-2 p-3 bg-red-50 border border-red-100 rounded-lg space-y-2">
                        <div class="flex items-center gap-1.5">
                            <i class="fa fa-triangle-exclamation text-red-400 text-xs shrink-0"></i>
                            <span class="text-xs font-semibold text-red-700">Masalah dari tab Mobil Bermasalah:</span>
                        </div>
                        <ul id="editRincianMasalahList" class="space-y-1 pl-1"></ul>
                        <div class="flex items-center justify-between pt-1 border-t border-red-200 mt-1">
                            <span class="text-xs font-semibold text-red-700">Total Biaya Akumulasi:</span>
                            <span id="editTotalBiayaAkumulasi" class="text-xs font-bold text-red-700"></span>
                        </div>
                    </div>
                </div>

                {{-- Info maks bulanan (edit) --}}
                <div class="md:col-span-2" id="editInfoWrapper">
                    <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg space-y-1">

                        {{-- Baris 1: Maks bulanan & sisa --}}
                        <div class="flex items-center gap-2">
                            <i class="fa fa-info-circle text-blue-400 text-sm"></i>
                            <span class="text-xs text-blue-700">
                                Batas bulanan: <strong id="editInfoMaksBulanan">-</strong>
                                &nbsp;|&nbsp;
                                Sisa bulan ini: <strong id="editInfoSisaLimit">-</strong>
                            </span>
                        </div>

                        {{-- Baris 2: Biaya tahunan & status pengeluaran --}}
                        <div class="flex items-center gap-2">
                            <i class="fa fa-chart-line text-blue-400 text-sm"></i>
                            <span class="text-xs text-blue-700">
                                Biaya tahunan: <strong id="editInfoBiayaTahunan">-</strong>
                                &nbsp;|&nbsp;
                                Status: <strong id="editInfoStatusPengeluaran">-</strong>
                            </span>
                        </div>

                    </div>
                </div>

                {{-- Keluhan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Keluhan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keluhan" id="edit_keluhan" required rows="3"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>

                {{-- Kilometer --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kilometer <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="kilometer" id="edit_kilometer" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 pr-24"
                            oninput="onKmEditInput()">
                        <span id="km-edit-badge"
                            class="absolute right-2 top-1/2 -translate-y-1/2 hidden items-center gap-1 text-[10px] font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md px-1.5 py-0.5 pointer-events-none">
                            <i class="fa-solid fa-database text-[9px]"></i> Dari DB
                        </span>
                    </div>
                </div>

                {{-- Total Biaya --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Total Biaya <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="total_biaya" id="edit_total_biaya" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>


                {{-- Status --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="edit_status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="proses">Proses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                {{-- Tanggal Service --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Service <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_service" id="edit_tanggal_service" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Bukti Pembayaran --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bukti Pembayaran</label>
                    <input type="file" name="bukti_pembayaran"
                        onchange="previewEdit(event)" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <!--<img id="previewEdit" class="mt-2 w-32 h-32 object-cover rounded-lg border hidden">-->
                    <!--<div id="previewLamaWrapper" class="mt-2 hidden">-->
                    <!--    <p class="text-xs text-gray-500 mb-1">Foto Saat Ini</p>-->
                    <!--    <img id="previewLama" class="w-32 h-32 object-cover rounded-lg border">-->
                    <!--</div>-->
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Lampiran Tambahan (opsional, bisa lebih dari 1)
                    </label>

                    <input id="edit_bukti_attachment" type="file" name="bukti_attachment[]" multiple
                        class="w-full border rounded-lg px-3 py-2"
                        onchange="renderListAttachment(this, 'listAttachmentEdit')">

                    <ul id="listAttachmentEdit" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                </div>



                {{-- Tombol --}}
                <div class="md:col-span-2 flex gap-3 pt-1">
                    <button type="button" onclick="closeModalEdit()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                        <i class="fa fa-save text-sm"></i> Update
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- ======================================
    MODAL UBAH STATUS SERVICE
====================================== --}}
    <div id="modalStatusService"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4"
        style="backdrop-filter:blur(3px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
            style="animation:slideUp .2s ease">

            {{-- Header strip warna dinamis --}}
            <div id="msvc-header" class="px-6 pt-6 pb-4 flex items-center gap-4">
                <div id="msvc-icon"
                    class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl">
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-800">Ubah Status Service</h2>
                    <p id="msvc-desc" class="text-xs text-gray-400 mt-0.5"></p>
                </div>
            </div>

            {{-- Pilihan status --}}
            <div class="px-6 pb-2 space-y-2">
                {{-- Proses --}}
                <label id="opt-proses"
                    class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                           border-transparent hover:border-amber-200 hover:bg-amber-50 has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50">
                    <input type="radio" name="msvc_status" value="proses" class="hidden">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Proses</p>
                        <p class="text-xs text-gray-400">Service sedang berjalan</p>
                    </div>
                    <div class="ml-auto w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center opt-radio-dot transition-colors">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 hidden opt-dot"></span>
                    </div>
                </label>

                {{-- Selesai --}}
                <label id="opt-selesai"
                    class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                           border-transparent hover:border-emerald-200 hover:bg-emerald-50 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50">
                    <input type="radio" name="msvc_status" value="selesai" class="hidden">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Selesai</p>
                        <p class="text-xs text-gray-400">Service telah diselesaikan</p>
                    </div>
                    <div class="ml-auto w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center opt-radio-dot transition-colors">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 hidden opt-dot"></span>
                    </div>
                </label>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 flex gap-3">
                <button type="button" onclick="closeMsvcModal()"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="msvc-confirm-btn" onclick="submitMsvcStatus()"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl flex items-center justify-center gap-2 transition-colors">
                    <i class="fa-solid fa-check text-xs"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================
    POPUP ALERT
====================================== --}}
    @if (session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

            <div id="alertBox"
                class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">

                @if (session('success'))
                    <div
                        class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @elseif (session('error'))
                    <div
                        class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                    </div>
                @else
                    <div
                        class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
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
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0"
                    aria-label="Tutup">
                    <i class="fa fa-times"></i>
                </button>

            </div>
        </div>
    @endif


    {{-- STYLE & SCRIPT --}}
    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Accordion group child row transition */
        tr.group-child {
            transition: opacity 0.2s ease;
        }

        tr.group-header {
            transition: background-color 0.15s ease;
        }

        #chevron-[id] {
            transition: transform 0.3s ease;
        }
    </style>

    <script>
        // -- TOGGLE KOLOM BUDGET ----------------------------
        var budgetColsVisible = false;
        function toggleBudgetCols() {
            budgetColsVisible = !budgetColsVisible;
            var cols = document.querySelectorAll('.col-budget');
            cols.forEach(function(el) {
                el.style.display = budgetColsVisible ? '' : 'none';
            });
            var icon = document.getElementById('iconBudget');
            var btn  = document.getElementById('btnToggleBudget');
            if (budgetColsVisible) {
                icon.className = 'fa fa-eye text-xs';
                btn.classList.add('text-blue-600', 'border-blue-300', 'bg-blue-50');
                btn.classList.remove('text-gray-600', 'border-gray-200');
            } else {
                icon.className = 'fa fa-eye-slash text-xs';
                btn.classList.remove('text-blue-600', 'border-blue-300', 'bg-blue-50');
                btn.classList.add('text-gray-600', 'border-gray-200');
            }
        }
        // Hide budget cols on load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.col-budget').forEach(function(el) {
                el.style.display = 'none';
            });
        });

        // -- SHOW ENTRIES -----------------------------------
        const allServiceRows = Array.from(document.querySelectorAll('#tableBody tr[data-search]'));

        function renderTable() {
            const perPageEl = document.getElementById('perPageSelect');
            const perPage   = perPageEl.value === 'all' ? Infinity : parseInt(perPageEl.value, 10);
            let shown = 0;

            // Only count/show child rows that belong to an expanded group
            allServiceRows.forEach(row => {
                const groupId  = row.getAttribute('data-group');
                const isOpen   = groupStates[groupId] === true;
                row.style.display = (isOpen && shown < perPage) ? '' : 'none';
                if (isOpen && shown < perPage) shown++;
            });

            const total = allServiceRows.length;
            const infoText = total === 0
                ? 'Tidak ada data'
                : `Menampilkan ${shown} dari ${total} entri`;

            const top = document.getElementById('entriesInfo');
            const bot = document.getElementById('entriesInfoBottom');
            if (top) top.innerText = infoText;
            if (bot) bot.innerText = infoText;
        }

        // -- ACCORDION GROUP TOGGLE -------------------------
        var groupStates = {};

        function toggleAccordion(groupId) {
            var isOpen  = groupStates[groupId] === true;
            var nowOpen = !isOpen;
            groupStates[groupId] = nowOpen;

            document.querySelectorAll('tr.group-child[data-group="' + groupId + '"]').forEach(function(row) {
                row.style.display = nowOpen ? '' : 'none';
            });

            var chevron = document.getElementById('chevron-' + groupId);
            if (chevron) {
                chevron.style.transform = nowOpen ? 'rotate(90deg)' : 'rotate(0deg)';
            }
        }

        function expandAllAccordion() {
            document.querySelectorAll('tr.group-header').forEach(function(hdr) {
                var gid = hdr.getAttribute('data-group');
                groupStates[gid] = true;
                document.querySelectorAll('tr.group-child[data-group="' + gid + '"]').forEach(function(r) { r.style.display = ''; });
                var ch = document.getElementById('chevron-' + gid);
                if (ch) ch.style.transform = 'rotate(90deg)';
            });
        }

        function collapseAllAccordion() {
            document.querySelectorAll('tr.group-header').forEach(function(hdr) {
                var gid = hdr.getAttribute('data-group');
                groupStates[gid] = false;
                document.querySelectorAll('tr.group-child[data-group="' + gid + '"]').forEach(function(r) { r.style.display = 'none'; });
                var ch = document.getElementById('chevron-' + gid);
                if (ch) ch.style.transform = 'rotate(0deg)';
            });
        }

        // -- MODAL TAMBAH -----------------------------------
        function openModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function renderListAttachment(input, listId) {
            const list = document.getElementById(listId);
            list.innerHTML = '';

            Array.from(input.files).forEach(file => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-1.5';
                li.innerHTML = `<i class="fa-solid fa-paperclip text-gray-400"></i> ${file.name}`;
                list.appendChild(li);
            });
        }

        function closeModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.getElementById('listAttachmentTambah').innerHTML = '';
            document.getElementById('bukti_attachment').value = '';
        }
        document.getElementById('modalTambah').addEventListener('click', function(e) {
            if (e.target === this) closeModalTambah();
        });

        // -- MODAL EDIT -------------------------------------
        // -- MODAL EDIT -------------------------------------
        function openEditModal(id, kendaraan_id, keluhan, kilometer, total_biaya,
            status, tanggal_service, maks_bulanan, biaya_tahunan, status_pengeluaran, bukti_pembayaran) {

            document.getElementById('edit_bukti_attachment').value = '';
            document.getElementById('listAttachmentEdit').innerHTML = '';

            var m = document.getElementById('modalEdit');
            m.classList.remove('hidden');
            m.classList.add('flex');

            document.getElementById('formEdit').action = '/admin/service-history/' + id;
            document.getElementById('edit_kendaraan_id').value = kendaraan_id;
            document.getElementById('edit_keluhan').value = keluhan;
            document.getElementById('edit_kilometer').value = kilometer;
            document.getElementById('edit_total_biaya').value = total_biaya;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_tanggal_service').value = tanggal_service;

            // Cek badge KM dari DB
            checkKmEditBadge();

            // Set info dari data record
            updateEditInfo(maks_bulanan, biaya_tahunan, status_pengeluaran);

            // Hitung sisa & tampilkan panel rincian untuk kendaraan terpilih
            updateEditSisa(kendaraan_id);
            showEditRincianPanel(kendaraan_id);
        }

        function showEditRincianPanel(kendaraan_id) {
            var sel   = document.getElementById('edit_kendaraan_id');
            var opt   = Array.from(sel.options).find(o => o.value == kendaraan_id);
            var panel = document.getElementById('editInfoMasalahPanel');
            var list  = document.getElementById('editRincianMasalahList');
            var total = document.getElementById('editTotalBiayaAkumulasi');

            if (!panel || !opt) return;

            var rincian  = [];
            var biayaVal = opt.dataset.biaya || 0;
            try {
                var raw = opt.getAttribute('data-rincian') || '[]';
                rincian = JSON.parse(raw);
            } catch(e) {
                console.warn('Gagal parse rincian edit JSON:', e);
                rincian = [];
            }

            if (rincian.length > 0) {
                list.innerHTML = '';
                rincian.forEach(function(item) {
                    var li = document.createElement('li');
                    li.className = 'flex items-start justify-between gap-2 text-xs text-red-700';
                    li.innerHTML =
                        '<span class="flex items-start gap-1">' +
                            '<i class="fa fa-circle text-[5px] mt-1.5 text-red-400 shrink-0"></i>' +
                            '<span>' + item.keluhan + ' <span class="text-red-400 font-normal">(' + item.tanggal + ')</span></span>' +
                        '</span>' +
                        '<span class="font-semibold shrink-0">Rp ' + parseInt(item.biaya || 0).toLocaleString('id-ID') + '</span>';
                    list.appendChild(li);
                });
                if (total) total.textContent = 'Rp ' + parseInt(biayaVal || 0).toLocaleString('id-ID');
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        }

        function fmt(n) {
            return n > 0 ? 'Rp ' + parseInt(n || 0).toLocaleString('id-ID') : '-';
        }

        function updateEditInfo(maks_bulanan, biaya_tahunan, status_pengeluaran) {
            document.getElementById('editInfoMaksBulanan').textContent = fmt(maks_bulanan);
            document.getElementById('editInfoBiayaTahunan').textContent = fmt(biaya_tahunan);

            var spEl = document.getElementById('editInfoStatusPengeluaran');
            spEl.textContent = status_pengeluaran || '-';
            spEl.className = status_pengeluaran === 'overservice' ?
                'text-red-600' : 'text-green-600';
        }

        function updateEditSisa(kendaraan_id) {
            var sel = document.getElementById('edit_kendaraan_id');
            var opt = Array.from(sel.options).find(o => o.value == kendaraan_id);
            if (!opt) return;

            var sisa = parseInt(opt.dataset.sisa || 0);
            var limit = parseInt(opt.dataset.limit || 0);

            var sisaEl = document.getElementById('editInfoSisaLimit');
            var maksBulEl = document.getElementById('editInfoMaksBulanan');

            maksBulEl.textContent = fmt(limit);

            if (limit <= 0) {
                sisaEl.textContent = 'Tidak ada limit';
                sisaEl.className = 'text-gray-500';
            } else if (sisa <= 0) {
                sisaEl.textContent = 'Limit Habis';
                sisaEl.className = 'text-red-600 font-semibold';
            } else if (sisa <= limit * 0.1) {
                sisaEl.textContent = fmt(sisa);
                sisaEl.className = 'text-yellow-600 font-semibold';
            } else {
                sisaEl.textContent = fmt(sisa);
                sisaEl.className = 'text-green-600 font-semibold';
            }
        }

        // Update sisa saat ganti kendaraan di modal edit
        (function() {
            var sel = document.getElementById('edit_kendaraan_id');
            if (!sel) return;
            sel.addEventListener('change', function() {
                var opt   = sel.options[sel.selectedIndex];
                var limit = parseInt(opt.dataset.limit || 0);
                var sisa  = parseInt(opt.dataset.sisa  || 0);

                document.getElementById('editInfoMaksBulanan').textContent = fmt(limit);

                var sisaEl = document.getElementById('editInfoSisaLimit');
                if (limit <= 0) {
                    sisaEl.textContent = 'Tidak ada limit';
                    sisaEl.className = 'text-gray-500';
                } else if (sisa <= 0) {
                    sisaEl.textContent = 'Limit Habis';
                    sisaEl.className = 'text-red-600 font-semibold';
                } else if (sisa <= limit * 0.1) {
                    sisaEl.textContent = fmt(sisa);
                    sisaEl.className = 'text-yellow-600 font-semibold';
                } else {
                    sisaEl.textContent = fmt(sisa);
                    sisaEl.className = 'text-green-600 font-semibold';
                }

                // Tampilkan panel rincian
                showEditRincianPanel(opt.value);
            });
        })();

        function closeModalEdit() {
            var m = document.getElementById('modalEdit');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        document.getElementById('modalEdit').addEventListener('click', function(e) {
            if (e.target === this) closeModalEdit();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalTambah();
                closeModalEdit();
            }
        });

        // -- PREVIEW FOTO -----------------------------------
        function previewTambah(event) {
            var file = event.target.files[0];
            if (!file) return;
            var img = document.getElementById('previewTambah');
            img.src = URL.createObjectURL(file);
            img.classList.remove('hidden');
        }

        function previewEdit(event) {
            var file = event.target.files[0];
            if (!file) return;
            var img = document.getElementById('previewEdit');
            img.src = URL.createObjectURL(file);
            img.classList.remove('hidden');
        }

        // -- INFO MAKS BULANAN + WARNING (MODAL TAMBAH) -----
        (function() {
            var sel      = document.getElementById('kendaraan_id_tambah');
            var biaya    = document.getElementById('total_biaya_tambah');
            var warning  = document.getElementById('warningLimitTambah');
            var infoWrap = document.getElementById('infoMaksBulananWrapper');
            var infoNominal = document.getElementById('infoMaksBulananNominal');

            // Field keluhan & kilometer di form
            var fieldKeluhan   = document.querySelector('#formTambah textarea[name="keluhan"]');
            var fieldKilometer = document.querySelector('#formTambah input[name="kilometer"]');

            // Panel rincian
            var panel         = document.getElementById('infoMasalahPanel');
            var listEl        = document.getElementById('rincianMasalahList');
            var totalAkumEl   = document.getElementById('totalBiayaAkumulasi');

            function fmt(n) {
                return 'Rp ' + parseInt(n || 0).toLocaleString('id-ID');
            }

            function fillFromOption(opt) {
                if (!opt || !opt.value) {
                    if (panel) panel.classList.add('hidden');
                    return;
                }

                var keluhan   = opt.dataset.keluhan   || '';
                var biayaVal  = opt.dataset.biaya     || 0;
                var kmVal     = opt.dataset.kilometer || 0;
                var rincian   = [];

                // Baca raw attribute (browser sudah decode HTML entity otomatis via dataset)
                try {
                    var raw = opt.getAttribute('data-rincian') || '[]';
                    rincian = JSON.parse(raw);
                } catch(e) {
                    console.warn('Gagal parse rincian JSON:', e);
                    rincian = [];
                }

                // Auto-fill keluhan
                if (fieldKeluhan && keluhan) {
                    fieldKeluhan.value = keluhan;
                }

                // Auto-fill kilometer
                if (fieldKilometer && kmVal) {
                    fieldKilometer.value = kmVal;
                }

                // Auto-fill total biaya
                if (biaya && biayaVal) {
                    biaya.value = biayaVal;
                    // Trigger check warning setelah diisi
                    biaya.dispatchEvent(new Event('input'));
                }

                // Tampilkan panel rincian
                if (panel && listEl && rincian.length > 0) {
                    listEl.innerHTML = '';
                    rincian.forEach(function(item) {
                        var li = document.createElement('li');
                        li.className = 'flex items-start justify-between gap-2 text-xs text-red-700';
                        li.innerHTML =
                            '<span class="flex items-start gap-1">' +
                                '<i class="fa fa-circle text-[5px] mt-1.5 text-red-400 shrink-0"></i>' +
                                '<span>' + item.keluhan + ' <span class="text-red-400 font-normal">(' + item.tanggal + ')</span></span>' +
                            '</span>' +
                            '<span class="font-semibold shrink-0">' + fmt(item.biaya) + '</span>';
                        listEl.appendChild(li);
                    });

                    if (totalAkumEl) {
                        totalAkumEl.textContent = fmt(biayaVal);
                    }

                    panel.classList.remove('hidden');
                } else if (panel) {
                    panel.classList.add('hidden');
                }
            }

            function check() {
                if (!sel || !biaya || !warning) return;
                var opt   = sel.options[sel.selectedIndex];
                var sisa  = parseInt(opt ? (opt.dataset.sisa  || 0) : 0);
                var limit = parseInt(opt ? (opt.dataset.limit || 0) : 0);
                var nilai = parseInt(biaya.value || 0);
                var after = sisa - nilai;

                // Tampilkan info maks bulanan
                if (limit > 0) {
                    infoNominal.textContent = fmt(limit);
                    infoWrap.style.display = '';
                } else {
                    infoWrap.style.display = 'none';
                }

                // Warning limit
                warning.className = 'md:col-span-2 hidden p-3 rounded-lg text-xs font-medium';
                if (nilai > 0 && limit > 0) {
                    if (after <= 0) {
                        warning.classList.remove('hidden');
                        warning.classList.add('bg-red-100', 'text-red-700');
                        warning.textContent = 'Limit service kendaraan ini akan habis atau melebihi batas bulan ini.';
                    } else if (after <= limit * 0.1) {
                        warning.classList.remove('hidden');
                        warning.classList.add('bg-yellow-100', 'text-yellow-700');
                        warning.textContent = 'Jatah service kendaraan bulan ini hampir habis.';
                    }
                }
            }

            // Saat kendaraan berubah: auto-fill + warning
            if (sel) {
                sel.addEventListener('change', function() {
                    fillFromOption(sel.options[sel.selectedIndex]);
                    check();
                });
            }

            // Saat biaya diketik manual: hanya update warning
            if (biaya) biaya.addEventListener('input', check);
        })();

        // -- UPDATE INFO MAKS BULANAN SAAT GANTI KENDARAAN (EDIT) --
        (function() {
            var sel = document.getElementById('edit_kendaraan_id');
            if (!sel) return;
            sel.addEventListener('change', function() {
                var opt = sel.options[sel.selectedIndex];
                var limit = parseInt(opt ? (opt.dataset.limit || 0) : 0);
                var infoEl = document.getElementById('editInfoMaksBulanan');
                if (infoEl) {
                    infoEl.textContent = limit > 0 ?
                        'Rp ' + limit.toLocaleString('id-ID') :
                        '-';
                }
            });
        })();

        // -- POPUP ALERT ------------------------------------
        (function() {
            var overlay = document.getElementById('alertOverlay');
            var box = document.getElementById('alertBox');
            if (!overlay) return;

            setTimeout(function() {
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
                box.style.transform = 'translateY(0)';
            }, 80);

            var timer = setTimeout(closeAlert, 4500);
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeAlert();
            });

            function closeAlert() {
                clearTimeout(timer);
                overlay.style.opacity = '0';
                overlay.style.pointerEvents = 'none';
                box.style.transform = 'translateY(-16px)';
            }
            window.closeAlert = closeAlert;
        })();

        // â”€â”€ MODAL STATUS SERVICE â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        var _msvcId = null;

        function ubahStatusService(id, currentStatus) {
            _msvcId = id;

            // Set deskripsi header
            var desc = document.getElementById('msvc-desc');
            var icon = document.getElementById('msvc-icon');
            if (currentStatus === 'proses') {
                desc.textContent = 'Status saat ini: Proses';
                icon.className = 'w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl bg-amber-100 text-amber-600';
                icon.innerHTML = '<i class="fa-solid fa-clock-rotate-left"></i>';
            } else {
                desc.textContent = 'Status saat ini: Selesai';
                icon.className = 'w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl bg-emerald-100 text-emerald-600';
                icon.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
            }

            // Set radio aktif sesuai status saat ini
            document.querySelectorAll('input[name="msvc_status"]').forEach(function(r) {
                r.checked = r.value === currentStatus;
            });
            updateMsvcRadioUI();

            // Buka modal
            var m = document.getElementById('modalStatusService');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeMsvcModal() {
            var m = document.getElementById('modalStatusService');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        // Update visual radio button
        function updateMsvcRadioUI() {
            ['proses', 'selesai'].forEach(function(val) {
                var label = document.getElementById('opt-' + val);
                var radio = label.querySelector('input[type="radio"]');
                var dot   = label.querySelector('.opt-dot');
                var ring  = label.querySelector('.opt-radio-dot');
                if (radio.checked) {
                    dot.classList.remove('hidden');
                    ring.classList.add(val === 'proses' ? 'border-amber-400' : 'border-emerald-400');
                    ring.classList.remove('border-gray-300');
                } else {
                    dot.classList.add('hidden');
                    ring.classList.remove('border-amber-400', 'border-emerald-400');
                    ring.classList.add('border-gray-300');
                }
            });
        }

        // Listen perubahan radio
        document.querySelectorAll('input[name="msvc_status"]').forEach(function(r) {
            r.addEventListener('change', updateMsvcRadioUI);
        });

        // Tutup saat klik backdrop
        document.getElementById('modalStatusService').addEventListener('click', function(e) {
            if (e.target === this) closeMsvcModal();
        });

        function submitMsvcStatus() {
            var selected = document.querySelector('input[name="msvc_status"]:checked');
            if (!selected) return;

            var btn = document.getElementById('msvc-confirm-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Menyimpan...';

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/service-history/' + _msvcId + '/status';
            form.innerHTML =
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '<input type="hidden" name="_method" value="PUT">' +
                '<input type="hidden" name="status" value="' + selected.value + '">';
            document.body.appendChild(form);
            form.submit();
        }


        function previewFileGPS(input) {
            const wrap = document.getElementById('previewWrap');
            const img = document.getElementById('previewImg');
            const link = document.getElementById('previewFileLink');
            const icon = document.getElementById('previewFileIcon');
            const nameEl = document.getElementById('previewFileName');

            const file = input.files[0];
            if (!file) return;

            wrap.classList.remove('hidden');
            const url = URL.createObjectURL(file);

            if (file.type.startsWith('image/')) {
                img.src = url;
                img.classList.remove('hidden');
                link.classList.add('hidden');
            } else {
                const ext = file.name.split('.').pop().toLowerCase();
                icon.className = getFileIconClass(ext);
                nameEl.textContent = file.name;
                link.href = url;
                link.classList.remove('hidden');
                img.classList.add('hidden');
            }
        }

        function getFileIconClass(ext) {
            if (ext === 'pdf') return 'fa-solid fa-file-pdf text-2xl text-red-500';
            if (ext === 'doc' || ext === 'docx') return 'fa-solid fa-file-word text-2xl text-blue-500';
            if (ext === 'xls' || ext === 'xlsx') return 'fa-solid fa-file-excel text-2xl text-green-600';
            return 'fa-solid fa-file text-2xl text-gray-500';
        }

        function hapusPreviewTambah() {
            document.getElementById('bukti_pembayaran').value = '';
            document.getElementById('previewWrap').classList.add('hidden');
            document.getElementById('previewImg').classList.add('hidden');
            document.getElementById('previewFileLink').classList.add('hidden');
        }

        // â”€â”€ KILOMETER AUTO-FILL â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        const kmMap = {
            @foreach ($kendaraan as $k)
            {{ $k->id }}: {{ $k->kilometer_sekarang ?? 0 }},
            @endforeach
        };

        function setKmBadge(inputEl, badgeEl, hintEl, show) {
            if (show) {
                badgeEl.classList.remove('hidden');
                badgeEl.classList.add('inline-flex');
                if (hintEl) hintEl.classList.remove('hidden');
            } else {
                badgeEl.classList.add('hidden');
                badgeEl.classList.remove('inline-flex');
                if (hintEl) hintEl.classList.add('hidden');
            }
        }

        // Modal Tambah
        function autoFillKmTambah() {
            const id     = document.getElementById('kendaraan_id_tambah').value;
            const input  = document.getElementById('tambah_kilometer');
            const badge  = document.getElementById('km-tambah-badge');
            const hint   = document.getElementById('km-tambah-hint');

            if (id && kmMap[id] !== undefined) {
                input.value = kmMap[id];
                setKmBadge(input, badge, hint, true);
            } else {
                setKmBadge(input, badge, hint, false);
            }
        }
        function onKmTambahInput() {
            setKmBadge(
                document.getElementById('tambah_kilometer'),
                document.getElementById('km-tambah-badge'),
                document.getElementById('km-tambah-hint'),
                false
            );
        }
        document.getElementById('kendaraan_id_tambah').addEventListener('change', autoFillKmTambah);

        // Modal Edit â€” tampilkan badge jika nilai sama dengan DB
        function checkKmEditBadge() {
            const id    = document.getElementById('edit_kendaraan_id').value;
            const input = document.getElementById('edit_kilometer');
            const badge = document.getElementById('km-edit-badge');

            if (id && kmMap[id] !== undefined && parseInt(input.value) === kmMap[id]) {
                setKmBadge(input, badge, null, true);
            } else {
                setKmBadge(input, badge, null, false);
            }
        }
        function onKmEditInput() {
            setKmBadge(
                document.getElementById('edit_kilometer'),
                document.getElementById('km-edit-badge'),
                null,
                false
            );
        }
        // â”€â”€ END KILOMETER AUTO-FILL â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    </script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
