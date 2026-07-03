<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    use HasFactory;

    protected $table = 'keuangans';

    protected $fillable = [
        'tanggal',
        'reference',
        'user_id',
        'kategori',
        'metode',
        'keterangan',
        'pemasukan',
        'pengeluaran',
        'saldo',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'pemasukan' => 'decimal:2',
        'pengeluaran' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];

    /**
     * RELASI USER
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}