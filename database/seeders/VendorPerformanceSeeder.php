<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VendorPerformance;

class VendorPerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'vendor'          => 'PT Maju Jaya',
                'total_order'     => 48,
                'ketepatan_waktu' => 91.67,
                'kualitas_barang' => 88.50,
                'komplain'        => 3,
                'penilaian_akhir' => 89.20,
                'catatan'         => 'Vendor terpercaya, pengiriman konsisten',
            ],
            [
                'vendor'          => 'CV Berkah Abadi',
                'total_order'     => 35,
                'ketepatan_waktu' => 74.29,
                'kualitas_barang' => 80.00,
                'komplain'        => 7,
                'penilaian_akhir' => 76.50,
                'catatan'         => 'Perlu peningkatan ketepatan waktu pengiriman',
            ],
            [
                'vendor'          => 'PT Sumber Makmur',
                'total_order'     => 60,
                'ketepatan_waktu' => 95.00,
                'kualitas_barang' => 92.30,
                'komplain'        => 2,
                'penilaian_akhir' => 93.50,
                'catatan'         => 'Performa terbaik, kualitas produk sangat baik',
            ],
            [
                'vendor'          => 'UD Sejahtera',
                'total_order'     => 22,
                'ketepatan_waktu' => 63.64,
                'kualitas_barang' => 70.00,
                'komplain'        => 9,
                'penilaian_akhir' => 65.80,
                'catatan'         => 'Banyak komplain, perlu evaluasi ulang kontrak',
            ],
            [
                'vendor'          => 'PT Indo Supplier',
                'total_order'     => 41,
                'ketepatan_waktu' => 82.93,
                'kualitas_barang' => 85.00,
                'komplain'        => 5,
                'penilaian_akhir' => 83.20,
                'catatan'         => 'Performa stabil, harga kompetitif',
            ],
        ];

        foreach ($vendors as $vendor) {
            VendorPerformance::updateOrCreate(
                ['vendor' => $vendor['vendor']],
                $vendor
            );
        }
    }
}
