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
                            {{-- Draft PDF badge --}}
                            @if($k->file_draft)
                            <a href="/{{ $k->file_draft }}" target="_blank"
                                class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-600 border border-red-200 hover:bg-red-100">
                                <i class="fa fa-file-pdf text-[10px]"></i> Draft
                            </a>
                            @endif
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

                                {{-- Upload & Approve: hanya status pending --}}
                                @if($k->status === 'pending')
                                <button onclick="openApproveModal({{ $k->id }}, '{{ $k->no_kontrak }}')"
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
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa fa-check-circle text-blue-600"></i> Approve Kontrak
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">Pilih penawaran — data kendaraan & durasi otomatis terisi</p>
            </div>
            <button onclick="closeModal('modalCreate')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">&times;</button>
        </div>
        <form action="{{ route('kontrak.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf

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

            {{-- Data Customer Pihak Kedua --}}
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
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pelanggan</label>
                    <select name="jenis_pelanggan" id="create_jenis_pelanggan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih --</option>
                        <option value="perorangan">Perorangan</option>
                        <option value="perusahaan">Perusahaan</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat Pihak Kedua</label>
                    <textarea name="alamat_kedua" id="create_alamat_kedua" rows="2"
                        placeholder="Alamat lengkap..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></textarea>
                </div>
            {{-- ── Ketentuan Asuransi Pasal 4 Ayat 7 ── --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-200">
                    <div>
                        <p class="text-xs font-semibold text-gray-700">Ketentuan Asuransi <span class="font-normal text-gray-400">(Pasal 4 Ayat 7)</span></p>
                        <p class="text-[10px] text-gray-400">Tiap poin dua bahasa: Indonesia & Inggris. Bisa tambah/hapus.</p>
                    </div>
                    <button type="button" onclick="addCreateKetentuanRow()"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 border border-blue-300 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg">
                        <i class="fa fa-plus text-[10px]"></i> Tambah Poin
                    </button>
                </div>
                <div id="createKetentuanList" class="divide-y divide-gray-100 max-h-64 overflow-y-auto"></div>
            </div>

            <div class="rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 flex items-start gap-2">
                <i class="fa fa-info-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                <p class="text-xs text-blue-700">
                    Setelah disimpan, <strong>draft PDF kontrak</strong> akan otomatis di-generate.
                    Silakan download, tandatangani, lalu upload kembali untuk <strong>Approve</strong>.
                </p>
            </div>

            <div class="rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 flex items-start gap-2">

            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalCreate')"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                    <i class="fa fa-save text-xs"></i> Simpan & Generate Draft
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     MODAL: UPLOAD & APPROVE
═══════════════════════════════════════════════════ --}}
<div id="modalApprove" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-800">Upload File & Approve</h2>
                <p class="text-xs text-gray-400 mt-0.5" id="approve_subtitle">Upload file kontrak yang sudah ditandatangani</p>
            </div>
            <button onclick="closeModal('modalApprove')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">&times;</button>
        </div>
        <form id="approveForm" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 flex items-start gap-2">
                <i class="fa fa-info-circle text-green-600 mt-0.5 flex-shrink-0"></i>
                <p class="text-xs text-green-700">
                    Upload file kontrak hasil tanda tangan kedua pihak. Setelah diupload, status akan berubah ke
                    <strong>Active</strong> dan rental kendaraan otomatis dibuat.
                </p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    File Kontrak TTD <span class="text-red-500">*</span>
                </label>
                <input type="file" name="file_kontrak" accept=".pdf"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" required>
                <p class="text-[10px] text-gray-400 mt-1">Format: PDF saja. Maks 10MB.</p>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalApprove')"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                    <i class="fa fa-check text-xs"></i> Approve Kontrak
                </button>
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
            <h2 class="text-base font-bold text-gray-800">Edit Kontrak</h2>
            <button onclick="closeModal('modalEdit')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">&times;</button>
        </div>

        {{-- Navtab --}}
        <div class="flex border-b border-gray-200 px-6 pt-3 gap-1">
            <button type="button" id="editTab1Btn"
                onclick="switchEditTab(1)"
                class="edit-tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg border border-b-0 border-transparent text-blue-600 border-blue-300 bg-blue-50">
                <i class="fa fa-file-alt mr-1 text-xs"></i> Data Kontrak
            </button>
            <button type="button" id="editTab2Btn"
                onclick="switchEditTab(2)"
                class="edit-tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg border border-b-0 border-transparent text-gray-400 hover:text-gray-600">
                <i class="fa fa-shield-alt mr-1 text-xs"></i> Ketentuan Asuransi
            </button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- ── TAB 1: Data Kontrak ── --}}
            <div id="editTab1" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Kontrak</label>
                    <input type="text" name="no_kontrak" id="edit_no_kontrak"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penawaran</label>
                    <select name="penawaran_id" id="edit_penawaran_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                        @foreach ($penawarans as $p)
                        <option value="{{ $p->id }}">{{ $p->no_penawaran }}</option>
                        @endforeach
                    </select>
                </div>
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
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pihak Kedua</label>
                        <input type="text" name="pihak_kedua" id="edit_pihak_kedua"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Pihak 2</label>
                        <input type="text" name="contact_kedua" id="edit_contact_kedua"
                            inputmode="numeric" maxlength="15"
                            oninput="this.value=this.value.replace(/\D/g,'').slice(0,15)"
                            placeholder="08xx-xxxx-xxxx"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                    <select name="status" id="edit_status"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                        @foreach(['pending','approved','active','completed','selesai-belum lunas','rejected','expired','terminated'] as $st)
                        <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="text-xs text-gray-400">File baru akan menggantikan file lama (opsional)</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Kontrak</label>
                        <input type="file" name="file_kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Persyaratan</label>
                        <input type="file" name="file_persyaratan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            </div>
            {{-- Data Customer Pihak Kedua --}}
            <div class="grid grid-cols-2 gap-3">
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
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pelanggan</label>
                    <select name="jenis_pelanggan" id="edit_jenis_pelanggan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih --</option>
                        <option value="perorangan">Perorangan</option>
                        <option value="perusahaan">Perusahaan</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat Pihak Kedua</label>
                    <textarea name="alamat_kedua" id="edit_alamat_kedua" rows="2"
                        placeholder="Alamat lengkap..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></textarea>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                <select name="status" id="edit_status"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" required>
                    @foreach(['pending','approved','active','completed','selesai-belum lunas','rejected','expired','terminated'] as $st)
                    <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-xs text-gray-400">File baru akan menggantikan file lama (opsional)</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">File Kontrak</label>
                    <input type="file" name="file_kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">

            {{-- ── TAB 2: Ketentuan Asuransi ── --}}
            <div id="editTab2" class="hidden px-6 py-5 space-y-4">
                <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 flex items-start gap-2">
                    <i class="fa fa-info-circle text-amber-500 mt-0.5 flex-shrink-0"></i>
                    <p class="text-xs text-amber-700">
                        Perubahan ketentuan ini akan langsung mengupdate <strong>draft PDF kontrak</strong> saat disimpan.
                        Urutan poin otomatis menjadi a, b, c, ... sesuai posisi.
                    </p>
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-700">
                        Ketentuan Asuransi <span class="font-normal text-gray-400">(Pasal 4 Ayat 7)</span>
                    </p>
                    <button type="button" onclick="addEditKetentuanRow()"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 border border-blue-300 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg">
                        <i class="fa fa-plus text-[10px]"></i> Tambah Poin
                    </button>
                </div>

                <div id="editKetentuanList" class="space-y-2 max-h-72 overflow-y-auto pr-1"></div>
            </div>

            {{-- Footer tombol --}}
            <div class="flex justify-end gap-2 px-6 py-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('modalEdit')"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
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
    function openApproveModal(id, noKontrak) {
        document.getElementById('approveForm').action = `/admin/kontrak/${id}/approve`;
        document.getElementById('approve_subtitle').textContent = `Kontrak: ${noKontrak}`;
        openModal('modalApprove');
    }

    // ── Ketentuan helpers (shared) ─────────────────
    const defaultKetentuan = [
        {
            id: 'Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab PIHAK KEDUA.',
            en: 'Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per occurrence. Exceeding amount becomes the SECOND PARTY responsibility.'
        },
        {
            id: 'Dalam hal kecelakaan/kehilangan/pencurian mobil yang disewa, dimana kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih menjadi tanggung jawab PIHAK KEDUA.',
            en: 'In the event of damage/loss/theft of the car, hence the claim is rejected by the insurance company and in effect will hold responsible fully to the cost effect of occurrence.'
        },
        {
            id: 'Selama proses pengurusan pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar 10% dari uang pertanggungan yang tertera di polis.',
            en: 'While undergoing the process of insurance claim for the loss/theft of the car, The SECOND PARTY will not receive replacement car and responsible to pay own risk claim of 10% of the insured sum that is written in the insurance policy.'
        },
        {
            id: 'Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair, PIHAK KEDUA berkewajiban membayar biaya resiko sendiri.',
            en: 'In the event of accident that requires body repair, the SECOND PARTY is obligated to pay own risk.'
        },
    ];

    const alphaLabel = i => String.fromCharCode(97 + i);

    function makeKetentuanRow(listId, namePrefix, idx, valId, valEn) {
        const label = alphaLabel(idx);
        const row   = document.createElement('div');
        row.className = 'ketentuan-row border border-gray-100 rounded-xl p-3 bg-gray-50';
        row.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="ktn-label text-[10px] font-bold text-gray-500 uppercase tracking-wide">Poin ${label.toUpperCase()}</span>
                <button type="button" onclick="removeKetentuanRow(this,'${listId}')"
                    class="text-red-400 hover:text-red-600 text-xs w-5 h-5 flex items-center justify-center rounded hover:bg-red-50">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="space-y-1.5">
                <div class="flex items-start gap-2">
                    <span class="text-[10px] font-semibold text-blue-500 w-5 mt-2 shrink-0">ID</span>
                    <textarea name="${namePrefix}[${idx}][id]" rows="2" placeholder="Teks ketentuan Bahasa Indonesia..."
                        class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-blue-300 focus:border-blue-300 resize-none bg-white">${valId}</textarea>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-[10px] font-semibold text-green-500 w-5 mt-2 shrink-0">EN</span>
                    <textarea name="${namePrefix}[${idx}][en]" rows="2" placeholder="English provision text..."
                        class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-green-300 focus:border-green-300 resize-none bg-white">${valEn}</textarea>
                </div>
            </div>`;
        return row;
    }

    function reorderLabels(listId) {
        document.querySelectorAll(`#${listId} .ketentuan-row`).forEach((row, i) => {
            const lbl = row.querySelector('.ktn-label');
            if (lbl) lbl.textContent = `Poin ${alphaLabel(i).toUpperCase()}`;
        });
    }

    function removeKetentuanRow(btn, listId) {
        btn.closest('.ketentuan-row').remove();
        reorderLabels(listId);
    }

    // ── Create modal: ketentuan ─────────────────────
    let createKtnIdx = 0;

    function initCreateKetentuan() {
        const list = document.getElementById('createKetentuanList');
        list.innerHTML = '';
        createKtnIdx = 0;
        defaultKetentuan.forEach(p => addCreateKetentuanRow(p.id, p.en));
    }

    function addCreateKetentuanRow(valId = '', valEn = '') {
        const list = document.getElementById('createKetentuanList');
        const idx  = createKtnIdx++;
        const row  = makeKetentuanRow('createKetentuanList', 'ketentuan', idx, valId, valEn);
        // wrap tiap baris dengan border bawah tipis
        row.classList.add('mx-3', 'my-2');
        list.appendChild(row);
        reorderLabels('createKetentuanList');
    }

    // ── Edit modal: ketentuan ───────────────────────
    let editKtnIdx = 0;

    function addEditKetentuanRow(valId = '', valEn = '') {
        const list = document.getElementById('editKetentuanList');
        const idx  = editKtnIdx++;
        const row  = makeKetentuanRow('editKetentuanList', 'ketentuan', idx, valId, valEn);
        row.classList.add('mx-1');
        list.appendChild(row);
        reorderLabels('editKetentuanList');
    }

    // ── switchEditTab ───────────────────────────────
    function switchEditTab(tab) {
        const t1 = document.getElementById('editTab1');
        const t2 = document.getElementById('editTab2');
        const b1 = document.getElementById('editTab1Btn');
        const b2 = document.getElementById('editTab2Btn');

        const activeClass   = ['text-blue-600', 'border-blue-300', 'bg-blue-50', 'border'];
        const inactiveClass = ['text-gray-400', 'hover:text-gray-600'];

        if (tab === 1) {
            t1.classList.remove('hidden');
            t2.classList.add('hidden');
            b1.classList.add(...activeClass);
            b1.classList.remove(...inactiveClass);
            b2.classList.remove(...activeClass);
            b2.classList.add(...inactiveClass);
        } else {
            t2.classList.remove('hidden');
            t1.classList.add('hidden');
            b2.classList.add(...activeClass);
            b2.classList.remove(...inactiveClass);
            b1.classList.remove(...activeClass);
            b1.classList.add(...inactiveClass);
        }
    }

    // ── Edit modal ─────────────────────────────────
    function openEditModal(data) {
        document.getElementById('editForm').action = `/admin/kontrak/${data.id}`;
        document.getElementById('edit_penawaran_id').value         = data.penawaran_id ?? '';
        document.getElementById('edit_no_kontrak').value           = data.no_kontrak ?? '';
        document.getElementById('edit_tanggal_kontrak').value      = data.tanggal_kontrak ? data.tanggal_kontrak.substring(0, 10) : '';
        document.getElementById('edit_perjanjian_pembayaran').value= data.perjanjian_pembayaran ? data.perjanjian_pembayaran.substring(0, 10) : '';
        document.getElementById('edit_pihak_pertama').value        = data.pihak_pertama ?? '';
        document.getElementById('edit_contact_pertama').value      = data.contact_pertama ?? '';
        document.getElementById('edit_pihak_kedua').value          = data.pihak_kedua ?? '';
        document.getElementById('edit_contact_kedua').value        = data.contact_kedua ?? '';
        document.getElementById('edit_no_ktp_kedua').value         = data.no_ktp_kedua ?? '';
        document.getElementById('edit_email_kedua').value          = data.email_kedua ?? '';
        document.getElementById('edit_jenis_pelanggan').value      = data.jenis_pelanggan ?? '';
        document.getElementById('edit_alamat_kedua').value         = data.alamat_kedua ?? '';
        document.getElementById('edit_penawaran_id').value          = data.penawaran_id ?? '';
        document.getElementById('edit_no_kontrak').value            = data.no_kontrak ?? '';
        document.getElementById('edit_tanggal_kontrak').value       = data.tanggal_kontrak ? data.tanggal_kontrak.substring(0, 10) : '';
        document.getElementById('edit_perjanjian_pembayaran').value = data.perjanjian_pembayaran ? data.perjanjian_pembayaran.substring(0, 10) : '';
        document.getElementById('edit_pihak_pertama').value         = data.pihak_pertama ?? '';
        document.getElementById('edit_contact_pertama').value       = data.contact_pertama ?? '';
        document.getElementById('edit_pihak_kedua').value           = data.pihak_kedua ?? '';
        document.getElementById('edit_contact_kedua').value         = data.contact_kedua ?? '';
        const validStatuses = ['dibuat','pending','approved','active','rejected','expired','completed','terminated','selesai-belum lunas'];
        document.getElementById('edit_status').value = validStatuses.includes(data.status) ? data.status : 'pending';

        // Populate Tab 2: ketentuan asuransi
        const editList = document.getElementById('editKetentuanList');
        editList.innerHTML = '';
        editKtnIdx = 0;
        const existing = data.ketentuan_asuransi;
        if (existing && existing.length > 0) {
            existing.forEach(p => addEditKetentuanRow(p.id ?? '', p.en ?? ''));
        } else {
            defaultKetentuan.forEach(p => addEditKetentuanRow(p.id, p.en));
        }

        // Reset ke Tab 1 saat buka
        switchEditTab(1);
        openModal('modalEdit');
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
</script>
@endsection
