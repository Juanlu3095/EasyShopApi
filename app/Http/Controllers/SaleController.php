<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Displays sales by benefits
     */
    public static function indexByBenefits()
    {
        $sales = Sale::orderBy('beneficios', 'desc')->get();

        if($sales) {
            return response()->json([
                'result' => 'Ventas encontradas.',
                'data' => SaleResource::collection($sales)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No hay ventas.'
            ], 404);
        }

    }

    /**
     * Displays sales by quantity
     */
    public static function indexByQuantity()
    {
        $sales = Sale::orderBy('ventas', 'desc')->get();

        if($sales) {
            return response()->json([
                'result' => 'Ventas encontradas.',
                'data' => SaleResource::collection($sales)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No hay ventas.'
            ], 404);
        }
    }
}
