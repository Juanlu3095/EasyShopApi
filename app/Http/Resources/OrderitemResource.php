<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderitemResource extends JsonResource
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
            'Pedido' => $this->order_id,
            'Producto' => $this->product->nombre,
            'Producto_id' => $this->product_id,
            'Cantidad' => $this->cantidad,
            'Subtotal' => $this->subtotal,
            'Total' => $this->total
        ];
    }
}
