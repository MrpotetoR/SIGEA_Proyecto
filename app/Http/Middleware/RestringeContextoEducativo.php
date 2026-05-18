<?php

namespace App\Http\Middleware;

use App\Support\ContextoEducativo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe el acceso a una ruta segun el contexto educativo activo.
 *
 * Uso en routes:
 *   Route::middleware(['contexto.solo:universidad'])->group(...)
 *   Route::middleware(['contexto.solo:bachillerato'])->group(...)
 *
 * Si el contexto activo no coincide con el permitido, redirige al dashboard
 * del Gestor con un mensaje informativo.
 *
 * Esto evita que un usuario:
 *   - Use el boton "Atras" del navegador para volver a una pagina que ya no
 *     aplica en el contexto recien seleccionado.
 *   - Escriba la URL directamente.
 *   - Use un bookmark de otra area.
 */
class RestringeContextoEducativo
{
    public function handle(Request $request, Closure $next, string $nivelPermitido): Response
    {
        $actual = ContextoEducativo::actual();

        if ($actual !== $nivelPermitido) {
            $labelPermitido = $nivelPermitido === ContextoEducativo::BACHILLERATO
                ? 'Bachillerato'
                : 'Universidad';
            $labelActual = $actual === ContextoEducativo::BACHILLERATO
                ? 'Bachillerato'
                : 'Universidad';

            return redirect()->route('gestor.dashboard')->with(
                'warning',
                "Esa sección solo está disponible en el área {$labelPermitido}. " .
                "Actualmente estás en {$labelActual}; cambia desde el selector del sidebar para verla."
            );
        }

        return $next($request);
    }
}
