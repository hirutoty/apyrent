<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServicePart;
use App\Models\ServiceCategoryLimit;
use Carbon\Carbon;

class RecalculateServicePartsKeteranganLimit extends Command
{
    protected $signature   = 'service:recalc-keterangan-limit {--dry-run : Tampilkan saja tanpa update}';
    protected $description = 'Recalculate keterangan_limit untuk semua service_parts yang null atau "-"';

    public function handle(): int
    {
        $parts = ServicePart::whereNull('keterangan_limit')
            ->orWhere('keterangan_limit', '-')
            ->orWhere('keterangan_limit', '')
            ->with(['serviceHistory'])
            ->get();

        $this->info("Ditemukan {$parts->count()} part yang perlu diupdate.");

        if ($parts->isEmpty()) {
            $this->info('Tidak ada yang perlu diupdate.');
            return 0;
        }

        // Pre-load semua limit rules
        $limitRulesMap = ServiceCategoryLimit::all()->keyBy(fn($r) => $r->kendaraan_id . '_' . $r->category_id);

        $updated = 0;
        $bar     = $this->output->createProgressBar($parts->count());
        $bar->start();

        foreach ($parts as $part) {
            $mapKey    = $part->kendaraan_id . '_' . $part->category_id;
            $limitRule = $limitRulesMap->get($mapKey);

            $tanggalServis = $part->serviceHistory?->tanggal_service
                ?? $part->tgl_pasang
                ?? now()->toDateString();

            $keterangan = $this->generateKeterangan($part, $limitRule, $tanggalServis);

            if (!$this->option('dry-run')) {
                $part->update(['keterangan_limit' => $keterangan]);
                $updated++;
            } else {
                $this->line("\n  Part #{$part->id} [{$part->nama_part}] → {$keterangan}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info('Dry-run selesai. Tidak ada yang diubah.');
        } else {
            $this->info("Berhasil update {$updated} part.");
        }

        return 0;
    }

    private function generateKeterangan(ServicePart $part, ?ServiceCategoryLimit $limitRule, string $tanggalServis): string
    {
        if (!$limitRule) {
            return '-';
        }

        $biaya          = (int) $part->biaya;
        $tglPasang      = Carbon::parse($part->tgl_pasang ?? now());
        $intervalNilai  = (int) ($part->interval_nilai ?? 0);
        $intervalSatuan = $part->interval_satuan ?? 'bulan';
        $kmPasang       = (int) $part->kilometer_pasang;
        $kmInput        = (int) ($part->serviceHistory?->kilometer ?? $kmPasang);

        // Hitung tanggal limit
        $tglLimit = match ($intervalSatuan) {
            'hari'   => (clone $tglPasang)->addDays($intervalNilai),
            'minggu' => (clone $tglPasang)->addWeeks($intervalNilai),
            'tahun'  => (clone $tglPasang)->addYears($intervalNilai),
            default  => (clone $tglPasang)->addMonths($intervalNilai),
        };

        $refTanggal = Carbon::parse($tanggalServis)->startOfDay();

        $hargaLimit  = $limitRule->limit_price;
        $kmLimit     = $limitRule->limit_km;
        $intervalAda = $intervalNilai > 0;

        // ── Biaya kumulatif dalam periode rolling ────────────────────────────
        // Part ini sudah tersimpan di DB, jadi total_dalam_periode sudah termasuk biaya part ini sendiri
        $biayaKumulatif  = $biaya;
        if ($hargaLimit && $limitRule->limit_nilai && $limitRule->kendaraan_id) {
            $controller    = app(\App\Http\Controllers\Admin\ServiceHistoryController::class);
            $kumulatifData = $controller->getKumulatifBiayaKategori(
                (int) $limitRule->kendaraan_id,
                (int) $limitRule->category_id,
                (int) $limitRule->limit_nilai,
                $limitRule->limit_satuan ?? 'bulan',
                (int) $hargaLimit,
                $tglPasang->toDateString()
            );
            if ($kumulatifData !== null) {
                $biayaKumulatif = $kumulatifData['total_dalam_periode'];
            }
        }

        $biayaLewat = $hargaLimit && $biayaKumulatif > $hargaLimit;
        $biayaSama  = $hargaLimit && $biayaKumulatif === $hargaLimit;
        $biayaAman  = !$hargaLimit || $biayaKumulatif < $hargaLimit;

        $waktuLewat = $intervalAda && $tglLimit->lt($refTanggal);
        $waktuSama  = $intervalAda && $tglLimit->eq($refTanggal);
        $waktuAman  = !$intervalAda || $tglLimit->gt($refTanggal);

        $kmAda      = $kmLimit && $kmLimit > 0;
        $kmLewat    = $kmAda && $kmInput > $kmLimit;
        $kmSama     = $kmAda && $kmInput === $kmLimit;
        $kmAman     = !$kmAda || $kmInput < $kmLimit;

        $adaLimit = $hargaLimit || $intervalAda || $kmAda;
        if (!$adaLimit || ($biayaAman && $waktuAman && $kmAman)) {
            return '-';
        }

        $parts = [];
        if ($biayaSama)  $parts['biaya'] = 'mencapai batas limit biaya';
        elseif ($biayaLewat) $parts['biaya'] = 'sudah melebihi limit biaya';

        if ($waktuSama)  $parts['waktu'] = 'mencapai batas limit jangka waktu';
        elseif ($waktuLewat) $parts['waktu'] = 'sudah melebihi batas waktu';

        if ($kmSama)     $parts['km'] = 'mencapai batas limit KM';
        elseif ($kmLewat) $parts['km'] = 'sudah melebihi batas limit KM';

        $belumParts = [];
        if ($hargaLimit && $biayaAman && !isset($parts['biaya']))
            $belumParts[] = 'belum mencapai limit biaya';
        if ($intervalAda && $waktuAman && !isset($parts['waktu']))
            $belumParts[] = 'belum mencapai limit jangka waktu';
        if ($kmAda && $kmAman && !isset($parts['km']))
            $belumParts[] = 'belum mencapai limit KM';

        $kalimat = array_merge(array_values($parts), $belumParts);
        if (empty($kalimat)) return '-';

        return ucfirst(implode(', ', $kalimat));
    }
}
