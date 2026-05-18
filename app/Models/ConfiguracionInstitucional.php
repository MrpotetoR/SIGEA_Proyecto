<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Configuración institucional editable por el Administrador.
 *
 * Acceso simplificado vía helpers estáticos:
 *   ConfiguracionInstitucional::get('tienda.cuenta_clabe')
 *   ConfiguracionInstitucional::set('tienda.cuenta_banco', 'BBVA')
 *
 * Los valores se cachean en memoria para no consultar BD en cada request.
 */
class ConfiguracionInstitucional extends Model
{
    protected $table = 'configuracion_institucional';
    protected $primaryKey = 'id_configuracion';

    protected $fillable = ['clave', 'valor', 'descripcion', 'grupo'];

    /** Claves vigentes del sistema. Agregar nuevas aquí cuando se necesite. */
    public const CLAVES_TIENDA = [
        'tienda.cuenta_banco'         => 'Banco institucional (ej. BBVA)',
        'tienda.cuenta_titular'       => 'Titular de la cuenta',
        'tienda.cuenta_numero'        => 'Numero de cuenta',
        'tienda.cuenta_clabe'         => 'CLABE interbancaria',
        'tienda.referencia_prefijo'   => 'Prefijo de referencia (ej. UDEA-)',
        'tienda.ubicacion_entrega'    => 'Ubicacion fisica de entrega',
        'tienda.horario_entrega'      => 'Horario de atencion para entregas',
        'tienda.instrucciones_pago'   => 'Texto adicional que vera el alumno',
    ];

    /** Cache key prefix. */
    private const CACHE_PREFIX = 'config_inst:';

    /** Obtiene un valor por clave, con fallback. */
    public static function get(string $clave, mixed $default = null): mixed
    {
        return Cache::remember(self::CACHE_PREFIX . $clave, 600, function () use ($clave, $default) {
            $row = self::where('clave', $clave)->first();
            return $row?->valor ?? $default;
        });
    }

    /** Establece o crea un valor. Limpia su cache. */
    public static function set(string $clave, mixed $valor, ?string $descripcion = null, string $grupo = 'general'): self
    {
        Cache::forget(self::CACHE_PREFIX . $clave);

        return self::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor, 'descripcion' => $descripcion, 'grupo' => $grupo]
        );
    }

    /** Helper para la cuenta bancaria completa. Devuelve null si no esta configurada. */
    public static function cuentaBancaria(): ?array
    {
        $clabe = self::get('tienda.cuenta_clabe');
        if (!$clabe) return null;

        return [
            'banco'   => self::get('tienda.cuenta_banco', 'No especificado'),
            'titular' => self::get('tienda.cuenta_titular', 'UDEA'),
            'numero'  => self::get('tienda.cuenta_numero'),
            'clabe'   => $clabe,
        ];
    }
}
