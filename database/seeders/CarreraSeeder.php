<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrera;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        $carreras = [
            ['nombre_carrera' => 'Desarrollo de Software Multiplataforma', 'clave_carrera' => 'DSM-2026'],
            ['nombre_carrera' => 'Gestión Empresarial', 'clave_carrera' => 'GE-2026'],
            ['nombre_carrera' => 'Mantenimiento de Equipo de Cómputo', 'clave_carrera' => 'MEC-2026'],
            ['nombre_carrera' => 'Administración', 'clave_carrera' => 'ADM-2026'],
        ];

        foreach ($carreras as $carrera) {
            Carrera::firstOrCreate(['clave_carrera' => $carrera['clave_carrera']], $carrera);
        }
    }
}
