<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pelanggan;

class Rental extends Model
{
    use HasFactory;

    protected $table = 'rentals';

    protected $fillable = [
        'user_id',
        'kendaraan_id',
        'member_id',

        'tanggal_mulai',
        'tanggal_selesai',
        'tujuan',

        'durasi_bulan',
        'durasi_jam',
        'durasi_hari',
        'durasi_tahun',

        'biaya_dasar',
        'biaya_tambahan_total',
        'total_biaya',

        'metode_pembayaran',
        'jenis_pembayaran',

        'nominal_dp',

        'nama_driver',
        'kontak_driver',
        'biaya_driver',

        'bukti_lunas',
        'bukti_dp',
        'bukti_pelunasan',

        'kelayakan',
        'invoice',

        'status_pembayaran',
        'status',
    ];

    /*
    | RELASI
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function member()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function biayaTambahans()
    {
        return $this->belongsToMany(BiayaTambahan::class, 'rental_biaya_tambahan')
            ->withPivot('jumlah', 'subtotal');
    }


    /*
    | HITUNG TOTAL
    */

    public function hitungTotal()
    {
        $k = $this->kendaraan;

        $dasar = ($this->durasi_hari * $k->harga_sewa_per_hari)
            + ($this->durasi_jam * $k->harga_sewa_per_jam);

        $tambahan = $this->biayaTambahans->sum('pivot.subtotal');

        return [
            'biaya_dasar' => $dasar,
            'biaya_tambahan_total' => $tambahan,
            'total' => $dasar + $tambahan
        ];
    }

    public function deposit()
    {
        return $this->hasOne(DepositCustomer::class);
    }

    public function dendas()
    {
        return $this->hasMany(DendaRental::class);
    }
}
