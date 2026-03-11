<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Carrera;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_alumnos' => Alumno::activos()->count(),
            'bajas_temporales' => Alumno::where('estatus', 'baja_temporal')->count(),
            'total_docentes' => Docente::count(),
            'total_carreras' => Carrera::count(),
            'ciclo_activo' => CicloEscolar::cicloActual(),
        ];

        return view('servicios.dashboard', compact('stats'));
    }
}
