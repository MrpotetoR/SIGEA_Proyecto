<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Services\KardexService;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function __construct(private KardexService $kardexService) {}

    public function index(Request $request)
    {
        $alumno = $request->user()->alumno;
        $historial = $alumno ? $this->kardexService->obtenerHistorialCompleto($alumno) : collect();
        $promedio = $alumno ? $this->kardexService->calcularPromedioGeneral($alumno) : 0;

        return view('alumno.kardex', compact('alumno', 'historial', 'promedio'));
    }

    public function descargarPdf(Request $request)
    {
        $alumno = $request->user()->alumno;
        if (!$alumno) abort(404);

        $path = $this->kardexService->generarKardexPDF($alumno);
        return response()->download($path);
    }
}
