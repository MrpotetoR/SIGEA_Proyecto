<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumno';
    protected $primaryKey = 'id_alumno';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'id_carrera', 'id_tutor', 'matricula',
        'nombre', 'apellidos', 'cuatrimestre_actual', 'estatus',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    public function tutor()
    {
        return $this->belongsTo(Docente::class, 'id_tutor', 'id_docente');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_alumno');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_alumno');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_alumno');
    }

    public function semaforosAcademicos()
    {
        return $this->hasMany(SemaforoAcademico::class, 'id_alumno');
    }

    public function hrsCulturales()
    {
        return $this->hasMany(HrsCulturalesDeportivas::class, 'id_alumno');
    }

    public function servicioSocial()
    {
        return $this->hasOne(ServicioSocial::class, 'id_alumno');
    }

    public function evaluacionesDocente()
    {
        return $this->hasMany(EvaluacionDocente::class, 'id_alumno');
    }

    public function constancias()
    {
        return $this->hasMany(Constancia::class, 'id_alumno');
    }

    public function historialBajas()
    {
        return $this->hasMany(HistorialBaja::class, 'id_alumno');
    }

    public function scopeActivos($query)
    {
        return $query->where('estatus', 'activo');
    }

    public function scopeDeCarrera($query, $carreraId)
    {
        return $query->where('id_carrera', $carreraId);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function getPromedioGeneralAttribute(): float
    {
        return round($this->calificaciones()->avg('calificacion') ?? 0, 2);
    }

    public function getNivelSemaforoAttribute(): string
    {
        $ciclo = CicloEscolar::cicloActual();
        if (!$ciclo) return 'verde';
        $semaforo = $this->semaforosAcademicos()->where('id_ciclo', $ciclo->id_ciclo)->first();
        return $semaforo?->nivel ?? 'verde';
    }
}
