<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CeklistKondisi extends Model
{
    use HasFactory;

    protected $table = 'ceklist_kondisi';

    protected $fillable = [
        'kendaraan_id',
        'nama',
        'perusahaan',
        'alamat',
        'no_telephone',
        'tanggal',
        'kilometer',
        'jam',
        'fuel',
        'pemeriksa',
        'status',
        'pemakaian_bbm',
        'ratio',
        'foto_depan',
        'foto_atas',
        'foto_belakang',
        'foto_sampingkiri',
        'foto_sampingkanan',
        'ket_depan',
        'ket_atas',
        'ket_belakang',
        'ket_sampingkiri',
        'ket_sampingkanan',
        'accecoris_tambahan',
        'kondisi_mesin',
        'kondisi_rem',
        'kondisi_kopling',
        'kondisi_ban',
        'kondisi_ac',
        'lampu_lampusen',
        'lampu_lampubesarkecil',
        'lampu_rembikplat',
        'accu',
        'airradiator',
        'olimesin_remkopling',
        'olipower_steering',
        'kebersihan_interior',
        'kerapihanbody_cat',
        'perlengkapan',
        'ban_cadang',
        'dongkrak',
        'kunciroda',
        'radio_tape',
        'merk',
        'karpet_lantai',
        'doproda',
        'pematikapi',
        'segitiga_pengaman',
        'kotakobat',
        'stnk',
        'kuncipintu',
        'spedoometer',
        'sopir',
        'ttd_supir',
        'mekanis',
        'ttd_mekanik',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke Kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}
