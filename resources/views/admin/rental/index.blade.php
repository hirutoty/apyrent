@extends('admin.layouts.app')

@section('title', 'Rental')

<style>
    /* Override Flatpickr disabled dates */
    .flatpickr-calendar .flatpickr-day.disabled,
    .flatpickr-calendar .flatpickr-day.disabled:hover,
    .flatpickr-calendar .flatpickr-day.flatpickr-disabled,
    .flatpickr-calendar .flatpickr-day.flatpickr-disabled:hover {
        background: #fee2e2 !important;
        color: #dc2626 !important;
        border-color: #fca5a5 !important;
        text-decoration: line-through !important;
        opacity: 1 !important;
        cursor: not-allowed !important;
    }
</style>
@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Rental</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola transaksi rental kendaraan</p>
            </div>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Rental
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Rental</p>
                        <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $totalRental }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-car-front text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Pendapatan</p>
                        <h2 class="text-lg font-bold text-green-600 mt-2 leading-tight">
                            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-cash-stack text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pending</p>
                        <h2 class="text-3xl font-bold text-gray-700 mt-2" id="count-Pending">
                            {{ $countPending }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                        <i class="bi bi-hourglass-split text-2xl text-gray-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Booking</p>
                        <h2 class="text-3xl font-bold text-blue-600 mt-2" id="count-booking">
                            {{ $countBooking }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-calendar-check text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Aktif</p>
                        <h2 class="text-3xl font-bold text-green-600 mt-2" id="count-aktif">
                            {{ $countAktif }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">
                        <i class="bi bi-check-circle text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Selesai</p>
                        <h2 class="text-3xl font-bold text-gray-800 mt-2" id="count-selesai">
                            {{ $countSelesai }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                        <i class="bi bi-flag text-2xl text-gray-700"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Batal</p>
                        <h2 class="text-3xl font-bold text-red-500 mt-2" id="count-batal">
                            {{ $countBatal }}</h2>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">
                        <i class="bi bi-x-circle text-2xl text-red-500"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- CHART FILTER --}}
        <x-chart-filter id="rentalChartFilter" defaultFilter="month" :showCustomRange="true" />

        {{-- CHART CONTAINER --}}
        <x-chart-container
            id="rentalChartContainer"
            layout="bar-top"
            pieTitle="Distribusi Status Pembayaran" pieId="rentalPieChart"
            barTitle="Pendapatan Rental per Bulan" barId="rentalBarChart"
            lineTitle="Trend Pendapatan Rental" lineId="rentalLineChart"
            :showStats="true" :statsData="[]"
        />

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- TAB NAVIGATION --}}
            <div class="flex items-center gap-1 px-5 pt-4 border-b border-gray-100 overflow-x-auto">
                @php
                    $tabs = [
                        ['key' => '', 'label' => 'Semua'],
                        ['key' => 'Pending', 'label' => 'Pending'],
                        ['key' => 'booking', 'label' => 'Booking'],
                        ['key' => 'aktif', 'label' => 'Aktif'],
                        ['key' => 'selesai', 'label' => 'Selesai'],
                        ['key' => 'batal', 'label' => 'Batal'],
                    ];
                    $activeStatus = request('status', '');
                @endphp

                @foreach ($tabs as $tab)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $tab['key'], 'page' => 1]) }}"
                        class="flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold rounded-t-lg whitespace-nowrap transition-all duration-150 border-b-2 -mb-px
                        {{ $activeStatus === $tab['key']
                            ? ($tab['key'] === '' ? 'border-blue-500 text-blue-600 bg-blue-50/60'
                                : ($tab['key'] === 'aktif' ? 'border-green-500 text-green-600 bg-green-50/60'
                                : ($tab['key'] === 'selesai' ? 'border-slate-800 text-slate-800 bg-slate-50/60'
                                : ($tab['key'] === 'batal' ? 'border-red-500 text-red-600 bg-red-50/60'
                                : 'border-blue-500 text-blue-600 bg-blue-50/60'))))
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        {{ $tab['label'] }}
                        <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold
                            {{ $activeStatus === $tab['key'] ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                            @if ($tab['key'] === '')
                                {{ $totalRental }}
                            @elseif ($tab['key'] === 'Pending')
                                {{ $countPending }}
                            @elseif ($tab['key'] === 'booking')
                                {{ $countBooking }}
                            @elseif ($tab['key'] === 'aktif')
                                {{ $countAktif }}
                            @elseif ($tab['key'] === 'selesai')
                                {{ $countSelesai }}
                            @elseif ($tab['key'] === 'batal')
                                {{ $countBatal }}
                            @endif
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- FILTER BAR --}}
            <form method="GET" action="{{ request()->url() }}"
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3 bg-gray-50/50 border-b border-gray-100">

                {{-- Preserve status --}}
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                {{-- Search --}}
                <div class="relative flex-1 max-w-xs">
                    <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" placeholder="Cari kendaraan / pelanggan..."
                        value="{{ request('search') }}"
                        class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fa fa-search text-xs"></i> Cari
                    </button>
                    @if(request()->hasAny(['search']))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => 1]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fa fa-rotate-left text-xs"></i> Reset
                        </a>
                    @endif
                    <button type="button" onclick="exportData()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                        <i class="fa fa-file-pdf text-xs"></i> Export Rental
                    </button>
                </div>
            </form>

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kendaraan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Pelanggan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Check-in</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Check-out</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Durasi</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Total</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Tipe Sewa</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Tujuan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Pengantaran / Penjemputan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Pembayaran</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rentalTableBody">
                        @forelse ($rentals as $i => $r)
                            @php
                                if ($r->durasi_bulan) {
                                    $jenisRental = 'Bulanan';
                                    $badge = 'bg-blue-100 text-blue-700';
                                } elseif ($r->durasi_hari) {
                                    $jenisRental = 'Harian';
                                    $badge = 'bg-green-100 text-green-700';
                                } elseif ($r->durasi_tahun) {
                                    $jenisRental = 'Tahunan';
                                    $badge = 'bg-amber-100 text-amber-700';
                                } else {
                                    $jenisRental = '-';
                                    $badge = 'bg-gray-100 text-gray-600';
                                }
                                $tanggalMulaiStr = $r->tanggal_mulai
                                    ? \Carbon\Carbon::parse($r->tanggal_mulai)->format('Y-m-d')
                                    : '';
                                $tanggalBulan = $r->tanggal_mulai
                                    ? \Carbon\Carbon::parse($r->tanggal_mulai)->format('Y-m')
                                    : '';
                            @endphp
                            <tr class="rental-row border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100">

                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium row-num">{{ $rentals->firstItem() + $loop->index }}</td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="bi bi-car-front text-blue-400 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $r->kendaraan->merk ?? '-' }}</p>
                                            <span
                                                class="font-mono text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">{{ $r->kendaraan->nopol ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $r->member->nama_pelanggan ?? '-' }}</td>

                                <td class="px-4 py-3.5 text-sm text-gray-700">{{ $r->tanggal_mulai }}</td>
                                <td class="px-4 py-3.5 text-sm text-gray-400">{{ $r->tanggal_selesai }}</td>

                                <td class="px-4 py-3.5">

                                    @if ($r->tanggal_mulai && $r->tanggal_selesai)
                                        @php
                                            $start = \Carbon\Carbon::parse($r->tanggal_mulai);
                                            $end = \Carbon\Carbon::parse($r->tanggal_selesai);

                                            $days = $start->diffInDays($end);
                                        @endphp

                                        {{-- DURASI --}}
                                        @if ($days >= 365)
                                            {{ floor($days / 365) }} Tahun
                                        @elseif ($days >= 30)
                                            {{ floor($days / 30) }} Bulan
                                        @else
                                            {{ $days }} Hari
                                        @endif

                                        @if ($r->status === 'aktif')
                                            @if ($r->terlambat)
                                                <div class="text-xs text-red-600 font-semibold mt-1 flex items-center gap-1">
                                                    <i class="fa fa-circle-exclamation text-[10px]"></i>
                                                    Terlambat {{ $r->sisa }}
                                                </div>
                                            @elseif ($r->reminder)
                                                @php
                                                    $diffSecRental = (int) (\Carbon\Carbon::parse($r->tanggal_selesai)->timestamp - now()->timestamp);
                                                    $isUrgent = $diffSecRental > 0 && $diffSecRental < 86400;
                                                @endphp
                                                <div class="text-xs font-semibold mt-1 flex items-center gap-1 {{ $isUrgent ? 'text-red-500' : 'text-orange-500' }}">
                                                    <i class="fa fa-triangle-exclamation text-[10px]"></i>
                                                    Berakhir {{ $r->sisa }} lagi
                                                </div>
                                            @endif
                                        @endif
                                    @else
                                        -
                                    @endif

                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="text-sm font-bold text-blue-600">Rp
                                        {{ number_format($r->total_biaya) }}</span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span
                                        class="inline-flex w-fit px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">{{ $jenisRental }}</span>
                                </td>

                                {{-- Tujuan Perjalanan --}}
                                <td class="px-4 py-3.5">
                                    @if ($r->tujuan_perjalanan)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                                            {{ $r->tujuan_perjalanan === 'luar_kota' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                            
                                            {{ $r->tujuan_perjalanan === 'dalam_kota' ? 'Dalam Kota' : 'Luar Kota' }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Pengantaran / Penjemputan --}}
                                <td class="px-4 py-3.5 text-xs text-gray-600 max-w-[180px]">
                                    @if ($r->alamat_pengantaran || $r->alamat_penjemputan)
                                        <div class="space-y-1">
                                            @if ($r->alamat_pengantaran)
                                                <div class="flex items-start gap-1">
                                                    <span class="text-green-500 mt-0.5 flex-shrink-0"><i class="fa fa-arrow-right text-[9px]"></i></span>
                                                    <span class="truncate" title="{{ $r->alamat_pengantaran }}">{{ $r->alamat_pengantaran }}</span>
                                                </div>
                                            @endif
                                            @if ($r->alamat_penjemputan)
                                                <div class="flex items-start gap-1">
                                                    <span class="text-orange-500 mt-0.5 flex-shrink-0"><i class="fa fa-arrow-left text-[9px]"></i></span>
                                                    <span class="truncate" title="{{ $r->alamat_penjemputan }}">{{ $r->alamat_penjemputan }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex flex-col gap-1.5">
                                        {{-- Badge status bayar — pakai status_pembayaran dari DB --}}
                                        @if ($r->status_pembayaran === 'lunas' || $r->bukti_pelunasan || $r->bukti_lunas)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 w-fit">
                                                <i class="fa fa-check-circle"></i> LUNAS
                                            </span>
                                        @elseif ($r->status_pembayaran === 'partial')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 w-fit">
                                                <i class="fa fa-clock"></i> PARTIAL
                                            </span>
                                        @elseif ($r->status_pembayaran === 'dp' || $r->bukti_dp)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 w-fit">
                                                <i class="fa fa-credit-card"></i> DP
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 w-fit">
                                                <i class="fa fa-times-circle"></i> BELUM BAYAR
                                            </span>
                                        @endif

                                       
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <button onclick="openStatusModal({{ $r->id }}, '{{ $r->status }}')">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-white text-xs font-semibold cursor-pointer
            @if ($r->status == 'Pending') bg-gray-500
            @elseif($r->status == 'booking') bg-blue-500
            @elseif($r->status == 'aktif') bg-green-500
            @elseif($r->status == 'selesai') bg-gray-800
            @else bg-red-500 @endif">
                                            {{ strtoupper($r->status) }}
                                        </span>
                                    </button>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('rental.show', $r->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-eye text-xs"></i> Detail
                                        </a>
                                        {{-- Task 8: Tombol cetak Invoice PDF --}}
                                        <a href="{{ route('rental.invoice-pdf', $r->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                            title="Cetak Invoice PDF">
                                            <i class="fa fa-file-pdf text-xs"></i> Invoice
                                        </a>
                                        <form action="{{ route('rental.destroy', $r->id) }}" method="POST"
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
                        @endforelse
                    </tbody>
                </table>
                <div class="py-3 px-5 border-t border-gray-100">
                    <x-pagination :paginator="$rentals" />
                </div>
            </div>

            {{-- MODAL STATUS --}}
            <div id="statusModal" class="fixed inset-0 hidden items-center justify-center bg-black/40 z-50">
                <div class="bg-white rounded-xl shadow-lg w-96 p-5">

                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-800">Ubah Status Rental</h2>
                        <button onclick="closeStatusModal()" class="text-gray-400 hover:text-red-500">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <input type="hidden" id="statusRentalId">

                    {{-- Info status saat ini --}}
                    <div class="mb-4 flex items-center gap-2">
                        <span class="text-xs text-gray-500">Status saat ini:</span>
                        <span id="statusCurrentBadge"
                            class="px-2.5 py-1 rounded-full text-white text-xs font-semibold"></span>
                    </div>

                    {{-- Warning bukti bayar --}}
                    <div id="warningBukti"
                        class="hidden mb-3 flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2.5 text-xs text-red-700">
                        <i class="fa fa-triangle-exclamation mt-0.5 shrink-0"></i>
                        <span id="warningBuktiText"></span>
                    </div>

                    {{-- Pilihan tombol status --}}
                    <div id="statusOptions" class="flex flex-col gap-2"></div>

                    <div class="flex justify-end mt-4">
                        <button onclick="closeStatusModal()"
                            class="px-4 py-1.5 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>

            {{-- EMPTY STATE --}}
            <div id="emptyState" class="hidden px-5 py-12 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="bi bi-car-front text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Tidak ada data ditemukan</p>
                    <p class="text-xs text-gray-400">Coba ubah filter atau tab yang dipilih</p>
                </div>
            </div>

            {{-- TABLE FOOTER --}}
            <div class="px-5 py-3 border-t border-gray-100">
                <p class="text-xs text-gray-400">{{ $rentals->total() }} total transaksi</p>
            </div>

        </div>

    </div>


    {{-- ======================================================================
     MODAL TAMBAH RENTAL
     
     ====================================================================== --}}

    <div id="modalRental" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 overflow-y-auto py-6"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 my-auto" style="animation:slideUp .2s ease">

            {{-- HEADER --}}
            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Rental</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data transaksi rental kendaraan</p>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="rentalStoreForm" action="{{ route('rental.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- -- PELANGGAN -- --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Data Pelanggan</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="relative">
                                <label class="text-xs text-gray-500 mb-1 block">Nama Pelanggan</label>
                                <input type="text" id="nama_pelanggan" name="nama_pelanggan"
                                    class="w-full border rounded-lg px-3 py-2" placeholder="Ketik Nama Pelanggan..."
                                    autocomplete="off" required value="{{ old('nama_pelanggan') }}">
                                <input type="hidden" name="member_id" id="member_id">
                                <div id="member-result"
                                    class="absolute z-50 w-full bg-white border rounded shadow hidden max-h-40 overflow-y-auto">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Kontak</label>
                                <input type="number" name="kontak_pelanggan" id="kontak_pelanggan" maxlength="15"
                                    placeholder="Ketik kontak pelanggan..." class="w-full border rounded-lg px-3 py-2"
                                    required value="{{ old('kontak_pelanggan') }}">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Alamat</label>
                                <textarea name="alamat_pelanggan" id="alamat_pelanggan" rows="2" class="w-full border rounded-lg px-3 py-2"
                                    placeholder="Ketik alamat pelanggan..." required>{{ old('alamat_pelanggan') }}</textarea>
                            </div>

                            {{-- -- Email Pelanggan (opsional) -- --}}
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">
                                    Email Pelanggan <span class="text-gray-400 font-normal">(opsional)</span>
                                </label>
                                <input type="email" name="email_pelanggan" id="email_pelanggan"
                                    placeholder="contoh@email.com" class="w-full border rounded-lg px-3 py-2" value="{{ old('email_pelanggan') }}">
                            </div>

                            {{-- -- Jenis Pelanggan (wajib) -- --}}
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">
                                    Jenis Pelanggan <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_pelanggan" id="jenis_pelanggan"
                                    class="w-full border rounded-lg px-3 py-2 text-sm" required>
                                    <option value="">-- Pilih Jenis Pelanggan --</option>
                                    <option value="perorangan" {{ old('jenis_pelanggan') == 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                                    <option value="perusahaan" {{ old('jenis_pelanggan') == 'perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- -- KENDARAAN -- --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span
                                class="text-red-500">*</span></label>
                        <select name="kendaraan_id" id="kendaraanSelect" onchange="setHargaKendaraan()"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            required>
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach ($kendaraans as $k)
                                <option value="{{ $k->id }}" {{ old('kendaraan_id') == $k->id ? 'selected' : '' }} data-hari="{{ $k->harga_sewa_per_hari }}"
                                    data-jam="{{ $k->harga_sewa_per_jam }}"
                                    data-bulan="{{ $k->harga_sewa_per_bulan ?? 0 }}">
                                    {{ $k->merk }} - {{ $k->nopol }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- -- TANGGAL MULAI -- --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Mulai <span
                                class="text-red-500">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai" id="tanggalMulai"
                            onchange="updateTanggalSelesai()"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                            required value="{{ old('tanggal_mulai') }}">
                    </div>

                    {{-- -- TIPE RENTAL (3 pilihan) -- --}}
                    <input type="hidden" name="tipe_rental" id="tipe_rental" value="bulan">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe Rental</label>
                        <div class="flex gap-2">
                            <button type="button" id="btnTahun" onclick="setTipeRental('tahun')"
                                class="flex-1 text-xs font-semibold px-3 py-2 rounded-lg transition-colors bg-gray-100 text-gray-600">
                                Per Tahun
                            </button>
                            <button type="button" id="btnBulan" onclick="setTipeRental('bulan')"
                                class="flex-1 text-xs font-semibold px-3 py-2 rounded-lg transition-colors bg-gray-100 text-gray-600">
                                Per Bulan
                            </button>
                            <button type="button" id="btnHari" onclick="setTipeRental('hari')"
                                class="flex-1 text-xs font-semibold px-3 py-2 rounded-lg transition-colors bg-gray-100 text-gray-600">
                                Per Hari
                            </button>
                        </div>
                    </div>

                    {{-- -- DURASI TAHUN -- --}}
                    <div id="formTahun" class="hidden">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Durasi Tahun <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="durasi_tahun" id="durasiTahun" value="" min="1"
                            oninput="updateTanggalSelesai(); hitungBiayaDasar()" placeholder="Jumlah tahun"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    {{-- -- DURASI BULAN -- --}}
                    <div id="formBulan">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Durasi Bulan <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="durasi_bulan" id="durasiBulan" value="" min="1"
                            oninput="updateTanggalSelesai(); hitungBiayaDasar()" placeholder="Jumlah bulan"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    {{-- -- DURASI HARI -- --}}
                    <div id="formHari" class="hidden">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Durasi Hari <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="durasi_hari" id="durasiHari" value="" min="1"
                            oninput="updateTanggalSelesai(); hitungBiayaDasar()" placeholder="Jumlah hari"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    {{-- -- TANGGAL SELESAI -- --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Selesai</label>
                        <input type="text" name="tanggal_selesai" id="tanggalSelesai" readonly
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500" value="{{ old('tanggal_selesai') }}">
                    </div>

                    {{-- -- HARGA KENDARAAN -- --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga Kendaraan</label>
                        <div class="border border-gray-200 rounded-lg px-3 py-2.5 bg-blue-50">
                            <p class="text-xs text-gray-500">Harga Per Hari</p>
                            <p class="text-sm font-bold text-blue-700 mt-0.5">Rp <span id="hargaHariText">0</span></p>
                        </div>
                    </div>

                    <!-- SECTION DRIVER: muncul hanya saat Per Hari -->
                    <div id="section-driver" class="md:col-span-2 hidden">
                        <div class="border border-blue-100 bg-blue-50/40 rounded-xl p-4">

                            <!-- Header -->
                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-blue-100">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-car text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">Informasi Perjalanan & Driver</p>
                                    <p class="text-xs text-gray-500">Tujuan wajib � Data driver bisa dikosongkan</p>
                                </div>
                                <span
                                    class="ml-auto text-xs font-medium px-2 py-1 rounded-full bg-blue-100 text-blue-700">Per
                                    Hari</span>
                            </div>

                            <!-- Tujuan Perjalanan (wajib untuk harian) -->
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Tujuan Perjalanan <span class="text-red-500">*</span>
                                </label>
                                <select name="tujuan_perjalanan" id="input_tujuan_perjalanan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">-- Pilih Tujuan --</option>
                                    <option value="dalam_kota" {{ old('tujuan_perjalanan') == 'dalam_kota' ? 'selected' : '' }}>Dalam Kota</option>
                                    <option value="luar_kota"  {{ old('tujuan_perjalanan') == 'luar_kota'  ? 'selected' : '' }}>Luar Kota</option>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">
                                    <i class="fa fa-info-circle text-blue-400"></i> Wajib diisi untuk rental harian
                                </p>
                            </div>

                            <!-- Driver (opsional) -->
                            <div class="bg-white/70 rounded-lg p-3 border border-blue-100">
                                <p class="text-xs font-semibold text-gray-600 mb-3 flex items-center gap-1.5">
                                    <i class="fa fa-user text-gray-400"></i> Data Driver
                                    <span class="font-normal text-gray-400">(opsional)</span>
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Driver</label>
                                        <input type="text" name="nama_driver"
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                                            placeholder="Nama lengkap driver" value="{{ old('nama_driver') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kontak Driver</label>
                                        <input type="number" name="kontak_driver" maxlength="15"
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                                            placeholder="Nomor HP / WhatsApp" value="{{ old('kontak_driver') }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        Biaya Driver <span class="text-gray-400 font-normal">(per hari)</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                        <input type="number" name="biaya_driver" id="biaya_driver" value="0"
                                            min="0" max="9999999999"
                                            class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm"
                                            oninput="hitungBiayaDasar()">
                                    </div>
                                </div>

                                <!-- Pengantaran & Penjemputan (opsional) -->
                                <div class="border-t border-gray-100 pt-3 mt-1">
                                    <p class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-1.5">
                                        <i class="fa fa-location-dot text-gray-400"></i>
                                        Pengantaran &amp; Penjemputan
                                        <span class="font-normal text-gray-400">(opsional)</span>
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                                <i class="fa fa-arrow-right text-green-500 text-[10px]"></i>
                                                Alamat Pengantaran
                                            </label>
                                            <input type="text" name="alamat_pengantaran"
                                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                                                placeholder="Contoh: Jl. Merdeka No. 10"
                                                value="{{ old('alamat_pengantaran') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                                <i class="fa fa-arrow-left text-orange-500 text-[10px]"></i>
                                                Alamat Penjemputan
                                            </label>
                                            <input type="text" name="alamat_penjemputan"
                                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                                                placeholder="Contoh: Bandara Soekarno-Hatta"
                                                value="{{ old('alamat_penjemputan') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ringkasan biaya driver -->
                            <div id="ringkasanDriver" class="hidden mt-3 bg-blue-100/60 rounded-lg px-4 py-3">
                                <div class="flex justify-between text-xs text-blue-800 mb-1">
                                    <span>Biaya kendaraan</span>
                                    <span id="ringkasanKendaraan">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-xs text-blue-800 mb-2">
                                    <span>Biaya driver</span>
                                    <span id="ringkasanBiayaDriver">Rp 0</span>
                                </div>
                                <div
                                    class="flex justify-between text-sm font-semibold text-blue-900 border-t border-blue-200 pt-2">
                                    <span>Total</span>
                                    <span id="ringkasanTotal">Rp 0</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- -- BIAYA DASAR -- --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya Dasar</label>
                        <div class="border border-gray-200 rounded-lg px-4 py-3 bg-green-50">
                            <p class="text-xs text-gray-500 mb-1">Perhitungan: <span id="rumusBiaya"
                                    class="text-gray-700">0 x 0</span></p>
                            <p class="text-xl font-bold text-green-700">Rp <span id="biayaDasarText">0</span></p>
                        </div>
                    </div>

                    {{-- -- METODE & JENIS PEMBAYARAN -- --}}
                    <select name="metode_pembayaran" id="metodePembayaran" class="border rounded px-3 py-2 text-sm">
                        <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    </select>

                    <select name="jenis_pembayaran" id="jenisPembayaran" onchange="toggleJenisPembayaran()"
                        class="border rounded px-3 py-2 text-sm">
                        <option value="lunas" {{ old('jenis_pembayaran') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="dp" {{ old('jenis_pembayaran') == 'dp' ? 'selected' : '' }}>DP</option>
                    </select>

                    {{-- -- BUKTI LUNAS (drag-drop) -- --}}
                    <div id="formLunas" class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Bukti Transfer Lunas</label>

                        <div id="drop-lunas"
                            class="w-full border-2 border-dashed border-blue-400 bg-blue-50 rounded-xl p-6
                               flex flex-col items-center justify-center text-center cursor-pointer
                               transition hover:bg-blue-100">
                            <input type="file" name="bukti_lunas" id="bukti_lunas" class="hidden"
                                onchange="previewFile(event,'preview_lunas','drop-lunas')">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                                <i class="fas fa-cloud-upload-alt text-2xl text-blue-600"></i>
                            </div>
                            <p class="text-sm font-semibold text-blue-700">Drag & Drop File</p>
                            <p class="text-xs text-gray-500 mt-1">atau <span class="font-semibold text-blue-600">klik di
                                    sini</span></p>
                            <p class="text-xs text-gray-400 mt-1"></p>
                        </div>
                        <div class="mt-3 flex justify-center">
                            <img id="preview_lunas"
                                class="hidden w-32 h-32 object-cover rounded-lg border border-blue-300 shadow-sm"
                                alt="Preview Lunas">
                        </div>
                    </div>

                    {{-- -- BUKTI DP -- --}}
                    <div id="formDP" class="hidden md:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nominal DP</label>
                                <input type="number" id="nominal_dp" name="nominal_dp" max="9999999999"
                                    class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Nominal DP"
                                    oninput="hitungPelunasan()" value="{{ old('nominal_dp') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Sisa Pelunasan</label>
                                <input type="number" id="sisa_pelunasan"
                                    class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-100" readonly>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Bukti DP</label>
                            <div id="drop-dp"
                                class="w-full border-2 border-dashed border-yellow-400 bg-yellow-50 rounded-xl p-6
                                   flex flex-col items-center justify-center text-center cursor-pointer
                                   transition hover:bg-yellow-100">
                                <input type="file" name="bukti_dp" id="bukti_dp" class="hidden"
                                    onchange="previewFile(event,'preview_dp','drop-dp')">
                                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center mb-3">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-yellow-600"></i>
                                </div>
                                <p class="text-sm font-semibold text-yellow-700">Drag & Drop File</p>
                                <p class="text-xs text-gray-500 mt-1">atau <span
                                        class="font-semibold text-yellow-600">klik di sini</span></p>
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, PDF (Maks 5MB)</p>
                            </div>
                            <div class="mt-3 flex justify-center">
                                <img id="preview_dp"
                                    class="hidden w-32 h-32 object-cover rounded-lg border border-yellow-300 shadow-sm"
                                    alt="Preview DP">
                            </div>
                        </div>
                    </div>

                    {{-- ----------------------------------------
                     DOKUMEN: INVOICE & KELAYAKAN
                     
                ---------------------------------------- --}}
                    <div class="md:col-span-2">
                        <div class="border-t border-gray-100 pt-4 mb-3">
                            <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wide">
                                <i class="fa fa-folder-open text-blue-400 mr-1"></i> Dokumen Pendukung
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Invoice & surat kelayakan (opsional, bisa diisi nanti)
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- -- INVOICE -- --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-gray-600">Invoice</label>
                                    <span id="invoice-badge"
                                        class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                                        Opsional
                                    </span>
                                </div>
                                <div id="drop-invoice"
                                    class="w-full border-2 border-dashed border-indigo-400 bg-indigo-50 rounded-xl p-5
                                       flex flex-col items-center justify-center text-center cursor-pointer
                                       transition hover:bg-indigo-100">
                                    <input type="file" name="invoice" id="file_invoice" class="hidden"
                                        onchange="previewDokumen(event,'prev-invoice','drop-invoice','nama-invoice')">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mb-2">
                                        <i class="fas fa-file-invoice text-xl text-indigo-600"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-indigo-700">Drag & Drop Invoice</p>
                                    <p class="text-xs text-gray-400 mt-1"></p>

                                    {{-- Nama file terpilih --}}
                                    <p id="nama-invoice"
                                        class="hidden mt-2 text-xs font-medium text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full max-w-full truncate">
                                    </p>
                                </div>
                                {{-- Preview gambar invoice (jika jpg/png) --}}
                                <div class="mt-2 flex justify-center">
                                    <img id="prev-invoice"
                                        class="hidden w-28 h-28 object-cover rounded-lg border border-indigo-300 shadow-sm"
                                        alt="Preview Invoice">
                                </div>
                            </div>

                            {{-- -- KELAYAKAN -- --}}
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-gray-600">
                                        Surat Kelayakan <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-medium">
                                        Wajib
                                    </span>
                                </div>
                                <div id="drop-kelayakan"
                                    class="w-full border-2 border-dashed border-teal-400 bg-teal-50 rounded-xl p-5
                                       flex flex-col items-center justify-center text-center cursor-pointer
                                       transition hover:bg-teal-100">
                                    <input type="file" name="kelayakan" id="file_kelayakan" class="hidden"
                                        onchange="previewDokumen(event,'prev-kelayakan','drop-kelayakan','nama-kelayakan')">
                                    <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mb-2">
                                        <i class="fas fa-file-shield text-xl text-teal-600"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-teal-700">Drag & Drop Kelayakan</p>
                                    <p class="text-xs text-gray-400 mt-1"></p>

                                    {{-- Nama file terpilih --}}
                                    <p id="nama-kelayakan"
                                        class="hidden mt-2 text-xs font-medium text-teal-600 bg-teal-100 px-2 py-1 rounded-full max-w-full truncate">
                                    </p>
                                </div>
                                {{-- Preview gambar kelayakan (jika jpg/png) --}}
                                <div class="mt-2 flex justify-center">
                                    <img id="prev-kelayakan"
                                        class="hidden w-28 h-28 object-cover rounded-lg border border-teal-300 shadow-sm"
                                        alt="Preview Kelayakan">
                                </div>
                                <p id="kelayakan-error" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                    <i class="fa fa-exclamation-circle"></i> Surat kelayakan wajib diupload
                                </p>
                            </div>

                        </div>
                    </div>

                </div>{{-- end grid --}}

                {{-- SUBMIT --}}
                <div class="flex justify-end gap-2 mt-5">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors duration-150">
                        <i class="fa fa-save text-sm"></i> Simpan Rental
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- ======================================================================
     SCRIPT TAMBAHAN � tempel di dalam <script> yang sudah ada
     
     ====================================================================== --}}
    <script>
        /* -------------------------------------------------------------------------
                                                                                           TIPE RENTAL: sekarang ada 3 pilihan ? tahun | bulan | hari
                                                                                           ------------------------------------------------------------------------- */
        function setTipeRental(tipe) {
            tipeRental = tipe;
            document.getElementById('tipe_rental').value = tipe;

            const fTahun = document.getElementById('formTahun');
            const fBulan = document.getElementById('formBulan');
            const fHari = document.getElementById('formHari');
            const dTahun = document.getElementById('durasiTahun');
            const dBulan = document.getElementById('durasiBulan');
            const dHari = document.getElementById('durasiHari');
            const btnTahun = document.getElementById('btnTahun');
            const btnBulan = document.getElementById('btnBulan');
            const btnHari = document.getElementById('btnHari');

            // Reset semua
            [fTahun, fBulan, fHari].forEach(el => el?.classList.add('hidden'));
            [dTahun, dBulan, dHari].forEach(el => {
                if (el) {
                    el.value = '';
                    el.disabled = true;
                }
            });
            [btnTahun, btnBulan, btnHari].forEach(btn => {
                btn?.classList.replace('bg-blue-600', 'bg-gray-100');
                btn?.classList.replace('text-white', 'text-gray-600');
            });

            // Aktifkan yang dipilih
            if (tipe === 'tahun') {
                fTahun?.classList.remove('hidden');
                if (dTahun) dTahun.disabled = false;
                btnTahun?.classList.replace('bg-gray-100', 'bg-blue-600');
                btnTahun?.classList.replace('text-gray-600', 'text-white');
            } else if (tipe === 'bulan') {
                fBulan?.classList.remove('hidden');
                if (dBulan) dBulan.disabled = false;
                btnBulan?.classList.replace('bg-gray-100', 'bg-blue-600');
                btnBulan?.classList.replace('text-gray-600', 'text-white');
            } else { // hari
                fHari?.classList.remove('hidden');
                if (dHari) dHari.disabled = false;
                btnHari?.classList.replace('bg-gray-100', 'bg-blue-600');
                btnHari?.classList.replace('text-gray-600', 'text-white');
            }

            // -- Tampilkan / sembunyikan section driver --  ? DIPINDAH KE SINI
            const sectionDriver = document.getElementById('section-driver');
            const inputTujuan = document.getElementById('input_tujuan');
            const invoiceBadge = document.getElementById('invoice-badge');
            const fileInvoice = document.getElementById('file_invoice');

            if (tipe === 'hari') {
                sectionDriver?.classList.remove('hidden');
                if (inputTujuan) inputTujuan.required = true;
                if (invoiceBadge) {
                    invoiceBadge.textContent = 'Opsional';
                    invoiceBadge.className = 'text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700';
                }
                if (fileInvoice) fileInvoice.required = false;
            } else {
                sectionDriver?.classList.add('hidden');
                if (inputTujuan) {
                    inputTujuan.required = false;
                    inputTujuan.value = '';
                }
                document.querySelector('[name="nama_driver"]') && (document.querySelector('[name="nama_driver"]').value =
                    '');
                document.querySelector('[name="kontak_driver"]') && (document.querySelector('[name="kontak_driver"]')
                    .value = '');
                const bd = document.getElementById('biaya_driver');
                if (bd) bd.value = 0;
                if (invoiceBadge) {
                    invoiceBadge.textContent = 'Wajib';
                    invoiceBadge.className = 'text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700';
                }
                if (fileInvoice) fileInvoice.required = true;
            }

            hitungBiayaDasar();
            updateTanggalSelesai();
        }



        /* -------------------------------------------------------------------------
           HITUNG BIAYA DASAR � support tahun | bulan | hari
           ------------------------------------------------------------------------- */
        function hitungBiayaDasar() {
            let total = 0,
                rumus = '';

            if (tipeRental === 'tahun') {
                const tahun = parseInt(document.getElementById('durasiTahun')?.value) || 0;
                total = tahun * 12 * 30 * hargaHari;
                rumus = `${tahun} Tahun (x12 Bulan x30 Hari) x Rp ${hargaHari.toLocaleString('id-ID')}`;
            } else if (tipeRental === 'bulan') {
                const bulan = parseInt(document.getElementById('durasiBulan')?.value) || 0;
                total = bulan * 30 * hargaHari;
                rumus = `${bulan} Bulan (x30 Hari) x Rp ${hargaHari.toLocaleString('id-ID')}`;
            } else {
                const hari = parseInt(document.getElementById('durasiHari')?.value) || 0;
                total = hari * hargaHari;
                rumus = `${hari} Hari x Rp ${hargaHari.toLocaleString('id-ID')}`;
            }

            const re = document.getElementById('rumusBiaya');
            const be = document.getElementById('biayaDasarText');
            if (re) re.innerText = rumus;
            if (be) be.innerText = total.toLocaleString('id-ID');
            biayaDasar = total;

            // -- Tambahkan biaya driver ke total (khusus Per Hari) --
            const biayaDriverVal = parseInt(document.getElementById('biaya_driver')?.value) || 0;
            const hariVal = parseInt(document.getElementById('durasiHari')?.value) || 0;
            const totalDriver = (tipeRental === 'hari') ? biayaDriverVal * hariVal : 0;
            const grandTotal = total + totalDriver;

            // Update ringkasan driver
            const ringkasan = document.getElementById('ringkasanDriver');
            if (tipeRental === 'hari' && (total > 0 || totalDriver > 0)) {
                document.getElementById('ringkasanKendaraan').textContent =
                    'Rp ' + total.toLocaleString('id-ID');
                document.getElementById('ringkasanBiayaDriver').textContent =
                    'Rp ' + totalDriver.toLocaleString('id-ID');
                document.getElementById('ringkasanTotal').textContent =
                    'Rp ' + grandTotal.toLocaleString('id-ID');
                ringkasan?.classList.remove('hidden');
            } else {
                ringkasan?.classList.add('hidden');
            }

            // Override biayaDasar dengan grand total (termasuk driver)
            biayaDasar = grandTotal;
            if (be) be.innerText = grandTotal.toLocaleString('id-ID');
            hitungPelunasan();
        }

        /* -------------------------------------------------------------------------
           UPDATE TANGGAL SELESAI � support tahun
           ------------------------------------------------------------------------- */
        function updateTanggalSelesai() {
            const mulai = fpInstance ?
                fpInstance.input.value :
                document.getElementById('tanggalMulai')?.value;
            if (!mulai) return;

            let tgl = new Date(mulai.replace(' ', 'T'));

            if (tipeRental === 'tahun') {
                const tahun = parseInt(document.getElementById('durasiTahun')?.value) || 0;
                tgl.setFullYear(tgl.getFullYear() + tahun);
            } else if (tipeRental === 'bulan') {
                const bulan = parseInt(document.getElementById('durasiBulan')?.value) || 0;
                tgl.setMonth(tgl.getMonth() + bulan);
            } else {
                const hari = parseInt(document.getElementById('durasiHari')?.value) || 0;
                tgl.setDate(tgl.getDate() + hari);
            }

            const fmt =
                `${tgl.getFullYear()}-${String(tgl.getMonth()+1).padStart(2,'0')}-${String(tgl.getDate()).padStart(2,'0')} ${String(tgl.getHours()).padStart(2,'0')}:${String(tgl.getMinutes()).padStart(2,'0')}:00`;
            const el = document.getElementById('tanggalSelesai');
            if (el) el.value = fmt;

            hitungBiayaDasar();
            validasiRentang();
        }

        /* -------------------------------------------------------------------------
           PREVIEW BUKTI BAYAR (lunas / dp) � tetap sama
           ------------------------------------------------------------------------- */
        function previewFile(event, previewId, dropId) {
            const file = event.target.files[0];
            const preview = document.getElementById(previewId);
            const drop = document.getElementById(dropId);
            if (!file || !preview) return;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                // PDF/doc: tampilkan nama file di area drop
                preview.classList.add('hidden');
                const label = drop?.querySelector('p.text-sm');
                if (label) label.textContent = '? ' + file.name;
            }
        }

        // Pasang event drag-drop untuk bukti lunas & dp
        (function() {
            function initDrop(dropId, inputId, previewId) {
                const drop = document.getElementById(dropId);
                const input = document.getElementById(inputId);
                if (!drop || !input) return;

                drop.addEventListener('click', () => input.click());

                drop.addEventListener('dragover', e => {
                    e.preventDefault();
                    drop.classList.add('opacity-80', 'scale-[1.01]');
                });
                drop.addEventListener('dragleave', () => {
                    drop.classList.remove('opacity-80', 'scale-[1.01]');
                });
                drop.addEventListener('drop', e => {
                    e.preventDefault();
                    drop.classList.remove('opacity-80', 'scale-[1.01]');
                    if (e.dataTransfer.files.length) {
                        input.files = e.dataTransfer.files;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            }

            initDrop('drop-lunas', 'bukti_lunas', 'preview_lunas');
            initDrop('drop-dp', 'bukti_dp', 'preview_dp');
        })();

        /* -------------------------------------------------------------------------
           PREVIEW DOKUMEN (invoice & kelayakan)
           Bedanya: kalau PDF/doc, tampilkan nama file (bukan icon PDF) di bubble
           ------------------------------------------------------------------------- */
        function previewDokumen(event, previewId, dropId, namaId) {
            const file = event.target.files[0];
            const preview = document.getElementById(previewId);
            const namaEl = document.getElementById(namaId);
            if (!file) return;

            // Sembunyikan dulu
            if (preview) preview.classList.add('hidden');
            if (namaEl) namaEl.classList.add('hidden');

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            } else {
                // PDF / doc / xls ? tampilkan nama file
                if (namaEl) {
                    namaEl.textContent = '? ' + file.name;
                    namaEl.classList.remove('hidden');
                }
            }
        }

        // Pasang event drag-drop untuk invoice & kelayakan
        (function() {
            function initDropDokumen(dropId, inputId, previewId, namaId) {
                const drop = document.getElementById(dropId);
                const input = document.getElementById(inputId);
                if (!drop || !input) return;

                drop.addEventListener('click', () => input.click());

                drop.addEventListener('dragover', e => {
                    e.preventDefault();
                    drop.classList.add('opacity-80');
                });
                drop.addEventListener('dragleave', () => {
                    drop.classList.remove('opacity-80');
                });
                drop.addEventListener('drop', e => {
                    e.preventDefault();
                    drop.classList.remove('opacity-80');
                    if (e.dataTransfer.files.length) {
                        input.files = e.dataTransfer.files;
                        // Trigger onchange
                        previewDokumen({
                            target: input
                        }, previewId, dropId, namaId);
                    }
                });
            }

            initDropDokumen('drop-invoice', 'file_invoice', 'prev-invoice', 'nama-invoice');
            initDropDokumen('drop-kelayakan', 'file_kelayakan', 'prev-kelayakan', 'nama-kelayakan');
        })();

        // -- VALIDASI KELAYAKAN WAJIB -----------------------
        document.getElementById('rentalStoreForm').addEventListener('submit', function(e) {
            const kelayakanInput = document.getElementById('file_kelayakan');
            const errorEl = document.getElementById('kelayakan-error');
            const dropEl  = document.getElementById('drop-kelayakan');

            if (!kelayakanInput.files || kelayakanInput.files.length === 0) {
                e.preventDefault();

                // Tampilkan pesan error
                errorEl.classList.remove('hidden');

                // Highlight drop zone merah
                dropEl.classList.add('border-red-400', 'bg-red-50');
                dropEl.classList.remove('border-teal-400', 'bg-teal-50');

                // Scroll ke drop zone
                dropEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                errorEl.classList.add('hidden');
                dropEl.classList.remove('border-red-400', 'bg-red-50');
                dropEl.classList.add('border-teal-400', 'bg-teal-50');
            }
        });

        // Hilangkan error saat file dipilih
        document.getElementById('file_kelayakan').addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                const errorEl = document.getElementById('kelayakan-error');
                const dropEl  = document.getElementById('drop-kelayakan');
                errorEl.classList.add('hidden');
                dropEl.classList.remove('border-red-400', 'bg-red-50');
                dropEl.classList.add('border-teal-400', 'bg-teal-50');
            }
        });
    </script>


    {{-- POPUP ALERT --}}
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
                @else
                    <div
                        class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        @if (session('error'))
                            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                        @else
                            <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <button onclick="closeAlert()"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    @endif


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

        /* Tab active state */
        .tab-btn.tab-active-semua {
            border-color: #3b82f6;
            color: #2563eb;
            background: rgba(219, 234, 254, .4);
        }

        .tab-btn.tab-active-Pending {
            border-color: #6b7280;
            color: #374151;
            background: rgba(243, 244, 246, .6);
        }

        .tab-btn.tab-active-booking {
            border-color: #3b82f6;
            color: #2563eb;
            background: rgba(219, 234, 254, .4);
        }

        .tab-btn.tab-active-aktif {
            border-color: #22c55e;
            color: #16a34a;
            background: rgba(220, 252, 231, .4);
        }

        .tab-btn.tab-active-selesai {
            border-color: #1e293b;
            color: #1e293b;
            background: rgba(241, 245, 249, .6);
        }

        .tab-btn.tab-active-batal {
            border-color: #ef4444;
            color: #dc2626;
            background: rgba(254, 226, 226, .4);
        }

        /* Filter tipe active */
        .ft-btn.ft-active {
            background: #2563eb;
            color: #fff;
        }

        /* Member autocomplete item */
        .mr-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
        }

        .mr-item:hover {
            background: #f0f9ff;
        }
    </style>



    {{-- SCRIPT --}}
    <script>
        /* -----------------------------
            STATE
        ---------------------------- */
        let tipeRental = 'bulan';
        let hargaHari = 0;
        let biayaDasar = 0;
        let fpInstance = null; // Flatpickr instance

        /* -----------------------------
            BOOKED DATES (dari server)
        ---------------------------- */
        // Format: { "kendaraan_id": [{ mulai: "...", selesai: "..." }, ...], ... }
        const bookedDates = @json($bookedDates ?? []);

        /**
         * Ambil array disable ranges untuk Flatpickr
         * berdasarkan kendaraan yang dipilih.
         */
        function getDisabledRanges(kendaraanId) {
            if (!kendaraanId || !bookedDates[kendaraanId]) return [];

            return bookedDates[kendaraanId].map(function(range) {
                return {
                    from: range.mulai.substring(0, 10), // YYYY-MM-DD
                    to: range.selesai.substring(0, 10),
                };
            });
        }


        /**
         * Cek apakah range [mulai, selesai] bertabrakan dengan booking yang ada
         * untuk kendaraan yang dipilih.
         * Return array booking yang tabrakan, atau [] jika aman.
         */
        function cekTabrakanBooking() {
            const sel = document.getElementById('kendaraanSelect');
            const kendaraanId = sel ? sel.value : null;
            if (!kendaraanId) return [];

            const mulaiVal = fpInstance ? fpInstance.input.value : document.getElementById('tanggalMulai')?.value;
            const selesaiVal = document.getElementById('tanggalSelesai')?.value;
            if (!mulaiVal || !selesaiVal) return [];

            const mulaiUser = mulaiVal.substring(0, 10); // YYYY-MM-DD
            const selesaiUser = selesaiVal.substring(0, 10);

            const ranges = getDisabledRanges(kendaraanId);

            // Tabrakan jika: mulaiUser < selesaiBooked DAN selesaiUser > mulaiBooked
            return ranges.filter(function(r) {
                return mulaiUser < r.to && selesaiUser > r.from;
            });
        }

        /**
         * Tampilkan / sembunyikan peringatan tabrakan.
         */
        function tampilkanPeringatan(tabrakan) {
            let el = document.getElementById('warningTabrakan');

            if (tabrakan.length === 0) {
                if (el) el.remove();
                return;
            }

            const detail = tabrakan.map(function(r) {
                return r.from + ' s/d ' + r.to;
            }).join(', ');

            if (!el) {
                el = document.createElement('div');
                el.id = 'warningTabrakan';
                el.className =
                    'md:col-span-2 flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-xs text-red-700';

                // Sisipkan setelah elemen biaya dasar
                const biayaEl = document.getElementById('biayaDasarText')?.closest('.md\\:col-span-2');
                if (biayaEl && biayaEl.parentNode) {
                    biayaEl.parentNode.insertBefore(el, biayaEl.nextSibling);
                }
            }

            el.innerHTML =
                '<i class="fa fa-triangle-exclamation text-red-500 mt-0.5 shrink-0"></i>' +
                '<div>' +
                '<p class="font-semibold text-red-700">Tanggal tidak tersedia!</p>' +
                '<p class="mt-0.5 text-red-600">Rentang tanggal yang Anda pilih bertabrakan dengan booking yang sudah ada: <strong>' +
                detail + '</strong>. Silakan ubah tanggal mulai atau durasi.</p>' +
                '</div>';
        }

        /**
         * Blokir / aktifkan tombol simpan.
         */
        function toggleSubmitBtn(disabled) {
            const btn = document.querySelector('button[type="submit"]');
            if (!btn) return;
            btn.disabled = disabled;
            if (disabled) {
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.classList.remove('hover:bg-blue-700');
            } else {
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.classList.add('hover:bg-blue-700');
            }
        }

        /**
         * Jalankan semua validasi setelah tanggal selesai terhitung.
         */
        function validasiRentang() {
            const tabrakan = cekTabrakanBooking();
            tampilkanPeringatan(tabrakan);
            toggleSubmitBtn(tabrakan.length > 0);
        }

        /**
         * Inisialisasi / re-inisialisasi Flatpickr pada input tanggal mulai.
         */
        function refreshDatePicker() {
            const sel = document.getElementById('kendaraanSelect');
            const kendaraanId = sel ? sel.value : null;
            const disabledRanges = getDisabledRanges(kendaraanId);

            // Hancurkan instance lama
            if (fpInstance) {
                fpInstance.destroy();
                fpInstance = null;
            }

            const inputEl = document.getElementById('tanggalMulai');
            if (!inputEl) return;

            fpInstance = flatpickr(inputEl, {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                minuteIncrement: 30,
                minDate: 'today',
                disable: disabledRanges,
                onChange: function() {
                    updateTanggalSelesai();
                },
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    // Tandai hari yang di-block dengan tooltip
                    const dateStr = dayElem.dateObj.toISOString().substring(0, 10);
                    const isBlocked = disabledRanges.some(function(r) {
                        return dateStr >= r.from && dateStr <= r.to;
                    });
                    if (isBlocked) {
                        dayElem.title = 'Tanggal sudah dipesan';
                    }
                }
            });
        }

        /* -----------------------------
            TAB SWITCHING
        ---------------------------- */
        const TAB_COLORS = {
            semua: {
                border: '#3b82f6',
                text: '#2563eb',
                bg: 'rgba(219,234,254,.4)',
                badge: 'bg-blue-100 text-blue-700'
            },
            Pending: {
                border: '#6b7280',
                text: '#374151',
                bg: 'rgba(243,244,246,.6)',
                badge: 'bg-gray-100 text-gray-600'
            },
            booking: {
                border: '#3b82f6',
                text: '#2563eb',
                bg: 'rgba(219,234,254,.4)',
                badge: 'bg-blue-100 text-blue-700'
            },
            aktif: {
                border: '#22c55e',
                text: '#16a34a',
                bg: 'rgba(220,252,231,.4)',
                badge: 'bg-green-100 text-green-700'
            },
            selesai: {
                border: '#1e293b',
                text: '#1e293b',
                bg: 'rgba(241,245,249,.6)',
                badge: 'bg-slate-100 text-slate-700'
            },
            batal: {
                border: '#ef4444',
                text: '#dc2626',
                bg: 'rgba(254,226,226,.4)',
                badge: 'bg-red-100 text-red-700'
            },
        };

        /* -----------------------------
            EXPORT PDF
        ---------------------------- */
        function exportData() {
            const params = new URLSearchParams(window.location.search);
            window.open('{{ route('rental.pdf') }}?' + params.toString(), '_blank');
        }

        /* -----------------------------
            MODAL
        ---------------------------- */
        const modalRental = document.getElementById('modalRental');

        function openModal() {
            modalRental?.classList.remove('hidden');
            modalRental?.classList.add('flex');
            // Init date picker saat modal dibuka (kendaraan belum dipilih ? semua terbuka)
            refreshDatePicker();
        }

        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (modalRental) {
                modalRental.classList.remove('hidden');
                modalRental.classList.add('flex');
                refreshDatePicker();
            }
        });
        @endif

        function closeModal() {
            modalRental?.classList.add('hidden');
            modalRental?.classList.remove('flex');
            if (fpInstance) {
                fpInstance.destroy();
                fpInstance = null;
            }
        }

        modalRental?.addEventListener('click', function(e) {
            if (e.target === modalRental) closeModal();
        });

        /* -----------------------------
            RENTAL LOGIC
        ---------------------------- */


        function setHargaKendaraan() {
            const sel = document.getElementById('kendaraanSelect');
            hargaHari = parseInt(sel?.options[sel.selectedIndex]?.dataset.hari) || 0;
            const el = document.getElementById('hargaHariText');
            if (el) el.innerText = hargaHari.toLocaleString('id-ID');

            const tanggalMulaiEl = document.getElementById('tanggalMulai');
            if (tanggalMulaiEl) tanggalMulaiEl.value = '';
            const tanggalSelesaiEl = document.getElementById('tanggalSelesai');
            if (tanggalSelesaiEl) tanggalSelesaiEl.value = '';

            // Reset warning & tombol saat kendaraan berganti
            tampilkanPeringatan([]);
            toggleSubmitBtn(false);

            refreshDatePicker();
            hitungBiayaDasar();
        }




        /* -----------------------------
            PAYMENT TOGGLE
        ---------------------------- */
        function toggleJenisPembayaran() {
            const jenis = document.getElementById('jenisPembayaran');
            const isLunas = jenis?.value === 'lunas';
            const fl = document.getElementById('formLunas');
            const fd = document.getElementById('formDP');
            if (fl) fl.style.display = isLunas ? 'block' : 'none';
            if (fd) fd.style.display = isLunas ? 'none' : 'block';
        }
        document.getElementById('jenisPembayaran')?.addEventListener('change', toggleJenisPembayaran);

        function hitungPelunasan() {
            const dp = parseFloat(document.getElementById('nominal_dp')?.value) || 0;
            const sisa = biayaDasar - dp;
            const el = document.getElementById('sisa_pelunasan');
            if (el) el.value = sisa >= 0 ? sisa : 0;
        }

        /* -----------------------------
            MEMBER AUTOCOMPLETE
        ---------------------------- */
        const members = @json($pelangganJson);
        const memberInput = document.getElementById('nama_pelanggan');
        const memberResult = document.getElementById('member-result');

        if (memberInput && memberResult) {
            memberInput.addEventListener('keyup', function() {
                const keyword = this.value.toLowerCase().trim();
                memberResult.innerHTML = '';
                const mid = document.getElementById('member_id');
                if (mid) mid.value = '';
                if (keyword.length < 2) {
                    memberResult.classList.add('hidden');
                    return;
                }
                const filtered = members.filter(function(m) {
                    return m.nama_pelanggan.toLowerCase().includes(keyword);
                });
                if (filtered.length === 0) {
                    memberResult.innerHTML =
                        '<div class="px-3 py-2 text-gray-500 text-xs">Pelanggan baru akan dibuat</div>';
                    memberResult.classList.remove('hidden');
                    return;
                }
                filtered.forEach(function(member) {
                    const item = document.createElement('div');
                    item.className = 'mr-item';
                    item.innerHTML = '<strong style="font-size:13px;">' + member.nama_pelanggan +
                        '</strong><br><small style="color:#6b7280;">' + (member.kontak_pelanggan ?? '') +
                        '</small>';
                    item.onclick = function() {
                        document.getElementById('member_id').value = member.id;
                        memberInput.value = member.nama_pelanggan;
                        document.getElementById('kontak_pelanggan').value = member.kontak_pelanggan ?? '';
                        document.getElementById('alamat_pelanggan').value = member.alamat ?? '';
                        document.getElementById('email_pelanggan').value = member.email_pelanggan ?? '';
                        document.getElementById('jenis_pelanggan').value = member.jenis_pelanggan ?? '';
                        memberResult.classList.add('hidden');
                    };
                    memberResult.appendChild(item);
                });
                memberResult.classList.remove('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!memberInput.contains(e.target) && !memberResult.contains(e.target)) {
                    memberResult.classList.add('hidden');
                }
            });
        }

        /* -----------------------------
            ALERT POPUP
        ---------------------------- */
        (function() {
            const overlay = document.getElementById('alertOverlay');
            const box = document.getElementById('alertBox');
            if (!overlay || !box) return;
            setTimeout(function() {
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
                box.style.transform = 'translateY(0)';
            }, 80);
            const timer = setTimeout(closeAlert, 4500);
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

        /* -----------------------------
            INIT
        ---------------------------- */
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('nominal_dp')?.addEventListener('input', hitungPelunasan);
            toggleJenisPembayaran();
        });
    </script>

    <script>
        const rentalData = {!! json_encode(
            $rentals->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'status' => $r->status,
                        'jenis_pembayaran' => $r->jenis_pembayaran,
                        'bukti_lunas' => $r->bukti_lunas ? true : false,
                        'bukti_dp' => $r->bukti_dp ? true : false,
                        'bukti_pelunasan' => $r->bukti_pelunasan ? true : false,
                    ];
                })->keyBy('id'),
        ) !!};

        const updateStatusUrl = "{{ url('admin/rental') }}";

        const STATUS_COLORS = {
            'Pending': 'bg-gray-500',
            'booking': 'bg-blue-500',
            'aktif': 'bg-green-500',
            'selesai': 'bg-gray-800',
            'batal': 'bg-red-500',
        };

        const STATUS_LABELS = {
            'Pending': 'PENDING',
            'booking': 'BOOKING',
            'aktif': 'AKTIF',
            'selesai': 'SELESAI',
            'batal': 'BATAL',
        };

        function openStatusModal(id, status) {
            document.getElementById('statusRentalId').value = id;

            const badge = document.getElementById('statusCurrentBadge');
            badge.textContent = STATUS_LABELS[status] || status.toUpperCase();
            badge.className = 'px-2.5 py-1 rounded-full text-white text-xs font-semibold ' +
                (STATUS_COLORS[status] || 'bg-gray-500');

            document.getElementById('warningBukti').classList.add('hidden');

            const options = document.getElementById('statusOptions');
            options.innerHTML = '';

            const rental = rentalData[id];

            if (status === 'Pending') {
                const punyaBukti = rental.bukti_lunas || rental.bukti_dp || rental.bukti_pelunasan;
                options.appendChild(buatTombolStatus(
                    'Booking', 'fa-calendar-check', 'bg-blue-600 hover:bg-blue-700', id,
                    !punyaBukti, 'Belum ada bukti pembayaran. Upload dulu di halaman detail.'
                ));
                options.appendChild(buatTombolStatus(
                    'Batal', 'fa-times-circle', 'bg-red-600 hover:bg-red-700', id
                ));

            } else if (status === 'booking') {
                var bolehAktif = false;
                var pesanWarning = '';
                if (rental.jenis_pembayaran === 'lunas') {
                    bolehAktif = rental.bukti_lunas;
                    pesanWarning = 'Pembayaran LUNAS, tapi bukti belum diupload. Upload dulu di halaman detail.';
                } else if (rental.jenis_pembayaran === 'dp') {
                    bolehAktif = rental.bukti_dp;
                    pesanWarning = 'Pembayaran DP, tapi bukti DP belum diupload. Upload dulu di halaman detail.';
                } else {
                    bolehAktif = true;
                }
                options.appendChild(buatTombolStatus(
                    'Aktif', 'fa-check-circle', 'bg-green-600 hover:bg-green-700', id,
                    !bolehAktif, pesanWarning
                ));
                options.appendChild(buatTombolStatus(
                    'Batal', 'fa-times-circle', 'bg-red-600 hover:bg-red-700', id
                ));

            } else if (status === 'aktif') {
                options.appendChild(buatTombolStatus(
                    'Selesai', 'fa-flag-checkered', 'bg-gray-800 hover:bg-gray-900', id
                ));
                options.appendChild(buatTombolStatus(
                    'Batal', 'fa-times-circle', 'bg-red-600 hover:bg-red-700', id
                ));

            } else {
                options.innerHTML = '<p class="text-xs text-gray-500 text-center py-3">Status <strong>' +
                    (STATUS_LABELS[status] || status) +
                    '</strong> tidak dapat diubah lagi.</p>';
            }

            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusModal').classList.add('flex');
        }

        function buatTombolStatus(label, icon, colorClass, rentalId, disabled, warningMsg) {
            disabled = disabled || false;
            warningMsg = warningMsg || '';

            var btn = document.createElement('button');
            btn.type = 'button';

            if (disabled) {
                btn.className =
                    'flex items-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white opacity-40 cursor-not-allowed bg-gray-400';
                btn.disabled = true;
                btn.title = warningMsg;
                btn.innerHTML = '<i class="fa ' + icon + '"></i> Ubah ke ' + label;
                btn.addEventListener('mouseenter', function() {
                    document.getElementById('warningBuktiText').textContent = warningMsg;
                    document.getElementById('warningBukti').classList.remove('hidden');
                });
                btn.addEventListener('mouseleave', function() {
                    document.getElementById('warningBukti').classList.add('hidden');
                });
            } else {
                btn.className =
                    'flex items-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition-colors ' +
                    colorClass;
                btn.innerHTML = '<i class="fa ' + icon + '"></i> Ubah ke ' + label;
                btn.onclick = function() {
                    saveStatus(rentalId, label.toLowerCase());
                };
            }

            return btn;
        }

        function saveStatus(id, status) {
            var statusMap = {
                booking: 'booking',
                aktif: 'aktif',
                selesai: 'selesai',
                batal: 'batal'
            };
            var statusValue = statusMap[status] || status;

            // Tampilkan loading di tombol yang diklik
            var btn = event.target.closest('button');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
            }

            fetch(updateStatusUrl + '/' + id + '/update-status2', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', // ? tambah ini
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: statusValue
                    })
                })
                .then(function(res) {
                    if (!res.ok) throw new Error('Request gagal: ' + res.status);
                    return res.json();
                })
                .then(function(data) {
                    if (data.success) {
                        closeStatusModal();
                        tampilkanToast(data.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1200);
                    } else {
                        tampilkanToast(data.message || 'Gagal update status', 'error');
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fa fa-save"></i> Simpan';
                        }
                    }
                })
                .catch(function(err) {
                    tampilkanToast('Terjadi kesalahan: ' + err.message, 'error');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa fa-save"></i> Simpan';
                    }
                });
        }

        function tampilkanToast(pesan, tipe) {
            var existing = document.getElementById('toastNotif');
            if (existing) existing.remove();

            var warna = tipe === 'success' ?
                'bg-green-600' :
                'bg-red-600';
            var icon = tipe === 'success' ?
                'fa-check-circle' :
                'fa-exclamation-circle';

            var toast = document.createElement('div');
            toast.id = 'toastNotif';
            toast.className = 'fixed bottom-6 right-6 z-[9999] flex items-center gap-3 ' +
                warna + ' text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg ' +
                'transition-all duration-300 opacity-0 translate-y-4';
            toast.innerHTML = '<i class="fa ' + icon + '"></i><span>' + pesan + '</span>';

            document.body.appendChild(toast);

            // Animasi masuk
            setTimeout(function() {
                toast.classList.remove('opacity-0', 'translate-y-4');
            }, 50);

            // Animasi keluar
            setTimeout(function() {
                toast.classList.add('opacity-0', 'translate-y-4');
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 1000);
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
            document.getElementById('statusModal').classList.remove('flex');
        }
    </script>

<script>
// ========================================
// CHART INITIALIZATION
// ========================================
const rentalChartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function() {
    initRentalCharts({ filter_type: 'month' });

    document.addEventListener('chartFilterChange', function(e) {
        if (e.detail.filterId === 'rentalChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
            };
            updateRentalCharts(filters);
        }
    });
});

async function initRentalCharts(filters) {
    try {
        await rentalChartManager.initChartsFromAPI('rental', {
            pie:  'rentalPieChart',
            bar:  'rentalBarChart',
            line: 'rentalLineChart',
        }, filters, { accentLine: true });
    } catch (error) {
        console.error('Error loading rental charts:', error);
    }
}

async function updateRentalCharts(filters) {
    try {
        const barOptions = { scrollable: filters.filter_type === 'custom', accentLine: true };
        await rentalChartManager.updateChartsFromAPI('rental', {
            pie:  'rentalPieChart',
            bar:  'rentalBarChart',
            line: 'rentalLineChart',
        }, filters, barOptions);
    } catch (error) {
        console.error('Error updating rental charts:', error);
    }
}
</script>

@endsection


