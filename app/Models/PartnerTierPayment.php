<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerTierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_tier_id',
        'amount',
        'paid_at',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function partnerTier(): BelongsTo
    {
        return $this->belongsTo(PartnerTier::class);
    }
} 