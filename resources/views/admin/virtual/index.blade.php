@extends('admin.layouts.app')

@section('title', 'Virtual Account')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Virtual Account</h1>
                <p class="text-sm text-gray-500 mt-0.5">Data pembayaran virtual account</p>
            </div>
            <button onclick="openModalTambah()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Data
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

            {{-- Total VA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Virtual Account</p>
                        <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ $data->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-credit-card-2-front-fill text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Expected --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Expected</p>
                        <h2 class="text-lg font-bold text-indigo-600 mt-2 leading-tight">
                            Rp {{ number_format($data->sum('expected_amount'), 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center">
                        <i class="bi bi-wallet2 text-2xl text-indigo-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Paid --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Paid</p>
                        <h2 class="text-lg font-bold text-green-600 mt-2 leading-tight">
                            Rp {{ number_format($data->sum('paid_amount'), 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-cash-stack text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Status Pending --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Status Pending</p>
                        <h2 class="text-3xl font-bold text-yellow-600 mt-2">{{ $data->where('status', 'Pending')->count() }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-50 flex items-center justify-center">
                        <i class="bi bi-clock-history text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>

            {{-- Status Paid --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Status Paid</p>
                        <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $data->where('status', 'paid')->count() }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-check-circle-fill text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Virtual Account</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('virtual.pdf') }}" id="pdfBtn" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition">
                        <i class="fa fa-file-pdf text-xs"></i> PDF
                    </a>

                    {{-- TAMBAHKAN INI --}}
                    <a href="{{ route('virtual.export.excel') }}" id="excelBtn"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                        <i class="fa fa-file-excel text-xs"></i> Excel
                    </a>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="searchInput" placeholder="Cari VA, member, bank, status..."
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg w-56">
                    </div>
                    <button onclick="window.location.href = window.location.pathname"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">VA
                                Number</th>
                                   <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Customer</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Invoice</th>
                                  <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Bukti</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Bank
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Expected</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Paid
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Expired</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($data as $item)
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                data-search="{{ strtolower($item->va_number . ' ' . ($item->member->nama_member ?? '') . ' ' . $item->bank . ' ' . $item->status) }}">

                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $data->firstItem() + $loop->index }}</td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->va_number }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($item->member->nama_member ?? 'U', 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $item->member->nama_member ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($item->invoice)
                                        <div class="text-xs">
                                            <span class="font-medium text-gray-700">{{ $item->invoice->invoice_no }}</span>
                                            <span class="text-gray-400 block">{{ $item->invoice->customer_name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                                    <td>
                                    @if ($item->bukti_pembayaran)
                                        @php
                                            $filename = basename($item->bukti_pembayaran);
                                        @endphp

                                        <a href="{{ asset($item->bukti_pembayaran) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800">

                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                              

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->bank }}</span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-medium text-indigo-600">Rp
                                        {{ number_format($item->expected_amount, 0, ',', '.') }}</span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-bold text-green-600">Rp
                                        {{ number_format($item->paid_amount, 0, ',', '.') }}</span>
                                </td>

                                <td class="px-4 py-3.5">
                                    @if ($item->status == 'paid')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Paid</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5">
                                    @if ($item->expired_at)
                                        @php $isExpired = $item->expired_at->isPast(); @endphp
                                        <span class="text-xs font-medium {{ $isExpired ? 'text-red-500' : 'text-gray-600' }}">
                                            {{ $item->expired_at->format('d/m/Y H:i') }}
                                            @if ($isExpired)
                                                <span class="ml-1 px-1.5 py-0.5 rounded bg-red-100 text-red-600 text-[10px]">Expired</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

            

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            onclick="openEditModal(
                                            '{{ $item->id }}',
                                            '{{ $item->va_number }}',
                                            '{{ $item->member_id }}',
                                            '{{ $item->invoice_id }}',
                                            '{{ $item->bank }}',
                                            '{{ $item->expected_amount }}',
                                            '{{ $item->paid_amount }}',
                                            '{{ $item->status }}',
                                            '{{ $item->expired_at ? $item->expired_at->format('Y-m-d\TH:i') : '' }}'
                                        )"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="{{ route('virtual.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
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
                                <td colspan="11" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="bi bi-credit-card-2-front-fill text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data virtual account</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Data" untuk menambahkan data baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
            </div>

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH
====================================== --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Virtual Account</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data virtual account dengan lengkap</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('virtual.store') }}" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Member <span
                            class="text-red-500">*</span></label>
                    <select name="member_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}">{{ $member->nama_member }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bank <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="bank" required placeholder="Nama bank"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expected Amount <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="expected_amount" required placeholder="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Paid Amount <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="paid_amount" value="0" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
                            class="text-red-500">*</span></label>
                    <select name="status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="Pending">Pending</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice ID <span
                            class="text-red-500">*</span></label>
                    <select name="invoice_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Invoice --</option>
                        @foreach ($invoices as $invoice)
                            <option value="{{ $invoice->id }}">{{ $invoice->invoice_no }} — {{ $invoice->customer_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expired At</label>
                    <input type="datetime-local" name="expired_at"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- UPLOAD GPS STYLE --}}
                <div class="md:col-span-2">

                    <label class="block text-xs font-semibold text-gray-600 mb-2">
                        Bukti Pembayaran
                    </label>

                    {{-- PREVIEW (DI ATAS) --}}
                    <div id="previewVA" class="hidden mb-3"></div>

                    {{-- UPLOAD BOX --}}
                    <label for="bukti_pembayaran"
                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>

                        <span class="text-sm text-gray-600 font-medium">
                            Klik atau drag file ke sini
                        </span>

                        <span class="text-[11px] text-gray-400 mt-1">
                           (max 2MB)
                        </span>
                    </label>

                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="hidden"
                        onchange="previewFileVA(event)" required>
                </div>

                <div class="md:col-span-2 flex gap-3 pt-1">
                    <button type="button" onclick="closeModalTambah()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                        <i class="fa fa-save text-sm"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- ======================================
    MODAL EDIT
====================================== --}}
    <div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Virtual Account</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data virtual account</p>
                </div>
                <button onclick="closeModalEdit()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">VA Number <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="va_number" id="edit_va_number" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Member <span
                            class="text-red-500">*</span></label>
                    <select name="member_id" id="edit_member_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}">{{ $member->nama_member }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice ID <span
                            class="text-red-500">*</span></label>
                    <select name="invoice_id" id="edit_invoice_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Invoice --</option>
                        @foreach ($invoices as $invoice)
                            <option value="{{ $invoice->id }}">{{ $invoice->invoice_no }} — {{ $invoice->customer_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bank <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="bank" id="edit_bank" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expected Amount <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="expected_amount" id="edit_expected_amount" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Paid Amount <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="paid_amount" id="edit_paid_amount" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
                            class="text-red-500">*</span></label>
                    <select name="status" id="edit_status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="Pending">Pending</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expired At</label>
                    <input type="datetime-local" name="expired_at" id="edit_expired_at"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div class="md:col-span-2">

                    <label class="block text-xs font-semibold text-gray-600 mb-2">
                        Bukti Pembayaran
                    </label>

                    {{-- PREVIEW (DI ATAS) --}}
                    <div id="previewVAEdit" class="hidden mb-3"></div>

                    {{-- UPLOAD BOX --}}
                    <label for="edit_bukti_pembayaran"
                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>

                        <span class="text-sm text-gray-600 font-medium">
                            Klik atau drag file ke sini
                        </span>

                        <span class="text-[11px] text-gray-400 mt-1">
                           (max 2MB)
                        </span>
                    </label>

                    <input type="file" name="bukti_pembayaran" id="edit_bukti_pembayaran" class="hidden"
                        onchange="previewFileVAEdit(event)">

                    
                </div>

                <div class="md:col-span-2 flex gap-3 pt-1">
                    <button type="button" onclick="closeModalEdit()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                        <i class="fa fa-save text-sm"></i> Update
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- ======================================
    POPUP ALERT (FIXED OVERLAY)
====================================== --}}
    @if (session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

            <div id="alertBox"
                class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">

                @if (session('success'))
                    <div
                        class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @elseif (session('error'))
                    <div
                        class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                    </div>
                @else
                    <div
                        class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button onclick="closeAlert()"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0"
                    aria-label="Tutup">
                    <i class="fa fa-times"></i>
                </button>

            </div>
        </div>
    @endif


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

    <script>
        // ── MODAL TAMBAH ───────────────────────────────────
        function openModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        document.getElementById('modalTambah').addEventListener('click', function(e) {
            if (e.target === this) closeModalTambah();
        });

        // ── MODAL EDIT ─────────────────────────────────────
        function openEditModal(id, va_number, member_id, invoice_id, bank, expected_amount, paid_amount, status, expired_at) {
            var m = document.getElementById('modalEdit');
            m.classList.remove('hidden');
            m.classList.add('flex');

            document.getElementById('formEdit').action = "{{ route('virtual.update', '') }}/" + id;
            document.getElementById('edit_va_number').value = va_number;
            document.getElementById('edit_member_id').value = member_id;
            document.getElementById('edit_invoice_id').value = invoice_id;
            document.getElementById('edit_bank').value = bank;
            document.getElementById('edit_expected_amount').value = expected_amount;
            document.getElementById('edit_paid_amount').value = paid_amount;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_expired_at').value = expired_at || '';

            // reset input & preview file setiap kali modal dibuka
            document.getElementById('edit_bukti_pembayaran').value = '';
            document.getElementById('previewVAEdit').innerHTML = '';
            document.getElementById('previewVAEdit').classList.add('hidden');
        }

        function closeModalEdit() {
            var m = document.getElementById('modalEdit');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        document.getElementById('modalEdit').addEventListener('click', function(e) {
            if (e.target === this) closeModalEdit();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalTambah();
                closeModalEdit();
            }
        });

        // ── UPDATE EXPORT LINKS ────────────────────────────
        function updateExportLinks(q) {
            const query = q ? '?search=' + encodeURIComponent(q) : '';
            document.getElementById('pdfBtn').href = "{{ route('virtual.pdf') }}" + query;
            document.getElementById('excelBtn').href = "{{ route('virtual.export.excel') }}" + query;
        }

        // ── SEARCH / FILTER ────────────────────────────────
        document.getElementById('searchInput').addEventListener('input', function() {
            const q = this.value.trim();

            let num = 0;
            document.querySelectorAll('#tableBody tr[data-search]').forEach(function(row) {
                const match = row.dataset.search.includes(q.toLowerCase());
                row.style.display = match ? '' : 'none';
                if (match) {
                    num++;
                    row.querySelector('td:first-child').textContent = num;
                }
            });

            // update URL browser (opsional, pertahankan behavior lama)
            const url = new URL(window.location.href);
            if (q) {
                url.searchParams.set('search', q);
            } else {
                url.searchParams.delete('search');
            }
            window.history.replaceState({}, '', url);

            // update link PDF & Excel sekaligus
            updateExportLinks(q);
        });

        // initial — set link saat halaman pertama load
        updateExportLinks('');

        // ── POPUP ALERT ────────────────────────────────────



        function previewFileVA(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('previewVA');

            if (!file) return;

            preview.classList.remove('hidden');
            preview.innerHTML = '';

            const ext = file.name.split('.').pop().toLowerCase();

            // IMAGE PREVIEW
            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.innerHTML = `
                <div class="relative inline-block">
                    <img src="${e.target.result}"
                        class="w-32 h-32 object-cover rounded-lg border shadow-sm">

                    <button type="button"
                        onclick="removeFileVA()"
                        class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full text-xs">
                        ×
                    </button>
                </div>
            `;
                };

                reader.readAsDataURL(file);
            }

            // PDF / DOC PREVIEW
            else {
                let icon = 'fa-file';

                if (ext === 'pdf') icon = 'fa-file-pdf text-red-500';
                if (ext === 'doc' || ext === 'docx') icon = 'fa-file-word text-blue-500';

                preview.innerHTML = `
            <div class="relative inline-flex items-center gap-2 px-3 py-2 border rounded-lg bg-gray-50">
                <i class="fa-solid ${icon} text-lg"></i>
                <span class="text-xs text-gray-600">${file.name}</span>

                <button type="button"
                    onclick="removeFileVA()"
                    class="ml-2 text-red-500 text-sm">
                    ×
                </button>
            </div>
        `;
            }
        }

        function removeFileVA() {
            document.getElementById('bukti_pembayaran').value = '';
            document.getElementById('previewVA').innerHTML = '';
            document.getElementById('previewVA').classList.add('hidden');
        }

        function previewFileVAEdit(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('previewVAEdit');

            if (!file) return;

            preview.classList.remove('hidden');
            preview.innerHTML = '';

            const ext = file.name.split('.').pop().toLowerCase();

            // IMAGE PREVIEW
            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.innerHTML = `
                <div class="relative inline-block">
                    <img src="${e.target.result}"
                        class="w-32 h-32 object-cover rounded-lg border shadow-sm">

                    <button type="button"
                        onclick="removeFileVAEdit()"
                        class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full text-xs">
                        ×
                    </button>
                </div>
            `;
                };

                reader.readAsDataURL(file);
            }

            // PDF / DOC PREVIEW
            else {
                let icon = 'fa-file';

                if (ext === 'pdf') icon = 'fa-file-pdf text-red-500';
                if (ext === 'doc' || ext === 'docx') icon = 'fa-file-word text-blue-500';

                preview.innerHTML = `
            <div class="relative inline-flex items-center gap-2 px-3 py-2 border rounded-lg bg-gray-50">
                <i class="fa-solid ${icon} text-lg"></i>
                <span class="text-xs text-gray-600">${file.name}</span>

                <button type="button"
                    onclick="removeFileVAEdit()"
                    class="ml-2 text-red-500 text-sm">
                    ×
                </button>
            </div>
        `;
            }
        }

        function removeFileVAEdit() {
            document.getElementById('edit_bukti_pembayaran').value = '';
            document.getElementById('previewVAEdit').innerHTML = '';
            document.getElementById('previewVAEdit').classList.add('hidden');
        }
    </script>

@endsection