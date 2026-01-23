<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\URL;

use Carbon\Carbon;

class DownloadProductService
{
    public function generateSignedUrl(User $user, Product $product): string
    {

        if (! $this->verifyingPurchase($user, $product)) {
            throw new Exception('No has comprado aún este producto');
        }
        return URL::temporarySignedRoute(
            'files.download',
            Carbon::now()->addMinutes(30),
            ['path' => $product->content_path],
        );
    }
    public function verifyingPurchase(User $user, Product $product): bool
    {
        return $user->orders()->whereHas('products', function ($query) use ($product) {
            $query->where('products.id', $product->id);
        })->exists();
    }
}
