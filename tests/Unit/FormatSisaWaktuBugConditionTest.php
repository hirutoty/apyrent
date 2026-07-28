<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Bug Condition Exploration Test: formatSisaWaktu Undefined Before Boot
 *
 * This test encodes Bug Condition Property 1 from the design:
 *   isBugCondition(X) = X.callsFormatSisaWaktu = TRUE
 *                    AND formatSisaWaktu_declared_in_global_namespace = FALSE
 *
 * EXPECTED OUTCOME ON UNFIXED CODE:
 *   - FAILS with "Error: Call to undefined function formatSisaWaktu()"
 *   - This failure IS the success case: it proves the bug exists.
 *
 * EXPECTED OUTCOME AFTER FIX:
 *   - PASSES: the function is available via Composer's files autoload
 *     without needing AppServiceProvider::boot() to have run.
 *
 * Validates: Requirements 1.1, 1.2
 */
class FormatSisaWaktuBugConditionTest extends TestCase
{
    /**
     * setUp() deliberately does NOT boot AppServiceProvider.
     * This is a plain PHPUnit\Framework\TestCase (not Laravel's TestCase),
     * so the Laravel application is never bootstrapped in this test class.
     * The function should therefore NOT be available on unfixed code.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // On UNFIXED code: AppServiceProvider::boot() has not run,
        // so the function must NOT be declared yet.
        // On FIXED code: Composer's files autoload will have included
        // app/helpers.php, so the function IS already declared here.
        // The assertion below documents the pre-fix state we are probing.
        // We do NOT assert false here because on fixed code it would be true —
        // the test body itself will surface the bug by calling the function.
    }

    /**
     * Test 1: formatSisaWaktu(3600) is callable without AppServiceProvider::boot()
     *         and returns a non-empty string.
     *
     * Bug condition: function is NOT declared in global namespace before boot.
     * On UNFIXED code: throws "Error: Call to undefined function formatSisaWaktu()"
     * On FIXED code  : returns "1 jam"
     *
     * Counterexample documented:
     *   formatSisaWaktu(3600) → Error: Call to undefined function formatSisaWaktu()
     */
    public function test_formatSisaWaktu_is_available_without_booting_service_provider(): void
    {
        // Assert the function exists in the global namespace
        // (without AppServiceProvider::boot() having run).
        // On unfixed code this is FALSE, causing the test to fail with a clear message.
        $this->assertTrue(
            function_exists('formatSisaWaktu'),
            'Bug confirmed: formatSisaWaktu() is not declared in the global namespace. ' .
            'AppServiceProvider::boot() has not run, and there is no Composer files autoload entry. ' .
            'This is the bug condition: isBugCondition(X) = TRUE.'
        );

        // On unfixed code the assertTrue above already fails, but if we reach here
        // (fixed code) the direct call must also succeed and return a non-empty string.
        $result = formatSisaWaktu(3600);

        $this->assertIsString($result, 'formatSisaWaktu(3600) must return a string.');
        $this->assertNotEmpty($result, 'formatSisaWaktu(3600) must return a non-empty string.');
    }

    /**
     * Test 2: formatSisaWaktu(0) is callable without AppServiceProvider::boot()
     *         and returns '0 jam'.
     *
     * Bug condition: same root cause as test 1 — function not declared before boot.
     * On UNFIXED code: throws "Error: Call to undefined function formatSisaWaktu()"
     * On FIXED code  : returns "0 jam"
     *
     * Counterexample documented:
     *   formatSisaWaktu(0) → Error: Call to undefined function formatSisaWaktu()
     */
    public function test_formatSisaWaktu_with_zero_is_available_without_booting_service_provider(): void
    {
        // Same bug condition check — function must be declared without boot.
        $this->assertTrue(
            function_exists('formatSisaWaktu'),
            'Bug confirmed: formatSisaWaktu() is not declared in the global namespace. ' .
            'AppServiceProvider::boot() has not run, and there is no Composer files autoload entry. ' .
            'Counterexample: formatSisaWaktu(0) throws Error: Call to undefined function formatSisaWaktu().'
        );

        // On fixed code: verify the zero-seconds edge case returns '0 jam'.
        $result = formatSisaWaktu(0);

        $this->assertSame('0 jam', $result, 'formatSisaWaktu(0) must return "0 jam".');
    }
}
