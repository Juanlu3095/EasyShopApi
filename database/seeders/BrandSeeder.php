<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::create([
            'nombre' => 'Befree'
        ]);

        Brand::create([
            'nombre' => 'Happy shopping'
        ]);

        Brand::create([
            'nombre' => 'No escape'
        ]);

        Brand::create([
            'nombre' => 'Social addict'
        ]);

        Brand::create([
            'nombre' => 'Solo play'
        ]);
        
    }
}
