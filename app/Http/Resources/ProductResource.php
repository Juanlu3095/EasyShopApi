<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'Descripcion' => $this->descripcion,
            'Descripcion_corta' => $this->descripcion_corta,
            'Categoria_id' => $this->productcategory_id,
            'Categoria' => $this->productcategory->nombre,
            'Marca_id' => $this->brand_id,
            'Marca' => $this->brand->nombre,
            'Estado_producto' => $this->estado_producto,
            'Precio_euros' => $this->precio,
            'Precio_rebajado_euros' => $this->precio_rebajado,
            'SKU' => $this->sku,
            'ISBN_EAN' => $this->isbn_ean,
            'Inventario' => $this->inventario,
            'Ultima_modificacion' => $this->updated_at->format('d-m-Y G:i'),
            'Imagen' => $this->images->map(function($image) { // Al ser una relación 1:Muchos debemos recorrer las images
                return [
                    'Id' => $image->id,
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
