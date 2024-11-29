<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderstatusResource;
use App\Models\Orderstatus;
use Illuminate\Http\Request;

class OrderstatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderstatuses = Orderstatus::all();
        return OrderstatusResource::collection($orderstatuses);
    }
}
