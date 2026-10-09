<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixPembayaranItemDecisions extends Command
{
    protected $signature = 'fix:pembayaran-item-decisions {--dry-run : Preview changes without applying}';
    protected $description = 'Fix item_decisions structure in existing Pembayaran records';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be saved');
            $this->newLine();
        }

        // Target pembayaran dengan source_type yang support per-item approval
        $supportedTypes = ['service_asuransi', 'service_part', 'service_incident', 'gps', 'gps_perpanjang'];
        
        $pembayarans = Pembayaran::whereIn('source_type', $supportedTypes)
            ->whereNotNull('source_data')
            ->get();

        $this->info("Found {$pembayarans->count()} pembayaran records to check");
        $this->newLine();

        $fixedCount = 0;
        $skippedCount = 0;

        foreach ($pembayarans as $pembayaran) {
            $sourceData = $pembayaran->source_data ?? [];
            $itemDecisions = $sourceData['item_decisions'] ?? [];
            
            if (empty($itemDecisions)) {
                // Tidak ada item_decisions, skip
                $skippedCount++;
                continue;
            }

            $needsFix = false;
            $fixedDecisions = [];
            $seenIdx = [];

            // Cek apakah ada duplikat idx
            foreach ($itemDecisions as $decision) {
                $idx = (int)($decision['idx'] ?? -1);
                
                if ($idx === -1) {
                    $needsFix = true;
                    $this->warn("  ⚠️  {$pembayaran->no_pr}: Found decision without valid idx");
                    continue;
                }

                if (isset($seenIdx[$idx])) {
                    $needsFix = true;
                    $this->warn("  ⚠️  {$pembayaran->no_pr}: Found duplicate idx {$idx}");
                    
                    // Prioritas: keep yang approved, atau yang paling baru
                    if ($decision['action'] === 'approved') {
                        $fixedDecisions[$idx] = array_merge($decision, ['idx' => $idx]);
                    } elseif (!isset($fixedDecisions[$idx]) || $fixedDecisions[$idx]['action'] !== 'approved') {
                        $fixedDecisions[$idx] = array_merge($decision, ['idx' => $idx]);
                    }
                } else {
                    $seenIdx[$idx] = true;
                    $fixedDecisions[$idx] = array_merge($decision, ['idx' => $idx]);
                }
            }

            if ($needsFix) {
                $this->info("  🔧 Fixing {$pembayaran->no_pr}:");
                $this->line("     Before: " . count($itemDecisions) . " decisions");
                $this->line("     After:  " . count($fixedDecisions) . " decisions");
                
                // Reindex array to sequential (0,1,2...) but keep 'idx' field intact
                $fixedDecisions = array_values($fixedDecisions);
                
                if (!$isDryRun) {
                    $sourceData['item_decisions'] = $fixedDecisions;
                    $pembayaran->update(['source_data' => $sourceData]);
                    $this->info("     ✅ Fixed and saved");
                } else {
                    $this->line("     📋 Would save: " . json_encode($fixedDecisions, JSON_PRETTY_PRINT));
                }
                
                $fixedCount++;
                $this->newLine();
            } else {
                $skippedCount++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  Fixed: {$fixedCount}");
        $this->line("  Skipped (no issues): {$skippedCount}");
        
        if ($isDryRun && $fixedCount > 0) {
            $this->newLine();
            $this->warn("This was a DRY RUN. Run without --dry-run to apply changes:");
            $this->line("  php artisan fix:pembayaran-item-decisions");
        }

        return 0;
    }
}
