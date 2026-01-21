<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Nette\Utils\Random;

use function Symfony\Component\Clock\now;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(20)->create();
        $products = Product::factory(20)->create();
        foreach ($users as $user) {
            if (rand(1, 2) === 1) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => 0,
                    'bought_at' => now(),
                ]);
                $count = rand(1, 3);
                $total_amount = 0;
                for ($i = 1; $i <= $count; $i++) {
                    $product = $products->random();
                    $order->products()->attach($product, ['price_at_purchase' => $product->price]);
                    $total_amount = $product->price + $total_amount;
                }
                $order->update(['total_amount' => $total_amount]);
            }
        }
    }
}
