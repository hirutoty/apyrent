<?php

namespace Database\Seeders;

use App\Models\DataLeasing;
use App\Models\DataKontrak;
use Illuminate\Database\Seeder;

class DataLeasingSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah sudah ada data
        if (DataLeasing::count() > 0) {
            $this->command->info('Data Leasing sudah ada, skip seeding.');
            return;
        }

        // Ambil beberapa data kontrak untuk referensi
        $kontraks = DataKontrak::limit(5)->get();

        if ($kontraks->isEmpty()) {
            $this->command->warn('Tidak ada Data Kontrak, membuat data leasing tanpa referensi kontrak.');
            
            // Buat 5 data leasing dummy tanpa referensi
            $data = [
                [
                    'no_kontrak' => 'LES/2026/001',
                    'mobil' => 'Toyota Avanza',
                    'tahun' => '2024',
                    'nopol' => 'B 1234 ABC',
                    'user_leasing' => 'PT. ABC Finance',
                    'angsuran_per_bulan' => 5000000,
                    'jatuh_tempo' => 10,
                    'periode_mulai' => '2024-01-01',
                    'periode_selesai' => '2026-12-01',
                    'personal_account' => 'Bank BCA',
                    'sumber_dana_debit' => 'BCA 1234567890',
                    'cara_bayar' => 'Auto Debit',
                    'asuransi_leasing' => 'Asuransi Astra',
                ],
                [
                    'no_kontrak' => 'LES/2026/002',
                    'mobil' => 'Honda Jazz',
                    'tahun' => '2023',
                    'nopol' => 'B 5678 XYZ',
                    'user_leasing' => 'PT. XYZ Leasing',
                    'angsuran_per_bulan' => 6500000,
                    'jatuh_tempo' => 15,
                    'periode_mulai' => '2024-06-01',
                    'periode_selesai' => '2027-05-01',
                    'personal_account' => 'Bank Mandiri',
                    'sumber_dana_debit' => 'Mandiri 9876543210',
                    'cara_bayar' => 'Transfer Manual',
                    'asuransi_leasing' => 'Jasindo',
                ],
                [
                    'no_kontrak' => 'LES/2026/003',
                    'mobil' => 'Mitsubishi Xpander',
                    'tahun' => '2025',
                    'nopol' => 'B 9012 DEF',
                    'user_leasing' => 'PT. DEF Finance',
                    'angsuran_per_bulan' => 7000000,
                    'jatuh_tempo' => 20,
                    'periode_mulai' => '2025-01-01',
                    'periode_selesai' => '2028-12-01',
                    'personal_account' => 'Bank BRI',
                    'sumber_dana_debit' => 'BRI 1122334455',
                    'cara_bayar' => 'Auto Debit',
                    'asuransi_leasing' => 'Allianz',
                ],
                [
                    'no_kontrak' => 'LES/2026/004',
                    'mobil' => 'Suzuki Ertiga',
                    'tahun' => '2024',
                    'nopol' => 'B 3456 GHI',
                    'user_leasing' => 'PT. GHI Leasing',
                    'angsuran_per_bulan' => 4500000,
                    'jatuh_tempo' => 5,
                    'periode_mulai' => '2024-03-01',
                    'periode_selesai' => '2027-02-01',
                    'personal_account' => 'Bank BNI',
                    'sumber_dana_debit' => 'BNI 5566778899',
                    'cara_bayar' => 'Transfer Manual',
                    'asuransi_leasing' => 'Sinarmas',
                ],
                [
                    'no_kontrak' => 'LES/2026/005',
                    'mobil' => 'Daihatsu Terios',
                    'tahun' => '2023',
                    'nopol' => 'B 7890 JKL',
                    'user_leasing' => 'PT. JKL Finance',
                    'angsuran_per_bulan' => 5500000,
                    'jatuh_tempo' => 25,
                    'periode_mulai' => '2023-10-01',
                    'periode_selesai' => '2026-09-01',
                    'personal_account' => 'Bank CIMB',
                    'sumber_dana_debit' => 'CIMB 6677889900',
                    'cara_bayar' => 'Auto Debit',
                    'asuransi_leasing' => 'Jasa Raharja',
                ],
            ];

            foreach ($data as $item) {
                DataLeasing::create($item);
            }

            $this->command->info('5 data leasing dummy berhasil ditambahkan.');
            return;
        }

        // Jika ada data kontrak, gunakan untuk referensi
        foreach ($kontraks as $kontrak) {
            DataLeasing::create([
                'data_kontrak_id' => $kontrak->id,
                'no_kontrak' => $kontrak->no_kontrak,
                'mobil' => $kontrak->mobil,
                'tahun' => $kontrak->tahun,
                'nopol' => $kontrak->nopol,
                'user_leasing' => $kontrak->user_kontrak,
                'angsuran_per_bulan' => $kontrak->angsuran_per_bulan,
                'jatuh_tempo' => $kontrak->jatuh_tempo ?? 10,
                'periode_mulai' => $kontrak->periode_mulai,
                'periode_selesai' => $kontrak->periode_selesai,
                'personal_account' => $kontrak->personal_account,
                'sumber_dana_debit' => $kontrak->sumber_dana_debit,
                'cara_bayar' => $kontrak->cara_bayar,
                'asuransi_leasing' => $kontrak->nama_asuransi,
            ]);
        }

        $this->command->info(count($kontraks) . ' data leasing berhasil ditambahkan dari data kontrak.');
    }
}
