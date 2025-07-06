<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'password',
        'phone',
        'first_name',
        'last_name',
        'profile_photo',
        'status',
        'last_login_at',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user's roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    /**
     * Get the user's business partner associations
     */
    public function businessPartners(): BelongsToMany
    {
        return $this->belongsToMany(BusinessPartner::class, 'business_partner_users')
            ->withPivot('position', 'department', 'access_level', 'is_primary_contact')
            ->withTimestamps();
    }

    /**
     * Get the user's institutional partner associations
     */
    public function institutionalPartners(): BelongsToMany
    {
        return $this->belongsToMany(InstitutionalPartner::class, 'institutional_partner_users')
            ->withPivot('position', 'department', 'access_level', 'is_primary_contact')
            ->withTimestamps();
    }

    /**
     * Get the inspector profile if user is an inspector
     */
    public function inspector(): HasOne
    {
        return $this->hasOne(Inspector::class);
    }

    /**
     * Get inspection requests made by this user
     */
    public function inspectionRequests(): HasMany
    {
        return $this->hasMany(InspectionRequest::class, 'requester_user_id');
    }

    /**
     * Get notifications sent to this user
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get audit logs for this user's actions
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the user who created this user
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get users created by this user
     */
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Get the institutional partner this user belongs to
     */
    public function institutionalPartner()
    {
        return $this->belongsTo(InstitutionalPartner::class, 'institutional_partner_id');
    }

    /**
     * Check if user is a client (created by an institutional user)
     */
    public function isClient()
    {
        return !is_null($this->created_by);
    }

    /**
     * Check if user is an institutional user (not a client)
     */
    public function isInstitutionalUser()
    {
        return !is_null($this->institutional_partner_id) && is_null($this->created_by);
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get users by role
     */
    public function scopeByRole($query, $roleName)
    {
        return $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Scope to search users by name or email
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user is an individual client
     */
    public function isIndividualClient(): bool
    {
        return $this->hasRole('individual_client');
    }

    /**
     * Check if user is a business partner
     */
    public function isBusinessPartner(): bool
    {
        return $this->hasRole('business_partner');
    }

    /**
     * Check if user is an inspector
     */
    public function isInspector(): bool
    {
        return $this->hasRole('inspector');
    }

    /**
     * Check if user is head technician
     */
    public function isHeadTechnician(): bool
    {
        return $this->hasRole('head_technician');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get user's initials
     */
    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    /**
     * Get user's business partners list
     */
    public function getBusinessPartnersList()
    {
        return $this->businessPartners()->pluck('name', 'id');
    }

    /**
     * Get user's institutional partners list
     */
    public function getInstitutionalPartnersList()
    {
        return $this->institutionalPartners()->pluck('name', 'id');
    }

    /**
     * Check if user can access business features
     */
    public function canAccessBusinessFeatures(): bool
    {
        return $this->businessPartners()->exists();
    }

    /**
     * Check if user can access institutional partner features
     */
    public function canAccessInstitutionalFeatures(): bool
    {
        return $this->institutionalPartners()->exists();
    }

    /**
     * Get primary business partner
     */
    public function getPrimaryBusinessPartner()
    {
        return $this->businessPartners()->wherePivot('is_primary_contact', true)->first();
    }

    /**
     * Get primary institutional partner
     */
    public function getPrimaryInstitutionalPartner()
    {
        return $this->institutionalPartners()->wherePivot('is_primary_contact', true)->first();
    }

    /**
     * Get current institutional partner based on subdomain
     */
    public function getCurrentInstitutionalPartner()
    {
        $subdomain = request()->getHost();
        if (str_contains($subdomain, '.')) {
            $subdomain = explode('.', $subdomain)[0];
        }
        
        return $this->institutionalPartners()->where('subdomain', $subdomain)->first();
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Activate user account
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Deactivate user account
     */
    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    /**
     * Suspend user account
     */
    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    /**
     * Check if user has a specific permission (across all roles)
     */
    public function hasPermission(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user is an institutional partner
     */
    public function isInstitutionalPartner(): bool
    {
        return $this->hasRole('institutional_partner');
    }

    /**
     * Check if user is an institutional partner user (has access to institutional partner portal)
     */
    public function isInstitutionalPartnerUser(): bool
    {
        return $this->institutionalPartners()->exists();
    }
}