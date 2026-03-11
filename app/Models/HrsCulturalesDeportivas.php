<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrsCulturalesDeportivas extends Model
{
    protected $table = 'hrs_culturales_deportivas';
    protected $primaryKey = 'id_registro';
    public $timestamps = false;

    protected $fillable = ['id_alumno', 'tipo', 'horas_acumuladas', 'descripcion'];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }
}
