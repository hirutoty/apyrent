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
    ->dailyAt('00.01');
// ->everyMinute();

Schedule::command('app:reminder-asuransi-command')
    ->dailyAt('00.01');
// ->everyMinute();

Schedule::command('app:reminder-gps-command')
    ->dailyAt('00.01');
// ->everyMinute();

Schedule::command('app:reminder-kir-command')
    ->dailyAt('00.01');
// ->everyMinute();

Schedule::command('app:reminder-rental-command')
    ->dailyAt('00.01');
// ->everyMinute();

Schedule::command('service:reminder-overservice')
    ->dailyAt('00.01');
// ->everyMinute();

Schedule::command('hutang:reminder')
    ->dailyAt('00.01');
// ->everyMinute();

Schedule::command('app:reminder-penawaran-command')
    ->dailyAt('00.01');
// ->everyMinute();

Schedule::command('aging:reminder')
    ->dailyAt('00.01');
// ->everyMinute();
