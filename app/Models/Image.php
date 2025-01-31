<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/* Imagen se relaciona tanto con categorias producto como con producto */
class Image extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'alt', 'descripcion', 'leyenda', 'ruta_archivo', 'nombre_archivo', 'imageable_id', 'imageable_type']; // imageable_id e imageable_type deberían modificarse desde laravel

    public function imageable(): MorphTo {
        return $this->morphTo(); // Tanto categorias de producto como productos como marcas pueden tener muchas imágenes
    }
}
