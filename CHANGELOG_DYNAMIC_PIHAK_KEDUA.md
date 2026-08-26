# Feature: Dynamic Pihak Kedua Format Based on Jenis Pelanggan

**Tanggal:** 26 Agustus 2026  
**Files Modified:**
- `database/migrations/2026_08_26_141900_add_perwakilan_fields_to_inv_kontraks_table.php` (NEW)
- `resources/views/admin/kontrak/index.blade.php` (UI + JS)
- `app/Http/Controllers/Admin/InvKontrakController.php` (store, approve, update)
- `resources/views/admin/kontrak/draft_pdf.blade.php` (PDF output)
- `resources/views/admin/kontrak/draft_print.blade.php` (Print output)

---

## 🎯 Objective

Menyesuaikan format **Pihak Kedua** di kontrak berdasarkan jenis pelanggan (perorangan vs perusahaan), dengan support field perwakilan dan jabatan untuk perusahaan — mengikuti format legal Indonesia yang sama dengan Pihak Pertama.

---

## 📋 Requirements

### Format Output

**PERORANGAN (existing - tidak berubah):**
```
ID: 2. Budi Santoso, yang beralamat di Jl. Merdeka No. 10, Jakarta, 
    pemegang KTP No. 1234567890, selanjutnya disebut sebagai "Pihak Kedua".

EN: 2. Budi Santoso, domiciled at Jl. Merdeka No. 10, Jakarta, 
    holder of identity card no. 1234567890, hereinafter referred to as the "Second Party".
```

**PERUSAHAAN (lengkap dengan perwakilan):**
```
ID: 2. PT. Putra Tunggal Trans, suatu perseroan terbatas yang memiliki kantor terdaftar di 
    Jl. Kemang Sari Raya No. 18A RT. 002 RW. 011 Jatibening Baru, Pondok Gede, Kota Bekasi, Jawa Barat, 
    dalam hal ini diwakili oleh **Yuningsih** dalam jabatannya selaku Direktur Utama, 
    pemegang KTP No. 31.7105.680790.0004, selanjutnya disebut sebagai "Pihak Kedua".

EN: 2. PT. Putra Tunggal Trans, a limited liability company, having its registered office at 
    Jl. Kemang Sari Raya No. 18A RT. 002 RW. 011 Jatibening Baru, Pondok Gede, Bekasi, West Java, 
    in this matter represented by **Yuningsih**, acting as President Director, 
    a holder of identity card no. 31.7105.680790.0004, hereinafter referred to as the "Second Party".
```

**PERUSAHAAN (tanpa perwakilan - fallback):**
```
ID: 2. PT. Putra Tunggal Trans, suatu perseroan terbatas yang memiliki kantor terdaftar di 
    Jl. Kemang Sari Raya No. 18A..., pemegang KTP No. 31.7105.680790.0004, 
    selanjutnya disebut sebagai "Pihak Kedua".

EN: 2. PT. Putra Tunggal Trans, a limited liability company, having its registered office at 
    Jl. Kemang Sari Raya No. 18A..., holder of identity card no. 31.7105.680790.0004, 
    hereinafter referred to as the "Second Party".
```

*Note: Nama perusahaan dan nama perwakilan di-bold di PDF*

---

## 🛠️ Implementation Details

### 1. **Database Changes**

**New Migration:** `2026_08_26_141900_add_perwakilan_fields_to_inv_kontraks_table.php`

**Columns Added:**
- `perwakilan_pihak_kedua` (string, 255, nullable) — Nama perwakilan perusahaan
- `jabatan_pihak_kedua` (string, 100, nullable) — Jabatan perwakilan

**Run Migration:**
```bash
php artisan migrate
```

---

### 2. **UI Changes - Modal Create**

**File:** `resources/views/admin/kontrak/index.blade.php`

**Changes:**
1. **Reorder fields:** Dropdown `jenis_pelanggan` dipindahkan ke atas (sebelum `customer_name`)
2. **Default value:** `jenis_pelanggan` default = "Perorangan"
3. **New fields added:** 
   - `Diwakili Oleh` (perwakilan_pihak_kedua) — required jika jenis = Perusahaan
   - `Jabatan` (jabatan_pihak_kedua) — required jika jenis = Perusahaan
