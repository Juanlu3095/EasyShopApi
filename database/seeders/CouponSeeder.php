<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::create([
            'nombre' => 'Black Friday',
            'codigo' => 'BLACKFRIDAY',
            'tipo' => 'fijo',
            'descuento' => 10.00,
            'descripcion' => 'Descuento del Black Friday 2024',
            'estado_cupon' => 'publicado',
            'fecha_caducidad' => '2024-12-05',
            'gasto_minimo' => 30.00,
            'limite_uso' => 50
        ]);
    }
}
