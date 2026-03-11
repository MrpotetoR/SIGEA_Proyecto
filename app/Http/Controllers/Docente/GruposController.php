<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use Illuminate\Http\Request;

class GruposController extends Controller
{
    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $ciclo = CicloEscolar::cicloActual();

        $grupos = collect();
        if ($docente && $ciclo) {
            $grupos = $docente->horarios()
                ->with(['grupo.carrera', 'grupo.inscripciones', 'materia'])
                ->whereHas('grupo', fn($q) => $q->where('id_ciclo', $ciclo->id_ciclo))
                ->get()
                ->groupBy('id_grupo');
        }

        return view('docente.grupos', compact('docente', 'ciclo', 'grupos'));
    }
}
