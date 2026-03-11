<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnosController extends Controller
{
    public function index(Request $request)
    {
        $director = $request->user()->docente;
        $carrera = $director?->carrerasDirigidas()->first();

        $alumnos = $carrera
            ? Alumno::deCarrera($carrera->id_carrera)
                ->when($request->estatus, fn($q) => $q->where('estatus', $request->estatus))
                ->when($request->cuatrimestre, fn($q) => $q->where('cuatrimestre_actual', $request->cuatrimestre))
                ->with('carrera', 'semaforosAcademicos')
                ->orderBy('apellidos')
                ->paginate(25)
            : collect();

        return view('director.alumnos', compact('director', 'carrera', 'alumnos'));
    }

    public function historial(Request $request, Alumno $alumno)
    {
        $historial = $alumno->calificaciones()
            ->with('materia', 'cicloEscolar')
            ->get()
            ->groupBy('id_ciclo');

        $asistencias = $alumno->asistencias()->with('horario.materia')->get();

        return view('director.historial-alumno', compact('alumno', 'historial', 'asistencias'));
    }
}
