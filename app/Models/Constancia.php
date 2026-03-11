<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constancia extends Model
{
    protected $table = 'constancia';
    protected $primaryKey = 'id_constancia';

    protected $fillable = ['id_alumno', 'generada_por', 'tipo', 'archivo_url', 'fecha_emision'];

    protected function casts(): array
    {
        return ['fecha_emision' => 'date'];
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function generadaPor()
    {
        return $this->belongsTo(User::class, 'generada_por');
    }
}