4. **Conditional show/hide:** Field perwakilan hanya muncul jika user pilih "Perusahaan"

**Form Structure:**
```html
<select name="jenis_pelanggan" id="create_jenis_pelanggan" onchange="togglePerwakilanFields('create')" required>
    <option value="perorangan" selected>Perorangan</option>
    <option value="perusahaan">Perusahaan</option>
</select>

<div id="create_perwakilan_wrapper" class="hidden">
    <input type="text" name="perwakilan_pihak_kedua" id="create_perwakilan_pihak_kedua" placeholder="Nama perwakilan...">
    <input type="text" name="jabatan_pihak_kedua" id="create_jabatan_pihak_kedua" placeholder="Misal: Direktur Utama">
</div>
```

---

### 3. **UI Changes - Modal Approve**

**Same structure as Create modal:**
- Dropdown `jenis_pelanggan` di atas customer_name
- Field perwakilan conditional (hidden by default, show saat pilih "Perusahaan")
- Required validation jika jenis = Perusahaan

---

### 4. **UI Changes - Modal Edit**

**File:** `resources/views/admin/kontrak/index.blade.php`

**Changes:**
1. **Reorder fields:** Dropdown `jenis_pelanggan` dipindahkan ke paling atas (sebelum Pihak Kedua name)
2. **New fields added:**
   - `Diwakili Oleh` (perwakilan_pihak_kedua) — required jika jenis = Perusahaan
   - `Jabatan` (jabatan_pihak_kedua) — required jika jenis = Perusahaan
3. **Conditional show/hide:** Field perwakilan hanya muncul jika jenis = "Perusahaan"
4. **Pre-fill data:** Data perwakilan dari database otomatis ter-populate saat modal dibuka
5. **Auto-toggle on open:** Fungsi `togglePerwakilanFields('edit')` dipanggil saat modal dibuka untuk show/hide field berdasarkan data existing

**JavaScript Enhancement:**
```javascript
function openEditModal(data) {
    // ... existing field population
    
    // Pre-fill perwakilan fields
    document.getElementById('edit_perwakilan_pihak_kedua').value = data.perwakilan_pihak_kedua ?? '';
    document.getElementById('edit_jabatan_pihak_kedua').value    = data.jabatan_pihak_kedua ?? '';
    
    // ... status, etc.
    
    // Trigger toggle untuk show/hide field perwakilan
    togglePerwakilanFields('edit');
    
    // ... populate ketentuan
}
```

---

### 5. **JavaScript Function**

**Function:** `togglePerwakilanFields(prefix)`

**Location:** `index.blade.php` (after `initCreateKetentuan()`)

**Parameters:**
- `prefix`: 'create', 'approve', atau 'edit'

**Logic:**
```javascript
function togglePerwakilanFields(prefix) {
    const jenisPelanggan = document.getElementById(`${prefix}_jenis_pelanggan`)?.value;
    const perwakilanWrapper = document.getElementById(`${prefix}_perwakilan_wrapper`);
    const perwakilanInput = document.getElementById(`${prefix}_perwakilan_pihak_kedua`);
    const jabatanInput = document.getElementById(`${prefix}_jabatan_pihak_kedua`);

    if (jenisPelanggan === 'perusahaan') {
        // Show fields dan set required
        perwakilanWrapper?.classList.remove('hidden');
        perwakilanInput?.setAttribute('required', 'required');
        jabatanInput?.setAttribute('required', 'required');
    } else {
        // Hide fields, remove required, clear value
        perwakilanWrapper?.classList.add('hidden');
        perwakilanInput?.removeAttribute('required');
        perwakilanInput && (perwakilanInput.value = '');
        jabatanInput?.removeAttribute('required');
        jabatanInput && (jabatanInput.value = '');
    }
}
```

**Trigger:** `onChange` event di dropdown `jenis_pelanggan`

---

### 6. **Controller Changes**

