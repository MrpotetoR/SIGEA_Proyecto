<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function index(Request $request)
    {
        $alumno = $request->user()->alumno?->load('carrera', 'tutor');
        return view('alumno.perfil', compact('alumno'));
    }
}
