<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Pedido de tienda institucional.
 *
 * Estados y transiciones validas:
 *   pendiente_pago  → vaucher_enviado, cancelado
 *   vaucher_enviado → aprobado, pendiente_pago (rechazo), cancelado
 *   aprobado        → listo_recoger, cancelado
 *   listo_recoger   → entregado, cancelado
 *   entregado       → (terminal)
 *   cancelado       → (terminal)
 *
 * El folio se autogenera en boot() con formato PED-YYYY-NNNN.
 */
class Pedido extends Model
{
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';

    protected $fillable = [
        'folio', 'user_id', 'nivel_educativo', 'total', 'estado',
        'vaucher_path', 'vaucher_subido_en', 'motivo_rechazo',
        'revisado_por', 'revisado_en',
        'fecha_listo_recoger', 'entregado_por', 'entregado_en',
    ];

    protected $casts = [
        'total'                => 'decimal:2',
        'vaucher_subido_en'    => 'datetime',
        'revisado_en'          => 'datetime',
        'fecha_listo_recoger'  => 'datetime',
        'entregado_en'         => 'datetime',
    ];

    public const ESTADOS = [
        'pendiente_pago'  => ['label' => 'Pendiente de pago',  'color' => 'gray'],
        'vaucher_enviado' => ['label' => 'Váucher enviado',     'color' => 'amber'],
        'aprobado'        => ['label' => 'Pago aprobado',       'color' => 'blue'],
        'listo_recoger'   => ['label' => 'Listo para recoger',  'color' => 'green'],
        'entregado'       => ['label' => 'Entregado',           'color' => 'emerald'],
        'cancelado'       => ['label' => 'Cancelado',           'color' => 'red'],
    ];

    /** Transiciones permitidas por estado actual. */
    public const TRANSICIONES = [
        'pendiente_pago'  => ['vaucher_enviado', 'cancelado'],
        'vaucher_enviado' => ['aprobado', 'pendiente_pago', 'cancelado'],
        'aprobado'        => ['listo_recoger', 'cancelado'],
        'listo_recoger'   => ['entregado', 'cancelado'],
        'entregado'       => [],
        'cancelado'       => [],
    ];

    protected static function booted(): void
    {
        static::creating(function (Pedido $pedido) {
            if (empty($pedido->folio)) {
                $pedido->folio = self::generarFolio();
            }
        });
    }

    public static function generarFolio(): string
    {
        $anio = date('Y');
        $ultimo = self::where('folio', 'like', "PED-{$anio}-%")->count();
        return 'PED-' . $anio . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }

    // ─── Relaciones ─────────────────────────────────────

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'id_pedido');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(PedidoEstadoHistorial::class, 'id_pedido')->orderBy('created_at');
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function entregador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }

    // ─── Helpers de estado ──────────────────────────────

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado]['label'] ?? $this->estado;
    }

    public function getEstadoColorAttribute(): string
    {
        return self::ESTADOS[$this->estado]['color'] ?? 'gray';
    }

    public function puedeTransicionarA(string $nuevoEstado): bool
    {
        return in_array($nuevoEstado, self::TRANSICIONES[$this->estado] ?? [], true);
    }

    /** Estados visibles desde el lado del alumno (sin transiciones admin). */
    public function getEsTerminalAttribute(): bool
    {
        return in_array($this->estado, ['entregado', 'cancelado'], true);
    }

    // ─── Scopes ─────────────────────────────────────────

    public function scopePendientesValidacion($q)
    {
        return $q->where('estatus', 'vaucher_enviado')
                 ->orderBy('vaucher_subido_en');
    }

    public function scopeDelUsuario($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }
}
