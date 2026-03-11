<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServicioSocialController extends Controller
{
    public function index(Request $request)
    {
        $alumno = $request->user()->alumno;
        $servicio = $alumno?->servicioSocial;

        return view('alumno.servicio-social', compact('alumno', 'servicio'));
    }
}
