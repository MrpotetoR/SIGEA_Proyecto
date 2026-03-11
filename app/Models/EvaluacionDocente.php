<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionDocente extends Model
{
    protected $table = 'evaluacion_docente';
    protected $primaryKey = 'id_evaluacion';
    public $timestamps = false;

    protected $fillable = ['id_docente', 'id_alumno', 'id_ciclo', 'calificacion_promedio', 'comentarios'];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function cicloEscolar()
    {
        return $this->belongsTo(CicloEscolar::class, 'id_ciclo');
    }

    public function respuestas()
    {
        return $this->hasMany(EncuestaRespuesta::class, 'id_evaluacion');
    }
}
