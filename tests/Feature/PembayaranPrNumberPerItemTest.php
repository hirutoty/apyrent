<?php

namespace Tests\Feature;

use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * End-to-end tests untuk PR Number Per Item feature di Pembayaran module.
 *
 * SKENARIO:
 *  #1  - Pembayaran baru dari PO (service_asuransi): item_pr_numbers terisi
 *  #2  - Partial approve (item 0 approved, item 1 rejected) → can resubmit
 *  #3  - Partial resubmit → PR baru dibuat dengan no_pr baru (increment)
 *  #4  - PR lama setelah resubmit hanya berisi item approved
 *  #5  - PR baru hanya berisi item rejected, item_pr_numbers = no_pr baru
 *  #6  - service_asuransi_id dipertahankan di PR baru
 *  #7  - service_incident_id dipertahankan di PR baru
 *  #8  - Full reject (semua item rejected) → PR yang sama direset (alur lama)
 *  #9  - GPS partial resubmit → PR baru dibuat
 *  #10 - no_pr PR baru adalah increment dari sequence terakhir (format PR-SVC-XXX)
 */
class PembayaranPrNumberPerItemTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($this->superadmin);
    }

    // =========================================================================
    // Helper: buat Pembayaran service_asuransi dengan 2 kejadian
    // =========================================================================
    private function makeServiceAsuransiPR(int $count = 2, string $noPr = null): Pembayaran
    {
        $kejadians = [];
        for ($i = 0; $i < $count; $i++) {
            $kejadians[] = [
                'nama_kejadian' => 'Kejadian-' . ($i + 1),
                'biaya'         => 300000 * ($i + 1),
                'lampiran'      => [],
            ];
        }

        $noPr = $noPr ?? 'PR-SAS-001';
        $itemPrNumbers = [];
        for ($i = 0; $i < $count; $i++) {
            $itemPrNumbers[$i] = $noPr;
        }

        return Pembayaran::create([
            'no_pr'           => $noPr,
            'tanggal'         => now()->toDateString(),
            'departemen'      => 'Test',
            'tipe_pembayaran' => 'service',
            'pemohon'         => 'User Test',
            'nominal'         => collect($kejadians)->sum('biaya'),
            'status'          => 'Diajukan',
            'source_type'     => 'service_asuransi',
            'source_data'     => [
                'kendaraan_id'        => 1,
                'kejadians'           => $kejadians,
                'item_pr_numbers'     => $itemPrNumbers,
                'service_asuransi_id' => 77,
                'nama_asuransi'       => 'Test Asuransi',
            ],
            'can_edit'        => false,
        ]);
    }

    // =========================================================================
    // Helper: buat Pembayaran service_part dengan 2 parts
    // =========================================================================
    private function makeServicePartPR(int $count = 2, string $noPr = null): Pembayaran
    {
        $parts = [];
        for ($i = 0; $i < $count; $i++) {
            $parts[] = [
                'nama_part'    => 'Part-' . ($i + 1),
                'biaya'        => 200000 * ($i + 1),
                'category_id'  => null,
                'nama_bank'    => 'BCA',
                'no_rekening'  => '12345' . $i,
                'nama_rekening'=> 'Vendor ' . ($i + 1),
            ];
        }

        $noPr = $noPr ?? 'PR-SVC-001';
        $itemPrNumbers = [];
        for ($i = 0; $i < $count; $i++) {
            $itemPrNumbers[$i] = $noPr;
        }

        return Pembayaran::create([
            'no_pr'           => $noPr,
            'tanggal'         => now()->toDateString(),
            'departemen'      => 'Test',
            'tipe_pembayaran' => 'service',
            'pemohon'         => 'User Test',
            'nominal'         => collect($parts)->sum('biaya'),
            'status'          => 'Diajukan',
            'source_type'     => 'service_part',
            'source_data'     => [
                'kendaraan_id'    => 1,
                'parts'           => $parts,
                'item_pr_numbers' => $itemPrNumbers,
                'tanggal_service' => now()->toDateString(),
                'kilometer'       => 10000,
            ],
            'can_edit'        => false,
        ]);
    }

    // =========================================================================
    // Helper: buat Pembayaran GPS dengan 2 items
    // =========================================================================
    private function makeGpsPR(int $count = 2, string $noPr = null): Pembayaran
    {
        $gpsItems = [];
        for ($i = 0; $i < $count; $i++) {
            $gpsItems[] = [
                'gps_id'       => $i + 1,
                'type'         => 'Type-' . ($i + 1),
                'biaya_sewa'   => 500000 * ($i + 1),
                'nama_bank'    => 'BNI',
                'no_rekening'  => '9876' . $i,
                'nama_pemilik' => 'GPS Vendor ' . ($i + 1),
            ];
        }

        $noPr = $noPr ?? 'PR-GPS-001';
        $itemPrNumbers = [];
        for ($i = 0; $i < $count; $i++) {
            $itemPrNumbers[$i] = $noPr;
        }

        return Pembayaran::create([
            'no_pr'           => $noPr,
            'tanggal'         => now()->toDateString(),
            'departemen'      => 'Test',
            'tipe_pembayaran' => 'service',
            'pemohon'         => 'User Test',
            'nominal'         => collect($gpsItems)->sum('biaya_sewa'),
            'status'          => 'Diajukan',
            'source_type'     => 'gps',
            'source_data'     => [
                'kendaraan_id'    => 1,
                'gps_items'       => $gpsItems,
                'item_pr_numbers' => $itemPrNumbers,
                'tanggal_bayar'   => now()->toDateString(),
                'tanggal_habis'   => now()->addYear()->toDateString(),
            ],
            'can_edit'        => false,
        ]);
    }

    // =========================================================================
    // Helper: simulate partial approval (set item_decisions)
    // =========================================================================
    private function simulatePartialApproval(Pembayaran $pr, array $approvedIdx, array $rejectedIdx): void
    {
        $sd = $pr->source_data;
        $itemDecisions = [];
        foreach ($approvedIdx as $idx) {
            $itemDecisions[] = ['idx' => $idx, 'action' => 'approved', 'catatan' => null];
        }
        foreach ($rejectedIdx as $idx) {
            $itemDecisions[] = ['idx' => $idx, 'action' => 'rejected', 'catatan' => 'Test rejection'];
        }
        $sd['item_decisions'] = $itemDecisions;

        $pr->update([
            'status'    => 'Disetujui',
            'can_edit'  => true,
            'source_data' => $sd,
        ]);
    }

    // =========================================================================
    // CEK #1 — Pembayaran service_asuransi baru memiliki item_pr_numbers
    // =========================================================================
    public function test_cek1_pembayaran_baru_item_pr_numbers_terisi(): void
    {
        $pr = $this->makeServiceAsuransiPR(2);
        $ipn = $pr->source_data['item_pr_numbers'] ?? [];

        $this->assertCount(2, $ipn, 'item_pr_numbers harus punya 2 entry');
        $this->assertEquals($pr->no_pr, $ipn[0]);
        $this->assertEquals($pr->no_pr, $ipn[1]);
    }

    // =========================================================================
    // CEK #2 — Partial approve → canBeEdited = true
    // =========================================================================
    public function test_cek2_partial_approve_pr_bisa_resubmit(): void
    {
        $pr = $this->makeServiceAsuransiPR(2);
        $this->simulatePartialApproval($pr, [0], [1]);
        $pr->refresh();

        $this->assertEquals('Disetujui', $pr->status);
        $this->assertTrue($pr->can_edit);
        $hasRejected = !empty(collect($pr->source_data['item_decisions'] ?? [])->where('action', 'rejected')->all());
        $this->assertTrue($hasRejected, 'Harus ada item rejected di item_decisions');
    }

    // =========================================================================
    // CEK #3 — Partial resubmit service_asuransi → PR baru dibuat
    // =========================================================================
    public function test_cek3_resubmit_partial_service_asuransi_membuat_pr_baru(): void
    {
        $pr = $this->makeServiceAsuransiPR(2);
        $this->simulatePartialApproval($pr, [0], [1]);

        $countBefore = Pembayaran::count();

        $controller = app(\App\Http\Controllers\Admin\PembayaranController::class);
        $request = Request::create('/x', 'POST', [
            '_token' => 'test',
            'items'  => [
                ['idx' => 1, 'biaya' => 350000, 'keterangan' => 'Updated keterangan'],
            ],
        ]);

        $response = $controller->resubmitRejectedItems($request, $pr);

        $this->assertEquals($countBefore + 1, Pembayaran::count(), 'Harus ada 1 PR baru');
    }

    // =========================================================================
    // CEK #4 — PR lama setelah resubmit hanya berisi item approved
    // =========================================================================
    public function test_cek4_pr_lama_hanya_berisi_item_approved(): void
    {
        $pr = $this->makeServiceAsuransiPR(2);
        $this->simulatePartialApproval($pr, [0], [1]);

        $controller = app(\App\Http\Controllers\Admin\PembayaranController::class);
        $request = Request::create('/x', 'POST', [
            '_token' => 'test',
            'items'  => [['idx' => 1, 'biaya' => 350000]],
        ]);
        $controller->resubmitRejectedItems($request, $pr);

        $pr->refresh();
        $kejadians = $pr->source_data['kejadians'] ?? [];

        $this->assertCount(1, $kejadians, 'PR lama harus hanya punya 1 kejadian (yang approved)');
        $this->assertEquals('Kejadian-1', $kejadians[0]['nama_kejadian'] ?? null);
        $this->assertEquals('Disetujui', $pr->status, 'PR lama tetap Disetujui');
        $this->assertFalse($pr->can_edit, 'PR lama tidak bisa di-edit lagi');
    }

    // =========================================================================
    // CEK #5 — PR baru berisi item rejected, item_pr_numbers = no_pr baru
    // =========================================================================
    public function test_cek5_pr_baru_berisi_item_rejected_dengan_pr_number_baru(): void
    {
        $pr = $this->makeServiceAsuransiPR(2);
        $this->simulatePartialApproval($pr, [0], [1]);

        $controller = app(\App\Http\Controllers\Admin\PembayaranController::class);
        $request = Request::create('/x', 'POST', [
            '_token' => 'test',
            'items'  => [['idx' => 1, 'biaya' => 350000]],
        ]);
        $controller->resubmitRejectedItems($request, $pr);

        $newPr = Pembayaran::where('id', '!=', $pr->id)
            ->where('source_type', 'service_asuransi')
            ->latest()->first();

        $this->assertNotNull($newPr, 'PR baru harus ada');
        $this->assertNotEquals($pr->no_pr, $newPr->no_pr, 'PR baru punya no_pr berbeda');
        $this->assertEquals('Diajukan', $newPr->status);

        $newKejadians = $newPr->source_data['kejadians'] ?? [];
        $this->assertCount(1, $newKejadians, 'PR baru berisi 1 kejadian');
        $this->assertEquals('Kejadian-2', $newKejadians[0]['nama_kejadian'] ?? null);
        $this->assertEquals(350000, $newKejadians[0]['biaya'] ?? 0, 'Biaya diupdate dari form');

        $newPrNumbers = $newPr->source_data['item_pr_numbers'] ?? [];
        $this->assertCount(1, $newPrNumbers);
        $this->assertEquals($newPr->no_pr, $newPrNumbers[0]);
    }

    // =========================================================================
    // CEK #6 — service_asuransi_id dipertahankan di PR baru
    // =========================================================================
    public function test_cek6_service_asuransi_id_preserved_in_new_pr(): void
    {
        $pr = $this->makeServiceAsuransiPR(2);
        $this->simulatePartialApproval($pr, [0], [1]);

        $controller = app(\App\Http\Controllers\Admin\PembayaranController::class);
        $request = Request::create('/x', 'POST', [
            '_token' => 'test',
            'items'  => [['idx' => 1, 'biaya' => 300000]],
        ]);
        $controller->resubmitRejectedItems($request, $pr);

        $newPr = Pembayaran::where('id', '!=', $pr->id)->where('source_type', 'service_asuransi')->latest()->first();
        $this->assertEquals(77, $newPr->source_data['service_asuransi_id'] ?? null,
            'service_asuransi_id harus dipertahankan di PR baru');
    }

    // =========================================================================
    // CEK #7 — service_incident_id dipertahankan di PR baru
    // =========================================================================
    public function test_cek7_service_incident_id_preserved_in_new_pr(): void
    {
        $parts = [
            ['nama_part' => 'Part-A', 'biaya' => 100000, 'category_id' => null],
            ['nama_part' => 'Part-B', 'biaya' => 200000, 'category_id' => null],
        ];
        $ipn = [0 => 'PR-SIN-001', 1 => 'PR-SIN-001'];

        $pr = Pembayaran::create([
            'no_pr'           => 'PR-SIN-001',
            'tanggal'         => now()->toDateString(),
            'departemen'      => 'Test',
            'tipe_pembayaran' => 'service',
            'pemohon'         => 'User Test',
            'nominal'         => 300000,
            'status'          => 'Diajukan',
            'source_type'     => 'service_incident',
            'source_data'     => [
                'kendaraan_id'        => 1,
                'parts'               => $parts,
                'item_pr_numbers'     => $ipn,
                'service_incident_id' => 55,
                'tanggal_service'     => now()->toDateString(),
                'kilometer'           => 8000,
            ],
            'can_edit' => false,
        ]);
        $this->simulatePartialApproval($pr, [0], [1]);

        $controller = app(\App\Http\Controllers\Admin\PembayaranController::class);
        $request = Request::create('/x', 'POST', [
            '_token' => 'test',
            'items'  => [['idx' => 1, 'biaya' => 220000, 'nama_bank' => 'BCA', 'no_rekening' => '123', 'nama_rekening' => 'Vendor']],
        ]);
        $controller->resubmitRejectedItems($request, $pr);

        $newPr = Pembayaran::where('id', '!=', $pr->id)->where('source_type', 'service_incident')->latest()->first();
        $this->assertNotNull($newPr);
        $this->assertEquals(55, $newPr->source_data['service_incident_id'] ?? null,
            'service_incident_id harus dipertahankan di PR baru');
    }

    // =========================================================================
    // CEK #8 — Full reject → PR yang sama direset (alur lama, tidak buat PR baru)
    // =========================================================================
    public function test_cek8_full_reject_tidak_membuat_pr_baru(): void
    {
        $pr = $this->makeServiceAsuransiPR(2);
        // Full reject: status Ditolak, semua item rejected (tidak ada approved)
        $pr->update(['status' => 'Ditolak', 'can_edit' => true]);
        // Tidak ada item_decisions → dianggap semua rejected

        $countBefore = Pembayaran::count();

        $controller = app(\App\Http\Controllers\Admin\PembayaranController::class);
        $request = Request::create('/x', 'POST', [
            '_token' => 'test',
            'items'  => [
                ['idx' => 0, 'biaya' => 300000],
                ['idx' => 1, 'biaya' => 600000],
            ],
        ]);
        $controller->resubmitRejectedItems($request, $pr);

        $this->assertEquals($countBefore, Pembayaran::count(), 'Full reject tidak membuat PR baru');

        $pr->refresh();
        $this->assertEquals('Diajukan', $pr->status, 'PR sama direset ke Diajukan');
    }

    // =========================================================================
    // CEK #9 — GPS partial resubmit → PR baru dibuat
    // =========================================================================
    public function test_cek9_gps_partial_resubmit_membuat_pr_baru(): void
    {
        $pr = $this->makeGpsPR(2);
        $this->simulatePartialApproval($pr, [0], [1]);

        $countBefore = Pembayaran::count();

        $controller = app(\App\Http\Controllers\Admin\PembayaranController::class);
        $request = Request::create('/x', 'POST', [
            '_token' => 'test',
            'items'  => [['idx' => 1, 'biaya' => 600000, 'nama_bank' => 'BCA', 'no_rekening' => '9876', 'nama_pemilik' => 'Vendor GPS 2']],
        ]);
        $controller->resubmitRejectedItems($request, $pr);

        $this->assertEquals($countBefore + 1, Pembayaran::count(), 'GPS partial resubmit harus buat 1 PR baru');

        $newPr = Pembayaran::where('id', '!=', $pr->id)->where('source_type', 'gps')->latest()->first();
        $this->assertNotNull($newPr);
        $newGpsItems = $newPr->source_data['gps_items'] ?? [];
        $this->assertCount(1, $newGpsItems, 'PR baru hanya berisi 1 GPS item');
        $this->assertEquals(600000, $newGpsItems[0]['biaya_sewa'] ?? 0);
    }

    // =========================================================================
    // CEK #10 — no_pr PR baru increment dari sequence terakhir (format PR-SAS-XXX)
    // =========================================================================
    public function test_cek10_no_pr_baru_adalah_increment(): void
    {
        $pr = $this->makeServiceAsuransiPR(2, 'PR-SAS-001');
        $this->simulatePartialApproval($pr, [0], [1]);

        $controller = app(\App\Http\Controllers\Admin\PembayaranController::class);
        $request = Request::create('/x', 'POST', [
            '_token' => 'test',
            'items'  => [['idx' => 1, 'biaya' => 300000]],
        ]);
        $controller->resubmitRejectedItems($request, $pr);

        $newPr = Pembayaran::where('id', '!=', $pr->id)->where('source_type', 'service_asuransi')->latest()->first();
        $this->assertNotNull($newPr);
        $this->assertEquals('PR-SAS-002', $newPr->no_pr,
            "PR baru harus PR-SAS-002, dapat: {$newPr->no_pr}");
    }
}
