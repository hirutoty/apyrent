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

                <button onclick="openModalTambah()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700
            text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    <i class="fa fa-plus text-sm"></i>
                    Tambah Summary
                </button>

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
                            'col-invoice'   => 'Invoice',
                            'col-penawaran' => 'Penawaran',
                            'col-kontrak'   => 'Kontrak',
                            'col-tipe'      => 'Tipe',
                            'col-total'     => 'Total',
                            'col-dibayar'   => 'Dibayar',
                            'col-sisa'      => 'Sisa',
                            'col-status'    => 'Status',
                            'col-aksi'      => 'Aksi',
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

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                            </th>
                            <th data-col="col-invoice" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Invoice</th>
                            <th data-col="col-penawaran" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Penawaran</th>
                            <th data-col="col-kontrak" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kontrak</th>
                            <th data-col="col-tipe" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tipe
                            </th>
                            <th data-col="col-total" class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Total</th>
                            <th data-col="col-dibayar" class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Dibayar</th>
                            <th data-col="col-sisa" class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Sisa</th>
                            <th data-col="col-status" class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status</th>
                            <th data-col="col-aksi" class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summaries as $i => $s)
                            @php
                                $statusColor = match ($s->payment_status) {
                                    'Paid' => 'bg-green-100 text-green-700',
                                    'Partial' => 'bg-yellow-100 text-yellow-700',
                                    'Unpaid' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                                $sisaColor =
                                    $s->remaining_amount > 0 ? 'text-red-600 font-bold' : 'text-green-600 font-bold';
                            @endphp
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                                <td class="px-4 py-3.5 text-xs text-gray-400">{{ $summaries->firstItem() + $i }}</td>
                                <td data-col="col-invoice" class="px-4 py-3.5">
                                    @if ($s->invoice)
                                        <p class="text-sm font-semibold text-blue-700">{{ $s->invoice->invoice_no }}</p>
                                        <p class="text-xs text-gray-500">{{ $s->invoice->customer_name }}</p>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td data-col="col-penawaran" class="px-4 py-3.5 text-sm text-gray-600">
                                    {{ optional($s->penawaran)->no_penawaran ?? '—' }}
                                </td>
                                <td data-col="col-kontrak" class="px-4 py-3.5 text-sm text-gray-600">
                                    {{ optional($s->kontrak)->no_kontrak ?? '—' }}
                                </td>
                                <td data-col="col-tipe" class="px-4 py-3.5">
                                    <span
                                        class="inline-flex items-center text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                        {{ $s->type }}
                                    </span>
                                </td>
                                <td data-col="col-total" class="px-4 py-3.5 text-right text-sm font-semibold text-gray-800">
                                    Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                                </td>
                                <td data-col="col-dibayar" class="px-4 py-3.5 text-right text-sm font-semibold text-green-700">
                                    Rp {{ number_format($s->paid_amount, 0, ',', '.') }}
                                </td>
                                <td data-col="col-sisa" class="px-4 py-3.5 text-right text-sm {{ $sisaColor }}">
                                    Rp {{ number_format($s->remaining_amount, 0, ',', '.') }}
                                </td>
                                <td data-col="col-status" class="px-4 py-3.5 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                        {{ $s->payment_status }}
                                    </span>
                                </td>
                                <td data-col="col-aksi" class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button onclick="openModalEdit({{ $s->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="{{ route('summary.destroy', $s->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus data summary ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                                <i class="fa fa-trash text-xs"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-12 text-gray-400 text-sm">
                                    <i class="fa fa-inbox text-3xl mb-3 block text-gray-300"></i>
                                    Belum ada data summary
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="py-3 border-t border-gray-100">
                {{ $summaries->links() }}
            </div>

        </div>
    </div>

    {{-- ================================================================
     MODAL TAMBAH
================================================================ --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 my-auto" style="animation:slideUp .2s ease">

            <form action="{{ route('summary.store') }}" method="POST">
                @csrf

                {{-- HEADER --}}
                <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-800">Tambah Summary</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Isi data ringkasan pembayaran invoice</p>
                    </div>
                    <button type="button" onclick="closeModalTambah()"
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
                                <select name="invoice_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($invoices as $inv)
                                        <option value="{{ $inv->id }}">{{ $inv->invoice_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penawaran</label>
                                <select name="penawaran_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($penawarans as $p)
                                        <option value="{{ $p->id }}">{{ $p->no_penawaran }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontrak</label>
                                <select name="kontrak_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($kontraks as $k)
                                        <option value="{{ $k->id }}">{{ $k->no_kontrak ?? '#' . $k->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 2: TIPE --}}
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
                            <select name="type" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="Rental">Rental</option>
                                <option value="Service">Service</option>
                                <option value="Leasing">Leasing</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total Amount (Rp) <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                    <input type="number" name="total_amount" id="tambah_total" required min="0"
                                        value="0" oninput="hitungSisaTambah()"
                                        class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sudah Dibayar (Rp) <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                    <input type="number" name="paid_amount" id="tambah_paid" required min="0"
                                        value="0" oninput="hitungSisaTambah()"
                                        class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
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
                                <span id="tambah_status_badge"
                                    class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">
                                    —
                                </span>
                            </div>
                            <div class="mt-2 flex items-baseline gap-1">
                                <span class="text-xs text-gray-400">Rp</span>
                                <span id="tambah_sisa_display" class="text-2xl font-bold text-gray-800">0</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">= Total Amount - Sudah Dibayar</p>
                        </div>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button" onclick="closeModalTambah()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                        <i class="fa fa-save text-sm"></i> Simpan Summary
                    </button>
                </div>

            </form>
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
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($invoices as $inv)
                                        <option value="{{ $inv->id }}">{{ $inv->invoice_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penawaran</label>
                                <select id="edit_penawaran_id" name="penawaran_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($penawarans as $p)
                                        <option value="{{ $p->id }}">{{ $p->no_penawaran }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontrak</label>
                                <select id="edit_kontrak_id" name="kontrak_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($kontraks as $k)
                                        <option value="{{ $k->id }}">{{ $k->no_kontrak ?? '#' . $k->id }}</option>
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
                                <option value="Rental">Rental</option>
                                <option value="Service">Service</option>
                                <option value="Leasing">Leasing</option>
                                <option value="Lainnya">Lainnya</option>
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
                                        class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sudah Dibayar (Rp) <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                    <input type="number" id="edit_paid" name="paid_amount" required min="0"
                                        oninput="hitungSisaEdit()"
                                        class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
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
                                    —
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

            /* --- Hitung sisa — modal tambah --- */
            function hitungSisaTambah() {
                const total = parseFloat(document.getElementById('tambah_total').value) || 0;
                const paid = parseFloat(document.getElementById('tambah_paid').value) || 0;
                const sisa = total - paid;
                updateSisaUI(sisa, paid, 'tambah_sisa_display', 'tambah_status_badge');
            }

            /* --- Hitung sisa — modal edit --- */
            function hitungSisaEdit() {
                const total = parseFloat(document.getElementById('edit_total').value) || 0;
                const paid = parseFloat(document.getElementById('edit_paid').value) || 0;
                const sisa = total - paid;
                updateSisaUI(sisa, paid, 'edit_sisa_display', 'edit_status_badge');
            }

            /* --- MODAL TAMBAH --- */
            const modalTambah = document.getElementById('modalTambah');

            function openModalTambah() {
                modalTambah.classList.remove('hidden');
                modalTambah.classList.add('flex');
                hitungSisaTambah();
            }

            function closeModalTambah() {
                modalTambah.classList.add('hidden');
                modalTambah.classList.remove('flex');
            }
            modalTambah.addEventListener('click', e => {
                if (e.target === modalTambah) closeModalTambah();
            });

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
                // Sembunyikan/tampilkan semua th dan td dengan data-col matching
                document.querySelectorAll(`[data-col="${colId}"]`).forEach(el => {
                    el.style.display = show ? '' : 'none';
                });
            }
        </script>
    @endpush

@endsection
