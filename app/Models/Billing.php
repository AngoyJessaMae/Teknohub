<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    protected $primaryKey = 'billing_id';

    protected $fillable = [
        'service_id',
        'employee_id',
        'warranty',
        'labor_fee',
        'parts_fee',
        'total_amount',
        'payment_status',
        'date_billed',
        'payment_mode',
        'payment_date',
    ];

    protected $casts = [
        'labor_fee' => 'decimal:2',
        'parts_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'date_billed' => 'date',
        'payment_date' => 'date',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_id', 'service_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
