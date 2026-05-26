<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fondo único global de Caja Chica.
 *
 * El sistema mantiene UN solo registro activo. El admin configura monto_base,
 * topes y umbrales; el saldo_actual se modifica automáticamente al autorizar
 * vales (resta), al cancelar vales autorizados (suma) o al reponer fondo (suma).
 *
 * Semáforo (configurable):
 *   verde     → saldo_actual >  umbral_verde
 *   amarillo  → saldo_actual >  umbral_amarillo  &&  saldo_actual <= umbral_verde
 *   rojo      → saldo_actual <= umbral_amarillo
 */
class FondoCajaChica extends Model
{
    protected $table = 'fondo_caja_chica';
    protected $primaryKey = 'id_fondo';

    protected $fillable = [
        'monto_base',
        'saldo_actual',
        'tope_vale_individual',
        'umbral_verde',
        'umbral_amarillo',
        'configurado_por',
        'configurado_en',
    ];

    protected $casts = [
        'monto_base'           => 'decimal:2',
        'saldo_actual'         => 'decimal:2',
        'tope_vale_individual' => 'decimal:2',
        'umbral_verde'         => 'decimal:2',
        'umbral_amarillo'      => 'decimal:2',
        'configurado_en'       => 'datetime',
    ];

    public const SEMAFORO_COLORES = [
        'verde'    => ['label' => 'Saldo saludable',  'tw' => 'green'],
        'amarillo' => ['label' => 'Saldo intermedio', 'tw' => 'amber'],
        'rojo'     => ['label' => 'Saldo crítico',    'tw' => 'red'],
    ];

    // ─── Relaciones ─────────────────────────────────────────────

    public function configurador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'configurado_por');
    }

    public function vales(): HasMany
    {
        return $this->hasMany(ValeCajaChica::class, 'id_fondo');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CajaChicaLog::class, 'fondo_id');
    }

    // ─── Helpers de semáforo y saldo ────────────────────────────

    /**
     * Devuelve 'verde', 'amarillo' o 'rojo' según los umbrales configurados.
     */
    public function getNivelSemaforoAttribute(): string
    {
        $saldo = (float) $this->saldo_actual;
        if ($saldo > (float) $this->umbral_verde)    return 'verde';
        if ($saldo > (float) $this->umbral_amarillo) return 'amarillo';
        return 'rojo';
    }

    public function getSemaforoColorTwAttribute(): string
    {
        return self::SEMAFORO_COLORES[$this->nivel_semaforo]['tw'] ?? 'gray';
    }

    public function getSemaforoLabelAttribute(): string
    {
        return self::SEMAFORO_COLORES[$this->nivel_semaforo]['label'] ?? '—';
    }

    /**
     * Cuánto falta reponer para volver al monto_base.
     * Negativo o cero significa que está completo.
     */
    public function getFaltanteReponerAttribute(): float
    {
        return max(0, (float) $this->monto_base - (float) $this->saldo_actual);
    }

    /**
     * Porcentaje de saldo respecto al monto base (0–100).
     */
    public function getPorcentajeSaldoAttribute(): float
    {
        $base = (float) $this->monto_base;
        if ($base <= 0) return 0;
        return round(((float) $this->saldo_actual / $base) * 100, 1);
    }

    /**
     * ¿Hay suficiente saldo para autorizar un vale por X monto?
     */
    public function tieneSaldoPara(float $monto): bool
    {
        return (float) $this->saldo_actual >= $monto;
    }

    /**
     * ¿El monto excede el tope individual configurado (si lo hay)?
     */
    public function excedeTopeIndividual(float $monto): bool
    {
        if (is_null($this->tope_vale_individual)) {
            return false;
        }
        return $monto > (float) $this->tope_vale_individual;
    }

    /**
     * Devuelve la instancia activa del fondo (singleton lógico).
     * Si no existe ningún registro, lo crea con valores por defecto.
     */
    public static function actual(): self
    {
        return self::firstOrCreate(
            [],
            [
                'monto_base'      => 0,
                'saldo_actual'    => 0,
                'umbral_verde'    => 2000,
                'umbral_amarillo' => 1000,
            ]
        );
    }
}
