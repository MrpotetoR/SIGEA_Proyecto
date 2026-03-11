<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use Illuminate\Http\Request;

class PlanEstudiosController extends Controller
{
    public function index(Request $request)
    {
        $director = $request->user()->docente;
        $carrera = $director?->carrerasDirigidas()->first();

        $materias = $carrera
            ? Materia::where('id_carrera', $carrera->id_carrera)
                ->orderBy('cuatrimestre')
                ->orderBy('nombre_materia')
                ->get()
                ->groupBy('cuatrimestre')
            : collect();

        return view('director.plan-estudios', compact('director', 'carrera', 'materias'));
    }
}
