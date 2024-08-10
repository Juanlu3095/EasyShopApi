<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Http\JsonResponse;

class JobController extends Controller
{
    /**
     * Display a listing of the resource in desc order by id
     */
    public function index()
    {
        $jobs = Job::orderBy('id', 'desc')->get();

        return response()->json(JobResource::collection($jobs), 200);
        
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
        $job = Job::create($request->all()); // Usar fillable en el modelo Job

        return response()->json([
            'success' => true,
            'data' => new JobResource($job)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = Job::find($id);
        return new JobResource($job);
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
    public function update(Request $request, $id): JsonResponse
    {
        $job = Job::find($id);
        $job->puesto = $request->puesto;
        $job->jobcategory_id = $request->jobcategory_id;
        $job->province_id = $request->province_id;
        $job->jornada = $request->jornada;
        $job->nivel_profesional = $request->nivel_profesional;
        $job->modalidad = $request->modalidad;
        $job->descripcion = $request->descripcion;
        $job->requisitos = $request->requisitos;
        $job->beneficios = $request->beneficios;
        $job->salario = $request->salario;
        $job->save();

        return response()->json([
            'success' => true,
            'data' => new JobResource($job)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Job::find($id)->delete();
        return response()->json([
            'success' => true
        ], 200);
    }

    /**
     * Remove the specified array of resources from storage. Se podría usar directamnete destroy() para eliminar tanto la selección como una única id
     */
    public function destroySelected(string $idArray)
    {
        Job::destroy(explode(",",$idArray));
        return response()->json([
            'success' => true
        ], 200);
    }
}
