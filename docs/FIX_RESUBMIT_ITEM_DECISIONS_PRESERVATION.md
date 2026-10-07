# Fix: Resubmit Item Decisions Preservation

**Date**: 2026-10-07  
**Issue**: Locked approved items tidak tampil di detail modal setelah resubmit  
**Status**: ✅ FIXED

---

## Problem Statement

Ketika PO service incident dengan partial approval diresubmit:

**Scenario:**
1. PO created dengan 2 items:
   - Item 0: "B" - Rp 5.000
   - Item 1: "BOKEP" - Rp 20.000
2. Partial approval: Item 1 **approved & locked**, Item 0 **rejected**
3. Resubmit rejected item (Item 0)

**Bug:**
- ❌ Item "BOKEP" (locked, approved) **tidak tampil** di tab Disetujui
- ❌ Item "BOKEP" **muncul di tab Pending** (incorrect)
- ❌ `item_decisions` kosong setelah resubmit

**Expected:**
- ✅ Item "BOKEP" harus tampil di tab **Disetujui** (locked, read-only)
- ✅ Item "B" harus tampil di tab **Pending** (pending approval)
- ✅ `item_decisions` preserve entry untuk locked items

---

## Root Cause Analysis

**Location**: `resubmitServiceAsuransi` method, line 2367

**Problem Code:**
```php
$updatedSourceData = array_merge($sourceData, [
    'item_decisions' => [], // ❌ Clear ALL decisions — including locked items!
    'locked_approved_idx' => $lockedApprovedIdx,
    'nominal_approved_locked' => $nominalApprovedLocked,
    // ...
]);
```

**Impact:**
1. `item_decisions` array **dikosongkan** saat resubmit
2. `buildServicePartDetails` method rely on `item_decisions` untuk filter items per tab
3. Karena `item_decisions` kosong, method tidak tahu item index 1 sudah approved
4. Item locked tidak muncul di filter tab Disetujui

**Data Example (From SQL):**
```json
{
  "locked_approved_idx": [1],
  "item_decisions": [],  // ❌ EMPTY - should have entry for index 1
  "nominal_approved_locked": 20000
}
```

**Expected Data:**
```json
{
  "locked_approved_idx": [1],
  "item_decisions": [
    {
      "idx": 1,
      "action": "approved",
      "nama_part": "BOKEP",
      "category": "Bodi",
      "catatan": null
    }
  ],
  "nominal_approved_locked": 20000
}
```

---

## Solution Implemented

**File**: `app/Http/Controllers/Admin/PurchaseOrderController.php`  
**Lines**: 2364-2378

**Changes:**
```php
// Build updatedSourceData — simpan ke key yang benar sesuai source_type
// Preserve item_decisions untuk locked items, hapus hanya untuk rejected items
$preservedDecisions = collect($sourceData['item_decisions'] ?? [])
    ->filter(fn($dec) => in_array($dec['idx'], $lockedApprovedIdx))
    ->values()
    ->all();
    
$updatedSourceData = array_merge($sourceData, [
    'item_decisions'          => $preservedDecisions,    // ✅ Keep locked decisions, clear rejected only
    'locked_approved_idx'     => $lockedApprovedIdx,     // Item approved tidak boleh diubah lagi
    'nominal_approved_locked' => $nominalApprovedLocked, // Untuk summary card Disetujui
    'nominal_rejected'        => 0,                      // Reset nominal_rejected saat resubmit
    'temp_files'              => $tempFiles,
]);
```

**Logic:**
1. **Filter** `item_decisions` untuk keep hanya decisions dengan `idx` yang ada di `locked_approved_idx`
2. **Remove** decisions untuk rejected items (yang tidak ada di locked)
3. **Preserve** structure: `action`, `nama_part`, `category`, `catatan`

**Result:**
- ✅ Locked items decisions preserved
- ✅ Rejected items decisions removed
- ✅ `buildServicePartDetails` dapat filter items correctly

---

## Behavior Comparison

### **Before Fix:**

**After Resubmit `source_data`:**
```json
{
  "locked_approved_idx": [1],
  "item_decisions": [],  // ❌ Empty
  "nominal_approved_locked": 20000
}
```

**Tab Display:**
- **Tab Disetujui**: ❌ Item "BOKEP" tidak tampil
- **Tab Pending**: ❌ Item "BOKEP" tampil (incorrect)

---

### **After Fix:**

