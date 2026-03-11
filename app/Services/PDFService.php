<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\Materia;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFService
{
    public function generarKardex(Alumno $alumno): string
    {
        $kardexService = app(KardexService::class);
        $historial = $kardexService->obtenerHistorialCompleto($alumno);
        $promedio = $kardexService->calcularPromedioGeneral($alumno);

        $pdf = Pdf::loadView('pdf.kardex', compact('alumno', 'historial', 'promedio'));
        $path = storage_path("app/public/kardex_{$alumno->matricula}.pdf");
        $pdf->save($path);
        return $path;
    }

    public function generarBoleta(Alumno $alumno, CicloEscolar $ciclo): string
    {
        $calService = app(CalificacionService::class);
        $boleta = $calService->obtenerBoletaPorAlumno($alumno, $ciclo);

        $pdf = Pdf::loadView('pdf.boleta', compact('alumno', 'ciclo', 'boleta'));
        $path = storage_path("app/public/boleta_{$alumno->matricula}_{$ciclo->nombre}.pdf");
        $pdf->save($path);
        return $path;
    }

    public function generarConstancia(Alumno $alumno, string $tipo): string
    {
        $pdf = Pdf::loadView("pdf.constancia-{$tipo}", compact('alumno', 'tipo'));
        $path = storage_path("app/public/constancia_{$alumno->matricula}_{$tipo}_" . now()->format('Ymd') . ".pdf");
        $pdf->save($path);
        return $path;
    }

    public function generarListaAsistencia(Grupo $grupo, Materia $materia): string
    {
        $alumnos = $grupo->alumnos()->orderBy('apellidos')->get();
        $pdf = Pdf::loadView('pdf.lista-asistencia', compact('grupo', 'materia', 'alumnos'));
        $pdf->setPaper('letter', 'portrait');
        $path = storage_path("app/public/lista_{$grupo->clave_grupo}_{$materia->id_materia}.pdf");
        $pdf->save($path);
        return $path;
    }

    public function generarReporteRendimiento(Grupo $grupo): string
    {
        $calService = app(CalificacionService::class);
        $reporte = $calService->generarReporteRendimiento($grupo);

        $pdf = Pdf::loadView('pdf.reporte-rendimiento', compact('grupo', 'reporte'));
        $path = storage_path("app/public/rendimiento_{$grupo->clave_grupo}.pdf");
        $pdf->save($path);
        return $path;
    }
}
