<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Preservation Property Tests: formatSisaWaktu Return Values Unchanged
 *
 * These tests record the CURRENT return values of the formatSisaWaktu logic
 * as defined inline in AppServiceProvider::boot(), so they can verify no
 * regressions after the fix is applied.
 *
 * Design note — unfixed code behaviour:
 *   On unfixed code, the function is defined inside AppServiceProvider::boot()
 *   which resides in a file with `namespace App\Providers`. PHP therefore
 *   registers it as \App\Providers\formatSisaWaktu(), NOT as the global
 *   \formatSisaWaktu(). The guard `if (!function_exists('formatSisaWaktu'))`
 *   checks the GLOBAL namespace and always passes, causing a redeclaration
 *   fatal error on the second boot() call. To avoid this, the app is booted
 *   exactly once in setUpBeforeClass(), covering the entire test class.
 *
 * Scope: inputs where isBugCondition() = FALSE — calls made AFTER
 * AppServiceProvider::boot() has run (the function is already declared, either
 * as \App\Providers\formatSisaWaktu on unfixed code, or as the global
 * \formatSisaWaktu after the fix).
 *
 * EXPECTED OUTCOME ON UNFIXED CODE  : PASS (confirms baseline to preserve)
 * EXPECTED OUTCOME AFTER FIX        : PASS (confirms no regressions)
 *
 * Validates: Requirements 3.2, 3.3
 */
class FormatSisaWaktuPreservationTest extends TestCase
{
    /** Laravel Application instance, shared across all tests in this class. */
    private static mixed $app = null;

