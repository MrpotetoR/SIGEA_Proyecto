<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, Carrera, CicloEscolar, Docente, Alumno, Tutor, Materia, Grupo, Horario, Inscripcion};

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Ciclo Escolar ─────────────────────────
        $ciclo = CicloEscolar::create([
            'nombre'       => 'Enero-Abril 2026',
            'fecha_inicio' => '2026-01-13',
            'fecha_fin'    => '2026-04-24',
            'activo'       => true,
        ]);

        // ─── Carreras ──────────────────────────────
        $tiid = Carrera::create([
            'nombre_carrera' => 'Tecnologías de la Información e Innovación Digital',
            'clave_carrera'  => 'TIID',
        ]);

        $mecatronica = Carrera::create([
            'nombre_carrera' => 'Mecatrónica',
            'clave_carrera'  => 'MEC',
        ]);

        // ─── Usuario Admin (Servicios Escolares) ───
        $adminUser = User::create([
            'name'     => 'Admin SIGEA',
            'email'    => 'admin@sigea.uttecam.edu.mx',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole('servicios_escolares');

        // ─── Docentes ──────────────────────────────
        $docentes = [];
        $datosDocentes = [
            ['nombre' => 'Carlos',   'apellidos' => 'López Martínez',    'especialidad' => 'Desarrollo Web'],
            ['nombre' => 'María',    'apellidos' => 'García Hernández',   'especialidad' => 'Bases de Datos'],
            ['nombre' => 'Roberto',  'apellidos' => 'Sánchez Pérez',     'especialidad' => 'Redes'],
            ['nombre' => 'Ana',      'apellidos' => 'Morales Gutiérrez', 'especialidad' => 'Matemáticas'],
        ];

        foreach ($datosDocentes as $i => $d) {
            $user = User::create([
                'name'     => "{$d['nombre']} {$d['apellidos']}",
                'email'    => strtolower($d['nombre']) . ($i + 1) . '@sigea.uttecam.edu.mx',
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('docente');

            $docentes[] = Docente::create([
                'user_id'       => $user->id,
                'nombre'        => $d['nombre'],
                'apellidos'     => $d['apellidos'],
                'especialidad'  => $d['especialidad'],
                'horas_contrato' => 20,
                'es_titular'    => $i === 0,
            ]);
        }

        // Asignar director de carrera
        $directorUser = $docentes[0]->user;
        $directorUser->assignRole('director_carrera');
        $tiid->update(['director_id' => $docentes[0]->id]);

        // ─── Materias TIID (cuatrimestre 1-3) ──────
        $materiasDatos = [
            ['nombre_materia' => 'Programación Web I',       'cuatrimestre' => 1, 'horas_semana' => 6, 'creditos' => 8],
            ['nombre_materia' => 'Base de Datos I',          'cuatrimestre' => 1, 'horas_semana' => 5, 'creditos' => 7],
            ['nombre_materia' => 'Matemáticas Discretas',    'cuatrimestre' => 1, 'horas_semana' => 4, 'creditos' => 6],
            ['nombre_materia' => 'Inglés I',                 'cuatrimestre' => 1, 'horas_semana' => 3, 'creditos' => 4],
            ['nombre_materia' => 'Programación Web II',      'cuatrimestre' => 2, 'horas_semana' => 6, 'creditos' => 8],
            ['nombre_materia' => 'Base de Datos II',         'cuatrimestre' => 2, 'horas_semana' => 5, 'creditos' => 7],
            ['nombre_materia' => 'Integradora I',            'cuatrimestre' => 2, 'horas_semana' => 5, 'creditos' => 7],
            ['nombre_materia' => 'Servicios Web (SOA)',      'cuatrimestre' => 3, 'horas_semana' => 6, 'creditos' => 8],
            ['nombre_materia' => 'Integradora II',           'cuatrimestre' => 3, 'horas_semana' => 6, 'creditos' => 8],
            ['nombre_materia' => 'Liderazgo de Equipos',     'cuatrimestre' => 3, 'horas_semana' => 3, 'creditos' => 4],
        ];

        $materias = [];
        foreach ($materiasDatos as $m) {
            $materias[] = Materia::create(array_merge($m, ['carrera_id' => $tiid->id]));
        }

        // ─── Grupos ────────────────────────────────
        $grupo1A = Grupo::create([
            'carrera_id'       => $tiid->id,
            'ciclo_id'         => $ciclo->id,
            'cuatrimestre'     => 1,
            'clave_grupo'      => '1A-TIID',
            'tutor_docente_id' => $docentes[0]->id,
        ]);

        $grupo3A = Grupo::create([
            'carrera_id'       => $tiid->id,
            'ciclo_id'         => $ciclo->id,
            'cuatrimestre'     => 3,
            'clave_grupo'      => '3A-TIID',
            'tutor_docente_id' => $docentes[1]->id,
        ]);

        // ─── Horarios de ejemplo ───────────────────
        // Grupo 1A — Prog Web I con docente 0
        Horario::create([
            'docente_id' => $docentes[0]->id, 'grupo_id' => $grupo1A->id,
            'materia_id' => $materias[0]->id, 'dia_semana' => 'lunes',
            'hora_inicio' => '07:00', 'hora_fin' => '09:00',
        ]);
        Horario::create([
            'docente_id' => $docentes[0]->id, 'grupo_id' => $grupo1A->id,
            'materia_id' => $materias[0]->id, 'dia_semana' => 'miercoles',
            'hora_inicio' => '07:00', 'hora_fin' => '09:00',
        ]);
        // BD I con docente 1
        Horario::create([
            'docente_id' => $docentes[1]->id, 'grupo_id' => $grupo1A->id,
            'materia_id' => $materias[1]->id, 'dia_semana' => 'martes',
            'hora_inicio' => '09:00', 'hora_fin' => '11:00',
        ]);
        // Grupo 3A — SOA con docente 0
        Horario::create([
            'docente_id' => $docentes[0]->id, 'grupo_id' => $grupo3A->id,
            'materia_id' => $materias[7]->id, 'dia_semana' => 'lunes',
            'hora_inicio' => '11:00', 'hora_fin' => '13:00',
        ]);

        // ─── Tutor (padre/madre) ───────────────────
        $tutor1 = Tutor::create([
            'nombre'   => 'Roberto Gutiérrez',
            'telefono' => '2221234567',
            'email'    => 'roberto.gutierrez@gmail.com',
        ]);

        // ─── Alumnos ───────────────────────────────
        $alumnosDatos = [
            ['nombre' => 'Erick Daniel',     'apellidos' => 'Gutiérrez Román',    'matricula' => '22TIID001', 'cuatrimestre' => 3],
            ['nombre' => 'Yosmar Abdiel',    'apellidos' => 'Ramírez Gómez',      'matricula' => '22TIID002', 'cuatrimestre' => 3],
            ['nombre' => 'Luis Ángel',       'apellidos' => 'Hernández Enríquez', 'matricula' => '22TIID003', 'cuatrimestre' => 3],
            ['nombre' => 'Delfino Alejandro','apellidos' => 'Mendoza Rosas',      'matricula' => '22TIID004', 'cuatrimestre' => 3],
            ['nombre' => 'Pedro',            'apellidos' => 'Martínez López',     'matricula' => '24TIID010', 'cuatrimestre' => 1],
            ['nombre' => 'Laura',            'apellidos' => 'Hernández Díaz',     'matricula' => '24TIID011', 'cuatrimestre' => 1],
        ];

        foreach ($alumnosDatos as $i => $a) {
            $user = User::create([
                'name'     => "{$a['nombre']} {$a['apellidos']}",
                'email'    => strtolower(str_replace(' ', '', $a['nombre'])) . '@alumnos.uttecam.edu.mx',
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('alumno');

            $alumno = Alumno::create([
                'user_id'              => $user->id,
                'carrera_id'           => $tiid->id,
                'matricula'            => $a['matricula'],
                'nombre'               => $a['nombre'],
                'apellidos'            => $a['apellidos'],
                'cuatrimestre_actual'   => $a['cuatrimestre'],
                'status'               => 'activo',
                'tutor_id'             => $i === 0 ? $tutor1->id : null,
            ]);

            // Inscribir al grupo correspondiente
            $grupo = $a['cuatrimestre'] === 3 ? $grupo3A : $grupo1A;
            Inscripcion::create([
                'alumno_id'         => $alumno->id,
                'grupo_id'          => $grupo->id,
                'fecha_inscripcion' => '2026-01-13',
            ]);
        }

        $this->command->info('✅ Datos de prueba creados exitosamente.');
        $this->command->info('   Admin: admin@sigea.uttecam.edu.mx / password');
        $this->command->info('   Docente: carlos1@sigea.uttecam.edu.mx / password');
        $this->command->info('   Alumno: erickdaniel@alumnos.uttecam.edu.mx / password');
    }
}
