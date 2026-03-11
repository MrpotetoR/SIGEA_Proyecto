<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use App\Services\EstadisticasCarreraService;
use Illuminate\Http\Request;

class EvaluacionDocenteController extends Controller
{
    public function __construct(private EstadisticasCarreraService $estadisticas) {}

    public function index(Request $request)
    {
        $director = $request->user()->docente;
        $carrera = $director?->carrerasDirigidas()->first();
        $ciclo = CicloEscolar::cicloActual();

        $promedios = ($carrera && $ciclo)
            ? $this->estadisticas->promedioEvaluacionDocente($carrera, $ciclo)
            : collect();

        return view('director.evaluacion-docente', compact('director', 'carrera', 'ciclo', 'promedios'));
    }
}
