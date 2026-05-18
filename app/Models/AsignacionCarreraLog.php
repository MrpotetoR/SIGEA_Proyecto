<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Registro de auditoría de una asignación/desasignación/reasignación
 * de carrera a un Gestor Escolar.
 */
class AsignacionCarreraLog extends Model
{
    protected $table = 'asignacion_carrera_log';

    public $timestamps = false; // solo created_at, manejado por DB.

    protected $fillable = [
        'user_id',
        'gestor_afectado_id',
        'id_carrera',
        'accion',
        'motivo',
        'motivo_personalizado',
        'ip',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public const ACCIONES = [
        'asignar'    => 'Asignación',
        'reasignar'  => 'Reasignación',
        'desasignar' => 'Desasignación',
    ];

    public const MOTIVOS = [
        'reorganizacion'      => 'Reorganización administrativa',
        'cambio_responsable'  => 'Cambio de responsable',
        'alta_carrera'        => 'Alta de nueva carrera',
        'cobertura_temporal'  => 'Cobertura temporal',
        'redistribucion'      => 'Redistribución de carga laboral',
        'otro'                => 'Otro',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gestorAfectado()
    {
        return $this->belongsTo(GestorEscolar::class, 'gestor_afectado_id', 'id_personal');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera', 'id_carrera');
    }

    public function getMotivoLegibleAttribute(): string
    {
        if ($this->motivo === 'otro' && $this->motivo_personalizado) {
            return $this->motivo_personalizado;
        }
        return self::MOTIVOS[$this->motivo] ?? $this->motivo;
    }

    public function getAccionLegibleAttribute(): string
    {
        return self::ACCIONES[$this->accion] ?? $this->accion;
    }
}
