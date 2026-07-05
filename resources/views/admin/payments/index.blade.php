@extends('admin.layouts.app')

@section('title', 'Data Pembayaran Invoice')

@section('content')

<div class="space-y-6 p-5">

    {{-- ALERT --}}
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

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Pembayaran</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola seluruh transaksi pembayaran invoice</p>
        </div>
        <button onclick="openModalTambah()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <i class="fa fa-plus text-sm"></i> Tambah Pembayaran
        </button>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Total Transaksi</p>
            <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $payments->total() }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Verified</p>
            <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $payments->getCollection()->where('status','Verified')->count() }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Pending</p>
            <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $payments->getCollection()->where('status','Pending')->count() }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Rejected</p>
            <h2 class="text-3xl font-bold text-red-500 mt-2">{{ $payments->getCollection()->where('status','Rejected')->count() }}</h2>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

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

        {{-- SEARCH --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <form method="GET" class="flex gap-2 flex-1 max-w-sm">
                <div class="relative flex-1">
                    <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari ID, metode, customer..."
                        class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <button class="bg-gray-800 text-white text-xs px-4 py-1.5 rounded-lg">Cari</button>
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
                    <label class="flex items-center gap-2 text-xs text-gray-700 py-1 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" checked onchange="toggleColumn('col-trxid', this.checked)" class="rounded"> Transaction ID
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 py-1 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" checked onchange="toggleColumn('col-invoice', this.checked)" class="rounded"> Invoice
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 py-1 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" checked onchange="toggleColumn('col-customer', this.checked)" class="rounded"> Customer
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 py-1 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" checked onchange="toggleColumn('col-tanggal', this.checked)" class="rounded"> Tanggal
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 py-1 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" checked onchange="toggleColumn('col-metode', this.checked)" class="rounded"> Metode
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 py-1 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" checked onchange="toggleColumn('col-jumlah', this.checked)" class="rounded"> Jumlah
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 py-1 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" checked onchange="toggleColumn('col-bukti', this.checked)" class="rounded"> Bukti
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 py-1 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" checked onchange="toggleColumn('col-status', this.checked)" class="rounded"> Status
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 py-1 cursor-pointer hover:text-gray-900">
                        <input type="checkbox" checked onchange="toggleColumn('col-aksi', this.checked)" class="rounded"> Aksi
                    </label>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th data-col="col-trxid" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Transaction ID</th>
                        <th data-col="col-invoice" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Invoice</th>
                        <th data-col="col-customer" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Customer</th>
                        <th data-col="col-tanggal" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                        <th data-col="col-metode" class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Metode</th>
                        <th data-col="col-jumlah" class="text-right text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jumlah</th>
                        <th data-col="col-bukti" class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Bukti</th>
                        <th data-col="col-status" class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th data-col="col-aksi" class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $i => $pay)
                        @php
                            $statusColor = match($pay->status) {
                                'Verified'  => 'bg-green-100 text-green-700',
                                'Pending'   => 'bg-yellow-100 text-yellow-700',
                                'Rejected'  => 'bg-red-100 text-red-700',
                                default     => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3.5 text-xs text-gray-400">{{ $payments->firstItem() + $i }}</td>
                            <td data-col="col-trxid" class="px-4 py-3.5">
                                <span class="font-mono text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">
                                    {{ $pay->transaction_id }}
                                </span>
                            </td>
                            <td data-col="col-invoice" class="px-4 py-3.5 text-sm font-semibold text-gray-800">
                                {{ optional($pay->invoice)->invoice_no ?? '-' }}
                            </td>
                            <td data-col="col-customer" class="px-4 py-3.5 text-sm text-gray-700">
                                {{ optional($pay->invoice)->customer_name ?? '-' }}
                            </td>
                            <td data-col="col-tanggal" class="px-4 py-3.5 text-sm text-gray-600">
                                {{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') : '-' }}
                            </td>
                            <td data-col="col-metode" class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                    <i class="fa fa-credit-card text-gray-400 text-[10px]"></i>
                                    {{ $pay->method }}
                                </span>
                            </td>
                            <td data-col="col-jumlah" class="px-4 py-3.5 text-right">
                                <span class="text-sm font-bold text-green-700">
                                    Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td data-col="col-bukti" class="px-4 py-3.5 text-center">
                                @if ($pay->file_pembayaran)
                                    <a href="{{ asset($pay->file_pembayaran) }}" target="_blank"
                                        class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                                        <i class="fa fa-file text-xs"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td data-col="col-status" class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ $pay->status }}
                                </span>
                            </td>
                            <td data-col="col-aksi" class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="openModalEdit({{ $pay->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition-colors">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <form action="{{ route('payments.destroy', $pay->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus data pembayaran ini?')" class="inline">
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
                                Belum ada data pembayaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $payments->links() }}
        </div>

    </div>
