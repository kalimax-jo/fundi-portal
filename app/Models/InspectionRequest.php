<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class InspectionRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'request_number',
        'requester_type',
        'requester_user_id',
        'business_partner_id',
        'property_id',
        'package_id',
        'purpose',
        'urgency',
        'preferred_date',
        'preferred_time_slot',
        'special_instructions',
        'loan_amount',
        'loan_reference',
        'applicant_name',
        'applicant_phone',
        'status',
        'assigned_inspector_id',
        'assigned_by',
        'assigned_at',
        'scheduled_date',
        'scheduled_time',
        'started_at',
        'completed_at',
        'total_cost',
        'payment_status'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'preferred_date' => 'date',
        'assigned_at' => 'datetime',
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'loan_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the user who made the request
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    /**
     * Get the business partner (if business request)
     */
    public function businessPartner(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class);
    }

    /**
     * Get the property to be inspected
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the inspection package
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(InspectionPackage::class);
    }

    /**
     * Get the assigned inspector
     */
    public function assignedInspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class, 'assigned_inspector_id');
    }

    /**
     * Get the user who assigned the inspector
     */
    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get status history
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(InspectionStatusHistory::class);
    }

    /**
     * Get payments for this request
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get requests by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get assigned requests
     */
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    /**
     * Scope to get in-progress requests
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope to get completed requests
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get cancelled requests
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope to get requests by requester type
     */
    public function scopeByRequesterType($query, $type)
    {
        return $query->where('requester_type', $type);
    }

    /**
     * Scope to get individual client requests
     */
    public function scopeIndividualRequests($query)
    {
        return $query->where('requester_type', 'individual');
    }

    /**
     * Scope to get business partner requests
     */
    public function scopeBusinessRequests($query)
    {
        return $query->where('requester_type', 'business_partner');
    }

    /**
     * Scope to get requests by urgency
     */
    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency', $urgency);
    }

    /**
     * Scope to get urgent requests
     */
    public function scopeUrgent($query)
    {
        return $query->whereIn('urgency', ['urgent', 'emergency']);
    }

    /**
     * Scope to get overdue requests
     */
    public function scopeOverdue($query)
    {
        return $query->where('scheduled_date', '<', Carbon::now()->toDateString())
            ->whereIn('status', ['assigned', 'in_progress']);
    }

    /**
     * Scope to get today's inspections
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', Carbon::today());
    }

    /**
     * Scope to get this week's inspections
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scheduled_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope to search requests
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('request_number', 'like', "%{$term}%")
              ->orWhereHas('property', function ($pq) use ($term) {
                  $pq->where('property_code', 'like', "%{$term}%")
                    ->orWhere('address', 'like', "%{$term}%");
              })
              ->orWhereHas('requester', function ($uq) use ($term) {
                  $uq->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
              });
        });
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Generate unique request number
     */
    public static function generateRequestNumber(): string
    {
        do {
            $code = 'REQ' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('request_number', $code)->exists());

        return $code;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName(): string
    {
        $statuses = [
            'pending' => 'Pending Assignment',
            'assigned' => 'Assigned to Inspector',
            'in_progress' => 'Inspection in Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'on_hold' => 'On Hold'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get urgency display name with color
     */
    public function getUrgencyInfo(): array
    {
        $urgencyInfo = [
            'normal' => ['name' => 'Normal', 'color' => '#10B981', 'priority' => 1],
            'urgent' => ['name' => 'Urgent', 'color' => '#F59E0B', 'priority' => 2],
            'emergency' => ['name' => 'Emergency', 'color' => '#EF4444', 'priority' => 3]
        ];

        return $urgencyInfo[$this->urgency] ?? $urgencyInfo['normal'];
    }

    /**
     * Get purpose display name
     */
    public function getPurposeDisplayName(): string
    {
        $purposes = [
            'rental' => 'Rental Property',
            'sale' => 'Property Sale',
            'purchase' => 'Property Purchase',
            'loan_collateral' => 'Loan Collateral',
            'insurance' => 'Insurance Requirement',
            'maintenance' => 'Maintenance Check',
            'other' => 'Other'
        ];

        return $purposes[$this->purpose] ?? ucfirst($this->purpose);
    }

    /**
     * Get payment status display name
     */
    public function getPaymentStatusDisplayName(): string
    {
        $statuses = [
            'pending' => 'Payment Pending',
            'partial' => 'Partially Paid',
            'paid' => 'Fully Paid',
            'refunded' => 'Refunded'
        ];

        return $statuses[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    /**
     * Check if request is from business partner
     */
    public function isBusinessRequest(): bool
    {
        return $this->requester_type === 'business_partner';
    }

    /**
     * Check if request is from individual client
     */
    public function isIndividualRequest(): bool
    {
        return $this->requester_type === 'individual';
    }

    /**
     * Check if request is urgent
     */
    public function isUrgent(): bool
    {
        return in_array($this->urgency, ['urgent', 'emergency']);
    }

    /**
     * Check if request is overdue
     */
    public function isOverdue(): bool
    {
        return $this->scheduled_date && 
               $this->scheduled_date < Carbon::today() && 
               in_array($this->status, ['assigned', 'in_progress']);
    }

    /**
     * Check if request can be assigned
     */
    public function canBeAssigned(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request can be started
     */
    public function canBeStarted(): bool
    {
        return $this->status === 'assigned' && $this->assigned_inspector_id;
    }

    /**
     * Check if request can be completed
     */
    public function canBeCompleted(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Assign inspector to request
     */
    public function assignInspector(Inspector $inspector, User $assignedBy): void
    {
        if (!$this->canBeAssigned()) {
            throw new \Exception('Request cannot be assigned in current status: ' . $this->status);
        }

        $this->update([
            'assigned_inspector_id' => $inspector->id,
            'assigned_by' => $assignedBy->id,
            'assigned_at' => Carbon::now(),
            'status' => 'assigned'
        ]);

        // Create status history
        $this->recordStatusChange('pending', 'assigned', $assignedBy->id, 'Assigned to inspector: ' . $inspector->user->full_name);

        // Update inspector status
        $inspector->setBusy();
    }

    /**
     * Schedule the inspection
     */
    public function schedule(Carbon $date, Carbon $time): void
    {
        $this->update([
            'scheduled_date' => $date->toDateString(),
            'scheduled_time' => $time->toTimeString()
        ]);
    }

    /**
     * Start the inspection
     */
    public function start(): void
    {
        if (!$this->canBeStarted()) {
            throw new \Exception('Request cannot be started in current status: ' . $this->status);
        }

        $this->update([
            'status' => 'in_progress',
            'started_at' => Carbon::now()
        ]);

        $this->recordStatusChange('assigned', 'in_progress', $this->assigned_inspector_id, 'Inspection started');
    }

    /**
     * Complete the inspection
     */
    public function complete(float $totalCost = null): void
    {
        if (!$this->canBeCompleted()) {
            throw new \Exception('Request cannot be completed in current status: ' . $this->status);
        }

        $updateData = [
            'status' => 'completed',
            'completed_at' => Carbon::now()
        ];

        if ($totalCost !== null) {
            $updateData['total_cost'] = $totalCost;
        }

        $this->update($updateData);

        $this->recordStatusChange('in_progress', 'completed', $this->assigned_inspector_id, 'Inspection completed');

        // Update property last inspection date
        $this->property->update(['last_inspection_date' => Carbon::now()->toDateString()]);

        // Update inspector stats
        if ($this->assignedInspector) {
            $this->assignedInspector->increment('total_inspections');
            $this->assignedInspector->setAvailable();
            $this->assignedInspector->updateRating();
        }
    }

    /**
     * Cancel the inspection
     */
    public function cancel(User $cancelledBy, string $reason = null): void
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'cancelled'
        ]);

        $this->recordStatusChange($oldStatus, 'cancelled', $cancelledBy->id, $reason ?: 'Request cancelled');

        // Free up inspector if assigned
        if ($this->assignedInspector) {
            $this->assignedInspector->setAvailable();
        }
    }

    /**
     * Calculate total cost including discounts
     */
    public function calculateTotalCost(): float
    {
        $basePrice = $this->package->price;

        // Apply business partner discount if applicable
        if ($this->isBusinessRequest() && $this->businessPartner) {
            $basePrice = $this->businessPartner->calculateDiscountedPrice($basePrice);
        }

        return $basePrice;
    }

    /**
     * Get estimated duration
     */
    public function getEstimatedDuration(): int
    {
        return $this->package->getTotalEstimatedDuration();
    }

    /**
     * Get days until preferred date
     */
    public function getDaysUntilPreferredDate(): ?int
    {
        if (!$this->preferred_date) {
            return null;
        }

        return Carbon::now()->diffInDays($this->preferred_date, false);
    }

    /**
     * Record status change in history
     */
    private function recordStatusChange(?string $oldStatus, string $newStatus, int $changedBy, string $reason = null): void
    {
        $this->statusHistory()->create([
            'old_status' => $oldStatus ?? 'new',
            'new_status' => $newStatus,
            'changed_by' => $changedBy,
            'change_reason' => $reason
        ]);
    }

    /**
     * Get request statistics
     */
    public function getStatistics(): array
    {
        $timeToCompletion = null;
        $timeToAssignment = null;

        if ($this->completed_at && $this->created_at) {
            $timeToCompletion = $this->created_at->diffInHours($this->completed_at);
        }

        if ($this->assigned_at && $this->created_at) {
            $timeToAssignment = $this->created_at->diffInHours($this->assigned_at);
        }

        return [
            'time_to_assignment_hours' => $timeToAssignment,
            'time_to_completion_hours' => $timeToCompletion,
            'is_overdue' => $this->isOverdue(),
            'is_urgent' => $this->isUrgent(),
            'is_business_request' => $this->isBusinessRequest(),
            'estimated_duration_minutes' => $this->getEstimatedDuration(),
            'days_until_preferred_date' => $this->getDaysUntilPreferredDate(),
            'total_cost' => $this->calculateTotalCost(),
            'payment_status' => $this->payment_status,
            'status_changes_count' => $this->statusHistory()->count()
        ];
    }

    /**
     * Auto-generate request number before creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (!$request->request_number) {
                $request->request_number = self::generateRequestNumber();
            }

            // Auto-calculate total cost if not set
            if (!$request->total_cost) {
                $request->total_cost = $request->calculateTotalCost();
            }
        });

        static::created(function ($request) {
            // Create initial status history
            $request->recordStatusChange('new', 'pending', $request->requester_user_id, 'Request created');
        });
    }
}