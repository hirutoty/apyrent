@extends('admin.layouts.app')
@section('title', 'Sales Order')
@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Sales Order</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola data pesanan penjualan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('sales-order.pdf') }}" target="_blank"
                class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-file-pdf text-sm"></i> Export PDF
            </a>
            <button onclick="openModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <i class="fa fa-plus text-sm"></i> Tambah Order
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg flex-shrink-0">
                <i class="fa fa-cart-shopping"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $total }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Order</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500 text-lg flex-shrink-0">
                <i class="fa fa-spinner"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalProses }}</p>
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-lg flex-shrink-0">
                <i class="fa fa-circle-xmark"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800 leading-none">{{ $totalBatal }}</p>
                <p class="text-xs text-gray-500 mt-1">Dibatalkan</p>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800 text-base">Daftar Sales Order</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $data->count() }} total data</p>
            </div>
            <div class="relative">
                <i class="fa fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Cari order..." oninput="onSearchInput(this.value)"
                    class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-44">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">No. Order</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Pelanggan</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Produk/Jasa</th>
                        <th class="text-right text-xs font-semibold uppercase text-gray-500 px-4 py-3">Total Harga</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Metode Bayar</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Sales</th>
                        <th class="text-left text-xs font-semibold uppercase text-gray-500 px-4 py-3">Status</th>
                        <th class="text-center text-xs font-semibold uppercase text-gray-500 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($data as $d)
                    <tr class="border-t border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400">{{ $loop->iteration + ($data->firstItem() - 1) }}</td>
                        <td class="px-4 py-3.5 text-xs font-mono text-blue-600">{{ $d->order_no }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800 text-xs">{{ $d->pelanggan }}</td>
                        <td class="px-4 py-3.5 text-gray-700 text-xs">{{ $d->produk_jasa }}</td>
                        <td class="px-4 py-3.5 text-right text-gray-800 text-xs font-semibold">
                            Rp {{ number_format($d->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 text-xs">{{ $d->metode_pembayaran }}</td>
                        <td class="px-4 py-3.5 text-gray-700 text-xs">{{ $d->sales }}</td>
                        <td class="px-4 py-3.5">
                            @php
                                $sc = match($d->status_order) {
                                    'Diproses'   => 'bg-indigo-100 text-indigo-700',
                                    'Selesai'    => 'bg-green-100 text-green-700',
                                    'Dibatalkan' => 'bg-red-100 text-red-600',
                                    default      => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}">
                                <i class="fa fa-circle text-[6px]"></i> {{ $d->status_order }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditModal({{ $d->id }})"
                                    class="w-7 h-7 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 flex items-center justify-center transition-colors">
                                    <i class="fa fa-pen text-xs"></i>
                                </button>
                                <form action="{{ route('sales-order.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
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
                        <td colspan="9" class="px-4 py-10 text-center text-gray-400 text-sm">
                            <i class="fa fa-inbox text-2xl mb-2 block"></i> Belum ada data sales order
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $data->links() }}</div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modalAdd" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Tambah Sales Order</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('sales-order.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Order <span class="text-red-500">*</span></label>
                    <input type="text" name="order_no" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="SO-001">
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty <span class="text-red-500">*</span></label>
                    <input type="number" name="qty" required min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Harga <span class="text-red-500">*</span></label>
                    <input type="number" name="total_harga" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <select name="metode_pembayaran" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">-- Pilih --</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="Cash">Cash</option>
                        <option value="Kredit">Kredit</option>
                        <option value="Tempo">Tempo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sales <span class="text-red-500">*</span></label>
                    <input type="text" name="sales" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status Order <span class="text-red-500">*</span></label>
                    <select name="status_order" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">-- Pilih --</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
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
            <h3 class="font-semibold text-gray-800">Edit Sales Order</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Order <span class="text-red-500">*</span></label>
                    <input type="text" name="order_no" id="edit_order_no" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty <span class="text-red-500">*</span></label>
                    <input type="number" name="qty" id="edit_qty" required min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Harga <span class="text-red-500">*</span></label>
                    <input type="number" name="total_harga" id="edit_total_harga" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <select name="metode_pembayaran" id="edit_metode_pembayaran" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="Cash">Cash</option>
                        <option value="Kredit">Kredit</option>
                        <option value="Tempo">Tempo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sales <span class="text-red-500">*</span></label>
                    <input type="text" name="sales" id="edit_sales" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status Order <span class="text-red-500">*</span></label>
                    <select name="status_order" id="edit_status_order" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
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
function openModal() { document.getElementById('modalAdd').classList.remove('hidden'); document.getElementById('modalAdd').classList.add('flex'); }
function closeModal() { document.getElementById('modalAdd').classList.add('hidden'); document.getElementById('modalAdd').classList.remove('flex'); }
function openEditModal(id) {
    fetch(`/admin/sales-order/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editForm').action = `/admin/sales-order/${id}`;
            document.getElementById('edit_order_no').value = d.order_no;
            document.getElementById('edit_tanggal').value = d.tanggal;
            document.getElementById('edit_pelanggan').value = d.pelanggan;
            document.getElementById('edit_produk_jasa').value = d.produk_jasa;
            document.getElementById('edit_qty').value = d.qty;
            document.getElementById('edit_total_harga').value = d.total_harga;
            document.getElementById('edit_metode_pembayaran').value = d.metode_pembayaran;
            document.getElementById('edit_sales').value = d.sales;
            document.getElementById('edit_status_order').value = d.status_order;
            document.getElementById('modalEdit').classList.remove('hidden');
            document.getElementById('modalEdit').classList.add('flex');
        });
}
function closeEditModal() { document.getElementById('modalEdit').classList.add('hidden'); document.getElementById('modalEdit').classList.remove('flex'); }

</script>

{{-- POPUP ALERT --}}
@if(session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox"
        class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if(session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Berhasil!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
        @elseif(session('error'))
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
            </div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800">Terjadi Kesalahan!</p>
                <ul class="text-xs text-gray-500 mt-0.5 leading-relaxed list-disc ml-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5 flex-shrink-0">
            <i class="fa fa-times"></i>
        </button>
    </div>
</div>
<script>
(function(){
    var overlay = document.getElementById('alertOverlay');
    var box = document.getElementById('alertBox');
    if (!overlay) return;
    setTimeout(function(){ overlay.style.opacity='1'; overlay.style.pointerEvents='auto'; box.style.transform='translateY(0)'; }, 80);
    var timer = setTimeout(closeAlert, 4500);
    overlay.addEventListener('click', function(e){ if(e.target===overlay) closeAlert(); });
    function closeAlert(){ clearTimeout(timer); overlay.style.opacity='0'; overlay.style.pointerEvents='none'; box.style.transform='translateY(-16px)'; }
    window.closeAlert = closeAlert;
})();
</script>
@endif
@endsection