    /**
     * Boot the full Laravel application once for the entire test class.
     *
     * Using setUpBeforeClass() rather than setUp() prevents multiple boot()
     * calls on unfixed code (which causes a fatal redeclaration error because
     * the function guard checks the wrong namespace).
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (self::$app === null) {
            // Load environment variables so DB and other services resolve.
            if (file_exists(dirname(__DIR__, 2) . '/.env.testing')) {
                $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2), '.env.testing');
            } else {
                $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
            }
            $dotenv->safeLoad();

            // Bootstrap the application (registers and boots all service providers).
            self::$app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
            $kernel = self::$app->make(\Illuminate\Contracts\Http\Kernel::class);
            $request = \Illuminate\Http\Request::create('/up');
            $kernel->handle($request);
        }
    }

    /**
     * Resolve formatSisaWaktu() regardless of whether it lives in the global
     * namespace (fixed code, via Composer files autoload) or in the
     * App\Providers namespace (unfixed code, declared inside a namespaced file).
     *
     * On unfixed code  : \App\Providers\formatSisaWaktu() is the declared symbol.
     * On fixed code    : \formatSisaWaktu() is the declared symbol.
     */
    private function invoke(int $seconds): string
    {
        if (function_exists('formatSisaWaktu')) {
            return formatSisaWaktu($seconds);
        }

        if (function_exists('App\Providers\formatSisaWaktu')) {
            return \App\Providers\formatSisaWaktu($seconds);
        }

        $this->fail(
            "formatSisaWaktu() is not callable. Expected either \\formatSisaWaktu " .
            "(fixed code) or \\App\\Providers\\formatSisaWaktu (unfixed code)."
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Exact baseline observations (recorded from the inline function)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_baseline_86400_seconds_returns_1_hari(): void
    {
        $this->assertSame('1 hari', $this->invoke(86400));
    }

    public function test_baseline_172800_seconds_returns_2_hari(): void
    {
        $this->assertSame('2 hari', $this->invoke(172800));
    }

    public function test_baseline_7200_seconds_returns_2_jam(): void
    {
        $this->assertSame('2 jam', $this->invoke(7200));
    }

    public function test_baseline_3600_seconds_returns_1_jam(): void
    {
        $this->assertSame('1 jam', $this->invoke(3600));
    }

    public function test_baseline_1800_seconds_returns_less_than_1_jam(): void
    {
        $this->assertSame('< 1 jam', $this->invoke(1800));
    }

    public function test_baseline_1_second_returns_less_than_1_jam(): void
    {
        $this->assertSame('< 1 jam', $this->invoke(1));
    }

    public function test_baseline_0_seconds_returns_0_jam(): void
    {
        $this->assertSame('0 jam', $this->invoke(0));
    }

    public function test_baseline_negative_100_returns_0_jam(): void
    {
        $this->assertSame('0 jam', $this->invoke(-100));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Boundary values
    // ─────────────────────────────────────────────────────────────────────────

    public function test_boundary_exactly_86400_matches_hari_pattern(): void
    {
        $result = $this->invoke(86400);
        $this->assertMatchesRegularExpression('/^\d+ hari$/', $result);
        $this->assertSame('1 hari', $result);
    }

    public function test_boundary_86399_matches_jam_pattern(): void
    {
        $result = $this->invoke(86399);
        $this->assertMatchesRegularExpression('/^\d+ jam$/', $result);
    }

    public function test_boundary_exactly_3600_matches_jam_pattern(): void
    {
        $result = $this->invoke(3600);
        $this->assertMatchesRegularExpression('/^\d+ jam$/', $result);
        $this->assertSame('1 jam', $result);
    }

    public function test_boundary_3599_returns_less_than_1_jam(): void
    {
        $this->assertSame('< 1 jam', $this->invoke(3599));
    }

    public function test_boundary_0_returns_0_jam(): void
    {
        $this->assertSame('0 jam', $this->invoke(0));
    }

    public function test_boundary_negative_1_returns_0_jam(): void
    {
        $this->assertSame('0 jam', $this->invoke(-1));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Property: integers ≥ 86400 always match /^\d+ hari$/
    // Simulated via representative data provider (no PBT library required)
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\DataProvider('provideIntegers86400AndAbove')]
    public function test_property_integers_at_or_above_86400_match_hari_pattern(int $seconds): void
    {
        $result = $this->invoke($seconds);
        $this->assertMatchesRegularExpression(
            '/^\d+ hari$/',
            $result,
            "formatSisaWaktu($seconds) = '$result' — expected /^\\d+ hari\$/"
        );
    }

    public static function provideIntegers86400AndAbove(): array
    {
        $cases = [];
        $cases['exactly 86400 (1 day)']  = [86400];
        $cases['86401']                  = [86401];
        $cases['172800 (2 days)']        = [172800];
        $cases['259200 (3 days)']        = [259200];
        $cases['604800 (7 days)']        = [604800];
        $cases['2592000 (30 days)']      = [2592000];
        for ($days = 1; $days <= 30; $days++) {
            $s = $days * 86400;
            $cases["$days days ($s s)"]  = [$s];
        }
        $cases['1 year approx']          = [31536000];
        $cases['2 years approx']         = [63072000];
        return $cases;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Property: integers in [3600, 86399] always match /^\d+ jam$/
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\DataProvider('provideIntegers3600To86399')]
    public function test_property_integers_3600_to_86399_match_jam_pattern(int $seconds): void
    {
        $result = $this->invoke($seconds);
        $this->assertMatchesRegularExpression(
            '/^\d+ jam$/',
            $result,
            "formatSisaWaktu($seconds) = '$result' — expected /^\\d+ jam\$/"
        );
    }

    public static function provideIntegers3600To86399(): array
    {
        $cases = [];
        $cases['exactly 3600 (1 hour)']    = [3600];
        $cases['3601']                     = [3601];
        $cases['7200 (2 hours)']           = [7200];
        $cases['10800 (3 hours)']          = [10800];
        $cases['86399 (just under 1 day)'] = [86399];
        $cases['86398']                    = [86398];
        for ($hours = 1; $hours <= 23; $hours++) {
            $s = $hours * 3600;
            $cases["$hours hours ($s s)"]  = [$s];
        }
        $cases['43200 (12 hours)']         = [43200];
        $cases['54000 (15 hours)']         = [54000];
        $cases['75600 (21 hours)']         = [75600];
        return $cases;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Property: integers in [1, 3599] always return '< 1 jam'
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\DataProvider('provideIntegers1To3599')]
    public function test_property_integers_1_to_3599_return_less_than_1_jam(int $seconds): void
    {
        $result = $this->invoke($seconds);
        $this->assertSame(
            '< 1 jam',
            $result,
            "formatSisaWaktu($seconds) = '$result' — expected '< 1 jam'"
        );
    }

    public static function provideIntegers1To3599(): array
    {
        return [
            '1 (minimum positive)'      => [1],
            '2'                         => [2],
            '60 (1 minute)'             => [60],
            '300 (5 minutes)'           => [300],
            '600 (10 minutes)'          => [600],
            '900 (15 minutes)'          => [900],
            '1800 (30 minutes)'         => [1800],
            '2700 (45 minutes)'         => [2700],
            '3000'                      => [3000],
            '3300'                      => [3300],
            '3598'                      => [3598],
            '3599 (just under 1 hour)'  => [3599],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Property: integers ≤ 0 always return '0 jam'
    // ─────────────────────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\DataProvider('provideIntegersAtOrBelowZero')]
    public function test_property_integers_at_or_below_0_return_0_jam(int $seconds): void
    {
        $result = $this->invoke($seconds);
        $this->assertSame(
            '0 jam',
            $result,
            "formatSisaWaktu($seconds) = '$result' — expected '0 jam'"
        );
    }

    public static function provideIntegersAtOrBelowZero(): array
    {
        return [
            'exactly 0'      => [0],
            '-1'             => [-1],
            '-60'            => [-60],
            '-100'           => [-100],
            '-3600'          => [-3600],
            '-86400'         => [-86400],
            '-1000000'       => [-1000000],
            'large negative' => [-PHP_INT_MAX],
        ];
    }
}
