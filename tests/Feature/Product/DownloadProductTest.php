<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


uses(RefreshDatabase::class);

test('can download a file', function () {
    //Arranges de almacenamiento falso
    Storage::fake('public');
    Storage::fake('local');
    //Arrange de subida de archivo
    $image = UploadedFile::fake()->image('cover.jpg');
    $book = UploadedFile::fake()->create('book.zip', 5000);
    //Arrange de creación de usuarios y productos
    $user = User::factory()->create(['is_admin' => true]);
    $product = Product::factory()->create(
        [
            'image_path' => $image,
            'content_path' => $book,
        ]
    );
    $order = Order::factory()->create(
        [
            'user_id' => $user->id,
            'bought_at' => now(),
        ]
    );
    //Definir la relación entre order, producto y  el envio del price_at_purchase de order_product
    $order->products()->attach($product, ['price_at_purchase' => $product->price]);
    //Actualización del total.
    $order->update(['total_amount' => $product->price]);
    //Creación de la petición a la ruta de descarga del producto.
    $response = $this->actingAs($user)
        ->getJson("/api/products/{$product->id}/download");  
    //validaciones    
    $response->assertOk()
        ->assertJsonStructure(['url']);
    $downloadUrl = $response->json('url');

    $this->assertStringContainsString('/signed/download', $downloadUrl);
    $this->assertStringContainsString('signature=', $downloadUrl);

});
