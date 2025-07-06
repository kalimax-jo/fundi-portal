<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'request_quota',
        'price',
    ];

    /**
     * The inspection packages allowed for this tier.
     */
    public function inspectionPackages(): BelongsToMany
    {
        return $this->belongsToMany(InspectionPackage::class, 'tier_inspection_package');
    }

    /**
     * (Future) The business partners assigned to this tier.
     */
    public function businessPartners(): HasMany
    {
        return $this->hasMany(BusinessPartner::class);
    }
} 