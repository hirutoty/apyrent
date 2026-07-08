<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTimeline extends Model
{
    protected $fillable = [
        'proyek',
        'kegiatan',
        'deadline',
        'reminder',
        'status',
    ];

    protected $casts = [
        'deadline' => 'date',
        'reminder' => 'boolean',
    ];
}
