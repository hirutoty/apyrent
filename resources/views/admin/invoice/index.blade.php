@extends('admin.layouts.app')

@section('title', 'Data Invoice')

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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Invoice</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola seluruh data invoice</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('invoices.export.excel', request()->query()) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-green-600 text-green-600 rounded-lg bg-transparent hover:bg-green-600 hover:text-white transition-colors">
                    <i class="fa fa-file-excel"></i> Export Excel
                </a>
                <button type="button" id="btnTambah"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    <i class="fa fa-plus text-sm"></i> Tambah Invoice
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

        {{-- CHART FILTER --}}
        <x-chart-filter
            id="invoiceChartFilter"
            defaultFilter="year"
            :showCustomRange="true"
        />

        {{-- CHART CONTAINER --}}
        <x-chart-container
            id="invoiceChartContainer"
            layout="stacked"
            pieTitle="Status Invoice"
            pieId="invoicePieChart"
            barTitle="Invoice per Bulan"
            barId="invoiceBarChart"
            lineTitle="Trend Invoice"
            lineId="invoiceLineChart"
            :showStats="true"
            :statsData="[]"
        />

        {{-- STAT CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Total Invoice</p>
                <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $invoices->total() }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Lunas</p>
                <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $invoices->getCollection()->where('status','lunas')->count() }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Partial</p>
                <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $invoices->getCollection()->where('status','partial')->count() }}</h2>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Overdue</p>
                <h2 class="text-3xl font-bold text-red-500 mt-2">{{ $invoices->getCollection()->where('status','overdue')->count() }}</h2>
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
                        placeholder="Cari no invoice, customer, atau order..."
                        class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <button class="bg-gray-800 text-white text-xs px-4 py-1.5 rounded-lg">Cari</button>
            </form>
            <div class="relative" id="colToggleWrap">
                <button type="button" onclick="toggleColDropdown()"
                    class="flex items-center gap-1.5 border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 bg-white hover:bg-gray-50 whitespace-nowrap">
                    <i class="bi bi-layout-three-columns"></i> Kolom <i class="bi bi-chevron-down text-[10px]"></i>
                </button>
                <div id="colDropdown"
                    class="hidden absolute right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-3 min-w-[160px] max-h-64 overflow-y-auto">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase mb-2">Tampilkan Kolom</p>
                    @foreach(['col-noinvoice'=>'No Invoice','col-tanggal'=>'Tanggal','col-customer'=>'Customer','col-penawaran'=>'Penawaran','col-kontrak'=>'Kontrak','col-kendaraan'=>'Kendaraan','col-status'=>'Status','col-pembayaran'=>'Pembayaran','col-reminder'=>'Terakhir Reminder','col-aksi'=>'Aksi'] as $cid=>$clabel)
                    <label class="flex items-center gap-2 py-1 cursor-pointer hover:text-blue-600 text-xs text-gray-700">
                        <input type="checkbox" class="col-toggle" data-col="{{ $cid }}" checked onchange="toggleColumn('{{ $cid }}', this.checked)"> {{ $clabel }}
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th data-col="col-noinvoice"  class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No Invoice</th>
                        <th data-col="col-tanggal"    class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                        <th data-col="col-customer"   class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Customer</th>
                        <th data-col="col-penawaran"  class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Penawaran</th>
                        <th data-col="col-kontrak"    class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kontrak</th>
                        <th data-col="col-kendaraan"  class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kendaraan</th>
                        <th data-col="col-status"     class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th data-col="col-pembayaran" class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Pembayaran</th>
                        <th data-col="col-reminder"   class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Terakhir Reminder</th>
                        <th data-col="col-aksi"       class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        @php
                            $statusColor = match($inv->status) {
                                'lunas'   => 'bg-green-100 text-green-700',
                                'partial' => 'bg-yellow-100 text-yellow-700',
                                'overdue' => 'bg-red-100 text-red-700',
                                default   => 'bg-gray-100 text-gray-600',
                            };
                            $payColor = match($inv->payment_status) {
                                'paid'   => 'bg-green-100 text-green-700',
                                'unpaid' => 'bg-red-100 text-red-700',
                                default  => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                            <td class="px-4 py-3.5 text-xs text-gray-400">{{ $invoices->firstItem() + $loop->index }}</td>
                            <td data-col="col-noinvoice"  class="px-4 py-3.5"><span class="font-mono text-xs font-semibold text-blue-700">{{ $inv->invoice_no }}</span></td>
                            <td data-col="col-tanggal"    class="px-4 py-3.5 text-sm text-gray-600">{{ optional($inv->invoice_date)->format('d M Y') }}</td>
                            <td data-col="col-customer"   class="px-4 py-3.5 text-sm font-semibold text-gray-800">{{ $inv->customer_name }}</td>
                            <td data-col="col-penawaran"  class="px-4 py-3.5 text-sm text-gray-600">{{ optional($inv->penawaran)->no_penawaran ?? '�' }}</td>
                            <td data-col="col-kontrak"    class="px-4 py-3.5 text-sm text-gray-600">{{ optional($inv->kontrak)->no_kontrak ?? '�' }}</td>
                            <td data-col="col-kendaraan" class="px-4 py-3.5 text-xs text-gray-600">
                                @php
                                    $kendaraanList = $inv->kendaraans->isNotEmpty()
                                        ? $inv->kendaraans
                                        : ($inv->kendaraan ? collect([$inv->kendaraan]) : collect());
                                    $isLunas = $inv->payment_status === 'paid' || $inv->status === 'lunas';
                                @endphp
                                @if($kendaraanList->isEmpty())
                                    <span class="text-gray-400">-</span>
                                @else
                                    <div class="flex flex-col gap-1">
                                    @foreach($kendaraanList as $kdNo => $kd)
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-[10px] font-bold text-gray-400 w-4">{{ $kdNo + 1 }}.</span>
                                            <span class="font-medium text-gray-800">{{ $kd->merk }} &ndash; {{ $kd->nopol }}</span>
                                            @if($isLunas)
                                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">Lunas</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    </div>
                                @endif
                            </td>
                            <td data-col="col-status" class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ $inv->status ? strtoupper($inv->status) : '�' }}
                                </span>
                            </td>
                            <td data-col="col-pembayaran" class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $payColor }}">
                                    {{ $inv->payment_status ? strtoupper($inv->payment_status) : '�' }}
                                </span>
                            </td>
                            <td data-col="col-reminder" class="px-4 py-3.5 text-center text-xs text-gray-500">
                                {{ $inv->last_email_sent_at?->format('d M Y') ?? '�' }}
                            </td>
                            <td data-col="col-aksi" class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <form action="{{ route('invoices.email', $inv->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200 transition-colors">
                                            <i class="fa fa-envelope text-xs"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('invoices.print', $inv->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
                                        <i class="fa fa-download text-xs"></i>
                                    </a>
                                    <a href="{{ route('invoices.show', $inv->id) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">
                                        <i class="fa fa-eye text-xs"></i>
                                    </a>
                                    <button type="button" class="editBtn inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $inv->id }}" data-invoice_no="{{ $inv->invoice_no }}"
                                        data-invoice_date="{{ optional($inv->invoice_date)->format('Y-m-d') }}"
                                        data-customer_name="{{ $inv->customer_name }}" data-type="{{ $inv->type }}"
                                        data-customer_address="{{ $inv->customer_address }}" data-telephone="{{ $inv->telephone }}"
                                        data-email="{{ $inv->email }}" data-contact_person="{{ $inv->contact_person }}"
                                        data-penawaran_id="{{ $inv->penawaran_id }}" data-kontrak_id="{{ $inv->kontrak_id }}"
                                        data-kendaraan_id="{{ $inv->kendaraan_id }}" data-satuan="{{ $inv->satuan }}"
                                        data-pengirim="{{ $inv->pengirim }}" data-ppn="{{ $inv->ppn }}"
                                        data-pph="{{ $inv->pph }}" data-total="{{ $inv->total }}"
                                        data-status="{{ $inv->status }}" data-payment_status="{{ $inv->payment_status }}"
                                        data-staff="{{ $inv->staff }}" data-name_staff="{{ $inv->name_staff }}"
                                        data-direktur="{{ $inv->direktur }}" data-name_direktur="{{ $inv->name_direktur }}"
                                        data-ttd_staff="{{ $inv->ttd_staff }}" data-ttd_direktur="{{ $inv->ttd_direktur }}">
                                        <i class="fa fa-edit text-xs"></i>
                                    </button>
                                    <form action="{{ route('invoices.destroy', $inv->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus data invoice ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                            <i class="fa fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-12 text-gray-400 text-sm">
                                <i class="fa fa-inbox text-3xl mb-3 block text-gray-300"></i>
                                Belum ada data invoice
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="py-3 border-t border-gray-100"><x-pagination :paginator="$invoices" /></div>

        </div>{{-- end TABLE CARD --}}
    </div>{{-- end space-y-6 --}}

    {{-- ========================= MODAL TAMBAH ========================= --}}
    <div id="modalTambah" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-[95%] max-w-3xl max-h-[95vh] overflow-y-auto">

            {{-- TAB HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex gap-0">
                    <button type="button" id="tambah_tab1_btn"
                        onclick="switchTambahTab(1)"
                        class="px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tl-lg">
                        <i class="fa fa-file-alt mr-1"></i> Data Invoice
                    </button>
                    <button type="button" id="tambah_tab2_btn"
                        onclick="switchTambahTab(2)"
                        class="px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400 cursor-not-allowed rounded-tr-lg"
                        disabled>
                        <i class="fa fa-calendar-alt mr-1"></i> Periode & Remaks
                    </button>
                </div>
                <button type="button" id="closeTambah"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            {{-- TAB 1: DATA INVOICE --}}
            <div id="tambah_tab1_content">
            <form id="formTambah" action="{{ route('invoices.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="px-6 py-5 space-y-6">

                    {{-- ===== SEKSI 0: NO KONTRAK ===== --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                                <i class="fa fa-file-contract text-white text-[9px]"></i>
                            </div>
                            <h3 class="text-xs font-semibold text-blue-700 uppercase tracking-wide">No Kontrak</h3>
                            <span class="text-xs text-blue-400">— semua data terisi otomatis</span>
                        </div>

                        {{-- Dropdown pilih kontrak --}}
                        <select id="tambah_no_kontrak_input"
                            class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 bg-white">
                            <option value="">-- Pilih No Kontrak --</option>
                            @foreach($kontraks as $ktk)
                                <option value="{{ $ktk->no_kontrak }}">
                                    {{ $ktk->no_kontrak }}
                                    @if($ktk->penawaran)— {{ $ktk->penawaran->customer_name ?? $ktk->pihak_kedua }}@endif
                                </option>
                            @endforeach
                        </select>

                        {{-- Status badge --}}
                        <div id="tambah_kontrak_status" class="mt-2 hidden">
                            <span id="tambah_kontrak_status_badge" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full"></span>
                        </div>

                        {{-- Hidden relasi — diisi dari lookup --}}
                        <input type="hidden" name="kontrak_ids[]"   id="tambah_hidden_kontrak_id">
                        <input type="hidden" name="penawaran_ids[]" id="tambah_hidden_penawaran_id">
                        <div id="tambah_hidden_kendaraan_container"></div>
                    </div>{{-- end SEKSI 0 --}}
                    {{-- SEKSI 1: INFO DASAR (readonly, auto-fill dari kontrak) --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">1</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi dasar</h3>
                            <span class="text-xs text-gray-400 italic">— terisi otomatis dari kontrak</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No invoice</label>
                                <input type="text" value="(otomatis dibuat sistem)" disabled class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal invoice <span class="text-red-500">*</span></label>
                                <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama customer <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" id="tambah_customer_name" required readonly placeholder="Otomatis dari kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe customer</label>
                                <input type="text" id="tambah_type_display" readonly placeholder="Otomatis dari kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                                <input type="hidden" name="type" id="tambah_type_hidden" value="perorangan">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat customer</label>
                                <textarea name="customer_address" id="tambah_customer_address" rows="2" readonly placeholder="Otomatis dari kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Telepon</label>
                                <input type="text" name="telephone" id="tambah_telephone" readonly placeholder="Otomatis dari kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 mt-3">Email</label>
                                <input type="email" name="email" id="tambah_email" readonly placeholder="Otomatis dari kontrak" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 2: KENDARAAN (auto dari penawaran) --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">2</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Kendaraan</h3>
                            <span class="text-xs text-gray-400 italic">— dari penawaran terkait</span>
                        </div>
                        <div id="tambah_kendaraan_display" class="text-xs text-gray-400 italic px-3 py-2 border border-dashed border-gray-200 rounded-lg">
                            Masukkan No Kontrak terlebih dahulu.
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 3 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">3</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi invoice</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- <label class="block text-xs font-semibold text-gray-600 mb-1.5">Satuan</label> --}}
                                <input hidden type="text" name="satuan" id="tambah_satuan" placeholder="Contoh: Car Rent/Day"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('satuan', 'Car Rent/Month') }}">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pengirim</label>
                                <input type="text" name="pengirim" id="tambah_pengirim" placeholder="Nama pengirim"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('pengirim') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    PPN (%)
                                    @if(($setting->ppn_default ?? 0) > 0)
                                        <span class="text-blue-400 font-normal">— dari setting</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="ppn" id="tambah_ppn" min="0"
                                        value="{{ old('ppn', $setting->ppn_default ?? 0) }}"
                                        oninput="recalcTambahTotal()"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-7 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    PPH (%)
                                    <span class="text-gray-400 font-normal">— opsional</span>
                                    @if(($setting->pph_default ?? 0) > 0)
                                        <span class="text-blue-400 font-normal">— dari setting</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="pph" id="tambah_pph" min="0"
                                        value="{{ old('pph', $setting->pph_default ?? 0) }}"
                                        oninput="recalcTambahTotal()"
                                        placeholder="0"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-7 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total (Rp)
                                    <span class="text-gray-400 font-normal">— otomatis</span>
                                </label>
                                <input type="number" step="0.01" name="total" id="tambah_total" value="0" min="0"
                                    readonly
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed focus:outline-none">
                                <p id="tambah_periode_info" class="mt-1 text-xs text-indigo-500 font-medium hidden"></p>
                                <p class="mt-1 text-xs text-blue-500 flex items-center gap-1">
                                    <i class="fa fa-info-circle"></i>
                                    Sudah termasuk PPN {{ $setting->ppn_default ?? 0 }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- Hidden: status & payment_status default draft/unpaid --}}
                    <input type="hidden" name="status" id="tambah_status_hidden" value="draft">
                    <input type="hidden" name="payment_status" id="tambah_payment_status_hidden" value="unpaid">
                    <input type="hidden" id="tambah_jumlah_bayar" name="jumlah_dibayar" value="0">

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 4 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">4</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penandatangan</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- STAFF --}}
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <p class="text-xs font-semibold text-gray-500"><i class="fa fa-user text-gray-400 mr-1"></i> Staff</p>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan staff</label>
                                    <select name="staff" id="tambah_staff"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                        <option value="">-- Pilih Jabatan Staf --</option>
                                        <option>Direktur Utama (CEO)</option>
                                        <option>Wakil Direktur (Vice President)</option>
                                        <option>Manajer Umum (General Manager)</option>
                                        <option>Manajer Operasional</option>
                                        <option>Manajer Keuangan</option>
                                        <option>Manajer Pemasaran</option>
                                        <option>Manajer SDM (HR Manager)</option>
                                        <option>Supervisor / Koordinator</option>
                                        <option>Staf Administrasi</option>
                                        <option>Staf Keuangan</option>
                                        <option>Staf Pemasaran</option>
                                        <option>Staf IT</option>
                                        <option>Customer Service</option>
                                        <option>Office Boy / Office Girl</option>
                                        <option>Security</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama staff</label>
                                    <input type="text" name="name_staff" id="tambah_name_staff" placeholder="Nama lengkap"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('name_staff') }}">
                                </div>
                                {{-- TTD Staff: hidden input path + UI tab --}}
                                <input type="hidden" name="ttd_staff_path" id="tambah_staff_path">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanda Tangan Staff</label>
                                    @include('admin.invoice._ttd_picker', ['uid' => 'tambah_staff', 'field' => 'ttd_staff'])
                                </div>
                            </div>

                            {{-- DIREKTUR --}}
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <p class="text-xs font-semibold text-gray-500"><i class="fa fa-user-tie text-gray-400 mr-1"></i> Direktur</p>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan direktur</label>
                                    <select name="direktur" id="tambah_direktur"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                        <option value="">-- Pilih Jabatan Direktur --</option>
                                        <option>Direktur Utama (CEO)</option>
                                        <option>Wakil Direktur (Vice President)</option>
                                        <option>Manajer Umum (General Manager)</option>
                                        <option>Manajer Operasional</option>
                                        <option>Manajer Keuangan</option>
                                        <option>Manajer Pemasaran</option>
                                        <option>Manajer SDM (HR Manager)</option>
                                        <option>Supervisor / Koordinator</option>
                                        <option>Staf Administrasi</option>
                                        <option>Staf Keuangan</option>
                                        <option>Staf Pemasaran</option>
                                        <option>Staf IT</option>
                                        <option>Customer Service</option>
                                        <option>Office Boy / Office Girl</option>
                                        <option>Security</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama direktur</label>
                                    <input type="text" name="name_direktur" id="tambah_name_direktur" placeholder="Nama lengkap"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('name_direktur') }}">
                                </div>
                                <input type="hidden" name="ttd_direktur_path" id="tambah_direktur_path">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanda Tangan Direktur</label>
                                    @include('admin.invoice._ttd_picker', ['uid' => 'tambah_direktur', 'field' => 'ttd_direktur'])
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button" id="closeTambah2"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="button" id="btnSimpanTambah"
                        onclick="handleLanjutKePeriode()"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                        <i class="fa fa-arrow-right text-sm"></i> Lanjut ke Periode & Remaks
                    </button>
                </div>
            </form>
            </div>{{-- end tab1 --}}

            {{-- TAB 2: PERIODE & REMAKS --}}
            <div id="tambah_tab2_content" class="hidden">
                <div class="px-6 py-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800">Periode & Remaks</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Invoice: <span id="tambah_invoice_no_label" class="font-semibold text-blue-600">-</span></p>
                        </div>
                        <button type="button" onclick="openTambahPeriodeModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <i class="fa fa-plus text-xs"></i> Tambah Periode
                        </button>
                    </div>
                    <div id="periodeListTambah" class="divide-y border rounded-xl min-h-[120px]">
                        <div class="py-10 text-center text-gray-400 text-xs">
                            <i class="fa fa-calendar-alt text-2xl mb-2 block"></i>
                            Belum ada periode. Klik "Tambah Periode" untuk memulai.
                        </div>
                    </div>
                    {{-- Summary periode --}}
                    <div class="mt-4 flex justify-end">
                        <table class="text-sm min-w-[260px]">
                            {{-- Sub total per item kendaraan --}}
                            <tbody id="tambahSummaryItemRows"></tbody>
                            <tr>
                                <td class="text-gray-500 pr-8 py-1">Sub Total</td>
                                <td class="text-right font-semibold text-gray-800 min-w-[130px]" id="tambahSummaryTotal">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="text-gray-500 pr-8 py-1" id="tambahSummaryPpnLabel">PPN</td>
                                <td class="text-right text-gray-700" id="tambahSummaryPpn">-</td>
                            </tr>
                            <tr>
                                <td class="text-gray-500 pr-8 py-1 text-[10px]">Jumlah Periode</td>
                                <td class="text-right text-gray-400 text-[10px]" id="tambahSummarySubTotal">-</td>
                            </tr>
                            <tr id="bayarKeRow" class="hidden">
                                <td class="text-gray-500 pr-8 py-1 text-[10px]">Bayar Ke</td>
                                <td class="text-right text-[10px] font-semibold text-blue-600" id="tambahSummaryBayarKe">-</td>
                            </tr>
                            <tr id="sisaRow" class="hidden">
                                <td class="text-gray-400 pr-8 py-1 text-[10px]">Sisa</td>
                                <td class="text-right text-[10px] font-medium text-orange-500" id="tambahSummarySisa">-</td>
                            </tr>
                            <tr class="border-t">
                                <td class="text-gray-800 font-bold pr-8 py-2">Total Invoice</td>
                                <td class="text-right font-bold text-blue-700 text-base" id="tambahSummaryGrand">Rp 0</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="border-t border-gray-100 px-6 py-4 flex justify-between items-center">
                    <button type="button" onclick="switchTambahTab(1)"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                        <i class="fa fa-arrow-left text-xs"></i> Kembali
                    </button>
                    <button type="button" id="btnSelesaiTambah"
                        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                        <i class="fa fa-save"></i> Simpan Invoice
                    </button>
                </div>
            </div>{{-- end tab2 --}}

        </div>
    </div>

    {{-- ========================= MODAL SHOW ========================= --}}
    <div id="modalShow" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-[95%] max-w-3xl max-h-[95vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Detail Invoice</h2>
                    <p id="show_subtitle" class="text-xs text-gray-400 mt-0.5"></p>
                </div>
                <button type="button" id="closeShow"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">
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
                            <p class="text-xs text-gray-400 mb-0.5">No Invoice</p>
                            <p id="show_invoice_no" class="text-sm font-semibold text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Tanggal Invoice</p>
                            <p id="show_invoice_date" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Nama Customer</p>
                            <p id="show_customer_name" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Tipe Customer</p>
                            <p id="show_type" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Alamat Customer</p>
                            <p id="show_customer_address" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Telepon</p>
                            <p id="show_telephone" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Email</p>
                            <p id="show_email" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Contact Person</p>
                            <p id="show_contact_person" class="text-sm text-gray-800">-</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 2: RELASI --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">2</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Relasi dokumen</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Penawaran</p>
                            <p id="show_penawaran" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Kontrak</p>
                            <p id="show_kontrak" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Kendaraan</p>
                            <p id="show_kendaraan" class="text-sm text-gray-800">-</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 3: INFO INVOICE --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">3</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi invoice</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Satuan</p>
                            <p id="show_satuan" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Pengirim</p>
                            <p id="show_pengirim" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">PPN (%)</p>
                            <p id="show_ppn" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">PPH (%)</p>
                            <p id="show_pph" class="text-sm text-gray-800">-</p>
                        </div>
                    </div>
                    <div class="mt-4 bg-blue-50 rounded-xl px-5 py-4 flex items-center justify-between">
                        <span class="text-sm font-semibold text-blue-700">Total</span>
                        <span id="show_total" class="text-xl font-bold text-blue-700">Rp 0</span>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 4: STATUS --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">4</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</h3>
                    </div>
                    <div class="flex gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1.5">Status Invoice</p>
                            <span id="show_status" class="px-3 py-1 rounded-full text-xs font-semibold">-</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1.5">Status Pembayaran</p>
                            <span id="show_payment_status" class="px-3 py-1 rounded-full text-xs font-semibold">-</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- SEKSI 5: PENANDATANGAN --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-600 text-[10px] font-bold">5</span>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penandatangan</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                            <p class="text-xs font-semibold text-gray-500"><i class="fa fa-user text-gray-400 mr-1"></i>
                                Staff</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <p class="text-xs text-gray-400 mb-0.5">Jabatan</p>
                                    <p id="show_staff" class="text-sm text-gray-800">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 mb-0.5">Nama</p>
                                    <p id="show_name_staff" class="text-sm text-gray-800">-</p>
                                </div>
                            </div>
                            <div id="show_ttd_staff_wrap" class="hidden">
                                <p class="text-xs text-gray-400 mb-1">Tanda Tangan</p>
                                <img id="show_ttd_staff_img" src="" alt="TTD Staff" class="w-28 rounded border">
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                            <p class="text-xs font-semibold text-gray-500"><i
                                    class="fa fa-user-tie text-gray-400 mr-1"></i> Direktur</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <p class="text-xs text-gray-400 mb-0.5">Jabatan</p>
                                    <p id="show_direktur" class="text-sm text-gray-800">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 mb-0.5">Nama</p>
                                    <p id="show_name_direktur" class="text-sm text-gray-800">-</p>
                                </div>
                            </div>
                            <div id="show_ttd_direktur_wrap" class="hidden">
                                <p class="text-xs text-gray-400 mb-1">Tanda Tangan</p>
                                <img id="show_ttd_direktur_img" src="" alt="TTD Direktur"
                                    class="w-28 rounded border">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="border-t border-gray-100 px-6 py-4 flex justify-end">
                <button type="button" id="closeShow2"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ========================= MODAL EDIT ========================= --}}
    <div id="modalEdit" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-[95%] max-w-3xl max-h-[95vh] overflow-y-auto">
            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-800">Edit Invoice</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Perbarui data invoice</p>
                    </div>
                    <button type="button" id="closeEdit"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-6">

                    {{-- SEKSI 1 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">1</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi dasar</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No invoice</label>
                                <input id="edit_invoice_no" type="text" readonly
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal invoice <span
                                        class="text-red-500">*</span></label>
                                <input id="edit_invoice_date" type="date" name="invoice_date" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('invoice_date') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama customer <span
                                        class="text-red-500">*</span></label>
                                <input id="edit_customer_name" type="text" name="customer_name" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('customer_name') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe customer</label>
                                <select id="edit_type" name="type"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="perorangan" {{ old('type') == 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                                    <option value="perusahaan" {{ old('type') == 'perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat customer</label>
                                <textarea id="edit_customer_address" name="customer_address" rows="2"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">{{ old('customer_address') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Telepon</label>
                                <input id="edit_telephone" type="number" name="telephone"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('telephone') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                                <input id="edit_email" type="email" name="email"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('email') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contact person</label>
                                <input id="edit_contact_person" type="text" name="contact_person"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('contact_person') }}">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 2 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">2</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Relasi dokumen</h3>
                            <span class="text-xs text-gray-400">(opsional, bisa lebih dari satu)</span>
                        </div>

                        {{-- Penawaran rows --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-semibold text-gray-600">Penawaran</label>
                                <button type="button" onclick="addRelRow('edit','penawaran')"
                                    class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                    <i class="fa fa-plus text-[10px]"></i> Tambah
                                </button>
                            </div>
                            <div id="edit_penawaran_rows" class="space-y-2">
                                <div class="flex gap-2 items-center rel-row">
                                    <select name="penawaran_ids[]" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                        <option value="">� Tidak ada �</option>
                                        @foreach ($penawarans as $p)
                                            <option value="{{ $p->id }}" {{ old('penawaran_ids[]') == $p->id ? 'selected' : '' }}>{{ $p->no_penawaran }} � {{ $p->customer_name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="removeRelRow(this)" class="text-red-400 hover:text-red-600 text-sm px-1"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>

                        {{-- Kontrak rows --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-semibold text-gray-600">Kontrak</label>
                                <button type="button" onclick="addRelRow('edit','kontrak')"
                                    class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                    <i class="fa fa-plus text-[10px]"></i> Tambah
                                </button>
                            </div>
                            <div id="edit_kontrak_rows" class="space-y-2">
                                <div class="flex gap-2 items-center rel-row">
                                    <select name="kontrak_ids[]" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                        <option value="">� Tidak ada �</option>
                                        @foreach ($kontraks as $k)
                                            <option value="{{ $k->id }}" {{ old('kontrak_ids[]') == $k->id ? 'selected' : '' }}>{{ $k->no_kontrak ?? '#' . $k->id }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="removeRelRow(this)" class="text-red-400 hover:text-red-600 text-sm px-1"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>

                        {{-- Kendaraan rows --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-semibold text-gray-600">Kendaraan</label>
                                <button type="button" onclick="addRelRow('edit','kendaraan')"
                                    class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                    <i class="fa fa-plus text-[10px]"></i> Tambah
                                </button>
                            </div>
                            <div id="edit_kendaraan_rows" class="space-y-2">
                                <div class="flex gap-2 items-center rel-row">
                                    <select name="kendaraan_ids[]" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                        <option value="">� Tidak ada �</option>
                                        @foreach ($kendaraans as $kd)
                                            <option value="{{ $kd->id }}" {{ old('kendaraan_ids[]') == $kd->id ? 'selected' : '' }}>{{ $kd->merk }} � {{ $kd->nopol }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="removeRelRow(this)" class="text-red-400 hover:text-red-600 text-sm px-1"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 3 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">3</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi invoice</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Satuan</label>
                                <input id="edit_satuan" type="text" name="satuan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('satuan') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pengirim</label>
                                <input id="edit_pengirim" type="text" name="pengirim"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('pengirim') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">PPN (%)</label>
                                <input id="edit_ppn" type="number" step="0.01" name="ppn" min="0"
                                    oninput="recalcEditTotal()"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('ppn') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">PPH (%) <span class="text-gray-400 font-normal">— opsional</span></label>
                                <input id="edit_pph" type="number" step="0.01" name="pph" min="0"
                                    placeholder="0"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('pph') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total (Rp)
                                    <span class="text-gray-400 font-normal">— otomatis</span>
                                </label>
                                <input id="edit_total" type="number" step="0.01" name="total" min="0"
                                    readonly
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed focus:outline-none" value="{{ old('total') }}">
                                <p class="mt-1 text-xs text-blue-500 flex items-center gap-1">
                                    <i class="fa fa-info-circle"></i>
                                    Sudah termasuk PPN {{ $setting->ppn_default ?? 0 }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 4 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">4</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status invoice</label>
                                <select id="edit_status" name="status"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="partial" {{ old('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    <option value="lunas" {{ old('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status pembayaran</label>
                                <select id="edit_payment_status" name="payment_status"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- SEKSI 5 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-[10px] font-bold">5</span>
                            </div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penandatangan</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <p class="text-xs font-semibold text-gray-500"><i
                                        class="fa fa-user text-gray-400 mr-1"></i> Staff</p>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan staff</label>
                                    <select id="edit_staff" name="staff"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                        <option value="">-- Pilih Jabatan Staf --</option>
                                        <option>Direktur Utama (CEO)</option>
                                        <option>Wakil Direktur (Vice President)</option>
                                        <option>Manajer Umum (General Manager)</option>
                                        <option>Manajer Operasional</option>
                                        <option>Manajer Keuangan</option>
                                        <option>Manajer Pemasaran</option>
                                        <option>Manajer SDM (HR Manager)</option>
                                        <option>Supervisor / Koordinator</option>
                                        <option>Staf Administrasi</option>
                                        <option>Staf Keuangan</option>
                                        <option>Staf Pemasaran</option>
                                        <option>Staf IT</option>
                                        <option>Customer Service</option>
                                        <option>Office Boy / Office Girl</option>
                                        <option>Security</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama staff</label>
                                    <input id="edit_name_staff" type="text" name="name_staff"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('name_staff') }}">
                                </div>
                                <input type="hidden" name="ttd_staff_path" id="edit_staff_path">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanda Tangan Staff</label>
                                    @include('admin.invoice._ttd_picker', ['uid' => 'edit_staff', 'field' => 'ttd_staff'])
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <p class="text-xs font-semibold text-gray-500"><i
                                        class="fa fa-user-tie text-gray-400 mr-1"></i> Direktur</p>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan direktur</label>
                                    <select id="edit_direktur" name="direktur"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                        <option value="">-- Pilih Jabatan Direktur --</option>
                                        <option>Direktur Utama (CEO)</option>
                                        <option>Wakil Direktur (Vice President)</option>
                                        <option>Manajer Umum (General Manager)</option>
                                        <option>Manajer Operasional</option>
                                        <option>Manajer Keuangan</option>
                                        <option>Manajer Pemasaran</option>
                                        <option>Manajer SDM (HR Manager)</option>
                                        <option>Supervisor / Koordinator</option>
                                        <option>Staf Administrasi</option>
                                        <option>Staf Keuangan</option>
                                        <option>Staf Pemasaran</option>
                                        <option>Staf IT</option>
                                        <option>Customer Service</option>
                                        <option>Office Boy / Office Girl</option>
                                        <option>Security</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama direktur</label>
                                    <input id="edit_name_direktur" type="text" name="name_direktur"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('name_direktur') }}">
                                </div>
                                <input type="hidden" name="ttd_direktur_path" id="edit_direktur_path">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanda Tangan Direktur</label>
                                    @include('admin.invoice._ttd_picker', ['uid' => 'edit_direktur', 'field' => 'ttd_direktur'])
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button" id="closeEdit2"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl">
                        <i class="fa fa-save text-sm"></i> Update Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL TAMBAH PERIODE (Tab 2) ===== --}}
    <div id="modalTambahPeriode" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[60]">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0">
                <h3 class="text-sm font-semibold text-gray-800">Tambah Periode</h3>
                <button type="button" id="closeTambahPeriode" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none"><i class="fa fa-times"></i></button>
            </div>

            {{-- Mode: pilih dari rental details (muncul jika ada rental details) --}}
            <div id="periodeCheckboxSection" class="hidden flex-1 overflow-y-auto">
                <div class="px-6 pt-4 pb-2">
                    <p class="text-xs text-gray-500 mb-3">
                        <i class="fa fa-info-circle text-blue-400 mr-1"></i>
                        Pilih satu atau lebih remak. Jika pilih &gt;1, periode awal diambil dari tanggal mulai remak pertama dan periode akhir dari tanggal selesai remak terakhir.
                    </p>
                    {{-- Tombol select all --}}
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 cursor-pointer mb-3 hover:bg-blue-50 hover:border-blue-200">
                        <input type="checkbox" id="checkAllPeriode" class="w-4 h-4 rounded accent-blue-600">
                        <span class="text-xs font-semibold text-gray-600">Pilih Semua</span>
                    </label>
                    {{-- Daftar rental details --}}
                    <div id="periodeCheckboxList" class="space-y-2"></div>
                </div>
                {{-- Preview periode terpilih --}}
                <div id="periodeCheckboxPreview" class="hidden px-6 pb-4 pt-2">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
                        <p class="text-xs font-semibold text-blue-700 mb-1.5"><i class="fa fa-calendar-check mr-1"></i> Periode yang akan dibuat:</p>
                        <div class="flex items-center gap-3 flex-wrap">
                            <div>
                                <span class="text-[10px] text-blue-400 block">Tanggal Awal</span>
                                <span id="previewPeriodeAwal" class="text-xs font-bold text-blue-800">–</span>
                            </div>
                            <div class="text-blue-300 text-sm">→</div>
                            <div>
                                <span class="text-[10px] text-blue-400 block">Tanggal Akhir</span>
                                <span id="previewPeriodeAkhir" class="text-xs font-bold text-blue-800">–</span>
                            </div>
                            <div class="ml-auto">
                                <span class="text-[10px] text-blue-400 block text-right">Jumlah Remak</span>
                                <span id="previewJumlahRemak" class="text-xs font-bold text-blue-800 block text-right">0 item</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mode: manual (muncul jika TIDAK ada rental details) --}}
            <div id="periodeManualSection" class="px-6 py-5 space-y-4">
                <p class="text-xs text-gray-400 italic">
                    <i class="fa fa-pencil-alt mr-1"></i> Tidak ada data rental aktif. Isi tanggal secara manual.
                </p>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Awal <span class="text-red-500">*</span></label>
                    <input type="date" id="tambahPeriodeAwal" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Akhir</label>
                    <input type="date" id="tambahPeriodeAkhir" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            <div class="border-t px-6 py-4 flex justify-end gap-2 flex-shrink-0">
                <button type="button" id="closeTambahPeriode2" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="button" id="saveTambahPeriode" class="px-5 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl">
                    <i class="fa fa-plus mr-1"></i> Simpan Periode
                </button>
            </div>
        </div>
    </div>

    {{-- ===== MODAL TAMBAH REMAK (Tab 2) ===== --}}
    <div id="modalTambahRemak" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[60]">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Tambah Remak</h3>
                <button type="button" id="closeTambahRemak" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none"><i class="fa fa-times"></i></button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan <span class="text-red-500">*</span></label>
                    <textarea id="tambahRemakText" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" placeholder="Contoh: Sewa Mobil Innova Reborn Nopol B 2471 SYM"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">QTY <span class="text-red-500">*</span></label>
                        <input type="number" id="tambahRemakQty" value="1" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" id="tambahRemakPrice" value="0" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                </div>
            </div>
            <div class="border-t px-6 py-4 flex justify-end gap-2">
                <button type="button" id="closeTambahRemak2" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="button" id="saveTambahRemak" class="px-5 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl">Simpan</button>
            </div>
        </div>
    </div>

    {{-- Global functions dipanggil dari inline oninput/onclick --}}
    <script>
        function rpFmt(n) { return 'Rp ' + Number(n||0).toLocaleString('id-ID'); }

        function fmtDate(s) {
            if (!s) return '';
            const d = new Date(s);
            return d.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
        }

        function recalcTambahTotal() {
            const ppnPct = parseFloat(document.getElementById('tambah_ppn')?.value) || 0;

            // Prioritas 1: base sudah dihitung dari tab 2 (loadPeriodesTambah)
            // Prioritas 2: hitung dari rental_details yang ada di tab 1
            let base = parseFloat(window._tambahSubTotalBase) || 0;
            if (!base) {
                const details = window._tambahRentalDetails || [];
                details.forEach(entry => {
                    (entry.remak_items || []).forEach(item => {
                        base += (parseFloat(item.qty) || 1) * (parseFloat(item.price) || 0);
                    });
                    if (entry.biaya_driver > 0) {
                        base += parseFloat(entry.biaya_driver) || 0;
                    }
                });
            }

            const ppnNom = Math.round(base * ppnPct / 100);
            const grand  = base + ppnNom;

            const ppnLabel = document.getElementById('tambahSummaryPpnLabel');
            const ppnEl    = document.getElementById('tambahSummaryPpn');
            const grandEl  = document.getElementById('tambahSummaryGrand');
            if (ppnLabel) ppnLabel.textContent = ppnPct > 0 ? `PPN ${ppnPct}%` : 'PPN';
            if (ppnEl)    ppnEl.textContent    = ppnNom > 0 ? rpFmt(ppnNom) : '-';
            // tambahSummarySubTotal = "Per Periode" — sudah diisi di renderPeriodeListLokal, tidak di-override
            if (grandEl)  grandEl.textContent  = rpFmt(grand);

            const totalEl = document.getElementById('tambah_total');
            if (totalEl) totalEl.value = grand;
        }

        function recalcEditTotal() {
            const ppnPct = parseFloat(document.getElementById('edit_ppn')?.value) || 0;
            const base   = parseFloat(window._editSubTotalBase || 0);
            const ppnNom = Math.round(base * ppnPct / 100);
            const grand  = base + ppnNom;

            const totalEl = document.getElementById('edit_total');
            if (totalEl) totalEl.value = grand;
        }

        function switchTambahTab(tab) {
            const tab1Btn = document.getElementById('tambah_tab1_btn');
            const tab2Btn = document.getElementById('tambah_tab2_btn');
            const tab1Con = document.getElementById('tambah_tab1_content');
            const tab2Con = document.getElementById('tambah_tab2_content');
            if (!tab1Btn) return;
            if (tab === 1) {
                tab1Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tl-lg';
                tab2Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400 cursor-not-allowed rounded-tr-lg';
                tab1Con.classList.remove('hidden');
                tab2Con.classList.add('hidden');
            } else {
                if (!window._currentTambahInvoiceId) return;
                tab1Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-blue-600 rounded-tl-lg';
                tab2Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tr-lg';
                tab1Con.classList.add('hidden');
                tab2Con.classList.remove('hidden');
                if (typeof loadPeriodesTambah === 'function') loadPeriodesTambah();
            }
        }

        function openTambahPeriodeModal() {
            // Reset input manual
            const elAwal  = document.getElementById('tambahPeriodeAwal');
            const elAkhir = document.getElementById('tambahPeriodeAkhir');
            if (elAwal)  elAwal.value  = '';
            if (elAkhir) elAkhir.value = '';

            const details = window._tambahRentalDetails || [];
            const checkboxSection = document.getElementById('periodeCheckboxSection');
            const manualSection   = document.getElementById('periodeManualSection');
            const listEl          = document.getElementById('periodeCheckboxList');
            const previewEl       = document.getElementById('periodeCheckboxPreview');
            const checkAll        = document.getElementById('checkAllPeriode');

            if (details.length > 0) {
                // Mode checkbox: tampilkan rental details
                checkboxSection.classList.remove('hidden');
                manualSection.classList.add('hidden');
                if (previewEl) previewEl.classList.add('hidden');
                if (checkAll)  checkAll.checked = false;

                // Render daftar checkbox dari rental_details
                listEl.innerHTML = details.map((entry, idx) => {
                    const labelAwal  = entry.tanggal_mulai
                        ? new Date(entry.tanggal_mulai).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
                        : '–';
                    const labelAkhir = entry.tanggal_selesai
                        ? new Date(entry.tanggal_selesai).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
                        : labelAwal;

                    // Hitung subtotal semua remak_items entry ini
                    const subTotal = (entry.remak_items || []).reduce((s, r) => s + (parseFloat(r.qty)||1) * (parseFloat(r.price)||0), 0)
                        + (parseFloat(entry.biaya_driver) || 0);

                    // Label kendaraan
                    const kendaraanLabels = (entry.remak_items || []).map(r => r.kendaraan || 'Item').join(', ');

                    return `<label class="periode-checkbox-row flex items-start gap-3 px-3 py-2.5 rounded-xl border border-gray-200 cursor-pointer hover:border-blue-300 hover:bg-blue-50/40 transition-all"
                                data-idx="${idx}"
                                data-awal="${entry.tanggal_mulai || ''}"
                                data-akhir="${entry.tanggal_selesai || entry.tanggal_mulai || ''}">
                        <input type="checkbox" class="periode-item-cb mt-0.5 w-4 h-4 rounded accent-blue-600 flex-shrink-0" data-idx="${idx}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <span class="text-xs font-semibold text-gray-800">${labelAwal} – ${labelAkhir}</span>
                                <span class="text-xs font-bold text-blue-700">${'Rp ' + Number(subTotal).toLocaleString('id-ID')}</span>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-0.5 truncate">${kendaraanLabels || '–'}</p>
                        </div>
                    </label>`;
                }).join('');

                // Bind perubahan checkbox → update preview
                listEl.querySelectorAll('.periode-item-cb').forEach(cb => {
                    cb.addEventListener('change', updatePeriodeCheckboxPreview);
                });

                // Bind "Pilih Semua"
                if (checkAll) {
                    checkAll.addEventListener('change', function() {
                        listEl.querySelectorAll('.periode-item-cb').forEach(cb => { cb.checked = this.checked; });
                        updatePeriodeCheckboxPreview();
                    });
                }

            } else {
                // Mode manual: tidak ada rental details
                checkboxSection.classList.add('hidden');
                manualSection.classList.remove('hidden');
            }

            const m = document.getElementById('modalTambahPeriode');
            if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
        }

        // Hitung tanggal periode dari checkbox yang dipilih dan tampilkan preview
        function updatePeriodeCheckboxPreview() {
            const selected = Array.from(document.querySelectorAll('.periode-item-cb:checked'));
            const previewEl = document.getElementById('periodeCheckboxPreview');
            if (!previewEl) return;

            // Sinkronisasi "Pilih Semua"
            const total   = document.querySelectorAll('.periode-item-cb').length;
            const checkAll = document.getElementById('checkAllPeriode');
            if (checkAll) checkAll.checked = (selected.length === total && total > 0);

            if (selected.length === 0) {
                previewEl.classList.add('hidden');
                return;
            }

            const details = window._tambahRentalDetails || [];

            // Kumpulkan semua tanggal dari yang dipilih
            let awalDates  = [];
            let akhirDates = [];
            selected.forEach(cb => {
                const idx   = parseInt(cb.dataset.idx);
                const entry = details[idx];
                if (!entry) return;
                if (entry.tanggal_mulai)   awalDates.push(new Date(entry.tanggal_mulai));
                if (entry.tanggal_selesai) akhirDates.push(new Date(entry.tanggal_selesai));
                else if (entry.tanggal_mulai) akhirDates.push(new Date(entry.tanggal_mulai));
            });

            const minAwal  = awalDates.length  ? new Date(Math.min(...awalDates))  : null;
            const maxAkhir = akhirDates.length ? new Date(Math.max(...akhirDates)) : null;

            const fmtPrev = d => d
                ? d.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' })
                : '–';

            document.getElementById('previewPeriodeAwal').textContent  = fmtPrev(minAwal);
            document.getElementById('previewPeriodeAkhir').textContent = fmtPrev(maxAkhir);

            // Hitung total jumlah remak items dari yang dipilih
            let jumlahRemak = 0;
            selected.forEach(cb => {
                const idx   = parseInt(cb.dataset.idx);
                const entry = details[idx];
                if (!entry) return;
                jumlahRemak += (entry.remak_items || []).length;
                if ((entry.biaya_driver || 0) > 0) jumlahRemak += 1;
            });
            document.getElementById('previewJumlahRemak').textContent = jumlahRemak + ' item';

            previewEl.classList.remove('hidden');
        }
        window.updatePeriodeCheckboxPreview = updatePeriodeCheckboxPreview;

        function openTambahRemakModal(periodeId) {
            window._activeTambahPeriodeId = periodeId;
            ['tambahRemakText','tambahRemakQty','tambahRemakPrice'].forEach((id, i) => {
                const el = document.getElementById(id);
                if (el) el.value = i === 0 ? '' : (i === 1 ? 1 : 0);
            });
            const m = document.getElementById('modalTambahRemak');
            if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
        }

        async function deleteTambahPeriode(periodeId) {
            if (!confirm('Hapus periode ini beserta semua remaksnya?')) return;
            await fetch('/admin/invoices/' + window._currentTambahInvoiceId + '/periodes/' + periodeId, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '', 'Accept': 'application/json' }
            });
            if (typeof loadPeriodesTambah === 'function') loadPeriodesTambah();
        }

        async function deleteTambahRemak(invId, periodeId, remakId) {
            if (!confirm('Hapus remaks ini?')) return;
            await fetch('/admin/invoices/' + invId + '/periodes/' + periodeId + '/remaks/' + remakId, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '', 'Accept': 'application/json' }
            });
            if (typeof loadPeriodesTambah === 'function') loadPeriodesTambah();
        }

        function unlockTab2(invoiceId, invoiceNo) {
            window._currentTambahInvoiceId = invoiceId;
            // sync ke variabel lokal di DOMContentLoaded block 2
            if (typeof _setCurrentTambahInvoiceId === 'function') _setCurrentTambahInvoiceId(invoiceId);
            const tab2Btn = document.getElementById('tambah_tab2_btn');
            if (tab2Btn) {
                tab2Btn.disabled = false;
                tab2Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-blue-600 rounded-tr-lg cursor-pointer';
            }
            const label = document.getElementById('tambah_invoice_no_label');
            if (label) label.textContent = invoiceNo;
        }
    </script>

    @push('scripts')
        <script>
        // -- Toggle Kolom --
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
        // Bind semua checkbox col-toggle via JS (untuk yang tidak pakai onchange inline)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.col-toggle').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    toggleColumn(this.dataset.col, this.checked);
                });
            });
        });

        // ===================== TTD PICKER =====================
        const TTD_LIBRARY_URL = '{{ route("invoices.ttd-library") }}';
        let ttdLibraryCache = null; // cache supaya tidak fetch berulang

        async function loadTtdLibrary() {
            if (ttdLibraryCache) return ttdLibraryCache;
            try {
                const res = await fetch(TTD_LIBRARY_URL, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                ttdLibraryCache = await res.json();
            } catch (e) {
                ttdLibraryCache = [];
            }
            return ttdLibraryCache;
        }

        function initTtdPicker(picker) {
            const uid        = picker.dataset.uid;
            const field      = picker.dataset.field;
            const tabLib     = picker.querySelector('.ttd-tab-lib');
            const tabUpload  = picker.querySelector('.ttd-tab-upload');
            const panelLib   = picker.querySelector('.ttd-panel-lib');
            const panelUpload = picker.querySelector('.ttd-panel-upload');
            const grid       = picker.querySelector('.ttd-library-grid');
            const preview    = picker.querySelector('.ttd-preview');
            const previewImg = picker.querySelector('.ttd-preview-img');
            const clearBtn   = picker.querySelector('.ttd-clear');
            const fileInput  = picker.querySelector('.ttd-file-input');

            // Hidden input path (untuk pilih dari library)
            const hiddenPath = document.getElementById(uid + '_path');

            let libLoaded = false;

            // -- Tab switch ------------------------------
            tabLib.addEventListener('click', () => {
                tabLib.classList.replace('bg-white', 'bg-blue-600');
                tabLib.classList.replace('text-gray-500', 'text-white');
                tabUpload.classList.replace('bg-blue-600', 'bg-white');
                tabUpload.classList.replace('text-white', 'text-gray-500');
                panelLib.classList.remove('hidden');
                panelUpload.classList.add('hidden');
                // Nonaktifkan file input agar tidak ikut submit
                fileInput.disabled = true;
                if (!libLoaded) renderLibrary();
            });

            tabUpload.addEventListener('click', () => {
                tabUpload.classList.replace('bg-white', 'bg-blue-600');
                tabUpload.classList.replace('text-gray-500', 'text-white');
                tabLib.classList.replace('bg-blue-600', 'bg-white');
                tabLib.classList.replace('text-white', 'text-gray-500');
                panelUpload.classList.remove('hidden');
                panelLib.classList.add('hidden');
                fileInput.disabled = false;
                // Reset path jika beralih ke upload
                if (hiddenPath) hiddenPath.value = '';
                clearPreview();
            });

            // -- Render library --------------------------
            async function renderLibrary() {
                libLoaded = true;
                const files = await loadTtdLibrary();
                if (files.length === 0) {
                    grid.innerHTML = `<div class="col-span-3 text-center text-gray-400 text-xs py-4">
                        Belum ada TTD tersimpan.<br>Gunakan tab "Upload Baru" untuk menambah.
                    </div>`;
                    return;
                }
                grid.innerHTML = files.map(f => `
                    <div class="ttd-lib-item cursor-pointer rounded-lg border-2 border-transparent hover:border-blue-400 p-1 bg-white transition-all"
                        data-path="${f.path}" data-url="${f.url}">
                        <img src="${f.url}" class="w-full h-12 object-contain rounded" alt="TTD">
                    </div>
                `).join('');

                grid.querySelectorAll('.ttd-lib-item').forEach(item => {
                    item.addEventListener('click', () => {
                        // Highlight pilihan
                        grid.querySelectorAll('.ttd-lib-item').forEach(i => {
                            i.classList.remove('border-blue-500', 'bg-blue-50');
                            i.classList.add('border-transparent');
                        });
                        item.classList.add('border-blue-500', 'bg-blue-50');
                        item.classList.remove('border-transparent');

                        // Set hidden path & preview
                        if (hiddenPath) hiddenPath.value = item.dataset.path;
                        showPreview(item.dataset.url);
                        fileInput.disabled = true;
                    });
                });
            }

            // -- Preview file upload baru --------------
            fileInput.addEventListener('change', function () {
                if (this.files[0]) {
                    const url = URL.createObjectURL(this.files[0]);
                    showPreview(url);
                    if (hiddenPath) hiddenPath.value = '';
                } else {
                    clearPreview();
                }
            });

            // -- Preview helpers -----------------------
            function showPreview(url) {
                previewImg.src = url;
                preview.classList.remove('hidden');
            }
            function clearPreview() {
                previewImg.src = '';
                preview.classList.add('hidden');
            }

            clearBtn.addEventListener('click', () => {
                clearPreview();
                if (hiddenPath) hiddenPath.value = '';
                fileInput.value = '';
                grid.querySelectorAll('.ttd-lib-item').forEach(i => {
                    i.classList.remove('border-blue-500', 'bg-blue-50');
                    i.classList.add('border-transparent');
                });
            });

            // -- Set preview dari luar (saat edit) ------
            picker.setCurrentTtd = function (path, url) {
                if (!path) return;
                if (hiddenPath) hiddenPath.value = path;
                showPreview(url);
                // Pastikan di tab library
                tabLib.click();
                // Highlight item yang cocok setelah library render
                loadTtdLibrary().then(() => {
                    const item = grid.querySelector(`[data-path="${path}"]`);
                    if (item) {
                        grid.querySelectorAll('.ttd-lib-item').forEach(i => i.classList.remove('border-blue-500','bg-blue-50'));
                        item.classList.add('border-blue-500', 'bg-blue-50');
                    }
                });
            };

            // Default: mulai di tab library, file input disabled
            fileInput.disabled = true;
            renderLibrary();
        }

        // Init semua picker saat DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.ttd-picker').forEach(initTtdPicker);
            // Invalidate cache setelah upload baru (supaya library fresh)
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', () => { ttdLibraryCache = null; });
            });
        });
        </script>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ===================== HELPER =====================
            function openModal(el) {
                el.classList.remove('hidden');
                el.classList.add('flex');
            }

            function closeModal(el) {
                el.classList.add('hidden');
                el.classList.remove('flex');
            }

            // Export ke global agar bisa dipakai dari DOMContentLoaded block lain
            window.openModal = openModal;
            window.closeModal = closeModal;

            function setVal(id, value) {
                const el = document.getElementById(id);
                if (el) el.value = value ?? '';
            }

            function setText(id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = value || '-';
            }

            function formatRupiah(num) {
                return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
            }

            // ===================== MODAL TAMBAH =====================
            const modalTambah = document.getElementById('modalTambah');
            document.getElementById('btnTambah').onclick = () => {
                // Reset tab ke 1 dan disable tab 2
                currentTambahInvoiceId = null;
                document.getElementById('tambah_tab1_btn').className = 'px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tl-lg';
                document.getElementById('tambah_tab2_btn').className = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400 cursor-not-allowed rounded-tr-lg';
                document.getElementById('tambah_tab2_btn').disabled = true;
                document.getElementById('tambah_tab1_content').classList.remove('hidden');
                document.getElementById('tambah_tab2_content').classList.add('hidden');
                document.getElementById('formTambah').reset();
                // Reset relasi rows ke satu baris kosong
                ['penawaran','kontrak','kendaraan'].forEach(type => {
                    const c = document.getElementById('tambah_' + type + '_rows');
                    if (c) { c.innerHTML = buildRelRow('tambah', type, ''); }
                });
                openModal(modalTambah);
            };
            // closeTambah binding ditangani oleh closeTambahModal di bawah

            // ===================== LOOKUP NO KONTRAK (dropdown) =====================
            const fmtRp = n => 'Rp ' + Number(n).toLocaleString('id-ID');

            // Total tagihan & rental details untuk auto-populate tab 2
            window._tambahRentalDetails = [];

            function resetTambahForm() {
                ['tambah_customer_name','tambah_telephone','tambah_email'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
                const addr = document.getElementById('tambah_customer_address');
                if (addr) addr.value = '';
                const td = document.getElementById('tambah_type_display');
                if (td) td.value = '';
                document.getElementById('tambah_type_hidden').value = 'perorangan';
                document.getElementById('tambah_hidden_kontrak_id').value = '';
                document.getElementById('tambah_hidden_penawaran_id').value = '';
                document.getElementById('tambah_hidden_kendaraan_container').innerHTML = '';
                document.getElementById('tambah_kendaraan_display').innerHTML =
                    '<span class="text-gray-400 italic">Pilih No Kontrak terlebih dahulu.</span>';
                document.getElementById('tambah_kontrak_status').classList.add('hidden');
                // Reset rental details
                window._tambahRentalDetails  = [];
                window._lokalPeriodes        = [];
                window._invoiceDraft         = false;
                window._existingInvoiceCount = 0;
                window._paidPeriodesCount    = 0;
                window._coveredPeriodesCount = 0;
                window._selectedFuturePis    = [];
                // Reset seksi 3
                setVal('tambah_satuan', 'Car Rent/Day');
                setVal('tambah_pengirim', '');
                setVal('tambah_ppn', '{{ $setting->ppn_default ?? 0 }}');
                setVal('tambah_pph', '{{ $setting->pph_default ?? 0 }}');
                setVal('tambah_total', 0);
                window._tambahSubTotalBase = 0;
                const pi = document.getElementById('tambah_periode_info');
                if (pi) { pi.textContent = ''; pi.classList.add('hidden'); }
                // Reset seksi 4 (hidden)
                const jb = document.getElementById('tambah_jumlah_bayar');
                if (jb) jb.value = 0;
                setTambahStatus(0, 0);
                // Reset seksi 5
                setVal('tambah_staff', '');
                setVal('tambah_name_staff', '');
                setVal('tambah_direktur', '');
                setVal('tambah_name_direktur', '');
            }

            function setTambahStatus(dibayar, total) {
                const statusEl  = document.getElementById('tambah_status_display');
                const payEl     = document.getElementById('tambah_payment_status_display');
                const statusHid = document.getElementById('tambah_status_hidden');
                const payHid    = document.getElementById('tambah_payment_status_hidden');
                const sisaLbl   = document.getElementById('tambah_lbl_sisa');

                const sisa = Math.max(0, total - dibayar);
                if (sisaLbl) {
                    sisaLbl.textContent = fmtRp(sisa);
                    sisaLbl.className   = 'font-semibold ' + (sisa > 0 ? 'text-red-600' : 'text-green-600');
                }

                if (total <= 0 || dibayar <= 0) {
                    statusHid.value = 'draft';
                    payHid.value    = 'unpaid';
                    statusEl.className   = 'inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold bg-gray-100 text-gray-500 w-full';
                    statusEl.textContent = 'Draft (belum ada pembayaran)';
                    payEl.className   = 'inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold bg-red-100 text-red-700 w-full';
                    payEl.textContent = 'Unpaid';
                } else if (sisa <= 0) {
                    statusHid.value = 'lunas';
                    payHid.value    = 'paid';
                    statusEl.className   = 'inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold bg-green-100 text-green-700 w-full';
                    statusEl.textContent = 'Lunas';
                    payEl.className   = 'inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold bg-green-100 text-green-700 w-full';
                    payEl.textContent = 'Paid';
                } else {
                    statusHid.value = 'partial';
                    payHid.value    = 'unpaid';
                    statusEl.className   = 'inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold bg-yellow-100 text-yellow-700 w-full';
                    statusEl.textContent = 'Partial (' + fmtRp(sisa) + ' belum terbayar)';
                    payEl.className   = 'inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold bg-yellow-100 text-yellow-700 w-full';
                    payEl.textContent = 'Unpaid';
                }
            }

            function onTambahJumlahBayarChange(input) {
                const dibayar = parseFloat(input.value) || 0;
                const total   = parseFloat(document.getElementById('tambah_total')?.value) || 0;
                setTambahStatus(dibayar, total);
            }

            function onTambahJumlahBayarChange(input) {
                const dibayar = parseFloat(input.value) || 0;
                const total   = parseFloat(document.getElementById('tambah_total')?.value) || 0;
                setTambahStatus(dibayar, total);
            }

            function doLookupKontrak(no) {
                const statusWrap  = document.getElementById('tambah_kontrak_status');
                const statusBadge = document.getElementById('tambah_kontrak_status_badge');

                if (!no) { resetTambahForm(); return; }

                statusWrap.classList.remove('hidden');
                statusBadge.className = 'inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-gray-100 text-gray-500';
                statusBadge.innerHTML = '<i class="fa fa-spinner fa-spin text-[10px]"></i> Memuat...';

                fetch(`{{ route('invoices.lookup-kontrak') }}?no=${encodeURIComponent(no)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.found) {
                        resetTambahForm();
                        statusWrap.classList.remove('hidden');
                        statusBadge.className = 'inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-red-100 text-red-700';
                        statusBadge.innerHTML = '<i class="fa fa-times-circle text-[10px]"></i> Kontrak tidak ditemukan';
                        return;
                    }

                    statusBadge.className = 'inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-green-100 text-green-700';
                    statusBadge.innerHTML = `<i class="fa fa-check-circle text-[10px]"></i> ${data.no_kontrak} &mdash; ${data.customer_name}`;

                    // Fill info dasar
                    document.getElementById('tambah_customer_name').value    = data.customer_name    ?? '';
                    document.getElementById('tambah_type_display').value     = data.type ? (data.type.charAt(0).toUpperCase() + data.type.slice(1)) : '';
                    document.getElementById('tambah_type_hidden').value      = data.type             ?? 'perorangan';
                    document.getElementById('tambah_customer_address').value = data.customer_address ?? '';
                    document.getElementById('tambah_telephone').value        = data.telephone        ?? '';
                    document.getElementById('tambah_email').value            = data.email            ?? '';

                    // Hidden relasi
                    document.getElementById('tambah_hidden_kontrak_id').value   = data.kontrak_id   ?? '';
                    document.getElementById('tambah_hidden_penawaran_id').value = data.penawaran_id ?? '';

                    // Kendaraan
                    const kCont = document.getElementById('tambah_hidden_kendaraan_container');
                    const kDisp = document.getElementById('tambah_kendaraan_display');
                    kCont.innerHTML = '';
                    if (data.kendaraans && data.kendaraans.length > 0) {
                        kDisp.innerHTML = data.kendaraans.map(k =>
                            `<div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-100 mb-1">
                                <i class="fa fa-car text-gray-400 text-xs"></i>
                                <span class="text-xs font-medium text-gray-700">${k.label}</span>
                            </div>`
                        ).join('');
                        data.kendaraans.forEach(k => {
                            const inp = document.createElement('input');
                            inp.type = 'hidden'; inp.name = 'kendaraan_ids[]'; inp.value = k.id;
                            kCont.appendChild(inp);
                        });
                    } else {
                        kDisp.innerHTML = '<span class="text-gray-400 italic">Tidak ada kendaraan terkait.</span>';
                    }

                    // Info cards dihapus — langsung ambil rental_details
                    window._tambahRentalDetails    = data.rental_details || [];
                    window._existingInvoiceCount   = data.existing_invoice_count ?? 0;
                    // Periode yang sudah PAID (dicoret hijau) — dari server
                    window._paidPeriodesCount      = data.paid_periodes_count ?? 0;
                    // Periode yang sudah ter-cover semua invoice paid+unpaid (dicoret biru)
                    window._coveredPeriodesCount   = data.covered_periodes_count ?? 0;

                    // Auto-fill Seksi 3: Informasi Invoice
                    setVal('tambah_satuan',  data.satuan  ?? 'Car Rent/Day');
                    setVal('tambah_pengirim', data.pengirim ?? '');
                    setVal('tambah_ppn',     data.ppn     ?? 0);
                    setVal('tambah_pph',     data.pph     ?? 0);

                    // Subtotal = total penawaran per bulan (tidak dikali jumlah periode)
                    const totalPerPeriode = parseFloat(data.total_per_periode) || 0;
                    const jumlahPeriode   = parseInt(data.jumlah_periode) || 1;
                    window._tambahSubTotalBase = totalPerPeriode;
                    recalcTambahTotal();

                    // Sembunyikan info periode (tidak perlu tampilkan perkalian)
                    const periodeInfoEl = document.getElementById('tambah_periode_info');
                    if (periodeInfoEl) periodeInfoEl.classList.add('hidden');

                    // Auto-fill Seksi 5: Penandatangan
                    setVal('tambah_staff',        data.staff         ?? '');
                    setVal('tambah_name_staff',   data.name_staff    ?? '');
                    setVal('tambah_direktur',     data.direktur      ?? '');
                    setVal('tambah_name_direktur', data.name_direktur ?? '');
                })
                .catch(() => {
                    statusBadge.className = 'inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-red-100 text-red-700';
                    statusBadge.innerHTML = '<i class="fa fa-exclamation-circle text-[10px]"></i> Gagal menghubungi server';
                });
            }

            // Trigger on dropdown change
            document.getElementById('tambah_no_kontrak_input').addEventListener('change', function() {
                doLookupKontrak(this.value);
            });

            // Reset form saat modal tambah ditutup — hook langsung ke tombol close & backdrop
            function closeTambahModal() {
                closeModal(modalTambah);
                resetTambahForm();
                document.getElementById('tambah_no_kontrak_input').value = '';
            }
            document.getElementById('closeTambah').onclick  = () => closeTambahModal();
            document.getElementById('closeTambah2').onclick = () => closeTambahModal();
            modalTambah.addEventListener('click', e => {
                if (e.target === modalTambah) closeTambahModal();
            });
            // ===================== END LOOKUP =====================

            // ===================== MODAL SHOW =====================
            const modalShow = document.getElementById('modalShow');
            document.getElementById('closeShow').onclick = () => closeModal(modalShow);
            document.getElementById('closeShow2').onclick = () => closeModal(modalShow);
            modalShow.addEventListener('click', e => {
                if (e.target === modalShow) closeModal(modalShow);
            });

            const statusColorMap = {
                lunas: 'bg-green-100 text-green-700',
                partial: 'bg-yellow-100 text-yellow-700',
                overdue: 'bg-red-100 text-red-700',
                draft: 'bg-gray-100 text-gray-700',
                paid: 'bg-green-100 text-green-700',
                unpaid: 'bg-red-100 text-red-700',
            };

            document.querySelectorAll('.showBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const d = this.dataset;

                    // subtitle
                    document.getElementById('show_subtitle').textContent = d.invoice_no;

                    // Seksi 1
                    setText('show_invoice_no', d.invoice_no);
                    setText('show_invoice_date', d.invoice_date);
                    setText('show_customer_name', d.customer_name);
                    setText('show_type', d.type ? d.type.charAt(0).toUpperCase() + d.type.slice(1) : '-');
                    setText('show_customer_address', d.customer_address);
                    setText('show_telephone', d.telephone);
                    setText('show_email', d.email);
                    setText('show_contact_person', d.contact_person);

                    // Seksi 2
                    setText('show_penawaran', d.penawaran);
                    setText('show_kontrak', d.kontrak);
                    setText('show_kendaraan', d.kendaraan);

                    // Seksi 3
                    setText('show_satuan', d.satuan);
                    setText('show_pengirim', d.pengirim);
                    setText('show_ppn', (d.ppn || '0') + '%');
                    setText('show_pph', (d.pph || '0') + '%');
                    setText('show_total', formatRupiah(d.total));

                    // Seksi 4 � status badges
                    const statusEl = document.getElementById('show_status');
                    statusEl.textContent = d.status ? d.status.toUpperCase() : '-';
                    statusEl.className = 'px-3 py-1 rounded-full text-xs font-semibold ' + (statusColorMap[d
                        .status] ?? 'bg-gray-100 text-gray-700');

                    const payEl = document.getElementById('show_payment_status');
                    payEl.textContent = d.payment_status ? d.payment_status.toUpperCase() : '-';
                    payEl.className = 'px-3 py-1 rounded-full text-xs font-semibold ' + (statusColorMap[d
                        .payment_status] ?? 'bg-gray-100 text-gray-700');

                    // Seksi 5
                    setText('show_staff', d.staff);
                    setText('show_name_staff', d.name_staff);
                    setText('show_direktur', d.direktur);
                    setText('show_name_direktur', d.name_direktur);

                    const ttdStaffWrap = document.getElementById('show_ttd_staff_wrap');
                    if (d.ttd_staff) {
                        document.getElementById('show_ttd_staff_img').src = '/' + d.ttd_staff;
                        ttdStaffWrap.classList.remove('hidden');
                    } else {
                        ttdStaffWrap.classList.add('hidden');
                    }

                    const ttdDirWrap = document.getElementById('show_ttd_direktur_wrap');
                    if (d.ttd_direktur) {
                        document.getElementById('show_ttd_direktur_img').src = '/' + d.ttd_direktur;
                        ttdDirWrap.classList.remove('hidden');
                    } else {
                        ttdDirWrap.classList.add('hidden');
                    }

                    openModal(modalShow);
                });
            });

            // ===================== MODAL EDIT =====================
            const modalEdit = document.getElementById('modalEdit');
            const formEdit = document.getElementById('formEdit');
            document.getElementById('closeEdit').onclick = () => closeModal(modalEdit);
            document.getElementById('closeEdit2').onclick = () => closeModal(modalEdit);
            modalEdit.addEventListener('click', e => {
                if (e.target === modalEdit) closeModal(modalEdit);
            });

            // Helper: rebuild relasi rows untuk modal edit dari array IDs
            function rebuildRelRows(prefix, type, selectedIds) {
                const container = document.getElementById(prefix + '_' + type + '_rows');
                if (!container) return;
                // Gunakan window.buildRelRow agar bisa diakses lintas DOMContentLoaded block
                const _buildRelRow = window.buildRelRow || buildRelRow;
                const _addRelRow   = window.addRelRow   || addRelRow;
                // Reset ke satu row kosong
                container.innerHTML = _buildRelRow(prefix, type, '');
                // Populate baris pertama
                const firstSelect = container.querySelector('select');
                if (firstSelect && selectedIds.length > 0) firstSelect.value = selectedIds[0];
                // Tambah baris ekstra jika lebih dari 1
                for (let i = 1; i < selectedIds.length; i++) {
                    _addRelRow(prefix, type, selectedIds[i]);
                }
            }

            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const d = this.dataset;
                    const invId = d.id;

                    // Set form action
                    formEdit.action = "{{ url('admin/invoices') }}/" + invId;

                    // Seksi 1
                    setVal('edit_invoice_no', d.invoice_no);
                    setVal('edit_invoice_date', d.invoice_date);
                    setVal('edit_customer_name', d.customer_name);
                    setVal('edit_type', d.type);
                    setVal('edit_customer_address', d.customer_address);
                    setVal('edit_telephone', d.telephone);
                    setVal('edit_email', d.email);
                    setVal('edit_contact_person', d.contact_person);

                    // Seksi 3
                    setVal('edit_satuan', d.satuan);
                    setVal('edit_pengirim', d.pengirim);
                    setVal('edit_ppn', d.ppn ?? 0);
                    setVal('edit_pph', d.pph ?? 0);

                    // Hitung kembali subtotal dari grand total tersimpan
                    // subtotal = total / (1 + ppn/100)
                    const editPpnPct = parseFloat(d.ppn) || 0;
                    const editGrand  = parseFloat(d.total) || 0;
                    window._editSubTotalBase = editPpnPct > 0
                        ? Math.round(editGrand / (1 + editPpnPct / 100))
                        : editGrand;
                    recalcEditTotal();

                    // Seksi 4
                    setVal('edit_status', d.status ?? 'draft');
                    setVal('edit_payment_status', d.payment_status ?? 'unpaid');

                    // Seksi 5
                    setVal('edit_staff', d.staff);
                    setVal('edit_name_staff', d.name_staff);
                    setVal('edit_direktur', d.direktur);
                    setVal('edit_name_direktur', d.name_direktur);

                    // TTD picker
                    const pickerStaff = document.querySelector('.ttd-picker[data-uid="edit_staff"]');
                    const pickerDir   = document.querySelector('.ttd-picker[data-uid="edit_direktur"]');
                    if (pickerStaff && d.ttd_staff) pickerStaff.setCurrentTtd(d.ttd_staff, '/storage/' + d.ttd_staff);
                    if (pickerDir && d.ttd_direktur) pickerDir.setCurrentTtd(d.ttd_direktur, '/storage/' + d.ttd_direktur);

                    // Seksi 2: Load pivot relasi via AJAX
                    try {
                        const resp = await fetch("{{ url('admin/invoices') }}/" + invId + "/edit", {
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const json = await resp.json();
                        rebuildRelRows('edit', 'penawaran', json.penawaran_ids ?? []);
                        rebuildRelRows('edit', 'kontrak',   json.kontrak_ids   ?? []);
                        rebuildRelRows('edit', 'kendaraan', json.kendaraan_ids ?? []);
                    } catch(e) {
                        rebuildRelRows('edit', 'penawaran', []);
                        rebuildRelRows('edit', 'kontrak',   []);
                        rebuildRelRows('edit', 'kendaraan', []);
                    }

                    openModal(modalEdit);
                });
            });

        }); // end DOMContentLoaded — script block 1
        </script>

        {{-- ====== RELASI ROWS TEMPLATE DATA ====== --}}
        <script>
        // Data untuk opsi select (dari Blade ke JS)
        const PENAWARAN_OPTIONS = @json($penawarans->map(fn($p) => ['id' => $p->id, 'label' => $p->no_penawaran . ' — ' . $p->customer_name]));
        const KONTRAK_OPTIONS   = @json($kontraks->map(fn($k) => ['id' => $k->id, 'label' => $k->no_kontrak ?? '#'.$k->id]));
        const KENDARAAN_OPTIONS = @json($kendaraans->map(fn($kd) => ['id' => $kd->id, 'label' => $kd->merk . ' — ' . $kd->nopol]));

        // Global: dibagi antara script block 1 dan script block 2
        window._tambahRentalDetails = window._tambahRentalDetails || [];


        document.addEventListener('DOMContentLoaded', function () {

        function getOptions(type) {
            const map = { penawaran: PENAWARAN_OPTIONS, kontrak: KONTRAK_OPTIONS, kendaraan: KENDARAAN_OPTIONS };
            return map[type] || [];
        }

        function buildRelRow(prefix, type, selectedId = '') {
            const opts = getOptions(type);
            const optHtml = '<option value="">� Tidak ada �</option>' +
                opts.map(o => `<option value="${o.id}" ${String(o.id) === String(selectedId) ? 'selected' : ''}>${o.label}</option>`).join('');
            return `<div class="flex gap-2 items-center rel-row">
                <select name="${type}_ids[]" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">${optHtml}</select>
                <button type="button" onclick="removeRelRow(this)" class="text-red-400 hover:text-red-600 text-sm px-1"><i class="fa fa-times"></i></button>
            </div>`;
        }

        function addRelRow(prefix, type, selectedId = '') {
            const container = document.getElementById(prefix + '_' + type + '_rows');
            if (!container) return;
            const div = document.createElement('div');
            div.innerHTML = buildRelRow(prefix, type, selectedId);
            container.appendChild(div.firstElementChild);
        }

        function removeRelRow(btn) {
            const row = btn.closest('.rel-row');
            const container = row.parentElement;
            // Jangan hapus kalau tinggal satu baris
            if (container.querySelectorAll('.rel-row').length <= 1) {
                row.querySelector('select').value = '';
                return;
            }
            row.remove();
        }

        // Expose ke window agar bisa diakses dari DOMContentLoaded block lain
        window.buildRelRow  = buildRelRow;
        window.addRelRow    = addRelRow;
        window.removeRelRow = removeRelRow;

        // ====== TAB SWITCH TAMBAH ======
        let currentTambahInvoiceId = null;
        window._currentTambahInvoiceId = null;
        // Setter dipanggil dari global unlockTab2
        window._setCurrentTambahInvoiceId = function(id) {
            currentTambahInvoiceId = id;
            window._currentTambahInvoiceId = id;
        };

        // Auto-populate periode & remak dari data penawaran kontrak
        async function autoPopulatePeriodeRemak(invoiceId) {
            console.log('[autoPopulate] invoiceId:', invoiceId, 'rentalDetails:', window._tambahRentalDetails);

            if (!window._tambahRentalDetails || window._tambahRentalDetails.length === 0) {
                console.warn('[autoPopulate] _tambahRentalDetails kosong, skip.');
                return;
            }

            const csrf = document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}';

            for (const entry of window._tambahRentalDetails) {
                if (!entry.tanggal_mulai) continue;

                // 1. Buat periode
                let periodeId = null;
                try {
                    const periodeResp = await fetch('/admin/invoices/' + invoiceId + '/periodes', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN':  csrf,
                            'Accept':        'application/json',
                            'Content-Type':  'application/json',
                        },
                        body: JSON.stringify({
                            periode_awal:  entry.tanggal_mulai,
                            periode_akhir: entry.tanggal_selesai || entry.tanggal_mulai,
                        }),
                    });
                    const periodeJson = await periodeResp.json();
                    console.log('[autoPopulate] periodeResp status:', periodeResp.status, 'json:', periodeJson);
                    periodeId = periodeJson.id ?? periodeJson.periode?.id ?? null;
                } catch (err) {
                    console.error('[autoPopulate] Gagal buat periode:', err);
                }

                if (!periodeId) {
                    console.warn('[autoPopulate] periodeId null, skip remaks.');
                    continue;
                }

                // 2. Remak per item kendaraan
                const remakItems = entry.remak_items || [];
                console.log('[autoPopulate] remak_items:', remakItems);
                for (const item of remakItems) {
                    try {
                        const remakResp = await fetch('/admin/invoices/' + invoiceId + '/periodes/' + periodeId + '/remaks', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN':  csrf,
                                'Accept':        'application/json',
                                'Content-Type':  'application/json',
                            },
                            body: JSON.stringify({
                                remaks: item.kendaraan || 'Item',
                                qty:    item.qty   || 1,
                                price:  item.price || 0,
                            }),
                        });
                        const remakJson = await remakResp.json();
                        console.log('[autoPopulate] remak response:', remakJson);
                    } catch (err) {
                        console.error('[autoPopulate] Gagal buat remak:', err);
                    }
                }

                // 3. Remak driver (jika ada)
                if (entry.biaya_driver > 0) {
                    const driverLabel = entry.nama_driver ? 'Driver: ' + entry.nama_driver : 'Biaya Driver';
                    try {
                        await fetch('/admin/invoices/' + invoiceId + '/periodes/' + periodeId + '/remaks', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN':  csrf,
                                'Accept':        'application/json',
                                'Content-Type':  'application/json',
                            },
                            body: JSON.stringify({
                                remaks: driverLabel,
                                qty:    entry.durasi_nilai ?? 1,
                                price:  entry.biaya_driver,
                            }),
                        });
                    } catch (err) {
                        console.error('[autoPopulate] Gagal buat remak driver:', err);
                    }
                }
            }
            console.log('[autoPopulate] selesai.');
        }

        // ====== TAB 1: LANJUT KE PERIODE (hanya validasi + pindah tab, BELUM simpan) ======
        function handleLanjutKePeriode() {
            const form = document.getElementById('formTambah');
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('border-red-400');
                    valid = false;
                } else {
                    field.classList.remove('border-red-400');
                }
            });
            if (!valid) {
                alert('Harap lengkapi semua field yang wajib diisi terlebih dahulu.');
                return;
            }

            // Aktifkan tab 2 tanpa invoice_id dulu (mode draft lokal)
            const tab2Btn = document.getElementById('tambah_tab2_btn');
            if (tab2Btn) {
                tab2Btn.disabled = false;
                tab2Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-blue-600 rounded-tr-lg cursor-pointer';
            }
            // Tandai bahwa kita di mode "belum disimpan"
            window._invoiceDraft = true;

            // Tampilkan tab 2 (periodeListTambah kosong, user tambah manual)
            const tab1Btn = document.getElementById('tambah_tab1_btn');
            const tab1Con = document.getElementById('tambah_tab1_content');
            const tab2Con = document.getElementById('tambah_tab2_content');
            tab1Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-blue-600 rounded-tl-lg';
            tab2Btn.className = 'px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-blue-50/50 rounded-tr-lg';
            tab1Con.classList.add('hidden');
            tab2Con.classList.remove('hidden');

            // Label invoice sementara
            const label = document.getElementById('tambah_invoice_no_label');
            if (label) label.textContent = '(akan dibuat setelah simpan)';

            // Auto-populate _lokalPeriodes dari rental_details kontrak
            // Setiap entry = 1 periode bulan, semua kendaraan jadi remaks per periode
            const rentalDetails = window._tambahRentalDetails || [];
            if (rentalDetails.length > 0 && window._lokalPeriodes.length === 0) {
                window._lokalPeriodes = rentalDetails
                    .filter(entry => entry.tanggal_mulai)
                    .map(entry => {
                        const remaks = (entry.remak_items || []).map(item => ({
                            text:  item.kendaraan || 'Item',
                            qty:   item.qty   || 1,
                            price: item.price || 0,
                        }));
                        if (entry.biaya_driver > 0) {
                            remaks.push({
                                text:  entry.nama_driver ? 'Driver: ' + entry.nama_driver : 'Biaya Driver',
                                qty:   entry.durasi_nilai ?? 1,
                                price: entry.biaya_driver,
                            });
                        }
                        return {
                            awal:   entry.tanggal_mulai,
                            akhir:  entry.tanggal_selesai || entry.tanggal_mulai,
                            remaks: remaks,
                        };
                    });
            }

            // Render daftar periode lokal
            renderPeriodeListLokal();
        }
        // Expose ke global agar bisa dipanggil dari onclick inline
        window.handleLanjutKePeriode = handleLanjutKePeriode;

        // ====== TAB 2: SIMPAN INVOICE (AJAX store + periode lokal) ======
        document.getElementById('btnSelesaiTambah')?.addEventListener('click', async function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Menyimpan...';

            const form      = document.getElementById('formTambah');
            const formData  = new FormData(form);

            // Inject grand total yang sudah mencakup semua periode terpilih
            // (tambah_total sudah di-sync oleh onFuturePeriodeCheck)
            const totalInput = document.getElementById('tambah_total');
            if (totalInput) formData.set('total', totalInput.value);

            // Hitung jumlah periode yang akan disimpan = aktif + future terpilih
            const coveredCntPre  = window._coveredPeriodesCount ?? 0;
            const selectedPisPre = window._selectedFuturePis || [];
            const periodeCountPre = 1 + selectedPisPre.length; // 1 = periode aktif + jumlah future
            formData.set('periode_count', periodeCountPre);

            try {
                // 1. Simpan invoice via AJAX
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData,
                });
                const json = await resp.json();

                if (!json.success) {
                    alert(json.message || 'Gagal menyimpan invoice.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-save"></i> Simpan Invoice';
                    return;
                }

                const invoiceId = json.invoice_id;

                // 2. Tentukan periode mana yang perlu disimpan:
                //    - Selalu simpan periode AKTIF (index = verifiedCount)
                //    - Tambah future yang dipilih user via checkbox
                const csrf          = document.querySelector('meta[name=csrf-token]')?.content || '';
                const lokalPeriodes = window._lokalPeriodes || [];
                const verifiedCnt   = window._existingInvoiceCount ?? 0;
                const coveredCnt    = window._coveredPeriodesCount ?? verifiedCnt;

                // Kumpulkan index future yang dipilih — dari global (bukan DOM agar tidak hilang saat re-render)
                const checkedFuturePis = window._selectedFuturePis || [];

                // Periode yang akan disimpan = [periode aktif (coveredCnt)] + [future terpilih], urut
                const periodesToSave = lokalPeriodes.filter((p, idx) => {
                    if (idx === coveredCnt) return true;            // periode aktif wajib
                    return checkedFuturePis.includes(idx);          // future yang dipilih
                });

                for (const periode of periodesToSave) {
                    let periodeId = null;
                    try {
                        const pr = await fetch('/admin/invoices/' + invoiceId + '/periodes', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ periode_awal: periode.awal, periode_akhir: periode.akhir }),
                        });
                        const pj = await pr.json();
                        periodeId = pj.id ?? pj.periode?.id ?? null;
                    } catch(e) { console.error('Gagal simpan periode:', e); }

                    if (!periodeId) continue;
                    for (const remak of (periode.remaks || [])) {
                        try {
                            await fetch('/admin/invoices/' + invoiceId + '/periodes/' + periodeId + '/remaks', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                                body: JSON.stringify({ remaks: remak.text, qty: remak.qty, price: remak.price }),
                            });
                        } catch(e) { console.error('Gagal simpan remak:', e); }
                    }
                }

                // 3. Recalculate summary total setelah periodes/remaks tersimpan
                // Kirim juga jumlah periode yang disimpan agar periode_count tersimpan benar
                try {
                    await fetch('/admin/invoice/' + invoiceId + '/recalculate-summary', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ periode_count: periodesToSave.length }),
                    });
                } catch(e) { console.error('Gagal recalculate summary:', e); }

                // 4. Selesai — tutup modal & reload
                window._lokalPeriodes = [];
                const modal = document.getElementById('modalTambah');
                if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
                window.location.reload();

            } catch(err) {
                alert('Terjadi kesalahan: ' + err.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-save"></i> Simpan Invoice';
            }
        });

        // ====== PERIODE LOKAL (sebelum invoice disimpan) ======
        window._lokalPeriodes = []; // [{awal, akhir, remaks:[{text,qty,price}]}]

        function renderPeriodeListLokal() {
            const list = document.getElementById('periodeListTambah');
            if (!list) return;
            const periodes = window._lokalPeriodes;
            if (!periodes.length) {
                list.innerHTML = '<div class="py-10 text-center text-gray-400 text-xs"><i class="fa fa-calendar-alt text-2xl mb-2 block"></i>Belum ada periode. Klik "Tambah Periode".</div>';
                return;
            }

            // Periode aktif = index ke covered_periodes_count (sudah ter-cover invoice sebelumnya)
            // isPaid = sudah lunas (paid), isInvoiced = sudah diinvoice tapi belum lunas, isActive = aktif sekarang
            const paidCount      = window._paidPeriodesCount    ?? 0;   // dicoret hijau
            const coveredCount   = window._coveredPeriodesCount ?? paidCount; // dicoret biru (paid+unpaid)
            // verifiedCount tidak dipakai lagi untuk render — gunakan paidCount dan coveredCount

            list.innerHTML = periodes.map((p, pi) => {
                const awal   = fmtDate(p.awal);
                const akhir  = p.akhir && p.akhir !== p.awal ? fmtDate(p.akhir) : '';
                const label  = awal + (akhir ? ' – ' + akhir : '');
                const subtot = (p.remaks || []).reduce((s, r) => s + (r.qty || 1) * (r.price || 0), 0);

                const isPaid      = pi < paidCount;                    // lunas: centang hijau + coret hijau
                const isInvoiced  = pi >= paidCount && pi < coveredCount; // sudah diinvoice tapi belum lunas: coret abu
                const isActive    = pi === coveredCount;               // periode aktif saat ini
                const isFuture    = pi > coveredCount;                 // belum dibuat invoice

                // ── Sudah dibayar (lunas): centang hijau + coret hijau ──────────
                if (isPaid) {
                    return `<div class="px-4 py-3 flex items-center justify-between border-b border-gray-50 bg-green-50/40">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 flex items-center justify-center rounded-full bg-green-100 flex-shrink-0">
                                <i class="fa fa-check text-[10px] text-green-600"></i>
                            </span>
                            <span class="text-xs font-semibold text-green-700 line-through">${label}</span>
                            <span class="text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded-full">Lunas</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-green-600 font-semibold">${rpFmt(subtot)}</span>
                            <button onclick="deleteLokalPeriode(${pi})" class="text-red-300 hover:text-red-500 text-xs px-1"><i class="fa fa-trash text-[10px]"></i></button>
                        </div>
                    </div>`;
                }

                // ── Sudah diinvoice tapi belum lunas: coret abu ──────────
                if (isInvoiced) {
                    return `<div class="px-4 py-3 flex items-center justify-between border-b border-gray-50 bg-blue-50/20">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 flex items-center justify-center rounded-full bg-blue-100 flex-shrink-0">
                                <i class="fa fa-file-invoice text-[10px] text-blue-500"></i>
                            </span>
                            <span class="text-xs font-medium text-blue-400 line-through">${label}</span>
                            <span class="text-[10px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full">Ditagih</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-blue-400">${rpFmt(subtot)}</span>
                            <button onclick="deleteLokalPeriode(${pi})" class="text-red-300 hover:text-red-500 text-xs px-1"><i class="fa fa-trash text-[10px]"></i></button>
                        </div>
                    </div>`;
                }

                // ── Belum saatnya: checkbox abu collapsed ─────────────
                if (isFuture) {
                    const isSelectedFuture = (window._selectedFuturePis || []).includes(pi);
                    return `<div class="periode-future-row px-4 py-3 flex items-center justify-between border-b border-gray-100 ${isSelectedFuture ? 'bg-blue-50/40' : 'bg-gray-50/60 hover:bg-gray-100/60'} transition-colors"
                        data-pi="${pi}" data-awal="${p.awal}" data-akhir="${p.akhir || p.awal}">
                        <label class="flex items-center gap-2 cursor-pointer flex-1 min-w-0">
                            <input type="checkbox" class="periode-future-cb w-4 h-4 rounded accent-blue-600 flex-shrink-0" data-pi="${pi}"
                                ${isSelectedFuture ? 'checked' : ''}
                                onchange="onFuturePeriodeCheck(this)">
                            <span class="text-xs ${isSelectedFuture ? 'text-blue-700 font-semibold' : 'text-gray-500 font-medium'} truncate">${label}</span>
                        </label>
                        <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                            <span class="text-xs ${isSelectedFuture ? 'text-blue-600 font-semibold' : 'text-gray-400'}">${rpFmt(subtot)}</span>
                            <button onclick="deleteLokalPeriode(${pi})" class="text-red-300 hover:text-red-500 text-xs px-1"><i class="fa fa-trash text-[10px]"></i></button>
                        </div>
                    </div>`;
                }

                // ── Periode AKTIF: expanded, border biru — checkbox wajib tercentang ──
                const remakRows = (p.remaks || []).map((r, ri) => `
                    <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 text-xs border border-blue-100">
                        <span class="text-gray-700 flex-1">${r.text}</span>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-2">
                            <span class="text-gray-400">${r.qty}x</span>
                            <span class="font-semibold text-gray-800">${rpFmt(r.price)}</span>
                            <button onclick="deleteLokalRemak(${pi},${ri})" class="text-red-400 hover:text-red-600"><i class="fa fa-times text-[10px]"></i></button>
                        </div>
                    </div>`).join('');

                return `<div class="border-l-4 border-blue-500 bg-blue-50/30">
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-2 flex-wrap">
                            <input type="checkbox" class="periode-aktif-cb w-4 h-4 rounded accent-blue-600 flex-shrink-0"
                                checked disabled title="Periode aktif selalu termasuk">
                            <span class="text-xs font-bold text-blue-800">${label}</span>
                            <span class="text-[10px] font-semibold bg-blue-600 text-white px-2 py-0.5 rounded-full animate-pulse">Aktif</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button onclick="openLokalRemakModal(${pi})" class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                                <i class="fa fa-plus text-[10px]"></i> Remak
                            </button>
                            <button onclick="deleteLokalPeriode(${pi})" class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                                <i class="fa fa-trash text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                    <div class="px-4 pb-3 space-y-1">
                        ${remakRows || '<p class="text-xs text-gray-400 italic">Belum ada remak. Klik "+ Remak".</p>'}
                    </div>
                    <div class="px-4 pb-3 flex justify-end">
                        <span class="text-xs font-bold text-blue-700">Sub Total: ${rpFmt(subtot)}</span>
                    </div>
                </div>`;
            }).join('');

            // ── Panel preview multi-periode (muncul saat ada future checkbox yang dipilih) ──
            const listEl2 = document.getElementById('periodeListTambah');
            if (listEl2 && !listEl2.querySelector('#futurePeriodeBanner')) {
                listEl2.insertAdjacentHTML('beforeend',
                    `<div id="futurePeriodeBanner" class="hidden border-t border-blue-200 bg-blue-50/60 px-4 py-3">
                        <div class="flex items-start justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <i class="fa fa-layer-group text-blue-500 text-xs"></i>
                                <span class="text-xs font-semibold text-blue-700">Invoice mencakup:</span>
                                <span id="futureBannerAwal" class="text-xs text-blue-600 font-medium">–</span>
                                <span class="text-blue-300 text-xs">→</span>
                                <span id="futureBannerAkhir" class="text-xs text-blue-600 font-medium">–</span>
                                <span class="text-xs text-blue-400" id="futureBannerCount"></span>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center gap-2 text-xs text-blue-600">
                                    <span>Sub Total</span>
                                    <span id="futureBannerSubtotal" class="font-semibold">–</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-blue-500">
                                    <span id="futureBannerPpnLabel">PPN</span>
                                    <span id="futureBannerPpn">-</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs border-t border-blue-200 pt-1 mt-1">
                                    <span class="font-semibold text-blue-700">Grand Total</span>
                                    <span id="futureBannerTotal" class="font-bold text-blue-800">–</span>
                                </div>
                            </div>
                        </div>
                    </div>`
                );
            }

            // Update summary
            // Base = subtotal periode aktif (periode pertama yang belum ter-cover invoice)
            const paidCountLokal    = window._paidPeriodesCount    ?? 0;
            const coveredCountLokal = window._coveredPeriodesCount ?? paidCountLokal;
            const aktivIdx          = Math.min(coveredCountLokal, periodes.length - 1);
            let base = 0;
            if (periodes[aktivIdx]) {
                (periodes[aktivIdx].remaks || []).forEach(r => {
                    base += (r.qty || 1) * (r.price || 0);
                });
            }

            const jumlahPeriode  = periodes.length;
            const basePerPeriode = base; // subtotal periode aktif (bukan rata-rata)

            // Simpan base PER PERIODE agar recalcTambahTotal pakai nilai yang benar
            window._tambahSubTotalBase = basePerPeriode;

            // Isi baris sub total per item kendaraan dari periode aktif
            const aktivPeriode = periodes[aktivIdx];
            const itemRowsEl   = document.getElementById('tambahSummaryItemRows');
            if (itemRowsEl && aktivPeriode) {
                itemRowsEl.innerHTML = (aktivPeriode.remaks || []).map(r => {
                    const subtotItem = (r.qty || 1) * (r.price || 0);
                    return `<tr>
                        <td class="text-gray-400 pr-8 py-0.5 text-xs">${r.text || '-'}</td>
                        <td class="text-right text-xs text-gray-600">${rpFmt(subtotItem)}</td>
                    </tr>`;
                }).join('');
            }

            // Isi kolom "Sub Total" dan "Jumlah Periode"
            const elSubTotal = document.getElementById('tambahSummaryTotal');
            if (elSubTotal) elSubTotal.textContent = rpFmt(basePerPeriode);
            const elJumlah = document.getElementById('tambahSummarySubTotal');
            if (elJumlah) elJumlah.textContent = jumlahPeriode + ' periode';

            // "Bayar Ke X / N" — default 1 periode aktif
            if (typeof updateBayarKe === 'function') updateBayarKe(1);

            recalcTambahTotal();
        }
        window.renderPeriodeListLokal = renderPeriodeListLokal;

        function deleteLokalPeriode(pi) {
            if (!confirm('Hapus periode ini?')) return;
            window._lokalPeriodes.splice(pi, 1);
            renderPeriodeListLokal();
        }

        function deleteLokalRemak(pi, ri) {
            window._lokalPeriodes[pi].remaks.splice(ri, 1);
            renderPeriodeListLokal();
        }

        function openLokalRemakModal(pi) {
            window._aktivLokalPeriodeIdx = pi;
            ['tambahRemakText','tambahRemakQty','tambahRemakPrice'].forEach((id, i) => {
                const el = document.getElementById(id);
                if (el) el.value = i === 0 ? '' : (i === 1 ? 1 : 0);
            });
            const m = document.getElementById('modalTambahRemak');
            if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
        }
        window.deleteLokalPeriode  = deleteLokalPeriode;
        window.deleteLokalRemak    = deleteLokalRemak;
        window.openLokalRemakModal = openLokalRemakModal;

        // ====== HELPER: Update "Bayar Ke X/N" di summary ======
        function updateBayarKe(jumlahDipilih) {
            const paidCount    = window._paidPeriodesCount    ?? 0;
            const coveredCount = window._coveredPeriodesCount ?? paidCount;
            const totalPeriode = (window._lokalPeriodes || []).length;
            const bayarKeRow     = document.getElementById('bayarKeRow');
            const bayarKeEl      = document.getElementById('tambahSummaryBayarKe');
            const sisaRow        = document.getElementById('sisaRow');
            const sisaEl         = document.getElementById('tambahSummarySisa');
            if (!bayarKeEl) return;

            if (totalPeriode === 0) {
                if (bayarKeRow) bayarKeRow.classList.add('hidden');
                if (sisaRow)    sisaRow.classList.add('hidden');
                return;
            }

            // jumlahDipilih = berapa periode yang dicakup invoice ini (default 1 = periode aktif saja)
            const count    = jumlahDipilih || 1;
            const dariKe   = coveredCount + 1;
            const sampaiKe = Math.min(coveredCount + count, totalPeriode);

            // Sisa = total - sampaiKe (periode yang belum dibayar setelah invoice ini)
            const sisa = totalPeriode - sampaiKe;

            if (bayarKeRow) bayarKeRow.classList.remove('hidden');
            bayarKeEl.textContent = dariKe === sampaiKe
                ? `${dariKe} / ${totalPeriode}`
                : `${dariKe} – ${sampaiKe} / ${totalPeriode}`;

            if (sisaRow && sisaEl) {
                if (sisa > 0) {
                    sisaRow.classList.remove('hidden');
                    sisaEl.className = 'text-right text-[10px] font-medium text-orange-500';
                    sisaEl.textContent = sisa + 'x lagi';
                } else {
                    sisaRow.classList.remove('hidden');
                    sisaEl.className = 'text-right text-[10px] font-medium text-green-600';
                    sisaEl.textContent = 'Semua periode lunas';
                }
            }
        }
        window.updateBayarKe = updateBayarKe;
        // Menyimpan index (data-pi) dari checkbox terakhir yang diubah secara manual
        window._lastCheckedFuturePi = null;
        // Menyimpan daftar pi yang sedang dipilih user (persisten walau DOM di-render ulang)
        window._selectedFuturePis = [];

        function onFuturePeriodeCheck(changedCb) {
            const allCbs   = Array.from(document.querySelectorAll('.periode-future-cb'));
            const periodes = window._lokalPeriodes || [];

            // Ambil index posisi DOM dari semua checkbox future (urut DOM = urut tampilan)
            const domIndices = allCbs.map(cb => parseInt(cb.dataset.pi));

            const changedPi   = parseInt(changedCb.dataset.pi);
            const changedPos  = domIndices.indexOf(changedPi);
            const isNowChecked = changedCb.checked;

            if (isNowChecked) {
                // ── User mencentang sebuah periode ──────────────────────────
                // Cari checkbox pertama yang sudah tercentang sebelumnya (posisi paling kecil)
                const alreadyChecked = allCbs
                    .filter(cb => cb !== changedCb && cb.checked)
                    .map(cb => domIndices.indexOf(parseInt(cb.dataset.pi)));

                if (alreadyChecked.length > 0) {
                    const minPos = Math.min(...alreadyChecked);
                    const maxPos = Math.max(Math.max(...alreadyChecked), changedPos);

                    // Isi semua checkbox di antara minPos dan maxPos (fill range)
                    allCbs.forEach((cb, idx) => {
                        if (idx >= minPos && idx <= maxPos) {
                            cb.checked = true;
                        }
                    });
                }

                window._lastCheckedFuturePi = changedPi;

            } else {
                // ── User UN-centang sebuah periode ──────────────────────────
                // Harus jaga konsistensi: tidak boleh ada "lubang" di tengah range
                // Cari semua yang masih tercentang dan un-centang semua yang di luar range baru
                const stillChecked = allCbs
                    .filter(cb => cb.checked)
                    .map(cb => domIndices.indexOf(parseInt(cb.dataset.pi)));

                if (stillChecked.length > 0) {
                    const minStill = Math.min(...stillChecked);
                    const maxStill = Math.max(...stillChecked);
                    // Un-centang semua di luar range yang tersisa (hapus lubang)
                    allCbs.forEach((cb, idx) => {
                        if (idx < minStill || idx > maxStill) {
                            cb.checked = false;
                        }
                    });
                }

                window._lastCheckedFuturePi = null;
            }

            // ── Update banner preview ───────────────────────────────────────
            const banner  = document.getElementById('futurePeriodeBanner');
            if (!banner) return;

            const finalChecked = Array.from(document.querySelectorAll('.periode-future-cb:checked'));

            // Simpan ke global agar persisten saat DOM di-render ulang
            window._selectedFuturePis = finalChecked.map(cb => parseInt(cb.dataset.pi));

            if (finalChecked.length === 0) {
                banner.classList.add('hidden');
                // Kembalikan summary ke nilai periode aktif
                renderPeriodeListLokal();
                return;
            }

            const selectedPeriodes = finalChecked
                .map(cb => ({ pi: parseInt(cb.dataset.pi), p: periodes[parseInt(cb.dataset.pi)] }))
                .filter(x => x.p)
                .sort((a, b) => new Date(a.p.awal) - new Date(b.p.awal));

            const firstP = selectedPeriodes[0].p;
            const lastP  = selectedPeriodes[selectedPeriodes.length - 1].p;

            // Hitung subtotal: periode AKTIF (wajib) + semua future yang dipilih
            const verifiedCountCb = window._existingInvoiceCount ?? 0;
            const coveredCountCb  = window._coveredPeriodesCount ?? verifiedCountCb;
            const aktivPeriodeCb  = periodes[coveredCountCb];

            // Mulai dari periode aktif (selalu ada)
            let subtotalSel = 0;
            if (aktivPeriodeCb) {
                (aktivPeriodeCb.remaks || []).forEach(r => { subtotalSel += (r.qty || 1) * (r.price || 0); });
            }
            // Tambah semua future yang dipilih
            selectedPeriodes.forEach(({ p }) => {
                (p.remaks || []).forEach(r => { subtotalSel += (r.qty || 1) * (r.price || 0); });
            });

            // Banner menampilkan range: dari periode aktif s/d future terakhir yang dipilih
            const bannerFirstP = aktivPeriodeCb || firstP;
            const bannerLastP  = lastP;

            // Ambil PPN dari field tab 1
            const ppnPct    = parseFloat(document.getElementById('tambah_ppn')?.value) || 0;
            const ppnNom    = Math.round(subtotalSel * ppnPct / 100);
            const grandSel  = subtotalSel + ppnNom;

            document.getElementById('futureBannerAwal').textContent     = fmtDate(bannerFirstP.awal);
            document.getElementById('futureBannerAkhir').textContent    = fmtDate(bannerLastP.akhir || bannerLastP.awal);
            document.getElementById('futureBannerCount').textContent    = '(' + (finalChecked.length + 1) + ' periode)';
            document.getElementById('futureBannerSubtotal').textContent = rpFmt(subtotalSel);
            document.getElementById('futureBannerPpnLabel').textContent = ppnPct > 0 ? 'PPN ' + ppnPct + '%' : 'PPN';
            document.getElementById('futureBannerPpn').textContent      = ppnNom > 0 ? rpFmt(ppnNom) : '-';
            document.getElementById('futureBannerTotal').textContent    = rpFmt(grandSel);
            banner.classList.remove('hidden');

            // ── Sinkronkan summary bawah dengan nilai terpilih ─────────────
            // Item rows: tampilkan remaks dari periode AKTIF + semua future terpilih
            const itemRowsEl = document.getElementById('tambahSummaryItemRows');
            if (itemRowsEl) {
                const allRemaks = [];
                // Sertakan periode aktif dulu
                if (aktivPeriodeCb) {
                    (aktivPeriodeCb.remaks || []).forEach(r => allRemaks.push(r));
                }
                // Tambah future terpilih
                selectedPeriodes.forEach(({ p }) => {
                    (p.remaks || []).forEach(r => allRemaks.push(r));
                });
                // Gabungkan item dengan teks sama
                const grouped = {};
                allRemaks.forEach(r => {
                    const key = r.text || 'Item';
                    if (!grouped[key]) grouped[key] = 0;
                    grouped[key] += (r.qty || 1) * (r.price || 0);
                });
                itemRowsEl.innerHTML = Object.entries(grouped).map(([lbl, tot]) =>
                    `<tr>
                        <td class="text-gray-400 pr-8 py-0.5 text-xs">${lbl}</td>
                        <td class="text-right text-xs text-gray-600">${rpFmt(tot)}</td>
                    </tr>`
                ).join('');
            }

            // Update Sub Total, PPN, Grand Total, Jumlah Periode di summary bawah
            const elSubTotal   = document.getElementById('tambahSummaryTotal');
            const elPpnLabel   = document.getElementById('tambahSummaryPpnLabel');
            const elPpn        = document.getElementById('tambahSummaryPpn');
            const elJumlah     = document.getElementById('tambahSummarySubTotal');
            const elGrand      = document.getElementById('tambahSummaryGrand');
            const elTotalInput = document.getElementById('tambah_total');
            if (elSubTotal)   elSubTotal.textContent   = rpFmt(subtotalSel);
            if (elPpnLabel)   elPpnLabel.textContent   = ppnPct > 0 ? 'PPN ' + ppnPct + '%' : 'PPN';
            if (elPpn)        elPpn.textContent        = ppnNom > 0 ? rpFmt(ppnNom) : '-';
            if (elJumlah)     elJumlah.textContent     = finalChecked.length + ' periode terpilih';
            if (elGrand)      elGrand.textContent      = rpFmt(grandSel);
            if (elTotalInput) elTotalInput.value       = grandSel;

            // Simpan base agar recalcTambahTotal() konsisten
            window._tambahSubTotalBase = subtotalSel;

            // "Bayar Ke X – Y / N" sesuai jumlah periode yang dipilih
            // +1 karena periode aktif selalu ikut
            if (typeof updateBayarKe === 'function') updateBayarKe(finalChecked.length + 1);
        }
        window.onFuturePeriodeCheck = onFuturePeriodeCheck;

        // ====== PERIODE DI TAB 2 MODAL TAMBAH ======
        function rpFmt(n) { return 'Rp ' + Number(n||0).toLocaleString('id-ID'); }
        function fmtDate(s) {
            if (!s) return '';
            const d = new Date(s);
            return d.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
        }

        async function loadPeriodesTambah() {
            if (!currentTambahInvoiceId) return;
            const url = '/admin/invoices/' + currentTambahInvoiceId + '/periodes';
            try {
                const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const periodes = await resp.json();
                renderPeriodeListTambah(periodes);
            } catch(e) { console.error(e); }
        }
        // Expose ke global agar bisa dipanggil dari block lain
        window.loadPeriodesTambah = loadPeriodesTambah;

        function renderPeriodeListTambah(periodes) {
            const list = document.getElementById('periodeListTambah');
            if (!periodes.length) {
                list.innerHTML = '<div class="py-10 text-center text-gray-400 text-xs"><i class="fa fa-calendar-alt text-2xl mb-2 block"></i>Belum ada periode.</div>';
                document.getElementById('tambahSummaryTotal').textContent = rpFmt(0);
                document.getElementById('tambahSummaryPpn').textContent = '-';
                document.getElementById('tambahSummarySubTotal').textContent = rpFmt(0);
                document.getElementById('tambahSummaryGrand').textContent = rpFmt(0);
                document.getElementById('tambah_total').value = 0;
                return;
            }

            // Ambil PPN dari field di tab 1
            const ppnPct = parseFloat(document.getElementById('tambah_ppn')?.value) || 0;
            const pphPct = parseFloat(document.getElementById('tambah_pph')?.value) || 0;

            let total = 0;
            list.innerHTML = periodes.map(p => {
                const remaks = p.remaks || [];
                const sub = remaks.reduce((s,r) => s + (r.qty||1)*(r.price||0), 0);
                total += sub;
                const awal = fmtDate(p.periode_awal);
                const akhir = p.periode_akhir ? ' – ' + fmtDate(p.periode_akhir) : '';
                const rows = remaks.map(r => `
                    <tr>
                        <td class="px-3 py-1.5 text-xs text-gray-700">${r.remaks||''}</td>
                        <td class="px-3 py-1.5 text-xs text-center">${r.qty||1}</td>
                        <td class="px-3 py-1.5 text-xs text-right">${(r.price||0).toLocaleString('id-ID')}</td>
                        <td class="px-3 py-1.5 text-xs text-right">${((r.qty||1)*(r.price||0)).toLocaleString('id-ID')}</td>
                    </tr>`).join('') || '<tr><td colspan="4" class="text-center py-2 text-gray-400 text-xs">Belum ada remaks</td></tr>';
                return `<div class="px-4 py-3 border-b">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-700">${awal}${akhir}</span>
                    </div>
                    <table class="w-full text-xs border border-gray-200 rounded">
                        <thead class="bg-gray-50"><tr>
                            <th class="px-3 py-1 text-left">Remaks</th>
                            <th class="px-3 py-1 text-center w-12">QTY</th>
                            <th class="px-3 py-1 text-right w-28">Harga</th>
                            <th class="px-3 py-1 text-right w-28">Sub Total</th>
                        </tr></thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>`;
            }).join('');

            // Simpan base agar recalcTambahTotal() bisa pakai saat PPN diubah realtime
            window._tambahSubTotalBase = total;

            // Hitung pajak
            const ppnNom    = Math.round(total * ppnPct / 100);
            const afterPpn  = total + ppnNom;
            const grandTotal = afterPpn; // PPh tidak mengurangi

            // Update summary
            document.getElementById('tambahSummaryTotal').textContent    = rpFmt(total);
            document.getElementById('tambahSummaryPpnLabel').textContent = ppnPct > 0 ? `PPN ${ppnPct}%` : 'PPN';
            document.getElementById('tambahSummaryPpn').textContent      = ppnNom > 0 ? rpFmt(ppnNom) : '-';
            document.getElementById('tambahSummarySubTotal').textContent = rpFmt(afterPpn);
            document.getElementById('tambahSummaryGrand').textContent    = rpFmt(grandTotal);

            // Sync field tambah_total (readonly) agar ikut tersimpan ke DB
            document.getElementById('tambah_total').value = grandTotal;
        }

        // ====== SIMPAN PERIODE (mode lokal: sebelum invoice disimpan) ======
        document.getElementById('saveTambahPeriode')?.addEventListener('click', function() {
            if (!window._lokalPeriodes) window._lokalPeriodes = [];

            const checkboxSection = document.getElementById('periodeCheckboxSection');
            const isCheckboxMode  = checkboxSection && !checkboxSection.classList.contains('hidden');

            if (isCheckboxMode) {
                // ── Mode Checkbox ──────────────────────────────────────────
                const selected = Array.from(document.querySelectorAll('.periode-item-cb:checked'));
                if (selected.length === 0) {
                    alert('Pilih minimal satu remak terlebih dahulu.');
                    return;
                }

                const details = window._tambahRentalDetails || [];

                // Kumpulkan semua entri yang dipilih
                const selectedEntries = selected.map(cb => details[parseInt(cb.dataset.idx)]).filter(Boolean);

                // Hitung periode_awal = min(tanggal_mulai), periode_akhir = max(tanggal_selesai)
                const awalDates  = selectedEntries.map(e => e.tanggal_mulai   ? new Date(e.tanggal_mulai)   : null).filter(Boolean);
                const akhirDates = selectedEntries.map(e => e.tanggal_selesai ? new Date(e.tanggal_selesai) : (e.tanggal_mulai ? new Date(e.tanggal_mulai) : null)).filter(Boolean);

                const minAwal  = awalDates.length  ? new Date(Math.min(...awalDates))  : null;
                const maxAkhir = akhirDates.length ? new Date(Math.max(...akhirDates)) : null;

                // Format ke YYYY-MM-DD untuk disimpan
                const toYMD = d => d ? d.toISOString().slice(0,10) : '';
                const awal  = toYMD(minAwal);
                const akhir = toYMD(maxAkhir);

                if (!awal) { alert('Tidak ada tanggal valid pada remak yang dipilih.'); return; }

                // Kumpulkan semua remak_items dari semua entri terpilih
                const remaks = [];
                selectedEntries.forEach(entry => {
                    (entry.remak_items || []).forEach(item => {
                        remaks.push({
                            text:  item.kendaraan || 'Item',
                            qty:   item.qty   || 1,
                            price: item.price || 0,
                        });
                    });
                    // Biaya driver (jika ada)
                    if ((parseFloat(entry.biaya_driver) || 0) > 0) {
                        remaks.push({
                            text:  entry.nama_driver ? 'Driver: ' + entry.nama_driver : 'Biaya Driver',
                            qty:   entry.durasi_nilai ?? 1,
                            price: parseFloat(entry.biaya_driver),
                        });
                    }
                });

                window._lokalPeriodes.push({ awal, akhir, remaks });

            } else {
                // ── Mode Manual ────────────────────────────────────────────
                const awal  = document.getElementById('tambahPeriodeAwal').value;
                if (!awal) { alert('Tanggal awal wajib diisi.'); return; }
                const akhir = document.getElementById('tambahPeriodeAkhir').value;
                window._lokalPeriodes.push({ awal, akhir: akhir || awal, remaks: [] });
            }

            closeModal(document.getElementById('modalTambahPeriode'));
            renderPeriodeListLokal();
        });

        // ====== SIMPAN REMAK (mode lokal) ======
        document.getElementById('saveTambahRemak')?.addEventListener('click', function() {
            const text  = document.getElementById('tambahRemakText').value.trim();
            const qty   = parseInt(document.getElementById('tambahRemakQty').value) || 1;
            const price = parseFloat(document.getElementById('tambahRemakPrice').value) || 0;
            if (!text) { alert('Keterangan wajib diisi.'); return; }

            const pi = window._aktivLokalPeriodeIdx;
            if (pi === undefined || pi === null || !window._lokalPeriodes?.[pi]) {
                alert('Pilih periode terlebih dahulu.'); return;
            }
            window._lokalPeriodes[pi].remaks.push({ text, qty, price });

            closeModal(document.getElementById('modalTambahRemak'));
            renderPeriodeListLokal();
        });

        // Close buttons untuk modal periode/remak tab 2
        ['closeTambahPeriode','closeTambahPeriode2'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.onclick = () => closeModal(document.getElementById('modalTambahPeriode'));
        });
        ['closeTambahRemak','closeTambahRemak2'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.onclick = () => closeModal(document.getElementById('modalTambahRemak'));
        });
        
        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        if (typeof openModalTambah === 'function') openModalTambah();
        @endif

        }); // end DOMContentLoaded — script block 2
        </script>

        {{-- ====== CHART INVOICE ====== --}}
        <script>
        (function () {
            const invoiceChartManager = new ChartManager();
            let invoiceChartsInitialized = false;

            function initInvoiceCharts(filters) {
                invoiceChartsInitialized = true;
                return invoiceChartManager.initChartsFromAPI('invoice', {
                    pie:  'invoicePieChart',
                    bar:  'invoiceBarChart',
                    line: 'invoiceLineChart',
                }, filters, { accentLine: true });
            }

            function updateInvoiceCharts(filters) {
                const isScrollable = filters.filter_type === 'custom';
                return invoiceChartManager.updateChartsFromAPI('invoice', {
                    pie:  'invoicePieChart',
                    bar:  'invoiceBarChart',
                    line: 'invoiceLineChart',
                }, filters, { scrollable: isScrollable, accentLine: true }, { scrollable: isScrollable });
            }

            // Filter change handler — menangani init pertama DAN update berikutnya
            document.addEventListener('chartFilterChange', function (e) {
                if (e.detail.filterId !== 'invoiceChartFilter') return;
                const filters = { filter_type: e.detail.filterType };
                if (e.detail.filterType === 'custom') {
                    filters.start_date = e.detail.startDate;
                    filters.end_date   = e.detail.endDate;
                }
                if (!invoiceChartsInitialized) {
                    initInvoiceCharts(filters);
                } else {
                    updateInvoiceCharts(filters);
                }
            });
        })();
        </script>
    @endpush

@endsection
