<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductcategoryResource extends JsonResource
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
            'Imagen' => $this->images->map(function($image) { // Al ser una relación 1:Muchos debemos recorrer las images
                return [
                    'Nombre_imagen' => $image->nombre,
                    'Alt' => $image->alt,
                    'Descripcion' => $image->descripcion,
                    'Leyenda' => $image->leyenda,
                    'Ruta_archivo' => $image->ruta_archivo,
                ];
            }),
        ];
    }
}
