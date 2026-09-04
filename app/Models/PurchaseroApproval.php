<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseroApproval extends Model
{
    use HasFactory;

    protected $table = 'purchasero_approvals';

    protected $fillable = [
        'purchasero_id',
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
     * Relasi ke Purchasero
     */
    public function purchasero()
    {
        return $this->belongsTo(Purchasero::class);
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
