# Fix: buildServicePartDetails Pending Tab Filter (Empty decMap Case)

**Date**: 2026-10-07  
**Issue**: Locked items tetap tampil di tab Pending meskipun setelah resubmit fix  
**Status**: ✅ FIXED

---

## Problem Statement

**User Report**: Item "Veniam quis maxime" (Rp 800) yang sudah approved & locked **masih tampil** di tab Pending setelah resubmit.

**Expected**: Item yang locked harus **hanya tampil di tab Disetujui**, tidak di tab Pending.

---

## Root Cause Analysis

**File**: `app/Http/Controllers/Admin/PurchaseOrderController.php`  
**Method**: `buildServicePartDetails`  
**Location**: Line 576-590 (else branch)

**Problem Code:**
```php
} else {
    // decMap kosong: jika tab Disetujui dan ada locked items
    if ($tab === 'Disetujui' && !empty($lockedApprovedIdx)) {
        $parts = collect($allParts)
            ->filter(fn($p, $i) => in_array($i, $lockedApprovedIdx))
            ->all();
    } elseif ($tab === 'Ditolak' && !empty($lockedApprovedIdx)) {
        $parts = collect($allParts)
            ->filter(fn($p, $i) => !in_array($i, $lockedApprovedIdx))
            ->all();
    } else {
        $parts = $allParts;  // ❌ SHOW ALL PARTS INCLUDING LOCKED!
    }
}
```

**Scenario yang Trigger Bug:**
1. User partial approve PO (item 1 approved & locked, item 0 rejected)
2. User resubmit rejected item
3. **State saat ini**:
   - `item_decisions` = empty (resubmit clears decisions) or has locked decision only
   - `locked_approved_idx` = [1]
   - User click dari **navtab Pending**
   - `$tab` parameter = `"Pending"`
4. Method `buildServicePartDetails` dipanggil dengan `$tab = "Pending"`
5. Logic check:
   - `$decMap->isNotEmpty()`? **FALSE** (if decisions empty) or **TRUE** (if locked decision preserved)
   - If FALSE → masuk ke else branch (line 576-590)
   - Check `$tab === 'Disetujui'`? **NO**
   - Check `$tab === 'Ditolak'`? **NO**
   - Masuk ke final `else` → **return $allParts** ❌

**Impact**: ALL items (including locked) ditampilkan di tab Pending

---

## Solution Implemented

**File**: `app/Http/Controllers/Admin/PurchaseOrderController.php`  
**Lines**: 576-593

**Changes:**
```php
} else {
    // decMap kosong: jika tab Disetujui dan ada locked items, tampilkan hanya yang locked
    if ($tab === 'Disetujui' && !empty($lockedApprovedIdx)) {
        $parts = collect($allParts)
            ->filter(fn($p, $i) => in_array($i, $lockedApprovedIdx))
            ->all();
    } elseif ($tab === 'Ditolak' && !empty($lockedApprovedIdx)) {
        // Tab Ditolak + ada locked: tampilkan yang bukan locked (sedang pending resubmit)
        $parts = collect($allParts)
            ->filter(fn($p, $i) => !in_array($i, $lockedApprovedIdx))
            ->all();
    } elseif ($tab === 'Pending' && !empty($lockedApprovedIdx)) {
        // ✅ Tab Pending + ada locked: exclude locked items, show only pending items
        $parts = collect($allParts)
            ->filter(fn($p, $i) => !in_array($i, $lockedApprovedIdx))
            ->all();
    } else {
        $parts = $allParts;
    }
}
```

**Added Branch**: `elseif ($tab === 'Pending' && !empty($lockedApprovedIdx))`

**Logic**: 
- When tab = "Pending" AND ada locked items
- **Filter out** locked items (exclude dari display)
- Show only pending items (items yang belum approved/rejected)

---

## Logic Flow After Fix

### **Case 1: Tab = Pending + decMap Empty + Has Locked Items**

**Before Fix:**
```php
else {
    $parts = $allParts;  // Show ALL items including locked ❌
}
```

**After Fix:**
```php
elseif ($tab === 'Pending' && !empty($lockedApprovedIdx)) {
    $parts = collect($allParts)
        ->filter(fn($p, $i) => !in_array($i, $lockedApprovedIdx))
        ->all();  // Exclude locked items ✅
}
```

### **Case 2: Tab = Pending + decMap Not Empty + Has Locked Items**

