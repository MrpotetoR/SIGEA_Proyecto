<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCuatrimestre extends Model
{
    protected $table = 'pago_cuatrimestre';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_alumno', 'cuatrimestre', 'baucher_path', 'subido_en',
        'estatus', 'comentario_rechazo', 'revisado_por', 'revisado_en', 'subido_por',
    ];

    protected $casts = [
        'revisado_en' => 'datetime',
    ];

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
