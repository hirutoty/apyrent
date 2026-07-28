# Bugfix Requirements Document

## Introduction

The `formatSisaWaktu()` helper function — which formats a duration in seconds into a human-readable string ("X hari", "X jam", "< 1 jam") — is defined as a plain PHP global function inside `AppServiceProvider::boot()`. Because this function lives inside a class method rather than in a dedicated autoloaded helpers file, PHP cannot guarantee it will be declared in the global namespace before the function is first called by a Blade view. This results in intermittent "Call to undefined function formatSisaWaktu()" fatal errors across at least 9 view files in the aging_ap, aging_ar, asuransi, gps, hutang_vendor, kir, pajak_kendaraan, keuangan, and rental modules.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN a Blade view calls `{{ formatSisaWaktu($seconds) }}` and `AppServiceProvider::boot()` has not yet fully executed in the current request lifecycle THEN the system throws a fatal "Call to undefined function formatSisaWaktu()" error.

1.2 WHEN the Laravel application is bootstrapped and `AppServiceProvider::boot()` defines `formatSisaWaktu()` as a global function inside a class method THEN the system relies on class-method execution order rather than PHP's standard autoload mechanism, making the function unreliable in contexts such as view composers, queued jobs, or artisan commands that render Blade views.

1.3 WHEN any of the 9+ affected Blade views (aging_ap, aging_ar, asuransi, gps, hutang_vendor, kir, pajak_kendaraan, keuangan, rental) attempt to render and the function is unavailable THEN the system renders a 500 error page instead of the requested view.

### Expected Behavior (Correct)

2.1 WHEN a Blade view calls `{{ formatSisaWaktu($seconds) }}` in any request context THEN the system SHALL resolve the function successfully and return the formatted string without throwing an error.

2.2 WHEN the Laravel application is bootstrapped THEN the system SHALL load `formatSisaWaktu()` via PHP's standard autoload mechanism (a dedicated helpers file included via `composer.json` `files` autoload) so that the function is always available globally before any view is rendered.

2.3 WHEN `formatSisaWaktu()` receives a value of zero or a negative integer THEN the system SHALL return `'0 jam'` without crashing.

2.4 WHEN `formatSisaWaktu()` receives a value in seconds representing one or more full days THEN the system SHALL return the count in days (e.g. `'3 hari'`).

2.5 WHEN `formatSisaWaktu()` receives a value in seconds representing less than one full day but one or more full hours THEN the system SHALL return the count in hours (e.g. `'5 jam'`).

2.6 WHEN `formatSisaWaktu()` receives a value in seconds representing less than one full hour THEN the system SHALL return `'< 1 jam'`.

### Unchanged Behavior (Regression Prevention)

3.1 WHEN a Blade view that does not call `formatSisaWaktu()` is rendered THEN the system SHALL CONTINUE TO render the view correctly without any side effects from the fix.

3.2 WHEN the `@formatSisaWaktu()` Blade directive (registered in `AppServiceProvider::boot()`) is used in a view THEN the system SHALL CONTINUE TO invoke the function and output the formatted string.

3.3 WHEN `formatSisaWaktu()` is called with a positive integer representing whole days, hours, or sub-hour seconds THEN the system SHALL CONTINUE TO return the same formatted output as before the fix.

3.4 WHEN the `AppServiceProvider::boot()` method runs THEN the system SHALL CONTINUE TO register the Blade directive and the View composer without disruption.

3.5 WHEN artisan commands (e.g. reminder commands) or queued jobs that render Blade views are executed THEN the system SHALL CONTINUE TO render those views successfully with `formatSisaWaktu()` available.

---

## Bug Condition Derivation

**Bug Condition Function:**
```pascal
FUNCTION isBugCondition(X)
  INPUT: X of type ViewRenderRequest
  OUTPUT: boolean

  // Returns true when the view calls formatSisaWaktu() but the function
  // has not been declared in the global namespace yet
  RETURN X.callsFormatSisaWaktu = TRUE
     AND formatSisaWaktu_defined_in_global_namespace = FALSE
END FUNCTION
```

**Property: Fix Checking**
```pascal
// Property: Fix Checking — function always available when view is rendered
FOR ALL X WHERE isBugCondition(X) DO
  result ← renderView'(X)
  ASSERT no_fatal_error(result)
  AND formatSisaWaktu_resolved(result)
END FOR
```

**Property: Preservation Checking**
```pascal
// Property: Preservation — non-affected views and callers unchanged
FOR ALL X WHERE NOT isBugCondition(X) DO
  ASSERT renderView(X) = renderView'(X)
END FOR
```
