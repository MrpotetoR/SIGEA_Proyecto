<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencia';
    protected $primaryKey = 'id_asistencia';
    public $timestamps = false;

    protected $fillable = ['id_alumno', 'id_horario', 'fecha', 'estatus'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario');
    }
}
