<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* User::create([
            'id' => 1, // Ponemos aquí un id porque hay que relacionarlo con la tabla de teléfonos
            'name' => 'Administrador',
            'email' => 'jcooldevelopment@gmail.com',
            'password' => Hash::make('123456789'), // Hash se utiliza para proteger la contraseña
            'role_id' => 1
        ]);

        User::create([
            'id' => 2, // Ponemos aquí un id porque hay que relacionarlo con la tabla de teléfonos
            'name' => 'Cliente',
            'email' => 'cliente@gmail.com',
            'password' => Hash::make('1234'), // Hash se utiliza para proteger la contraseña
            'role_id' => 3
        ]); */

        $user = new User;
        $user->name = 'Tienda';
        $user->email = "easyshop.notifications@gmail.com";
        $user->password = Hash::make('12345');
        $user->role_id = 1;
        $user->save();

        event(new Registered($user));
    }
}
