<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCuatrimestre extends Model
{
    protected $table = 'pago_cuatrimestre';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_alumno', 'cuatrimestre', 'monto', 'baucher_path', 'subido_en',
        'estatus', 'comentario_rechazo', 'revisado_por', 'revisado_en', 'subido_por',
    ];

    protected $casts = [
        'monto'       => 'decimal:2',
        'revisado_en' => 'datetime',
    ];

    /**
     * Tarifa default de colegiatura desde ConfiguracionInstitucional.
     * Si no está configurada, devuelve 0 (debe ser configurada por admin).
     */
    public static function tarifaDefault(): float
    {
        $valor = \App\Models\ConfiguracionInstitucional::get('colegiatura.monto_default', 0);
        return (float) $valor;
    }

    /**
     * Devuelve el monto a cobrar: el del pago si está capturado, si no, la
     * tarifa default.
     */
    public function getMontoEfectivoAttribute(): float
    {
        return $this->monto !== null
            ? (float) $this->monto
            : self::tarifaDefault();
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function revisor()
    {
        return $this->belongsTo(\App\Models\User::class, 'revisado_por');
    }

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'subido_por');
    }

    public function estaPendiente(): bool { return $this->estatus === 'pendiente'; }
    public function estaAprobado(): bool  { return $this->estatus === 'aprobado'; }
    public function estaRechazado(): bool { return $this->estatus === 'rechazado'; }
}
