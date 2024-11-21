<?php

namespace Database\Seeders;

use App\Models\Paymentmethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentmethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Paymentmethod::create([
            'id' => 1,
            'nombre' => 'Transferencia bancaria directa',
            'slug' => 'transferencia',
            'descripcion' => 'Permite al cliente realizar una transferencia bancaria una vez realizado el pedido. El administrador deberá confirmar el pago manualmente.',
            'descripcion_cliente' => 'Para confirmar el pedido, por favor realice el pago a la cuenta bancaria indicada a continuación.',
            'activo' => 2, // 1 = activo, 2 = no activo
            'configuracion'=> json_encode([
                'nombre' => 'easyshop_directo',
                'numero' => '123456789',
                'nombre_banco' => 'Banco de pruebas',
                'clasificacion' => '00-11-22',
                'iban' => 'ES9121000418450200051332',
                'bic_swift' => 'CAIXESBBXXX'
            ])
        ]);

        Paymentmethod::create([
            'id' => 2,
            'nombre' => 'Pago con tarjeta',
            'slug' => 'tarjeta',
            'descripcion' => 'Permite al cliente pagar directamente con tarjeta de crédito o débito.',
            'descripcion_cliente' => 'Le permite pagar con tarjeta de crédito o débito.',
            'activo' => 2, // 1 = activo, 2 = no activo
            'configuracion' => '{}'
        ]);
    }
}
