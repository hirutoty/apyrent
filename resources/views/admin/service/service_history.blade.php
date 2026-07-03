@extends('admin.layouts.app')

@section('title', 'Service History')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    @php
        $totalService = $data->count();
        $totalBiaya = $data->sum('total_biaya');
        $totalProses = $data->where('status', 'proses')->count();
        $totalSelesai = $data->where('status', 'selesai')->count();
    @endphp

    @php
        $aman = 0;
        $hampir = 0;
        $habis = 0;

        foreach ($kendaraan as $k) {
            $limit = $k->limit_bulan_service ?? 0;
            if ($limit <= 0) {
                continue;
            }

            $total = \App\Models\ServiceHistory::where('kendaraan_id', $k->id)
                ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan])
                ->whereYear('tanggal_service', now()->year)
                ->sum('total_biaya');

            $persen = ($total / $limit) * 100;

            if ($persen >= 100) {
                $habis++;
            } elseif ($persen >= 70) {
                $hampir++;
            } else {
                $aman++;
            }
        }
    @endphp

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Service History</h1>
                <p class="text-sm text-gray-500 mt-0.5">Riwayat Service Kendaraan</p>
            </div>
            <button onclick="openModalTambah()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Data
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-4">

            {{-- Total Service --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Service</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $totalService }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-screwdriver-wrench text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total Biaya --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Biaya</p>
                        <h3 class="text-lg font-bold text-green-600 mt-2 leading-tight">Rp
                            {{ number_format($totalBiaya, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Status Proses --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Status Proses</p>
                        <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $totalProses }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="fa-solid fa-gear text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Status Selesai --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Status Selesai</p>
                        <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $totalSelesai }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                    </div>
                </div>
            </div>



            {{-- Hampir Limit --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Hampir Limit</p>
                        <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $hampir }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Limit Habis --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Limit Habis</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $habis }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-ban text-2xl"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Service History</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $totalService }} total data service</p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">

                    {{-- FILTER BULAN --}}
                    <form method="GET" class="flex items-center gap-2">
                        <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}"
                            class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            <i class="fa fa-filter text-xs"></i> Filter
                        </button>

                        <a href="{{ url('/admin/service-history') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fa fa-rotate-left text-xs"></i> Reset
                        </a>
                    </form>

                    {{-- SEARCH + PDF --}}
                    <form method="GET" class="flex items-center gap-2">

                        <input type="hidden" name="bulan" value="{{ request('bulan') }}">

                        <div class="relative">
                            <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari kendaraan, keluhan..."
                                class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-56">
                        </div>

                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                            Cari
                        </button>

                        {{-- PDF (PASTI IKUT FILTER) --}}
                        <a href="{{ route('service-history.pdf', [
                            'bulan' => request('bulan'),
                            'search' => request('search'),
                        ]) }}"
                            target="_blank"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-xs border rounded-lg bg-red-600 text-white hover:bg-red-700">
                            <i class="fa fa-file-pdf"></i> PDF
                        </a>

                    </form>

                    <button onclick="window.location.reload()"
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
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kendaraan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Nopol</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Keluhan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">KM
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Maksimum Pertahun</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Maksimum Bulanan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Total Biaya</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Sisa Per-bulan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status Service</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status Pengeluaran</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Tanggal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Bukti</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($data as $d)
                            @php
                                $limit = $d->kendaraan->limit_bulan_service ?? 0;
                                $sisa = $d->sisa_limit ?? 0;

                                $rowClass = '';
                                if ($limit > 0) {
                                    if ($sisa <= 0) {
                                        $rowClass = 'bg-red-300 hover:bg-red-200';
                                    } elseif ($sisa <= $limit * 0.1) {
                                        $rowClass = 'bg-yellow-50 hover:bg-yellow-100';
                                    }
                                }
                            @endphp

                            <tr class="border-t border-gray-50 transition-colors duration-100 {{ $rowClass ?: 'hover:bg-gray-50' }}"
                                data-search="{{ strtolower(($d->kendaraan->merk ?? '') . ' ' . ($d->kendaraan->nopol ?? '') . ' ' . $d->keluhan . ' ' . $d->status) }}">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>

                                {{-- Kendaraan --}}
                                <td class="px-4 py-3.5">
                                    <span class="font-semibold text-gray-800">{{ $d->kendaraan->merk ?? '-' }}</span>
                                </td>

                                {{-- Nopol --}}
                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->kendaraan->nopol ?? '-' }}</span>
                                </td>

                                {{-- Keluhan --}}
                                <td class="px-4 py-3.5 text-gray-600">{{ $d->keluhan }}</td>

                                {{-- KM --}}
                                <td class="px-4 py-3.5 text-gray-600">{{ number_format($d->kilometer, 0, ',', '.') }}</td>

                                <td class="px-4 py-3.5 text-sm text-gray-600">
                                    @php $maksTahunan = ($d->kendaraan->limit_bulan_service ?? 0) * 12; @endphp
                                    @if ($maksTahunan > 0)
                                        Rp {{ number_format($maksTahunan, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Limit Service --}}
                                <td class="px-4 py-3.5 text-sm text-gray-600">
                                    @if ($d->kendaraan->limit_bulan_service ?? false)
                                        Rp {{ number_format($d->kendaraan->limit_bulan_service, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Total Biaya --}}
                                <td class="px-4 py-3.5 text-sm font-medium text-green-600">Rp
                                    {{ number_format($d->total_biaya, 0, ',', '.') }}</td>

                                {{-- Sisa Service --}}
                                <td class="px-4 py-3.5">
                                    @php
                                        $limit = $d->kendaraan->limit_bulan_service ?? 0;
                                        $sisa = $d->sisa_limit ?? 0;
                                    @endphp
                                    @if ($sisa < 0)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Over Rp {{ number_format(abs($sisa), 0, ',', '.') }}
                                        </span>
                                    @elseif ($sisa <= 0)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Limit Habis
                                        </span>
                                    @elseif ($limit > 0 && $sisa <= $limit * 0.1)
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            Sisa Rp {{ number_format($sisa, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Sisa Rp {{ number_format($sisa, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>



                                <td class="px-4 py-3.5">
                                    <button type="button"
                                        onclick="ubahStatusService({{ $d->id }}, '{{ $d->status }}')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium transition-all duration-200 hover:scale-105
        {{ $d->status == 'proses'
            ? 'bg-amber-50 text-amber-700 border border-amber-200'
            : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">

                                        <span
                                            class="w-1.5 h-1.5 rounded-full
            {{ $d->status == 'proses' ? 'bg-amber-500' : 'bg-emerald-500' }}">
                                        </span>

                                        {{ ucfirst($d->status) }}

                                        <i class="fa-solid fa-pen-to-square text-[10px] opacity-60"></i>
                                    </button>
                                </td>

                                <td class="px-4 py-3.5">
                                    @php
                                        $limitBulan = $d->kendaraan->limit_bulan_service ?? 0;
                                        $totalBulanIni = \App\Models\ServiceHistory::where(
                                            'kendaraan_id',
                                            $d->kendaraan_id,
                                        )
                                            ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [
                                                date('Y-m', strtotime($d->tanggal_service)),
                                            ])
                                            ->sum('total_biaya');
                                        $statusPengeluaran =
                                            $limitBulan > 0 && $totalBulanIni > $limitBulan ? 'overservice' : 'stabil';
                                    @endphp

                                    @if ($statusPengeluaran === 'overservice')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Overservice
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Stabil
                                        </span>
                                    @endif
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-4 py-3.5 text-gray-600">{{ $d->tanggal_service }}</td>

                                {{-- gambar --}}
                                <td>
                                    @if ($d->bukti_pembayaran)
                                        @php $filename = basename($d->bukti_pembayaran); @endphp
                                        <a href="{{ asset($d->bukti_pembayaran) }}" target="_blank"
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
                                            <form action="{{ route('service-history.attachment.destroy', $att->id) }}"
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

                                {{-- Aksi --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">

                                        <form action="{{ route('service-history.destroy', $d->id) }}" method="POST"
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
                                            <i class="fa fa-screwdriver-wrench text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data Service History</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Data" untuk menambahkan data baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    {{-- ======================================
    MODAL TAMBAH
====================================== --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Data Service</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data riwayat service kendaraan</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formTambah" action="{{ route('service-history.store') }}" method="POST"
                enctype="multipart/form-data" class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                {{-- Kendaraan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select name="kendaraan_id" id="kendaraan_id_tambah" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach ($kendaraan as $k)
                            @php
                                // Gunakan $bulan dari controller (Y-m), default bulan ini
                                $bulanAktif = $bulan ?? now()->format('Y-m');

                                // Hitung total semua status (proses + selesai) di bulan aktif
                                $totalSvc = \App\Models\ServiceHistory::where('kendaraan_id', $k->id)
                                    ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulanAktif])
                                    ->sum('total_biaya');

                                $limitK = $k->limit_bulan_service ?? 0;
                                $sisaK = $limitK > 0 ? max($limitK - $totalSvc, 0) : 0;
                                $persen10 = $limitK * 0.1;
                            @endphp
                            <option value="{{ $k->id }}" data-sisa="{{ $sisaK }}"
                                data-limit="{{ $limitK }}" data-status="{{ $k->status_kendaraan }}"
                                {{ $limitK > 0 && $sisaK <= 0 ?: '' }}>
                                {{ $k->merk }} - {{ $k->nopol }}
                                @if ($limitK > 0)
                                    @if ($sisaK < 0)
                                        (⚠ Over Rp {{ number_format(abs($sisaK), 0, ',', '.') }})
                                    @elseif ($sisaK <= 0)
                                        (Limit Habis)
                                    @elseif ($sisaK <= $persen10)
                                        (⚠ Sisa Rp {{ number_format($sisaK, 0, ',', '.') }})
                                    @else
                                        (Sisa Rp {{ number_format($sisaK, 0, ',', '.') }})
                                    @endif
                                @else
                                    (Tidak ada limit)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Info Maks Bulanan (read-only, dari kendaraan) --}}
                <div class="md:col-span-2" id="infoMaksBulananWrapper" style="display:none">
                    <div class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                        <i class="fa fa-info-circle text-blue-400 text-sm"></i>
                        <span class="text-xs text-blue-700">
                            Batas biaya bulanan kendaraan ini:
                            <strong id="infoMaksBulananNominal">-</strong>
                        </span>
                    </div>
                </div>

                {{-- Warning limit --}}
                <div id="warningLimitTambah" class="md:col-span-2 hidden p-3 rounded-lg text-xs font-medium"></div>

                {{-- Keluhan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Keluhan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keluhan" required rows="3" placeholder="Deskripsikan keluhan kendaraan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>

                {{-- Kilometer --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kilometer <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="kilometer" required placeholder="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Total Biaya --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Total Biaya <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="total_biaya" id="total_biaya_tambah" required placeholder="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="proses">Proses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                {{-- Tanggal Service --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Service <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_service" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>


                {{-- Bukti Pembayaran --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Bukti Pembayaran
                    </label>

                    {{-- PREVIEW AREA --}}
                    <div id="previewWrap" class="hidden mb-3 relative">
                        <img id="previewImg" src=""
                            class="hidden h-32 w-full rounded-xl border border-gray-200 object-cover cursor-pointer"
                            onclick="window.open(this.src,'_blank')">

                        <a id="previewFileLink" href="#" target="_blank"
                            class="hidden items-center gap-3 p-4 border rounded-xl bg-gray-50 hover:bg-gray-100">
                            <i id="previewFileIcon" class="fa-solid fa-file text-2xl text-gray-500"></i>
                            <div>
                                <div id="previewFileName" class="font-medium text-sm text-gray-700">File Bukti Pembayaran
                                </div>
                            </div>
                        </a>

                        <button type="button" onclick="hapusPreviewTambah()"
                            class="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs flex items-center justify-center">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        </button>
                    </div>

                    {{-- UPLOAD AREA --}}
                    <label for="bukti_pembayaran"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-1"></i>

                        <span class="text-xs text-gray-500">Klik untuk upload file</span>
                        <span class="text-[10px] text-gray-400">
                            (max 5MB)
                        </span>
                    </label>

                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="hidden"
                        onchange="previewFileGPS(this)" required>
                </div>

                {{-- Lampiran Tambahan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Lampiran Tambahan (opsional, bisa lebih dari 1)
                    </label>

                    <label for="bukti_attachment"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <i class="fa-solid fa-paperclip text-xl text-gray-400 mb-1"></i>
                        <span class="text-xs text-gray-500">Klik untuk upload lampiran tambahan</span>
                        <span class="text-xs text-gray-400">(Maks 5MB per file)</span>
                    </label>

                    <input type="file" name="bukti_attachment[]" id="bukti_attachment" class="hidden" multiple
                        onchange="renderListAttachment(this, 'listAttachmentTambah')">

                    <ul id="listAttachmentTambah" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                </div>

                {{-- Tombol --}}
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
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Data Service</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data riwayat service kendaraan</p>
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

                {{-- Kendaraan --}}
                <select name="kendaraan_id" id="edit_kendaraan_id" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @foreach ($kendaraan as $k)
                        @php
                            $bulanAktif = $bulan ?? now()->format('Y-m');

                            $totalSvcEdit = \App\Models\ServiceHistory::where('kendaraan_id', $k->id)
                                ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulanAktif])
                                ->sum('total_biaya');

                            $limitEdit = $k->limit_bulan_service ?? 0;
                            $sisaEdit = $limitEdit > 0 ? max($limitEdit - $totalSvcEdit, 0) : 0;
                        @endphp
                        <option value="{{ $k->id }}" data-limit="{{ $limitEdit }}"
                            data-sisa="{{ $sisaEdit }}">
                            {{ $k->merk }} - {{ $k->nopol }}
                            @if ($limitEdit > 0)
                                @if ($sisaEdit <= 0)
                                    (Limit Habis)
                                @else
                                    (Sisa Rp {{ number_format($sisaEdit, 0, ',', '.') }})
                                @endif
                            @else
                                (Tidak ada limit)
                            @endif
                        </option>
                    @endforeach
                </select>

                {{-- Info maks bulanan (edit) --}}
                <div class="md:col-span-2" id="editInfoWrapper">
                    <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg space-y-1">

                        {{-- Baris 1: Maks bulanan & sisa --}}
                        <div class="flex items-center gap-2">
                            <i class="fa fa-info-circle text-blue-400 text-sm"></i>
                            <span class="text-xs text-blue-700">
                                Batas bulanan: <strong id="editInfoMaksBulanan">-</strong>
                                &nbsp;|&nbsp;
                                Sisa bulan ini: <strong id="editInfoSisaLimit">-</strong>
                            </span>
                        </div>

                        {{-- Baris 2: Biaya tahunan & status pengeluaran --}}
                        <div class="flex items-center gap-2">
                            <i class="fa fa-chart-line text-blue-400 text-sm"></i>
                            <span class="text-xs text-blue-700">
                                Biaya tahunan: <strong id="editInfoBiayaTahunan">-</strong>
                                &nbsp;|&nbsp;
                                Status: <strong id="editInfoStatusPengeluaran">-</strong>
                            </span>
                        </div>

                    </div>
                </div>

                {{-- Keluhan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Keluhan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keluhan" id="edit_keluhan" required rows="3"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
                </div>

                {{-- Kilometer --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kilometer <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="kilometer" id="edit_kilometer" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Total Biaya --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Total Biaya <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="total_biaya" id="edit_total_biaya" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>


                {{-- Status --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="edit_status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="proses">Proses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                {{-- Tanggal Service --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Service <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_service" id="edit_tanggal_service" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Bukti Pembayaran --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bukti Pembayaran</label>
                    <input type="file" name="bukti_pembayaran" accept="image/*,.pdf,.doc,.docx"
                        onchange="previewEdit(event)" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <!--<img id="previewEdit" class="mt-2 w-32 h-32 object-cover rounded-lg border hidden">-->
                    <!--<div id="previewLamaWrapper" class="mt-2 hidden">-->
                    <!--    <p class="text-xs text-gray-500 mb-1">Foto Saat Ini</p>-->
                    <!--    <img id="previewLama" class="w-32 h-32 object-cover rounded-lg border">-->
                    <!--</div>-->
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Lampiran Tambahan (opsional, bisa lebih dari 1)
                    </label>

                    <input id="edit_bukti_attachment" type="file" name="bukti_attachment[]" multiple
                        class="w-full border rounded-lg px-3 py-2"
                        onchange="renderListAttachment(this, 'listAttachmentEdit')">

                    <ul id="listAttachmentEdit" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                </div>



                {{-- Tombol --}}
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
        // ── MODAL TAMBAH ───────────────────────────────────
        function openModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

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

        function closeModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.getElementById('listAttachmentTambah').innerHTML = '';
            document.getElementById('bukti_attachment').value = '';
        }
        document.getElementById('modalTambah').addEventListener('click', function(e) {
            if (e.target === this) closeModalTambah();
        });

        // ── MODAL EDIT ─────────────────────────────────────
        // ── MODAL EDIT ─────────────────────────────────────
        function openEditModal(id, kendaraan_id, keluhan, kilometer, total_biaya,
            status, tanggal_service, maks_bulanan, biaya_tahunan, status_pengeluaran, bukti_pembayaran) {

            document.getElementById('edit_bukti_attachment').value = '';
            document.getElementById('listAttachmentEdit').innerHTML = '';

            var m = document.getElementById('modalEdit');
            m.classList.remove('hidden');
            m.classList.add('flex');

            document.getElementById('formEdit').action = '/admin/service-history/' + id;
            document.getElementById('edit_kendaraan_id').value = kendaraan_id;
            document.getElementById('edit_keluhan').value = keluhan;
            document.getElementById('edit_kilometer').value = kilometer;
            document.getElementById('edit_total_biaya').value = total_biaya;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_tanggal_service').value = tanggal_service;

            // Set info dari data record
            updateEditInfo(maks_bulanan, biaya_tahunan, status_pengeluaran);

            // Hitung sisa dari option yang terpilih
            updateEditSisa(kendaraan_id);
        }

        function fmt(n) {
            return n > 0 ? 'Rp ' + parseInt(n || 0).toLocaleString('id-ID') : '-';
        }

        function updateEditInfo(maks_bulanan, biaya_tahunan, status_pengeluaran) {
            document.getElementById('editInfoMaksBulanan').textContent = fmt(maks_bulanan);
            document.getElementById('editInfoBiayaTahunan').textContent = fmt(biaya_tahunan);

            var spEl = document.getElementById('editInfoStatusPengeluaran');
            spEl.textContent = status_pengeluaran || '-';
            spEl.className = status_pengeluaran === 'overservice' ?
                'text-red-600' : 'text-green-600';
        }

        function updateEditSisa(kendaraan_id) {
            var sel = document.getElementById('edit_kendaraan_id');
            var opt = Array.from(sel.options).find(o => o.value == kendaraan_id);
            if (!opt) return;

            var sisa = parseInt(opt.dataset.sisa || 0);
            var limit = parseInt(opt.dataset.limit || 0);

            var sisaEl = document.getElementById('editInfoSisaLimit');
            var maksBulEl = document.getElementById('editInfoMaksBulanan');

            maksBulEl.textContent = fmt(limit);

            if (limit <= 0) {
                sisaEl.textContent = 'Tidak ada limit';
                sisaEl.className = 'text-gray-500';
            } else if (sisa <= 0) {
                sisaEl.textContent = 'Limit Habis';
                sisaEl.className = 'text-red-600 font-semibold';
            } else if (sisa <= limit * 0.1) {
                sisaEl.textContent = fmt(sisa);
                sisaEl.className = 'text-yellow-600 font-semibold';
            } else {
                sisaEl.textContent = fmt(sisa);
                sisaEl.className = 'text-green-600 font-semibold';
            }
        }

        // Update sisa saat ganti kendaraan di modal edit
        (function() {
            var sel = document.getElementById('edit_kendaraan_id');
            if (!sel) return;
            sel.addEventListener('change', function() {
                var opt = sel.options[sel.selectedIndex];
                var limit = parseInt(opt.dataset.limit || 0);
                var sisa = parseInt(opt.dataset.sisa || 0);

                document.getElementById('editInfoMaksBulanan').textContent = fmt(limit);

                var sisaEl = document.getElementById('editInfoSisaLimit');
                if (limit <= 0) {
                    sisaEl.textContent = 'Tidak ada limit';
                    sisaEl.className = 'text-gray-500';
                } else if (sisa <= 0) {
                    sisaEl.textContent = 'Limit Habis';
                    sisaEl.className = 'text-red-600 font-semibold';
                } else if (sisa <= limit * 0.1) {
                    sisaEl.textContent = fmt(sisa);
                    sisaEl.className = 'text-yellow-600 font-semibold';
                } else {
                    sisaEl.textContent = fmt(sisa);
                    sisaEl.className = 'text-green-600 font-semibold';
                }
            });
        })();

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

        // ── PREVIEW FOTO ───────────────────────────────────
        function previewTambah(event) {
            var file = event.target.files[0];
            if (!file) return;
            var img = document.getElementById('previewTambah');
            img.src = URL.createObjectURL(file);
            img.classList.remove('hidden');
        }

        function previewEdit(event) {
            var file = event.target.files[0];
            if (!file) return;
            var img = document.getElementById('previewEdit');
            img.src = URL.createObjectURL(file);
            img.classList.remove('hidden');
        }

        // ── INFO MAKS BULANAN + WARNING (MODAL TAMBAH) ─────
        (function() {
            var sel = document.getElementById('kendaraan_id_tambah');
            var biaya = document.getElementById('total_biaya_tambah');
            var warning = document.getElementById('warningLimitTambah');
            var infoWrap = document.getElementById('infoMaksBulananWrapper');
            var infoNominal = document.getElementById('infoMaksBulananNominal');

            function fmt(n) {
                return 'Rp ' + parseInt(n || 0).toLocaleString('id-ID');
            }

            function check() {
                if (!sel || !biaya || !warning) return;
                var opt = sel.options[sel.selectedIndex];
                var sisa = parseInt(opt ? (opt.dataset.sisa || 0) : 0);
                var limit = parseInt(opt ? (opt.dataset.limit || 0) : 0);
                var nilai = parseInt(biaya.value || 0);
                var after = sisa - nilai;

                // Tampilkan info maks bulanan
                if (limit > 0) {
                    infoNominal.textContent = fmt(limit);
                    infoWrap.style.display = '';
                } else {
                    infoWrap.style.display = 'none';
                }

                // Warning limit
                warning.className = 'md:col-span-2 hidden p-3 rounded-lg text-xs font-medium';
                if (nilai > 0 && limit > 0) {
                    if (after <= 0) {
                        warning.classList.remove('hidden');
                        warning.classList.add('bg-red-100', 'text-red-700');
                        warning.textContent = 'Limit service kendaraan ini akan habis atau melebihi batas bulan ini.';
                    } else if (after <= limit * 0.1) {
                        warning.classList.remove('hidden');
                        warning.classList.add('bg-yellow-100', 'text-yellow-700');
                        warning.textContent = 'Jatah service kendaraan bulan ini hampir habis.';
                    }
                }
            }

            if (sel) sel.addEventListener('change', check);
            if (biaya) biaya.addEventListener('input', check);
        })();

        // ── UPDATE INFO MAKS BULANAN SAAT GANTI KENDARAAN (EDIT) ──
        (function() {
            var sel = document.getElementById('edit_kendaraan_id');
            if (!sel) return;
            sel.addEventListener('change', function() {
                var opt = sel.options[sel.selectedIndex];
                var limit = parseInt(opt ? (opt.dataset.limit || 0) : 0);
                var infoEl = document.getElementById('editInfoMaksBulanan');
                if (infoEl) {
                    infoEl.textContent = limit > 0 ?
                        'Rp ' + limit.toLocaleString('id-ID') :
                        '-';
                }
            });
        })();

        // ── POPUP ALERT ────────────────────────────────────
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

        function ubahStatusService(id, currentStatus) {

            Swal.fire({
                title: 'Status Service',
                text: 'Pilih status baru',
                width: '320px',
                input: 'select',
                inputValue: currentStatus,
                inputOptions: {
                    proses: '🟡 Proses',
                    selesai: '🟢 Selesai'
                },
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                customClass: {
                    popup: 'rounded-3xl',
                    title: 'text-lg font-bold',
                    confirmButton: 'rounded-xl px-4 py-2',
                    cancelButton: 'rounded-xl px-4 py-2'
                }
            }).then((result) => {

                if (!result.isConfirmed) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('admin/service-history') }}/${id}/status`;

                form.innerHTML = `
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="status" value="${result.value}">
        `;

                document.body.appendChild(form);
                form.submit();
            });
        }


        function previewFileGPS(input) {
            const wrap = document.getElementById('previewWrap');
            const img = document.getElementById('previewImg');
            const link = document.getElementById('previewFileLink');
            const icon = document.getElementById('previewFileIcon');
            const nameEl = document.getElementById('previewFileName');

            const file = input.files[0];
            if (!file) return;

            wrap.classList.remove('hidden');
            const url = URL.createObjectURL(file);

            if (file.type.startsWith('image/')) {
                img.src = url;
                img.classList.remove('hidden');
                link.classList.add('hidden');
            } else {
                const ext = file.name.split('.').pop().toLowerCase();
                icon.className = getFileIconClass(ext);
                nameEl.textContent = file.name;
                link.href = url;
                link.classList.remove('hidden');
                img.classList.add('hidden');
            }
        }

        function getFileIconClass(ext) {
            if (ext === 'pdf') return 'fa-solid fa-file-pdf text-2xl text-red-500';
            if (ext === 'doc' || ext === 'docx') return 'fa-solid fa-file-word text-2xl text-blue-500';
            if (ext === 'xls' || ext === 'xlsx') return 'fa-solid fa-file-excel text-2xl text-green-600';
            return 'fa-solid fa-file text-2xl text-gray-500';
        }

        function hapusPreviewTambah() {
            document.getElementById('bukti_pembayaran').value = '';
            document.getElementById('previewWrap').classList.add('hidden');
            document.getElementById('previewImg').classList.add('hidden');
            document.getElementById('previewFileLink').classList.add('hidden');
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
