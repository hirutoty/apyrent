@extends('admin.layouts.app')
@section('title', 'DevOps')
@section('content')
<div class="space-y-6">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div><h1 class="text-2xl font-bold text-gray-800">DevOps</h1><p class="text-sm text-gray-500 mt-0.5">Kelola pipeline CI/CD dan deployment</p></div>
    <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors"><i class="fa fa-plus"></i> Tambah Pipeline</button>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-code-branch"></i></div>
        <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPipeline }}</p><p class="text-xs text-gray-500 mt-1">Total Pipeline</p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="fa fa-check-circle"></i></div>
        <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalAktif }}</p><p class="text-xs text-gray-500 mt-1">Aktif</p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 text-lg flex-shrink-0"><i class="fa fa-pause"></i></div>
        <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalNonaktif }}</p><p class="text-xs text-gray-500 mt-1">Nonaktif</p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0"><i class="fa fa-rocket"></i></div>
        <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalOtomatis }}</p><p class="text-xs text-gray-500 mt-1">Deploy Otomatis</p></div>
    </div>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
        <div><h2 class="font-semibold text-gray-800 text-base">Daftar Pipeline</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total</p></div>
        <div class="relative"><i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Cari aplikasi..." oninput="onSearch(this.value)" class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44"></div>
    </div>
    <div class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-gray-100 text-xs">
        <div class="flex items-center gap-2"><span class="text-gray-500">Show</span>
            <select id="perPage" onchange="renderTable()" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none"><option value="10" selected>10</option><option value="25">25</option><option value="50">50</option><option value="all">All</option></select>
            <span class="text-gray-500">entries</span></div>
        <select id="filterStatus" onchange="renderTable()" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
            <option value="">Semua Status</option><option>Aktif</option><option>Nonaktif</option></select>
        <div class="ml-auto text-xs text-gray-400" id="entriesInfoTop"></div>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="bg-gray-50 border-b border-gray-100">
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aplikasi</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tools</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Deployment Otomatis</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jadwal Build</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
        </tr></thead>
        <tbody id="tableBody">
            @forelse($data as $d)
            <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors"
                data-search="{{ strtolower($d->aplikasi.' '.$d->tools.' '.$d->jadwal_build) }}"
                data-status="{{ $d->status }}">
                <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>
                <td class="px-4 py-3.5 text-sm font-semibold text-gray-800">{{ $d->aplikasi }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->tools }}</td>
                <td class="px-4 py-3.5">
                    @if($d->deployment_otomatis === 'Ya')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-600">Ya</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Tidak</span>
                    @endif
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->jadwal_build }}</td>
                <td class="px-4 py-3.5">
                    @php $sc=['Aktif'=>'green','Nonaktif'=>'gray'][$d->status]??'gray' @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $sc }}-100 text-{{ $sc }}-600"><i class="fa fa-circle text-[6px]"></i> {{ $d->status }}</span>
                </td>
                <td class="px-4 py-3.5"><div class="flex items-center justify-center gap-1.5">
                    <button onclick="triggerEdit(this)" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                        data-action="{{ route('devops.update', $d->id) }}"
                        data-aplikasi="{{ $d->aplikasi }}" data-tools="{{ $d->tools }}"
                        data-deployment_otomatis="{{ $d->deployment_otomatis }}"
                        data-jadwal_build="{{ $d->jadwal_build }}" data-status="{{ $d->status }}">
                        <i class="fa fa-edit text-xs"></i> Edit</button>
                    <button onclick="triggerDelete(this)" type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                        data-action="{{ route('devops.destroy', $d->id) }}" data-name="{{ $d->aplikasi }}">
                        <i class="fa fa-trash text-xs"></i> Hapus</button>
                </div></td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-12 text-center"><div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i class="fa fa-code-branch text-2xl text-gray-300"></i></div>
                <p class="text-sm font-medium text-gray-500">Belum ada data DevOps</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div></div>
    <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfo"></div>
