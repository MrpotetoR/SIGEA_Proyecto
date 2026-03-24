<?php

namespace Database\Seeders;

use App\Models\Carrera;
use App\Models\Docente;
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

        // Usuario de prueba: director
        $directorUser = User::firstOrCreate(
            ['email' => 'director@sigea.edu.mx'],
            [
                'name' => 'Director Carrera',
                'password' => bcrypt('password'),
                'activo' => true,
            ]
        );
        $directorUser->assignRole('director_carrera');

        $directorDocente = Docente::firstOrCreate(
            ['user_id' => $directorUser->id],
            [
                'nombre'       => 'Director',
                'apellidos'    => 'De Carrera',
                'especialidad' => 'Administración Educativa',
                'horas_contrato' => null,
                'es_tutor'     => false,
            ]
        );

        $carrera = Carrera::first();
        if ($carrera) {
            $carrera->update(['id_director' => $directorDocente->id_docente]);
        }

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

        // Datos de prueba completos
        $this->call(DatosPruebaSeeder::class);
    }
}
