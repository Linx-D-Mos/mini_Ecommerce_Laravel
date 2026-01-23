<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Symfony\Component\Translation\StaticMessage;

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
            'user_id' => User::factory()->create(),
            'total_amount' => 0,
            'bought_at' => null,
        ];
    }

    public function withProducts(int $count = 1): static {
        return $this->afterCreating(function (Order $order) use ($count) {
            $products = Product::factory($count)->create();
            foreach($products as $product){
                $order->products()->attach($product, ['price_at_purchase' => $product->price]);
            }
            $order->update(['total_amount' => $products->sum('price')]);
        });
    }
    
    //$order = Order::factory()->withProducts(3)->create();
}
