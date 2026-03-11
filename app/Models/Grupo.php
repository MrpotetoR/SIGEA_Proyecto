<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupo';
    protected $primaryKey = 'id_grupo';
    public $timestamps = false;

    protected $fillable = ['id_carrera', 'id_ciclo', 'id_tutor', 'cuatrimestre', 'clave_grupo'];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    public function cicloEscolar()
    {
        return $this->belongsTo(CicloEscolar::class, 'id_ciclo');
    }

    public function tutorDocente()
    {
        return $this->belongsTo(Docente::class, 'id_tutor', 'id_docente');
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_grupo');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_grupo');
    }

    public function alumnos()
    {
        return $this->hasManyThrough(
            Alumno::class,
            Inscripcion::class,
            'id_grupo',
            'id_alumno',
            'id_grupo',
            'id_alumno'
        );
    }
}
