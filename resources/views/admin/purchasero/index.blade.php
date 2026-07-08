@extends('admin.layouts.app')

@section('title', 'Purchase Request')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Purchase Request</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola pengajuan permintaan pembelian barang & jasa</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
            <i class="fa fa-plus text-sm"></i>
            Tambah PR
        </button>
    </div>

    {{-- STAT CARDS + CHART (4 kolom sejajar, ukuran sama) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-file-invoice"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPR }}</p>
                <p class="text-xs text-gray-500 mt-1">Total PR</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDisetujui }}</p>
                <p class="text-xs text-gray-500 mt-1">Disetujui</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0">
                <i class="fa fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPending }}</p>
                <p class="text-xs text-gray-500 mt-1">Pending</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 flex-shrink-0 relative">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Distribusi Status</p>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Disetujui
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-yellow-500 inline-block"></span> Pending
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> Ditolak
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Purchase Request</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total PR</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari PR..."
                        oninput="onSearchInput(this.value)"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
                <button onclick="window.location.reload()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa fa-sync text-xs"></i> Refresh
                </button>
            </div>
        </div>

        {{-- FILTER BAR: Show entries + Bulan & Tahun --}}
        <div class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            {{-- Show entries --}}
            <div class="flex items-center gap-2">
                <span>Show</span>
                <select id="perPageSelect" onchange="renderTable()"
                    class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="all">All</option>
                </select>
                <span>entries</span>
            </div>

            <div class="w-px h-4 bg-gray-200"></div>

            {{-- Label --}}
            <span class="text-gray-400 font-medium">Tanggal PR:</span>

            {{-- Filter Hari --}}
            <div class="flex items-center gap-2">
                <i class="fa fa-calendar-day text-gray-400"></i>
                <select id="filterHari" onchange="renderTable()"
                    class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">Semua Hari</option>
                    @for ($d = 1; $d <= 31; $d++)
                        <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>
            </div>

            {{-- Filter Bulan --}}
            <div class="flex items-center gap-2">
                <i class="fa fa-calendar text-gray-400"></i>
                <select id="filterBulan" onchange="renderTable()"
                    class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">Semua Bulan</option>
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>

            {{-- Filter Tahun --}}
            <div class="flex items-center gap-2">
                <select id="filterTahun" onchange="renderTable()"
                    class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">Semua Tahun</option>
                    @php
                        $years = $data->map(fn($d) => $d->tanggal ? \Carbon\Carbon::parse($d->tanggal)->year : null)
                                     ->filter()->unique()->sortDesc();
                    @endphp
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reset --}}
            <button onclick="resetFilter()"
                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fa fa-rotate-left text-[10px]"></i> Reset
            </button>

            {{-- Entries info inline --}}
            <div class="ml-auto text-xs text-gray-400" id="entriesInfoTop"></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">ID</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No PR</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Departemen</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Pemohon</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Barang/Jasa</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kode Barang</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Qty</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Satuan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Alasan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Disetujui Oleh</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl Persetujuan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Catatan</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody id="purchaseroTableBody">
                    @forelse($data as $d)
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                            data-search="{{ strtolower($d->no_pr . ' ' . $d->departemen . ' ' . $d->pemohon . ' ' . $d->barang_jasa) }}"
                            data-tanggal="{{ $d->tanggal ? \Carbon\Carbon::parse($d->tanggal)->format('Y-m-d') : '' }}">

                            <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>

                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->no_pr }}</span>
                            </td>

                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal ? \Carbon\Carbon::parse($d->tanggal)->format('Y-m-d') : '-' }}</td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-building text-blue-400 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">{{ $d->departemen ?? '-' }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->pemohon ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-700 max-w-[140px] truncate">{{ $d->barang_jasa ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->kode_barang ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->qty ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->satuan ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[160px] truncate">{{ $d->alasan_permintaan ?? '-' }}</td>

                            <td class="px-4 py-3.5">
                                @if($d->status === 'Disetujui')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                                        <i class="fa fa-circle text-[6px]"></i> Disetujui
                                    </span>
                                @elseif($d->status === 'Ditolak')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                                        <i class="fa fa-circle text-[6px]"></i> Ditolak
                                    </span>
                                @elseif($d->status === 'Pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600">
                                        <i class="fa fa-circle text-[6px]"></i> Pending
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->disetujui_oleh ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal_persetujuan ? \Carbon\Carbon::parse($d->tanggal_persetujuan)->format('Y-m-d') : '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[160px] truncate">{{ $d->catatan ?? '-' }}</td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $d->id }}"
                                        data-action="{{ route('purchasero.update', $d->id) }}"
                                        data-no_pr="{{ $d->no_pr }}"
                                        data-tanggal="{{ $d->tanggal }}"
                                        data-departemen="{{ $d->departemen }}"
                                        data-pemohon="{{ $d->pemohon }}"
                                        data-barang_jasa="{{ $d->barang_jasa }}"
                                        data-kode_barang="{{ $d->kode_barang }}"
                                        data-qty="{{ $d->qty }}"
                                        data-satuan="{{ $d->satuan }}"
                                        data-alasan_permintaan="{{ $d->alasan_permintaan }}"
                                        data-status="{{ $d->status }}"
                                        data-disetujui_oleh="{{ $d->disetujui_oleh }}"
                                        data-tanggal_persetujuan="{{ $d->tanggal_persetujuan }}"
                                        data-catatan="{{ $d->catatan }}"
                                        onclick="triggerEdit(this)">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                        data-action="{{ route('purchasero.destroy', $d->id) }}"
                                        data-name="{{ $d->no_pr }}"
                                        onclick="triggerDelete(this)">
                                        <i class="fa fa-trash text-xs"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-file-invoice text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data Purchase Request</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah PR" untuk menambahkan PR baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ENTRIES INFO --}}
        <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfo"></div>

    </div>

</div>


{{-- ======================================
    MODAL TAMBAH / EDIT PURCHASE REQUEST
======================================--}}
<div id="purchaseroModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto"
         style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Purchase Request</h2>
                <p id="modalSubtitle" class="text-xs text-gray-500 mt-0.5">No PR akan dibuat otomatis</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="purchaseroForm" action="{{ route('purchasero.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>

            {{-- No PR: hanya tampil saat edit, non-editable --}}
            <div id="noPrBox" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No PR</label>
                <div class="flex items-center gap-2">
                    <span id="f_no_pr_display" class="font-mono text-xs text-gray-600 bg-gray-100 px-3 py-2 rounded-lg border border-gray-200"></span>
                    <span class="text-xs text-gray-400">(otomatis, tidak bisa diubah)</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="f_tanggal" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Departemen <span class="text-red-500">*</span></label>
                    <input type="text" name="departemen" id="f_departemen" required
                        placeholder="Contoh: Produksi"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pemohon <span class="text-red-500">*</span></label>
                    <input type="text" name="pemohon" id="f_pemohon" required
                        placeholder="Contoh: Rina"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Barang/Jasa <span class="text-red-500">*</span></label>
                    <input type="text" name="barang_jasa" id="f_barang_jasa" required
                        placeholder="Contoh: Label Baju"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Barang <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_barang" id="f_kode_barang" required
                        placeholder="Contoh: BRG-001"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Qty <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="qty" id="f_qty" required
                        placeholder="Contoh: 500"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Satuan <span class="text-red-500">*</span></label>
                    <input type="text" name="satuan" id="f_satuan" required
                        placeholder="Contoh: pcs"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alasan Permintaan <span class="text-red-500">*</span></label>
                <input type="text" name="alasan_permintaan" id="f_alasan_permintaan" required
                    placeholder="Contoh: Stok label habis"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                <select name="status" id="f_status" required onchange="toggleApprovalFields()"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">- Pilih Status -</option>
                    <option value="Pending">Pending</option>
                    <option value="Disetujui">Disetujui</option>
                    <option value="Ditolak">Ditolak</option>
                </select>
            </div>

            {{-- Blok Persetujuan: hanya aktif & wajib diisi jika status = Disetujui --}}
            <div id="approvalBox" class="hidden space-y-4 border border-green-100 bg-green-50/50 rounded-xl p-4">
                <p class="text-xs font-semibold text-green-700 flex items-center gap-1.5">
                    <i class="fa fa-circle-check"></i> Detail Persetujuan
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Disetujui Oleh <span class="text-red-500 approval-required">*</span></label>
                        <input type="text" name="disetujui_oleh" id="f_disetujui_oleh"
                            placeholder="Contoh: Manajer Produksi"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Persetujuan <span class="text-red-500 approval-required">*</span></label>
                        <input type="date" name="tanggal_persetujuan" id="f_tanggal_persetujuan"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                    <textarea name="catatan" id="f_catatan" rows="3"
                        placeholder="Catatan tambahan (opsional)..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>
            </div>

            <p id="approvalHint" class="text-xs text-gray-400 italic">
                Pilih status "Disetujui" untuk mengisi detail persetujuan (disetujui oleh, tanggal, dan catatan).
            </p>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                <i class="fa fa-save text-sm"></i> Simpan Data
            </button>
        </form>

    </div>
</div>


{{-- ======================================
    MODAL KONFIRMASI HAPUS
======================================--}}
<div id="deleteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4"
         style="animation:slideUp .2s ease">

        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl">
                <i class="fa fa-triangle-exclamation"></i>
            </div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Purchase Request?</h2>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                Kamu akan menghapus PR <strong id="deleteName" class="text-gray-700"></strong>.
                Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="submit"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-trash text-xs"></i> Hapus
            </button>
        </form>

    </div>
</div>


{{-- ======================================
    POPUP ALERT (FIXED OVERLAY)
======================================--}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay"
     class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
     style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

    <div id="alertBox"
         class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
         style="transform:translateY(-16px);transition:transform 0.25s">

        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
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


{{-- STYLE & SCRIPT --}}
<style>
@keyframes slideUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── PURCHASERO MODAL (Tambah/Edit) ──────────────────
const purchaseroModal = document.getElementById('purchaseroModal');
const purchaseroForm  = document.getElementById('purchaseroForm');
const methodContainer = document.getElementById('methodContainer');
const noPrBox          = document.getElementById('noPrBox');
const createUrl        = "{{ route('purchasero.store') }}";

const approvalBox   = document.getElementById('approvalBox');
const approvalHint  = document.getElementById('approvalHint');
const fDisetujuiOleh      = document.getElementById('f_disetujui_oleh');
const fTanggalPersetujuan = document.getElementById('f_tanggal_persetujuan');
const fCatatan            = document.getElementById('f_catatan');

// Tampilkan/blok blok persetujuan sesuai status yang dipilih
function toggleApprovalFields() {
    const status = document.getElementById('f_status').value;

    if (status === 'Disetujui') {
        approvalBox.classList.remove('hidden');
        approvalHint.classList.add('hidden');
        fDisetujuiOleh.setAttribute('required', 'required');
        fTanggalPersetujuan.setAttribute('required', 'required');
        fDisetujuiOleh.disabled = false;
        fTanggalPersetujuan.disabled = false;
        fCatatan.disabled = false;
    } else {
        approvalBox.classList.add('hidden');
        approvalHint.classList.remove('hidden');
        fDisetujuiOleh.removeAttribute('required');
        fTanggalPersetujuan.removeAttribute('required');
        fDisetujuiOleh.value = '';
        fTanggalPersetujuan.value = '';
        fCatatan.value = '';
        // Disabled agar tidak ikut ter-submit selagi status bukan Disetujui
        fDisetujuiOleh.disabled = true;
        fTanggalPersetujuan.disabled = true;
        fCatatan.disabled = true;
    }
}

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Purchase Request';
    document.getElementById('modalSubtitle').innerText = 'No PR akan dibuat otomatis';
    purchaseroForm.action = createUrl;
    methodContainer.innerHTML = '';
    noPrBox.classList.add('hidden');
    purchaseroForm.reset();
    toggleApprovalFields();
    purchaseroModal.classList.remove('hidden');
    purchaseroModal.classList.add('flex');
}

function closeModal() {
    purchaseroModal.classList.add('hidden');
    purchaseroModal.classList.remove('flex');
}

purchaseroModal.addEventListener('click', function (e) {
    if (e.target === purchaseroModal) closeModal();
});

function triggerEdit(btn) {
    document.getElementById('modalTitle').innerText = 'Edit Purchase Request';
    document.getElementById('modalSubtitle').innerText = 'Perbarui detail Purchase Request';
    purchaseroForm.action = btn.dataset.action;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

    document.getElementById('f_no_pr_display').innerText = btn.dataset.no_pr;
    noPrBox.classList.remove('hidden');

    document.getElementById('f_tanggal').value            = btn.dataset.tanggal ?? '';
    document.getElementById('f_departemen').value         = btn.dataset.departemen ?? '';
    document.getElementById('f_pemohon').value             = btn.dataset.pemohon ?? '';
    document.getElementById('f_barang_jasa').value         = btn.dataset.barang_jasa ?? '';
    document.getElementById('f_kode_barang').value         = btn.dataset.kode_barang ?? '';
    document.getElementById('f_qty').value                 = btn.dataset.qty ?? '';
    document.getElementById('f_satuan').value              = btn.dataset.satuan ?? '';
    document.getElementById('f_alasan_permintaan').value   = btn.dataset.alasan_permintaan ?? '';
    document.getElementById('f_status').value              = btn.dataset.status ?? '';

    toggleApprovalFields();

    if (btn.dataset.status === 'Disetujui') {
        fDisetujuiOleh.value      = btn.dataset.disetujui_oleh ?? '';
        fTanggalPersetujuan.value = btn.dataset.tanggal_persetujuan ?? '';
        fCatatan.value            = btn.dataset.catatan ?? '';
    }

    purchaseroModal.classList.remove('hidden');
    purchaseroModal.classList.add('flex');
}

// ── DELETE MODAL ─────────────────────────────────────
const deleteModal = document.getElementById('deleteModal');
const deleteForm  = document.getElementById('deleteForm');
const deleteName  = document.getElementById('deleteName');

function triggerDelete(btn) {
    deleteForm.action = btn.dataset.action;
    deleteName.innerText = btn.dataset.name || 'ini';
    deleteModal.classList.remove('hidden');
    deleteModal.classList.add('flex');
}

function closeDeleteModal() {
    deleteModal.classList.add('hidden');
    deleteModal.classList.remove('flex');
}

deleteModal.addEventListener('click', function (e) {
    if (e.target === deleteModal) closeDeleteModal();
});

// ── SEARCH + SHOW ENTRIES + FILTER BULAN/TAHUN ────────────────
const allRows      = Array.from(document.querySelectorAll('#purchaseroTableBody tr[data-search]'));
const entriesInfo  = document.getElementById('entriesInfo');
let currentSearch  = '';

function onSearchInput(value) {
    currentSearch = value.toLowerCase();
    renderTable();
}

function onPerPageChange(value) {
    renderTable();
}

function renderTable() {
    if (allRows.length === 0) return;

    const perPageEl   = document.getElementById('perPageSelect');
    const perPage     = perPageEl.value === 'all' ? Infinity : parseInt(perPageEl.value, 10);
    const filterBulan = document.getElementById('filterBulan').value;
    const filterTahun = document.getElementById('filterTahun').value;
    const filterHari  = document.getElementById('filterHari').value;

    const matched = allRows.filter(row => {
        const matchSearch = row.dataset.search.includes(currentSearch);
        const tanggal     = row.dataset.tanggal || '';  // "YYYY-MM-DD"
        const [rowYear, rowMonth, rowDay] = tanggal.split('-');
        const matchHari   = !filterHari  || rowDay   === filterHari;
        const matchBulan  = !filterBulan || rowMonth === filterBulan;
        const matchTahun  = !filterTahun || rowYear  === filterTahun;
        return matchSearch && matchHari && matchBulan && matchTahun;
    });

    let shownCount = 0;
    allRows.forEach(row => row.style.display = 'none');

    matched.forEach(row => {
        if (shownCount < perPage) {
            row.style.display = '';
            shownCount++;
        }
    });

    const infoText = matched.length === 0
        ? 'Tidak ada data yang cocok'
        : `Menampilkan ${shownCount} dari ${matched.length} entri` +
          (currentSearch || filterHari || filterBulan || filterTahun ? ' (difilter)' : '');

    if (entriesInfo) entriesInfo.innerText = infoText;
    const topInfo = document.getElementById('entriesInfoTop');
    if (topInfo) topInfo.innerText = infoText;
}

function resetFilter() {
    currentSearch = '';
    document.querySelector('input[oninput="onSearchInput(this.value)"]').value = '';
    document.getElementById('filterHari').value   = '';
    document.getElementById('filterBulan').value   = '';
    document.getElementById('filterTahun').value   = '';
    document.getElementById('perPageSelect').value = '10';
    renderTable();
}

document.addEventListener('DOMContentLoaded', renderTable);

// ── CHART DISTRIBUSI STATUS (compact, sejajar dengan stat card) ──
const statusLabels = {!! json_encode($statusStats->keys()) !!};
const statusData   = {!! json_encode($statusStats->values()) !!};

const statusColorMap = {
    'Disetujui': '#22c55e',
    'Pending':   '#eab308',
    'Ditolak':   '#ef4444',
};

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusData,
            backgroundColor: statusLabels.map(label => statusColorMap[label] || '#9ca3af'),
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        cutout: '68%'
    }
});

// ── POPUP ALERT (fixed overlay) ────────────────────
(function () {
    var overlay = document.getElementById('alertOverlay');
    var box     = document.getElementById('alertBox');
    if (!overlay) return;

    setTimeout(function () {
        overlay.style.opacity      = '1';
        overlay.style.pointerEvents = 'auto';
        box.style.transform        = 'translateY(0)';
    }, 80);

    var timer = setTimeout(closeAlert, 4500);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeAlert();
    });

    function closeAlert() {
        clearTimeout(timer);
        overlay.style.opacity      = '0';
        overlay.style.pointerEvents = 'none';
        box.style.transform        = 'translateY(-16px)';
    }
    window.closeAlert = closeAlert;
})();
</script>

@endsection