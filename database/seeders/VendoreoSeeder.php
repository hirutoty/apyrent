<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendoreo;

class VendoreoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vendoreo::create([
            'kode_vendor' => 'VDR-001',
            'nama_vendor' => 'PT Sumber Makmur',
            'kategori' => 'Bahan Baku',
            'alamat' => 'Jl. Ahmad Yani No. 10, Jakarta',
            'pic_vendor' => 'Budi Santoso',
            'no_telp' => '081234567890',
            'produk_jasa' => 'Kain Katun',
            'rating' => 5,
            'status' => 'Aktif',
            'tanggal_terakhir_order' => '2025-06-28',
            'catatan' => 'Vendor utama untuk pengadaan kain.',
        ]);

        Vendoreo::create([
            'kode_vendor' => 'VDR-002',
            'nama_vendor' => 'CV Maju Bersama',
            'kategori' => 'Packaging',
            'alamat' => 'Jl. Sudirman No. 20, Bandung',
            'pic_vendor' => 'Andi Wijaya',
            'no_telp' => '082345678901',
            'produk_jasa' => 'Kardus dan Label',
            'rating' => 4,
            'status' => 'Aktif',
            'tanggal_terakhir_order' => '2025-06-20',
            'catatan' => 'Respon cepat dan harga kompetitif.',
        ]);

        Vendoreo::create([
            'kode_vendor' => 'VDR-003',
            'nama_vendor' => 'PT Teknologi Nusantara',
            'kategori' => 'Jasa IT',
            'alamat' => 'Jl. Diponegoro No. 15, Yogyakarta',
            'pic_vendor' => 'Siti Rahma',
            'no_telp' => '083456789012',
            'produk_jasa' => 'Maintenance Sistem dan Infrastruktur IT',
            'rating' => 5,
            'status' => 'Tidak Aktif',
            'tanggal_terakhir_order' => '2025-05-15',
            'catatan' => 'Kontrak telah berakhir.',
        ]);
    }
}