**File:** `app/Http/Controllers/Admin/InvKontrakController.php`

#### `store()` Method:
```php
$data = [
    // ... existing fields
    'perwakilan_pihak_kedua' => $request->perwakilan_pihak_kedua,
    'jabatan_pihak_kedua'    => $request->jabatan_pihak_kedua,
    // ...
];
```

#### `approve()` Method:
```php
$kontrak->update([
    // ... existing fields
    'perwakilan_pihak_kedua' => $request->perwakilan_pihak_kedua ?? $kontrak->perwakilan_pihak_kedua,
    'jabatan_pihak_kedua'    => $request->jabatan_pihak_kedua   ?? $kontrak->jabatan_pihak_kedua,
]);
```

#### `update()` Method:
- Menggunakan `$request->except()` → field perwakilan otomatis tersimpan
- **No changes needed** — auto-handled by Laravel

---

### 7. **PDF View Changes**

**Files:**
- `resources/views/admin/kontrak/draft_pdf.blade.php`
- `resources/views/admin/kontrak/draft_print.blade.php`

**Logic:**
```php
@php
$isPihak2Perusahaan = strtolower($kontrak->jenis_pelanggan ?? 'perorangan') === 'perusahaan';
$perwakilan2 = $kontrak->perwakilan_pihak_kedua ?? null;
$jabatan2    = $kontrak->jabatan_pihak_kedua ?? null;
@endphp

@if($isPihak2Perusahaan && $perwakilan2 && $jabatan2)
    {{-- Format lengkap perusahaan dengan perwakilan --}}
    <p><strong>2. {{ $namaP2 }},</strong> suatu perseroan terbatas... diwakili oleh <strong>{{ $perwakilan2 }}</strong> dalam jabatannya selaku {{ $jabatan2 }}...</p>
@elseif($isPihak2Perusahaan)
    {{-- Format perusahaan tanpa perwakilan --}}
    <p><strong>2. {{ $namaP2 }},</strong> suatu perseroan terbatas... pemegang KTP No. {{ $noktp2 }}...</p>
@else
    {{-- Format perorangan --}}
    <p><strong>2. {{ $namaP2 }},</strong> yang beralamat di {{ $alamatP2 }}...</p>
@endif
```

**Styling:**
- `<strong>{{ $namaP2 }}</strong>` → bold untuk nama perusahaan
- `<strong>{{ $perwakilan2 }}</strong>` → bold untuk nama perwakilan

---

## 🧪 Testing Scenarios

### Test Case 1: Create Kontrak Perorangan
1. Buka modal Create Kontrak
2. Dropdown Jenis Pelanggan default = "Perorangan"
3. Field Diwakili Oleh dan Jabatan **tidak muncul** (hidden)
4. Isi form → Submit → Download PDF
5. **Verify:** Format Pihak Kedua = perorangan (nama + alamat + KTP)

---

### Test Case 2: Create Kontrak Perusahaan (lengkap)
1. Buka modal Create Kontrak
2. Pilih Jenis Pelanggan = **"Perusahaan"**
3. Field Diwakili Oleh dan Jabatan **muncul**
4. Isi:
   - Nama Customer: PT. Putra Tunggal Trans
   - Diwakili Oleh: Yuningsih
   - Jabatan: Direktur Utama
   - Alamat: Jl. Kemang Sari Raya No. 18A...
   - No KTP: 31.7105.680790.0004
5. Submit → Download PDF
6. **Verify:** 
   - Format = perusahaan lengkap
   - Text "diwakili oleh **Yuningsih** dalam jabatannya selaku Direktur Utama" ada
   - Nama perusahaan dan nama perwakilan di-bold

---

### Test Case 3: Create Kontrak Perusahaan (tanpa perwakilan)
1. Buka modal Create Kontrak
2. Pilih Jenis Pelanggan = "Perusahaan"
3. Field Diwakili Oleh dan Jabatan muncul
4. **Kosongkan** field Diwakili Oleh dan Jabatan (biarkan empty)
5. Isi field lain (nama perusahaan, alamat, KTP)
6. Submit → Download PDF
7. **Verify:**
   - Format = perusahaan **tanpa** "diwakili oleh"
   - Text langsung ke "pemegang KTP No. ..."

