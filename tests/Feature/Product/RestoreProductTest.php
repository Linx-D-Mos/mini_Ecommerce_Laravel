<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('admin can view deleted products', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $product = Product::factory()->create(['name' => 'borrame']);
    $product->delete();

    actingAs($admin)->getJson('/api/products')
        ->assertJsonMissing(['name' => 'borrame']);

    actingAs($admin)->getJson('/api/products/trashed')
        ->assertOk()
        ->assertJsonFragment(['name' => 'Borrame']);
});

it('admin can restore a deleted product', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $product = Product::factory()->create(['name' => 'arremangala rempulajada arremangala']);
    $product->delete();

    $response = actingAs($admin)
    ->patchJson("/api/products/{$product->id}/restore");

    $response->assertOk()->dump();

    expect($product->fresh()->deleted_at)->toBeNull();
});
