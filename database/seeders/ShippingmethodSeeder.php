<?php

namespace Database\Seeders;

use App\Models\Shippingmethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingmethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shippingmethod::create([
            'nombre' => 'Recogida en tienda',
            'precio' => 0
        ]);

        Shippingmethod::create([
            'nombre' => 'Envío a domicilio',
            'precio' => 5.99
        ]);
    }
}
