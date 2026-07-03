<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Procuremento;

class ProcurementoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Procuremento::create([
            'workflow_id' => 'WF001',
            'nama_workflow' => 'Persetujuan Pengadaan Barang',
            'trigger_event' => 'Pengajuan Barang',
            'syarat_tambahan' => 'Nominal > 5.000.000',
            'aksi_dilakukan' => 'Kirim Email ke Manager',
            'delay_aksi' => '1 Hari',
            'status' => 'Aktif',
            'pic' => 'Procurement',
            'catatan' => 'Workflow approval pengadaan barang.',
        ]);

        Procuremento::create([
            'workflow_id' => 'WF002',
            'nama_workflow' => 'Approval Vendor',
            'trigger_event' => 'Penambahan Vendor Baru',
            'syarat_tambahan' => null,
            'aksi_dilakukan' => 'Kirim Notifikasi ke Admin',
            'delay_aksi' => '30 Menit',
            'status' => 'Aktif',
            'pic' => 'Admin Procurement',
            'catatan' => 'Workflow untuk approval vendor.',
        ]);
    }
}