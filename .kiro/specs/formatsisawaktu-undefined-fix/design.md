# formatSisaWaktu Undefined Fix — Bugfix Design

## Overview

The `formatSisaWaktu()` helper function is currently defined as a plain PHP global function
inside `AppServiceProvider::boot()`. Because PHP does not autoload functions defined inside
class methods, the function is only guaranteed to exist after `AppServiceProvider::boot()` has
executed in the current process. In certain execution contexts — view composers, queued jobs,
artisan commands that render Blade views — the function is called before the service provider
runs, producing a fatal "Call to undefined function formatSisaWaktu()" error.

**Fix strategy**: Extract the function to a dedicated `app/helpers.php` file and register it
under the `files` autoload key in `composer.json`. PHP's autoloader then includes the file on
every bootstrap, guaranteeing the function is available in the global namespace before any
view is rendered. The Blade directive registration in `AppServiceProvider::boot()` stays in
place; only the inline function definition is removed.

---

## Glossary

- **Bug_Condition (C)**: The condition under which the bug manifests — a Blade view calls
  `formatSisaWaktu()` while the function has not yet been declared in PHP's global namespace.
- **Property (P)**: The desired correct behavior — `formatSisaWaktu()` is resolved successfully
  and returns the expected formatted string for any given input.
- **Preservation**: All existing behaviors unrelated to the autoload path of
  `formatSisaWaktu()` must remain identical after the fix.
- **`formatSisaWaktu(int $seconds): string`**: The helper function in
  `app/Providers/AppServiceProvider.php` (to be moved to `app/helpers.php`) that converts a
  duration in seconds to a human-readable string ("X hari", "X jam", "< 1 jam").
- **`AppServiceProvider::boot()`**: The service-provider method where the Blade directive
  `@formatSisaWaktu` is registered and where the inline function definition currently lives.
- **`files` autoload key**: A `composer.json` directive that instructs Composer to include
  listed PHP files on every bootstrap, making their globally-declared symbols always available.

---

## Bug Details

### Bug Condition

The bug manifests when a Blade view calls `formatSisaWaktu()` directly (as a plain PHP
function call, not through the Blade directive) and `AppServiceProvider::boot()` has not yet
executed in the current request lifecycle. Because the function is defined inside a class
method rather than in a Composer-autoloaded file, PHP cannot locate it via autoload, and a
fatal error is thrown.

**Formal Specification:**
```
FUNCTION isBugCondition(X)
  INPUT: X of type ViewRenderRequest
  OUTPUT: boolean

  RETURN X.callsFormatSisaWaktu = TRUE
     AND formatSisaWaktu_declared_in_global_namespace = FALSE
END FUNCTION
```

### Examples

- **Bug manifests** — A queued job renders `resources/views/admin/aging_ar/index.blade.php`
  before `AppServiceProvider::boot()` has run in that worker process.
  Expected: formatted string returned. Actual: fatal error "Call to undefined function
  formatSisaWaktu()".

- **Bug manifests** — An artisan command executes `ReminderRentalCommand` which renders a
  Blade view calling `{{ formatSisaWaktu($seconds) }}` as its first operation.
  Expected: rendered view with formatted string. Actual: 500 / fatal error.

- **Bug manifests** — Any of the 9+ affected views (aging_ap, aging_ar, asuransi, gps,
  hutang_vendor, kir, pajak_kendaraan, keuangan, rental) is rendered in an HTTP request where
  service-provider boot order places another provider's view composer ahead of
  `AppServiceProvider`.
  Expected: view renders normally. Actual: 500 error page.

- **Edge case (no bug)** — `formatSisaWaktu(0)` is called after the fix. Expected: `'0 jam'`.
  This should behave identically before and after the fix once the function is always available.

---

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Mouse/keyboard interactions with any page that does NOT call `formatSisaWaktu()` must
  continue to work exactly as before.
- The `@formatSisaWaktu($detik)` Blade directive registered in `AppServiceProvider::boot()`
  must continue to compile and invoke `formatSisaWaktu()` correctly.
- All existing return values of `formatSisaWaktu()` for positive integers (days, hours,
  sub-hour) must remain identical.
- `AppServiceProvider::boot()` must continue to register the Blade directive and the global
  View composer without disruption.
- Artisan reminder commands and queued jobs that render Blade views must continue to work
  correctly.

