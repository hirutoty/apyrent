<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use App\Models\Setting;



class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ── SQLite testing guard ──────────────────────────────────────────────
        // 30+ migration files in this project use MySQL-only `ALTER TABLE ... MODIFY COLUMN`
        // syntax incompatible with SQLite. Register a custom Migrator that silently skips
        // MODIFY statements when running on SQLite (testing only).
        // Production MySQL is unaffected.
        if (app()->environment('testing')) {
            $this->app->extend('migrator', function ($migrator, $app) {
                return new \App\Database\SqliteCompatMigrator(
                    $app['migration.repository'],
                    $app['db'],
                    $app['files'],
                    $app['events']
                );
            });
        }
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom-purple');

        // Daftarkan sebagai Blade directive supaya bisa @formatSisaWaktu($detik)
        Blade::directive('formatSisaWaktu', function ($expression) {
            return "<?php echo formatSisaWaktu((int)($expression)); ?>";
        });

        View::composer('*', function ($view) {
            $setting = Setting::first();
            View::share('globalSetting', $setting);
        });
    }
}