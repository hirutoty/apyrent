<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StrukturOrganisasi;
use Carbon\Carbon;

class StrukturOrganisasiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Direksi
            ['Direktur Utama',      'Budi Santoso',       'NIP-001', 'Direksi',     null,                'Jakarta',   'Tetap',    '2018-01-02'],
            ['Direktur Operasional','Siti Rahayu',        'NIP-002', 'Direksi',     'Budi Santoso',      'Jakarta',   'Tetap',    '2019-03-01'],
            ['Direktur Keuangan',   'Agus Wibowo',        'NIP-003', 'Direksi',     'Budi Santoso',      'Jakarta',   'Tetap',    '2019-03-01'],

            // HRD
            ['Manager HRD',         'Dewi Kusuma',        'NIP-010', 'HRD',         'Budi Santoso',      'Jakarta',   'Tetap',    '2020-01-15'],
            ['Staff HRD',           'Rini Apriani',       'NIP-011', 'HRD',         'Dewi Kusuma',       'Jakarta',   'Tetap',    '2021-04-01'],
            ['Staff HRD',           'Eko Prasetyo',       'NIP-012', 'HRD',         'Dewi Kusuma',       'Jakarta',   'Kontrak',  '2023-07-01'],

            // IT
            ['Manager IT',          'Hendra Gunawan',     'NIP-020', 'IT',          'Budi Santoso',      'Jakarta',   'Tetap',    '2020-02-01'],
            ['Developer',           'Rizky Fadillah',     'NIP-021', 'IT',          'Hendra Gunawan',    'Jakarta',   'Kontrak',  '2022-05-01'],
            ['IT Support',          'Yusuf Hidayat',      'NIP-022', 'IT',          'Hendra Gunawan',    'Jakarta',   'Kontrak',  '2023-01-01'],

            // Finance
            ['Manager Finance',     'Linda Permata',      'NIP-030', 'Finance',     'Agus Wibowo',       'Jakarta',   'Tetap',    '2020-06-01'],
            ['Staff Accounting',    'Wahyu Nugroho',      'NIP-031', 'Finance',     'Linda Permata',     'Jakarta',   'Tetap',    '2021-08-01'],
            ['Staff Pajak',         'Fitri Handayani',    'NIP-032', 'Finance',     'Linda Permata',     'Jakarta',   'Kontrak',  '2022-09-01'],

            // Operasional
            ['Manager Operasional', 'Dody Kurniawan',     'NIP-040', 'Operasional', 'Siti Rahayu',       'Surabaya',  'Tetap',    '2019-11-01'],
            ['Supervisor Lapangan', 'Teguh Santosa',      'NIP-041', 'Operasional', 'Dody Kurniawan',    'Surabaya',  'Tetap',    '2021-01-10'],
            ['Teknisi',             'Arif Budiman',       'NIP-042', 'Operasional', 'Teguh Santosa',     'Surabaya',  'Kontrak',  '2023-03-15'],
        ];

        foreach ($data as [$jabatan, $pegawai, $nip, $dept, $atasan, $lokasi, $status, $mulai]) {
            StrukturOrganisasi::updateOrCreate(
                ['nip_id' => $nip],
                [
                    'nama_jabatan'    => $jabatan,
                    'nama_pegawai'    => $pegawai,
                    'nip_id'          => $nip,
                    'departemen'      => $dept,
                    'atasan_langsung' => $atasan,
                    'lokasi'          => $lokasi,
                    'status_jabatan'  => $status,
                    'tanggal_mulai'   => $mulai,
                ]
            );
        }
    }
}
