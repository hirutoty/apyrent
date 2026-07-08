<?php

namespace Database\Seeders;

use App\Models\Kampanye;
use Illuminate\Database\Seeder;

class KampanyeSeeder extends Seeder
{
    public function run(): void
    {
        Kampanye::create([
            'id_kampanye' => 'MKT001',
            'nama_kampanye' => 'Promo Rental Akhir Tahun',
            'tipe_kampanye' => 'Promosi',
            'channel' => 'Email',
            'target_segment' => 'Pelanggan Aktif',
            'tanggal_mulai' => '2026-12-25',
            'tanggal_akhir' => '2026-12-31',
            'subjek_pesan' => 'Diskon Spesial Akhir Tahun!',
            'isi_pesan_ringkas' => 'Dapatkan diskon 20% untuk rental mobil',
            'status' => 'Dijadwalkan',
            'pic' => 'Rina Marketing',
        ]);

        Kampanye::create([
            'id_kampanye' => 'MKT002',
            'nama_kampanye' => 'Re-engagement Campaign',
            'tipe_kampanye' => 'Retensi',
            'channel' => 'WhatsApp',
            'target_segment' => 'Inaktif 6 Bulan',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_akhir' => '2026-08-15',
            'subjek_pesan' => 'Kami Merindukan Anda',
            'isi_pesan_ringkas' => 'Rental lagi dan dapat voucher',
            'status' => 'Aktif',
            'pic' => 'Ahmad Marketing',
        ]);
    }
}
