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

            // Hitung aktifCount untuk dimensi jumlah (pakai anchor KM)
            $aktifCountRec  = null;
            $limitJumlahRec = null;
            if ($limitRule && $limitRule->jumlah) {
                $anchorKmRec = ($limitRule->limit_km && $limitRule->limit_km_interval)
                    ? max(0, (int)$limitRule->limit_km - (int)$limitRule->limit_km_interval)
                    : null;
                $qRec = ServicePart::where('kendaraan_id', $part->kendaraan_id)
                    ->where('category_id', $part->category_id)
                    ->whereIn('status', ['Terpasang', 'Limit', 'tidak_aktif', 'aktif']);
                if ($anchorKmRec !== null) {
                    $qRec->where(fn($q) => $q->whereNull('kilometer_pasang')->orWhere('kilometer_pasang', '>=', $anchorKmRec));
                }
                $aktifCountRec  = $qRec->count();
                $limitJumlahRec = (int) $limitRule->jumlah;
            }

            $keterangan = $this->generateKeterangan($part, $limitRule, $tanggalServis, $aktifCountRec, $limitJumlahRec);

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

    private function generateKeterangan(ServicePart $part, ?ServiceCategoryLimit $limitRule, string $tanggalServis, ?int $aktifCount = null, ?int $limitJumlah = null): string
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

        $jumlahAda   = $limitJumlah !== null && $limitJumlah > 0 && $aktifCount !== null;
        $jumlahSama  = $jumlahAda && $aktifCount === $limitJumlah;
        $jumlahLewat = $jumlahAda && $aktifCount > $limitJumlah;
        $jumlahAman  = !$jumlahAda || $aktifCount < $limitJumlah;

        $adaLimit = $hargaLimit || $intervalAda || $kmAda || $jumlahAda;
        if (!$adaLimit || ($biayaAman && $waktuAman && $kmAman && $jumlahAman)) {
            return '-';
        }

        $parts = [];
        if ($biayaSama)      $parts['biaya'] = 'sudah mencapai batas limit biaya';
        elseif ($biayaLewat) $parts['biaya'] = 'sudah melebihi limit biaya';

        if ($waktuSama)      $parts['waktu'] = 'sudah mencapai batas limit jangka waktu';
        elseif ($waktuLewat) $parts['waktu'] = 'sudah melebihi batas waktu';

        if ($kmSama)      $parts['km'] = 'sudah mencapai batas limit KM';
        elseif ($kmLewat) $parts['km'] = 'sudah melebihi batas limit KM';

        if ($jumlahSama)      $parts['jumlah'] = "sudah mencapai batas jumlah part ({$aktifCount}/{$limitJumlah} pcs)";
        elseif ($jumlahLewat) $parts['jumlah'] = "sudah melebihi batas jumlah part ({$aktifCount}/{$limitJumlah} pcs)";

        $belumParts = [];
        if ($hargaLimit && $biayaAman && !isset($parts['biaya']))
            $belumParts[] = 'belum mencapai limit biaya';
        if ($intervalAda && $waktuAman && !isset($parts['waktu']))
            $belumParts[] = 'belum mencapai limit jangka waktu';

        $kalimat = array_merge(array_values($parts), $belumParts);
        if (empty($kalimat)) return '-';

        return ucfirst(implode(', ', $kalimat));
    }
}
