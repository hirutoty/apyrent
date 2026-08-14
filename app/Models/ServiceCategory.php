<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $table = 'service_categories';

    protected $fillable = ['nama'];

    public function parts()
    {
        return $this->hasMany(ServicePart::class, 'category_id');
    }
}
