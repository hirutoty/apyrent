@extends('admin.layouts.app')
@section('title', 'Data Kontrak Kendaraan')
@section('content')
<div class="space-y-6 p-5">

    {{-- ALERTS --}}
    @if (session('success'))
    <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        <i class="fa fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif
    @if (session('warning'))
    <div class="flex items-center gap-3 rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-700">
        <i class="fa fa-exclamation-triangle text-yellow-500"></i> {{ session('warning') }}
    </div>
    @endif
    @if (session('error'))
    <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <i class="fa fa-exclamation-circle text-red-500"></i> {{ session('error') }}
    </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Kontrak Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data kontrak penawaran</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('kontrak.pdf', request()->query()) }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf"></i> Export PDF
            </a>
            <a href="{{ route('kontrak.export.excel', request()->query()) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-green-600 text-green-600 rounded-lg bg-transparent hover:bg-green-600 hover:text-white transition-colors">
                <i class="fa fa-file-excel"></i> Export Excel
            </a>
            <button onclick="initCreateKetentuan(); openModal('modalCreate')"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                <i class="fa fa-check-circle text-sm"></i> Approve Kontrak
            </button>
        </div>
    </div>

    {{-- NAV TABS --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-0 -mb-px overflow-x-auto">
            @php
                $navItems = [
                    ['label' => 'Summary',   'url' => '/admin/summary',   'icon' => 'bi bi-bar-chart-line'],
                    ['label' => 'Penawaran', 'url' => '/admin/penawaran', 'icon' => 'bi bi-file-earmark-richtext'],
                    ['label' => 'Kontrak',   'url' => '/admin/kontrak',   'icon' => 'bi bi-file-earmark-lock'],
                    ['label' => 'Invoice',   'url' => '/admin/invoices',  'icon' => 'bi bi-receipt-cutoff'],
                    ['label' => 'Payments',  'url' => '/admin/payments',  'icon' => 'bi bi-credit-card-2-front'],
                    ['label' => 'Reminders', 'url' => '/admin/reminders', 'icon' => 'bi bi-bell'],
                ];
            @endphp
            @foreach ($navItems as $item)
                @php $isActive = request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*'); @endphp
                <a href="{{ $item['url'] }}"
                    class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                        {{ $isActive ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                    <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total Kontrak</p>
            <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $kontraks->total() }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Pending</p>
            <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $kontraks->getCollection()->where('status','pending')->count() }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Active</p>
            <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $kontraks->getCollection()->whereIn('status',['active','approved'])->count() }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Selesai-Belum Lunas</p>
            <h2 class="text-3xl font-bold text-orange-500 mt-2">{{ $kontraks->getCollection()->where('status','selesai-belum lunas')->count() }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Expired / Terminated</p>
            <h2 class="text-3xl font-bold text-red-500 mt-2">{{ $kontraks->getCollection()->whereIn('status',['expired','terminated','rejected'])->count() }}</h2>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

        {{-- SEARCH BAR --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <form method="GET" class="flex gap-2 flex-1 flex-wrap">
                <div class="relative flex-1 min-w-[180px]">
                    <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari no kontrak / pihak..."
                        class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <select name="status" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 focus:outline-none">
                    <option value="">Semua Status</option>
                    @foreach(['pending','approved','active','completed','selesai-belum lunas','rejected','expired','terminated'] as $st)
                    <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
                <button class="bg-gray-800 text-white text-xs px-4 py-1.5 rounded-lg">Cari</button>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No Kontrak</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Penawaran</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kendaraan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Durasi & Periode</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Perjanjian</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($kontraks as $no => $k)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50/50 hover:bg-blue-50/40 transition-colors">

                        <td class="px-4 py-3.5 text-xs text-gray-400">{{ $kontraks->firstItem() + $no }}</td>

                        {{-- No Kontrak --}}
                        <td class="px-4 py-3.5">
                            <span class="font-mono text-xs font-semibold text-blue-700">{{ $k->no_kontrak }}</span>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $k->tanggal_kontrak?->format('d M Y') }}</div>
                            <!-- {{-- Draft PDF badge --}}
                            @if($k->file_draft)
                            <a href="/{{ $k->file_draft }}" target="_blank"
                                class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-600 border border-red-200 hover:bg-red-100">
                                <i class="fa fa-file-pdf text-[10px]"></i> Draft
                            </a>
                            @endif
                            <a href="{{ route('kontrak.regenerate-draft', $k->id) }}"
                                class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100"
                                title="Regenerate & Download Draft PDF">
                                <i class="fa fa-sync text-[10px]"></i> Regen
                            </a> -->
                            <a href="{{ route('kontrak.draft-print', $k->id) }}" target="_blank"
                                class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-50 text-green-600 border border-green-200 hover:bg-green-100"
                                title="Buka halaman print (Ctrl+P)">
                                <i class="fa fa-print text-[10px]"></i> Print
                            </a>
                        </td>

                        {{-- Penawaran --}}
                        <td class="px-4 py-3.5 text-xs text-gray-600">
                            {{ $k->penawaran->no_penawaran ?? '-' }}
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $k->pihak_kedua }}</div>
                        </td>

                        {{-- Kendaraan --}}
                        <td class="px-4 py-3.5">
                            @if($k->penawaran && $k->penawaran->items->isNotEmpty())
                                <div class="flex flex-col gap-1">
                                @foreach($k->penawaran->items as $itemNo => $item)
                                    @if($item->kendaraan)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-bold text-gray-400 w-4">{{ $itemNo + 1 }}.</span>
                                        <span class="text-xs font-semibold text-gray-800">{{ $item->kendaraan->merk }} – {{ $item->kendaraan->nopol }}</span>
                                        @php
                                            $stc = match($item->kendaraan->status_kendaraan ?? '') {
                                                'tersedia'          => 'bg-green-100 text-green-700',
                                                'disewa'            => 'bg-blue-100 text-blue-700',
                                                'selesai-belum lunas' => 'bg-orange-100 text-orange-700',
                                                default             => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold {{ $stc }}">
                                            {{ ucfirst($item->kendaraan->status_kendaraan ?? '-') }}
                                        </span>
                                    </div>
                                    @endif
                                @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- Durasi & Periode --}}
                        <td class="px-4 py-3.5 text-xs text-gray-600">
                            @php
                                $items = $k->penawaran?->items ?? collect();
                                $hasPerjanjian = $k->perjanjian_pembayaran;
                                $mulaiRental = $hasPerjanjian
                                    ? \Carbon\Carbon::parse($k->perjanjian_pembayaran)
                                    : ($k->tanggal_kontrak ?? null);
                            @endphp
                            @if($items->isNotEmpty())
                                <div class="flex flex-col gap-1">
                                @foreach($items as $item)
                                    @php
                                        $durVal = (int) ($item->durasi ?? 1);
                                        $durSat = strtolower(trim($item->satuan_durasi ?? 'bulan'));
                                        if (!in_array($durSat, ['hari','bulan','tahun'])) $durSat = 'bulan';
                                        $selesaiItem = $mulaiRental ? match($durSat) {
                                            'hari'  => \Carbon\Carbon::parse($mulaiRental)->addDays($durVal),
                                            'tahun' => \Carbon\Carbon::parse($mulaiRental)->addYears($durVal),
                                            default => \Carbon\Carbon::parse($mulaiRental)->addMonths($durVal),
                                        } : null;
                                        $satLabel = match($durSat) { 'tahun' => 'Thn', 'hari' => 'Hr', default => 'Bln' };
                                    @endphp
                                    <div class="flex items-start gap-1.5">
                                        <i class="fa fa-car text-gray-300 text-[10px] mt-0.5 flex-shrink-0"></i>
                                        <div class="min-w-0">
                                            <span class="font-semibold text-indigo-700">{{ $durVal }} {{ $satLabel }}</span>
                                            @if($item->kendaraan)
                                            <span class="text-[10px] text-gray-400 ml-1">{{ $item->kendaraan->nopol }}</span>
                                            @endif
                                            @if($mulaiRental && $selesaiItem)
                                            <div class="text-[10px] text-gray-400 leading-tight">
                                                {{ \Carbon\Carbon::parse($mulaiRental)->format('d M Y') }} –<br>
                                                {{ $selesaiItem->format('d M Y') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            @else
                                {{-- fallback: data lama sebelum per-item --}}
                                @if($k->durasi_value)
                                <span class="font-semibold text-indigo-700">{{ $k->durasi_value }} {{ $k->durasi_satuan }}</span>
                                @endif
                                @if($mulaiRental && $k->tanggal_selesai)
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($mulaiRental)->format('d M Y') }} –<br>
                                    {{ $k->tanggal_selesai->format('d M Y') }}
                                </div>
                                @endif
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3.5 text-center">
                            @php
                                $sc = match($k->status) {
                                    'active'              => 'bg-green-100 text-green-700',
                                    'approved'            => 'bg-indigo-100 text-indigo-700',
                                    'completed'           => 'bg-blue-100 text-blue-700',
                                    'pending'             => 'bg-yellow-100 text-yellow-700',
                                    'rejected'            => 'bg-red-100 text-red-600',
                                    'expired'             => 'bg-gray-100 text-gray-600',
                                    'terminated'          => 'bg-gray-800 text-white',
                                    'selesai-belum lunas' => 'bg-orange-100 text-orange-700',
                                    default               => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">
                                {{ ucfirst($k->status) }}
                            </span>
                        </td>

                        {{-- Perjanjian Pembayaran --}}
                        <td class="px-4 py-3.5">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-gray-700">{{ $k->perjanjian_pembayaran?->format('d M Y') ?? '-' }}</span>
                                @if ($k->showReminder && $k->isExpired)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-semibold w-fit">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Terlambat {{ abs($k->sisaHari) }} hari
                                </span>
                                @elseif ($k->showReminder && $k->isSoon)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-[10px] font-semibold w-fit animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                    @if ($k->sisaHari == 0) Jatuh Tempo Hari Ini
                                    @elseif ($k->sisaHari == 1) Besok
                                    @else {{ $k->sisaHari }} hari lagi
                                    @endif
                                </span>
                                @endif
                            </div>
                        </td>

                        {{-- AKSI --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">

                                {{-- Detail --}}
                                <button onclick='openDetailModal(@json($k->load("penawaran.items.kendaraan")))'
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
                                    <i class="fa fa-eye text-xs"></i>
                                </button>

                                {{-- Upload & Approve: status pending atau approved --}}
                                @if(in_array($k->status, ['pending', 'approved']))
                                <button type="button"
                                    onclick="openApproveModal(this)"
                                    data-id="{{ $k->id }}"
                                    data-no="{{ $k->no_kontrak }}"
                                    data-customer="{{ $k->penawaran?->customer_name }}"
                                    data-contact="{{ $k->penawaran?->contact_person }}"
                                    data-ktp="{{ $k->no_ktp_kedua }}"
                                    data-email="{{ $k->email_kedua }}"
                                    data-jenis="{{ $k->jenis_pelanggan }}"
                                    data-alamat="{{ $k->alamat_kedua }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200">
                                    <i class="fa fa-upload text-xs"></i> Approve
                                </button>
                                @endif

                                {{-- Selesai: hanya status active --}}
                                @if($k->status === 'active')
                                <form action="{{ route('kontrak.selesai', $k->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Tandai kontrak ini selesai? Sistem akan memeriksa status pembayaran.')">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-teal-100 text-teal-700 hover:bg-teal-200">
                                        <i class="fa fa-flag-checkered text-xs"></i> Selesai
                                    </button>
                                </form>
                                @endif

                                {{-- Edit --}}
                                <button onclick='openEditModal(@json($k))'
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                    <i class="fa fa-edit text-xs"></i>
                                </button>

                                {{-- Hapus --}}
                                <form action="{{ route('kontrak.destroy', $k->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Hapus kontrak ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200">
                                        <i class="fa fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400 text-sm">
                            <i class="fa fa-inbox text-3xl mb-3 block text-gray-300"></i>
                            Belum ada data kontrak
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="py-3 border-t border-gray-100"><x-pagination :paginator="$kontraks" /></div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════
     MODAL: APPROVE KONTRAK (buat baru)
═══════════════════════════════════════════════════ --}}
<div id="modalCreate" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl max-h-[95vh] overflow-y-auto mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex gap-0">
                <button type="button" id="createTab1Btn" onclick="switchCreateTab(1)"
                    class="px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tl-lg">
                    <i class="fa fa-file-alt mr-1"></i> Data
                </button>
                <button type="button" id="createTab2Btn" onclick="switchCreateTab(2)" disabled
                    class="px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400 cursor-not-allowed rounded-tr-lg">
                    <i class="fa fa-list-alt mr-1"></i> Ketentuan
                </button>
            </div>
            <button onclick="closeModal('modalCreate')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">&times;</button>
        </div>
        <form action="{{ route('kontrak.store') }}" method="POST" id="formCreateKontrak">
            @csrf
            {{-- TAB 1: DATA --}}
            <div id="createTabContent1">
            <div class="px-6 py-5 space-y-4">

            {{-- Pilih Penawaran --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penawaran <span class="text-red-500">*</span></label>
                <select name="penawaran_id" id="create_penawaran_id"
                    onchange="fetchPenawaranDetail(this.value)"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                    <option value="">-- Pilih Penawaran --</option>
                    @foreach ($penawarans as $p)
                    <option value="{{ $p->id }}">{{ $p->no_penawaran }} – {{ $p->customer_name ?? $p->kepada }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Preview Kendaraan & Durasi (hidden awalnya) --}}
            <div id="preview_penawaran" class="hidden">
                <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4 space-y-3">
                    <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide flex items-center gap-2">
                        <i class="fa fa-car"></i> Data Kendaraan dari Penawaran
                    </p>
                    <div id="preview_kendaraan_list" class="space-y-2"></div>

                    <div class="pt-2 border-t border-blue-200">
                        <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-2">
                            <i class="fa fa-clock"></i> Durasi Sewa per Kendaraan
                        </p>
                        {{-- Durasi per item — diisi JS --}}
                        <div id="preview_durasi_list" class="space-y-2"></div>
                        {{-- Hidden inputs: durasi kontrak (pakai item terpanjang sebagai patokan) --}}
                        <input type="hidden" name="durasi_value" id="hidden_durasi_value">
                        <input type="hidden" name="durasi_satuan" id="hidden_durasi_satuan">
                    </div>

                    <div class="pt-2 border-t border-blue-200">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Total Nilai Penawaran</p>
                        <p class="text-sm font-bold text-green-700" id="preview_total">-</p>
                    </div>
                </div>
            </div>

            {{-- Tanggal --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Kontrak <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_kontrak" id="create_tanggal_kontrak"
                        min="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                        required value="{{ now()->format('Y-m-d') }}">
                    <p class="text-[10px] text-gray-400 mt-1">Tanggal terbit dokumen kontrak</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Perjanjian Pembayaran</label>
                    <input type="date" name="perjanjian_pembayaran" id="create_perjanjian_pembayaran"
                        onchange="calcTanggalSelesai()"
                        min="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <p class="text-[10px] text-gray-400 mt-1">Rental mulai dari tanggal ini</p>
                </div>
            </div>

            {{-- Tanggal selesai per kendaraan (otomatis) --}}
            <div id="selesai_per_kendaraan" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    <i class="fa fa-calendar-check text-indigo-500"></i> Estimasi Tanggal Selesai per Kendaraan
                </label>
                <div id="list_selesai_kendaraan" class="space-y-1.5"></div>
                <p class="text-[10px] text-gray-400 mt-1">Dihitung dari perjanjian pembayaran + durasi masing-masing kendaraan</p>
            </div>

            {{-- Pihak --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pihak Pertama <span class="text-red-500">*</span></label>
                    <input type="text" name="pihak_pertama" id="create_pihak_pertama"
                        value=""
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No KTP Pihak Pertama</label>
                    <input type="text" name="contact_pertama"
                        value=""
                        inputmode="numeric" maxlength="16"
                        oninput="this.value=this.value.replace(/\D/g,'').slice(0,16)"
                        placeholder="16 digit No KTP"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pihak Kedua <span class="text-red-500">*</span></label>
                    <input type="text" name="pihak_kedua" id="create_pihak_kedua"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Pihak 2</label>
                    <input type="text" name="contact_kedua" id="create_contact_kedua"
                        inputmode="numeric" maxlength="15"
                        oninput="this.value=this.value.replace(/\D/g,'').slice(0,15)"
                        placeholder="08xx-xxxx-xxxx"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            {{-- Jenis Pelanggan (dipindahkan ke atas) --}}
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pelanggan <span class="text-red-500">*</span></label>
                    <select name="jenis_pelanggan" id="create_jenis_pelanggan"
                        onchange="togglePerwakilanFields('create')"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                        <option value="perorangan" selected>Perorangan</option>
                        <option value="perusahaan">Perusahaan</option>
                    </select>
                </div>
            </div>

            {{-- Nama Customer --}}
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Customer <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="customer_name" id="create_customer_name"
                            autocomplete="off"
                            placeholder="Nama customer..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                        <ul id="create_customer_list"
                            class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-52 overflow-y-auto text-sm"></ul>
                    </div>
                </div>
            </div>

            {{-- Field Perwakilan Perusahaan (conditional - hidden by default) --}}
            <div id="create_perwakilan_wrapper" class="grid grid-cols-2 gap-3 hidden">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Diwakili Oleh <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="perwakilan_pihak_kedua" id="create_perwakilan_pihak_kedua"
                        placeholder="Nama perwakilan perusahaan..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Jabatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="jabatan_pihak_kedua" id="create_jabatan_pihak_kedua"
                        placeholder="Misal: Direktur Utama"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            {{-- Detail Customer Pihak Kedua --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No KTP Pihak Kedua</label>
                    <input type="text" name="no_ktp_kedua" id="create_no_ktp_kedua"
                        inputmode="numeric" maxlength="16"
                        oninput="this.value=this.value.replace(/\D/g,'').slice(0,16)"
                        placeholder="16 digit No KTP"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Pihak Kedua</label>
                    <input type="email" name="email_kedua" id="create_email_kedua"
                        placeholder="email@example.com"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat Pihak Kedua</label>
                    <textarea name="alamat_kedua" id="create_alamat_kedua" rows="2"
                        placeholder="Alamat lengkap..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></textarea>
                </div>
            </div>{{-- end detail customer --}}

            </div>{{-- end px-6 py-5 Tab1 --}}
            <div class="border-t border-gray-100 px-6 py-4 flex justify-between items-center">
                <button type="button" onclick="closeModal('modalCreate')"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="button" onclick="goToCreateTab2()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                    Selanjutnya <i class="fa fa-arrow-right text-xs"></i>
                </button>
            </div>
            </div>{{-- end Tab 1 --}}

            {{-- TAB 2: KETENTUAN --}}
            <div id="createTabContent2" class="hidden">
            <div class="px-6 py-5 space-y-4">

                <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa fa-file-contract text-blue-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-blue-800 mb-0.5">Editor Ketentuan Kontrak</p>
                        <p class="text-[11px] text-blue-600 leading-relaxed">
                            Edit isi ketentuan dalam dua bahasa. Default sudah terisi otomatis — ubah sesuai kebutuhan.
                            Setelah disimpan, <strong>draft PDF</strong> otomatis di-generate.
                        </p>
                    </div>
                </div>

                {{-- Textarea ID --}}
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-bold text-blue-700 mb-1.5 uppercase tracking-wide">
                        <i class="fa fa-language"></i> Ketentuan Bahasa Indonesia
                    </label>
                    <textarea
                        name="ketentuan_id"
                        id="create_ketentuan_id"
                        rows="22"
                        placeholder="Ketik isi ketentuan kontrak dalam Bahasa Indonesia..."
                        class="w-full border border-blue-200 rounded-xl px-4 py-3 text-xs font-mono leading-relaxed
                               focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                               resize-y bg-white text-gray-800"></textarea>
                </div>

                {{-- Textarea EN — readonly, auto-translate dari ID --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="flex items-center gap-1.5 text-xs font-bold text-green-700 uppercase tracking-wide">
                            <i class="fa fa-globe"></i> Contract Terms (English)
                        </label>
                        <span id="create_translate_status" class="text-[10px] text-gray-400 flex items-center gap-1">
                            <i class="fa fa-magic text-green-400"></i> Auto-translate dari Bahasa Indonesia
                        </span>
                    </div>
                    <textarea
                        name="ketentuan_en"
                        id="create_ketentuan_en"
                        rows="22"
                        readonly
                        placeholder="Terjemahan otomatis akan muncul di sini..."
                        class="w-full border border-green-200 rounded-xl px-4 py-3 text-xs font-mono leading-relaxed
                               resize-y bg-gray-50 text-gray-600 cursor-not-allowed"></textarea>
                </div>

            </div>{{-- end px-6 py-5 Tab2 --}}
            <div class="border-t border-gray-100 px-6 py-4 flex justify-between items-center">
                <button type="button" onclick="switchCreateTab(1)"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                    <i class="fa fa-arrow-left text-xs"></i> Kembali
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                    <i class="fa fa-save text-xs"></i> Simpan & Generate Draft
                </button>
            </div>
            </div>{{-- end Tab 2 --}}

        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     MODAL: UPLOAD & APPROVE
═══════════════════════════════════════════════════ --}}
<div id="modalApprove" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-[95%] max-w-2xl max-h-[95vh] overflow-y-auto">

        {{-- Header dengan tabs --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex gap-0">
                <button type="button" id="approveTab1Btn"
                    onclick="switchApproveTab(1)"
                    class="px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tl-lg">
                    <i class="fa fa-upload mr-1"></i> Data
                </button>
                <button type="button" id="approveTab2Btn"
                    onclick="switchApproveTab(2)"
                    disabled
                    class="px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400 cursor-not-allowed rounded-tr-lg">
                    <i class="fa fa-clipboard-list mr-1"></i> Ketentuan
                </button>
            </div>
            <button onclick="closeModal('modalApprove')"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="approveForm" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- TAB 1: DATA --}}
            <div id="approveTabContent1">
                <div class="px-6 py-5 space-y-5">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">1</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi kontrak</h3>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4">
                            <p class="text-xs font-semibold text-blue-700 mb-1"><i class="fa fa-file-contract mr-1"></i> <span id="approve_subtitle">–</span></p>
                            <p class="text-xs text-blue-600">Status akan berubah ke <strong>Active</strong> dan rental kendaraan akan dibuat otomatis setelah approve.</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">2</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Upload file kontrak TTD</h3>
                        </div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            File Kontrak <span class="text-red-500">*</span>
                        </label>
                        <input type="file" id="approve_file_input" name="file_kontrak" accept=".pdf"
                            onchange="onApproveFileChange(this)"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <p class="text-[10px] text-gray-400 mt-1">Format: PDF saja. Maks 10MB.</p>
                        <p id="approve_file_error" class="text-[10px] text-red-500 mt-1 hidden">Harap pilih file PDF terlebih dahulu.</p>

                        <div id="approveFilePreview" class="hidden mt-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 flex items-center gap-3">
                            <i class="fa fa-file-pdf text-green-600 text-lg flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <p id="approveFileName" class="text-xs font-semibold text-green-700 truncate"></p>
                                <p class="text-[10px] text-green-500 mt-0.5">File siap diupload</p>
                            </div>
                            <i class="fa fa-check-circle text-green-500 text-lg flex-shrink-0"></i>
                        </div>
                    </div>
                </div>

                {{-- Data Customer Pihak Kedua --}}
                <div class="border-t border-gray-100 mx-6"></div>
                <div class="px-6 pt-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">3</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Data Customer (Pihak Kedua)</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pelanggan <span class="text-red-500">*</span></label>
                            <select name="jenis_pelanggan" id="approve_jenis_pelanggan"
                                onchange="togglePerwakilanFields('approve')"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                                <option value="perorangan" selected>Perorangan</option>
                                <option value="perusahaan">Perusahaan</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Customer <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="customer_name" id="approve_customer_name"
                                    autocomplete="off"
                                    placeholder="Nama customer..."
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                                <ul id="approve_customer_list"
                                    class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-52 overflow-y-auto text-sm"></ul>
                            </div>
                        </div>

                        {{-- Field Perwakilan Perusahaan (conditional - hidden by default) --}}
                        <div id="approve_perwakilan_wrapper" class="col-span-2 grid grid-cols-2 gap-3 hidden">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Diwakili Oleh <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="perwakilan_pihak_kedua" id="approve_perwakilan_pihak_kedua"
                                    placeholder="Nama perwakilan perusahaan..."
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Jabatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="jabatan_pihak_kedua" id="approve_jabatan_pihak_kedua"
                                    placeholder="Misal: Direktur Utama"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Kontak</label>
                            <input type="text" name="contact_person" id="approve_contact_person"
                                inputmode="numeric" maxlength="15"
                                oninput="this.value=this.value.replace(/\D/g,'').slice(0,15)"
                                placeholder="08xx-xxxx-xxxx"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">No KTP</label>
                            <input type="text" name="no_ktp_kedua" id="approve_no_ktp_kedua"
                                inputmode="numeric" maxlength="16"
                                oninput="this.value=this.value.replace(/\D/g,'').slice(0,16)"
                                placeholder="16 digit No KTP"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                            <input type="email" name="email_kedua" id="approve_email_kedua"
                                placeholder="email@example.com"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
                            <textarea name="alamat_kedua" id="approve_alamat_kedua" rows="2"
                                placeholder="Alamat lengkap pihak kedua..."
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></textarea>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-between items-center">
                    <button type="button" onclick="closeModal('modalApprove')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="button" onclick="goToApproveTab2()"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                        Selanjutnya <i class="fa fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- TAB 2: KETENTUAN --}}
            <div id="approveTabContent2" class="hidden">
                <div class="px-6 py-5 space-y-5">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa fa-exclamation text-amber-600 text-[10px]"></i>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ketentuan approve kontrak</h3>
                        </div>
                        <div class="rounded-xl bg-amber-50 border border-amber-200 px-5 py-4 space-y-2">
                            <p class="text-xs text-amber-700 font-semibold mb-2">Harap baca dan pahami ketentuan berikut sebelum melanjutkan:</p>
                            @foreach([
                                'Status kontrak akan langsung berubah ke <strong>Active</strong> dan tidak dapat dikembalikan ke pending.',
                                'Rental kendaraan akan otomatis dibuat untuk semua item dalam kontrak ini.',
                                'Status kendaraan terkait akan berubah menjadi <strong>Disewa</strong>.',
                                'Pastikan file yang diupload adalah dokumen final yang sudah ditandatangani oleh kedua pihak.',
                                'Data kontrak tidak dapat diubah setelah status berubah ke Active.',
                            ] as $i => $point)
                            <div class="flex items-start gap-2.5">
                                <span class="w-4 h-4 rounded-full bg-amber-200 text-amber-700 text-[9px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i+1 }}</span>
                                <p class="text-xs text-amber-800 leading-relaxed">{!! $point !!}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <label class="flex items-start gap-3 cursor-pointer group bg-gray-50 rounded-xl px-4 py-3 border border-gray-200 hover:border-green-300 hover:bg-green-50/30 transition-colors">
                        <input type="checkbox" id="approveTncCheck"
                            onchange="onApproveTncChange(this)"
                            class="w-4 h-4 rounded mt-0.5 accent-green-600 flex-shrink-0">
                        <span class="text-xs text-gray-600 group-hover:text-gray-800 leading-relaxed">
                            Saya telah membaca, memahami, dan menyetujui seluruh ketentuan di atas serta memastikan file yang diupload sudah benar dan final.
                        </span>
                    </label>
                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-between items-center">
                    <button type="button" onclick="switchApproveTab(1)"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                        <i class="fa fa-arrow-left text-xs"></i> Kembali
                    </button>
                    <button type="submit" id="approveSubmitBtn" disabled
                        class="inline-flex items-center gap-2 bg-gray-200 text-gray-400 text-sm font-semibold px-5 py-2 rounded-xl cursor-not-allowed transition-all">
                        <i class="fa fa-check text-xs"></i> Approve Kontrak
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════
     MODAL: EDIT KONTRAK (2 Tab)
═══════════════════════════════════════════════════ --}}
<div id="modalEdit" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-auto">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl max-h-[95vh] overflow-y-auto mx-4 my-6">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex gap-0">
                <button type="button" id="editTab1Btn" onclick="switchEditTab(1)"
                    class="px-4 py-2 text-sm font-semibold rounded-tl-lg border-b-2 border-blue-600 text-blue-600 bg-blue-50/50">
                    <i class="fa fa-file-alt mr-1 text-xs"></i> Data Kontrak
                </button>
                <button type="button" id="editTab2Btn" onclick="switchEditTab(2)"
                    class="px-4 py-2 text-sm font-semibold rounded-tr-lg border-b-2 border-transparent text-gray-400 hover:text-gray-600">
                    <i class="fa fa-list-alt mr-1 text-xs"></i> Ketentuan Pasal
                </button>
            </div>
            <button onclick="closeModal('modalEdit')"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">&times;</button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- ── TAB 1: Data Kontrak ── --}}
            <div id="editTab1" class="px-6 py-5 space-y-4">

                {{-- No Kontrak --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Kontrak</label>
                    <input type="text" name="no_kontrak" id="edit_no_kontrak"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                </div>

                {{-- Penawaran --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penawaran</label>
                    <select name="penawaran_id" id="edit_penawaran_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                        @foreach ($penawarans as $p)
                        <option value="{{ $p->id }}">{{ $p->no_penawaran }} – {{ $p->customer_name ?? $p->kepada }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Kontrak</label>
                        <input type="date" name="tanggal_kontrak" id="edit_tanggal_kontrak"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Perjanjian Pembayaran</label>
                        <input type="date" name="perjanjian_pembayaran" id="edit_perjanjian_pembayaran"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                </div>

                {{-- Pihak 1 --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pihak Pertama</label>
                        <input type="text" name="pihak_pertama" id="edit_pihak_pertama"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">No KTP Pihak Pertama</label>
                        <input type="text" name="contact_pertama" id="edit_contact_pertama"
                            inputmode="numeric" maxlength="16"
                            oninput="this.value=this.value.replace(/\D/g,'').slice(0,16)"
                            placeholder="16 digit No KTP"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                </div>

                {{-- Pihak 2 --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pelanggan</label>
                        <select name="jenis_pelanggan" id="edit_jenis_pelanggan"
                            onchange="togglePerwakilanFields('edit')"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="">-- Pilih --</option>
                            <option value="perorangan">Perorangan</option>
                            <option value="perusahaan">Perusahaan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pihak Kedua</label>
                        <input type="text" name="pihak_kedua" id="edit_pihak_kedua"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Pihak Kedua</label>
                        <input type="text" name="contact_kedua" id="edit_contact_kedua"
                            inputmode="numeric" maxlength="15"
                            oninput="this.value=this.value.replace(/\D/g,'').slice(0,15)"
                            placeholder="08xx-xxxx-xxxx"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    {{-- Field Perwakilan Perusahaan (conditional - hidden by default) --}}
                    <div id="edit_perwakilan_wrapper" class="col-span-2 grid grid-cols-2 gap-3 hidden">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Diwakili Oleh <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="perwakilan_pihak_kedua" id="edit_perwakilan_pihak_kedua"
                                placeholder="Nama perwakilan perusahaan..."
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Jabatan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="jabatan_pihak_kedua" id="edit_jabatan_pihak_kedua"
                                placeholder="Misal: Direktur Utama"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">No KTP Pihak Kedua</label>
                        <input type="text" name="no_ktp_kedua" id="edit_no_ktp_kedua"
                            inputmode="numeric" maxlength="16"
                            oninput="this.value=this.value.replace(/\D/g,'').slice(0,16)"
                            placeholder="16 digit No KTP"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Pihak Kedua</label>
                        <input type="email" name="email_kedua" id="edit_email_kedua"
                            placeholder="email@example.com"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat Pihak Kedua</label>
                        <textarea name="alamat_kedua" id="edit_alamat_kedua" rows="2"
                            placeholder="Alamat lengkap..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></textarea>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                    <select name="status" id="edit_status"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                        @foreach(['pending','approved','active','completed','selesai-belum lunas','rejected','expired','terminated'] as $st)
                        <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>


            </div>{{-- end Tab 1 --}}

            {{-- ── TAB 2: Ketentuan Pasal ── --}}
            <div id="editTab2" class="hidden px-6 py-5 space-y-4">

                <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa fa-pencil-alt text-amber-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-amber-800 mb-0.5">Editor Ketentuan Kontrak</p>
                        <p class="text-[11px] text-amber-700 leading-relaxed">
                            Perubahan langsung mengupdate <strong>draft PDF</strong> saat disimpan.
                            Edit isi ketentuan dalam dua bahasa sesuai kebutuhan.
                        </p>
                    </div>
                </div>

                {{-- Textarea ID --}}
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-bold text-blue-700 mb-1.5 uppercase tracking-wide">
                        <i class="fa fa-language"></i> Ketentuan Bahasa Indonesia
                    </label>
                    <textarea
                        name="ketentuan_id"
                        id="edit_ketentuan_id"
                        rows="22"
                        placeholder="Ketik isi ketentuan kontrak dalam Bahasa Indonesia..."
                        class="w-full border border-blue-200 rounded-xl px-4 py-3 text-xs font-mono leading-relaxed
                               focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                               resize-y bg-white text-gray-800"></textarea>
                </div>

                {{-- Textarea EN — readonly, auto-translate dari ID --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="flex items-center gap-1.5 text-xs font-bold text-green-700 uppercase tracking-wide">
                            <i class="fa fa-globe"></i> Contract Terms (English)
                        </label>
                        <span id="edit_translate_status" class="text-[10px] text-gray-400 flex items-center gap-1">
                            <i class="fa fa-magic text-green-400"></i> Auto-translate dari Bahasa Indonesia
                        </span>
                    </div>
                    <textarea
                        name="ketentuan_en"
                        id="edit_ketentuan_en"
                        rows="22"
                        readonly
                        placeholder="Terjemahan otomatis akan muncul di sini..."
                        class="w-full border border-green-200 rounded-xl px-4 py-3 text-xs font-mono leading-relaxed
                               resize-y bg-gray-50 text-gray-600 cursor-not-allowed"></textarea>
                </div>

            </div>{{-- end Tab 2 --}}

            {{-- Footer --}}
            <div class="flex justify-between gap-2 px-6 py-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalEdit')"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                    <i class="fa fa-save text-xs"></i> Update
                </button>
            </div>

        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     MODAL: DETAIL KONTRAK
═══════════════════════════════════════════════════ --}}
<div id="modalDetail" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl max-h-[92vh] overflow-y-auto mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">Detail Kontrak</h2>
            <button onclick="closeModal('modalDetail')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">&times;</button>
        </div>
        <div class="px-6 py-5 space-y-4 text-sm">
            <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                <div><p class="text-xs text-gray-400">No Kontrak</p><p id="d_no_kontrak" class="font-semibold text-gray-800 mt-0.5 font-mono">-</p></div>
                <div><p class="text-xs text-gray-400">Status</p><p id="d_status" class="mt-0.5">-</p></div>
                <div><p class="text-xs text-gray-400">Penawaran</p><p id="d_penawaran" class="text-gray-700 mt-0.5">-</p></div>
                <div><p class="text-xs text-gray-400">Perjanjian Pembayaran</p><p id="d_perjanjian" class="text-gray-700 mt-0.5">-</p></div>
                <div><p class="text-xs text-gray-400">Pihak Pertama</p><p id="d_pihak1" class="text-gray-700 mt-0.5">-</p></div>
                <div><p class="text-xs text-gray-400">Pihak Kedua</p><p id="d_pihak2" class="text-gray-700 mt-0.5">-</p></div>
            </div>

            {{-- Durasi & Periode --}}
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Durasi & Periode Sewa</p>
                <div class="flex gap-4 flex-wrap">
                    <div><p class="text-xs text-gray-400">Durasi</p><p id="d_durasi" class="font-bold text-indigo-700 text-sm mt-0.5">-</p></div>
                    <div><p class="text-xs text-gray-400">Mulai</p><p id="d_tgl_mulai" class="text-gray-700 text-sm mt-0.5">-</p></div>
                    <div><p class="text-xs text-gray-400">Selesai</p><p id="d_tgl_selesai" class="text-gray-700 text-sm mt-0.5">-</p></div>
                </div>
            </div>

            {{-- Kendaraan --}}
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Kendaraan</p>
                <div id="d_kendaraan_list" class="flex flex-col gap-2"></div>
            </div>

            {{-- File --}}
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">File Dokumen</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div id="d_file_draft_wrap"></div>
                    <div id="d_file_kontrak_wrap"></div>
                    <div id="d_file_persyaratan_wrap"></div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-100 px-6 py-4 flex justify-end">
            <button onclick="closeModal('modalDetail')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Tutup</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════════ --}}
<script>
    // ── Modal helpers ──────────────────────────────
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }

    // ── Fetch detail penawaran via AJAX ────────────
    let _penawaranCache = {};

    function fetchPenawaranDetail(penawaranId) {
        if (!penawaranId) {
            _currentItems = [];
            document.getElementById('preview_penawaran').classList.add('hidden');
            document.getElementById('selesai_per_kendaraan').classList.add('hidden');
            return;
        }
        if (_penawaranCache[penawaranId]) {
            renderPenawaranPreview(_penawaranCache[penawaranId]);
            return;
        }
        fetch(`/admin/kontrak/penawaran/${penawaranId}/detail`)
            .then(r => r.json())
            .then(data => {
                _penawaranCache[penawaranId] = data;
                renderPenawaranPreview(data);
            })
            .catch(() => console.error('Gagal fetch penawaran detail'));
    }

    function renderPenawaranPreview(data) {
        const items = data.items || [];
        _currentItems = items; // simpan ke cache untuk calcTanggalSelesai

        // ── Durasi per item ──────────────────────────
        // Konversi semua durasi ke hari untuk menentukan yang terpanjang
        const toDays = (val, sat) => {
            val = parseInt(val) || 1;
            if (sat === 'tahun') return val * 365;
            if (sat === 'bulan') return val * 30;
            return val; // hari
        };

        let maxDays   = 0;
        let maxVal    = 1;
        let maxSat    = 'bulan';

        const durasiListEl = document.getElementById('preview_durasi_list');
        if (items.length > 0) {
            durasiListEl.innerHTML = items.map(item => {
                if (!item.kendaraan) return '';
                const durVal = parseInt(item.durasi) || (data.periode || 1);
                const durSat = (item.satuan_durasi || 'bulan').toLowerCase();
                const days   = toDays(durVal, durSat);
                if (days > maxDays) { maxDays = days; maxVal = durVal; maxSat = durSat; }
                const label  = `${item.kendaraan.merk || '-'} ${item.kendaraan.nopol || ''}`.trim();
                const satLabel = durSat === 'tahun' ? 'Tahun' : durSat === 'bulan' ? 'Bulan' : 'Hari';
                return `<div class="flex items-center justify-between bg-white border border-blue-200 rounded-lg px-3 py-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="fa fa-car text-blue-400 text-xs flex-shrink-0"></i>
                        <span class="text-xs text-gray-700 truncate">${label}</span>
                    </div>
                    <span class="ml-3 flex-shrink-0 text-sm font-bold text-indigo-700">
                        ${durVal} <span class="text-xs font-normal text-indigo-500">${satLabel}</span>
                    </span>
                </div>`;
            }).join('');
        } else {
            durasiListEl.innerHTML = '<p class="text-xs text-gray-400">Tidak ada item</p>';
        }

        // Set hidden: pakai durasi terpanjang sebagai patokan kontrak
        document.getElementById('hidden_durasi_value').value = maxVal;
        document.getElementById('hidden_durasi_satuan').value = maxSat;

        // Kalkulasi ulang tanggal selesai jika perjanjian sudah diisi
        calcTanggalSelesai();

        // Total
        const total = data.total ? 'Rp ' + parseFloat(data.total).toLocaleString('id-ID', {minimumFractionDigits:0}) : '-';
        document.getElementById('preview_total').textContent = total;

        // Daftar kendaraan
        const listEl = document.getElementById('preview_kendaraan_list');
        const statusColors = {
            'tersedia': 'bg-green-100 text-green-700',
            'disewa':   'bg-blue-100 text-blue-700',
        };
        if (data.items && data.items.length > 0) {
            listEl.innerHTML = data.items.map(item => {
                const k = item.kendaraan;
                if (!k) return '';
                const stClass = statusColors[k.status_kendaraan] || 'bg-gray-100 text-gray-600';
                return `<div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2">
                    <i class="fa fa-car text-blue-400 text-xs flex-shrink-0"></i>
                    <div class="flex-1 min-w-0">
                        <span class="text-xs font-semibold text-gray-800">${k.merk || '-'}</span>
                        <span class="font-mono text-xs text-blue-600 ml-1">${k.nopol || '-'}</span>
                        ${k.warna ? `<span class="text-[10px] text-gray-400 ml-1">${k.warna}</span>` : ''}
                        ${k.tahun_pembuatan ? `<span class="text-[10px] text-gray-400 ml-1">(${k.tahun_pembuatan})</span>` : ''}
                    </div>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold flex-shrink-0 ${stClass}">
                        ${k.status_kendaraan || '-'}
                    </span>
                    <span class="text-[10px] text-gray-400 flex-shrink-0">Qty: ${item.qty || 1}</span>
                </div>`;
            }).join('');
        } else {
            listEl.innerHTML = '<p class="text-xs text-gray-400">Tidak ada kendaraan di penawaran ini</p>';
        }

        document.getElementById('preview_penawaran').classList.remove('hidden');
    }

    // ── Hitung tanggal selesai otomatis per kendaraan ──────────
    // Mulai = perjanjian_pembayaran (langsung, tanpa +1 hari)
    // Selesai = mulai + durasi per item
    let _currentItems = []; // cache items dari penawaran terakhir

    function calcTanggalSelesai() {
        const perjanjianStr = document.getElementById('create_perjanjian_pembayaran').value;
        const selesaiWrap   = document.getElementById('selesai_per_kendaraan');
        const selesaiList   = document.getElementById('list_selesai_kendaraan');

        if (!perjanjianStr || _currentItems.length === 0) {
            selesaiWrap.classList.add('hidden');
            return;
        }

        const opts = { day: 'numeric', month: 'long', year: 'numeric' };

        // Mulai = perjanjian (langsung)
        const mulai = new Date(perjanjianStr);

        const rows = _currentItems.map(item => {
            if (!item.kendaraan) return '';
            const durVal = parseInt(item.durasi) || 1;
            const durSat = (item.satuan_durasi || 'bulan').toLowerCase();
            const satLabel = durSat === 'tahun' ? 'Tahun' : durSat === 'bulan' ? 'Bulan' : 'Hari';
            const label  = `${item.kendaraan.merk || '-'} – ${item.kendaraan.nopol || ''}`.trim();

            let selesai = new Date(mulai);
            if (durSat === 'hari')       selesai.setDate(selesai.getDate() + durVal);
            else if (durSat === 'tahun') selesai.setFullYear(selesai.getFullYear() + durVal);
            else                         selesai.setMonth(selesai.getMonth() + durVal);

            return `<div class="flex items-center justify-between bg-indigo-50 border border-indigo-200 rounded-lg px-3 py-2">
                <div class="flex items-center gap-2 min-w-0">
                    <i class="fa fa-car text-indigo-400 text-xs flex-shrink-0"></i>
                    <span class="text-xs font-semibold text-gray-800 truncate">${label}</span>
                    <span class="text-[10px] text-indigo-500 flex-shrink-0">(${durVal} ${satLabel})</span>
                </div>
                <span class="ml-3 flex-shrink-0 text-xs font-bold text-indigo-700">
                    ${mulai.toLocaleDateString('id-ID', opts)} – ${selesai.toLocaleDateString('id-ID', opts)}
                </span>
            </div>`;
        }).filter(Boolean).join('');

        selesaiList.innerHTML = rows || '<p class="text-xs text-gray-400">Tidak ada kendaraan</p>';
        selesaiWrap.classList.remove('hidden');
    }

    // ── Approve modal ──────────────────────────────
    function openApproveModal(btn) {
        const id       = btn.dataset.id;
        const noKontrak = btn.dataset.no;
        document.getElementById('approveForm').action = `/admin/kontrak/${id}/approve`;
        document.getElementById('approve_subtitle').textContent = `Kontrak: ${noKontrak}`;
        // Reset ke tab 1
        switchApproveTab(1);
        // Reset file & checkbox
        const fi = document.getElementById('approve_file_input');
        if (fi) fi.value = '';
        const preview = document.getElementById('approveFilePreview');
        if (preview) preview.classList.add('hidden');
        const err = document.getElementById('approve_file_error');
        if (err) err.classList.add('hidden');
        const tnc = document.getElementById('approveTncCheck');
        if (tnc) tnc.checked = false;
        const submitBtn = document.getElementById('approveSubmitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.className = 'inline-flex items-center gap-2 bg-gray-300 text-gray-500 text-sm font-semibold px-5 py-2 rounded-xl cursor-not-allowed transition-colors';
        }
        // Pre-fill customer fields dari data-* attribute tombol
        const custNameEl = document.getElementById('approve_customer_name');
        if (custNameEl) custNameEl.value = btn.dataset.customer ?? '';
        const contactEl = document.getElementById('approve_contact_person');
        if (contactEl) contactEl.value = btn.dataset.contact ?? '';
        const noKtpEl = document.getElementById('approve_no_ktp_kedua');
        if (noKtpEl) noKtpEl.value = btn.dataset.ktp ?? '';
        const emailEl = document.getElementById('approve_email_kedua');
        if (emailEl) emailEl.value = btn.dataset.email ?? '';
        const jenisEl = document.getElementById('approve_jenis_pelanggan');
        if (jenisEl) jenisEl.value = btn.dataset.jenis ?? '';
        const alamatEl = document.getElementById('approve_alamat_kedua');
        if (alamatEl) alamatEl.value = btn.dataset.alamat ?? '';
        // Setup autosuggest customer di modal approve
        setupCustomerAutosuggest(
            document.getElementById('approve_customer_name'),
            document.getElementById('approve_customer_list'),
            document.getElementById('approve_no_ktp_kedua'),
            document.getElementById('approve_alamat_kedua'),
            document.getElementById('approve_jenis_pelanggan'),
            document.getElementById('approve_email_kedua'),
            document.getElementById('approve_contact_person')
        );
        openModal('modalApprove');
    }

    function switchCreateTab(tab) {
        const t1Btn = document.getElementById('createTab1Btn');
        const t2Btn = document.getElementById('createTab2Btn');
        const c1    = document.getElementById('createTabContent1');
        const c2    = document.getElementById('createTabContent2');
        if (tab === 1) {
            t1Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tl-lg';
            t2Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400 cursor-not-allowed rounded-tr-lg';
            c1.classList.remove('hidden'); c2.classList.add('hidden');
        } else {
            t1Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-blue-600 rounded-tl-lg';
            t2Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tr-lg';
            c1.classList.add('hidden'); c2.classList.remove('hidden');
        }
    }

    function goToCreateTab2() {
        // Validasi field wajib di Tab 1
        const required = ['create_penawaran_id', 'create_tanggal_kontrak', 'create_pihak_pertama', 'create_pihak_kedua'];
        let valid = true;
        required.forEach(id => {
            const el = document.getElementById(id);
            if (!el || !el.value.trim()) {
                if (el) el.classList.add('border-red-400');
                valid = false;
            } else {
                if (el) el.classList.remove('border-red-400');
            }
        });
        if (!valid) { alert('Harap lengkapi semua field yang wajib diisi terlebih dahulu.'); return; }

        // ── Auto-resolve placeholder kontrak-spesifik di textarea Tab 2 ──
        const taId = document.getElementById('create_ketentuan_id');
        const taEn = document.getElementById('create_ketentuan_en');

        // Hanya resolve jika textarea masih default (belum diedit manual)
        // Cek apakah nilai textarea sama dengan defaultKetentuan (artinya belum diubah user)
        const isIdDefault = taId && (taId.value === defaultKetentuan.id || taId.value.trim() === '');
        const isEnDefault = taEn && (taEn.value === defaultKetentuan.en || taEn.value.trim() === '');

        if (isIdDefault && taId) {
            taId.value = resolveKontrakPlaceholders(defaultKetentuan.id);
        }
        if (isEnDefault && taEn) {
            taEn.value = resolveKontrakPlaceholders(defaultKetentuan.en);
        }

        // Aktifkan tab 2
        const t2Btn = document.getElementById('createTab2Btn');
        if (t2Btn) { t2Btn.disabled = false; t2Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-blue-600 rounded-tr-lg'; }
        switchCreateTab(2);
    }

    function switchApproveTab(tab) {
        const tab1Btn  = document.getElementById('approveTab1Btn');
        const tab2Btn  = document.getElementById('approveTab2Btn');
        const content1 = document.getElementById('approveTabContent1');
        const content2 = document.getElementById('approveTabContent2');
        if (tab === 1) {
            tab1Btn.className  = 'px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tl-lg';
            tab2Btn.className  = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400 cursor-not-allowed rounded-tr-lg';
            content1.classList.remove('hidden');
            content2.classList.add('hidden');
        } else {
            tab1Btn.className  = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-blue-600 rounded-tl-lg';
            tab2Btn.className  = 'px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tr-lg';
            content1.classList.add('hidden');
            content2.classList.remove('hidden');
        }
    }

    function onApproveFileChange(input) {
        const preview  = document.getElementById('approveFilePreview');
        const nameEl   = document.getElementById('approveFileName');
        const errEl    = document.getElementById('approve_file_error');
        if (input.files && input.files[0]) {
            if (nameEl)  nameEl.textContent = input.files[0].name;
            if (preview) preview.classList.remove('hidden');
            if (errEl)   errEl.classList.add('hidden');
        } else {
            if (preview) preview.classList.add('hidden');
        }
    }

    function goToApproveTab2() {
        const fi  = document.getElementById('approve_file_input');
        const err = document.getElementById('approve_file_error');
        if (!fi || !fi.files || fi.files.length === 0) {
            if (err) err.classList.remove('hidden');
            fi?.classList.add('border-red-400');
            return;
        }
        if (err) err.classList.add('hidden');
        fi.classList.remove('border-red-400');
        // Aktifkan tab 2
        const tab2Btn = document.getElementById('approveTab2Btn');
        if (tab2Btn) tab2Btn.disabled = false;
        switchApproveTab(2);
    }

    function onApproveTncChange(cb) {
        const submitBtn = document.getElementById('approveSubmitBtn');
        if (!submitBtn) return;
        if (cb.checked) {
            submitBtn.disabled = false;
            submitBtn.className = 'inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-all cursor-pointer';
        } else {
            submitBtn.disabled = true;
            submitBtn.className = 'inline-flex items-center gap-2 bg-gray-200 text-gray-400 text-sm font-semibold px-5 py-2 rounded-xl cursor-not-allowed transition-all';
        }
    }

    // ══════════════════════════════════════════════════════
    // KETENTUAN EDITOR  —  default plain text dari PHP helper
    // ══════════════════════════════════════════════════════
    // Default ketentuan kontrak — placeholder sudah teresolusi dari setting
    const defaultKetentuan = @json($defaultKetentuan);

    // Ketentuan per kontrak — sudah fully resolved server-side (setting + kontrak-spesifik)
    // Key = kontrak ID, value = { id: '...', en: '...' }
    const resolvedKetentuan = @json($resolvedKetentuan);
    // DEBUG — hapus setelah dicek
    console.log('[resolvedKetentuan]', resolvedKetentuan);

    // Map placeholder → nilai nyata (untuk resolve teks lama di textarea Edit)
    @php
        $jsReplacements = [
            '{NAMA_PERUSAHAAN}'    => $setting?->nama_perusahaan    ?? 'PT. Anugerah Panca Yoga',
            '{ALAMAT_PERUSAHAAN}'  => $setting?->alamat             ?? 'Jl. Catur No. 16, Menteng Dalam, Tebet, Jakarta Selatan 12870',
            '{TELEPON_PERUSAHAAN}' => $setting?->telepon            ?? '021 - 83792927',
            '{FAX_PERUSAHAAN}'     => $setting?->fax                ?? '021 - 8354565',
            '{NAMA_BANK}'          => $setting?->nama_bank          ?? 'BCA',
            '{NO_REKENING}'        => $setting?->nomor_rekening     ?? '272-1420-878',
            '{ATAS_NAMA}'          => $setting?->atas_nama_rekening ?? $setting?->nama_perusahaan ?? 'PT. Anugerah Panca Yoga',
            '{PPN}'                => (string)($setting?->ppn_default ?? 11),
            '{PPH}'                => (string)($setting?->pph_default ?? 2),
        ];
    @endphp
    const settingReplacements = @json($jsReplacements);

    /**
     * Ganti semua placeholder setting dalam teks dengan nilai nyata.
     * Digunakan saat mengisi textarea Edit modal agar tidak tampil literal.
     */
    function resolveSettingPlaceholders(text) {
        if (!text) return text;
        let result = text;
        for (const [key, val] of Object.entries(settingReplacements)) {
            // Escape karakter { dan } agar RegExp aman
            const escaped = key.replace(/[{()}]/g, '\\$&');
            result = result.split(key).join(val);
        }
        return result;
    }

    /**
     * Format tanggal ke bahasa Indonesia atau English tanpa library eksternal.
     * @param {Date} date - Objek Date
     * @param {string} lang - 'id' atau 'en'
     * @returns {string} - Format: "26 Agustus 2026" atau "26 August 2026"
     */
    function formatTanggalIndonesia(date, lang = 'id') {
        if (!date || !(date instanceof Date) || isNaN(date)) return '';
        
        const bulanId = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const bulanEn = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        
        const day   = date.getDate();
        const month = lang === 'en' ? bulanEn[date.getMonth()] : bulanId[date.getMonth()];
        const year  = date.getFullYear();
        
        return `${day} ${month} ${year}`;
    }

    /**
     * Ganti placeholder kontrak-spesifik dalam teks dengan nilai dari form Create Tab 1.
     * Placeholder: {DURASI}, {TANGGAL_MULAI}, {TANGGAL_SELESAI}, {TANGGAL_MULAI_EN}, {TANGGAL_SELESAI_EN},
     *              {NAMA_PIHAK_KEDUA}, {ALAMAT_PIHAK_KEDUA}, {KONTAK_PIHAK_KEDUA}
     * @param {string} text - Teks yang mengandung placeholder kontrak-spesifik
     * @returns {string} - Teks dengan placeholder tergantikan
     */
    function resolveKontrakPlaceholders(text) {
        if (!text) return text;

        // Ambil data dari form Tab 1
        const tglKontrakStr = document.getElementById('create_tanggal_kontrak')?.value || '';
        const durasiValue   = parseInt(document.getElementById('hidden_durasi_value')?.value) || 1;
        const durasiSatuan  = (document.getElementById('hidden_durasi_satuan')?.value || 'bulan').toLowerCase();
        const namaPihak2    = document.getElementById('create_pihak_kedua')?.value || '';
        const alamatPihak2  = document.getElementById('create_alamat_kedua')?.value || '';
        const kontakPihak2  = document.getElementById('create_contact_kedua')?.value || '';

        if (!tglKontrakStr) {
            // Jika tanggal kontrak belum diisi, kembalikan teks original tanpa replace
            return text;
        }

        // Parse tanggal mulai
        const mulai = new Date(tglKontrakStr);
        if (isNaN(mulai)) return text;

        // Hitung tanggal selesai berdasarkan durasi (sama seperti logik calcTanggalSelesai)
        let selesai = new Date(mulai);
        if (durasiSatuan === 'hari') {
            selesai.setDate(selesai.getDate() + durasiValue);
        } else if (durasiSatuan === 'tahun') {
            selesai.setFullYear(selesai.getFullYear() + durasiValue);
        } else {
            // bulan (default)
            selesai.setMonth(selesai.getMonth() + durasiValue);
        }

        // Format tanggal
        const mulaiId   = formatTanggalIndonesia(mulai, 'id');
        const selesaiId = formatTanggalIndonesia(selesai, 'id');
        const mulaiEn   = formatTanggalIndonesia(mulai, 'en');
        const selesaiEn = formatTanggalIndonesia(selesai, 'en');

        // Format durasi string: "12 Bulan", "2 Tahun", "30 Hari"
        const satuanLabel = durasiSatuan === 'tahun' ? 'Tahun' : durasiSatuan === 'hari' ? 'Hari' : 'Bulan';
        const durasiStr   = `${durasiValue} ${satuanLabel}`;

        // Build replacement map
        const replacements = {
            '{DURASI}':             durasiStr,
            '{TANGGAL_MULAI}':      mulaiId,
            '{TANGGAL_SELESAI}':    selesaiId,
            '{TANGGAL_MULAI_EN}':   mulaiEn,
            '{TANGGAL_SELESAI_EN}': selesaiEn,
            '{NAMA_PIHAK_KEDUA}':   namaPihak2,
            '{ALAMAT_PIHAK_KEDUA}': alamatPihak2,
            '{KONTAK_PIHAK_KEDUA}': kontakPihak2,
        };

        let result = text;
        for (const [key, val] of Object.entries(replacements)) {
            result = result.split(key).join(val);
        }

        return result;
    }

    // ── Init Create modal Tab 2 ────────────────────────
    function initCreateKetentuan() {
        switchCreateTab(1);
        const t2Btn = document.getElementById('createTab2Btn');
        if (t2Btn) {
            t2Btn.disabled = true;
            t2Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400 cursor-not-allowed rounded-tr-lg';
        }
        // Isi textarea dengan default konten
        const taId = document.getElementById('create_ketentuan_id');
        const taEn = document.getElementById('create_ketentuan_en');
        if (taId) taId.value = defaultKetentuan.id;
        if (taEn) taEn.value = defaultKetentuan.en;
    }

    /**
     * Toggle visibility field perwakilan perusahaan berdasarkan jenis_pelanggan
     * @param {string} prefix - 'create', 'approve', atau 'edit'
     */
    function togglePerwakilanFields(prefix) {
        const jenisPelanggan = document.getElementById(`${prefix}_jenis_pelanggan`)?.value;
        const perwakilanWrapper = document.getElementById(`${prefix}_perwakilan_wrapper`);
        const perwakilanInput = document.getElementById(`${prefix}_perwakilan_pihak_kedua`);
        const jabatanInput = document.getElementById(`${prefix}_jabatan_pihak_kedua`);

        if (jenisPelanggan === 'perusahaan') {
            // Show fields dan set required
            if (perwakilanWrapper) perwakilanWrapper.classList.remove('hidden');
            if (perwakilanInput) perwakilanInput.setAttribute('required', 'required');
            if (jabatanInput) jabatanInput.setAttribute('required', 'required');
        } else {
            // Hide fields dan remove required
            if (perwakilanWrapper) perwakilanWrapper.classList.add('hidden');
            if (perwakilanInput) {
                perwakilanInput.removeAttribute('required');
                perwakilanInput.value = ''; // Clear value
            }
            if (jabatanInput) {
                jabatanInput.removeAttribute('required');
                jabatanInput.value = ''; // Clear value
            }
        }
    }

    // ── switchEditTab ──────────────────────────────
    function switchEditTab(tab) {
        const t1 = document.getElementById('editTab1');
        const t2 = document.getElementById('editTab2');
        const b1 = document.getElementById('editTab1Btn');
        const b2 = document.getElementById('editTab2Btn');

        if (tab === 1) {
            t1.classList.remove('hidden');
            t2.classList.add('hidden');
            b1.className = 'px-4 py-2 text-sm font-semibold rounded-tl-lg border-b-2 border-blue-600 text-blue-600 bg-blue-50/50';
            b2.className = 'px-4 py-2 text-sm font-semibold rounded-tr-lg border-b-2 border-transparent text-gray-400 hover:text-gray-600';
        } else {
            t2.classList.remove('hidden');
            t1.classList.add('hidden');
            b2.className = 'px-4 py-2 text-sm font-semibold rounded-tr-lg border-b-2 border-blue-600 text-blue-600 bg-blue-50/50';
            b1.className = 'px-4 py-2 text-sm font-semibold rounded-tl-lg border-b-2 border-transparent text-gray-400 hover:text-gray-600';
        }
    }

    // ── Edit modal ─────────────────────────────────
    function openEditModal(data) {
        document.getElementById('editForm').action = `/admin/kontrak/${data.id}`;
        document.getElementById('edit_penawaran_id').value          = data.penawaran_id ?? '';
        document.getElementById('edit_no_kontrak').value            = data.no_kontrak ?? '';
        document.getElementById('edit_tanggal_kontrak').value       = data.tanggal_kontrak ? data.tanggal_kontrak.substring(0,10) : '';
        document.getElementById('edit_perjanjian_pembayaran').value = data.perjanjian_pembayaran ? data.perjanjian_pembayaran.substring(0,10) : '';
        document.getElementById('edit_pihak_pertama').value         = data.pihak_pertama ?? '';
        document.getElementById('edit_contact_pertama').value       = data.contact_pertama ?? '';
        document.getElementById('edit_pihak_kedua').value           = data.pihak_kedua ?? '';
        document.getElementById('edit_contact_kedua').value         = data.contact_kedua ?? '';
        document.getElementById('edit_no_ktp_kedua').value          = data.no_ktp_kedua ?? '';
        document.getElementById('edit_email_kedua').value           = data.email_kedua ?? '';
        document.getElementById('edit_jenis_pelanggan').value       = data.jenis_pelanggan ?? '';
        document.getElementById('edit_alamat_kedua').value          = data.alamat_kedua ?? '';
        
        // ── Pre-fill perwakilan fields ──
        document.getElementById('edit_perwakilan_pihak_kedua').value = data.perwakilan_pihak_kedua ?? '';
        document.getElementById('edit_jabatan_pihak_kedua').value    = data.jabatan_pihak_kedua ?? '';
        
        const validStatuses = ['dibuat','pending','approved','active','rejected','expired','completed','terminated','selesai-belum lunas'];
        document.getElementById('edit_status').value = validStatuses.includes(data.status) ? data.status : 'pending';

        // ── Trigger toggle untuk show/hide field perwakilan based on jenis_pelanggan ──
        togglePerwakilanFields('edit');

        // ── Populate Tab 2: textarea ketentuan ──
        // Lookup dari resolvedKetentuan yang sudah di-resolve sepenuhnya di server (PHP)
        const taId = document.getElementById('edit_ketentuan_id');
        const taEn = document.getElementById('edit_ketentuan_en');
        const resolved = resolvedKetentuan[data.id] ?? null;
        if (taId) taId.value = resolved ? resolved.id : defaultKetentuan.id;
        if (taEn) taEn.value = resolved ? resolved.en : defaultKetentuan.en;

        switchEditTab(1);
        openModal('modalEdit');
    }

    /**
     * Konversi array pasal JSON ke plain text — versi JS dari KontrakHelper::pasalToPlainText()
     * Format: judul pasal di atas, poin bernomor, dipisah baris kosong antar pasal.
     */
    function convertPasalToPlainText(pasalArr) {
        const buildText = (lang) => {
            const lines = [];
            (pasalArr || []).forEach((pasal) => {
                const judul = lang === 'en'
                    ? (pasal.judul_en || pasal.judul_id || '')
                    : (pasal.judul_id || '');

                // Judul (bisa multiline, pisah \n)
                judul.split('\n').forEach(line => {
                    if (line.trim()) lines.push(line.trim().toUpperCase());
                });

                const poin = pasal.poin || [];
                const tipe = pasal.tipe || 'list';

                poin.forEach((p, i) => {
                    const text = lang === 'en' ? (p.en || p.id || '') : (p.id || '');
                    if (!text.trim()) return;
                    if (tipe === 'paragraf') {
                        lines.push(text);
                    } else if (tipe === 'sublist') {
                        lines.push(String.fromCharCode(97 + i) + '. ' + text);
                    } else {
                        lines.push((i + 1) + '. ' + text);
                    }
                });

                lines.push(''); // baris kosong antar pasal
            });

            // Buang trailing baris kosong
            while (lines.length && lines[lines.length - 1].trim() === '') lines.pop();
            return lines.join('\n');
        };

        return { id: buildText('id'), en: buildText('en') };
    }

    // ── Detail modal ───────────────────────────────
    function openDetailModal(data) {
        const fmt = (d) => {
            if (!d) return '-';
            const dt = new Date(d);
            return dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        };
        const fileLink = (path, label) => {
            if (!path) return `<p class="text-xs text-gray-400 mb-1">${label}</p><span class="text-xs text-gray-400">Tidak ada file</span>`;
            const parts  = path.split('/');
            const raw    = parts[parts.length - 1];
            const idx    = raw.indexOf('_');
            const fname  = idx !== -1 ? raw.substring(idx + 1) : raw;
            const isRed  = label.includes('Draft') ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200';
            return `<p class="text-xs text-gray-400 mb-1">${label}</p>
                <a href="/${path}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium ${isRed} border transition-colors max-w-full hover:opacity-80">
                    <i class="fa fa-file-pdf text-xs flex-shrink-0"></i>
                    <span class="truncate">${fname}</span>
                </a>`;
        };

        document.getElementById('d_no_kontrak').textContent   = data.no_kontrak ?? '-';
        document.getElementById('d_penawaran').textContent    = data.penawaran?.no_penawaran ?? '-';
        document.getElementById('d_perjanjian').textContent   = fmt(data.perjanjian_pembayaran);
        document.getElementById('d_pihak1').textContent       = data.pihak_pertama ?? '-';
        document.getElementById('d_pihak2').textContent       = data.pihak_kedua ?? '-';
        document.getElementById('d_durasi').textContent       = data.durasi_value ? `${data.durasi_value} ${data.durasi_satuan}` : '-';
        document.getElementById('d_tgl_mulai').textContent    = fmt(data.tanggal_kontrak);
        document.getElementById('d_tgl_selesai').textContent  = fmt(data.tanggal_selesai);

        // Status badge
        const statusColors = {
            'active':              'bg-green-100 text-green-700',
            'approved':            'bg-indigo-100 text-indigo-700',
            'completed':           'bg-blue-100 text-blue-700',
            'pending':             'bg-yellow-100 text-yellow-700',
            'rejected':            'bg-red-100 text-red-600',
            'expired':             'bg-gray-100 text-gray-600',
            'terminated':          'bg-gray-800 text-white',
            'selesai-belum lunas': 'bg-orange-100 text-orange-700',
        };
        const st = data.status ?? '-';
        const stClass = statusColors[st] || 'bg-gray-100 text-gray-600';
        document.getElementById('d_status').innerHTML =
            `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${stClass}">${st.charAt(0).toUpperCase() + st.slice(1)}</span>`;

        // Kendaraan
        const kList = document.getElementById('d_kendaraan_list');
        const items = data.penawaran?.items ?? [];
        if (items.length === 0) {
            kList.innerHTML = '<p class="text-xs text-gray-400">Tidak ada kendaraan</p>';
        } else {
            kList.innerHTML = items.map(item => {
                const k = item.kendaraan;
                if (!k) return '';
                const kStColors = {'tersedia':'bg-green-100 text-green-700','disewa':'bg-blue-100 text-blue-700','selesai-belum lunas':'bg-orange-100 text-orange-700'};
                const kStClass = kStColors[k.status_kendaraan] || 'bg-gray-100 text-gray-600';
                return `<div class="flex items-center gap-2 p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                    <i class="fa fa-car text-gray-400 text-xs"></i>
                    <span class="text-xs font-semibold text-gray-800">${k.merk ?? '-'}</span>
                    <span class="font-mono text-xs text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">${k.nopol ?? '-'}</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold ${kStClass}">${k.status_kendaraan ?? '-'}</span>
                </div>`;
            }).join('');
        }

        // File
        document.getElementById('d_file_draft_wrap').innerHTML        = fileLink(data.file_draft,         'Draft PDF');
        document.getElementById('d_file_kontrak_wrap').innerHTML      = fileLink(data.file_kontrak,       'File Kontrak TTD');
        document.getElementById('d_file_persyaratan_wrap').innerHTML  = fileLink(data.file_persyaratan,   'File Persyaratan');

        openModal('modalDetail');
    }

    // ── Customer autosuggest (tambah kontrak & approve) ──────────
    const _customerSearchUrl = "{{ route('penawaran.customer-search') }}";
    let _customerTimer = null;

    function setupCustomerAutosuggest(inputEl, listEl, noKtpEl, alamatEl, jenisEl, emailEl, contactEl) {
        if (!inputEl || !listEl) return;
        inputEl.addEventListener('input', function () {
            clearTimeout(_customerTimer);
            const q = this.value.trim();
            if (q.length < 1) { listEl.classList.add('hidden'); return; }
            _customerTimer = setTimeout(() => {
                fetch(_customerSearchUrl + '?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(results => {
                        listEl.innerHTML = '';
                        if (!results.length) { listEl.classList.add('hidden'); return; }
                        results.forEach(member => {
                            const li = document.createElement('li');
                            li.className = 'px-3 py-2 cursor-pointer hover:bg-blue-50 text-gray-700 text-sm';
                            li.textContent = member.nama_pelanggan;
                            li.addEventListener('mousedown', function (e) {
                                e.preventDefault();
                                inputEl.value = member.nama_pelanggan;
                                if (noKtpEl)   noKtpEl.value   = member.no_ktp          ?? '';
                                if (alamatEl)  alamatEl.value  = member.alamat           ?? '';
                                if (jenisEl)   jenisEl.value   = member.jenis_pelanggan  ?? '';
                                if (emailEl)   emailEl.value   = member.email_pelanggan  ?? '';
                                if (contactEl) contactEl.value = member.kontak_pelanggan ?? '';
                                listEl.classList.add('hidden');
                            });
                            listEl.appendChild(li);
                        });
                        listEl.classList.remove('hidden');
                    })
                    .catch(() => listEl.classList.add('hidden'));
            }, 300);
        });
        inputEl.addEventListener('blur', () => setTimeout(() => listEl.classList.add('hidden'), 200));
        inputEl.addEventListener('focus', function () {
            if (this.value.trim().length > 0) this.dispatchEvent(new Event('input'));
        });
    }

    // Aktifkan autosuggest untuk form tambah kontrak
    setupCustomerAutosuggest(
        document.getElementById('create_customer_name'),
        document.getElementById('create_customer_list'),
        document.getElementById('create_no_ktp_kedua'),
        document.getElementById('create_alamat_kedua'),
        document.getElementById('create_jenis_pelanggan'),
        document.getElementById('create_email_kedua'),
        document.getElementById('create_contact_kedua')
    );

    // ── AUTO-TRANSLATE: ID → EN via DeepL proxy ────────────────
    const _translateUrl = "{{ route('translate') }}";
    const _csrfToken    = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    function setTranslateStatus(prefix, msg, color = 'text-gray-400') {
        const el = document.getElementById(prefix + '_translate_status');
        if (el) {
            el.innerHTML = msg;
            el.className = `text-[10px] ${color} flex items-center gap-1`;
        }
    }

    function setupAutoTranslate(idTextareaId, enTextareaId, statusPrefix) {
        const taId = document.getElementById(idTextareaId);
        const taEn = document.getElementById(enTextareaId);
        if (!taId || !taEn) return;

        let translateTimer = null;

        taId.addEventListener('input', function () {
            clearTimeout(translateTimer);
            setTranslateStatus(statusPrefix,
                '<i class="fa fa-clock text-yellow-400"></i> Menunggu selesai mengetik...',
                'text-yellow-500'
            );

            // Debounce 1.5 detik setelah berhenti mengetik
            translateTimer = setTimeout(async () => {
                const text = taId.value;

                if (!text.trim()) {
                    taEn.value = '';
                    setTranslateStatus(statusPrefix,
                        '<i class="fa fa-magic text-green-400"></i> Auto-translate dari Bahasa Indonesia',
                        'text-gray-400'
                    );
                    return;
                }

                setTranslateStatus(statusPrefix,
                    '<i class="fa fa-spinner fa-spin text-blue-400"></i> Menerjemahkan dengan DeepL...',
                    'text-blue-500'
                );

                try {
                    const res = await fetch(_translateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': _csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            text:        text,
                            source_lang: 'ID',
                            target_lang: 'EN-GB',
                        }),
                    });

                    const data = await res.json();

                    if (!res.ok || data.error) {
                        setTranslateStatus(statusPrefix,
                            `<i class="fa fa-exclamation-triangle text-red-400"></i> ${data.error ?? 'Gagal menerjemahkan'}`,
                            'text-red-500'
                        );
                        return;
                    }

                    taEn.value = data.translated ?? '';
                    setTranslateStatus(statusPrefix,
                        '<i class="fa fa-check text-green-500"></i> Terjemahan DeepL selesai',
                        'text-green-600'
                    );

                } catch (e) {
                    setTranslateStatus(statusPrefix,
                        '<i class="fa fa-exclamation-triangle text-red-400"></i> Gagal menghubungi server',
                        'text-red-500'
                    );
                }
            }, 1500);
        });
    }

    // Aktifkan auto-translate untuk create dan edit
    setupAutoTranslate('create_ketentuan_id', 'create_ketentuan_en', 'create');
    setupAutoTranslate('edit_ketentuan_id',   'edit_ketentuan_en',   'edit');
</script>
@endsection
