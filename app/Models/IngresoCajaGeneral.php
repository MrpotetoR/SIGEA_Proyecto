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

    /**
     * Cada tipo expone un SVG path (Heroicons outline) en lugar de emoji, para
     * mantener consistencia visual con el resto del sidebar/UI del sistema.
     * El path se renderiza inline en Blade dentro de un <svg> con
     * viewBox="0 0 24 24" stroke="currentColor".
     */
    public const TIPOS = [
        'colegiatura' => [
            'label' => 'Colegiatura',
            'tw'    => 'blue',
            'icon_path' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
        ],
        'producto'    => [
            'label' => 'Tienda',
            'tw'    => 'purple',
            'icon_path' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
        ],
        'tramite'     => [
            'label' => 'Trámite',
            'tw'    => 'amber',
            'icon_path' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
        'otro'        => [
            'label' => 'Otro',
            'tw'    => 'gray',
            'icon_path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
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

    /**
     * Devuelve el SVG path del icono asociado al tipo (Heroicons outline).
     * Pensado para renderizar con: <svg ...><path d="{{ $i->tipo_icon_path }}"/></svg>
     */
    public function getTipoIconPathAttribute(): string
    {
        return self::TIPOS[$this->tipo]['icon_path']
            ?? 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
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