**Scope:**
All code paths that do NOT involve calling `formatSisaWaktu()` as an unresolved global
function are completely unaffected by this fix. This includes:
- All views that do not call `formatSisaWaktu()`.
- Any PHP code using the `@formatSisaWaktu` Blade directive.
- Any code that calls `formatSisaWaktu()` after `AppServiceProvider::boot()` has already run
  (these calls already succeed today).

---

## Hypothesized Root Cause

Based on the bug description and code inspection of `AppServiceProvider.php`:

1. **Function defined inside a class method**: PHP's autoload mechanism only resolves classes,
   interfaces, traits, and enums — not free functions. A function declared inside a method body
   (even with `if (!function_exists(...))`) is only registered when that method actually
   executes. If `boot()` has not run yet, the function does not exist.

2. **No `files` autoload entry in `composer.json`**: The `autoload` section of `composer.json`
   uses only `psr-4` mappings. There is no `files` key listing a helpers file. Composer's
   generated autoloader therefore never proactively includes a helpers file.

3. **Service-provider boot order dependency**: Any code path (queued jobs, artisan commands,
   view composers registered by other providers) that renders a Blade view calling
   `formatSisaWaktu()` before `AppServiceProvider::boot()` completes will encounter an
   undefined function.

4. **Absence of a dedicated helpers file**: The project has no `app/helpers.php` (or
   equivalent) that could be registered under the `files` autoload key as a permanent solution.

---

## Correctness Properties

Property 1: Bug Condition — Function Always Resolved on View Render

_For any_ `ViewRenderRequest` where the bug condition holds (the view calls
`formatSisaWaktu()` and the function was previously undefined in the global namespace),
the fixed application SHALL resolve `formatSisaWaktu()` successfully, returning the
correctly formatted string without throwing a fatal error.

**Validates: Requirements 2.1, 2.2**

Property 2: Preservation — Return Values Unchanged

_For any_ call to `formatSisaWaktu(int $seconds)` where the function was already available
(bug condition does NOT hold), the fixed function SHALL produce exactly the same return
value as the original inline function for all integer inputs, preserving the
`'X hari'` / `'X jam'` / `'< 1 jam'` / `'0 jam'` formatting logic.

**Validates: Requirements 3.2, 3.3**

---

## Fix Implementation

### Changes Required

**File 1**: `app/helpers.php` *(new file)*

**Changes**:
1. **Create helpers file**: Define `formatSisaWaktu(int $seconds): string` as a top-level
   global function inside an `if (!function_exists(...))` guard, using the identical logic
   currently in `AppServiceProvider::boot()`.

```php
<?php

if (!function_exists('formatSisaWaktu')) {
    function formatSisaWaktu(int $seconds): string
    {
        if ($seconds <= 0)   return '0 jam';
        $hari = (int) floor($seconds / 86400);
        if ($hari >= 1)      return $hari . ' hari';
        $jam  = (int) floor($seconds / 3600);
        if ($jam  >= 1)      return $jam  . ' jam';
        return '< 1 jam';
    }
}
```

---

**File 2**: `composer.json`

**Changes**:
2. **Add `files` autoload entry**: Add `"app/helpers.php"` to the `autoload.files` array so
   Composer includes it on every bootstrap.

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/helpers.php"
    ]
},
```

---

**File 3**: `app/Providers/AppServiceProvider.php`

**Changes**:
3. **Remove inline function definition**: Delete the `if (!function_exists('formatSisaWaktu'))
   { ... }` block from `boot()`. The Blade directive registration (`Blade::directive(...)`)
   must remain untouched.

---

**Post-fix step** *(manual, not a code change)*:
4. Run `composer dump-autoload` so Composer regenerates its autoloader and includes
   `app/helpers.php` via the `files` key.

---

## Testing Strategy

### Validation Approach

The testing strategy follows a two-phase approach: first, surface counterexamples that
demonstrate the bug on unfixed code (exploratory), then verify the fix works correctly
(fix checking) and preserves all existing behavior (preservation checking).

---

### Exploratory Bug Condition Checking

**Goal**: Surface counterexamples that demonstrate the bug BEFORE implementing the fix.
Confirm or refute the root cause analysis. If refuted, re-hypothesize.

**Test Plan**: Write a PHPUnit test that simulates rendering a Blade view calling
`formatSisaWaktu()` WITHOUT having gone through `AppServiceProvider::boot()` first. Assert
that the function call triggers a fatal error (or is caught as an undefined-function
exception) on the unfixed code.

**Test Cases**:
1. **Direct function call without boot** — Call `formatSisaWaktu(3600)` in a test that does
   not boot the service provider. Will fail with "Call to undefined function" on unfixed code.
2. **Blade view render before boot** — Render a minimal Blade template containing
   `{{ formatSisaWaktu(86400) }}` before `AppServiceProvider::boot()` has run. Will fail
   on unfixed code.
3. **Artisan command context** — Simulate an artisan command rendering a view that calls
   `formatSisaWaktu()` as its first action. Will fail on unfixed code.
4. **Edge case — zero seconds** — Call `formatSisaWaktu(0)` in the same pre-boot context.
   May fail on unfixed code (same root cause).

**Expected Counterexamples**:
- PHP fatal error / `Error: Call to undefined function formatSisaWaktu()` is thrown.
- Possible causes confirmed: function declared inside class method, no `files` autoload entry.

---

### Fix Checking

**Goal**: Verify that for all inputs where the bug condition holds, the fixed function
produces the expected behavior.

**Pseudocode:**
```
FOR ALL X WHERE isBugCondition(X) DO
  result := renderView_fixed(X)
  ASSERT no_fatal_error(result)
  AND formatSisaWaktu_resolved(result)
