<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pembayaran;
use App\Http\Controllers\Admin\PembayaranController;

class RecalculatePembayaranLimitSnapshot extends Command
{
    protected $signature = 'pembayaran:recalc-limit-snapshot
        {--dry-run   : Tampilkan saja pembayaran yang akan diproses tanpa update}
        {--id=       : Batasi ke pembayaran_id tertentu}
        {--status=   : Filter status (default: Disetujui)}';

    protected $description = 'Recalculate keterangan_limit & sisa_pasang di source_data Pembayaran service lama setelah fix query aktifCount';

    public function handle(): int
    {
        $dryRun    = (bool) $this->option('dry-run');
        $targetId  = $this->option('id');
        $statusOpt = $this->option('status');

        // Default: semua yang sudah diproses (disetujui penuh atau sebagian)
        $statuses = $statusOpt
            ? array_map('trim', explode(',', $statusOpt))
            : ['Disetujui'];

        $query = Pembayaran::whereIn('source_type', ['service_part', 'service_incident'])
            ->whereIn('status', $statuses)
            ->whereNotNull('source_data');

        if ($targetId) {
            $query->where('id', (int) $targetId);
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('Tidak ada pembayaran yang perlu diproses.');
            return 0;
        }

        $this->info("Ditemukan {$total} pembayaran (source_type: service_part/service_incident, status: " . implode(', ', $statuses) . ').');

        if ($dryRun) {
            $this->warn('Mode dry-run aktif — tidak ada yang akan diubah.');
            $query->each(function (Pembayaran $p) {
                $partCount = count($p->source_data['parts'] ?? []);
                $this->line("  [ID {$p->id}] {$p->no_pr} | {$p->status} | {$p->source_type} | {$partCount} parts");
            });
            return 0;
        }

        $ctrl    = app(PembayaranController::class);
        $updated = 0;
        $failed  = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunk(50, function ($records) use ($ctrl, &$updated, &$failed, $bar) {
            foreach ($records as $pembayaran) {
                try {
                    $ctrl->updateSourceDataKeteranganLimit($pembayaran);
                    $updated++;
                } catch (\Throwable $e) {
                    $failed++;
                    $this->newLine();
                    $this->error("  Gagal ID {$pembayaran->id} ({$pembayaran->no_pr}): " . $e->getMessage());
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Selesai. Updated: {$updated}" . ($failed > 0 ? ", Gagal: {$failed}" : '') . '.');

        return $failed > 0 ? 1 : 0;
    }
}
