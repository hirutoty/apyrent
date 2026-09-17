<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ServiceIncidentPart extends Model
{
    use HasFactory;

    protected $table = 'service_incident_parts';

    protected $fillable = [
        'service_incident_id',
        'kendaraan_id',
        'category_id',
        'nama_part',
        'part_number',
        'serial_number',
        'posisi',
        'tgl_pasang',
        'kilometer_pasang',
        'kondisi',
        'status',
        'interval_nilai',
        'interval_satuan',
        'tanggal_limit',
        'biaya',
        'bukti',
        'keterangan_limit',
        'persetujuan',
        'nama_rekening',
        'nama_bank',
        'no_rekening',
        'supplier_id',
        'replaced_at',
        'replaced_by_part_id',
    ];

    protected $casts = [
        'tgl_pasang'       => 'date',
        'tanggal_limit'    => 'date',
        'kilometer_pasang' => 'integer',
        'biaya'            => 'integer',
        'interval_nilai'   => 'integer',
        'bukti'            => 'array',
        'replaced_at'      => 'datetime',
    ];

    public function serviceIncident()
    {
        return $this->belongsTo(ServiceIncident::class, 'service_incident_id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function replacedBy()
    {
        return $this->belongsTo(ServiceIncidentPart::class, 'replaced_by_part_id');
    }

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class, 'supplier_id');
    }

    public function hitungTanggalLimit(): Carbon
    {
        $tanggal = Carbon::parse($this->tgl_pasang);

        return match ($this->interval_satuan) {
            'hari'   => $tanggal->addDays($this->interval_nilai),
            'minggu' => $tanggal->addWeeks($this->interval_nilai),
            'bulan'  => $tanggal->addMonths($this->interval_nilai),
            'tahun'  => $tanggal->addYears($this->interval_nilai),
            default  => $tanggal->addMonths($this->interval_nilai),
        };
    }

    public function isLimit(): bool
    {
        if (!$this->tanggal_limit) return false;
        return Carbon::today()->gte(Carbon::parse($this->tanggal_limit));
    }

    public function sisaHari(): int
    {
        if (!$this->tanggal_limit) return 0;
        return (int) Carbon::today()->diffInDays(Carbon::parse($this->tanggal_limit), false);
    }
}
