<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TutoradosController extends Controller
{
    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $tutorados = $docente
            ? $docente->alumnosTutoria()->with('carrera')->orderBy('apellidos')->get()
            : collect();

        return view('docente.tutorados', compact('docente', 'tutorados'));
    }
}
