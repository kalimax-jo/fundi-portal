<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Inspector extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'inspector_code',
        'certification_level',
        'specializations',
        'experience_years',
        'certification_expiry',
        'equipment_assigned',
        'availability_status',
        'current_location_lat',
        'current_location_lng',
        'rating',
        'total_inspections'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'specializations' => 'array',
        'equipment_assigned' => 'array',
        'certification_expiry' => 'date',
        'current_location_lat' => 'decimal:8',
        'current_location_lng' => 'decimal:8',
        'rating' => 'decimal:2',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user associated with this inspector
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get inspector certifications
     */
    public function certifications(): HasMany
    {
        return $this->hasMany(InspectorCertification::class);
    }

    /**
     * Get active certifications
     */
    public function activeCertifications(): HasMany
    {
        return $this->certifications()->where('is_active', true);
    }

    /**
     * Get inspection requests assigned to this inspector
     */
    public function inspectionRequests(): HasMany
    {
        return $this->hasMany(InspectionRequest::class, 'assigned_inspector_id');
    }

    /**
     * Get completed inspections
     */
    public function completedInspections(): HasMany
    {
        return $this->inspectionRequests()->where('status', 'completed');
    }

    /**
     * Get current active inspections
     */
    public function activeInspections(): HasMany
    {
        return $this->inspectionRequests()->whereIn('status', ['assigned', 'in_progress']);
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get available inspectors
     */
    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available');
    }

    /**
     * Scope to get busy inspectors
     */
    public function scopeBusy($query)
    {
        return $query->where('availability_status', 'busy');
    }

    /**
     * Scope to get offline inspectors
     */
    public function scopeOffline($query)
    {
        return $query->where('availability_status', 'offline');
    }

    /**
     * Scope to get inspectors by certification level
     */
    public function scopeByCertificationLevel($query, $level)
    {
        return $query->where('certification_level', $level);
    }

    /**
     * Scope to get basic level inspectors
     */
    public function scopeBasicLevel($query)
    {
        return $query->where('certification_level', 'basic');
    }

    /**
     * Scope to get advanced level inspectors
     */
    public function scopeAdvancedLevel($query)
    {
        return $query->where('certification_level', 'advanced');
    }

    /**
     * Scope to get expert level inspectors
     */
    public function scopeExpertLevel($query)
    {
        return $query->where('certification_level', 'expert');
    }

    /**
     * Scope to get inspectors with specific specialization
     */
    public function scopeWithSpecialization($query, $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    /**
     * Scope to get inspectors within radius (in kilometers)
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radiusKm)
    {
        return $query->whereNotNull('current_location_lat')
            ->whereNotNull('current_location_lng')
            ->whereRaw(
                "(6371 * acos(cos(radians(?)) * cos(radians(current_location_lat)) * cos(radians(current_location_lng) - radians(?)) + sin(radians(?)) * sin(radians(current_location_lat)))) <= ?",
                [$latitude, $longitude, $latitude, $radiusKm]
            );
    }

    /**
     * Scope to get highly rated inspectors
     */
    public function scopeHighlyRated($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Scope to search inspectors
     */
    public function scopeSearch($query, $term)
    {
        return $query->whereHas('user', function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        })->orWhere('inspector_code', 'like', "%{$term}%");
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Generate unique inspector code
     */
    public static function generateInspectorCode(): string
    {
        do {
            $code = 'INS' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        } while (self::where('inspector_code', $code)->exists());

        return $code;
    }

    /**
     * Get certification level display name
     */
    public function getCertificationLevelDisplayName(): string
    {
        $levels = [
            'basic' => 'Basic Inspector',
            'advanced' => 'Advanced Inspector',
            'expert' => 'Expert Inspector'
        ];

        return $levels[$this->certification_level] ?? ucfirst($this->certification_level);
    }

    /**
     * Get availability status display name
     */
    public function getAvailabilityDisplayName(): string
    {
        $statuses = [
            'available' => 'Available',
            'busy' => 'Busy',
            'offline' => 'Offline'
        ];

        return $statuses[$this->availability_status] ?? ucfirst($this->availability_status);
    }

    /**
     * Check if inspector has specific specialization
     */
    public function hasSpecialization(string $specialization): bool
    {
        $specializations = $this->specializations ?? [];
        return in_array($specialization, $specializations);
    }

    /**
     * Add specialization
     */
    public function addSpecialization(string $specialization): void
    {
        $specializations = $this->specializations ?? [];
        
        if (!in_array($specialization, $specializations)) {
            $specializations[] = $specialization;
            $this->update(['specializations' => $specializations]);
        }
    }

    /**
     * Remove specialization
     */
    public function removeSpecialization(string $specialization): void
    {
        $specializations = $this->specializations ?? [];
        
        if (($key = array_search($specialization, $specializations)) !== false) {
            unset($specializations[$key]);
            $this->update(['specializations' => array_values($specializations)]);
        }
    }

    /**
     * Get specializations as formatted string
     */
    public function getSpecializationsList(): string
    {
        $specializations = $this->specializations ?? [];
        
        if (empty($specializations)) {
            return 'General Inspection';
        }

        $specialNames = [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'thermal_imaging' => 'Thermal Imaging',
            'environmental' => 'Environmental Assessment',
            'foundation' => 'Foundation Analysis',
            'electrical' => 'Electrical Systems',
            'plumbing' => 'Plumbing Systems'
        ];

        $formatted = array_map(function($spec) use ($specialNames) {
            return $specialNames[$spec] ?? ucfirst($spec);
        }, $specializations);

        return implode(', ', $formatted);
    }

    /**
     * Check if inspector has required equipment
     */
    public function hasEquipment(string $equipment): bool
    {
        $assignedEquipment = $this->equipment_assigned ?? [];
        return in_array($equipment, $assignedEquipment);
    }

    /**
     * Add equipment
     */
    public function addEquipment(string $equipment): void
    {
        $assignedEquipment = $this->equipment_assigned ?? [];
        
        if (!in_array($equipment, $assignedEquipment)) {
            $assignedEquipment[] = $equipment;
            $this->update(['equipment_assigned' => $assignedEquipment]);
        }
    }

    /**
     * Remove equipment
     */
    public function removeEquipment(string $equipment): void
    {
        $assignedEquipment = $this->equipment_assigned ?? [];
        
        if (($key = array_search($equipment, $assignedEquipment)) !== false) {
            unset($assignedEquipment[$key]);
            $this->update(['equipment_assigned' => array_values($assignedEquipment)]);
        }
    }

    /**
     * Update location
     */
    public function updateLocation(float $latitude, float $longitude): void
    {
        $this->update([
            'current_location_lat' => $latitude,
            'current_location_lng' => $longitude
        ]);
    }

    /**
     * Calculate distance to coordinates (in kilometers)
     */
    public function distanceTo($latitude, $longitude): ?float
    {
        if (!$this->current_location_lat || !$this->current_location_lng) {
            return null;
        }

        // Haversine formula
        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($this->current_location_lat);
        $lonFrom = deg2rad($this->current_location_lng);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + 
                 cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Set availability status
     */
    public function setAvailable(): void
    {
        $this->update(['availability_status' => 'available']);
    }

    /**
     * Set busy status
     */
    public function setBusy(): void
    {
        $this->update(['availability_status' => 'busy']);
    }

    /**
     * Set offline status
     */
    public function setOffline(): void
    {
        $this->update(['availability_status' => 'offline']);
    }

    /**
     * Check if inspector is available
     */
    public function isAvailable(): bool
    {
        return $this->availability_status === 'available';
    }

    /**
     * Check if inspector is busy
     */
    public function isBusy(): bool
    {
        return $this->availability_status === 'busy';
    }

    /**
     * Check if certification is expired or expiring soon
     */
    public function isCertificationExpiring(int $daysWarning = 30): bool
    {
        if (!$this->certification_expiry) {
            return false;
        }

        return $this->certification_expiry->lte(Carbon::now()->addDays($daysWarning));
    }

    /**
     * Get days until certification expires
     */
    public function getDaysUntilCertificationExpiry(): ?int
    {
        if (!$this->certification_expiry) {
            return null;
        }

        return Carbon::now()->diffInDays($this->certification_expiry, false);
    }

    /**
     * Get current workload (active inspections count)
     */
    public function getCurrentWorkload(): int
    {
        return $this->activeInspections()->count();
    }

    /**
     * Get this month's completed inspections
     */
    public function getThisMonthInspections(): int
    {
        return $this->completedInspections()
            ->whereMonth('completed_at', Carbon::now()->month)
            ->whereYear('completed_at', Carbon::now()->year)
            ->count();
    }

    /**
     * Update rating based on recent feedback
     */
    public function updateRating(): void
    {
        // This would be implemented based on feedback/review system
        // For now, we'll calculate based on completion rate and efficiency
        $completed = $this->completedInspections()->count();
        $total = $this->inspectionRequests()->count();
        
        if ($total > 0) {
            $completionRate = $completed / $total;
            $baseRating = 3.0 + ($completionRate * 2.0); // 3-5 scale
            
            // Adjust based on experience
            $experienceBonus = min(0.5, $this->experience_years * 0.05);
            
            $newRating = min(5.0, $baseRating + $experienceBonus);
            $this->update(['rating' => round($newRating, 2)]);
        }
    }

    /**
     * Get inspector performance statistics
     */
    public function getPerformanceStatistics(): array
    {
        $totalInspections = $this->inspectionRequests()->count();
        $completedInspections = $this->completedInspections()->count();
        $thisMonthInspections = $this->getThisMonthInspections();
        $currentWorkload = $this->getCurrentWorkload();

        return [
            'total_inspections' => $totalInspections,
            'completed_inspections' => $completedInspections,
            'completion_rate' => $totalInspections > 0 ? round(($completedInspections / $totalInspections) * 100, 2) : 0,
            'this_month_inspections' => $thisMonthInspections,
            'current_workload' => $currentWorkload,
            'rating' => $this->rating,
            'experience_years' => $this->experience_years,
            'certification_level' => $this->getCertificationLevelDisplayName(),
            'specializations_count' => count($this->specializations ?? []),
            'equipment_count' => count($this->equipment_assigned ?? []),
            'certification_expires_in_days' => $this->getDaysUntilCertificationExpiry(),
            'is_certification_expiring' => $this->isCertificationExpiring()
        ];
    }

    /**
     * Get available specializations
     */
    public static function getAvailableSpecializations(): array
    {
        return [
            'residential' => 'Residential Properties',
            'commercial' => 'Commercial Properties',
            'industrial' => 'Industrial Properties',
            'thermal_imaging' => 'Thermal Imaging Specialist',
            'environmental' => 'Environmental Assessment',
            'foundation' => 'Foundation & Structural Analysis',
            'electrical' => 'Electrical Systems Specialist',
            'plumbing' => 'Plumbing Systems Specialist',
            'safety' => 'Safety & Security Systems',
            'renovation' => 'Renovation & Construction'
        ];
    }

    /**
     * Find best inspector for assignment
     */
    public static function findBestInspectorForAssignment($latitude, $longitude, $propertyType, $requiredSpecializations = [], $maxDistance = 50)
    {
        $query = self::available()
            ->highlyRated(3.5)
            ->with('user');

        // Filter by location if coordinates provided
        if ($latitude && $longitude) {
            $query->withinRadius($latitude, $longitude, $maxDistance)
                ->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(current_location_lat)) * cos(radians(current_location_lng) - radians(?)) + sin(radians(?)) * sin(radians(current_location_lat)))) AS distance", [$latitude, $longitude, $latitude])
                ->orderBy('distance');
        }

        // Filter by specializations
        foreach ($requiredSpecializations as $specialization) {
            $query->withSpecialization($specialization);
        }

        // Prefer higher certification levels for complex properties
        if ($propertyType === 'commercial' || $propertyType === 'industrial') {
            $query->orderByRaw("CASE certification_level WHEN 'expert' THEN 1 WHEN 'advanced' THEN 2 ELSE 3 END");
        }

        // Order by rating and current workload
        $query->orderBy('rating', 'desc')
              ->orderByRaw('(SELECT COUNT(*) FROM inspection_requests WHERE assigned_inspector_id = inspectors.id AND status IN ("assigned", "in_progress"))');

        return $query->first();
    }

    /**
     * Auto-generate inspector code before creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inspector) {
            if (!$inspector->inspector_code) {
                $inspector->inspector_code = self::generateInspectorCode();
            }
        });

        static::updated(function ($inspector) {
            // Auto-update total inspections count
            if ($inspector->isDirty('total_inspections') === false) {
                $actualCount = $inspector->completedInspections()->count();
                if ($inspector->total_inspections !== $actualCount) {
                    $inspector->total_inspections = $actualCount;
                    $inspector->saveQuietly(); // Prevent infinite loop
                }
            }
        });
    }
}