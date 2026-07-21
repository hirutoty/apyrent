@extends('admin.layouts.app')
@section('title', 'Project Risk')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div><h1 class="text-2xl font-bold text-gray-800">Project Risk</h1><p class="text-sm text-gray-500 mt-0.5">Kelola risiko dan mitigasi proyek</p></div>
        <div class="flex items-center gap-2">
            <a href="{{ route('project.risk.pdf') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors"><i class="fa fa-file-pdf text-sm"></i> Export PDF</a>
            <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors"><i class="fa fa-plus text-sm"></i> Tambah Risiko</button>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0"><i class="fa fa-triangle-exclamation"></i></div><div><p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p><p class="text-xs text-gray-500 mt-1">Total Risiko</p></div></div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0"><i class="fa fa-shield-halved"></i></div><div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalTerkendali }}</p><p class="text-xs text-gray-500 mt-1">Terkendali</p></div></div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0"><i class="fa fa-hourglass-half"></i></div><div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDiajukan }}</p><p class="text-xs text-gray-500 mt-1">Diajukan</p></div></div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-lg flex-shrink-0"><i class="fa fa-fire"></i></div><div><p class="text-xl font-bold text-gray-800 leading-none">{{ $totalKritis }}</p><p class="text-xs text-gray-500 mt-1">Kritis</p></div></div>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2"><i class="fa fa-circle-check"></i> {{ session('success') }}</div>@endif
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div><h2 class="font-semibold text-gray-800 text-base">Daftar Project Risk</h2><p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p></div>
            <div class="relative"><i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i><input type="text" placeholder="Cari..." oninput="onSearchInput(this.value)" class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44"></div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Proyek</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Risiko</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Dampak</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Kemungkinan</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Mitigasi</th>
                    <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                    <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                </tr></thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $1->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-blue-600 font-semibold">{{ $d->proyek }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800 text-xs">{{ $d->risiko }}</td>
                        <td class="px-4 py-3.5 text-xs">
                            @php $dc=match($d->dampak){'Tinggi'=>'text-red-600','Sedang'=>'text-yellow-600',default=>'text-gray-600'}; @endphp
                            <span class="{{ $dc }} font-medium">{{ $d->dampak }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-700">{{ $d->kemungkinan }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-600 max-w-xs">{{ $d->mitigasi }}</td>
                        <td class="px-4 py-3.5">
                            @php $sc=match($d->status){'Terkendali'=>'bg-green-100 text-green-700','Diajukan'=>'bg-yellow-100 text-yellow-700','Kritis'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-600'}; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}"><i class="fa fa-circle text-[6px]"></i> {{ $d->status }}</span>
                        </td>
                        <td class="px-4 py-3.5"><div class="flex items-center justify-center gap-1.5">
                            <button onclick="openEditModal({{ $d->id }})" class="w-7 h-7 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 flex items-center justify-center transition-colors"><i class="fa fa-pen text-xs"></i></button>
                            <form action="{{ route('project.risk.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button type="submit" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors"><i class="fa fa-trash text-xs"></i></button></form>
                        </div></td>
                    </tr>
                    @empty<tr><td colspan="8" class="px-4 py-10 text-center text-gray-400 text-sm"><i class="fa fa-inbox text-2xl mb-2 block"></i> Belum ada data risk</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modalAdd" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Tambah Project Risk</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button></div>
        <form action="{{ route('project.risk.store') }}" method="POST" class="px-6 py-5 space-y-4">@csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Proyek <span class="text-red-500">*</span></label>
                    <select name="proyek" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"><option value="">-- Pilih --</option>@foreach($proyeks as $kode => $nama)<option value="{{ $kode }}" {{ old('proyek') == $kode ? 'selected' : '' }}>{{ $kode }} - {{ $nama }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Risiko <span class="text-red-500">*</span></label><input type="text" name="risiko" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('risiko') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Dampak <span class="text-red-500">*</span></label>
                    <select name="dampak" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"><option value="">-- Pilih --</option><option value="Rendah" {{ old('dampak') == 'Rendah' ? 'selected' : '' }}>Rendah</option><option value="Sedang" {{ old('dampak') == 'Sedang' ? 'selected' : '' }}>Sedang</option><option value="Tinggi" {{ old('dampak') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option></select></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Kemungkinan <span class="text-red-500">*</span></label>
                    <select name="kemungkinan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"><option value="">-- Pilih --</option><option value="Rendah" {{ old('kemungkinan') == 'Rendah' ? 'selected' : '' }}>Rendah</option><option value="Menengah" {{ old('kemungkinan') == 'Menengah' ? 'selected' : '' }}>Menengah</option><option value="Tinggi" {{ old('kemungkinan') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option></select></div>
                <div class="col-span-2"><label class="block text-xs font-medium text-gray-700 mb-1">Mitigasi</label><textarea name="mitigasi" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">{{ old('mitigasi') }}</textarea></div>
                <div class="col-span-2"><label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"><option value="">-- Pilih --</option><option value="Diajukan" {{ old('status') == 'Diajukan' ? 'selected' : '' }}>Diajukan</option><option value="Terkendali" {{ old('status') == 'Terkendali' ? 'selected' : '' }}>Terkendali</option><option value="Kritis" {{ old('status') == 'Kritis' ? 'selected' : '' }}>Kritis</option></select></div>
            </div>
            <div class="flex justify-end gap-2 pt-2"><button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button><button type="submit" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Simpan</button></div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Edit Project Risk</h3><button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button></div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-4">@csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Proyek <span class="text-red-500">*</span></label>
                    <select name="proyek" id="edit_proyek" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">@foreach($proyeks as $kode => $nama)<option value="{{ $kode }}" {{ old('proyek') == $kode ? 'selected' : '' }}>{{ $kode }} - {{ $nama }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Risiko <span class="text-red-500">*</span></label><input type="text" name="risiko" id="edit_risiko" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" value="{{ old('risiko') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Dampak <span class="text-red-500">*</span></label>
                    <select name="dampak" id="edit_dampak" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"><option value="Rendah" {{ old('dampak') == 'Rendah' ? 'selected' : '' }}>Rendah</option><option value="Sedang" {{ old('dampak') == 'Sedang' ? 'selected' : '' }}>Sedang</option><option value="Tinggi" {{ old('dampak') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option></select></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Kemungkinan <span class="text-red-500">*</span></label>
                    <select name="kemungkinan" id="edit_kemungkinan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"><option value="Rendah" {{ old('kemungkinan') == 'Rendah' ? 'selected' : '' }}>Rendah</option><option value="Menengah" {{ old('kemungkinan') == 'Menengah' ? 'selected' : '' }}>Menengah</option><option value="Tinggi" {{ old('kemungkinan') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option></select></div>
                <div class="col-span-2"><label class="block text-xs font-medium text-gray-700 mb-1">Mitigasi</label><textarea name="mitigasi" id="edit_mitigasi" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">{{ old('mitigasi') }}</textarea></div>
                <div class="col-span-2"><label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="edit_status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"><option value="Diajukan" {{ old('status') == 'Diajukan' ? 'selected' : '' }}>Diajukan</option><option value="Terkendali" {{ old('status') == 'Terkendali' ? 'selected' : '' }}>Terkendali</option><option value="Kritis" {{ old('status') == 'Kritis' ? 'selected' : '' }}>Kritis</option></select></div>
            </div>
            <div class="flex justify-end gap-2 pt-2"><button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button><button type="submit" class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">Update</button></div>
        </form>
    </div>
</div>
<script>
function openModal(){document.getElementById('modalAdd').classList.replace('hidden','flex');}
function closeModal(){document.getElementById('modalAdd').classList.replace('flex','hidden');}
function openEditModal(id){
    fetch(`/admin/project/risk/${id}`).then(r=>r.json()).then(d=>{
        document.getElementById('editForm').action=`/admin/project/risk/${id}`;
        document.getElementById('edit_proyek').value=d.proyek;
        document.getElementById('edit_risiko').value=d.risiko;
        document.getElementById('edit_dampak').value=d.dampak;
        document.getElementById('edit_kemungkinan').value=d.kemungkinan;
        document.getElementById('edit_mitigasi').value=d.mitigasi??'';
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
