<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Obtener notificaciones del usuario autenticado (para polling AJAX).
     */
    public function index(Request $request): JsonResponse
    {
        $notificaciones = Notificacion::where('user_id', auth()->id())
            ->recientes()
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $noLeidas = Notificacion::where('user_id', auth()->id())->noLeidas()->count();

        return response()->json([
            'notificaciones' => $notificaciones->map(fn($n) => [
                'id'         => $n->id,
                'tipo'       => $n->tipo,
                'titulo'     => $n->titulo,
                'mensaje'    => $n->mensaje,
                'icono_svg'  => $n->icono_svg,
                'color_class' => $n->color_class,
                'url'        => $n->url,
                'leida'      => $n->estaLeida(),
                'tiempo'     => $n->created_at->locale('es')->diffForHumans(),
                'created_at' => $n->created_at->toIso8601String(),
            ]),
            'no_leidas' => $noLeidas,
        ]);
    }

    /**
     * Marcar una notificación como leída.
     */
    public function marcarLeida(Notificacion $notificacion): JsonResponse
    {
        abort_unless($notificacion->user_id === auth()->id(), 403);
        $notificacion->marcarLeida();

        return response()->json(['ok' => true]);
    }

    /**
     * Marcar todas las notificaciones como leídas.
     */
    public function marcarTodasLeidas(): JsonResponse
    {
        Notificacion::where('user_id', auth()->id())
            ->noLeidas()
            ->update(['leida_en' => now()]);

        return response()->json(['ok' => true]);
    }
}
