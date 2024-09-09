<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ImageResource extends JsonResource
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
            'alt' => $this->alt,
            'descripcion' => $this->descripcion,
            'leyenda' => $this->leyenda,
            'archivo' => $this->ruta_archivo,
            'fecha' => $this->created_at,
            'tamano' => number_format(Storage::fileSize('public/' . $this->ruta_archivo) / 1024, 3), // number_format para establecer hasta 3 decimales
            'dimensiones' => getimagesize(Storage::path('public/' . $this->ruta_archivo))[0] . ' por ' . getimagesize(Storage::path('public/' . $this->ruta_archivo))[1] . ' píxeles',
            'tipo' => getimagesize(Storage::path('public/' . $this->ruta_archivo))['mime'],
            'nombre_archivo' => basename(Storage::path('public/' . $this->ruta_archivo))
        ];
    }
}
