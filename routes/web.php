<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// ── Legal ────────────────────────────────────────────────────
use App\Http\Controllers\Admin\LegalDocumentController;
use App\Http\Controllers\Admin\KontrakAktifController;
use App\Http\Controllers\Admin\ReviewLegalController;
use App\Http\Controllers\Admin\HakHukumController;
use App\Http\Controllers\Admin\LitigasiController;
use App\Http\Controllers\Admin\SertifikasiPerizinanController;
use App\Http\Controllers\Admin\DaftarNotarisController;
use App\Http\Controllers\Admin\KendaraanController;
use App\Http\Controllers\Admin\JenisController;
use App\Http\Controllers\Admin\KirController;
use App\Http\Controllers\Admin\KirHistoryController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberKendaraanController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceHistoryController;
use App\Http\Controllers\Admin\ServiceDetailController;
use App\Http\Controllers\Admin\ServiceAsuransiController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ReminderServiceController;
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
use App\Http\Controllers\Admin\IntegrasiController;
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
use App\Http\Controllers\Admin\DataLeasingController;
use App\Http\Controllers\Admin\DataKontrakController;
use App\Http\Controllers\Admin\InvoicesController;
use App\Http\Controllers\Admin\InvoicePeriodeController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\SummaryController;
use App\Http\Controllers\Admin\ReminderController;
use App\Http\Controllers\Admin\ProcurementoController;
use App\Http\Controllers\Admin\PurchaseroController;
use App\Http\Controllers\Admin\VendoreoController;
use App\Http\Controllers\Admin\Aging_ApsController;
use App\Http\Controllers\Admin\AgingARController;
// Marketing
use App\Http\Controllers\Admin\KampanyeController;
use App\Http\Controllers\Admin\OtomatisasiController;
use App\Http\Controllers\Admin\SegmentasiController;
use App\Http\Controllers\Admin\LoyaltyController;
use App\Http\Controllers\Admin\AfiliasiController;
use App\Http\Controllers\Admin\SosmedpController;
use App\Http\Controllers\Admin\TrackingutmController;
use App\Http\Controllers\Admin\AdsIntegrationController;
// Sales
use App\Http\Controllers\Admin\CrmProspekController;
use App\Http\Controllers\Admin\PenawaranSalesController;
use App\Http\Controllers\Admin\SalesOrderController;
use App\Http\Controllers\Admin\PricelistDiskonController;
use App\Http\Controllers\Admin\TargetPenjualanController;
use App\Http\Controllers\Admin\KomisiSalesController;
use App\Http\Controllers\Admin\ReturPenjualanController;
use App\Http\Controllers\Admin\SignatureDokumenController;

// Project
use App\Http\Controllers\Admin\IndukProyekController;
use App\Http\Controllers\Admin\ProjectPlanningController;
use App\Http\Controllers\Admin\ProjectTimelineController;
use App\Http\Controllers\Admin\ProjectCostController;
use App\Http\Controllers\Admin\ProjectRiskController;
use App\Http\Controllers\Admin\DokumenProyekController;
use App\Http\Controllers\Admin\PembelianProyekController;

// Purchase
use App\Http\Controllers\Admin\RequestforQuotationController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\VendorPricelistController;
use App\Http\Controllers\Admin\ApprovalWorkflowController;
use App\Http\Controllers\Admin\DropshippingController;
use App\Http\Controllers\Admin\VendorPerformanceController;
// IT Technology
use App\Http\Controllers\Admin\ItassetManagementController;
use App\Http\Controllers\Admin\SoftwareLicenseController;
use App\Http\Controllers\Admin\HelpdeskSupportController;
use App\Http\Controllers\Admin\UserAccessController;
use App\Http\Controllers\Admin\NetworkMonitoringController;
use App\Http\Controllers\Admin\CybersecurityController;
use App\Http\Controllers\Admin\EmailDomainController;
use App\Http\Controllers\Admin\ServerCloudController;
use App\Http\Controllers\Admin\SystemBackupController;
use App\Http\Controllers\Admin\ProjectManagementController;
use App\Http\Controllers\Admin\DevopsController;
use App\Http\Controllers\Admin\PolicyComplianceController;
// HRD
use App\Http\Controllers\Admin\StrukturOrganisasiController;
use App\Http\Controllers\Admin\DepartemenController;
use App\Http\Controllers\Admin\SkillMatrixController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Admin\ShiftLemburController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\CutiIzinController;
use App\Http\Controllers\Admin\KpiAppraisalController;
use App\Http\Controllers\Admin\ResignOffboardingController;
use App\Http\Controllers\Admin\HrdFileController;
// Asset Management
use App\Http\Controllers\Admin\Asset\IndukAssetController;
use App\Http\Controllers\Admin\Asset\PergerakanAssetController;
use App\Http\Controllers\Admin\Asset\PemeliharaanAssetController;
use App\Http\Controllers\Admin\Asset\PenyusutanAssetController;
use App\Http\Controllers\Admin\Asset\PerolehanAssetController;
use App\Http\Controllers\Admin\Asset\AssetDihapuskanController;
use App\Http\Controllers\Admin\Asset\DokumentasiAssetController;
use App\Http\Controllers\Admin\Asset\PenanggungJawabController;
use App\Http\Controllers\Admin\Asset\AuditAssetController;
// User
use App\Http\Controllers\User\ProfileController;


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



