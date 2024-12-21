<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingmethodRequest;
use App\Models\Shippingmethod;
use App\Http\Resources\ShippingmethodResource;
use Illuminate\Http\Request;

class ShippingmethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shoppingmethods = Shippingmethod::all();

        return response()->json([
            'result' => 'Métodos de envío encontrados.',
            'data' => ShippingmethodResource::collection($shoppingmethods)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShippingmethodRequest $request)
    {
        $shippingmethod = new Shippingmethod;
        $shippingmethod->nombre = $request->nombre;
        $shippingmethod->precio = $request->precio;
        $shippingmethod->save();

        return response()->json([
            'result' => 'Método de envío creado.',
            'data' => new ShippingmethodResource($shippingmethod)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shippingmethod = Shippingmethod::find($id);

        if($shippingmethod) {
            return response()->json([
                'result' => 'Método de envío encontrado.',
                'data' => new ShippingmethodResource($shippingmethod)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado el método de envío.'
            ], 404);
        }
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShippingmethodRequest $request, string $id)
    {
        $shippingmethod = Shippingmethod::find($id);

        if($shippingmethod) {
            $shippingmethod->update([
                'nombre' => $request->nombre,
                'precio' => $request->precio
            ]);

            return response()->json([
                'result' => 'Método de envío actualizado',
                'data' => new ShippingmethodResource($shippingmethod)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado el método de envío.'
            ], 404);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idArray)
    {
        $ids = explode(",",$idArray); // Obtenemos las id del Array
        $shippingmethods = Shippingmethod::find($ids); // Buscamos los productos en la base de datos con las id

        if($shippingmethods) {
            $shippingmethods->map(function($shippingmethod) {
                $shippingmethod->delete();
            });

            return response()->json([
                'result' => 'Método/s de envío eliminado/s.'
            ], 200);

        } else {

            return response()->json([
                'result' => 'No se ha podido procesar la petición.'
            ], 404);
        }
    }
}
