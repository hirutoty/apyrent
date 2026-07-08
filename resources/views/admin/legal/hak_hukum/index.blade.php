@extends('admin.layouts.app')
@section('title', 'Hak Hukum')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Hak Hukum</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola hak akses dan kewenangan hukum dokumen</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('hak-hukum.pdf') }}" target="_blank" class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Hak Hukum
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg"><i class="fa fa-scale-balanced"></i></div>
            <div><p class="text-xl font-bold text-gray-800">{{ $total }}</p><p class="text-xs text-gray-500 mt-1">Total Hak Hukum</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg"><i class="fa fa-circle-check"></i></div>
            <div><p class="text-xl font-bold text-gray-800">{{ $totalAktif }}</p><p class="text-xs text-gray-500 mt-1">Aktif</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 text-lg"><i class="fa fa-ban"></i></div>
            <div><p class="text-xl font-bold text-gray-800">{{ $totalNonAktif }}</p><p class="text-xs text-gray-500 mt-1">Non-Aktif</p></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 text-base">Daftar Hak Hukum</h2>
            <input type="text" placeholder="Cari..." oninput="filterTable(this.value)" class="pl-3 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 w-44">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="mainTable">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Jenis Akses</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Kategori Dokumen</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Penerima Akses</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Level Hak</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tgl Akses</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data as $i => $d)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $d->jenis_akses }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $d->kategori_dokumen }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->penerima_akses }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->level_hak }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($d->tanggal_akses)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            @php $sc = $d->status === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $sc }}">{{ $d->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="openEdit({{ $d->id }})" class="inline-flex items-center gap-1 text-xs bg-yellow-50 hover:bg-yellow-100 text-yellow-700 px-2.5 py-1.5 rounded-lg">
                                    <i class="fa fa-pen text-xs"></i> Edit
                                </button>
                                <form action="{{ route('hak-hukum.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
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
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modalCreate" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Tambah Hak Hukum</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('hak-hukum.store') }}" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Jenis Akses *</label><input type="text" name="jenis_akses" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Kategori Dokumen *</label><input type="text" name="kategori_dokumen" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Penerima Akses *</label><input type="text" name="penerima_akses" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Level Hak *</label><input type="text" name="level_hak" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Akses *</label><input type="date" name="tanggal_akses" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Aktif">Aktif</option>
                        <option value="Non-Aktif">Non-Aktif</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-xl hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Edit Hak Hukum</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-4 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Jenis Akses</label><input type="text" name="jenis_akses" id="edit_jenis_akses" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Kategori Dokumen</label><input type="text" name="kategori_dokumen" id="edit_kategori_dokumen" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Penerima Akses</label><input type="text" name="penerima_akses" id="edit_penerima_akses" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Level Hak</label><input type="text" name="level_hak" id="edit_level_hak" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Akses</label><input type="date" name="tanggal_akses" id="edit_tanggal_akses" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="edit_status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Aktif">Aktif</option>
                        <option value="Non-Aktif">Non-Aktif</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-yellow-500 text-white rounded-xl hover:bg-yellow-600">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openModal() { document.getElementById('modalCreate').classList.remove('hidden'); }
function closeModal() { document.getElementById('modalCreate').classList.add('hidden'); }
function closeEditModal() { document.getElementById('modalEdit').classList.add('hidden'); }
function openEdit(id) {
    fetch(`/admin/hak-hukum/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `/admin/hak-hukum/${d.id}`;
            document.getElementById('edit_jenis_akses').value = d.jenis_akses;
            document.getElementById('edit_kategori_dokumen').value = d.kategori_dokumen;
            document.getElementById('edit_penerima_akses').value = d.penerima_akses;
            document.getElementById('edit_level_hak').value = d.level_hak;
            document.getElementById('edit_tanggal_akses').value = d.tanggal_akses;
            document.getElementById('edit_status').value = d.status;
            document.getElementById('modalEdit').classList.remove('hidden');
        });
}
function filterTable(val) {
    document.querySelectorAll('#mainTable tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(val.toLowerCase()) ? '' : 'none';
    });
}
</script>
@endpush
