@extends('admin.layouts.app')

@section('title', 'Detail Laporan Kehilangan')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="{{ route('laporan-kehilangan.index', ['tab' => $kehilangan->status]) }}"
               class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 transition-colors shadow-sm shrink-0 mt-1">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h1 class="text-2xl font-bold text-slate-800">Detail Laporan Kehilangan</h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $kehilangan->statusBadgeClass() }}">
                        @if($kehilangan->status === 'sementara')
                            <i class="bi bi-hourglass-split"></i>
                        @elseif($kehilangan->status === 'permanen')
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        @else
                            <i class="bi bi-check-circle-fill"></i>
                        @endif
                        {{ $kehilangan->statusLabel() }}
                    </span>
                </div>
                <p class="text-sm text-slate-500">
                    Laporan #{{ $kehilangan->id }} &middot; {{ $kehilangan->nopol }} &mdash; {{ $kehilangan->merk }}
                </p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Tombol Ganti Status --}}
            @if($kehilangan->allowedTransitions())
                <button onclick="document.getElementById('modalGantiStatus').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
                    <i class="bi bi-arrow-repeat"></i> Ganti Status
                </button>
            @endif

            {{-- Tombol Hapus --}}
            @if($kehilangan->status === 'sementara')
                <form action="{{ route('laporan-kehilangan.destroy', $kehilangan->id) }}" method="POST"
                      onsubmit="return confirm('Hapus laporan ini? Tindakan tidak bisa dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium rounded-xl transition-colors">
                        <i class="bi bi-trash3"></i> Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success') || session('error') || $errors->any())
        <div id="alertOverlay" class="fixed inset-0 z-[99999999] flex items-start justify-center pt-6"
            style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

            <div id="alertBox"
                class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
                style="transform:translateY(-16px);transition:transform 0.25s">

                @if(session('success'))
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                @elseif(session('error'))
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                        <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button onclick="closeAlert()"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0"
                    aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>

            </div>
        </div>

        <script>
        window.closeAlert = function() {
            const overlay = document.getElementById('alertOverlay');
            if (overlay) overlay.style.display = 'none';
        };

        (function() {
            const overlay = document.getElementById('alertOverlay');
            if (!overlay) return;
            overlay.style.pointerEvents = 'auto';
            overlay.style.opacity = '1';
            const box = document.getElementById('alertBox');
            if (box) box.style.transform = 'translateY(0)';
            setTimeout(closeAlert, 4000);
        })();
        </script>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: Detail Info --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Kendaraan Card --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-br from-blue-50 to-indigo-50 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-blue-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <i class="bi bi-truck-front-fill text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Kendaraan</p>
                            <p class="font-bold text-slate-800 text-base">{{ $kehilangan->nopol }}</p>
                            <p class="text-sm text-slate-600">{{ $kehilangan->merk }}</p>
                        </div>
                    </div>
                </div>
                @if($kehilangan->kendaraan)
                    <div class="px-5 py-3 text-sm">
                        <div class="flex items-center justify-between py-2">
                            <span class="text-slate-500">Status Kendaraan</span>
                            <span class="font-semibold text-slate-700 capitalize">{{ $kehilangan->kendaraan->status_kendaraan }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Detail Laporan Card --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                        <i class="bi bi-info-circle text-slate-600"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Informasi Laporan</h3>
                </div>

                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Tanggal Hilang</p>
                        <p class="text-sm font-semibold text-slate-700">
                            {{ $kehilangan->tanggal ? $kehilangan->tanggal->format('d F Y') : '-' }}
                        </p>
                    </div>

                    @if($kehilangan->lokasi_kejadian)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Lokasi Kejadian</p>
                        <p class="text-sm text-slate-700 flex items-start gap-1.5">
                            <i class="bi bi-geo-alt text-red-500 mt-0.5"></i>
                            <span>{{ $kehilangan->lokasi_kejadian }}</span>
                        </p>
                    </div>
                    @endif

                    @if($kehilangan->keterangan)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Keterangan / Kronologi</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $kehilangan->keterangan }}</p>
                    </div>
                    @endif

                    @if($kehilangan->pengurus)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Penanggung Jawab</p>
                        <p class="text-sm font-medium text-slate-700">{{ $kehilangan->pengurus }}</p>
                    </div>
                    @endif

                    @if($kehilangan->pelanggan)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Pelanggan Terkait</p>
                        <p class="text-sm font-medium text-slate-700">{{ $kehilangan->pelanggan->nama_pelanggan }}</p>
                        @if($kehilangan->pelanggan->kontak_pelanggan)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $kehilangan->pelanggan->kontak_pelanggan }}</p>
                        @endif
                    </div>
                    @endif

                    @if($kehilangan->informasi_gps)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Informasi GPS</p>
                        <div class="text-xs text-slate-700 font-mono bg-slate-50 rounded-lg p-3 border border-slate-100">{{ $kehilangan->informasi_gps }}</div>
                    </div>
                    @endif

                    @if($kehilangan->file)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Lampiran Awal</p>
                        @php 
                            $ext = pathinfo($kehilangan->file, PATHINFO_EXTENSION);
                            $filename = basename($kehilangan->file);
                            $originalName = preg_replace('/^\d+_[a-f0-9]+_/', '', $filename) ?: $filename;
                        @endphp
                        @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                            <a href="{{ asset($kehilangan->file) }}" target="_blank" class="block">
                                <img src="{{ asset($kehilangan->file) }}" class="w-full h-40 object-cover rounded-xl border border-slate-200 hover:opacity-90 transition-opacity">
                            </a>
                            <p class="text-xs text-slate-500 mt-2 flex items-center gap-1.5">
                                <i class="bi bi-paperclip"></i>
                                <span class="break-all">{{ $originalName }}</span>
                            </p>
                        @else
                            <a href="{{ asset($kehilangan->file) }}" target="_blank"
                               class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-colors">
                                @if(strtolower($ext) === 'pdf')
                                    <i class="bi bi-file-pdf text-red-500"></i>
                                @else
                                    <i class="bi bi-file-earmark-text"></i>
                                @endif
                                <span class="break-all font-medium">{{ $originalName }}</span>
                            </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: Timeline History --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-slate-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500 text-white flex items-center justify-center shadow-sm">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">Riwayat Laporan Perkembangan</h3>
                            <p class="text-xs text-slate-600 mt-0.5">{{ $kehilangan->laporans->count() }} total update</p>
                        </div>
                    </div>
                    @if($kehilangan->status !== 'ditemukan')
                        <button onclick="document.getElementById('modalTambahUpdate').classList.remove('hidden')"
                                class="inline-flex items-center gap-1.5 text-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-colors font-medium shadow-sm">
                            <i class="bi bi-plus-lg"></i> Tambah Update
                        </button>
                    @endif
                </div>

                <div class="p-6">
                    @forelse ($kehilangan->laporans as $i => $laporan)
                        <div class="relative flex gap-4 {{ !$loop->last ? 'pb-8' : '' }}">
                            {{-- Garis timeline --}}
                            @if(!$loop->last)
                                <div class="absolute left-5 top-12 bottom-0 w-0.5 bg-gradient-to-b from-slate-200 to-transparent"></div>
                            @endif

                            {{-- Dot --}}
                            <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center shadow-md z-10
                                {{ $loop->first ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : 'bg-gradient-to-br from-indigo-400 to-purple-500' }}">
                                <i class="bi {{ $loop->first ? 'bi-flag-fill' : 'bi-arrow-repeat' }} text-white"></i>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 hover:shadow-md transition-shadow">
                                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                                        <p class="text-sm font-semibold text-slate-800 flex-1">{{ $laporan->laporan }}</p>
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 bg-white px-2.5 py-1 rounded-lg border border-slate-200 whitespace-nowrap">
                                            <i class="bi bi-calendar3"></i>
                                            {{ $laporan->tanggal_laporan ? $laporan->tanggal_laporan->format('d M Y') : '-' }}
                                        </span>
                                    </div>

                                    @if($laporan->keterangan)
                                        <p class="text-sm text-slate-600 leading-relaxed mb-3">{{ $laporan->keterangan }}</p>
                                    @endif

                                    @if($laporan->lokasi_pencarian)
                                        <div class="inline-flex items-center gap-1.5 text-xs text-slate-500 bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                                            <i class="bi bi-geo-alt text-red-500"></i>
                                            {{ $laporan->lokasi_pencarian }}
                                        </div>
                                    @endif

                                    @if($laporan->file)
                                        @php 
                                            $ext2 = pathinfo($laporan->file, PATHINFO_EXTENSION);
                                            $filename2 = basename($laporan->file);
                                            $originalName2 = preg_replace('/^\d+_[a-f0-9]+_/', '', $filename2) ?: $filename2;
                                        @endphp
                                        <div class="mt-3 pt-3 border-t border-slate-200">
                                            @if(in_array(strtolower($ext2), ['jpg','jpeg','png']))
                                                <a href="{{ asset($laporan->file) }}" target="_blank">
                                                    <img src="{{ asset($laporan->file) }}" class="max-h-40 rounded-lg object-cover border border-slate-200 hover:opacity-90 transition-opacity">
                                                </a>
                                                <p class="text-xs text-slate-500 mt-2 flex items-center gap-1.5">
                                                    <i class="bi bi-paperclip"></i>
                                                    <span class="break-all">{{ $originalName2 }}</span>
                                                </p>
                                            @else
                                                <a href="{{ asset($laporan->file) }}" target="_blank"
                                                   class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors font-medium">
                                                    @if(strtolower($ext2) === 'pdf')
                                                        <i class="bi bi-file-pdf text-red-500"></i>
                                                    @else
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    @endif
                                                    <span class="break-all">{{ $originalName2 }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center text-slate-400">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 mx-auto flex items-center justify-center mb-4">
                                <i class="bi bi-inbox text-3xl"></i>
                            </div>
                            <p class="text-sm font-medium">Belum ada riwayat laporan perkembangan</p>
                            <p class="text-xs mt-1">Klik tombol "Tambah Update" untuk menambahkan laporan baru</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>

{{-- MODAL GANTI STATUS --}}
@if($kehilangan->allowedTransitions())
<div id="modalGantiStatus" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-arrow-repeat text-indigo-500"></i> Ganti Status Kehilangan
            </h3>
            <button onclick="document.getElementById('modalGantiStatus').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('laporan-kehilangan.update-status', $kehilangan->id) }}" method="POST">
            @csrf
            <div class="px-6 py-5">

                {{-- Pilih Status Baru --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-3">
                        Pilih Status Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($kehilangan->allowedTransitions() as $statusOption)
                            @php
                                $optConfig = match($statusOption) {
                                    'permanen'  => ['color' => 'red',   'icon' => 'bi-exclamation-triangle-fill', 'label' => 'Permanen',  'desc' => 'Kendaraan keluar dari stok'],
                                    'ditemukan' => ['color' => 'green', 'icon' => 'bi-check-circle-fill',         'label' => 'Ditemukan', 'desc' => 'Kendaraan kembali tersedia'],
                                    default     => ['color' => 'gray',  'icon' => 'bi-circle',                    'label' => ucfirst($statusOption), 'desc' => ''],
                                };
                            @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="status_baru" value="{{ $statusOption }}" class="peer sr-only" required>
                                <div class="border-2 border-slate-200 peer-checked:border-{{ $optConfig['color'] }}-400 peer-checked:bg-{{ $optConfig['color'] }}-50 rounded-xl p-4 text-center transition-all hover:border-slate-300">
                                    <i class="bi {{ $optConfig['icon'] }} text-3xl text-{{ $optConfig['color'] }}-500 block mb-2"></i>
                                    <p class="text-sm font-bold text-slate-800">{{ $optConfig['label'] }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $optConfig['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-500 mt-3">
                        Detail tambahan dapat ditambahkan melalui <span class="font-semibold">"Tambah Update"</span> setelah perubahan status.
                    </p>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3 rounded-b-2xl">
                <button type="button"
                        onclick="document.getElementById('modalGantiStatus').classList.add('hidden')"
                        class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- MODAL TAMBAH UPDATE LAPORAN --}}
@if($kehilangan->status !== 'ditemukan')
<div id="modalTambahUpdate" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-start justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-8">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-plus-circle-fill text-indigo-500"></i> Tambah Update Laporan
            </h3>
            <button onclick="document.getElementById('modalTambahUpdate').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('laporan-kehilangan.tambah-laporan', $kehilangan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-5 space-y-4">

                {{-- Isi Laporan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Isi Laporan / Update <span class="text-red-500">*</span>
                    </label>
                    <textarea name="laporan" rows="3" required
                              placeholder="Contoh: Kendaraan terakhir terlihat di area Cempaka Putih, sudah dilaporkan ke Polsek setempat..."
                              class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 resize-none"></textarea>
                </div>

                {{-- Tanggal + Lokasi (2 kolom) --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_laporan" value="{{ date('Y-m-d') }}" required
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Lokasi <span class="text-slate-400 font-normal text-xs">(opsional)</span>
                        </label>
                        <input type="text" name="lokasi_pencarian"
                               placeholder="Area pencarian"
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400">
                    </div>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Keterangan Tambahan <span class="text-slate-400 font-normal text-xs">(opsional)</span>
                    </label>
                    <textarea name="keterangan" rows="2"
                              placeholder="Detail tambahan..."
                              class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 resize-none"></textarea>
                </div>

                {{-- File --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Lampiran <span class="text-slate-400 font-normal text-xs">(opsional — foto/dokumen maks 10MB)</span>
                    </label>
                    <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                           class="w-full text-sm text-slate-600 border border-slate-200 rounded-xl px-4 py-3
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                  file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100 transition-colors">
                </div>

            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3 rounded-b-2xl">
                <button type="button"
                        onclick="document.getElementById('modalTambahUpdate').classList.add('hidden')"
                        class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <i class="bi bi-send"></i> Simpan Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('modalTambahUpdate')?.addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
document.getElementById('modalGantiStatus')?.addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endif

@endsection
