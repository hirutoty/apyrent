# Purchase Order Flow Testing Checklist

## Setup Verification
- [x] Migration berhasil dijalankan (purchase_orders table extended)
- [x] Routes terdaftar dengan benar (approve, reject endpoints)
- [x] Models updated (PurchaseOrder dengan relations)
- [x] Services created (PurchaseOrderApprovalService)
- [x] Controllers updated (PurchaseOrderController, GpsKendaraanController)
- [x] Views created (Purchase Order approval UI)
- [x] Cache cleared (config, route, view)

## Test Scenario 1: Tambah GPS Baru → PO → Pembayaran → Aktif

### Steps:
1. **User tambah GPS baru (multiple items)**
   - Navigate ke: `/admin/gps-kendaraan`
   - Klik "Tambah GPS"
   - Pilih kendaraan
   - Tambah 2-3 GPS items dengan type berbeda
   - Upload lampiran untuk setiap item
   - Submit form

   **Expected Result:**
   - ✓ Purchase Order created dengan status "Pending"
   - ✓ GPS records created dengan:
     - `pembayaran_id = null`
     - `persetujuan = 'Pending'`
     - `status_gps = 'nonaktif'`
     - `status_sewa = 'tidak_aktif'`
   - ✓ Attachments tersimpan di table `attachments`
   - ✓ Success message: "Pengajuan GPS berhasil dikirim ke Purchase Order"
   - ✓ Redirect back ke halaman GPS

2. **Superadmin approve Purchase Order**
   - Navigate ke: `/admin/purchase-order?status=Pending`
   - Klik tab "Pending"
   - Lihat PO yang baru dibuat
   - Klik "Approve"
   - Optional: tambah catatan
   - Submit

   **Expected Result:**
   - ✓ PO status berubah jadi "Disetujui"
   - ✓ Pembayaran auto-created dengan:
     - `no_pr = PR-GPS-XXX`
     - `status = 'Pending'`
     - `source_type = 'gps'`
     - `source_data` berisi semua data GPS
   - ✓ PO `pembayaran_id` terisi dengan ID Pembayaran baru
   - ✓ Success message: "Purchase Order ... telah disetujui. Pembayaran ... otomatis dibuat"
   - ✓ Redirect ke tab "Disetujui"

3. **Superadmin approve Pembayaran**
   - Navigate ke: `/admin/payments?status=Pending`
   - Klik tab "Pending"
   - Lihat Pembayaran yang baru dibuat (dari PO)
   - Klik "Approve"
   - Submit

   **Expected Result:**
   - ✓ GPS records di-update:
     - `pembayaran_id = [ID Pembayaran]`
     - `persetujuan = 'Disetujui'`
     - `status_gps = 'aktif'`
     - `status_sewa = 'aktif'`
   - ✓ Jurnal Keuangan created
   - ✓ Jurnal Buku Besar created
   - ✓ Success message: "Pembayaran disetujui"

4. **Verify GPS aktif**
   - Navigate ke: `/admin/gps-kendaraan`
   - Check GPS records yang baru dibuat
   
   **Expected Result:**
   - ✓ GPS ditampilkan dengan status "Aktif"
   - ✓ Semua data lengkap (kendaraan, type, biaya, tanggal)
   - ✓ Lampiran bisa diakses

## Test Scenario 2: Reject PO → Edit → Resubmit

### Steps:
1. **User tambah GPS baru**
   - Submit form tambah GPS
   - PO created dengan status Pending

2. **Superadmin reject PO**
   - Navigate ke: `/admin/purchase-order?status=Pending`
   - Klik "Reject" pada PO
   - Isi alasan penolakan (wajib)
   - Submit

   **Expected Result:**
   - ✓ PO status = "Ditolak"
   - ✓ PO `can_edit = true`
   - ✓ PO `catatan_approval` terisi alasan penolakan
   - ✓ Success message: "Purchase Order ditolak. User dapat melakukan edit"

3. **User resubmit PO** (Feature untuk next iteration - belum implemented di UI)
   - Untuk saat ini, user harus submit GPS baru dari awal
   - Di future: bisa ada tombol "Edit & Resubmit" di halaman GPS

## Test Scenario 3: Perpanjangan GPS (Tetap via Pembayaran)

### Steps:
1. **User perpanjang GPS existing**
   - Navigate ke: `/admin/gps-kendaraan`
   - Klik "Perpanjang" pada GPS yang sudah ada
   - Isi form perpanjangan
   - Submit

   **Expected Result:**
   - ✓ Data LANGSUNG ke Pembayaran (TIDAK ke PO)
   - ✓ Pembayaran created dengan:
     - `source_type = 'gps_perpanjang'`
     - `status = 'Pending'`
   - ✓ Success message mengarah ke approval Pembayaran
   - ✓ PO TIDAK dibuat

