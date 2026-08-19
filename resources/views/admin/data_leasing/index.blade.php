@extends('admin.layouts.app')
@section('title', 'Data Leasing & Kontrak')
@section('content')
<div class="space-y-6 p-5">

    {{-- ALERTS --}}
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

    {{-- IMPORT SKIPPED DETAIL --}}
    @if (session('import_skipped'))
    <div x-data="{ open: false }" class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm">
        <div class="flex items-center justify-between">
            <span class="text-yellow-800 font-medium flex items-center gap-2">
                <i class="fa fa-exclamation-triangle text-yellow-500"></i>
                {{ count(session('import_skipped')) }} baris dengan catatan saat import
            </span>
            <button @click="open = !open" class="text-yellow-600 hover:text-yellow-800 text-xs underline">
                <span x-text="open ? 'Sembunyikan' : 'Lihat Detail'"></span>
            </button>
        </div>
        <div x-show="open" x-cloak class="mt-3 space-y-1.5">
            @foreach (session('import_skipped') as $skip)
            <div class="flex items-start gap-2 text-xs {{ empty($skip['warn']) ? 'text-red-700' : 'text-yellow-700' }}">
                <i class="fa {{ empty($skip['warn']) ? 'fa-times-circle text-red-400' : 'fa-warning text-yellow-400' }} mt-0.5"></i>
                <span>
                    <strong>Baris {{ $skip['row'] }}</strong>
                    ({{ $skip['data'] }}):
                    {{ implode(', ', $skip['errors']) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- TAB NAV --}}
    <div x-data="{ activeTab: '{{ request('tab', 'leasing') }}' }">

        <div class="flex gap-1 border-b border-gray-200 mb-6">
            <button @click="activeTab = 'leasing'"
                :class="activeTab === 'leasing'
                    ? 'border-b-2 border-blue-600 text-blue-600 font-semibold bg-blue-50'
                    : 'text-gray-500 hover:text-gray-700'"
                class="px-5 py-2.5 text-sm rounded-t-lg transition-colors">
                <i class="bi bi-bank mr-1.5"></i> Data Leasing
            </button>
            <button @click="activeTab = 'kontrak'"
                :class="activeTab === 'kontrak'
                    ? 'border-b-2 border-indigo-600 text-indigo-600 font-semibold bg-indigo-50'
                    : 'text-gray-500 hover:text-gray-700'"
                class="px-5 py-2.5 text-sm rounded-t-lg transition-colors">
                <i class="bi bi-file-earmark-text mr-1.5"></i> Data Kontrak
            </button>
        </div>

        {{-- ═══════════════════════════════════════════
             TAB: DATA LEASING
        ═══════════════════════════════════════════ --}}
        <div x-show="activeTab === 'leasing'" x-cloak>

            {{-- PAGE HEADER --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Data Leasing</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Kelola data cicilan leasing kendaraan</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Export Excel --}}
                    <a href="{{ route('data-leasing.export-full') }}"
                        class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                        <i class="fa fa-file-excel-o text-sm"></i> Export Excel
                    </a>
                    {{-- Unduh Template --}}
                    <a href="{{ route('data-leasing.template') }}"
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                        <i class="fa fa-download text-sm"></i> Unduh Template
                    </a>
                    {{-- Import Excel --}}
                    <button onclick="openModal('modalImportLeasing')"
                        class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                        <i class="fa fa-upload text-sm"></i> Import Excel
                    </button>
                    {{-- Tambah Manual --}}
                    <button onclick="openModal('modalCreateLeasing')"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                        <i class="fa fa-plus text-sm"></i> Tambah Data Leasing
                    </button>
                </div>
            </div>

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Total Data</p>
                    <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $leasings->total() }}</h2>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Total Angsuran/Bln</p>
                    <h2 class="text-xl font-bold text-green-600 mt-2">
                        Rp {{ number_format($leasings->getCollection()->sum('angsuran_per_bulan'), 0, ',', '.') }}
                    </h2>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Cicilan Partial</p>
                    <h2 class="text-3xl font-bold text-yellow-500 mt-2">
                        {{ $leasings->getCollection()->filter(fn($l) => $l->status_cicilan === 'Partial')->count() }}
                    </h2>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Sudah Lunas</p>
                    <h2 class="text-3xl font-bold text-emerald-600 mt-2">
                        {{ $leasings->getCollection()->filter(fn($l) => $l->status_cicilan === 'Lunas')->count() }}
                    </h2>
                </div>
            </div>

            {{-- TABLE DATA LEASING --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

                {{-- SEARCH BAR --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-2 flex-1 flex-wrap">
                        <input type="hidden" name="tab" value="leasing">
                        <div class="relative flex-1 min-w-[180px]">
                            <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <input type="text" name="leasing_search" value="{{ request('leasing_search') }}"
                                placeholder="Cari no kontrak / mobil / nopol..."
                                class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>

                        {{-- Filter Status Cicilan --}}
                        <div class="inline-flex rounded-lg border border-gray-200 overflow-hidden text-xs">
                            <a href="{{ request()->fullUrlWithQuery(['leasing_status' => '', 'tab' => 'leasing', 'leasing_page' => 1]) }}"
                                class="px-3 py-1.5 font-medium transition-colors {{ !request('leasing_status') ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                                Semua
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['leasing_status' => 'Partial', 'tab' => 'leasing', 'leasing_page' => 1]) }}"
                                class="px-3 py-1.5 font-medium transition-colors border-l border-gray-200 {{ request('leasing_status') === 'Partial' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                                Partial
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['leasing_status' => 'Lunas', 'tab' => 'leasing', 'leasing_page' => 1]) }}"
                                class="px-3 py-1.5 font-medium transition-colors border-l border-gray-200 {{ request('leasing_status') === 'Lunas' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                                Lunas
                            </a>
                        </div>

                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fa fa-search text-xs"></i> Cari
                        </button>
                        @if(request('leasing_search') || request('leasing_status'))
                            <a href="{{ request()->fullUrlWithQuery(['leasing_search' => '', 'leasing_status' => '', 'tab' => 'leasing', 'leasing_page' => 1]) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fa fa-rotate-left text-xs"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">No Kontrak</th>
                                <th class="px-4 py-3 text-left">Mobil</th>
                                <th class="px-4 py-3 text-left">Nopol</th>
                                <th class="px-4 py-3 text-right">Angsuran/Bln</th>
                                <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-left">Periode</th>
                                <th class="px-4 py-3 text-center">Cicilan</th>
                                <th class="px-4 py-3 text-right">Total Cicilan</th>
                                <th class="px-4 py-3 text-right">Sisa Cicilan</th>
                                <th class="px-4 py-3 text-left">Asuransi</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($leasings as $index => $item)
                            @php
                                $statusClass = match($item->status_cicilan) {
                                    'Lunas'       => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'Partial'     => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'Belum Mulai' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    default       => 'bg-gray-100 text-gray-500 border-gray-200',
                                };
                                $totalCicilan = $item->jumlah_cicilan * $item->angsuran_per_bulan;
                                $sisaCicilan  = $item->cicilan_tersisa * $item->angsuran_per_bulan;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-500">{{ $leasings->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-blue-700">{{ $item->no_kontrak ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->mobil ?? '-' }} {{ $item->tahun ? '('.$item->tahun.')' : '' }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $item->nopol ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-medium text-green-700">
                                    Rp {{ number_format($item->angsuran_per_bulan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">{{ $item->jatuh_tempo ? 'Tgl '.$item->jatuh_tempo : '-' }}</td>
                                <td class="px-4 py-3 text-xs">
                                    {{ $item->periode_mulai ? $item->periode_mulai->format('M Y') : '-' }}
                                    @if($item->periode_selesai) – {{ $item->periode_selesai->format('M Y') }} @endif
                                </td>
                                {{-- CICILAN: sisa x / total x --}}
                                <td class="px-4 py-3 text-center">
                                    @php $tc = $item->jumlah_cicilan; $sc = $item->cicilan_tersisa; $sudah = $tc - $sc; @endphp
                                    @if($tc > 0)
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span class="text-sm font-bold {{ $sc > 0 ? 'text-orange-600' : 'text-emerald-600' }}">
                                                {{ $sc }}x sisa
                                            </span>
                                    
                                    
                                    
                                        </div>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-700">
                                    Rp {{ number_format($totalCicilan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold {{ $sisaCicilan > 0 ? 'text-orange-600' : 'text-emerald-600' }}">
                                    Rp {{ number_format($sisaCicilan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    {{ $item->asuransi_leasing ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusClass }}">
                                        {{ $item->status_cicilan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openDetailLeasingModal({{ json_encode([
                                            'no_kontrak'         => $item->no_kontrak,
                                            'mobil'              => $item->mobil,
                                            'tahun'              => $item->tahun,
                                            'nopol'              => $item->nopol,
                                            'user_leasing'       => $item->user_leasing,
                                            'angsuran_per_bulan' => $item->angsuran_per_bulan,
                                            'jatuh_tempo'        => $item->jatuh_tempo,
                                            'periode_mulai'      => $item->periode_mulai?->format('d M Y'),
                                            'periode_selesai'    => $item->periode_selesai?->format('d M Y'),
                                            'total_cicilan'      => $totalCicilan,
                                            'sisa_cicilan'       => $sisaCicilan,
                                            'status_cicilan'     => $item->status_cicilan,
                                            'personal_account'   => $item->personal_account,
                                            'cara_bayar'         => $item->cara_bayar,
                                            'sumber_dana_debit'  => $item->sumber_dana_debit,
                                            'asuransi_leasing'   => $item->asuransi_leasing,
                                        ]) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                            <i class="fa fa-eye"></i> Detail
                                        </button>
                                        <form action="{{ route('data-leasing.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus data leasing ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13" class="px-4 py-12 text-center text-gray-400">
                                    <i class="bi bi-bank text-4xl block mb-3"></i>
                                    Belum ada data leasing
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($leasings->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    <x-pagination :paginator="$leasings" />
                </div>
                @endif
            </div>
        </div>
        {{-- END TAB LEASING --}}


        {{-- ═══════════════════════════════════════════
             TAB: DATA KONTRAK
        ═══════════════════════════════════════════ --}}
        <div x-show="activeTab === 'kontrak'" x-cloak>

            {{-- PAGE HEADER --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Data Kontrak</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Master data kontrak kendaraan</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Export Excel --}}
                    <a href="{{ route('data-kontrak.export-full') }}"
                        class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                        <i class="fa fa-file-excel-o text-sm"></i> Export Excel
                    </a>
                    <button onclick="openModal('modalCreateKontrak')"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                        <i class="fa fa-plus text-sm"></i> Tambah Data Kontrak
                    </button>
                </div>
            </div>

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Total Kontrak</p>
                    <h2 class="text-3xl font-bold text-indigo-600 mt-2">{{ $kontraks->total() }}</h2>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Total Angsuran/Bln</p>
                    <h2 class="text-xl font-bold text-green-600 mt-2">
                        Rp {{ number_format($kontraks->getCollection()->sum('angsuran_per_bulan'), 0, ',', '.') }}
                    </h2>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Sedang Berjalan</p>
                    <h2 class="text-3xl font-bold text-yellow-500 mt-2">
                        {{ $kontraks->getCollection()->filter(fn($k) => $k->status_cicilan === 'Partial')->count() }}
                    </h2>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Lunas</p>
                    <h2 class="text-3xl font-bold text-emerald-600 mt-2">
                        {{ $kontraks->getCollection()->filter(fn($k) => $k->status_cicilan === 'Lunas')->count() }}
                    </h2>
                </div>
            </div>

            {{-- TABLE DATA KONTRAK --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

                {{-- SEARCH BAR --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-2 flex-1 flex-wrap">
                        <input type="hidden" name="tab" value="kontrak">
                        <div class="relative flex-1 min-w-[180px]">
                            <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <input type="text" name="kontrak_search" value="{{ request('kontrak_search') }}"
                                placeholder="Cari no kontrak / mobil / nopol..."
                                class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
                        </div>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fa fa-search text-xs"></i> Cari
                        </button>
                        @if(request('kontrak_search'))
                            <a href="{{ request()->fullUrlWithQuery(['kontrak_search' => '', 'tab' => 'kontrak', 'kontrak_page' => 1]) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fa fa-rotate-left text-xs"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Serial</th>
                                <th class="px-4 py-3 text-left">No Kontrak</th>
                                <th class="px-4 py-3 text-left">Kendaraan</th>
                                <th class="px-4 py-3 text-left">Nopol</th>
                                <th class="px-4 py-3 text-left">User</th>
                                <th class="px-4 py-3 text-right">Angsuran/Bln</th>
                                <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-left">Periode</th>
                                <th class="px-4 py-3 text-left">Asuransi</th>
                                <th class="px-4 py-3 text-center">Bukti</th>
                                <th class="px-4 py-3 text-center">Lampiran</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($kontraks as $index => $item)
                            @php
                                $statusClass = match($item->status_cicilan) {
                                    'Lunas'       => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'Partial'     => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'Belum Mulai' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    default       => 'bg-gray-100 text-gray-500 border-gray-200',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-500">{{ $kontraks->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
                                        {{ $item->serial_number ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-700">{{ $item->no_kontrak ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->mobil ?? '-' }} {{ $item->tahun ? '('.$item->tahun.')' : '' }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $item->nopol ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->user_kontrak ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-medium text-green-700">
                                    Rp {{ number_format($item->angsuran_per_bulan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">{{ $item->jatuh_tempo ? 'Tgl '.$item->jatuh_tempo : '-' }}</td>
                                <td class="px-4 py-3 text-xs">
                                    {{ $item->periode_mulai ? $item->periode_mulai->format('M Y') : '-' }}
                                    @if($item->periode_selesai) – {{ $item->periode_selesai->format('M Y') }} @endif
                                </td>
                                <td class="px-4 py-3 text-xs">{{ $item->nama_asuransi ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($item->bukti)
                                    <button type="button"
                                        onclick="openSlideshow([{path:'{{ asset($item->bukti) }}', name:'{{ addslashes(basename($item->bukti)) }}'}], 0)"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                        <i class="bi bi-image text-sm"></i> {{ basename($item->bukti) }}
                                    </button>
                                    @else
                                    <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($item->attachments->count() > 0)
                                        @php
                                            $leasingAtts = $item->attachments->map(fn($a) => ['path' => asset($a->file_path), 'name' => $a->file_name])->values()->toArray();
                                        @endphp
                                        <button type="button"
                                            onclick="openSlideshow(JSON.parse(this.dataset.imgs),0)"
                                            data-imgs="{!! json_encode($leasingAtts, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_UNESCAPED_SLASHES) !!}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                            <i class="bi bi-images text-sm"></i>
                                            Lihat ({{ $item->attachments->count() }})
                                        </button>
                                    @else
                                    <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openDetailKontrakModal({{ json_encode([
                                            'id'                 => $item->id,
                                            'serial_number'      => $item->serial_number,
                                            'no_kontrak'         => $item->no_kontrak,
                                            'mobil'              => $item->mobil,
                                            'nopol'              => $item->nopol,
                                            'tahun'              => $item->tahun,
                                            'user_kontrak'       => $item->user_kontrak,
                                            'angsuran_per_bulan' => $item->angsuran_per_bulan,
                                            'jatuh_tempo'        => $item->jatuh_tempo,
                                            'periode_mulai'      => $item->periode_mulai?->format('d M Y'),
                                            'periode_selesai'    => $item->periode_selesai?->format('d M Y'),
                                            'personal_account'   => $item->personal_account,
                                            'sumber_dana_debit'  => $item->sumber_dana_debit,
                                            'cara_bayar'         => $item->cara_bayar,
                                            'nama_asuransi'      => $item->nama_asuransi,
                                            'alamat_asuransi'    => $item->alamat_asuransi,
                                            'nama_marketing'     => $item->nama_marketing,
                                            'kontak_marketing'   => $item->kontak_marketing,
                                            'nama_bengkel'       => $item->nama_bengkel,
                                            'kontak_bengkel'     => $item->kontak_bengkel,
                                            'bukti'              => $item->bukti,
                                            'attachments'        => $item->attachments->map(fn($a) => [
                                                'id'        => $a->id,
                                                'file_name' => $a->file_name,
                                                'file_path' => $a->file_path,
                                            ])->toArray(),
                                        ]) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                            <i class="fa fa-eye"></i> Detail
                                        </button>
                                        <button onclick="openEditKontrakModal({{ json_encode([
                                            'id'                 => $item->id,
                                            'serial_number'      => $item->serial_number,
                                            'no_kontrak'         => $item->no_kontrak,
                                            'kendaraan_id'       => $item->kendaraan_id,
                                            'mobil'              => $item->mobil,
                                            'nopol'              => $item->nopol,
                                            'tahun'              => $item->tahun,
                                            'user_kontrak'       => $item->user_kontrak,
                                            'angsuran_per_bulan' => $item->angsuran_per_bulan,
                                            'jatuh_tempo'        => $item->jatuh_tempo,
                                            'periode_mulai'      => $item->periode_mulai?->format('Y-m-d'),
                                            'periode_selesai'    => $item->periode_selesai?->format('Y-m-d'),
                                            'personal_account'   => $item->personal_account,
                                            'sumber_dana_debit'  => $item->sumber_dana_debit,
                                            'cara_bayar'         => $item->cara_bayar,
                                            'nama_asuransi'      => $item->nama_asuransi,
                                            'alamat_asuransi'    => $item->alamat_asuransi,
                                            'nama_marketing'     => $item->nama_marketing,
                                            'kontak_marketing'   => $item->kontak_marketing,
                                            'nama_bengkel'       => $item->nama_bengkel,
                                            'kontak_bengkel'     => $item->kontak_bengkel,
                                            'bukti'              => $item->bukti,
                                            'attachments'        => $item->attachments->map(fn($a) => [
                                                'id'        => $a->id,
                                                'file_name' => $a->file_name,
                                                'file_path' => $a->file_path,
                                            ])->toArray(),
                                        ]) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                                            <i class="fa fa-pencil"></i> Edit
                                        </button>
                                        <form action="{{ route('data-kontrak.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus kontrak {{ $item->serial_number ?? $item->no_kontrak }}? Data leasing terkait akan kehilangan referensinya.')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="px-4 py-12 text-center text-gray-400">
                                    <i class="bi bi-file-earmark-text text-4xl block mb-3"></i>
                                    Belum ada data kontrak
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($kontraks->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    <x-pagination :paginator="$kontraks" />
                </div>
                @endif
            </div>
        </div>
        {{-- END TAB KONTRAK --}}

    </div>{{-- END x-data tab wrapper --}}
</div>{{-- END .space-y-6 --}}



{{-- ══════════════════════════════════════════
     MODAL CREATE — DATA LEASING
══════════════════════════════════════════ --}}
<div id="modalCreateLeasing" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Tambah Data Leasing</h3>
            <button onclick="closeModal('modalCreateLeasing')" class="text-gray-400 hover:text-gray-600 text-xl">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form action="{{ route('data-leasing.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf

            {{-- Pilih Data Kontrak --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Pilih Data Kontrak <span class="text-gray-400 text-xs">(auto-fill semua field)</span>
                </label>
                <select name="data_kontrak_id" id="cl_data_kontrak_id"
                    onchange="fetchDataKontrakDetail(this.value, 'cl')"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Pilih Data Kontrak (Opsional) --</option>
                    @foreach ($dataKontraks as $dk)
                    <option value="{{ $dk->id }}">{{ $dk->no_kontrak ?? $dk->serial_number }} – {{ $dk->mobil ?? '-' }} ({{ $dk->nopol ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No Kontrak</label>
                    <input type="text" id="cl_no_kontrak_display" disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed"
                        placeholder="Auto-fill dari kontrak">
                    <input type="hidden" name="no_kontrak" id="cl_no_kontrak">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User Leasing</label>
                    <input type="text" id="cl_user_leasing_display" disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed"
                        placeholder="Auto-fill dari kontrak">
                    <input type="hidden" name="user_leasing" id="cl_user_leasing">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobil</label>
                    <input type="text" id="cl_mobil_display" disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed"
                        placeholder="Auto-fill dari kontrak">
                    <input type="hidden" name="mobil" id="cl_mobil">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nopol</label>
                    <input type="text" id="cl_nopol_display" disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed"
                        placeholder="Auto-fill dari kontrak">
                    <input type="hidden" name="nopol" id="cl_nopol">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <input type="text" id="cl_tahun_display" disabled
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed"
                        placeholder="Auto-fill dari kontrak">
                    <input type="hidden" name="tahun" id="cl_tahun">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Angsuran/Bulan (Rp)</label>
                    <input type="number" name="angsuran_per_bulan" id="cl_angsuran" min="0"
                        oninput="updateCicilanPreview('cl')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo <span class="text-gray-400 text-xs">(tgl/bln)</span></label>
                    <input type="number" name="jatuh_tempo" id="cl_jatuh_tempo" min="1" max="31"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="1–31">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Mulai</label>
                    <input type="date" name="periode_mulai" id="cl_periode_mulai"
                        oninput="updateCicilanPreview('cl')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Selesai</label>
                    <input type="date" name="periode_selesai" id="cl_periode_selesai"
                        oninput="updateCicilanPreview('cl')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personal Account <span class="text-gray-400 text-xs">(opsional)</span></label>
                    <input type="text" name="personal_account" id="cl_personal_account"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Nama / No. Rekening">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Dana Debit</label>
                    <input type="text" name="sumber_dana_debit" id="cl_sumber_dana_debit"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Nama bank / sumber dana">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cara Bayar</label>
                    <input type="text" name="cara_bayar" id="cl_cara_bayar"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Transfer / Auto Debit / dll">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asuransi Leasing</label>
                    <input type="text" name="asuransi_leasing" id="cl_asuransi"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Nama asuransi">
                </div>
            </div>

            {{-- Preview Cicilan --}}
            <div id="cl_cicilan_preview" class="hidden rounded-xl bg-blue-50 border border-blue-200 px-4 py-3">
                <p class="text-xs text-blue-600 font-semibold mb-1">Preview Cicilan</p>
                <div class="flex gap-6 text-sm">
                    <span>Total: <strong id="cl_preview_total" class="text-blue-700">-</strong></span>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('modalCreateLeasing')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">
                    <i class="fa fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════
     MODAL EDIT — DATA LEASING
══════════════════════════════════════════ --}}
<div id="modalEditLeasing" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Edit Data Leasing</h3>
            <button onclick="closeModal('modalEditLeasing')" class="text-gray-400 hover:text-gray-600 text-xl">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="formEditLeasing" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No Kontrak</label>
                    <input type="text" name="no_kontrak" id="el_no_kontrak"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User Leasing</label>
                    <input type="text" name="user_leasing" id="el_user_leasing"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobil</label>
                    <input type="text" name="mobil" id="el_mobil"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nopol</label>
                    <input type="text" name="nopol" id="el_nopol"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <input type="text" name="tahun" id="el_tahun"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Angsuran/Bulan (Rp)</label>
                    <input type="number" name="angsuran_per_bulan" id="el_angsuran" min="0"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo</label>
                    <input type="number" name="jatuh_tempo" id="el_jatuh_tempo" min="1" max="31"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Mulai</label>
                    <input type="date" name="periode_mulai" id="el_periode_mulai"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Selesai</label>
                    <input type="date" name="periode_selesai" id="el_periode_selesai"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personal Account <span class="text-gray-400 text-xs">(opsional)</span></label>
                    <input type="text" name="personal_account" id="el_personal_account"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Dana Debit</label>
                    <input type="text" name="sumber_dana_debit" id="el_sumber_dana"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cara Bayar</label>
                    <input type="text" name="cara_bayar" id="el_cara_bayar"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asuransi Leasing</label>
                    <input type="text" name="asuransi_leasing" id="el_asuransi"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            {{-- Info Cicilan (read-only) --}}
            <div class="rounded-xl bg-gray-50 border border-gray-200 px-4 py-3">
                <p class="text-xs text-gray-500 font-semibold mb-2">Info Cicilan (dihitung otomatis)</p>
                <div class="flex flex-wrap gap-4 text-sm">
                    <span>Total Cicilan: <strong id="el_info_total" class="text-blue-700">-</strong></span>
                    <span>Cicilan Tersisa: <strong id="el_info_tersisa" class="text-orange-600">-</strong></span>
                    <span>Status: <strong id="el_info_status" class="text-gray-700">-</strong></span>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('modalEditLeasing')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">
                    <i class="fa fa-save mr-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════
     MODAL CREATE — DATA KONTRAK
══════════════════════════════════════════ --}}
<div id="modalCreateKontrak" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Tambah Data Kontrak</h3>
            <button onclick="closeModal('modalCreateKontrak')" class="text-gray-400 hover:text-gray-600 text-xl">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form action="{{ route('data-kontrak.store') }}" method="POST" enctype="multipart/form-data"
            class="px-6 py-5 space-y-4">
            @csrf

            {{-- No Kontrak (auto-generate saat modal dibuka) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number <span class="text-xs text-gray-400">(auto-generate)</span></label>
                <input type="text" id="ck_no_kontrak_display" disabled
                    class="w-full border border-gray-200 bg-indigo-50 rounded-xl px-3 py-2.5 text-sm text-indigo-700 font-semibold outline-none cursor-not-allowed"
                    placeholder="Generating...">
                <input type="hidden" name="serial_number" id="ck_serial_number">
            </div>

            {{-- No Kontrak — input manual --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontrak <span class="text-red-500">*</span></label>
                <input type="text" name="no_kontrak" id="ck_no_kontrak" required
                    placeholder="Contoh: PKS/2026/001"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <p class="text-xs text-gray-400 mt-1">Nomor kontrak resmi dari dokumen fisik, tidak boleh sama</p>
            </div>

            {{-- Pilih Kendaraan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kendaraan <span class="text-gray-400 text-xs">(auto-fill Mobil & Nopol)</span>
                </label>
                <select name="kendaraan_id" id="ck_kendaraan_id"
                    onchange="fetchKendaraanDetail(this.value, 'ck')"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">-- Pilih Kendaraan (Opsional) --</option>
                    @foreach ($kendaraans as $k)
                    <option value="{{ $k->id }}">{{ $k->merk }} – {{ $k->nopol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobil / Merk</label>
                    <input type="text" name="mobil" id="ck_mobil" readonly
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none cursor-not-allowed"
                        placeholder="Auto-fill dari kendaraan">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nopol</label>
                    <input type="text" name="nopol" id="ck_nopol" readonly
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none cursor-not-allowed"
                        placeholder="Auto-fill dari kendaraan">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <input type="text" name="tahun" id="ck_tahun" readonly
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none cursor-not-allowed"
                        placeholder="Auto-fill dari kendaraan">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User / Customer</label>
                    <input type="text" name="user_kontrak"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="Nama pengguna atau customer">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Angsuran/Bulan (Rp)</label>
                    <input type="number" name="angsuran_per_bulan" id="ck_angsuran" min="0"
                        oninput="updateCicilanPreview('ck')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo <span class="text-gray-400 text-xs">(tgl/bln)</span></label>
                    <input type="number" name="jatuh_tempo" min="1" max="31"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="1–31">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Mulai</label>
                    <input type="date" name="periode_mulai" id="ck_periode_mulai"
                        oninput="updateCicilanPreview('ck')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Selesai</label>
                    <input type="date" name="periode_selesai" id="ck_periode_selesai"
                        oninput="updateCicilanPreview('ck')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personal Account (TA) <span class="text-gray-400 text-xs">(opsional)</span></label>
                    <input type="text" name="personal_account"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="Nama / No. Rekening">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Dana Debit</label>
                    <input type="text" name="sumber_dana_debit"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="Nama bank / sumber dana">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cara Bayar</label>
                    <input type="text" name="cara_bayar"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="Transfer / Auto Debit / dll">
                </div>
            </div>

            {{-- Preview Cicilan --}}
            <div id="ck_cicilan_preview" class="hidden rounded-xl bg-indigo-50 border border-indigo-200 px-4 py-3">
                <p class="text-xs text-indigo-600 font-semibold mb-1">Preview Cicilan (otomatis)</p>
                <div class="flex flex-wrap gap-4 text-sm">
                    <span>Total Cicilan: <strong id="ck_preview_total" class="text-indigo-700">-</strong></span>
                    <span>Total Nilai: <strong id="ck_preview_nilai" class="text-green-700">-</strong></span>
                </div>
            </div>

            {{-- ASURANSI --}}
            <div class="rounded-xl border border-indigo-200 bg-indigo-50/40 p-4 space-y-3">
                <p class="text-sm font-semibold text-indigo-700 flex items-center gap-1.5">
                    <i class="fa fa-shield"></i> Data Asuransi <span class="text-red-500 text-xs font-semibold">* Wajib diisi</span>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Asuransi <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_asuransi" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="Contoh: Jasa Raharja, Astra Insurance">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
                        <textarea name="alamat_asuransi" rows="2"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white resize-none"
                            placeholder="Masukkan alamat lengkap..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Marketing</label>
                        <input type="text" name="nama_marketing"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="Nama marketing">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kontak Marketing</label>
                        <input type="number" name="kontak_marketing"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="08xx-xxxx-xxxx">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Bengkel</label>
                        <input type="text" name="nama_bengkel"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="Nama bengkel rekanan">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kontak Bengkel</label>
                        <input type="number" name="kontak_bengkel"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
            </div>

            {{-- Bukti (single file) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Bukti <span class="text-red-500">*</span>
                    <span class="text-gray-400 text-xs font-normal">(wajib, maks 5MB)</span>
                </label>
                <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700">
            </div>

            {{-- Attachments (multiple) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lampiran <span class="text-gray-400 text-xs">(opsional, bisa lebih dari 1, maks 5MB/file)</span>
                </label>
                <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('modalCreateKontrak')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors">
                    <i class="fa fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════
     MODAL EDIT — DATA KONTRAK
══════════════════════════════════════════ --}}
<div id="modalEditKontrak" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Edit Data Kontrak</h3>
            <button onclick="closeModal('modalEditKontrak')" class="text-gray-400 hover:text-gray-600 text-xl">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="formEditKontrak" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')

            {{-- No Kontrak (readonly on edit) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number <span class="text-xs text-gray-400">(auto-generate, tidak dapat diubah)</span></label>
                <input type="text" id="ek_no_kontrak_display" disabled
                    class="w-full border border-gray-200 bg-indigo-50 rounded-xl px-3 py-2.5 text-sm text-indigo-700 font-semibold outline-none cursor-not-allowed">
            </div>

            {{-- No Kontrak — editable --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontrak <span class="text-red-500">*</span></label>
                <input type="text" name="no_kontrak" id="ek_no_kontrak" required
                    placeholder="Contoh: PKS/2026/001"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <p class="text-xs text-gray-400 mt-1">Nomor kontrak resmi dari dokumen fisik, tidak boleh sama</p>
            </div>

            {{-- Pilih Kendaraan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kendaraan</label>
                <select name="kendaraan_id" id="ek_kendaraan_id"
                    onchange="fetchKendaraanDetail(this.value, 'ek')"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">-- Pilih Kendaraan (Opsional) --</option>
                    @foreach ($kendaraans as $k)
                    <option value="{{ $k->id }}">{{ $k->merk }} – {{ $k->nopol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobil / Merk</label>
                    <input type="text" name="mobil" id="ek_mobil" readonly
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nopol</label>
                    <input type="text" name="nopol" id="ek_nopol" readonly
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <input type="text" name="tahun" id="ek_tahun" readonly
                        class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User / Customer</label>
                    <input type="text" name="user_kontrak" id="ek_user_kontrak"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Angsuran/Bulan (Rp)</label>
                    <input type="number" name="angsuran_per_bulan" id="ek_angsuran" min="0"
                        oninput="updateCicilanPreview('ek')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo</label>
                    <input type="number" name="jatuh_tempo" id="ek_jatuh_tempo" min="1" max="31"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Mulai</label>
                    <input type="date" name="periode_mulai" id="ek_periode_mulai"
                        oninput="updateCicilanPreview('ek')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Selesai</label>
                    <input type="date" name="periode_selesai" id="ek_periode_selesai"
                        oninput="updateCicilanPreview('ek')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personal Account (TA) <span class="text-gray-400 text-xs">(opsional)</span></label>
                    <input type="text" name="personal_account" id="ek_personal_account"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Dana Debit</label>
                    <input type="text" name="sumber_dana_debit" id="ek_sumber_dana"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cara Bayar</label>
                    <input type="text" name="cara_bayar" id="ek_cara_bayar"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            {{-- Preview Cicilan --}}
            <div id="ek_cicilan_preview" class="rounded-xl bg-indigo-50 border border-indigo-200 px-4 py-3">
                <p class="text-xs text-indigo-600 font-semibold mb-1">Info Cicilan (otomatis)</p>
                <div class="flex flex-wrap gap-4 text-sm">
                    <span>Total Cicilan: <strong id="ek_preview_total" class="text-indigo-700">-</strong></span>
                    <span>Total Nilai: <strong id="ek_preview_nilai" class="text-green-700">-</strong></span>
                </div>
            </div>

            {{-- ASURANSI --}}
            <div class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-4 space-y-3">
                <p class="text-sm font-semibold text-indigo-700 flex items-center gap-1.5">
                    <i class="fa fa-shield"></i> Data Asuransi <span class="text-xs font-normal text-gray-400">(opsional)</span>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Asuransi</label>
                        <input type="text" name="nama_asuransi" id="ek_nama_asuransi"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="Contoh: Jasa Raharja, Astra Insurance">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
                        <textarea name="alamat_asuransi" id="ek_alamat_asuransi" rows="2"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white resize-none"
                            placeholder="Masukkan alamat lengkap..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Marketing</label>
                        <input type="text" name="nama_marketing" id="ek_nama_marketing"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="Nama marketing">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kontak Marketing</label>
                        <input type="number" name="kontak_marketing" id="ek_kontak_marketing"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="08xx-xxxx-xxxx">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Bengkel</label>
                        <input type="text" name="nama_bengkel" id="ek_nama_bengkel"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="Nama bengkel rekanan">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kontak Bengkel</label>
                        <input type="number" name="kontak_bengkel" id="ek_kontak_bengkel"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
            </div>

            {{-- Bukti baru --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Baru <span class="text-gray-400 text-xs">(kosongkan jika tidak diubah)</span></label>
                <div id="ek_bukti_existing" class="mb-2 hidden">
                    <a id="ek_bukti_link" href="#" target="_blank"
                        class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
                        <i class="fa fa-file"></i> Lihat bukti saat ini
                    </a>
                </div>
                <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700">
            </div>

            {{-- Attachments yang sudah ada --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran Yang Ada</label>
                <div id="ek_attachments_list" class="space-y-2 mb-3">
                    <p class="text-sm text-gray-400 italic">Belum ada lampiran</p>
                </div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tambah Lampiran Baru</label>
                <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('modalEditKontrak')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors">
                    <i class="fa fa-save mr-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════
     MODAL DETAIL — DATA KONTRAK
══════════════════════════════════════════ --}}
<div id="modalDetailKontrak" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Detail Data Kontrak</h3>
                <p id="dk_subtitle" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button onclick="closeModal('modalDetailKontrak')" class="text-gray-400 hover:text-gray-600 text-xl">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="px-6 py-5 space-y-5">

            {{-- Info Utama --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Informasi Kendaraan</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Serial Number</p>
                        <p id="dk_serial_number" class="text-sm font-mono font-semibold text-indigo-600">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">No Kontrak</p>
                        <p id="dk_no_kontrak" class="text-sm font-semibold text-indigo-700">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Kendaraan</p>
                        <p id="dk_mobil" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Nopol</p>
                        <p id="dk_nopol" class="text-sm font-mono font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Tahun</p>
                        <p id="dk_tahun" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">User / Customer</p>
                        <p id="dk_user" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                </div>
            </div>

            {{-- Info Cicilan --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Cicilan & Pembayaran</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Angsuran/Bulan</p>
                        <p id="dk_angsuran" class="text-sm font-semibold text-green-700">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Jatuh Tempo</p>
                        <p id="dk_jatuh_tempo" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Periode</p>
                        <p id="dk_periode" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Personal Account</p>
                        <p id="dk_personal_account" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Sumber Dana Debit</p>
                        <p id="dk_sumber_dana" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Cara Bayar</p>
                        <p id="dk_cara_bayar" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                </div>
            </div>

            {{-- Info Asuransi --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Asuransi</p>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3 md:col-span-2">
                        <p class="text-xs text-gray-400 mb-0.5">Nama Asuransi</p>
                        <p id="dk_nama_asuransi" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 md:col-span-2">
                        <p class="text-xs text-gray-400 mb-0.5">Alamat Asuransi</p>
                        <p id="dk_alamat_asuransi" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Marketing</p>
                        <p id="dk_nama_marketing" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Kontak Marketing</p>
                        <p id="dk_kontak_marketing" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Bengkel</p>
                        <p id="dk_nama_bengkel" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Kontak Bengkel</p>
                        <p id="dk_kontak_bengkel" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                </div>
            </div>

            {{-- Bukti & Lampiran --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Dokumen</p>
                <div id="dk_dokumen" class="flex flex-wrap gap-2">
                    <span class="text-sm text-gray-400 italic">Tidak ada dokumen</span>
                </div>
            </div>

        </div>

        <div class="flex justify-end px-6 py-4 border-t border-gray-100">
            <button type="button" onclick="closeModal('modalDetailKontrak')"
                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL DETAIL — DATA LEASING
══════════════════════════════════════════ --}}
<div id="modalDetailLeasing" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Detail Data Leasing</h3>
                <p id="dl_subtitle" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button onclick="closeModal('modalDetailLeasing')" class="text-gray-400 hover:text-gray-600 text-xl">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="px-6 py-5 space-y-5">

            {{-- Info Kendaraan --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Informasi Kendaraan</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">No Kontrak</p>
                        <p id="dl_no_kontrak" class="text-sm font-semibold text-blue-700">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Kendaraan</p>
                        <p id="dl_mobil" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Nopol</p>
                        <p id="dl_nopol" class="text-sm font-mono font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Tahun</p>
                        <p id="dl_tahun" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 col-span-2">
                        <p class="text-xs text-gray-400 mb-0.5">User Leasing</p>
                        <p id="dl_user_leasing" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                </div>
            </div>

            {{-- Info Cicilan --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Cicilan & Pembayaran</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Angsuran/Bulan</p>
                        <p id="dl_angsuran" class="text-sm font-semibold text-green-700">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Jatuh Tempo</p>
                        <p id="dl_jatuh_tempo" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Periode</p>
                        <p id="dl_periode" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Total Cicilan</p>
                        <p id="dl_total_cicilan" class="text-sm font-semibold text-gray-700">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Sisa Cicilan</p>
                        <p id="dl_sisa_cicilan" class="text-sm font-semibold text-orange-600">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Status</p>
                        <p id="dl_status_cicilan" class="text-sm font-semibold">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Personal Account</p>
                        <p id="dl_personal_account" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Cara Bayar</p>
                        <p id="dl_cara_bayar" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Sumber Dana Debit</p>
                        <p id="dl_sumber_dana" class="text-sm font-medium text-gray-800">-</p>
                    </div>
                </div>
            </div>

            {{-- Info Asuransi --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Asuransi Leasing</p>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-400 mb-0.5">Nama Asuransi</p>
                    <p id="dl_asuransi_leasing" class="text-sm font-medium text-gray-800">-</p>
                </div>
            </div>

        </div>

        <div class="flex justify-end px-6 py-4 border-t border-gray-100">
            <button type="button" onclick="closeModal('modalDetailLeasing')"
                class="px-5 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL IMPORT — DATA LEASING
══════════════════════════════════════════ --}}
<div id="modalImportLeasing" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Import Data Leasing</h3>
            <button onclick="closeModal('modalImportLeasing')" class="text-gray-400 hover:text-gray-600 text-xl">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form action="{{ route('data-leasing.import') }}" method="POST" enctype="multipart/form-data"
            class="px-6 py-5 space-y-4">
            @csrf

            {{-- Petunjuk --}}
            <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700 space-y-1">
                <p class="font-semibold flex items-center gap-1.5"><i class="fa fa-info-circle"></i> Petunjuk Import</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-blue-600">
                    <li>Unduh template terlebih dahulu, isi data sesuai format</li>
                    <li>Hapus baris contoh (berwarna kuning) sebelum upload</li>
                    <li>Format tanggal: <code class="bg-blue-100 px-1 rounded">DD/MM/YYYY</code> (contoh: 01/01/2026)</li>
                    <li>Kolom <code class="bg-blue-100 px-1 rounded">angsuran_per_bulan</code>: angka tanpa titik/koma (contoh: 5000000)</li>
                    <li>Nomor kontrak akan di-generate otomatis (lanjut dari nomor terakhir)</li>
                    <li>Baris dengan error akan di-skip, baris valid tetap diimport</li>
                </ul>
            </div>

            {{-- File upload --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    File Excel <span class="text-red-500">*</span>
                    <span class="text-gray-400 text-xs font-normal">(xlsx, xls, csv — maks 5MB)</span>
                </label>
                <input type="file" name="file_import" accept=".xlsx,.xls,.csv" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                           file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                           file:text-xs file:font-medium file:bg-orange-50 file:text-orange-700
                           hover:file:bg-orange-100 cursor-pointer">
            </div>

            {{-- Link unduh template --}}
            <p class="text-xs text-gray-500">
                Belum punya template?
                <a href="{{ route('data-leasing.template') }}"
                    class="text-blue-600 hover:underline font-medium">
                    <i class="fa fa-download text-xs mr-0.5"></i> Unduh Template Excel
                </a>
            </p>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" onclick="closeModal('modalImportLeasing')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-xl transition-colors">
                    <i class="fa fa-upload mr-1"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════ --}}
<script>
/* ─── Helpers Modal ─── */
function openModal(id) {
    const el = document.getElementById(id);
    el.classList.remove('hidden');
    el.classList.add('flex');
    // Auto-generate no kontrak saat modal Create Kontrak dibuka
    if (id === 'modalCreateKontrak') {
        autoGenerateNoKontrak();
    }
}
function closeModal(id) {
    const el = document.getElementById(id);
    el.classList.add('hidden');
    el.classList.remove('flex');
}

/* ─── Popup Alert ─── */
(function() {
    const overlay = document.getElementById('alertOverlay');
    const box     = document.getElementById('alertBox');
    if (!overlay) return;

    setTimeout(function() {
        overlay.style.opacity      = '1';
        overlay.style.pointerEvents = 'auto';
        box.style.transform        = 'translateY(0)';
    }, 80);

    const timer = setTimeout(closeAlert, 5000);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeAlert();
    });

    function closeAlert() {
        clearTimeout(timer);
        overlay.style.opacity      = '0';
        overlay.style.pointerEvents = 'none';
        box.style.transform        = 'translateY(-16px)';
    }
    window.closeAlert = closeAlert;
})();

/* ─── Auto-reopen modal jika ada validation error ─── */
@if ($errors->any() && !session('success'))
document.addEventListener('DOMContentLoaded', function() {
    openModal('modalCreateKontrak');
});
@endif

/* ─── Hitung cicilan (JS) ─── */
function hitungCicilan(mulai, selesai) {
    if (!mulai || !selesai) return 0;
    const d1 = new Date(mulai);
    const d2 = new Date(selesai);
    return Math.max(0, (d2.getFullYear() - d1.getFullYear()) * 12 + (d2.getMonth() - d1.getMonth()));
}

function formatRupiah(n) {
    return 'Rp ' + parseInt(n || 0).toLocaleString('id-ID');
}

/* ─── Update preview cicilan di form kontrak ─── */
function updateCicilanPreview(prefix) {
    const mulai   = document.getElementById(prefix + '_periode_mulai')?.value;
    const selesai = document.getElementById(prefix + '_periode_selesai')?.value;
    const angsuran = parseInt(document.getElementById(prefix + '_angsuran')?.value || 0);
    const total   = hitungCicilan(mulai, selesai);
    const previewEl = document.getElementById(prefix + '_cicilan_preview');

    if (total > 0) {
        if (previewEl) previewEl.classList.remove('hidden');
        const totalEl = document.getElementById(prefix + '_preview_total');
        const nilaiEl = document.getElementById(prefix + '_preview_nilai');
        if (totalEl) totalEl.textContent = total + 'x cicilan';
        if (nilaiEl) nilaiEl.textContent = formatRupiah(total * angsuran);
    } else {
        if (previewEl) previewEl.classList.add('hidden');
    }
}

/* ─── AJAX: Fetch Data Kontrak detail untuk form Leasing ─── */
function fetchDataKontrakDetail(id, prefix) {
    if (!id) return;
    fetch(`{{ url('/admin/data-leasing/data-kontrak') }}/${id}/detail`)
        .then(r => r.json())
        .then(data => {
            setVal(prefix + '_no_kontrak_display', data.no_kontrak, true);
            setVal(prefix + '_no_kontrak', data.no_kontrak);
            setVal(prefix + '_user_leasing_display', data.user_leasing, true);
            setVal(prefix + '_user_leasing', data.user_leasing);
            setVal(prefix + '_mobil_display', data.mobil, true);
            setVal(prefix + '_mobil', data.mobil);
            setVal(prefix + '_nopol_display', data.nopol, true);
            setVal(prefix + '_nopol', data.nopol);
            setVal(prefix + '_tahun_display', data.tahun, true);
            setVal(prefix + '_tahun', data.tahun);
            setVal(prefix + '_angsuran', data.angsuran_per_bulan);
            setVal(prefix + '_jatuh_tempo', data.jatuh_tempo);
            setVal(prefix + '_periode_mulai', data.periode_mulai);
            setVal(prefix + '_periode_selesai', data.periode_selesai);
            setVal(prefix + '_personal_account', data.personal_account);
            setVal(prefix + '_sumber_dana_debit', data.sumber_dana_debit);
            setVal(prefix + '_cara_bayar', data.cara_bayar);
            setVal(prefix + '_asuransi', data.nama_asuransi);

            // Update preview cicilan
            const total = hitungCicilan(data.periode_mulai, data.periode_selesai);
            const previewEl = document.getElementById(prefix + '_cicilan_preview');
            const totalEl   = document.getElementById(prefix + '_preview_total');
            if (total > 0 && previewEl) {
                previewEl.classList.remove('hidden');
                if (totalEl) totalEl.textContent = total + 'x cicilan';
            }
        })
        .catch(e => console.error('Error fetch kontrak detail:', e));
}

/* ─── AJAX: Fetch Kendaraan detail untuk form Kontrak ─── */
function fetchKendaraanDetail(id, prefix) {
    if (!id) {
        setVal(prefix + '_mobil', '');
        setVal(prefix + '_nopol', '');
        setVal(prefix + '_tahun', '');
        return;
    }
    fetch(`{{ url('/admin/data-kontrak/kendaraan') }}/${id}/detail`)
        .then(r => r.json())
        .then(data => {
            setVal(prefix + '_mobil', data.mobil);
            setVal(prefix + '_nopol', data.nopol);
            setVal(prefix + '_tahun', data.tahun);
        })
        .catch(e => console.error('Error fetch kendaraan detail:', e));
}

/* ─── AJAX: Auto-Generate Serial Number (dipanggil saat modal dibuka) ─── */
function autoGenerateNoKontrak() {
    const displayEl = document.getElementById('ck_no_kontrak_display');
    const hiddenEl  = document.getElementById('ck_serial_number');
    if (displayEl) displayEl.value = 'Generating...';
    fetch(`{{ route('data-kontrak.generate-no') }}`)
        .then(r => r.json())
        .then(data => {
            const sn = data.serial_number || data.no_kontrak || '';
            if (displayEl) displayEl.value = sn;
            if (hiddenEl)  hiddenEl.value  = sn;
        })
        .catch(e => {
            console.error('Error generate serial number:', e);
            if (displayEl) displayEl.value = 'Error';
        });
}

/* ─── Helper set value ─── */
function setVal(id, val, isDisplay = false) {
    const el = document.getElementById(id);
    if (el) el.value = val ?? '';
}

/* ─── Open Edit Leasing Modal ─── */
function openEditLeasingModal(item) {
    document.getElementById('formEditLeasing').action = `/admin/data-leasing/${item.id}`;
    setVal('el_no_kontrak',       item.no_kontrak);
    setVal('el_user_leasing',     item.user_leasing);
    setVal('el_mobil',            item.mobil);
    setVal('el_nopol',            item.nopol);
    setVal('el_tahun',            item.tahun);
    setVal('el_angsuran',         item.angsuran_per_bulan);
    setVal('el_jatuh_tempo',      item.jatuh_tempo);
    setVal('el_periode_mulai',    item.periode_mulai);
    setVal('el_periode_selesai',  item.periode_selesai);
    setVal('el_personal_account', item.personal_account);
    setVal('el_sumber_dana',      item.sumber_dana_debit);
    setVal('el_cara_bayar',       item.cara_bayar);
    setVal('el_asuransi',         item.asuransi_leasing);

    // Hitung cicilan info
    const total   = hitungCicilan(item.periode_mulai, item.periode_selesai);
    const now     = new Date();
    const mulai   = item.periode_mulai ? new Date(item.periode_mulai) : null;
    let tersisa   = total;
    if (mulai && now >= mulai) {
        const lewat = (now.getFullYear() - mulai.getFullYear()) * 12 + (now.getMonth() - mulai.getMonth());
        tersisa = Math.max(0, total - lewat);
    }
    const status  = tersisa <= 0 ? 'Lunas' : (mulai && now >= mulai ? 'Partial' : 'Belum Mulai');

    setVal('el_info_total',   total + 'x');
    setVal('el_info_tersisa', tersisa + 'x');
    setVal('el_info_status',  status);

    // Update display fields manually since they don't have setVal ID
    document.getElementById('el_info_total').textContent   = total + 'x cicilan';
    document.getElementById('el_info_tersisa').textContent = tersisa + 'x';
    document.getElementById('el_info_status').textContent  = status;

    openModal('modalEditLeasing');
}

/* ─── Open Edit Kontrak Modal ─── */
function openEditKontrakModal(item) {
    document.getElementById('formEditKontrak').action = `/admin/data-kontrak/${item.id}`;

    document.getElementById('ek_no_kontrak_display').value = item.serial_number ?? '';
    setVal('ek_no_kontrak', item.no_kontrak);
    setVal('ek_mobil',            item.mobil);
    setVal('ek_nopol',            item.nopol);
    setVal('ek_tahun',            item.tahun);
    setVal('ek_user_kontrak',     item.user_kontrak);
    setVal('ek_angsuran',         item.angsuran_per_bulan);
    setVal('ek_jatuh_tempo',      item.jatuh_tempo);
    setVal('ek_periode_mulai',    item.periode_mulai);
    setVal('ek_periode_selesai',  item.periode_selesai);
    setVal('ek_personal_account', item.personal_account);
    setVal('ek_sumber_dana',      item.sumber_dana_debit);
    setVal('ek_cara_bayar',       item.cara_bayar);
    // 6 field asuransi
    setVal('ek_nama_asuransi',    item.nama_asuransi);
    const alamatEl = document.getElementById('ek_alamat_asuransi');
    if (alamatEl) alamatEl.value = item.alamat_asuransi ?? '';
    setVal('ek_nama_marketing',   item.nama_marketing);
    setVal('ek_kontak_marketing', item.kontak_marketing);
    setVal('ek_nama_bengkel',     item.nama_bengkel);
    setVal('ek_kontak_bengkel',   item.kontak_bengkel);

    // Set kendaraan dropdown
    const kdSelect = document.getElementById('ek_kendaraan_id');
    if (kdSelect && item.kendaraan_id) kdSelect.value = item.kendaraan_id;

    // Preview cicilan
    updateCicilanPreview('ek');

    // Bukti existing
    const buktiDiv  = document.getElementById('ek_bukti_existing');
    const buktiLink = document.getElementById('ek_bukti_link');
    if (item.bukti) {
        buktiDiv.classList.remove('hidden');
        buktiLink.href = '/' + item.bukti;
    } else {
        buktiDiv.classList.add('hidden');
    }

    // Attachments list
    const attList = document.getElementById('ek_attachments_list');
    if (item.attachments && item.attachments.length > 0) {
        attList.innerHTML = item.attachments.map(a => `
            <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2" id="att_row_${a.id}">
                <a href="/${a.file_path}" target="_blank"
                    class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1.5">
                    <i class="fa fa-paperclip text-xs"></i> ${a.file_name}
                </a>
                <button type="button" onclick="deleteAttachment(${a.id})"
                    class="text-red-400 hover:text-red-600 text-xs px-2 py-1 rounded hover:bg-red-50">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `).join('');
    } else {
        attList.innerHTML = '<p class="text-sm text-gray-400 italic">Belum ada lampiran</p>';
    }

    openModal('modalEditKontrak');
}

/* ─── Open Detail Leasing Modal ─── */
function openDetailLeasingModal(item) {
    document.getElementById('dl_subtitle').textContent   = item.no_kontrak ?? '';
    document.getElementById('dl_no_kontrak').textContent = item.no_kontrak ?? '-';
    document.getElementById('dl_mobil').textContent      = (item.mobil ?? '-') + (item.tahun ? ' (' + item.tahun + ')' : '');
    document.getElementById('dl_nopol').textContent      = item.nopol ?? '-';
    document.getElementById('dl_tahun').textContent      = item.tahun ?? '-';
    document.getElementById('dl_user_leasing').textContent = item.user_leasing ?? '-';

    document.getElementById('dl_angsuran').textContent   = item.angsuran_per_bulan
        ? 'Rp ' + parseInt(item.angsuran_per_bulan).toLocaleString('id-ID') : '-';
    document.getElementById('dl_jatuh_tempo').textContent  = item.jatuh_tempo ? 'Tgl ' + item.jatuh_tempo : '-';
    document.getElementById('dl_periode').textContent      = [item.periode_mulai, item.periode_selesai].filter(Boolean).join(' – ') || '-';
    document.getElementById('dl_total_cicilan').textContent = item.total_cicilan
        ? 'Rp ' + parseInt(item.total_cicilan).toLocaleString('id-ID') : '-';

    const sisaEl = document.getElementById('dl_sisa_cicilan');
    sisaEl.textContent  = item.sisa_cicilan ? 'Rp ' + parseInt(item.sisa_cicilan).toLocaleString('id-ID') : '-';
    sisaEl.className    = 'text-sm font-semibold ' + (parseInt(item.sisa_cicilan) > 0 ? 'text-orange-600' : 'text-emerald-600');

    const statusEl = document.getElementById('dl_status_cicilan');
    statusEl.textContent = item.status_cicilan ?? '-';
    statusEl.className   = 'text-sm font-semibold ' + (
        item.status_cicilan === 'Lunas'       ? 'text-emerald-600' :
        item.status_cicilan === 'Partial'     ? 'text-orange-500'  :
        item.status_cicilan === 'Belum Mulai' ? 'text-blue-600'    : 'text-gray-500'
    );

    document.getElementById('dl_personal_account').textContent = item.personal_account ?? '-';
    document.getElementById('dl_cara_bayar').textContent       = item.cara_bayar ?? '-';
    document.getElementById('dl_sumber_dana').textContent      = item.sumber_dana_debit ?? '-';
    document.getElementById('dl_asuransi_leasing').textContent = item.asuransi_leasing ?? '-';

    openModal('modalDetailLeasing');
}

/* ─── Open Detail Kontrak Modal ─── */
function openDetailKontrakModal(item) {
    document.getElementById('dk_subtitle').textContent = item.serial_number ?? item.no_kontrak ?? '';
    document.getElementById('dk_serial_number').textContent = item.serial_number ?? '-';
    document.getElementById('dk_no_kontrak').textContent    = item.no_kontrak ?? '-';
    document.getElementById('dk_mobil').textContent         = (item.mobil ?? '-') + (item.tahun ? ' (' + item.tahun + ')' : '');
    document.getElementById('dk_nopol').textContent         = item.nopol ?? '-';
    document.getElementById('dk_tahun').textContent         = item.tahun ?? '-';
    document.getElementById('dk_user').textContent          = item.user_kontrak ?? '-';

    document.getElementById('dk_angsuran').textContent      = item.angsuran_per_bulan
        ? 'Rp ' + parseInt(item.angsuran_per_bulan).toLocaleString('id-ID') : '-';
    document.getElementById('dk_jatuh_tempo').textContent   = item.jatuh_tempo ? 'Tgl ' + item.jatuh_tempo : '-';
    document.getElementById('dk_periode').textContent       = [item.periode_mulai, item.periode_selesai].filter(Boolean).join(' – ') || '-';
    document.getElementById('dk_personal_account').textContent = item.personal_account ?? '-';
    document.getElementById('dk_sumber_dana').textContent   = item.sumber_dana_debit ?? '-';
    document.getElementById('dk_cara_bayar').textContent    = item.cara_bayar ?? '-';

    // Asuransi
    document.getElementById('dk_nama_asuransi').textContent    = item.nama_asuransi ?? '-';
    document.getElementById('dk_alamat_asuransi').textContent  = item.alamat_asuransi ?? '-';
    document.getElementById('dk_nama_marketing').textContent   = item.nama_marketing ?? '-';
    document.getElementById('dk_kontak_marketing').textContent = item.kontak_marketing ?? '-';
    document.getElementById('dk_nama_bengkel').textContent     = item.nama_bengkel ?? '-';
    document.getElementById('dk_kontak_bengkel').textContent   = item.kontak_bengkel ?? '-';

    // Dokumen
    const dokDiv = document.getElementById('dk_dokumen');
    let dokHtml  = '';
    if (item.bukti) {
        // Ambil nama file asli dari path (bagian setelah slash terakhir)
        const buktiNama = item.bukti.split('/').pop();
        dokHtml += `<a href="/${item.bukti}" target="_blank"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100">
            <i class="fa fa-file"></i> ${buktiNama}
        </a>`;
    }
    if (item.attachments && item.attachments.length) {
        item.attachments.forEach(a => {
            dokHtml += `<a href="/${a.file_path}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-200">
                <i class="fa fa-paperclip"></i> ${a.file_name}
            </a>`;
        });
    }
    dokDiv.innerHTML = dokHtml || '<span class="text-sm text-gray-400 italic">Tidak ada dokumen</span>';

    openModal('modalDetailKontrak');
}

/* ─── Delete Attachment ─── */
function deleteAttachment(id) {
    if (!confirm('Hapus lampiran ini?')) return;
    fetch(`{{ url('/admin/data-kontrak/attachment') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById('att_row_' + id);
            if (row) row.remove();
        }
    })
    .catch(e => console.error('Error delete attachment:', e));
}
</script>
@endsection
