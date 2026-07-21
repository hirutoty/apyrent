@extends('admin.layouts.app')
@section('title', 'Pembelian Proyek')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div><h1 class="text-2xl font-bold text-gray-800">Pembelian Proyek</h1><p class="text-sm text-gray-500 mt-0.5">Kelola purchase request pengadaan proyek</p></div>
        <div class="flex items-center gap-2">
            <a href="{{ route('project.pembelian.pdf') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors"><i class="fa fa-file-pdf text-sm"></i> Export PDF</a>
            <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors"><i class="fa fa-plus text-sm"></i> Tambah PR</button>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-cart-shopping"></i></div><div><p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p><p class="text-xs text-gray-500 mt-1">Total PR</p></div></div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="fa fa-circle-check"></i></div><div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDisetujui }}</p><p class="text-xs text-gray-500 mt-1">Disetujui</p></div></div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0"><i class="fa fa-clock"></i></div><div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalPending }}</p><p class="text-xs text-gray-500 mt-1">Pending</p></div></div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-lg flex-shrink-0"><i class="fa fa-ban"></i></div><div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDitolak }}</p><p class="text-xs text-gray-500 mt-1">Ditolak</p></div></div>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2"><i class="fa fa-circle-check"></i> {{ session('success') }}</div>@endif
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Pembelian Proyek</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p></div>
            <div class="relative"><i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i><input type="text" placeholder="Cari..." oninput="onSearchInput(this.value)" class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44"></div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No PR</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Proyek</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Item Diminta</th>
                    <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Qty</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Vendor</th>
                    <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Estimasi Harga</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tgl Permintaan</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                    <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                </tr></thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $1->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-blue-600 font-semibold">{{ $d->pr_no }}</td>
                        <td class="px-4 py-3.5 text-xs text-blue-600">{{ $d->proyek }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800 text-xs">{{ $d->item_diminta }}</td>
                        <td class="px-4 py-3.5 text-center text-xs text-gray-700">{{ $d->qty }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-600">{{ $d->vendor }}</td>
                        <td class="px-4 py-3.5 text-xs text-right text-gray-800 font-medium">Rp {{ number_format($d->estimasi_harga,0,',','.') }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">{{ \Carbon\Carbon::parse($d->tgl_permintaan)->format('d M Y') }}</td>
                        <td class="px-4 py-3.5">
                            @php $sc=match($d->status){'Disetujui'=>'bg-green-100 text-green-700','Pending'=>'bg-yellow-100 text-yellow-700','Ditolak'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-600'}; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}"><i class="fa fa-circle text-[6px]"></i> {{ $d->status }}</span>
                        </td>
                        <td class="px-4 py-3.5"><div class="flex items-center justify-center gap-1.5">
                            <button onclick="openEditModal({{ $d->id }})" class="w-7 h-7 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 flex items-center justify-center transition-colors"><i class="fa fa-pen text-xs"></i></button>
                            <form action="{{ route('project.pembelian.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button type="submit" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors"><i class="fa fa-trash text-xs"></i></button></form>
                        </div></td>
                    </tr>
                    @empty<tr><td colspan="10" class="px-4 py-10 text-center text-gray-400 text-sm"><i class="fa fa-inbox text-2xl mb-2 block"></i> Belum ada data pembelian proyek</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modalAdd" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Tambah Purchase Request</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button></div>
        <form action="{{ route('project.pembelian.store') }}" method="POST" class="px-6 py-5 space-y-4">@csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">No PR <span class="text-red-500">*</span></label><input type="text" name="pr_no" required placeholder="PR-PRJ001-001" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('pr_no') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Proyek <span class="text-red-500">*</span></label>
                    <select name="proyek" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"><option value="">-- Pilih --</option>@foreach($proyeks as $kode => $nama)<option value="{{ $kode }}" {{ old('proyek') == $kode ? 'selected' : '' }}>{{ $kode }} - {{ $nama }}</option>@endforeach</select></div>
                <div class="col-span-2"><label class="block text-xs font-medium text-gray-700 mb-1">Item Diminta <span class="text-red-500">*</span></label><input type="text" name="item_diminta" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('item_diminta') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Qty <span class="text-red-500">*</span></label><input type="number" name="qty" required min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('qty') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Vendor</label><input type="text" name="vendor" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('vendor') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Estimasi Harga (Rp) <span class="text-red-500">*</span></label><input type="number" name="estimasi_harga" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('estimasi_harga') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Tgl Permintaan <span class="text-red-500">*</span></label><input type="date" name="tgl_permintaan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('tgl_permintaan') }}"></div>
                <div class="col-span-2"><label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"><option value="">-- Pilih --</option><option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option><option value="Disetujui" {{ old('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option><option value="Ditolak" {{ old('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option></select></div>
            </div>
            <div class="flex justify-end gap-2 pt-2"><button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button><button type="submit" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Simpan</button></div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Edit Pembelian Proyek</h3><button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button></div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-4">@csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">No PR <span class="text-red-500">*</span></label><input type="text" name="pr_no" id="edit_pr_no" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('pr_no') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Proyek <span class="text-red-500">*</span></label>
                    <select name="proyek" id="edit_proyek" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">@foreach($proyeks as $kode => $nama)<option value="{{ $kode }}" {{ old('proyek') == $kode ? 'selected' : '' }}>{{ $kode }} - {{ $nama }}</option>@endforeach</select></div>
                <div class="col-span-2"><label class="block text-xs font-medium text-gray-700 mb-1">Item Diminta <span class="text-red-500">*</span></label><input type="text" name="item_diminta" id="edit_item_diminta" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('item_diminta') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Qty</label><input type="number" name="qty" id="edit_qty" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('qty') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Vendor</label><input type="text" name="vendor" id="edit_vendor" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('vendor') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Estimasi Harga (Rp)</label><input type="number" name="estimasi_harga" id="edit_estimasi_harga" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('estimasi_harga') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Tgl Permintaan</label><input type="date" name="tgl_permintaan" id="edit_tgl_permintaan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('tgl_permintaan') }}"></div>
                <div class="col-span-2"><label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="edit_status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"><option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option><option value="Disetujui" {{ old('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option><option value="Ditolak" {{ old('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option></select></div>
            </div>
            <div class="flex justify-end gap-2 pt-2"><button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button><button type="submit" class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">Update</button></div>
        </form>
    </div>
</div>
<script>
function openModal(){document.getElementById('modalAdd').classList.replace('hidden','flex');}
function closeModal(){document.getElementById('modalAdd').classList.replace('flex','hidden');}
function openEditModal(id){
    fetch(`/admin/project/pembelian/${id}`).then(r=>r.json()).then(d=>{
        document.getElementById('editForm').action=`/admin/project/pembelian/${id}`;
        document.getElementById('edit_pr_no').value=d.pr_no;
        document.getElementById('edit_proyek').value=d.proyek;
        document.getElementById('edit_item_diminta').value=d.item_diminta;
        document.getElementById('edit_qty').value=d.qty;
        document.getElementById('edit_vendor').value=d.vendor??'';
        document.getElementById('edit_estimasi_harga').value=d.estimasi_harga;
        document.getElementById('edit_tgl_permintaan').value=d.tgl_permintaan;
        document.getElementById('edit_status').value=d.status;
        document.getElementById('modalEdit').classList.replace('hidden','flex');
    });
}
function closeEditModal(){document.getElementById('modalEdit').classList.replace('flex','hidden');}
let perPage=10;

        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof openModalTambah === 'function') openModalTambah();
            else if (typeof openModal === 'function') openModal();
        });
        @endif
</script>
@endsection
