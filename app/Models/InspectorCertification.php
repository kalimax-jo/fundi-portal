<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InspectorCertification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'inspector_certifications';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'inspector_id',
        'certification_name',
        'issuing_body',
        'issue_date',
        'expiry_date',
        'certificate_file',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the inspector this certification belongs to
     */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class);
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get only active certifications
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get expired certifications
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::today());
    }

    /**
     * Scope to get expiring soon certifications
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '>=', Carbon::today())
                    ->where('expiry_date', '<=', Carbon::today()->addDays($days));
    }

    /**
     * Scope to get valid certifications
     */
    public function scopeValid($query)
    {
        return $query->where('expiry_date', '>=', Carbon::today())
                    ->where('is_active', true);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if certification is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date < Carbon::today();
    }

    /**
     * Check if certification is expiring soon
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && 
               $this->expiry_date >= Carbon::today() && 
               $this->expiry_date <= Carbon::today()->addDays($days);
    }

    /**
     * Check if certification is valid
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return Carbon::today()->diffInDays($this->expiry_date, false);
    }

    /**
     * Get expiry status with color
     */
    public function getExpiryStatus(): array
    {
        if (!$this->expiry_date) {
            return ['status' => 'No Expiry', 'color' => '#6B7280', 'priority' => 0];
        }

        if ($this->isExpired()) {
            return ['status' => 'Expired', 'color' => '#EF4444', 'priority' => 3];
        }

        if ($this->isExpiringSoon(7)) {
            return ['status' => 'Expiring Soon', 'color' => '#F59E0B', 'priority' => 2];
        }

        if ($this->isExpiringSoon(30)) {
            return ['status' => 'Expiring This Month', 'color' => '#10B981', 'priority' => 1];
        }

        return ['status' => 'Valid', 'color' => '#10B981', 'priority' => 0];
    }

    /**
     * Get certification duration
     */
    public function getDuration(): ?int
    {
        if (!$this->issue_date || !$this->expiry_date) {
            return null;
        }

        return $this->issue_date->diffInDays($this->expiry_date);
    }

    /**
     * Get certification age in years
     */
    public function getAgeInYears(): ?int
    {
        if (!$this->issue_date) {
            return null;
        }

        return $this->issue_date->diffInYears(Carbon::today());
    }

    /**
     * Get certificate file URL
     */
    public function getCertificateFileUrl(): ?string
    {
        if (!$this->certificate_file) {
            return null;
        }

        return asset('storage/certificates/' . $this->certificate_file);
    }

    /**
     * Get certification summary
     */
    public function getCertificationSummary(): string
    {
        $summary = $this->certification_name;
        
        if ($this->issuing_body) {
            $summary .= " by {$this->issuing_body}";
        }
        
        if ($this->issue_date) {
            $summary .= " (Issued: " . $this->issue_date->format('M Y') . ")";
        }
        
        if ($this->expiry_date) {
            $summary .= " (Expires: " . $this->expiry_date->format('M Y') . ")";
        }

        return $summary;
    }
} 