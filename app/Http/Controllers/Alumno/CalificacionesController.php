<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use Illuminate\Http\Request;

class CalificacionesController extends Controller
{
    public function index(Request $request)
    {
        $alumno = $request->user()->alumno;
        $ciclos = CicloEscolar::orderByDesc('fecha_inicio')->get();
        $cicloSeleccionado = $request->input('ciclo_id')
            ? CicloEscolar::find($request->input('ciclo_id'))
            : CicloEscolar::cicloActual();

        $calificaciones = collect();
        if ($alumno && $cicloSeleccionado) {
            $calificaciones = $alumno->calificaciones()
                ->where('id_ciclo', $cicloSeleccionado->id_ciclo)
                ->with('materia')
                ->get()
                ->groupBy('id_materia');
        }

        return view('alumno.calificaciones', compact('alumno', 'ciclos', 'cicloSeleccionado', 'calificaciones'));
    }
}
