<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesPermissionsSeeder::class,
            CicloEscolarSeeder::class,
            CarreraSeeder::class,
            EncuestaPreguntaSeeder::class,
        ]);

        // Usuario de prueba para Servicios Escolares
        $admin = User::firstOrCreate(
            ['email' => 'servicios@sigea.edu.mx'],
            [
                'name' => 'Servicios Escolares',
                'password' => bcrypt('password'),
                'activo' => true,
            ]
        );
        $admin->assignRole('servicios_escolares');

        // Usuario de prueba: docente
        $docente = User::firstOrCreate(
            ['email' => 'docente@sigea.edu.mx'],
            [
                'name' => 'Docente Prueba',
                'password' => bcrypt('password'),
                'activo' => true,
            ]
        );
        $docente->assignRole('docente');

        // Usuario de prueba: alumno
        $alumno = User::firstOrCreate(
            ['email' => 'alumno@sigea.edu.mx'],
            [
                'name' => 'Alumno Prueba',
                'password' => bcrypt('password'),
                'activo' => true,
            ]
        );
        $alumno->assignRole('alumno');
    }
}
