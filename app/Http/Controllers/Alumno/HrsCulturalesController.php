<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HrsCulturalesController extends Controller
{
    public function index(Request $request)
    {
        $alumno = $request->user()->alumno;
        $registros = $alumno ? $alumno->hrsCulturales()->orderByDesc('id_registro')->get() : collect();
        $totalCultural = $registros->where('tipo', 'cultural')->sum('horas_acumuladas');
        $totalDeportiva = $registros->where('tipo', 'deportiva')->sum('horas_acumuladas');

        return view('alumno.horas-culturales', compact('alumno', 'registros', 'totalCultural', 'totalDeportiva'));
    }
}
