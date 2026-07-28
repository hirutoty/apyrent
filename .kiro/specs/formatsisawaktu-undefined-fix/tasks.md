# Implementation Plan

- [x] 1. Write bug condition exploration test
  - **Property 1: Bug Condition** - formatSisaWaktu Undefined Before Boot
  - **CRITICAL**: This test MUST FAIL on unfixed code — failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **NOTE**: This test encodes the expected behavior — it will validate the fix when it passes after implementation
  - **GOAL**: Surface counterexamples that demonstrate the bug exists
  - **Scoped PBT Approach**: Scope the property to the concrete failing case — call `formatSisaWaktu()` in a PHPUnit test that does NOT boot `AppServiceProvider`, then assert the call succeeds and returns a non-empty string
  - Create `tests/Unit/FormatSisaWaktuBugConditionTest.php`
  - In `setUp()`, confirm `AppServiceProvider::boot()` has NOT been called and the function is NOT yet declared (assert `function_exists('formatSisaWaktu') === false`)
  - Call `formatSisaWaktu(3600)` directly; on unfixed code this triggers `Error: Call to undefined function formatSisaWaktu()`
  - The test asserts `function_exists('formatSisaWaktu') === true` AND the call returns a non-empty string — this assertion will fail on unfixed code
  - Also test `formatSisaWaktu(0)` in same pre-boot context (same root cause, same expected failure)
  - Run test on UNFIXED code: `php artisan test tests/Unit/FormatSisaWaktuBugConditionTest.php`
  - **EXPECTED OUTCOME**: Test FAILS with "Call to undefined function formatSisaWaktu()" (this is correct — it proves the bug exists)
  - Document the counterexample found (e.g., `formatSisaWaktu(3600)` throws `Error: Call to undefined function formatSisaWaktu()`)
  - Mark task complete when test is written, run, and failure is documented
  - _Requirements: 1.1, 1.2_

- [x] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Return Values Unchanged for All Integer Inputs
  - **IMPORTANT**: Follow observation-first methodology
  - **Scope**: These tests cover inputs where `isBugCondition` returns FALSE — i.e., calls to `formatSisaWaktu()` AFTER `AppServiceProvider::boot()` has run (the function is already declared)
  - Create `tests/Unit/FormatSisaWaktuPreservationTest.php`
  - Observe and record current return values from the inline function in `AppServiceProvider::boot()` on unfixed code:
    - `formatSisaWaktu(86400)` → `'1 hari'`
    - `formatSisaWaktu(172800)` → `'2 hari'`
    - `formatSisaWaktu(7200)` → `'2 jam'`
    - `formatSisaWaktu(3600)` → `'1 jam'`
    - `formatSisaWaktu(1800)` → `'< 1 jam'`
    - `formatSisaWaktu(1)` → `'< 1 jam'`
    - `formatSisaWaktu(0)` → `'0 jam'`
    - `formatSisaWaktu(-100)` → `'0 jam'`
  - Write property-based tests asserting:
    - For all integers ≥ 86400: result matches `/^\d+ hari$/`
    - For all integers in [3600, 86399]: result matches `/^\d+ jam$/`
    - For all integers in [1, 3599]: result is `'< 1 jam'`
    - For all integers ≤ 0: result is `'0 jam'`
    - Boundary values: exactly 86400, exactly 3600, 86399, 3599, 0, -1
  - Boot `AppServiceProvider` in test setUp so the inline function is available on unfixed code
  - Run tests on UNFIXED code: `php artisan test tests/Unit/FormatSisaWaktuPreservationTest.php`
  - **EXPECTED OUTCOME**: Tests PASS (confirms baseline behavior to preserve)
  - Mark task complete when tests are written, run, and passing on unfixed code
  - _Requirements: 3.2, 3.3_