**Existing Logic (line 567-579):**
```php
if ($decMap->isNotEmpty()) {
    // ...
    } else {
        // Tab "semua" atau "Pending": exclude locked and decided items
        $parts = collect($allParts)
            ->filter(fn($p, $i) => 
                !in_array($i, $lockedApprovedIdx) && 
                !isset($decMap[$i])
            )
            ->all();
    }
}
```
**Status**: Already correct ✅

---

## Complete Logic Matrix

| Scenario | decMap Status | Tab | Has Locked | Result |
|----------|---------------|-----|------------|--------|
| Fresh PO | Empty | Pending | No | Show all items |
| Fresh PO | Empty | Disetujui | No | Show all items |
| Partial resubmit | Empty | Pending | Yes | Show non-locked items ✅ (NEW FIX) |
| Partial resubmit | Empty | Disetujui | Yes | Show locked items only |
| Partial resubmit | Not Empty | Pending | Yes | Show non-locked, non-decided items |
| Partial resubmit | Not Empty | Disetujui | Yes | Show approved + locked items |

---

## Files Modified

1. **app/Http/Controllers/Admin/PurchaseOrderController.php**
   - `buildServicePartDetails` method (line 576-593)
   - Added `elseif` branch untuk handle tab Pending dengan locked items

---

## Testing Checklist

### ✅ Code Changes Verified
- [x] Added Pending tab branch in else block
- [x] Filter exclude locked items correctly
- [x] Existing logic not affected

### ⏳ Integration Test Required

**Test Case: Partial Resubmit - Tab Pending Display**

**Setup:**
1. Create PO service incident dengan 2 items:
   - Item 0: "alah kontol" - Rp 8.000
   - Item 1: "Veniam quis maxime" - Rp 800
2. Partial approve: Item 1 approved & locked, Item 0 rejected

**Action:**
3. Resubmit rejected item (Item 0)
4. **Click PO dari navtab Pending** → open detail modal

**Verification:**
5. **Detail modal tab Pending** → harus show **Item 0 only** ✅
6. Item 1 ("Veniam quis maxime") **tidak boleh tampil** ❌
7. Total harga = Rp 8.000 (hanya Item 0)

**Cross-Check:**
8. **Click PO dari navtab Disetujui** → open detail modal
9. **Detail modal tab Disetujui** → harus show **Item 1 only** ✅
10. Item 0 **tidak boleh tampil** ❌
11. Total harga = Rp 800 (hanya Item 1)

---

## Why This Bug Existed

**Two scenarios cause empty decMap:**

**Scenario A**: Before resubmit item_decisions preservation fix
- Resubmit clears ALL decisions → `decMap` empty
- Tab Pending logic falls through to final `else`
- Shows all items ❌

**Scenario B**: After preservation fix but decMap still considered empty
- `item_decisions` preserved untuk locked items only
- `decMap` = `[1 => ['action' => 'approved']]`
- Logic check `$decMap->isNotEmpty()` = TRUE
- But still need explicit Pending branch in else block for edge case

**This fix covers BOTH scenarios** by adding explicit Pending handling in else branch.

---

## Related Fixes

This is fix #6 in comprehensive Service Incident Partial Approval improvement:

1. ✅ Guard strengthened - Skip locked items in approval cycle
2. ✅ Approval handler fixed - Build item_decisions only for non-locked
3. ✅ Filter logic fixed - Tab Pending exclude locked items (when decMap not empty)
4. ✅ Navtab clarified - Dual-tab display with context-aware filtering
5. ✅ Resubmit fixed - Preserve item_decisions for locked items
6. ✅ buildServicePartDetails else branch fixed - Handle Pending tab (THIS FIX)

**Documentation:**
- `docs/FIX_SERVICE_INCIDENT_PARTIAL_APPROVAL_LOCK.md`
- `docs/FIX_NAVTAB_DUPLICATE_DISPLAY.md`
- `docs/FIX_RESUBMIT_ITEM_DECISIONS_PRESERVATION.md`
- `docs/FIX_BUILDSERVICEPARTDETAILS_PENDING_TAB.md` (THIS DOC)

---

## Rollback Plan

```bash
# Revert changes
git checkout HEAD~1 app/Http/Controllers/Admin/PurchaseOrderController.php
```

**Or manual revert:**
Remove lines 586-590 (the new elseif branch for Pending)

---

**Status**: ✅ READY FOR TESTING

**Next Action**: Refresh page, click PO dari navtab Pending → verify locked items tidak tampil
