<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrmProspekSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode_prospek' => 'PRO-001', 'nama_kontak' => 'Budi Santoso',    'perusahaan' => 'PT Maju Bersama',    'telepon' => '0812-1111-1111', 'tahapan' => 'Prospek',   'status' => 'Aktif',       'sales' => 'Andi',   'tanggal_masuk' => '2026-01-10', 'catatan' => 'Butuh armada 5 unit'],
            ['kode_prospek' => 'PRO-002', 'nama_kontak' => 'Siti Rahayu',     'perusahaan' => 'CV Karya Indah',     'telepon' => '0813-2222-2222', 'tahapan' => 'Negosiasi', 'status' => 'Aktif',       'sales' => 'Budi',   'tanggal_masuk' => '2026-02-05', 'catatan' => 'Diskusi harga sudah selesai'],
            ['kode_prospek' => 'PRO-003', 'nama_kontak' => 'Ahmad Fauzi',     'perusahaan' => 'PT Sejahtera Abadi', 'telepon' => '0814-3333-3333', 'tahapan' => 'Closing',   'status' => 'Aktif',       'sales' => 'Cici',   'tanggal_masuk' => '2026-02-20', 'catatan' => 'Kontrak siap ditandatangani'],
            ['kode_prospek' => 'PRO-004', 'nama_kontak' => 'Dewi Lestari',    'perusahaan' => 'PT Global Trans',    'telepon' => '0815-4444-4444', 'tahapan' => 'Prospek',   'status' => 'Aktif',       'sales' => 'Andi',   'tanggal_masuk' => '2026-03-01', 'catatan' => 'Masih dalam penjajakan'],
            ['kode_prospek' => 'PRO-005', 'nama_kontak' => 'Rudi Hartono',    'perusahaan' => 'CV Jaya Mandiri',    'telepon' => '0816-5555-5555', 'tahapan' => 'Negosiasi', 'status' => 'Aktif',       'sales' => 'Dani',   'tanggal_masuk' => '2026-03-15', 'catatan' => 'Negosiasi tenor kontrak'],
            ['kode_prospek' => 'PRO-006', 'nama_kontak' => 'Lia Permata',     'perusahaan' => 'PT Nusantara Raya',  'telepon' => '0817-6666-6666', 'tahapan' => 'Closing',   'status' => 'Aktif',       'sales' => 'Budi',   'tanggal_masuk' => '2026-04-02', 'catatan' => 'Deal 3 unit minibus'],
            ['kode_prospek' => 'PRO-007', 'nama_kontak' => 'Hendra Wijaya',   'perusahaan' => 'PT Sinar Harapan',   'telepon' => '0818-7777-7777', 'tahapan' => 'Prospek',   'status' => 'Tidak Aktif', 'sales' => 'Cici',   'tanggal_masuk' => '2026-04-10', 'catatan' => 'Tidak merespon lagi'],
            ['kode_prospek' => 'PRO-008', 'nama_kontak' => 'Maya Anggraini',  'perusahaan' => 'CV Mitra Logistik',  'telepon' => '0819-8888-8888', 'tahapan' => 'Negosiasi', 'status' => 'Aktif',       'sales' => 'Andi',   'tanggal_masuk' => '2026-05-01', 'catatan' => 'Menunggu approval direksi'],
            ['kode_prospek' => 'PRO-009', 'nama_kontak' => 'Fajar Nugroho',   'perusahaan' => 'PT Berlian Trans',   'telepon' => '0821-9999-9999', 'tahapan' => 'Closing',   'status' => 'Aktif',       'sales' => 'Dani',   'tanggal_masuk' => '2026-05-20', 'catatan' => 'Siap kontrak'],
            ['kode_prospek' => 'PRO-010', 'nama_kontak' => 'Indah Kusuma',    'perusahaan' => 'PT Prima Raya',      'telepon' => '0822-1010-1010', 'tahapan' => 'Prospek',   'status' => 'Aktif',       'sales' => 'Budi',   'tanggal_masuk' => '2026-06-05', 'catatan' => 'Prospek baru dari referral'],
        ];

        foreach ($data as $row) {
            DB::table('crm_prospeks')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
