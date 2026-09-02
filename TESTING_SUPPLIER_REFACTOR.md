# Testing Checklist: Supplier Refactor Integration

## Objective
Verify complete integration of refactored Supplier master data with Pengadaan (Purchase Order) module.

## Pre-Testing Verification ✅

### Database Structure
- [x] Supplier table: kolom `nama_barang`, `jumlah_barang`, `harga_barang` sudah terhapus
- [x] Supplier table: hanya memiliki `id`, `user_id`, `nama_supplier`, `no_telp`, `timestamps`
- [x] Purchaseros table: kolom `supplier_id` (nullable) sudah ditambahkan
- [x] Foreign key: `supplier_id` references `supplier(id)`

### Model Relations
- [x] Supplier model: method `purchaseros()` exists (hasMany)
- [x] Purchasero model: method `supplier()` exists (belongsTo)
- [x] Purchasero model: `supplier_id` in fillable array

### API & Routes
- [x] Route `supplier.api.store` terdaftar (POST /admin/supplier/api/store)
- [x] SupplierController: method `storeApi()` exists
- [x] No syntax errors in SupplierController

### Data Seeding
- [x] Total suppliers in database: 100
- [x] Sample suppliers have correct structure (nama_supplier + no_telp only)

---

## Manual Testing Checklist

### 1. Master Supplier Module
#### View Supplier List
- [ ] Navigate to `/admin/supplier`
- [ ] Verify card "Total Nominal Barang" and "Total Barang" NOT displayed
- [ ] Verify only "Total Supplier" card shown
- [ ] Verify table shows: ID, Nama Supplier, No Telp, Tanggal, Action
- [ ] Verify NO columns for product details (nama_barang, jumlah, harga)

#### Create New Supplier (Traditional Form)
- [ ] Click "Tambah Supplier" button
- [ ] Verify form only has: Nama Supplier + No Telp
- [ ] Fill: Nama = "CV Test Manual", No Telp = "081234567890"
- [ ] Submit form
- [ ] Verify success message
- [ ] Verify new supplier appears in list

---

### 2. Pengadaan Module - Dropdown Supplier

#### View Create Pengadaan Form
- [ ] Navigate to `/admin/purchasero/create`
- [ ] Verify dropdown "Supplier" exists after field "Pemohon"
- [ ] Verify dropdown shows all suppliers from database
- [ ] Verify dropdown format: "Nama Supplier" (not showing phone in option text)
- [ ] Verify green "+" button next to dropdown

#### Test Dropdown Functionality
- [ ] Click dropdown, verify it opens properly
- [ ] Select a supplier from list
- [ ] Verify selection is highlighted
- [ ] Change selection to different supplier
- [ ] Verify new selection updates correctly

---

### 3. Modal Create Supplier On-The-Fly

#### Open Modal
- [ ] Click green "+" button next to supplier dropdown
- [ ] Verify modal appears with overlay
- [ ] Verify modal title: "Tambah Supplier Baru"
- [ ] Verify form has 2 fields: Nama Supplier + No Telepon
- [ ] Verify both fields are required (asterisk shown)

#### Cancel Modal
- [ ] Click "Batal" button
- [ ] Verify modal closes
- [ ] Verify form data not saved

#### Submit Valid Data
- [ ] Open modal again
- [ ] Fill: Nama = "CV Ajax Test", No Telp = "087777888999"
- [ ] Click "Simpan"
- [ ] Verify loading state (button disabled, spinner shown)
- [ ] Verify success alert appears
- [ ] Verify modal closes automatically
- [ ] Verify dropdown now contains new supplier "CV Ajax Test"
- [ ] Verify new supplier is auto-selected in dropdown

#### Submit Duplicate Data
- [ ] Open modal
- [ ] Fill with existing supplier name (e.g., "CV Ajax Test")
- [ ] Click "Simpan"
- [ ] Verify error message displayed in modal
- [ ] Verify modal stays open
- [ ] Verify no duplicate created in dropdown

#### Submit Invalid Data
- [ ] Open modal
- [ ] Leave fields empty
- [ ] Try to submit
- [ ] Verify HTML5 validation prevents submit (or error shown)

---

### 4. Create Pengadaan WITH Supplier

#### Complete Flow: New Supplier + New PR
- [ ] Navigate to `/admin/purchasero/create`
- [ ] Fill all required header fields (tanggal, departemen, pemohon, alasan)
- [ ] Click "+" to create new supplier via modal
- [ ] Create supplier: "CV Integration Test", "081999888777"
- [ ] Verify supplier auto-selected
- [ ] Add 2 items with complete details
- [ ] Submit form
- [ ] Verify success message with PR number (e.g., "PR-XXX berhasil diajukan")
- [ ] Verify redirect to `/admin/purchasero`

#### Verify Database
- [ ] Query database: `SELECT * FROM purchaseros WHERE no_pr = 'PR-XXX'`
- [ ] Verify `supplier_id` is NOT NULL
- [ ] Verify `supplier_id` matches the created supplier

