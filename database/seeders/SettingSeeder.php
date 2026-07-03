<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'nama_perusahaan'     => 'PT Rental Kendaraan Indonesia',
                'alamat'              => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'telepon'             => '021-12345678',
                'email'               => 'info@rentalkendaraan.co.id',
                'website'             => 'https://rentalkendaraan.co.id',
                'nama_bank'           => 'Bank BCA',
                'nomor_rekening'      => '1234567890',
                'atas_nama_rekening'  => 'PT Rental Kendaraan Indonesia',
                'kode_pos'            => '10110',
                'batas_reminder' => 1,
                'satuan_reminder' => 'bulan',

                // Simpan nama file logo yang ada di storage
                'logo'                => 'setting/logo.png',
            ]
        );
    }
}
