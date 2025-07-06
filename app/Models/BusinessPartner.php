<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusinessPartner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'subdomain',
        'email',
        'phone',
        'website',
        'type',
        'tier',
        'license_number',
        'registration_number',
        'contact_person',
        'contact_email',
        'contact_phone',
        'address',
        'city',
        'country',
        'logo',
        'partnership_start_date',
        'contract_end_date',
        'partnership_status',
        'billing_type',
        'billing_cycle',
        'discount_percentage',
        'credit_limit',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'partnership_start_date' => 'date',
        'contract_end_date' => 'date',
        'discount_percentage' => 'decimal:2',
        'credit_limit' => 'decimal:2',
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
            ->withPivot('position', 'department', 'access_level', 'is_primary_contact', 'added_by', 'added_at')
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

    /**
     * Get properties associated with this business partner through inspection requests
     */
    public function properties()
    {
        return $this->hasManyThrough(
            Property::class, 
            InspectionRequest::class,
            'business_partner_id', // Foreign key on inspection_requests table
            'id', // Foreign key on properties table
            'id', // Local key on business_partners table
            'property_id' // Local key on inspection_requests table
        );
    }

    /**
     * Get properties directly owned by this business partner
     */
    public function ownedProperties()
    {
        return $this->hasMany(Property::class, 'business_partner_id');
    }

    // =============================================
    // BUSINESS METHODS
    // =============================================

    /**
     * Set a user as the primary contact
     */
    /**
 * Set a user as the primary contact
 */
