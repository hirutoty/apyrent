@extends('admin.layouts.app')
@section('title', 'Purchase Order')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Purchase Order (PO)</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola purchase order ke vendor</p>
        </div>
        <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="fa fa-plus text-sm"></i> Tambah PO
        </button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-file-alt"></i></div>
            <div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPO }}</p><p class="text-xs text-gray-500 mt-1">Total PO</p></div>
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
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>Closed</span>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Purchase Order</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total PO</p></div>
            <div class="flex items-center gap-2">
                <div class="relative"><i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Cari PO..." oninput="onSearchInput(this.value)" class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
                </div>
                <button onclick="window.location.reload()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50"><i class="fa fa-sync text-xs"></i> Refresh</button>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            <div class="flex items-center gap-2"><span>Show</span>
                <select id="perPageSelect" onchange="renderTable()" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
                    <option value="5">5</option><option value="10" selected>10</option><option value="25">25</option><option value="50">50</option><option value="all">All</option>
                </select><span>entries</span>
            </div>
            <div class="w-px h-4 bg-gray-200"></div>
            <span class="text-gray-400 font-medium">Tanggal PO:</span>
            <select id="filterBulan" onchange="renderTable()" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
                <option value="">Semua Bulan</option>
                <option value="01">Januari</option><option value="02">Februari</option><option value="03">Maret</option><option value="04">April</option><option value="05">Mei</option><option value="06">Juni</option>
                <option value="07">Juli</option><option value="08">Agustus</option><option value="09">September</option><option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
            </select>
            <select id="filterTahun" onchange="renderTable()" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
                <option value="">Semua Tahun</option>
                @foreach($data->map(fn($d)=>$d->tanggal_po?\Carbon\Carbon::parse($d->tanggal_po)->year:null)->filter()->unique()->sortDesc() as $yr)
                    <option value="{{ $yr }}">{{ $yr }}</option>
                @endforeach
            </select>
            <button onclick="resetFilter()" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50"><i class="fa fa-rotate-left text-[10px]"></i> Reset</button>
            <div class="ml-auto text-xs text-gray-400" id="entriesInfoTop"></div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">PO ID</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tgl PO</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Vendor</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">RFQ Terkait</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Total Barang</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Total Harga</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tgl Kirim</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tgl Terima</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Catatan</th>
                    <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                </tr></thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors"
                        data-search="{{ strtolower($d->po_id.' '.$d->vendor.' '.($d->terkait_rfq??'')) }}"
                        data-tanggal="{{ $d->tanggal_po?\Carbon\Carbon::parse($d->tanggal_po)->format('Y-m-d'):'' }}">
                        <td class="px-4 py-3.5 text-gray-400">{{ $data->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5"><span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $d->po_id }}</span></td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal_po?\Carbon\Carbon::parse($d->tanggal_po)->format('Y-m-d'):'-' }}</td>
                        <td class="px-4 py-3.5 text-sm font-semibold text-gray-800">{{ $d->vendor??'-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->terkait_rfq??'-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">{{ number_format($d->total_barang??0,0,',','.') }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-700">Rp {{ number_format($d->total_harga??0,0,',','.') }}</td>
                        <td class="px-4 py-3.5">
                            @if($d->status_po==='Approved') <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600"><i class="fa fa-circle text-[6px]"></i> Approved</span>
                            @elseif($d->status_po==='Pending') <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600"><i class="fa fa-circle text-[6px]"></i> Pending</span>
                            @elseif($d->status_po==='Closed') <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600"><i class="fa fa-circle text-[6px]"></i> Closed</span>
                            @else <span class="text-xs text-gray-400">-</span> @endif
                        </td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal_kirim?\Carbon\Carbon::parse($d->tanggal_kirim)->format('Y-m-d'):'-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-500">{{ $d->tanggal_terima?\Carbon\Carbon::parse($d->tanggal_terima)->format('Y-m-d'):'-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-gray-500 max-w-[140px] truncate">{{ $d->catatan??'-' }}</td>
                        <td class="px-4 py-3.5"><div class="flex items-center justify-center gap-1.5">
                            <button class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                data-action="{{ route('purchase-order.update',$d->id) }}"
                                data-po_id="{{ $d->po_id }}" data-tanggal_po="{{ $d->tanggal_po }}"
                                data-vendor="{{ $d->vendor }}" data-terkait_rfq="{{ $d->terkait_rfq }}"
                                data-total_barang="{{ $d->total_barang }}" data-total_harga="{{ $d->total_harga }}"
                                data-status_po="{{ $d->status_po }}" data-tanggal_kirim="{{ $d->tanggal_kirim }}"
                                data-tanggal_terima="{{ $d->tanggal_terima }}" data-catatan="{{ $d->catatan }}"
                                onclick="triggerEdit(this)"><i class="fa fa-edit text-xs"></i> Edit</button>
                            <button type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                data-action="{{ route('purchase-order.destroy',$d->id) }}" data-name="{{ $d->po_id }}"
                                onclick="triggerDelete(this)"><i class="fa fa-trash text-xs"></i> Hapus</button>
                        </div></td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i class="fa fa-file-alt text-2xl text-gray-300"></i></div>
                            <p class="text-sm font-medium text-gray-500">Belum ada data Purchase Order</p>
                            <p class="text-xs text-gray-400">Klik "Tambah PO" untuk menambahkan data baru</p>
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
            <div><h2 id="modalTitle" class="text-base font-bold text-gray-800">Tambah PO</h2><p id="modalSubtitle" class="text-xs text-gray-500 mt-0.5">PO ID akan dibuat otomatis</p></div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-lg leading-none mt-0.5"><i class="fa fa-times"></i></button>
        </div>
        <form id="mainForm" action="{{ route('purchase-order.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div id="methodContainer"></div>
            <div id="idBox" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">PO ID</label>
                <span id="f_id_display" class="font-mono text-xs text-gray-600 bg-gray-100 px-3 py-2 rounded-lg border border-gray-200 inline-block"></span>
                <span class="text-xs text-gray-400 ml-2">(otomatis)</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal PO <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_po" id="f_tanggal_po" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('tanggal_po') }}"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Vendor <span class="text-red-500">*</span></label>
                    <input type="text" name="vendor" id="f_vendor" required placeholder="Nama vendor" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('vendor') }}"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">RFQ Terkait</label>
                    <input type="text" name="terkait_rfq" id="f_terkait_rfq" placeholder="RFQ-001 (opsional)" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('terkait_rfq') }}"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status_po" id="f_status_po" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">- Pilih Status -</option>
                        <option value="Pending" {{ old('status_po') == 'Pending' ? 'selected' : '' }}>Pending</option><option value="Approved" {{ old('status_po') == 'Approved' ? 'selected' : '' }}>Approved</option><option value="Closed" {{ old('status_po') == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Total Barang <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="total_barang" id="f_total_barang" required placeholder="10" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('total_barang') }}"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Total Harga <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="total_harga" id="f_total_harga" required placeholder="5000000" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('total_harga') }}"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Kirim</label>
                    <input type="date" name="tanggal_kirim" id="f_tanggal_kirim" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('tanggal_kirim') }}"></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Terima</label>
                    <input type="date" name="tanggal_terima" id="f_tanggal_terima" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('tanggal_terima') }}"></div>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
                <textarea name="catatan" id="f_catatan" rows="3" placeholder="Catatan tambahan..." class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none">{{ old('catatan') }}</textarea></div>
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
            <h2 class="text-base font-bold text-gray-800 mt-4">Hapus Purchase Order?</h2>
            <p class="text-xs text-gray-500 mt-1.5">Kamu akan menghapus <strong id="deleteName" class="text-gray-700"></strong>. Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <form id="deleteForm" action="" method="POST" class="px-6 pb-6 pt-4 flex items-center gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl py-2.5 hover:bg-gray-50">Batal</button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl py-2.5"><i class="fa fa-trash text-xs"></i> Hapus</button>
        </form>
    </div>
