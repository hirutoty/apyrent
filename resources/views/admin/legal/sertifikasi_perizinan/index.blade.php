@extends('admin.layouts.app')
@section('title', 'Sertifikasi & Perizinan')
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Sertifikasi & Perizinan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola sertifikasi dan perizinan perusahaan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('sertifikasi-perizinan.pdf') }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Sertifikasi
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-certificate"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Sertifikasi</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalAktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Aktif</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-400 text-lg flex-shrink-0">
                <i class="fa fa-clock"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalExp }}</p>
                <p class="text-xs text-gray-500 mt-1">Kadaluarsa</p>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 text-base">Daftar Sertifikasi & Perizinan</h2>
            <form method="GET" class="flex gap-1"><input type="text" placeholder="Cari sertifikasi..." name="search" value="{{ request('search') }}"
                class="pl-3 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 w-48">
                <button class="bg-gray-800 text-white text-xs px-3 py-1.5 rounded-lg ml-1">Cari</button></form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="mainTable">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Jenis</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Nomor</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Instansi</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Berlaku Hingga</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data as $i => $d)
                    <tr class="odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $i+1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $d->jenis }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-blue-600">{{ $d->nomor }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->instansi }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->berlaku_hingga ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($d->status === 'Aktif')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                            @elseif($d->status === 'Kadaluarsa')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Kadaluarsa</span>
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
                                <form action="{{ route('sertifikasi-perizinan.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
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
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada data sertifikasi & perizinan</td></tr>
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
            <h3 class="font-semibold text-gray-800">Tambah Sertifikasi & Perizinan</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('sertifikasi-perizinan.store') }}" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Jenis <span class="text-red-500">*</span></label>
                <input type="text" name="jenis" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Contoh: SIUP, TDP, Sertifikat ISO...">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor <span class="text-red-500">*</span></label>
                <input type="text" name="nomor" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Nomor sertifikat / izin">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Instansi <span class="text-red-500">*</span></label>
                <input type="text" name="instansi" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Instansi penerbit">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Berlaku Hingga</label>
                <input type="text" name="berlaku_hingga"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Contoh: 31/12/2025 atau Tidak Kadaluarsa">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">-- Pilih Status --</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Kadaluarsa">Kadaluarsa</option>
                </select>
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
            <h3 class="font-semibold text-gray-800">Edit Sertifikasi & Perizinan</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-4 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Jenis</label>
                <input type="text" name="jenis" id="edit_jenis" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor</label>
                <input type="text" name="nomor" id="edit_nomor" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Instansi</label>
                <input type="text" name="instansi" id="edit_instansi" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Berlaku Hingga</label>
                <input type="text" name="berlaku_hingga" id="edit_berlaku_hingga"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="edit_status" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="Aktif">Aktif</option>
                    <option value="Kadaluarsa">Kadaluarsa</option>
                </select>
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
    fetch(`/admin/sertifikasi-perizinan/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `{{ url('admin/sertifikasi-perizinan') }}/${d.id}`;
            document.getElementById('edit_jenis').value = d.jenis ?? '';
            document.getElementById('edit_nomor').value = d.nomor ?? '';
            document.getElementById('edit_instansi').value = d.instansi ?? '';
            document.getElementById('edit_berlaku_hingga').value = d.berlaku_hingga ?? '';
            document.getElementById('edit_status').value = d.status ?? '';
            document.getElementById('modalEdit').classList.remove('hidden');
        });
}

);
}
</script>
@endpush
