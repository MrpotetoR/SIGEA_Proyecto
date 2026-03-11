<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $director = $request->user()->docente;
        $carrera = $director?->carrerasDirigidas()->first();

        $grupos = $carrera
            ? Grupo::where('id_carrera', $carrera->id_carrera)->with('cicloEscolar')->get()
            : collect();

        $asistencias = collect();
        if ($request->filled('grupo_id')) {
            $grupo = Grupo::find($request->grupo_id);
            $asistencias = $grupo?->alumnos()
                ->with(['asistencias' => fn($q) =>
                    $q->when($request->fecha, fn($a) => $a->where('fecha', $request->fecha))
                ])
                ->get() ?? collect();
        }

        return view('director.asistencia', compact('director', 'carrera', 'grupos', 'asistencias'));
    }
}
