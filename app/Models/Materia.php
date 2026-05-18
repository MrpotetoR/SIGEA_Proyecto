<?php

namespace App\Models;

use App\Models\Concerns\FiltraPorCarrerasAsignadas;
use App\Models\Scopes\NivelEducativoScope;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use FiltraPorCarrerasAsignadas;

    protected $table = 'materia';
    protected $primaryKey = 'id_materia';
    public $timestamps = false;

    protected $fillable = [
        'id_carrera', 'id_plan_bachillerato',
        'nombre_materia', 'cuatrimestre',
        'horas_semana', 'nivel_educativo',
    ];

    public function planBachillerato()
    {
        return $this->belongsTo(BachilleratoPlan::class, 'id_plan_bachillerato');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new NivelEducativoScope());
    }

    public function scopeSinFiltroNivel($query)
    {
        return $query->withoutGlobalScope(NivelEducativoScope::class);
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_materia');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_materia');
    }
}
