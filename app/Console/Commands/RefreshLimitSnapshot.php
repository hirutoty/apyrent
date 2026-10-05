<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseOrder;
use App\Models\Pembayaran;
use App\Models\ServiceCategoryLimit;
use App\Http\Controllers\Admin\ServiceHistoryController;

class RefreshLimitSnapshot extends Command
{
    protected $signature = 'limit:refresh-snapshot
                            {--model=all : Target model (all, po, pembayaran)}
                            {--force    : Perbarui semua, termasuk yang sudah punya sisa_limit_biaya}';

    protected $description = 'Recalculate sisa_limit_biaya & sisa_limit_km di limit_snapshot pada PurchaseOrder dan Pembayaran';

    private ServiceHistoryController $shCtrl;

    public function handle(): int
    {
        $this->shCtrl = app(ServiceHistoryController::class);
        $model  = $this->option('model');
        $force  = (bool) $this->option('force');

        if (in_array($model, ['all', 'po'])) {
            $this->processModel(PurchaseOrder::class, 'PurchaseOrder', $force);
        }

        if (in_array($model, ['all', 'pembayaran'])) {
            $this->processModel(Pembayaran::class, 'Pembayaran', $force);
        }

        $this->info('Selesai.');
        return 0;
    }

    private function processModel(string $modelClass, string $label, bool $force): void
    {
        $this->info("Memproses {$label}...");

        $query = $modelClass::whereNotNull('source_data')
            ->where('source_type', 'service_part');

        $total   = $query->count();
        $updated = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunk(100, function ($records) use (&$updated, &$skipped, $force, $bar) {
            foreach ($records as $record) {
                $sourceData = is_array($record->source_data)
                    ? $record->source_data
                    : json_decode($record->source_data, true);

                if (empty($sourceData['parts']) || !is_array($sourceData['parts'])) {
                    $bar->advance();
                    $skipped++;
                    continue;
                }

                $changed = false;

                foreach ($sourceData['parts'] as &$part) {
                    $snap = $part['limit_snapshot'] ?? null;
                    if (!$snap) {
                        continue;
                    }

                    // Skip jika sudah ada semua field baru & tidak force
                    if (!$force
                        && array_key_exists('sisa_limit_biaya', $snap)
                        && array_key_exists('tgl_limit_interval', $snap)) {
                        continue;
                    }

                    $limitBiaya = (int) ($snap['limit_biaya'] ?? 0);
                    $limitKm    = isset($snap['limit_km_target']) ? (int) $snap['limit_km_target'] : null;
                    $serviceKm  = (int) ($snap['service_km'] ?? 0);

                    // ── Sisa limit biaya ──────────────────────────────────
                    $sisaLimitBiaya = null;
                    if ($limitBiaya > 0) {
                        $kendaraanId = $sourceData['kendaraan_id'] ?? null;
                        $categoryId  = $part['category_id'] ?? null;
                        $tglPasang   = $part['tgl_pasang'] ?? ($sourceData['tanggal_service'] ?? now()->toDateString());

                        $limitRule = ($kendaraanId && $categoryId)
                            ? ServiceCategoryLimit::where('kendaraan_id', $kendaraanId)
                                ->where('category_id', $categoryId)
                                ->first()
                            : null;

                        if ($limitRule && $limitRule->limit_nilai) {
                            $kumulatif = $this->shCtrl->getKumulatifBiayaKategori(
                                (int) $kendaraanId,
                                (int) $categoryId,
                                (int) $limitRule->limit_nilai,
                                $limitRule->limit_satuan ?? 'bulan',
                                $limitBiaya,
                                $tglPasang,
                                $limitRule->reset_at?->toDateString()
                            );

                            // reset_at ada tapi belum ada part setelah reset → periode baru
                            if ($kumulatif === null && $limitRule->reset_at) {
                                $kumulatif = ['total_dalam_periode' => 0, 'sisa_limit' => $limitBiaya];
                            }

                            if ($kumulatif !== null) {
                                // sisa = limit - total_yang_sudah_terpakai (tidak termasuk biaya baru)
                                $sisaLimitBiaya = $limitBiaya - $kumulatif['total_dalam_periode'];
                            }
                        }
                    }

                    // ── Sisa limit KM ─────────────────────────────────────
                    $sisaLimitKm = ($limitKm !== null) ? ($limitKm - $serviceKm) : null;

                    // ── Tanggal batas interval (tgl_limit_interval) ────────
                    // Hitung dari tgl_pasang + interval yang tersimpan di snap
                    $tglLimitInterval = null;
                    $intervalNilai    = (int) ($part['interval_nilai'] ?? 0);
                    $intervalSatuan   = $part['interval_satuan'] ?? 'bulan';
                    if ($intervalNilai > 0) {
                        $tglPasangCarbon = \Carbon\Carbon::parse(
                            $part['tgl_pasang'] ?? ($sourceData['tanggal_service'] ?? now()->toDateString())
                        );
                        $tglLimitCarbon = match ($intervalSatuan) {
                            'hari'   => (clone $tglPasangCarbon)->addDays($intervalNilai),
                            'minggu' => (clone $tglPasangCarbon)->addWeeks($intervalNilai),
                            'tahun'  => (clone $tglPasangCarbon)->addYears($intervalNilai),
                            default  => (clone $tglPasangCarbon)->addMonths($intervalNilai),
                        };
                        $tglLimitInterval = $tglLimitCarbon->format('Y-m-d');
                    }

                    $part['limit_snapshot']['sisa_limit_biaya']  = $sisaLimitBiaya;
                    $part['limit_snapshot']['sisa_limit_km']     = $sisaLimitKm;
                    $part['limit_snapshot']['tgl_limit_interval'] = $tglLimitInterval;

                    // Perbaiki biaya_lewat & biaya_sama berdasarkan keterangan_limit yang tersimpan.
                    // keterangan_limit adalah sumber kebenaran yang dibuat saat PO/pembayaran pertama kali disubmit.
                    // Lebih akurat daripada recalculate karena konteks periode mungkin sudah berubah.
                    $ketLimit = trim($part['keterangan_limit'] ?? '');
                    if ($ketLimit !== '' && $ketLimit !== '-') {
                        $ketLower = strtolower($ketLimit);
                        // Parse dari keterangan_limit: cari segmen yang membahas biaya
                        $segments = array_filter(array_map('trim', explode(',', $ketLimit)));
                        $biayaLewat = false;
                        $biayaSama  = false;
                        foreach ($segments as $seg) {
                            $segLower = strtolower($seg);
                            if (str_contains($segLower, 'biaya')) {
                                if (str_contains($segLower, 'melebihi')) {
                                    $biayaLewat = true;
                                } elseif (str_contains($segLower, 'mencapai')) {
                                    $biayaSama  = true;
                                }
                                break;
                            }
                        }
                        $part['limit_snapshot']['biaya_lewat'] = $biayaLewat;
                        $part['limit_snapshot']['biaya_sama']  = $biayaSama;
                    } elseif ($sisaLimitBiaya !== null) {
                        // Tidak ada keterangan_limit → hitung dari sisa
                        $biayaPart  = (int) ($snap['service_biaya'] ?? 0);
                        $sudahApprove = in_array($record->status ?? '', ['Disetujui', 'Ditolak']);
                        if ($sudahApprove) {
                            // Part sudah di DB: sisa sudah dipotong biaya ini
                            $part['limit_snapshot']['biaya_lewat'] = $sisaLimitBiaya < 0;
                            $part['limit_snapshot']['biaya_sama']  = $sisaLimitBiaya === 0;
                        } else {
                            // Part belum di DB: kurangi sisa dengan biaya ini
                            $sisaSetelah = $sisaLimitBiaya - $biayaPart;
                            $part['limit_snapshot']['biaya_lewat'] = $sisaSetelah < 0;
                            $part['limit_snapshot']['biaya_sama']  = $sisaSetelah === 0;
                        }
                    }

                    $changed = true;
                }
                unset($part);

                if ($changed) {
                    $record->source_data = $sourceData;
                    $record->saveQuietly();
                    $updated++;
                } else {
                    $skipped++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->line("  ✓ Updated: {$updated}, Skipped: {$skipped}");
    }
}
