@extends('admin.layouts.app')

@section('title', 'Workflow Procurement')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Workflow Procurement</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola aturan otomatisasi persetujuan pengadaan barang & vendor</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
            <i class="fa fa-plus text-sm"></i>
            Tambah Workflow
        </button>
    </div>

    {{-- STAT CARDS + CHART (4 kolom sejajar, ukuran sama) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-diagram-project"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalWorkflow }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Workflow</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalAktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Workflow Aktif</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-pause"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalNonaktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Workflow Nonaktif</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 flex-shrink-0 relative">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Distribusi Status</p>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Aktif
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> Nonaktif
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Workflow</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total workflow</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari workflow..."
                        oninput="onSearchInput(this.value)"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
                <button onclick="window.location.reload()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa fa-sync text-xs"></i> Refresh
                </button>
            </div>
        </div>

        {{-- SHOW ENTRIES FILTER --}}
        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            <span>Show</span>
            <select id="perPageSelect" onchange="onPerPageChange(this.value)"
                class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>
            <span>entries</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">ID</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Workflow ID</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Event</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Syarat Tambahan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Delay Aksi</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">PIC</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Catatan</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody id="procurementoTableBody">
                    @forelse($data as $d)
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors duration-100"
                            data-search="{{ strtolower($d->workflow_id . ' ' . $d->nama_workflow . ' ' . $d->trigger_event . ' ' . $d->pic) }}">

                            <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>

                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $d->workflow_id }}</span>
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-diagram-project text-blue-400 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">{{ $d->nama_workflow ?? '-' }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->trigger_event ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[160px] truncate">{{ $d->syarat_tambahan ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->aksi_dilakukan ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->delay_aksi ?? '-' }}</td>

                            <td class="px-4 py-3.5">
                                @if($d->status === 'Aktif')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                                        <i class="fa fa-circle text-[6px]"></i> Aktif
                                    </span>
                                @elseif($d->status === 'Nonaktif')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                                        <i class="fa fa-circle text-[6px]"></i> Nonaktif
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->pic ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[160px] truncate">{{ $d->catatan ?? '-' }}</td>

                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $d->id }}"
                                        data-action="{{ route('procuremento.update', $d->id) }}"
                                        data-workflow_id="{{ $d->workflow_id }}"
                                        data-nama_workflow="{{ $d->nama_workflow }}"
                                        data-trigger_event="{{ $d->trigger_event }}"
                                        data-syarat_tambahan="{{ $d->syarat_tambahan }}"
                                        data-aksi_dilakukan="{{ $d->aksi_dilakukan }}"
                                        data-delay_aksi="{{ $d->delay_aksi }}"
                                        data-status="{{ $d->status }}"
                                        data-pic="{{ $d->pic }}"
                                        data-catatan="{{ $d->catatan }}"
                                        onclick="triggerEdit(this)">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                        data-action="{{ route('procuremento.destroy', $d->id) }}"
                                        data-name="{{ $d->nama_workflow }}"
                                        onclick="triggerDelete(this)">
                                        <i class="fa fa-trash text-xs"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-diagram-project text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data workflow</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah Workflow" untuk menambahkan workflow baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
        </div>

        {{-- ENTRIES INFO --}}
        <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfo"></div>

    </div>

</div>


{{-- ======================================
    MODAL TAMBAH / EDIT WORKFLOW
======================================--}}
<div id="procurementoModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto"
         style="animation:slideUp .2s ease">

        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Workflow</h2>
                <p id="modalSubtitle" class="text-xs text-gray-500 mt-0.5">Workflow ID akan dibuat otomatis</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="procurementoForm" action="{{ route('procuremento.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>

            {{-- Workflow ID: hanya tampil saat edit, non-editable --}}
            <div id="workflowIdBox" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Workflow ID</label>
                <div class="flex items-center gap-2">
                    <span id="f_workflow_id_display" class="font-mono text-xs text-gray-600 bg-gray-100 px-3 py-2 rounded-lg border border-gray-200"></span>
                    <span class="text-xs text-gray-400">(otomatis, tidak bisa diubah)</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Workflow <span class="text-red-500">*</span></label>
                <input type="text" name="nama_workflow" id="f_nama_workflow" required
                    placeholder="Contoh: Follow-Up Keranjang Kosong"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Event <span class="text-red-500">*</span></label>
                <input type="text" name="trigger_event" id="f_trigger_event" required
                    placeholder="Contoh: Keranjang ditinggal > 1 hari"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Syarat Tambahan <span class="text-red-500">*</span></label>
                <input type="text" name="syarat_tambahan" id="f_syarat_tambahan" required
                    placeholder="Contoh: Total nilai > 100.000"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aksi <span class="text-red-500">*</span></label>
                    <input type="text" name="aksi_dilakukan" id="f_aksi_dilakukan" required
                        placeholder="Contoh: Kirim Email Reminder"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Delay Aksi <span class="text-red-500">*</span></label>
                    <input type="text" name="delay_aksi" id="f_delay_aksi" required
                        placeholder="Contoh: 24 jam"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="f_status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih Status -</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">PIC <span class="text-red-500">*</span></label>
                    <input type="text" name="pic" id="f_pic" required
                        placeholder="Contoh: Rina"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                <textarea name="catatan" id="f_catatan" rows="3"
                    placeholder="Catatan tambahan (opsional)..."
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
    MODAL KONFIRMASI HAPUS
