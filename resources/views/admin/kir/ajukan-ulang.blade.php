@extends('admin.layouts.app')

@section('title', 'Ajukan Ulang KIR')

@section('content')
<div class="space-y-6 p-5 max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('kir.index') }}"
           class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
            <i class="fa fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Ajukan Ulang KIR</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $kir->kendaraan->nopol ?? '-' }} — {{ $kir->kendaraan->merk ?? '-' }}
            </p>
        </div>
    </div>

    {{-- CATATAN PENOLAKAN --}}
    @if($rejectionReason)
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 flex items-start gap-3">
        <i class="fa fa-comment-slash text-red-500 mt-0.5 flex-shrink-0"></i>
        <div>
            <p class="text-sm font-semibold text-red-700 mb-0.5">Alasan Penolakan</p>
            <p class="text-sm text-red-600">{{ $rejectionReason }}</p>
        </div>
    </div>
    @endif

    {{-- INLINE ALERTS --}}
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 flex items-start gap-3 text-sm text-red-700">
        <i class="fa fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if(session('info'))
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-start gap-3 text-sm text-blue-700">
        <i class="fa fa-info-circle mt-0.5 flex-shrink-0"></i>
        <span>{{ session('info') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-sm text-red-700">
        <p class="font-semibold mb-1 flex items-center gap-1.5">
            <i class="fa fa-triangle-exclamation"></i> Harap perbaiki kesalahan berikut:
        </p>
        <ul class="list-disc ml-5 space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- FORM --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa fa-rotate-right text-amber-500"></i>
                Perbaiki dan Ajukan Ulang
            </h2>
        </div>

        <form id="formAjukanUlangKir"
              action="{{ route('kir.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="px-6 py-6">
            @csrf
            {{-- Support dua mode: resubmit PO atau resubmit Pembayaran --}}
            @if(!empty($editPoId))
                <input type="hidden" name="edit_purchase_order" value="{{ $editPoId }}">
            @else
                <input type="hidden" name="edit_pembayaran" value="{{ $editPembayaranId }}">
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Kendaraan (bisa diganti) --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select name="kendaraan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($kendaraan as $k)
                            <option value="{{ $k->id }}" {{ old('kendaraan_id', $kir->kendaraan_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nopol }} — {{ $k->merk }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- No. KTP --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        No. KTP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="no_ktp" required
                        value="{{ old('no_ktp', $kir->no_ktp) }}"
                        placeholder="Nomor KTP"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Nama KTP --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Nama (sesuai KTP) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_ktp" required
                        value="{{ old('nama_ktp', $kir->nama_ktp) }}"
                        placeholder="Nama sesuai KTP"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Lokasi Uji --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Lokasi Uji <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="lokasi_uji" required
                        value="{{ old('lokasi_uji', $kir->lokasi_uji) }}"
                        placeholder="Nama / alamat lokasi uji"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Penguji --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penguji</label>
                    <input type="text" name="penguji"
                        value="{{ old('penguji', $kir->penguji) }}"
                        placeholder="Nama penguji (opsional)"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Status Uji --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Status Uji <span class="text-red-500">*</span>
                    </label>
                    <select name="status_uji" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih Status --</option>
                        <option value="uji berkala" {{ old('status_uji', $kir->status_uji) === 'uji berkala' ? 'selected' : '' }}>Uji Berkala</option>
                        <option value="uji pertama" {{ old('status_uji', $kir->status_uji) === 'uji pertama' ? 'selected' : '' }}>Uji Pertama</option>
                    </select>
                </div>

                {{-- No. Uji --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Nomor Uji <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="no_uji" required
                        value="{{ old('no_uji', $kir->no_uji) }}"
                        placeholder="Nomor uji KIR"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Tanggal Ketentuan Bayar --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tgl Ketentuan Bayar <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_bayar" id="tanggalBayarKir" required
                        value="{{ old('tanggal_bayar', $kir->tanggal_bayar ? \Carbon\Carbon::parse($kir->tanggal_bayar)->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Masa Berlaku (auto) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Masa Berlaku</label>
                    <input type="date" id="masaBerlakuDisplay" readonly
                        value="{{ old('masa_berlaku', $kir->masa_berlaku ? \Carbon\Carbon::parse($kir->masa_berlaku)->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                    <input type="hidden" name="masa_berlaku" id="masaBerlakuHidden"
                        value="{{ old('masa_berlaku', $kir->masa_berlaku ? \Carbon\Carbon::parse($kir->masa_berlaku)->format('Y-m-d') : '') }}">
                    <p class="text-xs text-gray-400 mt-1">Otomatis tgl bayar + 6 bulan</p>
                </div>

                {{-- Biaya --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Biaya KIR <span class="text-red-500">*</span>
                    </label>
                    <input type="number" min="0" name="biaya" required
                        value="{{ old('biaya', $kir->biaya) }}"
                        placeholder="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Lampiran lama --}}
                @if($kir->attachments && $kir->attachments->count() > 0)
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Lampiran Sebelumnya</label>
                    <div class="space-y-1.5" id="lampiranList">
                        @foreach($kir->attachments as $att)
                        <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2"
                             id="att-row-{{ $att->id }}">
                            <a href="{{ asset($att->file_path) }}" target="_blank"
                               class="flex items-center gap-2 text-xs text-blue-600 hover:text-blue-800 truncate max-w-xs">
                                <i class="fa fa-paperclip text-[10px] flex-shrink-0"></i>
                                <span class="truncate">{{ $att->file_name }}</span>
                            </a>
                            <div class="flex items-center gap-2 ml-2 flex-shrink-0">
                                <span class="text-[10px] text-gray-400">
                                    {{ $att->file_size ? round($att->file_size / 1024, 1) . ' KB' : '' }}
                                </span>
                                <button type="button"
                                    onclick="hapusLampiranKir({{ $att->id }}, '{{ route('kir.attachment.destroy', $att->id) }}')"
                                    class="text-red-400 hover:text-red-600 text-xs p-0.5 transition-colors">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Upload lampiran baru --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tambah Lampiran Baru <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <label for="new_attachment_kir"
                        class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <i class="fa-solid fa-paperclip text-lg text-gray-400 mb-0.5"></i>
                        <span class="text-xs text-gray-500">Klik untuk upload lampiran tambahan (Maks 5MB)</span>
                    </label>
                    <input type="file" name="bukti_attachment[]" id="new_attachment_kir" class="hidden" multiple
                        onchange="renderNewAttachList(this)">
                    <ul id="newAttachList" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                </div>

                {{-- Info bukti bayar --}}
                <div class="sm:col-span-2">
                    <div class="flex items-start gap-2 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-xs text-blue-700">
                        <i class="fa fa-circle-info mt-0.5 flex-shrink-0"></i>
                        <span>Bukti pembayaran akan diunggah oleh Superadmin saat melakukan approval di halaman Pembayaran.</span>
                    </div>
                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="flex gap-3 pt-6 border-t border-gray-100 mt-6">
                <a href="{{ route('kir.index') }}"
                   class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors text-center">
                    Batal
                </a>
                <button type="submit" id="btnAjukanUlangKir"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-paper-plane text-sm"></i>
                    Ajukan Ulang
                </button>
            </div>
        </form>
    </div>

</div>

<script>
// Auto-hitung masa berlaku dari tanggal bayar + 6 bulan
document.getElementById('tanggalBayarKir')?.addEventListener('change', function () {
    const display = document.getElementById('masaBerlakuDisplay');
    const hidden  = document.getElementById('masaBerlakuHidden');
    if (!this.value) {
        display.value = '';
        if (hidden) hidden.value = '';
        return;
    }
    const d = new Date(this.value);
    d.setMonth(d.getMonth() + 6);
    const val = d.getFullYear() + '-'
        + String(d.getMonth() + 1).padStart(2, '0') + '-'
        + String(d.getDate()).padStart(2, '0');
    display.value = val;
    if (hidden) hidden.value = val;
});

// Render daftar lampiran baru yang dipilih
function renderNewAttachList(input) {
    const list = document.getElementById('newAttachList');
    list.innerHTML = '';
    Array.from(input.files).forEach(f => {
        const li = document.createElement('li');
        li.className = 'flex items-center gap-1.5';
        li.innerHTML = `<i class="fa fa-paperclip text-gray-400"></i> ${f.name}`;
        list.appendChild(li);
    });
}

// Hapus lampiran lama via AJAX
function hapusLampiranKir(attId, url) {
    if (!confirm('Hapus lampiran ini?')) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    fetch(url, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: new URLSearchParams({ _token: csrfToken, _method: 'DELETE' }),
    })
    .then(function (res) {
        if (res.ok || res.redirected) {
            const row = document.getElementById('att-row-' + attId);
            if (row) row.remove();
        } else {
            alert('Gagal menghapus lampiran (status ' + res.status + ').');
        }
    })
    .catch(function () {
        alert('Gagal menghapus lampiran. Periksa koneksi Anda.');
    });
}

// Anti double-submit
(function () {
    const form = document.getElementById('formAjukanUlangKir');
    const btn  = document.getElementById('btnAjukanUlangKir');
    if (!form || !btn) return;
    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
    });
})();
</script>

@endsection
