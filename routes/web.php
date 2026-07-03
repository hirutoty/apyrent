<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\KendaraanController;
use App\Http\Controllers\Admin\JenisController;
use App\Http\Controllers\Admin\KirController;
use App\Http\Controllers\Admin\KirHistoryController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberKendaraanController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceHistoryController;
use App\Http\Controllers\Admin\ServiceDetailController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AsuransiController;
use App\Http\Controllers\Admin\AsuransiHistoryController;
use App\Http\Controllers\Admin\AsuransiKendaraanController;
use App\Http\Controllers\Admin\JenisAsuransiController;
use App\Http\Controllers\Admin\GpsController;
use App\Http\Controllers\Admin\GpsKendaraanController;
use App\Http\Controllers\Admin\RentalController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\HutangVendorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\BudgetingController;
use App\Http\Controllers\Admin\KonsolidasiController;
use App\Http\Controllers\Admin\EfakturController;
use App\Http\Controllers\Admin\BupotController;
use App\Http\Controllers\Admin\RekonsiliasiController;
use App\Http\Controllers\Admin\VirtualController;
use App\Http\Controllers\Admin\BukubesarController;
use App\Http\Controllers\Admin\GpsKendaraanHistoryController;
use App\Http\Controllers\Admin\PajakController;
use App\Http\Controllers\Admin\PajakHistoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StnkController;
use App\Http\Controllers\Admin\StnkHistoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PenawaranKendaraanController;
use App\Http\Controllers\Admin\InvPenawaranController;
use App\Http\Controllers\Admin\InvKontrakController;
use App\Http\Controllers\Admin\InvoicesController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\SummaryController;
use App\Http\Controllers\Admin\ReminderController;
use App\Http\Controllers\Admin\ProcurementoController;
use App\Http\Controllers\Admin\PurchaseroController;
use App\Http\Controllers\Admin\VendoreoController;
// User
use App\Http\Controllers\User\ProfileController;
// schedule
use Illuminate\Support\Facades\Schedule;

Route::redirect('/', '/index');

Route::view('/index', 'index');

Route::get('/login', [LoginController::class, 'index'])
  ->name('login');

Route::post('/login', [LoginController::class, 'authenticate']);

Route::get('/register', [RegisterController::class, 'index'])
  ->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::post('/logout', [LoginController::class, 'logout'])
  ->name('logout');

Route::middleware(['auth'])->group(function () {

  Route::get('/profil', [ProfileController::class, 'index'])
    ->name('profil.index');

  Route::put('/profil', [ProfileController::class, 'update'])
    ->name('profil.update');
});



