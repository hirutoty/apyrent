@extends('admin.layouts.app')

@section('title', 'Asuransi Kendaraan')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    @php
        $totalAsuransi = $data->count();
        $totalAktif = $data->where('status_kendaraan', 'aktif')->count();
        $soon = $data
            ->filter(
                fn($d) => $d->tgl_berakhir &&
                    \Carbon\Carbon::parse($d->tgl_berakhir)->gt(now()) &&
                    \Carbon\Carbon::parse($d->tgl_berakhir)->lte(now()->addDays(30)),
            )
            ->count();
        $expired = $data
            ->filter(fn($d) => $d->tgl_berakhir && \Carbon\Carbon::parse($d->tgl_berakhir)->lte(now()))
            ->count();
    @endphp

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Asuransi Kendaraan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola data asuransi &amp; masa berlaku kendaraan</p>
            </div>
            <button onclick="openModalTambah()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Data
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            {{-- Total Asuransi --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Asuransi</p>
                        <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ $totalAsuransi }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-shield text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Aktif --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Aktif</p>
                        <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $totalAktif }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-shield-halved text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Segera Berakhir --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Segera Berakhir</p>
                        <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $soon }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <i class="fa-solid fa-hourglass-half text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Expired --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Expired</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $expired }}</h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-circle-xmark text-2xl"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Asuransi Kendaraan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $totalAsuransi }} total data asuransi</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="exportPdf()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fa fa-file-pdf text-xs"></i> Export PDF
                    </button>
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="searchInput" placeholder="Cari nopol, asuransi, jenis..."
                            oninput="filterTable(this.value)"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-56">
                    </div>
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
                                Asuransi</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Jenis</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl
                                Mulai</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                durasi</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl
                                Berakhir</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Biaya</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Bukti</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($data as $d)
                            @php
                                $tglBerakhir = \Carbon\Carbon::parse($d->tgl_berakhir)->startOfDay();
                                $hariIni = now()->startOfDay();

                                $sisaHari = $hariIni->diffInDays($tglBerakhir, false);

                                $isExpired = $sisaHari <= 0;
                                $isSoon = !$isExpired && $sisaHari <= $reminder;
                            @endphp
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100 {{ $isExpired ? 'bg-red-50' : '' }}"
                                data-search="{{ strtolower(($d->kendaraan->nopol ?? '') . ' ' . ($d->kendaraan->merk ?? '') . ' ' . ($d->asuransi->nama_asuransi ?? '') . ' ' . ($d->jenisAsuransi->nama_jenis ?? '') . ' ' . $d->status_kendaraan) }}">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>

                                {{-- Kendaraan --}}
                                <td class="px-4 py-3.5">
                                    <span class="font-semibold text-gray-800">{{ $d->kendaraan->nopol ?? '-' }}</span>
                                    @if ($d->kendaraan->merk ?? false)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $d->kendaraan->merk }}</p>
                                    @endif
                                </td>

                                {{-- Asuransi --}}
                                <td class="px-4 py-3.5 text-gray-700">{{ $d->asuransi->nama_asuransi ?? '-' }}</td>

                                {{-- Jenis --}}
                                <td class="px-4 py-3.5">
                                    <span
                                        class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->jenisAsuransi->nama_jenis ?? '-' }}</span>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3.5">
                                    @if ($d->status_kendaraan == 'aktif')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Expired</span>
                                    @endif
                                </td>

                                {{-- Tgl Mulai --}}
                                <td class="px-4 py-3.5 text-gray-600">
                                    {{ $d->tgl_mulai ? \Carbon\Carbon::parse($d->tgl_mulai)->format('d M Y') : '-' }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $d->durasi_bulan }} Bulan
                                </td>



                                {{-- Tgl Berakhir --}}
                                <td class="px-4 py-3.5">

                                    @if ($d->isExpired)
                                        <div class="flex flex-col gap-1">
                                            <span class="font-medium text-red-600">
                                                {{ \Carbon\Carbon::parse($d->tgl_berakhir)->format('d M Y') }}
                                            </span>

                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold w-fit">

                                                ⚠️ Terlambat {{ abs($d->sisaHari) }} hari

                                        </div>
                                    @elseif ($d->isSoon)
                                        <div class="flex flex-col gap-1">
                                            <span class="font-medium text-gray-600">
                                                {{ \Carbon\Carbon::parse($d->tgl_berakhir)->format('d M Y') }}
                                            </span>

                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold w-fit">

                                                ⚠️ Berakhir dalam {{ $d->sisaHari }} hari

                                        </div>
                                    @else
                                        <span class="text-gray-600">
                                            {{ \Carbon\Carbon::parse($d->tgl_berakhir)->format('d M Y') }}
                                        </span>
                                    @endif

                                </td>


                                <td class="px-4 py-3 text-sm text-gray-700">
                                    Rp {{ number_format($d->biaya, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if ($d->bukti_bayar)
                                        @php
                                            $filename = basename($d->bukti_bayar);
                                        @endphp

                                        <a href="{{ asset($d->bukti_bayar) }}" target="_blank"
                                            class="text-blue-600 underline text-xs hover:text-blue-800">

                                            {{ $filename }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                {{-- Aksi --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            onclick="openModalPerpanjang(
                                        '{{ $d->id }}',
                                        '{{ $d->asuransi_id }}',
                                        '{{ $d->jenis_asuransi_id }}',
                                        '{{ $d->kendaraan->nopol ?? '-' }}',
                                        '{{ $d->kendaraan->merk ?? '-' }}',
                                        '{{ $d->durasi_bulan }}',
                                        '{{ $d->biaya }}'
                                    )"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                            <i class="fa fa-rotate-right text-xs"></i> Perpanjang
                                        </button>


                                        <button
                                            onclick="openEditModal(
                                            '{{ $d->id }}',
                                            '{{ $d->kendaraan_id }}',
                                            '{{ $d->asuransi_id }}',
                                            '{{ $d->jenis_asuransi_id }}',
                                            '{{ $d->status_kendaraan }}',
                                              '{{ \Carbon\Carbon::parse($d->tgl_mulai)->format('Y-m-d') }}',
    '{{ \Carbon\Carbon::parse($d->tgl_berakhir)->format('Y-m-d') }}',
                                            '{{ $d->durasi_bulan }}',
                                            '{{ $d->biaya }}',
                                            '{{ $d->bukti_bayar }}'
                                        )"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="/admin/asuransi-kendaraan/{{ $d->id }}" method="POST"
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
                                            <i class="fa-solid fa-shield text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data Asuransi</p>
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
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Asuransi Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data asuransi dengan lengkap</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="/admin/asuransi-kendaraan" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span
                            class="text-red-500">*</span></label>
                    <select name="kendaraan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}">{{ $k->nopol }} – {{ $k->merk }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Perusahaan Asuransi <span
                            class="text-red-500">*</span></label>
                    <select name="asuransi_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Asuransi --</option>
                        @foreach ($asuransi as $a)
                            <option value="{{ $a->id }}">{{ $a->nama_asuransi }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Asuransi <span
                            class="text-red-500">*</span></label>
                    <select name="jenis_asuransi_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach ($jenisAsuransi as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_jenis }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Mulai <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tgl_mulai" id="tgl_mulai" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>



                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Durasi Asuransi <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <input type="number" id="durasi_bulan" name="durasi_bulan" min="1" required
                            placeholder="Masukkan durasi"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-16 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-gray-500">
                            Bulan
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Berakhir
                    </label>

                    <input type="date" id="tgl_berakhir" name="tgl_berakhir" readonly
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Biaya Asuransi <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">
                            Rp
                        </span>

                        <input type="number" name="biaya" min="0" required placeholder="0"
                            class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Bukti Pembayaran
                    </label>

                    {{-- Preview --}}
                    <div id="previewWrapAsuransi" class="hidden mb-3 relative">

                        {{-- Preview gambar --}}
                        <img id="previewImgAsuransi" src="" alt="Preview Bukti"
                            class="hidden h-36 w-full rounded-xl border border-gray-200 object-cover cursor-pointer"
                            onclick="window.open(this.src,'_blank')">

                        {{-- Preview file --}}
                        <div id="previewFileAsuransi"
                            class="hidden flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50">

                            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                                <i class="fa-solid fa-file text-red-500 text-xl"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p id="fileNameAsuransi" class="text-sm font-medium text-gray-700 truncate"></p>

                                <p class="text-xs text-gray-400">
                                    Dokumen siap diupload
                                </p>
                            </div>
                        </div>

                        <button type="button" onclick="hapusPreviewAsuransi()"
                            class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>

                    </div>

                    {{-- Upload Area --}}
                    <label for="bukti_bayar"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>

                        <span class="text-sm text-gray-600 font-medium">
                            Klik untuk upload bukti pembayaran
                        </span>

                        <span class="text-xs text-gray-400 mt-1">
                            (Maks 5MB)
                        </span>

                    </label>

                    <input type="file" name="bukti_bayar" id="bukti_bayar" class="hidden" required
                        onchange="previewBuktiAsuransi(this)">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
                            class="text-red-500">*</span></label>
                    <select name="status_kendaraan" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="aktif">Aktif</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>

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
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Asuransi Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data asuransi kendaraan</p>
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

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span
                            class="text-red-500">*</span></label>
                    <select name="kendaraan_id" id="edit_kendaraan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}">{{ $k->nopol }} – {{ $k->merk }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Perusahaan Asuransi <span
                            class="text-red-500">*</span></label>
                    <select name="asuransi_id" id="edit_asuransi_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Asuransi --</option>
                        @foreach ($asuransi as $a)
                            <option value="{{ $a->id }}">{{ $a->nama_asuransi }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Asuransi <span
                            class="text-red-500">*</span></label>
                    <select name="jenis_asuransi_id" id="edit_jenis_asuransi_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach ($jenisAsuransi as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_jenis }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Mulai <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tgl_mulai" id="edit_tgl_mulai" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Durasi Asuransi <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <input type="number" id="edit_durasi_bulan" name="durasi_bulan" min="1" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-16 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500">
                            Bulan
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Berakhir
                    </label>

                    <input type="date" name="tgl_berakhir" id="edit_tgl_berakhir" readonly
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Biaya Asuransi <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">
                            Rp
                        </span>

                        <input type="number" id="edit_biaya" name="biaya" min="0" required
                            class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Bukti Pembayaran
                    </label>

                    {{-- Preview --}}
                    <div id="previewWrapEdit" class="hidden mb-3 relative">

                        {{-- Preview gambar --}}
                        <img id="previewImgEdit" src="" alt="Preview Bukti"
                            class="hidden h-36 w-full rounded-xl border border-gray-200 object-cover cursor-pointer"
                            onclick="window.open(this.src,'_blank')">

                        {{-- Preview file --}}
                        <div id="previewFileEdit"
                            class="hidden flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50">

                            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                                <i class="fa-solid fa-file text-red-500 text-xl"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p id="fileNameEdit" class="text-sm font-medium text-gray-700 truncate"></p>
                                <p class="text-xs text-gray-400">Dokumen tersimpan / siap diupload</p>
                            </div>
                        </div>

                        <button type="button" onclick="hapusPreviewEdit()"
                            class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>

                    </div>

                    {{-- Upload Area --}}
                    <label for="edit_bukti_bayar"
                        class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">

                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>

                        <span class="text-sm text-gray-600 font-medium">
                            Klik untuk upload bukti pembayaran
                        </span>

                        <span class="text-xs text-gray-400 mt-1">
                            (Maks 5MB)
                        </span>

                    </label>

                    <input type="file" name="bukti_bayar" id="edit_bukti_bayar" class="hidden"
                        onchange="previewBuktiEdit(this)">
                </div>


                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span
                            class="text-red-500">*</span></label>
                    <select name="status_kendaraan" id="edit_status_kendaraan" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="aktif">Aktif</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>

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
    {{-- MODAL PERPANJANG --}}
    <div id="modalPerpanjang" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Perpanjang Asuransi Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Data lama akan disimpan ke history, lalu diperbarui.</p>
                </div>
                <button onclick="closeModalPerpanjang()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formPerpanjang" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                {{-- Kendaraan (readonly) --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kendaraan</label>
                    <div class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-sm text-gray-700 cursor-not-allowed">
                        <span id="perpanjang_kendaraan_text">-</span>
                    </div>
                </div>

                {{-- Asuransi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Perusahaan Asuransi <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="asuransi_id" id="hidden_asuransi_id">

                    <select id="perpanjang_asuransi_id" disabled
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100 cursor-not-allowed">

                        @foreach ($asuransi as $a)
                            <option value="{{ $a->id }}">{{ $a->nama_asuransi }}</option>
                        @endforeach

                    </select>
                </div>

                {{-- Jenis Asuransi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Jenis Asuransi <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="jenis_asuransi_id" id="hidden_jenis_asuransi_id">

                    <select id="perpanjang_jenis_id" disabled
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100 cursor-not-allowed">

                        @foreach ($jenisAsuransi as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_jenis }}</option>
                        @endforeach

                    </select>
                </div>

                {{-- Tgl Mulai Baru --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Mulai Baru</label>
                    <input type="date" name="tgl_mulai" id="perpanjang_tgl_mulai" required readonly
                        value="{{ now()->format('Y-m-d') }}"
                        class="w-full bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                </div>

                {{-- Durasi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Durasi (Bulan) <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="durasi_bulan" id="perpanjang_durasi" min="1" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                        oninput="hitungTglBerakhirPerpanjang()">
                </div>

                {{-- Tgl Berakhir (auto) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Berakhir Baru</label>
                    <input type="date" name="tgl_berakhir" id="perpanjang_tgl_berakhir" readonly
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600">
                    <p class="text-xs text-gray-400 mt-1">Otomatis dihitung dari tanggal mulai + durasi</p>
                </div>

                {{-- Biaya --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Biaya Baru <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                        <input type="number" name="biaya" id="perpanjang_biaya" min="0" required readonly
                            class="w-full border bg-gray-100 cursor-not-allowed rounded-lg pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>
                </div>

                {{-- Bukti --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Bukti Pembayaran Baru</label>
                    <input type="file" name="bukti_bayar" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="md:col-span-2 flex gap-3 pt-1">
                    <button type="button" onclick="closeModalPerpanjang()"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fa fa-rotate-right"></i> Perpanjang Asuransi
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


    {{-- ======================================
    POPUP ALERT
====================================== --}}
    @if (session(' (Maks 5MB)s') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

            <div id="alertBox"
                class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">

                @if (session(' (Maks 5MB)s'))
                    <div
                        class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session(' (Maks 5MB)s') }}</p>
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

        function closeModalTambah() {
            var m = document.getElementById('modalTambah');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        document.getElementById('modalTambah').addEventListener('click', function(e) {
            if (e.target === this) closeModalTambah();
        });

        // ── MODAL EDIT ─────────────────────────────────────
        function openEditModal(
            id,
            kendaraan_id,
            asuransi_id,
            jenis_asuransi_id,
            status_kendaraan,
            tgl_mulai,
            tgl_berakhir,
            durasi_bulan,
            biaya,
            bukti_bayar
        ) {

            var m = document.getElementById('modalEdit');
            m.classList.remove('hidden');
            m.classList.add('flex');

            document.getElementById('formEdit').action =
                '/admin/asuransi-kendaraan/' + id;

            document.getElementById('edit_kendaraan_id').value =
                kendaraan_id;

            document.getElementById('edit_asuransi_id').value =
                asuransi_id;

            document.getElementById('edit_jenis_asuransi_id').value =
                jenis_asuransi_id;

            document.getElementById('edit_status_kendaraan').value =
                status_kendaraan;

            document.getElementById('edit_tgl_mulai').value =
                tgl_mulai;

            document.getElementById('edit_tgl_berakhir').value =
                tgl_berakhir;

            document.getElementById('edit_durasi_bulan').value =
                durasi_bulan;

            document.getElementById('edit_biaya').value =
                biaya;

            // preview bukti lama
            const wrap = document.getElementById('previewWrapEdit');
            const img = document.getElementById('previewImgEdit');
            const fileBox = document.getElementById('previewFileEdit');
            const fileName = document.getElementById('fileNameEdit');

            if (bukti_bayar) {

                wrap.classList.remove('hidden');

                const ext = bukti_bayar.split('.').pop().toLowerCase();

                if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                    img.classList.remove('hidden');
                    img.src = '/storage/' + bukti_bayar;
                    fileBox.classList.add('hidden');
                } else {
                    img.classList.add('hidden');
                    fileBox.classList.remove('hidden');
                    fileName.textContent = bukti_bayar.split('/').pop();
                }

            } else {
                wrap.classList.add('hidden');
                img.classList.add('hidden');
                fileBox.classList.add('hidden');
                img.src = '';
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


        // ── MODAL PERPANJANG ───────────────────────────────
        function openModalPerpanjang(id, asuransi_id, jenis_id, nopol, merk, durasi, biaya) {
            document.getElementById('formPerpanjang').action =
                '/admin/asuransi-kendaraan/' + id + '/perpanjang';

            document.getElementById('perpanjang_kendaraan_text').innerText =
                nopol + ' - ' + merk;

            // Hidden input yang akan dikirim ke server
            document.getElementById('hidden_asuransi_id').value = asuransi_id;
            document.getElementById('hidden_jenis_asuransi_id').value = jenis_id;

            document.getElementById('perpanjang_asuransi_id').value = asuransi_id;
            document.getElementById('perpanjang_jenis_id').value = jenis_id;

            document.getElementById('perpanjang_durasi').value = durasi;
            document.getElementById('perpanjang_biaya').value = biaya;

            hitungTglBerakhirPerpanjang();

            const m = document.getElementById('modalPerpanjang');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModalPerpanjang() {
            var m = document.getElementById('modalPerpanjang');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        document.getElementById('modalPerpanjang').addEventListener('click', function(e) {
            if (e.target === this) closeModalPerpanjang();
        });

        function hitungTglBerakhirPerpanjang() {
            const mulai = document.getElementById('perpanjang_tgl_mulai').value;
            const durasi = parseInt(document.getElementById('perpanjang_durasi').value);
            if (!mulai || !durasi) return;
            const d = new Date(mulai);
            d.setMonth(d.getMonth() + durasi);
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            document.getElementById('perpanjang_tgl_berakhir').value = `${yyyy}-${mm}-${dd}`;
        }


        // ── SEARCH / FILTER ────────────────────────────────
        function filterTable(q) {
            var num = 0;
            document.querySelectorAll('#tableBody tr[data-search]').forEach(function(row) {
                var show = row.dataset.search.includes(q.toLowerCase());
                row.style.display = show ? '' : 'none';
                if (show) {
                    num++;
                    row.querySelector('td:first-child').textContent = num;
                }
            });
        }

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

        function exportPdf() {
            let search = document.getElementById('searchInput').value;

            window.open(
                '/admin/asuransi-kendaraan/export-pdf?search=' +
                encodeURIComponent(search),
                '_blank'
            );
        }



        function hitungTanggalBerakhir() {

            const tglMulai = document.getElementById('tgl_mulai').value;
            const durasi = document.getElementById('durasi_bulan').value;

            if (!tglMulai || !durasi) return;

            let tanggal = new Date(tglMulai);

            tanggal.setMonth(
                tanggal.getMonth() + parseInt(durasi)
            );

            const yyyy = tanggal.getFullYear();
            const mm = String(tanggal.getMonth() + 1).padStart(2, '0');
            const dd = String(tanggal.getDate()).padStart(2, '0');

            document.getElementById('tgl_berakhir').value =
                `${yyyy}-${mm}-${dd}`;
        }

        document
            .getElementById('tgl_mulai')
            .addEventListener('change', hitungTanggalBerakhir);

        document
            .getElementById('durasi_bulan')
            .addEventListener('input', hitungTanggalBerakhir);


        function hitungTanggalBerakhirEdit() {

            const mulai = document.getElementById('edit_tgl_mulai').value;
            const durasi = document.getElementById('edit_durasi_bulan').value;

            if (!mulai || !durasi) return;

            let tanggal = new Date(mulai);

            tanggal.setMonth(
                tanggal.getMonth() + parseInt(durasi)
            );

            const yyyy = tanggal.getFullYear();
            const mm = String(tanggal.getMonth() + 1).padStart(2, '0');
            const dd = String(tanggal.getDate()).padStart(2, '0');

            document.getElementById('edit_tgl_berakhir').value =
                `${yyyy}-${mm}-${dd}`;
        }

        document
            .getElementById('edit_tgl_mulai')
            .addEventListener('change', hitungTanggalBerakhirEdit);

        document
            .getElementById('edit_durasi_bulan')
            .addEventListener('input', hitungTanggalBerakhirEdit);

        function previewBuktiEdit(input) {

            const file = input.files[0];
            if (!file) return;

            const wrap = document.getElementById('previewWrapEdit');
            const img = document.getElementById('previewImgEdit');
            const fileBox = document.getElementById('previewFileEdit');
            const fileName = document.getElementById('fileNameEdit');

            wrap.classList.remove('hidden');

            const ext = file.name.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {

                img.classList.remove('hidden');
                fileBox.classList.add('hidden');

                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);

            } else {

                img.classList.add('hidden');
                fileBox.classList.remove('hidden');
                fileName.textContent = file.name;
            }
        }

        function hapusPreviewEdit() {

            document.getElementById('edit_bukti_bayar').value = '';

            document.getElementById('previewWrapEdit').classList.add('hidden');
            document.getElementById('previewImgEdit').classList.add('hidden');
            document.getElementById('previewFileEdit').classList.add('hidden');
            document.getElementById('previewImgEdit').src = '';
        }

        function previewBuktiAsuransi(input) {

            const file = input.files[0];

            if (!file) return;

            const wrap = document.getElementById('previewWrapAsuransi');
            const img = document.getElementById('previewImgAsuransi');
            const fileBox = document.getElementById('previewFileAsuransi');
            const fileName = document.getElementById('fileNameAsuransi');

            wrap.classList.remove('hidden');

            const ext = file.name.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {

                img.classList.remove('hidden');
                fileBox.classList.add('hidden');

                const reader = new FileReader();

                reader.onload = function(e) {
                    img.src = e.target.result;
                };

                reader.readAsDataURL(file);

            } else {

                img.classList.add('hidden');
                fileBox.classList.remove('hidden');

                fileName.textContent = file.name;
            }
        }

        function hapusPreviewAsuransi() {

            document.getElementById('bukti_bayar').value = '';

            document.getElementById('previewWrapAsuransi')
                .classList.add('hidden');

            document.getElementById('previewImgAsuransi')
                .classList.add('hidden');

            document.getElementById('previewFileAsuransi')
                .classList.add('hidden');

            document.getElementById('previewImgAsuransi').src = '';
        }
    </script>

@endsection
