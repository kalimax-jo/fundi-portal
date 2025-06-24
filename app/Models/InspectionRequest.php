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
    public function inspector(): BelongsTo
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

    /**
     * Get the client for the request (user or business partner)
     */
    public function getClientAttribute()
    {
        $client = $this->requester_type === 'business_partner'
            ? $this->businessPartner
            : $this->requester;

        if ($client) {
            $client->name = $this->requester_type === 'business_partner'
                ? $client->name
                : $client->full_name;
        }

        return $client;
    }

    /**
     * Get the total amount for the request
     */
    public function getTotalAmountAttribute()
    {
        return $this->total_cost;
    }

    /**
     * Get the display text for the status.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    /**
     * Get the color associated with the status.
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'assigned':
                return 'bg-blue-100 text-blue-800';
            case 'in_progress':
                return 'bg-purple-100 text-purple-800';
            case 'completed':
                return 'bg-green-100 text-green-800';
            case 'cancelled':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Get the inspection report for this request
     */
    public function report()
    {
        return $this->hasOne(\App\Models\InspectionReport::class, 'inspection_request_id', 'id');
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
     * Scope to get individual requests
     */
    public function scopeIndividualRequests($query)
    {
        return $query->byRequesterType('individual');
    }

    /**
     * Scope to get business requests
     */
    public function scopeBusinessRequests($query)
    {
        return $query->byRequesterType('business_partner');
    }

    /**
     * Scope to get requests by urgency
     */
    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency', $urgency);
    }

    /**
     * Scope to get urgent/emergency requests
     */
    public function scopeUrgent($query)
    {
        return $query->whereIn('urgency', ['urgent', 'emergency']);
    }

    /**
     * Scope to get requests that are overdue
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
            ->where('scheduled_date', '<', now());
    }

    /**
     * Scope to get requests scheduled for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', now());
    }

    /**
     * Scope to get requests scheduled for this week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scheduled_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include requests matching a search term.
     */
    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('request_number', 'like', "%{$term}%")
                ->orWhere('purpose', 'like', "%{$term}%")
                ->orWhereHas('property', function ($propQ) use ($term) {
                    $propQ->where('street_address', 'like', "%{$term}%")
                          ->orWhere('city', 'like', "%{$term}%");
              })
                ->orWhereHas('requester', function ($userQ) use ($term) {
                    $userQ->where('first_name', 'like', "%{$term}%")
                          ->orWhere('last_name', 'like', "%{$term}%");
              });
        });
    }

    // =============================================
    // HELPERS & LOGIC
    // =============================================

    /**
     * Generate a unique request number
     */
    public static function generateRequestNumber(): string
    {
        return 'INSP-' . now()->format('Ymd') . '-' . strtoupper(uniqid());
    }

    /**
     * Get display name for status
     */
    public function getStatusDisplayName(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    /**
     * Get info for urgency
     */
    public function getUrgencyInfo(): array
    {
        $urgencyMap = [
            'normal' => ['label' => 'Normal', 'color' => 'gray'],
            'urgent' => ['label' => 'Urgent', 'color' => 'yellow'],
            'emergency' => ['label' => 'Emergency', 'color' => 'red'],
        ];

        return $urgencyMap[$this->urgency] ?? $urgencyMap['normal'];
    }

    /**
     * Get display name for purpose
     */
    public function getPurposeDisplayName(): string
    {
        return ucwords(str_replace('_', ' ', $this->purpose));
    }

    /**
     * Get display name for payment status
     */
    public function getPaymentStatusDisplayName(): string
    {
        return ucwords(str_replace('_', ' ', $this->payment_status));
    }

    /**
     * Check if request is from a business partner
     */
    public function isBusinessRequest(): bool
    {
        return $this->requester_type === 'business_partner';
    }

    /**
     * Check if request is from an individual
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
        return $this->status !== 'completed' && $this->scheduled_date && $this->scheduled_date->isPast();
    }

    /**
     * Check if the request can be assigned
     */
    public function canBeAssigned(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request can be started by an inspector
     */
    public function canBeStarted(): bool
    {
        return $this->status === 'assigned' && $this->assigned_inspector_id;
    }

    /**
     * Check if the request can be completed
     */
    public function canBeCompleted(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Assign inspector to the request
     */
    public function assignInspector(Inspector $inspector, User $assignedBy): void
    {
        $this->update([
            'assigned_inspector_id' => $inspector->id,
            'assigned_by' => $assignedBy->id,
            'assigned_at' => now(),
            'status' => 'assigned',
        ]);

        $inspector->setBusy();
    }

    /**
     * Schedule the inspection
     */
    public function schedule(Carbon $date, Carbon $time): void
    {
        $this->update([
            'scheduled_date' => $date,
            'scheduled_time' => $time,
        ]);
    }

    /**
     * Start the inspection
     */
    public function start(): void
    {
        if (!$this->canBeStarted()) {
            // Or throw an exception
            return;
        }

        $oldStatus = $this->status;

        $this->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        $this->recordStatusChange($oldStatus, 'in_progress', $this->assigned_inspector_id, 'Inspection started');
    }

    /**
     * Complete the inspection
     */
    public function complete(float $totalCost = null): void
    {
        $oldStatus = $this->status;

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'total_cost' => $totalCost ?? $this->total_cost,
            'payment_status' => $this->requester_type === 'business_partner' ? 'invoiced' : 'paid',
        ]);

        $this->recordStatusChange($oldStatus, 'completed', $this->assigned_inspector_id, 'Inspection completed');

        // Update property last inspection date
        if ($this->property) {
            $this->property->update(['last_inspected_at' => now()]);
        }

        // Update inspector stats
        if ($this->inspector) {
            $this->inspector->increment('total_inspections');
            $this->inspector->setAvailable();
            $this->inspector->updateRating();
        }
    }

    /**
     * Cancel the inspection
     */
    public function cancel(User $cancelledBy, string $reason = null): void
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'cancelled',
        ]);

        $this->recordStatusChange($oldStatus, 'cancelled', $cancelledBy->id, $reason);

        // Free up inspector if assigned
        if ($this->inspector) {
            $this->inspector->setAvailable();
        }
    }

    /**
     * Calculate the total cost of the inspection.
     * This might involve summing up service costs from the package.
     */
    public function calculateTotalCost(): float
    {
        if ($this->package) {
            return $this->package->base_price;
        }
        return 0.0;
    }

    /**
     * Get total estimated duration in minutes
     */
    public function getEstimatedDuration(): int
    {
        return $this->package ? $this->package->services->sum('estimated_duration_minutes') : 0;
    }

    /**
     * Get days until preferred date
     */
    public function getDaysUntilPreferredDate(): ?int
    {
        if (!$this->preferred_date) {
            return null;
        }
        return now()->diffInDays($this->preferred_date, false);
    }

    /**
     * Record a status change in the history table
     */
    private function recordStatusChange(?string $oldStatus, string $newStatus, int $changedBy, string $reason = null): void
    {
        $this->statusHistory()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedBy,
            'notes' => $reason
        ]);
    }

    /**
     * Get some basic statistics for this request.
     */
    public function getStatistics(): array
    {
        $duration = $this->completed_at && $this->started_at
            ? $this->completed_at->diffInMinutes($this->started_at)
            : null;

        return [
            'status' => $this->getStatusDisplayName(),
            'urgency' => $this->getUrgencyInfo()['label'],
            'client' => $this->getClientAttribute()->name,
            'property_address' => $this->property->full_address,
            'inspector' => $this->inspector ? $this->inspector->user->full_name : 'N/A',
            'duration' => $duration,
            'cost' => $this->total_cost,
        ];
    }

    /**
     * Boot method for the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = self::generateRequestNumber();
            }
            if (empty($model->status)) {
                $model->status = 'pending';
            }
            if (empty($model->payment_status)) {
                $model->payment_status = 'pending';
            }
        });
    }
}