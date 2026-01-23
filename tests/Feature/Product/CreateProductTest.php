<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(RefreshDatabase::class);

it('allows an admin user can create a producto', function () {
    //Fake storage
    Storage::fake('public'); //Simulamos el disco público (imágenes).
    Storage::fake('local'); //Simulamos el disco local (archivos privados).

    //Arrange
    $admin = User::factory()->create(['is_admin' => true]);
    $image = UploadedFile::fake()->image('cover.jpg');
    $pdf = UploadedFile::fake()->create('guide.pdf', 5000);

    //Petición falsa
    $response = $this->actingAs($admin)
        ->postJson('/api/products', [
            'name' => 'Laravel Masterclass',
            'price' => 2500,
            'description' => 'A great book',
            'image' => $image,
            'content' => $pdf,
            'status' => 'draft'
        ]);
    //Verificación de la respuesta.
    $response->assertCreated();
    $this->assertDatabaseHas('products', [
        'name' => 'Laravel Masterclass',
        'slug' => 'laravel-masterclass',
        'price' => 2500,
    ]);
})->group('create_product');


test('regular users cannot create products', function () {
    $user = User::factory()->create(['is_admin' => false]);
    
    $response = $this->actingAs($user)
    ->postJson('/api/products', [
        'name' => 'Hackeado papu',
        'price' => 666,
    ]);
    $response->assertForbidden();

    $this->assertDatabaseMissing('products', ['name' => 'Hackeado papu']);
})->group('create_product');
it('cant create a producto without file', function () {});
it('cant create a product without name', function () {});
