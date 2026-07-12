@extends('admin.layouts.app')

@section('title', 'GPS Kendaraan')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">GPS Kendaraan</h1>
                <p class="text-sm text-slate-500 mt-1">Monitoring pemasangan GPS kendaraan rental</p>
            </div>
            <button onclick="openModal()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition inline-flex items-center gap-2">
                <i class="fa fa-plus"></i>
                Tambah GPS
            </button>
        </div>


        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Total GPS --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total GPS</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $data->count() }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-satellite-dish text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- GPS Aktif --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">GPS Aktif</p>
                        <h3 class="text-3xl font-bold text-green-600 mt-2">
                            {{ $data->where('status_gps', 'aktif')->count() }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- GPS Nonaktif --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">GPS Nonaktif</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">
                            {{ $data->where('status_gps', 'nonaktif')->count() }}</h3>
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
                        <p class="text-sm text-slate-500">Total Biaya GPS</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-2">Rp
                            {{ number_format($data->sum('biaya_sewa')) }}
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
                    <h2 class="font-semibold text-slate-800 text-base">Daftar GPS Kendaraan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $data->count() }} total perangkat terpasang</p>
                </div>
                <div class="flex items-center gap-2">

                    <a id="btnExportPdf" href="{{ route('gps-kendaraan.export.pdf') }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
                        <i class="fa fa-file-pdf"></i>
                        Export PDF
                    </a>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="searchInput" placeholder="Cari kendaraan, GPS, type..."
                            oninput="filterTable(this.value); updateExportLink();"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 w-52">
                    </div>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
                </div>
            </div>

            {{-- FILTER BAR: Show entries + Bulan & Tahun Habis --}}
            <div class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-slate-100 text-xs text-slate-500">
                {{-- Show entries --}}
                <div class="flex items-center gap-2">
                    <span>Show</span>
                    <select id="perPageSelect"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
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
                <span class="text-slate-400 font-medium">Tgl Habis:</span>

                {{-- Filter Hari --}}
                <div class="flex items-center gap-2">
                    <i class="fa fa-calendar-day text-slate-400"></i>
                    <select id="filterHari"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
                        <option value="">Semua Hari</option>
                        @for ($d = 1; $d <= 31; $d++)
                            <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Filter Bulan --}}
                <div class="flex items-center gap-2">
                    <i class="fa fa-calendar text-slate-400"></i>
                    <select id="filterBulan"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
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
                    <select id="filterTahun"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
                        <option value="">Semua Tahun</option>
                        @php
                            $years = $data->map(fn($d) => $d->tanggal_habis ? \Carbon\Carbon::parse($d->tanggal_habis)->year : null)
                                         ->filter()->unique()->sortDesc();
                        @endphp
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset --}}
                <button id="btnResetFilter"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa fa-rotate-left text-[10px]"></i> Reset
                </button>

                {{-- Entries info --}}
                <div class="ml-auto text-xs text-slate-400" id="entriesInfo"></div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">No</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Kendaraan</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">GPS</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Type</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Status GPS</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Tgl Pasang</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Tgl Habis</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Biaya</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Durasi</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Status Sewa</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Bukti</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100">

                        @forelse ($data as $d)
                            <tr class="hover:bg-slate-50 transition"
                                data-search="{{ strtolower(($d->kendaraan->merk ?? '') . ' ' . ($d->kendaraan->nopol ?? '') . ' ' . ($d->gps->nama_gps ?? '') . ' ' . $d->type . ' ' . $d->status_gps . ' ' . $d->status_sewa) }}"
                                data-tanggal-habis="{{ $d->tanggal_habis ? \Carbon\Carbon::parse($d->tanggal_habis)->format('Y-m-d') : '' }}">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>

                                {{-- Kendaraan --}}
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-800">{{ $d->kendaraan->merk ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 mt-1">{{ $d->kendaraan->nopol ?? '-' }}</div>
                                </td>

                                {{-- GPS --}}
                                <td class="px-5 py-4 text-slate-700">{{ $d->gps->nama_gps ?? '-' }}</td>

                                {{-- Type --}}
                                <td class="px-5 py-4 text-slate-700">{{ $d->type }}</td>

                                {{-- Status GPS --}}
                                <td class="px-5 py-4">
                                    @if ($d->status_gps == 'aktif')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Nonaktif</span>
                                    @endif
                                </td>

                                {{-- Tgl Pasang --}}
                                <td class="px-5 py-4 text-slate-600">{{ $d->tanggal_pasang }}</td>

                                {{-- Tgl Habis --}}
                                <td class="px-5 py-4">
                                    @php
                                        $tglHabis = \Carbon\Carbon::parse($d->tanggal_habis)->startOfDay();
                                        $hariIni = now()->startOfDay();

                                        $sisaHari = (int) $hariIni->diffInDays($tglHabis, false);
                                    @endphp

                                    <div class="flex flex-col gap-1">

                                        <span class="text-slate-600 text-sm">
                                            {{ $tglHabis->format('d M Y') }}
                                        </span>

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
                                                    Berakhir Hari Ini
                                                @elseif ($sisaHari == 1)
                                                    Berakhir Besok
                                                @else
                                                    Berakhir dalam {{ $sisaHari }} hari
                                                @endif

                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-full w-fit">
                                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                                Aktif
                                            </span>
                                        @endif

                                    </div>
                                </td>

                                {{-- Biaya --}}
                                <td class="px-5 py-4 font-semibold text-slate-800">Rp
                                    {{ number_format($d->biaya_sewa) }}
                                </td>

                                {{-- Durasi --}}
                                <td class="px-5 py-4 text-slate-700">{{ $d->durasi_bulan }} Bulan</td>

                                {{-- Status Sewa --}}
                                <td class="px-5 py-4">
                                    @if ($d->status_sewa == 'habis')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Masa
                                            Habis</span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($d->bukti_bayar)
                                        @php $filename = basename($d->bukti_bayar); @endphp
                                        <a href="{{ asset($d->bukti_bayar) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800 block">
                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif

                                    @foreach ($d->attachments as $att)
                                        <div class="flex items-center gap-1 mt-1">
                                            <a href="{{ asset($att->file_path) }}" target="_blank"
                                                class="text-blue-500 underline text-[11px] hover:text-blue-700">
                                                {{ $att->file_name }}
                                            </a>
                                            <form action="{{ route('gps.attachment.destroy', $att->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus lampiran ini?')" class="inline">
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

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Perpanjang --}}
                                        <button
                                            class="btn-perpanjang bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-lg text-xs"
                                            data-id="{{ $d->id }}" data-merk="{{ $d->kendaraan->merk ?? '-' }}"
                                            data-nopol="{{ $d->kendaraan->nopol ?? '-' }}"
                                            data-gps="{{ $d->gps->nama_gps ?? '-' }}" data-type="{{ $d->type }}"
                                            data-status="{{ $d->status_gps }}" data-biaya="{{ $d->biaya_sewa }}"
                                            data-durasi="{{ $d->durasi_bulan }}"
                                            data-tanggal-habis="{{ $d->tanggal_habis ? \Carbon\Carbon::parse($d->tanggal_habis)->format('Y-m-d') : '' }}">
                                            Perpanjang
                                        </button>

                                        {{-- Edit --}}
                                        {{-- Edit --}}
                                        <button
                                            class="btn-edit bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1"
                                            data-id="{{ $d->id }}" data-kendaraan_id="{{ $d->kendaraan_id }}"
                                            data-gps_id="{{ $d->gps_id }}" data-type="{{ $d->type }}"
                                            data-status_gps="{{ $d->status_gps }}"
                                            data-tanggal_pasang="{{ $d->tanggal_pasang }}"
                                            data-tanggal_habis="{{ $d->tanggal_habis }}"
                                            data-biaya_sewa="{{ $d->biaya_sewa }}"
                                            data-durasi_bulan="{{ $d->durasi_bulan }}"
                                            data-status_sewa="{{ $d->status_sewa }}"
                                            data-bukti_bayar="{{ $d->bukti_bayar ? asset($d->bukti_bayar) : '' }}"
                                            data-attachments="{{ htmlspecialchars(
                                                json_encode(
                                                    $d->attachments->map(
                                                        fn($a) => [
                                                            'id' => $a->id,
                                                            'name' => $a->file_name,
                                                            'url' => asset($a->file_path),
                                                        ],
                                                    ),
                                                ),
                                                ENT_QUOTES,
                                            ) }}">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
                                        </button>
                                        <form action="/admin/gps-kendaraan/{{ $d->id }}" method="POST"
                                            enctype="multipart/form-data"
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
                                <td colspan="12" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-satellite-dish text-4xl mb-3 block"></i>
                                    Belum ada data GPS kendaraan
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
            </div>

            {{-- Entries info bottom --}}
            <div class="px-5 py-3 border-t border-slate-100 text-xs text-slate-400" id="entriesInfoBottom"></div>

        </div>

    </div>


    {{-- ======================================
        MODAL TAMBAH / EDIT GPS KENDARAAN
    ====================================== --}}
    <div id="modal" class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800" id="modal-title">Tambah GPS Kendaraan</h2>
                    <p class="text-sm text-slate-500">Isi data pemasangan GPS kendaraan</p>
                </div>
                <button onclick="closeModal()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="form" action="/admin/gps-kendaraan" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="method-container"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Kendaraan --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kendaraan <span
                                class="text-red-500">*</span></label>
                        <select name="kendaraan_id" id="kendaraan_id" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            @foreach ($kendaraan as $k)
                                <option value="{{ $k->id }}">{{ $k->merk ?? '-' }} - {{ $k->nopol }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- GPS --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">GPS <span
                                class="text-red-500">*</span></label>
                        <select name="gps_id" id="gps_id" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            @foreach ($gps as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_gps }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Type GPS <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="type" id="type" required placeholder="Contoh: GT06N"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    {{-- Status GPS --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Status GPS <span
                                class="text-red-500">*</span></label>
                        <select name="status_gps" id="status_gps" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    {{-- Tanggal Pasang --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Pasang <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_pasang" id="tanggal_pasang" required
                            oninput="hitungTanggalHabis()"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    {{-- Durasi hidden = 12 --}}
                    <input type="hidden" name="durasi_bulan" id="durasi_bulan" value="12">

                    {{-- Tanggal Habis --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Habis</label>
                        <input type="date" id="tanggal_habis_display" disabled
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-500 cursor-not-allowed outline-none">
                        <input type="hidden" name="tanggal_habis" id="tanggal_habis">
                        <p class="text-xs text-slate-400 mt-1">Otomatis tanggal pasang + 1 tahun</p>
                    </div>

                    {{-- Biaya Sewa --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Biaya Sewa <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="biaya_sewa" id="biaya_sewa" required placeholder="0"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    {{-- Bukti Bayar --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Bukti Bayar</label>

                        <div id="previewWrap" class="hidden mb-3 relative">
                            <img id="previewImg" src=""
                                class="hidden h-36 w-full rounded-xl border border-slate-200 object-cover cursor-pointer"
                                onclick="window.open(this.src,'_blank')">
                            <a id="previewFile" href="#" target="_blank"
                                class="hidden flex items-center gap-3 p-4 border rounded-xl bg-slate-50 hover:bg-slate-100">
                                <i class="fa-solid fa-file text-2xl text-red-500"></i>
                                <div>
                                    <div class="font-medium text-sm text-slate-700">File Bukti Bayar</div>
                                    <div class="text-xs text-slate-500">Klik untuk membuka file</div>
                                </div>
                            </a>
                            <button type="button" onclick="hapusPreview()"
                                class="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs flex items-center justify-center">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                        </div>

                        <label for="bukti_bayar"
                            class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-1"></i>
                            <span class="text-xs text-slate-500">Klik untuk upload file</span>
                            <span class="text-xs text-slate-400"> (Maks 5MB)</span>
                        </label>
                        <input type="file" name="bukti_bayar" id="bukti_bayar" class="hidden"
                            onchange="previewBukti(this)" required>
                    </div>

                </div>

                {{-- Lampiran Tambahan --}}
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-slate-700 mb-1 block">
                        Lampiran Tambahan (opsional, bisa lebih dari 1)
                    </label>

                    <label for="bukti_attachment"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <i class="fa-solid fa-paperclip text-xl text-slate-400 mb-1"></i>
                        <span class="text-xs text-slate-500">Klik untuk upload lampiran tambahan</span>
                        <span class="text-xs text-slate-400">(Maks 5MB per file)</span>
                    </label>

                    <input type="file" name="bukti_attachment[]" id="bukti_attachment" class="hidden" multiple
                        onchange="renderListAttachment(this, 'listAttachmentGps')">

                    <ul id="listAttachmentGps" class="mt-2 space-y-1 text-xs text-slate-600"></ul>
                </div>

                <button type="submit"
                    class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data
                </button>

            </form>

        </div>
    </div>

    {{-- ======================================
        MODAL PERPANJANG GPS KENDARAAN
    ====================================== --}}
    <div id="modalPerpanjang"
        class="hidden fixed inset-0 z-[9999] flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 my-6" style="animation:slideUp .2s ease">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Perpanjang GPS Kendaraan</h2>
                    <p class="text-sm text-slate-500">Data lama akan dipindahkan ke history, lalu diperbarui.</p>
                </div>
                <button onclick="closeModalPerpanjang()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formPerpanjang" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Kendaraan readonly --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Kendaraan</label>
                        <div class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-500 cursor-not-allowed select-none">
                            <span id="perpanjang_kendaraan_text">-</span>
                        </div>
                    </div>

                    {{-- GPS readonly --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">GPS</label>
                        <div class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-500 cursor-not-allowed select-none">
                            <span id="perpanjang_gps_text">-</span>
                        </div>
                    </div>

                    {{-- Type GPS readonly --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Type GPS</label>
                        <input id="perpanjang_type" type="text" readonly
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-500 cursor-not-allowed">
                    </div>

                    {{-- Tanggal Habis Baru: otomatis tgl_habis_lama + 1 tahun --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Habis Baru</label>
                        <input id="perpanjang_tanggal_habis_display" type="date" disabled
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-500 cursor-not-allowed">
                        <input type="hidden" name="tanggal_habis" id="perpanjang_tanggal_habis">
                        <p class="text-xs text-slate-400 mt-1">Otomatis tanggal habis lama + 1 tahun</p>
                    </div>

                    {{-- Tanggal Bayar editable, default hari ini, sync ke tanggal_pasang --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" id="perpanjang_tanggal_bayar"
                            value="{{ now()->format('Y-m-d') }}"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        {{-- tanggal_pasang = tanggal_bayar (auto-sync) --}}
                        <input type="hidden" name="tanggal_pasang" id="perpanjang_tanggal_pasang" value="{{ now()->format('Y-m-d') }}">
                        <input type="hidden" name="durasi_bulan" value="12">
                    </div>

                    {{-- Biaya readonly (dari data lama) --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Biaya Sewa</label>
                        <input id="perpanjang_biaya" type="number" name="biaya_sewa" readonly
                            class="w-full border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed rounded-xl px-4 py-3 text-sm">
                    </div>

                    {{-- Bukti Bayar Baru (required) --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">Bukti Bayar Baru <span class="text-red-500">*</span></label>
                        <input type="file" name="bukti_bayar"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm" required>
                    </div>

                    {{-- Lampiran Tambahan --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1 block">
                            Lampiran Tambahan <span class="text-slate-400 font-normal">(opsional, bisa lebih dari 1)</span>
                        </label>
                        <input type="file" name="bukti_attachment[]" multiple
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm"
                            onchange="renderListAttachment(this, 'listAttachmentPerpanjangGps')">
                        <ul id="listAttachmentPerpanjangGps" class="mt-2 space-y-1 text-xs text-slate-600"></ul>
                    </div>

                </div>

                <button type="submit"
                    class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate-right"></i> Perpanjang GPS
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






    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const modal = document.getElementById('modal');
            const modalPerpanjang = document.getElementById('modalPerpanjang');
            const form = document.getElementById('form');
            const formPerpanjang = document.getElementById('formPerpanjang');
            const methodContainer = document.getElementById('method-container');
            const modalTitle = document.getElementById('modal-title');

            function show(el) {
                el.classList.remove('hidden');
            }

            function hide(el) {
                el.classList.add('hidden');
            }

            function resetForm() {
                form.reset();
                form.action = '/admin/gps-kendaraan';
                methodContainer.innerHTML = '';
                modalTitle.textContent = 'Tambah GPS Kendaraan';
                document.getElementById('previewWrap').classList.add('hidden');
                document.getElementById('previewImg').classList.add('hidden');
                document.getElementById('previewFile').classList.add('hidden');
                document.getElementById('listAttachmentGps').innerHTML = '';
                document.getElementById('bukti_attachment').value = '';
                document.getElementById('bukti_bayar').required = true;
                // Reset display tanggal habis
                const displayEl = document.getElementById('tanggal_habis_display');
                if (displayEl) displayEl.value = '';
            }

            // ✅ Expose ke global supaya onclick="openModal()" di HTML bisa jalan
            window.openModal = function() {
                resetForm();
                show(modal);
            };

            window.closeModal = function() {
                hide(modal);
            };
            window.closeModalPerpanjang = function() {
                hide(modalPerpanjang);
            };

            function openEdit(btn) {
                resetForm();
                modalTitle.textContent = 'Edit GPS Kendaraan';
                form.action = `/admin/gps-kendaraan/${btn.dataset.id}`;
                methodContainer.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

                document.getElementById('kendaraan_id').value = btn.dataset.kendaraan_id;
                document.getElementById('gps_id').value = btn.dataset.gps_id;
                document.getElementById('type').value = btn.dataset.type;
                document.getElementById('status_gps').value = btn.dataset.status_gps;
                document.getElementById('biaya_sewa').value = btn.dataset.biaya_sewa;

                // Set tanggal pasang, lalu hitung tanggal_habis = pasang + 1 tahun
                const tglPasang = btn.dataset.tanggal_pasang;
                document.getElementById('tanggal_pasang').value = tglPasang;
                if (tglPasang) {
                    const d = new Date(tglPasang);
                    d.setFullYear(d.getFullYear() + 1);
                    const val = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
                    document.getElementById('tanggal_habis_display').value = val;
                    document.getElementById('tanggal_habis').value = val;
                }
                // durasi_bulan hidden tetap 12

                // === Bukti bayar lama ===
                const buktiInput = document.getElementById('bukti_bayar');
                buktiInput.required = false; // tidak wajib upload ulang saat edit

                const buktiUrl = btn.dataset.bukti_bayar;
                if (buktiUrl) {
                    const wrap = document.getElementById('previewWrap');
                    const img = document.getElementById('previewImg');
                    const link = document.getElementById('previewFile');

                    wrap.classList.remove('hidden');

                    const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(buktiUrl);
                    if (isImage) {
                        img.src = buktiUrl;
                        img.classList.remove('hidden');
                        link.classList.add('hidden');
                    } else {
                        link.href = buktiUrl;
                        link.classList.remove('hidden');
                        img.classList.add('hidden');
                    }
                }

                // === Lampiran tambahan lama ===
                const list = document.getElementById('listAttachmentGps');
                list.innerHTML = '';
                try {
                    const attachments = JSON.parse(btn.dataset.attachments || '[]');
                    attachments.forEach(att => {
                        const li = document.createElement('li');
                        li.className = 'flex items-center gap-1.5';
                        li.innerHTML = `
                <i class="fa-solid fa-paperclip text-slate-400"></i>
                <a href="${att.url}" target="_blank" class="text-blue-600 underline">${att.name}</a>
                <span class="text-slate-400">(lampiran lama)</span>
            `;
                        list.appendChild(li);
                    });
                } catch (e) {
                    console.error('Gagal parse attachments lama:', e);
                }

                show(modal);
            }

            function syncPerpanjangGpsDates() {
                const bayarInput  = document.getElementById('perpanjang_tanggal_bayar');
                const startInput  = document.getElementById('perpanjang_tanggal_pasang');
                if (!bayarInput || !startInput) return;
                // tanggal_pasang = tanggal_bayar
                startInput.value = bayarInput.value;
            }

            const tanggalBayarGpsInput = document.getElementById('perpanjang_tanggal_bayar');
            if (tanggalBayarGpsInput) {
                tanggalBayarGpsInput.addEventListener('change', syncPerpanjangGpsDates);
            }

            function openPerpanjang(btn) {
                formPerpanjang.action = `/admin/gps-kendaraan/${btn.dataset.id}/perpanjang`;

                document.getElementById('perpanjang_kendaraan_text').innerText =
                    `${btn.dataset.merk} - ${btn.dataset.nopol}`;
                document.getElementById('perpanjang_gps_text').innerText = btn.dataset.gps;
                document.getElementById('perpanjang_type').value  = btn.dataset.type;
                document.getElementById('perpanjang_biaya').value = btn.dataset.biaya;
                document.getElementById('listAttachmentPerpanjangGps').innerHTML = '';

                // Set tanggal_bayar = hari ini, sync tanggal_pasang
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('perpanjang_tanggal_bayar').value  = today;
                document.getElementById('perpanjang_tanggal_pasang').value = today;

                // tanggal_habis_baru = tanggal_habis_lama + 1 tahun (dari data-tanggal-habis)
                const tglHabisLama = btn.dataset.tanggalHabis; // format Y-m-d
                if (tglHabisLama) {
                    const d = new Date(tglHabisLama);
                    d.setFullYear(d.getFullYear() + 1);
                    const val = d.getFullYear() + '-'
                        + String(d.getMonth() + 1).padStart(2, '0') + '-'
                        + String(d.getDate()).padStart(2, '0');
                    document.getElementById('perpanjang_tanggal_habis_display').value = val;
                    document.getElementById('perpanjang_tanggal_habis').value = val;
                }

                show(modalPerpanjang);
            }

            // ✅ Satu event listener, pakai closest, tanpa capture phase
            document.addEventListener('click', function(e) {

                const perpanjangBtn = e.target.closest('.btn-perpanjang');
                if (perpanjangBtn) {
                    openPerpanjang(perpanjangBtn);
                    return; // stop, jangan lanjut cek edit
                }

                const editBtn = e.target.closest('.btn-edit');
                if (editBtn) {
                    openEdit(editBtn);
                    return;
                }
            });

            // Tutup modal klik backdrop
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) hide(modal);
            });
            modalPerpanjang?.addEventListener('click', (e) => {
                if (e.target === modalPerpanjang) hide(modalPerpanjang);
            });

            window.hitungTanggalHabis = function() {
                const tgl = document.getElementById('tanggal_pasang').value;
                if (!tgl) return;
                const d = new Date(tgl);
                d.setFullYear(d.getFullYear() + 1);
                const val = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
                document.getElementById('tanggal_habis_display').value = val;
                document.getElementById('tanggal_habis').value = val;
            };

            window.hitungTanggalHabisPerpanjang = function() {
                const tgl = document.getElementById('perpanjang_tanggal_pasang').value;
                const dur = parseInt(document.getElementById('perpanjang_durasi').value);
                if (!tgl || !dur) return;
                const d = new Date(tgl);
                d.setMonth(d.getMonth() + dur);
                document.getElementById('perpanjang_tanggal_habis').value =
                    `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
            };

            window.filterTable = function(q) {
                applyFilter();
            };

            // ── SEARCH + SHOW ENTRIES + FILTER BULAN/TAHUN ──────────
            function applyFilter() {
                const keyword     = (document.getElementById('searchInput').value || '').toLowerCase();
                const filterBulan = document.getElementById('filterBulan').value;
                const filterTahun = document.getElementById('filterTahun').value;
                const filterHari  = document.getElementById('filterHari').value;
                const perPageEl   = document.getElementById('perPageSelect');
                const perPage     = perPageEl.value === 'all' ? Infinity : parseInt(perPageEl.value, 10);

                const allRows = Array.from(document.querySelectorAll('#tableBody tr[data-search]'));
                let matched   = [];
                let nomor     = 1;

                allRows.forEach(row => {
                    const matchSearch = row.dataset.search.includes(keyword);

                    const tglHabis         = row.dataset.tanggalHabis || '';  // "YYYY-MM-DD"
                    const [rowYear, rowMonth, rowDay] = tglHabis.split('-');
                    const matchHari   = !filterHari  || rowDay   === filterHari;
                    const matchBulan  = !filterBulan || rowMonth === filterBulan;
                    const matchTahun  = !filterTahun || rowYear  === filterTahun;

                    row.style.display = 'none';
                    if (matchSearch && matchHari && matchBulan && matchTahun) matched.push(row);
                });

                let shown = 0;
                matched.forEach(row => {
                    if (shown < perPage) {
                        row.style.display = '';
                        shown++;
                    }
                });

                const infoText = matched.length === 0
                    ? 'Tidak ada data yang cocok'
                    : 'Menampilkan ' + shown + ' dari ' + matched.length + ' entri' +
                      (keyword || filterHari || filterBulan || filterTahun ? ' (difilter)' : '');

                const topInfo = document.getElementById('entriesInfo');
                const botInfo = document.getElementById('entriesInfoBottom');
                if (topInfo) topInfo.innerText = infoText;
                if (botInfo) botInfo.innerText = infoText;
            }

            // Event listeners untuk dropdown filter
            document.getElementById('perPageSelect').addEventListener('change', applyFilter);
            document.getElementById('filterBulan').addEventListener('change', applyFilter);
            document.getElementById('filterTahun').addEventListener('change', applyFilter);
            document.getElementById('filterHari').addEventListener('change', applyFilter);

            document.getElementById('btnResetFilter').addEventListener('click', function() {
                document.getElementById('searchInput').value   = '';
                document.getElementById('filterHari').value   = '';
                document.getElementById('filterBulan').value  = '';
                document.getElementById('filterTahun').value  = '';
                document.getElementById('perPageSelect').value = '10';
                applyFilter();
            });

            // Juga fix updateExportLink supaya tidak error
            window.updateExportLink = function() {};

            // Jalankan saat halaman pertama load
            applyFilter();

            window.closeAlert = function() {
                const overlay = document.getElementById('alertOverlay');
                if (overlay) overlay.style.display = 'none';
            };

            // Auto-show alert
            const alertOverlay = document.getElementById('alertOverlay');
            if (alertOverlay) {
                alertOverlay.style.pointerEvents = 'auto';
                alertOverlay.style.opacity = '1';
                document.getElementById('alertBox').style.transform = 'translateY(0)';
                setTimeout(() => closeAlert(), 4000);
            }

            window.previewBukti = function(input) {
                const file = input.files[0];
                if (!file) return;
                const wrap = document.getElementById('previewWrap');
                const img = document.getElementById('previewImg');
                const link = document.getElementById('previewFile');
                const url = URL.createObjectURL(file);
                wrap.classList.remove('hidden');
                if (file.type.startsWith('image/')) {
                    img.src = url;
                    img.classList.remove('hidden');
                    link.classList.add('hidden'); // ✅ diperbaiki dari 'adda' menjadi 'add'
                } else {
                    link.href = url;
                    link.classList.remove('hidden');
                    img.classList.add('hidden');
                }
            };

            window.hapusPreview = function() {
                document.getElementById('bukti_bayar').value = '';
                document.getElementById('previewWrap').classList.add('hidden');
            };

        });

        document.querySelectorAll('.btn-perpanjang').forEach(btn => {
            console.log('btn-perpanjang found:', btn.dataset);
        });

        window.renderListAttachment = function(input, listId) {
            const list = document.getElementById(listId);
            list.innerHTML = '';

            Array.from(input.files).forEach(file => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-1.5';
                li.innerHTML = `<i class="fa-solid fa-paperclip text-slate-400"></i> ${file.name}`;
                list.appendChild(li);
            });
        };
    </script>


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
@endsection
