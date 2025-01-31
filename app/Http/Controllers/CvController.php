<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CvRequest;
use App\Http\Resources\CvResource;
use App\Mail\jobapplication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Cv;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Yaza\LaravelGoogleDriveStorage\Gdrive;

class CvController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cvs = Cv::orderBy('created_at', 'desc')->get();
        return response()->json(CvResource::collection($cvs), 200);
    }

    /**
     * Display a listing of CVs based on specific Job id.
     */
    public function indexByJob(int $idJob)
    {
        $cvs = Cv::where('job_id', $idJob)->orderBy('created_at', 'desc')->get();
        return response()->json(CvResource::collection($cvs), 200);
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
    public function store(CvRequest $request): JsonResponse
    {
        $cv = new Cv;
        $cv->nombre = $request->nombre;
        $cv->apellidos = $request->apellidos;
        $cv->email = $request->email;
        $cv->telefono = $request->telefono;
        $cv->pais = $request->pais;
        $cv->ciudad = $request->ciudad;
        $cv->incorporacion = $request->incorporacion;
        $cv->job_id = $request->job_id;
        $cv->estado_candidatura = $request->estado_candidatura;
        $cv->politica = 1;
        
        // Manejo del archivo
        if ($request->hasFile('ruta_cv')) {
            
            $file = $request->file('ruta_cv');
            $filename = "cv_job" . $cv->job_id . '_' . time() . "." . $file->guessExtension();

            Gdrive::put("easyshop/cv/$filename", $file); // guardamos el archivo en Drive

            $allfiles = Gdrive::all("easyshop/cv/"); // Obtenemos todos los archivos de la carpeta indicada
            // array_column devuelve sólo los valores de la columna 'path'
            // array_search nos devuelve la fila (index) que corresponde con la url del primer parámetro. El segundo parámetro es el array, usamos json_decode porque Drive nos
            // devuelve un json
            $updatedfileId = array_search("easyshop/cv/$filename", array_column(json_decode($allfiles, true), 'path'));
            
            // Guardamos la URL o ID del archivo y su nombre
            $cv->nombre_archivo = $filename;
            $cv->ruta_cv = $allfiles[$updatedfileId]['extra_metadata']['id']; // La ruta absoluta sería: https://drive.google.com/file/d/$cv->ruta_cv
            $cv->save();
            
            // Envío de email de confirmación al candidato
            $job = $cv->job->puesto; // relación 1:Muchos entre cv y job, 1 job muchos cvs

            $datos = Array(
                'nombre' => $cv->nombre,
                'job' => $job
            );

            Mail::to($cv->email)->send(new jobapplication($datos));

        } else {
            return response()->json(['error' => 'No se proporcionó un archivo PDF válido.'], 400);
        }
        
        $cv->save();

        return response()->json([
            'success' => true,
            'data' => $allfiles[$updatedfileId]['extra_metadata']['id']
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cv = Cv::find($id);
        return response()->json([
            'success' => true,
            'data' => new CvResource($cv)
        ]);
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
        $cv = Cv::find($id);
        $cv->update($request->all());
        $cv->save();

        return response()->json([
            'success' => true,
            'data' => new CvResource($cv)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idArray)
    {
        $cv = Cv::find(explode(",",$idArray));

        if( $cv ) {
            
            foreach ($cv as $row) {
                if($row->nombre_archivo) {
                    Gdrive::delete("easyshop/cv/$row->nombre_archivo");
                }
            }

            Cv::destroy(explode(",",$idArray)); // Elimina las candidaturas seleccionadas para el empleo concreto */
 
            return response()->json([
                'success' => true,
                'data' => $cv
            ], 200);

        } else {

            return response()->json([
                'success' => false,
                'data' => 'La candidatura no existe.'
            ], 404);
        }
    }
}
