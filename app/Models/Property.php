<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Property extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'property_code',
        'owner_name',
        'owner_phone',
        'owner_email',
        'property_type',
        'property_subtype',
        'address',
        'district',
        'sector',
        'cell',
        'latitude',
        'longitude',
        'built_year',
        'total_area_sqm',
        'floors_count',
        'bedrooms_count',
        'bathrooms_count',
        'market_value',
        'last_inspection_date',
        'property_photos',
        'additional_notes'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'total_area_sqm' => 'decimal:2',
        'market_value' => 'decimal:2',
        'last_inspection_date' => 'date',
        'property_photos' => 'array',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get inspection requests for this property
     */
    public function inspectionRequests(): HasMany
    {
        return $this->hasMany(InspectionRequest::class);
    }

    /**
     * Get completed inspections for this property
     */
    public function completedInspections(): HasMany
    {
        return $this->hasMany(InspectionRequest::class)
            ->where('status', 'completed');
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get properties by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('property_type', $type);
    }

    /**
     * Scope to get residential properties
     */
    public function scopeResidential($query)
    {
        return $query->where('property_type', 'residential');
    }

    /**
     * Scope to get commercial properties
     */
    public function scopeCommercial($query)
    {
        return $query->where('property_type', 'commercial');
    }

    /**
     * Scope to get properties by location
     */
    public function scopeByLocation($query, $district = null, $sector = null, $cell = null)
    {
        if ($district) {
            $query->where('district', $district);
        }
        if ($sector) {
            $query->where('sector', $sector);
        }
        if ($cell) {
            $query->where('cell', $cell);
        }
        return $query;
    }

    /**
     * Scope to search properties
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('property_code', 'like', "%{$term}%")
              ->orWhere('owner_name', 'like', "%{$term}%")
              ->orWhere('address', 'like', "%{$term}%")
              ->orWhere('district', 'like', "%{$term}%")
              ->orWhere('sector', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to get properties within a radius (in kilometers)
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radiusKm)
    {
        return $query->whereRaw(
            "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?",
            [$latitude, $longitude, $latitude, $radiusKm]
        );
    }

    /**
     * Scope to get properties needing inspection
     */
    public function scopeNeedsInspection($query, $monthsThreshold = 12)
    {
        $thresholdDate = Carbon::now()->subMonths($monthsThreshold);
        
        return $query->where(function ($q) use ($thresholdDate) {
            $q->whereNull('last_inspection_date')
              ->orWhere('last_inspection_date', '<', $thresholdDate);
        });
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Generate unique property code
     */
    public static function generatePropertyCode(): string
    {
        do {
            $code = 'PROP' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('property_code', $code)->exists());

        return $code;
    }

    /**
     * Get property type display name
     */
    public function getTypeDisplayName(): string
    {
        $typeNames = [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'mixed' => 'Mixed Use'
        ];

        return $typeNames[$this->property_type] ?? ucfirst($this->property_type);
    }

    /**
     * Get property subtype display name
     */
    public function getSubtypeDisplayName(): string
    {
        $subtypeNames = [
            // Residential
            'house' => 'House',
            'apartment' => 'Apartment',
            'villa' => 'Villa',
            'duplex' => 'Duplex',
            'townhouse' => 'Townhouse',
            'condo' => 'Condominium',
            
            // Commercial
            'office' => 'Office Building',
            'retail' => 'Retail Space',
            'restaurant' => 'Restaurant',
            'hotel' => 'Hotel',
            'shopping_center' => 'Shopping Center',
            
            // Industrial
            'warehouse' => 'Warehouse',
            'factory' => 'Factory',
            'manufacturing' => 'Manufacturing Facility'
        ];

        return $subtypeNames[$this->property_subtype] ?? ucfirst(str_replace('_', ' ', $this->property_subtype));
    }

    /**
     * Get full location string
     */
    public function getFullLocationAttribute(): string
    {
        $location = [];
        
        if ($this->cell) $location[] = $this->cell;
        if ($this->sector) $location[] = $this->sector;
        if ($this->district) $location[] = $this->district;
        
        return implode(', ', $location);
    }

    /**
     * Get property age in years
     */
    public function getPropertyAge(): ?int
    {
        if (!$this->built_year) {
            return null;
        }

        return Carbon::now()->year - $this->built_year;
    }

    /**
     * Get months since last inspection
     */
    public function getMonthsSinceLastInspection(): ?int
    {
        if (!$this->last_inspection_date) {
            return null;
        }

        return $this->last_inspection_date->diffInMonths(Carbon::now());
    }

    /**
     * Check if property needs inspection
     */
    public function needsInspection($monthsThreshold = 12): bool
    {
        if (!$this->last_inspection_date) {
            return true; // Never been inspected
        }

        return $this->getMonthsSinceLastInspection() >= $monthsThreshold;
    }

    /**
     * Get inspection history count
     */
    public function getInspectionCount(): int
    {
        return $this->completedInspections()->count();
    }

    /**
     * Get latest inspection
     */
    public function getLatestInspection()
    {
        return $this->completedInspections()
            ->latest('completed_at')
            ->first();
    }

    /**
     * Calculate distance to coordinates (in kilometers)
     */
    public function distanceTo($latitude, $longitude): ?float
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        // Haversine formula
        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + 
                 cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Get property value per square meter
     */
    public function getValuePerSquareMeter(): ?float
    {
        if (!$this->market_value || !$this->total_area_sqm || $this->total_area_sqm == 0) {
            return null;
        }

        return $this->market_value / $this->total_area_sqm;
    }

    /**
     * Get recommended inspection package
     */
    public function getRecommendedPackage(): string
    {
        // A-Check for rentals and basic inspections
        // B-Check for sales/purchases
        // C-Check for commercial properties

        if ($this->property_type === 'commercial' || $this->property_type === 'industrial') {
            return 'C_CHECK';
        }

        if ($this->market_value > 50000000) { // High-value properties
            return 'B_CHECK';
        }

        return 'A_CHECK';
    }

    /**
     * Add photo to property
     */
    public function addPhoto(string $photoPath): void
    {
        $photos = $this->property_photos ?? [];
        $photos[] = $photoPath;
        $this->update(['property_photos' => $photos]);
    }

    /**
     * Remove photo from property
     */
    public function removePhoto(string $photoPath): void
    {
        $photos = $this->property_photos ?? [];
        $photos = array_filter($photos, fn($photo) => $photo !== $photoPath);
        $this->update(['property_photos' => array_values($photos)]);
    }

    /**
     * Get property statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_inspections' => $this->getInspectionCount(),
            'months_since_last_inspection' => $this->getMonthsSinceLastInspection(),
            'needs_inspection' => $this->needsInspection(),
            'property_age_years' => $this->getPropertyAge(),
            'value_per_sqm' => $this->getValuePerSquareMeter(),
            'recommended_package' => $this->getRecommendedPackage(),
            'photo_count' => count($this->property_photos ?? []),
            'has_coordinates' => !is_null($this->latitude) && !is_null($this->longitude)
        ];
    }

    /**
     * Get Rwanda districts list
     */
    public static function getRwandaDistricts(): array
    {
        return [
            'Kigali' => [
                'Gasabo', 'Kicukiro', 'Nyarugenge'
            ],
            'Eastern' => [
                'Bugesera', 'Gatsibo', 'Kayonza', 'Kirehe', 'Ngoma', 'Nyagatare', 'Rwamagana'
            ],
            'Northern' => [
                'Burera', 'Gakenke', 'Gicumbi', 'Musanze', 'Rulindo'
            ],
            'Southern' => [
                'Gisagara', 'Huye', 'Kamonyi', 'Muhanga', 'Nyamagabe', 'Nyanza', 'Nyaruguru', 'Ruhango'
            ],
            'Western' => [
                'Karongi', 'Ngororero', 'Nyabihu', 'Nyamasheke', 'Rubavu', 'Rusizi', 'Rutsiro'
            ]
        ];
    }

    /**
     * Get property subtypes by type
     */
    public static function getSubtypesByType(): array
    {
        return [
            'residential' => [
                'house' => 'House',
                'apartment' => 'Apartment',
                'villa' => 'Villa',
                'duplex' => 'Duplex',
                'townhouse' => 'Townhouse',
                'condo' => 'Condominium'
            ],
            'commercial' => [
                'office' => 'Office Building',
                'retail' => 'Retail Space',
                'restaurant' => 'Restaurant',
                'hotel' => 'Hotel',
                'shopping_center' => 'Shopping Center',
                'bank' => 'Bank Branch',
                'clinic' => 'Medical Clinic'
            ],
            'industrial' => [
                'warehouse' => 'Warehouse',
                'factory' => 'Factory',
                'manufacturing' => 'Manufacturing Facility',
                'logistics' => 'Logistics Center'
            ],
            'mixed' => [
                'residential_commercial' => 'Residential + Commercial',
                'office_retail' => 'Office + Retail',
                'multi_purpose' => 'Multi-Purpose Building'
            ]
        ];
    }

    /**
     * Auto-generate property code before creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            if (!$property->property_code) {
                $property->property_code = self::generatePropertyCode();
            }
        });
    }
}