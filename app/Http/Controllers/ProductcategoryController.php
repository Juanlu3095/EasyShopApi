<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductcategoryResource;
use App\Models\Image;
use App\Models\Productcategory;
use Illuminate\Http\Request;

class ProductcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productcategories = Productcategory::all();
        return ProductcategoryResource::collection($productcategories);
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
        $productcategory = Productcategory::create([
            'nombre' => $request->nombre
        ]);

        $productcategory->images()->create([
            'nombre' => $request->nombre . '_category',
            'alt' => $request->alt,
            'descripcion' => $request->descripcion,
            'leyenda' => $request->leyenda,
            'ruta_archivo' => $request->ruta_archivo
        ]);

        return response()->json([
            'result' => 'Categoría de producto creada.',
            'data' => $productcategory
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $productcategory = Productcategory::find($id);
        return new ProductcategoryResource($productcategory);
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
    public function update(Request $request, string $id)
    {
        $productcategory = Productcategory::find($id);
        
        if($productcategory) {
            $productcategory->update([
                'nombre' => $request->nombre
            ]);

            // Desvinculamos la imagen actual, no la borramos para que se pueda volver a usar
            $productcategory->images()->update([
                'imageable_id' => null,
                'imageable_type' => null
            ]);

            // Vinculamos la nueva imagen seleccionada si ya estaba en la base de datos
            $nuevaImagen = Image::find($request->nueva_imagen_id); // Si se usase $productcategory->images()->update([...]), se actualizarán todas las images
                                                                   // vinculadas
            if($nuevaImagen) { // REVISAR ESTO
                $nuevaImagen->update([
                    'imageable_id' => $productcategory->id,
                    'imageable_type' => Productcategory::class, // se añade App\Models\Productcategory
                    'nombre' => $request->nombre_imagen ?? $nuevaImagen->nombre, // Si $request->nombre_imagen no es null se aplica éste. En caso contrario, $nuevaImagen->nombre. Si había datos nulos de la imagen, los corregimos con $nuevaImagen.
                    'alt' => $request->alt ?? $nuevaImagen->alt,
                    'descripcion' => $request->descripcion ?? $nuevaImagen->descripcion,
                    'leyenda' => $request->leyenda ?? $nuevaImagen->leyenda,
                    'ruta_archivo' => $request->ruta_archivo ?? $nuevaImagen->ruta_archivo,
                ]);

            } else { // Creamos una nueva imagen si no se encontrase ninguna en la base de datos

                $productcategory->images()->create([
                    'nombre' => $request->nombre . '_category',
                    'alt' => $request->alt,
                    'descripcion' => $request->descripcion,
                    'leyenda' => $request->leyenda,
                    'ruta_archivo' => $request->ruta_archivo
                ]);
            }

            return response()->json([
                'result' => 'Categoría de producto modificada.',
                'data' => $productcategory
            ], 200);

        } else {
            return response()->json([
                'result' => 'Categoría no encontrada.'
            ], 404);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $productcategory = Productcategory::find($id);

        // Desvinculamos la imagen actual de la categoría
        $productcategory->images()->update([
            'imageable_id' => null,
            'imageable_type' => null
        ]);

        // Desvinculamos la categoría de los productos con esa categoría
        $productcategory->products()->update([
            'productcategory_id' => null 
        ]);

        $productcategory->delete(); // Eliminamos la categoría

        return response()->json([
            'result' => 'Categoría de producto eliminada.',
            'data' => $productcategory
        ], 200);
    }
}
