@extends('admin.layouts.app')
@section('title', 'Induk Asset')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Induk Asset</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data pokok seluruh aset perusahaan</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="bi bi-plus-lg"></i> Tambah Aset
        </button>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="bi bi-building-check"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Aset</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalAktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Aktif</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-400 text-lg flex-shrink-0">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalNonaktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Nonaktif</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none text-sm">Rp {{ number_format($totalNilai,0,',','.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Nilai Perolehan</p>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Induk Aset</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total aset</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari aset..." oninput="onSearchInput(this.value)"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
                <select id="perPageSelect" onchange="renderTable()"
                    class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="10" selected>10</option><option value="25">25</option>
                    <option value="50">50</option><option value="all">All</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kode Aset</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama Aset</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kategori</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Lokasi</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl Perolehan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Harga</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">PIC</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Umur (thn)</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Metode</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors"
                        data-search="{{ strtolower($d->kode_aset.' '.$d->nama_aset.' '.$d->kategori.' '.$d->lokasi.' '.$d->pic) }}">
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $data->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5"><span class="font-mono text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded">{{ $d->kode_aset }}</span></td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800 text-sm">{{ $d->nama_aset }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->kategori }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->lokasi }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal_perolehan ? \Carbon\Carbon::parse($d->tanggal_perolehan)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">Rp {{ number_format($d->harga_perolehan,0,',','.') }}</td>
                        <td class="px-4 py-3.5">
                            @if($d->status === 'Aktif')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600"><i class="bi bi-circle-fill text-[6px]"></i> Aktif</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500"><i class="bi bi-circle-fill text-[6px]"></i> Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->pic }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-600 text-center">{{ $d->umur_ekonomis }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->metode_penyusutan }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                    data-id="{{ $d->id }}" data-action="{{ route('asset.induk.update', $d->id) }}"
                                    data-kode_aset="{{ $d->kode_aset }}" data-nama_aset="{{ $d->nama_aset }}"
                                    data-kategori="{{ $d->kategori }}" data-lokasi="{{ $d->lokasi }}"
                                    data-tanggal_perolehan="{{ $d->tanggal_perolehan }}" data-harga_perolehan="{{ $d->harga_perolehan }}"
                                    data-status="{{ $d->status }}" data-pic="{{ $d->pic }}"
                                    data-umur_ekonomis="{{ $d->umur_ekonomis }}" data-metode_penyusutan="{{ $d->metode_penyusutan }}"
                                    onclick="triggerEdit(this)">
                                    <i class="bi bi-pencil text-xs"></i> Edit
                                </button>
                                <button type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                    data-action="{{ route('asset.induk.destroy', $d->id) }}" data-name="{{ $d->kode_aset }}"
                                    onclick="triggerDelete(this)">
                                    <i class="bi bi-trash text-xs"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i class="bi bi-building-check text-2xl text-gray-300"></i></div>
                            <p class="text-sm font-medium text-gray-500">Belum ada data aset</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
        </div>
        <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfo"></div>
    </div>
</div>

{{-- MODAL TAMBAH/EDIT --}}
<div id="mainModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <div><h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Aset</h2></div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg leading-none"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="mainForm" action="{{ route('asset.induk.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_aset" id="f_kode_aset" required placeholder="AST-001"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('kode_aset') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_aset" id="f_nama_aset" required placeholder="Nama aset"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('nama_aset') }}">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="kategori" id="f_kategori" required placeholder="Kendaraan / Elektronik / dll"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('kategori') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi" id="f_lokasi" required placeholder="Gudang A / Kantor Pusat"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('lokasi') }}">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Perolehan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_perolehan" id="f_tanggal_perolehan" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('tanggal_perolehan') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga Perolehan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="harga_perolehan" id="f_harga_perolehan" required placeholder="50000000"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('harga_perolehan') }}">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="f_status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option><option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">PIC <span class="text-red-500">*</span></label>
                    <input type="text" name="pic" id="f_pic" required placeholder="Nama penanggung jawab"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('pic') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Umur Ekonomis (thn) <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="umur_ekonomis" id="f_umur_ekonomis" required placeholder="5"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('umur_ekonomis') }}">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Metode Penyusutan <span class="text-red-500">*</span></label>
                <select name="metode_penyusutan" id="f_metode_penyusutan" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">- Pilih Metode -</option>
                    <option value="Garis Lurus" {{ old('metode_penyusutan') == 'Garis Lurus' ? 'selected' : '' }}>Garis Lurus</option>
                    <option value="Saldo Menurun" {{ old('metode_penyusutan') == 'Saldo Menurun' ? 'selected' : '' }}>Saldo Menurun</option>
                    <option value="Unit Produksi" {{ old('metode_penyusutan') == 'Unit Produksi' ? 'selected' : '' }}>Unit Produksi</option>
                </select>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                <i class="bi bi-floppy"></i> Simpan Data
            </button>
        </form>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Aset?</h2>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">Kamu akan menghapus <strong id="deleteName" class="text-gray-700"></strong>. Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">Batal</button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5"><i class="bi bi-trash text-xs"></i> Hapus</button>
        </form>
    </div>
