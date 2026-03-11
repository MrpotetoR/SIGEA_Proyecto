<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Grupo;
use App\Services\CalificacionService;
use Illuminate\Http\Request;

class CalificacionesController extends Controller
{
    public function __construct(private CalificacionService $service) {}

    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $grupos = $docente
            ? $docente->horarios()->with('grupo.carrera', 'materia')->get()->groupBy('id_grupo')
            : collect();

        return view('docente.calificaciones.index', compact('docente', 'grupos'));
    }

    public function show(Request $request, Grupo $grupo)
    {
        $parcial = $request->input('parcial', 1);
        $alumnos = $grupo->alumnos()->orderBy('apellidos')->get();

        $horario = $grupo->horarios()
            ->where('id_docente', $request->user()->docente->id_docente)
            ->first();

        $calificaciones = Calificacion::where('id_materia', $horario?->id_materia)
            ->where('parcial', $parcial)
            ->whereIn('id_alumno', $alumnos->pluck('id_alumno'))
            ->pluck('calificacion', 'id_alumno');

        return view('docente.calificaciones.show', compact('grupo', 'horario', 'alumnos', 'calificaciones', 'parcial'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'calificaciones' => 'required|array',
            'calificaciones.*.id_alumno' => 'required|exists:alumno,id_alumno',
            'calificaciones.*.id_materia' => 'required|exists:materia,id_materia',
            'calificaciones.*.id_ciclo' => 'required|exists:ciclo_escolar,id_ciclo',
            'calificaciones.*.parcial' => 'required|integer|min:1|max:3',
            'calificaciones.*.calificacion' => 'required|numeric|min:0|max:10',
        ]);

        $this->service->registrarCalificaciones($request->calificaciones);
        return back()->with('success', 'Calificaciones guardadas.');
    }

    public function update(Request $request, Calificacion $calificacion)
    {
        $request->validate(['calificacion' => 'required|numeric|min:0|max:10']);
        $calificacion->update(['calificacion' => $request->calificacion]);
        return back()->with('success', 'Calificación actualizada.');
    }
}
