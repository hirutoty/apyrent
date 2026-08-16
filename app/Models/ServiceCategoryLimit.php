<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCategoryLimit extends Model
{
    use HasFactory;

    protected $table = 'service_category_limits';

    protected $fillable = [
        'kendaraan_id',
        'category_id',
        'limit_nilai',
        'limit_satuan',
        'limit_price',
    ];

    protected $casts = [
        'limit_nilai'  => 'integer',
        'limit_price'  => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Representasi interval sebagai string (misal: "1 tahun").
     */
    public function intervalLabel(): string
    {
        return "{$this->limit_nilai} {$this->limit_satuan}";
    }

    /**
     * Format limit_price ke Rupiah.
     */
    public function limitPriceFormatted(): string
    {
        if (!$this->limit_price) return '-';
        return 'Rp ' . number_format($this->limit_price, 0, ',', '.');
    }
}
