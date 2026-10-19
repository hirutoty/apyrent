<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\PurchaseOrderApprovalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * End-to-end tests untuk PO Number Per Item feature.
 *
 * SKENARIO:
 *  #1  - PO baru dibuat → item_po_numbers terisi, semua item = po_id dokumen
 *  #2  - Partial approve GPS (item 0 approved, item 1 rejected) → can resubmit
 *  #3  - Resubmit partial GPS → PO baru dibuat dengan po_id baru (increment)
 *  #4  - PO lama hanya berisi item approved, item_po_numbers di-update
 *  #5  - PO baru hanya berisi item rejected, item_po_numbers = po_id PO baru
 *  #6  - Service Part partial approve + resubmit → PO baru dibuat
 *  #7  - Service Asuransi partial approve + resubmit → PO baru dibuat, service_asuransi_id dipertahankan
 *  #8  - Service Incident partial approve + resubmit → PO baru dibuat, service_incident_id dipertahankan
 *  #9  - Full reject (semua item ditolak) → PO yang sama direset ke Pending (alur lama)
 *  #10 - Single-item types (Pajak) resubmit → PO yang sama direset (tidak membuat PO baru)
 *  #11 - PO baru hasil resubmit tidak memiliki locked_approved_idx (fresh cycle)
 *  #12 - po_id PO baru adalah increment dari sequence terakhir di tahun berjalan
 */
