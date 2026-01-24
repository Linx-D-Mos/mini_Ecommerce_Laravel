<?php

namespace App\Services;

use App\Models\Product;

class RestoreProductService
{
    public function restore(string $id): Product
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        return $product;
    }
}
