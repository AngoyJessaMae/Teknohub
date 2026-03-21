<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $primaryKey = 'purchase_id';

    protected $fillable = [
        'item_id',
        'service_id',
        'customer_id',
        'quantity',
        'total_price',
        'date_purchased',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'date_purchased' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_id', 'service_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
