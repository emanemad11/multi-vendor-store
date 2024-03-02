<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Product::paginate();
        $products = Product::filter($request->query())
            ->with('category:id,name', 'store:id,name', 'tags:id,name')
            ->paginate();

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'status' => 'in:active,inactive',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|gt:price',
        ]);

        // $user = $request->user();
        // if (!$user->tokenCan('products.create')) {
        //     abort(403, 'Not allowed');
        // }

        $product = Product::create($request->all());
        // return $product;


        return Response::json($product, 201, [
            'Location' => route('products.show', $product->id), //header
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {


        return new ProductResource($product);

        // return $product
            // ->load('category:id,name', 'store:id,name', 'tags:id,name');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'status' => 'in:active,inactive',
            'price' => 'sometimes|required|numeric|min:0',
            'compare_price' => 'nullable|numeric|gt:price',
        ]);

        // $user = $request->user();
        // if (!$user->tokenCan('products.update')) {
        //     abort(403, 'Not allowed');
        // }

        $product->update($request->all());


        return Response::json($product);
    }

    /**
     * Remove the specified resource from storage.
     */

     public function destroy($id)
    {
        // $user = Auth::guard('sanctum')->user();
        // if (!$user->tokenCan('products.delete')) {
        //     return response([
        //         'message' => 'Not allowed'
        //     ], 403);
        // }

        Product::destroy($id);
        return [
            'message' => 'Product deleted successfully',
        ];
    }
}
