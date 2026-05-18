<?php

namespace App\Http\Middleware;

use App\Support\ContextoEducativo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garantiza que el Gestor Escolar tenga un contexto activo
 * ('universidad' o 'bachillerato') antes de acceder al panel.
 *
 * Comportamiento:
 *  1. Si ya hay contexto valido en sesion -> continua.
 *  2. Si el usuario solo tiene un nivel disponible -> lo auto-selecciona.
 *  3. Si tiene ambos -> redirige a la pantalla de seleccion.
 *  4. Si no tiene ninguno -> 403.
 *
 * Comparte la variable `$contextoActual` con todas las vistas (via view()->share).
 */
class EstableceContextoEducativo
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // El admin tambien usa este middleware al entrar al panel gestor.
        $disponibles = ContextoEducativo::nivelesDisponiblesParaUsuario();
        if (empty($disponibles)) {
            abort(403, 'No tienes acceso a ningun area operativa. Contacta al administrador.');
        }

        $actual = ContextoEducativo::actual();

        // Sanitizar: si la sesion guarda un nivel al que ya no tiene acceso, limpiar.
        if ($actual && !in_array($actual, $disponibles, true)) {
            ContextoEducativo::limpiar();
            $actual = null;
        }

        // Auto-seleccion cuando solo hay uno disponible.
        if (!$actual && $unico = ContextoEducativo::nivelUnico()) {
            ContextoEducativo::establecer($unico);
            $actual = $unico;
        }

        // Si todavia no hay contexto, redirigir al selector (excepto si ya estamos ahi).
        if (!$actual && !$request->routeIs('gestor.contexto.*')) {
            return redirect()->route('gestor.contexto.seleccionar');
        }

        // Compartir con todas las vistas.
        view()->share([
            'contextoActual'      => $actual,
            'contextoDisponibles' => $disponibles,
            'contextoColor'       => ContextoEducativo::color($actual),
        ]);

        return $next($request);
    }
}
