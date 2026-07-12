<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ReminderService extends Model
{
    use HasFactory;

    protected $table = 'reminder_service';

    protected $fillable = [
        'kendaraan_id',
        'nama_reminder',
        'tanggal_mulai',
        'interval_nilai',
        'interval_satuan',
        'tanggal_jatuh_tempo',
        'keterangan',
        'status',
        'sudah_dibuat_masalah',
    ];

    protected $casts = [
        'tanggal_mulai'        => 'date',
        'tanggal_jatuh_tempo'  => 'date',
        'sudah_dibuat_masalah' => 'boolean',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    /**
     * Hitung tanggal jatuh tempo berdasarkan tanggal_mulai + interval
     */
    public function hitungJatuhTempo(): Carbon
    {
        $tanggal = Carbon::parse($this->tanggal_mulai);

        return match ($this->interval_satuan) {
            'hari'   => $tanggal->addDays($this->interval_nilai),
            'minggu' => $tanggal->addWeeks($this->interval_nilai),
            'bulan'  => $tanggal->addMonths($this->interval_nilai),
            'tahun'  => $tanggal->addYears($this->interval_nilai),
            default  => $tanggal->addMonths($this->interval_nilai),
        };
    }

    /**
     * Sisa hari hingga jatuh tempo (negatif = sudah lewat)
     */
    public function sisaHari(): int
    {
        if (!$this->tanggal_jatuh_tempo) return 0;
        return (int) Carbon::today()->diffInDays(
            Carbon::parse($this->tanggal_jatuh_tempo),
            false
        );
    }

    /**
     * Label status untuk tampilan
     */
    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'aktif'        => 'Aktif',
            'jatuh_tempo'  => 'Jatuh Tempo',
            'selesai'      => 'Selesai',
            default        => $this->status,
        };
    }
}
