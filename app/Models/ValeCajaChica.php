<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Vale de salida de la Caja Chica.
 *
 * Estados y transiciones válidas:
 *   solicitada  → autorizada, rechazada, cancelada
 *   autorizada  → comprobada (al subir factura), cancelada
 *   rechazada   → (terminal)
 *   comprobada  → cancelada (caso especial, devuelve saldo)
 *   cancelada   → (terminal)
 *
 * El folio se autogenera en booted() con formato VCC-YYYY-NNNN.
 *
 * Reglas de saldo:
 *   - Al transicionar a "autorizada": se descuenta monto del fondo.
 *   - Al transicionar a "cancelada" desde autorizada/comprobada: se devuelve.
 *   - Al transicionar a "rechazada" desde solicitada: no afecta saldo.
 */
class ValeCajaChica extends Model
{
    protected $table = 'vale_caja_chica';
    protected $primaryKey = 'id_vale';

    protected $fillable = [
        'folio',
        'id_fondo',
        'solicitante_nombre',
        'concepto',
        'monto',
        'estatus',
        'solicitado_por',
        'autorizado_por',
        'autorizado_en',
        'motivo_rechazo',
        'factura_path',
        'factura_subida_en',
        'factura_subida_por',
        'cerrado_por',
        'cerrado_en',
        'cancelado_por',
        'cancelado_en',
    ];

    protected $casts = [
        'monto'              => 'decimal:2',
        'autorizado_en'      => 'datetime',
        'factura_subida_en'  => 'datetime',
        'cerrado_en'         => 'datetime',
        'cancelado_en'       => 'datetime',
    ];

    public const ESTADOS = [
        'solicitada'  => ['label' => 'Solicitada',          'tw' => 'gray'],
        'autorizada'  => ['label' => 'Autorizada',          'tw' => 'blue'],
        'rechazada'   => ['label' => 'Rechazada',           'tw' => 'rose'],
        'comprobada'  => ['label' => 'Comprobada',          'tw' => 'green'],
        'cancelada'   => ['label' => 'Cancelada',           'tw' => 'slate'],
    ];

    /** Transiciones permitidas (origen → destinos válidos). */
    public const TRANSICIONES = [
        'solicitada'  => ['autorizada', 'rechazada', 'cancelada'],
        'autorizada'  => ['comprobada', 'cancelada'],
        'rechazada'   => [],
        'comprobada'  => ['cancelada'],
        'cancelada'   => [],
    ];

    protected static function booted(): void
    {
        static::creating(function (ValeCajaChica $vale) {
            if (empty($vale->folio)) {
                $vale->folio = self::generarFolio();
            }
        });
    }

    public static function generarFolio(): string
    {
        $anio = date('Y');
        $ultimo = self::where('folio', 'like', "VCC-{$anio}-%")->count();
        return 'VCC-' . $anio . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }

    // ─── Relaciones ─────────────────────────────────────────────

    public function fondo(): BelongsTo
    {
        return $this->belongsTo(FondoCajaChica::class, 'id_fondo');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    public function autorizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizado_por');
    }

    public function facturaSubidaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'factura_subida_por');
    }

    public function cierre(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    public function cancelador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CajaChicaLog::class, 'vale_id');
    }

    // ─── Helpers de estado ──────────────────────────────────────

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estatus]['label'] ?? $this->estatus;
    }

    public function getEstadoColorAttribute(): string
    {
        return self::ESTADOS[$this->estatus]['tw'] ?? 'gray';
    }

    public function puedeTransicionarA(string $nuevoEstado): bool
    {
        return in_array($nuevoEstado, self::TRANSICIONES[$this->estatus] ?? [], true);
    }

    public function getEsEditableAttribute(): bool
    {
        // Solo se puede editar mientras esté en "solicitada" (antes de autorizar).
        return $this->estatus === 'solicitada';
    }

    public function getEsTerminalAttribute(): bool
    {
        return in_array($this->estatus, ['rechazada', 'cancelada'], true);
    }

    public function getTieneFacturaAttribute(): bool
    {
        return !empty($this->factura_path);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopePendientesAutorizacion($q)
    {
        return $q->where('estatus', 'solicitada')->orderBy('created_at');
    }

    public function scopeAutorizadosSinFactura($q)
    {
        return $q->where('estatus', 'autorizada')->whereNull('factura_path');
    }

    public function scopeDelMes($q, ?int $anio = null, ?int $mes = null)
    {
        $anio = $anio ?? now()->year;
        $mes  = $mes  ?? now()->month;
        return $q->whereYear('created_at', $anio)->whereMonth('created_at', $mes);
    }
}
