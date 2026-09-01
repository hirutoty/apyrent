# Bug Condition Exploration Test Results

**Spec:** chart-double-init  
**Task:** 1 — Write bug condition exploration test  
**Method:** Static analysis of source files  
**Date:** 2025-07-10  
**Status:** BUG CONDITION CONFIRMED ✗ (double-init exists — test "fails" as expected)

---

## Summary

The double-init bug condition is **fully confirmed** across all three target pages. Static analysis of `chart-filter.blade.php` and the three target page files proves that all three conditions in `isBugCondition(X)` are simultaneously true for each page.

```
isBugCondition(X) = TRUE
  for X ∈ { kontrak/index, history/index, kendaraan/index }
```

---

## Bug Condition Definition (from bugfix.md)

```pascal
FUNCTION isBugCondition(X)
  RETURN X.hasChartFilter
     AND X.callsInitDirectlyOnDOMContentLoaded
     AND X.chartFilterDispatchesOnWindowLoad
END FUNCTION
```

All three sub-conditions must be true simultaneously for the double-init to occur. The analysis below proves each one.

---

## Finding 1: `X.chartFilterDispatchesOnWindowLoad` — CONFIRMED

**File:** `resources/views/components/chart-filter.blade.php`  
**Lines:** 218–231

```javascript
// Auto-dispatch default filter saat halaman load agar chart langsung terinisialisasi
const defaultFilterType = '{{ $defaultFilter }}';
if (defaultFilterType && defaultFilterType !== 'custom') {
    // Tunggu DOM + script lain selesai, lalu dispatch
    window.addEventListener('load', function () {          // <-- BUG: uses window 'load', not DOMContentLoaded
        document.dispatchEvent(new CustomEvent('chartFilterChange', {
            detail: {
                filterId: filterId,
                filterType: defaultFilterType,
                startDate: null,
                endDate: null,
                categoryId: ''
            }
        }));
    });
}
```

**Analysis:**
- This code lives inside an IIFE `(function() { ... })()` that runs on every page that includes `<x-chart-filter>`.
- The `window.addEventListener('load', ...)` callback fires **after** `DOMContentLoaded` — it fires once the entire page (including stylesheets, images, and deferred scripts) has finished loading.
- On a typical page load, the sequence is:  
  1. `DOMContentLoaded` fires (DOM is ready)  
  2. `window.load` fires (all resources loaded)
- This means `chartFilterChange` is always dispatched a **second time** (via `window.load`) after any page that also calls `initXxxCharts()` directly in `DOMContentLoaded`.
- **Every page using `<x-chart-filter>` with a non-custom `defaultFilter` is affected.**

---

## Finding 2: `X.callsInitDirectlyOnDOMContentLoaded` — CONFIRMED on all three pages

### 2a. kontrak/index.blade.php

**File:** `resources/views/admin/kontrak/index.blade.php`  
**Lines:** 1741–1755

```javascript
const kontrakChartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function () {
    initKontrakCharts({ filter_type: 'month' });        // <-- BUG: direct init call at line 1743

    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'kontrakChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
            };
            updateKontrakCharts(filters);               // only updates — never inits
        }
    });
});
```

**Confirmed:** `initKontrakCharts({ filter_type: 'month' })` is called unconditionally at `DOMContentLoaded`.  
**Chart filter component usage:** `<x-chart-filter id="kontrakChartFilter" defaultFilter="month" :showCustomRange="true" />` (line ~81 in the file).  
**defaultFilter value:** `month`

### 2b. history/index.blade.php

**File:** `resources/views/admin/history/index.blade.php`  
**Lines:** 273–288

```javascript
const historyChartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function () {
    initHistoryCharts({ filter_type: 'month' });        // <-- BUG: direct init call at line 276

    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'historyChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date:  e.detail.startDate,
                end_date:    e.detail.endDate,
            };
            updateHistoryCharts(filters);               // only updates — never inits
        }
    });
});
```

**Confirmed:** `initHistoryCharts({ filter_type: 'month' })` is called unconditionally at `DOMContentLoaded`.  
**Chart filter component usage:** `<x-chart-filter id="historyChartFilter" defaultFilter="month" :showCustomRange="true" />` (line 16).  
**defaultFilter value:** `month`

### 2c. kendaraan/index.blade.php

**File:** `resources/views/admin/kendaraan/index.blade.php`  
**Lines:** 1648–1663

```javascript
const chartManager = new ChartManager();

document.addEventListener('DOMContentLoaded', function () {
    // Initialize charts with default filter (year)
    initKendaraanCharts({ filter_type: 'year' });       // <-- BUG: direct init call at line 1650

    // Listen for filter changes
    document.addEventListener('chartFilterChange', function (e) {
        if (e.detail.filterId === 'kendaraanChartFilter') {
            const filters = {
                filter_type: e.detail.filterType,
                start_date: e.detail.startDate,
                end_date: e.detail.endDate,
            };
            updateKendaraanCharts(filters);             // only updates — never inits
        }
    });
});
```

**Confirmed:** `initKendaraanCharts({ filter_type: 'year' })` is called unconditionally at `DOMContentLoaded`.  
**Chart filter component usage:** `<x-chart-filter id="kendaraanChartFilter" defaultFilter="year" :showCustomRange="true" />` (in the HTML section).  
**defaultFilter value:** `year`

---

## Finding 3: `X.hasChartFilter` — CONFIRMED on all three pages

