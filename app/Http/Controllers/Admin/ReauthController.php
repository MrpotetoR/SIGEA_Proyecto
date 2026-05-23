<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Maneja la re-autenticación por contraseña para acciones administrativas
 * sensibles (otorgar permiso especial, asignar carreras a otros gestores,
 * crear carreras, etc.).
 *
 * Reglas:
 *  - 3 intentos por (usuario + acción).
 *  - Bloqueo de 5 minutos tras 3 fallos.
 *  - Tras una reauth exitosa, se concede un "grace period" de 10 min para
 *    esa acción (las siguientes ejecuciones no piden contraseña dentro del
 *    rango).
 */
class ReauthController extends Controller
{
    public const MAX_INTENTOS = 3;
    public const BLOQUEO_MINUTOS = 5;
    public const GRACE_PERIOD_MINUTOS = 10;

    /** Acciones permitidas por este endpoint (lista blanca). */
    private const ACCIONES_VALIDAS = [
        // Asignación de carreras
        'otorgar_permiso_especial',
        'revocar_permiso_especial',
        'crear_carrera',
        'asignar_carrera',
        // Caja Chica — permisos y configuración
        'otorgar_permiso_caja_chica',
        'revocar_permiso_caja_chica',
        'configurar_tope_caja_chica',
        // Caja Chica — operaciones sobre vales
        'autorizar_vale',
        'rechazar_vale',
        'cerrar_vale',
        'cancelar_vale',
        'subir_factura',
        // Caja Chica — movimientos de fondo
        'reponer_fondo',
    ];

    /**
     * Consulta el estado de bloqueo del usuario actual para una acción.
     * Se llama desde el front al abrir el modal para mostrar el contador
     * regresivo inmediatamente si todavía hay un bloqueo activo.
     */
    public function estado(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:' . implode(',', self::ACCIONES_VALIDAS),
        ]);

        $action    = $request->input('action');
        $key       = "reauth:{$request->user()->id}:{$action}";
        $bloqueado = RateLimiter::tooManyAttempts($key, self::MAX_INTENTOS);

        return response()->json([
            'bloqueado'          => $bloqueado,
            'segundos_espera'    => $bloqueado ? RateLimiter::availableIn($key) : null,
            'tiene_grace_period' => self::tieneGracePeriod($action),
        ]);
    }

    public function verificar(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
            'action'   => 'required|string|in:' . implode(',', self::ACCIONES_VALIDAS),
        ]);

        $user   = $request->user();
        $action = $request->input('action');
        $key    = "reauth:{$user->id}:{$action}";

        // ¿Está bloqueado por demasiados intentos?
        if (RateLimiter::tooManyAttempts($key, self::MAX_INTENTOS)) {
            return response()->json([
                'success'         => false,
                'bloqueado'       => true,
                'segundos_espera' => RateLimiter::availableIn($key),
                'message'         => 'Demasiados intentos fallidos. Intenta nuevamente en '
                                     . ceil(RateLimiter::availableIn($key) / 60) . ' minuto(s).',
            ], 429);
        }

        // ¿Contraseña correcta?
        if (!Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, self::BLOQUEO_MINUTOS * 60);
            $restantes     = max(0, self::MAX_INTENTOS - RateLimiter::attempts($key));
            $estaBloqueado = $restantes === 0;

            return response()->json([
                'success'            => false,
                'bloqueado'          => $estaBloqueado,
                'segundos_espera'    => $estaBloqueado ? RateLimiter::availableIn($key) : null,
                'intentos_restantes' => $restantes,
                'message'            => $estaBloqueado
                    ? 'Has agotado los intentos. Espera 5 minutos para intentar de nuevo.'
                    : "Contraseña incorrecta. Te quedan {$restantes} intento(s).",
            ], $estaBloqueado ? 429 : 422);
        }

        // Éxito: limpiar contador y conceder grace period.
        RateLimiter::clear($key);
        $expira = now()->addMinutes(self::GRACE_PERIOD_MINUTOS);
        session()->put("reauth_grace.{$action}", $expira->timestamp);

        return response()->json([
            'success'    => true,
            'expires_at' => $expira->toIso8601String(),
            'message'    => 'Verificación exitosa.',
        ]);
    }

    /**
     * Helper estático: verifica si el usuario actual tiene grace period
     * activo para una acción. Se usa desde otros controllers.
     */
    public static function tieneGracePeriod(string $action): bool
    {
        $timestamp = session()->get("reauth_grace.{$action}");
        if (!$timestamp) {
            return false;
        }
        return now()->timestamp < $timestamp;
    }

    /** Invalida el grace period (útil tras ejecutar una acción crítica). */
    public static function consumirGracePeriod(string $action): void
    {
        // El grace period NO se consume con cada uso; persiste hasta que
        // expire por tiempo. Este método existe por si en el futuro se
        // requiere invalidación inmediata por acción.
    }
}
