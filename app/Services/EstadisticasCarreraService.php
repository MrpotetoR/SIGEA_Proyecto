<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\EvaluacionDocente;
use App\Models\SemaforoAcademico;
use Illuminate\Support\Collection;

class EstadisticasCarreraService
{
    public function indiceAprobacion(Carrera $carrera, CicloEscolar $ciclo): array
    {
        $alumnosIds = Alumno::deCarrera($carrera->id_carrera)->pluck('id_alumno');

        $total = Calificacion::whereIn('id_alumno', $alumnosIds)
            ->where('id_ciclo', $ciclo->id_ciclo)
            ->count();

        $aprobadas = Calificacion::whereIn('id_alumno', $alumnosIds)
            ->where('id_ciclo', $ciclo->id_ciclo)
            ->where('calificacion', '>=', 7)
            ->count();

        return [
            'total' => $total,
            'aprobadas' => $aprobadas,
            'reprobadas' => $total - $aprobadas,
            'porcentaje_aprobacion' => $total > 0 ? round(($aprobadas / $total) * 100, 1) : 0,
        ];
    }

    public function indiceReprobacion(Carrera $carrera, CicloEscolar $ciclo): array
    {
        $data = $this->indiceAprobacion($carrera, $ciclo);
        return [
            'total' => $data['total'],
            'reprobadas' => $data['reprobadas'],
            'porcentaje_reprobacion' => $data['total'] > 0
                ? round(($data['reprobadas'] / $data['total']) * 100, 1)
                : 0,
        ];
    }

    public function distribucionSemaforo(Carrera $carrera, ?CicloEscolar $ciclo = null): array
    {
        $ciclo = $ciclo ?? CicloEscolar::cicloActual();
        if (!$ciclo) return ['verde' => 0, 'amarillo' => 0, 'rojo' => 0];

        $alumnosIds = Alumno::deCarrera($carrera->id_carrera)->activos()->pluck('id_alumno');
        if ($alumnosIds->isEmpty()) return ['verde' => 0, 'amarillo' => 0, 'rojo' => 0];

        // Derivamos el semaforo del promedio real de calificaciones del ciclo,
        // igual que en Rendimiento Docente, para que sea consistente con
        // las columnas de Aprobacion/Reprobacion del mismo reporte.
        //   < 7   -> rojo
        //   7-7.9 -> amarillo
        //   >= 8  -> verde
        // Los alumnos sin calificaciones en el ciclo no se cuentan en ningun nivel.
        $promedios = Calificacion::selectRaw('id_alumno, AVG(calificacion) as promedio')
            ->whereIn('id_alumno', $alumnosIds)
            ->where('id_ciclo', $ciclo->id_ciclo)
            ->groupBy('id_alumno')
            ->pluck('promedio', 'id_alumno');

        $dist = ['verde' => 0, 'amarillo' => 0, 'rojo' => 0];
        foreach ($promedios as $prom) {
            $prom = (float) $prom;
            if ($prom < 7)      $dist['rojo']++;
            elseif ($prom < 8)  $dist['amarillo']++;
            else                $dist['verde']++;
        }

        return $dist;
    }

    public function promedioEvaluacionDocente(Carrera $carrera, CicloEscolar $ciclo): Collection
    {
        return Docente::whereHas('horarios.grupo', fn($q) => $q->where('id_carrera', $carrera->id_carrera))
            ->with(['evaluaciones' => fn($q) => $q->where('id_ciclo', $ciclo->id_ciclo)])
            ->get()
            ->map(fn($d) => [
                'docente' => $d,
                'promedio' => round($d->evaluaciones->avg('calificacion_promedio') ?? 0, 2),
                'total_evaluaciones' => $d->evaluaciones->count(),
            ]);
    }

    public function horasDocentePorCarrera(Carrera $carrera): Collection
    {
        return Docente::whereHas('horarios.grupo', fn($q) => $q->where('id_carrera', $carrera->id_carrera))
            ->withCount('horarios')
            ->get()
            ->map(fn($d) => [
                'docente' => $d,
                'horas_contrato' => $d->horas_contrato,
                'grupos_asignados' => $d->horarios_count,
            ]);
    }
}
