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
                    @foreach ([
                        ['key' => 'Pending',   'label' => 'Pending',   'icon' => 'bi bi-hourglass-split',   'count' => $totalPending,   'color' => 'yellow'],
                        ['key' => 'Diajukan',  'label' => 'Diajukan',  'icon' => 'bi bi-paper-plane',        'count' => $totalDiajukan,  'color' => 'indigo'],
                        ['key' => 'Disetujui', 'label' => 'Disetujui', 'icon' => 'bi bi-check-circle-fill',  'count' => $totalDisetujui, 'color' => 'green'],
                        ['key' => 'Ditolak',   'label' => 'Ditolak',   'icon' => 'bi bi-x-circle-fill',      'count' => $totalDitolak,   'color' => 'red'],
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
                                    'yellow' => 'bg-yellow-100 text-yellow-700',
                                    default  => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="ml-1 text-xs font-bold px-2 py-0.5 rounded-full {{ $badgeCls }}">{{ $t['count'] }}</span>
                        </a>
                    @endforeach
                @else
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $d)
                        @php
                            $bc = match($d->status) {
                                'Disetujui' => 'bg-green-100 text-green-600',
                                'Ditolak'   => 'bg-red-100 text-red-600',
                                'Diajukan'  => 'bg-indigo-100 text-indigo-600',
                                'Pending'   => 'bg-yellow-100 text-yellow-600',
                                default     => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50 hover:bg-blue-50/50 transition-colors">
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
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $bc }}">
                                    <i class="fa fa-circle text-[6px]"></i> {{ $d->status ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">

                                    {{-- Detail selalu tampil --}}
                                    <button type="button"
                                        onclick="openDetailModal(
                                            '{{ $d->no_pr }}',
                                            '{{ $d->tanggal ? \Carbon\Carbon::parse($d->tanggal)->format('d M Y') : '-' }}',
                                            '{{ addslashes($d->departemen ?? '-') }}',
                                            '{{ addslashes($d->pemohon ?? '-') }}',
                                            '{{ addslashes($d->barang_jasa ?? '-') }}',
                                            '{{ $d->kode_barang ?? '-' }}',
                                            '{{ $d->qty ?? '-' }}',
                                            '{{ $d->satuan ?? '-' }}',
                                            '{{ addslashes($d->alasan_permintaan ?? '-') }}',
                                            '{{ $d->nominal ? number_format($d->nominal, 0, ',', '.') : '-' }}',
                                            '{{ $d->status ?? '-' }}',
                                            '{{ $bc }}',
                                            '{{ addslashes($d->disetujui_oleh ?? '') }}',
                                            '{{ $d->tanggal_persetujuan ? \Carbon\Carbon::parse($d->tanggal_persetujuan)->format('d M Y') : '-' }}',
                                            '{{ addslashes($d->catatan ?? '') }}',
                                            '{{ $d->terakhir_diajukan ? \Carbon\Carbon::parse($d->terakhir_diajukan)->format('d M Y H:i') : '-' }}'
                                        )"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors border border-blue-200">
                                        <i class="fa fa-eye text-[10px]"></i> Detail
                                    </button>

                                    @if ($role === 'superadmin')
                                        {{-- Superadmin: Setujui + Tolak hanya saat Diajukan --}}
                                        @if($d->status === 'Diajukan')
                                            <form action="{{ route('purchasero.status', $d->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="Disetujui">
                                                <button type="submit"
                                                    onclick="return confirm('Setujui pengadaan {{ $d->no_pr }}?')"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-200">
                                                    <i class="fa fa-check text-[10px]"></i> Setujui
                                                </button>
                                            </form>
                                            <button type="button"
                                                onclick="openTolakModal({{ $d->id }}, '{{ $d->no_pr }}')"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                                <i class="fa fa-times text-[10px]"></i> Tolak
                                            </button>
                                        @endif

                                    @else
                                        {{-- Non-superadmin: Edit + Hapus (hanya jika belum diajukan/disetujui) --}}
                                        @if(!in_array($d->status, ['Diajukan', 'Disetujui']))
                                            <button
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
                                                onclick="triggerEdit(this)"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition-colors border border-yellow-200">
                                                <i class="fa fa-edit text-[10px]"></i> Edit
                                            </button>
                                            <button type="button"
                                                data-action="{{ route('purchasero.destroy', $d->id) }}"
                                                data-name="{{ $d->no_pr }}"
                                                onclick="triggerDelete(this)"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                                <i class="fa fa-trash text-[10px]"></i> Hapus
                                            </button>
                                        @endif

                                        {{-- Ajukan: hanya saat Pending atau Ditolak --}}
                                        @if(in_array($d->status, ['Pending', 'Ditolak']))
                                            <form action="{{ route('purchasero.ajukan', $d->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Ajukan pengadaan {{ $d->no_pr }}?')"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors border border-indigo-200">
                                                    <i class="fa fa-paper-plane text-[10px]"></i> Ajukan
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400 text-sm">
                                <i class="fa fa-inbox text-3xl mb-3 block text-gray-300"></i>
                                Belum ada data Pengadaan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="py-3 border-t border-gray-100 px-5">
            <x-pagination :paginator="$data" />
        </div>

    </div>
</div>


{{-- ===== MODAL DETAIL ===== --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4" style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa fa-file-lines text-blue-500"></i> Detail Pengadaan
                </h2>
                <p id="d_no_pr" class="text-xs text-gray-400 mt-0.5 font-mono"></p>
            </div>
            <button onclick="closeDetailModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="px-6 py-4 space-y-3">

            {{-- Baris 1: info utama 4 kolom --}}
            <div class="grid grid-cols-4 gap-3">
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Tanggal</p>
                    <p id="d_tanggal" class="text-sm font-medium text-gray-700"></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Pemohon</p>
                    <p id="d_pemohon" class="text-sm font-medium text-gray-700"></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Departemen</p>
                    <p id="d_departemen" class="text-sm font-medium text-gray-700"></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Status</p>
                    <span id="d_status_badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"></span>
                </div>
            </div>

            {{-- Detail Barang --}}
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-100">
                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">Detail Barang / Jasa</p>
                </div>
                <div class="grid grid-cols-3 divide-x divide-y divide-gray-100">
                    <div class="px-4 py-2.5 col-span-2">
                        <p class="text-[10px] text-gray-400 mb-0.5">Barang/Jasa</p>
                        <p id="d_barang_jasa" class="text-sm font-medium text-gray-700"></p>
                    </div>
                    <div class="px-4 py-2.5">
                        <p class="text-[10px] text-gray-400 mb-0.5">Kode Barang</p>
                        <p id="d_kode_barang" class="text-sm font-mono text-gray-700"></p>
                    </div>
                    <div class="px-4 py-2.5">
                        <p class="text-[10px] text-gray-400 mb-0.5">Qty</p>
                        <p id="d_qty" class="text-sm font-medium text-gray-700"></p>
                    </div>
                    <div class="px-4 py-2.5">
                        <p class="text-[10px] text-gray-400 mb-0.5">Satuan</p>
                        <p id="d_satuan" class="text-sm font-medium text-gray-700"></p>
                    </div>
                    <div class="px-4 py-2.5">
                        <p class="text-[10px] text-gray-400 mb-0.5">Nominal</p>
                        <p id="d_nominal" class="text-sm font-semibold text-emerald-700"></p>
                    </div>
                    <div class="px-4 py-2.5 col-span-3">
                        <p class="text-[10px] text-gray-400 mb-0.5">Alasan Permintaan</p>
                        <p id="d_alasan" class="text-sm text-gray-700"></p>
                    </div>
                </div>
            </div>

            {{-- Info tambahan: persetujuan + catatan + terakhir diajukan dalam 1 baris --}}
            <div class="grid grid-cols-3 gap-3">
                <div id="d_approval_section" class="col-span-2 border border-gray-100 rounded-xl overflow-hidden hidden">
                    <div class="bg-gray-50 px-3 py-1.5 border-b border-gray-100">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">Info Persetujuan</p>
                    </div>
                    <div class="grid grid-cols-2 divide-x divide-gray-100">
                        <div class="px-3 py-2.5"><p class="text-[10px] text-gray-400 mb-0.5">Disetujui Oleh</p><p id="d_disetujui_oleh" class="text-sm font-medium text-gray-700"></p></div>
                        <div class="px-3 py-2.5"><p class="text-[10px] text-gray-400 mb-0.5">Tgl Persetujuan</p><p id="d_tgl_persetujuan" class="text-sm font-medium text-gray-700"></p></div>
                    </div>
                </div>

                <div class="bg-indigo-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-indigo-400 font-semibold uppercase tracking-wide mb-0.5">Terakhir Diajukan</p>
                    <p id="d_terakhir_diajukan" class="text-sm text-indigo-700"></p>
                </div>
            </div>

            <div id="d_catatan_section" class="hidden bg-red-50 border border-red-100 rounded-xl px-4 py-2.5">
                <p class="text-[10px] text-red-400 font-semibold uppercase tracking-wide mb-0.5">Catatan Penolakan</p>
                <p id="d_catatan" class="text-sm text-red-700"></p>
            </div>

        </div>

        <div class="px-6 pb-4">
            <button onclick="closeDetailModal()" class="w-full text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2 hover:bg-gray-50 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ===== MODAL TOLAK (superadmin) ===== --}}
@if ($role === 'superadmin')
<div id="tolakModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-sm flex-shrink-0"><i class="fa fa-times"></i></span>
                    Tolak Pengadaan
                </h2>
                <p id="tolakSubtitle" class="text-xs text-gray-500 mt-1 ml-10"></p>
            </div>
            <button onclick="closeTolakModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none"><i class="fa fa-times"></i></button>
        </div>
        <form id="tolakForm" action="" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="status" value="Ditolak">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="catatan" id="catatanTolak" rows="4" required placeholder="Tuliskan alasan penolakan..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-100 focus:border-red-400 resize-none"></textarea>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeTolakModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-times-circle"></i> Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ===== MODAL TAMBAH / EDIT (non-superadmin) ===== --}}
@if ($role !== 'superadmin')
<div id="purchaseroModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/50 p-4 overflow-y-auto" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 my-6" style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10 rounded-t-2xl">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Pengadaan</h2>
                <p class="text-xs text-gray-500 mt-0.5">Departemen: <span class="font-semibold text-blue-600">{{ $deptLabel }}</span> &nbsp;&middot;&nbsp; Bisa tambah banyak item sekaligus</p>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Form Tambah --}}
        <form id="purchaseroForm" action="{{ route('purchasero.store') }}" method="POST" class="px-6 py-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-5 mb-5 border-b border-gray-100">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="f_tanggal" required value="{{ old('tanggal') }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pemohon <span class="text-red-500">*</span></label>
                    <input type="text" name="pemohon" id="f_pemohon" required placeholder="Nama pemohon" value="{{ old('pemohon') }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold text-gray-700">Daftar Item</p>
                    <span class="text-xs text-gray-400">Bisa tambah lebih dari 1</span>
                </div>
                <div id="itemsContainer" class="space-y-3"></div>
                <button type="button" id="btnTambahItem"
                    class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-50 transition">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Item
                </button>
            </div>
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" id="btnSimpanPR" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>
        </form>

        {{-- Form Edit --}}
        <form id="purchaseroEditForm" action="" method="POST" class="px-6 py-5 hidden">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div class="mb-4 pb-4 border-b border-gray-100">
                <label class="block text-xs font-semibold text-gray-600 mb-1">No PR</label>
                <span id="f_no_pr_display" class="font-mono text-xs text-gray-600 bg-gray-100 px-3 py-2 rounded-lg border border-gray-200 inline-block"></span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="e_tanggal" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pemohon <span class="text-red-500">*</span></label>
                    <input type="text" name="pemohon" id="e_pemohon" required placeholder="Nama pemohon" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Barang/Jasa <span class="text-red-500">*</span></label>
                    <input type="text" name="barang_jasa" id="e_barang_jasa" required placeholder="Contoh: Label Baju" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Barang <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_barang" id="e_kode_barang" required placeholder="Contoh: BRG-001" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Qty <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="qty" id="e_qty" required placeholder="500" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Satuan <span class="text-red-500">*</span></label>
                    <input type="text" name="satuan" id="e_satuan" required placeholder="pcs" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alasan Permintaan <span class="text-red-500">*</span></label>
                    <input type="text" name="alasan_permintaan" id="e_alasan" required placeholder="Contoh: Stok habis" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 select-none">Rp</span>
                        <input type="number" min="0" name="nominal" id="e_nominal" placeholder="0" class="w-full border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                </div>
            </div>
            <div class="flex gap-3 pt-5 mt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </form>

    </div>
</div>
@endif

{{-- ===== MODAL HAPUS ===== --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl">
                <i class="fa fa-triangle-exclamation"></i>
            </div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Pengadaan?</h2>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                Kamu akan menghapus <strong id="deleteName" class="text-gray-700"></strong>. Tindakan ini tidak dapat dibatalkan.
            </p>
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

{{-- ===== POPUP ALERT ===== --}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
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
// ── Detail Modal ──────────────────────────────────────────────
function openDetailModal(no_pr, tanggal, departemen, pemohon, barang_jasa, kode_barang, qty, satuan, alasan, nominal, status, status_class, disetujui_oleh, tgl_persetujuan, catatan, terakhir_diajukan) {
    document.getElementById('d_no_pr').innerText            = no_pr;
    document.getElementById('d_tanggal').innerText          = tanggal;
    document.getElementById('d_pemohon').innerText          = pemohon;
    document.getElementById('d_departemen').innerText       = departemen;
    document.getElementById('d_barang_jasa').innerText      = barang_jasa;
    document.getElementById('d_kode_barang').innerText      = kode_barang;
    document.getElementById('d_qty').innerText              = qty;
    document.getElementById('d_satuan').innerText           = satuan;
    document.getElementById('d_alasan').innerText           = alasan;
    document.getElementById('d_nominal').innerText          = nominal !== '-' ? 'Rp ' + nominal : '-';
    document.getElementById('d_disetujui_oleh').innerText   = disetujui_oleh || '-';
    document.getElementById('d_tgl_persetujuan').innerText  = tgl_persetujuan;
    document.getElementById('d_catatan').innerText          = catatan;
    document.getElementById('d_terakhir_diajukan').innerText = terakhir_diajukan;

    var badge = document.getElementById('d_status_badge');
    badge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium ' + status_class;
    badge.innerHTML = '<i class="fa fa-circle text-[6px]"></i> ' + status;

    document.getElementById('d_approval_section').classList.toggle('hidden', !disetujui_oleh || disetujui_oleh === '-');
    document.getElementById('d_catatan_section').classList.toggle('hidden', !catatan || catatan.trim() === '');

    var m = document.getElementById('detailModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeDetailModal() {
    var m = document.getElementById('detailModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('detailModal').addEventListener('click', function(e) { if (e.target === this) closeDetailModal(); });

// ── Tolak Modal ───────────────────────────────────────────────
@if ($role === 'superadmin')
var tolakModal = document.getElementById('tolakModal');
var tolakForm  = document.getElementById('tolakForm');
function openTolakModal(id, noPr) {
    tolakForm.action = '/admin/purchasero/' + id + '/status';
    document.getElementById('tolakSubtitle').innerText = 'No PR: ' + noPr;
    document.getElementById('catatanTolak').value = '';
    tolakModal.classList.remove('hidden'); tolakModal.classList.add('flex');
    setTimeout(function() { document.getElementById('catatanTolak').focus(); }, 100);
}
function closeTolakModal() {
    tolakModal.classList.add('hidden'); tolakModal.classList.remove('flex');
}
tolakModal.addEventListener('click', function(e) { if (e.target === this) closeTolakModal(); });
@endif

// ── Tambah/Edit Modal ─────────────────────────────────────────
@if ($role !== 'superadmin')
var purchaseroModal    = document.getElementById('purchaseroModal');
var purchaseroForm     = document.getElementById('purchaseroForm');
var purchaseroEditForm = document.getElementById('purchaseroEditForm');
var itemCount          = 0;

function buatItemRow(idx) {
    var wrap = document.createElement('div');
    wrap.id        = 'item-' + idx;
    wrap.className = 'border border-gray-200 rounded-xl overflow-hidden';

    var header = document.createElement('div');
    header.className = 'flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-200';
    header.innerHTML = '<span class="text-xs font-semibold text-gray-500 item-label">Item #' + (idx + 1) + '</span>';

    var hapusBtn = document.createElement('button');
    hapusBtn.type      = 'button';
    hapusBtn.className = 'w-6 h-6 rounded-md bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 transition inline-flex items-center justify-center hapus-item-btn';
    hapusBtn.innerHTML = '<i class="fa-solid fa-times text-xs"></i>';
    hapusBtn.onclick   = function() { hapusItem(idx); };
    header.appendChild(hapusBtn);
    wrap.appendChild(header);

    var body = document.createElement('div');
    body.className = 'px-4 py-3 grid grid-cols-1 sm:grid-cols-2 gap-3';

    var fields = [
        { name: 'barang_jasa',       label: 'Barang/Jasa',       type: 'text',   placeholder: 'Contoh: Label Baju', required: true,  span: false },
        { name: 'kode_barang',       label: 'Kode Barang',       type: 'text',   placeholder: 'Contoh: BRG-001',   required: true,  span: false },
        { name: 'qty',               label: 'Qty',               type: 'number', placeholder: '500',               required: true,  span: false, min: '1' },
        { name: 'satuan',            label: 'Satuan',            type: 'text',   placeholder: 'pcs',               required: true,  span: false },
        { name: 'alasan_permintaan', label: 'Alasan Permintaan', type: 'text',   placeholder: 'Contoh: Stok habis', required: true,  span: true  },
        { name: 'nominal',           label: 'Nominal (opsional)',type: 'number', placeholder: '0',                 required: false, span: true, min: '0' },
    ];

    fields.forEach(function(f) {
        var col = document.createElement('div');
        if (f.span) col.className = 'sm:col-span-2';

        var lbl = document.createElement('label');
        lbl.className = 'text-xs font-semibold text-gray-500 mb-1 block';
        lbl.innerHTML = f.label + (f.required ? ' <span class="text-red-400">*</span>' : '');
        col.appendChild(lbl);

        var inp = document.createElement('input');
        inp.type        = f.type;
        inp.name        = 'items[' + idx + '][' + f.name + ']';
        inp.placeholder = f.placeholder;
        inp.className   = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none';
        if (f.required) inp.required = true;
        if (f.min !== undefined) inp.min = f.min;
        col.appendChild(inp);

        body.appendChild(col);
    });

    wrap.appendChild(body);
    return wrap;
}

function tambahItem() {
    var idx       = itemCount++;
    var container = document.getElementById('itemsContainer');
    container.appendChild(buatItemRow(idx));
    updateItemNumbers();
}

function hapusItem(idx) {
    var el = document.getElementById('item-' + idx);
    if (el) el.remove();
    updateItemNumbers();
}

function updateItemNumbers() {
    var items = document.querySelectorAll('#itemsContainer > div');
    items.forEach(function(div, i) {
        var lbl = div.querySelector('.item-label');
        if (lbl) lbl.textContent = 'Item #' + (i + 1);
        var btn = div.querySelector('.hapus-item-btn');
        if (btn) btn.style.visibility = items.length <= 1 ? 'hidden' : 'visible';
    });
}

document.getElementById('btnTambahItem').addEventListener('click', tambahItem);

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Pengadaan';
    purchaseroForm.reset();
    purchaseroForm.classList.remove('hidden');
    purchaseroEditForm.classList.add('hidden');
    document.getElementById('itemsContainer').innerHTML = '';
    itemCount = 0;
    tambahItem();
    purchaseroModal.classList.remove('hidden'); purchaseroModal.classList.add('flex');
}
function closeModal() {
    purchaseroModal.classList.add('hidden'); purchaseroModal.classList.remove('flex');
}
purchaseroModal.addEventListener('click', function(e) { if (e.target === this) closeModal(); });

purchaseroForm.addEventListener('submit', function() {
    var btn = document.getElementById('btnSimpanPR');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
    btn.classList.add('opacity-60', 'cursor-not-allowed');
});

@if ($errors->any() && !session('success'))
document.addEventListener('DOMContentLoaded', function() { openModal(); });
@endif

function triggerEdit(btn) {
    document.getElementById('modalTitle').innerText = 'Edit Pengadaan';
    purchaseroForm.classList.add('hidden');
    purchaseroEditForm.classList.remove('hidden');
    purchaseroEditForm.action = btn.dataset.action;
    document.getElementById('f_no_pr_display').innerText = btn.dataset.no_pr;
    document.getElementById('e_tanggal').value    = btn.dataset.tanggal    || '';
    document.getElementById('e_pemohon').value    = btn.dataset.pemohon    || '';
    document.getElementById('e_barang_jasa').value= btn.dataset.barang_jasa|| '';
    document.getElementById('e_kode_barang').value= btn.dataset.kode_barang|| '';
    document.getElementById('e_qty').value        = btn.dataset.qty        || '';
    document.getElementById('e_satuan').value     = btn.dataset.satuan     || '';
    document.getElementById('e_alasan').value     = btn.dataset.alasan_permintaan || '';
    var raw = btn.dataset.nominal || '';
    document.getElementById('e_nominal').value = (raw && raw !== '0') ? parseInt(raw, 10) || '' : '';
    purchaseroModal.classList.remove('hidden'); purchaseroModal.classList.add('flex');
}
@endif

// ── Delete Modal ──────────────────────────────────────────────
var deleteModal = document.getElementById('deleteModal');
var deleteForm  = document.getElementById('deleteForm');
function triggerDelete(btn) {
    deleteForm.action = btn.dataset.action;
    document.getElementById('deleteName').innerText = btn.dataset.name || 'ini';
    deleteModal.classList.remove('hidden'); deleteModal.classList.add('flex');
}
function closeDeleteModal() {
    deleteModal.classList.add('hidden'); deleteModal.classList.remove('flex');
}
deleteModal.addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });

// ── Popup Alert ───────────────────────────────────────────────
(function() {
    var overlay = document.getElementById('alertOverlay');
    var box     = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(function() {
        overlay.style.opacity = '1'; overlay.style.pointerEvents = 'auto';
        box.style.transform = 'translateY(0)';
    }, 80);
    var timer = setTimeout(closeAlert, 4500);
    overlay.addEventListener('click', function(e) { if (e.target === overlay) closeAlert(); });
    function closeAlert() {
        clearTimeout(timer);
        overlay.style.opacity = '0'; overlay.style.pointerEvents = 'none';
        box.style.transform = 'translateY(-16px)';
    }
    window.closeAlert = closeAlert;
})();
</script>

@endsection
