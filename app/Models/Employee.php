<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'user_id',
        'department_name',
        'job_title',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'employee_id', 'employee_id');
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class, 'employee_id', 'employee_id');
    }
}
