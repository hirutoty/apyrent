<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $existing = Setting::find(1);

        // Jangan timpa logo yang sudah valid
        $logoValue = null;
        if ($existing && $existing->logo) {
            $storagePath = storage_path('app/public/' . ltrim($existing->logo, '/'));
            $publicPath  = public_path(ltrim($existing->logo, '/'));
            if (file_exists($storagePath) || file_exists($publicPath)) {
                $logoValue = $existing->logo; // pertahankan logo lama yang valid
            }
        }

        $data = [
            'nama_perusahaan'     => 'PT Rental Kendaraan Indonesia',
            'alamat'              => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'telepon'             => '021-12345678',
            'email'               => 'info@rentalkendaraan.co.id',
            'website'             => 'https://rentalkendaraan.co.id',
            'nama_bank'           => 'Bank BCA',
            'nomor_rekening'      => '1234567890',
            'atas_nama_rekening'  => 'PT Rental Kendaraan Indonesia',
            'kode_pos'            => '10110',
            'batas_reminder'      => 1,
            'satuan_reminder'     => 'bulan',
        ];

        // Hanya set logo jika sudah ada file valid, jangan set 'setting/logo.png' yang tidak ada
        if ($logoValue) {
            $data['logo'] = $logoValue;
        }

        Setting::updateOrCreate(['id' => 1], $data);
    }
}
