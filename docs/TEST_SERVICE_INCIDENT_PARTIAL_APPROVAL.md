# Integration Test - Service Incident Partial Approval Flow

## Test Objective
Verify bahwa item yang sudah di-approve pada Service Incident PO akan locked permanent dan tidak muncul di halaman Pending saat resubmit item yang rejected.

## Prerequisites
- Database reset dengan data fresh
- Login sebagai Superadmin
- Ada kendaraan aktif untuk testing

## Test Scenario

### **Phase 1: Create PO dengan 2 Items**

**Steps:**
1. Navigate ke halaman Service Incident
2. Create new service incident dengan 2 parts:
   - **Part 1**: Nama = "Ban Depan", Biaya = 8000
   - **Part 2**: Nama = "Filter Oli", Biaya = 9000
3. Submit PO

**Expected Results:**
- ✅ PO created dengan status "Pending"
- ✅ total_harga = 17000 (8000 + 9000)
- ✅ total_barang = 2
- ✅ PO muncul di tab "Pending" di index Purchase Order

---

### **Phase 2: Partial Approval (Approve Part 2, Reject Part 1)**

**Steps:**
1. Buka PO dari tab "Pending"
2. Klik tombol "Approve Items"
3. Di modal approval:
   - **Part 1 (Ban Depan - 8000)**: Pilih "Reject" + isi catatan "Harga terlalu mahal"
   - **Part 2 (Filter Oli - 9000)**: Pilih "Approve" + upload bukti bayar
4. Submit approval

**Expected Results:**
- ✅ PO status berubah menjadi "Disetujui"
- ✅ source_data berisi:
  ```json
  {
    "locked_approved_idx": [1],
    "item_decisions": [
      {"idx": 0, "action": "rejected", "catatan": "Harga terlalu mahal"},
      {"idx": 1, "action": "approved", "catatan": null}
    ],
    "nominal_approved_locked": 9000,
    "nominal_rejected": 8000
  }
  ```
- ✅ total_harga = 9000 (hanya approved items)
- ✅ total_barang = 1
- ✅ can_edit = true (karena ada rejected items)
- ✅ Pembayaran otomatis dibuat dengan nominal 9000

**Verify Tab Display:**
- ✅ **Tab Disetujui**: PO muncul, detail show only Part 2 (Filter Oli - 9000)
- ✅ **Tab Ditolak**: PO muncul, detail show only Part 1 (Ban Depan - 8000)
- ✅ **Tab Pending**: PO TIDAK muncul (karena tidak ada item pending)

---

### **Phase 3: Resubmit Item Rejected (Edit 8000 → 10000)**

**Steps:**
1. Navigate ke tab "Ditolak"
2. Buka PO yang partial approved
3. Klik tombol "Ajukan Ulang"
4. Di modal resubmit:
   - **Part 1 (Ban Depan)**: Edit biaya dari 8000 menjadi 10000
   - Submit resubmit

**Expected Results:**
- ✅ PO status berubah menjadi "Pending"
- ✅ source_data updated:
  ```json
  {
    "parts": [
      {"nama_part": "Ban Depan", "biaya": 10000},
      {"nama_part": "Filter Oli", "biaya": 9000}
    ],
    "locked_approved_idx": [1],
    "item_decisions": [],
    "nominal_approved_locked": 9000,
    "nominal_rejected": 0
  }
  ```
- ✅ total_harga = 10000 (hanya item yang diajukan ulang)
- ✅ total_barang = 1 (hanya item yang diajukan ulang)
- ✅ Part 2 (Filter Oli - 9000) tetap locked dan tidak berubah

**Verify Tab Display:**
- ✅ **Tab Disetujui**: PO muncul, detail show only Part 2 (Filter Oli - 9000) - LOCKED
- ✅ **Tab Pending**: PO muncul, detail show only Part 1 (Ban Depan - 10000) - NEEDS APPROVAL
- ✅ **Tab Ditolak**: PO TIDAK muncul (karena tidak ada rejected items saat ini)

**Critical Verification:**
- ❌ Part 2 (Filter Oli - 9000) **TIDAK BOLEH** muncul di tab Pending
- ❌ Part 2 **TIDAK BOLEH** bisa di-approve/reject ulang
- ✅ Form approval hanya show Part 1 (Ban Depan - 10000)

---

### **Phase 4: Approve Item Resubmitted**

**Steps:**
1. Dari tab "Pending", buka PO
2. Klik "Approve Items"
3. Di modal approval:
   - **Part 1 (Ban Depan - 10000)**: Pilih "Approve" + upload bukti bayar
4. Submit approval

