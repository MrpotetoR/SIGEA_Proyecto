<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Services\GrupoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GruposController extends Controller
{
    public function __construct(private GrupoService $service) {}

    public function index(Request $request)
    {
        $director = $request->user()->docente;
        $carrera = $director?->carrerasDirigidas()->first();
        $grupos = $carrera
            ? Grupo::where('id_carrera', $carrera->id_carrera)->with('cicloEscolar', 'tutorDocente')->get()
            : collect();

        return view('director.grupos.index', compact('director', 'carrera', 'grupos'));
    }

    public function create(Request $request)
    {
        $director = $request->user()->docente;
        $carrera  = $director?->carrerasDirigidas()->first();
        $ciclos   = CicloEscolar::orderByDesc('fecha_inicio')->get();
        $docentes = Docente::orderBy('apellidos')->get();
        return view('director.grupos.create', compact('ciclos', 'docentes', 'carrera'));
    }

    public function store(Request $request)
    {
        $director = $request->user()->docente;
        $carrera = $director->carrerasDirigidas()->firstOrFail();
        $maxPeriodos = $carrera->max_periodos;

        $request->validate([
            'id_ciclo'     => 'required|exists:ciclo_escolar,id_ciclo',
            'cuatrimestre' => "required|integer|min:1|max:{$maxPeriodos}",
            'clave_grupo'  => 'required|string|max:20|unique:grupo,clave_grupo',
            'id_tutor'     => 'nullable|exists:docente,id_docente',
        ], [
            'cuatrimestre.max' => "Tu carrera solo tiene {$maxPeriodos} {$carrera->label_periodo}s.",
        ]);

        $grupo = $this->service->crearGrupo(array_merge($request->all(), ['id_carrera' => $carrera->id_carrera]));
        $inscritos = $this->service->autoInscribirAlumnos($grupo);
        return redirect()->route('director.grupos.index')->with('success', "Grupo creado. Se inscribieron $inscritos alumnos automáticamente.");
    }

    public function show(Grupo $grupo) { return view('director.grupos.show', compact('grupo')); }
    public function edit(Grupo $grupo)
    {
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();
        $docentes = Docente::orderBy('apellidos')->get();
        $carrera = $grupo->carrera;
        return view('director.grupos.edit', compact('grupo', 'ciclos', 'docentes', 'carrera'));
    }

    public function update(Request $request, Grupo $grupo)
    {
        $maxPeriodos = $grupo->carrera?->max_periodos ?? 10;
        $request->validate([
            'cuatrimestre' => "required|integer|min:1|max:{$maxPeriodos}",
            'id_tutor'     => 'nullable|exists:docente,id_docente',
        ]);
        $grupo->update($request->only('cuatrimestre', 'id_tutor'));
        return redirect()->route('director.grupos.index')->with('success', 'Grupo actualizado.');
    }

    public function destroy(Grupo $grupo)
    {
        if ($grupo->inscripciones()->exists()) {
            return redirect()->route('director.grupos.index')
                ->with('error', 'No se puede eliminar el grupo: tiene alumnos inscritos. Primero remueve a los alumnos del grupo.');
        }

        DB::transaction(function () use ($grupo) {
            $grupo->horarios()->delete();
            $grupo->delete();
        });

        return redirect()->route('director.grupos.index')->with('success', 'Grupo eliminado.');
    }

    public function inscribir(Request $request, Grupo $grupo)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
        ]);

        $existe = Inscripcion::where('id_alumno', $request->id_alumno)
            ->where('id_grupo', $grupo->id_grupo)
            ->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'El alumno ya está inscrito en este grupo.');
        }

        Inscripcion::create([
            'id_alumno' => $request->id_alumno,
            'id_grupo' => $grupo->id_grupo,
            'fecha_inscripcion' => now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Alumno inscrito correctamente.');
    }

    public function desinscribir(Grupo $grupo, Alumno $alumno)
    {
        Inscripcion::where('id_alumno', $alumno->id_alumno)
            ->where('id_grupo', $grupo->id_grupo)
            ->delete();

        return redirect()->back()->with('success', 'Alumno removido del grupo.');
    }
}
