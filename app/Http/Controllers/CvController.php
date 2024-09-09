<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CvRequest;
use App\Http\Resources\CvResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Cv;
use Illuminate\Support\Facades\Storage;

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
        $path = $file->storeAs('public/cv', $filename); // Guardar archivo en storage
        $rutaReal = 'cv/' . $filename; // La ruta del archivo que se guarda en la base de datos y desde la que se puede acceder al archivo desde la web
        //$rutaReal = Storage::url('cv/' . $filename); // Otra forma de guardar la ruta del archivo, más recomendable porque si se cambia el disco, las modificaciones son más simples.
        $cv->ruta_cv = $rutaReal; // Guardamos ruta relativa en ruta_cv en la base de datos

        } else {
            return response()->json(['error' => 'No se proporcionó un archivo PDF válido.'], 400);
        }
        
        $cv->save();
        
        return response()->json([
            'success' => true,
            'data' => $request
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
                if(Storage::disk('public')->exists($row->ruta_cv)) {
                    Storage::disk('public')->delete($row->ruta_cv); // Si existe el archivo del cv, lo eliminará. Debemos pasar la ruta a delete().  
                } 
                Cv::destroy(explode(",",$idArray)); // Elimina las candidaturas seleccionadas para el empleo concreto
            }
 
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
