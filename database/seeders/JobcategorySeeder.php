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
            'nombre' => 'Informática y telecomunicaciones'
        ]);

        Jobcategory::create([
            'id' => 2,
            'nombre' => 'Marketing y publicidad'
        ]);

        Jobcategory::create([
            'id' => 3,
            'nombre' => 'Comercio y ventas'
        ]);
    }
}
