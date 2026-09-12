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

            {{-- GPS Menunggu Pembayaran (sudah disetujui PO, belum disetujui Pembayaran) --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Menunggu Pembayaran</p>
                        <h3 class="text-3xl font-bold text-blue-600 mt-2">
                            {{ $data->where('persetujuan', 'Diajukan ke Pembayaran')->count() }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-paper-plane text-2xl"></i>
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



        {{-- CHART FILTER --}}
        <x-chart-filter id="gpsChartFilter" defaultFilter="year" :showCustomRange="true" />

        {{-- CHART CONTAINER --}}
        <x-chart-container
            id="gpsChartContainer"
            layout="stacked"
            pieTitle="Distribusi Status GPS" pieId="gpsPieChart"
            barTitle="Biaya GPS per Bulan" barId="gpsBarChart"
            lineTitle="Trend GPS" lineId="gpsLineChart"
            :showStats="true" :statsData="[]"
        />

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- TABLE HEADER + SEARCH --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <div>
                    <h2 class="font-semibold text-slate-800 text-base">Daftar GPS Kendaraan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $data->total() }} total perangkat terpasang</p>
                </div>
                <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-2">
                    <a id="btnExportPdf" href="{{ route('gps-kendaraan.export.pdf', request()->only(['search','hari','bulan','tahun'])) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" placeholder="Cari kendaraan, GPS, type..."
                            value="{{ request('search') }}"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 w-52">
                    </div>
                    {{-- preserve other filters --}}
                    @if(request('hari')) <input type="hidden" name="hari" value="{{ request('hari') }}"> @endif
                    @if(request('bulan')) <input type="hidden" name="bulan" value="{{ request('bulan') }}"> @endif
                    @if(request('tahun')) <input type="hidden" name="tahun" value="{{ request('tahun') }}"> @endif
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fa fa-search text-xs"></i> Cari
                    </button>
                    @if(request()->hasAny(['search','hari','bulan','tahun']))
                        <a href="{{ request()->url() }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fa fa-rotate-left text-xs"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- FILTER BAR: Bulan & Tahun Habis --}}
            <form method="GET" action="{{ request()->url() }}" id="formFilterGps"
                  class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-slate-100 text-xs text-slate-500">

                {{-- preserve search --}}
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                <div class="w-px h-4 bg-gray-200"></div>
                <span class="text-slate-400 font-medium">Tgl Habis:</span>

                {{-- Filter Hari --}}
                <div class="flex items-center gap-2">
                    <i class="fa fa-calendar-day text-slate-400"></i>
                    <select name="hari" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
                        <option value="">Semua Hari</option>
                        @for ($d = 1; $d <= 31; $d++)
                            <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}" {{ request('hari') == str_pad($d, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Filter Bulan --}}
                <div class="flex items-center gap-2">
                    <i class="fa fa-calendar text-slate-400"></i>
                    <select name="bulan" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
                        <option value="">Semua Bulan</option>
                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $nama)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tahun --}}
                <div class="flex items-center gap-2">
                    <select name="tahun" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
                        <option value="">Semua Tahun</option>
                        @foreach(range(date('Y') + 1, date('Y') - 3) as $yr)
                            <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <a href="{{ request()->url() }}"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-blue-50/50 transition-colors">
                    <i class="fa fa-rotate-left text-[10px]"></i> Reset
                </a>

            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">No</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Kendaraan</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">GPS</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Type</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Tgl Buat</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Tgl Ketentuan Bayar</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Jatuh Tempo</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Biaya</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Durasi</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Status Sewa</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Bukti</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Lampiran</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Persetujuan</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100">

                        @forelse ($data as $d)
                            <tr class="hover:bg-slate-50 transition"
                                data-search="{{ strtolower(($d->kendaraan->merk ?? '') . ' ' . ($d->kendaraan->nopol ?? '') . ' ' . ($d->gps->nama_gps ?? '') . ' ' . $d->type . ' ' . $d->status_sewa) }}"
                                data-tanggal-habis="{{ $d->tanggal_habis ? \Carbon\Carbon::parse($d->tanggal_habis)->format('Y-m-d') : '' }}">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-gray-400 row-number">{{ $data->firstItem() + $loop->index }}</td>

                                {{-- Kendaraan --}}
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-800">{{ $d->kendaraan->merk ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 mt-1">{{ $d->kendaraan->nopol ?? '-' }}</div>
                                </td>

                                {{-- GPS --}}
                                <td class="px-5 py-4 text-slate-700">{{ $d->gps->nama_gps ?? '-' }}</td>

                                {{-- Type --}}
                                <td class="px-5 py-4 text-slate-700">{{ $d->type }}</td>

                                {{-- Tgl Buat (tidak berubah saat perpanjang) --}}
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $d->tanggal_buat ? \Carbon\Carbon::parse($d->tanggal_buat)->format('d M Y') : '-' }}
                                </td>

                                {{-- Tgl Pasang --}}
                                <td class="px-5 py-4 text-slate-600">
    {{ $d->tanggal_pasang ? \Carbon\Carbon::parse($d->tanggal_pasang)->format('d M Y') : '-' }}
        </td>

                                {{-- Tgl Habis --}}
                                <td class="px-5 py-4">
                                @php
                                    $tglHabis = \Carbon\Carbon::parse($d->tanggal_habis)->startOfDay();
                                    $hariIni  = now()->startOfDay();
                                    $sisaHari = (int) $hariIni->diffInDays($tglHabis, false);
                                    // Hitung detik real-time ke akhir hari jatuh tempo
                                    $sisaDetikGps = (int) (\Carbon\Carbon::parse($d->tanggal_habis)->endOfDay()->timestamp - now()->timestamp);
                                @endphp

                                    <div class="flex flex-col gap-1">

                                        <span class="text-slate-600 text-sm">
                                            {{ $tglHabis->format('d M Y') }}
                                        </span>

                                        @if ($sisaHari < 0)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-red-600 bg-red-50 border border-red-200 px-2 py-1 rounded-full w-fit">
                                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                                Terlambat {{ formatSisaWaktu(abs($sisaDetikGps)) }}
                                            </span>
                                        @elseif ($sisaHari <= $reminder)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium {{ $sisaHari == 0 ? 'text-red-500 bg-red-50 border-red-200' : 'text-amber-600 bg-amber-50 border-amber-200' }} border px-2 py-1 rounded-full w-fit">
                                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                @if ($sisaHari == 0)
                                                    Berakhir {{ formatSisaWaktu($sisaDetikGps) }} lagi
                                                @elseif ($sisaHari == 1)
                                                    Berakhir Besok
                                                @else
                                                    Berakhir dalam {{ $sisaHari }} hari
                                                @endif
                                            </span>
                                        @else
                                            
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
                                    @if ($d->status_sewa == 'expired')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Expired</span>
                                    @elseif ($d->status_sewa == 'tidak_aktif')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Tidak Aktif</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if ($d->bukti_bayar)
                                        @php
                                            $gpsBuktiName = preg_replace('/^\d+_/', '', basename($d->bukti_bayar));
                                        @endphp
                                        <a href="{{ asset($d->bukti_bayar) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800 block truncate max-w-[140px]"
                                            title="{{ $gpsBuktiName }}">
                                            {{ $gpsBuktiName }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if($d->attachments->isNotEmpty())
                                        <div class="flex flex-col gap-1">
                                            @foreach ($d->attachments as $att)
                                                @php
                                                    $attExt   = strtolower(pathinfo($att->file_name, PATHINFO_EXTENSION));
                                                    $isImage  = in_array($attExt, ['jpg','jpeg','png','gif','webp']);
                                                @endphp
                                                <a href="{{ asset($att->file_path) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:text-blue-800 underline max-w-[140px] truncate"
                                                    title="{{ $att->file_name }}">
                                                    <i class="fa-solid {{ $isImage ? 'fa-image' : 'fa-paperclip' }} text-[9px]"></i>
                                                    {{ $att->file_name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Persetujuan --}}
                                <td class="px-5 py-4">
                                    @if($d->persetujuan === 'Disetujui')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui
                                        </span>
                                    @elseif($d->persetujuan === 'Diajukan ke Pembayaran')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            <i class="fa-solid fa-paper-plane text-[10px]"></i> Diajukan ke Pembayaran
                                        </span>
                                    @elseif($d->persetujuan === 'Ditolak')
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 w-fit">
                                                <i class="fa-solid fa-circle-xmark text-[10px]"></i> Ditolak di Pembayaran
                                            </span>
                                            
                                        </div>
                                    @elseif($d->persetujuan === 'Pending')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            <i class="fa-solid fa-clock text-[10px]"></i> Pending
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Ajukan Ulang: hanya untuk GPS Ditolak di Pembayaran --}}
                                        @if($d->persetujuan === 'Ditolak' && $d->pembayaran_id)
                                            @if($d->pembayaran?->source_type === 'gps_perpanjang')
                                            {{-- Perpanjang: ajukan ulang via pembayaran_id --}}
                                            <button type="button"
                                                onclick="openAjukanUlangPerpanjangModal({{ $d->pembayaran_id }}, '{{ $d->kendaraan->nopol ?? '-' }}', '{{ $d->kendaraan->merk ?? '-' }}', '{{ $d->gps->nama_gps ?? '-' }}', '{{ $d->type }}')"
                                                class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1">
                                                <i class="fa-solid fa-rotate-right text-xs"></i> Ajukan Ulang
                                            </button>
                                            @else
                                            {{-- Tambah baru: ajukan ulang via gps_kendaraan.id --}}
                                            <button type="button"
                                                onclick="openAjukanUlangModal({{ $d->id }}, '{{ $d->kendaraan->nopol ?? '-' }}', '{{ $d->kendaraan->merk ?? '-' }}', '{{ $d->gps->nama_gps ?? '-' }}', '{{ $d->type }}')"
                                                class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1">
                                                <i class="fa-solid fa-rotate-right text-xs"></i> Ajukan Ulang
                                            </button>
                                            @endif
                                        @endif

                                        {{-- Perpanjang: hanya tampil jika sudah dalam batas reminder --}}
                                        @if ($sisaHari <= $reminder)
                                        <button
                                            class="btn-perpanjang bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-lg text-xs font-medium transition inline-flex items-center gap-1"
                                            data-id="{{ $d->id }}"
                                            data-kendaraan-id="{{ $d->kendaraan_id }}"
                                            data-merk="{{ $d->kendaraan->merk ?? '-' }}"
                                            data-nopol="{{ $d->kendaraan->nopol ?? '-' }}"
                                            data-nama-gps="{{ $d->gps->nama_gps ?? '-' }}"
                                            data-type="{{ $d->type }}"
                                            data-status-gps="{{ $d->status_gps }}"
                                            data-biaya="{{ $d->biaya_sewa }}"
                                            data-tanggal-habis="{{ $d->tanggal_habis ? \Carbon\Carbon::parse($d->tanggal_habis)->format('Y-m-d') : '' }}"
                                            data-tanggal-habis-baru="{{ $d->tanggal_habis ? \Carbon\Carbon::parse($d->tanggal_habis)->addYear()->format('Y-m-d') : '' }}"
                                            data-tanggal-bayar="{{ $d->tanggal_bayar ? \Carbon\Carbon::parse($d->tanggal_bayar)->format('Y-m-d') : '' }}">
                                            <i class="fa-solid fa-rotate-right text-xs"></i> Perpanjang
                                        </button>
                                        @endif

                                        
                                        {{-- Edit --}}
                                        <!-- <button
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
                                        </button> -->
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
                                        <button type="button"
                                            onclick="openDetailModal('gps', {{ $d->id }}); event.stopPropagation()"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                            <i class="fa fa-eye text-xs"></i> Detail
                                        </button>
                                    </div>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="14" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-satellite-dish text-4xl mb-3 block"></i>
                                    Belum ada data GPS kendaraan
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
                <div class="py-3 px-5 border-t border-gray-100">
                    <x-pagination :paginator="$data" />
                </div>
            </div>

        </div>

    </div>


    {{-- ======================================
        MODAL TAMBAH GPS KENDARAAN
        Shared: kendaraan, status, tgl bayar, biaya, bukti
        Multi: GPS + Type (tambah baris)
    ====================================== --}}
    <div id="modal" class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 my-6" style="animation:slideUp .2s ease">

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Tambah GPS Kendaraan</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Data bersama + bisa tambah banyak GPS (type tidak boleh sama)</p>
                </div>
                <button onclick="closeModal()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="form" action="/admin/gps-kendaraan" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- ── DATA BERSAMA ─────────────────────────────────────── --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-5 mb-5 border-b border-slate-100">

                    {{-- Kendaraan --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1.5 block">Kendaraan <span class="text-red-500">*</span></label>
                        <select name="kendaraan_id" id="shared_kendaraan_id" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            @foreach ($kendaraan as $k)
                                <option value="{{ $k->id }}">{{ $k->merk ?? '-' }} — {{ $k->nopol }}</option>
                            @endforeach
                        </select>
                    </div>
  <div>
                        <label class="text-sm font-medium text-slate-700 mb-1.5 block">Jatuh Tempo</label>
                        <input type="date" id="shared_jatuh_tempo_display" disabled
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 text-slate-400 cursor-not-allowed outline-none">
                        <input type="hidden" name="tanggal_habis" id="shared_tanggal_habis">
                        <p class="text-[11px] text-slate-400 mt-1">Otomatis tanggal bayar + 1 tahun</p>
                    </div>
                    {{-- Tanggal Bayar --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-1.5 block">Tanggal Ketentuan Bayar <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_bayar" id="shared_tanggal_bayar" required
                            oninput="onSharedTglBayarChange()"
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    {{-- Jatuh Tempo (readonly) --}}
                  

                    {{-- (Biaya dipindah ke per-baris GPS) --}}
                    {{-- (Bukti bayar & lampiran dipindah ke per-baris GPS) --}}

                    {{-- Keterangan --}}
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 mb-1.5 block">Keterangan <span class="text-slate-400 font-normal text-xs">(opsional)</span></label>
                        <textarea name="keterangan" rows="2" placeholder="Catatan tambahan..."
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none"></textarea>
                    </div>

                </div>

                {{-- ── DAFTAR GPS + TYPE (multi-baris) ─────────────────── --}}
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-semibold text-slate-700">Daftar GPS</p>
                        <span class="text-xs text-slate-400">Type tidak boleh sama</span>
                    </div>

                    <div id="gpsItemsContainer" class="space-y-3"></div>

                    <button type="button" onclick="tambahGpsItem()"
                        class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-indigo-600 border border-indigo-300 rounded-lg hover:bg-indigo-50 transition">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah GPS
                    </button>
                </div>

                {{-- live warning duplikat --}}
                <div id="duplikatWarning" class="hidden mb-3 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                    <span id="duplikatText"></span>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="btnSimpanGps"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2.5 rounded-xl transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ======================================
        MODAL PERPANJANG GPS KENDARAAN
        Setiap GPS diperpanjang dengan biaya & bukti sendiri-sendiri
    ====================================== --}}
    <div id="modalPerpanjang"
        class="hidden fixed inset-0 z-[9999] flex items-start justify-center bg-black/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl p-6 my-6" style="animation:slideUp .2s ease">

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Perpanjang GPS Kendaraan</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Pengajuan akan dikirim ke Superadmin untuk disetujui. Bukti bayar diunggah saat approval.</p>
                </div>
                <button onclick="closeModalPerpanjang()"
                    class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formPerpanjang" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_modal_open" value="perpanjang">

                {{-- Info kendaraan --}}
                <div class="flex items-center gap-3 px-4 py-3 bg-indigo-50 border border-indigo-100 rounded-xl mb-5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-car text-indigo-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800" id="perp_kendaraan_label">-</p>
                    </div>
                </div>

                {{-- Tanggal Bayar (shared) --}}
                <div class="mb-5">
                    <label class="text-sm font-medium text-slate-700 mb-1.5 block">Tanggal Ketentuan Bayar <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_bayar" id="perp_tanggal_bayar" required
                        class="w-full md:w-64 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                {{-- Daftar GPS — per-baris dengan biaya & bukti sendiri --}}
                <div class="space-y-4" id="perp_gps_list">
                    {{-- diisi via JS --}}
                </div>

                <div class="flex gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeModalPerpanjang()"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="btnPerpanjangGps"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2.5 rounded-xl transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rotate-right"></i> Perpanjang GPS
                    </button>
                </div>
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
        // ── Data GPS dari PHP ke JS ───────────────────────────────────────────
        const GPS_LIST = @json($gps->map(fn($g) => ['id' => $g->id, 'label' => $g->nama_gps]));

        // ── Semua GPS per kendaraan (dari server, lengkap tidak ter-paginate) ─
        const GPS_PER_KENDARAAN = @json($gpsPerKendaraan);

        // ── Hitung jatuh tempo +1 tahun ──────────────────────────────────────
        function hitungJatuhTempo(tgl) {
            if (!tgl) return '';
            const d = new Date(tgl);
            d.setFullYear(d.getFullYear() + 1);
            return d.getFullYear() + '-'
                + String(d.getMonth() + 1).padStart(2, '0') + '-'
                + String(d.getDate()).padStart(2, '0');
        }

        // ── Saat tanggal bayar shared berubah → update display jatuh tempo ───
        function onSharedTglBayarChange() {
            const tgl = document.getElementById('shared_tanggal_bayar').value;
            const jt  = hitungJatuhTempo(tgl);
            document.getElementById('shared_jatuh_tempo_display').value = jt;
            document.getElementById('shared_tanggal_habis').value       = jt;
        }

        // ── GPS Items (hanya GPS + Type) ─────────────────────────────────────
        let gpsItemCount = 0;

        function tambahGpsItem(gpsId = '', type = '', biaya = '', namaBank = '', noRekening = '', namaPemilik = '') {
            const idx       = gpsItemCount++;
            const container = document.getElementById('gpsItemsContainer');
            const div       = document.createElement('div');
            div.id          = `gps-item-${idx}`;
            div.className   = 'border border-slate-200 rounded-xl overflow-hidden';

            let gOpts = GPS_LIST.map(g =>
                `<option value="${g.id}" ${g.id == gpsId ? 'selected' : ''}>${g.label}</option>`
            ).join('');

            div.innerHTML = `
                <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                    <span class="text-xs font-semibold text-slate-500 item-no-label">GPS #${gpsItemCount}</span>
                    <button type="button" onclick="hapusGpsItem(${idx})"
                        class="w-6 h-6 rounded-md bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 transition inline-flex items-center justify-center hapus-gps-btn">
                        <i class="fa-solid fa-times text-[10px]"></i>
                    </button>
                </div>
                <div class="px-4 py-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-[11px] font-semibold text-slate-500 mb-1 block">GPS <span class="text-red-400">*</span></label>
                        <select name="gps_items[${idx}][gps_id]" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            ${gOpts}
                        </select>
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold text-slate-500 mb-1 block">Type GPS <span class="text-red-400">*</span></label>
                        <input type="text" name="gps_items[${idx}][type]" required
                            placeholder="Contoh: GT06N"
                            value="${type}"
                            oninput="cekDuplikatType()"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none type-field">
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold text-slate-500 mb-1 block">Biaya Sewa <span class="text-red-400">*</span></label>
                        <input type="number" name="gps_items[${idx}][biaya_sewa]" required
                            placeholder="0" min="0" max="9999999999"
                            value="${biaya}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

               {{--      <div>
                        <label class="text-[11px] font-semibold text-slate-500 mb-1 block">Bukti Bayar</label>
                        <input type="file" name="gps_items[${idx}][bukti_bayar]"
                            accept="image/*,.pdf,.doc,.docx"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                     --}}

                    {{-- Divider info bank --}}
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-2 mt-1 mb-1">
                            <div class="h-px flex-1 bg-slate-100"></div>
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                                <i class="fa-solid fa-building-columns text-[9px]"></i> Info Bank Tujuan
                            </span>
                            <div class="h-px flex-1 bg-slate-100"></div>
                        </div>
                    </div>

                 <div>
    <label class="text-[11px] font-semibold text-slate-500 mb-1 block">
        Nama Bank
    </label>

    <select name="gps_items[${idx}][nama_bank]"
        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">

        <option value="">-- Pilih Bank --</option>

        <!-- ==================== BANK PERSERO ==================== -->
        <option value="Bank Rakyat Indonesia" ${namaBank === 'Bank Rakyat Indonesia' ? 'selected' : ''}>
            Bank Rakyat Indonesia (002)
        </option>
        <option value="Bank Mandiri" ${namaBank === 'Bank Mandiri' ? 'selected' : ''}>
            Bank Mandiri (008)
        </option>
        <option value="Bank Negara Indonesia" ${namaBank === 'Bank Negara Indonesia' ? 'selected' : ''}>
            Bank Negara Indonesia (009)
        </option>
        <option value="Bank Tabungan Negara" ${namaBank === 'Bank Tabungan Negara' ? 'selected' : ''}>
            Bank Tabungan Negara (200)
        </option>


        <!-- ==================== BANK SWASTA ==================== -->
        <option value="Bank Central Asia" ${namaBank === 'Bank Central Asia' ? 'selected' : ''}>
            Bank Central Asia (014)
        </option>
        <option value="Bank Danamon Indonesia" ${namaBank === 'Bank Danamon Indonesia' ? 'selected' : ''}>
            Bank Danamon Indonesia (011)
        </option>
        <option value="Bank Permata" ${namaBank === 'Bank Permata' ? 'selected' : ''}>
            Bank Permata (013)
        </option>
        <option value="Bank Maybank Indonesia" ${namaBank === 'Bank Maybank Indonesia' ? 'selected' : ''}>
            Bank Maybank Indonesia (016)
        </option>
        <option value="Bank Panin" ${namaBank === 'Bank Panin' ? 'selected' : ''}>
            Bank Panin (019)
        </option>
        <option value="Bank CIMB Niaga" ${namaBank === 'Bank CIMB Niaga' ? 'selected' : ''}>
            Bank CIMB Niaga (022)
        </option>
        <option value="Bank UOB Indonesia" ${namaBank === 'Bank UOB Indonesia' ? 'selected' : ''}>
            Bank UOB Indonesia (023)
        </option>
        <option value="Bank OCBC Indonesia" ${namaBank === 'Bank OCBC Indonesia' ? 'selected' : ''}>
            Bank OCBC Indonesia (028)
        </option>
        <option value="Bank DBS Indonesia" ${namaBank === 'Bank DBS Indonesia' ? 'selected' : ''}>
            Bank DBS Indonesia (046)
        </option>
        <option value="Bank Capital Indonesia" ${namaBank === 'Bank Capital Indonesia' ? 'selected' : ''}>
            Bank Capital Indonesia (054)
        </option>
        <option value="Bank Artha Graha Internasional" ${namaBank === 'Bank Artha Graha Internasional' ? 'selected' : ''}>
            Bank Artha Graha Internasional (037)
        </option>
        <option value="Bank Bumi Arta" ${namaBank === 'Bank Bumi Arta' ? 'selected' : ''}>
            Bank Bumi Arta (076)
        </option>
        <option value="Bank Ganesha" ${namaBank === 'Bank Ganesha' ? 'selected' : ''}>
            Bank Ganesha (161)
        </option>
        <option value="Bank Ina Perdana" ${namaBank === 'Bank Ina Perdana' ? 'selected' : ''}>
            Bank Ina Perdana (513)
        </option>
        <option value="Bank Jago" ${namaBank === 'Bank Jago' ? 'selected' : ''}>
            Bank Jago (542)
        </option>
        <option value="Bank Jasa Jakarta" ${namaBank === 'Bank Jasa Jakarta' ? 'selected' : ''}>
            Bank Jasa Jakarta (472)
        </option>
        <option value="Bank Maspion Indonesia" ${namaBank === 'Bank Maspion Indonesia' ? 'selected' : ''}>
            Bank Maspion Indonesia (157)
        </option>
        <option value="Bank Mayapada Internasional" ${namaBank === 'Bank Mayapada Internasional' ? 'selected' : ''}>
            Bank Mayapada Internasional (097)
        </option>
        <option value="Bank Mega" ${namaBank === 'Bank Mega' ? 'selected' : ''}>
            Bank Mega (426)
        </option>
        <option value="Bank MNC Internasional" ${namaBank === 'Bank MNC Internasional' ? 'selected' : ''}>
            Bank MNC Internasional (485)
        </option>
        <option value="Bank Nationalnobu" ${namaBank === 'Bank Nationalnobu' ? 'selected' : ''}>
            Bank Nationalnobu (503)
        </option>
        <option value="Bank Neo Commerce" ${namaBank === 'Bank Neo Commerce' ? 'selected' : ''}>
            Bank Neo Commerce (490)
        </option>
        <option value="Bank Oke Indonesia" ${namaBank === 'Bank Oke Indonesia' ? 'selected' : ''}>
            Bank Oke Indonesia (526)
        </option>
        <option value="Bank Sahabat Sampoerna" ${namaBank === 'Bank Sahabat Sampoerna' ? 'selected' : ''}>
            Bank Sahabat Sampoerna (523)
        </option>
        <option value="Bank Sinarmas" ${namaBank === 'Bank Sinarmas' ? 'selected' : ''}>
            Bank Sinarmas (153)
        </option>
        <option value="Bank Victoria International" ${namaBank === 'Bank Victoria International' ? 'selected' : ''}>
            Bank Victoria International (566)
        </option>


        <!-- ==================== BANK DIGITAL ==================== -->
        <option value="Bank Jago" ${namaBank === 'Bank Jago' ? 'selected' : ''}>
            Bank Jago (542)
        </option>
        <option value="SeaBank Indonesia" ${namaBank === 'SeaBank Indonesia' ? 'selected' : ''}>
            SeaBank Indonesia (535)
        </option>
        <option value="Bank Neo Commerce" ${namaBank === 'Bank Neo Commerce' ? 'selected' : ''}>
            Bank Neo Commerce (490)
        </option>
        <option value="Allo Bank Indonesia" ${namaBank === 'Allo Bank Indonesia' ? 'selected' : ''}>
            Allo Bank Indonesia (567)
        </option>
        <option value="Superbank Indonesia" ${namaBank === 'Superbank Indonesia' ? 'selected' : ''}>
            Superbank Indonesia (562)
        </option>
        <option value="Krom Bank Indonesia" ${namaBank === 'Krom Bank Indonesia' ? 'selected' : ''}>
            Krom Bank Indonesia (459)
        </option>
        <option value="Bank Raya Indonesia" ${namaBank === 'Bank Raya Indonesia' ? 'selected' : ''}>
            Bank Raya Indonesia (494)
        </option>


        <!-- ==================== BANK SYARIAH ==================== -->
        <option value="Bank Syariah Indonesia" ${namaBank === 'Bank Syariah Indonesia' ? 'selected' : ''}>
            Bank Syariah Indonesia (451)
        </option>
        <option value="Bank Muamalat Indonesia" ${namaBank === 'Bank Muamalat Indonesia' ? 'selected' : ''}>
            Bank Muamalat Indonesia (147)
        </option>
        <option value="Bank Mega Syariah" ${namaBank === 'Bank Mega Syariah' ? 'selected' : ''}>
            Bank Mega Syariah (506)
        </option>
        <option value="Bank BCA Syariah" ${namaBank === 'Bank BCA Syariah' ? 'selected' : ''}>
            Bank BCA Syariah (536)
        </option>
        <option value="Bank Panin Dubai Syariah" ${namaBank === 'Bank Panin Dubai Syariah' ? 'selected' : ''}>
            Bank Panin Dubai Syariah (517)
        </option>
        <option value="Bank BTPN Syariah" ${namaBank === 'Bank BTPN Syariah' ? 'selected' : ''}>
            Bank BTPN Syariah (547)
        </option>
        <option value="Bank Victoria Syariah" ${namaBank === 'Bank Victoria Syariah' ? 'selected' : ''}>
            Bank Victoria Syariah (405)
        </option>
        <option value="Bank Jabar Banten Syariah" ${namaBank === 'Bank Jabar Banten Syariah' ? 'selected' : ''}>
            Bank Jabar Banten Syariah (425)
        </option>


        <!-- ==================== BPD ==================== -->
        <option value="Bank DKI" ${namaBank === 'Bank DKI' ? 'selected' : ''}>
            Bank DKI (111)
        </option>
        <option value="Bank BJB" ${namaBank === 'Bank BJB' ? 'selected' : ''}>
            Bank BJB (110)
        </option>
        <option value="Bank Jateng" ${namaBank === 'Bank Jateng' ? 'selected' : ''}>
            Bank Jateng (113)
        </option>
        <option value="Bank Jatim" ${namaBank === 'Bank Jatim' ? 'selected' : ''}>
            Bank Jatim (114)
        </option>
        <option value="Bank BPD DIY" ${namaBank === 'Bank BPD DIY' ? 'selected' : ''}>
            Bank BPD DIY (112)
        </option>
        <option value="Bank BPD Bali" ${namaBank === 'Bank BPD Bali' ? 'selected' : ''}>
            Bank BPD Bali (129)
        </option>
        <option value="Bank Sumut" ${namaBank === 'Bank Sumut' ? 'selected' : ''}>
            Bank Sumut (117)
        </option>
        <option value="Bank Nagari" ${namaBank === 'Bank Nagari' ? 'selected' : ''}>
            Bank Nagari (118)
        </option>
        <option value="Bank Jambi" ${namaBank === 'Bank Jambi' ? 'selected' : ''}>
            Bank Jambi (115)
        </option>
        <option value="Bank Bengkulu" ${namaBank === 'Bank Bengkulu' ? 'selected' : ''}>
            Bank Bengkulu (133)
        </option>
        <option value="Bank Lampung" ${namaBank === 'Bank Lampung' ? 'selected' : ''}>
            Bank Lampung (121)
        </option>
        <option value="Bank Sumsel Babel" ${namaBank === 'Bank Sumsel Babel' ? 'selected' : ''}>
            Bank Sumsel Babel (120)
        </option>
        <option value="Bank Riau Kepri" ${namaBank === 'Bank Riau Kepri' ? 'selected' : ''}>
            Bank Riau Kepri (119)
        </option>
        <option value="Bank Kalbar" ${namaBank === 'Bank Kalbar' ? 'selected' : ''}>
            Bank Kalbar (123)
        </option>
        <option value="Bank Kalsel" ${namaBank === 'Bank Kalsel' ? 'selected' : ''}>
            Bank Kalsel (122)
        </option>
        <option value="Bank Kaltimtara" ${namaBank === 'Bank Kaltimtara' ? 'selected' : ''}>
            Bank Kaltimtara (124)
        </option>
        <option value="Bank Kalteng" ${namaBank === 'Bank Kalteng' ? 'selected' : ''}>
            Bank Kalteng (125)
        </option>
        <option value="Bank Sulselbar" ${namaBank === 'Bank Sulselbar' ? 'selected' : ''}>
            Bank Sulselbar (126)
        </option>
        <option value="Bank SulutGo" ${namaBank === 'Bank SulutGo' ? 'selected' : ''}>
            Bank SulutGo (127)
        </option>
        <option value="Bank Sulteng" ${namaBank === 'Bank Sulteng' ? 'selected' : ''}>
            Bank Sulteng (134)
        </option>
        <option value="Bank Sultra" ${namaBank === 'Bank Sultra' ? 'selected' : ''}>
            Bank Sultra (135)
        </option>
        <option value="Bank Maluku Malut" ${namaBank === 'Bank Maluku Malut' ? 'selected' : ''}>
            Bank Maluku Malut (131)
        </option>
        <option value="Bank Papua" ${namaBank === 'Bank Papua' ? 'selected' : ''}>
            Bank Papua (132)
        </option>
        <option value="Bank NTT" ${namaBank === 'Bank NTT' ? 'selected' : ''}>
            Bank NTT (130)
        </option>
        <option value="Bank Banten" ${namaBank === 'Bank Banten' ? 'selected' : ''}>
            Bank Banten (137)
        </option>


        <!-- ==================== BANK ASING ==================== -->
        <option value="HSBC Indonesia" ${namaBank === 'HSBC Indonesia' ? 'selected' : ''}>
            HSBC Indonesia (087)
        </option>
        <option value="Standard Chartered Bank" ${namaBank === 'Standard Chartered Bank' ? 'selected' : ''}>
            Standard Chartered Bank (050)
        </option>
        <option value="Bank of China Indonesia" ${namaBank === 'Bank of China Indonesia' ? 'selected' : ''}>
            Bank of China Indonesia (069)
        </option>
        <option value="MUFG Bank" ${namaBank === 'MUFG Bank' ? 'selected' : ''}>
            MUFG Bank (042)
        </option>
        <option value="Citibank" ${namaBank === 'Citibank' ? 'selected' : ''}>
            Citibank (031)
        </option>
        <option value="J.P. Morgan Chase Bank" ${namaBank === 'J.P. Morgan Chase Bank' ? 'selected' : ''}>
            J.P. Morgan Chase Bank (032)
        </option>
        <option value="Bank of America" ${namaBank === 'Bank of America' ? 'selected' : ''}>
            Bank of America (033)
        </option>
        <option value="Deutsche Bank" ${namaBank === 'Deutsche Bank' ? 'selected' : ''}>
            Deutsche Bank (067)
        </option>
        <option value="Bangkok Bank" ${namaBank === 'Bangkok Bank' ? 'selected' : ''}>
            Bangkok Bank (040)
        </option>

    </select>
</div>

                    <div>
                        <label class="text-[11px] font-semibold text-slate-500 mb-1 block">No. Rekening atau Virtual Account</label>
                        <input type="text" name="gps_items[${idx}][no_rekening]"
                            placeholder="Contoh: 1234567890"
                            value="${noRekening}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold text-slate-500 mb-1 block">Nama Pemilik Rekening</label>
                        <input type="text" name="gps_items[${idx}][nama_pemilik]"
                            placeholder="Nama sesuai rekening"
                            value="${namaPemilik}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[11px] font-semibold text-slate-500 mb-1 block">Attachment <span class="text-red-500">*</span></label>
                        <input type="file" name="gps_items[${idx}][lampiran][]" multiple required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                        <p class="text-[10px] text-red-500 mt-0.5">Wajib upload minimal 1 lampiran</p>
                    </div>
                </div>
            `;

            container.appendChild(div);
            updateGpsItemNumbers();
            cekDuplikatType();
        }

        function hapusGpsItem(idx) {
            const el = document.getElementById(`gps-item-${idx}`);
            if (el) el.remove();
            updateGpsItemNumbers();
            cekDuplikatType();
        }

        function updateGpsItemNumbers() {
            const items = document.querySelectorAll('#gpsItemsContainer > div');
            items.forEach((div, i) => {
                const label = div.querySelector('.item-no-label');
                if (label) label.textContent = `GPS #${i + 1}`;
                const btn = div.querySelector('.hapus-gps-btn');
                if (btn) btn.style.visibility = items.length <= 1 ? 'hidden' : 'visible';
            });
        }

        // ── Cek duplikat type (live) ──────────────────────────────────────────
        function cekDuplikatType() {
            const fields = document.querySelectorAll('#gpsItemsContainer .type-field');
            const seen   = {};
            let dupMsg   = '';

            fields.forEach((input, i) => {
                const val = input.value.trim().toLowerCase();
                input.classList.remove('border-red-400', 'bg-red-50');
                if (!val) return;

                if (seen[val] !== undefined) {
                    dupMsg = `Baris ${seen[val] + 1} dan baris ${i + 1} memiliki type GPS yang sama ("${input.value.trim()}"). Type tidak boleh duplikat.`;
                    input.classList.add('border-red-400', 'bg-red-50');
                } else {
                    seen[val] = i;
                }
            });

            const warn    = document.getElementById('duplikatWarning');
            const warnTxt = document.getElementById('duplikatText');
            const btn     = document.getElementById('btnSimpanGps');

            if (dupMsg) {
                warn.classList.remove('hidden');
                warnTxt.textContent = dupMsg;
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                warn.classList.add('hidden');
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // ── Modal open / close ───────────────────────────────────────────────
        const modal = document.getElementById('modal');

        window.openModal = function () {
            // reset shared fields
            document.getElementById('form').reset();
            document.getElementById('shared_jatuh_tempo_display').value = '';
            document.getElementById('shared_tanggal_habis').value       = '';
            // reset gps items
            document.getElementById('gpsItemsContainer').innerHTML = '';
            gpsItemCount = 0;
            document.getElementById('duplikatWarning').classList.add('hidden');
            // tambah 1 baris gps default
            tambahGpsItem();
            modal.classList.remove('hidden');
        };

        window.closeModal = function () {
            modal.classList.add('hidden');
        };

        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });

        // ── Anti double-submit ───────────────────────────────────────────────
        document.getElementById('form').addEventListener('submit', function () {
            const btn = document.getElementById('btnSimpanGps');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
            btn.classList.add('opacity-60', 'cursor-not-allowed');
        });

        // ── Preview & hapus bukti bayar sudah dipindah ke per-baris GPS ─────

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

    <script>
        // ── Modal Perpanjang ─────────────────────────────────────────────────
        const modalPerpanjang = document.getElementById('modalPerpanjang');
        const formPerpanjang  = document.getElementById('formPerpanjang');

        function show(el) { el.classList.remove('hidden'); }
        function hide(el) { el.classList.add('hidden'); }

        window.closeModalPerpanjang = function () {
            hide(modalPerpanjang);
            document.getElementById('perp_gps_list').innerHTML = '';
        };

        modalPerpanjang?.addEventListener('click', function (e) {
            if (e.target === modalPerpanjang) closeModalPerpanjang();
        });

        // ── Format tanggal Y-m-d dari Date object ────────────────────────────
        function fmtDate(d) {
            return d.getFullYear() + '-'
                + String(d.getMonth() + 1).padStart(2, '0') + '-'
                + String(d.getDate()).padStart(2, '0');
        }

        // ── Format tanggal readable ──────────────────────────────────────────
        function fmtReadable(ymd) {
            if (!ymd) return '-';
            const [y, m, d] = ymd.split('-');
            const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            return `${parseInt(d)} ${bulan[parseInt(m)-1]} ${y}`;
        }

        // ── Buka modal perpanjang dari tombol ────────────────────────────────
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-perpanjang');
            if (!btn) return;

            const gpsId          = btn.dataset.id;
            const kendaraanId    = btn.dataset.kendaraanId;
            const merk           = btn.dataset.merk;
            const nopol          = btn.dataset.nopol;
            const namaGps        = btn.dataset.namaGps;
            const type           = btn.dataset.type;
            const statusGps      = btn.dataset.statusGps;
            const biaya          = btn.dataset.biaya;
            const tanggalHabis   = btn.dataset.tanggalHabis;
            const tanggalHabisBaru = btn.dataset.tanggalHabisBaru;
            const tanggalBayarLama = btn.dataset.tanggalBayar;

            // Action ke route perpanjang per-ID
            formPerpanjang.action = `/admin/gps-kendaraan/${gpsId}/perpanjang`;

            // Label kendaraan
            document.getElementById('perp_kendaraan_label').textContent = `${merk} — ${nopol}`;

            // Tanggal bayar default = tanggal_bayar_lama + 1 tahun, min = nilai tersebut
            const tglBayarInput = document.getElementById('perp_tanggal_bayar');
            if (tanggalBayarLama) {
                const d = new Date(tanggalBayarLama);
                d.setFullYear(d.getFullYear() + 1);
                const val = d.getFullYear() + '-'
                    + String(d.getMonth() + 1).padStart(2, '0') + '-'
                    + String(d.getDate()).padStart(2, '0');
                tglBayarInput.value = val;
                tglBayarInput.min   = val;
            } else {
                const today = new Date().toISOString().split('T')[0];
                tglBayarInput.value = today;
                tglBayarInput.min   = today;
            }

            // Render 1 card GPS ini saja
            const list = document.getElementById('perp_gps_list');
            list.innerHTML = '';

            const statusBadge = statusGps === 'aktif'
                ? `<span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-semibold">Aktif</span>`
                : `<span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-semibold">Nonaktif</span>`;

            const card = document.createElement('div');
            card.className = 'border border-slate-200 rounded-xl overflow-hidden';
            card.innerHTML = `
                <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold flex items-center justify-center">1</span>
                        <span class="text-sm font-semibold text-slate-700">${namaGps}</span>
                        <span class="font-mono bg-slate-200 text-slate-600 text-[11px] px-1.5 py-0.5 rounded">${type}</span>
                        ${statusBadge}
                    </div>
                    <div class="text-xs text-slate-400">
                        <span class="text-slate-500">${fmtReadable(tanggalHabis)}</span>
                        <i class="fa-solid fa-arrow-right mx-1"></i>
                        <span class="font-semibold text-indigo-600">${fmtReadable(tanggalHabisBaru)}</span>
                    </div>
                </div>
                <div class="px-4 py-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="hidden" name="gps_items[0][gps_kendaraan_id]" value="${gpsId}">

                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Biaya Sewa <span class="text-red-500">*</span></label>
                        <input type="number" name="biaya_sewa" id="perp_biaya_sewa" required min="0" max="9999999999"
                            value="${biaya}" placeholder="0"
                            data-gps-id="${gpsId}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <p class="text-[10px] text-slate-400 mt-0.5">Perubahan harga otomatis tersimpan ke data GPS.</p>
                    </div>
                </div>
            `;
            list.appendChild(card);

            // Auto-update biaya_sewa di GPS record saat input berubah
            const biayaInput = document.getElementById('perp_biaya_sewa');
            let biayaTimer = null;
            biayaInput.addEventListener('input', function () {
                clearTimeout(biayaTimer);
                const newBiaya = parseInt(this.value) || 0;
                const inputEl  = this;
                biayaTimer = setTimeout(function () {
                    fetch(`/admin/gps-kendaraan/${gpsId}/update-biaya`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ biaya_sewa: newBiaya }),
                    })
                    .then(r => r.json())
                    .then(function(res) {
                        if (res.success) {
                            inputEl.classList.add('border-green-400');
                            inputEl.classList.remove('border-slate-300', 'border-red-400');
                            setTimeout(() => {
                                inputEl.classList.remove('border-green-400');
                                inputEl.classList.add('border-slate-300');
                            }, 1500);
                        }
                    })
                    .catch(function() {
                        inputEl.classList.add('border-red-400');
                        inputEl.classList.remove('border-slate-300', 'border-green-400');
                    });
                }, 600); // debounce 600ms
            });

            show(modalPerpanjang);
        });

        // ── Alert ────────────────────────────────────────────────────────────
        window.closeAlert = function () {
            const overlay = document.getElementById('alertOverlay');
            if (overlay) overlay.style.display = 'none';
        };

        (function () {
            const overlay = document.getElementById('alertOverlay');
            if (!overlay) return;
            overlay.style.pointerEvents = 'auto';
            overlay.style.opacity       = '1';
            const box = document.getElementById('alertBox');
            if (box) box.style.transform = 'translateY(0)';
            setTimeout(closeAlert, 4000);
        })();

        // ── Anti double-submit perpanjang ────────────────────────────────────
        (function () {
            const form = document.getElementById('formPerpanjang');
            const btn  = document.getElementById('btnPerpanjangGps');
            if (!form || !btn) return;
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
                btn.classList.add('opacity-60', 'cursor-not-allowed');
            });
        })();

        window.renderListAttachment = function (input, listId) {
            const list = document.getElementById(listId);
            if (!list) return;
            list.innerHTML = '';
            Array.from(input.files).forEach(file => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-1.5';
                li.innerHTML = `<i class="fa-solid fa-paperclip text-slate-400"></i> ${file.name}`;
                list.appendChild(li);
            });
        };

        // ── CHART GPS KENDARAAN ──────────────────────────────────────────────
        const chartManager = new ChartManager();

        document.addEventListener('DOMContentLoaded', function () {
            // No direct init — chart is initialized via chartFilterChange listener
            document.addEventListener('chartFilterChange', function (e) {
                if (e.detail.filterId === 'gpsChartFilter') {
                    const filters = {
                        filter_type: e.detail.filterType,
                        start_date: e.detail.startDate,
                        end_date: e.detail.endDate,
                    };
                    if (!chartManager.hasChart('gpsBarChart')) {
                        initGpsCharts(filters);
                    } else {
                        updateGpsCharts(filters);
                    }
                }
            });
        });

        async function initGpsCharts(filters) {
            try {
                await chartManager.initChartsFromAPI('gps-kendaraan', {
                    pie: 'gpsPieChart',
                    bar: 'gpsBarChart',
                    line: 'gpsLineChart'
                }, filters, { accentLine: true });
            } catch (error) {
                console.error('Error loading GPS charts:', error);
            }
        }

        async function updateGpsCharts(filters) {
            try {
                const isScrollable = filters.filter_type === 'custom';
                const barOptions  = { scrollable: isScrollable, accentLine: true };
                const lineOptions = { scrollable: isScrollable };
                await chartManager.updateChartsFromAPI('gps-kendaraan', {
                    pie: 'gpsPieChart',
                    bar: 'gpsBarChart',
                    line: 'gpsLineChart'
                }, filters, barOptions, lineOptions);
            } catch (error) {
                console.error('Error updating GPS charts:', error);
            }
        }

        // ── EXPAND ROW GPS (deprecated) ─────────────────────────────────────
        function toggleGpsRow(id, rowEl) { /* replaced by openDetailModal */ }

        // ── AJUKAN ULANG MODAL ───────────────────────────────────────────────
        function openAjukanUlangModal(gpsId, nopol, merk, namaGps, type) {
            const modal = document.getElementById('modalAjukanUlang');
            const form  = document.getElementById('formAjukanUlang');

            // Set subtitle
            document.getElementById('ajukanUlangSubtitle').textContent =
                nopol + ' — ' + merk + ' | ' + namaGps + ' (' + type + ')';

            // Set form action
            form.action = '/admin/gps-kendaraan/' + gpsId + '/ajukan-ulang';

            // Load alasan penolakan dari keterangan GPS (via detail API)
            document.getElementById('ajukanUlangAlasan').textContent = 'Memuat...';
            document.getElementById('ajukanUlangLampiranLama').innerHTML = '<span class="italic">Memuat...</span>';

            fetch('/admin/gps-kendaraan/' + gpsId + '/detail', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(function(data) {
                if (!data.success) return;
                const rec = data.record;

                // Alasan ditolak
                document.getElementById('ajukanUlangAlasan').textContent =
                    rec.keterangan || 'Tidak ada catatan';

                // Lampiran yang sudah ada
                const container = document.getElementById('ajukanUlangLampiranLama');
                // Lampiran dari histories / attachments sudah tidak dikembalikan via detail API
                // Gunakan AJAX ke endpoint attachments
                fetch('/admin/gps-kendaraan/' + gpsId + '/attachments', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(function(attData) {
                    if (!attData.success || !attData.attachments.length) {
                        container.innerHTML = '<span class="italic text-gray-400">Belum ada lampiran</span>';
                        return;
                    }
                    container.innerHTML = attData.attachments.map(function(att) {
                        const ext = (att.file_type || '').toLowerCase();
                        const isImg = ['jpg','jpeg','png','gif','webp'].includes(ext);
                        const icon = isImg ? 'fa-image text-blue-400' : (ext === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-paperclip text-gray-400');
                        return '<a href="' + att.url + '" target="_blank"'
                            + ' class="flex items-center gap-1.5 py-1 text-gray-600 hover:text-blue-600">'
                            + '<i class="fa-solid ' + icon + ' text-[10px]"></i>'
                            + '<span class="truncate max-w-[280px]">' + att.file_name + '</span>'
                            + '</a>';
                    }).join('');
                })
                .catch(function() {
                    container.innerHTML = '<span class="italic text-gray-400">Gagal memuat lampiran</span>';
                });
            })
            .catch(function() {
                document.getElementById('ajukanUlangAlasan').textContent = 'Gagal memuat data';
            });

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAjukanUlangModal() {
            const modal = document.getElementById('modalAjukanUlang');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('formAjukanUlang').reset();
        }

        document.getElementById('modalAjukanUlang')?.addEventListener('click', function(e) {
            if (e.target === this) closeAjukanUlangModal();
        });

        // ── AJUKAN ULANG PERPANJANG MODAL ────────────────────────────────────
        function openAjukanUlangPerpanjangModal(pembayaranId, nopol, merk, namaGps, type) {
            const modal = document.getElementById('modalAjukanUlangPerpanjang');
            const form  = document.getElementById('formAjukanUlangPerpanjang');

            document.getElementById('ajukanUlangPerpanjangSubtitle').textContent =
                nopol + ' — ' + merk + ' | ' + namaGps + ' (' + type + ') — Perpanjangan';

            form.action = '/admin/gps-kendaraan/perpanjang/' + pembayaranId + '/ajukan-ulang';

            // Load alasan penolakan dari API pembayaran
            document.getElementById('ajukanUlangPerpanjangAlasan').textContent = 'Memuat...';

            fetch('/admin/pembayaran/' + pembayaranId + '/approval-modal', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(function(data) {
                if (!data.success) return;
                const approvals = data.data?.pembayaran?.approvals ?? [];
                const lastReject = approvals.find(a => a.action === 'rejected');
                document.getElementById('ajukanUlangPerpanjangAlasan').textContent =
                    lastReject?.catatan || data.data?.pembayaran?.catatan || 'Tidak ada catatan';
            })
            .catch(function() {
                document.getElementById('ajukanUlangPerpanjangAlasan').textContent = 'Gagal memuat data';
            });

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAjukanUlangPerpanjangModal() {
            const modal = document.getElementById('modalAjukanUlangPerpanjang');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('formAjukanUlangPerpanjang').reset();
        }

        document.getElementById('modalAjukanUlangPerpanjang')?.addEventListener('click', function(e) {
            if (e.target === this) closeAjukanUlangPerpanjangModal();
        });
    </script>

@include('admin.partials.detail-modal')

{{-- MODAL: AJUKAN ULANG GPS (dari Pembayaran Ditolak) --}}
<div id="modalAjukanUlang" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Ajukan Ulang GPS</h3>
                <p class="text-sm text-gray-500 mt-0.5" id="ajukanUlangSubtitle">–</p>
            </div>
            <button onclick="closeAjukanUlangModal()"
                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formAjukanUlang" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            @csrf
            <div class="px-6 py-4 space-y-4">

                {{-- Info penolakan --}}
                <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm">
                    <p class="text-xs font-semibold text-red-600 mb-1"><i class="fa-solid fa-circle-xmark mr-1"></i> Alasan Penolakan Sebelumnya</p>
                    <p class="text-red-700" id="ajukanUlangAlasan">–</p>
                </div>

                {{-- Lampiran yang sudah ada --}}
                <div>
                    <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">
                        <i class="fa-solid fa-paperclip mr-1 text-gray-400"></i> Lampiran Saat Ini
                    </p>
                    <div id="ajukanUlangLampiranLama" class="space-y-1.5 text-xs text-gray-500">
                        <span class="italic">Memuat...</span>
                    </div>
                </div>

                {{-- Upload lampiran baru --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        <i class="fa-solid fa-plus mr-1 text-amber-500"></i> Tambah Lampiran Baru
                        <span class="text-gray-400 font-normal">(opsional, akan ditambahkan)</span>
                    </label>
                    <input type="file" name="lampiran[]" multiple
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600
                            file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                            file:text-xs file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    <p class="text-[11px] text-gray-400 mt-1">Lampiran lama tetap dipertahankan. File baru ditambahkan di atasnya.</p>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea name="catatan" rows="2"
                        placeholder="Tambahkan keterangan jika diperlukan..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-amber-100 focus:border-amber-400"></textarea>
                </div>
            </div>

            <div class="border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
                <button type="button" onclick="closeAjukanUlangModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl py-2.5 transition">
                    <i class="fa-solid fa-paper-plane"></i> Ajukan Ulang
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: AJUKAN ULANG PERPANJANG GPS (dari Pembayaran Ditolak) --}}
<div id="modalAjukanUlangPerpanjang" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Ajukan Ulang Perpanjangan GPS</h3>
                <p class="text-sm text-gray-500 mt-0.5" id="ajukanUlangPerpanjangSubtitle">–</p>
            </div>
            <button onclick="closeAjukanUlangPerpanjangModal()"
                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formAjukanUlangPerpanjang" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            @csrf
            <div class="px-6 py-4 space-y-4">
                <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm">
                    <p class="text-xs font-semibold text-red-600 mb-1"><i class="fa-solid fa-circle-xmark mr-1"></i> Alasan Penolakan Sebelumnya</p>
                    <p class="text-red-700" id="ajukanUlangPerpanjangAlasan">–</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        <i class="fa-solid fa-plus mr-1 text-amber-500"></i> Tambah Lampiran Baru
                        <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <input type="file" name="lampiran[]" multiple
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600
                            file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                            file:text-xs file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea name="catatan" rows="2"
                        placeholder="Tambahkan keterangan jika diperlukan..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-amber-100 focus:border-amber-400"></textarea>
                </div>
            </div>

            <div class="border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
                <button type="button" onclick="closeAjukanUlangPerpanjangModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl py-2.5 transition">
                    <i class="fa-solid fa-paper-plane"></i> Ajukan Ulang
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
