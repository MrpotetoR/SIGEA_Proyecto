<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripcion';
    protected $primaryKey = 'id_inscripcion';
    public $timestamps = false;

    protected $fillable = ['id_alumno', 'id_grupo', 'fecha_inscripcion'];

    protected function casts(): array
    {
        return ['fecha_inscripcion' => 'date'];
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }
}
