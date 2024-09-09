<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Productcategory extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    // Relación polimórfica de post de 1:Muchos con image.
    public function images(): MorphMany {
        return $this->morphMany(Image::class, 'imageable'); // Debemos indicar 'imageable' porque el nombre de la función 'images' no respeta la convención
    }

    // 1 categoría de producto pertenece a varios productos
    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }
}