======================================--}}
<div id="deleteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30"
     style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4"
         style="animation:slideUp .2s ease">

        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl">
                <i class="fa fa-triangle-exclamation"></i>
            </div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Workflow?</h2>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                Kamu akan menghapus workflow <strong id="deleteName" class="text-gray-700"></strong>.
                Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="submit"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-trash text-xs"></i> Hapus
            </button>
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


{{-- STYLE & SCRIPT --}}
<style>
@keyframes slideUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── PROCUREMENTO MODAL (Tambah/Edit) ────────────────
const procurementoModal = document.getElementById('procurementoModal');
const procurementoForm  = document.getElementById('procurementoForm');
const methodContainer   = document.getElementById('methodContainer');
const workflowIdBox     = document.getElementById('workflowIdBox');
const createUrl         = "{{ route('procuremento.store') }}";

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Workflow';
    document.getElementById('modalSubtitle').innerText = 'Workflow ID akan dibuat otomatis';
    procurementoForm.action = createUrl;
    methodContainer.innerHTML = '';
    workflowIdBox.classList.add('hidden');
    procurementoForm.reset();
    procurementoModal.classList.remove('hidden');
    procurementoModal.classList.add('flex');
}

function closeModal() {
    procurementoModal.classList.add('hidden');
    procurementoModal.classList.remove('flex');
}

procurementoModal.addEventListener('click', function (e) {
    if (e.target === procurementoModal) closeModal();
});

function triggerEdit(btn) {
    document.getElementById('modalTitle').innerText = 'Edit Workflow';
    document.getElementById('modalSubtitle').innerText = 'Perbarui detail workflow';
    procurementoForm.action = btn.dataset.action;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

    document.getElementById('f_workflow_id_display').innerText = btn.dataset.workflow_id;
    workflowIdBox.classList.remove('hidden');

    document.getElementById('f_nama_workflow').value   = btn.dataset.nama_workflow ?? '';
    document.getElementById('f_trigger_event').value   = btn.dataset.trigger_event ?? '';
    document.getElementById('f_syarat_tambahan').value = btn.dataset.syarat_tambahan ?? '';
    document.getElementById('f_aksi_dilakukan').value  = btn.dataset.aksi_dilakukan ?? '';
    document.getElementById('f_delay_aksi').value      = btn.dataset.delay_aksi ?? '';
    document.getElementById('f_status').value          = btn.dataset.status ?? '';
    document.getElementById('f_pic').value              = btn.dataset.pic ?? '';
    document.getElementById('f_catatan').value          = btn.dataset.catatan ?? '';

    procurementoModal.classList.remove('hidden');
    procurementoModal.classList.add('flex');
}

// ── DELETE MODAL ─────────────────────────────────────
const deleteModal = document.getElementById('deleteModal');
const deleteForm  = document.getElementById('deleteForm');
const deleteName  = document.getElementById('deleteName');

function triggerDelete(btn) {
    deleteForm.action = btn.dataset.action;
    deleteName.innerText = btn.dataset.name || 'ini';
    deleteModal.classList.remove('hidden');
    deleteModal.classList.add('flex');
}

function closeDeleteModal() {
    deleteModal.classList.add('hidden');
    deleteModal.classList.remove('flex');
}

deleteModal.addEventListener('click', function (e) {
    if (e.target === deleteModal) closeDeleteModal();
});

// ── SEARCH + SHOW ENTRIES (gabungan) ────────────────
const allRows      = Array.from(document.querySelectorAll('#procurementoTableBody tr[data-search]'));
const entriesInfo  = document.getElementById('entriesInfo');
let currentSearch  = '';
let currentPerPage = 10;

function onSearchInput(value) {
    currentSearch = value.toLowerCase();
    renderTable();
}

function onPerPageChange(value) {
    currentPerPage = value === 'all' ? Infinity : parseInt(value, 10);
    renderTable();
}

function renderTable() {
    if (allRows.length === 0) return;

    const matched = allRows.filter(row => row.dataset.search.includes(currentSearch));
    let shownCount = 0;

    allRows.forEach(row => row.style.display = 'none');

    matched.forEach(row => {
        if (shownCount < currentPerPage) {
            row.style.display = '';
            shownCount++;
        }
    });

    entriesInfo.innerText = matched.length === 0
        ? 'Tidak ada data yang cocok'
        : `Menampilkan ${shownCount} dari ${matched.length} entri` + (currentSearch ? ' (hasil pencarian)' : '');
}

document.addEventListener('DOMContentLoaded', renderTable);

// ── CHART DISTRIBUSI STATUS (compact, sejajar dengan stat card) ──
const statusLabels = {!! json_encode($statusStats->keys()) !!};
const statusData   = {!! json_encode($statusStats->values()) !!};

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusData,
            backgroundColor: statusLabels.map(label => label === 'Aktif' ? '#22c55e' : '#ef4444'),
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        cutout: '68%'
    }
});

// ── POPUP ALERT (fixed overlay) ────────────────────
(function () {
    var overlay = document.getElementById('alertOverlay');
    var box     = document.getElementById('alertBox');
    if (!overlay) return;

    setTimeout(function () {
        overlay.style.opacity      = '1';
        overlay.style.pointerEvents = 'auto';
        box.style.transform        = 'translateY(0)';
    }, 80);

    var timer = setTimeout(closeAlert, 4500);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeAlert();
    });

    function closeAlert() {
        clearTimeout(timer);
        overlay.style.opacity      = '0';
        overlay.style.pointerEvents = 'none';
        box.style.transform        = 'translateY(-16px)';
    }
    window.closeAlert = closeAlert;
})();
</script>

@endsection