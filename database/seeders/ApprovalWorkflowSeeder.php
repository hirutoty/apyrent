<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApprovalWorkflow;
use Carbon\Carbon;

class ApprovalWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $jabatan     = ['Supervisor Pembelian', 'Manager Operasional', 'Manager Keuangan', 'Direktur'];
        $approvers   = ['Budi Santoso', 'Rina Wulandari', 'Agus Prasetyo', 'Dewi Kusuma', 'Hendra Wijaya'];
        $statusList  = ['Pending', 'Approved', 'Rejected'];

        // Buat 2 urutan approval untuk 15 PO pertama
        $entry = 1;
        for ($po = 1; $po <= 15; $po++) {
            $idPo = 'PO-' . str_pad($po, 3, '0', STR_PAD_LEFT);

            for ($urutan = 1; $urutan <= 2; $urutan++) {
                $status  = $statusList[($entry - 1) % count($statusList)];
                $tanggal = $status !== 'Pending'
                    ? Carbon::now()->subDays(rand(1, 60))
                    : null;

                ApprovalWorkflow::updateOrCreate(
                    ['id_po' => $idPo, 'urutan_approval' => $urutan],
                    [
                        'id_po'           => $idPo,
                        'urutan_approval' => $urutan,
                        'jabatan'         => $jabatan[($urutan - 1) % count($jabatan)],
                        'nama_approver'   => $approvers[($entry - 1) % count($approvers)],
                        'tanggal'         => $tanggal,
                        'status_approval' => $status,
                        'catatan'         => $status !== 'Pending'
                            ? 'Review urutan ' . $urutan . ' untuk ' . $idPo
                            : null,
                    ]
                );
                $entry++;
            }
        }
    }
}
