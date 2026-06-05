<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function index(Request $request)
    {
        $gestor = $request->user()->gestorEscolar;
        return view('gestor.perfil', compact('gestor'));
    }
}
