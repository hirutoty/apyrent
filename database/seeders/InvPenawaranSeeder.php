<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvPenawaran;
use App\Models\InvPenawaranItem;
use Carbon\Carbon;

class InvPenawaranSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['PT Maju Jaya Abadi',       'Budi Hartono',     'Jl. Gatot Subroto No.1, Jakarta'],
            ['CV Berkah Mandiri',         'Siti Rahayu',      'Jl. Sudirman No.25, Bandung'],
            ['PT Teknologi Nusantara',    'Hendra Gunawan',   'Jl. Pemuda No.10, Surabaya'],
            ['UD Sumber Rejeki',          'Dewi Lestari',     'Jl. Ahmad Yani No.5, Semarang'],
            ['PT Logistik Andalan',       'Rizal Fahmi',      'Jl. Diponegoro No.88, Yogyakarta'],
            ['CV Karya Utama',            'Nur Hidayah',      'Jl. Imam Bonjol No.15, Medan'],
            ['PT Solusi Transportasi',    'Agus Setiawan',    'Jl. Pahlawan No.3, Makassar'],
            ['PT Global Rentcar',         'Maya Anggraini',   'Jl. Raya Darmo No.12, Surabaya'],
            ['CV Perdana Sejahtera',      'Wahyu Nugroho',    'Jl. Asia Afrika No.7, Bandung'],
            ['PT Aneka Niaga Indonesia',  'Fitri Handayani',  'Jl. Thamrin No.45, Jakarta'],
        ];

        $items = [
            ['Toyota Innova',   2022, 850000,  1, 'month'],
            ['Honda CR-V',      2021, 950000,  1, 'month'],
            ['Mitsubishi L300', 2020, 650000,  1, 'month'],
            ['Toyota HiAce',    2023, 1200000, 1, 'month'],
            ['Daihatsu GranMax',2021, 550000,  1, 'month'],
            ['Toyota Fortuner', 2022, 1100000, 1, 'month'],
            ['Isuzu Elf',       2020, 750000,  1, 'month'],
            ['Suzuki APV',      2021, 500000,  1, 'month'],
        ];

        $statusList  = ['dibuat', 'pending', 'approved', 'active', 'completed', 'expired'];
        $perihalList = [
            'Penawaran Sewa Kendaraan Operasional',
            'Penawaran Sewa Armada Angkutan',
            'Penawaran Layanan Transportasi',
            'Penawaran Sewa Kendaraan Proyek',
            'Penawaran Rental Kendaraan Jangka Panjang',
        ];

        for ($i = 1; $i <= 20; $i++) {
            [$custName, $cp, $addr] = $customers[($i - 1) % count($customers)];
            $tgl    = Carbon::now()->subDays(rand(10, 300));
            $status = $statusList[($i - 1) % count($statusList)];
            $qty    = rand(1, 4);
            $itemIdx = ($i - 1) % count($items);
            [$namaUnit, $tahun, $price, $durasi, $satuan] = $items[$itemIdx];
            $total  = $price * $qty * $durasi;

            $penawaran = InvPenawaran::create([
                'no_penawaran'      => 'PNW-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal_penawaran' => $tgl->toDateString(),
                'kepada'            => $custName,
                'up'                => $cp,
                'perihal'           => $perihalList[($i - 1) % count($perihalList)],
                'customer_name'     => $custName,
                'contact_person'    => $cp,
                'pengirim'          => 'Divisi Sales',
                'periode'           => $durasi,
                'staff'             => 'Staff Sales',
                'name_staff'        => 'Eko Prasetyo',
                'direktur'          => 'Direktur',
                'name_direktur'     => 'Budi Santoso',
                'status'            => $status,
                'total'             => $total,
                'file_penawaran'    => null,
                'file_persyaratan'  => null,
            ]);

            // Tambah 1–2 item per penawaran
            for ($j = 0; $j < min($qty, 2); $j++) {
                $idx2 = ($itemIdx + $j) % count($items);
                [, $tahun2, $price2, $dur2, $sat2] = $items[$idx2];
                InvPenawaranItem::create([
                    'penawaran_id' => $penawaran->id,
                    'kendaraan_id' => null,
                    'qty'          => 1,
                    'tahun_unit'   => (string) $tahun2,
                    'price'        => $price2,
                    'durasi'       => $dur2,
                    'satuan_durasi'=> $sat2,
                ]);
            }
        }
    }
}
