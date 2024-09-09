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
            'nombre' => 'Ordenadores'
        ]);

        Productcategory::create([
            'nombre' => 'Smartphones'
        ]);

        Productcategory::create([
            'nombre' => 'Tabletas'
        ]);

        Productcategory::create([
            'nombre' => 'Relojes'
        ]);

        Productcategory::create([
            'nombre' => 'Televisiones'
        ]);

        Productcategory::create([
            'nombre' => 'Audio'
        ]);
    }
}
