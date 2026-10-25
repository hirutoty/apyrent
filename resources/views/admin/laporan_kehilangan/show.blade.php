@extends('admin.layouts.app')

@section('title', 'Detail Laporan Kehilangan')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan-kehilangan.index', ['tab' => $kehilangan->status]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 transition-colors shadow-sm">
                <i class="bi bi-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Laporan Kehilangan</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    #{{ $kehilangan->id }} &mdash; {{ $kehilangan->kendaraan->nopol ?? '-' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{-- Badge status --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold {{ $kehilangan->statusBadgeClass() }}">
                @if($kehilangan->status === 'sementara')
                    <i class="bi bi-hourglass-split"></i>
                @elseif($kehilangan->status === 'permanen')
                    <i class="bi bi-exclamation-triangle-fill"></i>
                @else
                    <i class="bi bi-check-circle-fill"></i>
                @endif
                {{ $kehilangan->statusLabel() }}
            </span>

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
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-xl border border-red-100 transition-colors">
                        <i class="bi bi-trash3"></i> Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- FLASH / ERROR --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-2 text-green-700 text-sm">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-red-700 mb-1"><i class="bi bi-exclamation-triangle-fill mr-1"></i></p>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: Detail Info --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Kendaraan --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Kendaraan</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                        <i class="bi bi-truck-front-fill text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">{{ $kehilangan->kendaraan->nopol ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $kehilangan->kendaraan->merk ?? '-' }}</p>
                    </div>
                </div>
                @if($kehilangan->kendaraan)
                    <div class="mt-3 pt-3 border-t border-gray-50 text-xs text-gray-500 space-y-1">
                        <div class="flex justify-between">
                            <span>Status Kendaraan</span>
                            <span class="font-semibold text-gray-700">{{ ucfirst($kehilangan->kendaraan->status_kendaraan) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Detail Laporan --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-3">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Detail Laporan</h3>

                <div>
                    <p class="text-xs text-gray-400">Tanggal Hilang</p>
                    <p class="text-sm font-semibold text-gray-700 mt-0.5">
                        {{ $kehilangan->tanggal ? $kehilangan->tanggal->format('d F Y') : '-' }}
                    </p>
                </div>

                @if($kehilangan->lokasi_kejadian)
                <div>
                    <p class="text-xs text-gray-400">Lokasi Kejadian</p>
                    <p class="text-sm text-gray-700 mt-0.5">{{ $kehilangan->lokasi_kejadian }}</p>
                </div>
                @endif

                @if($kehilangan->keterangan)
                <div>
                    <p class="text-xs text-gray-400">Keterangan / Kronologi</p>
                    <p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $kehilangan->keterangan }}</p>
                </div>
                @endif

                @if($kehilangan->pengurus)
                <div>
                    <p class="text-xs text-gray-400">Pengurus</p>
                    <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $kehilangan->pengurus }}</p>
                </div>
                @endif

                @if($kehilangan->pelanggan)
                <div>
                    <p class="text-xs text-gray-400">Pelanggan Terkait</p>
                    <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $kehilangan->pelanggan->nama_pelanggan }}</p>
                    @if($kehilangan->pelanggan->kontak_pelanggan)
                        <p class="text-xs text-gray-400">{{ $kehilangan->pelanggan->kontak_pelanggan }}</p>
                    @endif
                </div>
                @endif

                @if($kehilangan->informasi_gps)
                <div>
                    <p class="text-xs text-gray-400">Informasi GPS</p>
                    <p class="text-sm text-gray-700 mt-0.5 font-mono text-xs bg-gray-50 rounded-lg p-2">{{ $kehilangan->informasi_gps }}</p>
                </div>
                @endif

                @if($kehilangan->file)
                <div>
                    <p class="text-xs text-gray-400 mb-1.5">Lampiran Awal</p>
                    @php $ext = pathinfo($kehilangan->file, PATHINFO_EXTENSION); @endphp
                    @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                        <a href="{{ asset($kehilangan->file) }}" target="_blank">
                            <img src="{{ asset($kehilangan->file) }}" class="w-full max-h-40 object-cover rounded-xl border border-gray-100 hover:opacity-90 transition-opacity">
                        </a>
                    @else
                        <a href="{{ asset($kehilangan->file) }}" target="_blank"
                           class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline bg-blue-50 px-3 py-2 rounded-lg">
                            <i class="bi bi-file-earmark-text"></i> Lihat Lampiran
                        </a>
                    @endif
                </div>
                @endif
            </div>

        </div>

        {{-- KOLOM KANAN: Timeline History --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="bi bi-clock-history text-indigo-500"></i> Riwayat Laporan
                        <span class="text-xs bg-gray-100 text-gray-500 font-medium px-2 py-0.5 rounded-full ml-1">
                            {{ $kehilangan->laporans->count() }} entri
                        </span>
                    </h3>
                    @if($kehilangan->status !== 'ditemukan')
                        <button onclick="document.getElementById('modalTambahUpdate').classList.remove('hidden')"
                                class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg transition-colors font-medium">
                            <i class="bi bi-plus-lg"></i> Tambah Update
                        </button>
                    @endif
                </div>

                <div class="p-5">
                    @forelse ($kehilangan->laporans as $i => $laporan)
                        <div class="relative flex gap-4 {{ !$loop->last ? 'pb-6' : '' }}">
                            {{-- Garis timeline --}}
                            @if(!$loop->last)
                                <div class="absolute left-4 top-8 bottom-0 w-px bg-gray-100"></div>
                            @endif

                            {{-- Dot --}}
                            <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center shadow-sm z-10
                                {{ $loop->first ? 'bg-blue-500' : 'bg-indigo-400' }}">
                                <i class="bi {{ $loop->first ? 'bi-flag-fill' : 'bi-arrow-repeat' }} text-white text-xs"></i>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0 pb-0">
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                        <p class="text-sm font-semibold text-gray-800">{{ $laporan->laporan }}</p>
                                        <span class="text-xs text-gray-400 whitespace-nowrap">
                                            {{ $laporan->tanggal_laporan ? $laporan->tanggal_laporan->format('d M Y') : '-' }}
                                        </span>
                                    </div>

                                    @if($laporan->keterangan)
                                        <p class="text-sm text-gray-600 leading-relaxed">{{ $laporan->keterangan }}</p>
                                    @endif

                                    @if($laporan->lokasi_pencarian)
                                        <p class="text-xs text-gray-400 mt-1.5">
                                            <i class="bi bi-geo-alt mr-1"></i>{{ $laporan->lokasi_pencarian }}
                                        </p>
                                    @endif

                                    @if($laporan->file)
                                        @php $ext2 = pathinfo($laporan->file, PATHINFO_EXTENSION); @endphp
                                        <div class="mt-2">
                                            @if(in_array(strtolower($ext2), ['jpg','jpeg','png']))
                                                <a href="{{ asset($laporan->file) }}" target="_blank">
                                                    <img src="{{ asset($laporan->file) }}" class="max-h-32 rounded-lg object-cover border border-gray-100 hover:opacity-90 transition-opacity">
                                                </a>
                                            @else
                                                <a href="{{ asset($laporan->file) }}" target="_blank"
                                                   class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline bg-blue-50 px-2.5 py-1.5 rounded-lg">
                                                    <i class="bi bi-file-earmark-text"></i> Lampiran
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-gray-400">
                            <i class="bi bi-inbox text-4xl"></i>
                            <p class="text-sm mt-2">Belum ada riwayat laporan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>

{{-- MODAL GANTI STATUS --}}
@if($kehilangan->allowedTransitions())
<div id="modalGantiStatus" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="bi bi-arrow-repeat text-indigo-500"></i> Ganti Status Kehilangan
            </h3>
            <button onclick="document.getElementById('modalGantiStatus').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <form action="{{ route('laporan-kehilangan.update-status', $kehilangan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-5 space-y-4">

                {{-- Pilih Status Baru --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Status Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-{{ count($kehilangan->allowedTransitions()) }} gap-3">
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
                                <div class="border-2 border-gray-200 peer-checked:border-{{ $optConfig['color'] }}-400 peer-checked:bg-{{ $optConfig['color'] }}-50 rounded-xl p-3 text-center transition-all">
                                    <i class="bi {{ $optConfig['icon'] }} text-xl text-{{ $optConfig['color'] }}-500 block mb-1"></i>
                                    <p class="text-sm font-bold text-gray-800">{{ $optConfig['label'] }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $optConfig['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Keterangan Update --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Keterangan Update</label>
                    <textarea name="keterangan_update" rows="3"
                              placeholder="Jelaskan alasan atau kondisi terkini..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
                </div>

                {{-- Lokasi Pencarian --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi Pencarian / Penemuan</label>
                    <input type="text" name="lokasi_pencarian"
                           placeholder="Lokasi terakhir atau tempat ditemukan"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>

                {{-- Lampiran Update --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lampiran <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                           class="w-full text-sm text-gray-600 border border-gray-200 rounded-xl px-4 py-2.5 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                <button type="button"
                        onclick="document.getElementById('modalGantiStatus').classList.add('hidden')"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
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

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="bi bi-plus-circle-fill text-indigo-500"></i> Tambah Update Laporan
            </h3>
            <button onclick="document.getElementById('modalTambahUpdate').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <form action="{{ route('laporan-kehilangan.tambah-laporan', $kehilangan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-5 space-y-4">

                {{-- Isi Laporan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Isi Laporan / Update <span class="text-red-500">*</span>
                    </label>
                    <textarea name="laporan" rows="3" required
                              placeholder="Contoh: Kendaraan terakhir terlihat di area Cempaka Putih, sudah dilaporkan ke Polsek setempat..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
                </div>

                {{-- Tanggal + Lokasi (2 kolom) --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_laporan" value="{{ date('Y-m-d') }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Lokasi Pencarian <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                        </label>
                        <input type="text" name="lokasi_pencarian"
                               placeholder="Area pencarian"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Keterangan Tambahan <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                    </label>
                    <textarea name="keterangan" rows="2"
                              placeholder="Detail tambahan..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
                </div>

                {{-- File --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Lampiran <span class="text-gray-400 font-normal text-xs">(opsional — foto/dokumen maks 10MB)</span>
                    </label>
                    <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                           class="w-full text-sm text-gray-600 border border-gray-200 rounded-xl px-4 py-2.5
                                  file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0
                                  file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100">
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                <button type="button"
                        onclick="document.getElementById('modalTambahUpdate').classList.add('hidden')"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
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
document.getElementById('modalTambahUpdate').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endif

@endsection
