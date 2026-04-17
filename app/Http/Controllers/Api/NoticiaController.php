<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticiaResource;
use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/noticias",
     *     tags={"Noticias"},
     *     summary="Listar noticias activas",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="desde", in="query", description="Fecha mínima (YYYY-MM-DD)", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="per_page", in="query", description="Resultados por página", @OA\Schema(type="integer", default=15)),
     *
     *     @OA\Response(response=200, description="Listado paginado")
     * )
     */
    public function index(Request $request)
    {
        $query = Noticia::with('autor')
            ->activas()
            ->when($request->desde, fn ($q, $d) => $q->where('fecha_publicacion', '>=', $d));

        return NoticiaResource::collection($query->paginate($request->per_page ?? 15));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/noticias/{id}",
     *     tags={"Noticias"},
     *     summary="Obtener una noticia",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Noticia encontrada"),
     *     @OA\Response(response=404, description="No encontrada")
     * )
     */
    public function show(Noticia $noticia)
    {
        $noticia->load('autor');

        return new NoticiaResource($noticia);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/noticias",
     *     tags={"Noticias"},
     *     summary="Crear una noticia",
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"titulo","contenido"},
     *
     *             @OA\Property(property="titulo", type="string", maxLength=200),
     *             @OA\Property(property="contenido", type="string"),
     *             @OA\Property(property="imagen_url", type="string", format="uri", nullable=true)
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Noticia creada"),
     *     @OA\Response(response=422, description="Validación fallida")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/v1/noticias/{id}",
     *     tags={"Noticias"},
     *     summary="Actualizar una noticia",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="titulo", type="string", maxLength=200),
     *             @OA\Property(property="contenido", type="string"),
     *             @OA\Property(property="imagen_url", type="string", format="uri", nullable=true)
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Noticia actualizada")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/v1/noticias/{id}",
     *     tags={"Noticias"},
     *     summary="Eliminar una noticia",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Noticia eliminada")
     * )
     */
    public function destroy(Noticia $noticia)
    {
        $noticia->delete();

        return response()->json(['message' => 'Noticia eliminada correctamente.']);
    }
}
