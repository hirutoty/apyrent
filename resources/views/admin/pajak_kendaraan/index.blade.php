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

        {{-- CHART FILTER --}}
        <x-chart-filter id="pajakChartFilter" defaultFilter="year" :showCustomRange="true" />

        {{-- CHART CONTAINER --}}
        <x-chart-container
            id="pajakChartContainer"
            layout="stacked"
            pieTitle="Distribusi Status Pajak" pieId="pajakPieChart"
            barTitle="Nominal Pajak per Bulan" barId="pajakBarChart"
            lineTitle="Trend Nominal Pajak" lineId="pajakLineChart"
            :showStats="true" :statsData="[]"
        />

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Pajak Kendaraan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $data->total() }} total data pajak</p>
                </div>

                <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap items-center gap-2">

                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" placeholder="Cari nopol, merk, jenis pajak..."
                            value="{{ request('search') }}"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-64">
                    </div>

                    {{-- Filter Status --}}
                    @if(request('hari')) <input type="hidden" name="hari" value="{{ request('hari') }}"> @endif
                    @if(request('bulan')) <input type="hidden" name="bulan" value="{{ request('bulan') }}"> @endif
                    @if(request('tahun')) <input type="hidden" name="tahun" value="{{ request('tahun') }}"> @endif

                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fa fa-search text-xs"></i> Cari
                    </button>

                    {{-- Status filter buttons --}}
                    <div class="flex items-center gap-1 border border-gray-200 rounded-lg p-0.5 bg-gray-50">
                        @foreach([''=>'Semua','sudah_bayar'=>'Lunas','belum_bayar'=>'Belum'] as $val => $label)
                            <a href="{{ request()->fullUrlWithQuery(['status' => $val, 'page' => 1]) }}"
                               class="px-3 py-1 text-xs font-medium rounded-md transition-colors
                                   {{ request('status', '') === $val
                                       ? ($val === 'sudah_bayar' ? 'bg-white text-green-700 shadow-sm border border-gray-200' : ($val === 'belum_bayar' ? 'bg-white text-red-600 shadow-sm border border-gray-200' : 'bg-white text-gray-700 shadow-sm border border-gray-200'))
                                       : 'text-gray-500 hover:text-gray-700' }}">
                                @if($val === 'sudah_bayar') <i class="fa fa-check text-[10px]"></i> @endif
                                @if($val === 'belum_bayar') <i class="fa fa-times text-[10px]"></i> @endif
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <button onclick="exportPdf()" type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                        <i class="fa fa-download text-xs"></i> Export
                    </button>

                    @if(request()->hasAny(['search','status','hari','bulan','tahun']))
                        <a href="{{ request()->url() }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fa fa-rotate-left text-xs"></i> Reset
                        </a>
                    @endif

                </form>
            </div>

            {{-- FILTER BAR: Hari, Bulan & Tahun --}}
            <form method="GET" action="{{ request()->url() }}" id="formFilterPajak"
                  class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">

                {{-- Preserve search & status --}}
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                {{-- Filter Hari --}}
                <div class="flex items-center gap-2">
                    <i class="fa fa-calendar-day text-gray-400"></i>
                    <select name="hari" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">Semua Hari</option>
                        @for ($d = 1; $d <= 31; $d++)
                            <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}" {{ request('hari') == str_pad($d, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>

                <div class="w-px h-4 bg-gray-200"></div>

                {{-- Filter Bulan --}}
                <div class="flex items-center gap-2">
                    <i class="fa fa-calendar text-gray-400"></i>
                    <select name="bulan" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">Semua Bulan</option>
                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $nama)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tahun --}}
                <div class="flex items-center gap-2">
                    <select name="tahun" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">Semua Tahun</option>
                        @foreach(range(date('Y') + 1, date('Y') - 3) as $yr)
                            <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset filter --}}
                <a href="{{ request()->url() }}"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-blue-50/50 transition-colors">
                    <i class="fa fa-rotate-left text-[10px]"></i> Reset
                </a>

            </form>

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
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl Dibuat</th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl Ketentuan
                                    Bayar</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Jatuh Tempo</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Bukti</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Lampiran</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Keterangan</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Persetujuan</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>                        </tr>
                    </thead>
                    <tbody id="pajakTableBody">
                        @forelse($data as $item)
                            @php
                                $today       = \Carbon\Carbon::now();
                                $jatuhTempo  = \Carbon\Carbon::parse($item->jatuh_tempo);
                                $selisihHari = (int) $today->startOfDay()->diffInDays($jatuhTempo->startOfDay(), false);
                                $sisaDetikPajak = (int) ($jatuhTempo->endOfDay()->timestamp - now()->timestamp);
                            @endphp
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100">

                                <td class="px-4 py-3.5 text-sm text-gray-500">{{ $data->firstItem() + $loop->index }}</td>

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

                                <td class="px-4 py-3.5 text-xs text-gray-600">
                                    {{ $item->tanggal_buat ? \Carbon\Carbon::parse($item->tanggal_buat)->translatedFormat('j F Y') : '-' }}
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-500">
                                    {{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->translatedFormat('j F') : '-' }}
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex flex-col gap-1">

                                        <span class="text-sm text-gray-700">
                                            {{ \Carbon\Carbon::parse($item->jatuh_tempo)->translatedFormat('j F Y') }}
                                        </span>

                                        @if ($selisihHari < 0)
                                            <span class="inline-flex items-center gap-1 text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full w-fit font-semibold">
                                                <i class="fa fa-circle-exclamation text-[10px]"></i>
                                                Terlambat {{ formatSisaWaktu(abs($sisaDetikPajak)) }}
                                            </span>
                                        @elseif ($selisihHari <= $reminder)
                                            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full w-fit font-semibold
                                                {{ $selisihHari == 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                <i class="fa fa-triangle-exclamation text-[10px]"></i>
                                                @if ($selisihHari == 0)
                                                    Jatuh tempo {{ formatSisaWaktu($sisaDetikPajak) }} lagi
                                                @elseif ($selisihHari == 1)
                                                    Jatuh tempo besok
                                                @else
                                                    Jatuh tempo {{ $selisihHari }} hari lagi
                                                @endif
                                            </span>
                                        @endif

                                    </div>
                                </td>

                                

                                <td class="px-4 py-3.5">
                                    @if ($item->status_aktif === 'aktif')
                                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <i class="fa fa-circle-check text-[10px]"></i> Aktif
                                        </span>
                                    @elseif ($item->status_aktif === 'expired')
                                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <i class="fa fa-circle-xmark text-[10px]"></i> Expired
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <i class="fa fa-circle text-[10px]"></i> Tidak Aktif
                                        </span>
                                    @endif
                                    
                                </td>
                                <td class="px-4 py-3.5">
                                    @if ($item->bukti)
                                        @php
                                            $pajakBuktiName = preg_replace('/^\d+_/', '', basename($item->bukti));
                                        @endphp
                                        <a href="{{ asset($item->bukti) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800 block truncate max-w-[140px]"
                                            title="{{ $pajakBuktiName }}">
                                            {{ $pajakBuktiName }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5">
                                    @if($item->attachments->isNotEmpty())
                                        @php
                                            $pajakAtts = $item->attachments->map(fn($a) => ['path' => asset($a->file_path), 'name' => $a->file_name])->values()->toArray();
                                        @endphp
                                      
                                        <div class="mt-1 flex flex-col gap-0.5">
                                            @foreach ($item->attachments as $att)
                                                <form action="{{ route('pajak.attachment.destroy', $att->id) }}"
                                                    method="POST" onsubmit="return confirm('Hapus lampiran ini?')"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-400 hover:text-red-600 text-[10px]">
                                                        <i class="fa fa-times"></i> {{ $att->file_name }}
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[140px] truncate">
                                    {{ $item->keterangan ?? '-' }}</td>

                                <td class="px-4 py-3.5 text-center">
                                    @if($item->persetujuan === 'Disetujui')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui
                                        </span>
                                    @elseif($item->persetujuan === 'Diajukan ke Pembayaran')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            <i class="fa-solid fa-paper-plane text-[10px]"></i> Diajukan ke Pembayaran
                                        </span>
                                    @elseif($item->persetujuan === 'Ditolak')
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 w-fit mx-auto">
                                                <i class="fa-solid fa-circle-xmark text-[10px]"></i> Ditolak di Pembayaran
                                            </span>
                                        </div>
                                    @elseif($item->persetujuan === 'Pending')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            <i class="fa-solid fa-clock text-[10px]"></i> Pending
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">

                                        {{-- Ajukan Ulang: tampil jika persetujuan Ditolak --}}
                                        @if($item->persetujuan === 'Ditolak' && $item->pembayaran_id)
                                        <button type="button"
                                            onclick="openPajakResubmitModal({{ $item->id }})"
                                            id="row-pajak-{{ $item->id }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-amber-100 text-amber-700 hover:bg-amber-200 transition-colors">
                                            <i class="fa fa-rotate-right text-xs"></i>
                                            Ajukan Ulang
                                        </button>
                                        @endif

                                        {{-- Perpanjangan: hanya tampil jika sudah dalam batas reminder --}}
                                        @if ($selisihHari <= $reminder)
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
                '{{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('Y-m-d') : '' }}',
                '{{ $item->status }}',
                '{{ addslashes($item->keterangan) }}',
                '{{ $item->bukti }}',
                '{{ addslashes($item->nama_pemilik ?? '') }}',
                '{{ addslashes($item->nama_bank ?? '') }}',
                '{{ addslashes($item->no_rekening ?? '') }}'
            )">
                                            <i class="fa fa-rotate-right text-xs"></i>
                                            Perpanjang
                                        </button>
                                        @endif

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
                '',
                '{{ addslashes($item->nama_pemilik ?? '') }}',
                '{{ addslashes($item->nama_bank ?? '') }}',
                '{{ addslashes($item->no_rekening ?? '') }}'
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

                                        {{-- Detail --}}
                                        <button type="button"
                                            onclick="openDetailModal('pajak', {{ $item->id }}); event.stopPropagation()"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                            <i class="fa fa-eye text-xs"></i>
                                            Detail
                                        </button>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-5 py-12 text-center">
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
                <div class="py-3 border-t border-gray-100">
                    <x-pagination :paginator="$data" />
                </div>
            </div>

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
                    <p class="text-xs text-gray-500 mt-0.5">Pengajuan akan masuk ke antrian approval Superadmin</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('pajak.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5">
                @csrf
                <input type="hidden" name="_modal_open" value="tambah">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span
                                class="text-red-500">*</span></label>
                        <select name="kendaraan_id" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach ($kendaraan as $k)
                                <option value="{{ $k->id }}" {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>{{ $k->nopol }} - {{ $k->merk }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pajak <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jenis_pajak" required placeholder="Contoh: PKB, BBNKB"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('jenis_pajak') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal <span
                                class="text-red-500">*</span></label>
                        <input type="number" min="0" max="9999999999" name="nominal" required placeholder="Nominal pajak"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('nominal') }}">
                    </div>

                    

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tgl Ketentuan Bayar <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_bayar" id="tambah_tanggal_bayar" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('tanggal_bayar') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Jatuh Tempo <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="jatuh_tempo" id="tambah_jatuh_tempo" readonly
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed focus:outline-none" value="{{ old('jatuh_tempo') }}">
                        <p class="text-xs text-gray-400 mt-1">Otomatis tanggal bayar + 1 tahun</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pemilik</label>
                        <input type="text" name="nama_pemilik" placeholder="Nama pemilik rekening"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('nama_pemilik') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Bank</label>
                        <input type="text" name="nama_bank" placeholder="Contoh: BRI, BCA, Mandiri"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('nama_bank') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Rekening</label>
                        <input type="text" name="no_rekening" placeholder="Nomor rekening tujuan"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('no_rekening') }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
                        <textarea name="keterangan" rows="3" placeholder="Tambahkan keterangan..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Lampiran <span class="text-red-500">*</span>
                        </label>
                        <label for="bukti_attachment"
                            class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <i class="fa-solid fa-paperclip text-xl text-gray-400 mb-1"></i>
                            <span class="text-xs text-gray-500">Klik untuk upload lampiran</span>
                            <span class="text-xs text-gray-400">(Maks 5MB per file)</span>
                        </label>
                        <input type="file" name="bukti_attachment[]" id="bukti_attachment" class="hidden" multiple required
                            onchange="renderListAttachment(this, 'listAttachmentTambah')">
                        <ul id="listAttachmentTambah" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                            <i class="fa fa-circle-exclamation text-[10px]"></i>
                            Wajib upload minimal 1 lampiran. Bukti bayar diunggah saat Superadmin approve.
                        </p>
                    </div>

                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModalTambah()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                        <i class="fa fa-paper-plane text-sm"></i> Kirim Pengajuan
                    </button>
                </div>
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

            <form id="formEdit" method="POST" class="px-6 py-5">
                @csrf
                @method('PUT')
                {{-- Hidden fields — nilai tidak berubah, dikirim agar controller tidak error --}}
                <input type="hidden" name="kendaraan_id"  id="edit_kendaraan_id">
                <input type="hidden" name="jenis_pajak"   id="edit_jenis_pajak">
                <input type="hidden" name="jatuh_tempo"   id="edit_jatuh_tempo">
                <input type="hidden" name="tanggal_bayar" id="edit_tanggal_bayar">
                <input type="hidden" name="status"        id="edit_status">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal <span class="text-red-500">*</span></label>
                        <input type="number" min="0" name="nominal" id="edit_nominal" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pemilik Rekening</label>
                        <input type="text" name="nama_pemilik" id="edit_nama_pemilik"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Bank</label>
                        <input type="text" name="nama_bank" id="edit_nama_bank"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Rekening</label>
                        <input type="text" name="no_rekening" id="edit_no_rekening"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" rows="3"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModalEdit()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                        <i class="fa fa-save text-sm"></i> Update Data
                    </button>
                </div>
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
                <input type="hidden" name="_modal_open" value="perpanjang">
                {{-- Hidden fields untuk menyimpan konteks perpanjang saat reopen --}}
                <input type="hidden" name="_perpanjang_id"         id="_perpanjang_id"         value="{{ old('_perpanjang_id') }}">
                <input type="hidden" name="_perpanjang_kendaraan_id" id="_perpanjang_kendaraan_id" value="{{ old('_perpanjang_kendaraan_id') }}">
                <input type="hidden" name="_perpanjang_nopol"      id="_perpanjang_nopol"      value="{{ old('_perpanjang_nopol') }}">
                <input type="hidden" name="_perpanjang_merk"       id="_perpanjang_merk"       value="{{ old('_perpanjang_merk') }}">
                <input type="hidden" name="_perpanjang_jenis"      id="_perpanjang_jenis"      value="{{ old('_perpanjang_jenis') }}">
                <input type="hidden" name="_perpanjang_jatuh_tempo" id="_perpanjang_jatuh_tempo" value="{{ old('_perpanjang_jatuh_tempo') }}">

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
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed" readonly value="{{ old('jenis_pajak') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Nominal Baru
                        </label>

                        <input id="perpanjang_nominal" type="number" min="0" name="nominal"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('nominal') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Jatuh Tempo Baru
                        </label>

                        {{-- Tampil disabled (tidak bisa diubah), value dikirim via hidden --}}
                        <input id="perpanjang_jatuh_tempo_display" type="date"
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed" disabled>
                        <input type="hidden" name="jatuh_tempo" id="perpanjang_jatuh_tempo">
                        <p class="text-xs text-gray-400 mt-1">Otomatis jatuh tempo lama + 1 tahun</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Tanggal Bayar
                        </label>

                        <input type="date" name="tanggal_bayar" id="perpanjang_tanggal_bayar"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Status
                        </label>

                        <div
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-sm text-gray-700 cursor-not-allowed select-none">
                            Lunas
                        </div>

                        <input type="hidden" name="status" value="sudah_bayar">
                    </div>

                    <div class="sm:col-span-2">
                        <div class="flex items-start gap-2 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-xs text-blue-700">
                            <i class="fa fa-circle-info mt-0.5 flex-shrink-0"></i>
                            <span>Bukti pembayaran akan diunggah oleh Superadmin saat melakukan approval di halaman Pembayaran.</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Pemilik Rekening</label>
                        <input type="text" name="nama_rekening" id="perpanjang_nama_rekening"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            placeholder="Nama pemilik rekening">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Bank</label>
                        <input type="text" name="nama_bank" id="perpanjang_nama_bank"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            placeholder="Contoh: BRI, BCA, Mandiri">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">No. Rekening</label>
                        <input type="text" name="no_rekening" id="perpanjang_no_rekening"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            placeholder="Nomor rekening tujuan">
                    </div>

                    <div class="sm:col-span-2">

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            Keterangan
                        </label>

                        <textarea id="perpanjang_keterangan" name="keterangan" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('keterangan') }}</textarea>

                    </div>

                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModalPerpanjang()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="btnPerpanjangPajak"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fa fa-rotate-right"></i> Perpanjang Pajak
                    </button>
                </div>
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
        // -- MODAL TAMBAH --------------------------------------
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

        // -- AUTO JATUH TEMPO: tanggal bayar + 1 tahun (form tambah) --
        (function () {
            var tglBayar   = document.getElementById('tambah_tanggal_bayar');
            var jatuhTempo = document.getElementById('tambah_jatuh_tempo');
            if (!tglBayar || !jatuhTempo) return;

            tglBayar.addEventListener('change', function () {
                if (!this.value) {
                    jatuhTempo.value = '';
                    return;
                }
                var d = new Date(this.value);
                d.setFullYear(d.getFullYear() + 1);
                // Format Y-m-d
                var y  = d.getFullYear();
                var m  = String(d.getMonth() + 1).padStart(2, '0');
                var dy = String(d.getDate()).padStart(2, '0');
                jatuhTempo.value = y + '-' + m + '-' + dy;
            });
        })();

        modalTambah.addEventListener('click', function(e) {
            if (e.target === modalTambah) closeModalTambah();
        });

        // -- MODAL EDIT --------------------------------------
        const modalEdit = document.getElementById('modalEdit');

        function openModalEdit(id, kendaraan_id, jenis_pajak, nominal, jatuh_tempo, tanggal_bayar, status, keterangan,
            bukti, nama_pemilik, nama_bank, no_rekening) {
            document.getElementById('formEdit').action = `/admin/pajak/${id}`;
            document.getElementById('edit_kendaraan_id').value  = kendaraan_id;
            document.getElementById('edit_jenis_pajak').value   = jenis_pajak;
            document.getElementById('edit_nominal').value       = nominal;
            document.getElementById('edit_jatuh_tempo').value   = formatDate(jatuh_tempo);
            document.getElementById('edit_tanggal_bayar').value = formatDate(tanggal_bayar);
            document.getElementById('edit_status').value        = status;
            document.getElementById('edit_keterangan').value    = keterangan;
            document.getElementById('edit_nama_pemilik').value  = nama_pemilik || '';
            document.getElementById('edit_nama_bank').value     = nama_bank    || '';
            document.getElementById('edit_no_rekening').value   = no_rekening  || '';

            modalEdit.classList.remove('hidden');
            modalEdit.classList.add('flex');
        }

        function syncPerpanjangPajakDates() {
            // Jatuh tempo baru sudah dihitung dari jatuh_tempo_lama saat modal dibuka
            // Fungsi ini tidak perlu melakukan apa-apa lagi
        }

        function closeModalEdit() {
            modalEdit.classList.add('hidden');
            modalEdit.classList.remove('flex');
        }

        modalEdit.addEventListener('click', function(e) {
            if (e.target === modalEdit) closeModalEdit();
        });

        const modalPerpanjang = document.getElementById('modalPerpanjang');

        const tanggalBayarInput = document.getElementById('perpanjang_tanggal_bayar');
        if (tanggalBayarInput) {
            tanggalBayarInput.addEventListener('change', syncPerpanjangPajakDates);
        }

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
            bukti,
            namaPemilik,
            namaBank,
            noRekening
        ) {
            document.getElementById('formPerpanjang').action =
                `/admin/pajak/${id}/perpanjang`;

            document.getElementById('perpanjang_kendaraan').value = kendaraan_id;
            document.getElementById('perpanjang_kendaraan_text').innerText =
                `${nopol} - ${merk}`;

            document.getElementById('perpanjang_jenis').value    = jenis;
            document.getElementById('perpanjang_nominal').value  = nominal;
            document.getElementById('perpanjang_keterangan').value = keterangan;

            // Pre-fill info bank dari data lama
            document.getElementById('perpanjang_nama_rekening').value = namaPemilik || '';
            document.getElementById('perpanjang_nama_bank').value     = namaBank    || '';
            document.getElementById('perpanjang_no_rekening').value   = noRekening  || '';

            // Simpan konteks ke hidden fields (untuk reopen saat validasi gagal)
            document.getElementById('_perpanjang_id').value           = id;
            document.getElementById('_perpanjang_kendaraan_id').value = kendaraan_id;
            document.getElementById('_perpanjang_nopol').value        = nopol;
            document.getElementById('_perpanjang_merk').value         = merk;
            document.getElementById('_perpanjang_jenis').value        = jenis;
            document.getElementById('_perpanjang_jatuh_tempo').value  = jatuhTempo;

            // jatuh_tempo_baru = jatuh_tempo_lama + 1 tahun (dari data lama, tidak berubah saat tanggal_bayar berubah)
            if (jatuhTempo) {
                const d = new Date(jatuhTempo);
                d.setFullYear(d.getFullYear() + 1);
                const val = d.getFullYear() + '-'
                    + String(d.getMonth() + 1).padStart(2, '0') + '-'
                    + String(d.getDate()).padStart(2, '0');
                document.getElementById('perpanjang_jatuh_tempo_display').value = val;
                document.getElementById('perpanjang_jatuh_tempo').value = val;
            }

            // tanggal_bayar: default = tanggal_bayar_lama + 1 tahun, min = nilai tersebut
            var tglBayarElPajak = document.getElementById('perpanjang_tanggal_bayar');
            var todayPajak = new Date().toISOString().split('T')[0];
            var defaultTglPajak;
            if (tanggalBayar) {
                var dp = new Date(tanggalBayar);
                dp.setFullYear(dp.getFullYear() + 1);
                defaultTglPajak = dp.getFullYear() + '-'
                    + String(dp.getMonth() + 1).padStart(2, '0') + '-'
                    + String(dp.getDate()).padStart(2, '0');
            } else {
                defaultTglPajak = todayPajak;
            }
            tglBayarElPajak.value = defaultTglPajak;
            tglBayarElPajak.min   = defaultTglPajak;

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

        // -- POPUP ALERT ----------------------------------------
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
            const params = new URLSearchParams(window.location.search);
            window.open("{{ route('pajak.export.pdf') }}?" + params.toString(), '_blank');
        }


        // -- PREVIEW BUKTI (dipakai bersama oleh Tambah & Edit) --
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

        // -- PREVIEW BUKTI KHUSUS MODAL EDIT --
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
    
        // Auto-reopen modal pada validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            @if (old('_modal_open') === 'perpanjang')
                // Reopen modal perpanjang dengan data old()
                (function() {
                    var id           = '{{ old('_perpanjang_id') }}';
                    var kendaraanId  = '{{ old('_perpanjang_kendaraan_id') }}';
                    var nopol        = '{{ old('_perpanjang_nopol') }}';
                    var merk         = '{{ old('_perpanjang_merk') }}';
                    var jenis        = '{{ old('_perpanjang_jenis') }}';
                    var nominal      = '{{ old('nominal') }}';
                    var jatuhTempo   = '{{ old('_perpanjang_jatuh_tempo') }}';
                    var keterangan   = '{{ old('keterangan') }}';

                    if (typeof openModalPerpanjang === 'function') {
                        openModalPerpanjang(id, kendaraanId, nopol, merk, jenis, nominal, jatuhTempo, '', 'sudah_bayar', keterangan, '');
                    }
                })();
            @else
                if (typeof openModalTambah === 'function') openModalTambah();
                else if (typeof openModal === 'function') openModal();
            @endif
        });
        @endif

        // ── Anti double-submit: disable tombol saat form perpanjang di-submit ──
        (function () {
            var form = document.getElementById('formPerpanjang');
            var btn  = document.getElementById('btnPerpanjangPajak');
            if (!form || !btn) return;
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
                btn.classList.add('opacity-60', 'cursor-not-allowed');
            });
        })();

        // ── CHART PAJAK ──────────────────────────────────────────────────────
        const chartManager = new ChartManager();

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize charts with default filter (year)
            initPajakCharts({ filter_type: 'year' });

            // Listen for filter changes
            document.addEventListener('chartFilterChange', function (e) {
                if (e.detail.filterId === 'pajakChartFilter') {
                    const filters = {
                        filter_type: e.detail.filterType,
                        start_date: e.detail.startDate,
                        end_date: e.detail.endDate,
                    };
                    updatePajakCharts(filters);
                }
            });
        });

        async function initPajakCharts(filters) {
            try {
                await chartManager.initChartsFromAPI('pajak-kendaraan', {
                    pie: 'pajakPieChart',
                    bar: 'pajakBarChart',
                    line: 'pajakLineChart'
                }, filters, { accentLine: true });
            } catch (error) {
                console.error('Error loading pajak charts:', error);
            }
        }

        async function updatePajakCharts(filters) {
            try {
                const isScrollable = filters.filter_type === 'custom';
                const barOptions  = { scrollable: isScrollable, accentLine: true };
                const lineOptions = { scrollable: isScrollable };
                await chartManager.updateChartsFromAPI('pajak-kendaraan', {
                    pie: 'pajakPieChart',
                    bar: 'pajakBarChart',
                    line: 'pajakLineChart'
                }, filters, barOptions, lineOptions);
            } catch (error) {
                console.error('Error updating pajak charts:', error);
            }
        }

        // ── EXPAND ROW PAJAK ─────────────────────────────────────────────────
        function togglePajakRow(id, rowEl) {
            // deprecated — replaced by openDetailModal
        }
