@extends('admin.layouts.app')

@section('title', 'Data Pajak Kendaraan')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Pajak Kendaraan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola pajak kendaraan armada</p>
            </div>
            <button onclick="openModalTambah()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Pajak
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Pajak</p>
                        <h2 class="text-3xl font-bold text-gray-800 mt-1">{{ $data->count() }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Sudah Lunas</p>
                        <h2 class="text-3xl font-bold text-green-600 mt-1">
                            {{ $data->where('status', 'sudah_bayar')->count() }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Belum Lunas</p>
                        <h2 class="text-3xl font-bold text-red-600 mt-1">
                            {{ $data->where('status', 'belum_bayar')->count() }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                        <i class="fa-solid fa-circle-xmark text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Nominal</p>
                        <h2 class="text-xl font-bold text-emerald-600 mt-1">Rp
                            {{ number_format($data->sum('nominal'), 0, ',', '.') }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-xl"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Pajak Kendaraan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data pajak</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input id="searchPajak" type="text" placeholder="Cari nopol, merk, status, jenis pajak..."
                            oninput="filterPajakTable(this.value)"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-64">
                    </div>
                    <button onclick="exportPdf()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-download text-xs"></i> Export
                    </button>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
                    {{-- FILTER STATUS --}}
                    <div class="flex items-center gap-1 border border-gray-200 rounded-lg p-0.5 bg-gray-50">
                        <button onclick="filterStatus('semua')" id="btnSemua"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors bg-white text-gray-700 shadow-sm border border-gray-200">
                            Semua
                        </button>
                        <button onclick="filterStatus('sudah_bayar')" id="btnLunas"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-gray-500 hover:text-green-600">
                            <i class="fa fa-check text-[10px]"></i> Lunas
                        </button>
                        <button onclick="filterStatus('belum_bayar')" id="btnBelum"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-gray-500 hover:text-red-500">
                            <i class="fa fa-times text-[10px]"></i> Belum
                        </button>
                    </div>
                </div>
            </div>

            {{-- FILTER BAR: Show entries + Hari, Bulan & Tahun --}}
            <div class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
                {{-- Show entries --}}
                <div class="flex items-center gap-2">
                    <span>Show</span>
                    <select id="perPageSelect" onchange="applyFilter()"
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

                {{-- Filter Hari --}}
                <div class="flex items-center gap-2">
                    <i class="fa fa-calendar-day text-gray-400"></i>
                    <select id="filterHari" onchange="applyFilter()"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">Semua Hari</option>
                        @for ($d = 1; $d <= 31; $d++)
                            <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>

                <div class="w-px h-4 bg-gray-200"></div>

                {{-- Filter Bulan --}}
                <div class="flex items-center gap-2">
                    <i class="fa fa-calendar text-gray-400"></i>
                    <select id="filterBulan" onchange="applyFilter()"
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
                    <select id="filterTahun" onchange="applyFilter()"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">Semua Tahun</option>
                        @php
                            $years = $data->map(fn($d) => $d->jatuh_tempo ? \Carbon\Carbon::parse($d->jatuh_tempo)->year : null)
                                         ->filter()->unique()->sortDesc();
                        @endphp
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset filter --}}
                <button onclick="resetFilter()"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa fa-rotate-left text-[10px]"></i> Reset
                </button>

                {{-- Entries info --}}
                <div class="ml-auto text-xs text-gray-400" id="entriesInfo"></div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kendaraan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Jenis Pajak</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Nominal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Jatuh Tempo</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl
                                Bayar</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Bukti</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Keterangan</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pajakTableBody">
                        @forelse($data as $item)
                            @php
                                $today = \Carbon\Carbon::now();
                                $jatuhTempo = \Carbon\Carbon::parse($item->jatuh_tempo);
                                $selisihHari = (int) $today->diffInDays($jatuhTempo, false);
                            @endphp
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                data-search="{{ strtolower(
                                    ($item->kendaraan->nopol ?? '') .
                                        ' ' .
                                        ($item->kendaraan->merk ?? '') .
                                        ' ' .
                                        ($item->jenis_pajak ?? '') .
                                        ' ' .
                                        ($item->nominal ?? '') .
                                        ' ' .
                                        ($item->jatuh_tempo ?? '') .
                                        ' ' .
                                        ($item->tanggal_bayar ?? '') .
                                        ' ' .
                                        ($item->status ?? '') .
                                        ' ' .
                                        ($item->keterangan ?? ''),
                                ) }}"
                                data-status="{{ $item->status }}"
                                data-jatuh-tempo="{{ $item->jatuh_tempo ? \Carbon\Carbon::parse($item->jatuh_tempo)->format('Y-m-d') : '' }}">

                                <td class="px-4 py-3.5 text-sm text-gray-500">{{ $loop->iteration }}</td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="fa fa-car text-blue-400 text-xs"></i>
                                        </div>

                                        <div class="flex flex-col leading-tight">
                                            <span class="text-sm font-semibold text-gray-800">
                                                {{ $item->kendaraan->nopol ?? '-' }}
                                            </span>

                                            <span class="text-xs text-gray-500">
                                                {{ $item->kendaraan->merk ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->jenis_pajak }}</td>

                                <td class="px-4 py-3.5">
                                    <span class="font-mono text-xs text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex flex-col gap-1">

                                        <span class="text-sm text-gray-700">
                                            {{ \Carbon\Carbon::parse($item->jatuh_tempo)->translatedFormat('j F Y') }}
                                        </span>

                                        @if ($selisihHari < 0)
                                            <span
                                                class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full w-fit font-semibold">

                                                ⚠️ Terlambat {{ abs($selisihHari) }} hari

                                            </span>
                                        @elseif ($selisihHari <= $reminder)
                                            <span
                                                class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full w-fit font-semibold">


                                                ⚠️ Jatuh tempo {{ $selisihHari }} hari lagi

                                            </span>
                                        @endif

                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-500">
                                    {{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->translatedFormat('j F Y') : '-' }}
                                </td>

                                <td class="px-4 py-3.5">
                                    @if ($item->status == 'sudah_bayar')
                                        <span
                                            class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <i class="fa fa-check text-[10px]"></i> Lunas
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 bg-red-100 text-red-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <i class="fa fa-times text-[10px]"></i> Belum
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->bukti)
                                        @php $filename = basename($item->bukti); @endphp
                                        <a href="{{ asset($item->bukti) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800 block">
                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif

                                    @foreach ($item->attachments as $att)
                                        <div class="flex items-center gap-1 mt-1">
                                            <a href="{{ asset($att->file_path) }}" target="_blank"
                                                class="text-blue-500 underline text-[11px] hover:text-blue-700">
                                                {{ $att->file_name }}
                                            </a>
                                            <form action="{{ route('pajak.attachment.destroy', $att->id) }}"
                                                method="POST" onsubmit="return confirm('Hapus lampiran ini?')"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-400 hover:text-red-600 text-[10px]">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[140px] truncate">
                                    {{ $item->keterangan ?? '-' }}</td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">

                                        {{-- Perpanjangan --}}
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors"
                                            onclick="openModalPerpanjang(
                '{{ $item->id }}',
                '{{ $item->kendaraan_id }}',
                 '{{ $item->kendaraan->nopol ?? '-' }}',
    '{{ $item->kendaraan->merk ?? '-' }}',
                '{{ $item->jenis_pajak }}',
                '{{ $item->nominal }}',
                '{{ $item->jatuh_tempo }}',
                '{{ $item->tanggal_bayar }}',
                '{{ $item->status }}',
                '{{ addslashes($item->keterangan) }}',
                '{{ $item->bukti }}'
            )">
                                            <i class="fa fa-rotate-right text-xs"></i>
                                            Perpanjang
                                        </button>

                                        {{-- Edit --}}
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                            onclick="openModalEdit(
                '{{ $item->id }}',
                '{{ $item->kendaraan_id }}',
                '{{ $item->jenis_pajak }}',
                '{{ $item->nominal }}',
                '{{ $item->jatuh_tempo }}',
                '{{ $item->tanggal_bayar }}',
                '{{ $item->status }}',
                '{{ addslashes($item->keterangan) }}',
                '{{ $item->bukti }}'
            )">
                                            <i class="fa fa-edit text-xs"></i>
                                            Edit
                                        </button>

                                        {{-- Hapus --}}
                                        <form action="{{ route('pajak.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors">

                                                <i class="fa fa-trash text-xs"></i>
                                                Hapus

                                            </button>

                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fa fa-file-invoice-dollar text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data pajak kendaraan</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Pajak" untuk menambahkan data baru
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Entries info bottom --}}
            <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfoBottom"></div>

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH
====================================== --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Pajak Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data pajak kendaraan</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('pajak.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span
                                class="text-red-500">*</span></label>
                        <select name="kendaraan_id" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach ($kendaraan as $k)
                                <option value="{{ $k->id }}">{{ $k->nopol }} - {{ $k->merk }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pajak <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jenis_pajak" required placeholder="Contoh: PKB, BBNKB"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="nominal" required placeholder="Nominal pajak"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Bayar <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_bayar" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="belum_bayar">Belum Lunas</option>
                            <option value="sudah_bayar">Lunas</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Bukti Pembayaran
                        </label>

                        {{-- Preview --}}
                        <div id="previewWrapPajak" class="hidden mb-3 relative">

                            {{-- Preview Gambar --}}
                            <img id="previewImgPajak" src=""
                                class="hidden h-40 w-full rounded-xl border border-gray-200 object-cover cursor-pointer"
                                onclick="window.open(this.src,'_blank')">

                            {{-- Preview File --}}
                            <a id="previewFilePajak" href="#" target="_blank"
                                class="hidden flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-gray-100">

                                <i class="fa-solid fa-file text-2xl text-red-500"></i>

                                <div>
                                    <div class="font-medium text-sm text-gray-700">
                                        File Bukti Pembayaran
                                    </div>
                                    <div class="text-xs text-gray-500">

                                    </div>
                                </div>
                            </a>

                            <button type="button" onclick="hapusPreviewPajak()"
                                class="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs flex items-center justify-center">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                        </div>

                        {{-- Upload Area --}}
                        <label for="bukti"
                            class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-1"></i>

                            <span class="text-xs text-gray-500">
                                Klik untuk upload bukti pembayaran
                            </span>

                            <span class="text-xs text-gray-400">
                                (Maks 5MB)
                            </span>
                        </label>

                        <input type="file" name="bukti" id="bukti" required class="hidden"
                            onchange="previewBuktiPajak(this)">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Lampiran Tambahan (opsional, bisa lebih dari 1)
                        </label>

                        <label for="bukti_attachment"
                            class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <i class="fa-solid fa-paperclip text-xl text-gray-400 mb-1"></i>
                            <span class="text-xs text-gray-500">Klik untuk upload lampiran tambahan</span>
                            <span class="text-xs text-gray-400">(Maks 5MB per file)</span>
                        </label>

                        <input type="file" name="bukti_attachment[]" id="bukti_attachment" class="hidden" multiple
                            onchange="renderListAttachment(this, 'listAttachmentTambah')">

                        <ul id="listAttachmentTambah" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan <span
                                class="text-red-500">*</span></label>
                        <textarea name="keterangan" rows="3" required placeholder="Tambahkan keterangan..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                    </div>

                </div>

                <button type="submit"
                    class="mt-5 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                    <i class="fa fa-save text-sm"></i> Simpan Data
                </button>
            </form>

        </div>
    </div>


    {{-- ======================================
    MODAL EDIT
====================================== --}}
    <div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Pajak Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data pajak kendaraan</p>
                </div>
                <button onclick="closeModalEdit()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" enctype="multipart/form-data" class="px-6 py-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan</label>
                        <select name="kendaraan_id" id="edit_kendaraan_id"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            @foreach ($kendaraan as $k)
                                <option value="{{ $k->id }}">{{ $k->nopol }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pajak</label>
                        <input type="text" name="jenis_pajak" id="edit_jenis_pajak"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal</label>
                        <input type="number" name="nominal" id="edit_nominal"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jatuh Tempo</label>
                        <input type="date" name="jatuh_tempo" id="edit_jatuh_tempo"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Tanggal Bayar
                        </label>

                        <input type="date" name="tanggal_bayar" value="{{ now()->format('Y-m-d') }}"
                            id="edit_tanggal_bayar" readonly
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                        <select name="status" id="edit_status"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="belum_bayar">Belum Lunas</option>
                            <option value="sudah_bayar">Lunas</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Bukti Pembayaran
                        </label>

                        {{-- Preview --}}
                        <div id="previewWrapEdit" class="hidden mb-3 relative">

                            {{-- Preview Gambar --}}
                            <img id="previewImgEdit" src=""
                                class="hidden h-40 w-full rounded-xl border border-gray-200 object-cover cursor-pointer"
                                onclick="window.open(this.src,'_blank')">

                            {{-- Preview File --}}
                            <a id="previewFileEdit" href="#" target="_blank"
                                class="hidden flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-gray-100">

                                <i class="fa-solid fa-file text-2xl text-red-500"></i>

                                <div>
                                    <div class="font-medium text-sm text-gray-700" id="previewFileNameEdit">
                                        File Bukti Pembayaran
                                    </div>
                                    <div class="text-xs text-gray-500">

                                    </div>
                                </div>
                            </a>

                            <button type="button" onclick="hapusPreviewEdit()"
                                class="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs flex items-center justify-center">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                        </div>

                        {{-- Upload Area --}}
                        <label for="edit_bukti"
                            class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-1"></i>

                            <span class="text-xs text-gray-500">
                                Klik untuk upload bukti pembayaran
                            </span>

                            <span class="text-xs text-gray-400">
                                (Maks 5MB, kosongkan jika tidak ingin mengubah bukti lama)
                            </span>
                        </label>

                        <input type="file" name="bukti" id="edit_bukti" class="hidden"
                            onchange="previewBuktiEdit(this)">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" rows="3"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                    </div>

                </div>

                <button type="submit"
                    class="mt-5 w-full bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                    <i class="fa fa-save text-sm"></i> Update Data
                </button>
            </form>

        </div>
    </div>

    {{-- ======================================
MODAL PERPANJANG
====================================== --}}
    <div id="modalPerpanjang" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">

        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">

                <div>
                    <h2 class="text-base font-bold text-gray-800">
                        Perpanjang Pajak Kendaraan
                    </h2>

                    <p class="text-xs text-gray-500 mt-0.5">
                        Data lama akan dipindahkan ke history kemudian diperbarui.
                    </p>
                </div>

                <button onclick="closeModalPerpanjang()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg">
                    <i class="fa fa-times"></i>
                </button>

            </div>

            <form id="formPerpanjang" method="POST" enctype="multipart/form-data" class="px-6 py-5">

                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Kendaraan
                        </label>

                        <div
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-sm text-gray-700 cursor-not-allowed select-none">
                            <span id="perpanjang_kendaraan_text">-</span>
                        </div>

                        <input type="hidden" name="kendaraan_id" id="perpanjang_kendaraan">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Jenis Pajak
                        </label>

                        <input id="perpanjang_jenis" type="text" name="jenis_pajak"
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Nominal Baru
                        </label>

                        <input id="perpanjang_nominal" type="number" name="nominal"
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Jatuh Tempo Baru
                        </label>

                        <input id="perpanjang_jatuh_tempo" type="date" name="jatuh_tempo"
                            class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Tanggal Bayar
                        </label>

                        <input type="date" name="tanggal_bayar" id="perpanjang_tanggal_bayar"
                            value="{{ now()->format('Y-m-d') }}"
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Status
                        </label>

                        <div
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-sm text-gray-700 cursor-not-allowed select-none">
                            Lunas
                        </div>

                        <input type="hidden" name="status" value="sudah_bayar" id="perpanjang_status">
                    </div>

                    <div class="sm:col-span-2">

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Bukti Pembayaran Baru
                        </label>

                        <input id="perpanjang_bukti" type="file" required class="w-full border rounded-lg px-3 py-2">

                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Lampiran Tambahan (opsional, bisa lebih dari 1)
                        </label>

                        <input id="perpanjang_bukti_attachment" type="file" name="bukti_attachment[]" multiple
                            class="w-full border rounded-lg px-3 py-2"
                            onchange="renderListAttachment(this, 'listAttachmentPerpanjang')">

                        <ul id="listAttachmentPerpanjang" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                    </div>

                    <div class="sm:col-span-2">

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Keterangan
                        </label>

                        <textarea id="perpanjang_keterangan" name="keterangan" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>

                    </div>

                </div>

                <button class="mt-5 w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold">

                    <i class="fa fa-rotate-right"></i>

                    Perpanjang Pajak

                </button>

            </form>

        </div>

    </div>

    {{-- ======================================
    POPUP ALERT
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
                        <p class="text-sm font-bold text-gray-800">Gagal!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">
                            {{ session('error') }}
                        </p>
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


    {{-- STYLE & SCRIPT --}}
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
        // ── MODAL TAMBAH ──────────────────────────────────────
        const modalTambah = document.getElementById('modalTambah');

        function openModalTambah() {
            modalTambah.classList.remove('hidden');
            modalTambah.classList.add('flex');
        }

        function closeModalTambah() {
            modalTambah.classList.add('hidden');
            modalTambah.classList.remove('flex');
            document.getElementById('listAttachmentTambah').innerHTML = '';
            document.getElementById('bukti_attachment').value = '';
        }

        modalTambah.addEventListener('click', function(e) {
            if (e.target === modalTambah) closeModalTambah();
        });

        // ── MODAL EDIT ──────────────────────────────────────
        const modalEdit = document.getElementById('modalEdit');

        function openModalEdit(id, kendaraan_id, jenis_pajak, nominal, jatuh_tempo, tanggal_bayar, status, keterangan,
            bukti) {
            document.getElementById('formEdit').action = `/admin/pajak/${id}`;
            document.getElementById('edit_kendaraan_id').value = kendaraan_id;
            document.getElementById('edit_jenis_pajak').value = jenis_pajak;
            document.getElementById('edit_nominal').value = nominal;
            document.getElementById('edit_jatuh_tempo').value = formatDate(jatuh_tempo);
            document.getElementById('edit_tanggal_bayar').value = formatDate(tanggal_bayar);
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_keterangan').value = keterangan;

            // reset input file & tampilkan preview bukti lama (kalau ada)
            document.getElementById('edit_bukti').value = '';
            tampilkanPreviewBuktiLama(bukti);

            modalEdit.classList.remove('hidden');
            modalEdit.classList.add('flex');
        }
        const modalPerpanjang = document.getElementById('modalPerpanjang');

        function openModalPerpanjang(
            id,
            kendaraan_id,
            nopol,
            merk,
            jenis,
            nominal,
            jatuhTempo,
            tanggalBayar,
            status,
            keterangan,
            bukti
        ) {
            document.getElementById('formPerpanjang').action =
                `/admin/pajak/${id}/perpanjang`;

            document.getElementById('perpanjang_kendaraan').value = kendaraan_id;
            document.getElementById('perpanjang_kendaraan_text').innerText =
                `${nopol} - ${merk}`;

            document.getElementById('perpanjang_jenis').value = jenis;
            document.getElementById('perpanjang_nominal').value = nominal;
            document.getElementById('perpanjang_jatuh_tempo').value = formatDate(jatuhTempo);
            document.getElementById('perpanjang_status').value = status;
            document.getElementById('perpanjang_keterangan').value = keterangan;

            modalPerpanjang.classList.remove('hidden');
            modalPerpanjang.classList.add('flex');
        }

        function closeModalEdit() {
            modalEdit.classList.add('hidden');
            modalEdit.classList.remove('flex');
        }

        modalEdit.addEventListener('click', function(e) {
            if (e.target === modalEdit) closeModalEdit();
        });

        function formatDate(dateString) {
            if (!dateString) return '';
            return dateString.split(' ')[0];
        }

        // ── SEARCH FILTER ──────────────────────────────────────
        // ── STATUS FILTER ──────────────────────────────────────
        let activeStatus = 'semua';

        function filterStatus(status) {
            activeStatus = status;

            // update tampilan tombol aktif
            const buttons = {
                semua: document.getElementById('btnSemua'),
                sudah_bayar: document.getElementById('btnLunas'),
                belum_bayar: document.getElementById('btnBelum'),
            };

            Object.entries(buttons).forEach(([key, btn]) => {
                if (key === status) {
                    btn.classList.add('bg-white', 'shadow-sm', 'border', 'border-gray-200');
                    if (key === 'sudah_bayar') btn.classList.add('text-green-700');
                    else if (key === 'belum_bayar') btn.classList.add('text-red-600');
                    else btn.classList.add('text-gray-700');
                    btn.classList.remove('text-gray-500');
                } else {
                    btn.classList.remove('bg-white', 'shadow-sm', 'border', 'border-gray-200', 'text-green-700',
                        'text-red-600', 'text-gray-700');
                    btn.classList.add('text-gray-500');
                }
            });

            applyFilter();
        }

        function filterPajakTable(keyword) {
            applyFilter(keyword);
        }

        function applyFilter(keyword) {
            if (keyword === undefined) {
                keyword = document.getElementById('searchPajak').value;
            }
            keyword = keyword.toLowerCase();

            const filterBulan  = document.getElementById('filterBulan').value;   // e.g. '07'
            const filterTahun  = document.getElementById('filterTahun').value;   // e.g. '2026'
            const filterHari   = document.getElementById('filterHari').value;    // e.g. '15'
            const perPageEl    = document.getElementById('perPageSelect');
            const perPage      = perPageEl.value === 'all' ? Infinity : parseInt(perPageEl.value, 10);

            const allRows = Array.from(document.querySelectorAll('#pajakTableBody tr[data-search]'));
            let matched   = [];
            let nomor     = 1;

            allRows.forEach(row => {
                const matchSearch = row.dataset.search.includes(keyword);
                const matchStatus = activeStatus === 'semua' || row.dataset.status === activeStatus;

                // data-jatuh-tempo format: "YYYY-MM-DD"
                const jatuhTempo  = row.dataset.jatuhTempo || '';  // "2026-07-15"
                const [rowYear, rowMonth, rowDay] = jatuhTempo.split('-');
                const matchHari   = !filterHari   || rowDay   === filterHari;
                const matchBulan  = !filterBulan  || rowMonth === filterBulan;
                const matchTahun  = !filterTahun  || rowYear  === filterTahun;

                const tampil = matchSearch && matchStatus && matchHari && matchBulan && matchTahun;
                row.style.display = 'none';

                if (tampil) matched.push(row);
            });

            let shown = 0;
            matched.forEach(row => {
                if (shown < perPage) {
                    row.style.display = '';
                    row.querySelector('td:first-child').textContent = nomor++;
                    shown++;
                }
            });

            const infoText = matched.length === 0
                ? 'Tidak ada data yang cocok'
                : 'Menampilkan ' + shown + ' dari ' + matched.length + ' entri' +
                  (keyword || filterHari || filterBulan || filterTahun || activeStatus !== 'semua' ? ' (difilter)' : '');

            const topInfo = document.getElementById('entriesInfo');
            const botInfo = document.getElementById('entriesInfoBottom');
            if (topInfo) topInfo.innerText = infoText;
            if (botInfo) botInfo.innerText = infoText;
        }

        function resetFilter() {
            document.getElementById('searchPajak').value  = '';
            document.getElementById('filterHari').value   = '';
            document.getElementById('filterBulan').value  = '';
            document.getElementById('filterTahun').value  = '';
            document.getElementById('perPageSelect').value = '10';
            activeStatus = 'semua';

            // reset tombol status
            const buttons = {
                semua: document.getElementById('btnSemua'),
                sudah_bayar: document.getElementById('btnLunas'),
                belum_bayar: document.getElementById('btnBelum'),
            };
            Object.entries(buttons).forEach(([key, btn]) => {
                if (key === 'semua') {
                    btn.classList.add('bg-white', 'shadow-sm', 'border', 'border-gray-200', 'text-gray-700');
                    btn.classList.remove('text-gray-500');
                } else {
                    btn.classList.remove('bg-white', 'shadow-sm', 'border', 'border-gray-200', 'text-green-700', 'text-red-600', 'text-gray-700');
                    btn.classList.add('text-gray-500');
                }
            });

            applyFilter('');
        }

        document.addEventListener('DOMContentLoaded', () => applyFilter(''));

        // ── POPUP ALERT ────────────────────────────────────────
        (function() {
            var overlay = document.getElementById('alertOverlay');
            var box = document.getElementById('alertBox');
            if (!overlay) return;


            setTimeout(function() {
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
                box.style.transform = 'translateY(0)';
            }, 80);

            var timer = setTimeout(closeAlert, 4500);

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


        function exportPdf() {
            let search = document.getElementById('searchPajak').value;

            window.open(
                "{{ route('pajak.export.pdf') }}?search=" + encodeURIComponent(search),
                '_blank'
            );
        }


        // ── PREVIEW BUKTI (dipakai bersama oleh Tambah & Edit) ──
        function renderPreviewBukti(file, wrapId, imgId, fileBoxId) {
            const wrap = document.getElementById(wrapId);
            const img = document.getElementById(imgId);
            const fileBox = document.getElementById(fileBoxId);

            wrap.classList.remove('hidden');

            const ext = file.name.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {

                fileBox.classList.add('hidden');
                img.classList.remove('hidden');

                const reader = new FileReader();

                reader.onload = function(e) {
                    img.src = e.target.result;
                };

                reader.readAsDataURL(file);

            } else {

                img.classList.add('hidden');
                fileBox.classList.remove('hidden');

                fileBox.querySelector('.font-medium').innerText = file.name;
            }
        }

        function previewBuktiPajak(input) {
            const file = input.files[0];
            if (!file) return;
            renderPreviewBukti(file, 'previewWrapPajak', 'previewImgPajak', 'previewFilePajak');
        }

        function hapusPreviewPajak() {

            document.getElementById('bukti').value = '';

            document.getElementById('previewWrapPajak')
                .classList.add('hidden');

            document.getElementById('previewImgPajak')
                .classList.add('hidden');

            document.getElementById('previewFilePajak')
                .classList.add('hidden');
        }

        // ── PREVIEW BUKTI KHUSUS MODAL EDIT ──
        function previewBuktiEdit(input) {
            const file = input.files[0];
            if (!file) return;
            renderPreviewBukti(file, 'previewWrapEdit', 'previewImgEdit', 'previewFileEdit');
        }

        function hapusPreviewEdit() {

            document.getElementById('edit_bukti').value = '';

            document.getElementById('previewWrapEdit')
                .classList.add('hidden');

            document.getElementById('previewImgEdit')
                .classList.add('hidden');

            document.getElementById('previewFileEdit')
                .classList.add('hidden');
        }

        // Menampilkan bukti lama (dari database) saat modal edit dibuka
        function tampilkanPreviewBuktiLama(buktiPath) {
            const wrap = document.getElementById('previewWrapEdit');
            const img = document.getElementById('previewImgEdit');
            const fileBox = document.getElementById('previewFileEdit');
            const fileName = document.getElementById('previewFileNameEdit');

            if (!buktiPath || buktiPath === '' || buktiPath === 'null') {
                wrap.classList.add('hidden');
                img.classList.add('hidden');
                fileBox.classList.add('hidden');
                return;
            }

            const ext = buktiPath.split('.').pop().toLowerCase();
            wrap.classList.remove('hidden');

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                fileBox.classList.add('hidden');
                img.classList.remove('hidden');
                img.src = '/' + buktiPath.replace(/^\/+/, '');
            } else {
                img.classList.add('hidden');
                fileBox.classList.remove('hidden');
                fileBox.href = '/' + buktiPath.replace(/^\/+/, '');
                fileName.innerText = buktiPath.split('/').pop();
            }
        }

        function closeModalPerpanjang() {
            modalPerpanjang.classList.add('hidden');
            modalPerpanjang.classList.remove('flex');
            document.getElementById('listAttachmentPerpanjang').innerHTML = '';
        }

        modalPerpanjang.addEventListener('click', function(e) {
            if (e.target === modalPerpanjang) closeModalPerpanjang();
        });

        function renderListAttachment(input, listId) {
            const list = document.getElementById(listId);
            list.innerHTML = '';

            Array.from(input.files).forEach(file => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-1.5';
                li.innerHTML = `<i class="fa-solid fa-paperclip text-gray-400"></i> ${file.name}`;
                list.appendChild(li);
            });
        }
    </script>

@endsection
