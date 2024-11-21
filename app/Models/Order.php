<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'user_id' // user_id se rellena desde la request en laravel con la info de las cabeceras
    ];

    // Relación 1:M con orderitems. 1 order tiene varios orderitems. El modelo orderitems debe tener la id de order
    public function orderitems(): HasMany {
        return $this->hasMany(Orderitem::class);
    }

    // Relación 1:M con paymentmethods. El modelo belongsTo debe tener la clave foránea
    public function paymentmethod(): BelongsTo {
        return $this->belongsTo(Paymentmethod::class);
    }

    // Relación 1:M con orderstatus. Incluir clave foránea
    public function orderstatus(): BelongsTo {
        return $this->belongsTo(Orderstatus::class);
    }
}
