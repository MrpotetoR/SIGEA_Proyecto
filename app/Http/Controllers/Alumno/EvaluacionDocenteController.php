<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\EncuestaPregunta;
use App\Models\EvaluacionDocente;
use App\Models\EncuestaRespuesta;
use Illuminate\Http\Request;

class EvaluacionDocenteController extends Controller
{
    public function index(Request $request)
    {
        $alumno = $request->user()->alumno;
        $ciclo = CicloEscolar::cicloActual();
        $preguntas = EncuestaPregunta::activas()->get();

        // Docentes del ciclo actual de los grupos inscritos
        $docentes = collect();
        $evaluados = collect();
        if ($alumno && $ciclo) {
            $docentes = Docente::whereHas('horarios.grupo.inscripciones', fn($q) =>
                $q->where('id_alumno', $alumno->id_alumno)
            )->get();

            $evaluados = EvaluacionDocente::where('id_alumno', $alumno->id_alumno)
                ->where('id_ciclo', $ciclo->id_ciclo)
                ->pluck('id_docente');
        }

        return view('alumno.evaluacion-docente', compact('alumno', 'ciclo', 'preguntas', 'docentes', 'evaluados'));
    }

    public function store(Request $request)
    {
        $alumno = $request->user()->alumno;
        if (!$alumno) abort(403);

        $ciclo = CicloEscolar::cicloActual();
        $request->validate([
            'id_docente' => 'required|exists:docente,id_docente',
            'respuestas' => 'required|array',
            'respuestas.*' => 'integer|min:1|max:5',
            'comentarios' => 'nullable|string|max:500',
        ]);

        // Verificar que no haya evaluado ya
        $existe = EvaluacionDocente::where([
            'id_docente' => $request->id_docente,
            'id_alumno' => $alumno->id_alumno,
            'id_ciclo' => $ciclo->id_ciclo,
        ])->exists();

        if ($existe) {
            return back()->with('error', 'Ya evaluaste a este docente en el ciclo actual.');
        }

        $promedio = round(collect($request->respuestas)->avg(), 2);

        $evaluacion = EvaluacionDocente::create([
            'id_docente' => $request->id_docente,
            'id_alumno' => $alumno->id_alumno,
            'id_ciclo' => $ciclo->id_ciclo,
            'calificacion_promedio' => $promedio,
            'comentarios' => $request->comentarios,
        ]);

        foreach ($request->respuestas as $preguntaId => $valor) {
            EncuestaRespuesta::create([
                'id_evaluacion' => $evaluacion->id_evaluacion,
                'id_pregunta' => $preguntaId,
                'valor' => $valor,
            ]);
        }

        return redirect()->route('alumno.evaluacion-docente')->with('success', 'Evaluación registrada correctamente.');
    }
}
