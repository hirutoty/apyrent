<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HrdFile;

class HrdFileSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = [
            'Budi Santoso',    'Siti Rahayu',     'Agus Wibowo',
            'Dewi Kusuma',     'Rini Apriani',    'Eko Prasetyo',
            'Hendra Gunawan',  'Rizky Fadillah',  'Yusuf Hidayat',
            'Linda Permata',   'Wahyu Nugroho',   'Fitri Handayani',
            'Dody Kurniawan',  'Teguh Santosa',   'Arif Budiman',
        ];

        // Setiap pegawai memiliki beberapa jenis dokumen
        $dokumen = [
            ['KTP',          'ktp',       'Kartu Tanda Penduduk'],
            ['NPWP',         'npwp',      'Nomor Pokok Wajib Pajak'],
            ['Ijazah',       'ijazah',    'Ijazah pendidikan terakhir'],
            ['SK Pengangkatan','sk',      'Surat Keputusan Pengangkatan Pegawai'],
            ['Kontrak Kerja','kontrak',   'Perjanjian kerja yang telah ditandatangani'],
            ['BPJS Kesehatan','bpjs_kes', 'Kartu BPJS Kesehatan'],
            ['BPJS TK',      'bpjs_tk',  'Kartu BPJS Ketenagakerjaan'],
        ];

        foreach ($pegawai as $p) {
            // Setiap pegawai dapat 3-5 dokumen secara acak
            $jumlah = rand(3, count($dokumen));
            $keys   = array_rand($dokumen, $jumlah);
            if (!is_array($keys)) $keys = [$keys];

            foreach ($keys as $k) {
                [$jenis, $prefix, $ket] = $dokumen[$k];
                $slug = strtolower(str_replace(' ', '_', $p));

                HrdFile::create([
                    'nama_pegawai'  => $p,
                    'nama_file'     => "{$jenis} - {$p}",
                    'jenis_dokumen' => $jenis,
                    'file_path'     => "hrd_files/{$slug}/{$prefix}_{$slug}.pdf",
                    'keterangan'    => $ket,
                ]);
            }
        }
    }
}
