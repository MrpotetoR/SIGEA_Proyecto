<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticiaResource;
use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function index(Request $request)
    {
        $query = Noticia::with('autor')
            ->activas()
            ->when($request->desde, fn ($q, $d) => $q->where('fecha_publicacion', '>=', $d));

        return NoticiaResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function show(Noticia $noticia)
    {
        $noticia->load('autor');

        return new NoticiaResource($noticia);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'contenido' => 'required|string',
            'imagen_url' => 'nullable|url|max:500',
        ]);

        $noticia = Noticia::create([
            ...$data,
            'user_id' => $request->user()->id,
            'fecha_publicacion' => now(),
            'activa' => true,
        ]);

        $noticia->load('autor');

        return new NoticiaResource($noticia);
    }

    public function update(Request $request, Noticia $noticia)
    {
        $data = $request->validate([
            'titulo' => 'sometimes|string|max:200',
            'contenido' => 'sometimes|string',
            'imagen_url' => 'nullable|url|max:500',
        ]);

        $noticia->update($data);
        $noticia->load('autor');

        return new NoticiaResource($noticia);
    }

    public function destroy(Noticia $noticia)
    {
        $noticia->delete();

        return response()->json(['message' => 'Noticia eliminada correctamente.']);
    }
}
