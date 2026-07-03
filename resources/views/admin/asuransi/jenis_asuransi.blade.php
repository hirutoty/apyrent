@extends('admin.layouts.app')

@section('title', 'Jenis Asuransi')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Jenis Asuransi</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola jenis-jenis produk asuransi kendaraan</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
            <i class="fa fa-plus text-sm"></i>
            Tambah Jenis
        </button>
    </div>


    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Jenis Asuransi</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total jenis asuransi</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari jenis asuransi..."
                        oninput="filterTable(this.value)"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-48">
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama Jenis</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Keterangan</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse ($data as $d)
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                            data-search="{{ strtolower($d->nama_jenis . ' ' . $d->keterangan) }}">
                            
                            {{-- No --}}
                            <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-tag text-blue-400 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">{{ $d->nama_jenis }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3.5 text-sm text-gray-500 max-w-xs">{{ $d->keterangan }}</td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        class="btn-edit inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $d->id }}"
                                        data-nama="{{ $d->nama_jenis }}"
                                        data-keterangan="{{ $d->keterangan }}">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <form action="/admin/jenis-asuransi/{{ $d->id }}" method="POST"
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
                            <td colspan="3" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-tag text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada jenis asuransi</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah Jenis" untuk menambahkan data baru</p>
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
    MODAL TAMBAH / EDIT JENIS ASURANSI
======================================--}}
<div id="jenisModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4"
         style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Jenis Asuransi</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data jenis produk asuransi</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="jenisForm" action="/admin/jenis-asuransi" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Jenis <span class="text-red-500">*</span></label>
                <input type="text" name="nama_jenis" id="f_nama_jenis" required
                    placeholder="Contoh: All Risk, TLO, Comprehensive"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan <span class="text-red-500">*</span></label>
                <textarea name="keterangan" id="f_keterangan" rows="4" required
                    placeholder="Masukkan keterangan jenis asuransi..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors duration-150 flex items-center justify-center gap-2">
                <i class="fa fa-save text-sm"></i> Simpan Data
            </button>
        </form>

    </div>
</div>


{{-- ======================================
    POPUP ALERT
======================================--}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay"
     class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
     style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

    <div id="alertBox"
         class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
         style="transform:translateY(-16px);transition:transform 0.25s">

        @if (session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
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
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script>
// ── MODAL ──────────────────────────────────────────
const jenisModal      = document.getElementById('jenisModal');
const jenisForm       = document.getElementById('jenisForm');
const methodContainer = document.getElementById('methodContainer');

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Jenis Asuransi';
    jenisForm.action = '/admin/jenis-asuransi';
    methodContainer.innerHTML = '';
    jenisForm.reset();
    jenisModal.classList.remove('hidden');
    jenisModal.classList.add('flex');
}

function closeModal() {
    jenisModal.classList.add('hidden');
    jenisModal.classList.remove('flex');
}

jenisModal.addEventListener('click', function (e) {
    if (e.target === jenisModal) closeModal();
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
});

document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('modalTitle').innerText = 'Edit Jenis Asuransi';
        jenisForm.action = '/admin/jenis-asuransi/' + this.dataset.id;
        methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('f_nama_jenis').value  = this.dataset.nama;
        document.getElementById('f_keterangan').value  = this.dataset.keterangan;

        jenisModal.classList.remove('hidden');
        jenisModal.classList.add('flex');
    });
});

function filterTable(q) {
    document.querySelectorAll('#tableBody tr[data-search]').forEach(row => {
        row.style.display = row.dataset.search.includes(q.toLowerCase()) ? '' : 'none';
    });
}

// ── POPUP ALERT ────────────────────────────────────
(function () {
    var overlay = document.getElementById('alertOverlay');
    var box     = document.getElementById('alertBox');
    if (!overlay) return;

    setTimeout(function () {
        overlay.style.opacity       = '1';
        overlay.style.pointerEvents = 'auto';
        box.style.transform         = 'translateY(0)';
    }, 80);

    var timer = setTimeout(closeAlert, 4500);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeAlert();
    });

    function closeAlert() {
        clearTimeout(timer);
        overlay.style.opacity       = '0';
        overlay.style.pointerEvents = 'none';
        box.style.transform         = 'translateY(-16px)';
    }
    window.closeAlert = closeAlert;
})();
</script>

@endsection