public function setPrimaryContact(User $user)
{
    DB::beginTransaction();
    
    try {
        // Remove primary contact status from all users
        DB::table('business_partner_users')
            ->where('business_partner_id', $this->id)
            ->update(['is_primary_contact' => false]);

        // Set new primary contact
        DB::table('business_partner_users')
            ->where('business_partner_id', $this->id)
            ->where('user_id', $user->id)
            ->update(['is_primary_contact' => true]);

        DB::commit();
        return true;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

    /**
     * Get the primary contact user
     */
    public function getPrimaryContact()
    {
        return $this->users()->wherePivot('is_primary_contact', true)->first();
    }

    /**
     * Get the primary contact user (attribute accessor)
     */
    public function getPrimaryContactAttribute()
    {
        return $this->users()->wherePivot('is_primary_contact', true)->first();
    }

    /**
     * Check if partner has a primary contact
     */
    public function hasPrimaryContact(): bool
    {
        return $this->users()->wherePivot('is_primary_contact', true)->exists();
    }

    /**
     * Get admin users for this business partner
     */
    public function getAdminUsers()
    {
        return $this->users()->wherePivot('access_level', 'admin');
    }

    /**
     * Get total inspections count
     */
    public function getTotalInspections()
    {
        return $this->inspectionRequests()->count();
    }

    /**
     * Get current month inspections count
     */
    public function getCurrentMonthInspections()
    {
        return $this->inspectionRequests()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    /**
     * Get total amount spent
     */
    public function getTotalAmountSpent()
    {
        return $this->billings()->where('status', 'paid')->sum('final_amount');
    }

    /**
     * Get pending billing amount
     */
    public function getPendingBillingAmount()
    {
        return $this->billings()->whereIn('status', ['pending', 'sent'])->sum('final_amount');
    }

    /**
     * Get partnership duration in months
     */
    public function getPartnershipDurationInMonths()
    {
        if (!$this->partnership_start_date) {
            return 0;
        }
        
        return Carbon::parse($this->partnership_start_date)->diffInMonths(Carbon::now());
    }

    /**
     * Calculate suggested discount percentage based on volume
     */
    public function getSuggestedDiscountPercentage()
    {
        $totalInspections = $this->getTotalInspections();
        
        if ($totalInspections >= 100) {
            return 15.0;
        } elseif ($totalInspections >= 50) {
            return 10.0;
        } elseif ($totalInspections >= 25) {
            return 5.0;
        }
        
        return 0.0;
    }

    /**
     * Check if partner qualifies for volume discount
     */
    public function qualifiesForVolumeDiscount(): bool
    {
        return $this->getTotalInspections() >= 25;
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
     * Get the full subdomain URL
     */
    public function getSubdomainUrlAttribute()
    {
        if (!$this->subdomain) {
            return null;
        }
        
        $domain = config('app.domain', 'fundi.info');
        return "https://{$this->subdomain}.{$domain}";
    }

    /**
     * Generate a unique subdomain from the business partner name
     */
    public function generateSubdomain()
    {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->name));
        $subdomain = $base;
        $counter = 1;
        
        while (BusinessPartner::where('subdomain', $subdomain)->where('id', '!=', $this->id)->exists()) {
            $subdomain = $base . $counter;
            $counter++;
        }
        
        return $subdomain;
    }

    /**
     * Check if the business partner has a subdomain
     */
    public function hasSubdomain(): bool
    {
        return !empty($this->subdomain);
    }

    /**
     * Get users with specific access level
     */
    public function getUsersByAccessLevel($accessLevel)
    {
        return $this->users()->wherePivot('access_level', $accessLevel);
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
     * Scope to get mortgage companies
     */
    public function scopeMortgage($query)
    {
        return $query->where('type', 'mortgage');
    }

    /**
     * Scope to get investment firms
     */
    public function scopeInvestment($query)
    {
        return $query->where('type', 'investment');
    }

    /**
     * Scope to search partners
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('contact_person', 'like', "%{$term}%")
              ->orWhere('registration_number', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to get partners by tier
     */
    public function scopeByTier($query, $tier)
    {
        return $query->where('tier', $tier);
    }

    /**
     * Scope to get premium partners (gold/platinum)
     */
    public function scopePremium($query)
    {
        return $query->whereIn('tier', ['gold', 'platinum']);
    }

    /**
     * Scope to get partners with contracts expiring soon
     */
    public function scopeExpiringContracts($query, $days = 30)
    {
        return $query->whereNotNull('contract_end_date')
            ->where('contract_end_date', '<=', Carbon::now()->addDays($days))
            ->where('contract_end_date', '>=', Carbon::now());
    }

    // =============================================
    // ACCESSORS & MUTATORS
    // =============================================

    /**
     * Get the partner's full display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . ucfirst($this->type) . ')';
    }

    /**
     * Get formatted discount percentage
     */
    public function getFormattedDiscountAttribute()
    {
        return $this->discount_percentage . '%';
    }

    /**
     * Get partnership status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->partnership_status) {
            'active' => 'green',
            'inactive' => 'gray',
            'suspended' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get tier badge color
     */
    public function getTierColorAttribute()
    {
        return match($this->tier) {
            'bronze' => 'yellow',
            'silver' => 'gray',
            'gold' => 'yellow',
            'platinum' => 'purple',
            default => 'gray'
        };
    }

    /**
     * Get the partner type display name
     */
    public function getTypeDisplayNameAttribute()
    {
        $types = [
            'bank' => 'Bank',
            'insurance' => 'Insurance Company',
            'microfinance' => 'Microfinance Institution',
            'mortgage' => 'Mortgage Company',
            'investment' => 'Investment Firm'
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get the partner type display name (method version)
     */
    public function getTypeDisplayName(): string
    {
        return $this->getTypeDisplayNameAttribute();
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
     * Get billing cycle display name
     */
    public function getBillingCycleDisplayName(): string
    {
        $cycles = [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'annually' => 'Annually'
        ];

        return $cycles[$this->billing_cycle] ?? ucfirst($this->billing_cycle);
    }

    /**
     * Get formatted credit limit
     */
    public function getFormattedCreditLimitAttribute()
    {
        if (!$this->credit_limit) {
            return 'No limit set';
        }

        return number_format($this->credit_limit, 0) . ' RWF';
    }

    /**
     * Check if contract is expiring soon
     */
    public function getIsContractExpiringSoonAttribute()
    {
        if (!$this->contract_end_date) {
            return false;
        }

        return Carbon::parse($this->contract_end_date)->diffInDays(Carbon::now()) <= 30;
    }

    /**
     * Get days until contract expiry
     */
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->contract_end_date) {
            return null;
        }

        $days = Carbon::now()->diffInDays(Carbon::parse($this->contract_end_date), false);
        return $days > 0 ? $days : 0;
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
     * Check if partner is suspended
     */
    public function isSuspended(): bool
    {
        return $this->partnership_status === 'suspended';
    }

    /**
     * Check if partner is premium tier
     */
    public function isPremium(): bool
    {
        return in_array($this->tier, ['gold', 'platinum']);
    }

    /**
     * Activate partner
     */
    public function activate(): bool
    {
        return $this->update(['partnership_status' => 'active']);
    }

    /**
     * Deactivate partner
     */
    public function deactivate(): bool
    {
        return $this->update(['partnership_status' => 'inactive']);
    }

    /**
     * Suspend partner
     */
    public function suspend(): bool
    {
        return $this->update(['partnership_status' => 'suspended']);
    }

    /**
     * Calculate discounted price based on partner discounts.
     */
    public function calculateDiscountedPrice(float $price): float
    {
        $discount = $this->discount_percentage;

        // Apply volume discount if it's greater than the set discount
        $suggested = $this->getSuggestedDiscountPercentage();
        if ($suggested > $discount) {
            $discount = $suggested;
        }

        return $discount > 0
            ? $price * (1 - ($discount / 100))
            : $price;
    }

    /**
     * Create default business partners for Rwanda
     */
    public static function createDefaultPartners(): array
    {
        $defaultPartners = [
            [
                'name' => 'Bank of Kigali',
                'type' => 'bank',
                'tier' => 'platinum',
                'email' => 'inspections@bk.rw',
                'phone' => '+250788123001',
                'address' => 'KG 11 Ave, Kigali',
                'city' => 'Kigali',
                'country' => 'Rwanda',
                'contact_person' => 'Property Manager',
                'contact_email' => 'property@bk.rw',
                'partnership_status' => 'active',
                'discount_percentage' => 15.0,
            ],
            [
                'name' => 'SANLAM Rwanda',
                'type' => 'insurance',
                'tier' => 'gold',
                'email' => 'inspections@sanlam.rw',
                'phone' => '+250788123002',
                'address' => 'KN 2 Ave, Kigali',
                'city' => 'Kigali',
                'country' => 'Rwanda',
                'contact_person' => 'Claims Manager',
                'contact_email' => 'claims@sanlam.rw',
                'partnership_status' => 'active',
                'discount_percentage' => 10.0,
            ]
        ];

        $created = [];
        foreach ($defaultPartners as $partnerData) {
            $partner = self::firstOrCreate(
                ['name' => $partnerData['name']],
                array_merge($partnerData, [
                    'partnership_start_date' => Carbon::now(),
                    'billing_cycle' => 'monthly',
                    'billing_type' => 'monthly'
                ])
            );
            $created[] = $partner;
        }

        return $created;
    }
}