<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['usuario', 'valoracion', 'comentario'];

    // Vinculación con product para la relación 1:Muchos
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
