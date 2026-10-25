@extends('admin.layouts.app')

@section('title', 'Buat Laporan Kehilangan')

@section('content')
<div class="space-y-6 max-w-8xl">

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('laporan-kehilangan.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 transition-colors shadow-sm">
            <i class="bi bi-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buat Laporan Kehilangan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Catat laporan kendaraan yang hilang</p>
        </div>
    </div>

    {{-- ERROR MESSAGES --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-red-700 mb-1"><i class="bi bi-exclamation-triangle-fill mr-1"></i> Terdapat kesalahan:</p>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('laporan-kehilangan.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @csrf

        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700 text-base flex items-center gap-2">
                <i class="bi bi-car-front-fill text-blue-500"></i> Informasi Kehilangan
            </h2>
        </div>

        <div class="px-6 py-5 space-y-5">

            {{-- Kendaraan --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Kendaraan <span class="text-red-500">*</span>
                </label>
                <select name="kendaraan_id" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('kendaraan_id') border-red-400 @enderror">
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach ($kendaraans as $k)
                        <option value="{{ $k->id }}" {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nopol }} — {{ $k->merk }} ({{ ucfirst($k->status_kendaraan) }})
                        </option>
                    @endforeach
                </select>
                @error('kendaraan_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Tanggal Kehilangan <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('tanggal') border-red-400 @enderror">
                @error('tanggal')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Lokasi Kejadian --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi Kejadian</label>
                <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}"
                       placeholder="Contoh: Jl. Sudirman No. 10, Jakarta Pusat"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Keterangan / Kronologi</label>
                <textarea name="keterangan" rows="4"
                          placeholder="Jelaskan kronologi kejadian kehilangan..."
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 resize-none">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100 pt-2">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Informasi Tambahan</p>
            </div>

            {{-- Pelanggan (opsional) --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Pelanggan Terkait <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <select name="pelanggan_id"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">-- Tidak ada / Tidak diketahui --</option>
                    @foreach ($pelanggans as $p)
                        <option value="{{ $p->id }}" {{ old('pelanggan_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_pelanggan }} {{ $p->kontak_pelanggan ? '(' . $p->kontak_pelanggan . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Pengurus --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Pengurus / Penanggung Jawab <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <input type="text" name="pengurus" value="{{ old('pengurus') }}"
                       placeholder="Nama pihak yang menangani (polisi, pengemudi, dll)"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>

            {{-- Informasi GPS --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Informasi GPS <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="informasi_gps" rows="2"
                          placeholder="Koordinat atau info GPS terakhir kendaraan..."
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 resize-none">{{ old('informasi_gps') }}</textarea>
            </div>

            {{-- File --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Lampiran <span class="text-gray-400 font-normal">(opsional — foto / dokumen maks 10MB)</span>
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-blue-300 transition-colors">
                    <input type="file" name="file" id="fileInput"
                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                           class="hidden"
                           onchange="previewFile(this)">
                    <label for="fileInput" class="flex flex-col items-center gap-2 cursor-pointer text-gray-400">
                        <i class="bi bi-cloud-arrow-up text-3xl"></i>
                        <span class="text-sm">Klik untuk pilih file</span>
                        <span class="text-xs">JPG, PNG, PDF, DOC, DOCX</span>
                    </label>
                    <div id="filePreview" class="hidden mt-3 text-center"></div>
                </div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
            <a href="{{ route('laporan-kehilangan.index') }}"
               class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                <i class="bi bi-save"></i> Simpan Laporan
            </button>
        </div>

    </form>

</div>

<script>
function previewFile(input) {
    const preview = document.getElementById('filePreview');
    const file = input.files[0];
    if (!file) { preview.classList.add('hidden'); return; }

    preview.classList.remove('hidden');

    const isImage = file.type.startsWith('image/');
    if (isImage) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" class="max-h-40 mx-auto rounded-lg shadow-sm object-cover">
                <p class="text-xs text-gray-500 mt-1">${file.name}</p>`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = `<div class="flex items-center gap-2 bg-gray-100 rounded-lg px-3 py-2 inline-flex">
            <i class="bi bi-file-earmark-text text-blue-500"></i>
            <span class="text-sm text-gray-700">${file.name}</span>
        </div>`;
    }
}
</script>
@endsection
