<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionStatusHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'inspection_status_history';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'inspection_request_id',
        'old_status',
        'new_status',
        'changed_by',
        'change_reason',
        'changed_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the inspection request this history belongs to
     */
    public function inspectionRequest(): BelongsTo
    {
        return $this->belongsTo(InspectionRequest::class);
    }

    /**
     * Get the user who made the status change
     */
    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get recent status changes
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('changed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get status changes by specific status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('new_status', $status);
    }

    /**
     * Scope to get status changes by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('changed_by', $userId);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Get status display name
     */
    public function getStatusDisplayName(string $status): string
    {
        $statuses = [
            'pending' => 'Pending Assignment',
            'assigned' => 'Assigned to Inspector',
            'in_progress' => 'Inspection in Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'on_hold' => 'On Hold',
            'new' => 'New Request'
        ];

        return $statuses[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Get old status display name
     */
    public function getOldStatusDisplayName(): string
    {
        return $this->getStatusDisplayName($this->old_status);
    }

    /**
     * Get new status display name
     */
    public function getNewStatusDisplayName(): string
    {
        return $this->getStatusDisplayName($this->new_status);
    }

    /**
     * Get time elapsed since status change
     */
    public function getTimeElapsed(): string
    {
        return $this->changed_at->diffForHumans();
    }

    /**
     * Check if this is the initial status change
     */
    public function isInitialStatus(): bool
    {
        return $this->old_status === null || $this->old_status === 'new';
    }

    /**
     * Get status change summary
     */
    public function getChangeSummary(): string
    {
        if ($this->isInitialStatus()) {
            return "Request created";
        }

        $summary = "Status changed from {$this->getOldStatusDisplayName()} to {$this->getNewStatusDisplayName()}";
        
        if ($this->change_reason) {
            $summary .= " - {$this->change_reason}";
        }

        return $summary;
    }

    /**
     * Get enhanced activity description with proper names
     */
    public function getActivityDescription(): string
    {
        $requestNumber = $this->inspectionRequest->request_number ?? 'Request #' . $this->inspection_request_id;
        $changedBy = $this->changedByUser->full_name ?? 'Unknown User';
        
        // Handle different types of activities
        if ($this->isInitialStatus()) {
            return "{$requestNumber} — Request created by {$changedBy}";
        }

        // Handle status changes
        $oldStatus = $this->getOldStatusDisplayName();
        $newStatus = $this->getNewStatusDisplayName();
        
        if ($oldStatus === $newStatus) {
            // Same status but with reason (like reassignment)
            if ($this->change_reason) {
                return "{$requestNumber} — {$this->change_reason} by {$changedBy}";
            }
            return "{$requestNumber} — Status updated by {$changedBy}";
        }

        // Different statuses
        $description = "{$requestNumber} — Status changed from {$oldStatus} to {$newStatus}";
        
        if ($this->change_reason) {
            $description .= " - {$this->change_reason}";
        }
        
        $description .= " by {$changedBy}";
        
        return $description;
    }

    /**
     * Get activity icon based on status change
     */
    public function getActivityIcon(): string
    {
        if ($this->isInitialStatus()) {
            return '📋'; // New request
        }

        switch ($this->new_status) {
            case 'assigned':
                return '👤'; // Assigned to inspector
            case 'in_progress':
                return '🔍'; // Inspection in progress
            case 'completed':
                return '✅'; // Completed
            case 'cancelled':
                return '❌'; // Cancelled
            case 'on_hold':
                return '⏸️'; // On hold
            default:
                return '📝'; // General update
        }
    }

    /**
     * Get activity color class
     */
    public function getActivityColorClass(): string
    {
        if ($this->isInitialStatus()) {
            return 'text-blue-600';
        }

        switch ($this->new_status) {
            case 'assigned':
                return 'text-green-600';
            case 'in_progress':
                return 'text-yellow-600';
            case 'completed':
                return 'text-green-700';
            case 'cancelled':
                return 'text-red-600';
            case 'on_hold':
                return 'text-orange-600';
            default:
                return 'text-gray-600';
        }
    }
} 