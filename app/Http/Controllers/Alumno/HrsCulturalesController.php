<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HrsCulturalesController extends Controller
{
    public const LIMITE_ACUDE = 90;

    public function index(Request $request)
    {
        $alumno = $request->user()->alumno;
        $registros = $alumno ? $alumno->hrsCulturales()->orderByDesc('id_registro')->get() : collect();
        $totalHoras = (float) $registros->sum('horas_acumuladas');
        $limite = self::LIMITE_ACUDE;

        return view('alumno.horas-culturales', compact('alumno', 'registros', 'totalHoras', 'limite'));
    }
}
