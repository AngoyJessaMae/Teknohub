<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'customer_id',
        'employee_id',
        'device_type',
        'device_description',
        'problem_description',
        'date_received',
        'date_created',
        'date_completed',
        'appointment_request',
        'status',
    ];

    protected $casts = [
        'date_created' => 'datetime',
        'date_completed' => 'datetime',
        'date_received' => 'datetime',
        'appointment_request' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function queue(): HasOne
    {
        return $this->hasOne(Queue::class, 'service_id', 'service_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'service_id', 'service_id');
    }

    public function billing(): HasOne
    {
        return $this->hasOne(Billing::class, 'service_id', 'service_id');
    }
}