class PurchaseOrderPoNumberPerItemTest extends TestCase
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
    // Helper: membuat PO dengan source_data GPS items
    // =========================================================================
    private function makeGpsPO(int $itemCount = 2, string $poId = null): PurchaseOrder
    {
        $gpsItems = [];
        for ($i = 0; $i < $itemCount; $i++) {
            $gpsItems[] = [
                'gps_id'       => $i + 1,
                'type'         => 'Type-' . ($i + 1),
                'biaya_sewa'   => 500000 * ($i + 1),
                'nama_bank'    => 'BCA',
                'no_rekening'  => '1234567' . $i,
                'nama_pemilik' => 'Vendor GPS ' . ($i + 1),
            ];
        }

        $year = now()->year;
        $sourceData = [
            'kendaraan_id' => 1,
            'gps_items'    => $gpsItems,
            'tanggal_bayar'=> now()->toDateString(),
            'tanggal_habis'=> now()->addYear()->toDateString(),
            'keterangan'   => 'Test GPS PO',
        ];

        // Build item_po_numbers
        $itemPoNumbers = [];
        for ($i = 0; $i < $itemCount; $i++) {
            $itemPoNumbers[$i] = $poId ?? "PO-{$year}-001";
        }
        $sourceData['item_po_numbers'] = $itemPoNumbers;

        return PurchaseOrder::create([
            'po_id'             => $poId ?? "PO-{$year}-001",
            'tanggal_po'        => now()->toDateString(),
            'vendor'            => 'Test Vendor',
            'total_barang'      => $itemCount,
            'total_harga'       => collect($gpsItems)->sum('biaya_sewa'),
            'status_po'         => 'Pending',
            'source_type'       => 'gps',
            'source_data'       => $sourceData,
            'status'            => 'Pending',
            'can_edit'          => false,
            'terakhir_diajukan' => now(),
        ]);
    }

    // =========================================================================
    // Helper: membuat PO dengan source_data Service Part items
    // =========================================================================
    private function makeServicePartPO(int $partCount = 2, string $poId = null): PurchaseOrder
    {
        $year  = now()->year;
        $parts = [];
        for ($i = 0; $i < $partCount; $i++) {
            $parts[] = [
                'nama_part'   => 'Part-' . ($i + 1),
                'category_id' => null,
                'biaya'       => 200000 * ($i + 1),
                'nama_bank'   => 'BNI',
                'no_rekening' => '9876' . $i,
                'nama_rekening' => 'Supplier Part ' . ($i + 1),
            ];
        }

        $sourceData = [
            'kendaraan_id'   => 1,
            'parts'          => $parts,
            'tanggal_service'=> now()->toDateString(),
            'kilometer'      => 10000,
            'keluhan'        => 'Test keluhan',
        ];

        $itemPoNumbers = [];
        for ($i = 0; $i < $partCount; $i++) {
            $itemPoNumbers[$i] = $poId ?? "PO-{$year}-001";
        }
        $sourceData['item_po_numbers'] = $itemPoNumbers;

        return PurchaseOrder::create([
            'po_id'             => $poId ?? "PO-{$year}-001",
            'tanggal_po'        => now()->toDateString(),
            'vendor'            => 'Test Vendor',
            'total_barang'      => $partCount,
            'total_harga'       => collect($parts)->sum('biaya'),
            'status_po'         => 'Pending',
            'source_type'       => 'service_part',
            'source_data'       => $sourceData,
            'status'            => 'Pending',
            'can_edit'          => false,
            'terakhir_diajukan' => now(),
        ]);
    }

    // =========================================================================
    // Helper: membuat PO dengan source_data Service Asuransi
    // =========================================================================
    private function makeServiceAsuransiPO(int $count = 2, string $poId = null): PurchaseOrder
    {
        $year      = now()->year;
        $kejadians = [];
        for ($i = 0; $i < $count; $i++) {
            $kejadians[] = [
                'nama_kejadian' => 'Kejadian-' . ($i + 1),
                'biaya'         => 300000 * ($i + 1),
            ];
        }

        $sourceData = [
            'kendaraan_id'     => 1,
            'kejadians'        => $kejadians,
            'nama_asuransi'    => 'Asuransi Test',
            'tanggal_service'  => now()->toDateString(),
            'service_asuransi_id' => 99, // simulated existing record
        ];

        $itemPoNumbers = [];
        for ($i = 0; $i < $count; $i++) {
            $itemPoNumbers[$i] = $poId ?? "PO-{$year}-001";
        }
        $sourceData['item_po_numbers'] = $itemPoNumbers;

        return PurchaseOrder::create([
            'po_id'             => $poId ?? "PO-{$year}-001",
            'tanggal_po'        => now()->toDateString(),
            'vendor'            => 'Test Vendor',
            'total_barang'      => $count,
            'total_harga'       => collect($kejadians)->sum('biaya'),
            'status_po'         => 'Pending',
            'source_type'       => 'service_asuransi',
            'source_data'       => $sourceData,
            'status'            => 'Pending',
            'can_edit'          => false,
            'terakhir_diajukan' => now(),
        ]);
    }

    // =========================================================================
    // Helper: simulate partial approval (set item_decisions + locked_approved_idx)
    // =========================================================================
    private function simulatePartialApproval(PurchaseOrder $po, array $approvedIdx, array $rejectedIdx): void
    {
        $sourceData    = $po->source_data;
        $itemDecisions = [];

        foreach ($approvedIdx as $idx) {
            $itemDecisions[] = ['idx' => $idx, 'action' => 'approved', 'catatan' => null];
        }
        foreach ($rejectedIdx as $idx) {
            $itemDecisions[] = ['idx' => $idx, 'action' => 'rejected', 'catatan' => 'Test rejection'];
        }

        $sourceData['item_decisions']      = $itemDecisions;
        $sourceData['locked_approved_idx'] = $approvedIdx;

        $po->update([
            'status'      => 'Disetujui',
            'can_edit'    => true,
            'source_data' => $sourceData,
        ]);
    }

    // =========================================================================
    // CEK #1 — PO baru: item_po_numbers terisi, semua item = po_id dokumen
    // =========================================================================
    public function test_cek1_po_baru_item_po_numbers_terisi(): void
    {
        $po = $this->makeGpsPO(2);

        $sourceData    = $po->source_data;
        $itemPoNumbers = $sourceData['item_po_numbers'] ?? [];

        $this->assertCount(2, $itemPoNumbers, 'item_po_numbers harus punya 2 entry');
        $this->assertEquals($po->po_id, $itemPoNumbers[0], 'Item 0 harus memiliki po_id dokumen');
        $this->assertEquals($po->po_id, $itemPoNumbers[1], 'Item 1 harus memiliki po_id dokumen');
    }

    // =========================================================================
    // CEK #2 — Partial approve GPS → isRejected() = true, can_edit = true
    // =========================================================================
    public function test_cek2_partial_approve_po_bisa_resubmit(): void
    {
        $po = $this->makeGpsPO(2);
        $this->simulatePartialApproval($po, [0], [1]);
        $po->refresh();

        $this->assertTrue($po->isRejected(), 'PO dengan item rejected harus isRejected() = true');
        $this->assertTrue($po->can_edit, 'PO partial harus can_edit = true');
        $this->assertEquals('Disetujui', $po->status);
    }

    // =========================================================================
    // CEK #3 — Resubmit GPS partial → PO baru dibuat dengan po_id increment
    // =========================================================================
    public function test_cek3_resubmit_gps_partial_membuat_po_baru(): void
    {
        $year   = now()->year;
        $firstPo = $this->makeGpsPO(2, "PO-{$year}-001");
        $this->simulatePartialApproval($firstPo, [0], [1]);

        $countBefore = PurchaseOrder::count();

        // Panggil resubmitGpsPartial via controller
        $request = Request::create(
            '/admin/purchase-order/' . $firstPo->id . '/resubmit-gps-partial',
            'POST',
            [
                '_token'      => 'test',
                'gps_items'   => [
                    ['original_idx' => 1, 'biaya_sewa' => 600000, 'nama_bank' => 'BCA', 'no_rekening' => '12345671', 'nama_pemilik' => 'Vendor GPS 2'],
                ],
                'tanggal_bayar' => now()->toDateString(),
                'tanggal_habis' => now()->addYear()->toDateString(),
                'keterangan'    => 'Resubmit test',
            ]
        );
        $request->headers->set('Accept', 'application/json');

        $controller = app(PurchaseOrderController::class);
        $response   = $controller->resubmitGpsPartial($request, $firstPo->id);
        $result     = json_decode($response->getContent(), true);

        $this->assertTrue($result['success'], 'Resubmit harus sukses: ' . ($result['message'] ?? ''));
        $this->assertEquals($countBefore + 1, PurchaseOrder::count(), 'Harus ada 1 PO baru');
    }

    // =========================================================================
    // CEK #4 — PO lama setelah resubmit: hanya berisi item approved
    // =========================================================================
    public function test_cek4_po_lama_hanya_berisi_item_approved(): void
    {
        $year   = now()->year;
        $firstPo = $this->makeGpsPO(2, "PO-{$year}-001");
        $this->simulatePartialApproval($firstPo, [0], [1]);

        $request = Request::create('/x', 'POST', [
            '_token'      => 'test',
            'gps_items'   => [
                ['original_idx' => 1, 'biaya_sewa' => 600000],
            ],
            'tanggal_bayar' => now()->toDateString(),
            'tanggal_habis' => now()->addYear()->toDateString(),
        ]);
        $request->headers->set('Accept', 'application/json');

        $controller = app(PurchaseOrderController::class);
        $controller->resubmitGpsPartial($request, $firstPo->id);

        $firstPo->refresh();
        $gpsItems = $firstPo->source_data['gps_items'] ?? [];

        $this->assertCount(1, $gpsItems, 'PO lama harus hanya punya 1 GPS item (yang approved)');
        $this->assertEquals('Type-1', $gpsItems[0]['type'] ?? null, 'Item yang tersisa harus Type-1 (index 0)');
        $this->assertEquals('Disetujui', $firstPo->status, 'PO lama tetap Disetujui');
        $this->assertFalse($firstPo->can_edit, 'PO lama tidak bisa di-edit lagi');
    }

    // =========================================================================
    // CEK #5 — PO baru setelah resubmit: hanya berisi item rejected, item_po_numbers = po_id baru
    // =========================================================================
    public function test_cek5_po_baru_berisi_item_rejected_dengan_po_number_baru(): void
    {
        $year    = now()->year;
        $firstPo = $this->makeGpsPO(2, "PO-{$year}-001");
        $this->simulatePartialApproval($firstPo, [0], [1]);

        $request = Request::create('/x', 'POST', [
            '_token'    => 'test',
            'gps_items' => [
                ['original_idx' => 1, 'biaya_sewa' => 600000],
            ],
            'tanggal_bayar' => now()->toDateString(),
            'tanggal_habis' => now()->addYear()->toDateString(),
        ]);
        $request->headers->set('Accept', 'application/json');

        $controller = app(PurchaseOrderController::class);
        $controller->resubmitGpsPartial($request, $firstPo->id);

        // Ambil PO terbaru (yang baru dibuat)
        $newPo = PurchaseOrder::where('id', '!=', $firstPo->id)
            ->where('source_type', 'gps')
            ->latest()
            ->first();

        $this->assertNotNull($newPo, 'PO baru harus ada');
        $this->assertNotEquals($firstPo->po_id, $newPo->po_id, 'PO baru harus punya po_id berbeda');
        $this->assertEquals('Pending', $newPo->status, 'PO baru harus Pending');

        $newGpsItems    = $newPo->source_data['gps_items'] ?? [];
        $newPoNumbers   = $newPo->source_data['item_po_numbers'] ?? [];

        $this->assertCount(1, $newGpsItems, 'PO baru harus punya 1 GPS item');
        $this->assertCount(1, $newPoNumbers, 'PO baru item_po_numbers harus punya 1 entry');
        $this->assertEquals($newPo->po_id, $newPoNumbers[0], 'Item di PO baru harus pakai po_id PO baru');
        $this->assertEmpty($newPo->source_data['locked_approved_idx'] ?? [], 'PO baru tidak boleh punya locked items');
    }

    // =========================================================================
    // CEK #6 — Service Part partial approve + resubmit via PengeluaranInterceptorService
    // =========================================================================
    public function test_cek6_service_part_resubmit_membuat_po_baru(): void
    {
        $year    = now()->year;
        $firstPo = $this->makeServicePartPO(2, "PO-{$year}-001");
        $this->simulatePartialApproval($firstPo, [0], [1]);

        $countBefore = PurchaseOrder::count();

        // Panggil resubmitToPurchaseOrder via PengeluaranInterceptorService
        $interceptor = app(\App\Services\PengeluaranInterceptorService::class);
        $request = Request::create('/x', 'POST', [
            '_token'         => 'test',
            'kendaraan_id'   => 1,
            'tanggal_service'=> now()->toDateString(),
            'kilometer'      => 12000,
            'keluhan'        => 'Updated keluhan',
            'parts'          => [
                [
                    'nama_part'    => 'Part-2-updated',
                    'biaya'        => 450000,
                    'nama_bank'    => 'BNI',
                    'no_rekening'  => '98761',
                    'nama_rekening'=> 'Supplier Updated',
                ],
            ],
        ]);

        $newPo = $interceptor->resubmitToPurchaseOrder($firstPo->id, $request, 'service_part');

        $this->assertEquals($countBefore + 1, PurchaseOrder::count(), 'Harus ada 1 PO baru');
        $this->assertNotEquals($firstPo->po_id, $newPo->po_id, 'PO baru punya po_id berbeda');
        $this->assertEquals('Pending', $newPo->status);

        $firstPo->refresh();
        $approvedParts = $firstPo->source_data['parts'] ?? [];
        $this->assertCount(1, $approvedParts, 'PO lama hanya berisi 1 part (approved)');
        $this->assertEquals('Part-1', $approvedParts[0]['nama_part'] ?? null);

        $newParts = $newPo->source_data['parts'] ?? [];
        $this->assertCount(1, $newParts, 'PO baru berisi 1 part (rejected/resubmit)');
        $this->assertEquals('Part-2-updated', $newParts[0]['nama_part'] ?? null);
    }

    // =========================================================================
    // CEK #7 — Service Asuransi partial resubmit → service_asuransi_id dipertahankan di PO baru
    // =========================================================================
    public function test_cek7_service_asuransi_resubmit_preserve_asuransi_id(): void
    {
        $year    = now()->year;
        $firstPo = $this->makeServiceAsuransiPO(2, "PO-{$year}-001");
        $this->simulatePartialApproval($firstPo, [0], [1]);

        $request = Request::create('/x', 'POST', [
            '_token'    => 'test',
            'kejadians' => [
                ['nama_kejadian' => 'Kejadian-2-updated', 'biaya' => 400000, 'lampiran_lama' => []],
            ],
            'tanggal_service' => now()->toDateString(),
            'nama_asuransi'   => 'Asuransi Test',
        ]);
        $request->headers->set('Accept', 'application/json');

        $controller = app(PurchaseOrderController::class);
        $response   = $controller->resubmitServiceAsuransi($request, $firstPo->id);
        $result     = json_decode($response->getContent(), true);

        $this->assertTrue($result['success'], 'Resubmit harus sukses: ' . ($result['message'] ?? ''));

        $newPo = PurchaseOrder::where('id', '!=', $firstPo->id)
            ->where('source_type', 'service_asuransi')
            ->latest()
            ->first();

        $this->assertNotNull($newPo, 'PO baru service_asuransi harus ada');
        $this->assertEquals(99, $newPo->source_data['service_asuransi_id'] ?? null,
            'service_asuransi_id harus dipertahankan di PO baru');
        $this->assertEquals('Pending', $newPo->status);
        $this->assertEmpty($newPo->source_data['locked_approved_idx'] ?? [], 'PO baru tidak punya locked items');
    }

    // =========================================================================
    // CEK #8 — Service Incident partial resubmit → service_incident_id dipertahankan di PO baru
    // =========================================================================
    public function test_cek8_service_incident_resubmit_preserve_incident_id(): void
    {
        $year    = now()->year;
        $parts   = [
            ['nama_part' => 'Part-A', 'biaya' => 100000, 'category_id' => null],
            ['nama_part' => 'Part-B', 'biaya' => 200000, 'category_id' => null],
        ];
        $ipn     = [0 => "PO-{$year}-001", 1 => "PO-{$year}-001"];
        $sd      = [
            'kendaraan_id'       => 1,
            'parts'              => $parts,
            'tanggal_service'    => now()->toDateString(),
            'kilometer'          => 8000,
            'service_incident_id'=> 55,
            'item_po_numbers'    => $ipn,
        ];

        $firstPo = PurchaseOrder::create([
            'po_id'             => "PO-{$year}-001",
            'tanggal_po'        => now()->toDateString(),
            'vendor'            => 'Vendor Test',
            'total_barang'      => 2,
            'total_harga'       => 300000,
            'status_po'         => 'Pending',
            'source_type'       => 'service_incident',
            'source_data'       => $sd,
            'status'            => 'Pending',
            'can_edit'          => false,
            'terakhir_diajukan' => now(),
        ]);
        $this->simulatePartialApproval($firstPo, [0], [1]);

        $request = Request::create('/x', 'POST', [
            '_token'    => 'test',
            'kejadians' => [
                ['nama_kejadian' => 'Part-B-updated', 'biaya' => 250000, 'lampiran_lama' => []],
            ],
            'tanggal_service' => now()->toDateString(),
            'kilometer'       => 8000,
        ]);
        $request->headers->set('Accept', 'application/json');

        $controller = app(PurchaseOrderController::class);
        $response   = $controller->resubmitServiceIncident($request, $firstPo->id);
        $result     = json_decode($response->getContent(), true);

        $this->assertTrue($result['success'], 'Resubmit harus sukses: ' . ($result['message'] ?? ''));

        $newPo = PurchaseOrder::where('id', '!=', $firstPo->id)
            ->where('source_type', 'service_incident')
            ->latest()
            ->first();

        $this->assertNotNull($newPo, 'PO baru service_incident harus ada');
        $this->assertEquals(55, $newPo->source_data['service_incident_id'] ?? null,
            'service_incident_id harus dipertahankan di PO baru');
        $this->assertEquals('Pending', $newPo->status);
    }

    // =========================================================================
    // CEK #9 — Full reject GPS → PO yang sama direset ke Pending (alur lama)
    // =========================================================================
    public function test_cek9_full_reject_gps_reset_po_yang_sama(): void
    {
        $year    = now()->year;
        $po      = $this->makeGpsPO(2, "PO-{$year}-001");
        // Full reject: status = Ditolak, can_edit = true
        $po->update(['status' => 'Ditolak', 'can_edit' => true]);

        $countBefore = PurchaseOrder::count();

        $approvalService = app(PurchaseOrderApprovalService::class);
        $request = Request::create('/x', 'POST', [
            '_token'        => 'test',
            'kendaraan_id'  => 1,
            'tanggal_bayar' => now()->toDateString(),
            'tanggal_habis' => now()->addYear()->toDateString(),
            'keterangan'    => 'Resubmit full reject',
            'gps_items'     => [
                ['gps_id' => 1, 'type' => 'Type-1', 'biaya_sewa' => 500000],
                ['gps_id' => 2, 'type' => 'Type-2', 'biaya_sewa' => 600000],
            ],
        ]);

        $po = $approvalService->resubmit($po, $request->all(), $request);

        $this->assertEquals($countBefore, PurchaseOrder::count(), 'Full reject tidak membuat PO baru');
        $this->assertEquals('Pending', $po->status, 'PO yang sama direset ke Pending');
    }

    // =========================================================================
    // CEK #10 — Single-item types (Pajak) resubmit → PO yang sama direset, tidak buat PO baru
    // =========================================================================
    public function test_cek10_pajak_resubmit_tidak_membuat_po_baru(): void
    {
        $year = now()->year;
        $po   = PurchaseOrder::create([
            'po_id'             => "PO-{$year}-001",
            'tanggal_po'        => now()->toDateString(),
            'vendor'            => 'Test Pajak Vendor',
            'total_barang'      => 1,
            'total_harga'       => 500000,
            'status_po'         => 'Pending',
            'source_type'       => 'pajak',
            'source_data'       => [
                'kendaraan_id' => 1,
                'nominal'      => 500000,
                'nama_bank'    => 'BRI',
                'no_rekening'  => '111222',
                'nama_pemilik' => 'Pemerintah',
            ],
            'status'            => 'Ditolak',
            'can_edit'          => true,
            'terakhir_diajukan' => now(),
        ]);

        $countBefore = PurchaseOrder::count();

        $response = $this->postJson(
            route('purchase-order.resubmit-update', $po->id),
            ['nominal' => 550000, 'nama_pemilik' => 'Pemerintah']
        );

        $response->assertJson(['success' => true]);
        $this->assertEquals($countBefore, PurchaseOrder::count(), 'Pajak resubmit tidak membuat PO baru');

        $po->refresh();
        $this->assertEquals('Pending', $po->status, 'PO Pajak direset ke Pending');
        $this->assertEquals(550000, $po->source_data['nominal'] ?? null, 'Nominal Pajak diupdate');
    }

    // =========================================================================
    // CEK #11 — PO baru hasil resubmit tidak memiliki locked_approved_idx
    // =========================================================================
    public function test_cek11_po_baru_tidak_punya_locked_approved_idx(): void
    {
        $year    = now()->year;
        $firstPo = $this->makeGpsPO(2, "PO-{$year}-001");
        $this->simulatePartialApproval($firstPo, [0], [1]);

        $request = Request::create('/x', 'POST', [
            '_token'    => 'test',
            'gps_items' => [['original_idx' => 1, 'biaya_sewa' => 600000]],
            'tanggal_bayar' => now()->toDateString(),
            'tanggal_habis' => now()->addYear()->toDateString(),
        ]);
        $request->headers->set('Accept', 'application/json');

        $controller = app(PurchaseOrderController::class);
        $controller->resubmitGpsPartial($request, $firstPo->id);

        $newPo = PurchaseOrder::where('id', '!=', $firstPo->id)->latest()->first();

        $lockedIdx = $newPo->source_data['locked_approved_idx'] ?? [];
        $this->assertEmpty($lockedIdx, 'PO baru hasil resubmit tidak boleh punya locked_approved_idx');

        $itemDecisions = $newPo->source_data['item_decisions'] ?? [];
        $this->assertEmpty($itemDecisions, 'PO baru tidak boleh punya item_decisions dari cycle sebelumnya');
    }

    // =========================================================================
    // CEK #12 — po_id PO baru adalah increment dari sequence terakhir
    // =========================================================================
    public function test_cek12_po_baru_po_id_adalah_increment(): void
    {
        $year    = now()->year;
        $firstPo = $this->makeGpsPO(2, "PO-{$year}-001");
        $this->simulatePartialApproval($firstPo, [0], [1]);

        $request = Request::create('/x', 'POST', [
            '_token'    => 'test',
            'gps_items' => [['original_idx' => 1, 'biaya_sewa' => 600000]],
            'tanggal_bayar' => now()->toDateString(),
            'tanggal_habis' => now()->addYear()->toDateString(),
        ]);
        $request->headers->set('Accept', 'application/json');

        $controller = app(PurchaseOrderController::class);
        $controller->resubmitGpsPartial($request, $firstPo->id);

        $newPo = PurchaseOrder::where('id', '!=', $firstPo->id)->latest()->first();

        // Format: PO-YYYY-XXX, PO baru harus PO-YYYY-002 (setelah PO-YYYY-001)
        $expectedPoId = "PO-{$year}-002";
        $this->assertEquals($expectedPoId, $newPo->po_id,
            "PO baru harus punya po_id {$expectedPoId}, dapat: {$newPo->po_id}");
    }
}
