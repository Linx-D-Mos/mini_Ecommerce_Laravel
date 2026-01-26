<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\DownloadProductService;
use App\Services\RestoreProductService;
use App\Services\SlugService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $products = Product::published()->search(request('search'))->paginate(10);
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, SlugService $slugService)
    {
        //Obtencion de datos validados
        $validated = $request->validated();
        $slug = $slugService->createSlug($validated['name']);

        //Subida de archivos al storage
        $imagePath = $request->file('image')->store('products', 'public');
        $contentPath = $request->file('content')->store('products_private', 'local');

        //Operación de creación
        $product = Product::create(array_merge(
            $validated,
            [
                'slug' => $slug,
                'image_path' => $imagePath,
                'content_path' => $contentPath,
            ]
        ));
        //Envio de respuesta
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
    public function download(Product $product, DownloadProductService $service)
    {
        $user = request()->user();
        try {
            $url = $service->generateSignedUrl($user, $product);

            return response()->json([
                'url' => $url,
                'message' => 'Url generada correctamente, expira en 30 minutos'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Business Logic Error',
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function trashed()
    {
        $products = Product::eliminados()->paginate(10);
        return ProductResource::collection($products);
    }
    
    public function restore(string $id, RestoreProductService $service)
    {
        $product = $service->restore($id);
        return (new ProductResource($product))
        ->additional(['message', '¡Producto recuperado con exito!'])
        ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
