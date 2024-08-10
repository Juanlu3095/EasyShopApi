<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jobcategory;

class JobcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jobcategory::create([
            'id' => 1,
            'nombre' => 'Informática y telecomunicaciones',
            'slug' => 'informatica-y-telecomunicaciones'
        ]);

        Jobcategory::create([
            'id' => 2,
            'nombre' => 'Marketing y publicidad',
            'slug' => 'marketing-y-publicidad'
        ]);

        Jobcategory::create([
            'id' => 3,
            'nombre' => 'Comercio y ventas',
            'slug' => 'comercio-y-ventas'
        ]);

        Jobcategory::create([
            'id' => 4,
            'nombre' => 'Redes sociales',
            'slug' => 'redes-sociales'
        ]);

        Jobcategory::create([
            'id' => 5,
            'nombre' => 'Artes gráficas',
            'slug' => 'artes-graficas'
        ]);

        Jobcategory::create([
            'id' => 6,
            'nombre' => 'Calidad y medio ambiente',
            'slug' => 'calidad-y-medio-ambiente'
        ]);

        Jobcategory::create([
            'id' => 7,
            'nombre' => 'Hostelería y turismo',
            'slug' => 'hosteleria-y-turismo'
        ]);
    }
}
