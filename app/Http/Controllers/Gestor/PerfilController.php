<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function index(Request $request)
    {
        $docente = $request->user()->docente;
        return view('gestor.perfil', compact('docente'));
    }
}
