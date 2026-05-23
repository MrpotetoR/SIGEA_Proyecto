<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Registro de auditoría del módulo Caja Chica.
 *
 * Cada acción sensible deja huella aquí: quién (user_id + ip + user_agent),
 * cuándo (created_at), qué (acción + monto_antes/después), por qué (motivo)
 * y con qué soporte (evidencia_path).
 *
 * Patrón replicado de AsignacionCarreraLog.
 */
class CajaChicaLog extends Model
{
    protected $table = 'caja_chica_log';

    public $timestamps = false; // solo created_at, manejado por DB.

    protected $fillable = [
        'user_id',
        'vale_id',
        'fondo_id',
        'gestor_afectado_id',
        'accion',
        'motivo',
        'motivo_personalizado',
        'monto_antes',
        'monto_despues',
        'evidencia_path',
        'ip',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at'    => 'datetime',
            'monto_antes'   => 'decimal:2',
            'monto_despues' => 'decimal:2',
        ];
    }

    public const ACCIONES = [
        'otorgar_permiso'      => 'Otorgar permiso',
        'revocar_permiso'      => 'Revocar permiso',
        'configurar_tope'      => 'Configurar tope',
        'configurar_umbrales'  => 'Configurar umbrales',
        'crear_vale'           => 'Crear vale',
        'editar_vale'          => 'Editar vale',
        'autorizar_vale'       => 'Autorizar vale',
        'rechazar_vale'        => 'Rechazar vale',
        'subir_factura'        => 'Subir factura',
        'cerrar_vale'          => 'Cerrar vale',
        'cancelar_vale'        => 'Cancelar vale',
        'reponer_fondo'        => 'Reponer fondo',
    ];

    public const MOTIVOS = [
        'emergencia'           => 'Emergencia',
        'gasto_operativo'      => 'Gasto operativo',
        'transporte'           => 'Transporte / Combustible',
        'reparacion_menor'     => 'Reparación menor',
        'tramite_urgente'      => 'Trámite urgente',
        'reposicion_mensual'   => 'Reposición mensual',
        'ajuste_configuracion' => 'Ajuste de configuración',
        'reorganizacion'       => 'Reorganización administrativa',
        'otro'                 => 'Otro',
    ];

    // ─── Relaciones ─────────────────────────────────────────────

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vale(): BelongsTo
    {
        return $this->belongsTo(ValeCajaChica::class, 'vale_id', 'id_vale');
    }

    public function fondo(): BelongsTo
    {
        return $this->belongsTo(FondoCajaChica::class, 'fondo_id', 'id_fondo');
    }

    public function gestorAfectado(): BelongsTo
    {
        return $this->belongsTo(GestorEscolar::class, 'gestor_afectado_id', 'id_personal');
    }

    // ─── Accessors ──────────────────────────────────────────────

    public function getAccionLegibleAttribute(): string
    {
        return self::ACCIONES[$this->accion] ?? $this->accion;
    }

    public function getMotivoLegibleAttribute(): string
    {
        if ($this->motivo === 'otro' && $this->motivo_personalizado) {
            return $this->motivo_personalizado;
        }
        return self::MOTIVOS[$this->motivo] ?? $this->motivo;
    }

    /**
     * Devuelve el texto de la columna MONTO de la tabla del historial.
     * - Acciones sobre vale: "$ 800.00"
     * - Acciones que cambian tope/umbral: "$ 2,500.00 → $ 3,000.00"
     * - Acciones de permiso: "—"
     */
    public function getMontoLegibleAttribute(): string
    {
        if (in_array($this->accion, ['otorgar_permiso', 'revocar_permiso'], true)) {
            return '—';
        }

        $antes    = $this->monto_antes;
        $despues  = $this->monto_despues;

        // Caso "antes → después" (ajustes de configuración o reposición)
        if (!is_null($antes) && !is_null($despues) && (float) $antes !== (float) $despues) {
            return '$ ' . number_format((float) $antes, 2) . ' → $ ' . number_format((float) $despues, 2);
        }

        // Caso monto único
        $monto = $despues ?? $antes;
        return !is_null($monto) ? '$ ' . number_format((float) $monto, 2) : '—';
    }

    public function getEvidenciaUrlAttribute(): ?string
    {
        return $this->evidencia_path
            ? Storage::url($this->evidencia_path)
            : null;
    }

    /**
     * Etiqueta corta del objeto afectado para la columna VALE/FONDO.
     */
    public function getObjetoAfectadoAttribute(): string
    {
        if ($this->vale_id && $this->vale) {
            return $this->vale->folio;
        }
        if ($this->fondo_id) {
            return 'Fondo ' . optional($this->created_at)->translatedFormat('F Y');
        }
        if ($this->gestor_afectado_id && $this->gestorAfectado) {
            return $this->gestorAfectado->nombre_completo;
        }
        return '—';
    }
}
