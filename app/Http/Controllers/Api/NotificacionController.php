<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificacionResource;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/notificaciones",
     *     tags={"Notificaciones"},
     *     summary="Listar notificaciones recientes del usuario",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="no_leidas", in="query", description="Filtrar solo no leídas", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *
     *     @OA\Response(response=200, description="Listado paginado con contador de no leídas")
     * )
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = Notificacion::where('user_id', $userId)
            ->when($request->no_leidas, fn ($q) => $q->noLeidas())
            ->recientes()
            ->orderByDesc('created_at');

        $noLeidas = Notificacion::where('user_id', $userId)->noLeidas()->count();

        return NotificacionResource::collection($query->paginate($request->per_page ?? 20))
            ->additional(['meta' => ['no_leidas' => $noLeidas]]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/notificaciones/{id}/leida",
     *     tags={"Notificaciones"},
     *     summary="Marcar una notificación como leída",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Notificación actualizada"),
     *     @OA\Response(response=403, description="No pertenece al usuario")
     * )
     */
    public function marcarLeida(Request $request, Notificacion $notificacion)
    {
        abort_unless($notificacion->user_id === $request->user()->id, 403);
        $notificacion->marcarLeida();

        return new NotificacionResource($notificacion);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/notificaciones/marcar-todas",
     *     tags={"Notificaciones"},
     *     summary="Marcar todas las notificaciones como leídas",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(response=200, description="Operación completada")
     * )
     */
    public function marcarTodasLeidas(Request $request)
    {
        Notificacion::where('user_id', $request->user()->id)
            ->noLeidas()
            ->update(['leida_en' => now()]);

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas.']);
    }
}
