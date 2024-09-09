<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }

    // Relación polimórfica de post de 1:Muchos con image
    public function images(): MorphMany {
        return $this->morphMany(Image::class, 'imageable');
    }
}
