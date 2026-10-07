# Fix Summary: Service Incident Partial Approval Lock

**Date**: 2026-10-07  
**Issue**: Item yang sudah approved ikut muncul di halaman Pending saat resubmit item rejected  
**Status**: ✅ FIXED

---

## Problem Statement

Ketika PO service incident dengan 2 item (8000 dan 9000) di-approve secara partial:
- Item 9000 → **Approved**
- Item 8000 → **Rejected**

Lalu item rejected (8000) diedit menjadi 10000 dan diajukan ulang:

**Bug:**
- ❌ Item yang sudah approved (9000) ikut muncul di tab Pending
- ❌ Item approved bisa di-approve/reject ulang
- ❌ Locked mechanism tidak berfungsi

**Expected:**
- ✅ Item approved (9000) harus locked di tab Disetujui
- ✅ Item approved tidak boleh muncul di tab Pending
- ✅ Hanya item rejected (10000) yang tampil untuk approval

---

## Root Cause Analysis

### 1. **Guard Insufficient** (approveItems)
- **Location**: `app/Http/Controllers/Admin/PurchaseOrderController.php` line 838-840
- **Problem**: Locked items masih masuk ke `$approvedIdx` meskipun di-override
- **Impact**: Locked items diproses ulang di handler

### 2. **Missing Locked Filter** (approveItemsServicePart)
- **Location**: line 1157-1162
- **Problem**: `$allPartDecisions` dibuat untuk ALL parts, termasuk locked items
- **Impact**: item_decisions tidak skip locked items, causing them to re-enter approval cycle

### 3. **Tab Filter Incomplete** (buildServicePartDetails)
- **Location**: line 567
- **Problem**: Tab "semua" return `$allParts` tanpa filter locked items
- **Impact**: Locked items tampil di tab Pending saat PO partial resubmit

---

## Solution Implemented

### **Fix 1: Strengthen Guard di approveItems**
**File**: `app/Http/Controllers/Admin/PurchaseOrderController.php`  
**Lines**: 838-855

**Changes:**
```php
// BEFORE: Override action tapi tetap masuk ke $approvedIdx
if (in_array((int)$idx, $lockedApprovedIdx)) {
    $action = 'approved';
}
if ($action === 'approved') $approvedIdx[] = (int) $idx;

// AFTER: Skip locked items completely
if (in_array((int)$idx, $lockedApprovedIdx)) {
    continue; // Skip locked items - jangan masukkan ke approval cycle
}
if ($action === 'approved') $approvedIdx[] = (int) $idx;
```

**Impact:**
- ✅ Locked items tidak masuk ke `$approvedIdx` dan `$rejectedIdx`
- ✅ Handler hanya proses items yang baru diputuskan

---

### **Fix 2: Copy Pattern dari Service Asuransi ke approveItemsServicePart**
**File**: `app/Http/Controllers/Admin/PurchaseOrderController.php`  
**Lines**: 1108-1183

**Changes:**
```php
// ── Add locked_approved_idx extraction ──
$lockedApprovedIdx = array_map('intval', $sourceData['locked_approved_idx'] ?? []);

// ── Filter approved/rejected index untuk exclude locked items ──
$approvedIdx = array_values(array_filter($approvedIdx, fn($i) => !in_array($i, $lockedApprovedIdx)));
$rejectedIdx = array_values(array_filter($rejectedIdx, fn($i) => !in_array($i, $lockedApprovedIdx)));

// ── Build item_decisions hanya untuk non-locked items ──
$allPartDecisions = [];
foreach ($parts as $idx => $part) {
    if (in_array($idx, $lockedApprovedIdx)) continue; // skip locked
    $allPartDecisions[] = [...];
}

// ── Merge locked_approved_idx secara akumulatif ──
$prevLockedIdx = array_map('intval', $sourceData['locked_approved_idx'] ?? []);
$newLockedIdx  = array_unique(array_merge($prevLockedIdx, $approvedIdx));
```

**Pattern Copied From:** `approveItemsServiceAsuransi` (line 1310-1385)

---

### **Fix 3: Update Filter Logic di buildServicePartDetails**
**File**: `app/Http/Controllers/Admin/PurchaseOrderController.php`  
**Lines**: 567-571

**Changes:**
```php
// BEFORE: Tab "semua" show all parts
} else {
    $parts = $allParts;
}

// AFTER: Tab "semua" exclude locked items
} else {
    $parts = collect($allParts)
        ->filter(fn($p, $i) => 
            !in_array($i, $lockedApprovedIdx) && 
            !isset($decMap[$i])
        )
        ->all();
}
```

---

## Files Modified

1. **app/Http/Controllers/Admin/PurchaseOrderController.php**
   - Guard strengthened (line 838-855)
   - approveItemsServicePart fixed (line 1108-1183)
   - buildServicePartDetails filter updated (line 567-571)

2. **docs/TEST_SERVICE_INCIDENT_PARTIAL_APPROVAL.md** (NEW)
   - Integration test plan dengan 10 test cases

---

## Testing Checklist

### ✅ Code Changes Verified
- [x] Locked items di-skip di approveItems guard
- [x] item_decisions hanya dibuat untuk non-locked items
- [x] locked_approved_idx akumulatif
- [x] Tab Pending exclude locked items

### ⏳ Integration Test Required
- [ ] Full partial approval flow (refer to test doc)

---

## Behavior Comparison

| Aspect | Before Fix | After Fix |
|--------|------------|-----------|
| Locked items in form | Muncul ❌ | Tidak muncul ✅ |
| Tab Pending | Show locked ❌ | Exclude locked ✅ |
| locked_approved_idx | Tidak akumulatif | Akumulatif ✅ |

---

**Status**: ✅ READY FOR TESTING
