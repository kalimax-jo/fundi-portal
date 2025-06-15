<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PartnerBilling extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'business_partner_id',
        'billing_reference',
        'billing_period_type',
        'billing_period_start',
        'billing_period_end',
        'total_inspections',
        'base_amount',
        'discount_amount',
        'discount_percentage',
        'tax_amount',
        'tax_percentage',
        'final_amount',
        'currency',
        'status',
        'due_date',
        'sent_date',
        'paid_date',
        'payment_method',
        'payment_reference',
        'notes',
        'inspection_details',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'sent_date' => 'date',
        'paid_date' => 'date',
        'approved_at' => 'datetime',
        'base_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'inspection_details' => 'array',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the business partner that owns this billing
     */
    public function businessPartner(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class);
    }

    /**
     * Get the user who created this billing
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this billing
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get pending billings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get paid billings
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to get overdue billings
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->orWhere(function($q) {
                        $q->whereIn('status', ['pending', 'sent'])
                          ->where('due_date', '<', Carbon::today());
                    });
    }

    /**
     * Scope to get billings for a specific period
     */
    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('billing_period_start', [$startDate, $endDate]);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Generate unique billing reference
     */
    public static function generateBillingReference(): string
    {
        $prefix = 'BILL';
        $date = Carbon::now()->format('Ymd');
        $sequence = str_pad(static::whereDate('created_at', Carbon::today())->count() + 1, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$date}-{$sequence}";
    }

    /**
     * Calculate total amount with discount and tax
     */
    public function calculateAmounts(): void
    {
        // Calculate discount amount
        $this->discount_amount = ($this->base_amount * $this->discount_percentage) / 100;
        
        // Calculate amount after discount
        $amountAfterDiscount = $this->base_amount - $this->discount_amount;
        
        // Calculate tax amount
        $this->tax_amount = ($amountAfterDiscount * $this->tax_percentage) / 100;
        
        // Calculate final amount
        $this->final_amount = $amountAfterDiscount + $this->tax_amount;
    }

    /**
     * Check if billing is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               in_array($this->status, ['pending', 'sent']);
    }

    /**
     * Check if billing is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Mark as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_date' => Carbon::now()
        ]);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(string $paymentMethod = null, string $paymentReference = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => Carbon::now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference
        ]);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName(): string
    {
        $statuses = [
            'draft' => 'Draft',
            'pending' => 'Pending Payment',
            'sent' => 'Sent to Client',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status color class
     */
    public function getStatusColorClass(): string
    {
        $colors = [
            'draft' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'sent' => 'bg-blue-100 text-blue-800',
            'paid' => 'bg-green-100 text-green-800',
            'overdue' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800'
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmount(string $field = 'final_amount'): string
    {
        return number_format($this->{$field}, 2) . ' ' . $this->currency;
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return $this->due_date->diffInDays(Carbon::now());
    }

    // =============================================
    // BOOT METHOD
    // =============================================

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate billing reference when creating
        static::creating(function ($billing) {
            if (!$billing->billing_reference) {
                $billing->billing_reference = static::generateBillingReference();
            }
            
            // Calculate amounts
            $billing->calculateAmounts();
        });

        // Recalculate amounts when updating
        static::updating(function ($billing) {
            if ($billing->isDirty(['base_amount', 'discount_percentage', 'tax_percentage'])) {
                $billing->calculateAmounts();
            }
        });
    }
}