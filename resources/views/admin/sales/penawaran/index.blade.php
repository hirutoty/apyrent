@extends('admin.layouts.app')
@section('title', 'Penawaran Sales')
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Penawaran Sales</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data quotation dan penawaran harga</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('penawaran-sales.pdf') }}" target="_blank"
                class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Penawaran
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-file-invoice"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Penawaran</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 text-lg flex-shrink-0">
                <i class="fa fa-pen-to-square"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDraft }}</p>
                <p class="text-xs text-gray-500 mt-1">Draft</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-lg flex-shrink-0">
                <i class="fa fa-paper-plane"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalTerkirim }}</p>
                <p class="text-xs text-gray-500 mt-1">Terkirim</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalDisetujui }}</p>
                <p class="text-xs text-gray-500 mt-1">Disetujui</p>
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
                <h2 class="font-semibold text-gray-800 text-base">Daftar Penawaran</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p>
            </div>
            <div class="relative">
                <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Cari penawaran..." oninput="onSearchInput(this.value)"
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
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No. Quotation</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Pelanggan</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Produk/Jasa</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Total Harga</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Valid Sampai</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors"
                        data-search="{{ strtolower($d->no_quotation.' '.$d->pelanggan.' '.$d->produk_jasa.' '.$d->status) }}">
                        <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-blue-600">{{ $d->no_quotation }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800 text-xs">{{ $d->pelanggan }}</td>
                        <td class="px-4 py-3.5 text-gray-700 text-xs">{{ $d->produk_jasa }}</td>
                        <td class="px-4 py-3.5 text-right text-gray-800 text-xs font-semibold">
                            Rp {{ number_format($d->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($d->valid_sampai)->format('d M Y') }}</td>
                        <td class="px-4 py-3.5">
                            @php
                                $sc = match($d->status) {
                                    'Draft'     => 'bg-gray-100 text-gray-600',
                                    'Terkirim'  => 'bg-yellow-100 text-yellow-700',
                                    'Disetujui' => 'bg-green-100 text-green-700',
                                    'Ditolak'   => 'bg-red-100 text-red-600',
                                    default     => 'bg-gray-100 text-gray-600',
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
                                <form action="{{ route('penawaran-sales.destroy', $d->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus data ini?')">
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
                        <td colspan="8" class="px-4 py-10 text-center text-gray-400 text-sm">
                            <i class="fa fa-inbox text-2xl mb-2 block"></i> Belum ada data penawaran
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
            <h3 class="font-semibold text-gray-800">Tambah Penawaran Sales</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('penawaran-sales.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Quotation <span class="text-red-500">*</span></label>
                    <input type="text" name="no_quotation" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="QUO-001">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pelanggan <span class="text-red-500">*</span></label>
                    <input type="text" name="pelanggan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Produk/Jasa <span class="text-red-500">*</span></label>
                    <input type="text" name="produk_jasa" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" required min="1" oninput="calcTotal()" id="jumlah" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_satuan" required min="0" oninput="calcTotal()" id="harga_satuan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Harga <span class="text-red-500">*</span></label>
                    <input type="number" name="total_harga" required min="0" id="total_harga" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Valid Sampai <span class="text-red-500">*</span></label>
                    <input type="date" name="valid_sampai" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">-- Pilih --</option>
                        <option value="Draft">Draft</option>
                        <option value="Terkirim">Terkirim</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
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
            <h3 class="font-semibold text-gray-800">Edit Penawaran Sales</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Quotation <span class="text-red-500">*</span></label>
                    <input type="text" name="no_quotation" id="edit_no_quotation" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="edit_tanggal" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pelanggan <span class="text-red-500">*</span></label>
                    <input type="text" name="pelanggan" id="edit_pelanggan" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Produk/Jasa <span class="text-red-500">*</span></label>
                    <input type="text" name="produk_jasa" id="edit_produk_jasa" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" id="edit_jumlah" required min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_satuan" id="edit_harga_satuan" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Harga <span class="text-red-500">*</span></label>
                    <input type="number" name="total_harga" id="edit_total_harga" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Valid Sampai <span class="text-red-500">*</span></label>
                    <input type="date" name="valid_sampai" id="edit_valid_sampai" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="edit_status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Draft">Draft</option>
                        <option value="Terkirim">Terkirim</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
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
function calcTotal() {
    const j = parseFloat(document.getElementById('jumlah').value) || 0;
    const h = parseFloat(document.getElementById('harga_satuan').value) || 0;
    document.getElementById('total_harga').value = j * h;
}
function openModal() {
    document.getElementById('modalAdd').classList.remove('hidden');
    document.getElementById('modalAdd').classList.add('flex');
}
function closeModal() {
    document.getElementById('modalAdd').classList.add('hidden');
    document.getElementById('modalAdd').classList.remove('flex');
}
function openEditModal(id) {
    fetch(`/admin/penawaran-sales/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `/admin/penawaran-sales/${id}`;
            document.getElementById('edit_no_quotation').value = d.no_quotation;
            document.getElementById('edit_tanggal').value = d.tanggal;
            document.getElementById('edit_pelanggan').value = d.pelanggan;
            document.getElementById('edit_produk_jasa').value = d.produk_jasa;
            document.getElementById('edit_jumlah').value = d.jumlah;
            document.getElementById('edit_harga_satuan').value = d.harga_satuan;
            document.getElementById('edit_total_harga').value = d.total_harga;
            document.getElementById('edit_valid_sampai').value = d.valid_sampai;
            document.getElementById('edit_status').value = d.status;
            document.getElementById('modalEdit').classList.remove('hidden');
            document.getElementById('modalEdit').classList.add('flex');
        });
}
function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
    document.getElementById('modalEdit').classList.remove('flex');
}
let perPage = 10;
function onSearchInput(val) { filterTable(val); }
function onPerPageChange(val) { perPage = val === 'all' ? 99999 : parseInt(val); filterTable(document.querySelector('input[placeholder]').value); }
function filterTable(search) {
    const rows = Array.from(document.querySelectorAll('#tableBody tr[data-search]'));
    const q = search.toLowerCase();
    let visible = 0;
    rows.forEach(r => {
        const match = r.dataset.search.includes(q);
        r.style.display = match && visible < perPage ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('paginationInfo').textContent = `Menampilkan ${Math.min(visible, perPage)} dari ${rows.filter(r=>r.dataset.search.includes(q)).length} data`;
}
filterTable('');
</script>
@endsection
