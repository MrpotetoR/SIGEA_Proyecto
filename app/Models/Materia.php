<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materia';
    protected $primaryKey = 'id_materia';
    public $timestamps = false;

    protected $fillable = ['id_carrera', 'nombre_materia', 'cuatrimestre', 'horas_semana'];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_materia');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_materia');
    }
}
