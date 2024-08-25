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
            'Id' => $this->id,
            'Nombre' => $this->nombre,
            'Apellidos' => $this->apellidos,
            'Teléfono' => $this->telefono,
            'Email' => $this->email,
            'Ruta_cv' => $this->ruta_cv,
            'Incorporación' => $this->incorporacion,
            'País' => $this->pais,
            'Ciudad' => $this->ciudad,
            'Fecha' => $this->created_at->format('d-m-Y'),
            'Candidatura' => $this->estado_candidatura
        ];
    }
}
