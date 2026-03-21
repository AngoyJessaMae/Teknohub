<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'user_id',
        'position',
        'date_added',
    ];

    protected $casts = [
        'date_added' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
