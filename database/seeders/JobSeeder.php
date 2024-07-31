<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Job::create([
            'puesto' => 'Programador/a web',
            'jobcategory_id' => 1,
            'province_id' => 7,
            'jornada' => 'Completa',
            'nivel_profesional' => 'Empleado',
            'modalidad' => 'Híbrido',
            'descripcion' => 'Se busca programador/a web para nuestra tienda en Málaga. Sus funciones serán las de mantener nuestra plataforma de e-commerce y resolver problemas derivados de la misma.',
            'requisitos' => 'El candidato/a debe tener conocimientos demostrables en Angular y Laravel, pues son las herramientas usadas en nuestra plataforma.',
            'beneficios' => 'Se ofrece salario acorde al convenio de informática. Horario flexible y teletrabajo. Ticket restaurante y seguro médico privado.'
        ]); */

        Job::create([
            'puesto' => 'Comercial de marketing',
            'jobcategory_id' => 3,
            'province_id' => 7,
            'jornada' => 'Completa',
            'nivel_profesional' => 'Empleado',
            'modalidad' => 'Presencial',
            'descripcion' => 'Se busca comercial para realizar contratos de nuestros servicios de márketing para la zona de Carretera de Cádiz. El candidato asesorará al cliente durante todo el proceso de venta.',
            'requisitos' => 'El candidato/a debe poseer don de gentes y tener experiencia de al menos 1 año en puesto relacionado. Orientado a resultados.',
            'beneficios' => 'Se ofrece salario fijo más variables por objeticos. Horario flexible.'
        ]);
    }
}
