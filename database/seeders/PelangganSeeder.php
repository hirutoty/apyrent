<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;
use Carbon\Carbon;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $namaPerorangan = [
            'Budi Santoso', 'Joko Widodo', 'Andi Saputra', 'Rizky Pratama', 'Dian Permata',
            'Siti Rahayu', 'Ahmad Fauzi', 'Dewi Lestari', 'Hendra Gunawan', 'Rina Wati',
            'Bambang Sutrisno', 'Nia Ramadhani', 'Ferdy Sambo', 'Lina Marlina', 'Tono Suprapto',
            'Yuli Astuti', 'Fajar Nugroho', 'Sri Wahyuni', 'Rudi Hartono', 'Mega Putri',
            'Wahyu Setiawan', 'Indah Kurniasih', 'Eko Prasetyo', 'Fitri Handayani', 'Galih Wicaksono',
            'Arif Rahman', 'Nina Sari', 'Tommy Gunawan', 'Ratna Dewi', 'Hadi Prabowo',
        ];

        $namaPerusahaan = [
            'PT Maju Bersama', 'CV Sumber Rezeki', 'PT Cahaya Abadi', 'CV Jaya Mandiri', 'PT Sukses Selalu',
            'PT Karya Utama', 'CV Harapan Baru', 'PT Gemilang Jaya', 'CV Delta Nusantara', 'PT Bintang Timur',
            'PT Nusantara Trans', 'CV Permata Hijau', 'PT Sinar Mas Logistik', 'CV Berkah Sejati', 'PT Indo Mitra',
            'PT Wahana Ekspres', 'CV Tirta Agung', 'PT Mandiri Karya', 'CV Perkasa Utama', 'PT Cipta Rasa',
            'PT Lancar Jaya', 'CV Mitra Usaha', 'PT Sejahtera Abadi', 'CV Putra Bangsa', 'PT Global Trans',
            'PT Dharma Karya', 'CV Bahagia Sentosa', 'PT Cakrawala Indah', 'CV Berkah Jaya', 'PT Insan Mandiri',
        ];

        $kota = ['Wonosobo', 'Magelang', 'Purworejo', 'Kebumen', 'Purwokerto', 'Temanggung', 'Kendal', 'Semarang', 'Yogyakarta', 'Solo'];
        
        $year = 2026;
        $counter = 0;

        // Generate 60 pelanggan: 5 per bulan (Jan-Des 2026)
        // 30 perorangan + 30 perusahaan
        for ($month = 1; $month <= 12; $month++) {
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

            for ($j = 0; $j < 5; $j++) {
                $day = rand(1, $daysInMonth);
                $createdAt = Carbon::create($year, $month, $day)
                    ->setTime(rand(8, 17), rand(0, 59), rand(0, 59));

                // Bergantian: perorangan & perusahaan
                if ($counter % 2 === 0) {
                    // Perorangan
                    $nama = $namaPerorangan[$counter % count($namaPerorangan)];
                    Pelanggan::create([
                        'nama_pelanggan'   => $nama,
                        'kontak_pelanggan' => '08' . rand(100000000, 999999999),
                        'email_pelanggan'  => strtolower(str_replace(' ', '.', $nama)) . rand(1,999) . '@gmail.com',
                        'jenis_pelanggan'  => 'perorangan',
                        'alamat'           => 'Jl. ' . $kota[$counter % count($kota)] . ' No. ' . rand(1, 100),
                        'created_at'       => $createdAt,
                        'updated_at'       => $createdAt,
                    ]);
                } else {
                    // Perusahaan
                    $nama = $namaPerusahaan[$counter % count($namaPerusahaan)];
                    Pelanggan::create([
                        'nama_pelanggan'   => $nama,
                        'kontak_pelanggan' => '02' . rand(10000000, 99999999),
                        'email_pelanggan'  => strtolower(str_replace([' ', '.'], ['', ''], $nama)) . rand(1,99) . '@mail.co.id',
                        'jenis_pelanggan'  => 'perusahaan',
                        'alamat'           => 'Jl. Raya ' . $kota[$counter % count($kota)] . ' No. ' . rand(1, 200),
                        'created_at'       => $createdAt,
                        'updated_at'       => $createdAt,
                    ]);
                }

                $counter++;
            }
        }
    }
}

