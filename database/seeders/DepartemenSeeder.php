<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departemen;

class DepartemenSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Direksi',         'Budi Santoso',       '2018-01-02',  3,  'Pimpinan tertinggi perusahaan',                  'Aktif'],
            ['HRD',             'Dewi Kusuma',        '2018-06-01',  8,  'Mengelola sumber daya manusia',                  'Aktif'],
            ['IT',              'Hendra Gunawan',     '2019-01-15',  6,  'Pengembangan dan pemeliharaan sistem teknologi',  'Aktif'],
            ['Finance',         'Linda Permata',      '2018-06-01', 10,  'Pengelolaan keuangan dan akuntansi',              'Aktif'],
            ['Operasional',     'Dody Kurniawan',     '2019-03-01', 15,  'Pengelolaan operasional lapangan',                'Aktif'],
            ['Marketing',       'Sari Dewanti',       '2020-02-01',  7,  'Pemasaran dan promosi produk',                   'Aktif'],
            ['Sales',           'Benny Kusuma',       '2020-04-01', 12,  'Penjualan dan hubungan pelanggan',                'Aktif'],
            ['Legal',           'Putri Wulandari',    '2021-01-01',  4,  'Urusan hukum dan kontrak perusahaan',             'Aktif'],
            ['Procurement',     'Bambang Irawan',     '2021-06-01',  5,  'Pengadaan barang dan jasa',                       'Aktif'],
            ['Maintenance',     'Suryono Hadi',       '2019-07-01',  8,  'Pemeliharaan aset dan kendaraan',                 'Aktif'],
            ['R&D',             'Indra Lesmana',      '2022-01-01',  4,  'Riset dan pengembangan produk',                   'Aktif'],
            ['Customer Service','Maya Anggraini',      '2020-09-01',  6,  'Layanan pelanggan',                               'Aktif'],
        ];

        foreach ($data as [$nama, $kepala, $tgl, $posisi, $ket, $status]) {
            Departemen::updateOrCreate(
                ['nama_departemen' => $nama],
                [
                    'nama_departemen'  => $nama,
                    'kepala_departemen'=> $kepala,
                    'tanggal_dibentuk' => $tgl,
                    'jumlah_posisi'    => $posisi,
                    'keterangan'       => $ket,
                    'status_aktif'     => $status,
                ]
            );
        }
    }
}
