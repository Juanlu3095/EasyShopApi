<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Orderitem extends Model
{
    use HasFactory;

    protected $guarded = [
        'id', 'created_at', 'updated_at'
    ];

    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }

    // Relación 1:M con orderitems. 1 producto pertenece a varios orderitems
    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }
}
