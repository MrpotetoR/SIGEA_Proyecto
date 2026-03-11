<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Noticia;

class NoticiasController extends Controller
{
    public function index()
    {
        $noticias = Noticia::activas()->paginate(10);
        return view('director.noticias', compact('noticias'));
    }
}
