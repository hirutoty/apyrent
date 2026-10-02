<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServicePart;
use App\Models\ServiceCategoryLimit;
use Carbon\Carbon;

class RecalculateServicePartsKeteranganLimit extends Command
{
    protected $signature   = 'service:recalc-keterangan-limit
        {--dry-run  : Tampilkan saja tanpa update}
        {--all      : Recalculate semua part, bukan hanya yang null/"-"}
        {--kendaraan= : Batasi ke kendaraan_id tertentu}';

    protected $description = 'Recalculate keterangan_limit untuk service_parts dengan akumulasi kumulatif berurutan per kategori dalam satu service_history';

    public function handle(): int
    {
        $query = ServicePart::with(['serviceHistory'])
            ->orderBy('service_history_id')
            ->orderBy('id');

        if ($kendaraanId = $this->option('kendaraan')) {
            $query->where('kendaraan_id', (int) $kendaraanId);
        }

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
        $limitRulesMap = ServiceCategoryLimit::all()
            ->keyBy(fn($r) => $r->kendaraan_id . '_' . $r->category_id);

        // ── Group per service_history_id agar bisa proses berurutan per kategori ──
        // Parts dalam satu service_history diproses berurutan (by id).
        // Kita perlu tahu biaya kumulatif dari DB SEBELUM part ini, yaitu:
        //   total_dalam_periode_dari_DB - biaya_part_ini - biaya_sibling_sebelumnya_dalam_batch
        // Caranya: untuk setiap service_history, kita proses part per part berurutan,
        // dan track berapa yang sudah "dialokasikan" dalam batch ini sekategori.
        $partsByHistory = $parts->groupBy('service_history_id');

        $updated = 0;
        $bar     = $this->output->createProgressBar($parts->count());
        $bar->start();

        foreach ($partsByHistory as $historyId => $historyParts) {
            // Untuk setiap service_history, track biaya yang sudah dihitung per kategori
            // Key: category_id → total biaya part-part sebelumnya dalam batch ini
            $batchBiayaAlreadyProcessed = [];

            foreach ($historyParts->sortBy('id') as $part) {
                $mapKey    = $part->kendaraan_id . '_' . $part->category_id;
                $limitRule = $limitRulesMap->get($mapKey);

                $tanggalServis = $part->serviceHistory?->tanggal_service
                    ?? $part->tgl_pasang
                    ?? now()->toDateString();

                // Biaya dari part-part lain dalam batch ini (sekategori, sudah diproses sebelumnya)
                $catId               = (int) ($part->category_id ?? 0);
                $biayaBatchSebelumnya = $catId ? (int) ($batchBiayaAlreadyProcessed[$catId] ?? 0) : 0;

                // Hitung aktifCount untuk dimensi jumlah
                $aktifCountRec  = null;
                $limitJumlahRec = null;
                if ($limitRule && $limitRule->jumlah) {
                    $qAktif = ServicePart::where('kendaraan_id', $part->kendaraan_id)
                        ->where('category_id', $part->category_id)
                        ->whereIn('status', ['Terpasang', 'aktif']);
                    // Filter berdasarkan reset_at jika ada — hanya hitung part setelah reset
                    if ($limitRule->reset_at) {
                        $qAktif->where('created_at', '>=', $limitRule->reset_at);
                    }
                    $aktifCountRec  = $qAktif->count();
                    $limitJumlahRec = (int) $limitRule->jumlah;
                }

                $keterangan = $this->generateKeterangan(
                    $part,
                    $limitRule,
                    $tanggalServis,
                    $aktifCountRec,
                    $limitJumlahRec,
                    $biayaBatchSebelumnya
                );

                if (!$this->option('dry-run')) {
                    $part->update(['keterangan_limit' => $keterangan]);
                    $updated++;
                } else {
                    $this->line("\n  History #{$historyId} Part #{$part->id} [{$part->nama_part}] batchExtra={$biayaBatchSebelumnya} → {$keterangan}");
                }

                // Akumulasikan biaya part ini untuk part berikutnya sekategori dalam batch yang sama
                if ($catId) {
                    $batchBiayaAlreadyProcessed[$catId] = $biayaBatchSebelumnya + (int) $part->biaya;
                }

                $bar->advance();
            }
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

    /**
     * Generate keterangan_limit untuk satu part.
     *
     * Karena part sudah tersimpan di DB, kita query total biaya sekategori dalam periode
     * HANYA untuk part dengan id <= part ini — sehingga sibling yang diproses sesudah ini
     * tidak ikut terhitung. Hasilnya mencerminkan sisa limit tepat setelah item ini.
     *
     * @param int $biayaBatchSebelumnya  Tidak dipakai lagi (digantikan oleh query id <= part->id),
     *                                   dipertahankan untuk kompatibilitas signature.
     */
    private function generateKeterangan(
        ServicePart $part,
        ?ServiceCategoryLimit $limitRule,
        string $tanggalServis,
        ?int $aktifCount = null,
        ?int $limitJumlah = null,
        int $biayaBatchSebelumnya = 0
    ): string {
        if (!$limitRule) {
            return '-';
        }

        $biaya          = (int) $part->biaya;
        $tglPasang      = Carbon::parse($part->tgl_pasang ?? now());
        $tglBaru        = $tglPasang->copy()->startOfDay();
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
        // Query biaya part sekategori dalam periode aktif, hanya id <= part->id,
        // sehingga sibling yang diproses sesudah ini tidak ikut terhitung.
        $biayaKumulatif = $biaya;
        $sisaBiaya      = $hargaLimit ? ((int)$hargaLimit - $biaya) : null;

        if ($hargaLimit && $limitRule->limit_nilai && $limitRule->kendaraan_id) {
            $limitNilai  = (int) $limitRule->limit_nilai;
            $limitSatuan = $limitRule->limit_satuan ?? 'bulan';
            $resetAt     = $limitRule->reset_at;

            // Cari anchor periode
            $anchor = null;
            if ($resetAt !== null) {
                $anchor = Carbon::parse($resetAt)->startOfDay();
                if ($tglBaru->lt($anchor)) {
                    $anchor = $tglBaru->copy();
                }
            } else {
                $partPertama = ServicePart::where('kendaraan_id', $limitRule->kendaraan_id)
                    ->where('category_id', $limitRule->category_id)
                    ->whereNotNull('tgl_pasang')
                    ->orderBy('tgl_pasang')
                    ->first();
                $anchor = $partPertama
                    ? Carbon::parse($partPertama->tgl_pasang)->startOfDay()
                    : $tglBaru->copy();
            }

            // Iterasi slot periode sampai tglBaru masuk
            $periodeMulai   = $anchor->copy();
            $periodeSelesai = $this->hitungSelesaiSlot($periodeMulai, $limitNilai, $limitSatuan);
            $maxIter        = 500;
            while ($tglBaru->gt($periodeSelesai) && $maxIter-- > 0) {
                $periodeMulai   = $this->hitungSlotBerikutnya($periodeMulai, $limitNilai, $limitSatuan);
                $periodeSelesai = $this->hitungSelesaiSlot($periodeMulai, $limitNilai, $limitSatuan);
            }

            // Sum biaya hanya untuk id <= part ini dalam periode
            $queryBiaya = ServicePart::where('kendaraan_id', $limitRule->kendaraan_id)
                ->where('category_id', $limitRule->category_id)
                ->where('id', '<=', $part->id)
                ->whereDate('tgl_pasang', '>=', $periodeMulai->toDateString())
                ->whereDate('tgl_pasang', '<=', $periodeSelesai->toDateString());

            if ($resetAt !== null) {
                $queryBiaya->where('created_at', '>=', Carbon::parse($resetAt));
            }

            $biayaKumulatif = (int) $queryBiaya->sum('biaya');
            $sisaBiaya      = (int) $hargaLimit - $biayaKumulatif;
        }

        $biayaLewat = $hargaLimit && $biayaKumulatif > (int) $hargaLimit;
        $biayaSama  = $hargaLimit && $biayaKumulatif === (int) $hargaLimit;
        $biayaAman  = !$hargaLimit || $biayaKumulatif < (int) $hargaLimit;

        $waktuLewat = $intervalAda && $tglLimit->lt($refTanggal);
        $waktuSama  = $intervalAda && $tglLimit->eq($refTanggal);
        $waktuAman  = !$intervalAda || $tglLimit->gt($refTanggal);

        $kmAda   = $kmLimit && $kmLimit > 0;
        $sisaKm  = $kmAda ? ((int)$kmLimit - $kmInput) : null;
        $kmLewat = $kmAda && $kmInput > (int)$kmLimit;
        $kmSama  = $kmAda && $kmInput === (int)$kmLimit;
        $kmAman  = !$kmAda || $kmInput < (int)$kmLimit;

        $jumlahAda   = $limitJumlah !== null && $limitJumlah > 0 && $aktifCount !== null;
        $jumlahSama  = $jumlahAda && $aktifCount === $limitJumlah;
        $jumlahLewat = $jumlahAda && $aktifCount > $limitJumlah;
        $jumlahAman  = !$jumlahAda || $aktifCount < $limitJumlah;
        $sisaPasang  = $jumlahAda ? ($limitJumlah - $aktifCount) : null;

        $adaLimit = $hargaLimit || $intervalAda || $kmAda || $jumlahAda;
        if (!$adaLimit) {
            return '-';
        }

        // ── Bangun keterangan: 1 baris status per dimensi + sisa ────────────
        $kalimat = [];

        if ($kmAda) {
            if ($kmSama)       $kalimat[] = 'Sudah mencapai batas limit KM';
            elseif ($kmLewat)  $kalimat[] = 'Sudah melebihi batas limit KM';
            else               $kalimat[] = 'Belum mencapai batas limit KM (sisa ' . number_format($sisaKm, 0, ',', '.') . ' km)';
        }

        if ($intervalAda) {
            if ($waktuSama)       $kalimat[] = 'Sudah mencapai batas limit jangka waktu';
            elseif ($waktuLewat)  $kalimat[] = 'Sudah melebihi batas limit jangka waktu';
            else {
                $sisaHari = max(0, (int) $refTanggal->diffInDays($tglLimit, false));
                $kalimat[] = 'Belum mencapai limit jangka waktu (' . $this->formatSisaWaktu($sisaHari) . ')';
            }
        }

        if ($hargaLimit) {
            if ($biayaSama)       $kalimat[] = 'Sudah mencapai batas limit biaya';
            elseif ($biayaLewat)  $kalimat[] = 'Sudah melebihi limit biaya';
            else                  $kalimat[] = 'Belum mencapai limit biaya (sisa Rp ' . number_format($sisaBiaya, 0, ',', '.') . ')';
        }

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

    private function hitungSelesaiSlot(Carbon $mulai, int $nilai, string $satuan): Carbon
    {
        return match ($satuan) {
            'hari'   => (clone $mulai)->addDays($nilai)->subDay(),
            'minggu' => (clone $mulai)->addWeeks($nilai)->subDay(),
            'tahun'  => (clone $mulai)->addYears($nilai)->subDay(),
            default  => (clone $mulai)->addMonths($nilai)->subDay(),
        };
    }

    private function hitungSlotBerikutnya(Carbon $mulai, int $nilai, string $satuan): Carbon
    {
        return match ($satuan) {
            'hari'   => (clone $mulai)->addDays($nilai),
            'minggu' => (clone $mulai)->addWeeks($nilai),
            'tahun'  => (clone $mulai)->addYears($nilai),
            default  => (clone $mulai)->addMonths($nilai),
        };
    }

    private function formatSisaWaktu(int $sisaHari): string
    {
        if ($sisaHari <= 0) return 'sisa 0 hari';

        if ($sisaHari <= 30) {
            return 'sisa ' . $sisaHari . ' hari';
        }

        if ($sisaHari < 360) {
            $bulan    = (int) floor($sisaHari / 30);
            $hariSisa = $sisaHari - ($bulan * 30);
            $str      = 'sisa ' . $bulan . ' bulan';
            if ($hariSisa > 0) $str .= ' ' . $hariSisa . ' hari';
            return $str;
        }

        // >= 360 hari → tampilkan dalam tahun
        $tahun            = (int) floor($sisaHari / 365);
        $sisaSetelahTahun = $sisaHari - ($tahun * 365);
        $bulan            = (int) floor($sisaSetelahTahun / 30);
        $str              = 'sisa ' . $tahun . ' tahun';
        if ($bulan > 0) $str .= ' ' . $bulan . ' bulan';
        return $str;
    }
}
