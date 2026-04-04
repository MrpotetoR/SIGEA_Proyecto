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

        $query = $carrera
            ? Horario::whereHas('grupo', fn($q) => $q->where('id_carrera', $carrera->id_carrera))
                ->with('docente', 'grupo', 'materia')
            : Horario::query()->whereRaw('1=0');

        if ($request->filled('grupo')) {
            $query->where('id_grupo', $request->grupo);
        }
        if ($request->filled('docente')) {
            $query->where('id_docente', $request->docente);
        }
        if ($request->filled('dia')) {
            $query->where('dia_semana', $request->dia);
        }

        $horarios = $query->orderByRaw("FIELD(dia_semana, 'lunes','martes','miercoles','jueves','viernes','sabado')")
            ->orderBy('hora_inicio')
            ->get();

        $grupos = $carrera
            ? Grupo::where('id_carrera', $carrera->id_carrera)->orderBy('clave_grupo')->get()
            : collect();

        $docentes = $carrera
            ? Docente::whereHas('horarios.grupo', fn($q) => $q->where('id_carrera', $carrera->id_carrera))
                ->orderBy('apellidos')->get()
            : collect();

        return view('director.horarios.index', compact('director', 'carrera', 'horarios', 'grupos', 'docentes'));
    }

    public function create()
    {
        $grupos = Grupo::with('carrera')->orderBy('clave_grupo')->get();
        $materias = Materia::orderBy('nombre_materia')->get();
        $docentes = Docente::orderBy('apellidos')->get();
        return view('director.horarios.create', compact('grupos', 'materias', 'docentes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_grupo' => 'required|exists:grupo,id_grupo',
            'id_docente' => 'required|exists:docente,id_docente',
            'id_materia' => 'required|exists:materia,id_materia',
            'dias' => 'required|array|min:1',
            'dias.*.hora_inicio' => 'required_with:dias.*.activo|date_format:H:i|before_or_equal:dias.*.hora_fin',
            'dias.*.hora_fin' => 'required_with:dias.*.activo|date_format:H:i|after:dias.*.hora_inicio|before_or_equal:18:00',
        ]);

        $diasSeleccionados = collect($request->dias)->filter(fn($d) => !empty($d['activo']));

        if ($diasSeleccionados->isEmpty()) {
            return back()->withInput()->withErrors(['dias' => 'Selecciona al menos un día.']);
        }

        $grupo = Grupo::findOrFail($request->id_grupo);
        $count = 0;

        foreach ($diasSeleccionados as $dia => $horario) {
            $this->service->asignarHorario($grupo, [
                'id_docente' => $request->id_docente,
                'id_materia' => $request->id_materia,
                'dia_semana' => $dia,
                'hora_inicio' => $horario['hora_inicio'],
                'hora_fin' => $horario['hora_fin'],
            ]);
            $count++;
        }

        return redirect()->route('director.horarios.index')
            ->with('success', "Horario asignado para {$count} día(s).");
    }

    public function show(Horario $horario) { return view('director.horarios.show', compact('horario')); }
    public function edit(Horario $horario)
    {
        $grupos = Grupo::with('carrera')->orderBy('clave_grupo')->get();
        $materias = Materia::orderBy('nombre_materia')->get();
        $docentes = Docente::orderBy('apellidos')->get();
        return view('director.horarios.edit', compact('horario', 'grupos', 'materias', 'docentes'));
    }

    public function update(Request $request, Horario $horario)
    {
        $request->validate([
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio|before_or_equal:18:00',
            'id_docente' => 'required|exists:docente,id_docente',
            'dia_semana' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado',
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
