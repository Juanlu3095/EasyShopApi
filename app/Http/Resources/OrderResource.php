<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'Cliente' => $this->nombre . ' ' . $this->apellidos,
            'Nombre' => $this->nombre,
            'Apellidos' => $this->apellidos,
            'Pais' => $this->pais,
            'Direccion' => $this->direccion,
            'Codigo_postal' => $this->codigo_postal,
            'Poblacion' => $this->poblacion,
            'Provincia' => $this->provincia,
            'Telefono' => $this->telefono,
            'Email' => $this->email,
            'Notas' => $this->notas,
            'Metodo_pago' => $this->paymentmethod->slug,
            'Subtotal' => $this->subtotal,
            'Estado' => $this->orderstatus->valor,
            'Estado_id' => $this->orderstatus_id,
            'Nombre_descuento' => $this->nombre_descuento,
            'Tipo_descuento' => $this->tipo_descuento,
            'Descuento' => $this->descuento,
            'Metodoenvio_id' => $this->shippingmethod_id,
            'Metodo_envio' => $this->shippingmethod->nombre,
            'Gastos_envio' => $this->gastos_envio,
            'Total' => $this->total . '€',
            'Cuenta_cliente' => $this->user_id,
            'Fecha' => $this->created_at->format('d-m-Y G:i'),
            'Productos' => $this->orderitems->map(function($orderitem) {
                return [
                    'Producto' => $orderitem->product->nombre,
                    'Subtotal' => $orderitem->subtotal,
                    'Cantidad' => $orderitem->cantidad,
                    'Total' => $orderitem->total
                ];
            })
        ];
    }
}
