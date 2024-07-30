<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cv extends Model
{
    use HasFactory;

    // Relación 1:Muchos con Job (1 Oferta de empleo tiene varios CVs)
    public function jobs(): BelongsTo{
        return $this->belongsTo(Job::class);
    }
}
