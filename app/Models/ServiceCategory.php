<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $table = 'service_categories';

    protected $fillable = ['nama'];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function parts()
    {
        return $this->hasMany(ServicePart::class, 'category_id');
    }

    public function limits()
    {
        return $this->hasMany(ServiceCategoryLimit::class, 'category_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Cek apakah kategori masih dipakai oleh ServicePart yang aktif.
     */
    public function hasActiveParts(): bool
    {
        return $this->parts()
            ->whereIn('status', ['Terpasang', 'Limit'])
            ->exists();
    }
}
