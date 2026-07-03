<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kendaraan;

class KendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kendaraan::create([
            'user_id' => 1,
            'jenis_id' => 1,

            'nopol' => 'AA 1234 ZX',
            'foto' => null,
            'nama_pemilik' => 'Budi Santoso',
            'alamat' => 'Wonosobo',
            'merk' => 'Toyota Avanza',

            'tahun_pembuatan' => 2021,
            'tahun_perakitan' => 2021,

            'isi_silinder' => '1500 CC',
            'warna' => 'Hitam',

            'no_rangka' => 'MHKS12345678901',
            'no_mesin' => 'ENG123456',
            'no_bpkb' => 'BPKB001122',

            'warna_tnkb' => 'Hitam',
            'bahan_bakar' => 'Pertalite',

            'kode_lokasi' => 'AA',
            'no_urut_pendaftaran' => '123456',

            'harga_sewa_per_hari' => 350000,
            'harga_sewa_per_jam' => 50000,
            'batas_biaya' => 1000000,

            'dokumen' => null,
            'masa_berlaku' => now()->addYear(),

            'kilometer_sekarang' => 45000,
            'limit_km_service' => 5000,
            'limit_bulan_service' => 6,
            'km_terakhir_service' => 40000,

            'tanggal_terakhir_service' => now()->subMonths(2),

            'status_service' => 'aman',
            'status_kendaraan' => 'tersedia',
        ]);

        Kendaraan::create([
            'user_id' => 1,
            'jenis_id' => 2,

            'nopol' => 'AA 5678 YY',
            'foto' => null,
            'nama_pemilik' => 'Joko Widodo',
            'alamat' => 'Magelang',
            'merk' => 'Honda Brio',

            'tahun_pembuatan' => 2020,
            'tahun_perakitan' => 2020,

            'isi_silinder' => '1200 CC',
            'warna' => 'Merah',

            'no_rangka' => 'MHKS987654321',
            'no_mesin' => 'ENG998877',
            'no_bpkb' => 'BPKB9988',

            'warna_tnkb' => 'Hitam',
            'bahan_bakar' => 'Pertamax',

            'kode_lokasi' => 'AA',
            'no_urut_pendaftaran' => '654321',

            'harga_sewa_per_hari' => 300000,
            'harga_sewa_per_jam' => 45000,
            'batas_biaya' => 900000,

            'dokumen' => null,
            'masa_berlaku' => now()->addMonths(8),

            'kilometer_sekarang' => 60000,
            'limit_km_service' => 5000,
            'limit_bulan_service' => 6,
            'km_terakhir_service' => 55000,

            'tanggal_terakhir_service' => now()->subMonths(5),

            'status_service' => 'service',
            'status_kendaraan' => 'disewa',
        ]);

        Kendaraan::create([
            'user_id' => 1,
            'jenis_id' => 3,

            'nopol' => 'AA 9090 KK',
            'foto' => null,
            'nama_pemilik' => 'Andi Saputra',
            'alamat' => 'Temanggung',
            'merk' => 'Mitsubishi Xpander',

            'tahun_pembuatan' => 2022,
            'tahun_perakitan' => 2022,

            'isi_silinder' => '1500 CC',
            'warna' => 'Putih',

            'no_rangka' => 'MTRX00112233',
            'no_mesin' => 'MESIN778899',
            'no_bpkb' => 'BPKB778899',

            'warna_tnkb' => 'Hitam',
            'bahan_bakar' => 'Solar',

            'kode_lokasi' => 'AA',
            'no_urut_pendaftaran' => '998877',

            'harga_sewa_per_hari' => 450000,
            'harga_sewa_per_jam' => 70000,
            'batas_biaya' => 1200000,

            'dokumen' => null,
            'masa_berlaku' => now()->addMonths(10),

            'kilometer_sekarang' => 22000,
            'limit_km_service' => 5000,
            'limit_bulan_service' => 6,
            'km_terakhir_service' => 18000,

            'tanggal_terakhir_service' => now()->subMonth(),

            'status_service' => 'aman',
            'status_kendaraan' => 'tersedia',
        ]);
    }
}