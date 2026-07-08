<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kendaraan;
use Carbon\Carbon;

class KendaraanSeeder extends Seeder
{
    public function run(): void
    {
        $merks = [
            'Toyota Avanza', 'Toyota Innova', 'Toyota Rush', 'Toyota Fortuner', 'Toyota Calya',
            'Honda Brio', 'Honda Mobilio', 'Honda HR-V', 'Honda CR-V', 'Honda Jazz',
            'Mitsubishi Xpander', 'Mitsubishi Pajero', 'Mitsubishi L300', 'Mitsubishi Outlander',
            'Daihatsu Xenia', 'Daihatsu Terios', 'Daihatsu Sigra', 'Daihatsu Gran Max',
            'Suzuki Ertiga', 'Suzuki APV', 'Suzuki Jimny', 'Suzuki Carry',
            'Nissan X-Trail', 'Nissan Livina', 'Nissan Terra',
            'Isuzu Panther', 'Isuzu D-Max', 'Isuzu Elf',
            'Wuling Almaz', 'Wuling Air ev',
        ];

        $warna = ['Hitam', 'Putih', 'Silver', 'Merah', 'Biru', 'Abu-abu', 'Coklat', 'Kuning'];
        $bahanBakar = ['Pertalite', 'Pertamax', 'Solar', 'Pertamax Turbo'];
        $statusService = ['aman', 'service'];
        $statusKendaraan = ['tersedia', 'disewa', 'service', 'bermasalah'];
        $kota = ['Wonosobo', 'Magelang', 'Purworejo', 'Kebumen', 'Purwokerto', 'Temanggung', 'Kendal', 'Batang'];
        $jenisIds = [1, 2, 3];

        $prefixNopol = ['AA', 'AB', 'AD', 'AE', 'AG'];

        for ($i = 1; $i <= 50; $i++) {
            $merk = $merks[($i - 1) % count($merks)];
            $tahun = rand(2015, 2024);
            $kmSekarang = rand(5000, 120000);
            $kmTerakhirService = max(0, $kmSekarang - rand(1000, 8000));

            Kendaraan::create([
                'user_id'               => 1,
                'jenis_id'              => $jenisIds[($i - 1) % count($jenisIds)],
                'nopol'                 => $prefixNopol[($i - 1) % count($prefixNopol)] . ' ' . str_pad($i * 11 % 9999 + 1000, 4, '0', STR_PAD_LEFT) . ' ' . chr(65 + ($i % 26)) . chr(65 + (($i + 3) % 26)),
                'foto'                  => null,
                'nama_pemilik'          => 'Pemilik Kendaraan ' . $i,
                'alamat'                => $kota[($i - 1) % count($kota)],
                'merk'                  => $merk,
                'tahun_pembuatan'       => $tahun,
                'tahun_perakitan'       => $tahun,
                'isi_silinder'          => rand(1, 3) * 500 . ' CC',
                'warna'                 => $warna[($i - 1) % count($warna)],
                'no_rangka'             => 'NR' . strtoupper(substr(md5($i . 'rangka'), 0, 13)),
                'no_mesin'              => 'NM' . strtoupper(substr(md5($i . 'mesin'), 0, 8)),
                'no_bpkb'               => 'BPKB' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'warna_tnkb'            => 'Hitam',
                'bahan_bakar'           => $bahanBakar[($i - 1) % count($bahanBakar)],
                'kode_lokasi'           => $prefixNopol[($i - 1) % count($prefixNopol)],
                'no_urut_pendaftaran'   => str_pad($i * 1234 % 999999, 6, '0', STR_PAD_LEFT),
                'harga_sewa_per_hari'   => rand(200, 600) * 1000,
                'harga_sewa_per_jam'    => rand(30, 80) * 1000,
                'batas_biaya'           => rand(500, 2000) * 1000,
                'dokumen'               => null,
                'masa_berlaku'          => Carbon::now()->addMonths(rand(-3, 24)),
                'kilometer_sekarang'    => $kmSekarang,
                'limit_km_service'      => 5000,
                'limit_bulan_service'   => 6,
                'km_terakhir_service'   => $kmTerakhirService,
                'tanggal_terakhir_service' => Carbon::now()->subMonths(rand(1, 12)),
                'status_service'        => $statusService[($i - 1) % count($statusService)],
                'status_kendaraan'      => $statusKendaraan[($i - 1) % count($statusKendaraan)],
            ]);
        }
    }
}
