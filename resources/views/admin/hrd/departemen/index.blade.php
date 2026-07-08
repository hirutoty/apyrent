@extends('admin.layouts.app')

@section('title', 'Departemen')

@section('content')

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Departemen</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data departemen perusahaan</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-150">
            <i class="fa fa-plus text-sm"></i> Tambah Departemen
        </button>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-building"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDepartemen }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Departemen</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalAktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Aktif</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-pause"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalNonAktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Non-Aktif</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0">
                <i class="fa fa-users"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPosisi }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Posisi</p>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Departemen</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari departemen..." oninput="onSearchInput(this.value)"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            <span>Show</span>
            <select onchange="onPerPageChange(this.value)"
                class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="all">All</option>
            </select>
            <span>entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama Departemen</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kepala</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl Dibentuk</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Posisi</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors"
                            data-search="{{ strtolower($d->nama_departemen . ' ' . $d->kepala_departemen) }}">
                            <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-building text-blue-400 text-xs"></i>
                                    </div>
                                    <span class="font-semibold text-gray-800">{{ $d->nama_departemen }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-gray-700">{{ $d->kepala_departemen }}</td>
                            <td class="px-4 py-3.5 text-gray-500">{{ \Carbon\Carbon::parse($d->tanggal_dibentuk)->format('d M Y') }}</td>
                            <td class="px-4 py-3.5 text-gray-700 font-medium">{{ $d->jumlah_posisi }}</td>
                            <td class="px-4 py-3.5">
                                @if($d->status_aktif === 'Aktif')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">
                                        <i class="fa fa-circle text-[6px]"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                                        <i class="fa fa-circle text-[6px]"></i> Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-id="{{ $d->id }}"
                                        data-action="{{ route('departemen.update', $d->id) }}"
                                        data-nama_departemen="{{ $d->nama_departemen }}"
                                        data-kepala_departemen="{{ $d->kepala_departemen }}"
                                        data-tanggal_dibentuk="{{ $d->tanggal_dibentuk }}"
                                        data-jumlah_posisi="{{ $d->jumlah_posisi }}"
                                        data-keterangan="{{ $d->keterangan }}"
                                        data-status_aktif="{{ $d->status_aktif }}"
                                        onclick="triggerEdit(this)">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                        data-action="{{ route('departemen.destroy', $d->id) }}"
                                        data-name="{{ $d->nama_departemen }}"
                                        onclick="triggerDelete(this)">
                                        <i class="fa fa-trash text-xs"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa fa-building text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada data departemen</p>
                                    <p class="text-xs text-gray-400">Klik "Tambah Departemen" untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
        </div>
        <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfo"></div>
    </div>
</div>

{{-- MODAL TAMBAH / EDIT --}}
<div id="mainModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <div>
                <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Departemen</h2>
                <p class="text-xs text-gray-500 mt-0.5">Isi data departemen dengan lengkap</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors text-lg leading-none mt-0.5">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="mainForm" action="{{ route('departemen.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Departemen <span class="text-red-500">*</span></label>
                <input type="text" name="nama_departemen" id="f_nama_departemen" required placeholder="Contoh: Human Resource"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kepala Departemen <span class="text-red-500">*</span></label>
                <input type="text" name="kepala_departemen" id="f_kepala_departemen" required placeholder="Nama kepala departemen"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Dibentuk <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_dibentuk" id="f_tanggal_dibentuk" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Posisi <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_posisi" id="f_jumlah_posisi" required min="1" placeholder="Contoh: 10"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                <select name="status_aktif" id="f_status_aktif" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">- Pilih Status -</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Non-Aktif">Non-Aktif</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
                <textarea name="keterangan" id="f_keterangan" rows="3" placeholder="Keterangan tambahan (opsional)..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                <i class="fa fa-save text-sm"></i> Simpan Data
            </button>
        </form>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl">
                <i class="fa fa-triangle-exclamation"></i>
            </div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Departemen?</h2>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                Kamu akan menghapus <strong id="deleteName" class="text-gray-700"></strong>. Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()"
                class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Batal</button>
            <button type="submit"
                class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5 transition-colors">
                <i class="fa fa-trash text-xs"></i> Hapus
            </button>
        </form>
    </div>
</div>

{{-- ALERT --}}
@if(session('success') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
            <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none mt-0.5"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>
@keyframes slideUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
</style>
<script>
const mainModal = document.getElementById('mainModal');
const mainForm  = document.getElementById('mainForm');
const methodContainer = document.getElementById('methodContainer');
const createUrl = "{{ route('departemen.store') }}";

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Departemen';
    mainForm.action = createUrl;
    methodContainer.innerHTML = '';
    mainForm.reset();
    mainModal.classList.remove('hidden'); mainModal.classList.add('flex');
}
function closeModal() { mainModal.classList.add('hidden'); mainModal.classList.remove('flex'); }
mainModal.addEventListener('click', e => { if(e.target===mainModal) closeModal(); });

function triggerEdit(btn) {
    document.getElementById('modalTitle').innerText = 'Edit Departemen';
    mainForm.action = btn.dataset.action;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('f_nama_departemen').value   = btn.dataset.nama_departemen ?? '';
    document.getElementById('f_kepala_departemen').value = btn.dataset.kepala_departemen ?? '';
    document.getElementById('f_tanggal_dibentuk').value  = btn.dataset.tanggal_dibentuk ?? '';
    document.getElementById('f_jumlah_posisi').value     = btn.dataset.jumlah_posisi ?? '';
    document.getElementById('f_keterangan').value        = btn.dataset.keterangan ?? '';
    document.getElementById('f_status_aktif').value      = btn.dataset.status_aktif ?? '';
    mainModal.classList.remove('hidden'); mainModal.classList.add('flex');
}

const deleteModal = document.getElementById('deleteModal');
const deleteForm  = document.getElementById('deleteForm');
function triggerDelete(btn) {
    deleteForm.action = btn.dataset.action;
    document.getElementById('deleteName').innerText = btn.dataset.name;
    deleteModal.classList.remove('hidden'); deleteModal.classList.add('flex');
}
function closeDeleteModal() { deleteModal.classList.add('hidden'); deleteModal.classList.remove('flex'); }
deleteModal.addEventListener('click', e => { if(e.target===deleteModal) closeDeleteModal(); });

const allRows = Array.from(document.querySelectorAll('#tableBody tr[data-search]'));
const entriesInfo = document.getElementById('entriesInfo');
let currentSearch = '', currentPerPage = 10;
function onSearchInput(v) { currentSearch = v.toLowerCase(); renderTable(); }
function onPerPageChange(v) { currentPerPage = v==='all' ? Infinity : parseInt(v); renderTable(); }
function renderTable() {
    if(!allRows.length) return;
    const matched = allRows.filter(r => r.dataset.search.includes(currentSearch));
    let shown = 0;
    allRows.forEach(r => r.style.display = 'none');
    matched.forEach(r => { if(shown < currentPerPage){ r.style.display=''; shown++; } });
    entriesInfo.innerText = matched.length === 0 ? 'Tidak ada data yang cocok'
        : `Menampilkan ${shown} dari ${matched.length} entri` + (currentSearch ? ' (hasil pencarian)' : '');
}
document.addEventListener('DOMContentLoaded', renderTable);

(function(){
    var o = document.getElementById('alertOverlay'), b = document.getElementById('alertBox');
    if(!o) return;
    setTimeout(()=>{ o.style.opacity='1'; o.style.pointerEvents='auto'; b.style.transform='translateY(0)'; }, 80);
    var t = setTimeout(closeAlert, 4500);
    o.addEventListener('click', e => { if(e.target===o) closeAlert(); });
    function closeAlert(){ clearTimeout(t); o.style.opacity='0'; o.style.pointerEvents='none'; b.style.transform='translateY(-16px)'; }
    window.closeAlert = closeAlert;
})();
</script>

@endsection
