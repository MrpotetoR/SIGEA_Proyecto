<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificacionResource;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
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

    public function marcarLeida(Request $request, Notificacion $notificacion)
    {
        abort_unless($notificacion->user_id === $request->user()->id, 403);
        $notificacion->marcarLeida();

        return new NotificacionResource($notificacion);
    }

    public function marcarTodasLeidas(Request $request)
    {
        Notificacion::where('user_id', $request->user()->id)
            ->noLeidas()
            ->update(['leida_en' => now()]);

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas.']);
    }
}
