<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bupot;
use Carbon\Carbon;

class BupotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'nomor_bukti'     => 'BUPOT-001',
                'tanggal_bukti'   => Carbon::now()->subDays(10),
                'tipe'            => 'PPh21',
                'npwp_pemotong'   => '01.234.567.8-901.000',
                'nama_pemotong'   => 'PT Rental Maju Jaya',
                'npwp_dipotong'   => '09.876.543.2-109.000',
                'nama_dipotong'   => 'Budi Santoso',
                'jumlah_bruto'    => 5000000,
                'tarif_pajak'     => 0.05,
                'jumlah_potong'   => 250000,
                'status'          => 'Approve',
                'file_bupot'      => null,
            ],

            [
                'nomor_bukti'     => 'BUPOT-002',
                'tanggal_bukti'   => Carbon::now()->subDays(8),
                'tipe'            => 'PPh23',
                'npwp_pemotong'   => '01.234.567.8-901.000',
                'nama_pemotong'   => 'PT Rental Maju Jaya',
                'npwp_dipotong'   => '08.765.432.1-000.000',
                'nama_dipotong'   => 'CV Sinar Abadi',
                'jumlah_bruto'    => 3000000,
                'tarif_pajak'     => 0.02,
                'jumlah_potong'   => 60000,
                'status'          => 'Approve',
                'file_bupot'      => null,
            ],

            [
                'nomor_bukti'     => 'BUPOT-003',
                'tanggal_bukti'   => Carbon::now()->subDays(5),
                'tipe'            => 'PPh26',
                'npwp_pemotong'   => '01.234.567.8-901.000',
                'nama_pemotong'   => 'PT Rental Maju Jaya',
                'npwp_dipotong'   => '07.654.321.0-999.000',
                'nama_dipotong'   => 'UD Jaya Motor',
                'jumlah_bruto'    => 10000000,
                'tarif_pajak'     => 0.1,
                'jumlah_potong'   => 1000000,
                'status'          => 'Draft',
                'file_bupot'      => null,
            ],

           
        ];

        foreach ($data as $item) {
            Bupot::create($item);
        }
    }
}