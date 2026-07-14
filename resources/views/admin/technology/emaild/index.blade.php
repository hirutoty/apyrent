@extends('admin.layouts.app')
@section('title', 'Email & Domain')
@section('content')
<div class="space-y-6">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div><h1 class="text-2xl font-bold text-gray-800">Email & Domain</h1><p class="text-sm text-gray-500 mt-0.5">Kelola domain dan email perusahaan</p></div>
    <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors"><i class="fa fa-plus"></i> Tambah Domain</button>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-globe"></i></div>
        <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDomain }}</p><p class="text-xs text-gray-500 mt-1">Total Domain</p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="fa fa-check-circle"></i></div>
        <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalAktif }}</p><p class="text-xs text-gray-500 mt-1">Aktif</p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-lg flex-shrink-0"><i class="fa fa-calendar-times"></i></div>
        <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalExpired }}</p><p class="text-xs text-gray-500 mt-1">Nonaktif</p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0"><i class="fa fa-lock"></i></div>
        <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDns }}</p><p class="text-xs text-gray-500 mt-1">DNS Terkelola</p></div>
    </div>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
        <div><h2 class="font-semibold text-gray-800 text-base">Daftar Domain</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total</p></div>
        <div class="relative"><i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Cari domain..." oninput="onSearch(this.value)" class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44"></div>
    </div>
    <div class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-gray-100 text-xs">
        <div class="flex items-center gap-2"><span class="text-gray-500">Show</span>
            <select id="perPage" onchange="renderTable()" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none"><option value="10" selected>10</option><option value="25">25</option><option value="50">50</option><option value="all">All</option></select>
            <span class="text-gray-500">entries</span></div>
        <select id="filterStatus" onchange="renderTable()" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
            <option value="">Semua Status</option><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select>
        <div class="ml-auto text-xs text-gray-400" id="entriesInfoTop"></div>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="bg-gray-50 border-b border-gray-100">
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama Domain</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Provider</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Expired Date</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Email Aktif</th>
            <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">DNS Terkelola</th>
            <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
        </tr></thead>
        <tbody id="tableBody">
            @forelse($data as $d)
            <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors"
                data-search="{{ strtolower($d->nama_domain.' '.$d->provider) }}"
                data-status="{{ $d->status }}">
                <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>
                <td class="px-4 py-3.5 text-sm font-semibold text-blue-600">{{ $d->nama_domain }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->provider }}</td>
                <td class="px-4 py-3.5">
                    @php $sc=['aktif'=>'green','nonaktif'=>'red'][$d->status]??'gray' @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $sc }}-100 text-{{ $sc }}-600"><i class="fa fa-circle text-[6px]"></i> {{ ucfirst($d->status) }}</span>
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->expired_date ? \Carbon\Carbon::parse($d->expired_date)->format('d/m/Y') : '-' }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-700 text-center">{{ $d->email_aktif }}</td>
                <td class="px-4 py-3.5">
                    @if($d->dns_terkelola)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-600">Ya</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Tidak</span>
                    @endif
                </td>
                <td class="px-4 py-3.5"><div class="flex items-center justify-center gap-1.5">
                    <button onclick="triggerEdit(this)" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                        data-action="{{ route('emaild.update', $d->id) }}"
                        data-nama_domain="{{ $d->nama_domain }}" data-provider="{{ $d->provider }}"
                        data-status="{{ $d->status }}" data-expired_date="{{ $d->expired_date }}"
                        data-email_aktif="{{ $d->email_aktif }}" data-dns_terkelola="{{ $d->dns_terkelola ? '1' : '0' }}">
                        <i class="fa fa-edit text-xs"></i> Edit</button>
                    <button onclick="triggerDelete(this)" type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                        data-action="{{ route('emaild.destroy', $d->id) }}" data-name="{{ $d->nama_domain }}">
                        <i class="fa fa-trash text-xs"></i> Hapus</button>
                </div></td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-5 py-12 text-center"><div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i class="fa fa-globe text-2xl text-gray-300"></i></div>
                <p class="text-sm font-medium text-gray-500">Belum ada data domain</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div></div>
    <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfo"></div>
</div>
<div id="mainModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Domain</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg leading-none"><i class="fa fa-times"></i></button>
        </div>
        <form id="mainForm" action="{{ route('emaild.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf <div id="methodContainer"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Domain <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_domain" id="f_nama_domain" required placeholder="perusahaan.com" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Provider <span class="text-red-500">*</span></label>
                    <input type="text" name="provider" id="f_provider" required placeholder="GoDaddy / Niagahoster" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="f_status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih -</option><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Expired Date</label>
                    <input type="date" name="expired_date" id="f_expired_date" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Aktif <span class="text-red-500">*</span></label>
                    <input type="number" name="email_aktif" id="f_email_aktif" required min="0" placeholder="50" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">DNS Terkelola <span class="text-red-500">*</span></label>
                <select name="dns_terkelola" id="f_dns_terkelola" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">- Pilih -</option><option value="1">Ya</option><option value="0">Tidak</option></select></div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2"><i class="fa fa-save"></i> Simpan Data</button>
        </form>
    </div>
</div>
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl"><i class="fa fa-triangle-exclamation"></i></div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Domain?</h2>
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
const createUrl="{{ route('emaild.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah Domain';mainForm.action=createUrl;methodContainer.innerHTML='';mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(btn){document.getElementById('modalTitle').innerText='Edit Domain';mainForm.action=btn.dataset.action;methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
['nama_domain','provider','status','expired_date','email_aktif','dns_terkelola'].forEach(k=>{const el=document.getElementById('f_'+k);if(el)el.value=btn.dataset[k]??'';});mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
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
