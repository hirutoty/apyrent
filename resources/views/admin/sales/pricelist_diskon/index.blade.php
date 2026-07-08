@extends('admin.layouts.app')
@section('title', 'Pricelist & Diskon')
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pricelist & Diskon</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola daftar harga dan program diskon produk</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pricelist-diskon.pdf') }}" target="_blank"
                class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Pricelist
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-tags"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Pricelist</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 text-lg flex-shrink-0">
                <i class="fa fa-percent"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ number_format($avgDiskon, 1) }}%</p>
                <p class="text-xs text-gray-500 mt-1">Rata-rata Diskon</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-check-circle"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $data->where('status','Aktif')->count() }}</p>
                <p class="text-xs text-gray-500 mt-1">Aktif</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
        <i class="fa fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Pricelist & Diskon</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p>
            </div>
            <div class="relative">
                <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Cari produk..." oninput="onSearchInput(this.value)"
                    class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
            </div>
        </div>
        <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 text-xs text-gray-500">
            <span>Show</span>
            <select onchange="onPerPageChange(this.value)" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="all">All</option>
            </select>
            <span>entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">ID Harga</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Nama Produk</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Level Pelanggan</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Harga Normal</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Diskon (%)</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Harga Diskon</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Periode</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors"
                        data-search="{{ strtolower($d->id_harga.' '.$d->nama_produk.' '.$d->level_pelanggan.' '.($d->status ?? '')) }}">
                        <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-blue-600">{{ $d->id_harga }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800 text-xs">{{ $d->nama_produk }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                {{ $d->level_pelanggan }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right text-gray-700 text-xs">Rp {{ number_format($d->harga_normal, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                {{ $d->diskon }}%
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right text-green-700 text-xs font-semibold">Rp {{ number_format($d->harga_diskon, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-gray-500 text-xs">
                            {{ \Carbon\Carbon::parse($d->periode_mulai)->format('d M Y') }} –
                            {{ \Carbon\Carbon::parse($d->periode_selesai)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            @php $sc = ($d->status ?? 'Aktif') === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}">
                                {{ $d->status ?? 'Aktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditModal({{ $d->id }})"
                                    class="w-7 h-7 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 flex items-center justify-center transition-colors">
                                    <i class="fa fa-pen text-xs"></i>
                                </button>
                                <form action="{{ route('pricelist-diskon.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors">
                                        <i class="fa fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-10 text-center text-gray-400 text-sm">
                            <i class="fa fa-inbox text-2xl mb-2 block"></i> Belum ada data pricelist
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="paginationInfo" class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400"></div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modalAdd" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Tambah Pricelist & Diskon</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('pricelist-diskon.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">ID Harga <span class="text-red-500">*</span></label>
                    <input type="text" name="id_harga" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="PRC-001">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_produk" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Level Pelanggan <span class="text-red-500">*</span></label>
                    <select name="level_pelanggan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">-- Pilih --</option>
                        <option value="Regular">Regular</option>
                        <option value="Silver">Silver</option>
                        <option value="Gold">Gold</option>
                        <option value="Platinum">Platinum</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga Normal <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_normal" required min="0" oninput="calcHargaDiskon()" id="harga_normal"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Diskon (%) <span class="text-red-500">*</span></label>
                    <input type="number" name="diskon" required min="0" max="100" oninput="calcHargaDiskon()" id="diskon"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga Diskon <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_diskon" id="harga_diskon" required min="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Periode Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="periode_mulai" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Periode Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="periode_selesai" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Edit Pricelist & Diskon</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">ID Harga <span class="text-red-500">*</span></label>
                    <input type="text" name="id_harga" id="edit_id_harga" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_produk" id="edit_nama_produk" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Level Pelanggan <span class="text-red-500">*</span></label>
                    <select name="level_pelanggan" id="edit_level_pelanggan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Regular">Regular</option>
                        <option value="Silver">Silver</option>
                        <option value="Gold">Gold</option>
                        <option value="Platinum">Platinum</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga Normal <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_normal" id="edit_harga_normal" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Diskon (%) <span class="text-red-500">*</span></label>
                    <input type="number" name="diskon" id="edit_diskon" required min="0" max="100" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga Diskon <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_diskon" id="edit_harga_diskon" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Periode Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="periode_mulai" id="edit_periode_mulai" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Periode Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="periode_selesai" id="edit_periode_selesai" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="edit_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function calcHargaDiskon() {
    const h = parseFloat(document.getElementById('harga_normal').value) || 0;
    const d = parseFloat(document.getElementById('diskon').value) || 0;
    document.getElementById('harga_diskon').value = (h - (h * d / 100)).toFixed(0);
}
function openModal() { document.getElementById('modalAdd').classList.remove('hidden'); document.getElementById('modalAdd').classList.add('flex'); }
function closeModal() { document.getElementById('modalAdd').classList.add('hidden'); document.getElementById('modalAdd').classList.remove('flex'); }
function openEditModal(id) {
    fetch(`/admin/pricelist-diskon/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `/admin/pricelist-diskon/${id}`;
            document.getElementById('edit_id_harga').value = d.id_harga;
            document.getElementById('edit_nama_produk').value = d.nama_produk;
            document.getElementById('edit_level_pelanggan').value = d.level_pelanggan;
            document.getElementById('edit_harga_normal').value = d.harga_normal;
            document.getElementById('edit_diskon').value = d.diskon;
            document.getElementById('edit_harga_diskon').value = d.harga_diskon;
            document.getElementById('edit_periode_mulai').value = d.periode_mulai;
            document.getElementById('edit_periode_selesai').value = d.periode_selesai;
            document.getElementById('edit_status').value = d.status ?? 'Aktif';
            document.getElementById('modalEdit').classList.remove('hidden');
            document.getElementById('modalEdit').classList.add('flex');
        });
}
function closeEditModal() { document.getElementById('modalEdit').classList.add('hidden'); document.getElementById('modalEdit').classList.remove('flex'); }
let perPage = 10;
function onSearchInput(val) { filterTable(val); }
function onPerPageChange(val) { perPage = val === 'all' ? 99999 : parseInt(val); filterTable(document.querySelector('input[placeholder]').value); }
function filterTable(search) {
    const rows = Array.from(document.querySelectorAll('#tableBody tr[data-search]'));
    const q = search.toLowerCase();
    let visible = 0;
    rows.forEach(r => { const match = r.dataset.search.includes(q); r.style.display = match && visible < perPage ? '' : 'none'; if (match) visible++; });
    document.getElementById('paginationInfo').textContent = `Menampilkan ${Math.min(visible, perPage)} dari ${rows.filter(r=>r.dataset.search.includes(q)).length} data`;
}
filterTable('');
</script>
@endsection
