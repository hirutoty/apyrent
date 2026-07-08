<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $namaPerorangan = [
            'Budi Santoso', 'Joko Widodo', 'Andi Saputra', 'Rizky Pratama', 'Dian Permata',
            'Siti Rahayu', 'Ahmad Fauzi', 'Dewi Lestari', 'Hendra Gunawan', 'Rina Wati',
            'Bambang Sutrisno', 'Nia Ramadhani', 'Ferdy Sambo', 'Lina Marlina', 'Tono Suprapto',
            'Yuli Astuti', 'Fajar Nugroho', 'Sri Wahyuni', 'Rudi Hartono', 'Mega Putri',
            'Wahyu Setiawan', 'Indah Kurniasih', 'Eko Prasetyo', 'Fitri Handayani', 'Galih Wicaksono',
        ];

        $namaPerusahaan = [
            'PT Maju Bersama', 'CV Sumber Rezeki', 'PT Cahaya Abadi', 'CV Jaya Mandiri', 'PT Sukses Selalu',
            'PT Karya Utama', 'CV Harapan Baru', 'PT Gemilang Jaya', 'CV Delta Nusantara', 'PT Bintang Timur',
            'PT Nusantara Trans', 'CV Permata Hijau', 'PT Sinar Mas Logistik', 'CV Berkah Sejati', 'PT Indo Mitra',
            'PT Wahana Ekspres', 'CV Tirta Agung', 'PT Mandiri Karya', 'CV Perkasa Utama', 'PT Cipta Rasa',
            'PT Lancar Jaya', 'CV Mitra Usaha', 'PT Sejahtera Abadi', 'CV Putra Bangsa', 'PT Global Trans',
        ];

        $kota = ['Wonosobo', 'Magelang', 'Purworejo', 'Kebumen', 'Purwokerto', 'Temanggung', 'Kendal', 'Semarang', 'Yogyakarta', 'Solo'];

        for ($i = 0; $i < 25; $i++) {
            Member::create([
                'nama_member'   => $namaPerorangan[$i],
                'kontak_member' => '08' . rand(100000000, 999999999),
                'email_member'  => strtolower(str_replace(' ', '.', $namaPerorangan[$i])) . '@gmail.com',
                'jenis_member'  => 'perorangan',
                'alamat'        => 'Jl. ' . $kota[$i % count($kota)] . ' No. ' . rand(1, 100),
            ]);
        }

        for ($i = 0; $i < 25; $i++) {
            Member::create([
                'nama_member'   => $namaPerusahaan[$i],
                'kontak_member' => '02' . rand(10000000, 99999999),
                'email_member'  => strtolower(str_replace([' ', '.'], ['', ''], $namaPerusahaan[$i])) . '@mail.co.id',
                'jenis_member'  => 'perusahaan',
                'alamat'        => 'Jl. Raya ' . $kota[$i % count($kota)] . ' No. ' . rand(1, 200),
            ]);
        }
    }
}
