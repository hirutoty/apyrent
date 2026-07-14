@extends('admin.layouts.app')
@section('title', 'Daftar Notaris')
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Notaris</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola daftar kantor notaris rekanan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('daftar-notaris.pdf') }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-500 text-red-500 rounded-lg bg-transparent hover:bg-red-500 hover:text-white transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Notaris
            </button>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- STAT CARD --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg">
                <i class="fa fa-building-columns"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Notaris</p>
            </div>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 text-base">Daftar Kantor Notaris</h2>
            <form method="GET" class="flex gap-1">
                <input type="text" placeholder="Cari notaris..."
                   name="search" value="{{ request('search') }}"
                   class="pl-3 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 w-44">
                <button class="bg-gray-800 text-white text-xs px-3 py-1.5 rounded-lg">Cari</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="mainTable">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Nama Kantor</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Layanan</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Kontak</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Email</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Terakhir Dipakai</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data as $i => $d)
                    <tr class="odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $d->nama_kantor }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->layanan }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $d->kontak }}</td>
                        <td class="px-4 py-3 text-blue-600 text-xs">
                            <a href="mailto:{{ $d->email }}" class="hover:underline">{{ $d->email }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">
                            @if($d->terakhir_dipakai)
                                {{ \Carbon\Carbon::parse($d->terakhir_dipakai)->format('d/m/Y') }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="openEdit({{ $d->id }})"
                                        class="inline-flex items-center gap-1 text-xs bg-yellow-50 hover:bg-yellow-100 text-yellow-700 px-2.5 py-1.5 rounded-lg transition-colors">
                                    <i class="fa fa-pen text-xs"></i> Edit
                                </button>
                                <form action="{{ route('daftar-notaris.destroy', $d->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2.5 py-1.5 rounded-lg transition-colors">
                                        <i class="fa fa-trash text-xs"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">
                            Belum ada data notaris
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>
</div>

{{-- MODAL CREATE --}}
<div id="modalCreate" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Tambah Notaris</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('daftar-notaris.store') }}" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kantor <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kantor" required placeholder="Contoh: Notaris Budi Santoso, SH"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Layanan <span class="text-red-500">*</span></label>
                <input type="text" name="layanan" required placeholder="Contoh: Akta Jual Beli, PPJB, Sewa"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kontak <span class="text-red-500">*</span></label>
                    <input type="number" name="kontak" required placeholder="08xx-xxxx-xxxx"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required placeholder="notaris@email.com"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Terakhir Dipakai <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="date" name="terakhir_dipakai"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Edit Notaris</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Kantor <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kantor" id="edit_nama_kantor" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Layanan <span class="text-red-500">*</span></label>
                <input type="text" name="layanan" id="edit_layanan" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kontak <span class="text-red-500">*</span></label>
                    <input type="number" name="kontak" id="edit_kontak" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="edit_email" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Terakhir Dipakai <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="date" name="terakhir_dipakai" id="edit_terakhir_dipakai"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl odd:bg-white even:bg-gray-100 hover:bg-blue-50/50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition-colors">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<div class="px-5 py-3 border-t border-gray-100">{{ $data->links() }}</div>
@push('scripts')
<script>
function openModal() {
    document.getElementById('modalCreate').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('modalCreate').classList.add('hidden');
}
function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}
function openEdit(id) {
    fetch(`/admin/daftar-notaris/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `/admin/daftar-notaris/${d.id}`;
            document.getElementById('edit_nama_kantor').value   = d.nama_kantor   ?? '';
            document.getElementById('edit_layanan').value       = d.layanan       ?? '';
            document.getElementById('edit_kontak').value        = d.kontak        ?? '';
            document.getElementById('edit_email').value         = d.email         ?? '';
            document.getElementById('edit_terakhir_dipakai').value = d.terakhir_dipakai ?? '';
            document.getElementById('modalEdit').classList.remove('hidden');
        });
}
);
}
// Close modal when clicking backdrop
['modalCreate', 'modalEdit'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>
@endpush
