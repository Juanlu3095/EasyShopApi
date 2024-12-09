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
        $sales = Sale::orderBy('beneficios', 'desc')->limit(10)->get();

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
        $sales = Sale::orderBy('ventas', 'desc')->limit(10)->get();

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

    // Nota: esta aproximación sólo permite mostrar los datos tal cual, sin filtro por unidad de tiempo, ya que para ello habría que crear cada venta por separado y luego
    // juntarlas en el front-end.
}
