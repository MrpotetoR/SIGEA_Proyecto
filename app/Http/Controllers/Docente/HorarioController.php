<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
        $horario = [];

        if ($docente) {
            foreach ($dias as $dia) {
                $horario[$dia] = $docente->horarios()
                    ->where('dia_semana', $dia)
                    ->with('materia', 'grupo')
                    ->orderBy('hora_inicio')
                    ->get();
            }
        }

        return view('docente.horario', compact('docente', 'horario', 'dias'));
    }
}