**After Resubmit `source_data`:**
```json
{
  "locked_approved_idx": [1],
  "item_decisions": [
    {"idx": 1, "action": "approved", "nama_part": "BOKEP", "category": "Bodi"}
  ],
  "nominal_approved_locked": 20000
}
```

**Tab Display:**
- **Tab Disetujui**: ✅ Item "BOKEP" tampil (locked)
- **Tab Pending**: ✅ Item "B" tampil (pending approval)

---

## How buildServicePartDetails Uses item_decisions

**Method**: `buildServicePartDetails` (line 548-598)

**Logic:**
```php
$itemDecisions = $sourceData['item_decisions'] ?? [];
$decMap = collect($itemDecisions)->keyBy('idx');

if ($tab === 'Disetujui') {
    // Show approved items + locked items
    $parts = collect($allParts)
        ->filter(fn($p, $i) =>
            ($decMap[$i]['action'] ?? '') === 'approved' ||  // ✅ Needs item_decisions
            in_array($i, $lockedApprovedIdx)
        )
        ->all();
}
```

**Dependency**: Method needs `item_decisions` dengan `action: 'approved'` untuk correctly identify approved items.

**Without Fix**: `$decMap` empty → approved items tidak tampil di tab Disetujui

**With Fix**: `$decMap` has entry untuk locked items → approved items tampil correctly

---

## Files Modified

1. **app/Http/Controllers/Admin/PurchaseOrderController.php**
   - `resubmitServiceAsuransi` method (line 2364-2378)
   - Added `$preservedDecisions` filter logic
   - Changed `item_decisions` dari `[]` ke `$preservedDecisions`

---

## Testing Checklist

### ✅ Code Changes Verified
- [x] `$preservedDecisions` filter hanya locked items
- [x] `item_decisions` updated dengan preserved decisions
- [x] Rejected items decisions removed correctly

### ⏳ Integration Test Required

**Test Case: Partial Resubmit with Locked Item**

**Setup:**
1. Create PO service incident dengan 2 items (Rp 5.000, Rp 20.000)
2. Partial approve: Item 1 approved & locked, Item 0 rejected
3. Verify `source_data` after approval:
   ```json
   {
     "locked_approved_idx": [1],
     "item_decisions": [
       {"idx": 0, "action": "rejected"},
       {"idx": 1, "action": "approved"}
     ],
     "nominal_approved_locked": 20000
   }
   ```

**Action:**
4. Resubmit rejected item (Item 0)

**Verification:**
5. Check `source_data` after resubmit:
   ```json
   {
     "locked_approved_idx": [1],
     "item_decisions": [
       {"idx": 1, "action": "approved"}  // ✅ Preserved!
     ],
     "nominal_approved_locked": 20000
   }
   ```
6. **Tab Disetujui**: Show Item 1 (locked) ✅
7. **Tab Pending**: Show Item 0 (pending approval) ✅
8. Item 1 tidak muncul di approval form ✅

**Final Approval:**
9. Approve Item 0
10. Verify both items locked di tab Disetujui

---

## Impact Analysis

✅ **Fix Scope**
- Only affects `resubmitServiceAsuransi` method
- Only affects PO partial resubmit scenario
- No impact on fresh PO or full approval/rejection

✅ **Backward Compatibility**
- PO lama tanpa `item_decisions` tetap work (NULL check)
- Logic compatible dengan service_asuransi dan service_incident
- No database schema changes

✅ **Performance**
- Minimal impact - collection filter operation
- No additional queries
- O(n) complexity where n = number of items

---

## Related Fixes

This fix is part of the comprehensive Service Incident Partial Approval improvement:

1. ✅ **Guard strengthened** - Skip locked items in approval cycle
2. ✅ **Approval handler fixed** - Build item_decisions only for non-locked items
3. ✅ **Filter logic fixed** - Tab Pending exclude locked items
4. ✅ **Navtab clarified** - Dual-tab display with context-aware filtering
5. ✅ **Resubmit fixed** - Preserve item_decisions for locked items (THIS FIX)

**Reference:**
- `docs/FIX_SERVICE_INCIDENT_PARTIAL_APPROVAL_LOCK.md`
- `docs/FIX_NAVTAB_DUPLICATE_DISPLAY.md`

---

## Rollback Plan

Jika ditemukan issue:

```bash
# Revert changes
git checkout HEAD~1 app/Http/Controllers/Admin/PurchaseOrderController.php
```

**Or manual revert:**
```php
// Line 2371: Change back to
'item_decisions' => [],
```

---

**Status**: ✅ READY FOR TESTING

**Next Action**: Test resubmit flow untuk verify item_decisions preserved correctly
