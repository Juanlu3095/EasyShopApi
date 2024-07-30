<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    // Relación 1:Muchos con User (1 rol pertenece a varios usuarios)
    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}
