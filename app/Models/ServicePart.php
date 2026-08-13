<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ServicePart extends Model
{
    use HasFactory;

    protected $table = 'service_parts';

    protected $fillable = [
        'service_history_id',
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
    ];

    protected $casts = [
        'tgl_pasang'     => 'date',
        'tanggal_limit'  => 'date',
        'kilometer_pasang' => 'integer',
        'biaya'          => 'integer',
        'interval_nilai' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function serviceHistory()
    {
        return $this->belongsTo(ServiceHistory::class, 'service_history_id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function reminder()
    {
        return $this->hasOne(ReminderService::class, 'service_part_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Hitung tanggal limit berdasarkan tgl_pasang + interval.
     */
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

    /**
     * Cek apakah part sudah melewati tanggal limit.
     */
    public function isLimit(): bool
    {
        if (!$this->tanggal_limit) {
            return false;
        }
        return Carbon::today()->gte(Carbon::parse($this->tanggal_limit));
    }

    /**
     * Sisa hari hingga limit (negatif = sudah lewat).
     */
    public function sisaHari(): int
    {
        if (!$this->tanggal_limit) return 0;
        return (int) Carbon::today()->diffInDays(
            Carbon::parse($this->tanggal_limit),
            false
        );
    }
}
