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

    public function distribucionSemaforo(Carrera $carrera): array
    {
        $ciclo = CicloEscolar::cicloActual();
        if (!$ciclo) return ['verde' => 0, 'amarillo' => 0, 'rojo' => 0];

        $alumnosIds = Alumno::deCarrera($carrera->id_carrera)->activos()->pluck('id_alumno');

        $distribucion = SemaforoAcademico::whereIn('id_alumno', $alumnosIds)
            ->where('id_ciclo', $ciclo->id_ciclo)
            ->selectRaw('nivel, count(*) as total')
            ->groupBy('nivel')
            ->pluck('total', 'nivel')
            ->toArray();

        return [
            'verde' => $distribucion['verde'] ?? 0,
            'amarillo' => $distribucion['amarillo'] ?? 0,
            'rojo' => $distribucion['rojo'] ?? 0,
        ];
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
