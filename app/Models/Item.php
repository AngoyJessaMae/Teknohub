<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'item_name',
        'category',
        'price',
        'stock_quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'item_id', 'item_id');
    }
}
