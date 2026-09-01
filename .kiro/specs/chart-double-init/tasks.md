# Implementation Plan

- [ ] 1. Write bug condition exploration test
  - **Property 1: Bug Condition** - Double Window.load Dispatch in chart-filter.blade.php
  - **IMPORTANT**: Write this property-based test BEFORE implementing the fix
  - **CRITICAL**: This test MUST FAIL on unfixed code — failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **GOAL**: Surface counterexamples that demonstrate that double-fetch occurs on unfixed code
  - **Scoped PBT Approach**: Scope the property to the concrete failing pattern — pages using `x-chart-filter` where `window.load` dispatch is still present in chart-filter.blade.php
  - Inspect `resources/views/components/chart-filter.blade.php` for presence of `window.addEventListener('load', ...)` that dispatches `chartFilterChange`
  - Inspect target pages (e.g. kontrak/index.blade.php, history/index.blade.php) for `initXxxCharts()` called directly inside `DOMContentLoaded` — confirming the double-init path exists
  - Write a static analysis property test (or browser-side instrumentation): for any page where `isBugCondition(X)` is true, assert that the page's JS does NOT contain both `window.addEventListener('load'` in chart-filter AND a direct `initXxxCharts` call at `DOMContentLoaded`
  - In a browser/DevTools environment: open kontrak/index page, observe Network tab — expect TWO requests to `/admin/chart-data/kontrak` within < 500ms of page load
  - **EXPECTED OUTCOME**: Test FAILS (this is correct — it proves the double-init bug exists)
  - Document counterexamples found (e.g., "kontrak page sends 2 requests to chart-data/kontrak", "window.load dispatch found in chart-filter.blade.php at line ~160")
  - Mark task complete when test is written, run, and the double-dispatch pattern is confirmed and documented
  - _Bug_Condition: isBugCondition(X) = X.hasChartFilter AND X.callsInitDirectlyOnDOMContentLoaded AND X.chartFilterDispatchesOnWindowLoad_
  - _Requirements: 1.1, 1.2, 1.3_

- [ ] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Filter Interactivity and Default Chart Load Behavior
  - **IMPORTANT**: Follow observation-first methodology
  - **Observe on UNFIXED code** (for inputs where isBugCondition = false, i.e. user interactions after initial load):
    - Observe: clicking "Bulan Ini" filter dispatches `chartFilterChange` with `{ filterType: 'month' }` and chart updates
    - Observe: clicking "Tahun Ini" dispatches `chartFilterChange` with `{ filterType: 'year' }` and chart updates
    - Observe: selecting custom date range and clicking Terapkan dispatches with `{ filterType: 'custom', startDate, endDate }`
    - Observe: members/index.blade.php loads chart exactly once (baseline reference — already correct pattern)
  - Write property-based tests capturing observed behaviors:
    - For all `filterType` in `['today', 'week', 'month', 'year']`: clicking the corresponding button dispatches `chartFilterChange` with correct detail
    - For custom range: applyCustomRange() dispatches event with `filterType: 'custom'` and populated `startDate`/`endDate`
    - For all pages (post-fix migration): `chartManager.hasChart('xxxChart')` correctly gates init vs update calls
  - Verify tests PASS on UNFIXED code (for the non-buggy interaction paths)
  - **EXPECTED OUTCOME**: Tests PASS on unfixed code (confirms baseline behavior to preserve)
  - Mark task complete when tests are written, run, and passing on unfixed code
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8_

- [ ] 3. Fix chart-filter.blade.php — replace window.load with DOMContentLoaded
  - Replace the `window.addEventListener('load', ...)` auto-dispatch block with `document.addEventListener('DOMContentLoaded', ...)` in `resources/views/components/chart-filter.blade.php`
  - The dispatched event payload (filterId, filterType, startDate, endDate, categoryId) remains identical — only the event timing changes
  - Verify no other `window.load` usages exist in this file for chart dispatch
  - _Bug_Condition: X.chartFilterDispatchesOnWindowLoad — the `window.addEventListener('load', ...)` block at the bottom of the IIFE in chart-filter.blade.php_
  - _Expected_Behavior: chartFilterChange is dispatched via DOMContentLoaded so chart initializes before window.load fires_
  - _Preservation: All filter button clicks, custom range, category filter behavior is unchanged — only the timing of the initial auto-dispatch changes_
  - _Requirements: 2.3, 2.4_

