<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Services\CalificacionService;
use Illuminate\Http\Request;

class ReporteRendimientoController extends Controller
{
    public function __construct(private CalificacionService $service) {}

    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $grupos = $docente
            ? $docente->horarios()->with('grupo', 'materia')->get()->groupBy('id_grupo')
            : collect();

        $reporte = null;
        if ($request->filled('grupo_id')) {
            $grupo = \App\Models\Grupo::find($request->grupo_id);
            $reporte = $grupo ? $this->service->generarReporteRendimiento($grupo) : null;
        }

        return view('docente.reporte-rendimiento', compact('docente', 'grupos', 'reporte'));
    }
}
