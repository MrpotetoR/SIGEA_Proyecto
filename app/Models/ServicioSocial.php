<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioSocial extends Model
{
    protected $table = 'servicio_social';
    protected $primaryKey = 'id_servicio';
    public $timestamps = false;

    protected $fillable = ['id_alumno', 'horas_acumuladas', 'horas_requeridas', 'estatus'];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function getPorcentajeAttribute(): float
    {
        if ($this->horas_requeridas == 0) return 0;
        return round(($this->horas_acumuladas / $this->horas_requeridas) * 100, 1);
    }
}
