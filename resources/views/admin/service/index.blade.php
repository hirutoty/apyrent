@extends('admin.layouts.app')

@section('title', 'Data Service')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Service</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data service kendaraan</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
            <i class="fa fa-plus text-sm"></i>
            Tambah Service
        </button>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Service</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $data->count() }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fa fa-wrench text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Biaya Default</p>
                    <h3 class="text-lg font-bold text-green-600 mt-2 leading-tight">Rp {{ number_format($data->sum('biaya_default'), 0, ',', '.') }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="fa fa-money-bill-wave text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Rata-rata Biaya</p>
                    <h3 class="text-lg font-bold text-purple-600 mt-2 leading-tight">Rp {{ number_format($data->avg('biaya_default'), 0, ',', '.') }}</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center">
                    <i class="fa fa-chart-bar text-2xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Service</h2>
                <p class="text-xs text-gray-400 mt-0.5" id="totalCount">{{ $data->count() }} total data service</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    <input type="text" placeholder="Cari nama service, user..."
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">User</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama Service</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Biaya Default</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $i => $d)
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                            data-search="{{ strtolower(($d->user->name ?? '') . ' ' . $d->nama_service) }}">

                            {{-- NO --}}
                            <td class="px-4 py-3.5 text-xs text-gray-400 font-medium row-number">{{ $i + 1 }}</td>

                            {{-- USER --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($d->user->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $d->user->name ?? '-' }}</span>
                                </div>
                            </td>

                            {{-- NAMA SERVICE --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-wrench text-orange-400 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">{{ $d->nama_service }}</span>
                                </div>
                            </td>

                            {{-- BIAYA DEFAULT --}}
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-semibold text-blue-600">Rp {{ number_format($d->biaya_default, 0, ',', '.') }}</span>
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        class="btn-edit inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $d->id }}"
                                        data-nama_service="{{ $d->nama_service }}"
                                        data-biaya_default="{{ $d->biaya_default }}">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <form action="{{ route('service.destroy', $d->id) }}" method="POST"
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
                            <td colspan="5" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-wrench text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data service</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah Service" untuk menambahkan data baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div id="noResultRow" class="hidden px-5 py-12 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fa fa-search text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Tidak ada hasil yang cocok</p>
                    <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian</p>
                </div>
            </div>

        </div>

    </div>

</div>


{{-- ======================================
    MODAL TAMBAH / EDIT
======================================--}}
<div id="modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 p-4"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg"
         style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Service</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data service kendaraan</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="form" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="method-container"></div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Service <span class="text-red-500">*</span></label>
                <input type="text" name="nama_service" id="nama_service" required
                    placeholder="Masukkan nama service"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Biaya Default <span class="text-red-500">*</span></label>
                <input type="number" name="biaya_default" id="biaya_default" required
                    placeholder="Masukkan biaya"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal()"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa fa-save text-sm"></i> Simpan
                </button>
            </div>
        </form>

    </div>
</div>


{{-- ======================================
    POPUP ALERT (FIXED OVERLAY)
======================================--}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay"
     class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
     style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">

    <div id="alertBox"
         class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
         style="transform:translateY(-16px);transition:transform 0.25s">

        @if(session('success'))
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


<style>
@keyframes slideUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script>
// ── MODAL ──────────────────────────────────────────
const modal = document.getElementById('modal');

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Service';
    document.getElementById('form').action = '/admin/service';
    document.getElementById('form').reset();
    document.getElementById('method-container').innerHTML = '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

modal.addEventListener('click', function (e) {
    if (e.target === modal) closeModal();
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
});

document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('modalTitle').innerText = 'Edit Service';
        document.getElementById('form').action = '/admin/service/' + this.dataset.id;
        document.getElementById('method-container').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('nama_service').value  = this.dataset.nama_service;
        document.getElementById('biaya_default').value = this.dataset.biaya_default;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });
});

// ── SEARCH / FILTER ────────────────────────────────
function filterTable(q) {
    const rows = document.querySelectorAll('#tableBody tr[data-search]');
    let visible = 0;

    rows.forEach(row => {
        const show = row.dataset.search.includes(q.toLowerCase());
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('totalCount').textContent = visible + ' total data service';

    const noResult = document.getElementById('noResultRow');
    if (noResult) noResult.classList.toggle('hidden', visible > 0 || rows.length === 0);

    let num = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cell = row.querySelector('.row-number');
            if (cell) cell.textContent = num++;
        }
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