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
        //
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