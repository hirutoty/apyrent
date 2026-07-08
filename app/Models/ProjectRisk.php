<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRisk extends Model
{
    protected $fillable = [
        'proyek',
        'risiko',
        'dampak',
        'kemungkinan',
        'mitigasi',
        'status',
    ];
}
