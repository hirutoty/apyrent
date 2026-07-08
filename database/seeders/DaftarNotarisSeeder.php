<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DaftarNotaris;

class DaftarNotarisSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_kantor' => 'Kantor Notaris Hendra, SH', 'layanan' => 'Akta Pendirian, Akta Jual Beli', 'kontak' => '021-5551234', 'email' => 'hendra.notaris@email.com', 'terakhir_dipakai' => '2024-01-15'],
            ['nama_kantor' => 'Notaris & PPAT Sari Wahyuni', 'layanan' => 'PPAT, Akta Perjanjian', 'kontak' => '021-5559876', 'email' => 'sari.ppat@email.com', 'terakhir_dipakai' => '2023-11-05'],
            ['nama_kantor' => 'Kantor Notaris Budi Prasetyo, MKn', 'layanan' => 'Akta Rapat, Akta Kuasa', 'kontak' => '021-7774567', 'email' => 'budi.mkn@email.com', 'terakhir_dipakai' => '2024-03-20'],
            ['nama_kantor' => 'Notaris Dewi Lestari, SH, MH', 'layanan' => 'Semua Layanan Notaris', 'kontak' => '022-4441122', 'email' => 'dewi.notaris@email.com', 'terakhir_dipakai' => null],
            ['nama_kantor' => 'PPAT & Notaris Agus Salim', 'layanan' => 'PPAT, Akta Perubahan', 'kontak' => '0274-8889900', 'email' => 'agus.ppat@email.com', 'terakhir_dipakai' => '2023-08-10'],
        ];
        foreach ($data as $item) {
            DaftarNotaris::create($item);
        }
    }
}
