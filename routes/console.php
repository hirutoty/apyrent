<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ======================================
//  SCHEDULED TASKS
// ======================================

Schedule::command('app:reminder-pajak-command')
    ->dailyAt('15:14');
// ->everyMinute();

Schedule::command('app:reminder-asuransi-command')
    ->dailyAt('16:25');
// ->everyMinute();

Schedule::command('app:reminder-gps-command')
    ->dailyAt('16:45');
// ->everyMinute();

Schedule::command('app:reminder-kir-command')
    ->dailyAt('19:11');
// ->everyMinute();

Schedule::command('app:reminder-rental-command')
    ->dailyAt('19:22');
// ->everyMinute();

Schedule::command('service:reminder-overservice')
    ->dailyAt('19:22');
// ->everyMinute();

Schedule::command('hutang:reminder')
    ->dailyAt('19:22');
// ->everyMinute();

Schedule::command('app:reminder-penawaran-command')
    ->dailyAt('19:22');
// ->everyMinute();

Schedule::command('aging:reminder')
    ->dailyAt('08:00');
// ->everyMinute();
