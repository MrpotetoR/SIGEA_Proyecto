<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\BachilleratoPlan;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Services\GrupoService;
use App\Support\ContextoEducativo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GruposController extends Controller
{
    public function __construct(private GrupoService $service) {}

    public function index(Request $request)
    {
        $esBachi = ContextoEducativo::actual() === ContextoEducativo::BACHILLERATO;

        if ($esBachi) {
            // En bachillerato no hay "carreras dirigidas"; el scope global filtra por nivel.
            $grupos = Grupo::with('cicloEscolar', 'tutorDocente', 'planBachillerato')->get();
            return view('gestor.grupos.index', [
                'director' => null,
                'carrera'  => null,
                'grupos'   => $grupos,
                'esBachi'  => true,
            ]);
        }

        // Universidad: filtra por la primera carrera asignada al gestor escolar.
        $carrera = Carrera::misCarreras()->first();
        $grupos = $carrera
            ? Grupo::where('id_carrera', $carrera->id_carrera)->with('cicloEscolar', 'tutorDocente')->get()
            : collect();

        return view('gestor.grupos.index', compact('carrera', 'grupos') + ['director' => null, 'esBachi' => false]);
    }

    public function create(Request $request)
    {
        $esBachi = ContextoEducativo::actual() === ContextoEducativo::BACHILLERATO;
        $ciclos   = CicloEscolar::orderByDesc('fecha_inicio')->get();
        $docentes = Docente::orderBy('apellidos')->get();

        if ($esBachi) {
            $planes = BachilleratoPlan::vigente()->orderBy('nombre_plan')->get();
            return view('gestor.grupos.create', [
                'ciclos'   => $ciclos,
                'docentes' => $docentes,
                'carrera'  => null,
                'planes'   => $planes,
                'esBachi'  => true,
            ]);
        }

        $carrera = Carrera::misCarreras()->first();
        return view('gestor.grupos.create', compact('ciclos', 'docentes', 'carrera') + ['esBachi' => false, 'planes' => collect()]);
    }

    public function store(Request $request)
    {
        $contexto = ContextoEducativo::actual();
        $esBachi  = $contexto === ContextoEducativo::BACHILLERATO;

        if ($esBachi) {
            $plan = BachilleratoPlan::findOrFail($request->id_plan_bachillerato);
            $maxPeriodos = $plan->num_semestres;

            $request->validate([
                'id_plan_bachillerato' => 'required|exists:bachillerato_plan,id_plan_bachillerato',
                'id_ciclo'     => 'required|exists:ciclo_escolar,id_ciclo',
                'cuatrimestre' => "required|integer|min:1|max:{$maxPeriodos}",
                'clave_grupo'  => 'required|string|max:20|unique:grupo,clave_grupo',
                'id_tutor'     => 'nullable|exists:docente,id_docente',
            ]);

            $grupo = Grupo::create([
                'id_carrera'            => null,
                'id_plan_bachillerato'  => $plan->id_plan_bachillerato,
                'id_ciclo'              => $request->id_ciclo,
                'id_tutor'              => $request->id_tutor,
                'cuatrimestre'          => $request->cuatrimestre,
                'clave_grupo'           => $request->clave_grupo,
                'nivel_educativo'       => 'bachillerato',
            ]);

            return redirect()->route('gestor.grupos.index')->with('success', "Grupo {$grupo->clave_grupo} creado en bachillerato.");
        }

        // Universidad
        $carrera = Carrera::misCarreras()->firstOrFail();
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
        return redirect()->route('gestor.grupos.index')->with('success', "Grupo creado. Se inscribieron $inscritos alumnos automaticamente.");
    }

    public function show(Grupo $grupo) { return view('gestor.grupos.show', compact('grupo')); }

    public function edit(Grupo $grupo)
    {
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();
        $docentes = Docente::orderBy('apellidos')->get();
        $carrera = $grupo->carrera;
        return view('gestor.grupos.edit', compact('grupo', 'ciclos', 'docentes', 'carrera'));
    }

    public function update(Request $request, Grupo $grupo)
    {
        $maxPeriodos = $grupo->nivel_educativo === 'bachillerato'
            ? ($grupo->planBachillerato?->num_semestres ?? 6)
            : ($grupo->carrera?->max_periodos ?? 10);

        $request->validate([
            'cuatrimestre' => "required|integer|min:1|max:{$maxPeriodos}",
            'id_tutor'     => 'nullable|exists:docente,id_docente',
        ]);
        $grupo->update($request->only('cuatrimestre', 'id_tutor'));
        return redirect()->route('gestor.grupos.index')->with('success', 'Grupo actualizado.');
    }

    public function destroy(Grupo $grupo)
    {
        if ($grupo->inscripciones()->exists()) {
            return redirect()->route('gestor.grupos.index')
                ->with('error', 'No se puede eliminar el grupo: tiene alumnos inscritos. Primero remueve a los alumnos del grupo.');
        }

        DB::transaction(function () use ($grupo) {
            $grupo->horarios()->delete();
            $grupo->delete();
        });

        return redirect()->route('gestor.grupos.index')->with('success', 'Grupo eliminado.');
    }

    /** Inscripcion individual (heredado de Director). */
    public function inscribir(Request $request, Grupo $grupo)
    {
        $request->validate(['id_alumno' => 'required|exists:alumno,id_alumno']);

        $existe = Inscripcion::where('id_alumno', $request->id_alumno)
            ->where('id_grupo', $grupo->id_grupo)->exists();
        if ($existe) {
            return back()->with('error', 'El alumno ya esta inscrito en este grupo.');
        }

        Inscripcion::create([
            'id_alumno' => $request->id_alumno,
            'id_grupo'  => $grupo->id_grupo,
            'fecha_inscripcion' => now(),
        ]);

        return back()->with('success', 'Alumno inscrito.');
    }

    public function desinscribir(Grupo $grupo, Alumno $alumno)
    {
        Inscripcion::where('id_alumno', $alumno->id_alumno)
            ->where('id_grupo', $grupo->id_grupo)
            ->delete();

        return back()->with('success', 'Alumno removido del grupo.');
    }
}
