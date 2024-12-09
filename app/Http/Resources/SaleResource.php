<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'producto' => $this->product->nombre, // $this representa a sale
            'beneficios' => $this->beneficios,
            'ventas' => $this->ventas
        ];
    }
}
