<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docente';
    protected $primaryKey = 'id_docente';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'nombre', 'apellidos',
        'especialidad', 'horas_contrato', 'es_tutor',
    ];

    protected function casts(): array
    {
        return ['es_tutor' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carrerasDirigidas()
    {
        return $this->hasMany(Carrera::class, 'id_director');
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_docente');
    }

    public function gruposTutoria()
    {
        return $this->hasMany(Grupo::class, 'id_tutor');
    }

    public function alumnosTutoria()
    {
        return $this->hasMany(Alumno::class, 'id_tutor');
    }

    public function evaluaciones()
    {
        return $this->hasMany(EvaluacionDocente::class, 'id_docente');
    }

    public function scopeConGruposActivos($query, $cicloId)
    {
        return $query->whereHas('horarios.grupo', fn($q) => $q->where('id_ciclo', $cicloId));
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function getPromedioEvaluacionAttribute(): float
    {
        return round($this->evaluaciones()->avg('calificacion_promedio') ?? 0, 2);
    }
}
