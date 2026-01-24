<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'slug',
        'description',
        'image_path',
        'content_path',
        'status',
    ];
    protected $casts = [
        'status' => ProductStatus::class,
    ];
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('price_at_purchase')
            ->withTimestamps();
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'ILIKE', "%{$term}%")
                    ->orWhere('description', 'ILIKE', "%{$term}%");
            });
        }
    }
    public function scopeEliminados(Builder $query):void
    {  
        $query->onlyTrashed();
    }

    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes)=>
            '$' . number_format($attributes['price'] / 100,2) . ' USD',
        );
    }
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => ucfirst(strtolower($value)),
        );
    }
}
