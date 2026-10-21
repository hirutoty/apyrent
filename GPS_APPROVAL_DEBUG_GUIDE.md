# GPS Approval Debug Guide

## 🔍 Cara Debugging

### 1. **Clear Cache Laravel**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 2. **Enable Debug Mode**
Edit `.env`:
```
APP_DEBUG=true
LOG_LEVEL=debug
```

### 3. **Monitor Log Real-time**
```bash
tail -f storage/logs/laravel.log
```

Atau di Windows PowerShell:
```powershell
Get-Content storage\logs\laravel.log -Wait -Tail 50
```

### 4. **Test Scenario**

#### Test 1: Fresh Approval (3 GPS Items)
1. Buat PO baru dengan 3 GPS items
2. Buka modal approval
3. Pilih: 2 Approve, 1 Reject
4. Submit approval

**Expected Log Output:**
```
[2026-10-20 ...] local.INFO: GPS Approval - Item Decisions Built  
{
  "po_id": 123,
  "total_decisions": 3,
  "approved_idx": [0, 1],
  "rejected_idx": [2],
  "locked_idx": [],
  "decisions": [
    {"idx": 0, "nama_gps": "GPS Tracker Pro", "action": "approved", ...},
    {"idx": 1, "nama_gps": "GPS Tracker Pro", "action": "approved", ...},
    {"idx": 2, "nama_gps": "GPS Tracker Pro", "action": "rejected", ...}
  ]
}
```

5. Refresh halaman, buka detail modal PO
6. Check log untuk `GPS Item Build` - harus ada 3 entries:

**Expected Log Output:**
```
[2026-10-20 ...] local.DEBUG: GPS Item Build  
{
  "loop_idx": 0,
  "orig_idx": 0,
  "gps_id": 1,
  "biaya": 25000,
  "has_decision": true,
  "in_locked": false,
  "decision_action": "approved"
}

[2026-10-20 ...] local.DEBUG: GPS Item Build  
{
  "loop_idx": 1,
  "orig_idx": 1,
  "gps_id": 1,
  "biaya": 25000,
  "has_decision": true,
  "in_locked": false,
  "decision_action": "approved"
}

[2026-10-20 ...] local.DEBUG: GPS Item Build  
{
  "loop_idx": 2,
  "orig_idx": 2,
  "gps_id": 1,
  "biaya": 25000,
  "has_decision": true,
  "in_locked": false,
  "decision_action": "rejected"
}
```

7. Verifikasi di browser:
   - Tab "Disetujui": tampil 2 item dengan badge hijau "Disetujui"
   - Tab "Ditolak": tampil 1 item dengan badge merah "Ditolak"
   - PO status: "Disetujui"
   - Total harga: Rp 50.000 (hanya yang approved)

### 5. **Kemungkinan Bug yang Muncul**

#### Bug A: `orig_idx` selalu sama dengan `loop_idx`
**Symptom**: Log menunjukkan `orig_idx === loop_idx` untuk semua items
**Cause**: Filter `$gpsItems` mengubah index, tapi lookup `$origIdx` gagal match
**Fix**: Improve matching logic di `buildGpsDetails()` line 843-868

#### Bug B: `has_decision` selalu false
**Symptom**: Log menunjukkan `has_decision: false` padahal `item_decisions` ada
**Cause**: `$decMap` menggunakan key yang salah (string vs int)
**Fix**: Pastikan `keyBy('idx')` dan `has()` konsisten typecast ke int

#### Bug C: Semua item tampil "Disetujui"
**Symptom**: Semua 3 items tampil badge hijau padahal ada yang ditolak
**Cause**: Frontend tidak menerima field `action` dari backend
**Fix**: Check response JSON di Network tab browser

## 🧪 SQL Query untuk Verifikasi Manual

```sql
-- Check PO source_data
SELECT 
    id, po_id, status, total_barang, total_harga,
    JSON_EXTRACT(source_data, '$.item_decisions') as decisions,
    JSON_EXTRACT(source_data, '$.locked_approved_idx') as locked_idx,
    JSON_EXTRACT(source_data, '$.gps_items') as gps_items
FROM purchase_orders 
WHERE po_id = 'PO-2026-001';
```

**Expected Result:**
```json
{
  "item_decisions": [
    {"idx": 0, "action": "approved", "nama_gps": "GPS Tracker Pro", ...},
    {"idx": 1, "action": "approved", "nama_gps": "GPS Tracker Pro", ...},
    {"idx": 2, "action": "rejected", "nama_gps": "GPS Tracker Pro", "catatan": "..."}
  ],
  "locked_approved_idx": [0, 1],
  "gps_items": [
    {"gps_id": 1, "biaya_sewa": 25000, ...},
    {"gps_id": 1, "biaya_sewa": 25000, ...},
    {"gps_id": 1, "biaya_sewa": 25000, ...}
  ]
}
```

## 📋 Checklist Debugging

- [ ] Log `GPS Approval - Item Decisions Built` muncul setelah approval
- [ ] `decisions` array berisi 3 entries dengan action yang benar
- [ ] `approved_idx` = [0, 1], `rejected_idx` = [2]
- [ ] `locked_idx` kosong (cycle pertama)
- [ ] Log `GPS Item Build` muncul 3x saat buka detail modal
- [ ] `orig_idx` match dengan index asli GPS (0, 1, 2)
- [ ] `has_decision` = true untuk semua items
- [ ] `decision_action` sesuai dengan yang dipilih (approved/rejected)
- [ ] Browser network response JSON berisi field `action` untuk setiap item
- [ ] Badge hijau/merah tampil sesuai action
- [ ] Total harga = Rp 50.000 (exclude rejected item)

## 🔧 Quick Fix Commands

### Jika Log Tidak Muncul
```bash
# Set permission log file
chmod -R 775 storage/logs
chown -R www-data:www-data storage/logs  # Linux
```

### Jika Frontend Tidak Update
```bash
# Clear browser cache dan hard reload
# Chrome: Ctrl+Shift+R (Windows) / Cmd+Shift+R (Mac)
```

### Jika Database Tidak Update
```sql
-- Reset PO untuk testing ulang
UPDATE purchase_orders 
SET status = 'Pending', 
    source_data = JSON_SET(source_data, '$.item_decisions', JSON_ARRAY()),
    locked_approved_idx = JSON_ARRAY()
WHERE po_id = 'PO-2026-001';
```

## 📱 Contact Support

Jika masih error setelah follow guide ini:
1. Copy semua log dari `storage/logs/laravel.log` yang contain "GPS"
2. Screenshot modal detail PO
3. Export SQL result dari query verification di atas
4. Kirim ke developer untuk analysis

---
**Last Updated**: 2026-10-20
**Version**: 1.0
