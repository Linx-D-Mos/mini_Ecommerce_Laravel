<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'total_amount',
        'bought_at',
    ];
    protected $casts = [
        'bought_at' => 'datetime',
    ];
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('price_at_purchase')
            ->withTimestamps();
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
