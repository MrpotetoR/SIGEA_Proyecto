<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use App\Services\NotificacionService;
use Illuminate\Http\Request;

class NoticiasController extends Controller
{
    public function __construct(private NotificacionService $notificaciones) {}

    public function index()
    {
        $noticias = Noticia::with('autor')->orderByDesc('fecha_publicacion')->paginate(15);
        return view('servicios.noticias.index', compact('noticias'));
    }

    public function create() { return view('servicios.noticias.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'contenido' => 'required|string',
            'fecha_publicacion' => 'required|date',
            'audiencia' => 'required|in:todos,roles',
            'roles'   => 'required_if:audiencia,roles|array',
            'roles.*' => 'in:servicios_escolares,director_carrera,docente,alumno',
        ]);

        $noticia = Noticia::create([
            'user_id' => auth()->id(),
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'fecha_publicacion' => $request->fecha_publicacion,
            'activa' => $request->boolean('activa', true),
        ]);

        // Generar notificación según audiencia seleccionada
        if ($noticia->activa) {
            $roles = $request->audiencia === 'roles' ? $request->roles : null;

            $this->notificaciones->notificarNuevaNoticia(
                $noticia->titulo,
                route('servicios.noticias.show', $noticia),
                $roles
            );
        }

        return redirect()->route('servicios.noticias.index')->with('success', 'Noticia publicada.');
    }

    public function show(Noticia $noticia) { return view('servicios.noticias.show', compact('noticia')); }

    public function edit(Noticia $noticia) { return view('servicios.noticias.edit', compact('noticia')); }

    public function update(Request $request, Noticia $noticia)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'contenido' => 'required|string',
        ]);
        $noticia->update($request->only('titulo', 'contenido', 'activa'));
        return redirect()->route('servicios.noticias.index')->with('success', 'Noticia actualizada.');
    }

    public function destroy(Noticia $noticia)
    {
        $noticia->delete();
        return redirect()->route('servicios.noticias.index')->with('success', 'Noticia eliminada.');
    }
}
