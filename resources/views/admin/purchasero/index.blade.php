@extends('admin.layouts.app')

@section('title', 'Pengadaan')

@section('content')

<div class="space-y-6 p-5">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengadaan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola pengajuan permintaan pembelian barang &amp; jasa</p>
        </div>
        @if ($role !== 'superadmin')
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="fa fa-plus"></i> Tambah Pengadaan
        </button>
        @endif
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @if ($role !== 'superadmin')
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total Pengadaan</p>
            <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $totalPR }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Pending</p>
            <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $totalPending }}</h2>
        </div>
        @endif
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
        <div class="bg-white rounded-2xl border border-gray-100 p-5 col-span-2 md:col-span-4">
            <p class="text-sm text-gray-500">Total Nominal (Diajukan + Disetujui)</p>
            <h2 class="text-2xl font-bold text-emerald-600 mt-2">
                Rp {{ number_format($totalNominal, 0, ',', '.') }}
            </h2>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

        {{-- NAV TABS --}}
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 -mb-px overflow-x-auto">

                @if ($role === 'superadmin')
    {{-- Superadmin: lihat hasil keputusan (Diajukan, Disetujui & Ditolak) --}}
    @foreach ([
        ['key' => 'Diajukan',  'label' => 'Diajukan',  'icon' => 'bi bi-paper-plane',      'count' => $totalDiajukan, 'color' => 'indigo'],
        ['key' => 'Disetujui', 'label' => 'Disetujui', 'icon' => 'bi bi-check-circle-fill', 'count' => $totalDisetujui, 'color' => 'green'],
        ['key' => 'Ditolak',   'label' => 'Ditolak',   'icon' => 'bi bi-x-circle-fill',   'count' => $totalDitolak,   'color' => 'red'],
    ] as $t)
        @php $isActive = $tab === $t['key']; @endphp
        <a href="{{ route('purchasero.index', ['tab' => $t['key'], 'sort' => $sort]) }}"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                {{ $isActive ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
            <i class="{{ $t['icon'] }}"></i>
            {{ $t['label'] }}
            @php
                $badgeCls = match($t['color']) {
                    'green'  => 'bg-green-100 text-green-700',
                    'red'    => 'bg-red-100 text-red-700',
                    'indigo' => 'bg-indigo-100 text-indigo-700',
                    default  => 'bg-gray-100 text-gray-700',
                };
            @endphp
            <span class="ml-1 text-xs font-bold px-2 py-0.5 rounded-full {{ $badgeCls }}">{{ $t['count'] }}</span>
        </a>
    @endforeach

                @else
                    {{-- Non-superadmin: Semua / Pending / Diajukan / Disetujui / Ditolak --}}
                    @php
                        $navTabs = [
                            ['key' => 'semua',    'label' => 'Semua',    'icon' => 'bi bi-list-ul',           'count' => $totalPR,       'badge' => 'bg-blue-100 text-blue-700'],
                            ['key' => 'Pending',  'label' => 'Pending',  'icon' => 'bi bi-hourglass-split',   'count' => $totalPending,  'badge' => 'bg-yellow-100 text-yellow-700'],
                            ['key' => 'Diajukan', 'label' => 'Diajukan', 'icon' => 'bi bi-paper-plane',       'count' => $totalDiajukan, 'badge' => 'bg-indigo-100 text-indigo-700'],
                            ['key' => 'Disetujui','label' => 'Disetujui','icon' => 'bi bi-check-circle-fill', 'count' => $totalDisetujui,'badge' => 'bg-green-100 text-green-700'],
                            ['key' => 'Ditolak',  'label' => 'Ditolak',  'icon' => 'bi bi-x-circle-fill',    'count' => $totalDitolak,  'badge' => 'bg-red-100 text-red-700'],
                        ];
                    @endphp
                    @foreach ($navTabs as $t)
                        @php $isActive = $tab === $t['key']; @endphp
                        <a href="{{ route('purchasero.index', ['tab' => $t['key'], 'sort' => $sort]) }}"
                            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                                {{ $isActive ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                            <i class="{{ $t['icon'] }}"></i>
                            {{ $t['label'] }}
                            <span class="ml-1 text-xs font-bold px-2 py-0.5 rounded-full {{ $t['badge'] }}">{{ $t['count'] }}</span>
                        </a>
                    @endforeach

                @endif

            </nav>
        </div>

        {{-- TOOLBAR --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <div class="flex-1 text-xs text-gray-500">
                Menampilkan <span class="font-semibold text-gray-700">{{ $data->total() }}</span> data
                @if ($role !== 'superadmin' && $tab !== 'semua')
                    — tab: <span class="font-semibold text-gray-700">{{ $tab }}</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 whitespace-nowrap">Urutkan:</span>
                <a href="{{ route('purchasero.index', ['tab' => $tab, 'sort' => 'terbaru']) }}"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                        {{ $sort === 'terbaru' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    <i class="bi bi-sort-down"></i> Terbaru
                </a>
                <a href="{{ route('purchasero.index', ['tab' => $tab, 'sort' => 'terlama']) }}"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                        {{ $sort === 'terlama' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    <i class="bi bi-sort-up"></i> Terlama
                </a>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No PR</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Departemen</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Pemohon</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Barang/Jasa</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kode</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Qty</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Satuan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Alasan</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nominal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Disetujui Oleh</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl Persetujuan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Catatan Tolak</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Terakhir Diajukan</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $d)
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3.5 text-xs text-gray-400">{{ $data->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->no_pr }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-500 whitespace-nowrap">
                                {{ $d->tanggal ? \Carbon\Carbon::parse($d->tanggal)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-800">
                                    <i class="fa fa-building text-blue-400 text-xs"></i>{{ $d->departemen ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->pemohon ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-700 max-w-[130px] truncate">{{ $d->barang_jasa ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->kode_barang ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->qty ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->satuan ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[150px] truncate">{{ $d->alasan_permintaan ?? '-' }}</td>

                            {{-- Nominal --}}
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                @if($d->nominal)
                                    <span class="text-sm font-semibold text-emerald-700">
                                        Rp {{ number_format($d->nominal, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">-</span>
                                @endif
                            </td>

                            {{-- Badge Status --}}
                            <td class="px-4 py-3.5">
                                @php
                                    $bc = match($d->status) {
                                        'Disetujui' => 'bg-green-100 text-green-600',
                                        'Ditolak'   => 'bg-red-100 text-red-600',
                                        'Diajukan'  => 'bg-indigo-100 text-indigo-600',
                                        'Pending'   => 'bg-yellow-100 text-yellow-600',
                                        default     => 'bg-gray-100 text-gray-500',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $bc }}">
                                    <i class="fa fa-circle text-[6px]"></i> {{ $d->status ?? '-' }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->disetujui_oleh ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500 whitespace-nowrap">
                                {{ $d->tanggal_persetujuan ? \Carbon\Carbon::parse($d->tanggal_persetujuan)->format('d M Y') : '-' }}
                            </td>

                            {{-- Catatan tolak --}}
                            <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[160px]">
                                @if($d->catatan)
                                    <span class="inline-flex items-start gap-1 text-red-600 text-xs">
                                        <i class="fa fa-comment-dots mt-0.5 flex-shrink-0"></i>
                                        <span class="line-clamp-2">{{ $d->catatan }}</span>
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">-</span>
                                @endif
                            </td>

                            {{-- Terakhir Diajukan --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($d->terakhir_diajukan)
                                    <span class="inline-flex items-center gap-1 text-indigo-600 text-xs">
                                        <i class="fa fa-clock text-[10px]"></i>
                                        {{ \Carbon\Carbon::parse($d->terakhir_diajukan)->format('d M Y H:i') }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">-</span>
                                @endif
                            </td>

                            {{-- ACTION --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">

                                    @if ($role === 'superadmin')
                                        {{-- Superadmin hanya bisa aksi pada data Diajukan --}}
                                        @if($d->status === 'Diajukan')
                                            {{-- Setujui --}}
                                            <form action="{{ route('purchasero.status', $d->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="Disetujui">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200 transition-colors"
                                                    onclick="return confirm('Setujui pengadaan {{ $d->no_pr }}?')">
                                                    <i class="fa fa-check"></i> Setujui
                                                </button>
                                            </form>
                                            {{-- Tolak: buka modal catatan --}}
                                            <button type="button"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                                onclick="openTolakModal({{ $d->id }}, '{{ $d->no_pr }}')">
                                                <i class="fa fa-times"></i> Tolak
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-300 italic">—</span>
                                        @endif

                                    @else
                                        {{-- Non-superadmin: Edit + Hapus + Ajukan --}}
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                            data-action="{{ route('purchasero.update', $d->id) }}"
                                            data-no_pr="{{ $d->no_pr }}"
                                            data-tanggal="{{ $d->tanggal }}"
                                            data-pemohon="{{ $d->pemohon }}"
                                            data-barang_jasa="{{ $d->barang_jasa }}"
                                            data-kode_barang="{{ $d->kode_barang }}"
                                            data-qty="{{ $d->qty }}"
                                            data-satuan="{{ $d->satuan }}"
                                            data-alasan_permintaan="{{ $d->alasan_permintaan }}"
                                            data-nominal="{{ $d->nominal }}"
                                            onclick="triggerEdit(this)">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>

                                        <button type="button"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                            data-action="{{ route('purchasero.destroy', $d->id) }}"
                                            data-name="{{ $d->no_pr }}"
                                            onclick="triggerDelete(this)">
                                            <i class="fa fa-trash"></i> Hapus
                                        </button>

                                        @if(in_array($d->status, ['Pending', 'Ditolak']))
                                            <form action="{{ route('purchasero.ajukan', $d->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors"
                                                    onclick="return confirm('Ajukan pengadaan {{ $d->no_pr }}?')">
                                                    <i class="fa fa-paper-plane"></i> Ajukan
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center py-12 text-gray-400 text-sm">
                                <i class="fa fa-inbox text-3xl mb-3 block text-gray-300"></i>
                                @if ($tab === 'Disetujui') Belum ada pengadaan yang disetujui
                                @elseif ($tab === 'Ditolak') Belum ada pengadaan yang ditolak
                                @elseif ($tab !== 'semua') Tidak ada data dengan status <strong>{{ $tab }}</strong>
                                @else Belum ada data Pengadaan
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="py-3 border-t border-gray-100 px-4">
            {{ $data->links() }}
        </div>

    </div>
</div>


{{-- ======================================================
    MODAL TOLAK (superadmin — wajib isi catatan)
====================================================== --}}
@if ($role === 'superadmin')
<div id="tolakModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-sm flex-shrink-0">
                        <i class="fa fa-times"></i>
                    </span>
                    Tolak Pengadaan
                </h2>
                <p id="tolakSubtitle" class="text-xs text-gray-500 mt-1 ml-10"></p>
            </div>
            <button onclick="closeTolakModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="tolakForm" action="" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="status" value="Ditolak">

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Catatan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="catatan" id="catatanTolak" rows="4" required
                    placeholder="Tuliskan alasan penolakan pengadaan ini..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-400 resize-none"></textarea>
                <p class="text-xs text-gray-400 mt-1">Catatan ini akan terlihat oleh pemohon sebagai alasan penolakan.</p>
            </div>

            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeTolakModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-times-circle"></i> Konfirmasi Tolak
                </button>
            </div>
        </form>

    </div>
</div>
@endif

{{-- ======================================================
    MODAL TAMBAH / EDIT (non-superadmin)
====================================================== --}}
@if ($role !== 'superadmin')
<div id="purchaseroModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 max-h-[90vh] overflow-y-auto"
         style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Pengadaan</h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Departemen: <span class="font-semibold text-blue-600">{{ $deptLabel }}</span>
                    &nbsp;·&nbsp; No PR dibuat otomatis
                </p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="purchaseroForm" action="{{ route('purchasero.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>

            {{-- No PR (edit mode only) --}}
            <div id="noPrBox" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No PR</label>
                <span id="f_no_pr_display" class="font-mono text-xs text-gray-600 bg-gray-100 px-3 py-2 rounded-lg border border-gray-200 inline-block"></span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="f_tanggal" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pemohon <span class="text-red-500">*</span></label>
                <input type="text" name="pemohon" id="f_pemohon" required placeholder="Nama pemohon"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Barang/Jasa <span class="text-red-500">*</span></label>
                    <input type="text" name="barang_jasa" id="f_barang_jasa" required placeholder="Contoh: Label Baju"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Barang <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_barang" id="f_kode_barang" required placeholder="Contoh: BRG-001"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Qty <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="qty" id="f_qty" required placeholder="Contoh: 500"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Satuan <span class="text-red-500">*</span></label>
                    <input type="text" name="satuan" id="f_satuan" required placeholder="Contoh: pcs"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alasan Permintaan <span class="text-red-500">*</span></label>
                <input type="text" name="alasan_permintaan" id="f_alasan_permintaan" required placeholder="Contoh: Stok habis"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal (Estimasi Harga)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium select-none">Rp</span>
                    <input type="text" name="nominal" id="f_nominal"
                        placeholder="0"
                        inputmode="numeric"
                        autocomplete="off"
                        oninput="formatNominalInput(this)"
                        class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <p class="text-xs text-gray-400 mt-1">Opsional — isi jika sudah ada estimasi harga.</p>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                <i class="fa fa-save"></i> Simpan Data
            </button>
        </form>
    </div>
</div>
@endif

{{-- MODAL HAPUS --}}
<div id="deleteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl">
                <i class="fa fa-triangle-exclamation"></i>
            </div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Pengadaan?</h2>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                Kamu akan menghapus <strong id="deleteName" class="text-gray-700"></strong>.
                Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">
                Batal
            </button>
            <button type="submit"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5">
                <i class="fa fa-trash"></i> Hapus
            </button>
        </form>
    </div>
</div>

{{-- POPUP ALERT --}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay"
     class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
     style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox"
         class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
         style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
            <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4 space-y-0.5">
                @if(session('error'))<li>{{ session('error') }}</li>@endif
                @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg flex-shrink-0"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
</style>

<script>
// ── MODAL TOLAK (superadmin) ─────────────────────────────────
@if ($role === 'superadmin')
const tolakModal = document.getElementById('tolakModal');
const tolakForm  = document.getElementById('tolakForm');

function openTolakModal(id, noPr) {
    tolakForm.action = "{{ url('admin/purchasero') }}/" + id + "/status";
    document.getElementById('tolakSubtitle').innerText = 'No PR: ' + noPr;
    document.getElementById('catatanTolak').value = '';
    tolakModal.classList.remove('hidden');
    tolakModal.classList.add('flex');
    setTimeout(() => document.getElementById('catatanTolak').focus(), 100);
}

function closeTolakModal() {
    tolakModal.classList.add('hidden');
    tolakModal.classList.remove('flex');
}

tolakModal.addEventListener('click', e => { if (e.target === tolakModal) closeTolakModal(); });
@endif

// ── MODAL TAMBAH/EDIT (non-superadmin) ───────────────────────
@if ($role !== 'superadmin')
const purchaseroModal = document.getElementById('purchaseroModal');
const purchaseroForm  = document.getElementById('purchaseroForm');
const methodContainer = document.getElementById('methodContainer');
const noPrBox         = document.getElementById('noPrBox');
const createUrl       = "{{ route('purchasero.store') }}";

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Pengadaan';
    purchaseroForm.action     = createUrl;
    methodContainer.innerHTML = '';
    noPrBox.classList.add('hidden');
    purchaseroForm.reset();
    purchaseroModal.classList.remove('hidden');
    purchaseroModal.classList.add('flex');
}

function closeModal() {
    purchaseroModal.classList.add('hidden');
    purchaseroModal.classList.remove('flex');
}

purchaseroModal.addEventListener('click', e => { if (e.target === purchaseroModal) closeModal(); });

function triggerEdit(btn) {
    document.getElementById('modalTitle').innerText = 'Edit Pengadaan';
    purchaseroForm.action     = btn.dataset.action;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

    document.getElementById('f_no_pr_display').innerText  = btn.dataset.no_pr;
    noPrBox.classList.remove('hidden');

    document.getElementById('f_tanggal').value           = btn.dataset.tanggal ?? '';
    document.getElementById('f_pemohon').value            = btn.dataset.pemohon ?? '';
    document.getElementById('f_barang_jasa').value        = btn.dataset.barang_jasa ?? '';
    document.getElementById('f_kode_barang').value        = btn.dataset.kode_barang ?? '';
    document.getElementById('f_qty').value                = btn.dataset.qty ?? '';
    document.getElementById('f_satuan').value             = btn.dataset.satuan ?? '';
    document.getElementById('f_alasan_permintaan').value  = btn.dataset.alasan_permintaan ?? '';

    // Nominal: tampilkan format ribuan di input
    const rawNominal = btn.dataset.nominal ?? '';
    const nominalEl = document.getElementById('f_nominal');
    if (rawNominal && rawNominal !== '0') {
        nominalEl.value = parseInt(rawNominal, 10).toLocaleString('id-ID');
    } else {
        nominalEl.value = '';
    }

    purchaseroModal.classList.remove('hidden');
    purchaseroModal.classList.add('flex');
}

// Format input nominal: 1000000 → "1.000.000", strip non-digit sebelum submit
function formatNominalInput(el) {
    const raw = el.value.replace(/\D/g, '');
    el.value  = raw ? parseInt(raw, 10).toLocaleString('id-ID') : '';
}

// Konversi nominal dari format ribuan ke angka murni sebelum form submit
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('purchaseroForm');
    if (!form) return;
    form.addEventListener('submit', function () {
        const el = document.getElementById('f_nominal');
        if (el) el.value = el.value.replace(/\D/g, '') || '';
    });
});

@endif

// ── DELETE MODAL ─────────────────────────────────────────────
const deleteModal = document.getElementById('deleteModal');
const deleteForm  = document.getElementById('deleteForm');

function triggerDelete(btn) {
    deleteForm.action = btn.dataset.action;
    document.getElementById('deleteName').innerText = btn.dataset.name || 'ini';
    deleteModal.classList.remove('hidden');
    deleteModal.classList.add('flex');
}

function closeDeleteModal() {
    deleteModal.classList.add('hidden');
    deleteModal.classList.remove('flex');
}

deleteModal.addEventListener('click', e => { if (e.target === deleteModal) closeDeleteModal(); });

// ── POPUP ALERT ───────────────────────────────────────────────
(function () {
    var overlay = document.getElementById('alertOverlay');
    var box     = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(function () {
        overlay.style.opacity = '1'; overlay.style.pointerEvents = 'auto';
        box.style.transform = 'translateY(0)';
    }, 80);
    var timer = setTimeout(closeAlert, 4500);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) closeAlert(); });
    function closeAlert() {
        clearTimeout(timer);
        overlay.style.opacity = '0'; overlay.style.pointerEvents = 'none';
        box.style.transform = 'translateY(-16px)';
    }
    window.closeAlert = closeAlert;
})();
</script>

@endsection
