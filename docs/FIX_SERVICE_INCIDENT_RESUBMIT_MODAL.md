# Fix: Modal Ajukan Ulang Service Incident

## Issue
Modal "Ajukan Ulang" untuk Service Incident fieldnya masih terbatas (hanya Nama Part + Biaya). Seharusnya ada field lengkap seperti form create Service Incident.

## Required Changes

### 1. Backend: Filter Hanya Parts Yang Rejected ✅ (DONE)
**File:** `app/Http/Controllers/Admin/PurchaseOrderController.php` - method `resubmit`

**Changes Applied:**
- Filter parts berdasarkan `item_decisions` → hanya return parts yang `action === 'rejected'`
- Return full part fields: category_id, supplier_id, part_number, serial_number, posisi, tgl_pasang, kilometer_pasang, kondisi, nama_bank, no_rekening, nama_rekening, keterangan
- Re-index array dengan `array_values()` agar modal dapat loop dengan index sequential

---

### 2. Frontend: Field Lengkap di Modal

**File:** `resources/views/admin/purchaseo/index.blade.php`

#### A. Update `renderRsaKejadian` Function (Line ~2743)

**Current Fields (Service Incident):**
```javascript
- Nama Part (text)
- Biaya (number)
- Part Number (text)
- Kondisi (select)
- Posisi (text)
- Nama Bank, No. Rekening, Nama Rekening (text)
- Keterangan (textarea)
```

**Missing Fields yang Perlu Ditambahkan:**
```javascript
1. Kategori (select dropdown)
   - Fetch dari: GET /api/service-categories
   - Field: category_id
   - Options: _rsaCategories

2. Posisi (select dropdown) - currently text input
   - Options: Depan Kanan, Depan Kiri, Belakang Kanan, Belakang Kiri, Tengah

3. Serial Number (text input)
   - Field: serial_number

4. Tgl Pasang (date input) - REQUIRED
   - Field: tgl_pasang
   - Default: tanggal_service dari PO

5. KM Pasang (number input)
   - Field: kilometer_pasang
   - Default: kilometer dari PO

6. Supplier / Bengkel (select dropdown)
   - Fetch dari: GET /api/suppliers
   - Field: supplier_id
   - Options: _rsaSuppliers
   - Include "+ Baru" button to add inline

7. Lampiran (file input)
   - Field: kejadians[idx][lampiran][]
   - Multiple files allowed
```

---

### 3. API Endpoints for Master Data

Buat route API sederhana untuk fetch categories dan suppliers:

**File:** `routes/api.php` (or add to web.php with /api prefix)

```php
// Service Categories
Route::get('/service-categories', function() {
    $categories = \App\Models\ServiceCategory::select('id', 'nama')->get();
    return response()->json(['categories' => $categories]);
});

// Suppliers
Route::get('/suppliers', function() {
    $suppliers = \App\Models\Supplier::select('id', 'nama_supplier')->get();
    return response()->json(['suppliers' => $suppliers]);
});
```

---

### 4. Updated Modal HTML Structure

**Template untuk Service Incident Part Card:**

