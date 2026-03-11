<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use App\Models\Noticia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        $ciclo = CicloEscolar::cicloActual();

        $grupos = collect();
        if ($docente && $ciclo) {
            $grupos = $docente->horarios()
                ->with('grupo', 'materia')
                ->whereHas('grupo', fn($q) => $q->where('id_ciclo', $ciclo->id_ciclo))
                ->get()
                ->groupBy('id_grupo');
        }

        $noticias = Noticia::activas()->take(3)->get();

        return view('docente.dashboard', compact('docente', 'ciclo', 'grupos', 'noticias'));
    }
}
