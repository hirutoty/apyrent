# Test Case: GPS Approval Fix Verification

## 🧪 Persiapan Test

### 1. Reset Database (Optional - untuk clean test)
```sql
-- Backup dulu!
-- Hapus PO test yang lama
DELETE FROM purchase_orders WHERE po_id LIKE 'PO-2026-%' AND status = 'Pending';
```

### 2. Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
```

### 3. Pastikan `.env` Debug Mode
```
APP_DEBUG=true
LOG_LEVEL=debug
```

---

## ✅ Test Case 1: Approval Partial (2 Approve, 1 Reject)

### Langkah:
1. Buka menu **GPS Kendaraan**
2. Pilih kendaraan (contoh: AD 1198 SV)
3. Klik tombol **Ajukan GPS**
4. Isi form dengan 3 GPS items:
   - GPS 1: GPS Tracker Pro (Gt018), Rp 25.000
   - GPS 2: GPS Tracker Pro (jbsb), Rp 25.000
   - GPS 3: GPS Tracker Pro (gpsMANTP), Rp 25.000
5. Submit form → PO akan dibuat dengan status "Pending"

### Expected Result Step 1:
- ✅ PO muncul di list dengan status "Pending"
- ✅ Total: 3 items, Rp 75.000

---

### Langkah Approval:
6. Login sebagai **Superadmin**
7. Buka menu **Purchase Order**
8. Klik tombol **Aksi** → **Approve Items** pada PO yang baru dibuat
9. Di modal approval:
   - Item 1 (GPS Tracker Pro - Gt018): Pilih **Approve** ✅
   - Item 2 (GPS Tracker Pro - jbsb): Pilih **Approve** ✅
   - Item 3 (GPS Tracker Pro - gpsMANTP): Pilih **Reject** ❌, isi catatan: "Vendor tidak cocok"
10. Klik **Submit Approval**

### Expected Result Step 2:
- ✅ Alert success: "PO PO-2026-XXX: 2 item disetujui, 1 item ditolak. Pembayaran PR-XXX otomatis dibuat."
- ✅ Redirect ke tab "Disetujui"
- ✅ PO status berubah jadi **"Disetujui"**
- ✅ Total harga: **Rp 50.000** (hanya 2 item yang approved)
- ✅ Total items: **2**

---

### Langkah Verifikasi Detail:
11. Klik tombol **Detail** pada PO tersebut
12. Modal detail terbuka

### Expected Result Step 3 - Modal Detail Tab "Semua":
- ✅ Header: "Detail — GPS Kendaraan"
- ✅ Kendaraan: AD 1198 SV - Daihatsu Gran Max
- ✅ Tampil 3 items:
  
  **Item 1:**
  - Nama: GPS Tracker Pro (Gt018)
  - Badge: 🟢 **Disetujui** (hijau)
  - Biaya: Rp 25.000 (hijau)
  
  **Item 2:**
  - Nama: GPS Tracker Pro (jbsb)
  - Badge: 🟢 **Disetujui** (hijau)
  - Biaya: Rp 25.000 (hijau)
  
  **Item 3:**
  - Nama: GPS Tracker Pro (gpsMANTP)
  - Badge: 🔴 **Ditolak** (merah)
  - Catatan: "Vendor tidak cocok" (italic merah)
  - Biaya: Rp 25.000 (merah)
  
- ✅ Total di footer: **Rp 75.000** (tab "Semua" show all, tapi warna merah untuk rejected)

---

### Langkah Verifikasi Tab:
13. Klik tab **"Disetujui"** di modal

### Expected Result Step 4 - Tab "Disetujui":
- ✅ Tampil 2 items saja (item 1 dan 2)
- ✅ Keduanya dengan badge hijau "Disetujui"
- ✅ Total di footer: **Rp 50.000**
- ✅ TIDAK tampil item 3 (yang ditolak)

---

14. Klik tab **"Ditolak"** di modal

### Expected Result Step 5 - Tab "Ditolak":
- ✅ Tampil 1 item saja (item 3)
- ✅ Badge merah "Ditolak"
- ✅ Catatan penolakan tampil
- ✅ Total di footer: **Rp 25.000**
- ✅ TIDAK tampil item 1 dan 2 (yang disetujui)

---

## ✅ Test Case 2: Resubmit Item yang Ditolak

### Langkah:
15. Di list Purchase Order, PO masih status "Disetujui"
16. Klik tombol **Ajukan Ulang** pada PO tersebut
17. Modal ajukan ulang terbuka, tampil data item 3 yang ditolak
18. Edit data jika perlu, klik **Submit**

### Expected Result Step 6:
- ✅ Alert success: "PO baru PO-2026-XXX dibuat untuk item yang diajukan ulang."
- ✅ PO baru muncul dengan status "Pending"
- ✅ PO baru hanya berisi 1 item (GPS Tracker Pro - gpsMANTP)
- ✅ PO lama tetap status "Disetujui" dengan 2 items yang approved
- ✅ Total PO lama tetap: Rp 50.000
- ✅ Total PO baru: Rp 25.000

---

## 🔍 Verifikasi Database

### Query Check:
```sql
-- Check PO yang sudah diapprove partial
SELECT 
    po_id,
    status,
    total_barang,
    total_harga,
    JSON_EXTRACT(source_data, '$.item_decisions') as decisions,
    JSON_EXTRACT(source_data, '$.locked_approved_idx') as locked,
    JSON_LENGTH(JSON_EXTRACT(source_data, '$.gps_items')) as total_items
