<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Productcategory;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::all();
        return $product;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category = Productcategory::find($request->productcategory_id); // Buscamos la categoría
        $brand = Brand::find($request->brand_id); // Buscamos la marca

        // Verificamos que la categoría exista, y si es así, se crea el producto
        if($category && $brand) {
            $product = $category->products()->create([ // No es necesario añadir la id de la categoría en el update si está en $category ya que products()
                'nombre' => $request->nombre,          // ya asegura la relación entre ambas tablas
                'descripcion' => $request->descripcion,
                'descripcion_corta' => $request->descripcion_corta,
                'brand_id' => $request->brand_id,
                'estado_producto' => $request->estado_producto,
                'precio' => $request->precio,
                'precio_rebajado' => $request->precio_rebajado,
                'sku' => $request->sku,
                'isbn_ean' => $request->isbn_ean,
                'inventario' => $request->inventario
            ]);

            return response()->json([
                'result' => 'Producto creado.',
                'data' => $product
            ], 201);

        } else {
            return response()->json([
                'result' => 'La categoría y/o la marca no existen.'
            ], 404);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if($product) {
            return $product;

        } else {
            return response()->json([
                'result' => 'No se ha encontrado el producto.'
            ]);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $product_id)
    {
        $category = Productcategory::find($request->productcategory_id); // Buscamos la categoría
        $brand = Brand::find($request->brand_id); // Buscamos la marca

        // Verificamos que la categoría existe
        if($category && $brand) {
            $product = $category->products()->where('id', $product_id)->update([ // No es necesario añadir la id de la categoría en el update si está en $category
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'descripcion_corta' => $request->descripcion_corta,
                'brand_id' => $request->brand_id,
                'estado_producto' => $request->estado_producto,
                'precio' => $request->precio,
                'precio_rebajado' => $request->precio_rebajado,
                'sku' => $request->sku,
                'isbn_ean' => $request->isbn_ean,
                'inventario' => $request->inventario
            ]);

            return response()->json([
                'result' => 'Producto actualizado.',
                'data' => $product
            ], 200);

        } else {
            return response()->json([
                'result' => 'La categoría y/o la marca no existen.'
            ], 404);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if($product) {
            $product->delete();

            return response()->json([
                'result' => 'Producto eliminado.'
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado el producto.'
            ], 404);
        }
        
        
    }
}
