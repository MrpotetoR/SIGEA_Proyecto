<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Manejo centralizado del "contexto educativo" del Gestor Escolar.
 *
 * El sistema opera en dos areas:
 *   - 'universidad'    : carreras, planes de estudio, cuatrimestres
 *   - 'bachillerato'   : grupos por grado, semestres
 *
 * El contexto activo se guarda en sesion y es leido por:
 *   - El scope global NivelEducativoScope (filtra queries automaticamente).
 *   - El sidebar/header (cambia colores e items visibles).
 *   - Las validaciones en formularios.
 */
class ContextoEducativo
{
    public const UNIVERSIDAD  = 'universidad';
    public const BACHILLERATO = 'bachillerato';
    public const SESSION_KEY  = 'contexto_educativo';

    /** Niveles validos. */
    public const NIVELES = [self::UNIVERSIDAD, self::BACHILLERATO];

    /** Devuelve el contexto activo o null si aun no se ha seleccionado. */
    public static function actual(): ?string
    {
        return session(self::SESSION_KEY);
    }

    /** Establece el contexto en sesion. Valida que sea uno de los niveles soportados. */
    public static function establecer(string $nivel): void
    {
        if (!in_array($nivel, self::NIVELES, true)) {
            throw new \InvalidArgumentException("Nivel educativo invalido: {$nivel}");
        }
        session([self::SESSION_KEY => $nivel]);
    }

    /** Limpia el contexto (forzara la pantalla de seleccion en el proximo request). */
    public static function limpiar(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * Devuelve la lista de niveles a los que el usuario actual tiene acceso,
     * segun los permisos `gestor.universidad` y `gestor.bachillerato`.
     * El admin tiene acceso a ambos siempre.
     */
    public static function nivelesDisponiblesParaUsuario(): array
    {
        $user = Auth::user();
        if (!$user) return [];

        if ($user->hasRole('admin')) {
            return self::NIVELES;
        }

        $disponibles = [];
        if ($user->can('gestor.universidad'))  $disponibles[] = self::UNIVERSIDAD;
        if ($user->can('gestor.bachillerato')) $disponibles[] = self::BACHILLERATO;

        return $disponibles;
    }

    /** True si el usuario puede operar en ambos niveles. */
    public static function tieneAmbos(): bool
    {
        return count(self::nivelesDisponiblesParaUsuario()) === 2;
    }

    /**
     * Si el usuario solo tiene un nivel disponible, lo devuelve para auto-seleccion.
     * Si tiene varios o ninguno, devuelve null.
     */
    public static function nivelUnico(): ?string
    {
        $disponibles = self::nivelesDisponiblesParaUsuario();
        return count($disponibles) === 1 ? $disponibles[0] : null;
    }

    /** Color tema asociado al contexto (para banner, pills, badges). */
    public static function color(?string $nivel = null): array
    {
        $nivel = $nivel ?? self::actual();
        return match ($nivel) {
            self::BACHILLERATO => [
                'hex'    => '#F59E0B',
                'tw_bg'  => 'bg-amber-500',
                'tw_text'=> 'text-amber-700',
                'tw_ring'=> 'ring-amber-400',
                'icono'  => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
                'label'  => 'Bachillerato',
            ],
            default => [
                'hex'    => '#0606F0',
                'tw_bg'  => 'bg-[#0606F0]',
                'tw_text'=> 'text-[#0606F0]',
                'tw_ring'=> 'ring-[#0606F0]',
                'icono'  => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
                'label'  => 'Universidad',
            ],
        };
    }
}
