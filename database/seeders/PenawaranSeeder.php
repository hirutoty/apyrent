<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenawaranSales;
use Carbon\Carbon;

class PenawaranSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggan = [
            'PT Maju Jaya Abadi',
            'CV Berkah Mandiri',
            'PT Teknologi Nusantara',
            'UD Sumber Rejeki',
            'PT Logistik Andalan',
            'CV Karya Utama',
            'PT Solusi Transportasi',
            'PT Global Rentcar',
            'CV Perdana Sejahtera',
            'PT Aneka Niaga',
            'PT Bintang Timur',
            'CV Mitra Sejati',
            'PT Indo Logistik',
            'CV Citra Mandiri',
            'PT Harapan Jaya',
        ];

        $produk = [
            'Sewa Kendaraan Operasional',
            'Layanan Transportasi Proyek',
            'Sewa Armada Angkutan Barang',
            'Sewa Kendaraan Jangka Panjang',
            'Layanan Shuttle Karyawan',
            'Sewa Minibus Pariwisata',
            'Sewa Truk Box',
            'Layanan Antar Jemput',
        ];

        $statusList = ['Draft', 'Terkirim', 'Disetujui', 'Ditolak'];

        // Data penawaran berdasarkan tahun dan bulan
        $dataSchedule = [
            // 2025
            ['year' => 2025, 'month' => 5, 'count' => 10],
            ['year' => 2025, 'month' => 6, 'count' => 2],
            // 2026
            ['year' => 2026, 'month' => 1, 'count' => 3],
            ['year' => 2026, 'month' => 2, 'count' => 2],
            ['year' => 2026, 'month' => 3, 'count' => 5],
            ['year' => 2026, 'month' => 4, 'count' => 10],
            ['year' => 2026, 'month' => 5, 'count' => 1],
            ['year' => 2026, 'month' => 12, 'count' => 15],
        ];

        $counter = 1;

        foreach ($dataSchedule as $schedule) {
            $year = $schedule['year'];
            $month = $schedule['month'];
            $count = $schedule['count'];

            for ($i = 0; $i < $count; $i++) {
                // Generate random date within the specified month
                $day = rand(1, Carbon::create($year, $month, 1)->daysInMonth);
                $tgl = Carbon::create($year, $month, $day);
                
                $validSampai = (clone $tgl)->addDays(rand(14, 60));
                $jumlah = rand(1, 10);
                $harga = rand(500000, 5000000);
                $total = $jumlah * $harga;
                $status = $statusList[array_rand($statusList)];

                PenawaranSales::create([
                    'no_quotation'  => 'QUO-' . $year . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT),
                    'tanggal'       => $tgl->toDateString(),
                    'pelanggan'     => $pelanggan[array_rand($pelanggan)],
                    'produk_jasa'   => $produk[array_rand($produk)],
                    'jumlah'        => $jumlah,
                    'harga_satuan'  => $harga,
                    'total_harga'   => $total,
                    'status'        => $status,
                    'valid_sampai'  => $validSampai->toDateString(),
                ]);

                $counter++;
            }
        }
    }
}
