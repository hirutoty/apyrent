# Integration Testing: Service Part Request dengan Approval

## Overview
Dokumen ini berisi panduan testing untuk fitur Service Part Request dengan sistem Approval yang baru diimplementasi.

---

## Prerequisites
- Database migration `add_approval_fields_to_service_history_table` sudah dijalankan
- User sudah login ke admin panel
- Ada minimal 1 kendaraan tersedia
- Ada minimal 1 service history dengan parts yang berstatus "Terpasang" atau "Limit"

---

## Test Scenarios

### ✅ Test 8.1: Duplicate Detection
**Tujuan**: Verifikasi sistem mendeteksi duplicate part saat tambah service normal

**Langkah**:
1. Buka `/admin/service-history`
2. Klik "Tambah Service"
3. Pilih kendaraan yang sudah punya parts Terpasang/Limit
4. Isi form service header (tanggal, km, status, keluhan)
5. Tambah part yang **SAMA** dengan part existing:
   - `kendaraan_id` sama
   - `nama_part` sama
   - `category_id` sama
   - `posisi` sama
   - Part existing status = "Terpasang" atau "Limit"
6. Submit form

**Expected Result**:
- Form di-reject dengan error message
- Pesan error list part yang duplicate
- Suggest user untuk gunakan "Request Part"
- Data tidak masuk ke database

**⚠️ Case-Insensitive Test Scenarios**:
- **Scenario A**: Existing "Ban - Depan Kanan", input "BAN - DEPAN KANAN" → **DUPLICATE** ✅
- **Scenario B**: Existing "Filter Oli", input "filter oli" → **DUPLICATE** ✅
- **Scenario C**: Existing "Aki", input " aki " (spaces) → **DUPLICATE** ✅
- **Scenario D**: Existing "Ban - Depan Kanan", input "Ban - Depan Kiri" → **VALID** (posisi beda)

---

### ✅ Test 8.2: Partial Success
**Tujuan**: Verifikasi partial success ketika submit campuran valid + duplicate parts

**Langkah**:
1. Buka `/admin/service-history/create`
2. Pilih kendaraan
3. Tambah 3 parts:
   - Part 1: Valid (nama part baru)
   - Part 2: Duplicate (sama dengan existing)
   - Part 3: Valid (nama part baru)
4. Submit form

**Expected Result**:
- Success message muncul
- Message include warning: "Part yang duplicate ditolak: [nama part]"
- Hanya Part 1 dan Part 3 yang masuk database
- Part 2 tidak masuk
- Service history tetap dibuat dengan 2 parts valid

---

### ✅ Test 8.3: Request Part Flow
**Tujuan**: Verifikasi create request part dengan status pending

**Langkah**:
1. Buka `/admin/service-history`
2. Klik "Request Part" (button amber)
3. Redirect ke `/admin/service-history/request/create`
4. Isi form lengkap (header + parts)
5. Boleh isi part duplicate atau valid (no check)
6. Submit form

**Expected Result**:
- Success message: "Request part berhasil dikirim dan menunggu approval"
- Redirect ke index
- Data masuk database dengan `status_approval = 'pending'`
- Badge "Pending" (kuning) muncul di status column
- Button "Edit" (biru) muncul di actions column

---

### ✅ Test 8.4: Edit Request Pending
**Tujuan**: Verifikasi edit request yang masih pending

**Langkah**:
1. Buka `/admin/service-history`
2. Filter tab "Pending Request"
3. Klik button "Edit" (biru) pada salah satu request pending
4. Redirect ke `/admin/service-history/{id}/edit-request`
5. Form harus pre-fill dengan data existing:
   - Kendaraan terpilih
   - Tanggal service
   - Kilometer
   - Status
   - Keluhan
   - Parts array (semua parts muncul di form)
6. Edit beberapa field (ubah km, tambah part, edit nama part)
7. Submit form

