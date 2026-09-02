<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = 2026;
        
        // Data nama supplier yang bervariasi
        $namaSuppliers = [
            'CV Suku Cadang Motor', 'PT Ban Indonesia', 'Toko Sparepart Jaya', 'CV Audio Mobil', 'PT Diesel Utama',
            'CV Maju Jaya Otomotif', 'PT Karya Teknik', 'Toko Bengkel Sejahtera', 'UD Cahaya Motor', 'CV Sentosa Part',
            'PT Indah Spare Part', 'Toko Lancar Jaya', 'CV Bintang Motor', 'PT Sukses Otomotif', 'UD Makmur Jaya',
            'CV Permata Spare Part', 'PT Abadi Motor', 'Toko Rejeki Nomplok', 'CV Barokah Motor', 'PT Jaya Abadi',
            'UD Berkah Usaha', 'CV Anugrah Motor', 'Toko Sumber Rezeki', 'PT Mitra Otomotif', 'CV Harapan Jaya',
            'UD Terang Bulan', 'PT Cahaya Terang', 'Toko Murah Meriah', 'CV Usaha Mandiri', 'PT Gemilang Motor',
            'UD Sinar Jaya', 'CV Karya Utama', 'Toko Melati Motor', 'PT Putra Jaya', 'CV Delta Motor',
            'UD Sejahtera Motor', 'PT Wahana Part', 'Toko Mawar Indah', 'CV Sakura Motor', 'PT Nusantara Teknik',
            'UD Rizky Motor', 'CV Bahagia Selalu', 'Toko Harmoni Motor', 'PT Indo Part', 'CV Tirta Motor',
            'UD Global Motor', 'PT Cakrawala Part', 'Toko Mentari Motor', 'CV Surya Jaya', 'PT Mega Motor',
        ];

        // Generate 50 supplier tersebar di tahun 2026
        for ($i = 0; $i < 50; $i++) {
            // Random tanggal di tahun 2026 untuk testing chart
            $month = rand(1, 12);
            $day = rand(1, \Carbon\Carbon::create($year, $month, 1)->daysInMonth);
            $createdAt = \Carbon\Carbon::create($year, $month, $day)
                ->setTime(rand(8, 17), rand(0, 59), rand(0, 59));

            Supplier::create([
                'user_id'        => 1,
                'nama_supplier'  => $namaSuppliers[$i] . ($i >= 50 ? ' ' . ($i - 49) : ''),
                'no_telp'        => '08' . rand(10, 99) . rand(10000000, 99999999),
                'created_at'     => $createdAt,
                'updated_at'     => $createdAt,
            ]);
        }
    }
}