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
        $carreras = Carrera::orderBy('nombre_carrera')->get();
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();

        return view('servicios.inscripciones.index', compact('inscripciones', 'grupos', 'carreras', 'ciclos'));
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
