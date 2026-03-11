<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use App\Services\EstadisticasCarreraService;
use Illuminate\Http\Request;

class IndiceAprobacionController extends Controller
{
    public function __construct(private EstadisticasCarreraService $estadisticas) {}

    public function index(Request $request)
    {
        $director = $request->user()->docente;
        $carrera = $director?->carrerasDirigidas()->first();
        $ciclo = CicloEscolar::cicloActual();

        $aprobacion = ($carrera && $ciclo) ? $this->estadisticas->indiceAprobacion($carrera, $ciclo) : [];
        $reprobacion = ($carrera && $ciclo) ? $this->estadisticas->indiceReprobacion($carrera, $ciclo) : [];

        return view('director.indice-aprobacion', compact('director', 'carrera', 'ciclo', 'aprobacion', 'reprobacion'));
    }
}
