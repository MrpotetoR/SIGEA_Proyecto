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
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder para validar el Panel Docente con datos reales.
 *
 * Genera:
 *  - 1 grupo (DSM1A) en la carrera 1 con el ciclo activo
 *  - 1 horario lunes 8:00-10:00 asignado a docente@sigea.edu.mx + Fundamentos de Programación
 *  - 8 alumnos inscritos en el grupo (reutiliza alumnos existentes, les asigna carrera 1)
 *  - 24 asistencias (3 fechas pasadas × 8 alumnos): 80% presente, 15% ausente, 5% retardo
 *  - 8 calificaciones de parcial 1 (rango 6.0–9.5)
 *
 * Idempotente: usa firstOrCreate/updateOrCreate; volver a correrlo no duplica registros.
 */
class DocenteDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Resolver entidades base (deben existir desde seeders previos)
        $docente = Docente::whereHas('user', fn($q) => $q->where('email', 'docente@sigea.edu.mx'))->first();
        if (! $docente) {
            $this->command->warn('Docente demo no encontrado (docente@sigea.edu.mx). Aborta.');
            return;
        }

        $ciclo = CicloEscolar::cicloActual();
        if (! $ciclo) {
            $this->command->warn('No hay ciclo activo. Aborta.');
            return;
        }

        $carrera = Carrera::find(1);
        $materia = Materia::find(1);
        if (! $carrera || ! $materia) {
            $this->command->warn('Carrera 1 o Materia 1 no encontradas. Aborta.');
            return;
        }

        DB::transaction(function () use ($docente, $ciclo, $carrera, $materia) {

            // 2) Grupo DSM1A
            $grupo = Grupo::firstOrCreate(
                ['clave_grupo' => 'DSM1A', 'id_ciclo' => $ciclo->id_ciclo],
                [
                    'id_carrera'           => $carrera->id_carrera,
                    'id_plan_bachillerato' => null,
                    'id_tutor'             => $docente->id_docente,
                    'cuatrimestre'         => 1,
                    'nivel_educativo'      => 'universidad',
                ]
            );

            // 3) Horario lunes 8-10
            $horario = Horario::firstOrCreate(
                [
                    'id_docente' => $docente->id_docente,
                    'id_grupo'   => $grupo->id_grupo,
                    'id_materia' => $materia->id_materia,
                    'dia_semana' => 'lunes',
                ],
                [
                    'hora_inicio' => '08:00:00',
                    'hora_fin'    => '10:00:00',
                ]
            );

            // 4) 8 alumnos inscritos (reutiliza los primeros 8 con id ≥ 1).
            //    Asegura que tengan carrera 1 para que las queries de carrera no fallen.
            $alumnos = Alumno::orderBy('id_alumno')->take(8)->get();
            foreach ($alumnos as $alumno) {
                if ($alumno->id_carrera !== $carrera->id_carrera) {
                    $alumno->update(['id_carrera' => $carrera->id_carrera]);
                }
                Inscripcion::firstOrCreate(
                    ['id_alumno' => $alumno->id_alumno, 'id_grupo' => $grupo->id_grupo],
                    ['fecha_inscripcion' => now()->subDays(30)]
                );
            }

            // 5) Asistencias: 3 lunes pasados, todos los 8 alumnos
            $fechas = [
                Carbon::today()->subWeeks(3)->next(Carbon::MONDAY),
                Carbon::today()->subWeeks(2)->next(Carbon::MONDAY),
                Carbon::today()->subWeeks(1)->next(Carbon::MONDAY),
            ];

            $estatusPool = [
                'presente', 'presente', 'presente', 'presente',
                'presente', 'presente', 'presente', 'presente',
                'ausente', 'ausente', 'ausente', 'retardo',
            ];

            foreach ($fechas as $fecha) {
                foreach ($alumnos as $i => $alumno) {
                    Asistencia::updateOrCreate(
                        [
                            'id_alumno'  => $alumno->id_alumno,
                            'id_horario' => $horario->id_horario,
                            'fecha'      => $fecha->toDateString(),
                        ],
                        ['estatus' => $estatusPool[($i + $fecha->day) % count($estatusPool)]]
                    );
                }
            }

            // 6) Calificaciones parcial 1: rango 6.0–9.5
            $calificacionesPool = [6.0, 7.0, 7.5, 8.0, 8.5, 8.5, 9.0, 9.5];
            foreach ($alumnos as $i => $alumno) {
                Calificacion::updateOrCreate(
                    [
                        'id_alumno'  => $alumno->id_alumno,
                        'id_materia' => $materia->id_materia,
                        'id_ciclo'   => $ciclo->id_ciclo,
                        'parcial'    => 1,
                    ],
                    ['calificacion' => $calificacionesPool[$i % count($calificacionesPool)]]
                );
            }

            $this->command->info("✓ Grupo DSM1A creado/actualizado (id={$grupo->id_grupo})");
            $this->command->info("✓ Horario lunes 8-10 asignado al docente Demo");
            $this->command->info("✓ {$alumnos->count()} alumnos inscritos en el grupo");
            $this->command->info("✓ Asistencias de 3 fechas creadas");
            $this->command->info("✓ Calificaciones de parcial 1 creadas");
        });
    }
}
