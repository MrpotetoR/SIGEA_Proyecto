<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $alumno = $request->user()->alumno;
        $ciclo = CicloEscolar::cicloActual();

        $semaforo = $alumno?->semaforosAcademicos()
            ->where('id_ciclo', $ciclo?->id_ciclo)
            ->first();

        $noticias = \App\Models\Noticia::activas()->take(3)->get();

        $proximasClases = [];
        if ($alumno) {
            $diaSemana = strtolower(now()->locale('es')->dayName);
            $proximasClases = $alumno->inscripciones()
                ->with(['grupo.horarios' => fn($q) => $q->where('dia_semana', $diaSemana)->with('materia', 'docente')])
                ->get()
                ->flatMap(fn($i) => $i->grupo->horarios ?? collect())
                ->sortBy('hora_inicio');
        }

        return view('alumno.dashboard', compact('alumno', 'ciclo', 'semaforo', 'noticias', 'proximasClases'));
    }
}
