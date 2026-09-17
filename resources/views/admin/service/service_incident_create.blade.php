@extends('admin.layouts.app')

@section('title', 'Tambah Service Incident')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="space-y-6 max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('service-incident.index') }}"
            class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors">
            <i class="fa fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Tambah Service Incident</h1>
            <p class="text-xs text-gray-500 mt-0.5">Isi header incident lalu tambahkan part yang dipasang</p>
        </div>
    </div>

    <form action="{{ route('service-incident.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- SECTION 1: HEADER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-xs">1</span>
                Informasi Incident
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Kendaraan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select name="kendaraan_id" id="kendaraan_id" required
                        onchange="onKendaraanChange(this.value)"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-400 {{ $errors->has('kendaraan_id') ? 'border-red-400' : '' }}">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->id }}"
                                data-km="{{ $k->kilometer_sekarang ?? 0 }}"
                                data-merk="{{ $k->merk }}"
                                data-nopol="{{ $k->nopol }}"
                                {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->merk }} — {{ $k->nopol }}
                            </option>
                        @endforeach
                    </select>
                    @error('kendaraan_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Service <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_service" required
                        value="{{ old('tanggal_service', now()->format('Y-m-d')) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-400">
                    @error('tanggal_service')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Kilometer --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kilometer <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="kilometer" id="kilometer" required
                            value="{{ old('kilometer') }}" placeholder="Auto dari kendaraan"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-400 pr-24">
                        <span id="km-badge" class="hidden absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-medium text-orange-600 bg-orange-50 border border-orange-200 rounded px-1.5 py-0.5">
                            <i class="fa fa-database text-[9px]"></i> Auto
                        </span>
                    </div>
                    @error('kilometer')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Keluhan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keluhan / Deskripsi Incident</label>
                    <textarea name="keluhan" rows="2" placeholder="Deskripsikan kejadian / insiden kendaraan..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-400 resize-none">{{ old('keluhan') }}</textarea>
                </div>

                {{-- Supplier dihapus dari header — dipindah per-part --}}

            </div>
        </div>

        {{-- SECTION 2: PARTS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4 mt-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-xs">2</span>
                    Part / Komponen yang Dipasang
                </h2>
                <button type="button" onclick="addPartRow()"
                    class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                    <i class="fa fa-plus text-xs"></i> Tambah Part
                </button>
            </div>

            {{-- Total biaya override --}}
            <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-xl">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="override_biaya" onchange="toggleOverrideBiaya(this)"
                        class="rounded text-orange-600">
                    <label for="override_biaya" class="text-xs font-semibold text-gray-600">Override Total Biaya</label>
                </div>
                <div class="text-xs text-gray-400">Auto-sum dari biaya per part:</div>
                <div class="font-bold text-sm text-gray-800" id="sum_biaya_display">Rp 0</div>
                <div id="override_biaya_wrap" class="hidden ml-auto">
                    <input type="number" name="total_biaya_override" id="total_biaya_override"
                        placeholder="Masukkan total manual"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-48 focus:outline-none focus:ring-2 focus:ring-orange-100">
                </div>
            </div>

            @error('parts')<div class="text-xs text-red-500">{{ $message }}</div>@enderror

            <div id="parts-container" class="space-y-4"></div>

            <div id="empty-parts-hint" class="text-center py-6 text-xs text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                <i class="fa fa-cogs text-gray-300 text-2xl mb-2 block"></i>
                Klik "+ Tambah Part" untuk menambahkan part/komponen
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex items-center justify-end gap-3 mt-5">
            <a href="{{ route('service-incident.index') }}"
                class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors">
                <i class="fa fa-paper-plane text-sm"></i> Ajukan ke PO
            </button>
        </div>

    </form>
</div>

{{-- MODAL TAMBAH SUPPLIER BARU --}}
<div id="supplierModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-800">Tambah Supplier Baru</h3>
            <button onclick="closeSupplierModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="supplierModalForm" onsubmit="submitNewSupplier(event)" class="px-6 py-4 space-y-4">
            <input type="hidden" id="supplierModalPartIdx" value="">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Supplier <span class="text-red-500">*</span></label>
                <input type="text" id="newSupplierName" required placeholder="Nama bengkel / supplier"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Telepon</label>
                <input type="text" id="newSupplierPhone" placeholder="opsional"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-400">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeSupplierModal()"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-semibold text-white bg-orange-600 rounded-xl hover:bg-orange-700 transition-colors">
                    Simpan & Pilih
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.part-row { animation: slideUp 0.15s ease; }
</style>

<script>
const categories = @json($categories->map(fn($c) => ['id' => $c->id, 'nama' => $c->nama]));
const suppliersData = @json($suppliers->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama_supplier]));
let partIndex = 0;

// ── Helper: build supplier options ────────────────────────────────────────
function buildSupplierOptions(selectedId = null) {
    let html = '<option value="">— Pilih Supplier/Bengkel —</option>';
    suppliersData.forEach(s => {
        html += `<option value="${s.id}" ${selectedId == s.id ? 'selected' : ''}>${s.nama}</option>`;
    });
    return html;
}

// ── Rebuild semua supplier select (dipanggil setelah tambah baru) ──────────
function rebuildAllSupplierSelects(newSupplier = null) {
    if (newSupplier) suppliersData.push(newSupplier);
    document.querySelectorAll('[id^="supplier-select-"]').forEach(sel => {
        const current = sel.value;
        sel.innerHTML = buildSupplierOptions(current || (newSupplier ? newSupplier.id : null));
        if (newSupplier && !current) sel.value = newSupplier.id;
    });
}

// ── Modal supplier baru ────────────────────────────────────────────────────
function openSupplierModal(partIdx) {
    document.getElementById('supplierModalPartIdx').value = partIdx;
    document.getElementById('newSupplierName').value = '';
    document.getElementById('newSupplierPhone').value = '';
    const modal = document.getElementById('supplierModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => document.getElementById('newSupplierName').focus(), 100);
}

function closeSupplierModal() {
    const modal = document.getElementById('supplierModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

async function submitNewSupplier(e) {
    e.preventDefault();
    const nama  = document.getElementById('newSupplierName').value.trim();
    const phone = document.getElementById('newSupplierPhone').value.trim();
    const idx   = document.getElementById('supplierModalPartIdx').value;

    if (!nama) return;

    try {
        const res = await fetch('/admin/supplier', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ nama_supplier: nama, no_telp: phone }),
        });

        if (!res.ok) throw new Error('Gagal menyimpan supplier');
        const data = await res.json();

        const newS = { id: data.id, nama: data.nama_supplier };
        suppliersData.push(newS);

        // Update hanya select di baris yang meminta
        const sel = document.getElementById('supplier-select-' + idx);
        if (sel) {
            const opt = document.createElement('option');
            opt.value = newS.id;
            opt.textContent = newS.nama;
            opt.selected = true;
            sel.appendChild(opt);
        }
        // Juga inject ke semua select lain (tanpa mengubah pilihan)
        document.querySelectorAll('[id^="supplier-select-"]').forEach(s => {
            if (s.id !== 'supplier-select-' + idx) {
                const o = document.createElement('option');
                o.value = newS.id;
                o.textContent = newS.nama;
                s.appendChild(o);
            }
        });

        closeSupplierModal();
    } catch (err) {
        alert('Gagal menyimpan supplier: ' + err.message);
    }
}

// ── Add Part Row ───────────────────────────────────────────────────────────
function addPartRow(data = null) {
    const container = document.getElementById('parts-container');
    const hint      = document.getElementById('empty-parts-hint');
    hint.style.display = 'none';

    const idx = partIndex++;
    const row = document.createElement('div');
    row.className = 'part-row bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3 relative';
    row.id        = 'part-row-' + idx;

    let catOptions = '<option value="">— Pilih Kategori —</option>';
    categories.forEach(c => {
        catOptions += `<option value="${c.id}" ${data?.category_id == c.id ? 'selected' : ''}>${c.nama}</option>`;
    });

    const supplierOpts = buildSupplierOptions(data?.supplier_id ?? null);

    row.innerHTML = `
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-bold text-gray-600">Part #${idx + 1}</span>
            <button type="button" onclick="removePartRow(${idx})"
                class="w-6 h-6 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center text-xs transition-colors">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">

            <!-- Nama Part -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Part <span class="text-red-400">*</span></label>
                <input type="text" name="parts[${idx}][nama_part]" required
                    value="${data?.nama_part || ''}"
                    placeholder="cth: Ban Depan, Kaca Spion..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- Kategori -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Kategori</label>
                <select name="parts[${idx}][category_id]" id="cat-select-${idx}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
                    ${catOptions}
                </select>
                <input type="hidden" name="parts[${idx}][nama_category_baru]" id="cat-new-${idx}" value="">
            </div>

            <!-- Posisi -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Posisi</label>
                <select name="parts[${idx}][posisi]"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
                    <option value="">-- Pilih Posisi --</option>
                    <optgroup label="Eksterior Depan">
                        <option value="Bumper Depan">Bumper Depan</option>
                        <option value="Kap Mesin">Kap Mesin</option>
                        <option value="Lampu Depan Kiri">Lampu Depan Kiri</option>
                        <option value="Lampu Depan Kanan">Lampu Depan Kanan</option>
                        <option value="Spion Kiri">Spion Kiri</option>
                        <option value="Spion Kanan">Spion Kanan</option>
                    </optgroup>
                    <optgroup label="Eksterior Samping">
                        <option value="Pintu Depan Kiri">Pintu Depan Kiri</option>
                        <option value="Pintu Depan Kanan">Pintu Depan Kanan</option>
                        <option value="Pintu Belakang Kiri">Pintu Belakang Kiri</option>
                        <option value="Pintu Belakang Kanan">Pintu Belakang Kanan</option>
                        <option value="Fender Depan Kiri">Fender Depan Kiri</option>
                        <option value="Fender Depan Kanan">Fender Depan Kanan</option>
                    </optgroup>
                    <optgroup label="Eksterior Belakang">
                        <option value="Bumper Belakang">Bumper Belakang</option>
                        <option value="Bagasi">Bagasi</option>
                        <option value="Lampu Belakang Kiri">Lampu Belakang Kiri</option>
                        <option value="Lampu Belakang Kanan">Lampu Belakang Kanan</option>
                    </optgroup>
                    <optgroup label="Kaca">
                        <option value="Kaca Depan">Kaca Depan</option>
                        <option value="Kaca Belakang">Kaca Belakang</option>
                        <option value="Kaca Pintu Depan Kiri">Kaca Pintu Depan Kiri</option>
                        <option value="Kaca Pintu Depan Kanan">Kaca Pintu Depan Kanan</option>
                        <option value="Kaca Pintu Belakang Kiri">Kaca Pintu Belakang Kiri</option>
                        <option value="Kaca Pintu Belakang Kanan">Kaca Pintu Belakang Kanan</option>
                    </optgroup>
                    <optgroup label="Mesin">
                        <option value="Mesin">Mesin</option>
                        <option value="Radiator">Radiator</option>
                        <option value="Aki/Battery">Aki/Battery</option>
                        <option value="Filter Udara">Filter Udara</option>
                        <option value="Filter Oli">Filter Oli</option>
                    </optgroup>
                    <optgroup label="Ban & Velg">
                        <option value="Ban Depan Kiri">Ban Depan Kiri</option>
                        <option value="Ban Depan Kanan">Ban Depan Kanan</option>
                        <option value="Ban Belakang Kiri">Ban Belakang Kiri</option>
                        <option value="Ban Belakang Kanan">Ban Belakang Kanan</option>
                    </optgroup>
                    <optgroup label="Kaki-kaki & Suspensi">
                        <option value="Shock Absorber Depan Kiri">Shock Absorber Depan Kiri</option>
                        <option value="Shock Absorber Depan Kanan">Shock Absorber Depan Kanan</option>
                        <option value="Shock Absorber Belakang Kiri">Shock Absorber Belakang Kiri</option>
                        <option value="Shock Absorber Belakang Kanan">Shock Absorber Belakang Kanan</option>
                    </optgroup>
                    <optgroup label="Rem">
                        <option value="Brake Pad Depan">Brake Pad Depan</option>
                        <option value="Brake Pad Belakang">Brake Pad Belakang</option>
                        <option value="Disc Brake Depan Kiri">Disc Brake Depan Kiri</option>
                        <option value="Disc Brake Depan Kanan">Disc Brake Depan Kanan</option>
                    </optgroup>
                    <optgroup label="AC & Interior">
                        <option value="Kompresor AC">Kompresor AC</option>
                        <option value="Evaporator AC">Evaporator AC</option>
                        <option value="Dashboard">Dashboard</option>
                        <option value="Jok Depan Kiri">Jok Depan Kiri</option>
                        <option value="Jok Depan Kanan">Jok Depan Kanan</option>
                        <option value="Jok Belakang">Jok Belakang</option>
                    </optgroup>
                    <optgroup label="Lain-lain">
                        <option value="Umum">Umum</option>
                        <option value="Keseluruhan">Keseluruhan</option>
                    </optgroup>
                </select>
            </div>

            <!-- Part Number -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Part Number</label>
                <input type="text" name="parts[${idx}][part_number]"
                    value="${data?.part_number || ''}" placeholder="No. part (opsional)"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- Serial Number -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Serial Number</label>
                <input type="text" name="parts[${idx}][serial_number]"
                    value="" placeholder="SN part"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- Tgl Pasang -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Tgl Pasang <span class="text-red-400">*</span></label>
                <input type="date" name="parts[${idx}][tgl_pasang]" required
                    value="{{ now()->format('Y-m-d') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- KM Pasang -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">KM Pasang</label>
                <input type="number" name="parts[${idx}][kilometer_pasang]" id="km-pasang-${idx}"
                    value="" placeholder="Auto dari header KM"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- Interval — read-only, diisi otomatis dari kategori. Input hidden + tampilan div -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Interval</label>
                <div class="flex gap-1 items-center">
                    <div id="interval-display-${idx}"
                        class="flex-1 bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 font-mono min-h-[38px] flex items-center">
                        <span id="interval-text-${idx}" class="text-gray-400 text-xs italic">— auto dari kategori —</span>
                    </div>
                    <input type="hidden" name="parts[${idx}][interval_nilai]"   id="interval-nilai-${idx}"   value="12">
                    <input type="hidden" name="parts[${idx}][interval_satuan]"  id="interval-satuan-${idx}"  value="bulan">
                </div>
            </div>

            <!-- Kondisi -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Kondisi</label>
                <select name="parts[${idx}][kondisi]"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
                    <option value="Baik">Baik</option>
                    <option value="Rusak">Rusak</option>
                    <option value="Perlu Ganti">Perlu Ganti</option>
                </select>
            </div>

            <!-- Status Part DIHAPUS — otomatis Proses -->
            <input type="hidden" name="parts[${idx}][status]" value="Proses">

            <!-- Biaya -->
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Biaya (Rp)</label>
                <input type="number" name="parts[${idx}][biaya]" id="biaya-${idx}" min="0"
                    value="0" onchange="recalcTotal()" oninput="recalcTotal()"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>

            <!-- Supplier per-part -->
            <div class="md:col-span-2">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">
                    Supplier / Bengkel
                    <span class="text-gray-400 text-[10px] font-normal ml-1">(opsional)</span>
                </label>
                <div class="flex gap-2">
                    <select name="parts[${idx}][supplier_id]" id="supplier-select-${idx}"
                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100">
                        ${supplierOpts}
                    </select>
                    <button type="button" onclick="openSupplierModal(${idx})"
                        class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold text-orange-600 border border-orange-200 rounded-lg hover:bg-orange-50 transition-colors whitespace-nowrap">
                        <i class="fa fa-plus text-xs"></i> Baru
                    </button>
                </div>
            </div>

            <!-- Keterangan Limit -->
            <div class="md:col-span-3">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Keterangan Limit</label>
                <textarea name="parts[${idx}][keterangan_limit]" rows="2"
                    placeholder="Catatan kondisi, penyebab kerusakan, dll..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 resize-none"></textarea>
            </div>

            <!-- Info Pembayaran -->
            <div class="md:col-span-3">
                <div class="border border-dashed border-orange-200 rounded-xl p-3 bg-orange-50/40 space-y-3">
                    <p class="text-[10px] font-semibold text-orange-600 uppercase tracking-wide flex items-center gap-1.5">
                        <i class="fa fa-university text-[10px]"></i> Info Pembayaran
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Rekening</label>
                            <input type="text" name="parts[${idx}][nama_rekening]"
                                placeholder="cth: Budi Santoso"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 bg-white">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 mb-1 block">Nama Bank</label>
                            <input type="text" name="parts[${idx}][nama_bank]"
                                placeholder="cth: BCA, Mandiri..."
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 bg-white">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 mb-1 block">No. Rekening</label>
                            <input type="text" name="parts[${idx}][no_rekening]"
                                placeholder="cth: 1234567890"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-100 bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bukti -->
            <div class="md:col-span-3">
                <label class="text-xs font-semibold text-gray-500 mb-1 block">
                    Bukti / Attachment
                    <span class="text-[10px] font-normal text-gray-400 ml-1">(opsional — foto kerusakan, nota bengkel, dll)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer border border-dashed border-orange-300 hover:border-orange-400 bg-gray-50 hover:bg-orange-50/40 rounded-lg px-3 py-2.5 transition-colors group">
                    <i class="fa fa-paperclip text-orange-400 group-hover:text-orange-500 text-sm transition-colors"></i>
                    <span class="text-xs text-gray-500 group-hover:text-orange-600 transition-colors">Klik untuk pilih file...</span>
                    <input type="file" id="bukti-input-${idx}" name="parts[${idx}][bukti][]"
                        multiple accept="image/*,video/mp4,video/mov"
                        onchange="updateFileList(${idx})"
                        class="hidden">
                </label>
                <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, MP4, MOV</p>
                <div id="bukti-list-${idx}" class="mt-2 space-y-1"></div>
            </div>

        </div>
    `;

    container.appendChild(row);
    syncKmPasang(idx);
    // Bind onchange kategori untuk auto-fill interval
    const catSel = document.getElementById('cat-select-' + idx);
    if (catSel) catSel.addEventListener('change', () => onCategoryChange(idx, catSel.value));
    recalcTotal();
}

// ── Auto-fill interval dari limit rule kategori ────────────────────────────
async function onCategoryChange(idx, categoryId) {
    const textEl   = document.getElementById('interval-text-' + idx);
    const nilaiEl  = document.getElementById('interval-nilai-' + idx);
    const satuanEl = document.getElementById('interval-satuan-' + idx);
    if (!categoryId) {
        textEl.textContent = '— auto dari kategori —';
        textEl.classList.add('text-gray-400', 'italic');
        nilaiEl.value  = '12';
        satuanEl.value = 'bulan';
        return;
    }
    try {
        const kendaraanId = document.getElementById('kendaraan_id').value;
        const url = `/admin/service-categories/limit-for?category_id=${categoryId}` +
                    (kendaraanId ? `&kendaraan_id=${kendaraanId}` : '');
        const res  = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (data && data.interval_nilai && data.interval_satuan) {
            nilaiEl.value  = data.interval_nilai;
            satuanEl.value = data.interval_satuan;
            textEl.textContent = data.interval_nilai + ' ' + data.interval_satuan;
            textEl.classList.remove('text-gray-400', 'italic');
            textEl.classList.add('text-orange-700', 'font-semibold');
        } else {
            textEl.textContent = '— tidak ada aturan —';
            textEl.classList.add('text-gray-400', 'italic');
        }
    } catch {
        textEl.textContent = '— gagal memuat —';
    }
}

function updateFileList(idx) {
    const input  = document.getElementById('bukti-input-' + idx);
    const listEl = document.getElementById('bukti-list-' + idx);
    if (!input || !listEl) return;

    const files = Array.from(input.files);
    listEl.innerHTML = '';
    if (files.length === 0) return;

    files.forEach(function(file, fileIdx) {
        const size = file.size < 1024 * 1024
            ? (file.size / 1024).toFixed(1) + ' KB'
            : (file.size / 1024 / 1024).toFixed(1) + ' MB';

        const icon = file.type.startsWith('image/')
            ? '<i class="fa fa-image text-orange-400 text-xs w-4 text-center"></i>'
            : file.type.startsWith('video/')
                ? '<i class="fa fa-film text-purple-400 text-xs w-4 text-center"></i>'
                : '<i class="fa fa-file text-gray-400 text-xs w-4 text-center"></i>';

        const item = document.createElement('div');
        item.id        = 'bukti-item-' + idx + '-' + fileIdx;
        item.className = 'flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs';
        item.innerHTML = icon +
            '<span class="flex-1 truncate text-gray-700 font-medium" title="' + file.name + '">' + file.name + '</span>' +
            '<span class="text-gray-400 text-[10px] flex-shrink-0">' + size + '</span>' +
            '<button type="button" onclick="removePartFile(' + idx + ', ' + fileIdx + ')" ' +
                'class="w-5 h-5 rounded flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors flex-shrink-0">' +
                '<i class="fa fa-times text-[10px]"></i></button>';
        listEl.appendChild(item);
    });
}

function removePartFile(idx, fileIdx) {
    const input = document.getElementById('bukti-input-' + idx);
    if (!input) return;
    const dt    = new DataTransfer();
    const files = Array.from(input.files);
    files.forEach(function(file, i) { if (i !== fileIdx) dt.items.add(file); });
    input.files = dt.files;
    updateFileList(idx);
}

function removePartRow(idx) {
    const row = document.getElementById('part-row-' + idx);
    if (row) row.remove();
    recalcTotal();
    const container = document.getElementById('parts-container');
    if (container.children.length === 0) {
        document.getElementById('empty-parts-hint').style.display = '';
    }
}

function syncKmPasang(idx) {
    const headerKm = document.getElementById('kilometer').value;
    const kmInput  = document.getElementById('km-pasang-' + idx);
    if (kmInput && !kmInput.value && headerKm) kmInput.value = headerKm;
}

function recalcTotal() {
    let sum = 0;
    document.querySelectorAll('[name$="[biaya]"]').forEach(input => {
        sum += parseInt(input.value || 0);
    });
    document.getElementById('sum_biaya_display').textContent = 'Rp ' + sum.toLocaleString('id-ID');
}

function toggleOverrideBiaya(cb) {
    const wrap = document.getElementById('override_biaya_wrap');
    wrap.classList.toggle('hidden', !cb.checked);
    if (!cb.checked) document.getElementById('total_biaya_override').value = '';
}

function onKendaraanChange(val) {
    const sel = document.getElementById('kendaraan_id');
    const opt = Array.from(sel.options).find(o => o.value == val);
    if (opt) {
        const km = opt.dataset.km || 0;
        document.getElementById('kilometer').value = km;
        document.getElementById('km-badge').classList.remove('hidden');
    } else {
        document.getElementById('km-badge').classList.add('hidden');
    }
}

document.getElementById('kilometer').addEventListener('input', function() {
    document.getElementById('km-badge').classList.add('hidden');
    document.querySelectorAll('[id^="km-pasang-"]').forEach(input => {
        if (!input.dataset.userEdited) input.value = this.value;
    });
});
</script>

{{-- ALERT --}}
@if (session('success') || session('error') || $errors->any())
<div id="alertOverlay" class="fixed inset-0 z-[9999] flex items-start justify-center pt-6"
    style="background:rgba(0,0,0,0.18);opacity:0;transition:opacity 0.2s;pointer-events:none">
    <div id="alertBox" class="bg-white rounded-xl shadow-xl border border-gray-100 px-5 py-4 flex items-start gap-3 w-full max-w-md mx-4"
        style="transform:translateY(-16px);transition:transform 0.25s">
        @if (session('success'))
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600 text-xl"><i class="fa fa-check-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Berhasil!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p></div>
        @elseif (session('error'))
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Error!</p><p class="text-xs text-gray-500 mt-0.5">{{ session('error') }}</p></div>
        @else
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500 text-xl"><i class="fa fa-exclamation-circle"></i></div>
            <div class="flex-1"><p class="text-sm font-bold text-gray-800">Validasi Error!</p><ul class="text-xs text-gray-500 mt-0.5 list-disc ml-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <button onclick="closeAlert()" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fa fa-times"></i></button>
    </div>
</div>
<script>
(function() {
    const overlay = document.getElementById('alertOverlay');
    if (!overlay) return;
    const box = document.getElementById('alertBox');
    setTimeout(() => { overlay.style.opacity='1'; overlay.style.pointerEvents='auto'; box.style.transform='translateY(0)'; }, 50);
    const timer = setTimeout(() => closeAlert(), 5000);
    function closeAlert() { clearTimeout(timer); overlay.style.opacity='0'; overlay.style.pointerEvents='none'; box.style.transform='translateY(-16px)'; }
    window.closeAlert = closeAlert;
})();
</script>
@endif

@endsection
