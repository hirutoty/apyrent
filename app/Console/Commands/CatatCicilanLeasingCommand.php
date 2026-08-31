<?php

namespace App\Console\Commands;

use App\Models\Bukubesar;
use App\Models\DataLeasing;
use App\Models\Keuangan;
use App\Models\LeasingPayment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CatatCicilanLeasingCommand extends Command
{
    protected $signature = 'leasing:catat-cicilan';

    protected $description = 'Catat cicilan leasing yang sudah jatuh tempo ke keuangan dan buku besar (backfill included)';

    public function handle()
    {
        Log::info('=== CATAT CICILAN LEASING START ===');

        // Ambil semua leasing yang sudah mulai dan belum lunas
        // (periode_mulai ada, angsuran > 0)
        $leasings = DataLeasing::whereNotNull('periode_mulai')
            ->where('angsuran_per_bulan', '>', 0)
            ->get();

        if ($leasings->isEmpty()) {
            Log::info('Tidak ada data leasing aktif.');
            return self::SUCCESS;
        }

        $totalCatat  = 0;
        $totalSkip   = 0;
        $bulanSekarang = Carbon::now()->startOfMonth();

        foreach ($leasings as $leasing) {
            $periodeMultai  = Carbon::parse($leasing->periode_mulai)->startOfMonth();
            $periodeSelesai = $leasing->periode_selesai
                ? Carbon::parse($leasing->periode_selesai)->startOfMonth()
                : null;

            // Batas akhir pencatatan: bulan sekarang atau periode_selesai (mana yang lebih awal)
            $batasAkhir = $periodeSelesai
                ? $bulanSekarang->copy()->min($periodeSelesai)
                : $bulanSekarang->copy();

            // Kalau belum mulai sama sekali, skip
            if ($periodeMultai->greaterThan($bulanSekarang)) {
                Log::info("Leasing #{$leasing->id} ({$leasing->no_kontrak}) belum mulai, skip.");
                continue;
            }

            // Ambil semua bulan_cicilan yang sudah tercatat untuk leasing ini
            $sudahTercatat = LeasingPayment::where('data_leasing_id', $leasing->id)
                ->pluck('bulan_cicilan')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-01'))
                ->toArray();

            // Generate semua bulan dari periode_mulai sampai batas_akhir
            $bulanIterasi = $periodeMultai->copy();

            while ($bulanIterasi->lessThanOrEqualTo($batasAkhir)) {
                $bulanKey = $bulanIterasi->format('Y-m-01');

                // Sudah tercatat? Skip
                if (in_array($bulanKey, $sudahTercatat)) {
                    $totalSkip++;
                    $bulanIterasi->addMonth();
                    continue;
                }

                // Belum tercatat — catat sekarang
                try {
                    DB::transaction(function () use ($leasing, $bulanKey, &$totalCatat) {
                        $nominal    = (int) $leasing->angsuran_per_bulan;
                        $kodeJurnal = 'LEASING-' . $leasing->id . '-' . Carbon::parse($bulanKey)->format('Y-m');
                        $bulanLabel = Carbon::parse($bulanKey)->translatedFormat('M Y');

                        // --- Catat ke Keuangan ---
                        $lastSaldo = (float) DB::table('keuangans')
                            ->lockForUpdate()
                            ->orderBy('id', 'desc')
                            ->value('saldo') ?? 0;

                        $keuangan = Keuangan::create([
                            'tanggal'     => Carbon::parse($bulanKey),
                            'reference'   => $kodeJurnal,
                            'user_id'     => null, // auto oleh sistem
                            'divisi'      => 'Keuangan',
                            'kategori'    => 'Pengeluaran',
                            'metode'      => 'Auto Debit',
                            'keterangan'  => 'Cicilan leasing ' . ($leasing->no_kontrak ?? '-') . ' - ' . ($leasing->mobil ?? '-') . ' bulan ' . $bulanLabel,
                            'pemasukan'   => 0,
                            'pengeluaran' => $nominal,
                            'saldo'       => $lastSaldo - $nominal,
                            'sumber'      => 'auto',
                        ]);

                        // --- Auto-posting ke Buku Besar ---
                        $saldoBBTerakhir = (float) DB::table('bukubesars')
                            ->lockForUpdate()
                            ->orderBy('id', 'desc')
                            ->value('saldo') ?? 0;

                        Bukubesar::create([
                            'kode_jurnal' => $kodeJurnal,
                            'transaksi'   => 'Cicilan Leasing - ' . ($leasing->no_kontrak ?? '-'),
                            'kategori'    => 'Beban',
                            'tanggal'     => Carbon::parse($bulanKey)->toDateString(),
                            'debit'       => $nominal,
                            'kredit'      => 0,
                            'saldo'       => $saldoBBTerakhir - $nominal,
                            'aktivitas'   => 'Operasi',
                            'keterangan'  => 'Auto-posting: Cicilan leasing ' . ($leasing->no_kontrak ?? '-') . ' ' . ($leasing->nopol ?? '-') . ' bulan ' . $bulanLabel,
                        ]);

                        // --- Catat ke leasing_payments (guard double-catat) ---
                        LeasingPayment::create([
                            'data_leasing_id' => $leasing->id,
                            'bulan_cicilan'   => $bulanKey,
                            'nominal'         => $nominal,
                            'tanggal_catat'   => now()->toDateString(),
                            'keuangan_id'     => $keuangan->id,
                        ]);

                        $totalCatat++;

                        Log::info("Cicilan leasing tercatat", [
                            'leasing_id'  => $leasing->id,
                            'no_kontrak'  => $leasing->no_kontrak,
                            'bulan'       => $bulanKey,
                            'nominal'     => $nominal,
                        ]);
                    });
                } catch (\Throwable $e) {
                    // Unique constraint violation = sudah tercatat oleh proses lain, aman diabaikan
                    if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'unique')) {
                        $totalSkip++;
                    } else {
                        Log::error("Gagal catat cicilan leasing #{$leasing->id} bulan {$bulanKey}", [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $bulanIterasi->addMonth();
            }
        }

        Log::info('=== CATAT CICILAN LEASING SELESAI ===', [
            'total_leasing' => $leasings->count(),
            'total_dicatat' => $totalCatat,
            'total_skip'    => $totalSkip,
        ]);

        return self::SUCCESS;
    }
}
