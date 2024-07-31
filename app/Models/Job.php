<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    // Relación 1:Muchos con Job (1 Oferta de empleo tiene varios CVs)
    public function cvs(): HasMany{
        return $this->hasMany(Cv::class);
    }

    // Relación 1:Muchos con JobCategory (1 jobcategory puede pertenecer a varias ofertas de empleo)
    public function jobcategory(): BelongsTo{
        return $this->belongsTo(Jobcategory::class);
    }

    // Relación 1:Muchos con Provinces (1 provincia puede pertenecer a varias ofertas de empleo)
    public function province(): BelongsTo{
        return $this->belongsTo(Province::class);
    }
}
