<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\CicloEscolar;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AsistenciaService
{
    public function registrarAsistencia(Horario $horario, Carbon $fecha, array $datos): void
    {
        foreach ($datos as $alumnoId => $estatus) {
            Asistencia::updateOrCreate(
                [
                    'id_alumno' => $alumnoId,
                    'id_horario' => $horario->id_horario,
                    'fecha' => $fecha->toDateString(),
                ],
                ['estatus' => $estatus]
            );
        }
    }

    public function obtenerReportePorGrupo(Grupo $grupo, CicloEscolar $ciclo): array
    {
        $alumnos = $grupo->alumnos()->with('asistencias')->get();
        $reporte = [];

        foreach ($alumnos as $alumno) {
            $asistenciasGrupo = $alumno->asistencias()
                ->whereHas('horario', fn($q) => $q->where('id_grupo', $grupo->id_grupo))
                ->whereHas('horario.grupo', fn($q) => $q->where('id_ciclo', $ciclo->id_ciclo))
                ->get();

            $total = $asistenciasGrupo->count();
            $presentes = $asistenciasGrupo->where('estatus', 'presente')->count();

            $reporte[] = [
                'alumno' => $alumno,
                'total' => $total,
                'presentes' => $presentes,
                'faltas' => $asistenciasGrupo->where('estatus', 'ausente')->count(),
                'justificadas' => $asistenciasGrupo->where('estatus', 'justificada')->count(),
                'porcentaje' => $total > 0 ? round(($presentes / $total) * 100, 1) : 100,
            ];
        }

        return $reporte;
    }

    public function generarListaPDF(Grupo $grupo, Materia $materia): string
    {
        $alumnos = $grupo->alumnos()->orderBy('apellidos')->get();

        $pdf = Pdf::loadView('pdf.lista-asistencia', compact('grupo', 'materia', 'alumnos'));
        $pdf->setPaper('letter', 'portrait');

        $path = storage_path("app/public/lista_{$grupo->clave_grupo}_{$materia->id_materia}.pdf");
        $pdf->save($path);

        return $path;
    }

    public function calcularPorcentajeAsistencia(Alumno $alumno, Materia $materia): float
    {
        $total = $alumno->asistencias()
            ->whereHas('horario', fn($q) => $q->where('id_materia', $materia->id_materia))
            ->count();

        if ($total === 0) return 100.0;

        $presentes = $alumno->asistencias()
            ->whereHas('horario', fn($q) => $q->where('id_materia', $materia->id_materia))
            ->where('estatus', 'presente')
            ->count();

        return round(($presentes / $total) * 100, 1);
    }
}
