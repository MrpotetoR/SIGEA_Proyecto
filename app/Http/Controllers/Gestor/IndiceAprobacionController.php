<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Services\EstadisticasCarreraService;
use Illuminate\Http\Request;

class IndiceAprobacionController extends Controller
{
    public function __construct(private EstadisticasCarreraService $estadisticas) {}

    public function index(Request $request)
    {
        $carrera = Carrera::misCarreras()->first();
        $ciclo = CicloEscolar::cicloActual();

        $aprobacion = ($carrera && $ciclo) ? $this->estadisticas->indiceAprobacion($carrera, $ciclo) : [];
        $reprobacion = ($carrera && $ciclo) ? $this->estadisticas->indiceReprobacion($carrera, $ciclo) : [];

        return view('gestor.indice-aprobacion', compact('carrera', 'ciclo', 'aprobacion', 'reprobacion') + ['director' => null]);
    }
}
