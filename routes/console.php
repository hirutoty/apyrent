
text/x-generic console.php ( ASCII text, with CRLF line terminators )
// <?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

use App\Console\Commands\ReminderAsuransiCommand;
use App\Console\Commands\ReminderPajakCommand;
use App\Console\Commands\ReminderGpsCommand;
use App\Console\Commands\ReminderKirCommand;
use App\Console\Commands\ReminderRentalCommand;
use App\Console\Commands\ReminderServiceCommand;
use App\Console\Commands\ReminderHutangVendorCommand;
use App\Console\Commands\ReminderPenawaranCommand;
use App\Console\Commands\SendAgingReminder;
use App\Console\Commands\CekJatuhTempoReminderService;
use App\Console\Commands\CheckServicePartLimit;
use App\Console\Commands\CatatCicilanLeasingCommand;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// ======================================
//  SCHEDULED TASKS (cPanel no proc_open)
// ======================================


// Pajak
Schedule::call(function () {
    app(ReminderPajakCommand::class)->handle();
// })->dailyAt('20:37');
})->everyMinute();


// Asuransi
Schedule::call(function () {
    app(ReminderAsuransiCommand::class)->handle();
// })->dailyAt('20:38');
})->everyMinute();


// GPS
Schedule::call(function () {
    app(ReminderGpsCommand::class)->handle();
// })->dailyAt('20:39');
})->everyMinute();


// KIR
Schedule::call(function () {
    app(ReminderKirCommand::class)->handle();
// })->dailyAt('20:40');
})->everyMinute();


// Rental
Schedule::call(function () {
    app(ReminderRentalCommand::class)->handle();
// })->dailyAt('20:41');
})->everyMinute();


// Over Service
Schedule::call(function () {
    app(ReminderServiceCommand::class)->handle();
// })->dailyAt('20:42');
})->everyMinute();


// Hutang
Schedule::call(function () {
    app(ReminderHutangVendorCommand::class)->handle();
// })->dailyAt('20:43');
})->everyMinute();


// Penawaran
Schedule::call(function () {
    app(ReminderPenawaranCommand::class)->handle();
// })->dailyAt('20:44');
})->everyMinute();


// Aging
Schedule::call(function () {
    app(SendAgingReminder::class)->handle();
// })->dailyAt('20:45');
})->everyMinute();


// Service jatuh tempo
Schedule::call(function () {
    app(CekJatuhTempoReminderService::class)->handle();
// })->dailyAt('20:46');
})->everyMinute();


// Part Limit Check — cek tanggal_limit tiap part, update status, buat reminder
Schedule::call(function () {
    app(CheckServicePartLimit::class)->handle();
// })->dailyAt('08:00');
})->everyMinute();


// Catat Cicilan Leasing — backfill + catat bulan berjalan ke keuangan & buku besar
Schedule::call(function () {
    app(CatatCicilanLeasingCommand::class)->handle();
// })->dailyAt('01:00');
})->everyMinute();


// php artisan schedule:run