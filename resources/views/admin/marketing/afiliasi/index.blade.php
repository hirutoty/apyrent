@extends('admin.layouts.app')
@section('title', 'Program Afiliasi')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Program Afiliasi</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola program referral & afiliasi</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('afiliasi.pdf') }}" target="_blank" class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Program
            </button>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-handshake"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p><p class="text-xs text-gray-500 mt-1">Total Program</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="fa fa-circle-check"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalAktif }}</p><p class="text-xs text-gray-500 mt-1">Aktif</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-lg flex-shrink-0"><i class="fa fa-circle-pause"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalNonaktif }}</p><p class="text-xs text-gray-500 mt-1">Nonaktif</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Program Afiliasi</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p></div>
            <div class="relative"><i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Cari program..." oninput="onSearchInput(this.value)" class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44"></div>
        </div>
        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            <span>Show</span><select onchange="onPerPageChange(this.value)" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
                <option value="5">5</option><option value="10" selected>10</option><option value="25">25</option><option value="all">All</option>
            </select><span>entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">ID</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Nama Program</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Kode Referral</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Diskon</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Bonus Pengajak</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Batas Waktu</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors" data-search="{{ strtolower($d->nama_program.' '.$d->kode_referral.' '.$d->status) }}">
                        <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-blue-600">{{ $d->id_program }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800">{{ $d->nama_program }}</td>
                        <td class="px-4 py-3.5"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ $d->kode_referral }}</span></td>
                        <td class="px-4 py-3.5 text-gray-700 font-medium">Rp {{ number_format($d->diskon_referral, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-600">{{ $d->bonus_pengajak }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">{{ \Carbon\Carbon::parse($d->batas_waktu)->format('d M Y') }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $d->status==='Aktif'?'bg-green-100 text-green-700':'bg-red-100 text-red-600' }}">
                                <i class="fa fa-circle text-[6px]"></i> {{ $d->status }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                    data-action="{{ route('afiliasi.update', $d->id) }}" data-id_program="{{ $d->id_program }}"
                                    data-nama_program="{{ $d->nama_program }}" data-kode_referral="{{ $d->kode_referral }}"
                                    data-diskon_referral="{{ $d->diskon_referral }}" data-bonus_pengajak="{{ $d->bonus_pengajak }}"
                                    data-batas_waktu="{{ $d->batas_waktu }}" data-status="{{ $d->status }}" onclick="triggerEdit(this)">
                                    <i class="fa fa-edit text-xs"></i> Edit</button>
                                <button type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                    data-action="{{ route('afiliasi.destroy', $d->id) }}" data-name="{{ $d->nama_program }}" onclick="triggerDelete(this)">
                                    <i class="fa fa-trash text-xs"></i> Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-5 py-12 text-center"><p class="text-sm text-gray-400">Belum ada data program afiliasi</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfo"></div>
    </div>
</div>
<div id="mainModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Program Afiliasi</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg"><i class="fa fa-times"></i></button>
        </div>
        <form id="mainForm" action="{{ route('afiliasi.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf<div id="methodContainer"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">ID Program <span class="text-red-500">*</span></label>
                <input type="text" name="id_program" id="f_id_program" required placeholder="AFI001" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Referral <span class="text-red-500">*</span></label>
                <input type="text" name="kode_referral" id="f_kode_referral" required placeholder="REF-APY001" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Program <span class="text-red-500">*</span></label>
            <input type="text" name="nama_program" id="f_nama_program" required placeholder="Nama program afiliasi" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Diskon Referral (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="diskon_referral" id="f_diskon_referral" required min="0" placeholder="50000" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Bonus Pengajak <span class="text-red-500">*</span></label>
                <input type="text" name="bonus_pengajak" id="f_bonus_pengajak" required placeholder="Rp 75.000 kredit" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Batas Waktu <span class="text-red-500">*</span></label>
                <input type="date" name="batas_waktu" id="f_batas_waktu" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                <select name="status" id="f_status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">- Pilih -</option><option value="Aktif">Aktif</option><option value="Nonaktif">Nonaktif</option>
                </select></div>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2"><i class="fa fa-save"></i> Simpan</button>
        </form>
    </div>
</div>
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 px-6 py-6 text-center">
        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl mb-4"><i class="fa fa-triangle-exclamation"></i></div>
        <h2 class="text-base font-bold text-gray-800">Hapus Program?</h2>
        <p class="text-xs text-gray-500 mt-1.5 mb-4">Kamu akan menghapus <strong id="deleteName"></strong>.</p>
        <form id="deleteForm" action="" method="POST" class="flex items-center gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 text-sm text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">Batal</button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5"><i class="fa fa-trash text-xs"></i> Hapus</button>
        </form>
    </div>
</div>
@if(session('success')||$errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6" style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4" style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
        <div class="flex-1"><p class="text-sm font-bold">Berhasil!</p><p class="text-xs text-gray-500">{{ session('success') }}</p></div>
        @else
        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
        <div class="flex-1"><p class="text-sm font-bold">Error!</p><ul class="text-xs text-gray-500 list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif
<script>
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer'),createUrl="{{ route('afiliasi.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah Program Afiliasi';mainForm.action=createUrl;methodContainer.innerHTML='';mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(btn){document.getElementById('modalTitle').innerText='Edit Program';mainForm.action=btn.dataset.action;methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
['id_program','nama_program','kode_referral','diskon_referral','bonus_pengajak','batas_waktu','status'].forEach(k=>{const el=document.getElementById('f_'+k);if(el)el.value=btn.dataset[k]??'';});
mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
const deleteModal=document.getElementById('deleteModal'),deleteForm=document.getElementById('deleteForm');
function triggerDelete(btn){deleteForm.action=btn.dataset.action;document.getElementById('deleteName').innerText=btn.dataset.name;deleteModal.classList.remove('hidden');deleteModal.classList.add('flex');}
function closeDeleteModal(){deleteModal.classList.add('hidden');deleteModal.classList.remove('flex');}
deleteModal.addEventListener('click',e=>{if(e.target===deleteModal)closeDeleteModal();});
const allRows=Array.from(document.querySelectorAll('#tableBody tr[data-search]')),entriesInfo=document.getElementById('entriesInfo');
let currentSearch='',currentPerPage=10;
function onSearchInput(v){currentSearch=v.toLowerCase();renderTable();}
function onPerPageChange(v){currentPerPage=v==='all'?Infinity:parseInt(v);renderTable();}
function renderTable(){if(!allRows.length)return;const matched=allRows.filter(r=>r.dataset.search.includes(currentSearch));let shown=0;allRows.forEach(r=>r.style.display='none');matched.forEach(r=>{if(shown<currentPerPage){r.style.display='';shown++;}});entriesInfo.innerText=matched.length===0?'Tidak ada data':`Menampilkan ${shown} dari ${matched.length} entri`;}
document.addEventListener('DOMContentLoaded',renderTable);
(function(){var o=document.getElementById('alertOverlay'),b=document.getElementById('alertBox');if(!o)return;setTimeout(()=>{o.style.opacity='1';o.style.pointerEvents='auto';b.style.transform='translateY(0)';},80);var t=setTimeout(closeAlert,4500);function closeAlert(){clearTimeout(t);o.style.opacity='0';o.style.pointerEvents='none';b.style.transform='translateY(-16px)';}window.closeAlert=closeAlert;})();
</script>
@endsection
