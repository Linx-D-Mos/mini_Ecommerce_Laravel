<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\SlugService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, SlugService $slugService)
    {
        $validated = $request->validated();
        $slug = $slugService->createSlug($validated['name']);

        $imagePath = $request->file('image')->store('products', 'public');
        $contentPath = $request->file('content')->store('products_private', 'local');

        $product = DB::transaction(
            function () use ($validated, $slug, $imagePath, $contentPath) {
                $data = array_merge($validated, [
                    'slug' => $slug,
                    'image_path' => $imagePath,
                    'content_path' => $contentPath,
                ]);
               $product = Product::create($data);
               return $product;
            }
        );
        return (new ProductResource($product))
            ->additional(
                ['message' => '¡Producto creado con exito!']
            )
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
