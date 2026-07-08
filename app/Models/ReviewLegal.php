<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewLegal extends Model
{
    protected $table = 'review_legals';

    protected $fillable = [
        'tanggal',
        'pemohon',
        'dokumen',
        'status_review',
        'pic_legal',
        'catatan',
    ];
}
