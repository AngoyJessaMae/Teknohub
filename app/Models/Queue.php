<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    protected $primaryKey = 'queue_id';

    protected $fillable = [
        'service_id',
        'queue_number',
        'queue_position',
        'priority_level',
        'queue_status',
        'status',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_id', 'service_id');
    }
}
