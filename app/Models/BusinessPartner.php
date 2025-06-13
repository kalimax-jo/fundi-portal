<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class BusinessPartner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'type',
        'license_number',
        'registration_number',
        'contact_person',
        'contact_email',
        'contact_phone',
        'address',
        'logo',
        'partnership_start_date',
        'partnership_status',
        'billing_type',
        'discount_percentage'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'partnership_start_date' => 'date',
        'discount_percentage' => 'decimal:2',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get users associated with this business partner
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_partner_users')
            ->withPivot('position', 'department', 'access_level', 'is_primary_contact')
            ->withTimestamps();
    }

    /**
     * Get inspection requests made by this business partner
     */
    public function inspectionRequests(): HasMany
    {
        return $this->hasMany(InspectionRequest::class);
    }

    /**
     * Get billing records for this business partner
     */
    public function billings(): HasMany
    {
        return $this->hasMany(PartnerBilling::class);
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get only active partners
     */
    public function scopeActive($query)
    {
        return $query->where('partnership_status', 'active');
    }

    /**
     * Scope to get partners by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get banks
     */
    public function scopeBanks($query)
    {
        return $query->where('type', 'bank');
    }

    /**
     * Scope to get insurance companies
     */
    public function scopeInsurance($query)
    {
        return $query->where('type', 'insurance');
    }

    /**
     * Scope to get microfinance institutions
     */
    public function scopeMicrofinance($query)
    {
        return $query->where('type', 'microfinance');
    }

    /**
     * Scope to search partners by name
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('contact_person', 'like', "%{$term}%")
            ->orWhere('contact_email', 'like', "%{$term}%");
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if partner is active
     */
    public function isActive(): bool
    {
        return $this->partnership_status === 'active';
    }

    /**
     * Activate partnership
     */
    public function activate(): void
    {
        $this->update(['partnership_status' => 'active']);
    }

    /**
     * Deactivate partnership
     */
    public function deactivate(): void
    {
        $this->update(['partnership_status' => 'inactive']);
    }

    /**
     * Suspend partnership
     */
    public function suspend(): void
    {
        $this->update(['partnership_status' => 'suspended']);
    }

    /**
     * Get primary contact user
     */
    public function getPrimaryContact()
    {
        return $this->users()
            ->wherePivot('is_primary_contact', true)
            ->first();
    }

    /**
     * Set a user as primary contact
     */
    public function setPrimaryContact(User $user): void
    {
        // Remove primary contact from all users
        $this->users()->updateExistingPivot(
            $this->users()->pluck('users.id'),
            ['is_primary_contact' => false]
        );

        // Set new primary contact
        $this->users()->updateExistingPivot($user->id, [
            'is_primary_contact' => true
        ]);
    }

    /**
     * Get admin users for this partner
     */
    public function getAdminUsers()
    {
        return $this->users()
            ->wherePivot('access_level', 'admin')
            ->get();
    }

    /**
     * Get total inspections count
     */
    public function getTotalInspections(): int
    {
        return $this->inspectionRequests()->count();
    }

    /**
     * Get inspections count for current month
     */
    public function getCurrentMonthInspections(): int
    {
        return $this->inspectionRequests()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    /**
     * Get total amount spent
     */
    public function getTotalAmountSpent(): float
    {
        return $this->billings()
            ->where('status', 'paid')
            ->sum('final_amount');
    }

    /**
     * Calculate discounted price
     */
    public function calculateDiscountedPrice(float $originalPrice): float
    {
        if ($this->discount_percentage > 0) {
            $discount = ($originalPrice * $this->discount_percentage) / 100;
            return $originalPrice - $discount;
        }

        return $originalPrice;
    }

    /**
     * Get current billing period
     */
    public function getCurrentBillingPeriod(): array
    {
        $now = Carbon::now();
        
        return [
            'start' => $now->startOfMonth()->toDateString(),
            'end' => $now->endOfMonth()->toDateString()
        ];
    }

    /**
     * Get pending billing amount for current period
     */
    public function getPendingBillingAmount(): float
    {
        $period = $this->getCurrentBillingPeriod();
        
        $inspections = $this->inspectionRequests()
            ->whereDate('created_at', '>=', $period['start'])
            ->whereDate('created_at', '<=', $period['end'])
            ->where('status', 'completed')
            ->with('package')
            ->get();

        $totalAmount = 0;
        foreach ($inspections as $inspection) {
            $totalAmount += $this->calculateDiscountedPrice($inspection->package->price);
        }

        return $totalAmount;
    }

    /**
     * Get partnership duration in months
     */
    public function getPartnershipDurationInMonths(): int
    {
        if (!$this->partnership_start_date) {
            return 0;
        }

        return $this->partnership_start_date->diffInMonths(Carbon::now());
    }

    /**
     * Check if partner qualifies for volume discount
     */
    public function qualifiesForVolumeDiscount(): bool
    {
        $monthlyInspections = $this->getCurrentMonthInspections();
        
        // Define volume thresholds
        $volumeThresholds = [
            'bank' => 20,
            'insurance' => 15,
            'microfinance' => 10,
            'mortgage' => 15,
            'investment' => 10
        ];

        $threshold = $volumeThresholds[$this->type] ?? 10;
        
        return $monthlyInspections >= $threshold;
    }

    /**
     * Get suggested discount percentage based on volume
     */
    public function getSuggestedDiscountPercentage(): float
    {
        if (!$this->qualifiesForVolumeDiscount()) {
            return 0;
        }

        $monthlyInspections = $this->getCurrentMonthInspections();

        // Progressive discount tiers
        if ($monthlyInspections >= 50) {
            return 15.0; // 15% for 50+ inspections
        } elseif ($monthlyInspections >= 30) {
            return 10.0; // 10% for 30+ inspections
        } elseif ($monthlyInspections >= 20) {
            return 7.5;  // 7.5% for 20+ inspections
        } else {
            return 5.0;  // 5% for qualifying volume
        }
    }

    /**
     * Get partner type display name
     */
    public function getTypeDisplayName(): string
    {
        $typeNames = [
            'bank' => 'Bank',
            'insurance' => 'Insurance Company',
            'microfinance' => 'Microfinance Institution',
            'mortgage' => 'Mortgage Company',
            'investment' => 'Investment Firm'
        ];

        return $typeNames[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get billing type display name
     */
    public function getBillingTypeDisplayName(): string
    {
        $billingTypes = [
            'monthly' => 'Monthly Billing',
            'per_inspection' => 'Per Inspection',
            'custom' => 'Custom Arrangement'
        ];

        return $billingTypes[$this->billing_type] ?? ucfirst($this->billing_type);
    }

    /**
     * Get partner statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_inspections' => $this->getTotalInspections(),
            'current_month_inspections' => $this->getCurrentMonthInspections(),
            'total_amount_spent' => $this->getTotalAmountSpent(),
            'pending_billing_amount' => $this->getPendingBillingAmount(),
            'partnership_duration_months' => $this->getPartnershipDurationInMonths(),
            'current_discount_percentage' => $this->discount_percentage,
            'suggested_discount_percentage' => $this->getSuggestedDiscountPercentage(),
            'qualifies_for_volume_discount' => $this->qualifiesForVolumeDiscount(),
            'active_users_count' => $this->users()->count(),
            'admin_users_count' => $this->getAdminUsers()->count()
        ];
    }

    /**
     * Create default business partners for Rwanda
     */
    public static function createDefaultPartners(): void
    {
        $defaultPartners = [
            // Banks
            [
                'name' => 'BPR Bank (Bank Populaire du Rwanda)',
                'type' => 'bank',
                'license_number' => 'BNR-001',
                'contact_person' => 'Maurice Toroitich',
                'contact_email' => 'corporate@bpr.rw',
                'contact_phone' => '+250788123001',
                'partnership_start_date' => '2024-01-01',
                'billing_type' => 'monthly',
                'discount_percentage' => 5.0
            ],
            [
                'name' => 'Bank of Kigali',
                'type' => 'bank',
                'license_number' => 'BNR-002',
                'contact_person' => 'Diane Karusisi',
                'contact_email' => 'corporate@bk.rw',
                'contact_phone' => '+250788123002',
                'partnership_start_date' => '2024-01-01',
                'billing_type' => 'monthly',
                'discount_percentage' => 7.5
            ],
            [
                'name' => 'Equity Bank Rwanda',
                'type' => 'bank',
                'license_number' => 'BNR-003',
                'contact_person' => 'Hannington Namara',
                'contact_email' => 'corporate@equitybank.rw',
                'contact_phone' => '+250788123003',
                'partnership_start_date' => '2024-02-01',
                'billing_type' => 'monthly',
                'discount_percentage' => 5.0
            ],
            // Insurance Companies
            [
                'name' => 'BK General Insurance',
                'type' => 'insurance',
                'license_number' => 'INS-001',
                'contact_person' => 'Insurance Director',
                'contact_email' => 'info@bkgeneral.rw',
                'contact_phone' => '+250788124001',
                'partnership_start_date' => '2024-01-15',
                'billing_type' => 'per_inspection',
                'discount_percentage' => 0.0
            ],
            [
                'name' => 'RADIANT Insurance',
                'type' => 'insurance',
                'license_number' => 'INS-002',
                'contact_person' => 'Claims Manager',
                'contact_email' => 'corporate@radiant.rw',
                'contact_phone' => '+250788124002',
                'partnership_start_date' => '2024-02-01',
                'billing_type' => 'monthly',
                'discount_percentage' => 3.0
            ]
        ];

        foreach ($defaultPartners as $partnerData) {
            self::firstOrCreate(
                ['name' => $partnerData['name']],
                $partnerData
            );
        }
    }
}