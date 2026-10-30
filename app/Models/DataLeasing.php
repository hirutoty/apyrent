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
       RELASI — ke LeasingPayment (pembayaran per bulan)
    ───────────────────────────────────────────── */
    public function payments()
    {
        return $this->hasMany(LeasingPayment::class, 'data_leasing_id');
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

        // +1 untuk menghitung bulan awal (inklusif)
        // Contoh: Agu-Des = 4 diff + 1 = 5 bulan
        return max(0, $mulaiC->diffInMonths($selesaiC) + 1);
    }

    /* ─────────────────────────────────────────────
       ACCESSOR — Cicilan Tersisa
       Dihitung dari bulan yang sudah lewat sejak periode_mulai.
       Cicilan bulan ini (bulan sekarang) belum dihitung sebagai
       yang sudah dibayar (jatuh_tempo masih di depan).
       
       Contoh: mulai Agu, sekarang Okt, jatuh_tempo = 15
       - Jika sekarang < 15 Okt → sudah lewat Agu, Sep = 2 bulan
       - Jika sekarang >= 15 Okt → sudah lewat Agu, Sep, Okt = 3 bulan
    ───────────────────────────────────────────── */
    public function getCicilanTersisaAttribute(): int
    {
        $total = $this->jumlah_cicilan;

        if ($total === 0 || !$this->periode_mulai) {
            return 0;
        }

        $now   = Carbon::now();
        $mulai = Carbon::parse($this->periode_mulai)->startOfMonth();

        // Belum mulai sama sekali
        if ($now->lessThan($mulai)) {
            return $total;
        }

        // Hitung bulan yang sudah lewat (termasuk bulan ini jika sudah melewati jatuh_tempo)
        $nowMonth = $now->copy()->startOfMonth();
        $sudahLewat = $mulai->diffInMonths($nowMonth);
        
        // Jika hari ini sudah >= jatuh_tempo di bulan ini, maka bulan ini juga sudah lewat
        $jatuhTempo = $this->jatuh_tempo ?? 1;
        if ($now->day >= $jatuhTempo) {
            $sudahLewat += 1;
        }

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