</div>
<div id="mainModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Pipeline</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg leading-none"><i class="fa fa-times"></i></button>
        </div>
        <form id="mainForm" action="{{ route('devops.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf <div id="methodContainer"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Aplikasi <span class="text-red-500">*</span></label>
                    <input type="text" name="aplikasi" id="f_aplikasi" required placeholder="API Backend / Frontend" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Tools <span class="text-red-500">*</span></label>
                    <input type="text" name="tools" id="f_tools" required placeholder="Jenkins / GitHub Actions" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Deployment Otomatis <span class="text-red-500">*</span></label>
                    <select name="deployment_otomatis" id="f_deployment_otomatis" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih -</option><option value="Ya">Ya</option><option value="Tidak">Tidak</option></select></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Jadwal Build <span class="text-red-500">*</span></label>
                    <input type="text" name="jadwal_build" id="f_jadwal_build" required placeholder="Setiap hari / Push" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="f_status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih -</option><option>Aktif</option><option>Nonaktif</option></select></div>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2"><i class="fa fa-save"></i> Simpan Data</button>
        </form>
    </div>
</div>
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl"><i class="fa fa-triangle-exclamation"></i></div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Pipeline?</h2>
            <p class="text-xs text-gray-500 mt-1.5">Kamu akan menghapus <strong id="deleteName" class="text-gray-700"></strong>.</p>
        </div>
        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">Batal</button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5"><i class="fa fa-trash text-xs"></i> Hapus</button>
        </form>
    </div>
</div>
@if(session('success')||$errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6" style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4" style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))<div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else<div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p><ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none flex-shrink-0"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif
<style>@keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}</style>
<script>
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer');
const createUrl="{{ route('devops.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah Pipeline';mainForm.action=createUrl;methodContainer.innerHTML='';mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(btn){document.getElementById('modalTitle').innerText='Edit Pipeline';mainForm.action=btn.dataset.action;methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
['aplikasi','tools','deployment_otomatis','jadwal_build','status'].forEach(k=>{const el=document.getElementById('f_'+k);if(el)el.value=btn.dataset[k]??'';});mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
const deleteModal=document.getElementById('deleteModal');
function triggerDelete(btn){document.getElementById('deleteForm').action=btn.dataset.action;document.getElementById('deleteName').innerText=btn.dataset.name||'ini';deleteModal.classList.remove('hidden');deleteModal.classList.add('flex');}
function closeDeleteModal(){deleteModal.classList.add('hidden');deleteModal.classList.remove('flex');}
deleteModal.addEventListener('click',e=>{if(e.target===deleteModal)closeDeleteModal();});
const allRows=Array.from(document.querySelectorAll('#tableBody tr[data-search]'));let currentSearch='';
function onSearch(v){currentSearch=v.toLowerCase();renderTable();}
function renderTable(){const perPage=document.getElementById('perPage').value==='all'?Infinity:parseInt(document.getElementById('perPage').value);const fS=document.getElementById('filterStatus').value;const matched=allRows.filter(r=>r.dataset.search.includes(currentSearch)&&(!fS||r.dataset.status===fS));let shown=0;allRows.forEach(r=>r.style.display='none');matched.forEach(r=>{if(shown<perPage){r.style.display='';shown++;}});const info=matched.length===0?'Tidak ada data':`Menampilkan ${shown} dari ${matched.length} entri`;document.getElementById('entriesInfo').innerText=info;document.getElementById('entriesInfoTop').innerText=info;}
document.addEventListener('DOMContentLoaded',renderTable);
(function(){var o=document.getElementById('alertOverlay'),b=document.getElementById('alertBox');if(!o)return;setTimeout(()=>{o.style.opacity='1';o.style.pointerEvents='auto';b.style.transform='translateY(0)';},80);var t=setTimeout(closeAlert,4500);o.addEventListener('click',e=>{if(e.target===o)closeAlert();});function closeAlert(){clearTimeout(t);o.style.opacity='0';o.style.pointerEvents='none';b.style.transform='translateY(-16px)';}window.closeAlert=closeAlert;})();
</script>
</div>
@endsection
