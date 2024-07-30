<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Province;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class ProvinceController extends Controller
{
    public function getProvinces(): JsonResponse{
        $provincias = Province::all();

        return response()->json($provincias, 200);
    }
}
