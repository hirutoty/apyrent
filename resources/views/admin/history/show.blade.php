@extends('admin.layouts.app')

@section('title', 'History Rental - ' . $kendaraan->merk)

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">History Rental</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $kendaraan->merk }} &mdash;
                    <span
                        class="font-mono bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs">{{ $kendaraan->nopol }}</span>
                </p>
            </div>
            <a href="{{ route('history.index') }}"
                class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors duration-150 mt-2 sm:mt-0">
                <i class="fa fa-arrow-left text-sm"></i>
                Kembali
            </a>
        </div>

        {{-- SUMMARY CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-4 w-fit">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fa fa-history text-blue-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total Rental</p>
                <p class="text-2xl font-bold text-blue-600">{{ $rentals->total() }}</p>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- TOOLBAR --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Transaksi Rental</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $rentals->total() }} total transaksi</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" class="flex flex-wrap items-center gap-1.5">
                        {{-- Search --}}
                        <div class="relative">
                            <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari pelanggan..."
                                class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                        </div>
                        {{-- Filter Status --}}
                        <div class="relative">
                            <i class="fa fa-filter absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <select name="status"
                                class="pl-7 pr-6 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 appearance-none bg-white cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                                <option value="booking"  {{ request('status') == 'booking'  ? 'selected' : '' }}>Booking</option>
                                <option value="aktif"    {{ request('status') == 'aktif'    ? 'selected' : '' }}>Aktif</option>
                                <option value="selesai"  {{ request('status') == 'selesai'  ? 'selected' : '' }}>Selesai</option>
                                <option value="batal"    {{ request('status') == 'batal'    ? 'selected' : '' }}>Batal</option>
                            </select>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-700 transition-colors">
                            Cari
                        </button>
                        @if(request('search') || request('status'))
                        <a href="{{ route('history.show', $kendaraan->id) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 transition-colors">
                            Reset
                        </a>
                        @endif
                    </form>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
                    <a href="{{ route('history.export.pdf', $kendaraan->id) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">ID
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Pelanggan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kontak Pelanggan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Jenis Pelanggan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Nama Driver
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Tanggal</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Durasi</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Jenis Sewa</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Tujuan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Pengantaran</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Penjemputan</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Total</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Bukti Pembayaran</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status Pembayaran</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody id="rentalTableBody">
                        @forelse($rentals as $r)
                            <tr class="border-t border-gray-50 transition-colors duration-100 {{ $r->status == 'aktif' ? 'bg-blue-200/50 hover:bg-blue-50' : 'hover:bg-gray-50' }}">

                                {{-- NO --}}
                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium">{{ $rentals->firstItem() + $loop->index }}</td>

                                {{-- ID --}}
                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">#{{ $r->id }}</span>
                                </td>

                                {{-- MEMBER --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($r->member->nama_pelanggan ?? 'U', 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $r->member->nama_pelanggan ?? '-' }}</span>
                                    </div>
                                </td>

                                
                                
                                <td class="px-4 py-3.5">
                                    <span class="text-xs text-gray-400">
                                        {{ $r->member->kontak_pelanggan ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="text-xs text-gray-400">
                                        {{ $r->member->jenis_pelanggan ?? '-' }}
                                    </span>
                                </td>
                                
                                    <td class="px-4 py-3.5">
                                        {{ $r->nama_driver ?? '-' }}
                                    </td>

                                <td class="px-4 py-3.5">
                                    <div class="text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($r->tanggal_mulai)->format('d-m-Y H:i') }}
                                    </div>

                                    <div class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($r->tanggal_selesai)->format('d-m-Y H:i') }}
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="text-sm text-gray-700">
                                        @if ($r->durasi_jam)
                                            {{ $r->durasi_jam }} Jam
                                        @elseif($r->durasi_hari)
                                            {{ $r->durasi_hari }} Hari
                                        @elseif($r->durasi_bulan)
                                            {{ $r->durasi_bulan }} Bulan
                                        @elseif($r->durasi_tahun)
                                            {{ $r->durasi_tahun }} Tahun
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="text-xs text-gray-400">
                                        @if ($r->durasi_jam)
                                            Jam
                                        @elseif($r->durasi_hari)
                                            Harian
                                        @elseif($r->durasi_bulan)
                                            Bulanan
                                        @elseif($r->durasi_tahun)
                                            Tahunan
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    @if ($r->tujuan_perjalanan)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold
                                            {{ $r->tujuan_perjalanan === 'luar_kota' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                            
                                            {{ $r->tujuan_perjalanan === 'dalam_kota' ? 'Dalam Kota' : 'Luar Kota' }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300">-</span>
                                    @endif
                                </td>

                                {{-- PENGANTARAN --}}
                                <td class="px-4 py-3.5 text-xs text-gray-600 max-w-[150px]">
                                    @if ($r->alamat_pengantaran)
                                        <div class="flex items-start gap-1">
                                            
                                            <span>{{ $r->alamat_pengantaran }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>

                                {{-- PENJEMPUTAN --}}
                                <td class="px-4 py-3.5 text-xs text-gray-600 max-w-[150px]">
                                    @if ($r->alamat_penjemputan)
                                        <div class="flex items-start gap-1">
                                        
                                            <span>{{ $r->alamat_penjemputan }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>




                                {{-- BIAYA --}}
                                <td class="px-4 py-3.5">
                                    <div class="text-sm font-bold text-blue-600">Rp {{ number_format($r->total_biaya) }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        Driver: Rp {{ number_format($r->biaya_driver) }}<br>
                                    </div>
                                </td>

                                {{-- BUKTI --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-col gap-1.5">
                                        @if ($r->bukti_lunas)
                                            <button type="button"
                                                onclick="openSlideshow([{path:'{{ asset($r->bukti_lunas) }}', name:'Bukti Lunas'}], 0)"
                                                class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:text-blue-800 hover:underline">
                                                <i class="bi bi-image text-[10px]"></i>
                                                Lunas: {{ basename($r->bukti_lunas) }}
                                            </button>
                                        @endif
                                        @if ($r->bukti_dp)
                                            <button type="button"
                                                onclick="openSlideshow([{path:'{{ asset($r->bukti_dp) }}', name:'Bukti DP'}], 0)"
                                                class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:text-blue-800 hover:underline">
                                                <i class="bi bi-image text-[10px]"></i>
                                                DP: {{ basename($r->bukti_dp) }}
                                            </button>
                                        @endif
                                        @if ($r->bukti_pelunasan)
                                            <button type="button"
                                                onclick="openSlideshow([{path:'{{ asset($r->bukti_pelunasan) }}', name:'Bukti Pelunasan'}], 0)"
                                                class="inline-flex items-center gap-1 text-[11px] text-green-600 hover:text-green-800 hover:underline">
                                                <i class="bi bi-image text-[10px]"></i>
                                                Pelunasan: {{ basename($r->bukti_pelunasan) }}
                                            </button>
                                        @endif
                                        @if (!$r->bukti_lunas && !$r->bukti_dp && !$r->bukti_pelunasan)
                                            <span class="text-xs text-gray-300">-</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    @if (!empty($r->bukti_lunas) || !empty($r->bukti_pelunasan))
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">
                                            LUNAS
                                        </span>
                                    @elseif (!empty($r->bukti_dp))
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-700">
                                            CICILAN / DP
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-600">
                                            BELUM LUNAS
                                        </span>
                                    @endif
                                </td>

                                {{-- STATUS --}}
                                <td class="px-4 py-3.5">
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if ($r->status == 'pending') bg-gray-100 text-gray-600
                                    @elseif($r->status == 'booking') bg-blue-100 text-blue-600
                                    @elseif($r->status == 'aktif') bg-green-100 text-green-600
                                    @elseif($r->status == 'selesai') bg-gray-800 text-white
                                    @else bg-red-100 text-red-600 @endif">
                                        {{ strtoupper($r->status) }}
                                    </span>
                                </td>


                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fa fa-history text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data rental</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="py-3 border-t border-gray-100">
                    <x-pagination :paginator="$rentals" />
                </div>

            </div>

        </div>

    </div>


    <style>
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%239ca3af'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            padding-right: 24px !important;
        }
    </style>

@endsection

