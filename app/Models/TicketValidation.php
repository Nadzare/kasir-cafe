<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketValidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'scanned_at',
        'scanned_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    /**
     * Relasi ke Transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relasi ke User (Gatekeeper)
     */
    public function gatekeeper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    /**
     * Format waktu scan untuk tampilan
     */
    public function getFormattedScannedAtAttribute()
    {
        return $this->scanned_at->format('d/m/Y H:i:s');
    }

    /**
     * Cek apakah tiket sudah digunakan
     */
    public function isAlreadyUsed(): bool
    {
        return $this->status === 'already_used';
    }
}