FROM purchase_orders 
WHERE po_id LIKE 'PO-2026-%'
ORDER BY id DESC 
LIMIT 2;
```

### Expected Result:
```
| po_id        | status     | total_barang | total_harga | decisions (summary)          | locked  | total_items |
|--------------|------------|--------------|-------------|------------------------------|---------|-------------|
| PO-2026-XXX  | Disetujui  | 2            | 50000       | [0:approved, 1:approved,     | [0,1]   | 3           |
|              |            |              |             |  2:rejected]                 |         |             |
| PO-2026-YYY  | Pending    | 1            | 25000       | []                           | []      | 1           |
| (resubmit)   |            |              |             |                              |         |             |
```

**Key Points:**
- ✅ PO lama `total_barang = 2` (hanya yang approved)
- ✅ PO lama `total_harga = 50000` (exclude rejected)
- ✅ PO lama `locked_approved_idx = [0, 1]` (kedua item di-lock)
- ✅ PO lama masih punya 3 items di `gps_items` (untuk history)
- ✅ PO baru `total_items = 1` (hanya rejected item yang di-resubmit)

---

## ❌ Known Issues (Bug Sebelum Fix)

### Bug A: Semua Item Tampil "Disetujui"
**Before Fix**: Semua 3 items tampil badge hijau "Disetujui"
**After Fix**: Item 1-2 hijau, item 3 merah "Ditolak"

### Bug B: Status PO "Ditolak" Padahal Ada yang Approved
**Before Fix**: PO status "Ditolak" meskipun 2 item sudah diapprove
**After Fix**: PO status "Disetujui" karena ada item yang approved

### Bug C: Total Harga Salah
**Before Fix**: Total Rp 75.000 (include rejected item)
**After Fix**: Total Rp 50.000 (exclude rejected item)

---

## 📞 Jika Test Gagal

### Symptom: Semua item masih tampil badge hijau
**Diagnose**:
1. Check log: `tail -f storage/logs/laravel.log | grep GPS`
2. Cari entry "GPS Approval - Item Decisions Built"
3. Verifikasi `decisions` array ada dan berisi action yang benar

**Possible Cause**:
- `item_decisions` tidak tersimpan ke database
- Frontend tidak menerima field `action` dari API

**Fix**:
- Check response JSON di browser Network tab
- Tambahkan `dd($itemDecisions)` di controller sebelum `$po->update()`

---

### Symptom: Index mismatch di log
**Diagnose**:
1. Check log: cari "GPS Item Index Mismatch"
2. Jika ada, berarti lookup `$origIdx` gagal

**Possible Cause**:
- GPS items dengan `gps_id` dan `biaya_sewa` yang identik
- Filter mengubah index array

**Fix**:
- Tambahkan field unique di GPS items (misal: `uuid`)
- Atau gunakan index fallback yang lebih robust

---

## ✅ Success Criteria

Test dianggap **PASS** jika:
- [ ] Step 1: PO dibuat dengan status "Pending", 3 items, Rp 75.000
- [ ] Step 2: Approval berhasil, status "Disetujui", 2 items, Rp 50.000
- [ ] Step 3: Modal tab "Semua" tampil 3 items dengan badge sesuai action
- [ ] Step 4: Modal tab "Disetujui" tampil 2 items approved saja
- [ ] Step 5: Modal tab "Ditolak" tampil 1 item rejected saja
- [ ] Step 6: Resubmit berhasil, PO baru dibuat dengan 1 item
- [ ] Database check: `item_decisions` dan `locked_approved_idx` sesuai expected

---

**Test Date**: _____________  
**Tested By**: _____________  
**Result**: ☐ PASS  ☐ FAIL  
**Notes**: _________________________________

