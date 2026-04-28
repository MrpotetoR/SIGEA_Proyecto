<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

class InscripcionesController extends Controller
{
    public function index(Request $request)
    {
        $inscripciones = Inscripcion::with('alumno.carrera', 'grupo.cicloEscolar')
            ->when($request->grupo, fn($q) => $q->where('id_grupo', $request->grupo))
            ->when($request->carrera, fn($q) =>
                $q->whereHas('alumno', fn($a) => $a->where('id_carrera', $request->carrera))
            )
            ->when($request->ciclo, fn($q) =>
                $q->whereHas('grupo', fn($g) => $g->where('id_ciclo', $request->ciclo))
            )
            ->when($request->buscar, fn($q) =>
                $q->whereHas('alumno', fn($a) =>
                    $a->where('matricula', 'like', "%{$request->buscar}%")
                      ->orWhere('nombre', 'like', "%{$request->buscar}%")
                      ->orWhere('apellidos', 'like', "%{$request->buscar}%")
                )
            )
            ->orderByDesc('fecha_inscripcion')
            ->paginate(25)->withQueryString();

        $grupos = Grupo::with('cicloEscolar', 'carrera')->orderBy('clave_grupo')->get();
        $carreras = Carrera::misCarreras()->orderBy('nombre_carrera')->get();
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();

        return view('servicios.inscripciones.index', compact('inscripciones', 'grupos', 'carreras', 'ciclos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'id_grupo' => 'required|exists:grupo,id_grupo',
        ]);

        $grupo = Grupo::findOrFail($request->id_grupo);

        // 1) Mismo alumno + mismo grupo (duplicado exacto)
        $existeMismoGrupo = Inscripcion::where('id_alumno', $request->id_alumno)
            ->where('id_grupo', $request->id_grupo)->exists();
        if ($existeMismoGrupo) {
            return back()->withInput()->with('error', 'El alumno ya se encuentra inscrito en este grupo y ciclo escolar.');
        }

        // 2) Mismo alumno en otro grupo del mismo ciclo escolar
        $existeMismoCiclo = Inscripcion::where('id_alumno', $request->id_alumno)
            ->whereHas('grupo', fn($g) => $g->where('id_ciclo', $grupo->id_ciclo))
            ->with('grupo')
            ->first();
        if ($existeMismoCiclo) {
            return back()->withInput()->with(
                'error',
                'El alumno ya se encuentra inscrito en este ciclo escolar (grupo '
                . $existeMismoCiclo->grupo->clave_grupo . '). Elimina esa inscripción primero si deseas cambiarlo.'
            );
        }

        Inscripcion::create([
            'id_alumno' => $request->id_alumno,
            'id_grupo' => $request->id_grupo,
            'fecha_inscripcion' => today(),
        ]);

        return back()->with('success', 'Inscripción realizada.');
    }

    /**
     * Endpoint AJAX: valida en tiempo real si el alumno ya tiene inscripción
     * en el mismo grupo o en otro grupo del mismo ciclo escolar.
     */
    public function check(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'id_grupo'  => 'required|exists:grupo,id_grupo',
        ]);

        $grupo = Grupo::with('cicloEscolar')->findOrFail($request->id_grupo);

        $mismoGrupo = Inscripcion::where('id_alumno', $request->id_alumno)
            ->where('id_grupo', $request->id_grupo)->exists();
        if ($mismoGrupo) {
            return response()->json([
                'conflict' => true,
                'message'  => 'El alumno ya se encuentra inscrito en este grupo y ciclo escolar.',
            ]);
        }

        $mismoCiclo = Inscripcion::where('id_alumno', $request->id_alumno)
            ->whereHas('grupo', fn($g) => $g->where('id_ciclo', $grupo->id_ciclo))
            ->with('grupo')->first();
        if ($mismoCiclo) {
            return response()->json([
                'conflict' => true,
                'message'  => 'El alumno ya está inscrito en este ciclo escolar (grupo '
                    . $mismoCiclo->grupo->clave_grupo . ').',
            ]);
        }

        return response()->json(['conflict' => false]);
    }

    public function destroy(Inscripcion $inscripcion)
    {
        $inscripcion->delete();
        return back()->with('success', 'Inscripción eliminada.');
    }

}
