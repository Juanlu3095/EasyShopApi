<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentmethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nombre' => $this->nombre,
            'activo' => $this->activo,
            'slug' => $this->slug,
            'descripcion' => $this->descripcion,
            'descripcion_cliente' => $this->descripcion_cliente,
            'configuracion' => $this->configuracion
        ];
    }
}
