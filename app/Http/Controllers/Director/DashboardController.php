<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Services\EstadisticasCarreraService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private EstadisticasCarreraService $estadisticas) {}

    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $carrera = $docente?->carrerasDirigidas()->first();
        $ciclo = CicloEscolar::cicloActual();

        $kpis = [
            'total_alumnos' => $carrera ? Alumno::deCarrera($carrera->id_carrera)->activos()->count() : 0,
            'total_docentes' => $carrera
                ? Docente::whereHas('horarios.grupo', fn($q) => $q->where('id_carrera', $carrera->id_carrera))->count()
                : 0,
        ];

        $distribucion_semaforo = ($carrera && $ciclo)
            ? $this->estadisticas->distribucionSemaforo($carrera)
            : ['verde' => 0, 'amarillo' => 0, 'rojo' => 0];

        $indice = ($carrera && $ciclo)
            ? $this->estadisticas->indiceAprobacion($carrera, $ciclo)
            : [];

        return view('director.dashboard', compact('docente', 'carrera', 'ciclo', 'kpis', 'distribucion_semaforo', 'indice'));
    }
}
