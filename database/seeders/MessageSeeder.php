<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Message::create([
            'nombre' => 'Pepe',
            'apellidos' => 'Tavares',
            'email' => 'pepe45@gmail.com',
            'asunto' => 'Pregunta sobre producto con referencia #1458',
            'mensaje' => 'Me gustaría saber si todavía os quedan existencias. Gracias.'
        ]);
    }
}