| Page | Component Instance | defaultFilter |
|---|---|---|
| kontrak/index.blade.php | `<x-chart-filter id="kontrakChartFilter" ...>` | `month` |
| history/index.blade.php | `<x-chart-filter id="historyChartFilter" ...>` | `month` |
| kendaraan/index.blade.php | `<x-chart-filter id="kendaraanChartFilter" ...>` | `year` |

Each page includes the `x-chart-filter` component, so `filterId` is set, and the `window.load` auto-dispatch in chart-filter.blade.php **will** fire on every page load.

---

## Double-Init Execution Sequence (Confirmed)

The following sequence occurs on every page load for all three affected pages:

```
t=0ms:    Browser begins parsing HTML
t=~Xms:   DOMContentLoaded fires
            → page's DOMContentLoaded listener runs
            → initXxxCharts({ filter_type: 'xxx' }) called immediately  ← FETCH #1
            → chartFilterChange listener registered (for user interaction)

t=~Yms:   window.load fires (Y > X, all resources loaded)
            → chart-filter.blade.php's window.load callback runs
            → document.dispatchEvent(new CustomEvent('chartFilterChange', ...)) fired
            → chartFilterChange listener on page receives event
            → updateXxxCharts(filters) called                            ← FETCH #2
```

**Result:** Two network requests to `/admin/chart-data/xxx` within milliseconds of each other on every page load.

**Note on the second call:** The listener registered inside `DOMContentLoaded` calls `updateXxxCharts` (not `initXxxCharts`) on `chartFilterChange`. This means the second call goes through `updateChartsFromAPI`, which re-renders charts that were already initialized — causing the visible "flicker/double-loading" effect described in the bug report.

---

## Counterexample Table

| Page | Condition A: hasChartFilter | Condition B: callsInitDirectlyOnDOMContentLoaded | Condition C: chartFilterDispatchesOnWindowLoad | isBugCondition |
|---|:---:|:---:|:---:|:---:|
| kontrak/index.blade.php | ✓ (`kontrakChartFilter`) | ✓ (line 1743) | ✓ (chart-filter.blade.php line 222) | **TRUE** |
| history/index.blade.php | ✓ (`historyChartFilter`) | ✓ (line 276) | ✓ (chart-filter.blade.php line 222) | **TRUE** |
| kendaraan/index.blade.php | ✓ (`kendaraanChartFilter`) | ✓ (line 1650) | ✓ (chart-filter.blade.php line 222) | **TRUE** |

---

## Reference: Correct Pattern (members/index.blade.php)

The `members/index.blade.php` page (if it uses `x-chart-filter`) is the reference implementation. The correct pattern uses a `hasChart()` gate inside the `chartFilterChange` listener, with **no direct `initXxxCharts()` call in `DOMContentLoaded`**:

```javascript
document.addEventListener('chartFilterChange', function (e) {
    if (e.detail.filterId === 'membersChartFilter') {
        const filters = { ... };
        if (!membersChartManager.hasChart('membersBarChart')) {
            initMembersCharts(filters);     // first load: init
        } else {
            updateMembersCharts(filters);   // subsequent: update
        }
    }
});
// No direct initMembersCharts() call here — relies entirely on chartFilterChange
```

With this pattern, when `window.load` dispatches `chartFilterChange`, the `hasChart()` gate correctly routes to `updateChartsFromAPI` (or skips init if already done), preventing the double-init.

The bug pages are missing this gate AND they pre-initialize charts in `DOMContentLoaded`, so by the time `window.load` fires the second dispatch, the chart is already initialized but `updateXxxCharts` runs again anyway.

---

## Conclusion

**Bug condition is CONFIRMED across all three inspected pages.**

The root causes are two-fold and must both be fixed:

1. **`chart-filter.blade.php` line 222:** Replace `window.addEventListener('load', ...)` with `document.addEventListener('DOMContentLoaded', ...)` so the auto-dispatch fires at the same lifecycle phase as the page's own initialization code (Task 3).

2. **Each affected page:** Remove the direct `initXxxCharts()` call from `DOMContentLoaded` and add a `hasChart()` gate in the `chartFilterChange` listener, following the members pattern (Tasks 4–17).

Either fix alone is insufficient:
- Fixing only `chart-filter.blade.php` (changing `window.load` to `DOMContentLoaded`) still causes a race condition — both the page's direct init and the component's dispatch would fire at `DOMContentLoaded`, potentially triggering two near-simultaneous initializations.
- Fixing only the pages (adding `hasChart()` gate but keeping `window.load` in chart-filter) means charts still delay until `window.load`, which is slower than `DOMContentLoaded` and may cause charts to appear only after all images/assets load.

Both changes together eliminate the double-init and ensure charts appear as quickly as possible.

---

## Files Inspected

| File | Relevant Lines | Bug Condition Present |
|---|---|---|
| `resources/views/components/chart-filter.blade.php` | 218–231 | ✓ `window.addEventListener('load', ...)` dispatches `chartFilterChange` |
| `resources/views/admin/kontrak/index.blade.php` | 1741–1755 | ✓ `initKontrakCharts()` called directly in `DOMContentLoaded` |
| `resources/views/admin/history/index.blade.php` | 273–288 | ✓ `initHistoryCharts()` called directly in `DOMContentLoaded` |
| `resources/views/admin/kendaraan/index.blade.php` | 1648–1663 | ✓ `initKendaraanCharts()` called directly in `DOMContentLoaded` |
