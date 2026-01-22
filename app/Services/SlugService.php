<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Support\Str;

class SlugService {
    public function createSlug(String $name):string {
        $slug = Str::slug($name);
        $originalSlug= $slug;
        $count = 1;
        while(Product::where('slug', $slug)->exists()){
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }
        return $slug;
    }
}
