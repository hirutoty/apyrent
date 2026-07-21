@extends('admin.layouts.app')
@section('title', 'Perolehan Asset')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Perolehan Asset</h1>
            <p class="text-sm text-gray-500 mt-0.5">Catat pengadaan dan pembelian aset baru</p>
        </div>
        <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="bi bi-plus-lg"></i> Tambah Perolehan
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="bi bi-bag-check-fill"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p><p class="text-xs text-gray-500 mt-1">Total Perolehan</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="bi bi-check-circle-fill"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalLunas }}</p><p class="text-xs text-gray-500 mt-1">Lunas</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0"><i class="bi bi-clock-history"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalCicilan }}</p><p class="text-xs text-gray-500 mt-1">Cicilan</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0"><i class="bi bi-cash-coin"></i></div>
            <div><p class="text-sm font-bold text-gray-800 leading-none">Rp {{ number_format($totalHarga,0,',','.') }}</p><p class="text-xs text-gray-500 mt-1">Total Harga</p></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Perolehan</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total record</p></div>
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
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Tgl Perolehan</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kode Aset</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Nama Aset</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Vendor</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Metode Beli</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Harga</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Pembayaran</th>
                        <th class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors"
                        data-search="{{ strtolower($d->kode_aset.' '.$d->nama_aset.' '.$d->vendor.' '.$d->status.' '.$d->pembayaran) }}">
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $data->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal_perolehan ? \Carbon\Carbon::parse($d->tanggal_perolehan)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3.5"><span class="font-mono text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded">{{ $d->kode_aset }}</span></td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800 text-sm">{{ $d->nama_aset }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->vendor }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-600">{{ $d->metode_pembelian }}</td>
                        <td class="px-4 py-3.5 text-sm font-medium text-gray-700">Rp {{ number_format($d->harga,0,',','.') }}</td>
                        <td class="px-4 py-3.5">
                            @if($d->status === 'Aktif')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">Aktif</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">{{ $d->status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($d->pembayaran === 'Lunas')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">Lunas</span>
                            @elseif($d->pembayaran === 'Cicilan')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600">Cicilan</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">{{ $d->pembayaran }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                    data-action="{{ route('asset.perolehan.update', $d->id) }}"
                                    data-tanggal_perolehan="{{ $d->tanggal_perolehan }}" data-kode_aset="{{ $d->kode_aset }}"
                                    data-nama_aset="{{ $d->nama_aset }}" data-vendor="{{ $d->vendor }}"
                                    data-metode_pembelian="{{ $d->metode_pembelian }}" data-harga="{{ $d->harga }}"
                                    data-status="{{ $d->status }}" data-pembayaran="{{ $d->pembayaran }}"
                                    onclick="triggerEdit(this)"><i class="bi bi-pencil text-xs"></i> Edit</button>
                                <button type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                    data-action="{{ route('asset.perolehan.destroy', $d->id) }}" data-name="{{ $d->kode_aset }}"
                                    onclick="triggerDelete(this)"><i class="bi bi-trash text-xs"></i> Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3"><div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i class="bi bi-bag-check-fill text-2xl text-gray-300"></i></div>
                        <p class="text-sm font-medium text-gray-500">Belum ada data perolehan</p></div>
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
            <h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah Perolehan</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="mainForm" action="{{ route('asset.perolehan.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Perolehan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_perolehan" id="f_tanggal_perolehan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('tanggal_perolehan') }}"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_aset" id="f_kode_aset" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('kode_aset') }}"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_aset" id="f_nama_aset" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('nama_aset') }}"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Vendor <span class="text-red-500">*</span></label>
                    <input type="text" name="vendor" id="f_vendor" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('vendor') }}"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Metode Pembelian <span class="text-red-500">*</span></label>
                    <select name="metode_pembelian" id="f_metode_pembelian" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih -</option><option value="Pembelian Langsung" {{ old('metode_pembelian') == 'Pembelian Langsung' ? 'selected' : '' }}>Pembelian Langsung</option>
                        <option value="Leasing" {{ old('metode_pembelian') == 'Leasing' ? 'selected' : '' }}>Leasing</option><option value="Hibah" {{ old('metode_pembelian') == 'Hibah' ? 'selected' : '' }}>Hibah</option><option value="Tender" {{ old('metode_pembelian') == 'Tender' ? 'selected' : '' }}>Tender</option>
                    </select></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" min="0" step="0.01" name="harga" id="f_harga" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('harga') }}"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="f_status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option><option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Pembayaran <span class="text-red-500">*</span></label>
                    <select name="pembayaran" id="f_pembayaran" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih -</option><option value="Lunas" {{ old('pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas</option><option value="Cicilan" {{ old('pembayaran') == 'Cicilan' ? 'selected' : '' }}>Cicilan</option>
                    </select></div>
            </div>
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
            <p class="text-xs text-gray-500 mt-1.5">Hapus data perolehan aset <strong id="deleteName" class="text-gray-700"></strong>?</p>
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
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer'),createUrl="{{ route('asset.perolehan.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah Perolehan';mainForm.action=createUrl;methodContainer.innerHTML='';mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(b){document.getElementById('modalTitle').innerText='Edit Perolehan';mainForm.action=b.dataset.action;methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
['tanggal_perolehan','kode_aset','nama_aset','vendor','metode_pembelian','harga','status','pembayaran'].forEach(k=>{let el=document.getElementById('f_'+k);if(el)el.value=b.dataset[k]??'';});
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
