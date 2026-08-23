@extends('admin.layouts.app')

@section('title', 'Data Summary')

@section('content')
    <div class="space-y-6 p-5">

        {{-- ALERT --}}
        @if (session('success'))
            <div
                class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <i class="fa fa-check-circle text-green-500"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <i class="fa fa-exclamation-circle text-red-500"></i> {{ session('error') }}
            </div>
        @endif

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            {{-- Judul --}}
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Summary Invoice</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    Rekap status pembayaran seluruh invoice
                </p>
            </div>

            {{-- Tombol --}}
            <div class="flex flex-wrap items-center gap-2">

                <a href="{{ route('summary.pdf') }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                    <i class="fa fa-file-pdf"></i>
                    Export PDF
                </a>

                <a href="{{ route('summary.export.excel', request()->query()) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-green-600 text-green-600 rounded-lg bg-transparent hover:bg-green-600 hover:text-white transition-colors">
                    <i class="fa fa-file-excel"></i>
                    Export Excel
                </a>

                {{-- Task 4: Tombol Tambah Summary dihilangkan — summary dibuat otomatis dari Invoice --}}

            </div>

        </div>

        {{-- NAV TABS --}}
        <div class="border-b border-gray-200">
            <nav class="flex gap-0 -mb-px overflow-x-auto">
                @php
                    $navItems = [
                        ['label' => 'Summary', 'url' => '/admin/summary', 'icon' => 'bi bi-bar-chart-line'],
                        ['label' => 'Penawaran', 'url' => '/admin/penawaran', 'icon' => 'bi bi-file-earmark-richtext'],
                        ['label' => 'Kontrak', 'url' => '/admin/kontrak', 'icon' => 'bi bi-file-earmark-lock'],
                        ['label' => 'Invoice', 'url' => '/admin/invoices', 'icon' => 'bi bi-receipt-cutoff'],
                        ['label' => 'Payments', 'url' => '/admin/payments', 'icon' => 'bi bi-credit-card-2-front'],
                        ['label' => 'Reminders', 'url' => '/admin/reminders', 'icon' => 'bi bi-bell'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php
                        $isActive =
                            request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*');
                    @endphp
                    <a href="{{ $item['url'] }}"
                        class="flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
                            {{ $isActive
                                ? 'border-blue-600 text-blue-600 bg-blue-50/50'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <i class="{{ $item['icon'] }}"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>









        {{-- CHART FILTER --}}
        <x-chart-filter id="summaryChartFilter" defaultFilter="year" :showCustomRange="true" />

        {{-- CHART CONTAINER --}}
        <x-chart-container
            id="summaryChartContainer"
            layout="stacked"
            pieTitle="Distribusi Status Pembayaran" pieId="summaryPieChart"
            barTitle="Total Tagihan per Periode" barId="summaryBarChart"
            lineTitle="Trend Tagihan" lineId="summaryLineChart"
            :showStats="true" :statsData="[]"
        />

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Total Data</p>
                <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['total'] }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Paid</p>
                <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $stats['paid'] }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Partial</p>
                <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $stats['partial'] }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Unpaid</p>
                <h2 class="text-3xl font-bold text-red-500 mt-2">{{ $stats['unpaid'] }}</h2>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

            {{-- SEARCH + TOGGLE KOLOM --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" class="flex gap-2 flex-1 flex-wrap">
                    <div class="relative flex-1 min-w-[180px]">
                        <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari invoice, customer, tipe..."
                            class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    {{-- Filter Status --}}
                    <select name="status"
                        class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 bg-white">
                        <option value="">Semua Status</option>
                        <option value="Paid"    {{ request('status') === 'Paid'    ? 'selected' : '' }}>Paid</option>
                        <option value="Partial" {{ request('status') === 'Partial' ? 'selected' : '' }}>Partial</option>
                        <option value="Unpaid"  {{ request('status') === 'Unpaid'  ? 'selected' : '' }}>Unpaid</option>
                    </select>
                    <button class="bg-gray-800 text-white text-xs px-4 py-1.5 rounded-lg">Cari</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('summary.index') }}"
                            class="text-xs px-3 py-1.5 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50">
                            Reset
                        </a>
                    @endif
                </form>

                {{-- TOGGLE KOLOM --}}
                <div class="relative" id="colToggleWrap">
                    <button onclick="toggleColDropdown()"
                        class="flex items-center gap-1.5 border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 bg-white hover:bg-gray-50 whitespace-nowrap">
                        <i class="bi bi-layout-three-columns"></i> Kolom
                        <i class="bi bi-chevron-down text-[10px]"></i>
                    </button>
                    <div id="colDropdown"
                        class="hidden absolute right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-3 min-w-[160px] max-h-64 overflow-y-auto">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase mb-2">Tampilkan Kolom</p>
                        @foreach ([
                            'col-invoice'    => 'Invoice',
                            'col-penawaran'  => 'Penawaran',
                            'col-kontrak'    => 'Kontrak',
                            'col-kendaraan'  => 'Kendaraan',
                            'col-tipe'       => 'Tipe',
                            'col-total'      => 'Total',
                            'col-dibayar'    => 'Dibayar',
                            'col-sisa'       => 'Sisa',
                            'col-status'     => 'Status',
                            'col-aksi'       => 'Aksi',
                        ] as $colId => $colLabel)
                        <label class="flex items-center gap-2 py-1 cursor-pointer hover:text-blue-600 text-xs text-gray-700">
                            <input type="checkbox" class="col-toggle" data-col="{{ $colId }}" checked
                                onchange="toggleColumn('{{ $colId }}', this.checked)">
                            {{ $colLabel }}
                        </label>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- ACCORDION PER KONTRAK --}}
            <div class="divide-y divide-gray-100">
                @forelse ($paginator as $kontrakKey => $items)
                    @php
                        $firstItem    = $items->first();
                        $kontrak      = $firstItem->kontrak;
                        $noKontrak    = $kontrak?->no_kontrak ?? 'Tanpa Kontrak';
                        $customer     = optional($firstItem->invoice)->customer_name ?? '-';
                        // Ambil total periode dari _total_periode yang sudah dikonversi di controller
                        $totalPeriode = $firstItem->_total_periode ?? $items->count();
                        $anyPartial   = $items->contains(fn($s) => strtolower($s->payment_status) === 'partial');
                        $allPaid      = $items->every(fn($s) => strtolower($s->payment_status) === 'paid');
                        $anyPaid      = $items->contains(fn($s) => strtolower($s->payment_status) === 'paid');
                        $kontrakStatus = $allPaid ? 'Paid' : ($anyPartial || $anyPaid ? 'Partial' : 'Unpaid');
                        $kontrakStatusColor = match($kontrakStatus) {
                            'Paid'    => 'bg-green-100 text-green-700',
                            'Partial' => 'bg-yellow-100 text-yellow-700',
                            default   => 'bg-red-100 text-red-700',
                        };
                        // Total kontrak = dihitung dari penawaran items × durasi per item + PPN
                        $grandTotal  = $firstItem->_grand_total ?? $items->sum('total_amount');
                        $grandPaid   = $items->sum('paid_amount');
                        $grandSisa   = $grandTotal - $grandPaid;
                        // paidCount = invoice yg sudah ada payment Verified (bukan harus fully paid)
                        $paidCount        = $firstItem->_paid_count ?? $items->filter(fn($s) => strtolower($s->payment_status) === 'paid')->count();
                        // paidPeriodes = total PERIODE yang sudah paid (bisa > paidCount jika multi-periode)
                        $paidPeriodes     = $firstItem->_paid_periodes ?? $items
                            ->filter(fn($s) => strtolower($s->payment_status) === 'paid')
                            ->sum(fn($s) => max((int)($s->periode_count ?? 1), 1));
                        $accordionId = 'acc_' . md5($kontrakKey);
                    @endphp
                    <div class="bg-white">
                        {{-- HEADER --}}
                        <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition-colors">
                            {{-- Kiri: tombol toggle + info kontrak --}}
                            <button type="button" onclick="toggleAccordion('{{ $accordionId }}')"
                                class="flex items-center gap-3 flex-wrap flex-1 text-left min-w-0">
                                <i id="{{ $accordionId }}_icon" class="fa fa-chevron-right text-xs text-gray-400 transition-transform duration-200 flex-shrink-0"></i>
                                <span class="font-mono text-sm font-bold text-blue-700">{{ $noKontrak }}</span>
                                <span class="text-xs text-gray-500 truncate">{{ $customer }}</span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $kontrakStatusColor }}">{{ $kontrakStatus }}</span>
                                <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full whitespace-nowrap">{{ $paidPeriodes }}/{{ $totalPeriode }} periode lunas</span>
                            </button>

                            {{-- Kanan: angka + tombol hapus --}}
                            <div class="flex items-center gap-4 flex-shrink-0 ml-4">
                                <div class="hidden sm:block text-right">
                                    <p class="text-[10px] text-gray-400">
                                        Total Tagihan
                                      
                                    </p>
                                    <p class="text-xs font-bold text-gray-800">Rp {{ number_format($grandTotal,0,',','.') }}</p>
                                </div>
                                <div class="hidden sm:block text-right">
                                    <p class="text-[10px] text-gray-400">Dibayar</p>
                                    <p class="text-xs font-bold text-green-600">Rp {{ number_format($grandPaid,0,',','.') }}</p>
                                </div>
                                <div class="hidden sm:block text-right">
                                    <p class="text-[10px] text-gray-400">Sisa Bayar</p>
                                    <p class="text-xs font-bold {{ $grandSisa > 0 ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($grandSisa,0,',','.') }}</p>
                                </div>

                                {{-- Tombol hapus semua per kontrak --}}
                                @if($kontrak)
                                <form action="{{ route('summary.destroyByKontrak', $kontrak->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus semua {{ $totalPeriode }} data summary untuk kontrak {{ $noKontrak }}?')"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-colors">
                                        <i class="fa fa-trash text-[10px]"></i>
                                        <span class="hidden sm:inline">Hapus Semua</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        {{-- DETAIL --}}
                        <div id="{{ $accordionId }}" class="hidden">
                            <div class="overflow-x-auto border-t border-gray-100">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                                            <th class="px-4 py-2 text-left">Invoice</th>
                                            <th class="px-4 py-2 text-left">Kendaraan</th>
                                            <th class="px-4 py-2 text-center">Bayar ke</th>
                                            <th class="px-4 py-2 text-center">Sisa</th>
                                            <th class="px-4 py-2 text-right">Total</th>
                                            <th class="px-4 py-2 text-right">Dibayar</th>
                                            <th class="px-4 py-2 text-right">Sisa Bayar</th>
                                            <th class="px-4 py-2 text-left">Bukti</th>
                                            <th class="px-4 py-2 text-left">Attachment</th>
                                            <th class="px-4 py-2 text-center">Status</th>
                                            <th class="px-4 py-2 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $s)
                                            @php
                                                $sc = match(strtolower($s->payment_status)) {
                                                    'paid'    => 'bg-green-100 text-green-700',
                                                    'partial' => 'bg-yellow-100 text-yellow-700',
                                                    'unpaid'  => 'bg-red-100 text-red-700',
                                                    default   => 'bg-gray-100 text-gray-600',
                                                };
                                                $sisaColor = $s->remaining_amount > 0 ? 'text-red-600 font-bold' : 'text-green-600';
                                                $isPaid    = strtolower($s->payment_status) === 'paid';
                                            @endphp
                                            <tr class="border-t border-gray-50 {{ $isPaid ? 'bg-green-50/30' : 'odd:bg-white even:bg-gray-50/40' }} hover:bg-blue-50/30 transition-colors">
                                                <td class="px-4 py-3">
                                                    @if($s->invoice)
                                                        <p class="text-xs font-semibold {{ $isPaid ? 'text-green-700' : 'text-blue-700' }}">
                                                            {{ $s->invoice->invoice_no }}
                                                            @if($isPaid)<i class="fa fa-check-circle text-[10px] ml-1"></i>@endif
                                                        </p>
                                                        <p class="text-[10px] text-gray-400">{{ $s->invoice->customer_name }}</p>
                                                    @else
                                                        <span class="text-xs text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-xs text-gray-600">
                                                    @if($s->invoice && $s->invoice->kendaraans->isNotEmpty())
                                                        @foreach($s->invoice->kendaraans as $kdNo => $kd)
                                                            <div class="flex items-center gap-1">
                                                                <span class="text-[10px] font-bold text-gray-400 w-4">{{ $kdNo + 1 }}.</span>
                                                                @if($isPaid)<i class="fa fa-check text-[10px] text-green-500"></i>@endif
                                                                <span>{{ $kd->merk }} {{ $kd->nopol }}</span>
                                                            </div>
                                                        @endforeach
                                                    @elseif($s->invoice?->kendaraan)
                                                        <span>{{ $s->invoice->kendaraan->merk }} {{ $s->invoice->kendaraan->nopol }}</span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($s->_paid_count === 0 && !$s->_sudah_bayar)
                                                        <span class="text-xs text-gray-400">-/{{ $s->_total_periode }}</span>
                                                    @elseif($s->_sudah_bayar)
                                                        <span class="text-xs font-bold text-green-600">
                                                            {{ $s->_pembayaran_ke }}/{{ $s->_total_periode }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs font-semibold text-blue-600">
                                                            {{ $s->_pembayaran_ke }}/{{ $s->_total_periode }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($s->_sisa_kali <= 0)
                                                        <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold">Lunas</span>
                                                    @elseif($s->_sisa_kali === 1)
                                                        <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-semibold">1x lagi</span>
                                                    @else
                                                        <span class="text-[10px] bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-semibold">{{ $s->_sisa_kali }}x lagi</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-right text-xs font-semibold text-gray-800">Rp {{ number_format($s->total_amount,0,',','.') }}</td>
                                                <td class="px-4 py-3 text-right text-xs font-semibold text-green-700">Rp {{ number_format($s->paid_amount,0,',','.') }}</td>
                                                <td class="px-4 py-3 text-right text-xs {{ $sisaColor }}">Rp {{ number_format($s->remaining_amount,0,',','.') }}</td>

                                                {{-- Bukti & Attachment dari payment invoice --}}
                                                @php
                                                    $invoicePayments = $s->invoice?->payments ?? collect();
                                                    // Ambil payment Verified terbaru, fallback ke payment terbaru apapun
                                                    $latestPayment = $invoicePayments->where('status', 'Verified')->sortByDesc('id')->first()
                                                        ?? $invoicePayments->sortByDesc('id')->first();
                                                    $payAtts = $latestPayment?->attachment;
                                                    if (is_string($payAtts)) $payAtts = json_decode($payAtts, true);
                                                @endphp
                                                <td class="px-4 py-3">
                                                    @if($latestPayment?->file_pembayaran)
                                                        <a href="{{ asset($latestPayment->file_pembayaran) }}" target="_blank"
                                                            class="inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:underline max-w-[140px]"
                                                            title="{{ $latestPayment->file_pembayaran_name ?? basename($latestPayment->file_pembayaran) }}">
                                                            <i class="fa fa-file text-[10px] flex-shrink-0"></i>
                                                            <span class="truncate">{{ $latestPayment->file_pembayaran_name ?? basename($latestPayment->file_pembayaran) }}</span>
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-gray-400">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if(!empty($payAtts) && is_array($payAtts))
                                                        <div class="flex flex-col gap-0.5">
                                                            @foreach($payAtts as $att)
                                                                <a href="{{ asset($att['path'] ?? '') }}" target="_blank"
                                                                    class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline max-w-[140px]"
                                                                    title="{{ $att['name'] ?? '' }}">
                                                                    <i class="fa fa-paperclip text-[10px] flex-shrink-0"></i>
                                                                    <span class="truncate">{{ $att['name'] ?? basename($att['path'] ?? '') }}</span>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">—</span>
                                                    @endif
                                                </td>

                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $sc }}">{{ ucfirst($s->payment_status) }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button onclick="openModalEdit({{ $s->id }})"
                                                            class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('summary.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus?')" class="inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="px-2 py-1 rounded text-xs bg-red-100 text-red-600 hover:bg-red-200">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center text-gray-400 text-sm">
                        <i class="fa fa-inbox text-3xl mb-3 block text-gray-300"></i>
                        Belum ada data summary
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            <div class="py-3 border-t border-gray-100">
                <x-pagination :paginator="$paginator" />
            </div>

        </div>
    </div>

    {{-- ================================================================
     MODAL EDIT
================================================================ --}}
    <div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 my-auto" style="animation:slideUp .2s ease">

            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')

                {{-- HEADER --}}
                <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-800">Edit Summary</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Perbarui data ringkasan pembayaran</p>
                    </div>
                    <button type="button" onclick="closeModalEdit()"
                        class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-6">

                    {{-- SEKSI 1: RELASI DOKUMEN --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">1</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Relasi dokumen</h3>
                            <span class="text-xs text-gray-400">(opsional)</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice</label>
                                <select id="edit_invoice_id" name="invoice_id"
                                    onchange="fetchInvoiceTotal(this.value)"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">� Tidak ada �</option>
                                    @foreach ($invoices as $inv)
                                        <option value="{{ $inv->id }}" {{ old('invoice_id') == $inv->id ? 'selected' : '' }}>{{ $inv->invoice_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penawaran</label>
                                <select id="edit_penawaran_id" name="penawaran_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">� Tidak ada �</option>
                                    @foreach ($penawarans as $p)
                                        <option value="{{ $p->id }}" {{ old('penawaran_id') == $p->id ? 'selected' : '' }}>{{ $p->no_penawaran }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontrak</label>
                                <select id="edit_kontrak_id" name="kontrak_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">� Tidak ada �</option>
                                    @foreach ($kontraks as $k)
                                        <option value="{{ $k->id }}" {{ old('kontrak_id') == $k->id ? 'selected' : '' }}>{{ $k->no_kontrak ?? '#' . $k->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 2: TIPE & NOMINAL --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">2</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipe & nominal</h3>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe <span
                                    class="text-red-500">*</span></label>
                            <select id="edit_type" name="type" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="Rental" {{ old('type') == 'Rental' ? 'selected' : '' }}>Rental</option>
                                <option value="Service" {{ old('type') == 'Service' ? 'selected' : '' }}>Service</option>
                                <option value="Leasing" {{ old('type') == 'Leasing' ? 'selected' : '' }}>Leasing</option>
                                <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total Amount (Rp) <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                    <input type="number" id="edit_total" name="total_amount" required min="0"
                                        oninput="hitungSisaEdit()"
                                        class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('total_amount') }}">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sudah Dibayar (Rp) <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                    <input type="number" id="edit_paid" name="paid_amount" required min="0"
                                        oninput="hitungSisaEdit()"
                                        class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('paid_amount') }}">
                                </div>
                            </div>
                        </div>

                        {{-- SISA OTOMATIS --}}
                        <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa fa-calculator text-gray-400 text-xs"></i>
                                    <span class="text-xs font-semibold text-gray-500">Sisa tagihan (otomatis)</span>
                                    <span class="text-[10px] text-gray-400 bg-gray-200 px-1.5 py-0.5 rounded">read
                                        only</span>
                                </div>
                                <span id="edit_status_badge"
                                    class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">
                                    �
                                </span>
                            </div>
                            <div class="mt-2 flex items-baseline gap-1">
                                <span class="text-xs text-gray-400">Rp</span>
                                <span id="edit_sisa_display" class="text-2xl font-bold text-gray-800">0</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">= Total Amount - Sudah Dibayar</p>
                        </div>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button" onclick="closeModalEdit()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                        <i class="fa fa-save text-sm"></i> Update Summary
                    </button>
                </div>

            </form>
        </div>
    </div>

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
    </style>

    @push('scripts')
        <script>
            /* --- Helpers --- */
            function fmt(n) {
                return Math.max(0, n).toLocaleString('id-ID');
            }

            function statusBadge(sisa, paid) {
                if (sisa <= 0) return {
                    label: 'Paid',
                    cls: 'bg-green-100 text-green-700'
                };
                if (paid == 0) return {
                    label: 'Unpaid',
                    cls: 'bg-red-100 text-red-700'
                };
                return {
                    label: 'Partial',
                    cls: 'bg-yellow-100 text-yellow-700'
                };
            }

            function updateSisaUI(sisa, paid, displayId, badgeId) {
                const s = statusBadge(sisa, paid);
                const display = document.getElementById(displayId);
                const badge = document.getElementById(badgeId);

                if (display) {
                    display.textContent = fmt(sisa);
                    display.className = 'text-2xl font-bold ' + (sisa > 0 ? 'text-red-600' : 'text-green-600');
                }
                if (badge) {
                    badge.textContent = s.label;
                    badge.className = 'text-xs font-semibold px-2.5 py-1 rounded-full ' + s.cls;
                }
            }

            /* --- Hitung sisa � modal tambah --- */
            function hitungSisaTambah() {
                const total = parseFloat(document.getElementById('tambah_total').value) || 0;
                const paid = parseFloat(document.getElementById('tambah_paid').value) || 0;
                const sisa = total - paid;
                updateSisaUI(sisa, paid, 'tambah_sisa_display', 'tambah_status_badge');
            }

            /* --- Hitung sisa � modal edit --- */
            function hitungSisaEdit() {
                const total = parseFloat(document.getElementById('edit_total').value) || 0;
                const paid = parseFloat(document.getElementById('edit_paid').value) || 0;
                const sisa = total - paid;
                updateSisaUI(sisa, paid, 'edit_sisa_display', 'edit_status_badge');
            }

            // Auto-fetch computeTotal() dari invoice yang dipilih → isi total_amount
            function fetchInvoiceTotal(invoiceId) {
                if (!invoiceId) return;
                fetch('/admin/invoices/' + invoiceId + '/compute-total', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.grand_total !== undefined) {
                        document.getElementById('edit_total').value = Math.round(data.grand_total);
                        hitungSisaEdit();
                    }
                })
                .catch(err => console.error('Gagal fetch invoice total:', err));
            }

            /* --- Task 4: Modal Tambah dihapus — summary dibuat otomatis dari Invoice --- */

            /* --- MODAL EDIT --- */
            const modalEdit = document.getElementById('modalEdit');
            const formEdit = document.getElementById('formEdit');

            function openModalEdit(id) {
                fetch("{{ url('admin/summary') }}/" + id + "/edit", {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(data => {
                        formEdit.action = "{{ url('admin/summary') }}/" + id;

                        document.getElementById('edit_invoice_id').value = data.invoice_id ?? '';
                        document.getElementById('edit_penawaran_id').value = data.penawaran_id ?? '';
                        document.getElementById('edit_kontrak_id').value = data.kontrak_id ?? '';
                        document.getElementById('edit_type').value = data.type ?? '';
                        document.getElementById('edit_total').value = data.total_amount ?? 0;
                        document.getElementById('edit_paid').value = data.paid_amount ?? 0;

                        hitungSisaEdit();

                        modalEdit.classList.remove('hidden');
                        modalEdit.classList.add('flex');
                    })
                    .catch(err => {
                        console.error('Gagal fetch data summary:', err);
                        alert('Gagal memuat data. Silakan coba lagi.');
                    });
            }

            function closeModalEdit() {
                modalEdit.classList.add('hidden');
                modalEdit.classList.remove('flex');
            }
            modalEdit.addEventListener('click', e => {
                if (e.target === modalEdit) closeModalEdit();
            });
        </script>

        {{-- TOGGLE KOLOM SCRIPT --}}
        <script>
            // Toggle dropdown
            function toggleColDropdown() {
                document.getElementById('colDropdown').classList.toggle('hidden');
            }

            // Tutup dropdown kalau klik di luar
            document.addEventListener('click', function(e) {
                const wrap = document.getElementById('colToggleWrap');
                if (wrap && !wrap.contains(e.target)) {
                    document.getElementById('colDropdown').classList.add('hidden');
                }
            });

            // Toggle kolom berdasarkan data-col attribute
            function toggleColumn(colId, show) {
                document.querySelectorAll(`[data-col="${colId}"]`).forEach(el => {
                    el.style.display = show ? '' : 'none';
                });
            }

            // Toggle accordion per kontrak
            function toggleAccordion(id) {
                const body = document.getElementById(id);
                const icon = document.getElementById(id + '_icon');
                if (!body) return;
                const isOpen = !body.classList.contains('hidden');
                body.classList.toggle('hidden', isOpen);
                if (icon) {
                    icon.style.transform = isOpen ? '' : 'rotate(90deg)';
                }
            }
        
        // Auto-reopen modal tambah on validation error — tidak diperlukan (Task 4)
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            // modal tambah sudah dihapus, tidak ada aksi
        });
        @endif

        // ── CHART SUMMARY ─────────────────────────────────────────────────────
        const summaryChartManager = new ChartManager();

        document.addEventListener('DOMContentLoaded', function () {
            initSummaryCharts({ filter_type: 'year' });

            document.addEventListener('chartFilterChange', function (e) {
                if (e.detail.filterId === 'summaryChartFilter') {
                    const filters = {
                        filter_type: e.detail.filterType,
                        start_date: e.detail.startDate,
                        end_date: e.detail.endDate,
                    };
                    updateSummaryCharts(filters);
                }
            });
        });

        async function initSummaryCharts(filters) {
            try {
                await summaryChartManager.initChartsFromAPI('summary', {
                    pie:  'summaryPieChart',
                    bar:  'summaryBarChart',
                    line: 'summaryLineChart',
                }, filters, { accentLine: true });
            } catch (error) {
                console.error('Error loading summary charts:', error);
            }
        }

        async function updateSummaryCharts(filters) {
            try {
                const isScrollable = filters.filter_type === 'custom';
                const barOptions  = { scrollable: isScrollable, accentLine: true };
                const lineOptions = { scrollable: isScrollable };
                await summaryChartManager.updateChartsFromAPI('summary', {
                    pie:  'summaryPieChart',
                    bar:  'summaryBarChart',
                    line: 'summaryLineChart',
                }, filters, barOptions, lineOptions);
            } catch (error) {
                console.error('Error updating summary charts:', error);
            }
        }
</script>
    @endpush

@endsection
