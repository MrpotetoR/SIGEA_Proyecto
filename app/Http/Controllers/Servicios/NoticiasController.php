<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'imagen'     => 'nullable|image|max:512',
            'imagen_url' => 'nullable|url|max:500',
        ]);

        $imagenUrl = $this->resolverImagen($request);

        $noticia = Noticia::create([
            'user_id' => auth()->id(),
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'imagen_url' => $imagenUrl,
            'fecha_publicacion' => $request->fecha_publicacion,
            'activa' => true,
        ]);

        $roles = $request->audiencia === 'roles' ? $request->roles : null;

        $this->notificaciones->notificarNuevaNoticia(
            $noticia->titulo,
            route('servicios.noticias.show', $noticia),
            $roles
        );

        return redirect()->route('servicios.noticias.index')->with('success', 'Noticia publicada.');
    }

    public function show(Noticia $noticia) { return view('servicios.noticias.show', compact('noticia')); }

    public function edit(Noticia $noticia) { return view('servicios.noticias.edit', compact('noticia')); }

    public function update(Request $request, Noticia $noticia)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'contenido' => 'required|string',
            'imagen'     => 'nullable|image|max:512',
            'imagen_url' => 'nullable|url|max:500',
        ]);

        $imagenUrl = $this->resolverImagen($request, $noticia);

        // Si marcó quitar imagen
        if ($request->boolean('quitar_imagen') && !$request->hasFile('imagen') && !$request->imagen_url) {
            $this->borrarImagenLocal($noticia);
            $imagenUrl = null;
        }

        $noticia->update([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'imagen_url' => $imagenUrl,
        ]);

        return redirect()->route('servicios.noticias.index')->with('success', 'Noticia actualizada.');
    }

    public function destroy(Noticia $noticia)
    {
        $this->borrarImagenLocal($noticia);
        $noticia->delete();
        return redirect()->route('servicios.noticias.index')->with('success', 'Noticia eliminada.');
    }

    private function resolverImagen(Request $request, ?Noticia $noticia = null): ?string
    {
        // Prioridad: archivo subido > URL externa > imagen existente
        if ($request->hasFile('imagen')) {
            if ($noticia) $this->borrarImagenLocal($noticia);
            return '/storage/' . $request->file('imagen')->store('noticias', 'public');
        }

        if ($request->filled('imagen_url')) {
            if ($noticia) $this->borrarImagenLocal($noticia);
            return $request->imagen_url;
        }

        return $noticia?->imagen_url;
    }

    private function borrarImagenLocal(Noticia $noticia): void
    {
        if ($noticia->imagen_url && str_starts_with($noticia->imagen_url, '/storage/')) {
            $path = str_replace('/storage/', '', $noticia->imagen_url);
            Storage::disk('public')->delete($path);
        }
    }
}
