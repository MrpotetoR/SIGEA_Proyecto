<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use App\Services\GrupoService;
use Illuminate\Http\Request;

class HorariosController extends Controller
{
    public function __construct(private GrupoService $service) {}

    public function index(Request $request)
    {
        $director = $request->user()->docente;
        $carrera = $director?->carrerasDirigidas()->first();
        $horarios = $carrera
            ? Horario::whereHas('grupo', fn($q) => $q->where('id_carrera', $carrera->id_carrera))
                ->with('docente', 'grupo', 'materia')
                ->get()
            : collect();

        return view('director.horarios.index', compact('director', 'carrera', 'horarios'));
    }

    public function create()
    {
        $grupos = Grupo::with('carrera')->orderBy('clave_grupo')->get();
        $docentes = Docente::orderBy('apellidos')->get();
        $materias = Materia::orderBy('nombre_materia')->get();
        return view('director.horarios.create', compact('grupos', 'docentes', 'materias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_grupo' => 'required|exists:grupo,id_grupo',
            'id_docente' => 'required|exists:docente,id_docente',
            'id_materia' => 'required|exists:materia,id_materia',
            'dia_semana' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $grupo = Grupo::findOrFail($request->id_grupo);
        $this->service->asignarHorario($grupo, $request->all());

        return redirect()->route('director.horarios.index')->with('success', 'Horario asignado.');
    }

    public function show(Horario $horario) { return view('director.horarios.show', compact('horario')); }
    public function edit(Horario $horario)
    {
        $grupos = Grupo::with('carrera')->orderBy('clave_grupo')->get();
        $docentes = Docente::orderBy('apellidos')->get();
        $materias = Materia::orderBy('nombre_materia')->get();
        return view('director.horarios.edit', compact('horario', 'grupos', 'docentes', 'materias'));
    }

    public function update(Request $request, Horario $horario)
    {
        $request->validate([
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'id_docente' => 'required|exists:docente,id_docente',
        ]);
        $horario->update($request->only('hora_inicio', 'hora_fin', 'id_docente', 'dia_semana'));
        return redirect()->route('director.horarios.index')->with('success', 'Horario actualizado.');
    }

    public function destroy(Horario $horario)
    {
        $horario->delete();
        return redirect()->route('director.horarios.index')->with('success', 'Horario eliminado.');
    }
}
