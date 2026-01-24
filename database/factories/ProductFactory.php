<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Services\SlugService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       $name = fake()->name();
        return [
            'name' => $name,
            'price' => fake()->numberBetween(5000,10000),
            'slug' =>  app(SlugService::class)->createSlug($name),
            'description' => fake()->paragraph(),
            'image_path' => fake()->url(),
            'content_path' => fake()->url(),
            'status' => fake()->randomElement(ProductStatus::cases()),
        ];
    }
}
