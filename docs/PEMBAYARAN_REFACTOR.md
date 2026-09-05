# Refactor Form Pembayaran - Multiple Items Support

## Overview
Refactor form Pembayaran dari single item menjadi multiple items support dengan struktur seperti service history. Implementasi menggunakan 2-section layout (Header + Dynamic Items) dengan JavaScript dynamic rows.

## Date Completed
2026-08-18

## Requirements Implemented

### 1. Multiple Items Support ✅
- 1 PR dapat memiliki multiple barang/jasa
- Dynamic rows dengan JavaScript
- Add/remove item functionality

### 2. Form Structure ✅
**Header Section:**
- No PR (auto-generate)
- Tanggal
- Departemen (conditional)
- Pemohon (manual input)
- Alasan Permintaan

**Items Section (12 fields per item):**
- Nama Barang (required)
- Kategori (text, nullable)
- Posisi (text, nullable)
- Part Number (nullable)
- Serial Number (nullable)
- QTY (required, numeric)
- Satuan (text, nullable)
- Harga Satuan (numeric, nullable)
- Subtotal (auto-calculate)
- Spesifikasi (text, nullable)
- Merk (nullable)
- Keterangan (nullable)
- Bukti (file upload, nullable)

### 3. Auto-Calculation ✅
- Subtotal = QTY × Harga Satuan
- Total Nominal = Sum of all subtotals

### 4. Departemen Logic ✅
- **Superadmin**: Dropdown pilih semua departemen
- **User lain**: Auto-set sesuai role (readonly)

### 5. Status Workflow ✅
- Tetap: Pending → Diajukan → Disetujui/Ditolak
- Button "Simpan & Ajukan" → langsung status Diajukan

### 6. Edit Permission ✅
- Hanya status Pending & Diajukan yang bisa edit
- Status Disetujui/Ditolak tidak bisa edit

### 7. Backward Compatibility ✅
- Data lama tetap format lama (single item)
- PR baru menggunakan format baru (multiple items)
- getTotalNominalAttribute accessor untuk compatibility

### 8. No PR Format ✅
- Format: PR-001, PR-002, dst
- Auto-increment dari record terakhir

## Database Structure

### New Table: pembayaran_items
```sql
CREATE TABLE pembayaran_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pembayaran_id BIGINT UNSIGNED NOT NULL,
    nama_barang VARCHAR(255) NOT NULL,
    kategori VARCHAR(255) NULL,
    posisi VARCHAR(255) NULL,
    part_number VARCHAR(255) NULL,
    serial_number VARCHAR(255) NULL,
    qty DECIMAL(10,2) NOT NULL,
    satuan VARCHAR(50) NULL,
    harga_satuan DECIMAL(15,2) NULL,
    subtotal DECIMAL(15,2) NULL,
    spesifikasi TEXT NULL,
    merk VARCHAR(255) NULL,
    keterangan TEXT NULL,
    bukti JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pembayaran_id) REFERENCES pembayarans(id) ON DELETE CASCADE
);
```

### Existing Table: pembayarans
- Tetap unchanged untuk backward compatibility
- Fields lama (barang_jasa, kode_barang, qty, satuan) tidak digunakan untuk data baru

## Model Relations

### Pembayaran Model
```php
public function items() {
    return $this->hasMany(PembayaranItem::class);
}

public function getTotalNominalAttribute() {
    return $this->items->isNotEmpty() 
        ? $this->items->sum('subtotal') 
        : $this->attributes['nominal'];
}
```

### PembayaranItem Model
```php
public function pembayaran() {
    return $this->belongsTo(Pembayaran::class);
}
```

## Routes

```php
// Resource routes (except show)
Route::resource('pembayaran', PembayaranController::class)->except(['show']);

// Additional routes
Route::get('pembayaran/{pembayaran}/details', [PembayaranController::class, 'details'])
    ->name('pembayaran.details');
Route::post('pembayaran/{pembayaran}/ajukan', [PembayaranController::class, 'ajukan'])
    ->name('pembayaran.ajukan');
Route::post('pembayaran/{pembayaran}/status', [PembayaranController::class, 'updateStatusInline'])
    ->name('pembayaran.status');
```

## Controller Methods

### 1. index()
- Eager load items relation
- Display total items & total nominal
- Filter per role & departemen

### 2. create()
- Generate No PR preview
- Departemen options untuk superadmin
- Return create.blade.php

### 3. store()
- Validate header + items array
- DB transaction
- Calculate total nominal
- File upload per item
- Status langsung "Diajukan"
- Conditional departemen logic

### 4. edit($pembayaran)
- Guard: hanya Pending & Diajukan
- Load items relation
- Return edit.blade.php

### 5. update($pembayaran)
- Guard: hanya Pending & Diajukan
- Validate header + items array
- DB transaction
- Delete old items, create new items
- Calculate total nominal
- File upload per item

