<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SignatureDokumenSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['document_id' => 'DOC-2026-001', 'jenis_dokumen' => 'Kontrak',    'tanggal' => '2026-01-20', 'pihak_terlibat' => 'PT Maju Bersama & PT APY Rent',    'status_ttd' => 'Ditandatangani', 'platform_digisign' => 'PrivyID',    'catatan' => 'Kontrak sewa 3 bulan'],
            ['document_id' => 'DOC-2026-002', 'jenis_dokumen' => 'Perjanjian', 'tanggal' => '2026-02-01', 'pihak_terlibat' => 'CV Karya Indah & PT APY Rent',      'status_ttd' => 'Ditandatangani', 'platform_digisign' => 'DocuSign',   'catatan' => 'PKS layanan transportasi'],
            ['document_id' => 'DOC-2026-003', 'jenis_dokumen' => 'MOU',        'tanggal' => '2026-02-15', 'pihak_terlibat' => 'PT Global Trans & PT APY Rent',     'status_ttd' => 'Menunggu',       'platform_digisign' => 'PrivyID',    'catatan' => 'Menunggu tanda tangan direktur'],
            ['document_id' => 'DOC-2026-004', 'jenis_dokumen' => 'Penawaran',  'tanggal' => '2026-03-01', 'pihak_terlibat' => 'PT Nusantara Raya & PT APY Rent',   'status_ttd' => 'Ditandatangani', 'platform_digisign' => 'Adobe Sign', 'catatan' => 'Penawaran disetujui'],
            ['document_id' => 'DOC-2026-005', 'jenis_dokumen' => 'Kontrak',    'tanggal' => '2026-03-15', 'pihak_terlibat' => 'CV Jaya Mandiri & PT APY Rent',     'status_ttd' => 'Ditolak',        'platform_digisign' => 'PrivyID',    'catatan' => 'Ditolak karena klausul tidak sesuai'],
            ['document_id' => 'DOC-2026-006', 'jenis_dokumen' => 'Perjanjian', 'tanggal' => '2026-04-01', 'pihak_terlibat' => 'PT Berlian Trans & PT APY Rent',    'status_ttd' => 'Ditandatangani', 'platform_digisign' => 'Peruri',     'catatan' => 'PKS jangka panjang'],
            ['document_id' => 'DOC-2026-007', 'jenis_dokumen' => 'Kontrak',    'tanggal' => '2026-04-20', 'pihak_terlibat' => 'PT Prima Raya & PT APY Rent',       'status_ttd' => 'Menunggu',       'platform_digisign' => 'DocuSign',   'catatan' => 'Dalam proses review'],
            ['document_id' => 'DOC-2026-008', 'jenis_dokumen' => 'MOU',        'tanggal' => '2026-05-05', 'pihak_terlibat' => 'PT Sejahtera Abadi & PT APY Rent',  'status_ttd' => 'Ditandatangani', 'platform_digisign' => 'Manual',     'catatan' => 'Ditandatangani secara fisik'],
            ['document_id' => 'DOC-2026-009', 'jenis_dokumen' => 'Lainnya',    'tanggal' => '2026-05-20', 'pihak_terlibat' => 'CV Mitra Logistik & PT APY Rent',   'status_ttd' => 'Menunggu',       'platform_digisign' => 'PrivyID',    'catatan' => 'Surat kuasa armada'],
            ['document_id' => 'DOC-2026-010', 'jenis_dokumen' => 'Kontrak',    'tanggal' => '2026-06-01', 'pihak_terlibat' => 'PT Sinar Harapan & PT APY Rent',    'status_ttd' => 'Ditandatangani', 'platform_digisign' => 'Adobe Sign', 'catatan' => 'Kontrak perpanjangan'],
        ];

        foreach ($data as $row) {
            DB::table('signature_dokumens')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
