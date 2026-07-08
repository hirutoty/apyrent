@extends('admin.layouts.app')
@section('title', 'Dokumen HRD')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dokumen HRD</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola arsip dan dokumen pegawai</p>
        </div>
        <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="fa fa-upload text-sm"></i> Upload Dokumen
        </button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-folder-open"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalFile }}</p><p class="text-xs text-gray-500 mt-1">Total Dokumen</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="fa fa-users"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPegawai }}</p><p class="text-xs text-gray-500 mt-1">Pegawai</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0"><i class="fa fa-tags"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalJenisDok }}</p><p class="text-xs text-gray-500 mt-1">Jenis Dokumen</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Dokumen</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total file</p></div>
            <div class="relative">
                <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Cari dokumen..." oninput="onSearchInput(this.value)"
                    class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
            </div>
        </div>
        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            <span>Show</span>
            <select onchange="onPerPageChange(this.value)" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
                <option value="5">5</option><option value="10" selected>10</option><option value="25">25</option><option value="all">All</option>
            </select>
            <span>entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama Pegawai</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama File</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Jenis Dokumen</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Keterangan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">File</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                        <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors"
                            data-search="{{ strtolower($d->nama_pegawai . ' ' . $d->nama_file . ' ' . $d->jenis_dokumen) }}">
                            <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3.5 font-semibold text-gray-800">{{ $d->nama_pegawai }}</td>
                            <td class="px-4 py-3.5 text-gray-700">{{ $d->nama_file }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-600">{{ $d->jenis_dokumen }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-500 text-xs max-w-[160px] truncate">{{ $d->keterangan ?? '-' }}</td>
                            <td class="px-4 py-3.5">
                                @if($d->file_path)
                                    <a href="{{ asset('storage/' . $d->file_path) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors">
                                        <i class="fa fa-eye text-xs"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                        data-action="{{ route('hrd-file.update', $d->id) }}"
                                        data-nama_pegawai="{{ $d->nama_pegawai }}" data-nama_file="{{ $d->nama_file }}"
                                        data-jenis_dokumen="{{ $d->jenis_dokumen }}" data-keterangan="{{ $d->keterangan }}"
                                        onclick="triggerEdit(this)">
                                        <i class="fa fa-edit text-xs"></i> Edit
                                    </button>
                                    <button type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                        data-action="{{ route('hrd-file.destroy', $d->id) }}" data-name="{{ $d->nama_file }}" onclick="triggerDelete(this)">
                                        <i class="fa fa-trash text-xs"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i class="fa fa-folder-open text-2xl text-gray-300"></i></div>
                                <p class="text-sm font-medium text-gray-500">Belum ada dokumen HRD</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400" id="entriesInfo"></div>
    </div>
</div>

{{-- MODAL UPLOAD / EDIT --}}
<div id="mainModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" style="animation:slideUp .2s ease">
        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white">
            <h2 id="modalTitle" class="text-base font-bold text-gray-800">Upload Dokumen</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg"><i class="fa fa-times"></i></button>
        </div>
        <form id="mainForm" action="{{ route('hrd-file.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pegawai <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pegawai" id="f_nama_pegawai" required placeholder="Nama lengkap"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama File <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_file" id="f_nama_file" required placeholder="Contoh: KTP Agus"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Dokumen <span class="text-red-500">*</span></label>
                <select name="jenis_dokumen" id="f_jenis_dokumen" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    <option value="">- Pilih Jenis -</option>
                    <option value="KTP">KTP</option>
                    <option value="Ijazah">Ijazah</option>
                    <option value="SK Pengangkatan">SK Pengangkatan</option>
                    <option value="Kontrak Kerja">Kontrak Kerja</option>
                    <option value="Sertifikat">Sertifikat</option>
                    <option value="NPWP">NPWP</option>
                    <option value="BPJS">BPJS</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div id="fileInputWrapper">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">File <span id="fileRequired" class="text-red-500">*</span></label>
                <input type="file" name="file" id="f_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                <p class="text-xs text-gray-400 mt-1">Format: PDF, JPG, PNG, DOC, DOCX. Maks 5MB.</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
                <textarea name="keterangan" id="f_keterangan" rows="2" placeholder="Keterangan (opsional)..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                <i class="fa fa-save text-sm"></i> Simpan
            </button>
        </form>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4" style="animation:slideUp .2s ease">
        <div class="px-6 pt-6 pb-2 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto text-red-500 text-2xl"><i class="fa fa-triangle-exclamation"></i></div>
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Dokumen?</h2>
            <p class="text-xs text-gray-500 mt-1.5">File <strong id="deleteName"></strong> akan dihapus permanen.</p>
        </div>
        <form id="deleteForm" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">Batal</button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5">
                <i class="fa fa-trash text-xs"></i> Hapus
            </button>
        </form>
    </div>
</div>

@if(session('success') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6" style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4" style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Error!</p><ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none mt-0.5"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif

<style>@keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}</style>
<script>
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer');
const fileInput=document.getElementById('f_file'),fileRequired=document.getElementById('fileRequired');
function openModal(){
    document.getElementById('modalTitle').innerText='Upload Dokumen';
    mainForm.action="{{route('hrd-file.store')}}";
    methodContainer.innerHTML='';
    mainForm.reset();
    mainForm.enctype='multipart/form-data';
    fileInput.required=true;
    fileRequired.style.display='';
    mainModal.classList.remove('hidden');mainModal.classList.add('flex');
}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(btn){
    document.getElementById('modalTitle').innerText='Edit Dokumen';
    mainForm.action=btn.dataset.action;
    methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
    fileInput.required=false;
    fileRequired.style.display='none';
    ['nama_pegawai','nama_file','jenis_dokumen','keterangan']
        .forEach(k=>{var el=document.getElementById('f_'+k);if(el)el.value=btn.dataset[k]??'';});
    mainModal.classList.remove('hidden');mainModal.classList.add('flex');
}
const deleteModal=document.getElementById('deleteModal'),deleteForm=document.getElementById('deleteForm');
function triggerDelete(btn){deleteForm.action=btn.dataset.action;document.getElementById('deleteName').innerText=btn.dataset.name;deleteModal.classList.remove('hidden');deleteModal.classList.add('flex');}
function closeDeleteModal(){deleteModal.classList.add('hidden');deleteModal.classList.remove('flex');}
deleteModal.addEventListener('click',e=>{if(e.target===deleteModal)closeDeleteModal();});
const allRows=Array.from(document.querySelectorAll('#tableBody tr[data-search]')),entriesInfo=document.getElementById('entriesInfo');
let cs='',cp=10;
function onSearchInput(v){cs=v.toLowerCase();renderTable();}
function onPerPageChange(v){cp=v==='all'?Infinity:parseInt(v);renderTable();}
function renderTable(){if(!allRows.length)return;const m=allRows.filter(r=>r.dataset.search.includes(cs));let s=0;allRows.forEach(r=>r.style.display='none');m.forEach(r=>{if(s<cp){r.style.display='';s++;}});entriesInfo.innerText=m.length===0?'Tidak ada data':`Menampilkan ${s} dari ${m.length} entri`+(cs?' (hasil pencarian)':'');}
document.addEventListener('DOMContentLoaded',renderTable);
(function(){var o=document.getElementById('alertOverlay'),b=document.getElementById('alertBox');if(!o)return;setTimeout(()=>{o.style.opacity='1';o.style.pointerEvents='auto';b.style.transform='translateY(0)';},80);var t=setTimeout(closeAlert,4500);o.addEventListener('click',e=>{if(e.target===o)closeAlert();});function closeAlert(){clearTimeout(t);o.style.opacity='0';o.style.pointerEvents='none';b.style.transform='translateY(-16px)';}window.closeAlert=closeAlert;})();
</script>
@endsection
