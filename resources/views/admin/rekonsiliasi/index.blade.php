@extends('admin.layouts.app')

@section('title', 'Rekonsiliasi Bank')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Rekonsiliasi Bank</h1>
                <p class="text-sm text-gray-500 mt-0.5">Data rekonsiliasi transaksi bank</p>
            </div>
            <button onclick="openModalTambah()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Data
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

            {{-- Total Rekonsiliasi --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Jumlah Rekonsiliasi</p>
                        <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ $data->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-bank text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Amount --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <h2 class="text-lg font-bold text-green-600 mt-2 leading-tight">
                            Rp {{ number_format($data->sum('amount'), 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-cash-stack text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Status Pending</p>
                        <h2 class="text-3xl font-bold text-yellow-600 mt-2">
                            {{ $data->where('status_rekonsiliasi', 'Pending')->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-50 flex items-center justify-center">
                        <i class="bi bi-clock-history text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>

            {{-- Matched --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Status Matched</p>
                        <h2 class="text-3xl font-bold text-green-600 mt-2">
                            {{ $data->where('status_rekonsiliasi', 'matched')->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-check-circle-fill text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Unmatched --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Status Unmatched</p>
                        <h2 class="text-3xl font-bold text-red-600 mt-2">
                            {{ $data->where('status_rekonsiliasi', 'unmatched')->count() }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">
                        <i class="bi bi-x-circle-fill text-2xl text-red-600"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Rekonsiliasi</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data rekonsiliasi</p>
                </div>
                <div class="flex items-center gap-2">
                    {{-- PDF --}}
                    <a id="pdfBtn" target="_blank" href="{{ route('rekonsiliasi.pdf') }}"
    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
    <i class="fa fa-file-pdf"></i> PDF
</a>

{{-- TAMBAHKAN INI --}}
<a id="excelBtn" href="{{ route('rekonsiliasi.export.excel') }}"
    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-green-600 text-green-600 rounded-lg bg-transparent hover:bg-green-600 hover:text-white transition-colors">
    <i class="fa fa-file-excel"></i> Excel
</a>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" placeholder="Cari referensi, deskripsi, status..."
                            oninput="filterTable(this.value)"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-56">
                    </div>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
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
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Tanggal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Deskripsi</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Reference</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Amount</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Bukti Pembayaran</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Currency</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Invoice</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                VA</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($data as $item)
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100"
                                data-search="{{ strtolower($item->tanggal . ' ' . $item->deskripsi . ' ' . $item->reference_no . ' ' . $item->currency . ' ' . $item->status_rekonsiliasi) }}">

                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $data->firstItem() + $loop->index }}</td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->tanggal }}</td>

                                <td class="px-4 py-3.5 text-sm text-gray-700 max-w-[180px] truncate">{{ $item->deskripsi }}
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->reference_no }}</span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-bold text-green-600">Rp
                                        {{ number_format($item->amount, 0, ',', '.') }}</span>
                                </td>

                                <td class="px-4 py-2">
                                    <span class="text-sm font-bold text-green-600"> 
                                        @if ($item->bukti_pembayaran)
                                            <a href="{{ asset($item->bukti_pembayaran) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
                                        @else
                                            Tidak ada
                                        @endif

                                    </span>

                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->currency }}</span>
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->invoice_id }}</td>

                                <td class="px-4 py-3.5">
                                    @if ($item->status_rekonsiliasi == 'matched')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Matched</span>
                                    @elseif ($item->status_rekonsiliasi == 'unmatched')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Unmatched</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->va ?? '-' }}</td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            onclick="openEditModal(
                                            '{{ $item->id }}',
                                            '{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}',
                                            '{{ addslashes($item->deskripsi) }}',
                                            '{{ $item->reference_no }}',
                                            '{{ $item->amount }}',
                                            '{{ $item->currency }}',
                                            '{{ $item->status_rekonsiliasi }}',
                                            '{{ $item->invoice_id }}'
                                        )"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="{{ route('rekonsiliasi.destroy', $item->id) }}" method="POST"
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
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="bi bi-bank text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data rekonsiliasi</p>
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
                    <h2 class="text-base font-bold text-gray-800">Tambah Data Rekonsiliasi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data rekonsiliasi bank dengan lengkap</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('rekonsiliasi.store') }}" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reference No <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="reference_no" required placeholder="Nomor referensi"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi <span
                            class="text-red-500">*</span></label>
                    <textarea name="deskripsi" rows="3" required placeholder="Deskripsi transaksi..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Amount <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="amount" required placeholder="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Currency <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="currency" value="IDR" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
                            class="text-red-500">*</span></label>
                    <select name="status_rekonsiliasi" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="Pending">Pending</option>
                        <option value="matched">Matched</option>
                        <option value="unmatched">Unmatched</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice ID <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="invoice_id" required placeholder="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">VA</label>
                    <input type="text" name="va" placeholder="Virtual Account"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bukti Pembayaran <span
                            class="text-red-500">*</span></label>
                    <input type="file" name="bukti_pembayaran" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div class="md:col-span-2 flex gap-3 pt-1">
                    <button type="button" onclick="closeModalTambah()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
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
                    <h2 class="text-base font-bold text-gray-800">Edit Data Rekonsiliasi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data rekonsiliasi bank</p>
                </div>
                <button onclick="closeModalEdit()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="edit_tanggal" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reference No <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="reference_no" id="edit_reference_no" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi <span
                            class="text-red-500">*</span></label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="3" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Amount <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="edit_amount" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Currency <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="currency" id="edit_currency" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
                            class="text-red-500">*</span></label>
                    <select name="status_rekonsiliasi" id="edit_status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="Pending">Pending</option>
                        <option value="matched">Matched</option>
                        <option value="unmatched">Unmatched</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice ID <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="invoice_id" id="edit_invoice_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div class="md:col-span-2 flex gap-3 pt-1">
                    <button type="button" onclick="closeModalEdit()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
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
        // -- MODAL TAMBAH -----------------------------------
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

        // -- MODAL EDIT -------------------------------------
        function openEditModal(id, tanggal, deskripsi, reference_no, amount, currency, status, invoice_id) {
            var m = document.getElementById('modalEdit');
            m.classList.remove('hidden');
            m.classList.add('flex');

            document.getElementById('formEdit').action = '/admin/rekonsiliasi/' + id;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_deskripsi').value = deskripsi;
            document.getElementById('edit_reference_no').value = reference_no;
            document.getElementById('edit_amount').value = amount;
            document.getElementById('edit_currency').value = currency;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_invoice_id').value = invoice_id;
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

       
        // -- SEARCH / FILTER --------------------------------
function filterTable(q) {
    var num = 0;
    document.querySelectorAll('#tableBody tr[data-search]').forEach(function(row) {
        var show = row.dataset.search.includes(q.toLowerCase());
        row.style.display = show ? '' : 'none';
        if (show) {
            num++;
            row.querySelector('td:first-child').textContent = num;
        }
    });

    // update link PDF & Excel sekaligus
    const query = q ? '?search=' + encodeURIComponent(q) : '';
    document.getElementById('pdfBtn').href   = "{{ route('rekonsiliasi.pdf') }}" + query;
    document.getElementById('excelBtn').href = "{{ route('rekonsiliasi.export.excel') }}" + query;
}

// initial — pastikan URL Excel sudah benar saat halaman load
document.getElementById('excelBtn').href = "{{ route('rekonsiliasi.export.excel') }}";

        // -- POPUP ALERT ------------------------------------
        (function() {
            var overlay = document.getElementById('alertOverlay');
            var box = document.getElementById('alertBox');
            if (!overlay) return;

            setTimeout(function() {
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
                box.style.transform = 'translateY(0)';
            }, 80);

            var timer = setTimeout(closeAlert, 45000);

            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeAlert();
            });

            function closeAlert() {
                clearTimeout(timer);
                overlay.style.opacity = '0';
                overlay.style.pointerEvents = 'none';
                box.style.transform = 'translateY(-16px)';
            }
            window.closeAlert = closeAlert;
        })();
    </script>

@endsection
