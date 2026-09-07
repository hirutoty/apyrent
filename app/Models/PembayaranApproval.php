<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranApproval extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_approvals';

    protected $fillable = [
        'pembayaran_id',
        'user_id',
        'action',
        'catatan',
        'bukti_files',
        'attachment_files',
    ];

    protected $casts = [
        'bukti_files'      => 'array',
        'attachment_files' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke Pembayaran
     */
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }

    /**
     * Relasi ke User (approver)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if this is an approval action
     */
    public function isApproved(): bool
    {
        return $this->action === 'approved';
    }

    /**
     * Check if this is a rejection action
     */
    public function isRejected(): bool
    {
        return $this->action === 'rejected';
    }

    /**
     * Get approver name
     */
    public function getApproverNameAttribute(): string
    {
        return $this->user ? $this->user->nama : 'Unknown';
    }

    /**
     * Get all bukti file paths
     */
    public function getBuktiPathsAttribute(): array
    {
        if (!$this->bukti_files) {
            return [];
        }

        return collect($this->bukti_files)->pluck('path')->toArray();
    }

    /**
     * Get all attachment file paths
     */
    public function getAttachmentPathsAttribute(): array
    {
        if (!$this->attachment_files) {
            return [];
        }

        return collect($this->attachment_files)->pluck('path')->toArray();
    }
}
