<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class AsuransiHistory extends Model
{

        protected $table = 'asuransi_history';

        protected $fillable = [
            'asuransi_kendaraan_id',
            'kendaraan_id',
            'asuransi_id',
            'jenis_asuransi_id',
            'tgl_mulai',
            'tgl_berakhir',
            'durasi_bulan',
            'biaya',
            'bukti_bayar',
            'status_kendaraan',
            'diperpanjang_pada',
            'tanggal_bayar',
        ];

        public function kendaraan()
        {
            return $this->belongsTo(Kendaraan::class);
        }

        public function asuransi()
        {
            return $this->belongsTo(Asuransi::class);
        }

        public function jenisAsuransi()
        {
            return $this->belongsTo(JenisAsuransi::class, 'jenis_asuransi_id');
        }
}

   
