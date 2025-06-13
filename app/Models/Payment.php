<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'inspection_request_id',
        'transaction_reference',
        'amount',
        'currency',
        'payment_method',
        'gateway_provider',
        'gateway_transaction_id',
        'gateway_reference',
        'status',
        'failure_reason',
        'payer_name',
        'payer_phone',
        'payer_email',
        'initiated_at',
        'completed_at',
        'failed_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the inspection request this payment is for
     */
    public function inspectionRequest(): BelongsTo
    {
        return $this->belongsTo(InspectionRequest::class);
    }

    /**
     * Get payment logs for debugging
     */
    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get payments by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get processing payments
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope to get completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get payments by method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope to get mobile money payments
     */
    public function scopeMobileMoney($query)
    {
        return $query->whereIn('payment_method', ['mtn_momo', 'airtel_money']);
    }

    /**
     * Scope to get card payments
     */
    public function scopeCardPayments($query)
    {
        return $query->whereIn('payment_method', ['visa', 'mastercard']);
    }

    /**
     * Scope to get today's payments
     */
    public function scopeToday($query)
    {
        return $query->whereDate('initiated_at', Carbon::today());
    }

    /**
     * Scope to get this month's payments
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('initiated_at', Carbon::now()->month)
            ->whereYear('initiated_at', Carbon::now()->year);
    }

    /**
     * Scope to search payments
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('transaction_reference', 'like', "%{$term}%")
              ->orWhere('gateway_transaction_id', 'like', "%{$term}%")
              ->orWhere('payer_name', 'like', "%{$term}%")
              ->orWhere('payer_phone', 'like', "%{$term}%")
              ->orWhere('payer_email', 'like', "%{$term}%");
        });
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Generate unique transaction reference
     */
    public static function generateTransactionReference(): string
    {
        do {
            $code = 'TXN' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('transaction_reference', $code)->exists());

        return $code;
    }

    /**
     * Get status display name with color
     */
    public function getStatusInfo(): array
    {
        $statusInfo = [
            'pending' => ['name' => 'Pending', 'color' => '#F59E0B', 'icon' => 'clock'],
            'processing' => ['name' => 'Processing', 'color' => '#3B82F6', 'icon' => 'refresh'],
            'completed' => ['name' => 'Completed', 'color' => '#10B981', 'icon' => 'check'],
            'failed' => ['name' => 'Failed', 'color' => '#EF4444', 'icon' => 'x'],
            'cancelled' => ['name' => 'Cancelled', 'color' => '#6B7280', 'icon' => 'x-circle'],
            'refunded' => ['name' => 'Refunded', 'color' => '#8B5CF6', 'icon' => 'arrow-left']
        ];

        return $statusInfo[$this->status] ?? $statusInfo['pending'];
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodDisplayName(): string
    {
        $methods = [
            'mtn_momo' => 'MTN Mobile Money',
            'airtel_money' => 'Airtel Money',
            'visa' => 'Visa Card',
            'mastercard' => 'Mastercard',
            'bank_transfer' => 'Bank Transfer'
        ];

        return $methods[$this->payment_method] ?? ucfirst(str_replace('_', ' ', $this->payment_method));
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 0, '.', ',') . ' ' . $this->currency;
    }

    /**
     * Check if payment is mobile money
     */
    public function isMobileMoney(): bool
    {
        return in_array($this->payment_method, ['mtn_momo', 'airtel_money']);
    }

    /**
     * Check if payment is card payment
     */
    public function isCardPayment(): bool
    {
        return in_array($this->payment_method, ['visa', 'mastercard']);
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Mark payment as processing
     */
    public function markAsProcessing(string $gatewayTransactionId = null): void
    {
        $updateData = ['status' => 'processing'];
        
        if ($gatewayTransactionId) {
            $updateData['gateway_transaction_id'] = $gatewayTransactionId;
        }

        $this->update($updateData);
        $this->logActivity('status_changed', 'Payment marked as processing', ['new_status' => 'processing']);
    }

    /**
     * Mark payment as completed
     */
    public function markAsCompleted(string $gatewayTransactionId = null, string $gatewayReference = null): void
    {
        $updateData = [
            'status' => 'completed',
            'completed_at' => Carbon::now()
        ];

        if ($gatewayTransactionId) {
            $updateData['gateway_transaction_id'] = $gatewayTransactionId;
        }

        if ($gatewayReference) {
            $updateData['gateway_reference'] = $gatewayReference;
        }

        $this->update($updateData);
        
        // Update inspection request payment status
        $this->inspectionRequest->update(['payment_status' => 'paid']);
        
        $this->logActivity('payment_completed', 'Payment completed successfully', [
            'gateway_transaction_id' => $gatewayTransactionId,
            'gateway_reference' => $gatewayReference
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(string $reason, array $responseData = []): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'failed_at' => Carbon::now()
        ]);

        $this->logActivity('payment_failed', 'Payment failed', [
            'failure_reason' => $reason,
            'response_data' => $responseData
        ]);
    }

    /**
     * Cancel payment
     */
    public function cancel(string $reason = 'Cancelled by user'): void
    {
        $this->update([
            'status' => 'cancelled',
            'failure_reason' => $reason
        ]);

        $this->logActivity('payment_cancelled', 'Payment cancelled', ['reason' => $reason]);
    }

    /**
     * Process refund
     */
    public function processRefund(float $refundAmount = null): void
    {
        if (!$this->isSuccessful()) {
            throw new \Exception('Cannot refund a payment that is not completed');
        }

        $refundAmount = $refundAmount ?? $this->amount;

        $this->update([
            'status' => 'refunded',
            'amount' => $this->amount - $refundAmount
        ]);

        // Update inspection request payment status
        if ($refundAmount >= $this->amount) {
            $this->inspectionRequest->update(['payment_status' => 'refunded']);
        } else {
            $this->inspectionRequest->update(['payment_status' => 'partial']);
        }

        $this->logActivity('payment_refunded', 'Payment refunded', [
            'refund_amount' => $refundAmount,
            'remaining_amount' => $this->amount
        ]);
    }

    /**
     * Get processing time in minutes
     */
    public function getProcessingTimeMinutes(): ?int
    {
        if (!$this->completed_at || !$this->initiated_at) {
            return null;
        }

        return $this->initiated_at->diffInMinutes($this->completed_at);
    }

    /**
     * Get payment gateway configuration
     */
    public static function getGatewayConfig(string $method): array
    {
        $configs = [
            'mtn_momo' => [
                'provider' => 'MTN Mobile Money',
                'api_endpoint' => config('payments.mtn.api_endpoint'),
                'api_key' => config('payments.mtn.api_key'),
                'timeout' => 30,
                'supported_currencies' => ['RWF']
            ],
            'airtel_money' => [
                'provider' => 'Airtel Money',
                'api_endpoint' => config('payments.airtel.api_endpoint'),
                'api_key' => config('payments.airtel.api_key'),
                'timeout' => 30,
                'supported_currencies' => ['RWF']
            ],
            'visa' => [
                'provider' => 'Stripe',
                'api_endpoint' => 'https://api.stripe.com/v1',
                'api_key' => config('payments.stripe.secret_key'),
                'timeout' => 60,
                'supported_currencies' => ['RWF', 'USD', 'EUR']
            ],
            'mastercard' => [
                'provider' => 'Stripe',
                'api_endpoint' => 'https://api.stripe.com/v1',
                'api_key' => config('payments.stripe.secret_key'),
                'timeout' => 60,
                'supported_currencies' => ['RWF', 'USD', 'EUR']
            ]
        ];

        return $configs[$method] ?? [];
    }

    /**
     * Log payment activity
     */
    private function logActivity(string $action, string $message, array $data = []): void
    {
        $this->logs()->create([
            'action' => $action,
            'request_data' => $data,
            'response_data' => null,
            'status_before' => $this->getOriginal('status'),
            'status_after' => $this->status,
            'notes' => $message
        ]);
    }

    /**
     * Get payment statistics
     */
    public function getStatistics(): array
    {
        return [
            'processing_time_minutes' => $this->getProcessingTimeMinutes(),
            'is_successful' => $this->isSuccessful(),
            'is_mobile_money' => $this->isMobileMoney(),
            'is_card_payment' => $this->isCardPayment(),
            'formatted_amount' => $this->getFormattedAmount(),
            'payment_method_display' => $this->getPaymentMethodDisplayName(),
            'status_info' => $this->getStatusInfo(),
            'days_since_payment' => $this->initiated_at ? $this->initiated_at->diffInDays(Carbon::now()) : null
        ];
    }

    /**
     * Get payment analytics for dashboard
     */
    public static function getAnalytics(Carbon $startDate = null, Carbon $endDate = null): array
    {
        $query = self::query();

        if ($startDate && $endDate) {
            $query->whereBetween('initiated_at', [$startDate, $endDate]);
        } else {
            $query->thisMonth();
        }

        $totalPayments = $query->count();
        $completedPayments = $query->clone()->completed()->count();
        $totalAmount = $query->clone()->completed()->sum('amount');
        $averageAmount = $completedPayments > 0 ? $totalAmount / $completedPayments : 0;

        // Payment method breakdown
        $methodBreakdown = $query->clone()
            ->completed()
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->payment_method => [
                    'count' => $item->count,
                    'total_amount' => $item->total_amount,
                    'percentage' => 0 // Will be calculated below
                ]];
            });

        // Calculate percentages
        foreach ($methodBreakdown as $method => $data) {
            $methodBreakdown[$method]['percentage'] = $totalAmount > 0 
                ? round(($data['total_amount'] / $totalAmount) * 100, 1) 
                : 0;
        }

        // Success rate
        $successRate = $totalPayments > 0 ? round(($completedPayments / $totalPayments) * 100, 1) : 0;

        // Failed payments analysis
        $failedPayments = $query->clone()->failed()->count();
        $failureRate = $totalPayments > 0 ? round(($failedPayments / $totalPayments) * 100, 1) : 0;

        return [
            'total_payments' => $totalPayments,
            'completed_payments' => $completedPayments,
            'failed_payments' => $failedPayments,
            'total_amount' => $totalAmount,
            'average_amount' => round($averageAmount, 2),
            'success_rate' => $successRate,
            'failure_rate' => $failureRate,
            'method_breakdown' => $methodBreakdown,
            'currency' => 'RWF'
        ];
    }

    /**
     * Auto-generate transaction reference before creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->transaction_reference) {
                $payment->transaction_reference = self::generateTransactionReference();
            }

            if (!$payment->initiated_at) {
                $payment->initiated_at = Carbon::now();
            }

            if (!$payment->currency) {
                $payment->currency = 'RWF';
            }
        });

        static::created(function ($payment) {
            // Log payment initiation
            $payment->logActivity('payment_initiated', 'Payment initiated', [
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payer_phone' => $payment->payer_phone
            ]);
        });
    }
}