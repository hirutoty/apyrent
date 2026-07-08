<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyCompliance extends Model
{
    use HasFactory;

    protected $table = 'policy_compliances';
    protected $guarded = [];

    protected $casts = [
        'tanggal_berlaku' => 'date',
    ];
}
