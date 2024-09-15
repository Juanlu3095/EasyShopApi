<?php

namespace Database\Seeders;

use App\Models\Productcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Productcategory::create([
            'nombre' => 'Ordenadores',
            'slug' => 'ordenadores'
        ]);

        Productcategory::create([
            'nombre' => 'Smartphones',
            'slug' => 'smartphones'
        ]);

        Productcategory::create([
            'nombre' => 'Tabletas',
            'slug' => 'tabletas'
        ]);

        Productcategory::create([
            'nombre' => 'Relojes',
            'slug' => 'relojes'
        ]);

        Productcategory::create([
            'nombre' => 'Televisiones',
            'slug' => 'televisiones'
        ]);

        Productcategory::create([
            'nombre' => 'Audio',
            'slug' => 'audio'
        ]);
    }
}
