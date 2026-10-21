# GPS Approval Bug Fix Summary - COMPREHENSIVE

## ⚠️ IMPORTANT: Test dengan Data Fresh
Setelah apply fix ini, **WAJIB test dengan PO baru** (bukan PO lama yang sudah corrupted data).
PO lama mungkin punya `source_data` yang tidak konsisten karena bug sebelumnya.

## 🔴 Masalah yang Ditemukan

### 1. **Item Partial (2 Approve, 1 Tolak) → Semua Item Masuk Ditolak**
**Root Cause**: 
- Di `approveItems()` line 997-1020, guard untuk `locked_approved_idx` men-skip item yang sudah locked SEBELUM memasukkan ke `$approvedIdx`
- Akibatnya ketika approval cycle ke-2, item yang sudah approved sebelumnya tidak masuk ke tracking
- Filter di `buildGpsDetails()` hanya melihat `item_decisions` cycle terbaru, tidak memperhitungkan `locked_approved_idx`

**Solusi Applied**:
✅ Perbaiki logik guard di `approveItems()` (line 987-1032):
   - Tetap skip locked items dari validation loop
   - Tambahkan check: jika tidak ada keputusan baru DAN tidak ada locked, reject request
   - Jika hanya ada locked tanpa keputusan baru, reject dengan pesan "sudah fully approved"

✅ Tambahkan field `action` dan `catatan_penolakan` di `buildGpsDetails()` (line 835-870):
   - Item dengan `item_decisions` → ambil action dari decisions
   - Item dengan `locked_approved_idx` → set action = 'approved' 
   - Item tanpa keduanya → action = null (pending)

### 2. **Status PO Malah "Disetujui Semua" Padahal Ada yang Ditolak**
**Root Cause**:
- Di `approveItemsGps()` line 1267, status PO selalu di-set ke 'Disetujui' tanpa cek apakah ada item approved atau tidak
- Variable `$finalStatus` tidak ada, langsung hardcode 'Disetujui'

**Solusi Applied**:
✅ Tambahkan logic `$finalStatus` di `approveItemsGps()` (line 1242-1275):
   - Jika `$totalApprovedItems > 0` → status 'Disetujui'
   - Jika semua ditolak → status 'Ditolak' (handled di guard awal)
   - Update PO dengan `$finalStatus` bukan hardcode

### 3. **Saat di PO Pending, Status Malah Sudah Disetujui**
**Root Cause**:
- Tab filtering di `index()` melihat `locked_approved_idx` sebagai "Disetujui" 
- Tapi PO status masih "Pending" karena ada item yang belum diputuskan (resubmit ke-2)
- Inkonsistensi antara status PO vs item status

**Solusi Applied**:
✅ Logic sudah benar di `index()` line 48-54:
   - Tab "Disetujui" include: PO status 'Disetujui' OR PO status 'Pending' dengan `locked_approved_idx` tidak kosong
   - Ini memastikan PO partial (yang ada item approved tapi ada item pending) muncul di 2 tab sekaligus:
     * Tab "Disetujui" → expand item approved saja
     * Tab "Pending" → expand item pending saja

✅ Tambahan fix di `buildGpsDetails()`:
   - Tab "Disetujui" + `decMap` tidak kosong → filter item dengan `action = 'approved'` OR ada di `locked_approved_idx`
   - Tab "Pending" → filter item TANPA action (belum diputuskan)

### 4. **PO Baru (Fresh Pending) Tampil Badge "Disetujui" untuk Semua Item**
**Root Cause**:
- PO yang baru dibuat (belum pernah di-approve) tidak punya `item_decisions` sama sekali
- Logic `buildGpsDetails()` set `$itemAction = null` untuk item tanpa decision
- Frontend tidak ada handling untuk `action = null`, sehingga fallback ke default (mungkin tampil hijau)

**Solusi Applied**:
✅ Tambahkan logic di `buildGpsDetails()` (line 877-895):
   - Jika PO status "Pending" dan tidak ada `item_decisions` → set `action = 'pending'` untuk semua item
   - Jika PO status "Disetujui" tanpa `item_decisions` (legacy full approval) → set `action = 'approved'`
   
✅ Update frontend badge rendering (purchaseo/index.blade.php line 2461-2477):
   - Tambahkan badge kuning "🟡 Pending" untuk `action = 'pending'`
   - Warna biaya: merah (rejected), kuning (pending), hijau (approved/default)

## 📋 Files Modified

1. **app/Http/Controllers/Admin/PurchaseOrderController.php**
   - Line 987-1032: Guard logic untuk `locked_approved_idx`
   - Line 260-264: Inject `po_status` ke `sourceData` di method `detail()`
   - Line 1242-1310: Fix status PO logic di `approveItemsGps()`
   - Line 877-912: Fix item action tracking di `buildGpsDetails()` (tambah logic `pending`)

2. **resources/views/admin/purchaseo/index.blade.php**
   - Line 2461-2477: Tambah badge kuning "Pending" dan fix warna biaya per status

## ✅ Expected Behavior After Fix

### Scenario 1: Approval Pertama (3 Item GPS)
- User approve 2 item, reject 1 item
- **Result**: 
  - PO status: "Disetujui" 
  - `locked_approved_idx`: [0, 1]
  - `item_decisions`: [{idx:0, action:'approved'}, {idx:1, action:'approved'}, {idx:2, action:'rejected'}]
  - Tab "Disetujui": tampil 2 item (approved)
  - Tab "Ditolak": tampil 1 item (rejected)

### Scenario 2: Resubmit Item yang Ditolak
- User edit item idx 2 yang ditolak, submit ulang
- PO baru dibuat dengan item idx 2 saja, PO lama tetap status "Disetujui" dengan 2 item

### Scenario 3: Approval Round 2 (PO Baru dengan 1 Item)
- User approve item di PO baru
- **Result**:
  - PO baru status: "Disetujui"
  - PO lama tetap status: "Disetujui" dengan 2 item

## 🧪 Testing Checklist

- [ ] Test approval pertama dengan 2 approve, 1 reject
- [ ] Cek tab "Disetujui" tampil 2 item dengan badge hijau
- [ ] Cek tab "Ditolak" tampil 1 item dengan badge merah  
- [ ] Cek status PO = "Disetujui" (bukan "Ditolak")
- [ ] Test resubmit item yang ditolak
- [ ] Cek PO lama tetap "Disetujui" dengan 2 item locked
- [ ] Cek PO baru status "Pending" dengan 1 item

## 🔍 Debug Log Points (Optional)

Jika masih ada bug, tambahkan log di:

1. **approveItems()** line 1020:
```php
\Log::info('Approval Decision', [
    'approved_idx' => $approvedIdx,
    'rejected_idx' => $rejectedIdx,
    'locked_idx' => $lockedApprovedIdx,
]);
```

2. **approveItemsGps()** line 1265:
```php
\Log::info('GPS Approval Final', [
    'total_approved' => $totalApprovedItems,
    'final_status' => $finalStatus,
    'new_locked_idx' => $newLockedIdx,
]);
```

3. **buildGpsDetails()** line 862:
```php
\Log::info('GPS Item Build', [
    'orig_idx' => $origIdx,
    'action' => $itemAction,
    'in_decisions' => $decMap->has($origIdx),
    'in_locked' => in_array($origIdx, $lockedApprovedIdx),
]);
```

---
**Generated**: 2026-10-20
**Fixed by**: Kiro AI Assistant
