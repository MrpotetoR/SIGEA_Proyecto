<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de control de acceso por rol.
 *
 * Roles vigentes (tras la fusion del rol "servicios_escolares" +
 * "director_carrera" en "gestor_escolar"):
 *   - admin
 *   - gestor_escolar
 *   - docente
 *   - alumno
 *
 * Alias retro-compatibilidad: si una ruta antigua referencia
 *   role:servicios_escolares  o  role:director_carrera,
 * este middleware acepta el alias y lo traduce al nuevo rol "gestor_escolar".
 * Esta capa puede eliminarse cuando todas las llamadas se migren.
 */
class CheckRole
{
    /** Mapa de roles antiguos -> rol nuevo unificado. */
    private const ALIAS_ROL = [
        'servicios_escolares' => 'gestor_escolar',
        'director_carrera'    => 'gestor_escolar',
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Traducir alias de roles antiguos -> gestor_escolar.
        $rolesNormalizados = array_unique(array_map(
            fn(string $r): string => self::ALIAS_ROL[$r] ?? $r,
            $roles,
        ));

        if (!$request->user()->hasAnyRole($rolesNormalizados)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        if (!$request->user()->activo) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Tu cuenta está desactivada.']);
        }

        return $next($request);
    }
}
