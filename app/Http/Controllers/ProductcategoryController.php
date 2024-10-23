<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductcategoryRequest;
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
    public function store(ProductcategoryRequest $request)
    {
        $productcategory = Productcategory::create([
            'nombre' => $request->nombre,
            'slug' => $request->slug
        ]);

        // PARA CREAR CATEGORÍA CON IMAGEN NUEVA
        /* $productcategory->images()->create([
            'nombre' => $request->nombre . '_category',
            'alt' => $request->alt,
            'descripcion' => $request->descripcion,
            'leyenda' => $request->leyenda,
            'ruta_archivo' => $request->ruta_archivo
        ]); */

        // PARA CREAR CATEGORÍA CON IMAGEN EXISTENTE
        $productcategoryId = $productcategory->id; // Obtenemos la id de la categoría creada
        $imageId = $request->imagen_id; // Contiene la id de la imagen a asignar para la categoría
        $imageEditar = Image::find($imageId); // El resultado de buscar la id del formulario del front en la base de datos

        if($imageEditar) {
            
            if($imageEditar && !$imageEditar->imageable_id) { // Nos aseguramos que la imagen no esté asignada a otro elemento
                $imageEditar->update([
                    'imageable_id' => $productcategoryId,
                    'imageable_type' => Productcategory::class,
                ]);
            } else {
                return response()->json([
                    'result' => 'La imagen ya está asignada a otro elemento.',
                    'data' => $imageId
                ], 403);
            }
        } // Lo malo de esto es que primero se debe crear la categoría para coger la id y asignarlo a la imagen, por lo que si la imagen no puede asignarse,
          // de todas formas se crea la categoría.

        return response()->json([
            'result' => 'Categoría de producto creada.',
            'data' => $imageEditar
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
    public function update(ProductcategoryRequest $request, string $id)
    {
        $productcategory = Productcategory::find($id);
        
        if($productcategory) {
            $productcategory->update([
                'nombre' => $request->nombre,
                'slug' => $request->slug
            ]);

            // Vinculamos la nueva imagen seleccionada si ya estaba en la base de datos
            $nuevaImagen = Image::find($request->imagen_id); // Si se usase $productcategory->images()->update([...]), se actualizarán todas las images vinculadas

            if($nuevaImagen) { // SI EXISTE LA IMAGEN, ACTUALIZAR LA ID Y EL TYPE

                if(!$nuevaImagen->imageable_id || $nuevaImagen->imageable_id == $id && $nuevaImagen->imageable_type == Productcategory::class) {

                    // Desvinculamos la imagen actual de la categoría si la hay.
                    // Ésto sólo lo hacemos en caso de que la id de la categoria no coincida con imageable_id de la imagen, para que no se pierda la imagen asignada
                    if($nuevaImagen->imageable_id != $id) {
                        $productcategory->images()->update([
                            'imageable_id' => null,
                            'imageable_type' => null
                        ]);
                    }

                    $nuevaImagen->update([
                        'imageable_id' => $productcategory->id,
                        'imageable_type' => Productcategory::class, // se añade App\Models\Productcategory
                    ]);

                    return response()->json([
                        'result' => 'Categoría de producto modificada.',
                        'data' => $productcategory
                    ], 200);

                } else {
                    return response()->json([
                        'result' => 'La imagen ya está asignada a otro elemento.'
                    ], 403);
                }
                
            } else {
                return response()->json([
                    'result' => 'Imagen no encontrada.'
                ], 404);
            }

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

        if($productcategory) {
            // Desvinculamos la imagen actual de la categoría, no se borra la imagen asignada de la base de datos
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

        } else {
            return response()->json([
                'result' => 'Categoría de producto no encontrada.',
            ], 404);

        }
        
    }
}
