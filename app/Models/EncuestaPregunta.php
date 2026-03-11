<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncuestaPregunta extends Model
{
    protected $table = 'encuesta_pregunta';
    protected $primaryKey = 'id_pregunta';
    public $timestamps = false;

    protected $fillable = ['texto_pregunta', 'orden', 'activa'];

    protected function casts(): array
    {
        return ['activa' => 'boolean'];
    }

    public function respuestas()
    {
        return $this->hasMany(EncuestaRespuesta::class, 'id_pregunta');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true)->orderBy('orden');
    }
}
