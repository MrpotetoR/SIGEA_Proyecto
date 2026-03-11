<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncuestaRespuesta extends Model
{
    protected $table = 'encuesta_respuesta';
    protected $primaryKey = 'id_respuesta';
    public $timestamps = false;

    protected $fillable = ['id_evaluacion', 'id_pregunta', 'valor', 'comentarios'];

    public function evaluacion()
    {
        return $this->belongsTo(EvaluacionDocente::class, 'id_evaluacion');
    }

    public function pregunta()
    {
        return $this->belongsTo(EncuestaPregunta::class, 'id_pregunta');
    }
}
