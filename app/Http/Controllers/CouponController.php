<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = Coupon::all();
        return CouponResource::collection($coupons);
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
    public function store(CouponRequest $request): JsonResponse
    {
        $coupon = new Coupon;
        $coupon->nombre = $request->nombre;
        $coupon->codigo = $request->codigo;
        $coupon->tipo = $request->tipo;
        $coupon->descuento = $request->descuento;
        $coupon->descripcion = $request->descripcion;
        $coupon->estado_cupon = $request->estado_cupon;
        $coupon->fecha_caducidad = $request->fecha_caducidad;
        $coupon->gasto_minimo = $request->gasto_minimo;
        $coupon->limite_uso = $request->limite_uso;
        $coupon->save();
        
        return response()->json([
            'result' => 'Cupón creado.',
            'data' => new CouponResource($coupon)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $coupon = Coupon::find($id);

        if($coupon) {
            return CouponResource::collection(collect([$coupon])); // Devuelve un único elemento como array
            //return new CouponResource($coupon); // Devuelve un único elemento

        } else {
            return response()->json([
                'result' => 'No existe el cupón solicitado.'
            ], 404);
        }
    }

    /**
     * Display the specified resource by code (codigo).
     */
    public function showByCode(Request $request)
    {
        $coupon = Coupon::where('codigo', $request->codigo)->where('estado_cupon', 'Publicado')->where('limite_uso', '>', 0)->first();

        if($coupon) {
            return response()->json([
                'result' => 'Cupón encontrado.',
                'data' => new CouponResource($coupon)
            ], 200);

        } else {
            return response()->json([
                'result' => 'Cupón no encontrado.'
            ], 404);
        }
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
    public function update(CouponRequest $request, string $id): JsonResponse
    {
        $coupon = Coupon::find($id);

        if($coupon) {

            $coupon->update($request->all());

            return response()->json([
                'result' => 'Cupón modificado.',
                'data' => new CouponResource($coupon)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado el registro.',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idArray): JsonResponse
    {
        $ids = explode(",",$idArray); // Obtenemos las id del Array
        $coupons = Coupon::find($ids); // Buscamos los productos en la base de datos con las id

        if($coupons) {
            $coupons->map(function($coupon) {
                $coupon->delete();
            });

            return response()->json([
                'result' => 'Registro/s eliminados.'
            ],200 );

        } else {

            return response()->json([
                'result' => 'No se ha podido procesar la petición.'
            ], 404);
        }
    }
}
