<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => 1, // Ponemos aquí un id porque hay que relacionarlo con la tabla de teléfonos
            'name' => 'Administrador',
            'email' => 'jcooldevelopment@gmail.com',
            'password' => Hash::make('123456789'), // Hash se utiliza para proteger la contraseña
            'role_id' => 1
        ]);
    }
}
