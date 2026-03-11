<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carrera';
    protected $primaryKey = 'id_carrera';
    public $timestamps = false;

    protected $fillable = ['id_director', 'nombre_carrera', 'clave_carrera'];

    public function director()
    {
        return $this->belongsTo(Docente::class, 'id_director', 'id_docente');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_carrera');
    }

    public function materias()
    {
        return $this->hasMany(Materia::class, 'id_carrera');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_carrera');
    }
}
