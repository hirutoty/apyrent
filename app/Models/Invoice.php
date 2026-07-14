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
        'last_email_sent_at' => 'datetime',
    ];

    public function penawaran() { return $this->belongsTo(InvPenawaran::class, 'penawaran_id'); }
    public function kontrak() { return $this->belongsTo(InvKontrak::class, 'kontrak_id'); }
    public function kendaraan() { return $this->belongsTo(Kendaraan::class, 'kendaraan_id'); }
    public function penawarans() { return $this->belongsToMany(InvPenawaran::class, 'invoice_penawarans', 'invoice_id', 'penawaran_id')->withTimestamps(); }
    public function kontraks() { return $this->belongsToMany(InvKontrak::class, 'invoice_kontraks', 'invoice_id', 'kontrak_id')->withTimestamps(); }
    public function kendaraans() { return $this->belongsToMany(Kendaraan::class, 'invoice_kendaraans', 'invoice_id', 'kendaraan_id')->withTimestamps(); }
    public function periodes() { return $this->hasMany(InvoicePeriode::class, 'invoice_id'); }
    public function remaks() { return $this->hasMany(InvoiceRemak::class, 'invoice_id'); }
    public function agingAr() { return $this->hasOne(AgingAr::class, 'invoice_id'); }

    /**
     * Hitung total invoice dari remaks (qty * price) + ppn - pph.
     *
     * Prioritas sumber remaks:
     *   1. periodes.remaks  — remaks yang terikat ke periode invoice
     *   2. remaks langsung  — remaks yang langsung terikat ke invoice (tanpa periode)
     *
     * Jika tidak ada remaks sama sekali dari kedua sumber, fallback ke nilai
     * kolom $this->total yang tersimpan di database.
     *
     * @return float
     */
    public function computeTotal(): float
    {
        // Eager-load periodes beserta remaks-nya jika belum di-load
        if (! $this->relationLoaded('periodes')) {
            $this->load('periodes.remaks');
        } elseif ($this->periodes->isNotEmpty() && ! $this->periodes->first()->relationLoaded('remaks')) {
            $this->load('periodes.remaks');
        }

        // Kumpulkan semua remaks dari periodes
        $remaksFromPeriodes = $this->periodes->flatMap(fn ($periode) => $periode->remaks);

        if ($remaksFromPeriodes->isNotEmpty()) {
            $subtotal = $remaksFromPeriodes->sum(fn ($remak) => (float) $remak->qty * (float) $remak->price);
        } else {
            // Fallback: gunakan remaks yang langsung terikat ke invoice
            if (! $this->relationLoaded('remaks')) {
                $this->load('remaks');
            }

            if ($this->remaks->isEmpty()) {
                // Tidak ada remaks sama sekali — kembalikan nilai tersimpan
                return (float) $this->total;
            }

            $subtotal = $this->remaks->sum(fn ($remak) => (float) $remak->qty * (float) $remak->price);
        }

        return $subtotal + (float) $this->ppn - (float) $this->pph;
    }
}
