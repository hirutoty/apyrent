<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LegalDocument;

class LegalDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode'          => 'LD-001',
                'nama_dokumen'  => 'Akta Pendirian Perusahaan',
                'jenis'         => 'Akta Notaris',
                'pihak_terkait' => 'Notaris & Kemenkumham',
                'tgl_terbit'    => '2018-01-15',
                'berlaku_hingga'=> null,
                'status'        => 'Aktif',
                'format'        => 'PDF',
            ],
            [
                'kode'          => 'LD-002',
                'nama_dokumen'  => 'NIB (Nomor Induk Berusaha)',
                'jenis'         => 'Perizinan',
                'pihak_terkait' => 'OSS / BKPM',
                'tgl_terbit'    => '2020-03-10',
                'berlaku_hingga'=> null,
                'status'        => 'Aktif',
                'format'        => 'PDF',
            ],
            [
                'kode'          => 'LD-003',
                'nama_dokumen'  => 'NPWP Perusahaan',
                'jenis'         => 'Pajak',
                'pihak_terkait' => 'Direktorat Jenderal Pajak',
                'tgl_terbit'    => '2018-02-01',
                'berlaku_hingga'=> null,
                'status'        => 'Aktif',
                'format'        => 'PDF',
            ],
            [
                'kode'          => 'LD-004',
                'nama_dokumen'  => 'SIUP (Surat Izin Usaha Perdagangan)',
                'jenis'         => 'Perizinan',
                'pihak_terkait' => 'Dinas Perdagangan',
                'tgl_terbit'    => '2019-06-20',
                'berlaku_hingga'=> '2024-06-20',
                'status'        => 'Kadaluarsa',
                'format'        => 'PDF',
            ],
            [
                'kode'          => 'LD-005',
                'nama_dokumen'  => 'Perjanjian Kerjasama Vendor',
                'jenis'         => 'Kontrak',
                'pihak_terkait' => 'PT Mitra Sejahtera',
                'tgl_terbit'    => '2024-01-05',
                'berlaku_hingga'=> '2026-01-05',
                'status'        => 'Aktif',
                'format'        => 'PDF',
            ],
        ];

        foreach ($data as $item) {
            LegalDocument::create($item);
        }
    }
}
