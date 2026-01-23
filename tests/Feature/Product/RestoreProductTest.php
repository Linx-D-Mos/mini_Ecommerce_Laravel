<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin can view deleted products', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $product = Product::factory()->create(['name' => 'borrame']);
    $product->delete();

    $this->actingAs($admin)->getJson('/api/products')
        ->assertJsonMissing(['name' => 'borrame']);

    $this->actingAs($admin)->getJson('/api/products/trashed')
        ->assertOk()
        ->assertJsonFragment(['name' => 'borrame']);
});

it('admin can restore a deleted product', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $product = Product::factory()->create(['name' => 'borrame']);
    $product->delete();

    $response = $this->actingAs($admin)
    ->pathJson("/api/products/{$product->id}/restore");

    $response->assertOk();

    expect($product->fresh()->deleted_At)->toBeNull();
})->skip();
