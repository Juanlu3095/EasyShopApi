<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Models\Image;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::all();
        return response()->json(BrandResource::collection($brands));
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
        $brand = Brand::create([
            'nombre' => $request->nombre
        ]);

        // PARA CREAR CATEGORÍA CON IMAGEN EXISTENTE
        $brandId = $brand->id; // Obtenemos la id de la marca creada
        $imageId = $request->imagen_id; // Contiene la id de la imagen a asignar para la marca
        $imageEditar = Image::find($imageId); // El resultado de buscar la id del formulario del front en la base de datos

        if($imageEditar) {
            
            if($imageEditar && !$imageEditar->imageable_id) { // Nos aseguramos que la imagen no esté asignada a otro elemento
                $imageEditar->update([
                    'imageable_id' => $brandId,
                    'imageable_type' => Brand::class,
                ]);
            } else {
                return response()->json([
                    'result' => 'La imagen ya está asignada a otro elemento.',
                    'data' => $imageId
                ], 403);
            }
        }

        return response()->json([
            'result' => 'Marca creada.',
            'data' => $imageEditar
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $brand = Brand::find($id);
        return new BrandResource(($brand));
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
        $brand = Brand::find($id);
        
        if($brand) {
            $brand->update([
                'nombre' => $request->nombre
            ]);

            // Vinculamos la nueva imagen seleccionada si ya estaba en la base de datos
            $nuevaImagen = Image::find($request->imagen_id); // Si se usase $brand->images()->update([...]), se actualizarán todas las images vinculadas

            if($nuevaImagen) { // SI EXISTE LA IMAGEN, ACTUALIZAR LA ID Y EL TYPE

                if(!$nuevaImagen->imageable_id || $nuevaImagen->imageable_id == $id) {

                    // Desvinculamos la imagen actual, no la borramos para que se pueda volver a usar. SÓLO lo hacemos si no hay imagen asignada a la nueva imagen.
                    // Ésto sólo lo hacemos en caso de que la id de la categoria no coincida con imageable_id de la imagen, para que no se pierda la imagen asignada
                    if($nuevaImagen->imageable_id != $id) {
                        $brand->images()->update([
                            'imageable_id' => null,
                            'imageable_type' => null
                        ]);
                    }
                    
                    $nuevaImagen->update([
                        'imageable_id' => $brand->id,
                        'imageable_type' => Brand::class, // se añade App\Models\Brand
                    ]);

                    return response()->json([
                        'result' => 'Marca modificada.',
                        'data' => $brand
                    ], 200);

                } else {
                    return response()->json([
                        'result' => 'La imagen ya está asignada a otro elemento.',    
                    ], 403);
                }
                
            } else {
                return response()->json([
                    'result' => 'Imagen no encontrada.'
                ], 404);
            }

        } else {
            return response()->json([
                'result' => 'Marca no encontrada.'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idArray)
    {
        $ids = explode(",",$idArray); // Obtenemos las id del Array
        $marcas = Brand::find($ids); // Buscamos las marcas en la base de datos con las id
        $marcas->map(function($marca) { // Mapeamos las marcas obtenidas de la BD para quitar todas las imágenes asignadas a cada id
            $marca->images()->update([ // Para cada marca desvinculamos las imágenes asignadas antes de eliminar las marcas
                'imageable_id' => null,
                'imageable_type' => null
            ]);

            $marca->products()->update([ // Para cada marca desvinculamos los productos asignados antes de eliminar las marcas
                'brand_id' => null
            ]);

            $marca->delete(); // Eliminamos la marca
        });

        return response()->json([
            'data' => $marcas
        ], 200); 
    }
}
