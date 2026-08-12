<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKontrak extends Model
{
    use HasFactory;

    protected $table = 'data_kontraks';

    protected $fillable = [
        'serial_number',
        'no_kontrak',
        'kendaraan_id',
        'mobil',
        'nopol',
        'tahun',
        'user_kontrak',
        'angsuran_per_bulan',
        'jatuh_tempo',
        'periode_mulai',
        'periode_selesai',
        'personal_account',
        'sumber_dana_debit',
        'cara_bayar',
        // Asuransi inline
        'nama_asuransi',
        'alamat_asuransi',
        'nama_marketing',
        'kontak_marketing',
        'nama_bengkel',
        'kontak_bengkel',
        'bukti',
    ];

    protected $casts = [
        'angsuran_per_bulan' => 'integer',
        'jatuh_tempo'        => 'integer',
        'periode_mulai'      => 'date',
        'periode_selesai'    => 'date',
    ];

    /* ─────────────────────────────────────────────
       RELASI
    ───────────────────────────────────────────── */

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function leasings()
    {
        return $this->hasMany(DataLeasing::class, 'data_kontrak_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'relation_id')
            ->where('relation_type', 'data_kontrak');
    }

    /* ─────────────────────────────────────────────
       ACCESSOR — Jumlah Cicilan Total
       = diff bulan antara periode_mulai & periode_selesai
    ───────────────────────────────────────────── */
    public function getJumlahCicilanAttribute(): int
    {
        if (!$this->periode_mulai || !$this->periode_selesai) {
            return 0;
        }

        $mulai   = Carbon::parse($this->periode_mulai)->startOfMonth();
        $selesai = Carbon::parse($this->periode_selesai)->startOfMonth();

        return max(0, $mulai->diffInMonths($selesai));
    }

    /* ─────────────────────────────────────────────
       ACCESSOR — Cicilan Tersisa
       = jumlah_cicilan - bulan yang sudah lewat
    ───────────────────────────────────────────── */
    public function getCicilanTersisaAttribute(): int
    {
        $total = $this->jumlah_cicilan;

        if ($total === 0 || !$this->periode_mulai) {
            return 0;
        }

        $now   = Carbon::now()->startOfMonth();
        $mulai = Carbon::parse($this->periode_mulai)->startOfMonth();

        if ($now->lessThan($mulai)) {
            // Belum mulai, cicilan masih penuh
            return $total;
        }

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
