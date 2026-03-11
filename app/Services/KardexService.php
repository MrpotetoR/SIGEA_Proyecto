<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\CicloEscolar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class KardexService
{
    public function obtenerHistorialCompleto(Alumno $alumno): Collection
    {
        return $alumno->calificaciones()
            ->with('materia', 'cicloEscolar')
            ->orderBy('id_ciclo')
            ->orderBy('parcial')
            ->get()
            ->groupBy(fn($c) => $c->cicloEscolar->nombre ?? 'Sin ciclo');
    }

    public function calcularPromedioGeneral(Alumno $alumno): float
    {
        return round($alumno->calificaciones()->avg('calificacion') ?? 0, 2);
    }

    public function generarKardexPDF(Alumno $alumno): string
    {
        $historial = $this->obtenerHistorialCompleto($alumno);
        $promedio = $this->calcularPromedioGeneral($alumno);

        $pdf = Pdf::loadView('pdf.kardex', compact('alumno', 'historial', 'promedio'));

        $path = storage_path("app/public/kardex_{$alumno->matricula}.pdf");
        $pdf->save($path);

        return $path;
    }
}
