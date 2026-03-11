<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicloEscolar extends Model
{
    protected $table = 'ciclo_escolar';
    protected $primaryKey = 'id_ciclo';
    public $timestamps = false;

    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin'];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_ciclo');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_ciclo');
    }

    public function semaforosAcademicos()
    {
        return $this->hasMany(SemaforoAcademico::class, 'id_ciclo');
    }

    public function scopeActivo($query)
    {
        return $query->where('fecha_inicio', '<=', now())
                     ->where('fecha_fin', '>=', now());
    }

    public static function cicloActual(): ?self
    {
        return static::query()->activo()->first();
    }
}
