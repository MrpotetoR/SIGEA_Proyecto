<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\Materia;
use Illuminate\Support\Collection;

class CalificacionService
{
    public function registrarCalificaciones(array $datos): void
    {
        foreach ($datos as $fila) {
            Calificacion::updateOrCreate(
                [
                    'id_alumno' => $fila['id_alumno'],
                    'id_materia' => $fila['id_materia'],
                    'id_ciclo' => $fila['id_ciclo'],
                    'parcial' => $fila['parcial'],
                ],
                ['calificacion' => $fila['calificacion']]
            );
        }
    }

    public function obtenerBoletaPorAlumno(Alumno $alumno, CicloEscolar $ciclo): array
    {
        return $alumno->calificaciones()
            ->where('id_ciclo', $ciclo->id_ciclo)
            ->with('materia')
            ->get()
            ->groupBy('id_materia')
            ->map(function ($cals) {
                return [
                    'materia' => $cals->first()->materia,
                    'parcial_1' => $cals->where('parcial', 1)->first()?->calificacion,
                    'parcial_2' => $cals->where('parcial', 2)->first()?->calificacion,
                    'parcial_3' => $cals->where('parcial', 3)->first()?->calificacion,
                    'promedio' => round($cals->avg('calificacion'), 2),
                ];
            })
            ->values()
            ->toArray();
    }

    public function calcularPromedioGrupo(Grupo $grupo, Materia $materia): float
    {
        return round(
            Calificacion::whereIn(
                'id_alumno',
                $grupo->alumnos()->pluck('id_alumno')
            )->where('id_materia', $materia->id_materia)->avg('calificacion') ?? 0,
            2
        );
    }

    public function generarReporteRendimiento(Grupo $grupo): array
    {
        $alumnos = $grupo->alumnos()->with('semaforosAcademicos')->get();
        $ciclo = CicloEscolar::cicloActual();

        return $alumnos->map(function ($alumno) use ($ciclo) {
            $promedio = $alumno->calificaciones()
                ->when($ciclo, fn($q) => $q->where('id_ciclo', $ciclo->id_ciclo))
                ->avg('calificacion') ?? 0;

            return [
                'alumno' => $alumno,
                'promedio' => round($promedio, 2),
                'nivel_semaforo' => $alumno->nivel_semaforo,
                'aprobado' => $promedio >= 7,
            ];
        })->toArray();
    }
}
