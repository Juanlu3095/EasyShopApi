<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = Image::all();
        return ImageResource::collection($images);
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
    public function store(ImageRequest $request)
    {
        $image = new Image;
        $image->nombre = $request->nombre;
        $image->alt = $request->alt;
        $image->descripcion = $request->descripcion;
        $image->leyenda = $request->leyenda;
        
        // Manejo del archivo de la imagen
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $filename = $file->getClientOriginalName(); // Para coger el nombre que ya traía el archivo
            $rutaTemporal = 'public/media/' . $filename;

            if(Storage::exists($rutaTemporal)) { // Comprobamos que el archivo a subir no exista ya en el storage, y si es así le cambiamos el nombre
                $newfilename = time() . '_' . $filename; // time() no puede ir después de $filename, ya que $filename acaba en .png

                $file->storeAs('public/media', $newfilename); // Guardar archivo en storage
                $rutaReal = 'media/' . $newfilename; // La ruta del archivo que se guarda en la base de datos y desde la que se puede acceder al archivo desde la web
                $image->ruta_archivo = $rutaReal;

            } else {
                $file->storeAs('public/media', $filename); // Guardar archivo en storage
                $rutaReal = 'media/' . $filename; // La ruta del archivo que se guarda en la base de datos y desde la que se puede acceder al archivo desde la web
                $image->ruta_archivo = $rutaReal;
            }
            
            
            $image->save();

            return response()->json([
                'result' => 'Imagen guardada.'
            ], 201);

        } else {
            return response()->json(['error' => 'No se proporcionó un archivo válido.'], 400);
        }

        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $image = Image::find($id);
        return new ImageResource($image);
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
        $image = Image::find($id);
        $image->update([
            'nombre' => $request->nombre,
            'alt' => $request->alt,
            'leyenda' => $request->leyenda,
            'descripcion' => $request->descripcion
        ]);

        $image->save();

        return response()->json([
            'success' => true,
            'data' => $image
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $image = Image::find($id);
        $imageFile = Storage::disk('public')->exists($image->ruta_archivo);

        if($image) {
            if($imageFile) { // Si existe el archivo en el storage, lo eliminamos.
                Storage::disk('public')->delete($image->ruta_archivo);
            }
            $image->delete();

            return response()->json([
                'result' => 'Imagen eliminada.'
            ], 200);

        } else {
            return response()->json([
                'result' => 'Imagen no encontrada.'
            ], 404);
        }

    }
}
