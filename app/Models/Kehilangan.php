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
        // Snapshot data kendaraan
        'kendaraan_nopol',
        'kendaraan_merk',
        'kendaraan_type',
        'kendaraan_warna',
        'kendaraan_tahun',
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
        // Sertakan soft deleted untuk bisa akses kendaraan yang sudah dihapus (status permanen)
        return $this->belongsTo(Kendaraan::class)->withTrashed();
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

    /**
     * Get kendaraan nopol (snapshot or relation)
     */
    public function getNopolAttribute(): string
    {
        return $this->kendaraan_nopol ?? $this->kendaraan->nopol ?? '-';
    }

    /**
     * Get kendaraan merk (snapshot or relation)
     */
    public function getMerkAttribute(): string
    {
        return $this->kendaraan_merk ?? $this->kendaraan->merk ?? '-';
    }

    /**
     * Get display name kendaraan
     */
    public function getKendaraanDisplayAttribute(): string
    {
        $nopol = $this->nopol;
        $merk = $this->merk;
        return "{$nopol} — {$merk}";
    }
}