</div>

{{-- ================================================================
     MODAL TAMBAH
================================================================ --}}
<div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 overflow-auto"
    style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 my-auto" style="animation:slideUp .2s ease">

        <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- HEADER --}}
            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Pembayaran</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data transaksi pembayaran invoice</p>
                </div>
                <button type="button" onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="px-6 py-5 space-y-6">

                {{-- SEKSI 1: INFO DASAR --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">1</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi dasar</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice <span class="text-red-500">*</span></label>
                            <select name="invoice_id" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="">— Pilih Invoice —</option>
                                @foreach ($invoices as $inv)
                                    <option value="{{ $inv->id }}">
                                        {{ $inv->invoice_no }} — {{ $inv->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Metode Pembayaran <span class="text-red-500">*</span></label>
                            <input type="text" name="method" required placeholder="Contoh: Transfer, Tunai, QRIS"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 2: NOMINAL & STATUS --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">2</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nominal & status</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Pembayaran (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                <input type="number" name="amount" required min="1" placeholder="0"
                                    class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                            <select name="status" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="Pending">Pending</option>
                                <option value="Verified">Verified</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 3: BUKTI PEMBAYARAN --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">3</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Bukti pembayaran</h3>
                        <span class="text-xs text-gray-400">(opsional)</span>
                    </div>
                    <div id="dropZoneTambah"
                        class="w-full border-2 border-dashed border-blue-300 bg-blue-50 rounded-xl p-6
                               flex flex-col items-center justify-center text-center cursor-pointer
                               hover:bg-blue-100 transition-colors">
                        <input type="file" name="file_pembayaran" id="fileTambah" class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png"
                            onchange="previewFilePembayaran(event,'previewTambah','dropZoneTambah')">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                            <i class="fa fa-cloud-upload-alt text-2xl text-blue-500"></i>
                        </div>
                        <p class="text-sm font-semibold text-blue-700">Drag & Drop File</p>
                        <p class="text-xs text-gray-500 mt-1">atau <span class="font-semibold text-blue-600">klik di sini</span></p>
                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Maks 4MB)</p>
                    </div>
                    <div class="mt-3 flex justify-center">
                        <img id="previewTambah" class="hidden w-32 h-32 object-cover rounded-lg border border-blue-300" alt="Preview">
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                <button type="button" onclick="closeModalTambah()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                    <i class="fa fa-save text-sm"></i> Simpan Pembayaran
                </button>
            </div>

        </form>
    </div>
</div>

{{-- ================================================================
     MODAL EDIT
================================================================ --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 overflow-auto"
    style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 my-auto" style="animation:slideUp .2s ease">

        <form id="formEdit" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- HEADER --}}
            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Pembayaran</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data transaksi pembayaran</p>
                </div>
                <button type="button" onclick="closeModalEdit()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="px-6 py-5 space-y-6">

                {{-- SEKSI 1: INFO DASAR --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">1</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi dasar</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Transaction ID</label>
                            <input id="edit_transaction_id" type="text" readonly
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice <span class="text-red-500">*</span></label>
                            <select id="edit_invoice_id" name="invoice_id" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="">— Pilih Invoice —</option>
                                @foreach ($invoices as $inv)
                                    <option value="{{ $inv->id }}">
                                        {{ $inv->invoice_no }} — {{ $inv->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                            <input id="edit_payment_date" type="date" name="payment_date" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Metode Pembayaran <span class="text-red-500">*</span></label>
                            <input id="edit_method" type="text" name="method" required
                                placeholder="Contoh: Transfer, Tunai, QRIS"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 2: NOMINAL & STATUS --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">2</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nominal & status</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Pembayaran (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                <input id="edit_amount" type="number" name="amount" required min="1"
                                    class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                            <select id="edit_status" name="status" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                <option value="Pending">Pending</option>
                                <option value="Verified">Verified</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 3: BUKTI PEMBAYARAN --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">3</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Bukti pembayaran</h3>
                        <span class="text-xs text-gray-400">(opsional — kosongkan jika tidak diganti)</span>
                    </div>

                    {{-- Preview file lama --}}
                    <div id="existingFile" class="hidden mb-3 flex items-center gap-3 bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-3">
                        <i class="fa fa-file text-indigo-500"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-indigo-700">File saat ini</p>
                            <a id="existingFileLink" href="#" target="_blank"
                                class="text-xs text-indigo-600 hover:underline truncate block">Lihat file</a>
                        </div>
                    </div>

                    <div id="dropZoneEdit"
                        class="w-full border-2 border-dashed border-blue-300 bg-blue-50 rounded-xl p-6
                               flex flex-col items-center justify-center text-center cursor-pointer
                               hover:bg-blue-100 transition-colors">
                        <input type="file" name="file_pembayaran" id="fileEdit" class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png"
                            onchange="previewFilePembayaran(event,'previewEdit','dropZoneEdit')">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                            <i class="fa fa-cloud-upload-alt text-2xl text-blue-500"></i>
                        </div>
                        <p class="text-sm font-semibold text-blue-700">Upload file baru</p>
                        <p class="text-xs text-gray-500 mt-1">atau <span class="font-semibold text-blue-600">klik di sini</span></p>
                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Maks 4MB)</p>
                    </div>
                    <div class="mt-3 flex justify-center">
                        <img id="previewEdit" class="hidden w-32 h-32 object-cover rounded-lg border border-blue-300" alt="Preview">
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                <button type="button" onclick="closeModalEdit()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                    <i class="fa fa-save text-sm"></i> Update Pembayaran
                </button>
            </div>

        </form>
    </div>
</div>

{{-- STYLE --}}
<style>
    @keyframes slideUp {
        from { opacity:0; transform:translateY(16px); }
        to   { opacity:1; transform:translateY(0); }
    }
</style>

@push('scripts')
<script>
    /* ── Toggle Kolom ── */
    function toggleColDropdown() {
        document.getElementById('colDropdown').classList.toggle('hidden');
    }
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('colToggleWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('colDropdown').classList.add('hidden');
        }
    });
    function toggleColumn(colId, show) {
        document.querySelectorAll(`[data-col="${colId}"]`).forEach(el => {
            el.style.display = show ? '' : 'none';
        });
    }

    /* ── MODAL TAMBAH ── */
    const modalTambah = document.getElementById('modalTambah');

    function openModalTambah() {
        modalTambah.classList.remove('hidden');
        modalTambah.classList.add('flex');
    }
    function closeModalTambah() {
        modalTambah.classList.add('hidden');
        modalTambah.classList.remove('flex');
    }
    modalTambah.addEventListener('click', e => { if (e.target === modalTambah) closeModalTambah(); });

    /* ── MODAL EDIT ── */
    const modalEdit = document.getElementById('modalEdit');
    const formEdit  = document.getElementById('formEdit');

    function openModalEdit(id) {
        fetch("{{ url('admin/payments') }}/" + id + "/edit", {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            formEdit.action = "{{ url('admin/payments') }}/" + id;

            document.getElementById('edit_transaction_id').value = data.transaction_id ?? '';
            document.getElementById('edit_invoice_id').value     = data.invoice_id ?? '';
            document.getElementById('edit_payment_date').value   = data.payment_date ?? '';
            document.getElementById('edit_method').value         = data.method ?? '';
            document.getElementById('edit_amount').value         = data.amount ?? '';
            document.getElementById('edit_status').value         = data.status ?? 'Pending';

            // File lama
            const existingBlock = document.getElementById('existingFile');
            const existingLink  = document.getElementById('existingFileLink');
            if (data.file_pembayaran) {
                existingBlock.classList.remove('hidden');
                existingLink.href        = '/' + data.file_pembayaran;
                existingLink.textContent = data.file_pembayaran.split('/').pop();
            } else {
                existingBlock.classList.add('hidden');
            }

            // Reset preview upload baru
            document.getElementById('previewEdit').classList.add('hidden');
            document.getElementById('fileEdit').value = '';

            modalEdit.classList.remove('hidden');
            modalEdit.classList.add('flex');
        })
        .catch(err => {
            console.error('Gagal fetch data pembayaran:', err);
            alert('Gagal memuat data. Silakan coba lagi.');
        });
    }

    function closeModalEdit() {
        modalEdit.classList.add('hidden');
        modalEdit.classList.remove('flex');
    }
    modalEdit.addEventListener('click', e => { if (e.target === modalEdit) closeModalEdit(); });

    /* ── DRAG DROP & PREVIEW ── */
    function previewFilePembayaran(event, previewId, dropId) {
        const file    = event.target.files[0];
        const preview = document.getElementById(previewId);
        const drop    = document.getElementById(dropId);
        if (!file) return;

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
            const label = drop.querySelector('p.text-sm');
            if (label) label.textContent = '✔ ' + file.name;
        }
    }

    function initDrop(dropId, inputId) {
        const drop  = document.getElementById(dropId);
        const input = document.getElementById(inputId);
        if (!drop || !input) return;
        drop.addEventListener('click', () => input.click());
        drop.addEventListener('dragover', e => { e.preventDefault(); drop.classList.add('opacity-80'); });
        drop.addEventListener('dragleave', () => drop.classList.remove('opacity-80'));
        drop.addEventListener('drop', e => {
            e.preventDefault();
            drop.classList.remove('opacity-80');
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    }

    initDrop('dropZoneTambah', 'fileTambah');
    initDrop('dropZoneEdit',   'fileEdit');
</script>
@endpush

@endsection