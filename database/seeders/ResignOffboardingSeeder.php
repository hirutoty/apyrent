<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResignOffboarding;
use Carbon\Carbon;

class ResignOffboardingSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Ahmad Rifai',      'Staff Gudang',          '2024-02-28', 'Mengundurkan diri',           'Selesai', 'Sudah',  'Sudah menyelesaikan serah terima aset dan dokumen'],
            ['Maya Sari',        'Staff Marketing',       '2024-04-15', 'Pindah domisili',             'Selesai', 'Sudah',  'Proses offboarding berjalan lancar'],
            ['Dika Pratama',     'Developer Junior',      '2024-06-01', 'Mendapat tawaran lebih baik', 'Selesai', 'Sudah',  'Akses sistem telah dicabut'],
            ['Sari Utami',       'Staff Finance',         '2024-07-31', 'Melanjutkan studi',           'Selesai', 'Sudah',  'Dokumen exit interview selesai'],
            ['Bowo Setiawan',    'Teknisi Lapangan',      '2024-09-30', 'Kesehatan',                   'Selesai', 'Sudah',  'Serah terima peralatan sudah dilakukan'],
            ['Nita Lestari',     'Admin HR',              '2024-11-15', 'Menikah dan pindah kota',     'Selesai', 'Sudah',  'Semua kewajiban telah diselesaikan'],
            ['Reza Aditya',      'IT Support',            '2025-01-31', 'Mendapat tawaran lebih baik', 'Selesai', 'Sudah',  'Credential akun sudah dinonaktifkan'],
            ['Putri Anggraini',  'Staff Operasional',     '2025-03-15', 'Alasan keluarga',             'Proses',  'Belum',  'Sedang dalam proses serah terima'],
            ['Galih Santoso',    'Supervisor Produksi',   '2025-05-30', 'Pensiun dini',                'Proses',  'Belum',  'Menunggu pengganti untuk serah terima'],
            ['Lina Permatasari', 'Staff Akuntansi',       '2025-06-30', 'Wirausaha',                   'Proses',  'Belum',  'Dalam proses dokumentasi offboarding'],
            ['Bagas Wicaksono',  'Driver',                '2025-07-01', 'Kontrak tidak diperpanjang',  'Proses',  'Belum',  'Mengembalikan kendaraan dinas'],
            ['Rina Kurniawati',  'Customer Service',      '2026-01-31', 'Mengurus anak',               'Proses',  'Belum',  'Exit interview sudah dilakukan'],
        ];

        foreach ($data as [$nama, $jabatan, $tgl, $alasan, $status, $serahTerima, $ket]) {
            ResignOffboarding::create([
                'nama_pegawai'        => $nama,
                'jabatan_terakhir'    => $jabatan,
                'tanggal_resign'      => $tgl,
                'alasan'              => $alasan,
                'status_offboarding'  => $status,
                'serah_terima'        => $serahTerima,
                'keterangan'          => $ket,
            ]);
        }
    }
}
