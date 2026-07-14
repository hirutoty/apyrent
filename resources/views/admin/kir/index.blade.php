@extends('admin.layouts.app')

@section('title', 'Uji Kendaraan Bermotor')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    @php
        $jumlahKir = $data->count();

        $jumlahAktif = $data
            ->filter(function ($item) {
                return \Carbon\Carbon::parse($item->masa_berlaku)->isFuture();
            })
            ->count();

        $jumlahNonaktif = $data
            ->filter(function ($item) {
                return \Carbon\Carbon::parse($item->masa_berlaku)->isPast();
            })
            ->count();

        $jumlahSegera = $data
            ->filter(function ($item) {
                $masa = \Carbon\Carbon::parse($item->masa_berlaku);
                $selisih = \Carbon\Carbon::today()->diffInDays($masa, false);
                return $selisih >= 0 && $selisih <= 10;
            })
            ->count();

        $jumlahKir = $data->count();
        $jumlahAktif = $data->where('masa_berlaku', '>=', now())->count();
        $jumlahSegera = $data
            ->filter(fn($d) => \Carbon\Carbon::parse($d->masa_berlaku)->between(now(), now()->addDays(30)))
            ->count();
        $jumlahNonaktif = $data->where('masa_berlaku', '<', now())->count();

        $totalBiayaKir = $data->sum('biaya');

    @endphp

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data KIR</h1>
                <p class="text-sm text-gray-500 mt-0.5">Uji Kendaraan Bermotor</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- <button onclick="openModalPerpanjangSemua()"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                    <i class="fa fa-rotate text-sm"></i>
                    Perpanjang Semua
                </button> -->
                <button onclick="openModalTambah()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                    <i class="fa fa-plus text-sm"></i>
                    Tambah Data
                </button>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">

            {{-- Total KIR --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Jumlah KIR</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $jumlahKir }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-circle-check text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total Biaya KIR --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Biaya KIR</p>
                        <h3 class="text-lg font-bold text-indigo-600 mt-2 leading-tight">
                            Rp {{ number_format($totalBiayaKir, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-wave text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- KIR Aktif --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">KIR Aktif</p>
                        <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $jumlahAktif }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Segera Berakhir --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Segera Berakhir</p>
                        <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $jumlahSegera }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="fa-solid fa-hourglass-half text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- KIR Nonaktif --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">KIR Nonaktif</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $jumlahNonaktif }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-xmark text-2xl"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar KIR Kendaraan</h2>
                    <p class="text-xs text-gray-400 mt-0.5" id="totalCount">{{ $jumlahKir }} total data KIR</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">

                    {{-- SHOW ENTRIES --}}
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-gray-500">Tampilkan</span>
                        <select id="showEntries" onchange="applyFilters()"
                            class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 bg-white">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-xs text-gray-500">data</span>
                    </div>

                    {{-- FILTER STATUS --}}
                    <div class="flex items-center gap-1 border border-gray-200 rounded-lg p-0.5 bg-gray-50">
                        <button type="button" onclick="setActiveStatus('semua')" id="btnSemua"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors bg-white text-gray-700 shadow-sm border border-gray-200">
                            Semua
                        </button>
                        <button type="button" onclick="setActiveStatus('aktif')" id="btnAktif"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-gray-500 hover:text-green-600">
                            <i class="fa fa-check text-[10px]"></i> Aktif
                        </button>
                        <button type="button" onclick="setActiveStatus('nonaktif')" id="btnNonaktif"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-gray-500 hover:text-red-500">
                            <i class="fa fa-times text-[10px]"></i> Nonaktif
                        </button>
                    </div>

                    {{-- FILTER TAHUN --}}
                    <div class="flex items-center gap-1">
                        <select id="filterTahun" onchange="setActiveTahun(this.value)"
                            class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 bg-white">
                            <option value="semua">Semua Tahun</option>
                            @php
                                $tahunList = $data->map(fn($d) => $d->masa_berlaku ? \Carbon\Carbon::parse($d->masa_berlaku)->year : null)
                                    ->filter()
                                    ->unique()
                                    ->sortDesc()
                                    ->values();
                            @endphp
                            @foreach ($tahunList as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FILTER HARI --}}
                    <div class="flex items-center gap-1">
                        <select id="filterHari" onchange="applyFilters()"
                            class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 bg-white">
                            <option value="">Semua Hari</option>
                            @for ($d = 1; $d <= 31; $d++)
                                <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- FILTER BULAN --}}
                    <div class="flex items-center gap-1">
                        <input type="month" id="filterBulan" onchange="setActiveBulan(this.value)"
                            class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <button type="button" onclick="resetBulan()" title="Hapus filter bulan"
                            class="px-2 py-1.5 text-xs text-gray-400 hover:text-red-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <a id="pdfBtn" href="{{ route('kir.pdf', ['search' => request('search')]) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="searchInput" placeholder="Cari nopol, no uji..." value="{{ request('search') }}"
                            oninput="applyFilters()"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                    </div>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
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
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                                Uji</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Masa
                                Berlaku</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Tanggal Bayar</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Biaya</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Bukti</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Lampiran</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($data as $d)
                            @php
                                $today = now()->startOfDay();
                                $masa = \Carbon\Carbon::parse($d->masa_berlaku)->startOfDay();
                                $selisih = (int) $today->diffInDays($masa, false);
                                $rowStatus = $selisih < 0 ? 'nonaktif' : 'aktif';
                            @endphp
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100"
                                data-search="{{ strtolower(($d->kendaraan->nopol ?? '') . ' ' . $d->no_uji . ' ' . $d->masa_berlaku) }}"
                                data-status="{{ $rowStatus }}"
                                data-bulan="{{ $d->masa_berlaku ? \Carbon\Carbon::parse($d->masa_berlaku)->format('Y-m-d') : '' }}">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-gray-400 row-number">{{ $data->firstItem() + $loop->index }}</td>

                                {{-- Kendaraan --}}
                                <td class="px-4 py-3.5">
                                    <span class="font-semibold text-gray-800">{{ $d->kendaraan->nopol ?? '-' }}</span>
                                    @if ($d->kendaraan->merk ?? false)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $d->kendaraan->merk }}</p>
                                    @endif
                                </td>

                                {{-- No Uji --}}
                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->no_uji }}</span>
                                </td>

                                {{-- Masa Berlaku --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-medium text-gray-700">{{ $masa->format('d M Y') }}</span>
                                        @if ($selisih < 0)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold w-fit">
                                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                Terlambat {{ abs($selisih) }} hari
                                            </span>
                                        @elseif ($selisih <= $reminder)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold w-fit animate-pulse">
                                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>

                                                @if ($selisih == 0)
                                                    Berakhir Hari Ini
                                                @elseif ($selisih == 1)
                                                    Berakhir Besok
                                                @else
                                                    Berakhir dalam {{ $selisih }} hari
                                                @endif

                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold w-fit">
                                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                Aktif
                                            </span>
                                        @endif

                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $d->tanggal_bayar ? \Carbon\Carbon::parse($d->tanggal_bayar)->format('d M Y') : '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    Rp {{ number_format($d->biaya, 0, ',', '.') }}
                                </td>

                                {{-- Bukti --}}
                                <td class="px-4 py-3.5">
                                    @if ($d->image)
                                        @php $filename = basename($d->image); @endphp
                                        <a href="{{ asset($d->image) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800 block">
                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Lampiran --}}
                                <td class="px-4 py-3.5">
                                    @if($d->attachments->isNotEmpty())
                                        <div class="flex flex-col gap-1">
                                            @foreach ($d->attachments as $att)
                                                <div class="flex items-center gap-1">
                                                    <a href="{{ asset($att->file_path) }}" target="_blank"
                                                        class="text-blue-500 underline text-[11px] hover:text-blue-700">
                                                        {{ $att->file_name }}
                                                    </a>
                                                    <form action="{{ route('kir.attachment.destroy', $att->id) }}" method="POST"
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
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Perpanjang --}}
                                        <button
                                            onclick="openModalPerpanjang(
        '{{ $d->id }}',
        '{{ $d->kendaraan->nopol ?? '-' }}',
        '{{ $d->kendaraan->merk ?? '-' }}',
        '{{ $d->no_uji }}',
        '{{ $d->biaya }}',
        '{{ \Carbon\Carbon::parse($d->masa_berlaku)->format('Y-m-d') }}'
    )"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                            <i class="fa fa-rotate-right text-xs"></i> Perpanjang
                                        </button>

                                        <button
                                            onclick="openEditModal(
        '{{ $d->id }}',
        '{{ $d->kendaraan_id }}',
        '{{ $d->no_uji }}',
        '{{ $d->biaya }}',
        '{{ $d->masa_berlaku }}',
        '{{ $d->image ? asset($d->image) : '' }}',
        '{{ $d->image ? basename($d->image) : '' }}'
    )"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>


                                        <form action="/admin/kir/{{ $d->id }}" method="POST"
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
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fa-solid fa-file-circle-check text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data KIR</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Data" untuk menambahkan data baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER: SHOWING INFO + PAGINATION --}}
            <div id="tableFooter" class="border-t border-gray-100 bg-gray-50/50">
                <div class="py-3">{{ $data->links() }}</div>
            </div>

        </div>

    </div>


    {{-- ======================================
    MODAL TAMBAH
====================================== --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Data KIR</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data uji kendaraan bermotor</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="/admin/kir" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 grid grid-cols-1 gap-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span
                            class="text-red-500">*</span></label>
                    <select name="kendaraan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}">{{ $k->nopol }} - {{ $k->merk }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Uji <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="no_uji" required placeholder="Nomor uji kendaraan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Masa Berlaku <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="masa_berlaku" id="tambah_masa_berlaku" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya <span
                            class="text-red-500">*</span></label>

                    <input type="text" inputmode="numeric" name="biaya"
                        placeholder="Tambahkan biaya kir"
                        class="format-rupiah w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                        required>
                </div>



                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Upload File <span class="text-red-500">*</span>
                    </label>

                    {{-- PREVIEW AREA --}}
                    <div id="previewWrap" class="hidden mb-3 relative"></div>

                    {{-- UPLOAD AREA --}}
                    <label for="image"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-1"></i>

                        <span class="text-xs text-slate-500">
                            Klik untuk upload / ganti file
                        </span>

                        <span class="text-[11px] text-slate-400">
                            (maks 2MB)
                        </span>
                    </label>

                    <input type="file" name="image" id="image" class="hidden" onchange="previewFile(this)"
                        required>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Lampiran Tambahan (opsional, bisa lebih dari 1)
                    </label>
                    <input type="file" name="bukti_attachment[]" multiple
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                        onchange="renderListAttachment(this, 'listAttachmentPerpanjang')">
                    <ul id="listAttachmentPerpanjang" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModalTambah()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
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
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Data KIR</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data uji kendaraan bermotor</p>
                </div>
                <button onclick="closeModalEdit()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" enctype="multipart/form-data" class="px-6 py-5 grid grid-cols-1 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span
                            class="text-red-500">*</span></label>
                    <select name="kendaraan_id" id="edit_kendaraan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}">{{ $k->nopol }} - {{ $k->merk }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Uji <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="no_uji" id="edit_no_uji" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Masa Berlaku <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="masa_berlaku" id="edit_masa_berlaku" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <p class="text-xs text-gray-400 mt-1">Isi tanggal masa berlaku KIR</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya <span
                            class="text-red-500">*</span></label>
                    <input type="text" inputmode="numeric" name="biaya" id="edit_biaya" required
                        class="format-rupiah w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Image</label>

                    {{-- PREVIEW AREA --}}
                    <div id="previewWrapEdit" class="hidden mb-3 relative"></div>

                    {{-- UPLOAD AREA --}}
                    <label for="edit_image"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-1"></i>

                        <span class="text-xs text-slate-500">
                            Klik untuk upload / ganti file
                        </span>

                        <span class="text-[11px] text-slate-400">
                            (maks 2MB)
                        </span>
                    </label>

                    <input type="file" name="image" id="edit_image" class="hidden"
                        onchange="previewFileEdit(this)">


                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModalEdit()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
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

    {{-- MODAL PERPANJANG --}}
    <div id="modalPerpanjang" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Perpanjang KIR Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Data lama akan disimpan ke history, lalu diperbarui.</p>
                </div>
                <button onclick="closeModalPerpanjang()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formPerpanjang" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 grid grid-cols-1 gap-4">
                @csrf

                {{-- Kendaraan readonly --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kendaraan</label>
                    <div class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-sm text-gray-700 cursor-not-allowed">
                        <span id="perpanjang_kendaraan_text">-</span>
                    </div>
                </div>

                {{-- No Uji --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">No Uji Baru <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="no_uji" id="perpanjang_no_uji" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Masa Berlaku Baru: lama + 1 tahun, disabled --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Masa Berlaku Baru <span
                            class="text-red-500">*</span></label>
                    <input type="date" id="perpanjang_masa_berlaku_display" disabled
                        class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                    <input type="hidden" name="masa_berlaku" id="perpanjang_masa_berlaku">
                    <p class="text-xs text-gray-400 mt-1">Otomatis masa berlaku lama + 1 tahun</p>
                </div>

                {{-- Tanggal Bayar --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" id="perpanjang_tanggal_bayar"
                        value="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Biaya --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Biaya Baru <span
                            class="text-red-500">*</span></label>
                    <input type="text" inputmode="numeric" name="biaya" id="perpanjang_biaya" required
                        class="format-rupiah w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Upload Bukti --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Upload Bukti Baru <span class="text-red-500">*</span>
                    </label>

                    {{-- Preview area --}}
                    <div id="previewWrapPerpanjang" class="hidden mb-3 relative"></div>

                    <label for="perpanjang_image"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-1"></i>
                        <span class="text-xs text-slate-500">Klik untuk upload / ganti file</span>
                        <span class="text-[11px] text-slate-400">(maks 2MB)</span>
                    </label>

                    <input type="file" name="image" id="perpanjang_image" class="hidden" required
                        onchange="previewFilePerpanjang(this)">
                </div>

                {{-- Lampiran Tambahan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Lampiran Tambahan <span class="text-gray-400 font-normal">(opsional, bisa lebih dari 1)</span>
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

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModalPerpanjang()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fa fa-rotate-right"></i> Perpanjang KIR
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
        // -- MODAL TAMBAH -----------------------------------
        function openModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        document.getElementById('modalTambah').addEventListener('click', function(e) {
            if (e.target === this) closeModalTambah();
        });

        // -- MODAL EDIT -------------------------------------
        function openEditModal(id, kendaraan_id, no_uji, biaya, masa_berlaku, image, imageName) {
            var m = document.getElementById('modalEdit');
            m.classList.remove('hidden');
            m.classList.add('flex');

            document.getElementById('formEdit').action = '/admin/kir/' + id;
            document.getElementById('edit_kendaraan_id').value = kendaraan_id;
            document.getElementById('edit_no_uji').value = no_uji;
            document.getElementById('edit_biaya').value = biaya;
            document.getElementById('edit_biaya').dispatchEvent(new Event('input', { bubbles: true }));

            // Set masa berlaku dari data yang ada
            document.getElementById('edit_masa_berlaku').value = masa_berlaku;

            document.getElementById('edit_image').value = '';

            const wrap = document.getElementById('previewWrapEdit');
            wrap.innerHTML = '';

            if (image) {
                wrap.classList.remove('hidden');

                const ext = image.split('.').pop().toLowerCase();
                let html = '';

                if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                    html = `
                <div class="relative">
                    <img src="${image}" class="h-36 w-full object-cover rounded-xl border">
                    <span class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[11px] px-2 py-1 truncate">
                        ${imageName}
                    </span>
                    <a href="${image}" target="_blank"
                        class="absolute top-2 right-2 w-6 h-6 flex items-center justify-center bg-white/90 text-gray-600 rounded-full text-xs">
                        <i class="fa fa-eye"></i>
                    </a>
                </div>
            `;
                } else {
                    let icon = 'fa-file';
                    let color = 'text-gray-600 bg-gray-50';

                    if (ext === 'pdf') {
                        icon = 'fa-file-pdf';
                        color = 'text-red-600 bg-red-50';
                    }
                    if (ext === 'doc' || ext === 'docx') {
                        icon = 'fa-file-word';
                        color = 'text-blue-600 bg-blue-50';
                    }

                    html = `
                <div class="flex items-center justify-between p-3 border rounded-xl ${color}">
                    <div class="flex items-center gap-2 text-sm font-semibold truncate">
                        <i class="fa-solid ${icon}"></i>
                        <span class="truncate">${imageName}</span>
                    </div>
                    <a href="${image}" target="_blank" class="text-xs underline flex-shrink-0 ml-2">Lihat</a>
                </div>
            `;
                }

                wrap.innerHTML = html;
            } else {
                wrap.classList.add('hidden');
            }
        }
        

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

        function syncPerpanjangKirDates() {
            // Fungsi ini sekarang tidak perlu melakukan apa-apa untuk masa_berlaku
            // karena masa_berlaku sudah dihitung dari masaBerlakuLama saat modal dibuka
        }

        const tanggalBayarKirInput = document.getElementById('perpanjang_tanggal_bayar');
        if (tanggalBayarKirInput) {
            tanggalBayarKirInput.addEventListener('change', syncPerpanjangKirDates);
        }

        // -- MODAL PERPANJANG -------------------------------
        function openModalPerpanjang(id, nopol, merk, no_uji, biaya, masaBerlakuLama) {
            document.getElementById('formPerpanjang').action = '/admin/kir/' + id + '/perpanjang';
            document.getElementById('perpanjang_kendaraan_text').innerText = nopol + ' - ' + merk;
            document.getElementById('perpanjang_no_uji').value = no_uji;
            document.getElementById('perpanjang_biaya').value = biaya;
            document.getElementById('perpanjang_biaya').dispatchEvent(new Event('input', { bubbles: true }));

            // Set tanggal_bayar = hari ini
            document.getElementById('perpanjang_tanggal_bayar').value = new Date().toISOString().split('T')[0];

            // masa_berlaku_baru = masaBerlakuLama + 1 tahun (dari data lama, tidak berubah saat tanggal_bayar berubah)
            if (masaBerlakuLama) {
                const d = new Date(masaBerlakuLama);
                d.setFullYear(d.getFullYear() + 1);
                const val = d.getFullYear() + '-'
                    + String(d.getMonth() + 1).padStart(2, '0') + '-'
                    + String(d.getDate()).padStart(2, '0');
                document.getElementById('perpanjang_masa_berlaku_display').value = val;
                document.getElementById('perpanjang_masa_berlaku').value = val;
            }

            var m = document.getElementById('modalPerpanjang');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModalPerpanjang() {
            var m = document.getElementById('modalPerpanjang');
            m.classList.add('hidden');
            m.classList.remove('flex');
            // reset upload
            removePreviewPerpanjang();
            document.getElementById('listAttachmentTambah').innerHTML = '';
            document.getElementById('bukti_attachment').value = '';
        }

        document.getElementById('modalPerpanjang').addEventListener('click', function(e) {
            if (e.target === this) closeModalPerpanjang();
        });

        // -- SEARCH / FILTER --------------------------------
        let activeStatus = 'semua';
        let activeBulan  = 'semua';
        let activeTahun  = 'semua';
        let currentPage  = 1;

        function setActiveStatus(status) {
            activeStatus = status;
            currentPage = 1;

            const buttons = {
                semua:    document.getElementById('btnSemua'),
                aktif:    document.getElementById('btnAktif'),
                nonaktif: document.getElementById('btnNonaktif'),
            };

            Object.entries(buttons).forEach(([key, btn]) => {
                if (!btn) return;
                if (key === status) {
                    btn.classList.add('bg-white', 'shadow-sm', 'border', 'border-gray-200');
                    if (key === 'aktif')    btn.classList.add('text-green-700');
                    else if (key === 'nonaktif') btn.classList.add('text-red-600');
                    else btn.classList.add('text-gray-700');
                    btn.classList.remove('text-gray-500');
                } else {
                    btn.classList.remove('bg-white', 'shadow-sm', 'border', 'border-gray-200',
                        'text-green-700', 'text-red-600', 'text-gray-700');
                    btn.classList.add('text-gray-500');
                }
            });

            applyFilters();
        }

        function setActiveTahun(tahun) {
            activeTahun = tahun;
            document.getElementById('filterBulan').value = '';
            activeBulan = 'semua';
            currentPage = 1;
            applyFilters();
        }

        function setActiveBulan(bulan) {
            activeBulan = (bulan && bulan.trim() !== '') ? bulan : 'semua';
            currentPage = 1;
            applyFilters();
        }

        function resetBulan() {
            document.getElementById('filterBulan').value = '';
            activeBulan = 'semua';
            currentPage = 1;
            applyFilters();
        }

        function applyFilters() {
            const keyword    = document.getElementById('searchInput').value.toLowerCase().trim();
            const filterHari = document.getElementById('filterHari').value;
            const showVal    = document.getElementById('showEntries').value;
            const perPage    = showVal === 'all' ? Infinity : parseInt(showVal);
            const rows       = document.querySelectorAll('#tableBody tr[data-search]');

            // Kumpulkan baris yang lolos filter
            const matched = [];
            rows.forEach(row => {
                const matchSearch  = !keyword || row.dataset.search.includes(keyword);
                const matchStatus  = activeStatus === 'semua' || row.dataset.status === activeStatus;

                // data-bulan sekarang format Y-m-d
                const tgl      = row.dataset.bulan || '';  // "YYYY-MM-DD"
                const [rowYear, rowMonth, rowDay] = tgl.split('-');
                const rowYM    = rowYear && rowMonth ? `${rowYear}-${rowMonth}` : '';

                const matchHari  = !filterHari   || rowDay  === filterHari;
                const matchBulan = activeBulan === 'semua' || rowYM === activeBulan;
                const matchTahun = activeTahun === 'semua' || rowYear === activeTahun;

                if (matchSearch && matchStatus && matchHari && matchBulan && matchTahun) matched.push(row);
            });

            const total      = matched.length;
            const totalPages = perPage === Infinity ? 1 : Math.ceil(total / perPage);
            if (currentPage > totalPages) currentPage = 1;

            const start = perPage === Infinity ? 0 : (currentPage - 1) * perPage;
            const end   = perPage === Infinity ? total : Math.min(start + perPage, total);

            // Tampilkan / sembunyikan baris
            rows.forEach(row => row.style.display = 'none');
            matched.forEach((row, idx) => {
                row.style.display = (idx >= start && idx < end) ? '' : 'none';
            });

            // Nomor urut
            let num = start + 1;
            matched.forEach((row, idx) => {
                if (idx >= start && idx < end) {
                    const cell = row.querySelector('.row-number');
                    if (cell) cell.textContent = num++;
                }
            });

            // Info "Menampilkan X sampai Y dari Z data"
            const showingInfo = document.getElementById('showingInfo');
            if (showingInfo) {
                showingInfo.textContent = total === 0
                    ? 'Tidak ada data yang ditampilkan'
                    : `Menampilkan ${start + 1} sampai ${end} dari ${total} data`;
            }

            // Pagination
            renderPagination(totalPages);

            // Update total count header
            document.getElementById('totalCount').textContent = total + ' total data KIR';

            // Update PDF link
            updatePdfLink();
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('paginationControls');
            if (!container) return;
            container.innerHTML = '';
            if (totalPages <= 1) return;

            const btnClass    = 'px-2.5 py-1 text-xs rounded-lg border transition-colors';
            const activeClass = 'bg-blue-600 text-white border-blue-600';
            const normalClass = 'border-gray-200 text-gray-600 hover:bg-gray-50';

            // Prev
            const prev = document.createElement('button');
            prev.innerHTML = '<i class="fa fa-chevron-left text-[10px]"></i>';
            prev.className = `${btnClass} ${currentPage === 1 ? 'opacity-40 cursor-not-allowed border-gray-200 text-gray-400' : normalClass}`;
            prev.disabled = currentPage === 1;
            prev.onclick = () => { currentPage--; applyFilters(); };
            container.appendChild(prev);

            const range = 2;
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - range && i <= currentPage + range)) {
                    const btn = document.createElement('button');
                    btn.textContent = i;
                    btn.className = `${btnClass} ${i === currentPage ? activeClass : normalClass}`;
                    btn.onclick = (function(page) {
                        return function() { currentPage = page; applyFilters(); };
                    })(i);
                    container.appendChild(btn);
                } else if (i === currentPage - range - 1 || i === currentPage + range + 1) {
                    const dots = document.createElement('span');
                    dots.textContent = '...';
                    dots.className = 'px-1 text-xs text-gray-400';
                    container.appendChild(dots);
                }
            }

            // Next
            const next = document.createElement('button');
            next.innerHTML = '<i class="fa fa-chevron-right text-[10px]"></i>';
            next.className = `${btnClass} ${currentPage === totalPages ? 'opacity-40 cursor-not-allowed border-gray-200 text-gray-400' : normalClass}`;
            next.disabled = currentPage === totalPages;
            next.onclick = () => { currentPage++; applyFilters(); };
            container.appendChild(next);
        }

        function updatePdfLink() {
            const keyword = document.getElementById('searchInput').value;
            const filterHari = document.getElementById('filterHari').value;
            const pdfBtn  = document.getElementById('pdfBtn');
            const params  = new URLSearchParams();

            if (keyword)               params.set('search', keyword);
            if (filterHari)            params.set('hari', filterHari);
            if (activeStatus !== 'semua') params.set('status', activeStatus);
            if (activeBulan  !== 'semua') params.set('bulan', activeBulan);
            if (activeTahun  !== 'semua') params.set('tahun', activeTahun);

            const qs = params.toString();
            pdfBtn.href = "{{ route('kir.pdf') }}" + (qs ? '?' + qs : '');
        }

        // Inisialisasi
        applyFilters();

        // -- POPUP ALERT ------------------------------------
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

        function previewFile(input) {
            const wrap = document.getElementById('previewWrap');
            wrap.innerHTML = '';
            wrap.classList.remove('hidden');

            const file = input.files[0];
            if (!file) return;

            const ext = file.name.split('.').pop().toLowerCase();
            const url = URL.createObjectURL(file);

            let html = '';

            // IMAGE PREVIEW
            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                html = `
            <div class="relative">
                <img src="${url}"
                    class="h-36 w-full object-cover rounded-xl border">

                <button type="button"
                    onclick="removePreview()"
                    class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    ?
                </button>
            </div>
        `;
            }

            // PDF / DOC / DOCX
            else {
                let icon = 'fa-file';
                let color = 'text-gray-600 bg-gray-50';

                if (ext === 'pdf') {
                    icon = 'fa-file-pdf';
                    color = 'text-red-600 bg-red-50';
                }

                if (ext === 'doc' || ext === 'docx') {
                    icon = 'fa-file-word';
                    color = 'text-blue-600 bg-blue-50';
                }

                html = `
            <div class="flex items-center justify-between p-3 border rounded-xl ${color}">
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <i class="fa-solid ${icon}"></i>
                    ${file.name}
                </div>

                <button type="button"
                    onclick="removePreview()"
                    class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    ?
                </button>
            </div>
        `;
            }

            wrap.innerHTML = html;
        }

        function removePreview() {
            const input = document.getElementById('image');
            const wrap = document.getElementById('previewWrap');

            input.value = '';
            wrap.innerHTML = '';
            wrap.classList.add('hidden');
        }

        function previewFileEdit(input) {
            const wrap = document.getElementById('previewWrapEdit');
            wrap.innerHTML = '';
            wrap.classList.remove('hidden');

            const file = input.files[0];
            if (!file) return;

            const ext = file.name.split('.').pop().toLowerCase();
            const url = URL.createObjectURL(file);

            let html = '';

            // IMAGE PREVIEW
            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                html = `
            <div class="relative">
                <img src="${url}"
                    class="h-36 w-full object-cover rounded-xl border">

                <button type="button"
                    onclick="removePreviewEdit()"
                    class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    ?
                </button>
            </div>
        `;
            }

            // PDF / DOC / DOCX
            else {
                let icon = 'fa-file';
                let color = 'text-gray-600 bg-gray-50';

                if (ext === 'pdf') {
                    icon = 'fa-file-pdf';
                    color = 'text-red-600 bg-red-50';
                }

                if (ext === 'doc' || ext === 'docx') {
                    icon = 'fa-file-word';
                    color = 'text-blue-600 bg-blue-50';
                }

                html = `
            <div class="flex items-center justify-between p-3 border rounded-xl ${color}">
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <i class="fa-solid ${icon}"></i>
                    ${file.name}
                </div>

                <button type="button"
                    onclick="removePreviewEdit()"
                    class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    ?
                </button>
            </div>
        `;
            }

            wrap.innerHTML = html;
        }

        function removePreviewEdit() {
            const input = document.getElementById('edit_image');
            const wrap = document.getElementById('previewWrapEdit');

            input.value = '';
            wrap.innerHTML = '';
            wrap.classList.add('hidden');
        }

        function previewFilePerpanjang(input) {
            const wrap = document.getElementById('previewWrapPerpanjang');
            wrap.innerHTML = '';
            wrap.classList.remove('hidden');

            const file = input.files[0];
            if (!file) return;

            const ext = file.name.split('.').pop().toLowerCase();
            const url = URL.createObjectURL(file);
            let html = '';

            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                html = `
                    <div class="relative">
                        <img src="${url}" class="h-36 w-full object-cover rounded-xl border">
                        <button type="button" onclick="removePreviewPerpanjang()"
                            class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs">?</button>
                    </div>`;
            } else {
                let icon = 'fa-file';
                let color = 'text-gray-600 bg-gray-50';
                if (ext === 'pdf') { icon = 'fa-file-pdf'; color = 'text-red-600 bg-red-50'; }
                if (ext === 'doc' || ext === 'docx') { icon = 'fa-file-word'; color = 'text-blue-600 bg-blue-50'; }
                html = `
                    <div class="flex items-center justify-between p-3 border rounded-xl ${color}">
                        <div class="flex items-center gap-2 text-sm font-semibold">
                            <i class="fa-solid ${icon}"></i> ${file.name}
                        </div>
                        <button type="button" onclick="removePreviewPerpanjang()"
                            class="w-6 h-6 bg-red-500 text-white rounded-full text-xs">?</button>
                    </div>`;
            }

            wrap.innerHTML = html;
        }

        function removePreviewPerpanjang() {
            const input = document.getElementById('perpanjang_image');
            const wrap = document.getElementById('previewWrapPerpanjang');
            input.value = '';
            wrap.innerHTML = '';
            wrap.classList.add('hidden');
        }
    </script>

    {{-- ======================================
    MODAL PERPANJANG SEMUA
====================================== --}}
    <div id="modalPerpanjangSemua" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Perpanjang Semua KIR</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Semua data KIR akan diperpanjang +1 tahun dari masa berlaku masing-masing. Data lama tersimpan ke history.</p>
                </div>
                <button onclick="closeModalPerpanjangSemua()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('kir.perpanjang-semua') }}" method="POST" class="px-6 py-5 grid grid-cols-1 gap-4">
                @csrf

                {{-- Info jumlah data --}}
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                    <i class="fa-solid fa-circle-info text-blue-500 text-lg"></i>
                    <p class="text-xs text-blue-700">
                        Total <strong>{{ $data->total() }}</strong> data KIR akan diperpanjang sekaligus.
                    </p>
                </div>

                {{-- Tanggal Bayar --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" value="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan untuk menggunakan tanggal hari ini.</p>
                </div>

                {{-- Biaya Default (opsional) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Biaya Baru <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <input type="text" inputmode="numeric" name="biaya_default"
                        placeholder="Kosongkan untuk pakai biaya masing-masing"
                        class="format-rupiah w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400">
                    <p class="text-xs text-gray-400 mt-1">Jika diisi, semua KIR akan diupdate dengan biaya ini.</p>
                </div>

                {{-- Konfirmasi --}}
                <div class="flex items-start gap-2 bg-yellow-50 border border-yellow-100 rounded-xl px-4 py-3">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-500 mt-0.5"></i>
                    <p class="text-xs text-yellow-700">
                        Tindakan ini tidak dapat dibatalkan. Pastikan data sudah benar sebelum melanjutkan.
                    </p>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModalPerpanjangSemua()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <!-- <button type="submit"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fa fa-rotate"></i> Ya, Perpanjang Semua
                    </button> -->
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalPerpanjangSemua() {
            var m = document.getElementById('modalPerpanjangSemua');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModalPerpanjangSemua() {
            var m = document.getElementById('modalPerpanjangSemua');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        document.getElementById('modalPerpanjangSemua').addEventListener('click', function(e) {
            if (e.target === this) closeModalPerpanjangSemua();
        });
    </script>

@endsection
