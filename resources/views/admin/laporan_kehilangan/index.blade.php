@extends('admin.layouts.app')

@section('title', 'Laporan Kehilangan Kendaraan')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Laporan Kehilangan Kendaraan</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola laporan kendaraan yang hilang beserta status penanganannya</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('modalTambahLaporan').classList.remove('hidden')"
                    class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
                <i class="bi bi-journal-plus"></i>
                Tambah Laporan
            </button>
            <button onclick="document.getElementById('modalBuatLaporan').classList.remove('hidden')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                Buat Laporan
            </button>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Sementara</p>
                    <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $countSementara }}</h3>
                    <p class="text-xs text-slate-400 mt-1">Sedang dalam penyelidikan</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                    <i class="bi bi-hourglass-split text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Permanen</p>
                    <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $countPermanen }}</h3>
                    <p class="text-xs text-slate-400 mt-1">Hilang permanen, keluar stok</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                    <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Ditemukan</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-2">{{ $countDitemukan }}</h3>
                    <p class="text-xs text-slate-400 mt-1">Kendaraan kembali ke stok</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="bi bi-check-circle-fill text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART FILTER --}}
    <x-chart-filter 
        id="laporanKehilanganChartFilter" 
        defaultFilter="year" 
        :showCustomRange="true"
    />

    {{-- CHART CONTAINER --}}
    <x-chart-container
        id="laporanKehilanganChartContainer"
        layout="stacked"
        pieTitle="Distribusi Status Kehilangan" 
        pieId="laporanKehilanganPieChart"
        barTitle="Jumlah Laporan per Periode" 
        barId="laporanKehilanganBarChart"
        lineTitle="Trend Kehilangan Sementara" 
        lineId="laporanKehilanganLineChart"
        :showStats="true" 
        :statsData="[]"
    />

    {{-- MAIN CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        {{-- TAB HEADER + SEARCH --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-slate-100">
            {{-- Nav Tabs --}}
            <nav class="flex items-center gap-2">
                @php
                    $tabs = [
                        ['key' => 'sementara', 'label' => 'Sementara',  'count' => $countSementara, 'icon' => 'bi bi-hourglass-split',          'color' => 'text-yellow-600'],
                        ['key' => 'permanen',  'label' => 'Permanen',   'count' => $countPermanen,  'icon' => 'bi bi-exclamation-triangle-fill', 'color' => 'text-red-600'],
                        ['key' => 'ditemukan', 'label' => 'Ditemukan',  'count' => $countDitemukan, 'icon' => 'bi bi-check-circle-fill',         'color' => 'text-green-600'],
                    ];
                @endphp
                @foreach ($tabs as $t)
                    <a href="{{ route('laporan-kehilangan.index', ['tab' => $t['key'], 'search' => $search]) }}"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold whitespace-nowrap rounded-lg transition-all duration-150
                           {{ $tab === $t['key'] ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                        <i class="{{ $t['icon'] }}"></i>
                        {{ $t['label'] }}
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold rounded-full
                            {{ $tab === $t['key'] ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">
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
                        <th class="px-4 py-3 text-center font-semibold w-12">
                            <i class="bi bi-chevron-down text-gray-400"></i>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold w-16">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Kendaraan</th>
                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Tanggal Hilang</th>
                        <th class="px-4 py-3 text-left font-semibold">Keterangan</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Lokasi Kejadian</th>
                        <th class="px-4 py-3 text-left font-semibold">Pelanggan</th>
                        <th class="px-4 py-3 text-left font-semibold">Pengurus</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Informasi GPS</th>
                        <th class="px-4 py-3 text-center font-semibold">Lampiran</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $i => $item)
                        {{-- MAIN ROW --}}
                        <tr class="hover:bg-gray-50/60 transition-colors border-b border-gray-50 cursor-pointer row-main" 
                            data-row-id="{{ $item->id }}"
                            onclick="toggleExpandRow({{ $item->id }})">
                            {{-- Expand Icon --}}
                            <td class="px-4 py-3.5 text-center">
                                <i class="bi bi-chevron-right text-gray-400 transition-transform duration-200" 
                                   id="icon-{{ $item->id }}"></i>
                            </td>
                            
                            {{-- ID --}}
                            <td class="px-4 py-3.5 text-gray-600 font-mono text-xs">
                                #{{ $i + 1 }}
                            </td>
                            
                            {{-- Kendaraan --}}
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-gray-800 whitespace-nowrap">{{ $item->nopol }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $item->merk }}</div>
                            </td>
                            
                            {{-- Status --}}
                            <td class="px-4 py-3.5 text-center" onclick="event.stopPropagation()">
                                @if($item->status === 'sementara')
                                    <button onclick="openQuickStatusModal({{ $item->id }}, '{{ $item->nopol }}')"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->statusBadgeClass() }} whitespace-nowrap hover:opacity-80 transition-opacity">
                                        {{ $item->statusLabel() }}
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->statusBadgeClass() }} whitespace-nowrap">
                                        {{ $item->statusLabel() }}
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Tanggal Hilang --}}
                            <td class="px-4 py-3.5 text-gray-600 whitespace-nowrap">
                                {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                            </td>
                            
                            {{-- Keterangan --}}
                            <td class="px-4 py-3.5 text-gray-600 max-w-[200px]">
                                <span class="line-clamp-2 text-xs">{{ $item->keterangan ?: '-' }}</span>
                            </td>
                            
                            {{-- Lokasi Kejadian --}}
                            <td class="px-4 py-3.5 text-gray-600 max-w-[180px]">
                                <span class="line-clamp-2 text-xs">{{ $item->lokasi_kejadian ?: '-' }}</span>
                            </td>
                            
                            {{-- Pelanggan --}}
                            <td class="px-4 py-3.5 text-gray-600">
                                <div class="text-xs whitespace-nowrap">{{ $item->pelanggan->nama_pelanggan ?? '-' }}</div>
                            </td>
                            
                            {{-- Pengurus --}}
                            <td class="px-4 py-3.5 text-gray-600">
                                <span class="text-xs whitespace-nowrap">{{ $item->pengurus ?: '-' }}</span>
                            </td>
                            
                            {{-- Informasi GPS --}}
                            <td class="px-4 py-3.5 text-gray-600 max-w-[150px]">
                                <span class="font-mono text-xs break-all">{{ $item->informasi_gps ?: '-' }}</span>
                            </td>
                            
                            {{-- Lampiran --}}
                            <td class="px-4 py-3.5 text-center" onclick="event.stopPropagation()">
                                @if($item->file)
                                    @php 
                                        $ext = pathinfo($item->file, PATHINFO_EXTENSION);
                                        $filename = basename($item->file);
                                        // Extract original filename (remove timestamp prefix if exists)
                                        // Format: timestamp_uniqid_originalname.ext
                                        $displayName = preg_replace('/^\d+_[a-f0-9]+_/', '', $filename) ?: $filename;
                                    @endphp
                                    <a href="{{ asset($item->file) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors font-medium max-w-[180px]"
                                       title="{{ $displayName }}">
                                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif']))
                                            <i class="bi bi-image"></i>
                                        @elseif(strtolower($ext) === 'pdf')
                                            <i class="bi bi-file-pdf"></i>
                                        @else
                                            <i class="bi bi-file-earmark-text"></i>
                                        @endif
                                        <span class="truncate">{{ $displayName }}</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            
                            {{-- Aksi --}}
                            <td class="px-4 py-3.5 text-center" onclick="event.stopPropagation()">
                                <a href="{{ route('laporan-kehilangan.show', $item->id) }}"
                                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors font-medium whitespace-nowrap">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>

                        {{-- EXPANDABLE ROW --}}
                        <tr id="expand-row-{{ $item->id }}" class="hidden border-b border-gray-100 bg-gray-50/30">
                            <td colspan="12" class="px-0 py-0">
                                <div class="px-6 py-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                                            <i class="bi bi-clock-history text-indigo-600 text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-800">
                                            Riwayat Laporan Perkembangan
                                            <span class="text-xs bg-indigo-100 text-indigo-700 font-medium px-2 py-0.5 rounded-full ml-2">
                                                {{ $item->laporans->count() }} entri
                                            </span>
                                        </h4>
                                    </div>
                                    
                                    @if($item->laporans->count() > 0)
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm border border-gray-200 rounded-lg">
                                                <thead>
                                                    <tr class="bg-white border-b border-gray-200">
                                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 w-12">#</th>
                                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Laporan</th>
                                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Tanggal Laporan</th>
                                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Keterangan</th>
                                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Lokasi Pencarian</th>
                                                        <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600">File</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($item->laporans as $idx => $laporan)
                                                        <tr class="bg-white border-b border-gray-100 hover:bg-gray-50">
                                                            <td class="px-3 py-2.5 text-gray-500 font-mono text-xs">{{ $idx + 1 }}</td>
                                                            <td class="px-3 py-2.5 text-gray-800 text-xs">
                                                                {{ $laporan->laporan }}
                                                            </td>
                                                            <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">
                                                                {{ $laporan->tanggal_laporan ? $laporan->tanggal_laporan->format('d M Y') : '-' }}
                                                            </td>
                                                            <td class="px-3 py-2.5 text-gray-600 text-xs">
                                                                {{ $laporan->keterangan ?: '-' }}
                                                            </td>
                                                            <td class="px-3 py-2.5 text-gray-600 text-xs">
                                                                @if($laporan->lokasi_pencarian)
                                                                    <span class="inline-flex items-center gap-1">
                                                                        <i class="bi bi-geo-alt text-gray-400"></i>
                                                                        {{ $laporan->lokasi_pencarian }}
                                                                    </span>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td class="px-3 py-2.5 text-center">
                                                                @if($laporan->file)
                                                                    @php 
                                                                        $ext2 = pathinfo($laporan->file, PATHINFO_EXTENSION);
                                                                        $filename2 = basename($laporan->file);
                                                                        $displayName2 = preg_replace('/^\d+_[a-f0-9]+_/', '', $filename2) ?: $filename2;
                                                                    @endphp
                                                                    <a href="{{ asset($laporan->file) }}" target="_blank"
                                                                       class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline"
                                                                       title="{{ $displayName2 }}">
                                                                        @if(in_array(strtolower($ext2), ['jpg','jpeg','png','gif']))
                                                                            <i class="bi bi-image"></i>
                                                                        @elseif(strtolower($ext2) === 'pdf')
                                                                            <i class="bi bi-file-pdf"></i>
                                                                        @else
                                                                            <i class="bi bi-file-earmark-text"></i>
                                                                        @endif
                                                                        <span class="truncate max-w-[120px]">{{ $displayName2 }}</span>
                                                                    </a>
                                                                @else
                                                                    <span class="text-xs text-gray-400">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-6 bg-white border border-gray-200 rounded-lg">
                                            <i class="bi bi-inbox text-3xl text-gray-300"></i>
                                            <p class="text-xs text-gray-400 mt-2 italic">Belum ada laporan perkembangan</p>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="12" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <i class="bi bi-inbox text-5xl"></i>
                                    <p class="text-sm font-medium">Tidak ada laporan kehilangan dengan status
                                        <span class="font-bold text-gray-600">{{ ucfirst($tab) }}</span>
                                    </p>
                                    @if($tab === 'sementara')
                                        <button onclick="document.getElementById('modalBuatLaporan').classList.remove('hidden')"
                                                class="text-blue-600 hover:underline text-xs">
                                            + Buat laporan baru
                                        </button>
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
    @if(session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[99999999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

            <div id="alertBox"
                class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">

                @if(session('success'))
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @elseif(session('error'))
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button onclick="closeAlert()"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0"
                    aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>

            </div>
        </div>

        <script>
        window.closeAlert = function() {
            const overlay = document.getElementById('alertOverlay');
            if (overlay) overlay.style.display = 'none';
        };

        (function() {
            const overlay = document.getElementById('alertOverlay');
            if (!overlay) return;
            overlay.style.pointerEvents = 'auto';
            overlay.style.opacity = '1';
            const box = document.getElementById('alertBox');
            if (box) box.style.transform = 'translateY(0)';
            setTimeout(closeAlert, 4000);
        })();
        </script>
    @endif

</div>

{{-- ══════════════════════════════════════════
     MODAL BUAT LAPORAN KEHILANGAN
══════════════════════════════════════════ --}}
<div id="modalBuatLaporan" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-start justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-8">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-red-500"></i> Buat Laporan Kehilangan
            </h3>
            <button onclick="tutupModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- Error --}}
        @if ($errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 rounded-xl p-3">
                <p class="text-xs font-semibold text-red-700 mb-1"><i class="bi bi-exclamation-triangle-fill mr-1"></i> Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-xs text-red-600 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('laporan-kehilangan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="px-6 py-5 space-y-4">

                {{-- Kendaraan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select name="kendaraan_id" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-300 @error('kendaraan_id') border-red-400 @enderror">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach ($kendaraans as $k)
                            <option value="{{ $k->id }}" {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nopol }} — {{ $k->merk }} ({{ ucfirst($k->status_kendaraan) }})
                            </option>
                        @endforeach
                    </select>
                    @error('kendaraan_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status + Tanggal (2 kolom) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Status Kehilangan <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-300">
                            <option value="sementara" {{ old('status', 'sementara') == 'sementara' ? 'selected' : '' }}>
                                Sementara
                            </option>
                            <option value="permanen" {{ old('status') == 'permanen' ? 'selected' : '' }}>
                                Permanen
                            </option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="font-medium">Sementara:</span> kendaraan sedang dicari. 
                            <span class="font-medium">Permanen:</span> kendaraan hilang tetap &amp; keluar dari stok.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Tanggal Hilang <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                </div>

                {{-- Pelanggan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Pelanggan Terkait <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                    </label>
                    <select name="pelanggan_id"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="">-- Tidak ada --</option>
                        @foreach ($pelanggans as $p)
                            <option value="{{ $p->id }}" {{ old('pelanggan_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_pelanggan }}{{ $p->kontak_pelanggan ? ' (' . $p->kontak_pelanggan . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi Kejadian</label>
                    <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}"
                           placeholder="Contoh: Jl. Sudirman No. 10, Jakarta Pusat"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Keterangan / Kronologi</label>
                    <textarea name="keterangan" rows="3"
                              placeholder="Jelaskan kronologi kejadian kehilangan..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 resize-none">{{ old('keterangan') }}</textarea>
                </div>

                {{-- Pengurus + GPS (2 kolom) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Pengurus <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                        </label>
                        <input type="text" name="pengurus" value="{{ old('pengurus') }}"
                               placeholder="Nama pihak yang menangani"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Informasi GPS <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                        </label>
                        <input type="text" name="informasi_gps" value="{{ old('informasi_gps') }}"
                               placeholder="Koordinat terakhir GPS"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 font-mono text-xs">
                    </div>
                </div>

                {{-- File --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Lampiran <span class="text-gray-400 font-normal text-xs">(opsional — foto/dokumen maks 10MB)</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-blue-300 transition-colors">
                        <input type="file" name="file" id="modalFileInput"
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                               class="hidden"
                               onchange="previewModalFile(this)">
                        <label for="modalFileInput" class="flex flex-col items-center gap-2 cursor-pointer text-gray-400">
                            <i class="bi bi-cloud-arrow-up text-3xl"></i>
                            <span class="text-sm">Klik untuk pilih file</span>
                            <span class="text-xs">JPG, PNG, PDF, DOC, DOCX</span>
                        </label>
                        <div id="modalFilePreview" class="hidden mt-3 text-center"></div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="tutupModal()"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <i class="bi bi-save"></i> Simpan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function tutupModal() {
    document.getElementById('modalBuatLaporan').classList.add('hidden');
}

// Tutup modal klik di luar
document.getElementById('modalBuatLaporan').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});

// Buka otomatis jika ada error validasi (form gagal submit)
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('modalBuatLaporan').classList.remove('hidden');
    });
@endif

function previewModalFile(input) {
    const preview = document.getElementById('modalFilePreview');
    const file = input.files[0];
    if (!file) { preview.classList.add('hidden'); return; }
    preview.classList.remove('hidden');
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" class="max-h-32 mx-auto rounded-lg shadow-sm object-cover">
                <p class="text-xs text-gray-500 mt-1">${file.name}</p>`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = `<div class="inline-flex items-center gap-2 bg-gray-100 rounded-lg px-3 py-2">
            <i class="bi bi-file-earmark-text text-blue-500"></i>
            <span class="text-sm text-gray-700">${file.name}</span>
        </div>`;
    }
}
</script>

{{-- ══════════════════════════════════════════
     MODAL TAMBAH LAPORAN (progress update per kasus)
══════════════════════════════════════════ --}}
<div id="modalTambahLaporan" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-start justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-8">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="bi bi-journal-plus text-indigo-500"></i> Tambah Laporan Perkembangan
            </h3>
            <button onclick="document.getElementById('modalTambahLaporan').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <form action="{{ route('laporan-kehilangan.tambah-laporan-index') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-5 space-y-4">

                {{-- Pilih Kendaraan/Kasus --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Kasus Kehilangan <span class="text-red-500">*</span>
                    </label>
                    <select name="kehilangan_id" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">-- Pilih kasus --</option>
                        @foreach(\App\Models\Kehilangan::with('kendaraan')->where('status', 'sementara')->orderBy('created_at','desc')->get() as $kasus)
                            <option value="{{ $kasus->id }}">
                                {{ $kasus->nopol }} — {{ $kasus->merk }}
                                ({{ $kasus->tanggal?->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Hanya menampilkan kasus dengan status <span class="font-semibold">Sementara</span> (sedang dalam pencarian)
                    </p>
                </div>

                {{-- Isi Laporan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Isi Laporan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="laporan" rows="3" required
                              placeholder="Tuliskan perkembangan pencarian atau informasi terbaru..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
                </div>

                {{-- Tanggal + Lokasi --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_laporan" value="{{ date('Y-m-d') }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Lokasi Pencarian <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                        </label>
                        <input type="text" name="lokasi_pencarian"
                               placeholder="Area pencarian"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Keterangan <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                    </label>
                    <textarea name="keterangan" rows="2"
                              placeholder="Detail tambahan..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
                </div>

                {{-- File --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Lampiran <span class="text-gray-400 font-normal text-xs">(opsional — foto/dokumen maks 10MB)</span>
                    </label>
                    <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                           class="w-full text-sm text-gray-600 border border-gray-200 rounded-xl px-4 py-2.5
                                  file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0
                                  file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100">
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                <button type="button"
                        onclick="document.getElementById('modalTambahLaporan').classList.add('hidden')"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <i class="bi bi-send"></i> Simpan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('modalTambahLaporan').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});

// ========================================
// QUICK STATUS CHANGE MODAL
// ========================================
let currentKehilanganId = null;

function openQuickStatusModal(kehilanganId, nopol) {
    currentKehilanganId = kehilanganId;
    document.getElementById('quickStatusNopol').textContent = nopol;
    
    // Set form action dinamis
    const form = document.getElementById('quickStatusForm');
    form.action = `/admin/laporan-kehilangan/${kehilanganId}/status`;
    
    document.getElementById('modalQuickStatus').classList.remove('hidden');
    // Reset radio selection
    document.querySelectorAll('input[name="quick_status_baru"]').forEach(el => el.checked = false);
    document.getElementById('quickStatusInput').value = '';
}

function closeQuickStatusModal() {
    document.getElementById('modalQuickStatus').classList.add('hidden');
    currentKehilanganId = null;
}

function submitQuickStatusChange() {
    const selectedStatus = document.querySelector('input[name="quick_status_baru"]:checked');
    if (!selectedStatus) {
        alert('Silakan pilih status terlebih dahulu');
        return;
    }
    
    if (!currentKehilanganId) {
        alert('Terjadi kesalahan: ID kehilangan tidak ditemukan');
        return;
    }

    const confirmMsg = selectedStatus.value === 'permanen' 
        ? 'Ubah status ke PERMANEN? Kendaraan akan dihapus dari stok.'
        : 'Ubah status ke DITEMUKAN? Kendaraan akan dikembalikan ke stok.';
    
    if (!confirm(confirmMsg)) return;

    // Submit form
    document.getElementById('quickStatusForm').submit();
}

// Close modal on outside click
document.getElementById('modalQuickStatus')?.addEventListener('click', function(e) {
    if (e.target === this) closeQuickStatusModal();
});
</script>

{{-- ══════════════════════════════════════════
     MODAL QUICK STATUS CHANGE (dari tabel index)
══════════════════════════════════════════ --}}
<div id="modalQuickStatus" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-arrow-repeat text-indigo-500"></i> Ubah Status
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kendaraan: <span id="quickStatusNopol" class="font-semibold text-slate-700"></span></p>
            </div>
            <button onclick="closeQuickStatusModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="quickStatusForm" method="POST" action="">
            @csrf
            <input type="hidden" name="from_index" value="1">
            <input type="hidden" name="status_baru" id="quickStatusInput">
            
            <div class="px-6 py-5">
                <label class="block text-sm font-semibold text-slate-700 mb-3">
                    Pilih Status Baru <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    {{-- Permanen --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="quick_status_baru" value="permanen" class="peer sr-only" 
                               onchange="document.getElementById('quickStatusInput').value = 'permanen'">
                        <div class="border-2 border-slate-200 peer-checked:border-red-400 peer-checked:bg-red-50 rounded-xl p-4 text-center transition-all hover:border-slate-300">
                            <i class="bi bi-exclamation-triangle-fill text-3xl text-red-500 block mb-2"></i>
                            <p class="text-sm font-bold text-slate-800">Permanen</p>
                            <p class="text-xs text-slate-500 mt-1">Keluar dari stok</p>
                        </div>
                    </label>

                    {{-- Ditemukan --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="quick_status_baru" value="ditemukan" class="peer sr-only"
                               onchange="document.getElementById('quickStatusInput').value = 'ditemukan'">
                        <div class="border-2 border-slate-200 peer-checked:border-green-400 peer-checked:bg-green-50 rounded-xl p-4 text-center transition-all hover:border-slate-300">
                            <i class="bi bi-check-circle-fill text-3xl text-green-500 block mb-2"></i>
                            <p class="text-sm font-bold text-slate-800">Ditemukan</p>
                            <p class="text-xs text-slate-500 mt-1">Kembali tersedia</p>
                        </div>
                    </label>
                </div>
                <p class="text-xs text-slate-500 mt-3">
                    <i class="bi bi-info-circle mr-1"></i>
                    Detail tambahan dapat ditambahkan di halaman detail setelah perubahan status.
                </p>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeQuickStatusModal()"
                        class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="button" onclick="submitQuickStatusChange()"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <i class="bi bi-check-lg"></i> Ubah Status
                </button>
            </div>
        </form>
    </div>
</div>

{{-- CHART INITIALIZATION --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/chart-manager.js') }}"></script>
<script>
// ========================================
// CHART INITIALIZATION — Laporan Kehilangan
// ========================================
const chartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function() {
    // Init charts dengan filter default (akan di-trigger oleh chart-filter component)
    // Tidak perlu manual init karena component akan auto-dispatch default filter
});

// Listen to chart filter changes dari component
document.addEventListener('chartFilterChange', async function(e) {
    const { filterId, filterType, startDate, endDate, specificYear, categoryId } = e.detail;
    
    // Only handle events for this page's filter
    if (filterId !== 'laporanKehilanganChartFilter') return;

    // Build filter object (only include non-null values)
    const filters = {
        filter_type: filterType
    };
    
    // Only add optional parameters if they have values
    if (startDate) filters.start_date = startDate;
    if (endDate) filters.end_date = endDate;
    if (specificYear) filters.specific_year = specificYear;
    if (categoryId) filters.category_id = categoryId;

    try {
        await chartManager.updateChartsFromAPI('laporan-kehilangan', {
            pie:  'laporanKehilanganPieChart',
            bar:  'laporanKehilanganBarChart',
            line: 'laporanKehilanganLineChart'
        }, filters, { isCurrency: false }, { isCurrency: false });
    } catch (error) {
        console.error('Error updating charts:', error);
    }
});

// ========================================
// EXPANDABLE ROW FUNCTIONALITY
// ========================================
function toggleExpandRow(rowId) {
    const expandRow = document.getElementById('expand-row-' + rowId);
    const icon = document.getElementById('icon-' + rowId);
    
    if (!expandRow || !icon) return;
    
    if (expandRow.classList.contains('hidden')) {
        expandRow.classList.remove('hidden');
        icon.classList.add('rotate-90');
    } else {
        expandRow.classList.add('hidden');
        icon.classList.remove('rotate-90');
    }
}
</script>

@endsection
