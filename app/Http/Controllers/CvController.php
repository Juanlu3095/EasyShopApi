<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CvResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Cv;

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
     * Display a listing of CVs based on specific Job.
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
    public function store(Request $request): JsonResponse
    {
        $cv = Cv::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $cv
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}