**Expected Result**:
- Success message: "Request berhasil diupdate"
- Data di database terupdate
- Parts lama dihapus, parts baru di-insert
- `status_approval` tetap "pending"
- Badge tetap "Pending"

---

### ✅ Test 8.5: Approve Request
**Tujuan**: Verifikasi approve request dan update status service + kendaraan

**Langkah**:
1. Buka `/admin/service-history`
2. Filter tab "Pending Request"
3. Klik badge "Pending" (kuning) pada salah satu request
4. Modal "Approval Request Part" muncul
5. Pilih status service:
   - Radio "Proses" ATAU
   - Radio "Selesai"
6. Klik button "Setuju" (hijau)

**Expected Result**:
- Success message: "Request berhasil disetujui"
- `status_approval` berubah jadi "approved"
- `approval_by` = user ID yang approve
- `approval_at` = timestamp sekarang
- Status service update sesuai pilihan (proses/selesai)
- Badge berubah jadi "Approved" (hijau)
- Badge tidak bisa diklik lagi
- Button "Edit" hilang
- Jika status = "selesai" dan km > 0:
  - `kendaraan.km_terakhir_service` = service.kilometer
  - `kendaraan.kilometer_sekarang` = service.kilometer
- `kendaraan.status_kendaraan` = "service" (jika proses) atau "tersedia" (jika selesai)

---

### ✅ Test 8.6: Reject Request
**Tujuan**: Verifikasi reject request

**Langkah**:
1. Buka `/admin/service-history`
2. Filter tab "Pending Request"
3. Klik badge "Pending" pada request lain
4. Modal muncul
5. Klik button "Tolak" (merah)
6. Confirm di browser alert

**Expected Result**:
- Success message: "Request telah ditolak"
- `status_approval` berubah jadi "rejected"
- `approval_by` = user ID
- `approval_at` = timestamp
- Badge berubah jadi "Rejected" (merah)
- Badge tidak bisa diklik
- Button "Edit" hilang
- Data tetap di database (tidak dihapus)
- Kendaraan status tidak berubah

---

### ✅ Test 8.7: Filter Tabs
**Tujuan**: Verifikasi tab filters berfungsi

**Langkah**:
1. Buka `/admin/service-history`
2. Pastikan ada data dengan status: pending, approved, rejected, dan normal (null)
3. Klik tab "Semua":
   - URL: `?approval_status=` (kosong)
   - Show semua data
4. Klik tab "Pending Request":
   - URL: `?approval_status=pending`
   - Show hanya pending
5. Klik tab "Approved":
   - URL: `?approval_status=approved`
   - Show hanya approved
6. Klik tab "Rejected":
   - URL: `?approval_status=rejected`
   - Show hanya rejected
7. Test kombinasi filter (tab + bulan + kendaraan + search)

**Expected Result**:
- Setiap tab filter data sesuai status_approval
- Active tab punya background warna (yellow/green/red/blue)
- Inactive tab abu-abu dengan hover gray
- Filter lain (bulan, kendaraan, search) tetap preserved
- Query string correct

---

### ✅ Test 8.8: Badge Display & Modal
**Tujuan**: Verifikasi badge display dan modal interaction

**Langkah**:
1. Buka `/admin/service-history`
2. Verifikasi badge display di status column:
   - **Pending**: background kuning, border kuning, icon clock, text "Pending"
   - **Approved**: background hijau, border hijau, icon check, text "Approved"
   - **Rejected**: background merah, border merah, icon times, text "Rejected"
   - **Normal (null)**: tidak ada badge approval
3. Test modal approval (pending only):
   - Klik badge "Pending"
   - Modal "Approval Request Part" muncul
   - 2 radio options: "Proses" dan "Selesai"
   - 3 buttons: "Batal", "Tolak", "Setuju"
4. Test close modal:
   - Klik "Batal" → modal close
   - Klik outside modal (backdrop) → modal close
5. Test validation:
   - Klik "Setuju" tanpa pilih radio → alert "Pilih status service terlebih dahulu"

