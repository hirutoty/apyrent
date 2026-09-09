@extends('admin.layouts.app')

@section('title', 'Data KIR Kendaraan')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data KIR Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data uji berkala kendaraan armada</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openModalTambah()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah KIR
            </button>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total KIR</p>
                    <h2 class="text-3xl font-bold text-gray-800 mt-1">{{ $data->total() }}</h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                    <i class="fa fa-id-card text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Masih Aktif</p>
                    <h2 class="text-3xl font-bold text-green-600 mt-1">
                        {{ $data->filter(fn($d) => $d->masa_berlaku && \Carbon\Carbon::parse($d->masa_berlaku)->gte(now()))->count() }}
                    </h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-500 flex items-center justify-center">
                    <i class="fa fa-circle-check text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Sudah Kedaluwarsa</p>
                    <h2 class="text-3xl font-bold text-red-600 mt-1">
                        {{ $data->filter(fn($d) => $d->masa_berlaku && \Carbon\Carbon::parse($d->masa_berlaku)->lt(now()))->count() }}
                    </h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                    <i class="fa fa-circle-xmark text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Biaya</p>
                    <h2 class="text-xl font-bold text-emerald-600 mt-1">Rp {{ number_format($data->sum('biaya'), 0, ',', '.') }}</h2>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                    <i class="fa fa-wallet text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar KIR Kendaraan</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->total() }} total data KIR</p>
            </div>
            <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" placeholder="Cari nopol, merk, no uji..."
                        value="{{ request('search') }}"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-64">
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fa fa-search text-xs"></i> Cari
                </button>
                <a href="{{ route('kir.pdf', request()->query()) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors">
                    <i class="fa fa-file-pdf text-xs"></i> Export PDF
                </a>
                @if(request()->hasAny(['search','status','bulan','tahun']))
                    <a href="{{ request()->url() }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa fa-rotate-left text-xs"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kendaraan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No Uji</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Lokasi Uji</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status Uji</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Masa Berlaku</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl Ketentuan Bayar</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Biaya</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Bukti</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Lampiran</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Persetujuan</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        @php
                            $masaBerlaku   = \Carbon\Carbon::parse($item->masa_berlaku);
                            $selisihHari   = (int) now()->startOfDay()->diffInDays($masaBerlaku->startOfDay(), false);
                            $isKadaluarsa  = $selisihHari < 0;
                            $isReminder    = !$isKadaluarsa && $selisihHari <= $reminder;
                        @endphp
                        <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-50 hover:bg-blue-50/50 transition-colors">
                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $data->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-car text-blue-400 text-xs"></i>
                                    </div>
                                    <div class="flex flex-col leading-tight">
                                        <span class="text-sm font-semibold text-gray-800">{{ $item->kendaraan->nopol ?? '-' }}</span>
                                        <span class="text-xs text-gray-500">{{ $item->kendaraan->merk ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $item->no_uji }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $item->lokasi_uji ?? '-' }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $item->status_uji === 'uji berkala' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ ucfirst($item->status_uji ?? '-') }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm text-gray-700">{{ $masaBerlaku->translatedFormat('j F Y') }}</span>
                                    @if($isKadaluarsa)
                                        <span class="inline-flex items-center gap-1 text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full w-fit font-semibold">
                                            <i class="fa fa-circle-exclamation text-[10px]"></i>
                                            Kadaluarsa {{ abs($selisihHari) }} hari lalu
                                        </span>
                                    @elseif($isReminder)
                                        <span class="inline-flex items-center gap-1 text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full w-fit font-semibold">
                                            <i class="fa fa-triangle-exclamation text-[10px]"></i>
                                            {{ $selisihHari == 0 ? 'Hari ini' : ($selisihHari == 1 ? 'Besok' : $selisihHari . ' hari lagi') }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                             {{-- Tgl Ketentuan Bayar --}}
                            <td class="px-4 py-3.5 text-xs text-gray-600">
                                {{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('d M Y') : '-' }}
                            </td>
                            
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                    Rp {{ number_format($item->biaya, 0, ',', '.') }}
                                </span>
                            </td>
                           
                            {{-- Bukti --}}
                            <td class="px-4 py-3.5">
                                @if($item->image)
                                    <a href="{{ asset($item->image) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-600 hover:bg-amber-100 transition">
                                        <i class="fa fa-image text-[9px]"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            {{-- Lampiran --}}
                            <td class="px-4 py-3.5">
                                @if($item->attachments->isNotEmpty())
                                    <div class="flex flex-col gap-0.5">
                                        @foreach($item->attachments as $att)
                                            <a href="{{ asset($att->file_path) }}" target="_blank"
                                                class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:text-blue-800 underline max-w-[150px] truncate"
                                                title="{{ $att->file_name }}">
                                                <i class="fa fa-paperclip text-[9px] flex-shrink-0"></i>
                                                {{ $att->file_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            {{-- Status --}}
                            <td class="px-4 py-3.5">
                                @if($item->status === 'aktif')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                @elseif($item->status === 'expired')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Expired</span>
                                @elseif($item->status === 'tidak_aktif')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Tidak Aktif</span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            {{-- Persetujuan --}}
                            <td class="px-4 py-3.5">
                                @if($item->persetujuan === 'Disetujui')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui
                                    </span>
                                @elseif($item->persetujuan === 'Ditolak')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i> Ditolak
                                    </span>
                                @elseif($item->persetujuan === 'Pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        <i class="fa-solid fa-clock text-[10px]"></i> Pending
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">

                                    {{-- Detail --}}
                                    <button type="button"
                                        onclick="openDetailModal('kir', {{ $item->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                        <i class="fa fa-eye text-xs"></i>
                                        Detail
                                    </button>

                                    {{-- Perpanjang --}}
                                    @if($isKadaluarsa || $isReminder)
                                    <button type="button"
                                        onclick="openModalPerpanjang({{ $item->id }}, '{{ $item->kendaraan->nopol ?? '-' }}', '{{ $item->kendaraan->merk ?? '-' }}', '{{ $item->no_uji }}', '{{ $item->biaya }}', '{{ $item->masa_berlaku ? \Carbon\Carbon::parse($item->masa_berlaku)->format('Y-m-d') : '' }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                        <i class="fa fa-rotate-right text-xs"></i>
                                        Perpanjang
                                    </button>
                                    @endif

                                    {{-- Edit --}}
                                    <button type="button"
                                        onclick="openModalEdit({{ $item->id }}, {{ $item->kendaraan_id }}, '{{ addslashes($item->no_ktp) }}', '{{ addslashes($item->nama_ktp) }}', '{{ addslashes($item->lokasi_uji) }}', '{{ addslashes($item->penguji) }}', '{{ $item->status_uji }}', '{{ addslashes($item->no_uji) }}', '{{ $item->masa_berlaku ? \Carbon\Carbon::parse($item->masa_berlaku)->format('Y-m-d') : '' }}', '{{ $item->biaya }}', '{{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('Y-m-d') : '' }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                        <i class="fa fa-edit text-xs"></i>
                                        Edit
                                    </button>

                                    {{-- Hapus --}}
                                    <form action="{{ route('kir.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus data KIR ini?')" class="inline">
                                        @csrf @method('DELETE')
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
                            <td colspan="12" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-id-card text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data KIR kendaraan</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah KIR" untuk menambahkan data baru</p>
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

{{-- ==============================
     MODAL TAMBAH
============================== --}}
<div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div>
                <h2 class="text-base font-bold text-gray-800">Tambah KIR Kendaraan</h2>
                <p class="text-xs text-gray-500 mt-0.5">Pengajuan akan masuk ke antrian approval Superadmin</p>
            </div>
            <button onclick="closeModalTambah()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form action="{{ route('kir.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan <span class="text-red-500">*</span></label>
                    <select name="kendaraan_id" id="tambah_kendaraan_id" required onchange="loadKendaraanDetail(this.value)"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($kendaraan as $k)
                            <option value="{{ $k->id }}" {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>{{ $k->nopol }} - {{ $k->merk }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No KTP <span class="text-red-500">*</span></label>
                    <input type="text" name="no_ktp" required placeholder="Nomor KTP"
                        value="{{ old('no_ktp') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama KTP <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_ktp" required placeholder="Nama sesuai KTP"
                        value="{{ old('nama_ktp') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi Uji <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi_uji" required placeholder="Contoh: UPTD PKB Kota X"
                        value="{{ old('lokasi_uji') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penguji</label>
                    <input type="text" name="penguji" placeholder="Nama penguji (opsional)"
                        value="{{ old('penguji') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Uji <span class="text-red-500">*</span></label>
                    <select name="status_uji" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Status --</option>
                        <option value="uji berkala" {{ old('status_uji') === 'uji berkala' ? 'selected' : '' }}>Uji Berkala</option>
                        <option value="uji pertama" {{ old('status_uji') === 'uji pertama' ? 'selected' : '' }}>Uji Pertama</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Uji <span class="text-red-500">*</span></label>
                    <input type="text" name="no_uji" required placeholder="Nomor uji KIR"
                        value="{{ old('no_uji') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Masa Berlaku <span class="text-red-500">*</span></label>
                    <input type="date" name="masa_berlaku" id="tambah_masa_berlaku" required readonly
                        value="{{ old('masa_berlaku') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">Otomatis tgl ketentuan bayar + 6 bulan</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="biaya" required placeholder="Biaya KIR"
                        value="{{ old('biaya') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Ketentuan Bayar <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_bayar" id="tambah_tanggal_bayar" required
                        value="{{ old('tanggal_bayar', now()->format('Y-m-d')) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Lampiran <span class="text-red-500">*</span>
                    </label>
                    <label for="bukti_attachment_kir"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <i class="fa-solid fa-paperclip text-xl text-gray-400 mb-1"></i>
                        <span class="text-xs text-gray-500">Klik untuk upload lampiran</span>
                        <span class="text-xs text-gray-400">(Maks 5MB per file)</span>
                    </label>
                    <input type="file" name="bukti_attachment[]" id="bukti_attachment_kir" class="hidden" multiple required
                        onchange="renderListAttachmentKir(this, 'listAttachmentKirTambah')">
                    <ul id="listAttachmentKirTambah" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <i class="fa fa-circle-exclamation text-[10px]"></i>
                        Wajib upload minimal 1 lampiran
                    </p>
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModalTambah()"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-paper-plane text-sm"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==============================
     MODAL EDIT
============================== --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div>
                <h2 class="text-base font-bold text-gray-800">Edit KIR Kendaraan</h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui data KIR kendaraan</p>
            </div>
            <button onclick="closeModalEdit()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="formEdit" method="POST" enctype="multipart/form-data" class="px-6 py-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan</label>
                    <select name="kendaraan_id" id="edit_kendaraan_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        @foreach($kendaraan as $k)
                            <option value="{{ $k->id }}">{{ $k->nopol }} - {{ $k->merk }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No KTP</label>
                    <input type="text" name="no_ktp" id="edit_no_ktp"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama KTP</label>
                    <input type="text" name="nama_ktp" id="edit_nama_ktp"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi Uji</label>
                    <input type="text" name="lokasi_uji" id="edit_lokasi_uji"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penguji</label>
                    <input type="text" name="penguji" id="edit_penguji"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Uji</label>
                    <select name="status_uji" id="edit_status_uji"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="uji berkala">Uji Berkala</option>
                        <option value="uji pertama">Uji Pertama</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No Uji</label>
                    <input type="text" name="no_uji" id="edit_no_uji"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Masa Berlaku</label>
                    <input type="date" name="masa_berlaku" id="edit_masa_berlaku" readonly
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">Otomatis tgl ketentuan bayar + 6 bulan</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya</label>
                    <input type="number" min="0" name="biaya" id="edit_biaya"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Ketentuan Bayar</label>
                    <input type="date" name="tanggal_bayar" id="edit_tanggal_bayar"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dokumen / Bukti (kosongkan jika tidak diubah)</label>
                    <input type="file" name="image" accept="image/*,.pdf"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModalEdit()"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-save text-sm"></i> Update Data
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==============================
     MODAL PERPANJANG
============================== --}}
<div id="modalPerpanjang" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div>
                <h2 class="text-base font-bold text-gray-800">Perpanjang KIR Kendaraan</h2>
                <p class="text-xs text-gray-500 mt-0.5">Masa berlaku baru = masa berlaku lama + 6 bulan. Pengajuan akan masuk approval.</p>
            </div>
            <button onclick="closeModalPerpanjang()" class="text-gray-400 hover:text-red-500 transition-colors text-lg">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="formPerpanjang" method="POST" enctype="multipart/form-data" class="px-6 py-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kendaraan</label>
                    <div class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-sm text-gray-700 cursor-not-allowed select-none">
                        <span id="perpanjang_kendaraan_text">-</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">No Uji Baru <span class="text-red-500">*</span></label>
                    <input type="text" name="no_uji" id="perpanjang_no_uji" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                        placeholder="Nomor uji baru">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Biaya <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="biaya" id="perpanjang_biaya" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Masa Berlaku Baru</label>
                    <div class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-sm text-gray-500 cursor-not-allowed">
                        <span id="perpanjang_masa_berlaku_text">Otomatis masa berlaku lama + 6 bulan</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Dihitung otomatis saat disetujui</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Ketentuan Bayar</label>
                    <input type="date" name="tanggal_bayar" id="perpanjang_tanggal_bayar"
                        value="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Lampiran <span class="text-red-500">*</span>
                    </label>
                    <label for="bukti_attachment_perpanjang"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <i class="fa-solid fa-paperclip text-xl text-gray-400 mb-1"></i>
                        <span class="text-xs text-gray-500">Klik untuk upload lampiran</span>
                        <span class="text-xs text-gray-400">(Maks 5MB per file)</span>
                    </label>
                    <input type="file" name="bukti_attachment[]" id="bukti_attachment_perpanjang" class="hidden" multiple required
                        onchange="renderListAttachmentKir(this, 'listAttachmentKirPerpanjang')">
                    <ul id="listAttachmentKirPerpanjang" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                        <i class="fa fa-circle-exclamation text-[10px]"></i>
                        Wajib upload minimal 1 lampiran
                    </p>
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModalPerpanjang()"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnPerpanjangKir"
                    class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-rotate-right"></i> Kirim Perpanjangan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ALERT --}}
@if (session('success') || session('error') || session('info') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox"
        class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @elseif(session('info'))
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-600 text-xl"><i class="fa fa-info-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Info</p><p class="text-xs text-gray-500 mt-0.5">{{ session('info') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">{{ session('error') ? 'Gagal!' : 'Terjadi Kesalahan!' }}</p>
                <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4 space-y-0.5">
                    @if(session('error'))<li>{{ session('error') }}</li>@endif
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none flex-shrink-0"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
</style>

<script>
// ── AUTO MASA BERLAKU: tanggal_bayar + 6 bulan ────────────────────────────
function hitungMasaBerlakuKir(inputId, outputId) {
    const input  = document.getElementById(inputId);
    const output = document.getElementById(outputId);
    if (!input || !output) return;
    input.addEventListener('change', function () {
        if (!this.value) { output.value = ''; return; }
        const d = new Date(this.value);
        d.setMonth(d.getMonth() + 6);
        const y  = d.getFullYear();
        const m  = String(d.getMonth() + 1).padStart(2, '0');
        const dy = String(d.getDate()).padStart(2, '0');
        output.value = `${y}-${m}-${dy}`;
    });
}
hitungMasaBerlakuKir('tambah_tanggal_bayar', 'tambah_masa_berlaku');
hitungMasaBerlakuKir('edit_tanggal_bayar',   'edit_masa_berlaku');

// Inisialisasi masa berlaku di form Tambah jika old('tanggal_bayar') sudah ada
(function () {
    const tgl = document.getElementById('tambah_tanggal_bayar');
    const mb  = document.getElementById('tambah_masa_berlaku');
    if (!tgl || !mb) return;
    // Jika sudah ada nilai old() tapi masa_berlaku kosong, hitung ulang
    if (tgl.value && !mb.value) {
        const d = new Date(tgl.value);
        d.setMonth(d.getMonth() + 6);
        mb.value = d.getFullYear() + '-'
            + String(d.getMonth() + 1).padStart(2, '0') + '-'
            + String(d.getDate()).padStart(2, '0');
    }
})();

// ── MODAL TAMBAH ──────────────────────────────────────────
const modalTambah = document.getElementById('modalTambah');
function openModalTambah() { modalTambah.classList.remove('hidden'); modalTambah.classList.add('flex'); }
function closeModalTambah() { modalTambah.classList.add('hidden'); modalTambah.classList.remove('flex'); }
modalTambah.addEventListener('click', e => { if (e.target === modalTambah) closeModalTambah(); });

// ── MODAL EDIT ────────────────────────────────────────────
const modalEdit = document.getElementById('modalEdit');
function openModalEdit(id, kendaraanId, noKtp, namaKtp, lokasiUji, penguji, statusUji, noUji, masaBerlaku, biaya, tanggalBayar) {
    document.getElementById('formEdit').action = `/admin/kir/${id}`;
    document.getElementById('edit_kendaraan_id').value  = kendaraanId;
    document.getElementById('edit_no_ktp').value        = noKtp;
    document.getElementById('edit_nama_ktp').value      = namaKtp;
    document.getElementById('edit_lokasi_uji').value    = lokasiUji;
    document.getElementById('edit_penguji').value       = penguji;
    document.getElementById('edit_status_uji').value    = statusUji;
    document.getElementById('edit_no_uji').value        = noUji;
    document.getElementById('edit_masa_berlaku').value  = masaBerlaku;
    document.getElementById('edit_biaya').value         = biaya;
    document.getElementById('edit_tanggal_bayar').value = tanggalBayar;
    modalEdit.classList.remove('hidden'); modalEdit.classList.add('flex');
}

// ── MODAL PERPANJANG ──────────────────────────────────────
const modalPerpanjang = document.getElementById('modalPerpanjang');
function openModalPerpanjang(id, nopol, merk, noUji, biaya, masaBerlaku) {
    document.getElementById('formPerpanjang').action = `/admin/kir/${id}/perpanjang`;
    document.getElementById('perpanjang_kendaraan_text').innerText = `${nopol} — ${merk}`;
    document.getElementById('perpanjang_no_uji').value   = noUji;
    document.getElementById('perpanjang_biaya').value    = biaya;
    // Tampilkan preview masa berlaku baru
    if (masaBerlaku) {
        const d = new Date(masaBerlaku);
        d.setMonth(d.getMonth() + 6);
        const y = d.getFullYear(), m = String(d.getMonth()+1).padStart(2,'0'), dy = String(d.getDate()).padStart(2,'0');
        document.getElementById('perpanjang_masa_berlaku_text').innerText = `${dy}/${m}/${y} (estimasi setelah approval)`;
    }
    modalPerpanjang.classList.remove('hidden'); modalPerpanjang.classList.add('flex');
}
function closeModalPerpanjang() { modalPerpanjang.classList.add('hidden'); modalPerpanjang.classList.remove('flex'); }
modalPerpanjang.addEventListener('click', e => { if (e.target === modalPerpanjang) closeModalPerpanjang(); });

// Anti double-submit perpanjang
(function() {
    const f = document.getElementById('formPerpanjang');
    const b = document.getElementById('btnPerpanjangKir');
    if (!f || !b) return;
    f.addEventListener('submit', function() {
        b.disabled = true;
        b.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
        b.classList.add('opacity-60', 'cursor-not-allowed');
    });
})();

// ── POPUP ALERT ──────────────────────────────────────────
(function() {
    const overlay = document.getElementById('alertOverlay');
    const box     = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(() => { overlay.style.opacity = '1'; overlay.style.pointerEvents = 'auto'; box.style.transform = 'translateY(0)'; }, 80);
    const timer = setTimeout(closeAlert, 4500);
    overlay.addEventListener('click', e => { if (e.target === overlay) closeAlert(); });
    function closeAlert() { clearTimeout(timer); overlay.style.opacity = '0'; overlay.style.pointerEvents = 'none'; box.style.transform = 'translateY(-16px)'; }
    window.closeAlert = closeAlert;
})();

// ── Render list attachment ─────────────────────────────────────────────────
function renderListAttachmentKir(input, listId) {
    const ul = document.getElementById(listId);
    if (!ul) return;
    ul.innerHTML = '';
    Array.from(input.files).forEach(function(file) {
        const li = document.createElement('li');
        li.className = 'flex items-center gap-2';
        li.innerHTML = `<i class="fa-solid fa-paperclip text-gray-400 text-xs"></i>
            <span class="truncate text-gray-600">${file.name}</span>
            <span class="text-gray-400">(${(file.size/1024).toFixed(1)} KB)</span>`;
        ul.appendChild(li);
    });
}

// ── Reset attachment saat modal Tambah ditutup ─────────────────────────────
const _origCloseTambah = window.closeModalTambah;
window.closeModalTambah = function() {
    _origCloseTambah && _origCloseTambah();
    const att = document.getElementById('bukti_attachment_kir');
    if (att) att.value = '';
    const list = document.getElementById('listAttachmentKirTambah');
    if (list) list.innerHTML = '';
};

// ── Reset attachment saat modal Perpanjang ditutup ─────────────────────────
const _origClosePerpanjang = window.closeModalPerpanjang;
window.closeModalPerpanjang = function() {
    _origClosePerpanjang && _origClosePerpanjang();
    const att = document.getElementById('bukti_attachment_perpanjang');
    if (att) att.value = '';
    const list = document.getElementById('listAttachmentKirPerpanjang');
    if (list) list.innerHTML = '';
};
</script>

@include('admin.partials.detail-modal')

@endsection
