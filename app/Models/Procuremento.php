<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procuremento extends Model
{
    use HasFactory;

    protected $table = 'procurementos';

    protected $fillable = [
        'workflow_id',
        'nama_workflow',
        'trigger_event',
        'syarat_tambahan',
        'aksi_dilakukan',
        'delay_aksi',
        'status',
        'pic',
        'catatan',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->workflow_id)) {
                $last = self::orderBy('id', 'desc')->first();
                $nextNumber = 1;

                if ($last && preg_match('/(\d+)$/', (string) $last->workflow_id, $matches)) {
                    $nextNumber = ((int) $matches[1]) + 1;
                }

                $model->workflow_id = 'AUTO' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}