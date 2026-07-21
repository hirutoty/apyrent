@extends('admin.layouts.app')
@section('title', 'Review Legal')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Review Legal</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola pengajuan review dokumen legal</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('review-legal.pdf') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Review
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg"><i class="fa fa-magnifying-glass"></i></div>
            <div><p class="text-xl font-bold text-gray-800">{{ $total }}</p><p class="text-xs text-gray-500 mt-1">Total Review</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg"><i class="fa fa-circle-check"></i></div>
            <div><p class="text-xl font-bold text-gray-800">{{ $totalSelesai }}</p><p class="text-xs text-gray-500 mt-1">Selesai</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg"><i class="fa fa-spinner"></i></div>
            <div><p class="text-xl font-bold text-gray-800">{{ $totalProses }}</p><p class="text-xs text-gray-500 mt-1">Proses</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 text-lg"><i class="fa fa-clock"></i></div>
            <div><p class="text-xl font-bold text-gray-800">{{ $totalPending }}</p><p class="text-xs text-gray-500 mt-1">Pending</p></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 text-base">Daftar Review Legal</h2>
            <form method="GET" class="flex gap-1"><input type="text" placeholder="Cari..." name="search" value="{{ request('search') }}" class="pl-3 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 w-44"><button class="bg-gray-800 text-white text-xs px-3 py-1.5 rounded-lg ml-1">Cari</button></form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="mainTable">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Pemohon</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Dokumen</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">PIC Legal</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Catatan</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data as $i => $d)
                    <tr class="odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $i+1 }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $d->pemohon }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $d->dokumen }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->pic_legal }}</td>
                        <td class="px-4 py-3">
                            @php $sc=match($d->status_review){'Selesai'=>'bg-green-100 text-green-700','Proses'=>'bg-yellow-100 text-yellow-700','Pending'=>'bg-gray-100 text-gray-500',default=>'bg-blue-100 text-blue-700'}; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $sc }}">{{ $d->status_review }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">{{ $d->catatan ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="openEdit({{ $d->id }})" class="inline-flex items-center gap-1 text-xs bg-yellow-50 hover:bg-yellow-100 text-yellow-700 px-2.5 py-1.5 rounded-lg">
                                    <i class="fa fa-pen text-xs"></i> Edit
                                </button>
                                <form action="{{ route('review-legal.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2.5 py-1.5 rounded-lg">
                                        <i class="fa fa-trash text-xs"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>
</div>

<div id="modalCreate" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Tambah Review Legal</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('review-legal.store') }}" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Tanggal *</label><input type="date" name="tanggal" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('tanggal') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Status Review *</label>
                    <select name="status_review" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Pending" {{ old('status_review') == 'Pending' ? 'selected' : '' }}>Pending</option><option value="Proses" {{ old('status_review') == 'Proses' ? 'selected' : '' }}>Proses</option><option value="Selesai" {{ old('status_review') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
            </div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Pemohon *</label><input type="text" name="pemohon" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('pemohon') }}"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Dokumen *</label><input type="text" name="dokumen" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('dokumen') }}"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">PIC Legal *</label><input type="text" name="pic_legal" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('pic_legal') }}"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label><textarea name="catatan" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('catatan') }}</textarea></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-xl hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Edit Review Legal</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-4 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Tanggal</label><input type="date" name="tanggal" id="edit_tanggal" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('tanggal') }}"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Status Review</label>
                    <select name="status_review" id="edit_status_review" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Pending" {{ old('status_review') == 'Pending' ? 'selected' : '' }}>Pending</option><option value="Proses" {{ old('status_review') == 'Proses' ? 'selected' : '' }}>Proses</option><option value="Selesai" {{ old('status_review') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
            </div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Pemohon</label><input type="text" name="pemohon" id="edit_pemohon" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('pemohon') }}"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Dokumen</label><input type="text" name="dokumen" id="edit_dokumen" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('dokumen') }}"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">PIC Legal</label><input type="text" name="pic_legal" id="edit_pic_legal" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" value="{{ old('pic_legal') }}"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label><textarea name="catatan" id="edit_catatan" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('catatan') }}</textarea></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-yellow-500 text-white rounded-xl hover:bg-yellow-600">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
<div class="py-3 border-t border-gray-100">{{ $data->links() }}</div>
@push('scripts')
<script>
function openModal(){ document.getElementById('modalCreate').classList.remove('hidden'); }
function closeModal(){ document.getElementById('modalCreate').classList.add('hidden'); }
function closeEditModal(){ document.getElementById('modalEdit').classList.add('hidden'); }
function openEdit(id){
    fetch(`/admin/review-legal/${id}`).then(r=>r.json()).then(d=>{
        document.getElementById('editForm').action=`/admin/review-legal/${d.id}`;
        document.getElementById('edit_tanggal').value=d.tanggal;
        document.getElementById('edit_pemohon').value=d.pemohon;
        document.getElementById('edit_dokumen').value=d.dokumen;
        document.getElementById('edit_pic_legal').value=d.pic_legal;
        document.getElementById('edit_status_review').value=d.status_review;
        document.getElementById('edit_catatan').value=d.catatan??'';
        document.getElementById('modalEdit').classList.remove('hidden');
    });
}


        // Auto-reopen modal tambah on validation error
        @if ($errors->any() && !session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof openModalTambah === 'function') openModalTambah();
            else if (typeof openModal === 'function') openModal();
        });
        @endif
</script>
@endpush