### 6. details($id)
- AJAX endpoint
- Return JSON dengan items array
- Format data untuk modal display

## Views

### 1. create.blade.php
- 2-section layout
- Dynamic JavaScript for items
- Auto-calculate subtotal & total
- Conditional departemen field
- Minimum 1 item required

### 2. edit.blade.php
- 2-section layout
- Preload existing items
- Dynamic JavaScript for items
- Auto-calculate subtotal & total
- Conditional departemen field

### 3. index.blade.php
- Table display with Total Items & Total Nominal columns
- Detail modal dengan AJAX call
- Display multiple items in modal
- Backward compatibility untuk old structure
- Edit button link ke route edit
- Tambah button link ke route create

## JavaScript Features

### Dynamic Items (create & edit)
- `addItem()` - Add new item row
- `removeItem(index)` - Remove item row
- `calculateSubtotal(index)` - Calculate qty × harga
- `updateGrandTotal()` - Sum all subtotals
- `updateItemNumbers()` - Update item labels

### Detail Modal (index)
- `openDetailModal(id)` - Fetch via AJAX
- `populateDetailModal(data)` - Display data
- Support new structure (multiple items)
- Fallback untuk old structure

## File Uploads
- Location: `storage/app/public/pembayaran/bukti/`
- Format: `timestamp_index_originalname`
- Allowed: JPG, JPEG, PNG, PDF, DOC, DOCX
- Max size: 2MB per file
- Storage: JSON metadata in bukti field

## Validation Rules

### Header
- tanggal: required, date
- departemen: required untuk superadmin
- pemohon: required, string, max 255
- alasan_permintaan: required, string

### Items (array, min 1)
- nama_barang: required, string, max 255
- kategori: nullable, string, max 255
- posisi: nullable, string, max 255
- part_number: nullable, string, max 255
- serial_number: nullable, string, max 255
- qty: required, numeric, min 0.01
- satuan: nullable, string, max 50
- harga_satuan: nullable, numeric, min 0
- subtotal: nullable, numeric, min 0
- spesifikasi: nullable, string
- merk: nullable, string, max 255
- keterangan: nullable, string
- bukti: nullable, file, mimes, max 2048

## Files Modified

1. `database/migrations/2026_08_18_210645_create_pembayaran_items_table.php` - New
2. `app/Models/PembayaranItem.php` - New
3. `app/Models/Pembayaran.php` - Added relations & accessor
4. `app/Http/Controllers/Admin/PembayaranController.php` - Updated all methods
5. `resources/views/admin/pembayaran/create.blade.php` - New (2-section layout)
6. `resources/views/admin/pembayaran/edit.blade.php` - New (2-section layout)
7. `resources/views/admin/pembayaran/index.blade.php` - Updated for multiple items
8. `routes/web.php` - Updated routes

## Testing Checklist

### Create Form
- [ ] Superadmin dapat pilih departemen
- [ ] User lain departemen auto-set
- [ ] Add item button works
- [ ] Remove item button works
- [ ] Subtotal calculation works
- [ ] Grand total calculation works
- [ ] Minimum 1 item validation
- [ ] Required fields validation
- [ ] File upload works
- [ ] Data tersimpan ke DB
- [ ] Status langsung "Diajukan"

### Edit Form
- [ ] Only Pending & Diajukan can edit
- [ ] Existing items preloaded
- [ ] Add/remove item works
- [ ] Calculation works
- [ ] Update saves correctly
- [ ] File upload works

### Index View
- [ ] Total items displayed correctly
- [ ] Total nominal displayed correctly
- [ ] Detail button opens modal
- [ ] AJAX loads data correctly
- [ ] Multiple items displayed in modal
- [ ] Old structure fallback works
- [ ] Edit button visible for editable status
- [ ] Edit button redirects correctly

### Permissions
- [ ] Superadmin: dapat pilih departemen
- [ ] User: auto-set departemen
- [ ] Edit: hanya Pending & Diajukan
- [ ] Delete: hanya Pending & Ditolak
- [ ] Ajukan: hanya Pending & Ditolak

### Backward Compatibility
- [ ] Old data masih terbaca
- [ ] Total nominal accessor works
- [ ] Detail modal fallback works
- [ ] Index table display works

## Known Issues / Notes

1. Migration harus dijalankan: `php artisan migrate`
2. Storage link harus di-setup: `php artisan storage:link`
3. Data lama tidak otomatis migrate ke struktur baru (by design)
4. Departemen options di create/edit harus disesuaikan dengan departemen yang ada

## Future Enhancements

1. Bulk import items dari CSV/Excel
2. Item template untuk repeated orders
3. Approval workflow multi-level
4. Email notification
5. Export to PDF
6. History tracking untuk perubahan
7. Duplicate PR functionality
8. Item search/autocomplete dari database

---

**Status**: ✅ Complete (9/10 tasks)  
**Last Updated**: 2026-08-18T21:54:37+07:00
