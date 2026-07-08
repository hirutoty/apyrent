<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailDomain extends Model
{
    use HasFactory;

    protected $table = 'email_domains';
    protected $guarded = [];

    protected $casts = [
        'expired_date'   => 'date',
        'dns_terkelola'  => 'boolean',
        'email_aktif'    => 'integer',
    ];
}
