<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlumnoResource;
use App\Services\KardexService;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function __construct(private KardexService $kardexService) {}

    /**
     * @OA\Get(
     *     path="/api/v1/kardex",
     *     tags={"Kardex"},
     *     summary="Historial académico del alumno autenticado",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Historial agrupado por ciclo",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="alumno", type="object"),
     *             @OA\Property(property="promedio", type="number", format="float"),
     *             @OA\Property(property="historial", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(response=404, description="El usuario no es alumno")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/kardex/pdf",
     *     tags={"Kardex"},
     *     summary="Descargar kardex en PDF",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Archivo PDF",
     *
     *         @OA\MediaType(mediaType="application/pdf")
     *     ),
     *
     *     @OA\Response(response=404, description="El usuario no es alumno")
     * )
     */
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
