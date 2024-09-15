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
            'Id' => $this->id,
            'Nombre' => $this->nombre,
            'Alt' => $this->alt,
            'Descripcion' => $this->descripcion,
            'Leyenda' => $this->leyenda,
            'Archivo' => $this->ruta_archivo,
            'Fecha' => $this->created_at,
            'Tamano' => number_format(Storage::fileSize('public/' . $this->ruta_archivo) / 1024, 3), // number_format para establecer hasta 3 decimales
            'Dimensiones' => getimagesize(Storage::path('public/' . $this->ruta_archivo))[0] . ' por ' . getimagesize(Storage::path('public/' . $this->ruta_archivo))[1] . ' píxeles',
            'Tipo' => getimagesize(Storage::path('public/' . $this->ruta_archivo))['mime'],
            'Nombre_archivo' => basename(Storage::path('public/' . $this->ruta_archivo))
        ];
    }
}
