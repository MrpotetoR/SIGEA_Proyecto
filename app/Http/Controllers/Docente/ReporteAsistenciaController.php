<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use App\Services\AsistenciaService;
use Illuminate\Http\Request;

class ReporteAsistenciaController extends Controller
{
    public function __construct(private AsistenciaService $service) {}

    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $ciclo = CicloEscolar::cicloActual();
        $grupos = $docente
            ? $docente->horarios()->with('grupo', 'materia')->get()->groupBy('id_grupo')
            : collect();

        $reporte = null;
        if ($request->filled('grupo') && $ciclo) {
            $grupo = \App\Models\Grupo::find($request->grupo);
            $reporte = $grupo ? $this->service->obtenerReportePorGrupo($grupo, $ciclo) : null;
        }

        return view('docente.reporte-asistencia', compact('docente', 'grupos', 'reporte', 'ciclo'));
    }
}
