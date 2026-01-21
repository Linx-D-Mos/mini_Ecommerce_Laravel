<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
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
        $slug = fake()->words(3);
        return [
            'name' => fake()->name(),
            'price' => fake()->numberBetween(5000,10000),
            'slug' =>  fake()->bothify('???-???-???'),
            'description' => fake()->paragraph(),
            'image_path' => fake()->url(),
            'content_path' => fake()->url(),
            'status' => fake()->randomElement(ProductStatus::cases()),
        ];
    }
}
