<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServicePart;
use App\Models\ServiceCategoryLimit;
use Carbon\Carbon;

class RecalculateServicePartsKeteranganLimit extends Command
{
    protected $signature   = 'service:recalc-keterangan-limit {--dry-run : Tampilkan saja tanpa update} {--all : Recalculate semua part, bukan hanya yang null/"-"}';
    protected $description = 'Recalculate keterangan_limit untuk service_parts (default: hanya null/"-", --all: semua)';

    public function handle(): int
    {
        $query = ServicePart::with(['serviceHistory']);

        if (!$this->option('all')) {
            $query->where(function ($q) {
                $q->whereNull('keterangan_limit')
                  ->orWhere('keterangan_limit', '-')
                  ->orWhere('keterangan_limit', '');
            });
        }

        $parts = $query->get();

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

            // Hitung aktifCount untuk dimensi jumlah
            $aktifCountRec  = null;
            $limitJumlahRec = null;
            if ($limitRule && $limitRule->jumlah) {
                $aktifCountRec  = ServicePart::where('kendaraan_id', $part->kendaraan_id)
                    ->where('category_id', $part->category_id)
                    ->whereIn('status', ['Terpasang', 'Limit', 'tidak_aktif', 'aktif'])
                    ->count();
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
        $kmInput        = (int) ($part->serviceHistory?->kilometer ?? $part->kilometer_pasang);

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
        // Part sudah tersimpan di DB → total_dalam_periode sudah termasuk biaya part ini sendiri
        $biayaKumulatif = $biaya;
        $sisaBiaya      = $hargaLimit ? ((int)$hargaLimit - $biaya) : null;
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
                // Part sudah tersimpan, jadi total_dalam_periode sudah termasuk biaya ini
                $biayaKumulatif = $kumulatifData['total_dalam_periode'];
                $sisaBiaya      = (int)$hargaLimit - $biayaKumulatif; // bisa negatif
            }
        }

        $biayaLewat = $hargaLimit && $biayaKumulatif > $hargaLimit;
        $biayaSama  = $hargaLimit && $biayaKumulatif === $hargaLimit;
        $biayaAman  = !$hargaLimit || $biayaKumulatif < $hargaLimit;

        $waktuLewat = $intervalAda && $tglLimit->lt($refTanggal);
        $waktuSama  = $intervalAda && $tglLimit->eq($refTanggal);
        $waktuAman  = !$intervalAda || $tglLimit->gt($refTanggal);

        $kmAda   = $kmLimit && $kmLimit > 0;
        $sisaKm  = $kmAda ? ((int)$kmLimit - $kmInput) : null; // bisa negatif
        $kmLewat = $kmAda && $kmInput > (int)$kmLimit;
        $kmSama  = $kmAda && $kmInput === (int)$kmLimit;
        $kmAman  = !$kmAda || $kmInput < (int)$kmLimit;

        $jumlahAda   = $limitJumlah !== null && $limitJumlah > 0 && $aktifCount !== null;
        $jumlahSama  = $jumlahAda && $aktifCount === $limitJumlah;
        $jumlahLewat = $jumlahAda && $aktifCount > $limitJumlah;
        $jumlahAman  = !$jumlahAda || $aktifCount < $limitJumlah;
        $sisaPasang  = $jumlahAda ? ($limitJumlah - $aktifCount) : null; // bisa negatif

        // Tidak ada limit dikonfigurasi → "-"
        $adaLimit = $hargaLimit || $intervalAda || $kmAda || $jumlahAda;
        if (!$adaLimit) {
            return '-';
        }

        // ── Bangun keterangan: 1 baris status per dimensi, jumlah pasang + sisa pcs ─
        $kalimat = [];

        // KM — status saja, tanpa angka sisa
        if ($kmAda) {
            if ($kmSama)       $kalimat[] = 'Sudah mencapai batas limit KM';
            elseif ($kmLewat)  $kalimat[] = 'Sudah melebihi batas limit KM';
            else               $kalimat[] = 'Belum mencapai batas limit KM';
        }

        // Jangka waktu — status saja
        if ($intervalAda) {
            if ($waktuSama)       $kalimat[] = 'Sudah mencapai batas limit jangka waktu';
            elseif ($waktuLewat)  $kalimat[] = 'Sudah melebihi batas limit jangka waktu';
            else                  $kalimat[] = 'Belum mencapai limit jangka waktu';
        }

        // Biaya — status saja, tanpa angka sisa
        if ($hargaLimit) {
            if ($biayaSama)       $kalimat[] = 'Sudah mencapai batas limit biaya';
            elseif ($biayaLewat)  $kalimat[] = 'Sudah melebihi limit biaya';
            else                  $kalimat[] = 'Belum mencapai limit biaya';
        }

        // Jumlah pasang — status + sisa pcs
        if ($jumlahAda) {
            if ($jumlahSama)       $kalimat[] = 'Sudah mencapai batas pemasangan part';
            elseif ($jumlahLewat)  $kalimat[] = 'Sudah melebihi batas pemasangan part';
            else                   $kalimat[] = 'Belum mencapai batas pemasangan part (sisa ' . $sisaPasang . ' pcs)';
        }

        if (empty($kalimat)) {
            return '-';
        }

        return implode(', ', $kalimat);
    }
}
