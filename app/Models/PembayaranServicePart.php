<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembayaranServicePart extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_service_parts';

    protected $fillable = [
        'pembayaran_id',
        'kendaraan_id',
        'category_id',
        'nama_part',
        'part_number',
        'serial_number',
        'posisi',
        'merk',
        'tgl_pasang',
        'kilometer_pasang',
        'kondisi',
        'status_part',
        'interval_nilai',
        'interval_satuan',
        'biaya',
        'keterangan',
        'is_over_limit',
    ];

    protected $casts = [
        'tgl_pasang'       => 'date',
        'kilometer_pasang' => 'integer',
        'biaya'            => 'integer',
        'is_over_limit'    => 'boolean',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}