- [ ] 4. Migrate kontrak/index.blade.php to members pattern
  - File: `resources/views/admin/kontrak/index.blade.php`
  - Remove the direct `initKontrakCharts({ filter_type: 'month' })` call from inside `DOMContentLoaded`
  - In the `chartFilterChange` listener, add `hasChart()` gate: if `!kontrakChartManager.hasChart('kontrakBarChart')` → call `initKontrakCharts(filters)`, else → call `updateKontrakCharts(filters)`
  - Verify the `filterId` check (`e.detail.filterId === 'kontrakChartFilter'`) is preserved
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded — kontrakCharts called directly before listener-only pattern_
  - _Expected_Behavior: chart initializes exactly once via chartFilterChange listener using hasChart() gate_
  - _Preservation: Filter buttons, custom range, and default month filter continue to work_
  - _Requirements: 2.1, 2.2, 3.1, 3.2, 3.3_

- [ ] 5. Migrate kendaraan/index.blade.php to members pattern
  - File: `resources/views/admin/kendaraan/index.blade.php`
  - Remove the direct `initKendaraanCharts({ filter_type: 'year' })` (or equivalent) call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener: if `!kendaraanChartManager.hasChart('kendaraanBarChart')` → `initKendaraanCharts(filters)`, else → `updateKendaraanCharts(filters)`
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded — kendaraanCharts called directly_
  - _Expected_Behavior: chart initializes exactly once via chartFilterChange listener_
  - _Preservation: Filter interactions unchanged; defaultFilter='year' still renders on first load_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 6. Migrate invoice/index.blade.php to members pattern
  - File: `resources/views/admin/invoice/index.blade.php`
  - Remove direct `initInvoiceCharts()` call from `DOMContentLoaded`
  - If `invoiceChartsInitialized` boolean flag exists, replace it with `invoiceChartManager.hasChart('invoiceBarChart')` (or whichever is the primary canvas) for consistency
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded — invoiceCharts called directly, plus legacy invoiceChartsInitialized flag_
  - _Expected_Behavior: unified hasChart() gate, single init path_
  - _Preservation: Invoice filter and default year filter unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 7. Migrate history/index.blade.php to members pattern
  - File: `resources/views/admin/history/index.blade.php`
  - Remove direct `initHistoryCharts({ filter_type: 'month' })` call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded_
  - _Expected_Behavior: single init via listener_
  - _Preservation: defaultFilter='month', filter buttons, custom range unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 8. Migrate rental/index.blade.php to members pattern
  - File: `resources/views/admin/rental/index.blade.php`
  - Remove direct `initRentalCharts()` call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded_
  - _Expected_Behavior: single init via listener_
  - _Preservation: Filter interactions and default filter unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 9. Migrate penawaran/index.blade.php to members pattern
  - File: `resources/views/admin/penawaran/index.blade.php`
  - Remove direct `initPenawaranCharts()` call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded_
  - _Expected_Behavior: single init via listener_
  - _Preservation: Filter interactions and default filter unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 10. Migrate asuransi/index.blade.php to members pattern
  - File: `resources/views/admin/asuransi/index.blade.php`
  - Remove direct `initAsuransiCharts()` call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded_
  - _Expected_Behavior: single init via listener_
  - _Preservation: Filter interactions and default filter unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 11. Migrate asuransi/asuransi_kendaraan.blade.php to members pattern
  - File: `resources/views/admin/asuransi/asuransi_kendaraan.blade.php`
  - Remove direct chart init call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded_
  - _Expected_Behavior: single init via listener_
  - _Preservation: Filter interactions and default filter unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 12. Migrate asuransi/history.blade.php to members pattern
  - File: `resources/views/admin/asuransi/history.blade.php`
  - Remove direct chart init call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded_
  - _Expected_Behavior: single init via listener_
  - _Preservation: Filter interactions and default filter unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 13. Migrate asuransi/jenis_asuransi.blade.php to members pattern
  - File: `resources/views/admin/asuransi/jenis_asuransi.blade.php`
  - Remove direct chart init call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded_
  - _Expected_Behavior: single init via listener_
  - _Preservation: Filter interactions and default filter unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 14. Migrate gps/gps_kendaraan.blade.php to members pattern
  - File: `resources/views/admin/gps/gps_kendaraan.blade.php`
  - Remove direct `initGpsCharts({ filter_type: 'year' })` call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded_
  - _Expected_Behavior: single init via listener; defaultFilter='year' still renders on first load_
  - _Preservation: Filter interactions unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 15. Migrate data_leasing/index.blade.php + remove duplicated component
  - File: `resources/views/admin/data_leasing/index.blade.php`
  - Remove the duplicate `x-chart-filter` and `x-chart-container` component definitions — keep only one instance of each
  - Remove direct chart init call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded AND duplicate x-chart-filter/x-chart-container registration causing duplicate events and duplicate filter UI_
  - _Expected_Behavior: single component instance, single init via listener_
  - _Preservation: Chart and filter UI appear once; filter interactions unchanged_
  - _Requirements: 1.4, 2.1, 2.2, 3.1, 3.3_

