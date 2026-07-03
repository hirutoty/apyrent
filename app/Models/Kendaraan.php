<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceHistory;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan'; // ⚠️ sesuaikan dengan migrasi

    protected $fillable = [
        'user_id',
        'jenis_id',

        'nopol',
        'foto',
        'nama_pemilik',
        'alamat',
        'merk',

        'tahun_pembuatan',
        'tahun_perakitan',

        'isi_silinder',
        'warna',

        'no_rangka',
        'no_mesin',
        'no_bpkb', // ⚠️ sesuaikan migrasi (bukan no_bpkb)

        'warna_tnkb',
        'bahan_bakar',

        'kode_lokasi',
        'no_urut_pendaftaran',

        // 💰 biaya rental
        'harga_sewa_per_hari',
        'harga_sewa_per_jam',
        'batas_biaya',

        'dokumen',
        'masa_berlaku',

        'kilometer_sekarang',
        'limit_km_service',
        'limit_bulan_service',
        'km_terakhir_service',

        'tanggal_terakhir_service',

        'status_service',
        'status_kendaraan',
    ];

    /*
    | RELASI
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }

    public function biayaTambahan()
    {
        return $this->hasMany(BiayaTambahan::class);
    }

    public function biayaTambahans()
    {
        return $this->hasMany(BiayaTambahan::class);
    }

    public function serviceHistory()
    {
        return $this->hasMany(ServiceHistory::class);
    }

    public function gpsKendaraan()
    {
        return $this->hasMany(GpsKendaraan::class);
    }

    public function asuransiKendaraan()
    {
        return $this->hasMany(AsuransiKendaraan::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function biayaOperasional()
    {
        return $this->hasMany(BiayaOperasionalKendaraan::class);
    }

    public function pajaks()
    {
        return $this->hasMany(PajakKendaraan::class);
    }

    public function serviceHistories()
{
    return $this->hasMany(ServiceHistory::class);
}
    /*
    | CASTING
    */

    protected $casts = [
        'tahun_pembuatan' => 'integer',
        'tahun_perakitan' => 'integer',

        'harga_sewa_per_hari' => 'integer',
        'harga_sewa_per_jam' => 'integer',
        'batas_biaya' => 'integer',

        'kilometer_sekarang' => 'integer',
        'limit_km_service' => 'integer',
        'limit_bulan_service' => 'integer',
        'km_terakhir_service' => 'integer',

        'masa_berlaku' => 'date',
        'tanggal_terakhir_service' => 'date',
    ];
}
