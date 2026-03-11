<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $alumno = $request->user()->alumno;
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
        $horario = [];

        if ($alumno) {
            $inscripciones = $alumno->inscripciones()->with(['grupo.horarios.materia', 'grupo.horarios.docente'])->get();

            foreach ($dias as $dia) {
                $horario[$dia] = $inscripciones
                    ->flatMap(fn($i) => $i->grupo->horarios->where('dia_semana', $dia))
                    ->sortBy('hora_inicio')
                    ->values();
            }
        }

        return view('alumno.horario', compact('alumno', 'horario', 'dias'));
    }
}
