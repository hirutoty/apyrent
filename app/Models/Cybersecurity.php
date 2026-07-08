<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cybersecurity extends Model
{
    use HasFactory;

    protected $table = 'cybersecurities';
    protected $guarded = [];

    protected $casts = [
        'tanggal_audit' => 'date',
    ];
}
