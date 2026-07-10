@extends('admin.layouts.app')

@section('title', 'Manajemen Vendor')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Vendor</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data vendor / supplier barang & jasa</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
            <i class="fa fa-plus text-sm"></i>
            Tambah Vendor
        </button>
    </div>

    {{-- STAT CARDS + CHART (4 kolom sejajar, ukuran sama) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-truck-field"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalVendor }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Vendor</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalAktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Vendor Aktif</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-pause"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalNonaktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Vendor Tidak Aktif</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 flex-shrink-0 relative">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Distribusi Status</p>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Aktif
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> Tidak Aktif
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Vendor</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total vendor</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari vendor..."
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
            <span class="text-gray-400 font-medium">Tgl Order Terakhir:</span>

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
                        $years = $data->map(fn($d) => $d->tanggal_terakhir_order ? \Carbon\Carbon::parse($d->tanggal_terakhir_order)->year : null)
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kode Vendor</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama Vendor</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kategori</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">PIC Vendor</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No. Telp</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Produk/Jasa</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Rating</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Terakhir Order</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody id="vendoreoTableBody">
                    @forelse($data as $d)
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                            data-search="{{ strtolower($d->kode_vendor . ' ' . $d->nama_vendor . ' ' . $d->kategori . ' ' . $d->pic_vendor) }}"
                            data-tanggal="{{ $d->tanggal_terakhir_order ? \Carbon\Carbon::parse($d->tanggal_terakhir_order)->format('Y-m-d') : '' }}">

                            <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>

                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->kode_vendor }}</span>
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-truck-field text-blue-400 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">{{ $d->nama_vendor ?? '-' }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->kategori ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->pic_vendor ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->no_telp ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[160px] truncate">{{ $d->produk_jasa ?? '-' }}</td>

                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-500">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star {{ $i <= ($d->rating ?? 0) ? 'text-amber-400' : 'text-gray-200' }}"></i>
                                    @endfor
                                    <span class="text-gray-500 ml-1">({{ $d->rating ?? '-' }})</span>
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                @if($d->status === 'Aktif')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                                        <i class="fa fa-circle text-[6px]"></i> Aktif
                                    </span>
                                @elseif($d->status === 'Tidak Aktif')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                                        <i class="fa fa-circle text-[6px]"></i> Tidak Aktif
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal_terakhir_order ? \Carbon\Carbon::parse($d->tanggal_terakhir_order)->format('Y-m-d') : '-' }}</td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $d->id }}"
                                        data-action="{{ route('vendoreo.update', $d->id) }}"
                                        data-kode_vendor="{{ $d->kode_vendor }}"
                                        data-nama_vendor="{{ $d->nama_vendor }}"
                                        data-kategori="{{ $d->kategori }}"
                                        data-alamat="{{ $d->alamat }}"
                                        data-pic_vendor="{{ $d->pic_vendor }}"
                                        data-no_telp="{{ $d->no_telp }}"
                                        data-produk_jasa="{{ $d->produk_jasa }}"
                                        data-rating="{{ $d->rating }}"
                                        data-status="{{ $d->status }}"
                                        data-tanggal_terakhir_order="{{ $d->tanggal_terakhir_order }}"
                                        data-catatan="{{ $d->catatan }}"
                                        onclick="triggerEdit(this)">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                        data-action="{{ route('vendoreo.destroy', $d->id) }}"
                                        data-name="{{ $d->nama_vendor }}"
                                        onclick="triggerDelete(this)">
                                        <i class="fa fa-trash text-xs"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-truck-field text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data vendor</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah Vendor" untuk menambahkan vendor baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
        </div>

        {{-- ENTRIES INFO --}}
        <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfo"></div>

    </div>

</div>


