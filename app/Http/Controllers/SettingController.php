<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::all();

        if($settings) {
            return response()->json([
                'result' => 'Datos encontrados.',
                'data' => SettingResource::collection($settings)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado ningún dato.'
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $setting = Setting::find($id);

        if($setting) {
            return response()->json([
                'result' => 'Datos encontrados.',
                'data' => new SettingResource($setting)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado ningún dato.',
            ], 404);
        }
    }

    /**
     * Display the admin email.
     */
    public function showbyname(SettingRequest $request)
    {
        $ajuste = Setting::where('configuracion', $request->ajuste)->first();

        if($ajuste) {
            return response()->json([
                'result' => 'Ajuste encontrado.',
                'data' => new SettingResource($ajuste)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado ningún dato.',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingRequest $request, string $id)
    {
        $setting = Setting::find($id);

        if($setting) {
            $setting->update([
                'valor' => $request->ajuste
            ]);

            return response()->json([
                'result' => 'Configuración actualizada.',
                'data' => new SettingResource($setting)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado ningún dato.'
            ], 404);
        }
        
    }

}