---

### 5. Create Pengadaan WITHOUT Supplier (Backward Compatibility)

#### Test Optional Supplier
- [ ] Navigate to `/admin/purchasero/create`
- [ ] Fill all required fields
- [ ] Leave supplier dropdown as "-- Pilih Supplier --" (empty)
- [ ] Add items
- [ ] Submit form
- [ ] Verify form submits successfully
- [ ] Verify success message

#### Verify Database
- [ ] Query latest purchasero
- [ ] Verify `supplier_id` IS NULL
- [ ] Verify other fields saved correctly
- [ ] This confirms backward compatibility (old flow still works)

---

### 6. Edit Existing Pengadaan

#### Edit PR with Supplier
- [ ] Navigate to purchasero list
- [ ] Find PR with status "Pending" or "Diajukan"
- [ ] Click "Edit"
- [ ] Verify dropdown shows currently selected supplier (if any)
- [ ] Change supplier to different one
- [ ] Update items
- [ ] Submit
- [ ] Verify success message

#### Edit PR - Add Supplier to Existing PR
- [ ] Find PR that has NO supplier (supplier_id = NULL)
- [ ] Click "Edit"
- [ ] Verify dropdown shows "-- Pilih Supplier --"
- [ ] Select a supplier
- [ ] Submit
- [ ] Verify database: supplier_id now populated

#### Edit PR - Remove Supplier
- [ ] Edit PR that has supplier
- [ ] Change dropdown back to "-- Pilih Supplier --"
- [ ] Submit
- [ ] Verify database: supplier_id becomes NULL

---

### 7. View Pengadaan Details

#### PR with Supplier
- [ ] View details of PR that has supplier_id
- [ ] Verify supplier name displayed (if details modal/page shows it)
- [ ] Verify correct supplier linked

#### PR without Supplier
- [ ] View details of PR without supplier_id
- [ ] Verify no error occurs
- [ ] Verify "Supplier: -" or similar placeholder shown

---

### 8. API Endpoint Testing (Optional Advanced)

#### Using Browser Console / Postman
```javascript
// Test POST /admin/supplier/api/store
fetch('/admin/supplier/api/store', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
    },
    body: new FormData(document.getElementById('supplierForm'))
})
.then(res => res.json())
.then(data => console.log(data));
```

Expected Response:
```json
{
    "success": true,
    "message": "Supplier berhasil ditambahkan",
    "data": {
        "id": 101,
        "nama_supplier": "CV Test",
        "no_telp": "08123456789",
        "user_id": 1,
        "created_at": "2026-09-02T12:00:00.000000Z",
        "updated_at": "2026-09-02T12:00:00.000000Z"
    }
}
```

---

## Edge Cases Testing

### Modal Behavior
- [ ] Open modal, close it, reopen → verify form is reset
- [ ] Submit via Enter key → verify it triggers submit
- [ ] Click overlay (outside modal) → should NOT close (intentional UX)

### Dropdown Behavior
- [ ] Create supplier via modal while dropdown has existing selection
- [ ] Verify new supplier replaces selection
- [ ] If no internet/JS error → verify modal shows error gracefully

### Form Validation
- [ ] Try create PR without items → verify error
- [ ] Try create PR with invalid supplier_id in URL manipulation → verify validation catches it

### Permission Testing (if applicable)
- [ ] Test as superadmin → can select any supplier
- [ ] Test as regular user → can select any supplier
- [ ] Verify user_id in supplier table matches creator

---

## Performance Checks
- [ ] Dropdown loads quickly with 100+ suppliers
- [ ] Modal opens/closes smoothly without lag
- [ ] AJAX submit completes within 1-2 seconds
- [ ] No console errors in browser developer tools

---

## Regression Testing
- [ ] Old purchaseros data (before refactor) still displays correctly
- [ ] Existing PR approval flow not affected
- [ ] Charts/reports using purchaseros data still work
- [ ] Export/print PR functionality not broken

---

## Final Verification

### Code Quality
- [x] No PHP syntax errors
- [ ] No JavaScript console errors
- [ ] No broken links or 404 errors
- [ ] Proper error handling in AJAX

### User Experience
- [ ] All buttons have hover states
- [ ] Loading indicators shown during AJAX
- [ ] Success/error messages clear and helpful
- [ ] Form fields have proper labels and placeholders

### Data Integrity
- [ ] No orphaned records
- [ ] Foreign key constraints enforced
- [ ] Nullable supplier_id allows backward compatibility
- [ ] Supplier deletion (if implemented) handles relations properly

---

## Test Results Summary

**Date Tested:** _______________  
**Tested By:** _______________  
**Environment:** _______________

**Total Tests:** 60+  
**Passed:** _____  
**Failed:** _____  
**Blocked:** _____  

**Critical Issues Found:**
1. 
2. 
3. 

**Minor Issues Found:**
1. 
2. 
3. 

**Sign-off:** _______________ (Developer)  
**Sign-off:** _______________ (QA/User)
