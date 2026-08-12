<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataLeasing extends Model
{
    use HasFactory;

    protected $table = 'data_leasings';

    protected $fillable = [
        'data_kontrak_id',
        'no_kontrak',
        'mobil',
        'tahun',
        'nopol',
        'user_leasing',
        'angsuran_per_bulan',
        'jatuh_tempo',
        'periode_mulai',
        'periode_selesai',
        'jumlah_cicilan',
        'personal_account',
        'sumber_dana_debit',
        'cara_bayar',
        'asuransi_leasing',
    ];

    protected $casts = [
        'jatuh_tempo'        => 'integer',
        'angsuran_per_bulan' => 'integer',
        'jumlah_cicilan'     => 'integer',
        'periode_mulai'      => 'date',
        'periode_selesai'    => 'date',
    ];

    /* ─────────────────────────────────────────────
       RELASI — ke DataKontrak (master)
    ───────────────────────────────────────────── */
    public function dataKontrak()
    {
        return $this->belongsTo(DataKontrak::class, 'data_kontrak_id');
    }

    /* ─────────────────────────────────────────────
       ACCESSOR — Jumlah Cicilan Total
       Prioritas: kolom jumlah_cicilan (manual via import/form)
       Fallback : hitung otomatis dari selisih periode
    ───────────────────────────────────────────── */
    public function getJumlahCicilanAttribute(): int
    {
        // Jika diisi manual (via import atau form), pakai nilai itu
        $manual = $this->attributes['jumlah_cicilan'] ?? null;
        if ($manual !== null && (int) $manual > 0) {
            return (int) $manual;
        }

        // Fallback: hitung dari selisih bulan periode_mulai → periode_selesai
        $mulai   = $this->periode_mulai;
        $selesai = $this->periode_selesai;

        if (!$mulai || !$selesai) {
            return 0;
        }

        $mulaiC   = Carbon::parse($mulai)->startOfMonth();
        $selesaiC = Carbon::parse($selesai)->startOfMonth();

        return max(0, $mulaiC->diffInMonths($selesaiC));
    }

    /* ─────────────────────────────────────────────
       ACCESSOR — Cicilan Tersisa
       Dihitung dari selisih bulan antara periode_mulai
       dan bulan sekarang. Cicilan bulan ini belum dihitung
       sebagai yang sudah dibayar.
       Contoh: mulai Mei, sekarang Ags → sudah lewat 3 (Mei/Jun/Jul),
       tersisa = total - 3
    ───────────────────────────────────────────── */
    public function getCicilanTersisaAttribute(): int
    {
        $total = $this->jumlah_cicilan;

        if ($total === 0 || !$this->periode_mulai) {
            return 0;
        }

        $now   = Carbon::now()->startOfMonth();
        $mulai = Carbon::parse($this->periode_mulai)->startOfMonth();

        // Belum mulai sama sekali
        if ($now->lessThan($mulai)) {
            return $total;
        }

        // Cicilan yang sudah lewat = bulan dari mulai sampai sebelum bulan sekarang
        // diffInMonths(Mei-01, Ags-01) = 3 → tersisa = total - 3
        $sudahLewat = $mulai->diffInMonths($now);

        return max(0, $total - $sudahLewat);
    }

    /* ─────────────────────────────────────────────
       ACCESSOR — Status Cicilan
    ───────────────────────────────────────────── */
    public function getStatusCicilanAttribute(): string
    {
        if (!$this->periode_mulai || !$this->periode_selesai) {
            return 'Belum Diatur';
        }

        $now   = Carbon::now()->startOfMonth();
        $mulai = Carbon::parse($this->periode_mulai)->startOfMonth();

        if ($now->lessThan($mulai)) {
            return 'Belum Mulai';
        }

        $tersisa = $this->cicilan_tersisa;

        if ($tersisa <= 0) {
            return 'Lunas';
        }

        return 'Partial';
    }
}
