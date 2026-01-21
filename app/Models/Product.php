<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'slug',
        'description',
        'public_img',
        'content_path',
        'status',
    ];
    protected $casts = [
        'status' => ProductStatus::class,
    ];
    public function orders(): BelongsToMany{
        return $this->belongsToMany(Order::class)
        ->withPivot('price_at_purchase')
        ->withTimestamps();
    }
}
