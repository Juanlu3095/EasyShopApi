<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shippingmethod extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'precio'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    // Relación 1:M con Order.
    public function orders(): HasMany {
        return $this->hasMany(Order::class);
    }
}
