# Service Part Request dengan Approval — Quick Reference

## 📋 Fitur yang Diimplementasi

### 1. **Duplicate Check** (Task 1-3)
- Sistem deteksi part duplicate saat tambah service normal
- Criteria: `kendaraan_id` + `nama_part` + `category_id` + `posisi`
- Only check parts dengan status "Terpasang" atau "Limit"
- Partial success: valid parts masuk, duplicate ditolak dengan warning

### 2. **Request Part Flow** (Task 4)
- Button "Request Part" di index (amber)
- Form dedicated: `/admin/service-history/request/create`
- Submit tanpa duplicate check
- Status: `status_approval = 'pending'`

### 3. **Approval System** (Task 5)
- Click badge "Pending" → modal approval
- Approve: pilih status service (Proses/Selesai), update kendaraan
- Reject: update status_approval = 'rejected'
- Badge display: Pending (kuning), Approved (hijau), Rejected (merah)

### 4. **Edit Request Pending** (Task 6)
- Button "Edit" (biru) hanya muncul untuk pending
- Route: `/admin/service-history/{id}/edit-request`
- Edit semua field (header + parts)
- Setelah approve/reject tidak bisa edit

### 5. **Filter Tabs** (Task 7)
- 4 tabs: All | Pending Request | Approved | Rejected
- Active tab warna terang, inactive abu-abu
- Preserve query params lain (bulan, kendaraan, search)

---

## 🗂️ Files Modified

### Backend
- `app/Http/Controllers/Admin/ServiceHistoryController.php`
  - Methods: `checkDuplicateParts()`, `requestCreate()`, `requestStore()`, `editRequest()`, `updateRequest()`, `approve()`, `reject()`
  - Updated: `store()` (duplicate check), `index()` (approval filter)

- `app/Models/ServiceHistory.php`
  - Added: approval fields to fillable, cast, `approver()` relationship

- `database/migrations/2026_08_14_220853_add_approval_fields_to_service_history_table.php`
  - Added: `status_approval`, `approval_by`, `approval_at`

### Frontend
- `resources/views/admin/service/service_history.blade.php`
  - Approval modal with radio buttons
  - Badge display (pending/approved/rejected)
  - Filter tabs
  - Edit button (pending only)
  - JS functions: `openApprovalModal()`, `closeApprovalModal()`, `submitApprove()`, `submitReject()`

- `resources/views/admin/service/service_history_request.blade.php` (NEW)
  - Request part form (no duplicate check)

- `resources/views/admin/service/service_history_edit_request.blade.php` (NEW)
  - Edit request pending form (prefill all fields)

### Routes
- `routes/web.php`
  - `GET /admin/service-history/request/create`
  - `POST /admin/service-history/request`
  - `GET /admin/service-history/{id}/edit-request`
  - `PUT /admin/service-history/{id}/edit-request`
  - `POST /admin/service-history/{id}/approve`
  - `POST /admin/service-history/{id}/reject`

---

## 🎯 User Flow

### Flow 1: Normal Service (Duplicate Detected)
1. User klik "Tambah Service"
2. Isi form + tambah parts
3. Submit → duplicate detected
4. Error message: "Part duplicate: [list]"
5. User gunakan "Request Part" button

### Flow 2: Request Part
1. User klik "Request Part" (amber)
2. Isi form lengkap (boleh duplicate)
3. Submit → status pending
4. Badge "Pending" muncul di index
5. Button "Edit" tersedia

### Flow 3: Edit Request Pending
1. Filter tab "Pending Request"
2. Klik "Edit" (biru)
3. Form prefill dengan data existing
4. Edit field yang diinginkan
5. Submit → data updated, tetap pending

### Flow 4: Approve Request
1. Klik badge "Pending" (kuning)
2. Modal muncul
3. Pilih status: Proses atau Selesai
4. Klik "Setuju"
5. Badge jadi "Approved" (hijau)
6. Kendaraan status updated

### Flow 5: Reject Request
1. Klik badge "Pending"
2. Modal muncul
3. Klik "Tolak" (merah)
4. Confirm
5. Badge jadi "Rejected" (merah)
6. Data tetap di database

---

## 🔍 Testing

Lihat dokumen lengkap: `TESTING_SERVICE_PART_APPROVAL.md`

Quick test checklist:
- [ ] Test duplicate detection (Task 8.1)
- [ ] Test partial success (Task 8.2)
- [ ] Test request part flow (Task 8.3)
- [ ] Test edit request pending (Task 8.4)
- [ ] Test approve request (Task 8.5)
- [ ] Test reject request (Task 8.6)
- [ ] Test filter tabs (Task 8.7)
- [ ] Test badge display & modal (Task 8.8)

---

## 📊 Database Schema

```sql
-- service_history table (added columns)
ALTER TABLE service_history ADD COLUMN status_approval ENUM('pending', 'approved', 'rejected') NULL;
ALTER TABLE service_history ADD COLUMN approval_by BIGINT UNSIGNED NULL;
ALTER TABLE service_history ADD COLUMN approval_at TIMESTAMP NULL;
ALTER TABLE service_history ADD FOREIGN KEY (approval_by) REFERENCES users(id) ON DELETE SET NULL;
```

---

## 🚀 Deployment Checklist

Before deploy to production:
- [x] Migration created: `add_approval_fields_to_service_history_table`
- [x] Migration tested locally
- [ ] Run migration on production: `php artisan migrate`
- [ ] Test all flows on staging
- [ ] Backup database before deploy
- [ ] Clear cache after deploy: `php artisan cache:clear`
- [ ] User training/documentation

---

## 📝 Notes

### Duplicate Check Logic
- Only check parts dengan status "Terpasang" atau "Limit"
- Status "Diganti" tidak di-check (karena part sudah diganti)
- Match criteria: kendaraan_id, nama_part, category_id, posisi
- **Case-insensitive**: "Ban" = "BAN" = "ban" ✅
- **Trim whitespace**: "Depan Kanan" = "depankanan" ✅

### Request Behavior
- Request tidak ada duplicate check (all parts allowed)
- Request pending bisa diedit semua field
- Setelah approve/reject, tidak bisa edit lagi

### Approval Logic
- Approve: update status service, update kendaraan, recalculate total_biaya
- Reject: only update status_approval, no other changes
- No role restriction (anyone can approve)
- No comment field (minimal implementation)

---

**Last Updated**: 2026-08-14
**Version**: 1.0
**Status**: ✅ Development Complete — Ready for Testing
