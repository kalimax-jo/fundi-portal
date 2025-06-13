<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionPackage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'price',
        'currency',
        'duration_hours',
        'is_custom_quote',
        'target_client_type',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_custom_quote' => 'boolean',
        'is_active' => 'boolean',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get services included in this package
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(InspectionService::class, 'package_services')
            ->withPivot('is_mandatory', 'sort_order')
            ->withTimestamps()
            ->orderBy('package_services.sort_order');
    }

    /**
     * Get mandatory services for this package
     */
    public function mandatoryServices(): BelongsToMany
    {
        return $this->services()->wherePivot('is_mandatory', true);
    }

    /**
     * Get optional services for this package
     */
    public function optionalServices(): BelongsToMany
    {
        return $this->services()->wherePivot('is_mandatory', false);
    }

    /**
     * Get inspection requests using this package
     */
    public function inspectionRequests(): HasMany
    {
        return $this->hasMany(InspectionRequest::class, 'package_id');
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get only active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get packages by client type
     */
    public function scopeForClientType($query, $clientType)
    {
        return $query->where(function ($q) use ($clientType) {
            $q->where('target_client_type', $clientType)
              ->orWhere('target_client_type', 'both');
        });
    }

    /**
     * Scope to get packages for individual clients
     */
    public function scopeForIndividuals($query)
    {
        return $query->forClientType('individual');
    }

    /**
     * Scope to get packages for business partners
     */
    public function scopeForBusinessPartners($query)
    {
        return $query->forClientType('business');
    }

    /**
     * Scope to get fixed-price packages (not custom quotes)
     */
    public function scopeFixedPrice($query)
    {
        return $query->where('is_custom_quote', false);
    }

    /**
     * Scope to get custom quote packages
     */
    public function scopeCustomQuote($query)
    {
        return $query->where('is_custom_quote', true);
    }

    /**
     * Scope to order by price
     */
    public function scopeOrderByPrice($query, $direction = 'asc')
    {
        return $query->orderBy('price', $direction);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if package is available for client type
     */
    public function isAvailableForClientType(string $clientType): bool
    {
        return $this->target_client_type === 'both' || $this->target_client_type === $clientType;
    }

    /**
     * Get total estimated duration including all services
     */
    public function getTotalEstimatedDuration(): int
    {
        $serviceDuration = $this->services()->sum('estimated_duration_minutes');
        
        // Add buffer time (20% of service duration or minimum 30 minutes)
        $bufferTime = max(30, $serviceDuration * 0.2);
        
        return $serviceDuration + $bufferTime;
    }

    /**
     * Get services count
     */
    public function getServicesCount(): int
    {
        return $this->services()->count();
    }

    /**
     * Get mandatory services count
     */
    public function getMandatoryServicesCount(): int
    {
        return $this->mandatoryServices()->count();
    }

    /**
     * Get services by category
     */
    public function getServicesByCategory(): array
    {
        $services = $this->services()->get();
        $grouped = [];

        foreach ($services as $service) {
            $category = $service->category;
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $service;
        }

        return $grouped;
    }

    /**
     * Get price with currency formatting
     */
    public function getFormattedPrice(): string
    {
        if ($this->is_custom_quote) {
            return 'Custom Quote';
        }

        return number_format($this->price, 0, '.', ',') . ' ' . $this->currency;
    }

    /**
     * Calculate discounted price for business partner
     */
    public function getDiscountedPrice(float $discountPercentage = 0): float
    {
        if ($this->is_custom_quote) {
            return 0; // Custom quotes handled separately
        }

        if ($discountPercentage > 0) {
            $discount = ($this->price * $discountPercentage) / 100;
            return $this->price - $discount;
        }

        return $this->price;
    }

    /**
     * Get package usage statistics
     */
    public function getUsageStatistics(): array
    {
        $totalRequests = $this->inspectionRequests()->count();
        $completedRequests = $this->inspectionRequests()->where('status', 'completed')->count();
        $thisMonthRequests = $this->inspectionRequests()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total_requests' => $totalRequests,
            'completed_requests' => $completedRequests,
            'completion_rate' => $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100, 2) : 0,
            'this_month_requests' => $thisMonthRequests,
            'services_count' => $this->getServicesCount(),
            'estimated_duration_minutes' => $this->getTotalEstimatedDuration()
        ];
    }

    /**
     * Check if package includes a specific service
     */
    public function includesService(string $serviceName): bool
    {
        return $this->services()->where('name', $serviceName)->exists();
    }

    /**
     * Add service to package
     */
    public function addService(InspectionService $service, bool $isMandatory = true, int $sortOrder = 0): void
    {
        if (!$this->services()->where('inspection_service_id', $service->id)->exists()) {
            $this->services()->attach($service->id, [
                'is_mandatory' => $isMandatory,
                'sort_order' => $sortOrder ?: ($this->services()->count() + 1)
            ]);
        }
    }

    /**
     * Remove service from package
     */
    public function removeService(InspectionService $service): void
    {
        $this->services()->detach($service->id);
    }

    /**
     * Update service settings in package
     */
    public function updateService(InspectionService $service, bool $isMandatory = null, int $sortOrder = null): void
    {
        $updateData = [];
        
        if (!is_null($isMandatory)) {
            $updateData['is_mandatory'] = $isMandatory;
        }
        
        if (!is_null($sortOrder)) {
            $updateData['sort_order'] = $sortOrder;
        }

        if (!empty($updateData)) {
            $this->services()->updateExistingPivot($service->id, $updateData);
        }
    }

    /**
     * Get recommended use cases for package
     */
    public function getRecommendedUseCases(): array
    {
        $useCases = [
            'A_CHECK' => [
                'Rental properties (before/after lease)',
                'Basic property condition assessment',
                'Annual maintenance checks',
                'Insurance requirement compliance',
                'Quick property overview'
            ],
            'B_CHECK' => [
                'Property buying/selling transactions',
                'Comprehensive property evaluation',
                'Real estate due diligence',
                'Loan collateral assessment',
                'Investment property analysis'
            ],
            'C_CHECK' => [
                'Commercial property assessments',
                'High-value residential properties',
                'Complete property health evaluation',
                'Environmental compliance checks',
                'Long-term property planning (5+ years)'
            ]
        ];

        return $useCases[$this->name] ?? [];
    }

    /**
     * Get client type display name
     */
    public function getClientTypeDisplayName(): string
    {
        $clientTypes = [
            'individual' => 'Individual Clients',
            'business' => 'Business Partners',
            'both' => 'All Client Types'
        ];

        return $clientTypes[$this->target_client_type] ?? ucfirst($this->target_client_type);
    }

    /**
     * Update package price (admin only)
     */
    public function updatePrice(float $newPrice): void
    {
        $this->update(['price' => $newPrice]);
    }

    /**
     * Set as custom quote package
     */
    public function setAsCustomQuote(): void
    {
        $this->update([
            'is_custom_quote' => true,
            'price' => 0.00
        ]);
    }

    /**
     * Set as fixed price package
     */
    public function setAsFixedPrice(float $price): void
    {
        $this->update([
            'is_custom_quote' => false,
            'price' => $price
        ]);
    }

    /**
     * Create default inspection packages (without fixed prices)
     */
    public static function createDefaultPackages(): void
    {
        $packages = [
            [
                'name' => 'A_CHECK',
                'display_name' => 'A-Check Package',
                'description' => 'Exterior, interior, plumbing, electrical, air, and fire safety. Recommended before and after rental.',
                'price' => 0.00, // Admin will set price
                'currency' => 'RWF',
                'duration_hours' => 3,
                'is_custom_quote' => false,
                'target_client_type' => 'both',
                'is_active' => true
            ],
            [
                'name' => 'B_CHECK',
                'display_name' => 'B-Check Package',
                'description' => 'A-Check + foundation cracks, garden trees, fence, and flooding risks. Recommended for buy or sell.',
                'price' => 0.00, // Admin will set price
                'currency' => 'RWF',
                'duration_hours' => 5,
                'is_custom_quote' => false,
                'target_client_type' => 'both',
                'is_active' => true
            ],
            [
                'name' => 'C_CHECK',
                'display_name' => 'C-Check Package',
                'description' => 'Comprehensive inspection (A+B), environmental hazards, and septic tank. Recommended every 5 years.',
                'price' => 0.00,
                'currency' => 'RWF',
                'duration_hours' => 8,
                'is_custom_quote' => true, // Always custom quote
                'target_client_type' => 'both',
                'is_active' => true
            ]
        ];

        foreach ($packages as $packageData) {
            self::firstOrCreate(
                ['name' => $packageData['name']],
                $packageData
            );
        }
    }

    /**
     * Get package comparison data
     */
    public static function getPackageComparison(): array
    {
        $packages = self::active()->orderBy('price')->get();
        $comparison = [];

        foreach ($packages as $package) {
            $comparison[] = [
                'name' => $package->name,
                'display_name' => $package->display_name,
                'price' => $package->getFormattedPrice(),
                'duration_hours' => $package->duration_hours,
                'services_count' => $package->getServicesCount(),
                'target_clients' => $package->getClientTypeDisplayName(),
                'recommended_for' => $package->getRecommendedUseCases(),
                'is_custom_quote' => $package->is_custom_quote
            ];
        }

        return $comparison;
    }
}