Route::middleware(['auth', 'check.status'])->prefix('admin')->group(function () {
  // Laporan
  Route::get('/supplier/pdf', [SupplierController::class, 'pdf'])
    ->name('supplier.export.pdf');
  
  // API untuk create supplier via AJAX
  Route::post('/supplier/api/store', [SupplierController::class, 'storeApi'])
    ->name('supplier.api.store');

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

  Route::get('/admin/history/{kendaraan}/export-pdf', [HistoryController::class, 'exportPdf'])
    ->name('history.export.pdf');

  Route::post('gps-kendaraan/kendaraan/{kendaraanId}/perpanjang-semua', [GpsKendaraanController::class, 'perpanjangKendaraan'])
    ->middleware('throttle:5,1')
    ->name('gps-kendaraan.perpanjang-semua');

  Route::post('gps-kendaraan/{id}/perpanjang', [GpsKendaraanController::class, 'perpanjang'])
    ->middleware('throttle:5,1')
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
)->name('asuransi-kendaraan.export.pdf'); // ← tambahkan ini


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
  )->middleware('throttle:5,1')->name('pajak.perpanjang');
  Route::delete('/admin/history/pajak/{id}', [PajakHistoryController::class, 'destroy'])
    ->name('history.pajak.destroy');



  Route::get('/user/export-pdf', [UserController::class, 'exportPdf'])
    ->name('user.export.pdf');

  Route::get('/pelanggan/pdf', [PelangganController::class, 'pdf']);

  Route::get('/members/pdf', [MemberController::class, 'pdf'])->name('members.pdf');

  Route::get('/admin/rental/pdf', [RentalController::class, 'pdf'])
    ->name('rental.pdf');
  Route::get('/rental/{id}/invoice', [RentalController::class, 'invoice'])
    ->name('rental.invoice');
  // Task 8: Cetak invoice PDF dari halaman rental (same style as invoices.print)
  Route::get('/rental/{id}/invoice-pdf', [RentalController::class, 'invoicePdf'])
    ->name('rental.invoice-pdf');

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

  // ── INTEGRASI BANK ─────────────────────────────────────
  Route::get('/integrasi-bank', [IntegrasiController::class, 'index'])
    ->name('integrasi-bank.index');

  // Rekonsiliasi via integrasi
  Route::post('/integrasi-bank/rekonsiliasi', [IntegrasiController::class, 'rekonsiliasiStore'])
    ->name('integrasi-bank.rekonsiliasi.store');
  Route::put('/integrasi-bank/rekonsiliasi/{id}', [IntegrasiController::class, 'rekonsiliasiUpdate'])
    ->name('integrasi-bank.rekonsiliasi.update');
  Route::delete('/integrasi-bank/rekonsiliasi/{id}', [IntegrasiController::class, 'rekonsiliasiDestroy'])
    ->name('integrasi-bank.rekonsiliasi.destroy');
  Route::get('/integrasi-bank/rekonsiliasi/pdf', [IntegrasiController::class, 'rekonsiliasiPdf'])
    ->name('integrasi-bank.rekonsiliasi.pdf');
  Route::get('/integrasi-bank/rekonsiliasi/excel', [IntegrasiController::class, 'rekonsiliasiExcel'])
    ->name('integrasi-bank.rekonsiliasi.excel');

  // Virtual Account via integrasi
  Route::post('/integrasi-bank/virtual', [IntegrasiController::class, 'virtualStore'])
    ->name('integrasi-bank.virtual.store');
  Route::put('/integrasi-bank/virtual/{id}', [IntegrasiController::class, 'virtualUpdate'])
    ->name('integrasi-bank.virtual.update');
  Route::delete('/integrasi-bank/virtual/{id}', [IntegrasiController::class, 'virtualDestroy'])
    ->name('integrasi-bank.virtual.destroy');
  Route::get('/integrasi-bank/virtual/pdf', [IntegrasiController::class, 'virtualPdf'])
    ->name('integrasi-bank.virtual.pdf');
  Route::get('/integrasi-bank/virtual/excel', [IntegrasiController::class, 'virtualExcel'])
    ->name('integrasi-bank.virtual.excel');

  Route::get('/bukubesar/pdf', [BukubesarController::class, 'pdf'])
    ->name('bukubesar.pdf');
  Route::get('/bukubesar/excel', [BukubesarController::class, 'exportExcel'])
    ->name('bukubesar.export.excel');
  Route::get('/bukubesar/csv', [BukubesarController::class, 'exportCsv'])
    ->name('bukubesar.export.csv');

  Route::get('/bukubesar/laba-rugi/csv', [BukubesarController::class, 'exportCsvLabaRugi'])
    ->name('bukubesar.laba-rugi.csv');
  Route::get('/bukubesar/laba-rugi/excel', [BukubesarController::class, 'exportExcelLabaRugi'])
    ->name('bukubesar.laba-rugi.excel');
  Route::get('/bukubesar/laba-rugi/pdf', [BukubesarController::class, 'pdfLabaRugi'])
    ->name('bukubesar.laba-rugi.pdf');

  Route::get('/service-detail/pdf', [ServiceDetailController::class, 'pdf'])
    ->name('service-detail.pdf');


  Route::get('/kir/pdf', [KirController::class, 'pdf'])
    ->name('kir.pdf');

  Route::get('/kir/kendaraan/{id}/detail', [KirController::class, 'getKendaraanDetail'])
    ->name('kir.kendaraan.detail');

  Route::patch('kir/{id}/status', [KirController::class, 'updateStatusUji'])
    ->name('kir.update-status');

  Route::get('/admin/history-kir/export-pdf', [KirHistoryController::class, 'exportPdf'])
    ->name('history.kir.export');

  Route::post('kir/{id}/perpanjang', [KirController::class, 'perpanjang'])
    ->middleware('throttle:5,1')
    ->name('kir.perpanjang');
  Route::post('kir/perpanjang-semua', [KirController::class, 'perpanjangSemua'])->name('kir.perpanjang-semua');
  Route::get('/kir-history', [KirHistoryController::class, 'index'])
    ->name('history.kir.index');


  Route::get('/service-history/pdf', [ServiceHistoryController::class, 'pdf'])
    ->name('service-history.pdf');
  Route::post('/service-history/{id}/terpasang', [ServiceHistoryController::class, 'terpasang'])
    ->name('service-history.terpasang');
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

  Route::resource('purchasero', PurchaseroController::class) // Pengadaan
    ->except(['show']); // Multiple items structure dengan create.blade.php dan edit.blade.php

  Route::get('purchasero/{purchasero}/details', [PurchaseroController::class, 'details'])
    ->name('purchasero.details');

  Route::post('purchasero/{purchasero}/ajukan', [PurchaseroController::class, 'ajukan'])
    ->name('purchasero.ajukan');

  Route::post('purchasero/{purchasero}/status', [PurchaseroController::class, 'updateStatusInline'])
    ->name('purchasero.status');

  // Setujui pengadaan service (dengan upload bukti)
  Route::post('purchasero/{purchasero}/approve-service', [PurchaseroController::class, 'approveService'])
    ->name('purchasero.approve-service');

  // ── Approval Workflow Routes (Pengeluaran Kendaraan) ────────────────────────
  Route::get('purchasero/{purchasero}/approval-modal', [PurchaseroController::class, 'showApprovalModal'])
    ->name('purchasero.approval-modal');
  
  Route::post('purchasero/{purchasero}/approve', [PurchaseroController::class, 'approve'])
    ->name('purchasero.approve');
  
  Route::post('purchasero/{purchasero}/reject', [PurchaseroController::class, 'reject'])
    ->name('purchasero.reject');
  
  Route::post('purchasero/bulk-approve', [PurchaseroController::class, 'bulkApprove'])
    ->name('purchasero.bulk-approve');
  
  Route::post('purchasero/bulk-reject', [PurchaseroController::class, 'bulkReject'])
    ->name('purchasero.bulk-reject');
  
  Route::delete('purchasero/{purchasero}/withdraw', [PurchaseroController::class, 'withdraw'])
    ->name('purchasero.withdraw');
  
  Route::get('purchasero/{purchasero}/edit-rejected', [PurchaseroController::class, 'editRejected'])
    ->name('purchasero.edit-rejected');

  // AJAX endpoints untuk form pengadaan service
  Route::get('purchasero/api/kendaraan-service', [PurchaseroController::class, 'apiKendaraan'])
    ->name('purchasero.api.kendaraan');
  Route::get('purchasero/api/category-limit', [PurchaseroController::class, 'apiCategoryLimit'])
    ->name('purchasero.api.category-limit');
  Route::post('purchasero/{purchasero}/terpasang', [PurchaseroController::class, 'terpasang'])
    ->name('purchasero.terpasang');

  Route::resource('vendoreo', VendoreoController::class) // Manajemen Vendor
    ->except(['create', 'edit', 'show']); // Form CRUD With Modal

  Route::post('/rental/{id}/update-status', [RentalController::class, 'updateStatus'])
    ->name('rental.update-status');
  Route::post('/rental/{id}/update-status2', [RentalController::class, 'toogletatus'])->name('rental.update-status2');



  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

  // Test Chart System (Development only)
  Route::get('/test-chart', function () {
      return view('admin.test-chart');
  })->name('test-chart');

  // Chart Data API
  Route::get('/chart-data/{page}', [App\Http\Controllers\Admin\ChartDataController::class, 'getData'])
    ->name('chart.data');

  Route::resource('kendaraan', KendaraanController::class);

  // Service History per Kendaraan
  Route::get(
    '/kendaraan/{id}/service-history',
    [KendaraanController::class, 'serviceHistory']
  )->name('kendaraan.service-history');

  // Pajak / Asuransi / KIR / GPS History per Kendaraan
  Route::get(
    '/kendaraan/{id}/pajak-history',
    [PajakHistoryController::class, 'kendaraan']
  )->name('kendaraan.pajak-history');

  Route::get(
    '/kendaraan/{id}/asuransi-history',
    [AsuransiHistoryController::class, 'kendaraan']
  )->name('kendaraan.asuransi-history');

  Route::get(
    '/kendaraan/{id}/kir-history',
    [KirHistoryController::class, 'kendaraan']
  )->name('kendaraan.kir-history');

  Route::get(
    '/kendaraan/{id}/gps-history',
    [GpsKendaraanHistoryController::class, 'kendaraan']
  )->name('kendaraan.gps-history');

  Route::patch(
    '/kendaraan/{kendaraan}/status',
    [KendaraanController::class, 'updateStatus']
  )->name('kendaraan.updateStatus');

  Route::delete(
    '/kendaraan/{kendaraan}/foto-masalah',
    [KendaraanController::class, 'deleteFotoMasalah']
  )->name('kendaraan.foto-masalah.delete');



  Route::resource('jenis', JenisController::class);

  Route::resource('kir', KirController::class);

  Route::resource('pelanggan', PelangganController::class)->except(['show']);

  Route::get('/members/search', [MemberController::class, 'search'])->name('members.search');
  Route::resource('members', MemberController::class)->except(['create', 'edit']);
  Route::delete('/members/{id}/stnk', [MemberController::class, 'destroyStnk'])->name('members.stnk.destroy');
  Route::delete('/members/{id}/attachment', [MemberController::class, 'destroyAttachment'])->name('members.attachment.destroy');

  Route::resource('member-rental', MemberKendaraanController::class);


  Route::resource('service', ServiceController::class);

  // Route tambahan Service History — HARUS di atas resource agar tidak bentrok dengan {service_history} param
  Route::post('service-history/categories', [ServiceHistoryController::class, 'storeCategory'])
      ->name('service-history.storeCategory');
  Route::delete('service-history/attachment/{id}', [ServiceHistoryController::class, 'destroyAttachment'])
      ->name('service-history.attachment.destroy');
  Route::delete('service-parts/{id}/bukti', [ServiceHistoryController::class, 'deletePartBukti'])
      ->name('service-parts.bukti.delete');
  Route::put('service-parts/{id}/status', [ServiceHistoryController::class, 'updatePartStatus'])
      ->name('service-parts.update-status');
  Route::get('service-history/kendaraan/{id}/data', [ServiceHistoryController::class, 'getKendaraanData'])
      ->name('service-history.kendaraan-data');

  // Request Part routes
  Route::get('service-history/request/create', [ServiceHistoryController::class, 'requestCreate'])
      ->name('service-history.request.create');
  Route::post('service-history/request', [ServiceHistoryController::class, 'requestStore'])
      ->name('service-history.request.store');
  Route::get('service-history/{id}/edit-request', [ServiceHistoryController::class, 'editRequest'])
      ->name('service-history.request.edit');
  Route::put('service-history/{id}/edit-request', [ServiceHistoryController::class, 'updateRequest'])
      ->name('service-history.request.update');
  Route::post('service-history/{id}/approve', [ServiceHistoryController::class, 'approve'])
      ->name('service-history.approve');
  Route::post('service-history/{id}/reject', [ServiceHistoryController::class, 'reject'])
      ->name('service-history.reject');

  // Approve / Reject per part (superadmin only)
  Route::middleware(['role:superadmin'])->group(function () {
      Route::post('service-parts/{id}/approve', [ServiceHistoryController::class, 'approvePart'])
          ->name('service-parts.approve');
      Route::post('service-parts/{id}/reject', [ServiceHistoryController::class, 'rejectPart'])
          ->name('service-parts.reject');
  });

  Route::resource('service-history', ServiceHistoryController::class);
  Route::put('service-history/{id}/status', [ServiceHistoryController::class, 'updateStatus'])
    ->name('service-history.update-status');

  Route::resource('service-detail', ServiceDetailController::class);
  Route::put('service/service-detail/{id}/status', [ServiceDetailController::class, 'updateStatus']);
  Route::delete('service-detail/{id}/bukti', [ServiceDetailController::class, 'deleteBukti'])
      ->name('service-detail.bukti.delete');
  Route::delete('service-detail/{id}/attachment', [ServiceDetailController::class, 'deleteAttachment'])
      ->name('service-detail.attachment.delete');

  // Service Asuransi
  Route::resource('service-asuransi', ServiceAsuransiController::class);
  Route::delete('service-asuransi/{id}/bukti', [ServiceAsuransiController::class, 'deleteBukti'])
      ->name('service-asuransi.bukti.delete');
  Route::delete('service-asuransi/{id}/attachment', [ServiceAsuransiController::class, 'deleteAttachment'])
      ->name('service-asuransi.attachment.delete');
  Route::put('service-asuransi/{id}/status', [ServiceAsuransiController::class, 'updateStatus'])
      ->name('service-asuransi.update-status');

  // Reminder Service
  Route::resource('reminder-service', ReminderServiceController::class);
  Route::put('reminder-service/{id}/status', [ReminderServiceController::class, 'updateStatus'])
      ->name('reminder-service.update-status');

  // Service Categories CRUD + Limit Rules
  // Service Categories - Superadmin Only
  Route::middleware(['role:superadmin'])->group(function () {
      // Ajax endpoint HARUS di atas resource agar tidak bentrok dengan {service_category} param
      Route::get('service-categories/limit-for',       [ServiceCategoryController::class, 'getLimitFor'])
          ->withoutMiddleware('role:superadmin') // Allow all users untuk get limit (dipake di form)
          ->name('service-categories.limit-for');
      Route::resource('service-categories', ServiceCategoryController::class)->except(['show', 'create', 'edit']);
      Route::post('service-categories/{id}/limits',    [ServiceCategoryController::class, 'storeLimitRule'])
          ->name('service-categories.limits.store');
      Route::put('service-categories/limits/{limitId}', [ServiceCategoryController::class, 'updateLimitRule'])
          ->name('service-categories.limits.update');
      Route::delete('service-categories/limits/{limitId}', [ServiceCategoryController::class, 'destroyLimitRule'])
          ->name('service-categories.limits.destroy');
  });



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

  Route::resource('aging_ap', Aging_ApsController::class);

  Route::post('/aging-ar/{id}/bayar', [AgingArController::class, 'bayar'])->name('aging_ar.bayar');
  Route::get('/aging_ar/lunas', [AgingArController::class, 'lunas'])->name('aging_ar.lunas.index');
  Route::get('/aging_ar/reminder', [AgingArController::class, 'reminder'])
    ->name('aging_ar.reminder');
  Route::resource('aging_ar', AgingARController::class)
    ->except(['show']);





  Route::get('invoice/{id}/print', [InvoicesController::class, 'print'])
    ->name('invoices.print');
  Route::post('invoice/{id}/recalculate-summary', [InvoicesController::class, 'recalculateSummary'])
    ->name('invoices.recalculateSummary');
  Route::post('invoice/{id}/send-email', [InvoicesController::class, 'sendEmail'])
    ->name('invoices.email');
  Route::get('invoices/ttd-library', [InvoicesController::class, 'ttdLibrary'])
    ->name('invoices.ttd-library');
  Route::get('invoices/export/excel', [InvoicesController::class, 'exportExcel'])
    ->name('invoices.export.excel');
  Route::get('invoices/lookup-kontrak', [InvoicesController::class, 'lookupKontrak'])
    ->name('invoices.lookup-kontrak');
  Route::get('invoices/{invoice}/compute-total', [InvoicesController::class, 'getComputedTotal'])
    ->name('invoices.compute-total');
  Route::resource('invoices', InvoicesController::class);

  // Task 5: Tambah pembayaran cicilan langsung dari detail invoice
  Route::post('invoices/{invoice}/payments', [InvoicesController::class, 'addPayment'])
    ->name('invoices.payments.store');

  // ── PERIODE & REMAKS (nested, AJAX) ────────────────────────
  Route::prefix('invoices/{invoice}/periodes')->group(function () {
    Route::get('/',                                      [InvoicePeriodeController::class, 'index'])        ->name('invoices.periodes.index');
    Route::post('/',                                     [InvoicePeriodeController::class, 'store'])        ->name('invoices.periodes.store');
    Route::put('/{periode}',                             [InvoicePeriodeController::class, 'update'])       ->name('invoices.periodes.update');
    Route::delete('/{periode}',                          [InvoicePeriodeController::class, 'destroy'])      ->name('invoices.periodes.destroy');
    Route::post('/{periode}/remaks',                     [InvoicePeriodeController::class, 'storeRemak'])   ->name('invoices.periodes.remaks.store');
    Route::put('/{periode}/remaks/{remak}',              [InvoicePeriodeController::class, 'updateRemak'])  ->name('invoices.periodes.remaks.update');
    Route::delete('/{periode}/remaks/{remak}',           [InvoicePeriodeController::class, 'destroyRemak']) ->name('invoices.periodes.remaks.destroy');
  });

  Route::get('/payments/pdf', [PaymentsController::class, 'exportPdf'])
    ->name('payments.pdf');
  Route::get('/payments/export/excel', [PaymentsController::class, 'exportExcel'])
    ->name('payments.export.excel');
  Route::resource('payments', PaymentsController::class);


  Route::resource('summary', SummaryController::class);
  Route::delete('/summary/destroy-by-kontrak/{kontrak_id}', [SummaryController::class, 'destroyByKontrak'])
    ->name('summary.destroyByKontrak');
  Route::get('/summary/kontrak/{kontrak_id}/detail', [SummaryController::class, 'detailKontrak'])
    ->name('summary.detailKontrak');
  Route::get('/admin/summary/pdf', [SummaryController::class, 'exportPdf'])
    ->name('summary.pdf');
  Route::get('/summary/export/excel', [SummaryController::class, 'exportExcel'])
    ->name('summary.export.excel');

  Route::get('/reminders/pdf', [ReminderController::class, 'exportPdf'])
    ->name('reminders.pdf');
  Route::get('/reminders/export/excel', [ReminderController::class, 'exportExcel'])
    ->name('reminders.export.excel');
  Route::resource('reminders', ReminderController::class);


  // ── PENAWARAN ─── custom routes HARUS sebelum resource ──────────────
  Route::get('/penawaran/customer-search', [InvPenawaranController::class, 'customerSearch'])
    ->name('penawaran.customer-search');
  Route::get('/admin/penawaran/pdf', [InvPenawaranController::class, 'exportPdf'])
    ->name('penawaran.pdf');
  Route::get('/penawaran/export/excel', [InvPenawaranController::class, 'exportExcel'])
    ->name('penawaran.export.excel');
  Route::post('penawaran/{id}/approve', [InvPenawaranController::class, 'approve'])
    ->name('penawaran.approve');
  Route::get('penawaran/{id}/download-draft', [InvPenawaranController::class, 'downloadDraft'])
    ->name('penawaran.download-draft');
  Route::post('/penawaran/{id}/reject', [InvPenawaranController::class, 'reject'])
    ->name('penawaran.reject');
  Route::get('penawaran/{id}/print', [InvPenawaranController::class, 'printView'])
    ->name('penawaran.print');
  Route::resource('penawaran', InvPenawaranController::class)
    ->except(['create', 'show']);

  Route::get('/kontrak/pdf', [InvKontrakController::class, 'pdf'])
    ->name('kontrak.pdf');
  Route::get('/kontrak/export/excel', [InvKontrakController::class, 'exportExcel'])
    ->name('kontrak.export.excel');
  Route::get('/kontrak/{id}/regenerate-draft', [InvKontrakController::class, 'regenerateDraft'])
    ->name('kontrak.regenerate-draft');
  Route::get('/kontrak/{id}/draft-print', [InvKontrakController::class, 'draftPrint'])
    ->name('kontrak.draft-print');

  // Approve & Selesai workflow
  Route::post('/kontrak/{id}/approve', [InvKontrakController::class, 'approve'])
    ->name('kontrak.approve');
  Route::post('/kontrak/{id}/selesai', [InvKontrakController::class, 'selesai'])
    ->name('kontrak.selesai');

  // AJAX: get penawaran detail untuk auto-fill form
  Route::get('/kontrak/penawaran/{id}/detail', [InvKontrakController::class, 'getPenawaranDetail'])
    ->name('kontrak.penawaran.detail');

  Route::resource('kontrak', InvKontrakController::class);

  // ── DATA LEASING ────────────────────────────────────────────────────
  Route::get('/data-leasing/template', [DataLeasingController::class, 'exportTemplate'])
    ->name('data-leasing.template');
  Route::get('/data-leasing/export', [DataLeasingController::class, 'exportData'])
    ->name('data-leasing.export');
  Route::get('/data-leasing/export-full', [DataLeasingController::class, 'exportLeasing'])
    ->name('data-leasing.export-full');
  Route::get('/data-kontrak/export-full', [DataKontrakController::class, 'exportKontrak'])
    ->name('data-kontrak.export-full');
  Route::post('/data-leasing/import', [DataLeasingController::class, 'import'])
    ->name('data-leasing.import');
  Route::get('/data-leasing/data-kontrak/{id}/detail', [DataLeasingController::class, 'getDataKontrakDetail'])
    ->name('data-leasing.data-kontrak-detail');
  Route::resource('data-leasing', DataLeasingController::class)
    ->except(['create', 'edit', 'show']);

  // ── DATA KONTRAK ─────────────────────────────────────────────────────
  Route::get('/data-kontrak/generate-no', [DataKontrakController::class, 'generateNoKontrak'])
    ->name('data-kontrak.generate-no');
  Route::get('/data-kontrak/kendaraan/{id}/detail', [DataKontrakController::class, 'getKendaraanDetail'])
    ->name('data-kontrak.kendaraan-detail');
  Route::delete('/data-kontrak/attachment/{id}', [DataKontrakController::class, 'destroyAttachment'])
    ->name('data-kontrak.attachment.destroy');
  Route::resource('data-kontrak', DataKontrakController::class)
    ->except(['create', 'edit', 'show']);

  Route::get('/setting', [SettingController::class, 'index'])
    ->name('setting.index');

  Route::post('/setting', [SettingController::class, 'update'])
    ->name('setting.update');

  // ── LEGAL ────────────────────────────────────────────────────
  Route::get('legal-document/pdf', [LegalDocumentController::class, 'pdf'])->name('legal-document.pdf');
  Route::resource('legal-document', LegalDocumentController::class)->except(['create', 'edit']);

  Route::get('kontrak-aktif/pdf', [KontrakAktifController::class, 'pdf'])->name('kontrak-aktif.pdf');
  Route::resource('kontrak-aktif', KontrakAktifController::class)->except(['create', 'edit']);

  Route::get('review-legal/pdf', [ReviewLegalController::class, 'pdf'])->name('review-legal.pdf');
  Route::resource('review-legal', ReviewLegalController::class)->except(['create', 'edit']);

  Route::get('hak-hukum/pdf', [HakHukumController::class, 'pdf'])->name('hak-hukum.pdf');
  Route::resource('hak-hukum', HakHukumController::class)->except(['create', 'edit']);

  Route::get('litigasi/pdf', [LitigasiController::class, 'pdf'])->name('litigasi.pdf');
  Route::resource('litigasi', LitigasiController::class)->except(['create', 'edit']);

  Route::get('sertifikasi-perizinan/pdf', [SertifikasiPerizinanController::class, 'pdf'])->name('sertifikasi-perizinan.pdf');
  Route::resource('sertifikasi-perizinan', SertifikasiPerizinanController::class)->except(['create', 'edit']);

  Route::get('daftar-notaris/pdf', [DaftarNotarisController::class, 'pdf'])->name('daftar-notaris.pdf');
  Route::resource('daftar-notaris', DaftarNotarisController::class)->except(['create', 'edit']);

  

  // BUG
  // Schedule::command('app:reminder-pajak-command')
  // ->everyMinute();


  // attachment
  Route::delete('/pajak/attachment/{id}', [PajakController::class, 'destroyAttachment'])->name('pajak.attachment.destroy');
  Route::delete('/gps-kendaraan/attachment/{id}', [GpsKendaraanController::class, 'destroyAttachment'])->name('gps.attachment.destroy');
  Route::delete('/asuransi-kendaraan/attachment/{id}', [AsuransiKendaraanController::class, 'destroyAttachment'])->name('asuransi.attachment.destroy');
  Route::delete('/kir/attachment/{id}', [KirController::class, 'destroyAttachment'])->name('kir.attachment.destroy');
  Route::delete('/service-history/attachment/{id}', [ServiceHistoryController::class, 'destroyAttachment'])->name('service-history.attachment.destroy');

  // ── DETAIL AJAX (modal per-record) ──────────────────────────────────────
  Route::get('/pajak/{id}/detail',             [PajakController::class,             'detail'])->name('pajak.detail');
  Route::get('/asuransi-kendaraan/{id}/detail',[AsuransiKendaraanController::class, 'detail'])->name('asuransi-kendaraan.detail');
  Route::get('/gps-kendaraan/{id}/detail',     [GpsKendaraanController::class,      'detail'])->name('gps-kendaraan.detail');
  Route::get('/kir/{id}/detail',               [KirController::class,               'detail'])->name('kir.detail');

  // AutoSuggest
  Route::get('/ajax/pelanggan', [AgingArController::class, 'searchMember']);
  Route::get('/ajax/invoices', [AgingArController::class, 'searchInvoice']);

  // ── PURCHASE ───────────────────────────────────────────────
  Route::resource('requestfor-quotation', RequestforQuotationController::class)->except(['create', 'edit', 'show']);
  Route::resource('purchase-order', PurchaseOrderController::class)->except(['create', 'edit', 'show']);
  Route::resource('vendor-pricelist', VendorPricelistController::class)->except(['create', 'edit', 'show']);
  Route::resource('approval-workflow', ApprovalWorkflowController::class)->except(['create', 'edit', 'show']);
  Route::resource('dropshipping', DropshippingController::class)->except(['create', 'edit', 'show']);
  Route::resource('vendor-performance', VendorPerformanceController::class)->except(['create', 'edit', 'show']);

  // ── IT TECHNOLOGY ──────────────────────────────────────────
  Route::resource('assetm', ItassetManagementController::class)->except(['create', 'edit', 'show']);
  Route::resource('softwarel', SoftwareLicenseController::class)->except(['create', 'edit', 'show']);
  Route::resource('helpdesk', HelpdeskSupportController::class)->except(['create', 'edit', 'show']);
  Route::resource('useraccess', UserAccessController::class)->except(['create', 'edit', 'show']);
  Route::resource('networkm', NetworkMonitoringController::class)->except(['create', 'edit', 'show']);
  Route::resource('cybers', CybersecurityController::class)->except(['create', 'edit', 'show']);
  Route::resource('emaild', EmailDomainController::class)->except(['create', 'edit', 'show']);
  Route::resource('serverc', ServerCloudController::class)->except(['create', 'edit', 'show']);
  Route::resource('systemb', SystemBackupController::class)->except(['create', 'edit', 'show']);
  Route::resource('projectm', ProjectManagementController::class)->except(['create', 'edit', 'show']);
  Route::resource('devops', DevopsController::class)->except(['create', 'edit', 'show']);
  Route::resource('policyc', PolicyComplianceController::class)->except(['create', 'edit', 'show']);

  // ── HRD ────────────────────────────────────────────────────
  Route::resource('struktur', StrukturOrganisasiController::class)->except(['create', 'edit', 'show']);
  Route::resource('departemen', DepartemenController::class)->except(['create', 'edit', 'show']);
  Route::resource('skills', SkillMatrixController::class)->except(['create', 'edit', 'show']);
  Route::resource('presensi', PresensiController::class)->except(['create', 'edit', 'show']);
  Route::resource('shift', ShiftLemburController::class)->except(['create', 'edit', 'show']);
  Route::resource('payroll', PayrollController::class)->except(['create', 'edit', 'show']);
  Route::resource('cuti', CutiIzinController::class)->except(['create', 'edit', 'show']);
  Route::resource('kpi', KpiAppraisalController::class)->except(['create', 'edit', 'show']);
  Route::resource('resign', ResignOffboardingController::class)->except(['create', 'edit', 'show']);
  Route::resource('hrd-file', HrdFileController::class)->except(['create', 'edit', 'show']);

  // ── SALES ────────────────────────────────────────────────────────────────
  Route::get('crm-prospek/pdf', [CrmProspekController::class, 'pdf'])->name('crm-prospek.pdf');
  Route::resource('crm-prospek', CrmProspekController::class)->except(['create', 'edit']);

  Route::get('penawaran-sales/pdf', [PenawaranSalesController::class, 'pdf'])->name('penawaran-sales.pdf');
  Route::resource('penawaran-sales', PenawaranSalesController::class)->except(['create', 'edit']);

  Route::get('sales-order/pdf', [SalesOrderController::class, 'pdf'])->name('sales-order.pdf');
  Route::resource('sales-order', SalesOrderController::class)->except(['create', 'edit']);

  Route::get('pricelist-diskon/pdf', [PricelistDiskonController::class, 'pdf'])->name('pricelist-diskon.pdf');
  Route::resource('pricelist-diskon', PricelistDiskonController::class)->except(['create', 'edit']);

  Route::get('target-penjualan/pdf', [TargetPenjualanController::class, 'pdf'])->name('target-penjualan.pdf');
  Route::resource('target-penjualan', TargetPenjualanController::class)->except(['create', 'edit']);

  Route::get('komisi-sales/pdf', [KomisiSalesController::class, 'pdf'])->name('komisi-sales.pdf');
  Route::resource('komisi-sales', KomisiSalesController::class)->except(['create', 'edit']);

  Route::get('retur-penjualan/pdf', [ReturPenjualanController::class, 'pdf'])->name('retur-penjualan.pdf');
  Route::resource('retur-penjualan', ReturPenjualanController::class)->except(['create', 'edit']);

  Route::get('signature-dokumen/pdf', [SignatureDokumenController::class, 'pdf'])->name('signature-dokumen.pdf');
  Route::resource('signature-dokumen', SignatureDokumenController::class)->except(['create', 'edit']);

  // ── MARKETING ───────────────────────────────────────────────────────────
  Route::get('kampanye/pdf', [KampanyeController::class, 'pdf'])->name('kampanye.pdf');
  Route::resource('kampanye', KampanyeController::class)->except(['create', 'edit']);

  Route::get('otomatisasi/pdf', [OtomatisasiController::class, 'pdf'])->name('otomatisasi.pdf');
  Route::resource('otomatisasi', OtomatisasiController::class)->except(['create', 'edit', 'show']);

  Route::get('segmentasi/pdf', [SegmentasiController::class, 'pdf'])->name('segmentasi.pdf');
  Route::resource('segmentasi', SegmentasiController::class)->except(['create', 'edit', 'show']);

  Route::get('loyalty/pdf', [LoyaltyController::class, 'pdf'])->name('loyalty.pdf');
  Route::resource('loyalty', LoyaltyController::class)->except(['create', 'edit', 'show']);

  Route::get('afiliasi/pdf', [AfiliasiController::class, 'pdf'])->name('afiliasi.pdf');
  Route::resource('afiliasi', AfiliasiController::class)->except(['create', 'edit', 'show']);

  Route::get('sosmedp/pdf', [SosmedpController::class, 'pdf'])->name('sosmedp.pdf');
  Route::resource('sosmedp', SosmedpController::class)->except(['create', 'edit', 'show']);

  Route::get('trackingutm/pdf', [TrackingutmController::class, 'pdf'])->name('trackingutm.pdf');
  Route::resource('trackingutm', TrackingutmController::class)->except(['create', 'edit', 'show']);

  Route::get('adsintegration/pdf', [AdsIntegrationController::class, 'pdf'])->name('adsintegration.pdf');
  Route::resource('adsintegration', AdsIntegrationController::class)->except(['create', 'edit', 'show']);

  // ── PROJECT ─────────────────────────────────────────────────────────────
  Route::prefix('project')->name('project.')->group(function () {
    Route::get('induk-proyek/pdf', [IndukProyekController::class, 'pdf'])->name('induk-proyek.pdf');
    Route::resource('induk-proyek', IndukProyekController::class)->except(['create', 'edit']);

    Route::get('planning/pdf', [ProjectPlanningController::class, 'pdf'])->name('planning.pdf');
    Route::resource('planning', ProjectPlanningController::class)->except(['create', 'edit']);

    Route::get('timeline/pdf', [ProjectTimelineController::class, 'pdf'])->name('timeline.pdf');
    Route::resource('timeline', ProjectTimelineController::class)->except(['create', 'edit']);

    Route::get('cost/pdf', [ProjectCostController::class, 'pdf'])->name('cost.pdf');
    Route::resource('cost', ProjectCostController::class)->except(['create', 'edit']);

    Route::get('risk/pdf', [ProjectRiskController::class, 'pdf'])->name('risk.pdf');
    Route::resource('risk', ProjectRiskController::class)->except(['create', 'edit']);

    Route::get('dokumen/pdf', [DokumenProyekController::class, 'pdf'])->name('dokumen.pdf');
    Route::resource('dokumen', DokumenProyekController::class)->except(['create', 'edit']);

    Route::get('pembelian/pdf', [PembelianProyekController::class, 'pdf'])->name('pembelian.pdf');
    Route::resource('pembelian', PembelianProyekController::class)->except(['create', 'edit']);
  });
  // ── ASSET MANAGEMENT ──────────────────────────────────────────────────────
  Route::resource('asset/induk',        IndukAssetController::class,        ['as' => 'asset'])->except(['create', 'edit', 'show']);
  Route::resource('asset/pergerakan',   PergerakanAssetController::class,   ['as' => 'asset'])->except(['create', 'edit', 'show']);
  Route::resource('asset/pemeliharaan', PemeliharaanAssetController::class, ['as' => 'asset'])->except(['create', 'edit', 'show']);
  Route::resource('asset/penyusutan',   PenyusutanAssetController::class,   ['as' => 'asset'])->except(['create', 'edit', 'show']);
  Route::resource('asset/perolehan',    PerolehanAssetController::class,    ['as' => 'asset'])->except(['create', 'edit', 'show']);
  Route::resource('asset/dihapuskan',   AssetDihapuskanController::class,   ['as' => 'asset'])->except(['create', 'edit', 'show']);
  Route::resource('asset/dokumentasi',  DokumentasiAssetController::class,  ['as' => 'asset'])->except(['create', 'edit', 'show']);
  Route::resource('asset/pj',           PenanggungJawabController::class,   ['as' => 'asset'])->except(['create', 'edit', 'show']);
  Route::resource('asset/audit',        AuditAssetController::class,        ['as' => 'asset'])->except(['create', 'edit', 'show']);

  // ── CHART DATA (centralized AJAX endpoint) ──────────────────────────────
  Route::get('/chart-data/{page}', [\App\Http\Controllers\Admin\ChartDataController::class, 'getData'])
    ->name('chart-data');
});
