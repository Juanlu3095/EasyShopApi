<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jobcategory;
use App\Http\Resources\JobcategoryResource;

class JobcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobcategories = Jobcategory::all();

        return response()->json($jobcategories, 200);
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
        $jobcategories = Jobcategory::create($request->all());

        return response()->json([
            'success' => true,
            'data' => new JobcategoryResource($jobcategories)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jobcategory = Jobcategory::find($id);
        return new JobcategoryResource(($jobcategory));
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
        $jobcategory = Jobcategory::find($id);
        $jobcategory->nombre = $request->nombre;
        $jobcategory->slug = $request->slug;
        $jobcategory->save();

        return response()->json([
            'success' => true,
            'data' => new JobcategoryResource($jobcategory)
        ], 200);
    }

    /**
     * Remove the specified resource from storage. Se eliminan tanto arrays como sólo un sólo id.
     */
    public function destroy(string $id)
    {
        Jobcategory::destroy(explode(",", $id));
        return response()->json([
            'success' => true
        ], 200);
    }
}