2. **Superadmin approve Pembayaran perpanjangan**
   - Approve Pembayaran seperti biasa
   
   **Expected Result:**
   - ✓ GPS history created
   - ✓ GPS tanggal_habis diperpanjang
   - ✓ Jurnal created

## Test Scenario 4: Delete PO

### Steps:
1. **Delete PO Pending**
   - Navigate ke: `/admin/purchase-order?status=Pending`
   - Klik tombol delete pada PO
   - Confirm

   **Expected Result:**
   - ✓ PO deleted
   - ✓ Temp files cleaned up
   - ✓ GPS records (yang linked) dengan status Pending juga deleted
   - ✓ Success message

2. **Try delete PO Disetujui** (Should fail)
   - Coba delete PO yang sudah disetujui
   
   **Expected Result:**
   - ✓ Error message: "Hanya PO Pending/Ditolak yang bisa dihapus"

## Known Limitations (To be addressed later)

1. **Resubmit UI belum ada di GPS form**
   - Saat ini jika PO ditolak, user harus submit GPS baru dari awal
   - Future: tambahkan tombol "Edit & Resubmit" yang pre-fill form dengan data PO yang ditolak

2. **Detail PO modal belum implemented**
   - Tombol "Detail" di list PO belum menampilkan detail lengkap
   - Placeholder: "Detail implementation coming soon..."
   - Future: tampilkan GPS items, lampiran, history approval

3. **Vendor field opsional tapi tidak ada di form GPS**
   - Vendor auto-filled dengan default "Vendor GPS"
   - Future: bisa tambahkan dropdown vendor di form GPS jika diperlukan

4. **Attachment duplicate prevention perlu testing lebih lanjut**
   - Logic sudah ada di `transferGps()` tapi perlu diverify dengan real data

## Manual Testing Checklist

- [ ] Test full flow: Tambah GPS → Approve PO → Approve Pembayaran → GPS Aktif
- [ ] Test reject flow: Tambah GPS → Reject PO → Verify PO ditolak
- [ ] Test perpanjangan: Perpanjang GPS → Verify langsung ke Pembayaran (skip PO)
- [ ] Test delete: Delete PO Pending → Verify cleanup
- [ ] Test authorization: Non-superadmin tidak bisa approve/reject
- [ ] Test validation: Form GPS validation tetap berfungsi
- [ ] Test attachments: Lampiran GPS tersimpan dan bisa diakses
- [ ] Test jurnal: Keuangan & Buku Besar auto-created setelah Pembayaran approved
- [ ] Test UI responsiveness: Tab filtering, modals, buttons berfungsi
- [ ] Test pagination: List PO dengan banyak data

## SQL Queries untuk Verifikasi

```sql
-- Check PO yang dibuat
SELECT id, po_id, source_type, vendor, total_harga, status, created_at 
FROM purchase_orders 
ORDER BY id DESC LIMIT 10;

-- Check GPS records dengan status Pending
SELECT id, kendaraan_id, type, biaya_sewa, pembayaran_id, persetujuan, status_sewa
FROM gps_kendaraan 
WHERE persetujuan = 'Pending';

-- Check Pembayaran dari PO
SELECT p.id, p.no_pr, p.source_type, p.status, po.po_id
FROM pembayarans p
LEFT JOIN purchase_orders po ON po.pembayaran_id = p.id
WHERE p.source_type = 'gps'
ORDER BY p.id DESC LIMIT 10;

-- Check attachments GPS
SELECT a.id, a.relation_type, a.relation_id, a.file_name, g.type
FROM attachments a
JOIN gps_kendaraan g ON g.id = a.relation_id
WHERE a.relation_type = 'gps'
ORDER BY a.id DESC LIMIT 20;
```

## Deployment Checklist

- [x] Database migration ready
- [x] Code changes completed
- [x] Cache cleared
- [ ] Backup database production
- [ ] Run migration di production: `php artisan migrate`
- [ ] Test di staging environment
- [ ] Monitor logs untuk errors
- [ ] Inform users tentang flow baru

## Rollback Plan (If needed)

1. Revert migration:
   ```bash
   php artisan migrate:rollback --step=1
   ```

2. Restore old views:
   ```bash
   mv resources/views/admin/purchaseo/index_old.blade.php resources/views/admin/purchaseo/index.blade.php
   ```

3. Revert code changes via git:
   ```bash
   git checkout HEAD~1 -- app/Http/Controllers/Admin/GpsKendaraanController.php
   # ... other files
   ```