</div>

{{-- FLASH ALERT --}}
@if(session('success') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6" style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4" style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="bi bi-check-circle-fill"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="bi bi-exclamation-circle-fill"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none"><i class="bi bi-x-lg"></i></button>
    </div>
</div>
@endif

<style>@keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}</style>
<script>
const mainModal = document.getElementById('mainModal');
const mainForm  = document.getElementById('mainForm');
const methodContainer = document.getElementById('methodContainer');
const createUrl = "{{ route('asset.induk.store') }}";

function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Aset';
    mainForm.action = createUrl; methodContainer.innerHTML = ''; mainForm.reset();
    mainModal.classList.remove('hidden'); mainModal.classList.add('flex');
}
function closeModal() { mainModal.classList.add('hidden'); mainModal.classList.remove('flex'); }
mainModal.addEventListener('click', e => { if(e.target===mainModal) closeModal(); });

function triggerEdit(btn) {
    document.getElementById('modalTitle').innerText = 'Edit Aset';
    mainForm.action = btn.dataset.action;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('f_kode_aset').value          = btn.dataset.kode_aset ?? '';
    document.getElementById('f_nama_aset').value          = btn.dataset.nama_aset ?? '';
    document.getElementById('f_kategori').value           = btn.dataset.kategori ?? '';
    document.getElementById('f_lokasi').value             = btn.dataset.lokasi ?? '';
    document.getElementById('f_tanggal_perolehan').value  = btn.dataset.tanggal_perolehan ?? '';
    document.getElementById('f_harga_perolehan').value    = btn.dataset.harga_perolehan ?? '';
    document.getElementById('f_status').value             = btn.dataset.status ?? '';
    document.getElementById('f_pic').value                = btn.dataset.pic ?? '';
    document.getElementById('f_umur_ekonomis').value      = btn.dataset.umur_ekonomis ?? '';
    document.getElementById('f_metode_penyusutan').value  = btn.dataset.metode_penyusutan ?? '';
    mainModal.classList.remove('hidden'); mainModal.classList.add('flex');
}

const deleteModal = document.getElementById('deleteModal');
function triggerDelete(btn) {
    document.getElementById('deleteForm').action = btn.dataset.action;
    document.getElementById('deleteName').innerText = btn.dataset.name || 'ini';
    deleteModal.classList.remove('hidden'); deleteModal.classList.add('flex');
}
function closeDeleteModal() { deleteModal.classList.add('hidden'); deleteModal.classList.remove('flex'); }
deleteModal.addEventListener('click', e => { if(e.target===deleteModal) closeDeleteModal(); });

const allRows = Array.from(document.querySelectorAll('#tableBody tr[data-search]'));
let currentSearch = '';
function onSearchInput(v) { currentSearch = v.toLowerCase(); renderTable(); }
function renderTable() {
    if (!allRows.length) return;
    const perPage = document.getElementById('perPageSelect').value === 'all' ? Infinity : parseInt(document.getElementById('perPageSelect').value);
    const matched = allRows.filter(r => r.dataset.search.includes(currentSearch));
    let shown = 0;
    allRows.forEach(r => r.style.display='none');
    matched.forEach(r => { if(shown < perPage){ r.style.display=''; shown++; } });
    document.getElementById('entriesInfo').innerText = matched.length ? `Menampilkan ${shown} dari ${matched.length} entri` : 'Tidak ada data';
}
document.addEventListener('DOMContentLoaded', renderTable);

(function(){
    var overlay=document.getElementById('alertOverlay'), box=document.getElementById('alertBox');
    if(!overlay) return;
    setTimeout(()=>{ overlay.style.opacity='1'; overlay.style.pointerEvents='auto'; box.style.transform='translateY(0)'; },80);
    var t=setTimeout(closeAlert,4500);
    overlay.addEventListener('click',e=>{ if(e.target===overlay) closeAlert(); });
    function closeAlert(){ clearTimeout(t); overlay.style.opacity='0'; overlay.style.pointerEvents='none'; box.style.transform='translateY(-16px)'; }
    window.closeAlert=closeAlert;
})();

        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof openModalTambah === 'function') openModalTambah();
            else if (typeof openModal === 'function') openModal();
        });
        @endif
</script>
@endsection
