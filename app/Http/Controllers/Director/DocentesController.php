<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use Illuminate\Http\Request;

class DocentesController extends Controller
{
    public function index(Request $request)
    {
        $director = $request->user()->docente;
        $carrera = $director?->carrerasDirigidas()->first();

        $docentes = $carrera
            ? Docente::whereHas('horarios.grupo', fn($q) =>
                $q->where('id_carrera', $carrera->id_carrera)
              )->with('horarios.materia', 'horarios.grupo')->get()
            : collect();

        return view('director.docentes', compact('director', 'carrera', 'docentes'));
    }
}
