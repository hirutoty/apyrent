<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendoreo;
use Carbon\Carbon;

class VendoreoSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = ['Bahan Baku', 'Packaging', 'Jasa IT', 'Spare Part', 'Logistik', 'Maintenance', 'Cleaning', 'Security', 'Percetakan', 'ATK'];
        $produkJasa = [
            'Kain Katun', 'Kardus dan Label', 'Maintenance Sistem', 'Spare Part Kendaraan',
            'Pengiriman Barang', 'Servis Mesin', 'Jasa Kebersihan', 'Jasa Keamanan',
            'Cetak Dokumen', 'Alat Tulis Kantor', 'Cat dan Kimia', 'Komputer dan Aksesoris',
            'Mebel Kantor', 'Genset dan Panel', 'Seragam Karyawan',
        ];
        $kota = ['Jakarta', 'Bandung', 'Semarang', 'Yogyakarta', 'Surabaya', 'Medan', 'Makassar', 'Palembang', 'Malang', 'Solo'];

        for ($i = 1; $i <= 50; $i++) {
            Vendoreo::create([
                'kode_vendor'             => 'VDR-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_vendor'             => ($i % 2 === 0 ? 'PT ' : 'CV ') . 'Vendor Nusantara ' . $i,
                'kategori'                => $kategori[($i - 1) % count($kategori)],
                'alamat'                  => 'Jl. ' . $kota[($i - 1) % count($kota)] . ' No. ' . ($i * 3),
                'pic_vendor'              => 'PIC Vendor ' . $i,
                'no_telp'                 => '08' . rand(100000000, 999999999),
                'produk_jasa'             => $produkJasa[($i - 1) % count($produkJasa)],
                'rating'                  => rand(2, 5),
                'status'                  => ($i % 5 === 0) ? 'Tidak Aktif' : 'Aktif',
                'tanggal_terakhir_order'  => Carbon::now()->subDays(rand(1, 365)),
                'catatan'                 => 'Catatan vendor ke-' . $i,
            ]);
        }
    }
}