```html
<div id="rsa-kej-{idx}" class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-gray-600">Part #{idx + 1}</span>
        <button type="button" onclick="removeRsaKejadian({idx})"
            class="w-6 h-6 rounded-lg bg-red-100 text-red-500 hover:bg-red-200">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <!-- Row 1: Nama Part + Kategori -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label>Nama Part <span class="text-red-400">*</span></label>
            <input type="text" name="kejadians[{idx}][nama_kejadian]" required
                value="{kej.nama_part}"
                placeholder="cth: Ban Depan, Kaca Spion...">
        </div>
        <div>
            <label>Kategori</label>
            <select name="kejadians[{idx}][category_id]">
                <option value="">— Pilih Kategori —</option>
                {foreach _rsaCategories as cat}
                    <option value="{cat.id}" {selected if kej.category_id === cat.id}>
                        {cat.nama}
                    </option>
                {endforeach}
            </select>
        </div>
    </div>

    <!-- Row 2: Posisi + Part Number + Serial Number -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label>Posisi</label>
            <select name="kejadians[{idx}][posisi]">
                <option value="">-- Pilih Posisi --</option>
                <option value="Depan Kanan">Depan Kanan</option>
                <option value="Depan Kiri">Depan Kiri</option>
                <option value="Belakang Kanan">Belakang Kanan</option>
                <option value="Belakang Kiri">Belakang Kiri</option>
                <option value="Tengah">Tengah</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        <div>
            <label>Part Number</label>
            <input type="text" name="kejadians[{idx}][part_number]"
                value="{kej.part_number}"
                placeholder="No. part (opsional)">
        </div>
        <div>
            <label>Serial Number</label>
            <input type="text" name="kejadians[{idx}][serial_number]"
                value="{kej.serial_number}"
                placeholder="SN part">
        </div>
    </div>

    <!-- Row 3: Tgl Pasang + KM Pasang + Kondisi -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label>Tgl Pasang <span class="text-red-400">*</span></label>
            <input type="date" name="kejadians[{idx}][tgl_pasang]" required
                value="{kej.tgl_pasang || data.tanggal_service}">
        </div>
        <div>
            <label>KM Pasang</label>
            <input type="number" name="kejadians[{idx}][kilometer_pasang]"
                value="{kej.kilometer_pasang || data.kilometer}"
                min="0">
        </div>
        <div>
            <label>Kondisi</label>
            <select name="kejadians[{idx}][kondisi]">
                <option value="Baik" {selected if kej.kondisi === 'Baik'}>Baik</option>
                <option value="Rusak" {selected if kej.kondisi === 'Rusak'}>Rusak</option>
                <option value="Perlu Ganti" {selected if kej.kondisi === 'Perlu Ganti'}>Perlu Ganti</option>
            </select>
        </div>
    </div>

    <!-- Row 4: Biaya + Supplier -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label>Biaya (Rp) <span class="text-red-400">*</span></label>
            <input type="number" name="kejadians[{idx}][biaya]" required
                value="{kej.biaya}"
                min="0"
                onchange="updateRsaTotalBiaya()"
                oninput="updateRsaTotalBiaya()">
        </div>
        <div>
            <label>Supplier / Bengkel (opsional)</label>
            <div class="flex gap-2">
                <select name="kejadians[{idx}][supplier_id]" class="flex-1">
                    <option value="">— Pilih Supplier/Bengkel —</option>
                    {foreach _rsaSuppliers as supplier}
                        <option value="{supplier.id}" {selected if kej.supplier_id === supplier.id}>
                            {supplier.nama_supplier}
                        </option>
                    {endforeach}
                </select>
                <button type="button" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600"
                    onclick="alert('Feature tambah supplier inline belum tersedia')">
                    Baru
                </button>
            </div>
        </div>
    </div>

    <!-- Info Pembayaran -->
    <div>
        <label class="text-xs font-semibold text-gray-600 mb-2 block">Info Pembayaran</label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label>Nama Rekening</label>
                <input type="text" name="kejadians[{idx}][nama_rekening]"
                    value="{kej.nama_rekening}"
                    placeholder="cth: Budi Santoso">
            </div>
            <div>
                <label>Nama Bank</label>
                <input type="text" name="kejadians[{idx}][nama_bank]"
                    value="{kej.nama_bank}"
                    placeholder="cth: BCA, Mandiri...">
            </div>
            <div>
                <label>No. Rekening</label>
                <input type="text" name="kejadians[{idx}][no_rekening]"
                    value="{kej.no_rekening}"
                    placeholder="cth: 1234567890">
            </div>
        </div>
    </div>

    <!-- Lampiran -->
    <div>
        <label>Lampiran (opsional — foto kerusakan, nota bengkel, dll)</label>
        <input type="file" name="kejadians[{idx}][lampiran][]"
            multiple
            accept="image/*,.pdf"
            class="w-full border rounded-lg px-3 py-2 text-sm">
        
        <!-- Lampiran existing -->
        {if kej.lampiran_existing && kej.lampiran_existing.length > 0}
            <div class="mt-1 space-y-1">
                {foreach kej.lampiran_existing as lf}
                    <a href="/storage/{lf.path}" target="_blank"
                        class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline">
                        <i class="fa fa-paperclip"></i>
                        <span>{lf.original_name}</span>
                    </a>
                {endforeach}
            </div>
        {endif}
    </div>
</div>
```

---

### 5. JavaScript Variables

Add global variables at top of modal JS:

```javascript
let _rsaCategories = [];
let _rsaSuppliers = [];
```

Update `openResubmitServiceAsuransiModal` to fetch master data:

```javascript
Promise.all([
    fetch('/admin/purchase-order/' + poId + '/resubmit', {...}),
    fetch('/api/service-categories').then(r => r.json()),
    fetch('/api/suppliers').then(r => r.json()),
])
.then(([poData, catData, suppData]) => {
    _rsaCategories = catData.categories || [];
    _rsaSuppliers = suppData.suppliers || [];
    renderRsaForm(poData);
    ...
});
```

---

## Implementation Steps

1. ✅ **Backend filter rejected parts** - DONE
2. ⏳ **Create API routes** for categories & suppliers
3. ⏳ **Update frontend JS** to fetch master data
4. ⏳ **Update `renderRsaKejadian` function** with full fields template
5. ⏳ **Test** resubmit flow dengan field lengkap

---

## Testing Checklist

- [ ] Modal hanya menampilkan parts yang rejected
- [ ] Semua fields muncul dengan benar
- [ ] Dropdown kategori terisi dari API
- [ ] Dropdown supplier terisi dari API
- [ ] Dropdown posisi ada 6 options
- [ ] Tgl Pasang default = tanggal_service
- [ ] KM Pasang default = kilometer dari PO
- [ ] Biaya required dan trigger updateTotalBiaya
- [ ] Upload lampiran multiple files working
- [ ] Submit form berhasil dengan data lengkap

---

**Status:** Dokumentasi created, implementasi frontend pending (kompleks, butuh banyak update JS)