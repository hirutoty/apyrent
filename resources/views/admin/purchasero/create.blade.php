@extends('admin.layouts.app')

@section('title', 'Tambah Pengadaan')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6 max-w-5xl mx-auto">

    {{-- PAGE HEADER --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('purchasero.index') }}" 
           class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors">
            <i class="fa fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Tambah Pengadaan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Buat permintaan pengadaan barang & jasa dengan multiple items</p>
        </div>
    </div>

    <form action="{{ route('purchasero.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- SECTION 1: HEADER PENGADAAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">1</span>
                Informasi Pengadaan
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- No PR --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        No PR <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="no_pr" readonly
                        value="Auto Generate"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-600 cursor-not-allowed focus:outline-none">
                </div>

                {{-- Tanggal Pengajuan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Pengajuan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal" required
                        value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @error('tanggal')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Departemen --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Departemen <span class="text-red-500">*</span>
                    </label>
                    @if(auth()->user()->role === 'superadmin')
                        <select name="departemen" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="">-- Pilih Departemen --</option>
                            <option value="Finance" {{ old('departemen') === 'Finance' ? 'selected' : '' }}>Finance</option>
                            <option value="Operasional" {{ old('departemen') === 'Operasional' ? 'selected' : '' }}>Operasional</option>
                            <option value="IT" {{ old('departemen') === 'IT' ? 'selected' : '' }}>IT</option>
                            <option value="HRD" {{ old('departemen') === 'HRD' ? 'selected' : '' }}>HRD</option>
                            <option value="Marketing" {{ old('departemen') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="Procurement" {{ old('departemen') === 'Procurement' ? 'selected' : '' }}>Procurement</option>
                            <option value="Workshop" {{ old('departemen') === 'Workshop' ? 'selected' : '' }}>Workshop</option>
                            <option value="Warehouse" {{ old('departemen') === 'Warehouse' ? 'selected' : '' }}>Warehouse</option>
                        </select>
                    @else
                        <input type="text" name="departemen" required readonly
                            value="{{ auth()->user()->departemen ?? 'Departemen User' }}"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-600 cursor-not-allowed focus:outline-none">
                    @endif
                    @error('departemen')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Pemohon --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Pemohon <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="pemohon" required
                        value="{{ old('pemohon') }}" placeholder="Nama pemohon"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                    @error('pemohon')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Alasan Permintaan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Alasan Permintaan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alasan_permintaan" rows="3" required placeholder="Jelaskan alasan dan kebutuhan pengadaan..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 resize-none">{{ old('alasan_permintaan') }}</textarea>
                    @error('alasan_permintaan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- SECTION 2: ITEMS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">2</span>
                    Barang / Jasa yang Dibutuhkan
                </h2>
                <button type="button" onclick="addItemRow()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                    <i class="fa fa-plus text-xs"></i> Tambah Barang
                </button>
            </div>

            {{-- Total nominal display --}}
            <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-xl">
                <div class="text-xs text-gray-400">Total Nominal:</div>
                <div class="font-bold text-lg text-gray-800" id="total_display">Rp 0</div>
                <input type="hidden" name="total_nominal" id="total_nominal_input" value="0">
            </div>

            @error('items')<div class="text-xs text-red-500">{{ $message }}</div>@enderror

            {{-- Items container --}}
            <div id="items-container" class="space-y-4">
                {{-- Rows will be added via JS --}}
            </div>

            <div id="empty-items-hint" class="text-center py-6 text-xs text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                <i class="fa fa-shopping-cart text-gray-300 text-2xl mb-2 block"></i>
                Klik "+ Tambah Barang" untuk menambahkan item pengadaan
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('purchasero.index') }}"
                class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors">
                <i class="fa fa-paper-plane text-sm"></i>
                Simpan & Ajukan
            </button>
        </div>

    </form>
</div>

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.item-row { animation: slideUp 0.15s ease; }
</style>

<script>
// ── Data dan variabel global ──────────────────────────────────────────
let itemIndex = 0;

// ── Tambah row item ──────────────────────────────────────────
function addItemRow(data = null) {
    const container = document.getElementById('items-container');
    const hint      = document.getElementById('empty-items-hint');
    const idx       = itemIndex++;

    // Hide empty hint
    if (hint) hint.style.display = 'none';

    // Create row element
    const row = document.createElement('div');
    row.className = 'item-row bg-gray-50 border border-gray-200 rounded-xl p-4';
    row.id = `item-row-${idx}`;

    row.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-gray-600">Item #${idx + 1}</span>
            <button type="button" onclick="removeItemRow(${idx})"
                class="w-6 h-6 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center text-xs transition-colors">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

            <!-- Nama Barang -->
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Barang/Jasa <span class="text-red-400">*</span></label>
                <input type="text" name="items[${idx}][nama_barang]" required
                    value="${data?.nama_barang || ''}"
                    placeholder="Contoh: Laptop Dell, Tinta Printer, Jasa Cleaning"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Kategori -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Kategori</label>
                <input type="text" name="items[${idx}][kategori]"
                    value="${data?.kategori || ''}"
                    placeholder="ATK, Elektronik, Spare Part"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Posisi -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Posisi</label>
                <input type="text" name="items[${idx}][posisi]"
                    value="${data?.posisi || ''}"
                    placeholder="Kantor Pusat, Gudang A"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Part Number -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Part Number/Kode</label>
                <input type="text" name="items[${idx}][part_number]"
                    value="${data?.part_number || ''}"
                    placeholder="Kode barang (opsional)"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Serial Number -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Serial Number</label>
                <input type="text" name="items[${idx}][serial_number]"
                    value="${data?.serial_number || ''}"
                    placeholder="SN barang (opsional)"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- QTY -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">QTY <span class="text-red-400">*</span></label>
                <input type="number" name="items[${idx}][qty]" id="qty-${idx}" required min="1"
                    value="${data?.qty || 1}"
                    onchange="calculateSubtotal(${idx})" oninput="calculateSubtotal(${idx})"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Satuan -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Satuan</label>
                <input type="text" name="items[${idx}][satuan]"
                    value="${data?.satuan || ''}"
                    placeholder="pcs, unit, set, box"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Harga Satuan -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Harga Satuan</label>
                <input type="number" name="items[${idx}][harga_satuan]" id="harga-${idx}" min="0" step="0.01"
                    value="${data?.harga_satuan || 0}"
                    onchange="calculateSubtotal(${idx})" oninput="calculateSubtotal(${idx})"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Subtotal -->
            <div class="hidden">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Subtotal</label>
                <input type="number" name="items[${idx}][subtotal]" id="subtotal-${idx}" readonly
                    value="${data?.subtotal || 0}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600 cursor-not-allowed focus:outline-none">
            </div>

            <!-- Spesifikasi -->
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Spesifikasi</label>
                <textarea name="items[${idx}][spesifikasi]" rows="2"
                    placeholder="Detail spesifikasi teknis, ukuran, warna, dll..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none">${data?.spesifikasi || ''}</textarea>
            </div>

            <!-- Merk -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Merk/Brand</label>
                <input type="text" name="items[${idx}][merk]"
                    value="${data?.merk || ''}"
                    placeholder="Dell, HP, Canon"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Keterangan -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Keterangan</label>
                <textarea name="items[${idx}][keterangan]" rows="2"
                    placeholder="Catatan tambahan..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none">${data?.keterangan || ''}</textarea>
            </div>

            <!-- Bukti -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Bukti/Lampiran</label>
                <input type="file" name="items[${idx}][bukti][]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

        </div>
    `;

    container.appendChild(row);

    // Auto-calculate subtotal jika ada data
    if (data && data.qty && data.harga_satuan) {
        calculateSubtotal(idx);
    }
}

// ── Remove row item ──────────────────────────────────────────
function removeItemRow(idx) {
    const row = document.getElementById(`item-row-${idx}`);
    if (row) {
        row.remove();
        recalcTotal();
        
        // Show empty hint if no items
        const container = document.getElementById('items-container');
        const hint = document.getElementById('empty-items-hint');
        if (container.children.length === 0 && hint) {
            hint.style.display = 'block';
        }
    }
}

// ── Calculate subtotal per item ──────────────────────────────
function calculateSubtotal(idx) {
    const qtyEl = document.getElementById(`qty-${idx}`);
    const hargaEl = document.getElementById(`harga-${idx}`);
    const subtotalEl = document.getElementById(`subtotal-${idx}`);

    if (qtyEl && hargaEl && subtotalEl) {
        const qty = parseFloat(qtyEl.value) || 0;
        const harga = parseFloat(hargaEl.value) || 0;
        const subtotal = qty * harga;
        
        subtotalEl.value = subtotal;
        recalcTotal();
    }
}

// ── Calculate total from all subtotals ──────────────────────
function recalcTotal() {
    let total = 0;
    document.querySelectorAll('[name$="[subtotal]"]').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    document.getElementById('total_display').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total_nominal_input').value = total;
}

// ── Initialize form ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Add initial empty row
    addItemRow();
});
</script>

@endsection