**Expected Result**:
- Badge display correct sesuai status
- Badge pending clickable, badge approved/rejected tidak
- Modal berfungsi dengan benar
- Radio selection required
- Close modal works

---

## Validation Checklist

### Database Schema
- [x] `service_history.status_approval` column exists (enum: pending, approved, rejected, nullable)
- [x] `service_history.approval_by` column exists (FK to users.id)
- [x] `service_history.approval_at` column exists (timestamp nullable)

### Routes
- [x] GET `/admin/service-history/request/create`
- [x] POST `/admin/service-history/request`
- [x] GET `/admin/service-history/{id}/edit-request`
- [x] PUT `/admin/service-history/{id}/edit-request`
- [x] POST `/admin/service-history/{id}/approve`
- [x] POST `/admin/service-history/{id}/reject`

### Controller Methods
- [x] `checkDuplicateParts()`
- [x] `requestCreate()`
- [x] `requestStore()`
- [x] `editRequest()`
- [x] `updateRequest()`
- [x] `approve()`
- [x] `reject()`
- [x] `index()` with approval_status filter

### Views
- [x] `service_history_request.blade.php`
- [x] `service_history_edit_request.blade.php`
- [x] `service_history.blade.php` with approval modal
- [x] Badge display (pending/approved/rejected)
- [x] Filter tabs (All/Pending/Approved/Rejected)
- [x] Edit button (pending only)
- [x] Request Part button

### JavaScript Functions
- [x] `openApprovalModal(id)`
- [x] `closeApprovalModal()`
- [x] `submitApprove()`
- [x] `submitReject()`

---

## Known Issues & Notes

### Duplicate Check Logic
- Check by: `kendaraan_id` + `nama_part` + `category_id` + `posisi`
- **Case-insensitive**: "Ban" = "BAN" = "ban" ✅
- **Trim whitespace**: "Depan Kanan" = " depankanan " = "DEPANKANAN" ✅
- Only check parts with status "Terpasang" atau "Limit"
- Status "Diganti" di-exclude dari check
- Uses SQL `LOWER(TRIM())` for accurate matching

### Request Part Behavior
- Request tidak ada duplicate check (semua part allowed)
- Request bisa edit semua field (header + parts)
- Setelah approve/reject, request tidak bisa edit lagi

### Approval Logic
- Approve: update status service, recalculate total_biaya, update kendaraan
- Reject: only update status_approval, no kendaraan update
- No comment/notes field (minimal implementation)

---

## Test Data Setup

### Sample Service History (for duplicate test)
```sql
-- Insert sample kendaraan
INSERT INTO kendaraan (merk, nomor_polisi, jenis_id, kilometer_sekarang, status_kendaraan) 
VALUES ('Toyota Avanza', 'B 1234 XYZ', 1, 50000, 'tersedia');

-- Insert sample service history
INSERT INTO service_history (kendaraan_id, tanggal_service, kilometer, status, total_biaya, status_approval)
VALUES (1, '2026-08-01', 50000, 'selesai', 500000, NULL);

-- Insert sample parts (status Terpasang)
INSERT INTO service_parts (service_history_id, kendaraan_id, category_id, nama_part, posisi, tgl_pasang, kilometer_pasang, kondisi, status, interval_nilai, interval_satuan, tanggal_limit, biaya)
VALUES (1, 1, 1, 'Filter Oli', 'Mesin Depan', '2026-08-01', 50000, 'Baik', 'Terpasang', 6, 'bulan', '2027-02-01', 150000);
```

---

## Summary

Total test scenarios: **8 tests**
- Duplicate Detection
- Partial Success
- Request Part Flow
- Edit Request Pending
- Approve Request
- Reject Request
- Filter Tabs
- Badge Display & Modal

Semua test dilakukan secara manual via browser karena ini end-to-end testing untuk UI interaction.

---

**Testing Date**: 2026-08-14
**Tested By**: [Nama Tester]
**Environment**: Development/Local
**Status**: ⏳ Pending Manual Test