{{-- ======================================
    MODAL TAMBAH / EDIT VENDOR
======================================--}}
<div id="vendoreoModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto"
         style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Vendor</h2>
                <p id="modalSubtitle" class="text-xs text-gray-500 mt-0.5">Kode Vendor akan dibuat otomatis</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="vendoreoForm" action="{{ route('vendoreo.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>

            {{-- Kode Vendor: hanya tampil saat edit, non-editable --}}
            <div id="kodeVendorBox" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Vendor</label>
                <div class="flex items-center gap-2">
                    <span id="f_kode_vendor_display" class="font-mono text-xs text-gray-600 bg-gray-100 px-3 py-2 rounded-lg border border-gray-200"></span>
                    <span class="text-xs text-gray-400">(otomatis, tidak bisa diubah)</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Vendor <span class="text-red-500">*</span></label>
                <input type="text" name="nama_vendor" id="f_nama_vendor" required
                    placeholder="Contoh: PT Tekstil Nusantara"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" id="f_kategori" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih Kategori -</option>
                        <option value="Bahan">Bahan</option>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Jasa">Jasa</option>
                        <option value="Mesin & Peralatan">Mesin & Peralatan</option>
                        <option value="Packaging">Packaging</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">PIC Vendor <span class="text-red-500">*</span></label>
                    <input type="text" name="pic_vendor" id="f_pic_vendor" required
                        placeholder="Contoh: Sari"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat <span class="text-red-500">*</span></label>
                <input type="text" name="alamat" id="f_alamat" required
                    placeholder="Contoh: Jl. Industri No. 12, Bandung"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Telp <span class="text-red-500">*</span></label>
                    <input type="number" name="no_telp" id="f_no_telp" required
                        placeholder="Contoh: 0812-3333-8888"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Rating <span class="text-red-500">*</span></label>
                    <select name="rating" id="f_rating" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih Rating -</option>
                        <option value="1">1 - Sangat Kurang</option>
                        <option value="2">2 - Kurang</option>
                        <option value="3">3 - Cukup</option>
                        <option value="4">4 - Baik</option>
                        <option value="5">5 - Sangat Baik</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Produk/Jasa <span class="text-red-500">*</span></label>
                <input type="text" name="produk_jasa" id="f_produk_jasa" required
                    placeholder="Contoh: Kain, Furing"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="f_status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih Status -</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Terakhir Order</label>
                    <input type="date" name="tanggal_terakhir_order" id="f_tanggal_terakhir_order"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <p class="text-[11px] text-gray-400 mt-1">Opsional, kosongkan jika vendor belum pernah order</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                <textarea name="catatan" id="f_catatan" rows="3"
                    placeholder="Catatan tambahan (opsional)..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>

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
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Vendor?</h2>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                Kamu akan menghapus vendor <strong id="deleteName" class="text-gray-700"></strong>.
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
// ── VENDOREO MODAL (Tambah/Edit) ────────────────────
const vendoreoModal   = document.getElementById('vendoreoModal');
const vendoreoForm    = document.getElementById('vendoreoForm');
const methodContainer = document.getElementById('methodContainer');
const kodeVendorBox   = document.getElementById('kodeVendorBox');
const createUrl       = "{{ route('vendoreo.store') }}";

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Vendor';
    document.getElementById('modalSubtitle').innerText = 'Kode Vendor akan dibuat otomatis';
    vendoreoForm.action = createUrl;
    methodContainer.innerHTML = '';
    kodeVendorBox.classList.add('hidden');
    vendoreoForm.reset();
    vendoreoModal.classList.remove('hidden');
    vendoreoModal.classList.add('flex');
}

function closeModal() {
    vendoreoModal.classList.add('hidden');
    vendoreoModal.classList.remove('flex');
}

vendoreoModal.addEventListener('click', function (e) {
    if (e.target === vendoreoModal) closeModal();
});

function triggerEdit(btn) {
    document.getElementById('modalTitle').innerText = 'Edit Vendor';
    document.getElementById('modalSubtitle').innerText = 'Perbarui detail vendor';
    vendoreoForm.action = btn.dataset.action;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

    document.getElementById('f_kode_vendor_display').innerText = btn.dataset.kode_vendor;
    kodeVendorBox.classList.remove('hidden');

    document.getElementById('f_nama_vendor').value             = btn.dataset.nama_vendor ?? '';
    document.getElementById('f_kategori').value                = btn.dataset.kategori ?? '';
    document.getElementById('f_pic_vendor').value               = btn.dataset.pic_vendor ?? '';
    document.getElementById('f_alamat').value                   = btn.dataset.alamat ?? '';
    document.getElementById('f_no_telp').value                  = btn.dataset.no_telp ?? '';
    document.getElementById('f_rating').value                   = btn.dataset.rating ?? '';
    document.getElementById('f_produk_jasa').value              = btn.dataset.produk_jasa ?? '';
    document.getElementById('f_status').value                   = btn.dataset.status ?? '';
    document.getElementById('f_tanggal_terakhir_order').value   = btn.dataset.tanggal_terakhir_order ?? '';
    document.getElementById('f_catatan').value                  = btn.dataset.catatan ?? '';

    vendoreoModal.classList.remove('hidden');
    vendoreoModal.classList.add('flex');
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
const allRows      = Array.from(document.querySelectorAll('#vendoreoTableBody tr[data-search]'));
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
    const searchEl = document.querySelector('input[oninput="onSearchInput(this.value)"]');
    if (searchEl) searchEl.value = '';
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

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusData,
            backgroundColor: statusLabels.map(label => label === 'Aktif' ? '#22c55e' : '#ef4444'),
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