<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentmethodRequest;
use App\Http\Requests\PaymentmethodsoloactivoRequest;
use App\Http\Resources\PaymentmethodclientResource;
use App\Http\Resources\PaymentmethodResource;
use App\Models\Paymentmethod;
use Illuminate\Http\Request;

class PaymentmethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentmethods = Paymentmethod::all();
        return PaymentmethodResource::collection($paymentmethods);
    }

    /**
     * Display a listing of the resource to frontend.
     */
    public function indexClient()
    {
        $paymentmethods = Paymentmethod::where('activo', 1)->get();
        return PaymentmethodclientResource::collection($paymentmethods);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $paymentmethod = Paymentmethod::where('slug', $slug)->first();

        if($paymentmethod) {

            return new PaymentmethodResource($paymentmethod);

        } else {

            return response()->json([
                'result' => 'Método de pago no encontrado.'
            ], 404);
        }
    }

    /**
     * Activates or deactivates paymentmethod.
     */
    public function switchActivo(PaymentmethodsoloactivoRequest $request, string $slug)
    {
        $paymentmethod = Paymentmethod::where('slug', $slug)->first();

        if($paymentmethod) {

            $paymentmethod->update([
                'activo' => $request->activo,
            ]);

            return response()->json([
                'result' => 'Método de pago actualizado.',
            ], 200);

        } else {

            return response()->json([
                'result' => 'Método de pago no encontrado.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentmethodRequest $request, string $slug)
    {
        $paymentmethod = Paymentmethod::where('slug', $slug)->first();

        if($paymentmethod) {

            $paymentmethod->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'descripcion_cliente' => $request->descripcion_cliente,
                'activo' => $request->activo,
                'configuracion' => $request->configuracion
            ]);

            return response()->json([
                'result' => 'Método de pago actualizado.'
            ], 200);

        } else {

            return response()->json([
                'result' => 'Método de pago no encontrado.'
            ], 404);
        }
    }
}
