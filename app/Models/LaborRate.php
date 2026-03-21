<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborRate extends Model
{
    protected $fillable = [
        'service_type',
        'standard_fee',
        'description',
    ];

    protected $casts = [
        'standard_fee' => 'decimal:2',
    ];

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
