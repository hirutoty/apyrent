<?php

namespace Tests\Feature;

use App\Models\Bukubesar;
use App\Models\Keuangan;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Verifikasi Sistem Pengeluaran — 7 Checklist
 *
 * CEK #1  - sumber = 'manual' saat input pengeluaran manual
 * CEK #1b - saldo dihitung benar setelah pengeluaran manual
 * CEK #2  - kolom sumber = 'auto' bisa tersimpan via Eloquent (fillable fix)
 * CEK #3  - tidak ada metode '-' atau 'auto' di entri baru
 * CEK #4  - saldo Buku Besar HutangVendor BERKURANG saat lunas
 * CEK #5  - hapus keuangan manual → saldo baris berikutnya recalculate
 * CEK #6  - PurchaseOrder Closed → jurnal keuangan & buku besar terbuat
 * CEK #7  - PurchaseOrder reopen → jurnal terhapus & saldo recalculate
 */
class PengeluaranVerifikasiTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($this->user);
    }

    // =========================================================================
    // CEK #1 — sumber = 'manual' saat input pengeluaran manual
    // =========================================================================

    public function test_cek1_pengeluaran_manual_menyimpan_sumber_manual(): void
    {
        Keuangan::create([
            'tanggal'     => now()->toDateString(),
            'reference'   => 'INIT-0',
            'user_id'     => $this->user->id,
            'kategori'    => 'Pemasukan',
            'metode'      => 'Cash',
            'keterangan'  => 'Saldo awal',
            'pemasukan'   => 1_000_000,
            'pengeluaran' => 0,
            'saldo'       => 1_000_000,
            'sumber'      => 'manual',
        ]);

        $response = $this->post(route('keuangan.store'), [
            'jenis'      => 'Pengeluaran',
            'kategori'   => 'Beban',
            'metode'     => 'Cash',
            'nominal'    => 200_000,
            'keterangan' => 'Test pengeluaran manual',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('keuangans', [
            'pengeluaran' => 200_000,
            'sumber'      => 'manual',   // ← kunci
        ]);
    }

    // =========================================================================
    // CEK #1b — saldo dihitung benar setelah pengeluaran manual
    // =========================================================================

    public function test_cek1b_pengeluaran_manual_saldo_benar(): void
    {
        Keuangan::create([
            'tanggal'     => now()->toDateString(),
            'reference'   => 'INIT-0',
            'user_id'     => $this->user->id,
            'kategori'    => 'Pemasukan',
            'metode'      => 'Cash',
            'keterangan'  => 'Saldo awal',
            'pemasukan'   => 1_000_000,
            'pengeluaran' => 0,
            'saldo'       => 1_000_000,
            'sumber'      => 'manual',
        ]);

        $this->post(route('keuangan.store'), [
            'jenis'      => 'Pengeluaran',
            'kategori'   => 'Beban',
            'metode'     => 'Cash',
            'nominal'    => 300_000,
            'keterangan' => 'Test saldo',
        ]);

        $baru = Keuangan::latest('id')->first();

        $this->assertEquals(700_000, (int) $baru->saldo,
            "Saldo seharusnya 700.000 (1.000.000 - 300.000), actual: {$baru->saldo}"
        );
        $this->assertEquals(300_000, (int) $baru->pengeluaran);
        $this->assertEquals(0, (int) $baru->pemasukan);
    }

    // =========================================================================
    // CEK #2 — kolom sumber = 'auto' bisa tersimpan via Eloquent (fillable fix)
    // =========================================================================

    public function test_cek2_kolom_sumber_auto_tersimpan_via_model(): void
    {
        $keuangan = Keuangan::create([
            'tanggal'     => now()->toDateString(),
            'reference'   => 'STNK-99-' . now()->timestamp,
            'user_id'     => $this->user->id,
            'kategori'    => 'Pengeluaran',
            'metode'      => 'Cash',
            'keterangan'  => 'Perpanjangan STNK test',
            'pemasukan'   => 0,
            'pengeluaran' => 500_000,
            'saldo'       => 0,
            'sumber'      => 'auto',
        ]);

        $fresh = Keuangan::find($keuangan->id);

        $this->assertSame('auto', $fresh->sumber,
            "BUG: kolom sumber = null — 'sumber' belum ada di \$fillable Keuangan model"
        );
    }

    // =========================================================================
    // CEK #3 — tidak ada metode '-' atau 'auto' di entri baru
    // =========================================================================

    public function test_cek3_tidak_ada_metode_invalid(): void
    {
        $autoPostings = [
            ['reference' => 'STNK-1', 'metode' => 'Cash', 'keterangan' => 'Perpanjangan STNK'],
            ['reference' => 'KIR-1',  'metode' => 'Cash', 'keterangan' => 'Pembayaran KIR'],
            ['reference' => 'GPS-1',  'metode' => 'Cash', 'keterangan' => 'Perpanjangan GPS'],
            ['reference' => 'Asuransi-1', 'metode' => 'Cash', 'keterangan' => 'Asuransi kendaraan'],
        ];

        foreach ($autoPostings as $data) {
            Keuangan::create([
                'tanggal'     => now()->toDateString(),
                'reference'   => $data['reference'],
                'user_id'     => $this->user->id,
                'kategori'    => 'Pengeluaran',
                'metode'      => $data['metode'],
                'keterangan'  => $data['keterangan'],
                'pemasukan'   => 0,
                'pengeluaran' => 100_000,
                'saldo'       => 0,
                'sumber'      => 'auto',
            ]);
        }

        $invalid = Keuangan::whereIn('metode', ['-', 'auto'])->count();

        $this->assertEquals(0, $invalid,
            "Masih ada {$invalid} entri keuangan dengan metode tidak valid ('-' atau 'auto')"
        );
    }

    // =========================================================================
    // CEK #4 — Saldo Buku Besar HutangVendor BERKURANG saat lunas
    // =========================================================================

    public function test_cek4_saldo_bukubesar_hutang_vendor_berkurang(): void
    {
        // Seed saldo awal keuangan
        Keuangan::create([
            'tanggal'     => now()->toDateString(),
            'reference'   => 'INIT-KEU',
            'user_id'     => $this->user->id,
            'kategori'    => 'Pemasukan',
            'metode'      => 'Cash',
            'keterangan'  => 'Saldo awal',
            'pemasukan'   => 5_000_000,
            'pengeluaran' => 0,
            'saldo'       => 5_000_000,
            'sumber'      => 'manual',
        ]);

        // Seed saldo awal buku besar
        Bukubesar::create([
            'kode_jurnal' => 'INIT-BB',
            'transaksi'   => 'Saldo awal',
            'kategori'    => 'Aktiva',
            'tanggal'     => now()->toDateString(),
            'debit'       => 0,
            'kredit'      => 5_000_000,
            'saldo'       => 5_000_000,
            'aktivitas'   => 'Operasi',
            'keterangan'  => 'Saldo awal BB',
        ]);

        $response = $this->post(route('hutang-vendor.store'), [
            'nama_vendor' => 'Vendor Test',
            'kategori'    => 'Operasional',
            'nominal'     => 500_000,
            'dibayar'     => 500_000,
            'jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status'      => 'lunas',
            'keterangan'  => 'Test verifikasi CEK #4',
        ]);

        $response->assertRedirect();

        $jurnal = Bukubesar::where('kode_jurnal', 'like', 'HTG-%')->latest('id')->first();

        $this->assertNotNull($jurnal, 'Jurnal HTG tidak terbuat di bukubesars');
        $this->assertEquals(500_000, (int) $jurnal->debit);
        $this->assertEquals(0, (int) $jurnal->kredit);

        // ★ KUNCI: saldo harus BERKURANG (5.000.000 - 500.000 = 4.500.000)
        $this->assertEquals(4_500_000, (int) $jurnal->saldo,
            "BUG: Saldo seharusnya 4.500.000 (berkurang), actual: {$jurnal->saldo}. " .
            "Kemungkinan masih pakai + bukan -"
        );
    }

    // =========================================================================
    // CEK #5 — Hapus keuangan manual → saldo baris berikutnya recalculate
    // =========================================================================

    public function test_cek5_hapus_keuangan_recalculate_saldo(): void
    {
        // Baris 1: saldo 900.000 (asumsi saldo awal 1.000.000, keluar 100.000)
        $baris1 = Keuangan::create([
            'tanggal'     => now()->toDateString(),
            'reference'   => 'EXP-TEST-1',
            'user_id'     => $this->user->id,
            'kategori'    => 'Beban',
            'metode'      => 'Cash',
            'keterangan'  => 'Pengeluaran 1',
            'pemasukan'   => 0,
            'pengeluaran' => 100_000,
            'saldo'       => 900_000,
            'sumber'      => 'manual',
        ]);

        // Baris 2: saldo 700.000 (900.000 - 200.000)
        $baris2 = Keuangan::create([
            'tanggal'     => now()->toDateString(),
            'reference'   => 'EXP-TEST-2',
            'user_id'     => $this->user->id,
            'kategori'    => 'Beban',
            'metode'      => 'Cash',
            'keterangan'  => 'Pengeluaran 2',
            'pemasukan'   => 0,
            'pengeluaran' => 200_000,
            'saldo'       => 700_000,
            'sumber'      => 'manual',
        ]);

        // Baris 3: saldo 400.000 (700.000 - 300.000)
        $baris3 = Keuangan::create([
            'tanggal'     => now()->toDateString(),
            'reference'   => 'EXP-TEST-3',
            'user_id'     => $this->user->id,
            'kategori'    => 'Beban',
            'metode'      => 'Cash',
            'keterangan'  => 'Pengeluaran 3',
            'pemasukan'   => 0,
            'pengeluaran' => 300_000,
            'saldo'       => 400_000,
            'sumber'      => 'manual',
        ]);

        // Hapus baris ke-2 via endpoint
        $this->delete(route('keuangan.destroy', $baris2->id))->assertRedirect();

        // Baris 2 harus terhapus
        $this->assertDatabaseMissing('keuangans', ['id' => $baris2->id]);

        // ★ KUNCI: saldo baris-3 harus recalculate
        // Setelah baris-2 (200.000) dihapus:
        // saldo = saldo baris-1 (900.000) - pengeluaran baris-3 (300.000) = 600.000
        $saldoBaris3 = (int) Keuangan::find($baris3->id)->saldo;

        $this->assertEquals(600_000, $saldoBaris3,
            "BUG: Saldo baris-3 seharusnya 600.000 setelah baris-2 dihapus, actual: {$saldoBaris3}. " .
            "recalculateKeuanganSaldo() tidak dipanggil di destroy()"
        );
    }

    // =========================================================================
    // CEK #6 — PurchaseOrder Closed → jurnal keuangan & buku besar terbuat
    // =========================================================================

    public function test_cek6_purchase_order_closed_membuat_jurnal(): void
    {
        // Seed saldo awal
        Keuangan::create([
            'tanggal'     => now()->toDateString(),
            'reference'   => 'INIT-KEU',
            'user_id'     => $this->user->id,
            'kategori'    => 'Pemasukan',
            'metode'      => 'Cash',
            'keterangan'  => 'Saldo awal',
            'pemasukan'   => 10_000_000,
            'pengeluaran' => 0,
            'saldo'       => 10_000_000,
            'sumber'      => 'manual',
        ]);

        Bukubesar::create([
            'kode_jurnal' => 'INIT-BB',
            'transaksi'   => 'Saldo awal BB',
            'kategori'    => 'Aktiva',
            'tanggal'     => now()->toDateString(),
            'debit'       => 0,
            'kredit'      => 10_000_000,
            'saldo'       => 10_000_000,
            'aktivitas'   => 'Operasi',
            'keterangan'  => 'Saldo awal',
        ]);

        // Buat PO Pending
        $po = PurchaseOrder::create([
            'tanggal_po'   => now()->toDateString(),
            'vendor'       => 'Vendor ABC',
            'total_barang' => 5,
            'total_harga'  => 2_000_000,
            'status_po'    => 'Pending',
        ]);

        // Panggil controller langsung (bypass HTTP + RefreshDatabase isolation)
        $controller = new \App\Http\Controllers\Admin\PurchaseOrderController();
        $request    = \Illuminate\Http\Request::create(
            route('purchase-order.update', $po),
            'PUT',
            [
                'tanggal_po'   => now()->toDateString(),
                'vendor'       => 'Vendor ABC',
                'total_barang' => 5,
                'total_harga'  => 2_000_000,
                'status_po'    => 'Closed',
            ]
        );
        $request->setUserResolver(fn () => $this->user);

        $controller->update($request, $po);

        $kodeJurnal = 'PO-' . $po->id;

        // ★ Harus ada di keuangans
        $this->assertDatabaseHas('keuangans', [
            'reference'   => $kodeJurnal,
            'pengeluaran' => 2_000_000,
            'sumber'      => 'auto',
        ]);

        // ★ Harus ada di bukubesars
        $this->assertDatabaseHas('bukubesars', [
            'kode_jurnal' => $kodeJurnal,
            'debit'       => 2_000_000,
        ]);

        // ★ Saldo keuangan harus berkurang
        $jurnal = Keuangan::where('reference', $kodeJurnal)->first();
        $this->assertEquals(8_000_000, (int) $jurnal->saldo,
            "Saldo seharusnya 8.000.000 (10.000.000 - 2.000.000), actual: {$jurnal->saldo}"
        );
    }

    // =========================================================================
    // CEK #7 — PurchaseOrder reopen dari Closed → jurnal terhapus & recalculate
    // =========================================================================

    public function test_cek7_purchase_order_reopen_menghapus_jurnal(): void
    {
        // Seed saldo awal
        Keuangan::create([
            'tanggal'     => now()->toDateString(),
            'reference'   => 'INIT-KEU',
            'user_id'     => $this->user->id,
            'kategori'    => 'Pemasukan',
            'metode'      => 'Cash',
            'keterangan'  => 'Saldo awal',
            'pemasukan'   => 10_000_000,
            'pengeluaran' => 0,
            'saldo'       => 10_000_000,
            'sumber'      => 'manual',
        ]);

        Bukubesar::create([
            'kode_jurnal' => 'INIT-BB',
            'transaksi'   => 'Saldo awal BB',
            'kategori'    => 'Aktiva',
            'tanggal'     => now()->toDateString(),
            'debit'       => 0,
            'kredit'      => 10_000_000,
            'saldo'       => 10_000_000,
            'aktivitas'   => 'Operasi',
            'keterangan'  => 'Saldo awal',
        ]);

        $po = PurchaseOrder::create([
            'tanggal_po'   => now()->toDateString(),
            'vendor'       => 'Vendor XYZ',
            'total_barang' => 3,
            'total_harga'  => 1_500_000,
            'status_po'    => 'Pending',
        ]);

        $controller = new \App\Http\Controllers\Admin\PurchaseOrderController();

        // Close dulu
        $reqClose = \Illuminate\Http\Request::create(
            route('purchase-order.update', $po), 'PUT',
            ['tanggal_po' => now()->toDateString(), 'vendor' => 'Vendor XYZ',
             'total_barang' => 3, 'total_harga' => 1_500_000, 'status_po' => 'Closed']
        );
        $reqClose->setUserResolver(fn () => $this->user);
        $controller->update($reqClose, $po);

        $kodeJurnal = 'PO-' . $po->id;
        $this->assertDatabaseHas('keuangans', ['reference' => $kodeJurnal]);

        // Refresh PO supaya status_po ter-update
        $po->refresh();

        // Reopen ke Approved
        $reqReopen = \Illuminate\Http\Request::create(
            route('purchase-order.update', $po), 'PUT',
            ['tanggal_po' => now()->toDateString(), 'vendor' => 'Vendor XYZ',
             'total_barang' => 3, 'total_harga' => 1_500_000, 'status_po' => 'Approved']
        );
        $reqReopen->setUserResolver(fn () => $this->user);
        $controller->update($reqReopen, $po);

        // ★ Jurnal harus TERHAPUS
        $this->assertDatabaseMissing('keuangans', ['reference' => $kodeJurnal]);
        $this->assertDatabaseMissing('bukubesars', ['kode_jurnal' => $kodeJurnal]);

        // ★ Saldo keuangan kembali ke 10.000.000
        $saldoAkhir = (int) Keuangan::latest('id')->value('saldo');
        $this->assertEquals(10_000_000, $saldoAkhir,
            "Saldo seharusnya kembali ke 10.000.000 setelah PO di-reopen, actual: {$saldoAkhir}"
        );
    }
}