**Expected Results:**
- ✅ PO status tetap "Disetujui"
- ✅ source_data updated:
  ```json
  {
    "locked_approved_idx": [1, 0],
    "item_decisions": [
      {"idx": 0, "action": "approved", "catatan": null}
    ],
    "nominal_approved_locked": 19000,
    "nominal_rejected": 0
  }
  ```
- ✅ total_harga = 19000 (akumulasi semua approved: 9000 + 10000)
- ✅ total_barang = 2
- ✅ can_edit = false (semua items sudah approved)
- ✅ Pembayaran kedua dibuat dengan nominal 10000

**Verify Tab Display:**
- ✅ **Tab Disetujui**: PO muncul, detail show Part 1 & Part 2 (keduanya locked)
- ✅ **Tab Pending**: PO TIDAK muncul (tidak ada item pending)
- ✅ **Tab Ditolak**: PO TIDAK muncul (tidak ada rejected items)

---

## Test Cases Summary

| Test Case | Expected | Status |
|-----------|----------|--------|
| TC-1: PO created dengan 2 items | status=Pending, total=17000 | ⏳ |
| TC-2: Partial approve (1 approved, 1 rejected) | locked_approved_idx=[1], status=Disetujui | ⏳ |
| TC-3: Tab Disetujui show only approved items | Part 2 only | ⏳ |
| TC-4: Tab Ditolak show only rejected items | Part 1 only | ⏳ |
| TC-5: Tab Pending tidak show PO partial | PO not visible | ⏳ |
| TC-6: Resubmit rejected item | status=Pending, total=10000 | ⏳ |
| TC-7: Locked item tidak muncul di tab Pending | Part 2 not visible in Pending | ⏳ |
| TC-8: Locked item tidak bisa di-approve ulang | Form show Part 1 only | ⏳ |
| TC-9: Approve resubmitted item | locked_approved_idx=[1,0], total=19000 | ⏳ |
| TC-10: Final state - all items locked | Both parts in Disetujui, PO not in Pending | ⏳ |

---

## Key Verification Points

### ✅ **Guard Mechanism**
- Locked items di-skip di approveItems (line 841 continue statement)
- $approvedIdx dan $rejectedIdx tidak berisi locked items

### ✅ **Approval Handler**
- approveItemsServicePart filter locked items dari approval cycle
- item_decisions hanya dibuat untuk non-locked items
- locked_approved_idx akumulatif (merge previous + new)

### ✅ **Filter Logic**
- buildServicePartDetails tab "Pending" exclude locked items dan items di item_decisions
- Tab "Disetujui" include approved + locked items
- Tab "Ditolak" include rejected items only

### ✅ **Resubmit Logic**
- resubmitServiceAsuransi detect partial via locked_approved_idx
- Hanya rejected items yang di-update
- locked_approved_idx dan nominal_approved_locked preserved

### ✅ **Index Query**
- PO Pending dengan locked items muncul di tab Disetujui
- PO Pending dengan total_barang = 0 tidak muncul di tab Pending
- Nominal calculation include locked items

---

## Bug Reproduction (Before Fix)

**Problem:** Setelah resubmit item rejected, item yang sudah approved (Part 2 - 9000) ikut muncul di tab Pending dan bisa di-approve ulang.

**Root Cause:**
1. Guard di approveItems tidak skip locked items → masuk ke $approvedIdx
2. approveItemsServicePart tidak filter locked items → build item_decisions untuk ALL items
3. buildServicePartDetails tab "semua" return allParts tanpa filter → locked items tampil di Pending

**Fix Applied:**
1. ✅ Guard strengthened dengan 'continue' statement untuk skip locked items
2. ✅ approveItemsServicePart filter locked items (copy pattern dari service_asuransi)
3. ✅ buildServicePartDetails tab "semua" exclude locked items dan items di item_decisions

---

## Expected vs Actual After Fix

| Aspect | Before Fix | After Fix |
|--------|------------|-----------|
| Locked items di approval form | Muncul, bisa di-approve ulang | Tidak muncul (skipped di guard) |
| item_decisions | Dibuat untuk ALL items | Dibuat hanya untuk non-locked items |
| Tab Pending | Show locked items | Exclude locked items |
| locked_approved_idx | Tidak akumulatif | Akumulatif (merge previous + new) |
| Resubmit behavior | Locked items bisa berubah | Locked items preserved |

---

## Test Execution Date
- **Date**: _____________________
- **Tester**: _____________________
- **Result**: ✅ PASS / ❌ FAIL
- **Notes**: _____________________

---

## Rollback Plan (If Test Fails)

Jika test gagal, rollback changes dengan:
```bash
git checkout HEAD~1 app/Http/Controllers/Admin/PurchaseOrderController.php
```

Dan report issues dengan detail:
1. Test case yang gagal
2. Expected vs actual behavior
3. Screenshot error
4. Database dump untuk reproduce
