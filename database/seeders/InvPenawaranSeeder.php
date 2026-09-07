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
            ['PT Maju Jaya Abadi', 'Budi Hartono', 'Jl. Gatot Subroto No.1, Jakarta', 'budi.hartono@majujaya.com'],
            ['CV Berkah Mandiri', 'Siti Rahayu', 'Jl. Sudirman No.25, Bandung', 'siti.rahayu@berkahmandiri.com'],
            ['PT Teknologi Nusantara', 'Hendra Gunawan', 'Jl. Pemuda No.10, Surabaya', 'hendra@teknusantara.co.id'],
            ['UD Sumber Rejeki', 'Dewi Lestari', 'Jl. Ahmad Yani No.5, Semarang', 'dewi@sumberrejeki.com'],
            ['PT Logistik Andalan', 'Rizal Fahmi', 'Jl. Diponegoro No.88, Yogyakarta', 'rizal@logistikandalan.co.id'],
            ['CV Karya Utama', 'Nur Hidayah', 'Jl. Imam Bonjol No.15, Medan', 'nur@karyautama.com'],
            ['PT Solusi Transportasi', 'Agus Setiawan', 'Jl. Pahlawan No.3, Makassar', 'agus@solusitrans.co.id'],
            ['PT Global Rentcar', 'Maya Anggraini', 'Jl. Raya Darmo No.12, Surabaya', 'maya@globalrentcar.com'],
            ['CV Perdana Sejahtera', 'Wahyu Nugroho', 'Jl. Asia Afrika No.7, Bandung', 'wahyu@perdanasejahtera.com'],
            ['PT Aneka Niaga Indonesia', 'Fitri Handayani', 'Jl. Thamrin No.45, Jakarta', 'fitri@anekaniaga.co.id'],
            ['PT Indo Logistik', 'Bambang Wijaya', 'Jl. Sudirman No.88, Jakarta', 'bambang@indologistik.com'],
            ['CV Mitra Sejati', 'Rina Kusuma', 'Jl. Veteran No.22, Surabaya', 'rina@mitrasejati.com'],
            ['PT Harapan Jaya', 'Dedi Santoso', 'Jl. Gatot Kaca No.99, Bandung', 'dedi@harapanjaya.co.id'],
            ['PT Citra Mandiri', 'Lina Wijayanti', 'Jl. Pahlawan No.12, Semarang', 'lina@citramandiri.com'],
            ['CV Karya Sejahtera', 'Eko Prasetyo', 'Jl. Pemuda No.45, Yogyakarta', 'eko@karyasejahtera.com'],
        ];

        $perihalList = [
            'Penawaran Sewa Kendaraan Operasional',
            'Penawaran Sewa Armada Angkutan',
            'Penawaran Layanan Transportasi',
            'Penawaran Sewa Kendaraan Proyek',
            'Penawaran Rental Kendaraan Jangka Panjang',
            'Penawaran Sewa Kendaraan Shuttle Karyawan',
            'Penawaran Sewa Minibus Pariwisata',
        ];

        $statusList = ['dibuat', 'pending', 'approved', 'active', 'completed', 'expired'];

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

                [$custName, $cp, $addr, $email] = $customers[array_rand($customers)];
                $status = $statusList[array_rand($statusList)];
                $periode = rand(1, 12);
                $total = rand(5000000, 50000000);

                $penawaran = InvPenawaran::create([
                    'no_penawaran'      => 'PNW-' . $year . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT),
                    'tanggal_penawaran' => $tgl->toDateString(),
                    'kepada'            => $custName,
                    'up'                => $cp,
                    'perihal'           => $perihalList[array_rand($perihalList)],
                    'customer_name'     => $custName,
                    'contact_person'    => $cp,
                    'email_person'      => $email,
                    'alamat'            => $addr,
                    'no_ktp'            => null,
                    'jenis_pelanggan'   => 'perusahaan',
                    'pengirim'          => 'Divisi Sales',
                    'periode'           => $periode,
                    'periode_satuan'    => 'bulan',
                    'staff'             => 'Staff Sales',
                    'name_staff'        => 'Eko Prasetyo',
                    'ttd_image'         => null,
                    'direktur'          => 'Direktur',
                    'name_direktur'     => 'Budi Santoso',
                    'status'            => $status,
                    'total'             => $total,
                    'file_penawaran'    => null,
                    'ketentuan'         => null,
                    'file_persyaratan'  => null,
                ]);

                // Tambah 1-3 item per penawaran
                $itemCount = rand(1, 3);
                for ($j = 0; $j < $itemCount; $j++) {
                    $qty = rand(1, 5);
                    $price = rand(800000, 5000000);
                    $durasi = rand(1, $periode);

                    InvPenawaranItem::create([
                        'penawaran_id'  => $penawaran->id,
                        'kendaraan_id'  => null,
                        'qty'           => $qty,
                        'tahun_unit'    => (string) rand(2019, 2024),
                        'price'         => $price,
                        'durasi'        => $durasi,
                        'satuan_durasi' => 'bulan',
                    ]);
                }

                $counter++;
            }
        }
    }
}
