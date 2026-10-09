# Clarification: Navtab Design Intention for PO Partial Resubmit

**Date**: 2026-10-07  
**Issue**: Misunderstanding design intention - REVERTED previous fix  
**Status**: ✅ CLARIFIED

---

## Design Intention (Correct Understanding)

PO service incident dengan partial approval (some items approved, some rejected) yang sedang diresubmit **HARUS muncul di 2 navtab sekaligus**:

✅ **Tab Disetujui**: Show **locked approved items**
✅ **Tab Pending**: Show **items yang menunggu approval** (belum decided)

**Reason**: User needs to see:
1. Items yang sudah approved dan locked (di tab Disetujui)
2. Items yang masih pending dan perlu di-approve (di tab Pending)

**Example:**
- PO dengan 2 items: Item A (approved & locked), Item B (rejected → resubmit → pending)
- **Tab Disetujui**: Show PO → detail modal show Item A only
- **Tab Pending**: Show PO → detail modal show Item B only

---

## Previous Fix (INCORRECT ❌)

**What I Did Wrong:**
- Excluded PO dengan `locked_approved_idx` dari tab Pending
- Logic: "PO sudah muncul di tab Disetujui, jadi tidak perlu di tab Pending"
- **Result**: PO partial resubmit tidak muncul di tab Pending, user cannot access pending items

**Why It Was Wrong:**
- Ignored comment in code (line 44-46): "PO partial muncul di dua tab sekaligus"
- Misunderstood design intention: dual-tab display with different item filters
- Broke workflow: user cannot find PO to approve pending items

---

## Correct Implementation (REVERTED ✅)

### **Index Navigation Tabs**

**Tab Disetujui Filter:**
```php
// Include: PO Disetujui OR PO Pending dengan locked items
$query->where(function ($q) {
    $q->where('status', 'Disetujui')
      ->orWhere(function ($q2) {
          $q2->where('status', 'Pending')
             ->whereRaw("JSON_LENGTH(JSON_EXTRACT(source_data, '$.locked_approved_idx')) > 0");
      });
});
```

**Tab Pending Filter:**
```php
// Include: PO Pending dengan total_barang > 0
// This INCLUDES PO partial resubmit (has locked items but also has pending items)
$query->where('status', 'Pending')->where('total_barang', '>', 0);
```

**Result**: PO partial resubmit muncul di **KEDUA** tab ✅

---

### **Detail Modal Tab Filter** (buildServicePartDetails)

When user clicks PO dari different navtab, detail modal filters items based on `$tab` parameter:

**From Tab Disetujui (`$tab = 'Disetujui'`):**
```php
// Show: approved items + locked items
$parts = collect($allParts)
    ->filter(fn($p, $i) =>
        ($decMap[$i]['action'] ?? '') === 'approved'
        || in_array($i, $lockedApprovedIdx)
    )
    ->all();
```

**From Tab Pending (`$tab = 'semua'` or `'Pending'`):**
```php
// Show: items yang belum decided (exclude locked, exclude approved/rejected)
$parts = collect($allParts)
    ->filter(fn($p, $i) => 
        !in_array($i, $lockedApprovedIdx) && 
        !isset($decMap[$i])
    )
    ->all();
```

**Result**: Same PO shows different items based on which navtab user clicked from ✅

---

## Behavior After Clarification

| Scenario | Tab Disetujui | Tab Pending | Detail from Disetujui | Detail from Pending |
|----------|---------------|-------------|-----------------------|---------------------|
| **Fresh PO** | ❌ | ✅ | N/A | Show all items |
| **Partial Approval** | ✅ | ✅ | Show locked items | Show pending items |
| **Full Approved** | ✅ | ❌ (total_barang=0) | Show all items | N/A |

**Key Point**: PO partial resubmit muncul di **2 navtab** dengan **item filter berbeda di detail modal**.

---

## Why This Design Makes Sense

**User Journey:**
1. User creates PO dengan 2 items
2. Approver partial approve (1 approved, 1 rejected)
3. User resubmit rejected item
4. **Tab Disetujui**: Approver can see items that were already approved (read-only, locked)
5. **Tab Pending**: Approver can see items that need approval (actionable)
6. Same PO, different context, different items shown

**Benefits:**
- ✅ Transparency: User can see both approved and pending items
- ✅ Workflow clarity: Pending items are clearly in "Pending" tab
- ✅ No confusion: Detail modal filters items based on context
- ✅ Audit trail: History of approved items preserved in Disetujui tab

---

## Files Status

**No Changes Needed** - Reverted to original implementation which was correct

1. **app/Http/Controllers/Admin/PurchaseOrderController.php**
   - Tab Pending filter: Include PO partial (original) ✅
   - Stats count: Include PO partial (original) ✅
   - buildServicePartDetails: Tab-based item filter (already correct) ✅

---

## Lesson Learned

**Always read existing comments in code** - They often explain design decisions!

Comment at line 44-46 clearly stated:
```php
// PO partial yang item-nya sebagian pending muncul di dua tab sekaligus:
// - Di Disetujui: hanya expand item approved
// - Di Pending: hanya expand item yang belum di-approve
```

This was the **design specification**, not a bug to fix.

---

**Status**: ✅ CLARIFIED - Original implementation was correct
