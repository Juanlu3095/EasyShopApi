<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Error;
use App\Models\Image;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yaza\LaravelGoogleDriveStorage\Gdrive;
use Google\Client;
use Google\Service\Drive;

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

            try {
                // Debemos declarar esto aquí, porque si lo hacemos fuera de try/catch producirá un error si no existe la carpeta
                Gdrive::all("easyshop/images"); // Obtenemos todos los archivos en la ruta indicada en este momento
            } catch (Exception $e) {
                Gdrive::makeDir('easyshop/images'); // Si no existe la carpeta, la creamos
            }

            // Creación de cliente y servicio con los que manejar permisos
            $client = new Client();
            $client->setClientId(config('filesystems.disks.google.clientId'));
            $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
            $accessToken = Cache::get('accessToken'); // Obtenemos el accessToken de la caché

            // Comprobamos que exista $accessToken en la caché, y si es así se lo pasamos al cliente.
            // Ponemos esto aquí porque si lo ponemos en un else en el if de debajo, entrará en el primer if ya que se cumple $client->isAccessTokenExpired()
            // Tener en cuenta que setAccessToken($accessToken) puede dar error su el token no existe o es null y parar el programa
            if($accessToken) {
                $client->setAccessToken($accessToken);
            }

            // Comprobamos si el token está en caché o si el token del cliente ha expirado
            if (!$accessToken || $client->isAccessTokenExpired()) {
                $newAccessToken = $client->fetchAccessTokenWithRefreshToken(config('filesystems.disks.google.refreshToken'));
                Cache::put('accessToken', $newAccessToken, now()->addMinutes(55));
                $client->setAccessToken($newAccessToken);
            }

            $service = new Drive($client);

            $permisos = new Drive\Permission(); // Para dar establecer permisos con los archivos a subir
            $permisos->setRole('reader'); // Asignamos el rol que va a tener
            $permisos->setType('anyone'); // Permitimos el acceso a cualquiera con el link
            
            try {
                // PROBLEMA: SI NO HAY ARCHIVOS EN LA RUTA INDICADA DA ERROR SI USAMOS GDRIVE:ALL()
                $data = Gdrive::all("easyshop/images/"); // Obtenemos todos los archivos en la ruta indicada antes de cualquier otro cambio
                $paths = array_column(json_decode($data), 'path'); // Obtenemos todas las rutas (path) de los archivos que hay en la ruta $data
    
                if ($data && in_array("easyshop/images/$filename", $paths)) { // Si hay un archivo con el mismo nombre
                    $newnamefile = 'image_' . time() . '.' . $file->guessExtension();
                    $image->nombre_archivo = $newnamefile;
                    Gdrive::put("easyshop/images/$newnamefile", $file); // Creamos un nuevo nombre
    
                    $updatedData = Gdrive::all("easyshop/images/");
                    $imageId = array_search("easyshop/images/$newnamefile", array_column(json_decode($updatedData, true), 'path')); // Usar $updatedData que está actualizado
                    $image->ruta_archivo = $updatedData[$imageId]['extra_metadata']['id']; // AQUÍ FUNCIONA $data PORQUE HEMOS COMPROBADO QUE EXISTE EN EL IF

                    $service->permissions->create($image->ruta_archivo, $permisos);
                } else {
                    $image->nombre_archivo = $filename; 
                    Gdrive::put("easyshop/images/$filename", $file);

                    $updatedData = Gdrive::all("easyshop/images/"); // SI SE USA $data SE OBTENDRÁN LOS DATOS DE ANTES DE GUARDAR EL ARCHIVO
                    $imageId = array_search("easyshop/images/$filename", array_column(json_decode($updatedData, true), 'path'));
                    $image->ruta_archivo = $updatedData[$imageId]['extra_metadata']['id'];

                    $service->permissions->create($image->ruta_archivo, $permisos);
                }
                
                $image->save();

            } catch (Exception $error) {

                Error::create([
                    'funcion' => 'ImageController@store',
                    'mensaje' => $error->getMessage(),
                    'archivo' => $error->getFile(),
                    'linea' => $error->getLine()
                ]);
                
                return response()->json([
                    'result' => 'Error al procesar la imagen.'
                ], 400);
            }

            return response()->json([
                'result' => 'Imagen guardada.',
                'data' => $image
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
        $driveFiles = Gdrive::all('easyshop/images');

        if($image) {
            // Comprobamos que el archivo existe en Drive, y si es así lo eliminamos
            if (in_array("easyshop/images/$image->nombre_archivo", array_column(json_decode($driveFiles, true), 'path'))) {
                Gdrive::delete("easyshop/images/$image->nombre_archivo");
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
