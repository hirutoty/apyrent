<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kehilangan extends Model
{
    use HasFactory;

    protected $table = 'kehilangans';

    protected $fillable = [
        'kendaraan_id',
        'status',
        'tanggal',
        'keterangan',
        'lokasi_kejadian',
        'pelanggan_id',
        'pengurus',
        'informasi_gps',
        'file',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function laporans()
    {
        return $this->hasMany(LaporanKehilangan::class)->orderBy('created_at', 'asc');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Label badge warna untuk setiap status
     */
    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'sementara' => 'bg-yellow-100 text-yellow-800',
            'permanen'  => 'bg-red-100 text-red-800',
            'ditemukan' => 'bg-green-100 text-green-800',
            default     => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Label status dalam bahasa Indonesia
     */
    public function statusLabel(): string
    {
        return match($this->status) {
            'sementara' => 'Sementara',
            'permanen'  => 'Permanen',
            'ditemukan' => 'Ditemukan',
            default     => ucfirst($this->status),
        };
    }

    /**
     * Transisi status yang diizinkan dari status saat ini
     */
    public function allowedTransitions(): array
    {
        return match($this->status) {
            'sementara' => ['permanen', 'ditemukan'],
            'permanen'  => ['ditemukan'],
            'ditemukan' => [],
            default     => [],
        };
    }
}
