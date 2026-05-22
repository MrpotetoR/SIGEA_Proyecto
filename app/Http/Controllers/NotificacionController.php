<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Noticia;
use App\Services\NotificacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificacionController extends Controller
{
    public function __construct(private NotificacionService $notificaciones) {}

    /**
     * Obtener notificaciones del usuario autenticado (para polling AJAX).
     * Además, dispara notificaciones de noticias programadas cuyo momento ya llegó.
     */
    public function index(Request $request): JsonResponse
    {
        $this->procesarNoticiasProgramadas();

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

    /**
     * Recorre las noticias cuya fecha_publicacion ya pasó pero aún no se notifican,
     * envía la notificación a los destinatarios y las marca como notificadas.
     *
     * Se invoca "lazy" desde el polling del panel — no requiere cron. Para evitar que
     * cada poll de cada usuario pague el costo del procesamiento, usamos un lock global
     * en cache: sólo el primer request dentro de cada ventana de 60s ejecuta el trabajo.
     * Si una noticia se programa para "ahora", el retraso máximo será de ~60s, aceptable.
     */
    private function procesarNoticiasProgramadas(): void
    {
        // Throttle global: si ya corrió en los últimos 60s, salir inmediatamente.
        // Cache::add devuelve true solo si la clave no existía (atómico en cache).
        if (!Cache::add('procesar-noticias-programadas:lock', 1, 60)) {
            return;
        }

        // Salida temprana barata: si no hay nada pendiente, evitamos el ->get() y el loop.
        if (!Noticia::pendientesNotificacion()->exists()) {
            return;
        }

        $pendientes = Noticia::pendientesNotificacion()->get();
        foreach ($pendientes as $n) {
            $this->notificaciones->notificarNuevaNoticia(
                $n->titulo,
                route('noticias.show', $n),
                $n->destinatarios
            );
            $n->update(['notificado' => true]);
        }
    }
}