- [x] 3. Fix: extract formatSisaWaktu to autoloaded helpers file

  - [x] 3.1 Create `app/helpers.php` with the global function definition
    - Create new file `app/helpers.php`
    - Define `formatSisaWaktu(int $seconds): string` as a top-level global function inside an `if (!function_exists('formatSisaWaktu'))` guard
    - Copy the identical logic from `AppServiceProvider::boot()`:
      - `if ($seconds <= 0) return '0 jam';`
      - `$hari = (int) floor($seconds / 86400); if ($hari >= 1) return $hari . ' hari';`
      - `$jam = (int) floor($seconds / 3600); if ($jam >= 1) return $jam . ' jam';`
      - `return '< 1 jam';`
    - _Bug_Condition: `isBugCondition(X)` where `X.callsFormatSisaWaktu = TRUE AND formatSisaWaktu_defined_in_global_namespace = FALSE`_
    - _Expected_Behavior: `formatSisaWaktu()` is resolved successfully from the global namespace for any `ViewRenderRequest`, returning the formatted string without fatal error_
    - _Preservation: return values for all integer inputs remain byte-for-byte identical to the original inline definition_
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_

  - [x] 3.2 Update `composer.json` to add `files` autoload entry
    - Open `composer.json` and locate the `"autoload"` section
    - Add a `"files"` key with value `["app/helpers.php"]` alongside the existing `"psr-4"` entries
    - Ensure the JSON is valid after the edit
    - _Requirements: 2.2_

  - [x] 3.3 Remove the inline function definition from `AppServiceProvider::boot()`
    - Open `app/Providers/AppServiceProvider.php`
    - Delete the entire `if (!function_exists('formatSisaWaktu')) { ... }` block from `boot()`
    - Leave the `Blade::directive('formatSisaWaktu', ...)` registration untouched
    - Leave the View composer registration untouched
    - Confirm the `boot()` method still registers the Blade directive after the edit
    - _Requirements: 3.4_

  - [x] 3.4 Run `composer dump-autoload`
    - Execute `composer dump-autoload` in the project root to regenerate Composer's autoloader
    - Verify the command exits successfully (exit code 0)
    - Verify `vendor/composer/autoload_files.php` contains a reference to `app/helpers.php`
    - _Requirements: 2.2_

  - [x] 3.5 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - formatSisaWaktu Undefined Before Boot
    - **IMPORTANT**: Re-run the SAME test from task 1 — do NOT write a new test
    - The test from task 1 encodes the expected behavior: function is available in the global namespace without booting `AppServiceProvider`, and returns the correct value
    - Run: `php artisan test tests/Unit/FormatSisaWaktuBugConditionTest.php`
    - **EXPECTED OUTCOME**: Test PASSES (confirms bug is fixed — `formatSisaWaktu()` is now always available via Composer autoload)
    - _Requirements: 2.1, 2.2_

  - [x] 3.6 Verify preservation tests still pass
    - **Property 2: Preservation** - Return Values Unchanged for All Integer Inputs
    - **IMPORTANT**: Re-run the SAME tests from task 2 — do NOT write new tests
    - Run: `php artisan test tests/Unit/FormatSisaWaktuPreservationTest.php`
    - **EXPECTED OUTCOME**: Tests PASS (confirms no regressions — all return values are identical to the original inline function)
    - Confirm all boundary values still return correct output after the inline definition was removed
    - _Requirements: 3.2, 3.3_

- [x] 4. Checkpoint — Ensure all tests pass
  - Run the full test suite: `php artisan test`
  - Verify `FormatSisaWaktuBugConditionTest` passes (bug fixed)
  - Verify `FormatSisaWaktuPreservationTest` passes (no regressions)
  - Verify no other tests have been broken by the autoload change
  - Confirm `function_exists('formatSisaWaktu')` returns `true` in a fresh bootstrap without manually booting `AppServiceProvider`
  - Ask the user if any questions arise or if manual smoke-testing of affected views (aging_ar, rental, kir, asuransi, gps, keuangan, pajak_kendaraan) is needed