</script>

{{-- ============================================================
     MODAL AJUKAN ULANG (muncul saat URL punya ?edit_pembayaran=X)
============================================================ --}}
@if($editPembayaranId && $pajakDitolak)
<div id="modalAjukanUlang" class="fixed inset-0 z-50 flex items-start justify-center bg-black/30 overflow-y-auto py-6"
     style="backdrop-filter:blur(2px)">
    <div id="modalAjukanUlangInner" class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4"
         style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 text-amber-600 text-xs"><i class="fa fa-rotate-right"></i></span>
                    Ajukan Ulang Pajak Kendaraan
                </h2>
                <p class="text-xs text-gray-500 mt-0.5 ml-9">
                    Kendaraan: <span class="font-semibold text-gray-700">{{ $pajakDitolak->kendaraan->nopol ?? '-' }} — {{ $pajakDitolak->kendaraan->merk ?? '-' }}</span>
                </p>
            </div>
            <a href="{{ route('pajak.index') }}"
               class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </a>
        </div>

        {{-- Catatan penolakan --}}
        @if($rejectionReason)
        <div class="mx-6 mt-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-xs text-red-700">
            <p class="font-semibold mb-0.5"><i class="fa fa-comment-slash mr-1"></i>Alasan Penolakan:</p>
            <p>{{ $rejectionReason }}</p>
        </div>
        @endif

        <form id="formAjukanUlang" action="{{ route('pajak.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5" novalidate>
            @csrf
            <input type="hidden" name="edit_pembayaran" value="{{ $editPembayaranId }}">
            {{-- Field readonly: kendaraan & jenis pajak tidak bisa diubah --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan</label>
                    <div class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 select-none cursor-not-allowed">
                        {{ $pajakDitolak->kendaraan->nopol ?? '-' }} — {{ $pajakDitolak->kendaraan->merk ?? '-' }}
                    </div>
                    <input type="hidden" name="kendaraan_id" value="{{ $pajakDitolak->kendaraan_id }}">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pajak</label>
                    <div class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                        {{ $pajakDitolak->jenis_pajak }}
                    </div>
                    <input type="hidden" name="jenis_pajak" value="{{ $pajakDitolak->jenis_pajak }}">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nominal <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="nominal"
                        value="{{ old('nominal', $pajakDitolak->nominal) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tgl Ketentuan Bayar</label>
                    <input type="date" name="tanggal_bayar" id="resubmit_tanggal_bayar"
                        value="{{ old('tanggal_bayar', $pajakDitolak->tanggal_bayar ? \Carbon\Carbon::parse($pajakDitolak->tanggal_bayar)->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Jatuh Tempo</label>
                    <input type="date" id="resubmit_jatuh_tempo" readonly
                        value="{{ old('jatuh_tempo', $pajakDitolak->jatuh_tempo ? \Carbon\Carbon::parse($pajakDitolak->jatuh_tempo)->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed focus:outline-none">
                    <input type="hidden" name="jatuh_tempo" id="resubmit_jatuh_tempo_hidden"
                        value="{{ old('jatuh_tempo', $pajakDitolak->jatuh_tempo ? \Carbon\Carbon::parse($pajakDitolak->jatuh_tempo)->format('Y-m-d') : '') }}">
                    <p class="text-xs text-gray-400 mt-1">Otomatis tanggal bayar + 1 tahun</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pemilik</label>
                    <input type="text" name="nama_pemilik"
                        value="{{ old('nama_pemilik', $pajakDitolak->nama_pemilik) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Bank</label>
                    <input type="text" name="nama_bank"
                        value="{{ old('nama_bank', $pajakDitolak->nama_bank) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Rekening</label>
                    <input type="text" name="no_rekening"
                        value="{{ old('no_rekening', $pajakDitolak->no_rekening) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
                    <textarea name="keterangan" rows="2"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none">{{ old('keterangan', $pajakDitolak->keterangan) }}</textarea>
                </div>

                {{-- Lampiran lama --}}
                @if($pajakDitolak->attachments && $pajakDitolak->attachments->count() > 0)
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-2">
                        Lampiran Sebelumnya
                    </label>
                    <div class="space-y-1.5">
                        @foreach($pajakDitolak->attachments as $att)
                        <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                            <a href="{{ asset($att->file_path) }}" target="_blank"
                               class="flex items-center gap-2 text-xs text-blue-600 hover:text-blue-800 truncate max-w-xs">
                                <i class="fa fa-paperclip text-[10px] flex-shrink-0"></i>
                                <span class="truncate">{{ $att->file_name }}</span>
                            </a>
                            <div class="flex items-center gap-2 ml-2 flex-shrink-0">
                                <span class="text-[10px] text-gray-400">
                                    {{ $att->file_size ? round($att->file_size / 1024, 1) . ' KB' : '' }}
                                </span>
                                <form action="{{ route('pajak.attachment.destroy', $att->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus lampiran ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-red-400 hover:text-red-600 text-xs p-0.5 transition-colors">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Upload lampiran baru --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tambah Lampiran Baru <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <label for="resubmit_attachment"
                        class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <i class="fa-solid fa-paperclip text-lg text-gray-400 mb-0.5"></i>
                        <span class="text-xs text-gray-500">Klik untuk upload lampiran tambahan</span>
                        <span class="text-xs text-gray-400">(Maks 5MB per file)</span>
                    </label>
                    <input type="file" name="bukti_attachment[]" id="resubmit_attachment" class="hidden" multiple
                        onchange="renderListAttachment(this, 'listResubmitAttachment')">
                    <ul id="listResubmitAttachment" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                </div>

            </div>

            <div class="flex gap-3 pt-4 sticky bottom-0 bg-white pb-1">
                <a href="{{ route('pajak.index') }}"
                   class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors text-center">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-paper-plane text-sm"></i> Ajukan Ulang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-hitung jatuh tempo di modal ajukan ulang
(function() {
    // Scroll ke atas modal saat dibuka
    const modalEl = document.getElementById('modalAjukanUlang');
    if (modalEl) {
        modalEl.scrollTop = 0;
        // Juga scroll window ke atas
        window.scrollTo(0, 0);
    }

    const tgl    = document.getElementById('resubmit_tanggal_bayar');
    const jt     = document.getElementById('resubmit_jatuh_tempo');
    const jtHide = document.getElementById('resubmit_jatuh_tempo_hidden');
    if (!tgl || !jt) return;
    tgl.addEventListener('change', function() {
        if (!this.value) { jt.value = ''; if(jtHide) jtHide.value = ''; return; }
        const d = new Date(this.value);
        d.setFullYear(d.getFullYear() + 1);
        const val = d.getFullYear() + '-'
            + String(d.getMonth() + 1).padStart(2, '0') + '-'
            + String(d.getDate()).padStart(2, '0');
        jt.value = val;
        if (jtHide) jtHide.value = val;
    });
})();
</script>
@endif

@include('admin.partials.detail-modal')

{{-- MODAL AJUKAN ULANG PAJAK (via AJAX) --}}
<div id="modalPajakResubmit" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-base font-bold text-gray-800">Ajukan Ulang — Pajak Kendaraan</h3>
                <p class="text-xs text-gray-500 mt-0.5" id="pajakResubmitKendaraan">-</p>
            </div>
            <button onclick="closePajakResubmitModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div id="pajakResubmitLoading" class="flex items-center justify-center py-12">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
                <p class="text-sm">Memuat data...</p>
            </div>
        </div>
        <div id="pajakResubmitBody" class="hidden flex-1 overflow-y-auto flex flex-col">
            <div class="px-6 pt-4 pb-2">
                <div id="pajakResubmitCatatan" class="hidden bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 mb-3"></div>
            </div>
            <div class="flex-1 overflow-y-auto px-6 pb-2 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" id="pr_nominal" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-100 focus:border-amber-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Bayar <span class="text-red-500">*</span></label>
                        <input type="date" id="pr_tanggal_bayar" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-100 focus:border-amber-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan</label>
                    <input type="text" id="pr_keterangan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-100 focus:border-amber-400">
                </div>
            </div>
            <div class="border-t border-gray-100 px-6 py-4 flex gap-2 flex-shrink-0">
                <button type="button" onclick="closePajakResubmitModal()"
                    class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="pajakResubmitSubmitBtn" onclick="submitPajakResubmit()"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-xl py-2.5 transition-colors">
                    <i class="fa fa-rotate-right"></i> Ajukan Ulang
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── MODAL AJUKAN ULANG PAJAK ──────────────────────────────────
let _pajakResubmitId = null;

function openPajakResubmitModal(id) {
    _pajakResubmitId = id;
    document.getElementById('pajakResubmitLoading').classList.remove('hidden');
    document.getElementById('pajakResubmitBody').classList.add('hidden');
    document.getElementById('modalPajakResubmit').classList.remove('hidden');
    document.getElementById('modalPajakResubmit').classList.add('flex');

    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    fetch('/admin/pajak/' + id + '/resubmit-data', {
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(function(data) {
        if (!data.success) throw new Error(data.message || 'Gagal memuat data');
        document.getElementById('pajakResubmitKendaraan').textContent = data.kendaraan || '-';
        const catatanEl = document.getElementById('pajakResubmitCatatan');
        if (data.catatan_penolakan) {
            catatanEl.textContent = 'Alasan penolakan: ' + data.catatan_penolakan;
            catatanEl.classList.remove('hidden');
        } else {
            catatanEl.classList.add('hidden');
        }
        document.getElementById('pr_nominal').value       = data.nominal       || '';
        document.getElementById('pr_tanggal_bayar').value = new Date().toISOString().split('T')[0];
        document.getElementById('pr_keterangan').value    = data.keterangan    || '';
        document.getElementById('pajakResubmitLoading').classList.add('hidden');
        document.getElementById('pajakResubmitBody').classList.remove('hidden');
    })
    .catch(function(err) {
        document.getElementById('pajakResubmitLoading').innerHTML =
            '<div class="text-center text-red-500 py-8 px-6"><i class="fa fa-exclamation-triangle text-xl mb-2"></i><p class="text-sm">' + err.message + '</p></div>';
    });
}

function closePajakResubmitModal() {
    document.getElementById('modalPajakResubmit').classList.replace('flex','hidden');
    document.getElementById('modalPajakResubmit').classList.add('hidden');
    _pajakResubmitId = null;
}

async function submitPajakResubmit() {
    if (!_pajakResubmitId) return;
    const btn   = document.getElementById('pajakResubmitSubmitBtn');
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const nominal = document.getElementById('pr_nominal').value;
    const tglBayar = document.getElementById('pr_tanggal_bayar').value;
    if (!nominal || !tglBayar) { alert('Nominal dan Tanggal Bayar wajib diisi.'); return; }

    const formData = new FormData();
    formData.append('_token',        token);
    formData.append('nominal',       nominal);
    formData.append('tanggal_bayar', tglBayar);
    formData.append('keterangan',    document.getElementById('pr_keterangan').value);

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
    try {
        const res    = await fetch('/admin/pajak/' + _pajakResubmitId + '/resubmit-submit', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.success) {
            closePajakResubmitModal();
            window.location.reload();
        } else {
            alert(result.message || 'Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-rotate-right"></i> Ajukan Ulang';
        }
    } catch(e) {
        alert('Terjadi kesalahan jaringan.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-rotate-right"></i> Ajukan Ulang';
    }
}

document.getElementById('modalPajakResubmit')?.addEventListener('click', function(e) {
    if (e.target === this) closePajakResubmitModal();
});

// ── HIGHLIGHT baris dari ?highlight_pembayaran ─────────────────
(function() {
    const params = new URLSearchParams(window.location.search);
    const hpId = params.get('highlight_pembayaran');
    if (!hpId) return;
    // Cari baris dengan data-pembayaran-id atau tombol yang punya id row-pajak-*
    document.querySelectorAll('tr').forEach(function(tr) {
        if (tr.querySelector('[id^="row-pajak-"]')) {
            const btn = tr.querySelector('[id^="row-pajak-"]');
            // Scroll ke baris ini dan highlight
            tr.classList.add('ring-2', 'ring-amber-400', 'ring-inset');
            setTimeout(function() {
                tr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }
    });
})();
</script>
@endpush
