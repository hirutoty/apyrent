<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkMonitoring extends Model
{
    use HasFactory;

    protected $table = 'network_monitorings';
    protected $guarded = [];
}
