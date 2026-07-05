@extends('admin.layouts.app')

@section('title', 'Aging AR')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Aging AR</h1>
                <p class="text-sm text-slate-500 mt-1">Monitoring umur piutang (accounts receivable) customer</p>
            </div>
            <button type="button" onclick="openModal()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
                <i class="fa fa-plus"></i>
                Tambah Aging AR
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Total Invoice --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Invoice</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $data->count() }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Piutang Lancar (Current) --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Piutang Lancar</p>
                        <h3 class="text-3xl font-bold text-green-600 mt-2">
                            {{ $data->where('kategori', 'Current')->count() }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Piutang Overdue --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Piutang Overdue</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">
                            {{ $data->where('kategori', '!=', 'Current')->count() }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-xmark text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total Piutang --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Piutang</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-2">Rp
                            {{ number_format($data->sum('total')) }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-2xl"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- TABLE HEADER + SEARCH --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <div>
                    <h2 class="font-semibold text-slate-800 text-base">Daftar Aging AR</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $data->count() }} total invoice tercatat</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="searchInput" placeholder="Cari invoice, customer, kategori..."
                            oninput="filterTable(this.value)"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 w-56">
                    </div>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">No</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Invoice</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Customer</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Email</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Kontak</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Jatuh Tempo</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Umur</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Total</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Kategori</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100">

                        @forelse ($data as $d)
                            <tr class="hover:bg-slate-50 transition"
                                data-search="{{ strtolower(
                                    $d->invoice->invoice_no .
                                        ' ' .
                                        $d->member->nama_member .
                                        ' ' .
                                        ($d->member->email_member ?? '') .
                                        ' ' .
                                        ($d->member->kontak_member ?? '') .
                                        ' ' .
                                        $d->kategori .
                                        ' ' .
                                        $d->total .
                                        ' ' .
                                        $d->umur,
                                ) }}">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>

                                {{-- Invoice --}}
                                <td class="px-5 py-4 font-medium text-slate-800">{{ $d->invoice->invoice_no }}</td>

                                {{-- Customer --}}
                                <td class="px-5 py-4 text-slate-700">{{ $d->member->nama_member }}</td>

                                {{-- Email --}}
                                <td class="px-5 py-4 text-slate-600 text-xs">
                                    {{ $d->member->email_member ?? '-' }}
                                </td>

                                {{-- Kontak --}}
                                <td class="px-5 py-4 text-slate-600 text-xs">
                                    {{ $d->member->kontak_member ?? '-' }}
                                </td>

                                {{-- Jatuh Tempo --}}
                                <td class="px-5 py-4">
                                    @php
                                        $jatuhTempo = \Carbon\Carbon::parse($d->jatuh_tempo)->startOfDay();
                                        $hariIni = now()->startOfDay();

                                        $sisaHari = $hariIni->diffInDays($jatuhTempo, false);
                                    @endphp

                                    <div class="flex flex-col gap-1">
                                        <span class="text-slate-600 text-sm">
                                            {{ $jatuhTempo->format('d M Y') }}
                                        </span>

                                        @if ($d->status != 'Bayar')
                                            @if ($sisaHari < 0)
                                                <span
                                                    class="inline-flex items-center gap-1 text-[11px] font-medium text-red-600 bg-red-50 border border-red-200 px-2 py-1 rounded-full w-fit">
                                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                                    Terlambat {{ abs($sisaHari) }} hari
                                                </span>
                                            @elseif ($sisaHari <= $reminder)
                                                <span
                                                    class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1 rounded-full w-fit">
                                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>

                                                    @if ($sisaHari == 0)
                                                        Jatuh Tempo Hari Ini
                                                    @elseif ($sisaHari == 1)
                                                        Jatuh Tempo Besok
                                                    @else
                                                        Jatuh Tempo dalam {{ $sisaHari }} hari
                                                    @endif
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                {{-- Umur --}}
                                <td class="px-5 py-4 font-semibold text-slate-800">
                                    {{ $d->umur_otomatis ?? $d->umur }} Hari
                                </td>

                                {{-- Total --}}
                                <td class="px-5 py-4 font-semibold text-slate-800">Rp
                                    {{ number_format($d->total) }}
                                </td>

                                {{-- Kategori --}}
                                <td class="px-5 py-4">
                                    @if ($d->kategori == 'Current')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Current</span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ $d->kategori }}</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Edit --}}
                                        <button onclick='openEditModal(@json($d))'
                                            class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
                                        </button>

                                        {{-- Delete --}}
                                        <form action="{{ route('aging_ar.destroy', $d->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1">
                                                <i class="fa-solid fa-trash text-xs"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-file-invoice-dollar text-4xl mb-3 block"></i>
                                    Belum ada data aging AR
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>

    </div>

    {{-- ======================================
        MODAL TAMBAH AGING AR
    ====================================== --}}
    <div id="modal" class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Tambah Aging AR</h2>
                    <p class="text-sm text-slate-500">Isi data piutang customer baru</p>
                </div>
                <button onclick="closeModal()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="form" action="{{ route('aging_ar.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Customer --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Customer <span
                                class="text-red-500">*</span></label>
                        <select id="member_id" name="member_id" style="width:100%"></select>
                    </div>

                    {{-- Invoice --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Invoice <span
                                class="text-red-500">*</span></label>
                        <select id="invoice_id" name="invoice_id" style="width:100%"></select>
                    </div>

                    {{-- Email Member (auto) --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Email Member</label>
                        <input type="text" id="add_email_member" readonly placeholder="Terisi otomatis"
                            class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-sm text-slate-500 outline-none">
                    </div>

                    {{-- Kontak Member (auto) --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kontak Member</label>
                        <input type="text" id="add_kontak_member" readonly placeholder="Terisi otomatis"
                            class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-sm text-slate-500 outline-none">
                    </div>

                    {{-- Jatuh Tempo --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" id="add_jatuh_tempo" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    {{-- Total --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Total <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="total" id="add_total" required placeholder="0"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    {{-- Kategori --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kategori <span
                                class="text-red-500">*</span></label>
                        <select name="kategori" id="add_kategori" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="Current">Current</option>
                            <option value="1-30 Hari">1-30 Hari</option>
                            <option value="31-60 Hari">31-60 Hari</option>
                            <option value="61-90 Hari">61-90 Hari</option>
                            <option value=">90 Hari">&gt;90 Hari</option>
                        </select>
                    </div>

                </div>

                <button type="submit"
                    class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data
                </button>

            </form>

        </div>
    </div>

    {{-- ======================================
        MODAL EDIT AGING AR
    ====================================== --}}
    <div id="editModal" class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Edit Aging AR</h2>
                    <p class="text-sm text-slate-500">Perbarui data piutang customer</p>
                </div>
                <button onclick="closeEditModal()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Customer --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Customer <span
                                class="text-red-500">*</span></label>
                        <select id="edit_member_id" name="member_id" style="width:100%"></select>
                    </div>

                    {{-- Invoice --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Invoice <span
                                class="text-red-500">*</span></label>
                        <select id="edit_invoice_id" name="invoice_id" style="width:100%"></select>
                    </div>

                    {{-- Email Member (auto) --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Email Member</label>
                        <input type="text" id="edit_email_member" readonly placeholder="Terisi otomatis"
                            class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-sm text-slate-500 outline-none">
                    </div>

                    {{-- Kontak Member (auto) --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kontak Member</label>
                        <input type="text" id="edit_kontak_member" readonly placeholder="Terisi otomatis"
                            class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-sm text-slate-500 outline-none">
                    </div>

                    {{-- Jatuh Tempo --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" id="jatuh_tempo" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    {{-- Total --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Total <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="total" id="total" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                </div>

                <button type="submit"
                    class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Update Data
                </button>

            </form>

        </div>
    </div>

    {{-- ======================================
        POPUP ALERT
    ====================================== --}}
    @if (session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[99999999] flex items-start justify-center pt-6"
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


    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {

            // ===== CUSTOMER (MODAL TAMBAH) =====
            $('#member_id').select2({
                dropdownParent: $('#modal'),
                placeholder: 'Cari customer...',
                ajax: {
                    url: '/admin/ajax/members',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.name,
                                email: item.email,
                                kontak: item.kontak
                            }))
                        };
                    },
                    cache: true
                }
            });

            // Auto-isi email & kontak saat member dipilih (modal tambah)
            $('#member_id').on('select2:select', function(e) {
                const data = e.params.data;
                $('#add_email_member').val(data.email ?? '');
                $('#add_kontak_member').val(data.kontak ?? '');
            });

            $('#member_id').on('select2:clear', function() {
                $('#add_email_member').val('');
                $('#add_kontak_member').val('');
            });

            // ===== INVOICE (MODAL TAMBAH) =====
            $('#invoice_id').select2({
                dropdownParent: $('#modal'),
                placeholder: 'Cari invoice...',
                ajax: {
                    url: '/admin/ajax/invoices',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.no_invoice
                            }))
                        };
                    },
                    cache: true
                }
            });

            // ===== CUSTOMER (MODAL EDIT) =====
            $('#edit_member_id').select2({
                dropdownParent: $('#editModal'),
                placeholder: 'Cari customer...',
                ajax: {
                    url: '/admin/ajax/members',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.name,
                                email: item.email,
                                kontak: item.kontak
                            }))
                        };
                    },
                    cache: true
                }
            });

            // Auto-isi email & kontak saat member dipilih (modal edit)
            $('#edit_member_id').on('select2:select', function(e) {
                const data = e.params.data;
                $('#edit_email_member').val(data.email ?? '');
                $('#edit_kontak_member').val(data.kontak ?? '');
            });

            $('#edit_member_id').on('select2:clear', function() {
                $('#edit_email_member').val('');
                $('#edit_kontak_member').val('');
            });

            // ===== INVOICE (MODAL EDIT) =====
            $('#edit_invoice_id').select2({
                dropdownParent: $('#editModal'),
                placeholder: 'Cari invoice...',
                ajax: {
                    url: '/admin/ajax/invoices',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.no_invoice
                            }))
                        };
                    },
                    cache: true
                }
            });

        });

        document.addEventListener('DOMContentLoaded', () => {

            const addModal = document.getElementById('modal');
            const addForm = document.getElementById('form');
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');

            // ===== MODAL TAMBAH =====
            window.openModal = function() {
                const modal = document.getElementById('modal');
                const form = document.getElementById('form');

                form.reset();

                // reset select2 WAJIB
                $('#member_id').val(null).trigger('change');
                $('#invoice_id').val(null).trigger('change');

                // reset field auto
                document.getElementById('add_email_member').value = '';
                document.getElementById('add_kontak_member').value = '';

                modal.classList.remove('hidden');
            };

            window.closeModal = function() {
                const modal = document.getElementById('modal');
                modal.classList.add('hidden');
            };

            addModal?.addEventListener('click', (e) => {
                if (e.target === addModal) addModal.classList.add('hidden');
            });

            // ===== MODAL EDIT =====
            window.openEditModal = function(data) {
                $('#edit_invoice_id').append(new Option(data.invoice.invoice_no, data.invoice.id, true, true))
                    .trigger('change');

                $('#edit_member_id').append(new Option(data.member.nama_member, data.member.id, true, true))
                    .trigger('change');

                // isi field auto dari data member yang sudah ada
                document.getElementById('edit_email_member').value = data.member.email_member ?? '';
                document.getElementById('edit_kontak_member').value = data.member.kontak_member ?? '';

                document.getElementById('jatuh_tempo').value = data.jatuh_tempo.substring(0, 10);
                document.getElementById('total').value = data.total;
                editForm.action = '/admin/aging_ar/' + data.id;
                editModal.classList.remove('hidden');
            };

            window.closeEditModal = function() {
                editModal.classList.add('hidden');
            };

            editModal?.addEventListener('click', (e) => {
                if (e.target === editModal) editModal.classList.add('hidden');
            });

            // ===== SEARCH FILTER =====
            window.filterTable = function(q) {
                q = q.toLowerCase().trim();

                const rows = document.querySelectorAll('#tableBody tr[data-search]');

                rows.forEach(row => {
                    const text = row.dataset.search || '';
                    row.style.display = text.includes(q) ? '' : 'none';
                });
            };

            // ===== ALERT POPUP =====
            window.closeAlert = function() {
                const overlay = document.getElementById('alertOverlay');
                if (overlay) overlay.style.display = 'none';
            };

            const alertOverlay = document.getElementById('alertOverlay');
            if (alertOverlay) {
                alertOverlay.style.pointerEvents = 'auto';
                alertOverlay.style.opacity = '1';
                document.getElementById('alertBox').style.transform = 'translateY(0)';
                setTimeout(() => closeAlert(), 4000);
            }

        });

        window.closeModal = function() {
            document.getElementById('modal').classList.add('hidden');
        };
    </script>

    {{-- STYLE --}}
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


@endsection
