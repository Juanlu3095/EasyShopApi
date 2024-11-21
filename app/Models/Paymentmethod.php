<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paymentmethod extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'slug'];

    protected $hidden = [
        'created_at', 'updated_at', 'id'
    ];

    // Relación 1:M con Order
    public function orders(): HasMany {
        return $this->hasMany(Order::class);
    }
}
