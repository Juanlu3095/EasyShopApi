<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Id' => $this->id,
            'Nombre' => $this->nombre,
            'Codigo' => $this->codigo,
            'Tipo' => $this->tipo,
            'Descuento' => $this->descuento,
            'Descripcion' => $this->descripcion,
            'Estado' => $this->estado_cupon,
            'Caducidad' => $this->fecha_caducidad,
            'Gasto_minimo' => $this->gasto_minimo,
            'Limite_uso' => $this->limite_uso,
            'Fecha_creacion' => $this->created_at->format('d-m-Y G:i')
        ];
    }
}