</div>
@if(session('success')||session('error')||$errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6" style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4" style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                <ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4 space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg leading-none flex-shrink-0"><i class="fa fa-times"></i></button>
    </div>
</div>
@endif
<style>@keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const mainModal=document.getElementById('mainModal'),mainForm=document.getElementById('mainForm'),methodContainer=document.getElementById('methodContainer'),idBox=document.getElementById('idBox'),createUrl="{{ route('purchase-order.store') }}";
function openModal(){document.getElementById('modalTitle').innerText='Tambah PO';document.getElementById('modalSubtitle').innerText='PO ID akan dibuat otomatis';mainForm.action=createUrl;methodContainer.innerHTML='';idBox.classList.add('hidden');mainForm.reset();mainModal.classList.remove('hidden');mainModal.classList.add('flex');}
function closeModal(){mainModal.classList.add('hidden');mainModal.classList.remove('flex');}
mainModal.addEventListener('click',e=>{if(e.target===mainModal)closeModal();});
function triggerEdit(btn){
    document.getElementById('modalTitle').innerText='Edit PO';document.getElementById('modalSubtitle').innerText='Perbarui data Purchase Order';
    mainForm.action=btn.dataset.action;methodContainer.innerHTML='<input type="hidden" name="_method" value="PUT">';
    document.getElementById('f_id_display').innerText=btn.dataset.po_id;idBox.classList.remove('hidden');
    document.getElementById('f_tanggal_po').value=btn.dataset.tanggal_po??'';
    document.getElementById('f_vendor').value=btn.dataset.vendor??'';
    document.getElementById('f_terkait_rfq').value=btn.dataset.terkait_rfq??'';
    document.getElementById('f_total_barang').value=btn.dataset.total_barang??'';
    document.getElementById('f_total_harga').value=btn.dataset.total_harga??'';
    document.getElementById('f_status_po').value=btn.dataset.status_po??'';
    document.getElementById('f_tanggal_kirim').value=btn.dataset.tanggal_kirim??'';
    document.getElementById('f_tanggal_terima').value=btn.dataset.tanggal_terima??'';
    document.getElementById('f_catatan').value=btn.dataset.catatan??'';
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
    const perPage=document.getElementById('perPageSelect').value==='all'?Infinity:parseInt(document.getElementById('perPageSelect').value);
    const fB=document.getElementById('filterBulan').value,fT=document.getElementById('filterTahun').value;
    const matched=allRows.filter(r=>{const[y,m]=(r.dataset.tanggal||'').split('-');return r.dataset.search.includes(currentSearch)&&(!fB||m===fB)&&(!fT||y===fT);});
    let shown=0;allRows.forEach(r=>r.style.display='none');
    matched.forEach(r=>{if(shown<perPage){r.style.display='';shown++;}});
    const info=matched.length===0?'Tidak ada data':`Menampilkan ${shown} dari ${matched.length} entri`+(currentSearch||fB||fT?' (difilter)':'');
    document.getElementById('entriesInfo').innerText=info;document.getElementById('entriesInfoTop').innerText=info;
}
function resetFilter(){currentSearch='';document.querySelector('input[oninput="onSearchInput(this.value)"]').value='';document.getElementById('filterBulan').value='';document.getElementById('filterTahun').value='';document.getElementById('perPageSelect').value='10';renderTable();}
document.addEventListener('DOMContentLoaded',renderTable);
const statusLabels={!! json_encode($statusStats->keys()) !!},statusData={!! json_encode($statusStats->values()) !!},colorMap={Approved:'#22c55e',Pending:'#eab308',Closed:'#9ca3af'};
new Chart(document.getElementById('statusChart'),{type:'doughnut',data:{labels:statusLabels,datasets:[{data:statusData,backgroundColor:statusLabels.map(l=>colorMap[l]||'#9ca3af'),borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},cutout:'68%'}});
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
