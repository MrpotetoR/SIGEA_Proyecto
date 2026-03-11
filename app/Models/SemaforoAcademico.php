<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemaforoAcademico extends Model
{
    protected $table = 'semaforo_academico';
    protected $primaryKey = 'id_semaforo';
    public $timestamps = false;

    protected $fillable = [
        'id_alumno', 'id_ciclo', 'nivel',
        'promedio_calificaciones', 'porcentaje_asistencia',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function cicloEscolar()
    {
        return $this->belongsTo(CicloEscolar::class, 'id_ciclo');
    }

    public function getColorAttribute(): string
    {
        return match ($this->nivel) {
            'verde' => 'green',
            'amarillo' => 'yellow',
            'rojo' => 'red',
            default => 'gray',
        };
    }
}
