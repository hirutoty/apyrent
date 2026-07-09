<?php

namespace Database\Seeders;

use App\Models\Otomatisasi;
use Illuminate\Database\Seeder;

class OtomatisasiSeeder extends Seeder
{
    public function run(): void
    {
        Otomatisasi::create([
            'workflow_id' => 'WF001',
            'nama_workflow' => 'Welcome Email',
            'trigger_event' => 'Registrasi Baru',
            'syarat_tambahan' => 'Member baru',
            'aksi' => 'Kirim Email Selamat Datang',
            'delay_aksi' => '10 menit',
            'status' => 'Aktif',
            'pic' => 'System',
            'catatan' => 'Auto-email untuk member baru',
        ]);

        Otomatisasi::create([
            'workflow_id' => 'WF002',
            'nama_workflow' => 'Reminder Pembayaran',
            'trigger_event' => 'H-2 Jatuh Tempo',
            'syarat_tambahan' => 'Belum bayar',
            'aksi' => 'Kirim Notifikasi WA',
            'delay_aksi' => 'Langsung',
            'status' => 'Aktif',
            'pic' => 'Finance',
            'catatan' => 'Pengingat otomatis pembayaran',
        ]);
    }
}

