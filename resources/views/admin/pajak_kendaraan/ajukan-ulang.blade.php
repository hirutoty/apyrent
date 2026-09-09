@extends('admin.layouts.app')

@section('title', 'Ajukan Ulang Pajak Kendaraan')

@section('content')
<div class="space-y-6 p-5 max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('pajak.index') }}"
           class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
            <i class="fa fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Ajukan Ulang Pajak Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $pajak->kendaraan->nopol ?? '-' }} — {{ $pajak->kendaraan->merk ?? '-' }}
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

        <form id="formAjukanUlangPage"
              action="{{ route('pajak.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="px-6 py-6">
            @csrf
            <input type="hidden" name="edit_pembayaran" value="{{ $pembayaranId }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Readonly: Kendaraan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan</label>
                    <div class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 select-none">
                        {{ $pajak->kendaraan->nopol ?? '-' }} — {{ $pajak->kendaraan->merk ?? '-' }}
                    </div>
                    <input type="hidden" name="kendaraan_id" value="{{ $pajak->kendaraan_id }}">
                </div>

                {{-- Readonly: Jenis Pajak --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pajak</label>
                    <div class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
                        {{ $pajak->jenis_pajak }}
                    </div>
                    <input type="hidden" name="jenis_pajak" value="{{ $pajak->jenis_pajak }}">
                </div>

                {{-- Nominal --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Nominal <span class="text-red-500">*</span>
                    </label>
                    <input type="number" min="0" name="nominal" required
                        value="{{ old('nominal', $pajak->nominal) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Tgl Ketentuan Bayar --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tgl Ketentuan Bayar</label>
                    <input type="date" name="tanggal_bayar" id="tgl_bayar"
                        value="{{ old('tanggal_bayar', $pajak->tanggal_bayar ? \Carbon\Carbon::parse($pajak->tanggal_bayar)->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Tanggal Jatuh Tempo (auto-hitung) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Jatuh Tempo</label>
                    <input type="date" id="jatuh_tempo_display" readonly
                        value="{{ old('jatuh_tempo', $pajak->jatuh_tempo ? \Carbon\Carbon::parse($pajak->jatuh_tempo)->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                    <input type="hidden" name="jatuh_tempo" id="jatuh_tempo_hidden"
                        value="{{ old('jatuh_tempo', $pajak->jatuh_tempo ? \Carbon\Carbon::parse($pajak->jatuh_tempo)->format('Y-m-d') : '') }}">
                    <p class="text-xs text-gray-400 mt-1">Otomatis tgl ketentuan bayar + 1 tahun</p>
                </div>

                {{-- Nama Pemilik --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pemilik</label>
                    <input type="text" name="nama_pemilik"
                        value="{{ old('nama_pemilik', $pajak->nama_pemilik) }}"
                        placeholder="Nama pemilik rekening"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Nama Bank --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Bank</label>
                    <input type="text" name="nama_bank"
                        value="{{ old('nama_bank', $pajak->nama_bank) }}"
                        placeholder="Contoh: BRI, BCA, Mandiri"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- No Rekening --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Rekening</label>
                    <input type="text" name="no_rekening"
                        value="{{ old('no_rekening', $pajak->no_rekening) }}"
                        placeholder="Nomor rekening tujuan"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>

                {{-- Keterangan --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
                    <textarea name="keterangan" rows="2"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none">{{ old('keterangan', $pajak->keterangan) }}</textarea>
                </div>

                {{-- Lampiran lama --}}
                @if($pajak->attachments && $pajak->attachments->count() > 0)
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Lampiran Sebelumnya</label>
                    <div class="space-y-1.5" id="lampiranList">
                        @foreach($pajak->attachments as $att)
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
                                {{-- Tombol hapus lampiran: pakai AJAX agar tidak butuh nested form --}}
                                <button type="button"
                                    onclick="hapusLampiran({{ $att->id }}, '{{ route('pajak.attachment.destroy', $att->id) }}')"
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
                    <label for="new_attachment"
                        class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <i class="fa-solid fa-paperclip text-lg text-gray-400 mb-0.5"></i>
                        <span class="text-xs text-gray-500">Klik untuk upload lampiran tambahan (Maks 5MB)</span>
                    </label>
                    <input type="file" name="bukti_attachment[]" id="new_attachment" class="hidden" multiple
                        onchange="renderNewAttachmentList(this)">
                    <ul id="newAttachmentList" class="mt-2 space-y-1 text-xs text-gray-600"></ul>
                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="flex gap-3 pt-6 border-t border-gray-100 mt-6">
                <a href="{{ route('pajak.index') }}"
                   class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors text-center">
                    Batal
                </a>
                <button type="submit" id="btnAjukanUlangPage"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-paper-plane text-sm"></i>
                    Ajukan Ulang
                </button>
            </div>
        </form>
    </div>

</div>

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
</style>

<script>
// Auto-hitung jatuh tempo dari tgl ketentuan bayar
document.getElementById('tgl_bayar')?.addEventListener('change', function () {
    const display = document.getElementById('jatuh_tempo_display');
    const hidden  = document.getElementById('jatuh_tempo_hidden');
    if (!this.value) {
        display.value = '';
        if (hidden) hidden.value = '';
        return;
    }
    const d = new Date(this.value);
    d.setFullYear(d.getFullYear() + 1);
    const val = d.getFullYear() + '-'
        + String(d.getMonth() + 1).padStart(2, '0') + '-'
        + String(d.getDate()).padStart(2, '0');
    display.value = val;
    if (hidden) hidden.value = val;
});

// Render daftar lampiran yang dipilih
function renderNewAttachmentList(input) {
    const list = document.getElementById('newAttachmentList');
    list.innerHTML = '';
    Array.from(input.files).forEach(f => {
        const li = document.createElement('li');
        li.className = 'flex items-center gap-1.5';
        li.innerHTML = `<i class="fa fa-paperclip text-gray-400"></i> ${f.name}`;
        list.appendChild(li);
    });
}

// Hapus lampiran lama via AJAX (tanpa nested form agar tidak merusak submit utama)
function hapusLampiran(attId, url) {
    if (!confirm('Hapus lampiran ini?')) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const body = new URLSearchParams({ _token: csrfToken, _method: 'DELETE' });

    fetch(url, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: body,
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
    const form = document.getElementById('formAjukanUlangPage');
    const btn  = document.getElementById('btnAjukanUlangPage');
    if (!form || !btn) return;
    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
    });
})();
</script>

@endsection
