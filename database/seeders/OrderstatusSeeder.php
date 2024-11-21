<?php

namespace Database\Seeders;

use App\Models\Orderstatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderstatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Orderstatus::create([
            'id' => 1,
            'valor' => 'Pendiente de pago'
        ]);

        Orderstatus::create([
            'id' => 2,
            'valor' => 'Procesando'
        ]);

        Orderstatus::create([
            'id' => 3,
            'valor' => 'En espera'
        ]);

        Orderstatus::create([
            'id' => 4,
            'valor' => 'Completado'
        ]);

        Orderstatus::create([
            'id' => 5,
            'valor' => 'Cancelado'
        ]);

        Orderstatus::create([
            'id' => 6,
            'valor' => 'Reembolsado'
        ]);

        Orderstatus::create([
            'id' => 7,
            'valor' => 'Fallido'
        ]);

        Orderstatus::create([
            'id' => 8,
            'valor' => 'Borrador'
        ]);
    }
}
