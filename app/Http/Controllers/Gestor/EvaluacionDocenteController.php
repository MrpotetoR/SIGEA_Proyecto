<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Services\EstadisticasCarreraService;
use Illuminate\Http\Request;

class EvaluacionDocenteController extends Controller
{
    public function __construct(private EstadisticasCarreraService $estadisticas) {}

    public function index(Request $request)
    {
        $carrera = Carrera::misCarreras()->first();
        $ciclo = CicloEscolar::cicloActual();

        $promedios = ($carrera && $ciclo)
            ? $this->estadisticas->promedioEvaluacionDocente($carrera, $ciclo)
            : collect();

        return view('gestor.evaluacion-docente', compact('carrera', 'ciclo', 'promedios') + ['director' => null]);
    }
}
