<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

use function Illuminate\Support\hours;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'total_amount' => fake()->numberBetween(10000,50000),
            'bought_at' => fake()->dateTimeBetween('-3 days','-1 hour'),
        ];
    }
    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            if($order->products()->count() === 0){
                $order->products()->attach(Product::factory()->create());
            }
        });
    }
}
