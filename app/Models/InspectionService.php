<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'requires_equipment',
        'estimated_duration_minutes',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'requires_equipment' => 'array',
        'is_active' => 'boolean',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get packages that include this service
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(InspectionPackage::class, 'package_services', 'service_id', 'package_id')
            ->withPivot('is_mandatory', 'sort_order')
            ->withTimestamps();
    }

    /**
     * Get inspection findings for this service
     * TODO: Uncomment when InspectionFinding model is created
     */
    /*
    public function inspectionFindings(): HasMany
    {
        return $this->hasMany(InspectionFinding::class, 'service_id');
    }
    */

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get only active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get services by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get exterior services
     */
    public function scopeExterior($query)
    {
        return $query->where('category', 'exterior');
    }

    /**
     * Scope to get interior services
     */
    public function scopeInterior($query)
    {
        return $query->where('category', 'interior');
    }

    /**
     * Scope to get plumbing services
     */
    public function scopePlumbing($query)
    {
        return $query->where('category', 'plumbing');
    }

    /**
     * Scope to get electrical services
     */
    public function scopeElectrical($query)
    {
        return $query->where('category', 'electrical');
    }

    /**
     * Scope to get environmental services
     */
    public function scopeEnvironmental($query)
    {
        return $query->where('category', 'environmental');
    }

    /**
     * Scope to get safety services
     */
    public function scopeSafety($query)
    {
        return $query->where('category', 'safety');
    }

    /**
     * Scope to get foundation services
     */
    public function scopeFoundation($query)
    {
        return $query->where('category', 'foundation');
    }

    /**
     * Scope to search services by name or description
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to get services requiring specific equipment
     */
    public function scopeRequiringEquipment($query, $equipment)
    {
        return $query->whereJsonContains('requires_equipment', $equipment);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Get category display name
     */
    public function getCategoryDisplayName(): string
    {
        $categoryNames = [
            'exterior' => 'Exterior Inspection',
            'interior' => 'Interior Inspection',
            'plumbing' => 'Plumbing Systems',
            'electrical' => 'Electrical Systems',
            'foundation' => 'Foundation & Structure',
            'environmental' => 'Environmental Assessment',
            'safety' => 'Safety & Security'
        ];

        return $categoryNames[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDuration(): string
    {
        $hours = floor($this->estimated_duration_minutes / 60);
        $minutes = $this->estimated_duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
        }

        return $minutes . ' minutes';
    }

    /**
     * Check if service requires specific equipment
     */
    public function requiresEquipment(string $equipment): bool
    {
        $requiredEquipment = $this->requires_equipment ?? [];
        return in_array($equipment, $requiredEquipment);
    }

    /**
     * Add required equipment
     */
    public function addRequiredEquipment(string $equipment): void
    {
        $requiredEquipment = $this->requires_equipment ?? [];
        
        if (!in_array($equipment, $requiredEquipment)) {
            $requiredEquipment[] = $equipment;
            $this->update(['requires_equipment' => $requiredEquipment]);
        }
    }

    /**
     * Remove required equipment
     */
    public function removeRequiredEquipment(string $equipment): void
    {
        $requiredEquipment = $this->requires_equipment ?? [];
        
        if (($key = array_search($equipment, $requiredEquipment)) !== false) {
            unset($requiredEquipment[$key]);
            $this->update(['requires_equipment' => array_values($requiredEquipment)]);
        }
    }

    /**
     * Get required equipment list as formatted string
     */
    public function getRequiredEquipmentList(): string
    {
        $equipment = $this->requires_equipment ?? [];
        
        if (empty($equipment)) {
            return 'Basic inspection tools';
        }

        return implode(', ', array_map('ucfirst', $equipment));
    }

    /**
     * Get packages count that include this service
     */
    public function getPackagesCount(): int
    {
        return $this->packages()->count();
    }

    /**
     * Get usage statistics for this service
     */
    public function getUsageStatistics(): array
    {
        // TODO: Update when InspectionFinding model is created
        $totalFindings = 0; // $this->inspectionFindings()->count();
        $criticalFindings = 0; // $this->inspectionFindings()->where('condition_rating', 'critical')->count();
        $thisMonthFindings = 0; // $this->inspectionFindings()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        return [
            'total_inspections' => $totalFindings,
            'critical_findings' => $criticalFindings,
            'critical_rate' => $totalFindings > 0 ? round(($criticalFindings / $totalFindings) * 100, 2) : 0,
            'this_month_inspections' => $thisMonthFindings,
            'packages_using_service' => $this->getPackagesCount(),
            'estimated_duration_minutes' => $this->estimated_duration_minutes
        ];
    }

    /**
     * Check if service is included in specific package
     */
    public function isInPackage(string $packageName): bool
    {
        return $this->packages()->where('name', $packageName)->exists();
    }

    /**
     * Get all available equipment types
     */
    public static function getAvailableEquipment(): array
    {
        return [
            'thermal_camera' => 'Thermal Imaging Camera',
            'moisture_meter' => 'Moisture Meter',
            'gas_detector' => 'Gas Detector',
            'foundation_scanner' => 'Foundation Scanner',
            'multimeter' => 'Digital Multimeter',
            'pressure_gauge' => 'Pressure Gauge',
            'ph_meter' => 'pH Meter',
            'air_quality_monitor' => 'Air Quality Monitor',
            'sound_level_meter' => 'Sound Level Meter',
            'infrared_thermometer' => 'Infrared Thermometer',
            'endoscope' => 'Inspection Camera/Endoscope',
            'ladder' => 'Extension Ladder',
            'flashlight' => 'High-Power Flashlight',
            'measuring_tape' => 'Measuring Tape',
            'digital_camera' => 'Digital Camera',
            'tablet' => 'Inspection Tablet'
        ];
    }

    /**
     * Get service categories with descriptions
     */
    public static function getServiceCategories(): array
    {
        return [
            'exterior' => [
                'name' => 'Exterior Inspection',
                'description' => 'External building assessment including walls, roof, windows, doors',
                'color' => '#3B82F6' // Blue
            ],
            'interior' => [
                'name' => 'Interior Inspection',
                'description' => 'Internal spaces, floors, ceilings, walls, fixtures',
                'color' => '#10B981' // Green
            ],
            'plumbing' => [
                'name' => 'Plumbing Systems',
                'description' => 'Water supply, drainage, fixtures, pipes, water pressure',
                'color' => '#06B6D4' // Cyan
            ],
            'electrical' => [
                'name' => 'Electrical Systems',
                'description' => 'Wiring, outlets, panels, safety systems, grounding',
                'color' => '#F59E0B' // Amber
            ],
            'foundation' => [
                'name' => 'Foundation & Structure',
                'description' => 'Structural integrity, foundation cracks, load-bearing elements',
                'color' => '#8B5CF6' // Purple
            ],
            'environmental' => [
                'name' => 'Environmental Assessment',
                'description' => 'Air quality, hazardous materials, mold, asbestos',
                'color' => '#EF4444' // Red
            ],
            'safety' => [
                'name' => 'Safety & Security',
                'description' => 'Fire safety, emergency exits, security systems',
                'color' => '#F97316' // Orange
            ]
        ];
    }

    /**
     * Create default inspection services
     */
    public static function createDefaultServices(): void
    {
        $services = [
            // A-Check services (Basic package)
            [
                'name' => 'Exterior Property Assessment',
                'description' => 'Complete evaluation of building exterior including walls, roof, windows, doors, and structural elements',
                'category' => 'exterior',
                'requires_equipment' => ['digital_camera', 'measuring_tape', 'ladder', 'flashlight'],
                'estimated_duration_minutes' => 45, // you can cahnge this value as needed
                'is_active' => true
            ],
            [
                'name' => 'Interior Condition Evaluation',
                'description' => 'Assessment of interior spaces, floors, ceilings, walls, fixtures, and general condition',
                'category' => 'interior',
                'requires_equipment' => ['digital_camera', 'measuring_tape', 'flashlight', 'moisture_meter'],
                'estimated_duration_minutes' => 60, // you can cahnge this value as needed
                'is_active' => true
            ],
            [
                'name' => 'Plumbing System Inspection',
                'description' => 'Water supply, drainage systems, fixtures, pipes, and water pressure testing',
                'category' => 'plumbing',
                'requires_equipment' => ['pressure_gauge', 'flashlight', 'digital_camera'],
                'estimated_duration_minutes' => 30, // you can cahnge this value as needed
                'is_active' => true
            ],
            [
                'name' => 'Electrical Safety Check',
                'description' => 'Electrical systems safety inspection including wiring, outlets, panels, and grounding',
                'category' => 'electrical',
                'requires_equipment' => ['multimeter', 'digital_camera', 'flashlight'],
                'estimated_duration_minutes' => 30, // you can cahnge this value as needed
                'is_active' => true
            ],
            [
                'name' => 'Air Quality Assessment',
                'description' => 'Indoor air quality evaluation including ventilation, humidity, and potential contaminants',
                'category' => 'environmental',
                'requires_equipment' => ['air_quality_monitor', 'digital_camera'],
                'estimated_duration_minutes' => 20, // you can cahnge this value as needed
                'is_active' => true
            ],
            [
                'name' => 'Fire Safety Evaluation',
                'description' => 'Fire safety systems assessment including exits, extinguishers, smoke detectors',
                'category' => 'safety',
                'requires_equipment' => ['digital_camera', 'measuring_tape'],
                'estimated_duration_minutes' => 25, // you can cahnge this value as needed
                'is_active' => true
            ],

            // B-Check additional services
            [
                'name' => 'Foundation Crack Analysis',
                'description' => 'Detailed foundation inspection including crack assessment and structural integrity',
                'category' => 'foundation',
                'requires_equipment' => ['foundation_scanner', 'measuring_tape', 'digital_camera'],
                'estimated_duration_minutes' => 40,
                'is_active' => true
            ],
            [
                'name' => 'Garden and Trees Assessment',
                'description' => 'Landscape evaluation including tree health, root systems, and potential property risks',
                'category' => 'exterior',
                'requires_equipment' => ['digital_camera', 'measuring_tape'],
                'estimated_duration_minutes' => 30,
                'is_active' => true
            ],
            [
                'name' => 'Fence and Boundary Evaluation',
                'description' => 'Property boundary assessment including fence condition and security features',
                'category' => 'exterior',
                'requires_equipment' => ['digital_camera', 'measuring_tape'],
                'estimated_duration_minutes' => 20,
                'is_active' => true
            ],
            [
                'name' => 'Flooding Risk Analysis',
                'description' => 'Water damage assessment and flooding risk evaluation including drainage systems',
                'category' => 'environmental',
                'requires_equipment' => ['moisture_meter', 'digital_camera', 'measuring_tape'],
                'estimated_duration_minutes' => 35,
                'is_active' => true
            ],

            // C-Check additional services
            [
                'name' => 'Environmental Hazards Assessment',
                'description' => 'Comprehensive evaluation of environmental hazards including asbestos, lead, and toxic materials',
                'category' => 'environmental',
                'requires_equipment' => ['air_quality_monitor', 'ph_meter', 'digital_camera', 'gas_detector'],
                'estimated_duration_minutes' => 60,
                'is_active' => true
            ],
            [
                'name' => 'Septic Tank Inspection',
                'description' => 'Septic system evaluation including tank condition, drainage field, and pumping requirements',
                'category' => 'plumbing',
                'requires_equipment' => ['endoscope', 'digital_camera', 'gas_detector'],
                'estimated_duration_minutes' => 45,
                'is_active' => true
            ]
        ];

        foreach ($services as $serviceData) {
            self::firstOrCreate(
                ['name' => $serviceData['name']],
                $serviceData
            );
        }
    }

    /**
     * Get services grouped by category
     */
    public static function getServicesByCategory(): array
    {
        $services = self::active()->get();
        $grouped = [];
        $categories = self::getServiceCategories();

        foreach ($categories as $categoryKey => $categoryInfo) {
            $grouped[$categoryKey] = [
                'info' => $categoryInfo,
                'services' => $services->where('category', $categoryKey)->values()
            ];
        }

        return $grouped;
    }
}