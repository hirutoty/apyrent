<?php

namespace App\Services;

use App\Models\Bukubesar;
use App\Models\DataLeasing;
use App\Models\Keuangan;
use App\Models\LeasingPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeasingPaymentService
{
    /**
     * Catat cicilan leasing yang sudah jatuh tempo untuk data leasing tertentu
     * 
     * @param DataLeasing $leasing
     * @param int|null $userId User ID yang melakukan pencatatan (null = sistem)
     * @return array ['total_dicatat' => int, 'total_skip' => int]
     */
    public function catatCicilanLeasing(DataLeasing $leasing, ?int $userId = null): array
    {
        $totalCatat  = 0;
        $totalSkip   = 0;
        $bulanSekarang = Carbon::now()->startOfMonth();

        if (!$leasing->periode_mulai || $leasing->angsuran_per_bulan <= 0) {
            return ['total_dicatat' => 0, 'total_skip' => 0];
        }

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
            return ['total_dicatat' => 0, 'total_skip' => 0];
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
                DB::transaction(function () use ($leasing, $bulanKey, $userId, &$totalCatat) {
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
                        'user_id'     => $userId, // User yang login atau null (sistem)
                        'divisi'      => 'Keuangan',
                        'kategori'    => 'Pengeluaran',
                        'metode'      => 'Auto Debit',
                        'keterangan'  => 'Cicilan leasing ' . ($leasing->no_kontrak ?? '-') . ' - ' . ($leasing->mobil ?? '-') . ' bulan ' . $bulanLabel,
                        'pemasukan'   => 0,
                        'pengeluaran' => $nominal,
                        'saldo'       => $lastSaldo - $nominal,
                        'sumber'      => $userId ? 'manual' : 'auto',
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
                        'keterangan'  => ($userId ? 'Manual input' : 'Auto-posting') . ': Cicilan leasing ' . ($leasing->no_kontrak ?? '-') . ' ' . ($leasing->nopol ?? '-') . ' bulan ' . $bulanLabel,
                    ]);

                    // --- Catat ke leasing_payments ---
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
                        'user_id'     => $userId,
                    ]);
                });
            } catch (\Throwable $e) {
                // Unique constraint violation = sudah tercatat, aman diabaikan
                if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'unique')) {
                    $totalSkip++;
                } else {
                    Log::error("Gagal catat cicilan leasing #{$leasing->id} bulan {$bulanKey}", [
                        'error' => $e->getMessage(),
                    ]);
                    throw $e; // Rethrow untuk handling di controller
                }
            }

            $bulanIterasi->addMonth();
        }

        return [
            'total_dicatat' => $totalCatat,
            'total_skip'    => $totalSkip,
        ];
    }
}