END FOR
```

---

### Preservation Checking

**Goal**: Verify that for all inputs where the bug condition does NOT hold, the fixed function
produces the same result as the original function.

**Pseudocode:**
```
FOR ALL input WHERE NOT isBugCondition(input) DO
  ASSERT formatSisaWaktu_original(input) = formatSisaWaktu_fixed(input)
END FOR
```

**Testing Approach**: Property-based testing is recommended for preservation checking because:
- It generates many integer inputs automatically across the full input domain.
- It catches boundary cases (e.g., exactly 86400 seconds, 3600 seconds, 1 second, -1 second).
- It provides strong guarantees that the extracted function is byte-for-byte equivalent to the
  original inline definition.

**Test Plan**: Record the return values of the original inline function for a wide range of
inputs, then assert the fixed `app/helpers.php` function produces identical output.

**Test Cases**:
1. **Return value preservation — days**: Verify `formatSisaWaktu(86400)` → `'1 hari'`,
   `formatSisaWaktu(172800)` → `'2 hari'`, etc.
2. **Return value preservation — hours**: Verify `formatSisaWaktu(7200)` → `'2 jam'`,
   `formatSisaWaktu(3600)` → `'1 jam'`.
3. **Return value preservation — sub-hour**: Verify `formatSisaWaktu(1800)` → `'< 1 jam'`,
   `formatSisaWaktu(1)` → `'< 1 jam'`.
4. **Return value preservation — edge cases**: Verify `formatSisaWaktu(0)` → `'0 jam'`,
   `formatSisaWaktu(-100)` → `'0 jam'`.

---

### Unit Tests

- Test `formatSisaWaktu()` is callable without booting `AppServiceProvider` (after fix).
- Test all return-value branches: `'X hari'`, `'X jam'`, `'< 1 jam'`, `'0 jam'`.
- Test boundary values: 86400 (exactly 1 day), 3600 (exactly 1 hour), 86399 (just under a
  day), 3599 (just under an hour), 0, -1.
- Test that `AppServiceProvider::boot()` still registers the `@formatSisaWaktu` Blade
  directive without error after the inline definition is removed.

### Property-Based Tests

- Generate random integers ≥ 1 and verify `formatSisaWaktu()` returns a non-empty string
  matching one of the three patterns (`/^\d+ hari$/`, `/^\d+ jam$/`, `'< 1 jam'`).
- Generate random integers in the range `[0, 86399]` and verify the result is either a
  jam-format string or `'< 1 jam'` (never a hari-format string).
- Generate random integers in the range `[86400, PHP_INT_MAX / 2]` and verify the result
  always matches the `'\d+ hari'` pattern.
- Generate random non-positive integers and verify the result is always `'0 jam'`.

### Integration Tests

- Render a real Blade view from the affected modules (e.g., `admin.rental.index`) in a full
  Laravel test case and assert no exception is thrown and the output contains a formatted
  sisa-waktu string.
- Boot the application without manually triggering `AppServiceProvider::boot()` first and
  assert `function_exists('formatSisaWaktu')` returns `true` (verifying Composer autoloads
  the helpers file).
- Run an artisan command that renders a view calling `formatSisaWaktu()` and assert it
  exits with code 0.
