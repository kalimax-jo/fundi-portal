<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get users with this role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot('assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get roles by name
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Scope to get client-type roles
     */
    public function scopeClientRoles($query)
    {
        return $query->whereIn('name', ['individual_client', 'business_partner']);
    }

    /**
     * Scope to get staff roles
     */
    public function scopeStaffRoles($query)
    {
        return $query->whereIn('name', ['admin', 'head_technician', 'inspector']);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions);
    }

    /**
     * Add permission to role
     */
    public function addPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    /**
     * Remove permission from role
     */
    public function removePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        
        if (($key = array_search($permission, $permissions)) !== false) {
            unset($permissions[$key]);
            $this->update(['permissions' => array_values($permissions)]);
        }
    }

    /**
     * Get role permissions as a formatted list
     */
    public function getPermissionsList(): array
    {
        return $this->permissions ?? [];
    }

    /**
     * Count users with this role
     */
    public function getUsersCount(): int
    {
        return $this->users()->count();
    }

    /**
     * Check if this is an admin role
     */
    public function isAdminRole(): bool
    {
        return $this->name === 'admin';
    }

    /**
     * Check if this is a client role
     */
    public function isClientRole(): bool
    {
        return in_array($this->name, ['individual_client', 'business_partner']);
    }

    /**
     * Check if this is a staff role
     */
    public function isStaffRole(): bool
    {
        return in_array($this->name, ['admin', 'head_technician', 'inspector']);
    }

    /**
     * Get default permissions for each role
     */
    public static function getDefaultPermissions(): array
    {
        return [
            'admin' => [
                'manage_users',
                'manage_roles',
                'manage_business_partners',
                'manage_inspectors',
                'manage_properties',
                'manage_inspections',
                'manage_payments',
                'manage_reports',
                'manage_settings',
                'view_analytics',
                'manage_notifications'
            ],
            'head_technician' => [
                'assign_inspections',
                'manage_inspectors',
                'view_inspections',
                'approve_reports',
                'view_properties',
                'manage_schedules',
                'view_analytics'
            ],
            'inspector' => [
                'view_assigned_inspections',
                'update_inspection_status',
                'upload_inspection_files',
                'create_inspection_reports',
                'update_location',
                'view_properties'
            ],
            'business_partner' => [
                'create_inspection_requests',
                'view_own_inspections',
                'view_inspection_reports',
                'manage_partner_users',
                'view_billing',
                'download_reports'
            ],
            'individual_client' => [
                'create_inspection_requests',
                'view_own_inspections',
                'view_inspection_reports',
                'make_payments',
                'download_reports',
                'manage_properties'
            ]
        ];
    }

    /**
     * Create default roles with permissions
     */
    public static function createDefaultRoles(): void
    {
        $defaultRoles = [
            [
                'name' => 'admin',
                'display_name' => 'System Administrator',
                'description' => 'Full system access and management capabilities'
            ],
            [
                'name' => 'head_technician',
                'display_name' => 'Head of Technician',
                'description' => 'Operations manager for inspections and inspector assignments'
            ],
            [
                'name' => 'inspector',
                'display_name' => 'Certified Inspector',
                'description' => 'Field inspection personnel'
            ],
            [
                'name' => 'business_partner',
                'display_name' => 'Business Partner',
                'description' => 'Financial institutions and corporate clients'
            ],
            [
                'name' => 'individual_client',
                'display_name' => 'Individual Client',
                'description' => 'Property owners and individual users'
            ]
        ];

        $defaultPermissions = self::getDefaultPermissions();

        foreach ($defaultRoles as $roleData) {
            $role = self::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'permissions' => $defaultPermissions[$roleData['name']] ?? []
                ]
            );
        }
    }

    /**
     * Get human-readable permission names
     */
    public static function getPermissionLabels(): array
    {
        return [
            'manage_users' => 'Manage Users',
            'manage_roles' => 'Manage Roles & Permissions',
            'manage_business_partners' => 'Manage Business Partners',
            'manage_inspectors' => 'Manage Inspectors',
            'manage_properties' => 'Manage Properties',
            'manage_inspections' => 'Manage Inspections',
            'manage_payments' => 'Manage Payments',
            'manage_reports' => 'Manage Reports',
            'manage_settings' => 'Manage System Settings',
            'view_analytics' => 'View Analytics & Reports',
            'manage_notifications' => 'Manage Notifications',
            'assign_inspections' => 'Assign Inspections',
            'approve_reports' => 'Approve Inspection Reports',
            'manage_schedules' => 'Manage Schedules',
            'view_assigned_inspections' => 'View Assigned Inspections',
            'update_inspection_status' => 'Update Inspection Status',
            'upload_inspection_files' => 'Upload Inspection Files',
            'create_inspection_reports' => 'Create Inspection Reports',
            'update_location' => 'Update Location',
            'create_inspection_requests' => 'Create Inspection Requests',
            'view_own_inspections' => 'View Own Inspections',
            'view_inspection_reports' => 'View Inspection Reports',
            'manage_partner_users' => 'Manage Partner Users',
            'view_billing' => 'View Billing Information',
            'download_reports' => 'Download Reports',
            'make_payments' => 'Make Payments',
            'view_inspections' => 'View All Inspections',
            'view_properties' => 'View Properties'
        ];
    }

    /**
     * Get permissions grouped by category
     */
    public static function getGroupedPermissions(): array
    {
        return [
            'User Management' => [
                'manage_users',
                'manage_roles',
                'manage_business_partners',
                'manage_inspectors'
            ],
            'Inspection Management' => [
                'manage_inspections',
                'assign_inspections',
                'view_inspections',
                'view_assigned_inspections',
                'update_inspection_status',
                'create_inspection_requests'
            ],
            'Property Management' => [
                'manage_properties',
                'view_properties'
            ],
            'Reports & Documentation' => [
                'manage_reports',
                'approve_reports',
                'create_inspection_reports',
                'view_inspection_reports',
                'download_reports',
                'upload_inspection_files'
            ],
            'Financial Management' => [
                'manage_payments',
                'make_payments',
                'view_billing'
            ],
            'System Management' => [
                'manage_settings',
                'manage_notifications',
                'view_analytics',
                'manage_schedules'
            ],
            'Personal Actions' => [
                'view_own_inspections',
                'update_location',
                'manage_partner_users'
            ]
        ];
    }
}