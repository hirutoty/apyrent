@extends('admin.layouts.app')

@section('title', 'Ajukan Ulang KIR')

@section('content')
<div class="space-y-6 p-5 max-w-2xl mx-auto">

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

    {{-- ALASAN PENOLAKAN --}}
    @if($rejectionReason)
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 flex items-start gap-3">
        <i class="fa fa-comment-slash text-red-500 mt-0.5 flex-shrink-0"></i>
        <div>
            <p class="text-sm font-semibold text-red-700 mb-0.5">Alasan Penolakan</p>
            <p class="text-sm text-red-600">{{ $rejectionReason }}</p>
        </div>
    </div>
    @endif

    {{-- ALERTS --}}
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-sm text-red-700 flex items-start gap-2">
        <i class="fa fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- INFO BOX --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-start gap-3 text-sm text-blue-700">
        <i class="fa fa-circle-info mt-0.5 flex-shrink-0"></i>
        <span>Data akan diajukan kembali tanpa perubahan. Isi keterangan jika ada catatan untuk reviewer.</span>
    </div>

    {{-- INFO KIR (readonly) --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Informasi KIR</p>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <p class="text-xs text-gray-400">Kendaraan</p>
                <p class="font-semibold text-gray-800">{{ $kir->kendaraan->nopol ?? '-' }} — {{ $kir->kendaraan->merk ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">No. Uji</p>
                <p class="font-medium text-gray-700">{{ $kir->no_uji ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Biaya</p>
                <p class="font-medium text-gray-700">Rp {{ number_format($kir->biaya ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Masa Berlaku</p>
                <p class="font-medium text-gray-700">
                    {{ $kir->masa_berlaku ? \Carbon\Carbon::parse($kir->masa_berlaku)->translatedFormat('j F Y') : '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i class="fa fa-rotate-right text-amber-500"></i>
                Ajukan Ulang
            </h2>
        </div>

        <form id="formAjukanUlangKir"
              action="{{ route('kir.store') }}"
              method="POST"
              class="px-6 py-6">
            @csrf
            @if(!empty($editPoId))
                <input type="hidden" name="edit_purchase_order" value="{{ $editPoId }}">
            @else
                <input type="hidden" name="edit_pembayaran" value="{{ $editPembayaranId }}">
            @endif
            {{-- Hidden fields yang dibutuhkan controller --}}
            <input type="hidden" name="kendaraan_id"  value="{{ $kir->kendaraan_id }}">
            <input type="hidden" name="no_ktp"        value="{{ $kir->no_ktp }}">
            <input type="hidden" name="nama_ktp"      value="{{ $kir->nama_ktp }}">
            <input type="hidden" name="lokasi_uji"    value="{{ $kir->lokasi_uji }}">
            <input type="hidden" name="penguji"       value="{{ $kir->penguji }}">
            <input type="hidden" name="status_uji"    value="{{ $kir->status_uji }}">
            <input type="hidden" name="no_uji"        value="{{ $kir->no_uji }}">
            <input type="hidden" name="tanggal_bayar" value="{{ $kir->tanggal_bayar ? \Carbon\Carbon::parse($kir->tanggal_bayar)->format('Y-m-d') : '' }}">
            <input type="hidden" name="masa_berlaku"  value="{{ $kir->masa_berlaku ? \Carbon\Carbon::parse($kir->masa_berlaku)->format('Y-m-d') : '' }}">
            <input type="hidden" name="biaya"         value="{{ $kir->biaya }}">

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Keterangan <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="keterangan" rows="4"
                        placeholder="Catatan atau penjelasan untuk reviewer..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-100 focus:border-amber-400 resize-none">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-5 border-t border-gray-100 mt-5">
                <a href="{{ route('kir.index') }}"
                   class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors text-center">
                    Batal
                </a>
                <button type="submit" id="btnAjukanUlangKir"
                    class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-rotate-right text-sm"></i>
                    Ajukan Ulang
                </button>
            </div>
        </form>
    </div>

</div>

<script>
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