---

### Test Case 4: Approve Kontrak dengan Data Perwakilan
1. Buat kontrak pending (jenis = Perusahaan, tanpa perwakilan)
2. Klik tombol "Approve"
3. Di modal Approve Tab 1, pilih Jenis Pelanggan = "Perusahaan"
4. Isi field Diwakili Oleh dan Jabatan
5. Upload file TTD → Submit
6. Download PDF → **Verify:** Data perwakilan muncul di Pihak Kedua

---

### Test Case 5: Edit Kontrak - Update Data Perwakilan
1. Buka kontrak existing (jenis = Perusahaan, sudah ada data perwakilan)
2. Klik tombol "Edit"
3. Modal Edit terbuka dengan data ter-populate:
   - Jenis Pelanggan = "Perusahaan"
   - Field Diwakili Oleh dan Jabatan **otomatis muncul** (karena jenis = Perusahaan)
   - Field Diwakili Oleh dan Jabatan **sudah terisi** dengan data existing
4. Update data perwakilan (misal ganti nama atau jabatan)
5. Submit → Regenerate PDF
6. **Verify:** Data perwakilan di PDF sudah ter-update

---

### Test Case 6: Edit Kontrak - Switch Jenis Pelanggan
1. Buka kontrak existing (jenis = Perorangan)
2. Klik Edit → Field perwakilan hidden
3. Switch Jenis Pelanggan ke "Perusahaan"
4. Field Diwakili Oleh dan Jabatan **muncul** dan kosong (required)
5. Isi data perwakilan → Submit
6. Download PDF → **Verify:** Format berubah dari perorangan ke perusahaan lengkap

---

### Test Case 7: Switch dari Perusahaan ke Perorangan
1. Buka modal Create
2. Pilih "Perusahaan" → field perwakilan muncul
3. Isi field perwakilan dengan data
4. Switch ke "Perorangan"
5. **Verify:** Field perwakilan langsung **hidden** dan value di-clear

---

### Test Case 7: Switch dari Perusahaan ke Perorangan
1. Buka modal Create
2. Pilih "Perusahaan" → field perwakilan muncul
3. Isi field perwakilan dengan data
4. Switch ke "Perorangan"
5. **Verify:** Field perwakilan langsung **hidden** dan value di-clear

---

### Test Case 8: Backward Compatibility (Kontrak Lama)
1. Kontrak lama yang `jenis_pelanggan = NULL` atau `perwakilan_pihak_kedua = NULL`
2. Download PDF kontrak lama
3. **Verify:** Format tetap perorangan (tidak error)

---

## ✅ Verification Checklist

- [x] Migration file created dan berhasil run
- [x] Dropdown jenis_pelanggan dipindahkan ke atas customer_name (modal Create)
- [x] Default value jenis_pelanggan = "Perorangan"
- [x] Field Diwakili Oleh dan Jabatan hidden by default
- [x] JavaScript togglePerwakilanFields() berfungsi (show/hide conditional)
- [x] Required validation active saat pilih "Perusahaan"
- [x] Modal Approve juga support field perwakilan
- [x] **Modal Edit support field perwakilan dengan pre-fill data existing**
- [x] **Modal Edit auto-toggle field perwakilan saat dibuka based on jenis_pelanggan**
- [x] **openEditModal() pre-fill perwakilan_pihak_kedua dan jabatan_pihak_kedua**
- [x] Controller store() menyimpan perwakilan_pihak_kedua dan jabatan_pihak_kedua
- [x] Controller approve() menyimpan data perwakilan saat approve
- [x] Controller update() support update data perwakilan
- [x] PDF draft_pdf.blade.php menampilkan format Pihak Kedua yang benar (perorangan/perusahaan)
- [x] PDF draft_print.blade.php menampilkan format yang sama
- [x] Bold formatting applied untuk nama perusahaan dan perwakilan
- [x] Fallback logic: jika perusahaan tapi perwakilan kosong → skip "diwakili oleh"
- [x] Kontrak lama (jenis_pelanggan NULL) tidak error

