@extends('admin.layouts.app')
@section('title', 'Approval Workflow')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Approval Workflow</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola alur persetujuan purchase order</p>
        </div>
        <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="fa fa-plus text-sm"></i> Tambah Approval
        </button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-sitemap"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalApproval }}</p><p class="text-xs text-gray-500 mt-1">Total Approval</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="fa fa-circle-check"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalApproved }}</p><p class="text-xs text-gray-500 mt-1">Approved</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0"><i class="fa fa-hourglass-half"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPending }}</p><p class="text-xs text-gray-500 mt-1">Pending</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 flex-shrink-0 relative"><canvas id="statusChart"></canvas></div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Distribusi Status</p>
                <div class="flex flex-wrap gap-x-3 gap-y-1">
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>Approved</span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span class="w-2 h-2 rounded-full bg-yellow-500 inline-block"></span>Pending</span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>Rejected</span>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Approval Workflow</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total</p></div>
            <div class="flex items-center gap-2">
                <div class="relative"><i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari approval..." oninput="onSearchInput(this.value)" class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
                <button onclick="window.location.reload()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50"><i class="fa fa-sync text-xs"></i> Refresh</button>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            <div class="flex items-center gap-2"><span>Show</span>
                <select id="perPageSelect" onchange="renderTable()" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
                    <option value="5">5</option><option value="10" selected>10</option><option value="25">25</option><option value="all">All</option>
                </select><span>entries</span>
            </div>
            <button onclick="resetFilter()" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50"><i class="fa fa-rotate-left text-[10px]"></i> Reset</button>
            <div class="ml-auto text-xs text-gray-400" id="entriesInfoTop"></div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">ID PO</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Urutan</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Jabatan</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Nama Approver</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tanggal</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Catatan</th>
                    <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                </tr></thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors"
                        data-search="{{ strtolower($d->id_po.' '.$d->jabatan.' '.$d->nama_approver) }}">
                        <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5"><span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $d->id_po??'-' }}</span></td>
                        <td class="px-4 py-3.5 text-sm text-center text-gray-700">{{ $d->urutan_approval??'-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->jabatan??'-' }}</td>
                        <td class="px-4 py-3.5 text-sm font-semibold text-gray-800">{{ $d->nama_approver??'-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal?\Carbon\Carbon::parse($d->tanggal)->format('Y-m-d'):'-' }}</td>
                        <td class="px-4 py-3.5">
                            @if($d->status_approval==='Approved')<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600"><i class="fa fa-circle text-[6px]"></i> Approved</span>
                            @elseif($d->status_approval==='Pending')<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600"><i class="fa fa-circle text-[6px]"></i> Pending</span>
                            @elseif($d->status_approval==='Rejected')<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600"><i class="fa fa-circle text-[6px]"></i> Rejected</span>
                            @else<span class="text-xs text-gray-400">-</span>@endif
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[140px] truncate">{{ $d->catatan??'-' }}</td>
                        <td class="px-4 py-3.5"><div class="flex items-center justify-center gap-1.5">
                            <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                data-action="{{ route('approval-workflow.update',$d->id) }}"
                                data-id_po="{{ $d->id_po }}" data-urutan_approval="{{ $d->urutan_approval }}"
                                data-jabatan="{{ $d->jabatan }}" data-nama_approver="{{ $d->nama_approver }}"
                                data-tanggal="{{ $d->tanggal }}" data-status_approval="{{ $d->status_approval }}"
                                data-catatan="{{ $d->catatan }}"
                                onclick="triggerEdit(this)"><i class="fa fa-edit text-xs"></i> Edit</button>
                            <button type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                data-action="{{ route('approval-workflow.destroy',$d->id) }}" data-name="{{ $d->id_po }}"
                                onclick="triggerDelete(this)"><i class="fa fa-trash text-xs"></i> Hapus</button>
                        </div></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i class="fa fa-sitemap text-2xl text-gray-300"></i></div>
                            <p class="text-sm font-medium text-gray-500">Belum ada data Approval Workflow</p>
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

<div id="mainModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Approval</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg leading-none"><i class="fa fa-times"></i></button>
        </div>
        <form id="mainForm" action="{{ route('approval-workflow.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf<div id="methodContainer"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">ID PO <span class="text-red-500">*</span></label>
                    <input type="text" name="id_po" id="f_id_po" required placeholder="PO-001" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Urutan Approval <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="urutan_approval" id="f_urutan_approval" required placeholder="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="jabatan" id="f_jabatan" required placeholder="Manager Pembelian" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Approver <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_approver" id="f_nama_approver" required placeholder="Nama lengkap" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" id="f_tanggal" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status_approval" id="f_status_approval" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih Status -</option>
                        <option value="Pending">Pending</option><option value="Approved">Approved</option><option value="Rejected">Rejected</option>
                    </select></div>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                <textarea name="catatan" id="f_catatan" rows="3" placeholder="Catatan..." class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea></div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2"><i class="fa fa-save"></i> Simpan Data</button>
        </form>
    </div>
</div>
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl"><i class="fa fa-triangle-exclamation"></i></div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Approval?</h2>
            <p class="text-xs text-gray-500 mt-1.5">Kamu akan menghapus approval untuk <strong id="deleteName" class="text-gray-700"></strong>.</p>
        </div>
        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">@csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">Batal</button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5"><i class="fa fa-trash text-xs"></i> Hapus</button>
        </form>
    </div>
</div>
@if(session('success')||session('error')||$errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6" style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4" style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))<div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div><div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else<div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div><div class="flex-1"><p class="text-sm font-bold text-gray-800">Error!</p><ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none flex-shrink-0"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif
<style>@keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer'),createUrl="{{ route('approval-workflow.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah Approval';mainForm.action=createUrl;methodContainer.innerHTML='';mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(btn){document.getElementById('modalTitle').innerText='Edit Approval';mainForm.action=btn.dataset.action;methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
    ['id_po','urutan_approval','jabatan','nama_approver','tanggal','status_approval','catatan'].forEach(f=>document.getElementById('f_'+f).value=btn.dataset[f]??'');
    mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
const deleteModal=document.getElementById('deleteModal');
function triggerDelete(btn){document.getElementById('deleteForm').action=btn.dataset.action;document.getElementById('deleteName').innerText=btn.dataset.name||'ini';deleteModal.classList.remove('hidden');deleteModal.classList.add('flex');}
function closeDeleteModal(){deleteModal.classList.add('hidden');deleteModal.classList.remove('flex');}
deleteModal.addEventListener('click',e=>{if(e.target===deleteModal)closeDeleteModal();});
const allRows=Array.from(document.querySelectorAll('#tableBody tr[data-search]'));let currentSearch='';
function onSearchInput(v){currentSearch=v.toLowerCase();renderTable();}
function renderTable(){if(!allRows.length)return;const p=document.getElementById('perPageSelect').value==='all'?Infinity:parseInt(document.getElementById('perPageSelect').value);const m=allRows.filter(r=>r.dataset.search.includes(currentSearch));let s=0;allRows.forEach(r=>r.style.display='none');m.forEach(r=>{if(s<p){r.style.display='';s++;}});const i=m.length===0?'Tidak ada data':`Menampilkan ${s} dari ${m.length} entri`;document.getElementById('entriesInfo').innerText=i;document.getElementById('entriesInfoTop').innerText=i;}
function resetFilter(){currentSearch='';document.querySelector('input[oninput="onSearchInput(this.value)"]').value='';document.getElementById('perPageSelect').value='10';renderTable();}
document.addEventListener('DOMContentLoaded',renderTable);
const sL={!! json_encode($statusStats->keys()) !!},sD={!! json_encode($statusStats->values()) !!},cM={Approved:'#22c55e',Pending:'#eab308',Rejected:'#ef4444'};
new Chart(document.getElementById('statusChart'),{type:'doughnut',data:{labels:sL,datasets:[{data:sD,backgroundColor:sL.map(l=>cM[l]||'#9ca3af'),borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},cutout:'68%'}});
(function(){var o=document.getElementById('alertOverlay'),b=document.getElementById('alertBox');if(!o)return;setTimeout(()=>{o.style.opacity='1';o.style.pointerEvents='auto';b.style.transform='translateY(0)';},80);var t=setTimeout(closeAlert,4500);o.addEventListener('click',e=>{if(e.target===o)closeAlert();});function closeAlert(){clearTimeout(t);o.style.opacity='0';o.style.pointerEvents='none';b.style.transform='translateY(-16px)';}window.closeAlert=closeAlert;})();
</script>
@endsection
