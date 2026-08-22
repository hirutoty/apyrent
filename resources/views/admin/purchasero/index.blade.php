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
        <a href="{{ route('purchasero.create') }}"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="fa fa-plus"></i> Tambah Pengadaan
        </a>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total Pengadaan</p>
            <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $totalPR }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Pending</p>
            <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $totalPending }}</h2>
        </div>
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
        <div class="bg-white rounded-2xl border border-gray-100 p-5 col-span-2 md:col-span-3">
            <p class="text-sm text-gray-500">Total Nominal (Diajukan + Disetujui)</p>
            <h2 class="text-2xl font-bold text-emerald-600 mt-2">
                Rp {{ number_format($totalNominal, 0, ',', '.') }}
            </h2>
        </div>
    </div>

    {{-- CHART FILTER --}}
    @php
        $deptList = collect(['Keuangan','Produksi','HRD','Purchase','Sales','Marketing','IT'])
            ->map(fn($d) => ['id' => $d, 'nama' => $d]);
    @endphp
    <x-chart-filter id="purchaseroChartFilter" defaultFilter="month" :showCustomRange="true"
        :showCategoryFilter="true" :categories="$deptList" />

    {{-- CHART CONTAINER --}}
    <x-chart-container
        id="purchaseroChartContainer"
        layout="bar-top"
        pieTitle="Distribusi Status" pieId="purchaseroPieChart"
        barTitle="Nominal Pengadaan per Bulan" barId="purchaseroBarChart"
        lineTitle="Trend Nominal Pengadaan" lineId="purchaseroLineChart"
        :showStats="true" :statsData="[]"
    />

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
        <div class="flex flex-col gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1 text-xs text-gray-500">
                    Menampilkan <span class="font-semibold text-gray-700">{{ $data->total() }}</span> data
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 whitespace-nowrap">Urutkan:</span>
                    <a href="{{ route('purchasero.index', array_merge(request()->except('sort'), ['sort' => 'terbaru'])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                            {{ $sort === 'terbaru' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        <i class="bi bi-sort-down"></i> Terbaru
                    </a>
                    <a href="{{ route('purchasero.index', array_merge(request()->except('sort'), ['sort' => 'terlama'])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                            {{ $sort === 'terlama' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        <i class="bi bi-sort-up"></i> Terlama
                    </a>
                </div>
            </div>

            {{-- FILTER FORM --}}
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="sort" value="{{ $sort }}">

                {{-- Filter Bulan --}}
                <input type="month" name="bulan" value="{{ $bulan ?? '' }}"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

                {{-- Filter Departemen (superadmin saja) --}}
                @if($role === 'superadmin')
                <select name="departemen"
                    class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">Semua Departemen</option>
                    @foreach(['Keuangan','Produksi','HRD','Purchase','Sales','Marketing','IT'] as $dept)
                        <option value="{{ $dept }}" {{ ($deptFilter ?? '') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
                @endif

                <button type="submit"
                    class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    <i class="fa fa-filter text-xs mr-1"></i> Filter
                </button>
                @if($bulan || $deptFilter)
                    <a href="{{ route('purchasero.index', ['tab' => $tab, 'sort' => $sort]) }}"
                        class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </form>
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Total Items</th>
                        <th class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Total Nominal</th>
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
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-gray-600">
                                    <i class="fa fa-boxes text-blue-400 text-xs"></i>
                                    {{ $d->items->count() > 0 ? $d->items->count() : '1' }} item{{ ($d->items->count() > 1) ? 's' : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <span class="text-sm font-semibold text-emerald-600">
                                    Rp {{ number_format($d->total_nominal ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $bc }}">
                                    <i class="fa fa-circle text-[6px]"></i> {{ $d->status ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">

                                    {{-- Detail selalu tampil --}}
                                    <button type="button"
                                        onclick="openDetailModal({{ $d->id }})"
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
                                            <a href="{{ route('purchasero.edit', $d->id) }}"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition-colors border border-yellow-200">
                                                <i class="fa fa-edit text-[10px]"></i> Edit
                                            </a>
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
                            <td colspan="8" class="text-center py-12 text-gray-400 text-sm">
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

            {{-- Detail Barang - Multiple Items --}}
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-100">
                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">Detail Items</p>
                </div>
                <div id="d_items_container" class="divide-y divide-gray-100">
                    <!-- Items will be populated by JavaScript -->
                </div>
                
                <!-- Fallback for old structure (single item) -->
                <div id="d_old_structure" class="hidden grid grid-cols-3 divide-x divide-y divide-gray-100">
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
                        <p id="d_nominal_old" class="text-sm font-semibold text-emerald-700"></p>
                    </div>
                </div>
            </div>

            {{-- Total & Alasan Permintaan --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-emerald-50 rounded-xl px-3 py-2.5 border border-emerald-100">
                    <p class="text-[10px] text-emerald-400 font-semibold uppercase tracking-wide mb-0.5">Total Nominal</p>
                    <p id="d_total_nominal" class="text-base font-bold text-emerald-700"></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Total Items</p>
                    <p id="d_total_items" class="text-base font-semibold text-gray-700"></p>
                </div>
            </div>

            {{-- Alasan Permintaan --}}
            <div class="bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                <p class="text-[10px] text-blue-400 font-semibold uppercase tracking-wide mb-1">Alasan Permintaan</p>
                <p id="d_alasan" class="text-sm text-blue-700"></p>
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
function openDetailModal(purchaseroId) {
    // Show modal immediately with loading state
    var modal = document.getElementById('detailModal');
    modal.classList.remove('hidden'); 
    modal.classList.add('flex');
    
    // Show loading state
    document.getElementById('d_no_pr').innerText = 'Loading...';
    document.getElementById('d_items_container').innerHTML = '<div class="px-4 py-6 text-center text-gray-400"><i class="fa fa-spinner fa-spin mr-2"></i>Loading items...</div>';
    
    // Fetch data via AJAX
    fetch('/admin/purchasero/' + purchaseroId + '/details')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateDetailModal(data.purchasero);
            } else {
                alert('Gagal memuat detail: ' + (data.message || 'Unknown error'));
                closeDetailModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat detail');
            closeDetailModal();
        });
}

function populateDetailModal(pr) {
    // Basic info
    document.getElementById('d_no_pr').innerText = pr.no_pr;
    document.getElementById('d_tanggal').innerText = pr.tanggal_formatted;
    document.getElementById('d_pemohon').innerText = pr.pemohon;
    document.getElementById('d_departemen').innerText = pr.departemen;
    document.getElementById('d_alasan').innerText = pr.alasan_permintaan || '-';
    document.getElementById('d_terakhir_diajukan').innerText = pr.terakhir_diajukan_formatted || '-';
    
    // Status badge
    var statusBadge = document.getElementById('d_status_badge');
    statusBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium ' + pr.status_class;
    statusBadge.innerHTML = '<i class="fa fa-circle text-[6px]"></i> ' + pr.status;
    
    // Total info
    document.getElementById('d_total_nominal').innerText = 'Rp ' + pr.total_nominal_formatted;
    document.getElementById('d_total_items').innerText = pr.total_items + ' item' + (pr.total_items > 1 ? 's' : '');
    
    // Items container
    var itemsContainer = document.getElementById('d_items_container');
    var oldStructure = document.getElementById('d_old_structure');
    
    if (pr.items && pr.items.length > 0) {
        // New structure: multiple items
        itemsContainer.innerHTML = '';
        oldStructure.classList.add('hidden');
        
        pr.items.forEach(function(item, index) {
            var itemDiv = document.createElement('div');
            itemDiv.className = 'p-4';
            
            itemDiv.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Item #${index + 1}</span>
                    ${item.subtotal ? '<span class="text-sm font-semibold text-emerald-600">Rp ' + item.subtotal_formatted + '</span>' : ''}
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                    <div>
                        <p class="text-[10px] text-gray-400 mb-0.5 uppercase">Nama Barang</p>
                        <p class="text-sm font-medium text-gray-700">${item.nama_barang}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 mb-0.5 uppercase">Kategori</p>
                        <p class="text-sm text-gray-600">${item.kategori || '-'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 mb-0.5 uppercase">Qty</p>
                        <p class="text-sm font-medium text-gray-700">${item.qty} ${item.satuan || ''}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 mb-0.5 uppercase">Harga Satuan</p>
                        <p class="text-sm text-gray-600">${item.harga_satuan ? 'Rp ' + item.harga_satuan_formatted : '-'}</p>
                    </div>
                </div>
                ${item.part_number || item.serial_number || item.spesifikasi || item.merk || item.keterangan ? `
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                    ${item.part_number ? '<div><span class="text-gray-400">Part#:</span> <span class="text-gray-600">' + item.part_number + '</span></div>' : ''}
                    ${item.serial_number ? '<div><span class="text-gray-400">Serial#:</span> <span class="text-gray-600">' + item.serial_number + '</span></div>' : ''}
                    ${item.spesifikasi ? '<div><span class="text-gray-400">Spesifikasi:</span> <span class="text-gray-600">' + item.spesifikasi + '</span></div>' : ''}
                    ${item.merk ? '<div><span class="text-gray-400">Merk:</span> <span class="text-gray-600">' + item.merk + '</span></div>' : ''}
                    ${item.posisi ? '<div><span class="text-gray-400">Posisi:</span> <span class="text-gray-600">' + item.posisi + '</span></div>' : ''}
                    ${item.keterangan ? '<div class="col-span-full"><span class="text-gray-400">Keterangan:</span> <span class="text-gray-600">' + item.keterangan + '</span></div>' : ''}
                </div>` : ''}
            `;
            
            itemsContainer.appendChild(itemDiv);
        });
    } else {
        // Old structure: fallback for legacy data
        oldStructure.classList.remove('hidden');
        itemsContainer.innerHTML = '';
        document.getElementById('d_barang_jasa').innerText = pr.barang_jasa || '-';
        document.getElementById('d_kode_barang').innerText = pr.kode_barang || '-';
        document.getElementById('d_qty').innerText = pr.qty || '-';
        document.getElementById('d_satuan').innerText = pr.satuan || '-';
        document.getElementById('d_nominal_old').innerText = pr.nominal ? 'Rp ' + pr.nominal_formatted : '-';
    }
    
    // Approval section
    var approvalSection = document.getElementById('d_approval_section');
    if (pr.disetujui_oleh) {
        document.getElementById('d_disetujui_oleh').innerText = pr.disetujui_oleh;
        document.getElementById('d_tgl_persetujuan').innerText = pr.tanggal_persetujuan_formatted || '-';
        approvalSection.classList.remove('hidden');
    } else {
        approvalSection.classList.add('hidden');
    }
    
    // Catatan section
    var catatanSection = document.getElementById('d_catatan_section');
    if (pr.catatan && pr.catatan.trim()) {
        document.getElementById('d_catatan').innerText = pr.catatan;
        catatanSection.classList.remove('hidden');
    } else {
        catatanSection.classList.add('hidden');
    }
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

// ========================================
// CHART INITIALIZATION
// ========================================
const purchaseroChartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function() {
    initPurchaseroCharts({ filter_type: 'month' });

    document.addEventListener('chartFilterChange', function(e) {
        if (e.detail.filterId === 'purchaseroChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
                departemen:  e.detail.categoryId ?? '',
            };
            updatePurchaseroCharts(filters);
        }
    });
});

async function initPurchaseroCharts(filters) {
    try {
        await purchaseroChartManager.initChartsFromAPI('purchasero', {
            pie:  'purchaseroPieChart',
            bar:  'purchaseroBarChart',
            line: 'purchaseroLineChart',
        }, filters);
    } catch (error) {
        console.error('Error loading purchasero charts:', error);
    }
}

async function updatePurchaseroCharts(filters) {
    try {
        const barOptions = { scrollable: filters.filter_type === 'custom' };
        await purchaseroChartManager.updateChartsFromAPI('purchasero', {
            pie:  'purchaseroPieChart',
            bar:  'purchaseroBarChart',
            line: 'purchaseroLineChart',
        }, filters, barOptions);
    } catch (error) {
        console.error('Error updating purchasero charts:', error);
    }
}
</script>

@endsection
