@extends('admin.layouts.app')

@section('title', 'Daftar Kendaraan')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Kendaraan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola data kendaraan armada</p>
            </div>
            <button onclick="openModal('modalTambah')"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
                <i class="fa fa-plus text-sm"></i>
                Tambah Kendaraan
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Kendaraan</p>
                        <h2 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalKendaraan }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center">
                        <i class="fa fa-truck text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">
                            Tersedia
                        </p>
                        <h2 class="text-3xl font-bold text-emerald-600 mt-1">
                            {{ $totalTersedia }}
                        </h2>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                        <i class="fa fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">
                            Tidak Tersedia
                        </p>
                        <h2 class="text-3xl font-bold text-red-600 mt-1">
                            {{ $totalHabis }}
                        </h2>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                        <i class="fa fa-times-circle text-xl"></i>
                    </div>
                </div>
            </div>



        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Kendaraan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $totalKendaraan }} total kendaraan</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" placeholder="Cari kendaraan..." oninput="filterKendaraanTable(this.value)"
                            class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                    </div>
                    <a href="{{ route('kendaraan.export.merk') }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                        <i class="fa fa-file-pdf text-xs"></i>
                        Export PDF
                    </a>
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <i class="fa fa-sync text-xs"></i> Refresh
                    </button>
                </div>
            </div>

        {{-- nomor baris sekarang dari server, $data->firstItem() --}}

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th
                                class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3 w-10">
                                No</th>

                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama
                                / Merk</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Stok</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Status hari ini</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kendaraanTableBody">
                        @forelse($data as $i => $d)
                            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors duration-100"
                                data-search="{{ strtolower($d->merk . ' ' . $d->nopol . ' ' . $d->nama_pemilik . ' ' . ($d->jenis->nama_jenis ?? '')) }}">

                                <td class="px-4 py-3.5 text-xs text-gray-400 font-semibold">{{ $data->firstItem() + $loop->index }}</td>



                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="fa fa-car text-blue-400 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">{{ $d->merk }}</div>
                                            <div class="text-xs text-gray-400">{{ $d->jenis->nama_jenis ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">
                                        {{ $d->total_unit }} Unit
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    @if ($d->tersedia_unit > 0)
                                        <span
                                            class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            Tersedia
                                            <span class="bg-white/70 px-1.5 py-0.5 rounded text-[10px] font-bold">
                                                {{ $d->tersedia_unit }} Unit
                                            </span>
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-2 bg-red-100 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                            Habis
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">

                                        <a href="{{ route('kendaraan.show', $d->merk) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-600 hover:bg-indigo-200">
                                            <i class="fa fa-eye"></i>
                                            lebih banyak
                                        </a>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fa fa-truck text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data kendaraan</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Kendaraan" untuk menambahkan data
                                            baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
            </div>

        </div>

    </div>

    {{-- ======================================
    MODAL UBAH STATUS
====================================== --}}
    <div id="modalStatus" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Ubah Status Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih status kendaraan baru</p>
                </div>
                <button onclick="closeModalStatus()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formStatus" method="POST" class="px-6 py-5">
                @csrf
                @method('PUT')
                <input type="hidden" name="status_kendaraan" id="statusKendaraanInput">

                <div class="grid grid-cols-2 gap-3 mb-5">

                    <button type="button" onclick="pilihStatus('bermasalah')"
                        class="status-btn bg-red-50 hover:bg-red-100 text-red-700 rounded-xl py-3 text-sm font-semibold transition-colors">
                        Bermasalah
                    </button>

                    {{-- Service: non-interaktif, hanya info --}}
                    <div
                        class="bg-amber-50 text-amber-400 rounded-xl py-3 text-sm font-semibold text-center cursor-not-allowed select-none border border-amber-100">
                        Service
                        <p class="text-xs font-normal mt-0.5 text-amber-300">otomatis dari menu service</p>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModalStatus()"
                        class="px-4 py-2 text-sm font-semibold rounded-xl border border-gray-200 text-gray-600 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-xl bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                        <i class="fa fa-save text-xs"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ======================================
    MODAL DETAIL
====================================== --}}
    <div id="modalDetail" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 shrink-0">
                <div>
                    <h2 id="d_title" class="text-base font-bold text-gray-800">Detail Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Informasi lengkap kendaraan</p>
                </div>
                <button onclick="closeAllModals()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-6">

                <div id="d_foto_wrap" class="hidden">
                    @if (!empty($d->foto))
                        <img id="d_foto_img" src="{{ asset($d->foto) }}" alt="Foto"
                            class="w-full max-h-52 object-cover rounded-xl border border-gray-200">
                    @else
                        <span class="text-xs text-gray-400">Tidak ada foto</span>
                    @endif
                </div>

                {{-- Informasi Dasar --}}
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                        <i class="fa fa-user mr-1"></i> Informasi Dasar
                    </p>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">User</span><span
                                id="d_user" class="text-gray-700 font-medium">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Jenis</span><span
                                id="d_jenis" class="text-gray-700 font-medium">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Merk</span><span
                                id="d_merk" class="text-gray-800 font-semibold">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Warna</span><span
                                id="d_warna" class="text-gray-700 font-medium">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Nomor
                                Polisi</span><span id="d_nopol"
                                class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded inline-block">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Nama
                                Pemilik</span><span id="d_nama_pemilik" class="text-gray-700 font-medium">—</span></div>
                        <div class="col-span-2 flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Alamat</span><span
                                id="d_alamat" class="text-gray-500">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Harga /
                                Bulan</span><span id="d_harga_jam" class="text-gray-800 font-semibold">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Harga /
                                Hari</span><span id="d_harga_hari" class="text-gray-800 font-semibold">—</span></div>
                    </div>
                </div>

                {{-- Data Registrasi --}}
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                        <i class="fa fa-id-card mr-1"></i> Data Registrasi
                    </p>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Tahun
                                Pembuatan</span><span id="d_tahun_pembuatan"
                                class="font-mono text-gray-500 text-xs">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Tahun
                                Perakitan</span><span id="d_tahun_perakitan"
                                class="font-mono text-gray-500 text-xs">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Isi
                                Silinder</span><span id="d_isi_silinder" class="text-gray-500">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Bahan Bakar</span><span
                                id="d_bahan_bakar" class="text-gray-500">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Warna TNKB</span><span
                                id="d_warna_tnkb" class="text-gray-500">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Kode Lokasi</span><span
                                id="d_kode_lokasi" class="font-mono text-gray-500 text-xs">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">No Urut</span><span
                                id="d_no_urut" class="font-mono text-gray-500 text-xs">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">No Rangka</span><span
                                id="d_no_rangka" class="font-mono text-gray-500 text-xs">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">No Mesin</span><span
                                id="d_no_mesin" class="font-mono text-gray-500 text-xs">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">No BPKB</span><span
                                id="d_no_bpkb" class="font-mono text-gray-500 text-xs">—</span></div>
                    </div>
                </div>

                {{-- Dokumen & Biaya --}}
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                        <i class="fa fa-file-alt mr-1"></i> Dokumen & Biaya
                    </p>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Batas Biaya</span><span
                                id="d_batas_biaya" class="text-gray-800 font-semibold">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Masa
                                Berlaku</span><span id="d_masa_berlaku" class="font-mono text-gray-500 text-xs">—</span>
                        </div>
                        
                        </div>
                    </div>
                </div>

                {{-- Servis & Kilometer --}}
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                        <i class="fa fa-tachometer-alt mr-1"></i> Servis & Kilometer
                    </p>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">KM Sekarang</span><span
                                id="d_km_sekarang" class="font-mono text-gray-500 text-xs">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Limit KM
                                Service</span><span id="d_limit_km" class="font-mono text-gray-500 text-xs">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Limit Bulan
                                Service</span><span id="d_limit_bln" class="text-gray-500">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">KM Terakhir
                                Service</span><span id="d_km_svc" class="font-mono text-gray-500 text-xs">—</span></div>
                        <div class="flex flex-col gap-0.5"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Tanggal Terakhir
                                Service</span><span id="d_tgl_svc" class="font-mono text-gray-500 text-xs">—</span></div>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <p
                        class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                        <i class="fa fa-toggle-on mr-1"></i> Status
                    </p>
                    <div class="flex items-center gap-6">
                        <div class="flex flex-col gap-1"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Status
                                Service</span><span id="d_status_service"></span></div>
                        <div class="flex flex-col gap-1"><span
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Status
                                Kendaraan</span><span id="d_status_kendaraan"></span></div>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-100 shrink-0">
                <button onclick="closeAllModals()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl border border-gray-200 text-gray-600 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                    <i class="fa fa-times text-xs"></i> Tutup
                </button>
            </div>

        </div>
    </div>


    {{-- ======================================
    MODAL TAMBAH
====================================== --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 shrink-0">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data lengkap kendaraan</p>
                </div>
                <button onclick="closeAllModals()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('kendaraan.store') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-6">

                    {{-- Informasi Dasar --}}
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                            <i class="fa fa-user mr-1"></i> Informasi Dasar
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Kendaraan <span
                                        class="text-red-500">*</span></label>
                                <select name="jenis_id" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">-- Pilih Jenis --</option>
                                    @foreach ($jenis as $j)
                                        <option value="{{ $j->id }}" {{ old('jenis_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jenis }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor Polisi <span
                                        class="text-red-500">*</span></label>
                                <input name="nopol" required placeholder="AB 1234 CD"
                                    value="{{ old('nopol') }}"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga Sewa / Hari <span
                                        class="text-red-500">*</span></label>
                                <input name="harga_sewa_per_hari" required type="number" placeholder="0"
                                    value="{{ old('harga_sewa_per_hari') }}"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga Sewa / Bulan <span
                                        class="text-red-500">*</span></label>
                                <input name="harga_sewa_per_jam" required type="number" placeholder="0"
                                    value="{{ old('harga_sewa_per_jam') }}"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pemilik <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" name="nama_pemilik" id="nama_pemilik" autocomplete="off" required
                                        placeholder="Ketik nama member..."
                                        value="{{ old('nama_pemilik') }}"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <input type="hidden" name="member_id" id="member_id">
                                    <div id="member-result-tambah"
                                        class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-48 overflow-y-auto"></div>
                                </div>
                                <p id="member_warning_tambah" class="hidden mt-1 text-xs text-red-500 flex items-center gap-1">
                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Member tidak ditemukan
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Merk <span
                                        class="text-red-500">*</span></label>
                                <input name="merk" required placeholder="Toyota, Honda"
                                    value="{{ old('merk') }}"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Warna <span
                                        class="text-red-500">*</span></label>
                                <input name="warna" required placeholder="Hitam / Putih"
                                    value="{{ old('warna') }}"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input name="alamat" id="alamat" required placeholder="Alamat lengkap"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 pr-8">
                                    <button type="button" id="alamat_edit_btn"
                                        onclick="document.getElementById('alamat').readOnly=false;this.style.display='none'"
                                        style="display:none"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 hover:text-blue-500"
                                        title="Edit alamat manual"><i class="fa fa-pen"></i></button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Foto <span
                                        class="text-red-500">*</span></label>
                                <input name="foto" type="file" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    {{-- Data Registrasi --}}
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                            <i class="fa fa-id-card mr-1"></i> Data Registrasi
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            {{-- Tahun Pembuatan --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tahun Pembuatan
                                    <span class="text-red-500">*</span></label>
                                <select name="tahun_pembuatan" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">-- Pilih Tahun --</option>
                                    @for ($y = date('Y'); $y >= 1980; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            {{-- Tahun Perakitan --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tahun Perakitan
                                    <span class="text-red-500">*</span></label>
                                <select name="tahun_perakitan" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">-- Pilih Tahun --</option>
                                    @for ($y = date('Y'); $y >= 1980; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            {{-- Isi Silinder --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Isi Silinder
                                    <span class="text-red-500">*</span></label>
                                <input name="isi_silinder" required placeholder="Masukkan Isi Silinder (cc)"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            {{-- Bahan Bakar (dropdown) --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bahan Bakar
                                    <span class="text-red-500">*</span></label>
                                <select name="bahan_bakar" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">-- Pilih Bahan Bakar --</option>
                                    <option value="Bensin">Bensin</option>
                                    <option value="Solar">Solar</option>
                                    <option value="Pertamax">Pertamax</option>
                                    <option value="Pertalite">Pertalite</option>
                                    <option value="Listrik">Listrik</option>
                                    <option value="Hybrid">Hybrid</option>
                                </select>
                            </div>
                            {{-- Warna TNKB (dropdown) --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Warna TNKB
                                    <span class="text-red-500">*</span></label>
                                <select name="warna_tnkb" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">-- Pilih Warna TNKB --</option>
                                    <option value="Hitam">Hitam</option>
                                    <option value="Putih">Putih</option>
                                    <option value="Merah">Merah</option>
                                </select>
                            </div>
                            {{-- Kode Lokasi --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Lokasi
                                    <span class="text-red-500">*</span></label>
                                <input name="kode_lokasi" required placeholder="Masukkan Kode Lokasi"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            {{-- No Urut Pendaftaran --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Urut Pendaftaran
                                    <span class="text-red-500">*</span></label>
                                <input name="no_urut_pendaftaran" required placeholder="Masukkan No Urut Pendaftaran"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            {{-- No Rangka --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Rangka
                                    <span class="text-red-500">*</span></label>
                                <input name="no_rangka" required placeholder="Masukkan No Rangka"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            {{-- No Mesin --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Mesin
                                    <span class="text-red-500">*</span></label>
                                <input name="no_mesin" required placeholder="Masukkan No Mesin"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            {{-- No BPKB --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No BPKB
                                    <span class="text-red-500">*</span></label>
                                <input name="no_bpkb" required placeholder="Masukkan No BPKB"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    {{-- Dokumen & Biaya --}}
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                            <i class="fa fa-file-alt mr-1"></i> Dokumen & Biaya
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Batas Biaya <span
                                        class="text-red-500">*</span></label>
                                <input name="batas_biaya" required type="number" placeholder="0"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Masa Berlaku <span
                                        class="text-red-500">*</span></label>
                                <input name="masa_berlaku" required type="date"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dokumen <span
                                        class="text-red-500">*</span></label>
                                <input name="dokumen[]" type="file" multiple required id="dokumen_tambah"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                                    onchange="previewDokumen(this,'preview_dokumen_tambah')">
                                <div id="preview_dokumen_tambah" class="mt-2 space-y-1 hidden"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Servis & Kilometer --}}
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                            <i class="fa fa-tachometer-alt mr-1"></i> Servis & Kilometer
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach ([['kilometer_sekarang', 'Kilometer Sekarang'], ['limit_km_service', 'Limit KM Service'], ['limit_bulan_service', 'Limit Bulan Service'], ['km_terakhir_service', 'KM Terakhir Service']] as [$name, $label])
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $label }}
                                        <span class="text-red-500">*</span></label>
                                    <input name="{{ $name }}" required type="number" placeholder="0"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                            @endforeach
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Terakhir Service
                                    <span class="text-red-500">*</span></label>
                                <input name="tanggal_terakhir_service" required type="date"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                            <i class="fa fa-toggle-on mr-1"></i> Status
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Service <span
                                        class="text-red-500">*</span></label>
                                <select name="status_service" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="aman">Aman</option>
                                    <option value="service">Service</option>
                                </select>
                            </div>
                            <div>
                                {{-- <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Kendaraan <span
                                        class="text-red-500">*</span></label> --}}
                                <select hidden name="status_kendaraan" required 
                                    name="status_kendaraan" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="tersedia">Tersedia</option>
                                    <option value="disewa">Disewa</option>
                                    <option value="bermasalah" disabled>Bermasalah (otomatis dari menu)</option>
                                    <option value="service" disabled>Service (otomatis dari menu)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100 shrink-0">
                    <button type="button" onclick="closeAllModals()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl border border-gray-200 text-gray-600 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <i class="fa fa-times text-xs"></i> Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-xl bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                        <i class="fa fa-save text-xs"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ======================================
    MODAL EDIT
====================================== --}}
    <div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col"
            style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 shrink-0">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Kendaraan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data kendaraan</p>
                </div>
                <button onclick="closeAllModals()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" enctype="multipart/form-data"
                class="flex flex-col flex-1 overflow-hidden">
                @csrf
                @method('PUT')
                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-6">

                    {{-- Informasi Dasar --}}
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                            <i class="fa fa-user mr-1"></i> Informasi Dasar
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Kendaraan</label>
                                <select name="jenis_id" id="e_jenis_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    @foreach ($jenis as $j)
                                        <option value="{{ $j->id }}">{{ $j->nama_jenis }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor Polisi</label>
                                <input name="nopol" id="e_nopol"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga Sewa / Hari</label>
                                <input name="harga_sewa_per_hari" id="e_harga_sewa_per_hari" type="number"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga Sewa / Bulan</label>
                                <input name="harga_sewa_per_jam" id="e_harga_sewa_per_jam" type="number"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pemilik</label>
                                <div class="relative">
                                    <input type="text" name="nama_pemilik" id="e_nama_pemilik" autocomplete="off"
                                        placeholder="Ketik nama member..."
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <input type="hidden" name="member_id" id="e_member_id">
                                    <div id="member-result-edit"
                                        class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-48 overflow-y-auto"></div>
                                </div>
                                <p id="member_warning_edit" class="hidden mt-1 text-xs text-red-500 flex items-center gap-1">
                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Member tidak ditemukan
                                </p>
                            </div>
                        
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Merk</label>
                                <input name="merk" id="e_merk"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Warna</label>
                                <input name="warna" id="e_warna"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
                                <div class="relative">
                                    <input name="alamat" id="e_alamat"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 pr-8">
                                    <button type="button" id="e_alamat_edit_btn"
                                        onclick="document.getElementById('e_alamat').readOnly=false;this.style.display='none'"
                                        style="display:none"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 hover:text-blue-500"
                                        title="Edit alamat manual"><i class="fa fa-pen"></i></button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Foto (baru)</label>
                                <input name="foto" type="file"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    {{-- Data Registrasi --}}
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                            <i class="fa fa-id-card mr-1"></i> Data Registrasi
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            {{-- Tahun Pembuatan --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-600 mb-1.5">Tahun Pembuatan</label>
                                <select name="tahun_pembuatan" id="e_tahun_pembuatan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">-- Pilih Tahun --</option>
                                    @for ($y = date('Y'); $y >= 1980; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            {{-- Tahun Perakitan --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-600 mb-1.5">Tahun Perakitan</label>
                                <select name="tahun_perakitan" id="e_tahun_perakitan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">-- Pilih Tahun --</option>
                                    @for ($y = date('Y'); $y >= 1980; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            {{-- Isi Silinder --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Isi Silinder (cc)</label>
                                <input name="isi_silinder" id="e_isi_silinder"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            {{-- Bahan Bakar (dropdown) --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bahan Bakar</label>
                                <select name="bahan_bakar" id="e_bahan_bakar"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">-- Pilih Bahan Bakar --</option>
                                    <option value="Bensin">Bensin</option>
                                    <option value="Solar">Solar</option>
                                    <option value="Pertamax">Pertamax</option>
                                    <option value="Pertalite">Pertalite</option>
                                    <option value="Listrik">Listrik</option>
                                    <option value="Hybrid">Hybrid</option>
                                </select>
                            </div>
                            {{-- Warna TNKB (dropdown) --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Warna TNKB</label>
                                <select name="warna_tnkb" id="e_warna_tnkb"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="">-- Pilih Warna TNKB --</option>
                                    <option value="Hitam">Hitam</option>
                                    <option value="Putih">Putih</option>
                                    <option value="Merah">Merah</option>
                                </select>
                            </div>
                            {{-- Kode Lokasi --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Lokasi</label>
                                <input name="kode_lokasi" id="e_kode_lokasi"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            {{-- No Urut Pendaftaran --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Urut Pendaftaran</label>
                                <input name="no_urut_pendaftaran" id="e_no_urut_pendaftaran"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            {{-- No Rangka --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Rangka</label>
                                <input name="no_rangka" id="e_no_rangka"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            {{-- No Mesin --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Mesin</label>
                                <input name="no_mesin" id="e_no_mesin"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            {{-- No BPKB --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No BPKB</label>
                                <input name="no_bpkb" id="e_no_bpkb"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    {{-- Dokumen & Biaya --}}
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                            <i class="fa fa-file-alt mr-1"></i> Dokumen & Biaya
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Batas Biaya</label>
                                <input name="batas_biaya" id="e_batas_biaya" type="number"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Masa Berlaku</label>
                                <input name="masa_berlaku" id="e_masa_berlaku" type="date"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dokumen (baru)</label>
                                <input name="dokumen[]" type="file" multiple id="dokumen_edit"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                                    onchange="previewDokumen(this,'preview_dokumen_edit')">
                                <div id="preview_dokumen_edit" class="mt-2 space-y-1 hidden"></div>
                                <div id="dokumenPreview" class="mt-2 hidden">
                                    <p class="text-[10px] text-gray-400 mb-1">Dokumen tersimpan:</p>
                                    <div id="dokumenLamaList" class="space-y-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Servis & Kilometer --}}
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                            <i class="fa fa-tachometer-alt mr-1"></i> Servis & Kilometer
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach ([['kilometer_sekarang', 'Kilometer Sekarang'], ['limit_km_service', 'Limit KM Service'], ['limit_bulan_service', 'Limit Bulan Service'], ['km_terakhir_service', 'KM Terakhir Service']] as [$name, $label])
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $label }}</label>
                                    <input name="{{ $name }}" id="e_{{ $name }}" type="number"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                </div>
                            @endforeach
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Terakhir
                                    Service</label>
                                <input name="tanggal_terakhir_service" id="e_tanggal_terakhir_service" type="date"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-widest text-gray-400 pb-2 border-b border-gray-100 mb-3">
                            <i class="fa fa-toggle-on mr-1"></i> Status
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Service</label>
                                <select name="status_service" id="e_status_service"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="aman">Aman</option>
                                    <option value="service">Service</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Kendaraan</label>
                                {{-- Di modal edit --}}
                                <select name="status_kendaraan" id="e_status_kendaraan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                                    <option value="tersedia">Tersedia</option>
                                    <option value="disewa">Disewa</option>
                                    <option value="bermasalah">Bermasalah (otomatis dari menu)</option>
                                    <option value="service" disabled>Service (otomatis dari menu service)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100 shrink-0">
                    <button type="button" onclick="closeAllModals()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl border border-gray-200 text-gray-600 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <i class="fa fa-times text-xs"></i> Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-xl bg-yellow-500 hover:bg-yellow-600 text-white transition-colors">
                        <i class="fa fa-save text-xs"></i> Update
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
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0">
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
        // -- MODAL HELPERS --------------------------------------
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            openModal('modalTambah');
        });
        @endif

        function closeAllModals() {            ['modalTambah', 'modalEdit', 'modalDetail', 'modalStatus'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    el.classList.add('hidden');
                    el.classList.remove('flex');
                }
            });
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAllModals();
        });

        // Click-outside to close
        ['modalTambah', 'modalEdit', 'modalDetail', 'modalStatus'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('click', function(e) {
                if (e.target === el) closeAllModals();
            });
        });

        // -- MODAL STATUS --------------------------------------
        function ubahStatus(id, statusSekarang) {
            document.getElementById('formStatus').action = '/admin/kendaraan/' + id;
            document.getElementById('statusKendaraanInput').value = statusSekarang;
            openModal('modalStatus');
        }

        function pilihStatus(status) {
            document.getElementById('statusKendaraanInput').value = status;
        }

        function closeModalStatus() {
            var el = document.getElementById('modalStatus');
            el.classList.add('hidden');
            el.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // -- HELPERS --------------------------------------
        function fmtRupiah(val) {
            var n = parseInt(val) || 0;
            return 'Rp ' + n.toLocaleString('id-ID');
        }

        function fmtKm(val) {
            var n = parseInt(val) || 0;
            return n.toLocaleString('id-ID') + ' km';
        }

        function badgeStatus(val) {
            var map = {
                tersedia: ['bg-emerald-100 text-emerald-700', 'bg-emerald-500', 'Tersedia'],
                disewa: ['bg-blue-100 text-blue-700', 'bg-blue-500', 'Disewa'],
                service: ['bg-amber-100 text-amber-700', 'bg-amber-400', 'Service'],
                bermasalah: ['bg-red-100 text-red-700', 'bg-red-500', 'Bermasalah'],
            };
            var m = map[val] || map['service'];
            return '<span class="inline-flex items-center gap-1.5 ' + m[0] +
                ' text-xs font-semibold px-2.5 py-1 rounded-full">' +
                '<span class="w-1.5 h-1.5 rounded-full ' + m[1] + ' inline-block"></span>' + m[2] + '</span>';
        }

        function badgeSvc(val) {
            if (val === 'aman') {
                return '<span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-full">' +
                    '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Aman</span>';
            }
            return '<span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-semibold px-2.5 py-1 rounded-full">' +
                '<span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>Service</span>';
        }

        // -- MODAL DETAIL --------------------------------------
        document.querySelectorAll('.btn-detail').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var d = this.dataset;

                document.getElementById('d_title').textContent = (d.merk || '—') + ' — ' + (d.nopol || '—');

                var fotoWrap = document.getElementById('d_foto_wrap');
                var fotoImg = document.getElementById('d_foto_img');
                if (d.foto) {
                    fotoImg.src = '/storage/' + d.foto;
                    fotoWrap.classList.remove('hidden');
                } else {
                    fotoWrap.classList.add('hidden');
                }

                document.getElementById('d_user').textContent = d.user || '—';
                document.getElementById('d_jenis').textContent = d.jenis || '—';
                document.getElementById('d_merk').textContent = d.merk || '—';
                document.getElementById('d_warna').textContent = d.warna || '—';
                document.getElementById('d_nopol').textContent = d.nopol || '—';
                document.getElementById('d_nama_pemilik').textContent = d.nama_pemilik || '—';
                document.getElementById('d_alamat').textContent = d.alamat || '—';
                document.getElementById('d_harga_jam').textContent = fmtRupiah(d.harga_jam);
                document.getElementById('d_harga_hari').textContent = fmtRupiah(d.harga_hari);

                document.getElementById('d_tahun_pembuatan').textContent = d.tahun_pembuatan || '—';
                document.getElementById('d_tahun_perakitan').textContent = d.tahun_perakitan || '—';
                document.getElementById('d_isi_silinder').textContent = d.isi_silinder ? d.isi_silinder +
                    ' cc' : '—';
                document.getElementById('d_bahan_bakar').textContent = d.bahan_bakar || '—';
                document.getElementById('d_warna_tnkb').textContent = d.warna_tnkb || '—';
                document.getElementById('d_kode_lokasi').textContent = d.kode_lokasi || '—';
                document.getElementById('d_no_urut').textContent = d.no_urut || '—';
                document.getElementById('d_no_rangka').textContent = d.no_rangka || '—';
                document.getElementById('d_no_mesin').textContent = d.no_mesin || '—';
                document.getElementById('d_no_bpkb').textContent = d.no_bpkb || '—';

                document.getElementById('d_batas_biaya').textContent = fmtRupiah(d.batas_biaya);
                document.getElementById('d_masa_berlaku').textContent = d.masa_berlaku || '—';
                var dokWrap = document.getElementById('d_dokumen_wrap');
                if (d.dokumen) {
                    dokWrap.innerHTML = '<a href="/storage/' + d.dokumen + '" target="_blank"' +
                        ' class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 transition-colors">' +
                        '<i class="fa fa-file-alt text-xs"></i> Lihat Dokumen</a>';
                } else {
                    dokWrap.textContent = '—';
                }

                document.getElementById('d_km_sekarang').textContent = fmtKm(d.km_sekarang);
                document.getElementById('d_limit_km').textContent = fmtKm(d.limit_km);
                document.getElementById('d_limit_bln').textContent = d.limit_bln ? d.limit_bln + ' bulan' :
                    '—';
                document.getElementById('d_km_svc').textContent = fmtKm(d.km_svc);
                document.getElementById('d_tgl_svc').textContent = d.tgl_svc || '—';

                document.getElementById('d_status_service').innerHTML = badgeSvc(d.status_service);
                document.getElementById('d_status_kendaraan').innerHTML = badgeStatus(d.status_kendaraan);

                openModal('modalDetail');
            });
        });

        // -- MEMBER AUTOCOMPLETE (inline data) -------------------------
        const memberData = {!! json_encode($members->map(function($m) { return ['id' => $m->id, 'nama' => $m->nama, 'kontak' => $m->kontak ?? '', 'alamat' => $m->alamat ?? '', 'jenis_member' => $m->jenis_member ?? '']; })) !!};

        function initMemberAutocomplete(inputId, resultId, hiddenId, alamatId, warningId, editBtnId) {
            const input   = document.getElementById(inputId);
            const result  = document.getElementById(resultId);
            const hidden  = document.getElementById(hiddenId);
            const warning = document.getElementById(warningId);
            if (!input || !result) return;

            input.addEventListener('keyup', function() {
                const keyword = this.value.toLowerCase().trim();
                result.innerHTML = '';
                if (hidden) hidden.value = '';

                if (keyword.length < 1) {
                    result.classList.add('hidden');
                    if (warning) warning.classList.add('hidden');
                    return;
                }

                const filtered = memberData.filter(m => m.nama.toLowerCase().includes(keyword));

                if (filtered.length === 0) {
                    result.innerHTML = '<div class="px-3 py-2 text-xs text-red-500 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Member tidak ditemukan</div>';
                    result.classList.remove('hidden');
                    if (warning) warning.classList.remove('hidden');
                    return;
                }

                if (warning) warning.classList.add('hidden');

                filtered.forEach(function(m) {
                    const item = document.createElement('div');
                    item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0';
                    item.innerHTML = '<div class="text-xs font-semibold text-gray-800">' + m.nama + '</div>'
                                   + '<div class="text-[11px] text-gray-400 mt-0.5">'
                                   + (m.kontak ? '<span class="mr-2"><i class="fa fa-phone text-[10px]"></i> ' + m.kontak + '</span>' : '')
                                   + '<span class="capitalize text-blue-400">' + (m.jenis_member || '') + '</span>'
                                   + '</div>';

                    item.onclick = function() {
                        input.value = m.nama;
                        if (hidden) hidden.value = m.id;
                        result.classList.add('hidden');
                        if (warning) warning.classList.add('hidden');

                        // Auto-fill alamat
                        const alamatEl = document.getElementById(alamatId);
                        const editBtn  = document.getElementById(editBtnId);
                        if (alamatEl && m.alamat) {
                            alamatEl.value    = m.alamat;
                            alamatEl.readOnly = true;
                            if (editBtn) editBtn.style.display = '';
                        }
                    };
                    result.appendChild(item);
                });

                result.classList.remove('hidden');
            });

            // Tutup saat klik di luar
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !result.contains(e.target)) {
                    result.classList.add('hidden');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMemberAutocomplete('nama_pemilik',   'member-result-tambah', 'member_id',   'alamat',   'member_warning_tambah', 'alamat_edit_btn');
            initMemberAutocomplete('e_nama_pemilik', 'member-result-edit',   'e_member_id', 'e_alamat', 'member_warning_edit',   'e_alamat_edit_btn');
        });

        // -- MODAL EDIT --------------------------------------
        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('formEdit').action = '/admin/kendaraan/' + this.dataset.id;

                var dokumen = this.dataset.dokumen;
                var preview = document.getElementById('dokumenPreview');
                var link = document.getElementById('dokumenLink');
                if (dokumen) {
                    preview.classList.remove('hidden');
                    link.href = '/storage/' + dokumen;
                } else {
                    preview.classList.add('hidden');
                }

                var fields = [
                    'jenis_id', 'nopol', 'nama_pemilik', 'alamat', 'merk',
                    'tahun_pembuatan', 'tahun_perakitan', 'isi_silinder', 'warna',
                    'no_rangka', 'no_mesin', 'no_bpkb', 'warna_tnkb', 'bahan_bakar',
                    'kode_lokasi', 'no_urut_pendaftaran', 'batas_biaya', 'masa_berlaku',
                    'kilometer_sekarang', 'limit_km_service', 'limit_bulan_service',
                    'km_terakhir_service', 'tanggal_terakhir_service',
                    'status_service', 'status_kendaraan',
                    'harga_sewa_per_hari', 'harga_sewa_per_jam',
                ];

                var self = this;
                fields.forEach(function(f) {
                    var el = document.getElementById('e_' + f);
                    if (!el) return;
                    var val = self.dataset[f] || '';
                    if (el.tagName === 'SELECT') {
                        Array.from(el.options).forEach(function(opt) {
                            opt.selected = opt.value === val;
                        });
                    } else {
                        el.value = val;
                    }
                });

                var memberHidden = document.getElementById('e_member_id');
                if (memberHidden) {
                    memberHidden.value = self.dataset.member_id || '';
                }

                openModal('modalEdit');
            });
        });

        // -- SEARCH (client-side, hanya filter baris yang ada di halaman ini) --
        function filterKendaraanTable(q) {
            const keyword = q.toLowerCase();
            document.querySelectorAll('#kendaraanTableBody tr[data-search]').forEach(row => {
                row.style.display = row.dataset.search.includes(keyword) ? '' : 'none';
            });
        }

        // -- POPUP ALERT --------------------------------------
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
    </script>

@endsection
