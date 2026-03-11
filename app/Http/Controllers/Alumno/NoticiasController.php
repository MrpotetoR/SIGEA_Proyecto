<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiasController extends Controller
{
    public function index(Request $request)
    {
        $noticias = Noticia::activas()
            ->when($request->filled('desde'), fn($q) => $q->where('fecha_publicacion', '>=', $request->desde))
            ->paginate(10);

        return view('alumno.noticias', compact('noticias'));
    }
}
