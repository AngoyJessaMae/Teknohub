<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    protected $primaryKey = 'billing_id';

    protected $fillable = [
        'service_id',
        'labor_fee',
        'parts_fee',
        'total_amount',
        'payment_status',
    ];

    protected $casts = [
        'labor_fee' => 'decimal:2',
        'parts_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_id', 'service_id');
    }
}
