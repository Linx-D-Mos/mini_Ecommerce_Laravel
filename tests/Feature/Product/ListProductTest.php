<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('only show the published products', function () {
    $user = User::factory()->create(['is_admin' => true]);
    $producto_visible = Product::factory()->create(
        [
            'name' => 'Producto real',
            'status' => 'published'
        ]
    );
    $producto_invisible = Product::factory()->create(
        [
            'name' => 'Cant see me',
            'status' => 'draft'
        ]
    );
    $response = actingAs($user)
    ->getJson('/api/products');
    $response->assertOk();
    $response->assertJsonCount(1,'data');
    $response->assertJsonFragment(['name' => 'Producto real']);
    $response->assertJsonMissing(['name' => 'Cant see me']);
    $response->assertJsonFragment([
        'price_formatted' => '$' . number_format($producto_visible->price / 100, 2) . ' USD'
    ]);
});

it('can show a search producto that is published', function () {
    $user = User::factory()->create(['is_admin' => true]);
    $producto_visible = Product::factory()->create(
        [
            'name' => 'Laravel book',
            'status' => 'published'
        ]
    );
    $producto_invisible = Product::factory()->create(
        [
            'name' => 'Python Book',
            'status' => 'published'
        ]
    );
    $response = actingAs($user)
    ->getJson('/api/products?search=laravel');
    $response->assertOk();
    $response->assertJsonCount(1,'data');
    $response->assertJsonFragment(['name' => 'Laravel book']);
    $response->assertJsonMissing(['name' => 'Python Book']);
    $response->dump();
});
