@extends('admin.layouts.app')
@section('title', 'Audit Asset')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Audit Asset</h1>
            <p class="text-sm text-gray-500 mt-0.5">Rekam hasil pemeriksaan fisik dan audit aset</p>
        </div>
        <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="bi bi-plus-lg"></i> Tambah Audit
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="bi bi-clipboard2-check-fill"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p><p class="text-xs text-gray-500 mt-1">Total Audit</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="bi bi-shield-check"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalBaik }}</p><p class="text-xs text-gray-500 mt-1">Kondisi Baik</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-lg flex-shrink-0"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalRusak }}</p><p class="text-xs text-gray-500 mt-1">Rusak</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 text-lg flex-shrink-0"><i class="bi bi-question-circle-fill"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalHilang }}</p><p class="text-xs text-gray-500 mt-1">Hilang</p></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Audit</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total record</p></div>
            <div class="flex items-center gap-2">
                <div class="relative"><i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari..." oninput="onSearchInput(this.value)" class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
                <select id="perPageSelect" onchange="renderTable()" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100">
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl Audit</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Diperiksa Oleh</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status Fisik</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Temuan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tindakan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Catatan</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors"
                        data-search="{{ strtolower($d->kode_aset.' '.$d->diperiksa_oleh.' '.$d->status_fisik.' '.$d->temuan) }}">
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $data->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5"><span class="font-mono text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded">{{ $d->kode_aset }}</span></td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal_audit ? \Carbon\Carbon::parse($d->tanggal_audit)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3.5 text-sm font-medium text-gray-700">{{ $d->diperiksa_oleh }}</td>
                        <td class="px-4 py-3.5">
                            @php $sc=['Baik'=>'green','Rusak Ringan'=>'yellow','Rusak Berat'=>'red','Hilang'=>'gray']; $c=$sc[$d->status_fisik]??'blue'; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $c }}-100 text-{{ $c }}-600">{{ $d->status_fisik }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            @php $fc=['Tidak Ada Temuan'=>'green','Ada Temuan Minor'=>'yellow','Ada Temuan Mayor'=>'red']; $fc2=$fc[$d->temuan]??'blue'; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $fc2 }}-100 text-{{ $fc2 }}-600">{{ $d->temuan }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tindakan ?? '-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[120px] truncate">{{ $d->catatan ?? '-' }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                    data-action="{{ route('asset.audit.update', $d->id) }}"
                                    data-kode_aset="{{ $d->kode_aset }}" data-tanggal_audit="{{ $d->tanggal_audit }}"
                                    data-diperiksa_oleh="{{ $d->diperiksa_oleh }}" data-status_fisik="{{ $d->status_fisik }}"
                                    data-temuan="{{ $d->temuan }}" data-tindakan="{{ $d->tindakan }}" data-catatan="{{ $d->catatan }}"
                                    onclick="triggerEdit(this)"><i class="bi bi-pencil text-xs"></i> Edit</button>
                                <button type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                    data-action="{{ route('asset.audit.destroy', $d->id) }}" data-name="{{ $d->kode_aset }}"
                                    onclick="triggerDelete(this)"><i class="bi bi-trash text-xs"></i> Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3"><div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i class="bi bi-clipboard2-check-fill text-2xl text-gray-300"></i></div>
                        <p class="text-sm font-medium text-gray-500">Belum ada data audit</p></div>
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
            <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Audit</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="mainForm" action="{{ route('asset.audit.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_aset" id="f_kode_aset" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('kode_aset') }}"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Audit <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_audit" id="f_tanggal_audit" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('tanggal_audit') }}"></div>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Diperiksa Oleh <span class="text-red-500">*</span></label>
                <input type="text" name="diperiksa_oleh" id="f_diperiksa_oleh" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('diperiksa_oleh') }}"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Fisik <span class="text-red-500">*</span></label>
                    <select name="status_fisik" id="f_status_fisik" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih -</option><option value="Baik" {{ old('status_fisik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ old('status_fisik') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option><option value="Rusak Berat" {{ old('status_fisik') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option><option value="Hilang" {{ old('status_fisik') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                    </select></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Temuan <span class="text-red-500">*</span></label>
                    <select name="temuan" id="f_temuan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih -</option><option value="Tidak Ada Temuan" {{ old('temuan') == 'Tidak Ada Temuan' ? 'selected' : '' }}>Tidak Ada Temuan</option>
                        <option value="Ada Temuan Minor" {{ old('temuan') == 'Ada Temuan Minor' ? 'selected' : '' }}>Ada Temuan Minor</option><option value="Ada Temuan Mayor" {{ old('temuan') == 'Ada Temuan Mayor' ? 'selected' : '' }}>Ada Temuan Mayor</option>
                    </select></div>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Tindakan</label>
                <input type="text" name="tindakan" id="f_tindakan" placeholder="Tindakan yang perlu diambil" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('tindakan') }}"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                <textarea name="catatan" id="f_catatan" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none">{{ old('catatan') }}</textarea></div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                <i class="bi bi-floppy"></i> Simpan Data</button>
        </form>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Data?</h2>
            <p class="text-xs text-gray-500 mt-1.5">Hapus data audit aset <strong id="deleteName" class="text-gray-700"></strong>?</p>
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
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer'),createUrl="{{ route('asset.audit.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah Audit';mainForm.action=createUrl;methodContainer.innerHTML='';mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(b){document.getElementById('modalTitle').innerText='Edit Audit';mainForm.action=b.dataset.action;methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
['kode_aset','tanggal_audit','diperiksa_oleh','status_fisik','temuan','tindakan','catatan'].forEach(k=>{let el=document.getElementById('f_'+k);if(el)el.value=b.dataset[k]??'';});
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

        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof openModalTambah === 'function') openModalTambah();
            else if (typeof openModal === 'function') openModal();
        });
        @endif
</script>
@endsection
