@extends('admin.layouts.app')

@section('title', 'Budgeting Proyek')

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Budgeting Proyek</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kelola data budgeting proyek perusahaan</p>
            </div>
            <button onclick="openModalTambah()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150 mt-2 sm:mt-0">
                <i class="fa fa-plus text-sm"></i>
                Tambah Budget
            </button>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-folder-open text-blue-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-semibold text-gray-400">Data</p>
                    <p class="text-2xl font-bold text-gray-800 leading-tight">{{ $data->count() }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Total Budgeting</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-wallet text-green-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-semibold text-gray-400">Budget</p>
                    <p class="text-sm font-bold text-green-600 leading-tight">Rp
                        {{ number_format($data->sum('budget'), 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Total Budget</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-coins text-orange-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-semibold text-gray-400">Realisasi</p>
                    <p class="text-sm font-bold text-orange-600 leading-tight">Rp
                        {{ number_format($data->sum('realisasi'), 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Total Realisasi</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa fa-chart-pie text-red-500 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-semibold text-gray-400">Sisa</p>
                    <p class="text-sm font-bold text-red-600 leading-tight">Rp
                        {{ number_format($data->sum('sisa'), 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Total Sisa Budget</p>
                </div>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- TOOLBAR --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="font-semibold text-gray-800 text-base">Daftar Budgeting</h2>
                    <p class="text-xs text-gray-400 mt-0.5" id="totalCount">{{ $data->count() }} total data</p>
                </div>

                <div class="flex items-center gap-2">
                    <a id="pdfBtn"
    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700"
    target="_blank"
    href="{{ route('budgeting.pdf') }}">
    <i class="fa fa-file-pdf"></i> PDF
</a>

{{-- TAMBAHKAN INI --}}
<a id="excelBtn"
    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700"
    target="_blank"
    href="{{ route('budgeting.export.excel') }}">
    <i class="fa fa-file-excel"></i> Excel
</a>
                    <div class="relative">
                        <i
                            class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <input type="text" id="searchInput" placeholder="Cari proyek, kategori..."
                            oninput="applyFilters()"
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
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Proyek</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Kategori</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Budget</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Realisasi</th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Sisa
                            </th>
                            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Tepakai</th>
                            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="budgetingTableBody">
                        @forelse($data as $i => $item)
                            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                                data-search="{{ strtolower($item->proyek . ' ' . $item->kategori) }}">

                                {{-- NO --}}
                                <td class="px-4 py-3.5 text-xs text-gray-400 font-medium row-number">{{ $i + 1 }}
                                </td>

                                {{-- PROYEK --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="fa fa-folder text-blue-400 text-xs"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-800">{{ $item->proyek }}</span>
                                    </div>
                                </td>

                                {{-- KATEGORI --}}
                                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $item->kategori }}</td>

                                {{-- BUDGET --}}
                                <td class="px-4 py-3.5">
                                    <span class="text-sm text-gray-700">Rp
                                        {{ number_format($item->budget, 0, ',', '.') }}</span>
                                </td>

                                {{-- REALISASI --}}
                                <td class="px-4 py-3.5">
                                    <span class="text-sm text-orange-600 font-semibold">Rp
                                        {{ number_format($item->realisasi, 0, ',', '.') }}</span>
                                </td>

                                {{-- SISA --}}
                                <td class="px-4 py-3.5">
                                    <span class="text-sm text-red-500 font-semibold">Rp
                                        {{ number_format($item->sisa, 0, ',', '.') }}</span>
                                </td>

                                {{-- PERSEN --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2 min-w-[100px]">
                                        <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-blue-500 h-1.5 rounded-full transition-all"
                                                style="width: {{ min($item->persen_terpakai, 100) }}%"></div>
                                        </div>
                                        <span
                                            class="text-xs font-bold text-gray-600 whitespace-nowrap">{{ number_format($item->persen_terpakai, 1) }}%</span>
                                    </div>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            onclick="openModalEdit('{{ $item->id }}','{{ $item->proyek }}','{{ $item->kategori }}','{{ $item->budget }}','{{ $item->realisasi }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                            <i class="fa fa-edit text-xs"></i> Edit
                                        </button>
                                        <form action="{{ route('budgeting.destroy', $item->id) }}" method="POST"
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
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fa fa-folder-open text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data budgeting</p>
                                        <p class="text-xs text-gray-400">Klik "Tambah Budget" untuk menambahkan data baru
                                        </p>
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
    MODAL TAMBAH
====================================== --}}
    <div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Tambah Budgeting</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Isi data budgeting proyek baru</p>
                </div>
                <button onclick="closeModalTambah()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form action="{{ route('budgeting.store') }}" method="POST" class="px-6 py-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Proyek <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="proyek" required placeholder="Nama proyek"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="kategori" required placeholder="Kategori proyek"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Budget <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="budget" required placeholder="Nominal budget"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Realisasi <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="realisasi" required placeholder="Nominal realisasi"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                </div>

                <div class="flex justify-end gap-3 pt-5">
                    <button type="button" onclick="closeModalTambah()"
                        class="px-5 py-2 rounded-xl text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white transition-colors flex items-center gap-2">
                        <i class="fa fa-save text-sm"></i> Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>


    {{-- ======================================
    MODAL EDIT
====================================== --}}
    <div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
        style="backdrop-filter:blur(2px)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4" style="animation:slideUp .2s ease">

            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Budgeting</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data budgeting proyek</p>
                </div>
                <button onclick="closeModalEdit()"
                    class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" class="px-6 py-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Proyek <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="proyek" id="edit_proyek" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="kategori" id="edit_kategori" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Budget <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="budget" id="edit_budget" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Realisasi <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="realisasi" id="edit_realisasi" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    </div>

                </div>

                <div class="flex justify-end gap-3 pt-5">
                    <button type="button" onclick="closeModalEdit()"
                        class="px-5 py-2 rounded-xl text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white transition-colors flex items-center gap-2">
                        <i class="fa fa-save text-sm"></i> Update
                    </button>
                </div>
            </form>

        </div>
    </div>


    {{-- ======================================
    POPUP ALERT (FIXED OVERLAY)
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
                    class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0"
                    aria-label="Tutup">
                    <i class="fa fa-times"></i>
                </button>

            </div>
        </div>
    @endif


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
        // ── MODAL TAMBAH ───────────────────────────────────
const modalTambah = document.getElementById('modalTambah');
const modalEdit   = document.getElementById('modalEdit');

function openModalTambah() {
    modalTambah.classList.remove('hidden');
    modalTambah.classList.add('flex');
}
function closeModalTambah() {
    modalTambah.classList.add('hidden');
    modalTambah.classList.remove('flex');
}
modalTambah.addEventListener('click', e => { if (e.target === modalTambah) closeModalTambah(); });

// ── MODAL EDIT ─────────────────────────────────────
function openModalEdit(id, proyek, kategori, budget, realisasi) {
    document.getElementById('formEdit').action      = `/admin/budgeting/${id}`;
    document.getElementById('edit_proyek').value    = proyek;
    document.getElementById('edit_kategori').value  = kategori;
    document.getElementById('edit_budget').value    = budget;
    document.getElementById('edit_realisasi').value = realisasi;
    modalEdit.classList.remove('hidden');
    modalEdit.classList.add('flex');
}
function closeModalEdit() {
    modalEdit.classList.add('hidden');
    modalEdit.classList.remove('flex');
}
modalEdit.addEventListener('click', e => { if (e.target === modalEdit) closeModalEdit(); });

// ── UPDATE EXPORT LINKS ────────────────────────────
function updateExportLinks() {
    const keyword = document.getElementById('searchInput').value.trim();
    const query   = keyword ? '?search=' + encodeURIComponent(keyword) : '';

    document.getElementById('pdfBtn').href   = `/admin/budgeting/pdf${query}`;
    document.getElementById('excelBtn').href = `/admin/budgeting/excel${query}`;
}

// ── SEARCH / FILTER ────────────────────────────────
function applyFilters() {
    const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
    const rows    = document.querySelectorAll('#budgetingTableBody tr[data-search]');
    let visible   = 0;

    rows.forEach(row => {
        const show = !keyword || row.dataset.search.includes(keyword);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    updateExportLinks();

    document.getElementById('totalCount').textContent = visible + ' total data';

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

// initial
updateExportLinks();

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
