@extends('admin.layouts.app')
@section('title', 'Litigasi')
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Litigasi</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data perkara litigasi perusahaan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('litigasi.pdf') }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Litigasi
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-scale-balanced"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Kasus</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0">
                <i class="fa fa-spinner"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalProses }}</p>
                <p class="text-xs text-gray-500 mt-1">Proses</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalSelesai }}</p>
                <p class="text-xs text-gray-500 mt-1">Selesai</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 text-lg flex-shrink-0">
                <i class="fa fa-handshake"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalMediasi }}</p>
                <p class="text-xs text-gray-500 mt-1">Mediasi</p>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 text-base">Daftar Perkara Litigasi</h2>
            <form method="GET" class="flex gap-1"><input type="text" placeholder="Cari kasus..." name="search" value="{{ request('search') }}"
                class="pl-3 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 w-44">
                <button class="bg-gray-800 text-white text-xs px-3 py-1.5 rounded-lg ml-1">Cari</button></form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="mainTable">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Kasus</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Lawan</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Jenis Kasus</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Pengacara</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tgl Sidang</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data as $i => $d)
                    <tr class="odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $i+1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $d->kasus }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->lawan }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->jenis_kasus }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->pengacara }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $d->tanggal_sidang ? \Carbon\Carbon::parse($d->tanggal_sidang)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($d->status === 'Proses')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Proses</span>
                            @elseif($d->status === 'Mediasi')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Mediasi</span>
                            @elseif($d->status === 'Selesai')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Selesai</span>
                            @elseif($d->status === 'Menunggu')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Menunggu</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $d->status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="openEdit({{ $d->id }})"
                                    class="inline-flex items-center gap-1 text-xs bg-yellow-50 hover:bg-yellow-100 text-yellow-700 px-2.5 py-1.5 rounded-lg transition-colors">
                                    <i class="fa fa-pen text-xs"></i> Edit
                                </button>
                                <form action="{{ route('litigasi.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2.5 py-1.5 rounded-lg transition-colors">
                                        <i class="fa fa-trash text-xs"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada data litigasi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modalCreate" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Tambah Litigasi</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('litigasi.store') }}" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Kasus <span class="text-red-500">*</span></label>
                <input type="text" name="kasus" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Lawan <span class="text-red-500">*</span></label>
                <input type="text" name="lawan" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Kasus <span class="text-red-500">*</span></label>
                <input type="text" name="jenis_kasus" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Pengacara</label>
                <input type="text" name="pengacara"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Sidang</label>
                    <input type="date" name="tanggal_sidang"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">-- Pilih Status --</option>
                        <option value="Proses">Proses</option>
                        <option value="Mediasi">Mediasi</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Menunggu">Menunggu</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-xl hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Edit Litigasi</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-4 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Kasus</label>
                <input type="text" name="kasus" id="edit_kasus" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Lawan</label>
                <input type="text" name="lawan" id="edit_lawan" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Kasus</label>
                <input type="text" name="jenis_kasus" id="edit_jenis_kasus" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Pengacara</label>
                <input type="text" name="pengacara" id="edit_pengacara"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Sidang</label>
                    <input type="date" name="tanggal_sidang" id="edit_tanggal_sidang"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="edit_status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Proses">Proses</option>
                        <option value="Mediasi">Mediasi</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Menunggu">Menunggu</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm bg-yellow-500 text-white rounded-xl hover:bg-yellow-600">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection
<div class="px-5 py-3 border-t border-gray-100">{{ $data->links() }}</div>
@push('scripts')
<script>
function openModal() { document.getElementById('modalCreate').classList.remove('hidden'); }
function closeModal() { document.getElementById('modalCreate').classList.add('hidden'); }
function closeEditModal() { document.getElementById('modalEdit').classList.add('hidden'); }

function openEdit(id) {
    fetch(`/admin/litigasi/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `{{ url('admin/litigasi') }}/${d.id}`;
            document.getElementById('edit_kasus').value = d.kasus ?? '';
            document.getElementById('edit_lawan').value = d.lawan ?? '';
            document.getElementById('edit_jenis_kasus').value = d.jenis_kasus ?? '';
            document.getElementById('edit_pengacara').value = d.pengacara ?? '';
            document.getElementById('edit_tanggal_sidang').value = d.tanggal_sidang ?? '';
            document.getElementById('edit_status').value = d.status ?? '';
            document.getElementById('modalEdit').classList.remove('hidden');
        });
}

);
}
</script>
@endpush
