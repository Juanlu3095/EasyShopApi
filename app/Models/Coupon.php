<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'codigo', 'tipo', 'descuento', 'descripcion', 'estado_cupon', 'gasto_minimo', 'limite_uso', 'fecha_caducidad'];

    protected $casts = ['fecha_caducidad' => 'datetime'];

    protected $hidden = ['created_at', 'updated_at'];
}
