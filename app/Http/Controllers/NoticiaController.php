<?php

namespace App\Http\Controllers;

use App\Models\Noticia;

/**
 * Visualización universal de una noticia para cualquier rol autenticado.
 *
 * Autoriza acceso solo si:
 *  - La noticia está activa.
 *  - Su fecha_publicacion ya llegó (no está programada al futuro).
 *  - Si tiene destinatarios definidos, el usuario pertenece a alguno de esos roles
 *    (servicios_escolares siempre puede verla, ya que es quien publica).
 */
class NoticiaController extends Controller
{
    public function show(Noticia $noticia)
    {
        $user = auth()->user();

        // Si está programada o desactivada, la ocultamos como si no existiera.
        if (!$noticia->activa || ($noticia->fecha_publicacion && $noticia->fecha_publicacion->isFuture())) {
            abort(404);
        }

        // Si tiene destinatarios restringidos, validar rol del usuario.
        if (!empty($noticia->destinatarios)) {
            $tienePermiso = $user->hasRole('gestor_escolar')
                || $user->hasAnyRole($noticia->destinatarios);

            abort_unless($tienePermiso, 403, 'Esta noticia no está dirigida a tu rol.');
        }

        return view('noticias.show', compact('noticia'));
    }
}
