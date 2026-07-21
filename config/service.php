<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Interval Reminder Service Rutin (dalam bulan)
    |--------------------------------------------------------------------------
    | Digunakan sebagai fallback jika kendaraan tidak punya riwayat reminder
    | sebelumnya. Dapat diubah sesuai kebijakan operasional.
    */
    'reminder_interval_bulan' => env('SERVICE_REMINDER_INTERVAL_BULAN', 3),
];
