<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'id' => 1,
            'nombre' => 'administrador'
        ]);

        Role::create([
            'id' => 2,
            'nombre' => 'vendedor'
        ]);

        Role::create([
            'id' => 3,
            'nombre' => 'cliente'
        ]);
    }
}
