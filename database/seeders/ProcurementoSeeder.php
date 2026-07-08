<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Procuremento;

class ProcurementoSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'workflow_id'    => 'WF001',
                'nama_workflow'  => 'Persetujuan Pengadaan Barang',
                'trigger_event'  => 'Pengajuan Barang',
                'syarat_tambahan'=> 'Nominal > 5.000.000',
                'aksi_dilakukan' => 'Kirim Email ke Manager',
                'delay_aksi'     => '1 Hari',
                'status'         => 'Aktif',
                'pic'            => 'Procurement',
                'catatan'        => 'Workflow approval pengadaan barang.',
            ],
            [
                'workflow_id'    => 'WF002',
                'nama_workflow'  => 'Approval Vendor Baru',
                'trigger_event'  => 'Penambahan Vendor Baru',
                'syarat_tambahan'=> null,
                'aksi_dilakukan' => 'Kirim Notifikasi ke Admin',
                'delay_aksi'     => '30 Menit',
                'status'         => 'Aktif',
                'pic'            => 'Admin Procurement',
                'catatan'        => 'Workflow untuk approval vendor.',
            ],
            [
                'workflow_id'    => 'WF003',
                'nama_workflow'  => 'Review Purchase Request',
                'trigger_event'  => 'PR Diajukan',
                'syarat_tambahan'=> 'Qty > 100 pcs',
                'aksi_dilakukan' => 'Kirim ke Manajer Gudang',
                'delay_aksi'     => '2 Jam',
                'status'         => 'Aktif',
                'pic'            => 'Manajer Gudang',
                'catatan'        => 'Workflow review permintaan barang dari gudang.',
            ],
            [
                'workflow_id'    => 'WF004',
                'nama_workflow'  => 'Approval Kontrak Vendor',
                'trigger_event'  => 'Kontrak Baru Dibuat',
                'syarat_tambahan'=> 'Nilai Kontrak > 50.000.000',
                'aksi_dilakukan' => 'Kirim Email ke Direktur',
                'delay_aksi'     => '1 Hari',
                'status'         => 'Aktif',
                'pic'            => 'Legal & Finance',
                'catatan'        => 'Persetujuan kontrak vendor bernilai besar.',
            ],
            [
                'workflow_id'    => 'WF005',
                'nama_workflow'  => 'Notifikasi Stok Menipis',
                'trigger_event'  => 'Stok < Minimum',
                'syarat_tambahan'=> null,
                'aksi_dilakukan' => 'Kirim Alert ke Procurement',
                'delay_aksi'     => 'Langsung',
                'status'         => 'Aktif',
                'pic'            => 'Procurement',
                'catatan'        => 'Otomatis kirim notifikasi saat stok mendekati batas minimum.',
            ],
            [
                'workflow_id'    => 'WF006',
                'nama_workflow'  => 'Evaluasi Vendor Periodik',
                'trigger_event'  => 'Akhir Bulan',
                'syarat_tambahan'=> 'Rating < 3',
                'aksi_dilakukan' => 'Kirim Laporan ke Manager',
                'delay_aksi'     => '1 Hari',
                'status'         => 'Aktif',
                'pic'            => 'Procurement',
                'catatan'        => 'Evaluasi performa vendor setiap bulan.',
            ],
            [
                'workflow_id'    => 'WF007',
                'nama_workflow'  => 'Approval Pembelian Aset',
                'trigger_event'  => 'Pengajuan Pembelian Aset',
                'syarat_tambahan'=> 'Nilai > 100.000.000',
                'aksi_dilakukan' => 'Kirim ke Komite Anggaran',
                'delay_aksi'     => '3 Hari',
                'status'         => 'Nonaktif',
                'pic'            => 'Finance & Direktur',
                'catatan'        => 'Pembelian aset besar perlu persetujuan komite.',
            ],
            [
                'workflow_id'    => 'WF008',
                'nama_workflow'  => 'Reminder Jatuh Tempo Kontrak',
                'trigger_event'  => 'H-30 Kontrak Berakhir',
                'syarat_tambahan'=> null,
                'aksi_dilakukan' => 'Kirim Email Reminder',
                'delay_aksi'     => 'Langsung',
                'status'         => 'Aktif',
                'pic'            => 'Procurement',
                'catatan'        => 'Pengingat otomatis sebelum kontrak vendor habis.',
            ],
        ];

        foreach ($data as $item) {
            Procuremento::updateOrCreate(
                ['workflow_id' => $item['workflow_id']],
                $item
            );
        }
    }
}
