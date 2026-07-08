@extends('admin.layouts.app')
@section('title', 'Retur Penjualan')
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Retur Penjualan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data retur penjualan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('retur-penjualan.pdf') }}" target="_blank"
                class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Retur
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-rotate-left"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Retur</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0">
                <i class="fa fa-clock"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalMenunggu }}</p>
                <p class="text-xs text-gray-500 mt-1">Menunggu</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500 text-lg flex-shrink-0">
                <i class="fa fa-spinner"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDiproses }}</p>
                <p class="text-xs text-gray-500 mt-1">Diproses</p>
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
    </div>

    {{-- ALERT --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
        <i class="fa fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Retur Penjualan</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p>
            </div>
            <div class="relative">
                <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Cari retur..." oninput="onSearchInput(this.value)"
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
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No Retur</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No Order</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Pelanggan</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Produk</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Qty</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Nilai Retur</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors"
                        data-search="{{ strtolower($d->no_retur.' '.$d->no_order.' '.$d->pelanggan.' '.$d->produk.' '.$d->status) }}">
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-blue-600 font-semibold">{{ $d->no_retur }}</td>
                        <td class="px-4 py-3.5 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($d->tanggal)->format('d M Y') }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-600">{{ $d->no_order }}</td>
                        <td class="px-4 py-3.5 text-xs font-semibold text-gray-800">{{ $d->pelanggan }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-700">{{ $d->produk }}</td>
                        <td class="px-4 py-3.5 text-center text-xs text-gray-700">{{ $d->qty }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-800 font-medium">Rp {{ number_format($d->nilai_retur, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5">
                            @php
                                $sc = match($d->status) {
                                    'Menunggu' => 'bg-yellow-100 text-yellow-700',
                                    'Diproses' => 'bg-indigo-100 text-indigo-700',
                                    'Selesai'  => 'bg-green-100 text-green-700',
                                    default    => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}">
                                <i class="fa fa-circle text-[6px]"></i> {{ $d->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditModal({{ $d->id }})"
                                    class="w-7 h-7 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 flex items-center justify-center transition-colors">
                                    <i class="fa fa-pen text-xs"></i>
                                </button>
                                <form action="{{ route('retur-penjualan.destroy', $d->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus data retur ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors">
                                        <i class="fa fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-10 text-center text-gray-400 text-sm">
                            <i class="fa fa-inbox text-2xl mb-2 block"></i> Belum ada data retur penjualan
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
            <h3 class="font-semibold text-gray-800">Tambah Retur Penjualan</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('retur-penjualan.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No Retur <span class="text-red-500">*</span></label>
                    <input type="text" name="no_retur" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                        placeholder="RTR-001">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No Order <span class="text-red-500">*</span></label>
                    <input type="text" name="no_order" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                        placeholder="SO-2026-001">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pelanggan <span class="text-red-500">*</span></label>
                    <input type="text" name="pelanggan" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="produk" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty <span class="text-red-500">*</span></label>
                    <input type="number" name="qty" required min="1"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nilai Retur <span class="text-red-500">*</span></label>
                    <input type="number" name="nilai_retur" required min="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="">-- Pilih --</option>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Alasan <span class="text-red-500">*</span></label>
                <textarea name="alasan" required rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                    placeholder="Alasan retur..."></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Edit Retur Penjualan</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No Retur <span class="text-red-500">*</span></label>
                    <input type="text" name="no_retur" id="edit_no_retur" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="edit_tanggal" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No Order <span class="text-red-500">*</span></label>
                    <input type="text" name="no_order" id="edit_no_order" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pelanggan <span class="text-red-500">*</span></label>
                    <input type="text" name="pelanggan" id="edit_pelanggan" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="produk" id="edit_produk" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty <span class="text-red-500">*</span></label>
                    <input type="number" name="qty" id="edit_qty" required min="1"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nilai Retur <span class="text-red-500">*</span></label>
                    <input type="number" name="nilai_retur" id="edit_nilai_retur" required min="0"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="edit_status" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                        <option value="Menunggu">Menunggu</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Alasan <span class="text-red-500">*</span></label>
                <textarea name="alasan" id="edit_alasan" required rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modalAdd').classList.remove('hidden');
    document.getElementById('modalAdd').classList.add('flex');
}
function closeModal() {
    document.getElementById('modalAdd').classList.add('hidden');
    document.getElementById('modalAdd').classList.remove('flex');
}
function openEditModal(id) {
    fetch(`/admin/retur-penjualan/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `/admin/retur-penjualan/${id}`;
            document.getElementById('edit_no_retur').value   = d.no_retur;
            document.getElementById('edit_tanggal').value    = d.tanggal;
            document.getElementById('edit_no_order').value   = d.no_order;
            document.getElementById('edit_pelanggan').value  = d.pelanggan;
            document.getElementById('edit_produk').value     = d.produk;
            document.getElementById('edit_qty').value        = d.qty;
            document.getElementById('edit_nilai_retur').value = d.nilai_retur;
            document.getElementById('edit_status').value     = d.status;
            document.getElementById('edit_alasan').value     = d.alasan;
            document.getElementById('modalEdit').classList.remove('hidden');
            document.getElementById('modalEdit').classList.add('flex');
        });
}
function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
    document.getElementById('modalEdit').classList.remove('flex');
}

// Search & Pagination
let perPage = 10;
function onSearchInput(val) { filterTable(val); }
function onPerPageChange(val) { perPage = val === 'all' ? 99999 : parseInt(val); filterTable(document.querySelector('input[placeholder]').value); }
function filterTable(search) {
    const rows = Array.from(document.querySelectorAll('#tableBody tr[data-search]'));
    const q = search.toLowerCase();
    let visible = 0;
    rows.forEach(r => {
        const match = r.dataset.search.includes(q);
        r.style.display = (match && visible < perPage) ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('paginationInfo').textContent =
        `Menampilkan ${Math.min(visible, perPage)} dari ${rows.filter(r => r.dataset.search.includes(q)).length} data`;
}
filterTable('');
</script>
@endsection
