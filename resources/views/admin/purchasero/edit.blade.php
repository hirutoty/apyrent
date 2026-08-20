@extends('admin.layouts.app')

@section('title', 'Edit Pengadaan')

@section('content')
<div class="space-y-6 p-5">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Pengadaan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Edit permintaan pembelian barang & jasa</p>
        </div>
        <a href="{{ route('purchasero.index') }}" 
            class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- FORM CARD --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        
        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">{{ $purchasero->no_pr }}</h2>
                    <p class="text-blue-100 text-sm">Status: {{ $purchasero->status }}</p>
                </div>
                <div class="text-right text-sm text-blue-100">
                    <div>{{ $purchasero->departemen }}</div>
                    <div>{{ \Carbon\Carbon::parse($purchasero->tanggal)->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        {{-- FORM --}}
        <form action="{{ route('purchasero.update', $purchasero->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            {{-- Header Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 pb-6 border-b border-gray-100">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $purchasero->tanggal) }}" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pemohon <span class="text-red-500">*</span></label>
                    <input type="text" name="pemohon" value="{{ old('pemohon', $purchasero->pemohon) }}" required 
                        placeholder="Nama pemohon" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                @if(auth()->user()->role === 'superadmin')
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Departemen <span class="text-red-500">*</span></label>
                    <select name="departemen" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Departemen</option>
                        @foreach(['Keuangan', 'Produksi', 'HRD', 'Purchase', 'Sales', 'Marketing', 'IT', 'Manajemen'] as $dept)
                            <option value="{{ $dept }}" {{ old('departemen', $purchasero->departemen) === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Permintaan <span class="text-red-500">*</span></label>
                    <textarea name="alasan_permintaan" rows="3" required 
                        placeholder="Jelaskan alasan permintaan pengadaan..." 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('alasan_permintaan', $purchasero->alasan_permintaan) }}</textarea>
                </div>
            </div>

            {{-- Items Section --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Items</h3>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Total Items: <span id="totalItemsCount">{{ $purchasero->items->count() }}</span></p>
                        <p class="text-sm font-semibold text-emerald-600">Total: Rp <span id="grandTotal">{{ number_format($purchasero->total_nominal, 0, ',', '.') }}</span></p>
                    </div>
                </div>

                <div id="itemsContainer" class="space-y-4">
                    <!-- Items will be populated by JavaScript -->
                </div>

                <button type="button" id="btnTambahItem" 
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors">
                    <i class="fa fa-plus"></i> Tambah Item
                </button>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('purchasero.index') }}" 
                    class="flex-1 text-center py-3 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                    <i class="fa fa-save mr-2"></i> Update Pengadaan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript --}}
<script>
let itemCount = 0;
let existingItems = @json($purchasero->items);

// Fungsi untuk membuat item row
function createItemRow(index, item = null) {
    const itemDiv = document.createElement('div');
    itemDiv.className = 'border border-gray-200 rounded-lg overflow-hidden';
    itemDiv.id = `item-${index}`;
    
    itemDiv.innerHTML = `
        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
            <span class="text-sm font-semibold text-gray-700">Item #${index + 1}</span>
            <button type="button" onclick="removeItem(${index})" 
                class="w-7 h-7 rounded-full bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center transition-colors">
                <i class="fa fa-times text-xs"></i>
            </button>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" name="items[${index}][nama_barang]" value="${item?.nama_barang || ''}" required
                    placeholder="Contoh: Laptop Dell Inspiron" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <input type="text" name="items[${index}][kategori]" value="${item?.kategori || ''}"
                    placeholder="Contoh: Elektronik" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Qty <span class="text-red-500">*</span></label>
                <input type="number" name="items[${index}][qty]" value="${item?.qty || ''}" required min="0.01" step="0.01"
                    placeholder="1" onchange="calculateSubtotal(${index})"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                <input type="text" name="items[${index}][satuan]" value="${item?.satuan || ''}"
                    placeholder="pcs" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                <input type="number" name="items[${index}][harga_satuan]" value="${item?.harga_satuan || ''}" min="0" step="0.01"
                    placeholder="100000" onchange="calculateSubtotal(${index})"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                <input type="number" name="items[${index}][subtotal]" value="${item?.subtotal || ''}" readonly
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none">
            </div>
        </div>
    `;
    
    return itemDiv;
}

// Calculate subtotal & update grand total
function calculateSubtotal(index) {
    const qtyInput = document.querySelector(`input[name="items[${index}][qty]"]`);
    const hargaInput = document.querySelector(`input[name="items[${index}][harga_satuan]"]`);
    const subtotalInput = document.querySelector(`input[name="items[${index}][subtotal]"]`);
    
    if (qtyInput && hargaInput && subtotalInput) {
        const qty = parseFloat(qtyInput.value) || 0;
        const harga = parseFloat(hargaInput.value) || 0;
        const subtotal = qty * harga;
        
        subtotalInput.value = subtotal;
        updateGrandTotal();
    }
}

function updateGrandTotal() {
    const subtotalInputs = document.querySelectorAll('input[name*="[subtotal]"]');
    let total = 0;
    subtotalInputs.forEach(input => total += parseFloat(input.value) || 0);
    document.getElementById('grandTotal').textContent = total.toLocaleString('id-ID');
}

function addItem() {
    const container = document.getElementById('itemsContainer');
    const itemDiv = createItemRow(itemCount);
    container.appendChild(itemDiv);
    itemCount++;
    updateItemNumbers();
}

function removeItem(index) {
    const itemDiv = document.getElementById(`item-${index}`);
    if (itemDiv) {
        itemDiv.remove();
        updateItemNumbers();
        updateGrandTotal();
    }
}

function updateItemNumbers() {
    const items = document.querySelectorAll('#itemsContainer > div');
    items.forEach((item, i) => {
        const label = item.querySelector('.text-gray-700');
        if (label) label.textContent = `Item #${i + 1}`;
    });
    document.getElementById('totalItemsCount').textContent = items.length;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('itemsContainer');
    
    if (existingItems.length > 0) {
        existingItems.forEach((item, index) => {
            container.appendChild(createItemRow(index, item));
        });
        itemCount = existingItems.length;
    } else {
        addItem();
    }
    
    updateItemNumbers();
    updateGrandTotal();
});

document.getElementById('btnTambahItem').addEventListener('click', addItem);
</script>

@endsection