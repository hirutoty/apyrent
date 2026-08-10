@extends('admin.layouts.app')
@section('title', 'Data Leasing & Kontrak')
@section('content')
<div class="space-y-6 p-5">

    {{-- ALERTS --}}
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
                                <th class="px-4 py-3 text-right">Jml Cicilan</th>
                                <th class="px-4 py-3 text-right">Cicilan Tersisa</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-left">Cara Bayar</th>
                                <th class="px-4 py-3 text-left">Sumber Dana</th>
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
                                <td class="px-4 py-3 text-right font-medium">{{ $item->jumlah_cicilan }}x</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $item->cicilan_tersisa > 0 ? 'text-orange-600' : 'text-emerald-600' }}">
                                    {{ $item->cicilan_tersisa }}x
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusClass }}">
                                        {{ $item->status_cicilan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">{{ $item->cara_bayar ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $item->sumber_dana_debit ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditLeasingModal({{ json_encode($item) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                                            <i class="fa fa-pencil"></i> Edit
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
                    {{ $leasings->links() }}
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
                <button onclick="openModal('modalCreateKontrak')"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    <i class="fa fa-plus text-sm"></i> Tambah Data Kontrak
                </button>
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
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">No Kontrak</th>
                                <th class="px-4 py-3 text-left">Kendaraan</th>
                                <th class="px-4 py-3 text-left">Nopol</th>
                                <th class="px-4 py-3 text-left">User</th>
                                <th class="px-4 py-3 text-right">Angsuran/Bln</th>
                                <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-left">Periode</th>
                                <th class="px-4 py-3 text-right">Jml Cicilan</th>
                                <th class="px-4 py-3 text-right">Cicilan Tersisa</th>
                                <th class="px-4 py-3 text-center">Status</th>
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
                                <td class="px-4 py-3 font-medium text-indigo-700">{{ $item->no_kontrak }}</td>
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
                                <td class="px-4 py-3 text-right font-medium">{{ $item->jumlah_cicilan }}x</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $item->cicilan_tersisa > 0 ? 'text-orange-600' : 'text-emerald-600' }}">
                                    {{ $item->cicilan_tersisa }}x
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusClass }}">
                                        {{ $item->status_cicilan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">{{ $item->nama_asuransi ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($item->bukti)
                                    <a href="{{ asset($item->bukti) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs text-blue-600 hover:text-blue-800">
                                        <i class="fa fa-file"></i> Lihat
                                    </a>
                                    @else
                                    <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($item->attachments->count() > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs text-gray-600 bg-gray-100 rounded-lg">
                                        <i class="fa fa-paperclip"></i> {{ $item->attachments->count() }}
                                    </span>
                                    @else
                                    <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditKontrakModal({{ json_encode([
                                            'id'                 => $item->id,
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
                                            onsubmit="return confirm('Yakin hapus kontrak {{ $item->no_kontrak }}? Data leasing terkait akan kehilangan referensinya.')">
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
                                <td colspan="15" class="px-4 py-12 text-center text-gray-400">
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
                    {{ $kontraks->appends(['tab' => 'kontrak'])->links() }}
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
                    <option value="{{ $dk->id }}">{{ $dk->no_kontrak }} – {{ $dk->mobil ?? '-' }} ({{ $dk->nopol ?? '-' }})</option>
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
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode Selesai</label>
                    <input type="date" name="periode_selesai" id="cl_periode_selesai"
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
                    <input type="text" name="sumber_dana_debit"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Nama bank / sumber dana">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cara Bayar</label>
                    <input type="text" name="cara_bayar"
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontrak</label>
                <input type="text" id="ck_no_kontrak_display" disabled
                    class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-indigo-700 font-semibold outline-none cursor-not-allowed"
                    placeholder="Generating...">
                <input type="hidden" name="no_kontrak" id="ck_no_kontrak">
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
            <div class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-4 space-y-3">
                <p class="text-sm font-semibold text-indigo-700 flex items-center gap-1.5">
                    <i class="fa fa-shield"></i> Data Asuransi <span class="text-xs font-normal text-gray-400">(opsional)</span>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Asuransi</label>
                        <input type="text" name="nama_asuransi"
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
                        <input type="text" name="kontak_marketing"
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
                        <input type="text" name="kontak_bengkel"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white"
                            placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
            </div>

            {{-- Bukti (single file) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Bukti <span class="text-gray-400 text-xs">(opsional, maks 5MB)</span>
                </label>
                <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf"
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontrak</label>
                <input type="text" id="ek_no_kontrak_display" disabled
                    class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none cursor-not-allowed">
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
                        <input type="text" name="kontak_marketing" id="ek_kontak_marketing"
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
                        <input type="text" name="kontak_bengkel" id="ek_kontak_bengkel"
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

/* ─── AJAX: Auto-Generate No Kontrak (dipanggil saat modal dibuka) ─── */
function autoGenerateNoKontrak() {
    const displayEl = document.getElementById('ck_no_kontrak_display');
    const hiddenEl  = document.getElementById('ck_no_kontrak');
    if (displayEl) displayEl.value = 'Generating...';
    fetch(`{{ route('data-kontrak.generate-no') }}`)
        .then(r => r.json())
        .then(data => {
            if (displayEl) displayEl.value = data.no_kontrak;
            if (hiddenEl)  hiddenEl.value  = data.no_kontrak;
        })
        .catch(e => {
            console.error('Error generate no kontrak:', e);
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

    document.getElementById('ek_no_kontrak_display').value = item.no_kontrak ?? '';
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
