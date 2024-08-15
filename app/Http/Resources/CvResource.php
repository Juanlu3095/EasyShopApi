<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CvResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'ruta_cv' => $this->ruta_cv,
            'incorporacion' => $this->incorporacion,
            'pais' => $this->pais,
            'ciudad' => $this->ciudad,
            'fecha' => $this->created_at,
            'estado_candidatura' => $this->estado_candidatura
        ];
    }
}
