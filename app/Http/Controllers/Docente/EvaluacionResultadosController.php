<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use Illuminate\Http\Request;

class EvaluacionResultadosController extends Controller
{
    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();
        $ciclo = CicloEscolar::cicloActual();

        $evaluaciones = $docente
            ? $docente->evaluaciones()
                ->where('id_ciclo', $ciclo?->id_ciclo)
                ->with('respuestas.pregunta')
                ->get()
            : collect();

        $promedio = $evaluaciones->avg('calificacion_promedio');

        return view('docente.evaluacion-resultados', compact('docente', 'ciclo', 'ciclos', 'evaluaciones', 'promedio'));
    }
}
