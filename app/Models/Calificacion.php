<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificacion';
    protected $primaryKey = 'id_calificacion';
    public $timestamps = false;

    protected $fillable = ['id_alumno', 'id_materia', 'id_ciclo', 'parcial', 'calificacion'];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia');
    }

    public function cicloEscolar()
    {
        return $this->belongsTo(CicloEscolar::class, 'id_ciclo');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('calificacion', '>=', 7);
    }
}
