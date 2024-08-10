<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jobcategory extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'slug'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    // Relación 1:Muchos con Jobs (1 jobcategory puede pertenecer a varias ofertas de empleo)
    public function jobs(): HasMany{
        return $this->hasMany(Job::class);
    }
}
