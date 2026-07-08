@extends('admin.layouts.app')
@section('title', 'Vendor Performance')
@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Vendor Performance</h1>
            <p class="text-sm text-gray-500 mt-0.5">Evaluasi kinerja vendor berdasarkan order dan kualitas</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="fa fa-plus text-sm"></i> Tambah Evaluasi
        </button>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-building"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalVendor }}</p><p class="text-xs text-gray-500 mt-1">Total Vendor</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="fa fa-clock"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $avgKetepatan }}%</p><p class="text-xs text-gray-500 mt-1">Rata-rata Ketepatan</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0"><i class="fa fa-star"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $avgKualitas }}</p><p class="text-xs text-gray-500 mt-1">Rata-rata Kualitas</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 text-lg flex-shrink-0"><i class="fa fa-trophy"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $avgPenilaian }}</p><p class="text-xs text-gray-500 mt-1">Rata-rata Penilaian</p></div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Vendor Performance</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total vendor</p></div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari vendor..." oninput="onSearchInput(this.value)"
                        class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
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
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Vendor</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Total Order</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Ketepatan (%)</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Kualitas</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Komplain</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Penilaian Akhir</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Catatan</th>
                    <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                </tr></thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors"
                        data-search="{{ strtolower($d->vendor) }}">
                        <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5 text-sm font-semibold text-gray-800">{{ $d->vendor??'-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->total_order??0 }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-green-500 h-1.5 rounded-full" style="width:{{ min($d->ketepatan_waktu??0,100) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 w-10 text-right">{{ number_format($d->ketepatan_waktu??0,1) }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-purple-500 h-1.5 rounded-full" style="width:{{ min($d->kualitas_barang??0,100) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 w-10 text-right">{{ number_format($d->kualitas_barang??0,1) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ $d->komplain??0 }}</td>
                        <td class="px-4 py-3.5">
                            @php $score = $d->penilaian_akhir ?? 0; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $score >= 80 ? 'bg-green-100 text-green-700' : ($score >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                <i class="fa fa-star text-[8px]"></i> {{ number_format($score,1) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[140px] truncate">{{ $d->catatan??'-' }}</td>
                        <td class="px-4 py-3.5"><div class="flex items-center justify-center gap-1.5">
                            <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                data-action="{{ route('vendor-performance.update',$d->id) }}"
                                data-vendor="{{ $d->vendor }}" data-total_order="{{ $d->total_order }}"
                                data-ketepatan_waktu="{{ $d->ketepatan_waktu }}" data-kualitas_barang="{{ $d->kualitas_barang }}"
                                data-komplain="{{ $d->komplain }}" data-penilaian_akhir="{{ $d->penilaian_akhir }}"
                                data-catatan="{{ $d->catatan }}"
                                onclick="triggerEdit(this)"><i class="fa fa-edit text-xs"></i> Edit</button>
                            <button type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                data-action="{{ route('vendor-performance.destroy',$d->id) }}" data-name="{{ $d->vendor }}"
                                onclick="triggerDelete(this)"><i class="fa fa-trash text-xs"></i> Hapus</button>
                        </div></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i class="fa fa-building text-2xl text-gray-300"></i></div>
                            <p class="text-sm font-medium text-gray-500">Belum ada data Vendor Performance</p>
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
            <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Evaluasi Vendor</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg leading-none"><i class="fa fa-times"></i></button>
        </div>
        <form id="mainForm" action="{{ route('vendor-performance.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Vendor <span class="text-red-500">*</span></label>
                <input type="text" name="vendor" id="f_vendor" required placeholder="Nama vendor"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total Order <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="total_order" id="f_total_order" required placeholder="50"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Komplain <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="komplain" id="f_komplain" required placeholder="2"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Ketepatan Waktu (%) <span class="text-red-500">*</span></label>
                    <input type="number" min="0" max="100" step="0.01" name="ketepatan_waktu" id="f_ketepatan_waktu" required placeholder="85"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kualitas Barang <span class="text-red-500">*</span></label>
                    <input type="number" min="0" max="100" step="0.01" name="kualitas_barang" id="f_kualitas_barang" required placeholder="90"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penilaian Akhir <span class="text-red-500">*</span></label>
                    <input type="number" min="0" max="100" step="0.01" name="penilaian_akhir" id="f_penilaian_akhir" required placeholder="87"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                <textarea name="catatan" id="f_catatan" rows="3" placeholder="Catatan evaluasi..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                <i class="fa fa-save"></i> Simpan Data
            </button>
        </form>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl"><i class="fa fa-triangle-exclamation"></i></div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Evaluasi Vendor?</h2>
            <p class="text-xs text-gray-500 mt-1.5">Kamu akan menghapus data <strong id="deleteName" class="text-gray-700"></strong>. Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">Batal</button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5"><i class="fa fa-trash text-xs"></i> Hapus</button>
        </form>
    </div>
</div>

@if(session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6" style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4" style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Error!</p><ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4 space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none flex-shrink-0"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>@keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}</style>
<script>
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer'),createUrl="{{ route('vendor-performance.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah Evaluasi Vendor';mainForm.action=createUrl;methodContainer.innerHTML='';mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(btn){
    document.getElementById('modalTitle').innerText='Edit Evaluasi Vendor';
    mainForm.action=btn.dataset.action;
    methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
    ['vendor','total_order','ketepatan_waktu','kualitas_barang','komplain','penilaian_akhir','catatan'].forEach(f=>{
        document.getElementById('f_'+f).value=btn.dataset[f]??'';
    });
    mainModal.classList.remove('hidden');mainModal.classList.add('flex');
}
const deleteModal=document.getElementById('deleteModal');
function triggerDelete(btn){document.getElementById('deleteForm').action=btn.dataset.action;document.getElementById('deleteName').innerText=btn.dataset.name||'ini';deleteModal.classList.remove('hidden');deleteModal.classList.add('flex');}
function closeDeleteModal(){deleteModal.classList.add('hidden');deleteModal.classList.remove('flex');}
deleteModal.addEventListener('click',e=>{if(e.target===deleteModal)closeDeleteModal();});
const allRows=Array.from(document.querySelectorAll('#tableBody tr[data-search]'));
let currentSearch='';
function onSearchInput(v){currentSearch=v.toLowerCase();renderTable();}
function renderTable(){
    if(!allRows.length)return;
    const p=document.getElementById('perPageSelect').value==='all'?Infinity:parseInt(document.getElementById('perPageSelect').value);
    const m=allRows.filter(r=>r.dataset.search.includes(currentSearch));
    let s=0;allRows.forEach(r=>r.style.display='none');
    m.forEach(r=>{if(s<p){r.style.display='';s++;}});
    const i=m.length===0?'Tidak ada data':`Menampilkan ${s} dari ${m.length} entri`;
    document.getElementById('entriesInfo').innerText=i;document.getElementById('entriesInfoTop').innerText=i;
}
function resetFilter(){currentSearch='';document.querySelector('input[oninput="onSearchInput(this.value)"]').value='';document.getElementById('perPageSelect').value='10';renderTable();}
document.addEventListener('DOMContentLoaded',renderTable);
(function(){var o=document.getElementById('alertOverlay'),b=document.getElementById('alertBox');if(!o)return;setTimeout(()=>{o.style.opacity='1';o.style.pointerEvents='auto';b.style.transform='translateY(0)';},80);var t=setTimeout(closeAlert,4500);o.addEventListener('click',e=>{if(e.target===o)closeAlert();});function closeAlert(){clearTimeout(t);o.style.opacity='0';o.style.pointerEvents='none';b.style.transform='translateY(-16px)';}window.closeAlert=closeAlert;})();
</script>
@endsection
