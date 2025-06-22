<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_request_id',
        'inspector_id',
        'data',
        'status',
        'progress',
        'completed_at',
    ];

    protected $casts = [
        'data' => 'array',
        'completed_at' => 'datetime',
    ];

    public function inspectionRequest()
    {
        return $this->belongsTo(InspectionRequest::class);
    }

    public function inspector()
    {
        return $this->belongsTo(Inspector::class);
    }
}
