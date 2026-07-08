<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReviewLegal;

class ReviewLegalSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'tanggal'       => '2024-01-10',
                'pemohon'       => 'Divisi Pengadaan',
                'dokumen'       => 'Draft Kontrak Vendor Baru',
                'status_review' => 'Selesai',
                'pic_legal'     => 'Andi Hukum',
                'catatan'       => 'Telah disetujui dengan beberapa revisi minor',
            ],
            [
                'tanggal'       => '2024-02-15',
                'pemohon'       => 'Divisi HRD',
                'dokumen'       => 'Perjanjian Kerja Karyawan Kontrak',
                'status_review' => 'Proses',
                'pic_legal'     => 'Siti Legal',
                'catatan'       => 'Menunggu konfirmasi dari manajemen',
            ],
            [
                'tanggal'       => '2024-03-05',
                'pemohon'       => 'Direktur Utama',
                'dokumen'       => 'MOU dengan Mitra Strategis',
                'status_review' => 'Selesai',
                'pic_legal'     => 'Andi Hukum',
                'catatan'       => 'Disetujui dan ditandatangani',
            ],
            [
                'tanggal'       => '2024-04-20',
                'pemohon'       => 'Divisi Keuangan',
                'dokumen'       => 'Perjanjian Pinjaman Bank',
                'status_review' => 'Proses',
                'pic_legal'     => 'Budi Legal',
                'catatan'       => null,
            ],
            [
                'tanggal'       => '2024-05-12',
                'pemohon'       => 'Divisi Operasional',
                'dokumen'       => 'Addendum Kontrak Sewa Gedung',
                'status_review' => 'Pending',
                'pic_legal'     => 'Siti Legal',
                'catatan'       => 'Belum diterima dokumennya',
            ],
        ];

        foreach ($data as $item) {
            ReviewLegal::create($item);
        }
    }
}
