<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HistorialAcademicoController extends Controller
{
    public function index(Request $request)
    {
        $alumno = $request->user()->alumno;
        $historial = $alumno
            ? $alumno->calificaciones()->with('materia', 'cicloEscolar')->get()->groupBy('id_ciclo')
            : collect();

        return view('alumno.historial', compact('alumno', 'historial'));
    }
}
