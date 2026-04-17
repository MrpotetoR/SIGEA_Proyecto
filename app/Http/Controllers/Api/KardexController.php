<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlumnoResource;
use App\Services\KardexService;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function __construct(private KardexService $kardexService) {}

    public function show(Request $request)
    {
        $alumno = $request->user()?->alumno;

        if (! $alumno) {
            return response()->json(['message' => 'Solo los alumnos tienen kardex.'], 404);
        }

        $alumno->load('carrera');
        $historial = $this->kardexService->obtenerHistorialCompleto($alumno);
        $promedio = $this->kardexService->calcularPromedioGeneral($alumno);

        return response()->json([
            'alumno' => new AlumnoResource($alumno),
            'promedio' => $promedio,
            'historial' => $historial->map(fn ($cals, $ciclo) => [
                'ciclo' => $ciclo,
                'calificaciones' => $cals->map(fn ($c) => [
                    'id' => $c->id_calificacion,
                    'materia' => $c->materia?->nombre_materia,
                    'parcial' => $c->parcial,
                    'calificacion' => (float) $c->calificacion,
                ])->values(),
            ])->values(),
        ]);
    }

    public function pdf(Request $request)
    {
        $alumno = $request->user()?->alumno;

        if (! $alumno) {
            return response()->json(['message' => 'Solo los alumnos tienen kardex.'], 404);
        }

        $path = $this->kardexService->generarKardexPDF($alumno);

        return response()->download($path)->deleteFileAfterSend();
    }
}
