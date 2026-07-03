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
        Supplier::create([
            'user_id' => 1,
            'nama_supplier' => 'CV Suku Cadang Motor',
            'no_telp' => '081234567890',
            'nama_barang' => 'Oli Mesin',
            'jumlah_barang' => 50,
            'harga_barang' => 75000,
        ]);

        Supplier::create([
            'user_id' => 1,
            'nama_supplier' => 'PT Ban Indonesia',
            'no_telp' => '082233445566',
            'nama_barang' => 'Ban Mobil',
            'jumlah_barang' => 20,
            'harga_barang' => 850000,
        ]);

        Supplier::create([
            'user_id' => 1,
            'nama_supplier' => 'Toko Sparepart Jaya',
            'no_telp' => '081377788899',
            'nama_barang' => 'Aki Mobil',
            'jumlah_barang' => 15,
            'harga_barang' => 1200000,
        ]);

        Supplier::create([
            'user_id' => 1,
            'nama_supplier' => 'CV Audio Mobil',
            'no_telp' => '081299988877',
            'nama_barang' => 'GPS Tracker',
            'jumlah_barang' => 10,
            'harga_barang' => 450000,
        ]);

        Supplier::create([
            'user_id' => 1,
            'nama_supplier' => 'PT Diesel Utama',
            'no_telp' => '082122334455',
            'nama_barang' => 'Filter Solar',
            'jumlah_barang' => 40,
            'harga_barang' => 95000,
        ]);
    }
}