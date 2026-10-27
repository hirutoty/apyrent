<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kehilangan;

class FixKehilanganKendaraanStatus extends Command
{
    protected $signature = 'fix:kehilangan-status';
    protected $description = 'Fix status kendaraan untuk semua laporan kehilangan yang ada';

    public function handle()
    {
        $this->info('Memulai perbaikan status kendaraan...');

        // Ambil semua kehilangan dengan status sementara atau permanen
        $kehilangans = Kehilangan::with('kendaraan')
            ->whereIn('status', ['sementara', 'permanen'])
            ->get();

        $updated = 0;
        foreach ($kehilangans as $kehilangan) {
            if ($kehilangan->kendaraan && $kehilangan->kendaraan->status_kendaraan !== 'hilang') {
                $oldStatus = $kehilangan->kendaraan->status_kendaraan;
                $kehilangan->kendaraan->update(['status_kendaraan' => 'hilang']);
                
                $this->info("Kendaraan ID {$kehilangan->kendaraan->id} ({$kehilangan->kendaraan->nopol}): {$oldStatus} → hilang");
                $updated++;
            }
        }

        $this->info("\nSelesai! Total {$updated} kendaraan berhasil diupdate.");
        return Command::SUCCESS;
    }
}
