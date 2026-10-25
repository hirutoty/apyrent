@extends('admin.layouts.app')

@section('title', 'Laporan Kehilangan Kendaraan')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Kehilangan Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola laporan kendaraan yang hilang beserta status penanganannya</p>
        </div>
        <div>
            <a href="{{ route('laporan-kehilangan.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="bi bi-plus-lg"></i> Buat Laporan
            </a>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Sementara</p>
                    <h2 class="text-3xl font-bold text-yellow-600 mt-1">{{ $countSementara }}</h2>
                    <p class="text-xs text-gray-400 mt-1">Sedang dalam penyelidikan</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-500 flex items-center justify-center">
                    <i class="bi bi-hourglass-split text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Permanen</p>
                    <h2 class="text-3xl font-bold text-red-600 mt-1">{{ $countPermanen }}</h2>
                    <p class="text-xs text-gray-400 mt-1">Hilang permanen, keluar stok</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                    <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Ditemukan</p>
                    <h2 class="text-3xl font-bold text-green-600 mt-1">{{ $countDitemukan }}</h2>
                    <p class="text-xs text-gray-400 mt-1">Kendaraan kembali ke stok</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center">
                    <i class="bi bi-check-circle-fill text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- TAB HEADER + SEARCH --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            {{-- Nav Tabs --}}
            <nav class="inline-flex gap-1 bg-gray-100 rounded-xl p-1">
                @php
                    $tabs = [
                        ['key' => 'sementara', 'label' => 'Sementara',  'count' => $countSementara, 'icon' => 'bi bi-hourglass-split',          'color' => 'text-yellow-600'],
                        ['key' => 'permanen',  'label' => 'Permanen',   'count' => $countPermanen,  'icon' => 'bi bi-exclamation-triangle-fill', 'color' => 'text-red-600'],
                        ['key' => 'ditemukan', 'label' => 'Ditemukan',  'count' => $countDitemukan, 'icon' => 'bi bi-check-circle-fill',         'color' => 'text-green-600'],
                    ];
                @endphp
                @foreach ($tabs as $t)
                    <a href="{{ route('laporan-kehilangan.index', ['tab' => $t['key'], 'search' => $search]) }}"
                       class="flex items-center gap-2 px-4 py-2 text-sm font-semibold whitespace-nowrap rounded-lg transition-all duration-150
                           {{ $tab === $t['key'] ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-white/60' }}">
                        <i class="{{ $t['icon'] }} {{ $tab === $t['key'] ? $t['color'] : '' }}"></i>
                        {{ $t['label'] }}
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold rounded-full
                            {{ $tab === $t['key'] ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-600' }}">
                            {{ $t['count'] }}
                        </span>
                    </a>
                @endforeach
            </nav>

            {{-- Search --}}
            <form method="GET" action="{{ route('laporan-kehilangan.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nopol / merk..."
                           class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 w-52">
                </div>
                <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm rounded-lg transition-colors">
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('laporan-kehilangan.index', ['tab' => $tab]) }}"
                       class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm rounded-lg transition-colors">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide border-b border-gray-100">
                        <th class="px-5 py-3 text-left font-semibold">No</th>
                        <th class="px-5 py-3 text-left font-semibold">Kendaraan</th>
                        <th class="px-5 py-3 text-left font-semibold">Tanggal Hilang</th>
                        <th class="px-5 py-3 text-left font-semibold">Lokasi Kejadian</th>
                        <th class="px-5 py-3 text-left font-semibold">Pelanggan</th>
                        <th class="px-5 py-3 text-left font-semibold">Pengurus</th>
                        <th class="px-5 py-3 text-left font-semibold">Status</th>
                        <th class="px-5 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($data as $i => $item)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-3.5 text-gray-400 font-mono text-xs">
                                {{ ($data->currentPage() - 1) * $data->perPage() + $i + 1 }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-gray-800">{{ $item->kendaraan->nopol ?? '-' }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $item->kendaraan->merk ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 max-w-[180px]">
                                <span class="line-clamp-2 text-xs">{{ $item->lokasi_kejadian ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">
                                {{ $item->pelanggan->nama_pelanggan ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">
                                {{ $item->pengurus ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->statusBadgeClass() }}">
                                    {{ $item->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('laporan-kehilangan.show', $item->id) }}"
                                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors font-medium">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <i class="bi bi-inbox text-5xl"></i>
                                    <p class="text-sm font-medium">Tidak ada laporan kehilangan dengan status
                                        <span class="font-bold text-gray-600">{{ ucfirst($tab) }}</span>
                                    </p>
                                    @if($tab === 'sementara')
                                        <a href="{{ route('laporan-kehilangan.create') }}"
                                           class="text-blue-600 hover:underline text-xs">+ Buat laporan baru</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($data->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $data->links('vendor.pagination.custom-purple') }}
            </div>
        @endif

    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed bottom-6 right-6 z-50 bg-green-600 text-white text-sm font-medium px-5 py-3 rounded-xl shadow-lg flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

</div>
@endsection
