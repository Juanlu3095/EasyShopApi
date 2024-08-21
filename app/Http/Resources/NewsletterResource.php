<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsletterResource extends JsonResource
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
            'Email' => $this->email,
            'Fecha' => $this->created_at->format('d-m-Y')
        ];
        // Ponemos los nombres de los campos con la primera letra mayúscula para luego mostrarlos correctamente en la tabla reutilizable
        // Aunque aquí pongamos los campos con mayúsculas, en la request los campos deben ir como en el modelo, en este caso en minúsculas
    }
}
