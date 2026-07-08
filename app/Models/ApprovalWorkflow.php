<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalWorkflow extends Model
{
    use HasFactory;

    protected $table = 'approval_workflows';

    protected $fillable = [
        'id_po',
        'urutan_approval',
        'jabatan',
        'nama_approver',
        'tanggal',
        'status_approval',
        'catatan',
    ];

    protected $casts = [
        'tanggal'         => 'date',
        'urutan_approval' => 'integer',
    ];
}
