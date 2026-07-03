<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Kendaraan;
use App\Models\ServiceHistory;
use App\Models\Setting;



class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
{
    View::composer('*', function ($view) {

    $warningIds = [];

    $kendaraanList = Kendaraan::all();

    foreach ($kendaraanList as $k) {

        $limit = $k->limit_bulan_service ?? 0;

        $total = ServiceHistory::where('kendaraan_id', $k->id)
            ->whereMonth('tanggal_service', now()->month)
            ->whereYear('tanggal_service', now()->year)
            ->sum('total_biaya');

        $sisa = $limit - $total;

        if ($sisa <= 0 || ($limit > 0 && $sisa <= ($limit * 0.1))) {
            $warningIds[] = $k->id;
        }
    }

    $setting = Setting::first();

    View::share('globalSetting', $setting);

    $view->with('serviceWarning', $warningIds);
});


}
}