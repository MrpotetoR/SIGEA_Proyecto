<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Materia;
use App\Models\PagoCuatrimestre;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder principal — modo MANUAL DE USUARIO.
 *
 * Crea un estado mínimo coherente del sistema:
 *  - Catálogo institucional (roles, ciclos, carreras, encuestas).
 *  - 5 usuarios base — uno por rol.
 *  - Datos académicos mínimos para que cada usuario tenga algo coherente
 *    al iniciar sesión (1 carrera dirigida, 1 grupo, 1 materia, 1 horario,
 *    1 inscripción, 1 pago aprobado, calificaciones del primer parcial).
 *
 * Ejecuta con: `php artisan migrate:fresh --seed`
 *
 * Para volver al estado con datos masivos de demo, descomenta la línea
 * `// $this->call(DatosPruebaSeeder::class);` al final del método run().
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ───────────────────────────────────────────────────────────────
        // 1. Catálogo (configuración del sistema, no son datos de prueba).
        // ───────────────────────────────────────────────────────────────
        $this->call([
            RolesPermissionsSeeder::class,
            CicloEscolarSeeder::class,
            CarreraSeeder::class,
            EncuestaPreguntaSeeder::class,
        ]);

        // ───────────────────────────────────────────────────────────────
        // 2. Usuarios base — uno por cada rol.
        // ───────────────────────────────────────────────────────────────

        // Admin (superusuario que crea Personal de SE y otros admins).
        $admin = User::firstOrCreate(
            ['email' => 'admin@sigea.edu.mx'],
            [
                'name'     => 'Administrador SIGEA',
                'password' => bcrypt('admin2026'),
                'activo'   => true,
            ]
        );
        $admin->assignRole('admin');

        // Servicios Escolares (sin perfil de personal — para que el manual
        // demuestre el flujo "admin crea personal de SE y asigna carreras").
        $servicios = User::firstOrCreate(
            ['email' => 'servicios@sigea.edu.mx'],
            [
                'name'     => 'Servicios Escolares',
                'password' => bcrypt('password'),
                'activo'   => true,
            ]
        );
        $servicios->assignRole('servicios_escolares');

        // Director de carrera.
        $directorUser = User::firstOrCreate(
            ['email' => 'director@sigea.edu.mx'],
            [
                'name'     => 'Director de Carrera',
                'password' => bcrypt('password'),
                'activo'   => true,
            ]
        );
        $directorUser->assignRole('director_carrera');

        // Docente.
        $docenteUser = User::firstOrCreate(
            ['email' => 'docente@sigea.edu.mx'],
            [
                'name'     => 'Docente Demo',
                'password' => bcrypt('password'),
                'activo'   => true,
            ]
        );
        $docenteUser->assignRole('docente');

        // Alumno.
        $alumnoUser = User::firstOrCreate(
            ['email' => 'alumno@sigea.edu.mx'],
            [
                'name'     => 'Alumno Demo',
                'password' => bcrypt('password'),
                'activo'   => true,
            ]
        );
        $alumnoUser->assignRole('alumno');

        // ───────────────────────────────────────────────────────────────
        // 3. Datos académicos mínimos coherentes.
        //    Carrera principal: DSM-2026 (Desarrollo de Software).
        // ───────────────────────────────────────────────────────────────

        $carrera = Carrera::where('clave_carrera', 'DSM-2026')->first();
        $ciclo   = CicloEscolar::where('nombre', '2026-1')->first()
                ?? CicloEscolar::orderByDesc('id_ciclo')->first();

        if (!$carrera || !$ciclo) {
            $this->command->warn('No se encontró carrera DSM-2026 o ciclo. Saltando datos académicos.');
            return;
        }

        // 3.1 Director — Docente record que respalda al usuario director.
        $director = Docente::firstOrCreate(
            ['user_id' => $directorUser->id],
            [
                'nombre'         => 'Director',
                'apellidos'      => 'De Carrera',
                'especialidad'   => 'Administración Educativa',
                'horas_contrato' => null,
                'es_tutor'       => false,
            ]
        );

        // Asignar director a la carrera DSM-2026.
        $carrera->update(['id_director' => $director->id_docente]);

        // 3.2 Docente — record + vínculo a la carrera DSM-2026.
        $docente = Docente::firstOrCreate(
            ['user_id' => $docenteUser->id],
            [
                'nombre'         => 'Docente',
                'apellidos'      => 'Demo',
                'especialidad'   => 'Programación y Desarrollo Web',
                'horas_contrato' => 20,
                'es_tutor'       => true,
            ]
        );
        $docente->carrerasImparte()->syncWithoutDetaching([$carrera->id_carrera]);

        // 3.3 Materia: una materia básica del 1er cuatrimestre de DSM.
        $materia = Materia::firstOrCreate(
            [
                'id_carrera'     => $carrera->id_carrera,
                'nombre_materia' => 'Fundamentos de Programación',
                'cuatrimestre'   => 1,
            ],
            ['horas_semana' => 6]
        );

        // 3.4 Grupo: DSM-1A en el ciclo activo, con el docente como tutor.
        $grupo = Grupo::firstOrCreate(
            [
                'clave_grupo' => 'DSM-1A',
            ],
            [
                'id_carrera'   => $carrera->id_carrera,
                'id_ciclo'     => $ciclo->id_ciclo,
                'id_tutor'     => $docente->id_docente,
                'cuatrimestre' => 1,
            ]
        );

        // 3.5 Horario: Lunes 7:00–9:00 para esa materia/grupo/docente.
        Horario::firstOrCreate(
            [
                'id_grupo'   => $grupo->id_grupo,
                'id_materia' => $materia->id_materia,
                'dia_semana' => 'lunes',
            ],
            [
                'id_docente'  => $docente->id_docente,
                'hora_inicio' => '07:00:00',
                'hora_fin'    => '09:00:00',
            ]
        );

        // 3.6 Alumno — record + matrícula + inscripción al grupo.
        $alumno = Alumno::firstOrCreate(
            ['user_id' => $alumnoUser->id],
            [
                'id_carrera'          => $carrera->id_carrera,
                'id_tutor'            => $docente->id_docente,
                'matricula'           => 'DSM20260001',
                'nombre'              => 'Alumno',
                'apellidos'           => 'Demo',
                'cuatrimestre_actual' => 1,
                'estatus'             => 'activo',
            ]
        );

        Inscripcion::firstOrCreate(
            ['id_alumno' => $alumno->id_alumno, 'id_grupo' => $grupo->id_grupo],
            ['fecha_inscripcion' => now()]
        );

        // 3.7 Pago de cuatrimestre 1 — aprobado, para que el módulo de pagos
        //     y la columna "Estado de pago" del listado tengan datos visibles.
        PagoCuatrimestre::firstOrCreate(
            ['id_alumno' => $alumno->id_alumno, 'cuatrimestre' => 1],
            [
                'baucher_path' => 'demo/pago_dsm20260001_c1.pdf',
                'estatus'      => 'aprobado',
                'subido_por'   => $servicios->id,
                'revisado_por' => $servicios->id,
                'revisado_en'  => now(),
            ]
        );

        // 3.8 Calificación del primer parcial (8.5) para mostrar el flujo
        //     de calificaciones / kardex / semáforo.
        Calificacion::firstOrCreate(
            [
                'id_alumno'  => $alumno->id_alumno,
                'id_materia' => $materia->id_materia,
                'id_ciclo'   => $ciclo->id_ciclo,
                'parcial'    => 1,
            ],
            ['calificacion' => 8.5]
        );

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  SIGEA inicializado con datos mínimos para manual de usuario');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  Usuarios base (todos con contraseña "password"');
        $this->command->info('  excepto admin):');
        $this->command->line('    • admin@sigea.edu.mx       admin2026');
        $this->command->line('    • servicios@sigea.edu.mx   password');
        $this->command->line('    • director@sigea.edu.mx    password');
        $this->command->line('    • docente@sigea.edu.mx     password');
        $this->command->line('    • alumno@sigea.edu.mx      password');
        $this->command->info('───────────────────────────────────────────────────────────');
        $this->command->info('  Estado académico inicial:');
        $this->command->line('    • Carrera DSM-2026 dirigida por director@');
        $this->command->line('    • Grupo DSM-1A con docente@ (tutor)');
        $this->command->line('    • Alumno DSM20260001 inscrito en DSM-1A');
        $this->command->line('    • Pago 1° cuatrimestre aprobado');
        $this->command->line('    • Calificación parcial 1 = 8.5 en Fundamentos de Prog.');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  Para cargar datos masivos de demostración, descomenta');
        $this->command->info('  la última línea de DatabaseSeeder y vuelve a ejecutar.');
        $this->command->info('═══════════════════════════════════════════════════════════');

        // Para volver al modo demo con muchos alumnos/docentes, descomenta:
        // $this->call(DatosPruebaSeeder::class);
    }
}
