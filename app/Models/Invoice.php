<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'penawaran_id',
        'kontrak_id',
        'kendaraan_id',

        'type',
        'invoice_no',
        'order_no',

        'customer_name',
        'customer_address',
        'contact_person',
        'telephone',
        'email',

        'satuan',
        'invoice_date',

        'pengirim',
        'staff',
        'name_staff',
        'direktur',
        'name_direktur',
        'ttd_staff',
        'ttd_direktur',

        'status',
        'payment_status',

        'ppn',
        'pph',
        'total',




        'last_email_sent_at',
    ];

    protected $casts = [

        'invoice_date'       => 'date',
        'ppn'                => 'decimal:2',
        'pph'                => 'decimal:2',
        'total'              => 'decimal:2',

        'invoice_date'   => 'date',
        'ppn'            => 'decimal:2',
        'pph'            => 'decimal:2',
        'total'          => 'decimal:2',

        'last_email_sent_at' => 'datetime',
    ];

    /**
     * Relasi ke Penawaran (single, backward-compat)
     */
    public function penawaran()
    {
        return $this->belongsTo(InvPenawaran::class, 'penawaran_id');
    }

    /**
     * Relasi ke Kontrak (single, backward-compat)
     */
    public function kontrak()
    {
        return $this->belongsTo(InvKontrak::class, 'kontrak_id');
    }

    /**
     * Relasi ke Kendaraan (single, backward-compat)
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    /**
     * Relasi ke Penawarans (multiple via pivot)
     */
    public function penawarans()
    {
        return $this->belongsToMany(InvPenawaran::class, 'invoice_penawarans', 'invoice_id', 'penawaran_id')
                    ->withTimestamps();
    }

    /**
     * Relasi ke Kontraks (multiple via pivot)
     */
    public function kontraks()
    {
        return $this->belongsToMany(InvKontrak::class, 'invoice_kontraks', 'invoice_id', 'kontrak_id')
                    ->withTimestamps();
    }

    /**
     * Relasi ke Kendaraans (multiple via pivot)
     */
    public function kendaraans()
    {
        return $this->belongsToMany(Kendaraan::class, 'invoice_kendaraans', 'invoice_id', 'kendaraan_id')
                    ->withTimestamps();
    }


    /**
     * Relasi ke Periode sewa (hasMany InvoicePeriode)
     */
    public function periodes()
    {
        return $this->hasMany(InvoicePeriode::class, 'invoice_id');
    }

    /**
     * Relasi ke semua Remaks invoice
     */
    public function remaks()
    {
        return $this->hasMany(InvoiceRemak::class, 'invoice_id');
    }

    public function agingAr()
    {
        return $this->hasOne(AgingAr::class, 'invoice_id');
    }
}
