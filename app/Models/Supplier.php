<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'user_id',
        'nama_supplier',
        'no_telp',
        'alamat',
        'nama_marketing',
        'kontak_marketing',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke pembayarans (purchase order / PR)
     */
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Relasi ke service_parts (part yang dipasang via service history)
     */
    public function serviceParts()
    {
        return $this->hasMany(ServicePart::class, 'supplier_id');
    }

    /**
     * Relasi ke service_incident_parts (part yang dipasang via service incident)
     */
    public function incidentParts()
    {
        return $this->hasMany(ServiceIncidentPart::class, 'supplier_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR GABUNGAN
    | Menggabungkan data dari 3 sumber: pembayarans, serviceParts, incidentParts
    |--------------------------------------------------------------------------
    */

    /**
     * Nama barang gabungan (PR items + service parts + incident parts)
     * Mengambil hingga 2 nama unik untuk tampilan ringkas di tabel
     */
    public function getNamaBarangGabunganAttribute(): string
    {
        $fromPr = $this->pembayarans->flatMap(function ($pr) {
            if ($pr->items && $pr->items->isNotEmpty()) {
                return $pr->items->pluck('nama_barang');
            }
            return collect([$pr->barang_jasa]);
        })->filter();

        $fromService  = $this->serviceParts->pluck('nama_part');
        $fromIncident = $this->incidentParts->pluck('nama_part');

        return $fromPr->merge($fromService)
                      ->merge($fromIncident)
                      ->unique()
                      ->take(2)
                      ->implode(', ') ?: '-';
    }

    /**
     * Total jumlah barang gabungan dari semua sumber
     */
    public function getJumlahTotalGabunganAttribute(): int
    {
        $fromPr = $this->pembayarans->flatMap(function ($pr) {
            if ($pr->items && $pr->items->isNotEmpty()) {
                return $pr->items->pluck('qty');
            }
            return collect([$pr->qty ?? 0]);
        })->sum();

        return $fromPr
            + $this->serviceParts->count()
            + $this->incidentParts->count();
    }

    /**
     * Harga rata-rata dari semua item (PR items + service parts + incident parts)
     */
    public function getHargaRataGabunganAttribute(): float
    {
        $allHarga = $this->pembayarans->flatMap(function ($pr) {
            if ($pr->items && $pr->items->isNotEmpty()) {
                return $pr->items->pluck('harga_satuan');
            }
            return collect([]);
        })->merge($this->serviceParts->pluck('biaya'))
          ->merge($this->incidentParts->pluck('biaya'))
          ->filter();

        return $allHarga->isNotEmpty() ? (float) $allHarga->avg() : 0.0;
    }

    /**
     * Total nominal gabungan dari semua sumber
     */
    public function getTotalNominalGabunganAttribute(): float
    {
        $fromPr = $this->pembayarans->sum(function ($pr) {
            if ($pr->items && $pr->items->isNotEmpty()) {
                return $pr->items->sum('subtotal');
            }
            return $pr->nominal ?? 0;
        });

        return $fromPr
            + $this->serviceParts->sum('biaya')
            + $this->incidentParts->sum('biaya');
    }
}
