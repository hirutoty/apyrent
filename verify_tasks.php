<?php
$base = __DIR__;
$ok = 0; $fail = 0;

function chk(string $label, bool $result): void {
    global $ok, $fail;
    echo ($result ? '[OK]  ' : '[FAIL] ') . $label . PHP_EOL;
    $result ? $ok++ : $fail++;
}

// T1
$c = file_get_contents($base . '/app/Http/Controllers/Admin/InvKontrakController.php');
chk('T1: Filter penawaran approved di dropdown kontrak', strpos($c, "where('status', 'approved')") !== false);

// T2
chk('T2: Auto-create Rental saat kontrak disimpan', strpos($c, 'Rental::create') !== false);

// T3
$cv = file_get_contents($base . '/resources/views/admin/kontrak/index.blade.php');
chk('T3: Kolom kendaraan di tabel kontrak', strpos($cv, 'col-kendaraan') !== false);
chk('T3: Badge showReminder&&isSoon', strpos($cv, 'showReminder && $k->isSoon') !== false);

// T4
$ci = file_get_contents($base . '/app/Http/Controllers/Admin/InvoicesController.php');
chk('T4: Auto-create InvSummary saat invoice disimpan', strpos($ci, 'InvSummary::firstOrCreate') !== false);
$cs = file_get_contents($base . '/resources/views/admin/summary/index.blade.php');
chk('T4: Tombol Tambah Summary dihapus dari view', strpos($cs, 'openModalTambah') === false);

// T5
$vs = file_get_contents($base . '/resources/views/admin/invoice/show.blade.php');
chk('T5: Section Riwayat Pembayaran di invoice/show', strpos($vs, 'Riwayat Pembayaran') !== false);
chk('T5: Modal Tambah Pembayaran di invoice/show', strpos($vs, 'modalBayar') !== false);
chk('T5: Route invoices.payments.store di form', strpos($vs, 'invoices.payments.store') !== false);
chk('T5: Tampilkan $payments, $totalPaid, $remaining di view', strpos($vs, '$totalPaid') !== false);

// T6
$vp = file_get_contents($base . '/resources/views/admin/payments/index.blade.php');
chk('T6: Modal Tambah dihapus dari payments/index', strpos($vp, 'id="modalTambah"') === false);
chk('T6: Tombol "Tambah Pembayaran" dihapus dari header', strpos($vp, 'openModalTambah()') === false);
chk('T6: Modal Edit masih ada (untuk koreksi)', strpos($vp, 'id="modalEdit"') !== false);

// T7
$vh = file_get_contents($base . '/resources/views/admin/history/show.blade.php');
chk('T7: Badge CICILAN/DP di history/show', strpos($vh, 'CICILAN / DP') !== false);
chk('T7: Badge BELUM LUNAS tetap ada', strpos($vh, 'BELUM LUNAS') !== false);

// T8
$vri = file_get_contents($base . '/resources/views/admin/rental/index.blade.php');
chk('T8: Tombol invoice-pdf di rental/index (kolom aksi)', strpos($vri, 'rental.invoice-pdf') !== false);
$vrs = file_get_contents($base . '/resources/views/admin/rental/show.blade.php');
chk('T8: Tombol Cetak Invoice PDF di rental/show header', strpos($vrs, 'rental.invoice-pdf') !== false);
$cr = file_get_contents($base . '/app/Http/Controllers/Admin/RentalController.php');
chk('T8: Method invoicePdf di RentalController', strpos($cr, 'function invoicePdf') !== false);
chk('T8: Delegate ke InvoicesController@print', strpos($cr, 'InvoicesController') !== false && strpos($cr, '->print(') !== false);

echo PHP_EOL;
echo "TOTAL: {$ok} OK, {$fail} FAIL" . PHP_EOL;
if ($fail === 0) echo "Semua task PASSED!" . PHP_EOL;
