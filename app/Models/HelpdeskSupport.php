<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpdeskSupport extends Model
{
    use HasFactory;

    protected $table = 'helpdesk_supports';
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
