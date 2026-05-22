<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Illuminate\Http\Request;

/**
 * Historial académico de alumnos — vista heredada del antiguo Director de Carrera.
 * El gestor escolar ve los alumnos de las carreras que tiene asignadas (admin: todas).
 */
class HistorialAlumnosController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $carrerasIds = $user->carrerasAsignadasIds();

        $alumnos = !empty($carrerasIds)
            ? Alumno::whereIn('id_carrera', $carrerasIds)
                ->when($request->estatus, fn($q) => $q->where('estatus', $request->estatus))
                ->when($request->cuatrimestre, fn($q) => $q->where('cuatrimestre_actual', $request->cuatrimestre))
                ->with('carrera', 'ultimoSemaforo')
                ->orderBy('apellidos')
                ->paginate(25)
            : collect();

        $carrera = null;
        return view('gestor.historial-alumnos.index', compact('alumnos', 'carrera'));
    }

    public function show(Request $request, Alumno $alumno)
    {
        $historial = $alumno->calificaciones()
            ->with('materia', 'cicloEscolar')
            ->get()
            ->groupBy('id_ciclo');

        $asistencias = $alumno->asistencias()->with('horario.materia')->get();

        return view('gestor.historial-alumnos.show', compact('alumno', 'historial', 'asistencias'));
    }
}