- [ ] 16. Migrate keuangan/index.blade.php — 3 chart managers, preserve lazy-init tabs
  - File: `resources/views/admin/keuangan/index.blade.php`
  - Three chart managers involved: keuangan (cashflow), aging-ap, aging-ar
  - **Cashflow chart**: remove direct init from `DOMContentLoaded`; add `hasChart()` gate in its `chartFilterChange` listener
  - **Aging AP chart**: preserve existing lazy-init pattern — chart is ONLY initialized when the AP tab is first opened; keep `agingApChartInited` flag (or replace with `hasChart()` on the AP canvas); do NOT trigger AP chart from `chartFilterChange` event
  - **Aging AR chart**: same as AP — preserve lazy-init on tab open, keep `agingArChartInited` flag
  - Ensure the `chartFilterChange` listener for keuangan only affects the cashflow chart, not AP/AR
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded — cashflow charts called directly_
  - _Expected_Behavior: cashflow chart uses hasChart() gate; AP/AR charts retain tab-triggered lazy-init_
  - _Preservation: Tab switching lazy-init for AP/AR unchanged; cashflow filter interactions unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3, 3.6_

- [ ] 17. Migrate summary/index.blade.php to members pattern
  - File: `resources/views/admin/summary/index.blade.php`
  - Remove direct chart init call from `DOMContentLoaded`
  - Add `hasChart()` gate in the `chartFilterChange` listener
  - _Bug_Condition: X.callsInitDirectlyOnDOMContentLoaded_
  - _Expected_Behavior: single init via listener_
  - _Preservation: Filter interactions and default filter unchanged_
  - _Requirements: 2.1, 2.2, 3.1, 3.3_

- [ ] 18. Fix checking — verify fix correctness after implementation

  - [ ] 18.1 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - No window.load dispatch, single fetch per page load
    - **IMPORTANT**: Re-run the SAME inspection from task 1 — do NOT write a new test
    - Confirm `chart-filter.blade.php` no longer contains `window.addEventListener('load', ...)` for `chartFilterChange` dispatch
    - Confirm all migrated pages no longer call `initXxxCharts()` directly in `DOMContentLoaded`
    - Confirm all migrated pages have `hasChart()` gate in their `chartFilterChange` listener
    - In browser/DevTools: reload kontrak/history/rental pages and verify only ONE request to `/admin/chart-data/xxx` per page load
    - **EXPECTED OUTCOME**: Test PASSES (confirms the double-init bug is fixed)
    - _Requirements: 2.1, 2.2, 2.3, 2.4_

  - [ ] 18.2 Verify preservation tests still pass
    - **Property 2: Preservation** - Filter Interactivity and Default Chart Load Behavior
    - **IMPORTANT**: Re-run the SAME tests from task 2 — do NOT write new tests
    - Verify clicking filter buttons still dispatches `chartFilterChange` and updates charts correctly
    - Verify custom date range still dispatches with `filterType: 'custom'` and correct dates
    - Verify default filter renders chart on first load without user interaction on all migrated pages
    - Verify keuangan page: AP/AR tabs still lazy-init on first tab open; cashflow chart uses single init path
    - Verify data_leasing page: filter UI appears only once, no duplicate components
    - Verify members/index.blade.php is unchanged (reference implementation)
    - **EXPECTED OUTCOME**: Tests PASS (confirms no regressions)
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8_

- [ ] 19. Checkpoint — Ensure all tests pass
  - Ensure all tests pass; ask the user if any questions arise about edge cases
  - Verify all 14 pages (Tasks 4–17) have been migrated to the members pattern
  - Verify `chart-filter.blade.php` uses `DOMContentLoaded` (not `window.load`) for the initial auto-dispatch
  - Do a final review pass to ensure no page was missed or partially migrated
  - Confirm that `members/index.blade.php` remains untouched as the reference implementation
