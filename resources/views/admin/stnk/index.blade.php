@extends('admin.layouts.app')

@section('title', 'Data STNK Kendaraan')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Data STNK Kendaraan</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola STNK kendaraan armada</p>
            </div>
            <button onclick="openModalTambah()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
                <i class="fa fa-plus"></i>
                Tambah STNK
            </button>
        </div>


        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Total STNK --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total STNK</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $data->count() }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-contract text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- STNK Aktif --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">STNK Aktif</p>
                        <h3 class="text-3xl font-bold text-green-600 mt-2">
                            {{ $data->filter(fn($item) => !\Carbon\Carbon::parse($item->masa_berlaku)->isPast())->count() }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- STNK Expired --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">STNK Expired</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">
                            {{ $data->filter(fn($item) => \Carbon\Carbon::parse($item->masa_berlaku)->isPast())->count() }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-xmark text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total Biaya --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Biaya STNK</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-2">Rp
                            {{ number_format($data->sum('biaya')) }}
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

            {{-- TABLE HEADER + FILTER + SEARCH --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <div>
                    <h2 class="font-semibold text-slate-800 text-base">Daftar STNK Kendaraan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $data->count() }} total dokumen terdaftar</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">

                    {{-- Export PDF � href diupdate JS setiap filter berubah --}}
                    {{-- <a id="btnExportPdf" href="{{ route('stnk.export.pdf') }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a> --}}

                    {{-- Filter Bulan � nilai option pakai angka murni "06" bukan "-06-" --}}
                    <select id="filterBulan" onchange="filterTable(); updateExportLink();"
                        class="pl-3 pr-7 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 appearance-none bg-white">
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

                    {{-- Filter Tahun --}}
                    <select id="filterTahun" onchange="filterTable(); updateExportLink();"
                        class="pl-3 pr-7 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 appearance-none bg-white">
                        <option value="">Semua Tahun</option>
                        @foreach (range(date('Y'), date('Y') - 5) as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>

                    {{-- Search --}}
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="searchInput" placeholder="Cari kendaraan, pemilik, model..."
                            oninput="filterTable()"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 w-52">
                    </div>

                    {{-- Refresh --}}
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>

                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">No</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Kendaraan</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Pemilik</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Model</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Masa Berlaku</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Status</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Biaya</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Bukti</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100">

                        @forelse($data as $item)
                            @php
                                $masaBerlaku = \Carbon\Carbon::parse($item->masa_berlaku);
                                $expired     = $masaBerlaku->isPast();
                                $sisaHari    = (int) now()->diffInDays($masaBerlaku, false) + 1;
                            @endphp

                            {{--
                                data-masa  : format YYYY-MM-DD  ? dipakai JS untuk filter bulan/tahun
                                Contoh     : "2026-06-15"
                                Filter bulan "06" ? masa.substring(5,7) === "06"  ?
                            --}}
                            <tr class="hover:bg-slate-50 transition"
                                data-search="{{ strtolower(($item->nopol ?? '') . ' ' . ($item->merk ?? '') . ' ' . $item->nama_pemilik . ' ' . $item->jenis_model) }}"
                                data-masa="{{ \Carbon\Carbon::parse($item->masa_berlaku)->format('Y-m-d') }}">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>

                                {{-- Kendaraan --}}
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-800">{{ $item->nopol ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 mt-1">{{ $item->merk ?? '-' }}</div>
                                </td>

                                {{-- Pemilik --}}
                                <td class="px-5 py-4 text-slate-700">{{ $item->nama_pemilik }}</td>

                                {{-- Model --}}
                                <td class="px-5 py-4 text-slate-700">{{ $item->jenis_model }}</td>

                                {{-- Masa Berlaku --}}
                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-slate-600 text-sm">{{ $masaBerlaku->format('d M Y') }}</span>
                                        @if ($expired)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-red-600 bg-red-50 border border-red-200 px-2 py-1 rounded-full w-fit">
                                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                                Sudah Expired
                                            </span>
                                        @elseif ($sisaHari <= 30)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1 rounded-full w-fit">
                                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                {{ $sisaHari }} Hari Lagi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-full w-fit">
                                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                                Aktif
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4">
                                    @if ($expired)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Expired</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                    @endif
                                </td>

                                {{-- Biaya --}}
                                <td class="px-5 py-4 font-semibold text-slate-800">
                                    Rp {{ number_format($item->biaya, 0, ',', '.') }}
                                </td>

                                {{-- Bukti --}}
                               <td>
                                    @if ($item->bukti)
                                        @php
                                            $filename = basename($item->bukti);
                                        @endphp

                                        <a href="{{ asset($item->bukti) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800">

                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Perpanjang --}}
                                        <button type="button"
                                            class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1"
                                            onclick="openPerpanjang(
                                                '{{ $item->id }}',
                                                '{{ addslashes($item->nopol) }}',
                                                '{{ addslashes($item->merk) }}',
                                                '{{ $item->biaya }}'
                                            )">
                                            <i class="fa-solid fa-rotate-right text-xs"></i> Perpanjang
                                        </button>

                                        {{-- Edit --}}
                                        <button
                                            class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1"
                                            onclick="openEdit(
                                                '{{ $item->id }}',
                                                '{{ $item->kendaraan_id }}',
                                                '{{ addslashes($item->nama_pemilik) }}',
                                                '{{ addslashes($item->jenis_model) }}',
                                                '{{ $item->masa_berlaku }}',
                                                '{{ $item->biaya }}',
                                                '{{ $item->bukti }}'
                                            )">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
                                        </button>

                                        {{-- Hapus --}}
                                        <form action="{{ route('stnk.destroy', $item->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                            {{-- Tabel benar-benar kosong dari DB --}}
                            <tr id="emptyRow">
                                <td colspan="9" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-file-contract text-4xl mb-3 block"></i>
                                    Belum ada data STNK kendaraan
                                </td>
                            </tr>
                        @endforelse

                        {{-- Baris kosong hasil filter � hanya muncul jika ada data di DB tapi filter tidak cocok --}}
                        @if ($data->isNotEmpty())
                            <tr id="emptyRow" class="hidden">
                                <td colspan="9" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-calendar-xmark text-4xl mb-3 block"></i>
                                    <p class="mt-2 text-sm">Maaf, data bulan ini kosong</p>
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
            </div>

        </div>

    </div>


    {{-- ======================================
        MODAL TAMBAH STNK
    ====================================== --}}
    <div id="modalTambah"
        class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Tambah STNK</h2>
                    <p class="text-sm text-slate-500">Isi data STNK kendaraan</p>
                </div>
                <button onclick="closeTambah()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('stnk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kendaraan <span class="text-red-500">*</span></label>
                        <select name="kendaraan_id" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Pilih Kendaraan</option>
                            @foreach ($kendaraan as $k)
                                <option value="{{ $k->id }}" {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>{{ $k->nopol }} - {{ $k->merk }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Nama Pemilik <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pemilik" required placeholder="Nama sesuai STNK"
                            value="{{ old('nama_pemilik') }}"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Jenis Model <span class="text-red-500">*</span></label>
                        <input type="text" name="jenis_model" required placeholder="Contoh: Minibus"
                            value="{{ old('jenis_model') }}"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Masa Berlaku <span class="text-red-500">*</span></label>
                        <input type="date" name="masa_berlaku" required
                            value="{{ old('masa_berlaku') }}"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Biaya <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric" name="biaya" required placeholder="0"
                            value="{{ old('biaya') }}"
                            class="format-rupiah w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Bukti Bayar</label>

                        <div id="tambahPreviewWrap" class="hidden mb-3 relative">
                            <img id="tambahPreviewImg" src=""
                                class="hidden h-36 w-full rounded-xl border border-slate-200 object-cover cursor-pointer"
                                onclick="window.open(this.src,'_blank')">
                            <a id="tambahPreviewFile" href="#" target="_blank"
                                class="hidden flex items-center gap-3 p-4 border rounded-xl bg-slate-50 hover:bg-slate-100">
                                <i class="fa-solid fa-file text-2xl text-red-500"></i>
                                <div>
                                    <div class="font-medium text-sm text-slate-700">File Bukti</div>
                                    <div class="text-xs text-slate-500">Klik untuk membuka file</div>
                                </div>
                            </a>
                            <button type="button" onclick="hapusTambahPreview()"
                                class="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs flex items-center justify-center">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                        </div>

                        <label for="bukti_tambah"
                            class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-1"></i>
                            <span class="text-xs text-slate-500">Klik untuk upload file</span>
                            <span class="text-xs text-slate-400">(Maks 5MB)</span>
                        </label>
                        <input type="file" name="bukti" id="bukti_tambah" accept=".jpg,.jpeg,.png,.pdf"
                            class="hidden" onchange="previewBuktiTambah(this)">
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
        MODAL EDIT STNK
    ====================================== --}}
    <div id="modalEdit"
        class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Edit STNK</h2>
                    <p class="text-sm text-slate-500">Perbarui data STNK kendaraan</p>
                </div>
                <button onclick="closeEdit()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kendaraan <span class="text-red-500">*</span></label>
                        <select name="kendaraan_id" id="edit_kendaraan_id" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            @foreach ($kendaraan as $k)
                                <option value="{{ $k->id }}">{{ $k->nopol }} - {{ $k->merk }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Nama Pemilik <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pemilik" id="edit_nama_pemilik" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Jenis Model <span class="text-red-500">*</span></label>
                        <input type="text" name="jenis_model" id="edit_jenis_model" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Masa Berlaku <span class="text-red-500">*</span></label>
                        <input type="date" name="masa_berlaku" id="edit_masa_berlaku" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Biaya <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric" name="biaya" id="edit_biaya" required class="format-rupiah"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Bukti Bayar</label>

                        <div id="editPreviewWrap" class="hidden mb-3 relative">
                            <img id="editPreviewImg" src=""
                                class="hidden h-36 w-full rounded-xl border border-slate-200 object-cover cursor-pointer"
                                onclick="window.open(this.src,'_blank')">
                            <a id="editPreviewFile" href="#" target="_blank"
                                class="hidden flex items-center gap-3 p-4 border rounded-xl bg-slate-50 hover:bg-slate-100">
                                <i class="fa-solid fa-file text-2xl text-red-500"></i>
                                <div>
                                    <div class="font-medium text-sm text-slate-700">File Bukti</div>
                                    <div class="text-xs text-slate-500">Klik untuk membuka file</div>
                                </div>
                            </a>
                        </div>

                        <label for="bukti_edit"
                            class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-1"></i>
                            <span class="text-xs text-slate-500">Klik untuk ganti file</span>
                            <span class="text-xs text-slate-400">(Maks 5MB � kosongkan jika tidak diganti)</span>
                        </label>
                        <input type="file" name="bukti" id="bukti_edit" accept=".jpg,.jpeg,.png,.pdf"
                            class="hidden" onchange="previewBuktiEdit(this)">
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
        MODAL PERPANJANG STNK
    ====================================== --}}
    <div id="modalPerpanjang"
        class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Perpanjang STNK</h2>
                    <p class="text-sm text-slate-500">Data lama akan dipindahkan ke history, lalu diperbarui.</p>
                </div>
                <button onclick="closePerpanjang()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formPerpanjang" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kendaraan</label>
                        <div class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-500 cursor-not-allowed select-none">
                            <span id="perpanjang_kendaraan_text">-</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Masa Berlaku Baru <span class="text-red-500">*</span></label>
                        <input type="date" name="masa_berlaku" id="perpanjang_masa_berlaku" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Biaya Baru <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric" name="biaya" id="perpanjang_biaya" required placeholder="0" class="format-rupiah"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Bukti Bayar Baru</label>

                        <div id="perpanjangPreviewWrap" class="hidden mb-3 relative">
                            <img id="perpanjangPreviewImg" src=""
                                class="hidden h-36 w-full rounded-xl border border-slate-200 object-cover cursor-pointer"
                                onclick="window.open(this.src,'_blank')">
                            <a id="perpanjangPreviewFile" href="#" target="_blank"
                                class="hidden flex items-center gap-3 p-4 border rounded-xl bg-slate-50 hover:bg-slate-100">
                                <i class="fa-solid fa-file text-2xl text-red-500"></i>
                                <div>
                                    <div class="font-medium text-sm text-slate-700">File Bukti</div>
                                    <div class="text-xs text-slate-500">Klik untuk membuka file</div>
                                </div>
                            </a>
                            <button type="button" onclick="hapusPerpanjangPreview()"
                                class="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs flex items-center justify-center">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                        </div>

                        <label for="bukti_perpanjang"
                            class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-green-400 hover:bg-green-50 transition">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-1"></i>
                            <span class="text-xs text-slate-500">Klik untuk upload file</span>
                            <span class="text-xs text-slate-400">(Maks 5MB)</span>
                        </label>
                        <input type="file" name="bukti" id="bukti_perpanjang" accept=".jpg,.jpeg,.png,.pdf"
                            class="hidden" onchange="previewBuktiPerpanjang(this)">
                    </div>

                </div>

                <button type="submit"
                    class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate-right"></i> Perpanjang STNK
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
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @elseif (session('error'))
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
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


    {{-- STYLE --}}
    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>


    {{-- SCRIPT --}}
    <script>
        // -- AUTO-REOPEN MODAL TAMBAH ON VALIDATION ERROR ---
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            openModalTambah();
        });
        @endif

        // -- MODAL TAMBAH --------------------------------------
        function openModalTambah() {
            document.getElementById('modalTambah').classList.remove('hidden');
        }
        function closeTambah() {
            document.getElementById('modalTambah').classList.add('hidden');
        }
        document.getElementById('modalTambah').addEventListener('click', function(e) {
            if (e.target === this) closeTambah();
        });

        // -- MODAL EDIT ----------------------------------------
        function openEdit(id, kendaraan_id, nama_pemilik, jenis_model, masa_berlaku, biaya, bukti) {
            document.getElementById('formEdit').action = `/admin/stnk/${id}`;
            document.getElementById('edit_kendaraan_id').value  = kendaraan_id;
            document.getElementById('edit_nama_pemilik').value  = nama_pemilik;
            document.getElementById('edit_jenis_model').value   = jenis_model;
            document.getElementById('edit_masa_berlaku').value  = masa_berlaku.split(' ')[0];
            document.getElementById('edit_biaya').value         = biaya;
            document.getElementById('edit_biaya').dispatchEvent(new Event('input', { bubbles: true }));

            const wrap = document.getElementById('editPreviewWrap');
            const img  = document.getElementById('editPreviewImg');
            const file = document.getElementById('editPreviewFile');

            if (bukti && bukti !== '') {
                wrap.classList.remove('hidden');
                if (/\.(jpg|jpeg|png)$/i.test(bukti)) {
                    img.src = '/' + bukti;
                    img.classList.remove('hidden');
                    file.classList.add('hidden');
                } else {
                    file.href = '/' + bukti;
                    file.classList.remove('hidden');
                    img.classList.add('hidden');
                }
            } else {
                wrap.classList.add('hidden');
            }

            document.getElementById('modalEdit').classList.remove('hidden');
        }
        function closeEdit() {
            document.getElementById('modalEdit').classList.add('hidden');
        }
        document.getElementById('modalEdit').addEventListener('click', function(e) {
            if (e.target === this) closeEdit();
        });

        // -- MODAL PERPANJANG ----------------------------------
        function openPerpanjang(id, nopol, merk, biayaLama) {
            document.getElementById('formPerpanjang').action = `/admin/stnk/${id}/perpanjang`;
            document.getElementById('perpanjang_kendaraan_text').innerText = `${nopol} - ${merk}`;

            // Prefill biaya pakai nilai lama supaya tinggal disesuaikan
            document.getElementById('perpanjang_biaya').value = biayaLama;
            document.getElementById('perpanjang_biaya').dispatchEvent(new Event('input', { bubbles: true }));
            document.getElementById('perpanjang_masa_berlaku').value = '';

            hapusPerpanjangPreview();

            document.getElementById('modalPerpanjang').classList.remove('hidden');
        }
        function closePerpanjang() {
            document.getElementById('modalPerpanjang').classList.add('hidden');
        }
        document.getElementById('modalPerpanjang').addEventListener('click', function(e) {
            if (e.target === this) closePerpanjang();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTambah();
                closeEdit();
                closePerpanjang();
            }
        });

        // -- PREVIEW BUKTI TAMBAH ------------------------------
        function previewBuktiTambah(input) {
            const file = input.files[0];
            if (!file) return;
            const wrap = document.getElementById('tambahPreviewWrap');
            const img  = document.getElementById('tambahPreviewImg');
            const link = document.getElementById('tambahPreviewFile');
            const url  = URL.createObjectURL(file);
            wrap.classList.remove('hidden');
            if (file.type.startsWith('image/')) {
                img.src = url; img.classList.remove('hidden'); link.classList.add('hidden');
            } else {
                link.href = url; link.classList.remove('hidden'); img.classList.add('hidden');
            }
        }
        function hapusTambahPreview() {
            document.getElementById('bukti_tambah').value = '';
            document.getElementById('tambahPreviewWrap').classList.add('hidden');
        }

        // -- PREVIEW BUKTI EDIT --------------------------------
        function previewBuktiEdit(input) {
            const file = input.files[0];
            if (!file) return;
            const wrap = document.getElementById('editPreviewWrap');
            const img  = document.getElementById('editPreviewImg');
            const link = document.getElementById('editPreviewFile');
            const url  = URL.createObjectURL(file);
            wrap.classList.remove('hidden');
            if (file.type.startsWith('image/')) {
                img.src = url; img.classList.remove('hidden'); link.classList.add('hidden');
            } else {
                link.href = url; link.classList.remove('hidden'); img.classList.add('hidden');
            }
        }

        // -- PREVIEW BUKTI PERPANJANG ---------------------------
        function previewBuktiPerpanjang(input) {
            const file = input.files[0];
            if (!file) return;
            const wrap = document.getElementById('perpanjangPreviewWrap');
            const img  = document.getElementById('perpanjangPreviewImg');
            const link = document.getElementById('perpanjangPreviewFile');
            const url  = URL.createObjectURL(file);
            wrap.classList.remove('hidden');
            if (file.type.startsWith('image/')) {
                img.src = url; img.classList.remove('hidden'); link.classList.add('hidden');
            } else {
                link.href = url; link.classList.remove('hidden'); img.classList.add('hidden');
            }
        }
        function hapusPerpanjangPreview() {
            document.getElementById('bukti_perpanjang').value = '';
            document.getElementById('perpanjangPreviewWrap').classList.add('hidden');
            document.getElementById('perpanjangPreviewImg').classList.add('hidden');
            document.getElementById('perpanjangPreviewFile').classList.add('hidden');
        }

        // -- FILTER TABLE --------------------------------------
        // data-masa format: "YYYY-MM-DD"
        // nilai option bulan: "06" (2 digit)
        // cocokkan: masa.substring(5,7) === "06"
        function filterTable() {
            const q     = (document.getElementById('searchInput').value || '').toLowerCase();
            const bulan = document.getElementById('filterBulan').value;   // "06" atau ""
            const tahun = document.getElementById('filterTahun').value;   // "2026" atau ""

            let visible = 0;

            document.querySelectorAll('#tableBody tr[data-search]').forEach(row => {
                const search = row.dataset.search || '';
                const masa   = row.dataset.masa   || '';  // "2026-06-15"

                const matchQ = !q     || search.includes(q);
                const matchB = !bulan || masa.substring(5, 7) === bulan;  // cocok ke "06"
                const matchT = !tahun || masa.startsWith(tahun);           // cocok ke "2026"
                const show   = matchQ && matchB && matchT;

                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            // Tampilkan baris "kosong" jika tidak ada yang cocok
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) {
                emptyRow.classList.toggle('hidden', visible > 0);
            }

            // Sinkronkan link Export PDF
            updateExportLink();
        }

        // -- UPDATE LINK EXPORT PDF ----------------------------
        // Mengirim bulan (angka: "06") dan tahun ("2026") ke controller via query string
        function updateExportLink() {
            const bulan = document.getElementById('filterBulan').value;  // "06" atau ""
            const tahun = document.getElementById('filterTahun').value;  // "2026" atau ""

            const params = new URLSearchParams();
            if (bulan) params.set('bulan', bulan);   // controller: (int)"06" = 6
            if (tahun) params.set('tahun', tahun);

            const base = "{{ route('stnk.export.pdf') }}";
            document.getElementById('btnExportPdf').href =
                base + (params.toString() ? '?' + params.toString() : '');
        }

        // -- POPUP ALERT ---------------------------------------
        const alertOverlay = document.getElementById('alertOverlay');
        if (alertOverlay) {
            alertOverlay.style.pointerEvents = 'auto';
            alertOverlay.style.opacity       = '1';
            document.getElementById('alertBox').style.transform = 'translateY(0)';
            setTimeout(() => closeAlert(), 4000);
        }
        function closeAlert() {
            const overlay = document.getElementById('alertOverlay');
            if (overlay) overlay.style.display = 'none';
        }
    </script>

@endsection