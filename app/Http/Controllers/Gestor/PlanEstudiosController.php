<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Materia;
use Illuminate\Http\Request;

class PlanEstudiosController extends Controller
{
    public function index(Request $request)
    {
        $carrera = Carrera::misCarreras()->first();

        $materias = $carrera
            ? Materia::where('id_carrera', $carrera->id_carrera)
                ->orderBy('cuatrimestre')
                ->orderBy('nombre_materia')
                ->get()
                ->groupBy('cuatrimestre')
            : collect();

        return view('gestor.plan-estudios', compact('carrera', 'materias') + ['director' => null]);
    }
}
