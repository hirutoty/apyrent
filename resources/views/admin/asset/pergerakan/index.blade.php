@extends('admin.layouts.app')
@section('title', 'Pergerakan Asset')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pergerakan Asset</h1>
            <p class="text-sm text-gray-500 mt-0.5">Rekam mutasi, peminjaman, dan pengembalian aset</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="bi bi-plus-lg"></i> Tambah Pergerakan
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="bi bi-arrow-left-right"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p><p class="text-xs text-gray-500 mt-1">Total Pergerakan</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0"><i class="bi bi-truck"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalMutasi }}</p><p class="text-xs text-gray-500 mt-1">Mutasi</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0"><i class="bi bi-box-arrow-in-down"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPeminjaman }}</p><p class="text-xs text-gray-500 mt-1">Peminjaman</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="bi bi-box-arrow-in-up"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPengembalian }}</p><p class="text-xs text-gray-500 mt-1">Pengembalian</p></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Pergerakan</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total record</p></div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari..." oninput="onSearchInput(this.value)"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
                <select id="perPageSelect" onchange="renderTable()"
                    class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="10" selected>10</option><option value="25">25</option><option value="50">50</option><option value="all">All</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kode Aset</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jenis</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Dari Lokasi</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Ke Lokasi</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Dilakukan Oleh</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Disetujui Oleh</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Catatan</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors"
                        data-search="{{ strtolower($d->kode_aset.' '.$d->jenis_pergerakan.' '.$d->dari_lokasi.' '.$d->ke_lokasi.' '.$d->dilakukan_oleh) }}">
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $data->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5"><span class="font-mono text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded">{{ $d->kode_aset }}</span></td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal ? \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3.5">
                            @php $jColors = ['Mutasi'=>'purple','Peminjaman'=>'yellow','Pengembalian'=>'green']; $jc = $jColors[$d->jenis_pergerakan] ?? 'gray'; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $jc }}-100 text-{{ $jc }}-600">{{ $d->jenis_pergerakan }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->dari_lokasi }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->ke_lokasi }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-700 font-medium">{{ $d->dilakukan_oleh }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->disetujui_oleh }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[120px] truncate">{{ $d->catatan ?? '-' }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                    data-id="{{ $d->id }}" data-action="{{ route('asset.pergerakan.update', $d->id) }}"
                                    data-kode_aset="{{ $d->kode_aset }}" data-tanggal="{{ $d->tanggal }}"
                                    data-jenis_pergerakan="{{ $d->jenis_pergerakan }}" data-dari_lokasi="{{ $d->dari_lokasi }}"
                                    data-ke_lokasi="{{ $d->ke_lokasi }}" data-dilakukan_oleh="{{ $d->dilakukan_oleh }}"
                                    data-disetujui_oleh="{{ $d->disetujui_oleh }}" data-catatan="{{ $d->catatan }}"
                                    onclick="triggerEdit(this)">
                                    <i class="bi bi-pencil text-xs"></i> Edit
                                </button>
                                <button type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                    data-action="{{ route('asset.pergerakan.destroy', $d->id) }}" data-name="{{ $d->kode_aset }}"
                                    onclick="triggerDelete(this)">
                                    <i class="bi bi-trash text-xs"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i class="bi bi-arrow-left-right text-2xl text-gray-300"></i></div>
                            <p class="text-sm font-medium text-gray-500">Belum ada data pergerakan</p>
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
            <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Pergerakan</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="mainForm" action="{{ route('asset.pergerakan.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_aset" id="f_kode_aset" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="f_tanggal" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Pergerakan <span class="text-red-500">*</span></label>
                <select name="jenis_pergerakan" id="f_jenis_pergerakan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">- Pilih Jenis -</option>
                    <option value="Mutasi">Mutasi</option>
                    <option value="Peminjaman">Peminjaman</option>
                    <option value="Pengembalian">Pengembalian</option>
                </select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dari Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="dari_lokasi" id="f_dari_lokasi" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Ke Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="ke_lokasi" id="f_ke_lokasi" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dilakukan Oleh <span class="text-red-500">*</span></label>
                    <input type="text" name="dilakukan_oleh" id="f_dilakukan_oleh" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Disetujui Oleh <span class="text-red-500">*</span></label>
                    <input type="text" name="disetujui_oleh" id="f_disetujui_oleh" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                <textarea name="catatan" id="f_catatan" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
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
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Data?</h2>
            <p class="text-xs text-gray-500 mt-1.5">Kamu akan menghapus pergerakan aset <strong id="deleteName" class="text-gray-700"></strong>.</p>
        </div>
        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">Batal</button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5"><i class="bi bi-trash text-xs"></i> Hapus</button>
        </form>
    </div>
</div>

@if(session('success') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6" style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4" style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="bi bi-check-circle-fill"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="bi bi-exclamation-circle-fill"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Error!</p><ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg"><i class="bi bi-x-lg"></i></button>
    </div>
</div>
@endif

<style>@keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}</style>
<script>
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer'),createUrl="{{ route('asset.pergerakan.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah Pergerakan';mainForm.action=createUrl;methodContainer.innerHTML='';mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(b){document.getElementById('modalTitle').innerText='Edit Pergerakan';mainForm.action=b.dataset.action;methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
['kode_aset','tanggal','jenis_pergerakan','dari_lokasi','ke_lokasi','dilakukan_oleh','disetujui_oleh','catatan'].forEach(k=>{let el=document.getElementById('f_'+k);if(el)el.value=b.dataset[k]??'';});
mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
const deleteModal=document.getElementById('deleteModal');
function triggerDelete(b){document.getElementById('deleteForm').action=b.dataset.action;document.getElementById('deleteName').innerText=b.dataset.name||'ini';deleteModal.classList.remove('hidden');deleteModal.classList.add('flex');}
function closeDeleteModal(){deleteModal.classList.add('hidden');deleteModal.classList.remove('flex');}
deleteModal.addEventListener('click',e=>{if(e.target===deleteModal)closeDeleteModal();});
const allRows=Array.from(document.querySelectorAll('#tableBody tr[data-search]'));let currentSearch='';
function onSearchInput(v){currentSearch=v.toLowerCase();renderTable();}
function renderTable(){if(!allRows.length)return;const p=document.getElementById('perPageSelect').value==='all'?Infinity:parseInt(document.getElementById('perPageSelect').value);const m=allRows.filter(r=>r.dataset.search.includes(currentSearch));let s=0;allRows.forEach(r=>r.style.display='none');m.forEach(r=>{if(s<p){r.style.display='';s++;}});document.getElementById('entriesInfo').innerText=m.length?`Menampilkan ${s} dari ${m.length} entri`:'Tidak ada data';}
document.addEventListener('DOMContentLoaded',renderTable);
(function(){var o=document.getElementById('alertOverlay'),b=document.getElementById('alertBox');if(!o)return;setTimeout(()=>{o.style.opacity='1';o.style.pointerEvents='auto';b.style.transform='translateY(0)';},80);var t=setTimeout(closeAlert,4500);o.addEventListener('click',e=>{if(e.target===o)closeAlert();});function closeAlert(){clearTimeout(t);o.style.opacity='0';o.style.pointerEvents='none';b.style.transform='translateY(-16px)';}window.closeAlert=closeAlert;})();
</script>
@endsection