Route::middleware('auth')->prefix('admin')->group(function () {
  // Laporan
  Route::get('/supplier/pdf', [SupplierController::class, 'pdf'])
    ->name('supplier.export.pdf');

  Route::get(
    '/kendaraan/{merk}/export-pdf',
    [KendaraanController::class, 'exportPdf']
  )->name('kendaraan.export.pdf');

  Route::get(
    '/kendaraan/export/merk',
    [KendaraanController::class, 'exportMerkPdf']
  )->name('kendaraan.export.merk');

  Route::get('/keuangan/export/pdf', [KeuanganController::class, 'exportPdf'])
    ->name('keuangan.export.pdf');
  Route::get('/keuangan/export/excel', [KeuanganController::class, 'exportExcel'])
    ->name('keuangan.export.excel');

  Route::get(
    'gps-kendaraan/export/pdf',
    [GpsKendaraanController::class, 'exportPdf']
  )->name('gps-kendaraan.export.pdf');

  Route::post('gps-kendaraan/{id}/perpanjang', [GpsKendaraanController::class, 'perpanjang'])
    ->name('gps-kendaraan.perpanjang');

  Route::resource('gps-kendaraan-history', GpsKendaraanHistoryController::class)
    ->only('index');
  Route::delete('/admin/gpskendaraan/pajak/{id}', [GpsKendaraanHistoryController::class, 'destroy'])
    ->name('history.gpskendaraan.destroy');
  Route::get('gps-kendaraan-history/export', [GpsKendaraanHistoryController::class, 'exportPdf'])
    ->name('gps-kendaraan-history.export');

  Route::get(
    'asuransi-kendaraan/export-pdf',
    [AsuransiKendaraanController::class, 'exportPdf']
  );

  // Taruh SEBELUM Route::resource('asuransi-kendaraan', ...)
  Route::post('asuransi-kendaraan/{id}/perpanjang', [AsuransiKendaraanController::class, 'perpanjang'])
    ->name('asuransi-kendaraan.perpanjang');

  Route::get(
    'asuransi-history',
    [AsuransiHistoryController::class, 'index']
  )->name('history.asuransi.index');
  Route::delete('/admin/history/asuransi/{id}', [AsuransiHistoryController::class, 'destroy'])
    ->name('history.asuransi.destroy');
  Route::get('/admin/asuransi/history-pdf', [AsuransiHistoryController::class, 'exportPdf'])
    ->name('history.asuransi.export');

  Route::get('/pajak/export-pdf', [PajakController::class, 'exportPdf'])
    ->name('pajak.export.pdf');
  Route::get('/admin/history-pajak/export-pdf', [PajakHistoryController::class, 'exportPdf'])
    ->name('history.pajak.export');
  Route::get('/pajak-history', [PajakHistoryController::class, 'index'])
    ->name('history.pajak.index');
  Route::post(
    'pajak/{id}/perpanjang',
    [PajakController::class, 'perpanjang']
  )->name('pajak.perpanjang');
  Route::delete('/admin/history/pajak/{id}', [PajakHistoryController::class, 'destroy'])
    ->name('history.pajak.destroy');



  Route::get('/user/export-pdf', [UserController::class, 'exportPdf'])
    ->name('user.export.pdf');

  Route::get('/member/pdf', [MemberController::class, 'pdf']);

  Route::get('/admin/rental/pdf', [RentalController::class, 'pdf'])
    ->name('rental.pdf');
  Route::get('/rental/{id}/invoice', [RentalController::class, 'invoice'])
    ->name('rental.invoice');

  Route::get('/hutang-vendor/pdf', [HutangVendorController::class, 'pdf'])
    ->name('hutang-vendor.pdf');

  Route::get('/hutang-vendor/excel', [HutangVendorController::class, 'exportExcel'])
    ->name('hutang-vendor.export.excel');

  Route::get('/budgeting/pdf', [BudgetingController::class, 'pdf'])
    ->name('budgeting.pdf');
  Route::get('/budgeting/excel', [BudgetingController::class, 'exportExcel'])
    ->name('budgeting.export.excel');

  Route::get('/konsolidasi/pdf', [KonsolidasiController::class, 'pdf'])
    ->name('konsolidasi.pdf');
  Route::get('/konsolidasi/excel', [KonsolidasiController::class, 'exportExcel'])
    ->name('konsolidasi.export.excel');

  Route::get('/efaktur/pdf', [EfakturController::class, 'pdf'])
    ->name('efaktur.pdf');
  Route::get('/efaktur/excel', [EfakturController::class, 'exportExcel'])
    ->name('efaktur.export.excel');

  Route::get('/bupot/pdf', [BupotController::class, 'pdf'])
    ->name('bupot.pdf');
  Route::get('/bupot/excel', [BupotController::class, 'exportExcel'])
    ->name('bupot.export.excel');

  Route::get('/rekonsiliasi/pdf', [RekonsiliasiController::class, 'pdf'])
    ->name('rekonsiliasi.pdf');
  Route::get('/rekonsiliasi/excel', [RekonsiliasiController::class, 'exportExcel'])
    ->name('rekonsiliasi.export.excel');

  Route::get('/virtual-account/pdf', [VirtualController::class, 'pdf'])
    ->name('virtual.pdf');
  Route::get('/virtual/excel', [VirtualController::class, 'exportExcel'])
    ->name('virtual.export.excel');

  Route::get('/bukubesar/pdf', [BukuBesarController::class, 'pdf'])
    ->name('bukubesar.pdf');
  Route::get('/bukubesar/excel', [BukuBesarController::class, 'exportExcel'])
    ->name('bukubesar.export.excel');

  Route::get('/service-detail/pdf', [ServiceDetailController::class, 'pdf'])
    ->name('service-detail.pdf');


  Route::get('/kir/pdf', [KirController::class, 'pdf'])
    ->name('kir.pdf');

  Route::get('/admin/history-kir/export-pdf', [KirHistoryController::class, 'exportPdf'])
    ->name('history.kir.export');

  Route::post('kir/{id}/perpanjang', [KirController::class, 'perpanjang'])->name('kir.perpanjang');
  Route::get('/kir-history', [KirHistoryController::class, 'index'])
    ->name('history.kir.index');


  Route::get('/service-history/pdf', [ServiceHistoryController::class, 'pdf'])
    ->name('service-history.pdf');
  Route::delete('/admin/history/kir/{id}', [KirHistoryController::class, 'destroy'])
    ->name('history.kir.destroy');



  Route::get('/admin/stnk/export/pdf', [StnkController::class, 'exportPdf'])
    ->name('stnk.export.pdf');
  Route::delete('/admin/stnk-history/{id}', [StnkHistoryController::class, 'destroy'])
    ->name('stnk-history.destroy');
  Route::post('stnk/{id}/perpanjang', [StnkController::class, 'perpanjang'])
    ->name('stnk.perpanjang');
  Route::resource('stnk-history', StnkHistoryController::class)
    ->only('index');
  Route::get('/stnk-history/export/pdf', [StnkHistoryController::class, 'exportPdf'])
    ->name('stnk-history.export.pdf');


Route::resource('procuremento', ProcurementoController::class) // Pengadaan - Procurements
    ->except(['create', 'edit', 'show']); // Form CRUD With Modal

    Route::resource('purchasero', PurchaseroController::class) // Purchase Request
    ->except(['create', 'edit', 'show']); // Form CRUD With Modal

    Route::resource('vendoreo', VendoreoController::class) // Manajemen Vendor
    ->except(['create', 'edit', 'show']); // Form CRUD With Modal

  Route::post('/rental/{id}/update-status', [RentalController::class, 'updateStatus'])
    ->name('rental.update-status');
  Route::post('/rental/{id}/update-status2', [RentalController::class, 'toogletatus'])->name('rental.update-status2');



  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

  Route::resource('kendaraan', KendaraanController::class);

  Route::patch(
    '/kendaraan/{kendaraan}/status',
    [KendaraanController::class, 'updateStatus']
  )->name('kendaraan.updateStatus');



  Route::resource('jenis', JenisController::class);

  Route::resource('kir', KirController::class);

  Route::resource('member', MemberController::class)->except(['show']);

  Route::resource('member-rental', MemberKendaraanController::class);


  Route::resource('service', ServiceController::class);
  Route::resource('service-history', ServiceHistoryController::class);
  Route::put('service-history/{id}/status', [ServiceHistoryController::class, 'updateStatus'])
    ->name('service-history.update-status');

  Route::resource('service-detail', ServiceDetailController::class);
  Route::put('service/service-detail/{id}/status', [ServiceDetailController::class, 'updateStatus']);



  Route::resource('supplier', SupplierController::class);

  Route::resource('user', UserController::class);

  Route::resource('asuransi', AsuransiController::class);

  Route::resource('jenis-asuransi', JenisAsuransiController::class);

  Route::resource('asuransi-kendaraan', AsuransiKendaraanController::class);

  Route::resource('gps', GpsController::class);

  Route::resource('history', historyController::class);

  Route::resource('gps-kendaraan', GpskendaraanController::class);

  Route::resource('stnk', StnkController::class);


  Route::resource('keuangan', KeuanganController::class);

  Route::post('rental/{id}/status', [RentalController::class, 'updateStatus'])
    ->name('rental.updateStatus');

  Route::post('/rental/{id}/pelunasan', [RentalController::class, 'pelunasan'])
    ->name('rental.pelunasan');

  Route::post(
    'rental/{id}/tambah-biaya',
    [RentalController::class, 'tambahBiaya']
  )->name('rental.tambahBiaya');


  Route::post('/admin/rental/{id}/upload-bukti', [RentalController::class, 'uploadBuktiTf'])
    ->name('rental.uploadBuktiTf');

  Route::resource('rental', RentalController::class)
    ->except(['create', 'edit']);

  Route::resource('hutang-vendor', HutangVendorController::class);

  Route::resource('budgeting', BudgetingController::class);

  Route::resource('konsolidasi', KonsolidasiController::class);

  Route::resource('efaktur', EfakturController::class);

  Route::resource('bupot', BupotController::class);

  Route::resource('rekonsiliasi', RekonsiliasiController::class);

  Route::resource('virtual', VirtualController::class);

  Route::resource('bukubesar', BukubesarController::class);

  Route::resource('pajak', PajakController::class);

  Route::get('invoice/{id}/print', [InvoicesController::class, 'print'])
    ->name('invoices.print');
  Route::post('invoice/{id}/send-email', [InvoicesController::class, 'sendEmail'])
    ->name('invoices.email');
  Route::resource('invoices', InvoicesController::class);

  Route::get('/payments/pdf', [PaymentsController::class, 'exportPdf'])
    ->name('payments.pdf');
  Route::resource('payments', PaymentsController::class);


  Route::resource('summary', SummaryController::class);
  Route::get('/admin/summary/pdf', [SummaryController::class, 'exportPdf'])
    ->name('summary.pdf');

  Route::get('/reminders/pdf', [ReminderController::class, 'exportPdf'])
    ->name('reminders.pdf');
  Route::resource('reminders', ReminderController::class);


  Route::resource('penawaran', InvPenawaranController::class)
    ->except(['create', 'show']);
  Route::post('penawaran/{id}/approve', [InvPenawaranController::class, 'approve'])
    ->name('penawaran.approve');
  Route::post('/penawaran/{id}/reject', [InvPenawaranController::class, 'reject'])
    ->name('penawaran.reject');
  Route::get('/admin/penawaran/pdf', [InvPenawaranController::class, 'exportPdf'])
    ->name('penawaran.pdf');

    Route::get('/kontrak/pdf', [InvKontrakController::class, 'pdf'])
      ->name('kontrak.pdf');
  Route::resource('kontrak', InvKontrakController::class);

  Route::get('/setting', [SettingController::class, 'index'])
    ->name('setting.index');

  Route::post('/setting', [SettingController::class, 'update'])
    ->name('setting.update');

    //Schedule
    Schedule::command('app:reminder-pajak-command')
    // ->dailyAt('15:14');
    ->everyMinute();
    Schedule::command('app:reminder-asuransi-command')
    // ->dailyAt('16:25');
    ->everyMinute();
    Schedule::command('app:reminder-gps-command')
    //  ->dailyAt('16:45');
    ->everyMinute();
    Schedule::command('app:reminder-kir-command')
    // ->dailyAt('19:11');
    ->everyMinute();
    Schedule::command('app:reminder-rental-command')
    // ->dailyAt('19:22');
    ->everyMinute();
    Schedule::command('service:reminder-overservice')
    // ->dailyAt('19:22');
    ->everyMinute();
     Schedule::command('hutang:reminder')
    //  ->dailyAt('19:22');
    ->everyMinute();
     Schedule::command('hutang:app:reminder-invoice-command')
    // ->dailyAt('19:22');
    ->everyMinute();
    Schedule::command('app:reminder-penawaran-command')
    // ->dailyAt('19:22');
    ->everyMinute();
    

    // BUG
    // Schedule::command('app:reminder-pajak-command')
    // ->everyMinute();
});
