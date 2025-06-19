<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'payment_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'payment_id',
        'action',
        'request_data',
        'response_data',
        'status_before',
        'status_after',
        'notes',
        'logged_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'logged_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the payment this log belongs to
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope to get logs by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get recent logs
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('logged_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope to get error logs
     */
    public function scopeErrors($query)
    {
        return $query->where('action', 'like', '%error%')
                    ->orWhere('action', 'like', '%fail%');
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Get time elapsed since log entry
     */
    public function getTimeElapsed(): string
    {
        return $this->logged_at->diffForHumans();
    }

    /**
     * Check if this is an error log
     */
    public function isError(): bool
    {
        return str_contains(strtolower($this->action), 'error') || 
               str_contains(strtolower($this->action), 'fail');
    }

    /**
     * Get formatted request data
     */
    public function getFormattedRequestData(): string
    {
        if (!$this->request_data) {
            return 'No request data';
        }

        return json_encode($this->request_data, JSON_PRETTY_PRINT);
    }

    /**
     * Get formatted response data
     */
    public function getFormattedResponseData(): string
    {
        if (!$this->response_data) {
            return 'No response data';
        }

        return json_encode($this->response_data, JSON_PRETTY_PRINT);
    }

    /**
     * Get log summary
     */
    public function getLogSummary(): string
    {
        $summary = "Action: {$this->action}";
        
        if ($this->status_before && $this->status_after) {
            $summary .= " | Status: {$this->status_before} → {$this->status_after}";
        }
        
        if ($this->notes) {
            $summary .= " | {$this->notes}";
        }

        return $summary;
    }
} 