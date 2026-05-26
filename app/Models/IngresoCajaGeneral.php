<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Evento de ingreso de la Caja General.
 *
 * Cada fila representa un ingreso registrado en el sistema, proveniente de
 * 4 fuentes posibles:
 *   - colegiatura: alumno aprobado por gestor (referencia → pago_cuatrimestre)
 *   - producto:    pedido de tienda aprobado (referencia → pedido)
 *   - tramite:     cobro manual de kárdex/constancia (referencia → cobro_tramite)
 *   - otro:        ingreso manual capturado por admin (sin referencia)
 *
 * Para reversas, el registro NO se borra: se marca con cancelado_en para
 * mantener trazabilidad. Las queries de reporte excluyen cancelados por
 * default (scope `vigentes`).
 */
class IngresoCajaGeneral extends Model
{
    protected $table = 'ingreso_caja_general';
    protected $primaryKey = 'id_ingreso';

    protected $fillable = [
        'folio',
        'tipo',
        'referencia_tipo',
        'referencia_id',
        'concepto',
        'monto',
        'alumno_id',
        'user_id',
        'metodo_pago',
        'fecha_cobro',
        'cancelado_en',
        'cancelado_por',
        'motivo_cancelacion',
    ];

    protected $casts = [
        'monto'        => 'decimal:2',
        'fecha_cobro'  => 'datetime',
        'cancelado_en' => 'datetime',
    ];

    public const TIPOS = [
        'colegiatura' => ['label' => 'Colegiatura',    'tw' => 'blue',    'icon' => '🎓'],
        'producto'    => ['label' => 'Tienda',         'tw' => 'purple',  'icon' => '🛍'],
        'tramite'     => ['label' => 'Trámite',        'tw' => 'amber',   'icon' => '📋'],
        'otro'        => ['label' => 'Otro',           'tw' => 'gray',    'icon' => '💰'],
    ];

    public const METODOS_PAGO = [
        'transferencia' => 'Transferencia',
        'efectivo'      => 'Efectivo',
        'tarjeta'       => 'Tarjeta',
        'otro'          => 'Otro',
    ];

    protected static function booted(): void
    {
        static::creating(function (IngresoCajaGeneral $i) {
            if (empty($i->folio)) {
                $i->folio = self::generarFolio();
            }
        });
    }

    public static function generarFolio(): string
    {
        $anio = date('Y');
        $ultimo = self::where('folio', 'like', "ICG-{$anio}-%")->count();
        return 'ICG-' . $anio . '-' . str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);
    }

    // ─── Relaciones ────────────────────────────────────────

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id', 'id_alumno');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cancelador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    /**
     * Resuelve la entidad origen del ingreso (PagoCuatrimestre, Pedido o CobroTramite).
     * Retorna null si es ingreso manual o si la referencia ya no existe.
     */
    public function origen()
    {
        return match ($this->referencia_tipo) {
            'pago_cuatrimestre' => PagoCuatrimestre::find($this->referencia_id),
            'pedido'            => Pedido::find($this->referencia_id),
            'cobro_tramite'     => CobroTramite::find($this->referencia_id),
            default             => null,
        };
    }

    // ─── Accessors ─────────────────────────────────────────

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo]['label'] ?? $this->tipo;
    }

    public function getTipoColorAttribute(): string
    {
        return self::TIPOS[$this->tipo]['tw'] ?? 'gray';
    }

    public function getTipoIconoAttribute(): string
    {
        return self::TIPOS[$this->tipo]['icon'] ?? '💰';
    }

    public function getMetodoPagoLabelAttribute(): string
    {
        return self::METODOS_PAGO[$this->metodo_pago] ?? $this->metodo_pago;
    }

    public function getEstaCanceladoAttribute(): bool
    {
        return !is_null($this->cancelado_en);
    }

    // ─── Scopes ────────────────────────────────────────────

    public function scopeVigentes($q)
    {
        return $q->whereNull('cancelado_en');
    }

    public function scopeDelTipo($q, string $tipo)
    {
        return $q->where('tipo', $tipo);
    }

    public function scopeEntreFechas($q, ?string $desde, ?string $hasta)
    {
        if ($desde) $q->whereDate('fecha_cobro', '>=', $desde);
        if ($hasta) $q->whereDate('fecha_cobro', '<=', $hasta);
        return $q;
    }

    public function scopeDeHoy($q)
    {
        return $q->whereDate('fecha_cobro', today());
    }

    public function scopeDeLaSemana($q)
    {
        return $q->whereBetween('fecha_cobro', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeDelMes($q, ?int $anio = null, ?int $mes = null)
    {
        $anio = $anio ?? now()->year;
        $mes  = $mes  ?? now()->month;
        return $q->whereYear('fecha_cobro', $anio)->whereMonth('fecha_cobro', $mes);
    }
}
