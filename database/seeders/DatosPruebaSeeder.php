<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Materia;
use App\Models\Noticia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generando datos de prueba...');

        $ciclo = CicloEscolar::where('nombre', '2025-2')->first();
        $dsm   = Carrera::where('clave_carrera', 'DSM')->first();
        $ge    = Carrera::where('clave_carrera', 'GE')->first();
        $admin = User::where('email', 'servicios@sigea.edu.mx')->first();

        // ── 1. MATERIAS ────────────────────────────────────────────────────

        $this->command->info('Creando materias...');

        $materiasDSM = [
            ['cuatrimestre' => 1, 'nombre_materia' => 'Fundamentos de Programación', 'horas_semana' => 5],
            ['cuatrimestre' => 1, 'nombre_materia' => 'Matemáticas para Computación', 'horas_semana' => 4],
            ['cuatrimestre' => 1, 'nombre_materia' => 'Desarrollo Web I',             'horas_semana' => 4],
            ['cuatrimestre' => 3, 'nombre_materia' => 'Programación Orientada a Objetos', 'horas_semana' => 5],
            ['cuatrimestre' => 3, 'nombre_materia' => 'Base de Datos Relacional',     'horas_semana' => 4],
            ['cuatrimestre' => 3, 'nombre_materia' => 'Redes de Computadoras',        'horas_semana' => 3],
            ['cuatrimestre' => 5, 'nombre_materia' => 'Desarrollo de Apps Móviles',   'horas_semana' => 5],
            ['cuatrimestre' => 5, 'nombre_materia' => 'Seguridad Informática',        'horas_semana' => 3],
        ];

        $materiasGE = [
            ['cuatrimestre' => 1, 'nombre_materia' => 'Gastronomía Básica',           'horas_semana' => 6],
            ['cuatrimestre' => 1, 'nombre_materia' => 'Historia de la Gastronomía',   'horas_semana' => 3],
            ['cuatrimestre' => 1, 'nombre_materia' => 'Nutrición y Dietética',        'horas_semana' => 4],
            ['cuatrimestre' => 3, 'nombre_materia' => 'Cocina Internacional',         'horas_semana' => 6],
            ['cuatrimestre' => 3, 'nombre_materia' => 'Panadería y Repostería',       'horas_semana' => 5],
        ];

        foreach ($materiasDSM as $m) {
            Materia::firstOrCreate(['id_carrera' => $dsm->id_carrera, 'nombre_materia' => $m['nombre_materia']], $m + ['id_carrera' => $dsm->id_carrera]);
        }
        foreach ($materiasGE as $m) {
            Materia::firstOrCreate(['id_carrera' => $ge->id_carrera, 'nombre_materia' => $m['nombre_materia']], $m + ['id_carrera' => $ge->id_carrera]);
        }

        // ── 2. DOCENTES ────────────────────────────────────────────────────

        $this->command->info('Creando docentes...');

        $docentesData = [
            ['email' => 'docente@sigea.edu.mx',        'nombre' => 'Roberto',   'apellidos' => 'García Mendoza',   'especialidad' => 'Ingeniería en Sistemas',      'horas_contrato' => 20, 'es_tutor' => true],
            ['email' => 'lperez@sigea.edu.mx',         'nombre' => 'Laura',     'apellidos' => 'Pérez Villegas',   'especialidad' => 'Desarrollo Web',              'horas_contrato' => 20, 'es_tutor' => false],
            ['email' => 'cmorales@sigea.edu.mx',       'nombre' => 'Carlos',    'apellidos' => 'Morales Ruiz',     'especialidad' => 'Base de Datos',               'horas_contrato' => 15, 'es_tutor' => true],
            ['email' => 'aherrera@sigea.edu.mx',       'nombre' => 'Ana',       'apellidos' => 'Herrera Castro',   'especialidad' => 'Redes y Telecomunicaciones',  'horas_contrato' => 12, 'es_tutor' => false],
            ['email' => 'msanchez@sigea.edu.mx',       'nombre' => 'Miguel',    'apellidos' => 'Sánchez Torres',   'especialidad' => 'Seguridad Informática',       'horas_contrato' => 10, 'es_tutor' => false],
            ['email' => 'eramirez@sigea.edu.mx',       'nombre' => 'Elena',     'apellidos' => 'Ramírez López',    'especialidad' => 'Gastronomía Internacional',   'horas_contrato' => 18, 'es_tutor' => true],
        ];

        $docentes = [];
        foreach ($docentesData as $d) {
            $user = User::firstOrCreate(
                ['email' => $d['email']],
                ['name' => $d['nombre'] . ' ' . $d['apellidos'], 'password' => bcrypt('password'), 'activo' => true]
            );
            if (!$user->hasRole('docente')) $user->assignRole('docente');

            $docentes[$d['email']] = Docente::firstOrCreate(
                ['user_id' => $user->id],
                ['nombre' => $d['nombre'], 'apellidos' => $d['apellidos'], 'especialidad' => $d['especialidad'], 'horas_contrato' => $d['horas_contrato'], 'es_tutor' => $d['es_tutor']]
            );
        }

        $tutorDSM = $docentes['docente@sigea.edu.mx'];
        $tutorGE  = $docentes['eramirez@sigea.edu.mx'];

        // ── 3. GRUPOS ──────────────────────────────────────────────────────

        $this->command->info('Creando grupos...');

        $grupoDSM1 = Grupo::firstOrCreate(
            ['clave_grupo' => 'DSM-1A', 'id_ciclo' => $ciclo->id_ciclo],
            ['id_carrera' => $dsm->id_carrera, 'id_tutor' => $tutorDSM->id_docente, 'cuatrimestre' => 1]
        );
        $grupoDSM3 = Grupo::firstOrCreate(
            ['clave_grupo' => 'DSM-3A', 'id_ciclo' => $ciclo->id_ciclo],
            ['id_carrera' => $dsm->id_carrera, 'id_tutor' => $docentes['cmorales@sigea.edu.mx']->id_docente, 'cuatrimestre' => 3]
        );
        $grupoGE1  = Grupo::firstOrCreate(
            ['clave_grupo' => 'GE-1A', 'id_ciclo' => $ciclo->id_ciclo],
            ['id_carrera' => $ge->id_carrera, 'id_tutor' => $tutorGE->id_docente, 'cuatrimestre' => 1]
        );

        // ── 4. HORARIOS ────────────────────────────────────────────────────

        $this->command->info('Creando horarios...');

        $matDSM1 = Materia::where('id_carrera', $dsm->id_carrera)->where('cuatrimestre', 1)->get();
        $matDSM3 = Materia::where('id_carrera', $dsm->id_carrera)->where('cuatrimestre', 3)->get();
        $matGE1  = Materia::where('id_carrera', $ge->id_carrera)->where('cuatrimestre', 1)->get();

        $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
        $horas = [
            ['inicio' => '07:00', 'fin' => '09:00'],
            ['inicio' => '09:00', 'fin' => '11:00'],
            ['inicio' => '11:00', 'fin' => '13:00'],
        ];

        // Horarios DSM-1A
        $docentesDSM1 = [$docentes['docente@sigea.edu.mx'], $docentes['lperez@sigea.edu.mx'], $docentes['cmorales@sigea.edu.mx']];
        foreach ($matDSM1->take(3) as $i => $materia) {
            Horario::firstOrCreate(
                ['id_grupo' => $grupoDSM1->id_grupo, 'id_materia' => $materia->id_materia],
                ['id_docente' => $docentesDSM1[$i]->id_docente, 'dia_semana' => $diasSemana[$i], 'hora_inicio' => $horas[$i]['inicio'], 'hora_fin' => $horas[$i]['fin']]
            );
        }

        // Horarios DSM-3A
        $docentesDSM3 = [$docentes['cmorales@sigea.edu.mx'], $docentes['aherrera@sigea.edu.mx'], $docentes['msanchez@sigea.edu.mx']];
        foreach ($matDSM3->take(3) as $i => $materia) {
            Horario::firstOrCreate(
                ['id_grupo' => $grupoDSM3->id_grupo, 'id_materia' => $materia->id_materia],
                ['id_docente' => $docentesDSM3[$i]->id_docente, 'dia_semana' => $diasSemana[$i + 1], 'hora_inicio' => $horas[$i]['inicio'], 'hora_fin' => $horas[$i]['fin']]
            );
        }

        // Horarios GE-1A
        $docentesGE = [$docentes['eramirez@sigea.edu.mx'], $docentes['eramirez@sigea.edu.mx'], $docentes['lperez@sigea.edu.mx']];
        foreach ($matGE1->take(3) as $i => $materia) {
            Horario::firstOrCreate(
                ['id_grupo' => $grupoGE1->id_grupo, 'id_materia' => $materia->id_materia],
                ['id_docente' => $docentesGE[$i]->id_docente, 'dia_semana' => $diasSemana[$i], 'hora_inicio' => $horas[$i]['inicio'], 'hora_fin' => $horas[$i]['fin']]
            );
        }

        // ── 5. ALUMNOS ────────────────────────────────────────────────────

        $this->command->info('Creando alumnos...');

        $alumnosData = [
            // DSM cuatrimestre 1 (grupo DSM-1A)
            ['email' => 'alumno@sigea.edu.mx',        'nombre' => 'Carlos',     'apellidos' => 'Hernández Ruiz',    'carrera' => 'DSM', 'cuatrimestre' => 1],
            ['email' => 'amlopez@estudiante.edu.mx',  'nombre' => 'Ana María',  'apellidos' => 'López Castillo',    'carrera' => 'DSM', 'cuatrimestre' => 1],
            ['email' => 'jgonzalez@estudiante.edu.mx','nombre' => 'Jorge',      'apellidos' => 'González Fuentes',  'carrera' => 'DSM', 'cuatrimestre' => 1],
            ['email' => 'mramirez@estudiante.edu.mx', 'nombre' => 'María',      'apellidos' => 'Ramírez Ochoa',     'carrera' => 'DSM', 'cuatrimestre' => 1],
            ['email' => 'dflores@estudiante.edu.mx',  'nombre' => 'Diego',      'apellidos' => 'Flores Mendoza',    'carrera' => 'DSM', 'cuatrimestre' => 1],
            ['email' => 'svazquez@estudiante.edu.mx', 'nombre' => 'Sofía',      'apellidos' => 'Vázquez Pérez',     'carrera' => 'DSM', 'cuatrimestre' => 1],
            ['email' => 'lmoreno@estudiante.edu.mx',  'nombre' => 'Luis',       'apellidos' => 'Moreno Jiménez',    'carrera' => 'DSM', 'cuatrimestre' => 1],
            ['email' => 'pcastro@estudiante.edu.mx',  'nombre' => 'Paola',      'apellidos' => 'Castro Reyes',      'carrera' => 'DSM', 'cuatrimestre' => 1],
            // DSM cuatrimestre 3 (grupo DSM-3A)
            ['email' => 'etorres@estudiante.edu.mx',  'nombre' => 'Eduardo',    'apellidos' => 'Torres Salinas',    'carrera' => 'DSM', 'cuatrimestre' => 3],
            ['email' => 'ngarcia@estudiante.edu.mx',  'nombre' => 'Natalia',    'apellidos' => 'García Ramos',      'carrera' => 'DSM', 'cuatrimestre' => 3],
            ['email' => 'rjimenez@estudiante.edu.mx', 'nombre' => 'Ricardo',    'apellidos' => 'Jiménez Cruz',      'carrera' => 'DSM', 'cuatrimestre' => 3],
            ['email' => 'vsoria@estudiante.edu.mx',   'nombre' => 'Valentina',  'apellidos' => 'Soria Medina',      'carrera' => 'DSM', 'cuatrimestre' => 3],
            // GE cuatrimestre 1 (grupo GE-1A)
            ['email' => 'irojas@estudiante.edu.mx',   'nombre' => 'Isabela',    'apellidos' => 'Rojas Vargas',      'carrera' => 'GE', 'cuatrimestre' => 1],
            ['email' => 'fnavarro@estudiante.edu.mx', 'nombre' => 'Fernando',   'apellidos' => 'Navarro Estrada',   'carrera' => 'GE', 'cuatrimestre' => 1],
            ['email' => 'cguerrero@estudiante.edu.mx','nombre' => 'Carmen',     'apellidos' => 'Guerrero Leal',     'carrera' => 'GE', 'cuatrimestre' => 1],
        ];

        $alumnos = [];
        $seq = ['DSM' => Alumno::where('id_carrera', $dsm->id_carrera)->count(), 'GE' => Alumno::where('id_carrera', $ge->id_carrera)->count()];

        foreach ($alumnosData as $a) {
            $carrera  = $a['carrera'] === 'DSM' ? $dsm : $ge;
            $tutorObj = $a['carrera'] === 'DSM' ? $tutorDSM : $tutorGE;

            $user = User::firstOrCreate(
                ['email' => $a['email']],
                ['name' => $a['nombre'] . ' ' . $a['apellidos'], 'password' => bcrypt('password'), 'activo' => true]
            );
            if (!$user->hasRole('alumno')) $user->assignRole('alumno');

            $seq[$a['carrera']]++;
            $matricula = strtoupper($a['carrera']) . date('Y') . str_pad($seq[$a['carrera']], 3, '0', STR_PAD_LEFT);

            $alumno = Alumno::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'id_carrera'        => $carrera->id_carrera,
                    'id_tutor'          => $tutorObj->id_docente,
                    'matricula'         => $matricula,
                    'nombre'            => $a['nombre'],
                    'apellidos'         => $a['apellidos'],
                    'cuatrimestre_actual' => $a['cuatrimestre'],
                    'estatus'           => 'activo',
                ]
            );

            $alumnos[] = ['model' => $alumno, 'carrera' => $a['carrera'], 'cuatrimestre' => $a['cuatrimestre']];
        }

        // ── 6. INSCRIPCIONES ──────────────────────────────────────────────

        $this->command->info('Creando inscripciones...');

        foreach ($alumnos as $a) {
            if ($a['carrera'] === 'DSM' && $a['cuatrimestre'] === 1) $grupo = $grupoDSM1;
            elseif ($a['carrera'] === 'DSM' && $a['cuatrimestre'] === 3) $grupo = $grupoDSM3;
            else $grupo = $grupoGE1;

            Inscripcion::firstOrCreate(
                ['id_alumno' => $a['model']->id_alumno, 'id_grupo' => $grupo->id_grupo],
                ['fecha_inscripcion' => Carbon::create(2025, 9, 1)]
            );
        }

        // ── 7. CALIFICACIONES ─────────────────────────────────────────────

        $this->command->info('Generando calificaciones...');

        // Distribución: 60% aprueban bien (8-10), 30% aprueban justo (7-8), 10% reprueban (4-6)
        foreach ($alumnos as $a) {
            if ($a['carrera'] === 'DSM' && $a['cuatrimestre'] === 1) $materias = $matDSM1;
            elseif ($a['carrera'] === 'DSM' && $a['cuatrimestre'] === 3) $materias = $matDSM3;
            else $materias = $matGE1;

            $perfil = rand(1, 10) <= 6 ? 'bueno' : (rand(1, 10) <= 7 ? 'regular' : 'malo');

            foreach ($materias as $materia) {
                for ($parcial = 1; $parcial <= 3; $parcial++) {
                    $cal = match ($perfil) {
                        'bueno'   => rand(80, 100) / 10,
                        'regular' => rand(70, 85) / 10,
                        'malo'    => rand(40, 69) / 10,
                    };
                    Calificacion::firstOrCreate(
                        ['id_alumno' => $a['model']->id_alumno, 'id_materia' => $materia->id_materia, 'id_ciclo' => $ciclo->id_ciclo, 'parcial' => $parcial],
                        ['calificacion' => round($cal, 1)]
                    );
                }
            }
        }

        // ── 8. ASISTENCIAS ────────────────────────────────────────────────

        $this->command->info('Generando asistencias...');

        // Últimas 4 semanas de clases (lunes a viernes)
        $fechas = [];
        $fecha  = Carbon::now()->subWeeks(4)->startOfWeek();
        while ($fecha->lte(Carbon::now())) {
            if ($fecha->isWeekday()) $fechas[] = $fecha->copy();
            $fecha->addDay();
        }

        $horariosDSM1 = Horario::where('id_grupo', $grupoDSM1->id_grupo)->get();
        $horariosDSM3 = Horario::where('id_grupo', $grupoDSM3->id_grupo)->get();
        $horariosGE1  = Horario::where('id_grupo', $grupoGE1->id_grupo)->get();

        $diasMap = ['lunes' => 1, 'martes' => 2, 'miercoles' => 3, 'jueves' => 4, 'viernes' => 5];

        foreach ($alumnos as $a) {
            $horarios = match (true) {
                $a['carrera'] === 'DSM' && $a['cuatrimestre'] === 1 => $horariosDSM1,
                $a['carrera'] === 'DSM' && $a['cuatrimestre'] === 3 => $horariosDSM3,
                default => $horariosGE1,
            };

            // 90% de asistencia normal, 10% ausente
            $tasaAsistencia = rand(75, 98);

            foreach ($horarios as $horario) {
                $diaN = $diasMap[$horario->dia_semana] ?? null;
                if (!$diaN) continue;

                foreach ($fechas as $f) {
                    if ($f->dayOfWeekIso !== $diaN) continue;

                    $estatus = rand(1, 100) <= $tasaAsistencia ? 'presente' : (rand(0, 1) ? 'ausente' : 'justificada');
                    Asistencia::firstOrCreate(
                        ['id_alumno' => $a['model']->id_alumno, 'id_horario' => $horario->id_horario, 'fecha' => $f->toDateString()],
                        ['estatus' => $estatus]
                    );
                }
            }
        }

        // ── 9. NOTICIAS ───────────────────────────────────────────────────

        $this->command->info('Creando noticias...');

        $noticias = [
            ['titulo' => 'Inicio del ciclo escolar 2025-2', 'contenido' => 'Se informa a toda la comunidad estudiantil que el ciclo escolar 2025-2 da inicio el 1 de septiembre de 2025. Los alumnos deberán presentarse en sus aulas asignadas en el horario correspondiente. Se recuerda traer credencial vigente para acceder a las instalaciones.', 'fecha' => Carbon::create(2025, 8, 28)],
            ['titulo' => 'Período de reinscripciones abiertas', 'contenido' => 'El departamento de Servicios Escolares informa que el período de reinscripciones para el ciclo 2026-1 estará abierto del 15 al 30 de enero de 2026. Los alumnos deberán acudir con su documentación en regla y no tener adeudos administrativos.', 'fecha' => Carbon::create(2026, 1, 10)],
            ['titulo' => 'Semana Cultural y Deportiva 2026', 'contenido' => 'Del 17 al 21 de febrero se llevará a cabo la Semana Cultural y Deportiva SIGEA 2026. Habrá eventos de danza, exposiciones de arte, torneos deportivos y presentaciones académicas. La participación genera horas culturales válidas para el expediente del alumno.', 'fecha' => Carbon::create(2026, 2, 10)],
            ['titulo' => 'Actualización de reglamento escolar', 'contenido' => 'Se pone a disposición de la comunidad el nuevo reglamento escolar 2026. Los cambios principales incluyen la política de asistencias (mínimo 80% para derecho a examen) y el proceso de bajas voluntarias. Disponible para descarga en la sección de Documentos Institucionales.', 'fecha' => Carbon::now()->subDays(5)],
        ];

        foreach ($noticias as $n) {
            Noticia::firstOrCreate(
                ['titulo' => $n['titulo']],
                ['contenido' => $n['contenido'], 'user_id' => $admin->id, 'fecha_publicacion' => $n['fecha'], 'activa' => true]
            );
        }

        $this->command->info('');
        $this->command->info('✔ Datos de prueba generados correctamente:');
        $this->command->info('  · 6 docentes  (incluye docente@sigea.edu.mx)');
        $this->command->info('  · 15 alumnos  (incluye alumno@sigea.edu.mx)');
        $this->command->info('  · 13 materias (8 DSM + 5 GE)');
        $this->command->info('  · 3 grupos    (DSM-1A, DSM-3A, GE-1A)');
        $this->command->info('  · 9 horarios');
        $this->command->info('  · Calificaciones 3 parciales por alumno/materia');
        $this->command->info('  · Asistencias de las últimas 4 semanas');
        $this->command->info('  · 4 noticias institucionales');
    }
}
