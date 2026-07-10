<?php

namespace Tests\Feature;

use App\Models\AsuransiKendaraan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsuransiKendaraanTanggalBayarTest extends TestCase
{
    use RefreshDatabase;

    public function test_tanggal_bayar_can_be_mass_assigned_on_asuransi_kendaraan(): void
    {
        $model = new AsuransiKendaraan([
            'tanggal_bayar' => '2026-07-10',
        ]);

        $this->assertSame('2026-07-10', $model->tanggal_bayar);
    }
}
