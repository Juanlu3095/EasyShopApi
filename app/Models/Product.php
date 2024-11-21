<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'descripcion_corta', 'productcategory_id', 'brand_id', 'estado_producto', 'precio', 'precio_rebajado', 'sku', 'isbn_ean', 'inventario'];

    // Relación polimórfica de post de 1:Muchos con image
    public function images(): MorphMany {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    // 1 producto pertenece a 1 sóla categoría de producto
    public function productcategory(): BelongsTo {
        return $this->belongsTo(Productcategory::class);
    }

    // 1 producto pertenece a 1 sóla marca
    public function brand(): BelongsTo {
        return $this->belongsTo(Brand::class);
    }

    // Relación 1:M con orderitems. 1 producto pertenece a varios orderitems
    public function orderitems(): HasMany {
        return $this->hasMany(Orderitem::class);
    }
}
