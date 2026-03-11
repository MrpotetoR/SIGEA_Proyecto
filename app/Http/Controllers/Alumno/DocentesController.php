<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use Illuminate\Http\Request;

class DocentesController extends Controller
{
    public function index(Request $request)
    {
        $alumno = $request->user()->alumno;
        $docentes = collect();

        if ($alumno) {
            $docentes = Docente::whereHas('horarios.grupo.inscripciones', fn($q) =>
                $q->where('id_alumno', $alumno->id_alumno)
            )->with('horarios.materia')->get();
        }

        return view('alumno.mis-docentes', compact('alumno', 'docentes'));
    }
}
