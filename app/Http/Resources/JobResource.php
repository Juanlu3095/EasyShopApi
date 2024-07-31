<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\JobcategoryResource;
use App\Http\Resources\ProvinceResource;

class JobResource extends JsonResource
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
            'puesto' =>  $this->puesto, 
            'jobcategory' => $this->jobcategory->nombre,
            //'jobcategory' => new JobcategoryResource($this->jobcategory), // new Jobcategoryresource para 1:M, si fuera M:M usamos collection. Con esto accedemos a todos los datos
            'province' => $this->province->nombre,
            'jornada' => $this->jornada,
            'nivel_profesional' => $this->nivel_profesional,
            'modalidad' => $this->modalidad,
            'descripcion' => $this->descripcion,
            'requisitos' => $this->requisitos,
            'beneficios' => $this->beneficios,
            'fecha' => $this->created_at->format('d-m-Y') // Damos formato a la fecha
        ];
    }
}
