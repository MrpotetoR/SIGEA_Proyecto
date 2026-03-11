<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Grupo;
use App\Services\AsistenciaService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function __construct(private AsistenciaService $service) {}

    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $grupos = $docente
            ? $docente->horarios()->with('grupo.carrera', 'materia')->get()->groupBy('id_grupo')
            : collect();

        return view('docente.asistencia.index', compact('docente', 'grupos'));
    }

    public function show(Request $request, Grupo $grupo)
    {
        $fecha = $request->input('fecha', today()->toDateString());
        $horario = $grupo->horarios()
            ->where('id_docente', $request->user()->docente->id_docente)
            ->with('materia')
            ->first();

        $alumnos = $grupo->alumnos()->orderBy('apellidos')->get();

        $asistencias = Asistencia::where('id_horario', $horario?->id_horario)
            ->where('fecha', $fecha)
            ->pluck('estatus', 'id_alumno');

        return view('docente.asistencia.show', compact('grupo', 'horario', 'alumnos', 'asistencias', 'fecha'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_grupo' => 'required|exists:grupo,id_grupo',
            'id_horario' => 'required|exists:horario,id_horario',
            'fecha' => 'required|date',
            'asistencias' => 'required|array',
        ]);

        $this->service->registrarAsistencia(
            \App\Models\Horario::find($request->id_horario),
            Carbon::parse($request->fecha),
            $request->asistencias
        );

        return back()->with('success', 'Asistencia registrada correctamente.');
    }

    public function pdf(Request $request, Grupo $grupo)
    {
        $horario = $grupo->horarios()
            ->where('id_docente', $request->user()->docente->id_docente)
            ->with('materia')
            ->first();

        if (!$horario) abort(404);

        $path = $this->service->generarListaPDF($grupo, $horario->materia);
        return response()->download($path);
    }
}
