<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

class InscripcionesController extends Controller
{
    public function index(Request $request)
    {
        $inscripciones = Inscripcion::with('alumno.carrera', 'grupo.cicloEscolar')
            ->when($request->grupo_id, fn($q) => $q->where('id_grupo', $request->grupo_id))
            ->orderByDesc('fecha_inscripcion')
            ->paginate(25)->withQueryString();

        $grupos = Grupo::with('cicloEscolar', 'carrera')->orderBy('clave_grupo')->get();
        $alumnos = Alumno::activos()->orderBy('apellidos')->get();

        return view('servicios.inscripciones.index', compact('inscripciones', 'grupos', 'alumnos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumno,id_alumno',
            'id_grupo' => 'required|exists:grupo,id_grupo',
        ]);

        $existe = Inscripcion::where('id_alumno', $request->id_alumno)
            ->where('id_grupo', $request->id_grupo)->exists();

        if ($existe) {
            return back()->with('error', 'El alumno ya está inscrito en este grupo.');
        }

        Inscripcion::create([
            'id_alumno' => $request->id_alumno,
            'id_grupo' => $request->id_grupo,
            'fecha_inscripcion' => today(),
        ]);

        return back()->with('success', 'Inscripción realizada.');
    }

    public function destroy(Inscripcion $inscripcion)
    {
        $inscripcion->delete();
        return back()->with('success', 'Inscripción eliminada.');
    }
}
