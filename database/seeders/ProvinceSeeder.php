<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Province::create([
            'id' => 1,
            'nombre' => 'Almería'
        ]);

        Province::create([
            'id' => 2,
            'nombre' => 'Cádiz'
        ]);

        Province::create([
            'id' => 3,
            'nombre' => 'Córdoba'
        ]);

        Province::create([
            'id' => 4,
            'nombre' => 'Granada'
        ]);

        Province::create([
            'id' => 5,
            'nombre' => 'Huelva'
        ]);

        Province::create([
            'id' => 6,
            'nombre' => 'Jaén'
        ]);

        Province::create([
            'id' => 7,
            'nombre' => 'Málaga'
        ]);

        Province::create([
            'id' => 8,
            'nombre' => 'Sevilla'
        ]);
    }
}
