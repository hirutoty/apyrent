<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchasero extends Model
{
    use HasFactory;

    protected $table = 'purchaseros';

    protected $fillable = [
        'no_pr',
        'tanggal',
        'departemen',
        'pemohon',
        'barang_jasa',
        'kode_barang',
        'qty',
        'satuan',
        'alasan_permintaan',
        'nominal',
        'status',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'catatan',
        'terakhir_diajukan',
    ];

    protected $casts = [
        'terakhir_diajukan' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // no_pr di-generate manual di store() untuk multi-item
        // agar semua item dalam 1 submit mendapat No PR yang sama
    }
}