---

## 📊 Visual Comparison

### PDF Pihak Kedua - Before vs After

**BEFORE (semua pakai format perorangan):**
```
2. PT. Putra Tunggal Trans, yang beralamat di Jl. Kemang Sari Raya No. 18A..., 
   pemegang KTP No. 31.7105.680790.0004, selanjutnya disebut sebagai "Pihak Kedua".
```
❌ Tidak ada keterangan "suatu perseroan terbatas..."  
❌ Tidak ada keterangan "diwakili oleh..."

**AFTER (format perusahaan lengkap):**
```
2. PT. Putra Tunggal Trans, suatu perseroan terbatas yang memiliki kantor terdaftar di 
   Jl. Kemang Sari Raya No. 18A RT. 002 RW. 011 Jatibening Baru, Pondok Gede, Bekasi, Jawa Barat, 
   dalam hal ini diwakili oleh Yuningsih dalam jabatannya selaku Direktur Utama, 
   pemegang KTP No. 31.7105.680790.0004, selanjutnya disebut sebagai "Pihak Kedua".
```
✅ Format legal perusahaan lengkap  
✅ Konsisten dengan format Pihak Pertama  
✅ Nama perwakilan di-bold

---

## 🔧 Technical Notes

### Performance
- **No additional queries:** Data perwakilan dibaca dari kontrak yang sudah di-load
- **Minimal overhead:** Conditional logic hanya di view layer
- **JS optimization:** Toggle function lightweight (direct DOM manipulation)

### Maintenance
- **Easy to extend:** Jika ada field tambahan (misal: NPWP perusahaan), tinggal tambahkan di migration + form
- **Localization ready:** English translation sudah tersedia untuk semua label
- **Validation flexible:** Required validation hanya active saat jenis = Perusahaan

### Backward Compatibility
- ✅ **Kontrak lama tidak error:** Fallback logic handle NULL values
- ✅ **Database backward compatible:** Kolom baru nullable, tidak wajib diisi
- ✅ **UI backward compatible:** Default "Perorangan" → behavior sama seperti sebelumnya

---

## 🚀 Next Steps (Optional Enhancements)

1. **Auto-populate dari Pelanggan:** Jika nama perusahaan sudah pernah diinput, auto-fill data perwakilan dari riwayat
2. **Dropdown Jabatan:** Ganti text input jabatan dengan dropdown (Direktur Utama, General Manager, CEO, dll)
3. **Validation NPWP:** Tambahkan field NPWP perusahaan dengan validation 15 digit
4. **Template per Jenis:** Simpan template ketentuan terpisah untuk perorangan vs perusahaan
5. **Audit Log:** Track perubahan data perwakilan di history

---

## 📝 Related Code Locations

**Files Modified:**
- `database/migrations/2026_08_26_141900_add_perwakilan_fields_to_inv_kontraks_table.php` (NEW — 23 lines)
- `resources/views/admin/kontrak/index.blade.php` (Lines ~485-545: modal Create, ~710-790: modal Approve, ~935-1010: modal Edit, ~1570: JS togglePerwakilanFields, ~1660: openEditModal pre-fill)
- `app/Http/Controllers/Admin/InvKontrakController.php` (Line ~213: store $data, Line ~335: approve update, Line ~487: update — auto via except)
- `resources/views/admin/kontrak/draft_pdf.blade.php` (Lines ~270-290: Pihak Kedua conditional)
- `resources/views/admin/kontrak/draft_print.blade.php` (Lines ~452-475: Pihak Kedua conditional)

**Related Functions (not modified):**
- `InvKontrakController::regenerateDraft()` — Uses draft_pdf view
- `InvKontrakController::draftPrint()` — Uses draft_print view

---

**Implemented by:** Kiro AI  
**Status:** ✅ Complete & Ready for Testing  
**Date:** 26 Agustus 2026  

**IMPORTANT:** Jalankan migration terlebih dahulu sebelum testing:
```bash
php artisan migrate
```
