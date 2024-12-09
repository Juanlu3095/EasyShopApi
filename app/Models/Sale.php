<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'beneficios', 'ventas'];
    
    // Relación 1:M con products. 1 producto tiene muchas ventas, 1 venta pertenece a un producto
    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }
}
