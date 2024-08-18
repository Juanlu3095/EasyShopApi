<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mensajes = Message::orderBy('created_at', 'desc')->get();
        return response()->json(MessageResource::collection($mensajes), 200);
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
        $mensaje = Message::create($request->all());

        return response()->json([
            'success' => true,
            'data' => new MessageResource($mensaje)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mensaje = Message::find($id);
        return new MessageResource($mensaje);
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
        $mensaje = Message::find($id);
        $mensaje->update($request->all()); // Asegurarnos de que $fillable esté bien definido en el modelo
        $mensaje->save();

        return response()->json([
            'success' => true,
            'data' => new MessageResource($mensaje)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idArray)
    {
        $mensaje = Message::destroy(explode(",",$idArray));

        if( $mensaje ) {
            return response()->json([
                'success' => true,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
            ], 404);
        }
        
    }
}
