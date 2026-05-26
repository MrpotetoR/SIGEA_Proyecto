<?php

namespace App\Models;

use App\Models\ConfiguracionInstitucional;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Cobro manual de un trámite administrativo (kárdex, constancia, etc.).
 *
 * Cada cobro de trámite crea automáticamente una fila en
 * `ingreso_caja_general` con referencia a este registro, para que aparezca
 * en los reportes unificados de Caja General.
 *
 * Las tarifas default vienen de `configuracion_institucional`:
 *   tramite.kardex.precio, tramite.constancia_estudios.precio, etc.
 * Si el admin las configura, el form de cobro las precarga.
 */
class CobroTramite extends Model
{
    protected $table = 'cobro_tramite';
    protected $primaryKey = 'id_cobro';

    protected $fillable = [
        'folio',
        'tipo_tramite',
        'concepto_personalizado',
        'monto',
        'alumno_id',
        'cobrado_por',
        'metodo_pago',
        'referencia_externa',
        'evidencia_path',
        'estatus',
        'cobrado_en',
        'cancelado_en',
        'cancelado_por',
        'motivo_cancelacion',
    ];

    protected $casts = [
        'monto'         => 'decimal:2',
        'cobrado_en'    => 'datetime',
        'cancelado_en'  => 'datetime',
    ];

    public const TIPOS_TRAMITE = [
        'kardex'                  => 'Kárdex académico',
        'constancia_estudios'     => 'Constancia de estudios',
        'constancia_terminacion'  => 'Constancia de terminación',
        'constancia_no_adeudo'    => 'Constancia de no adeudo',
        'otro'                    => 'Otro trámite',
    ];

    public const CLAVES_TARIFA = [
        'kardex'                  => 'tramite.kardex.precio',
        'constancia_estudios'     => 'tramite.constancia_estudios.precio',
        'constancia_terminacion'  => 'tramite.constancia_terminacion.precio',
        'constancia_no_adeudo'    => 'tramite.constancia_no_adeudo.precio',
    ];

    protected static function booted(): void
    {
        static::creating(function (CobroTramite $c) {
            if (empty($c->folio)) {
                $c->folio = self::generarFolio();
            }
        });
    }

    public static function generarFolio(): string
    {
        $anio = date('Y');
        $ultimo = self::where('folio', 'like', "CTR-{$anio}-%")->count();
        return 'CTR-' . $anio . '-' . str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Devuelve la tarifa configurada para un tipo de trámite, o null si no
     * está definida. Se usa en el form para precargar el campo monto.
     */
    public static function tarifaDefault(string $tipoTramite): ?float
    {
        $clave = self::CLAVES_TARIFA[$tipoTramite] ?? null;
        if (!$clave) return null;

        $valor = ConfiguracionInstitucional::get($clave);
        return $valor !== null ? (float) $valor : null;
    }

    // ─── Relaciones ────────────────────────────────────────

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id', 'id_alumno');
    }

    public function cobrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cobrado_por');
    }

    public function cancelador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    /**
     * Ingreso vinculado en la tabla unificada (puede ser null si fue cancelado).
     */
    public function ingreso()
    {
        return $this->hasOne(IngresoCajaGeneral::class, 'referencia_id')
            ->where('referencia_tipo', 'cobro_tramite');
    }

    // ─── Accessors ─────────────────────────────────────────

    public function getConceptoLegibleAttribute(): string
    {
        if ($this->tipo_tramite === 'otro' && $this->concepto_personalizado) {
            return $this->concepto_personalizado;
        }
        return self::TIPOS_TRAMITE[$this->tipo_tramite] ?? $this->tipo_tramite;
    }

    public function getEstaCanceladoAttribute(): bool
    {
        return $this->estatus === 'cancelado';
    }
}
