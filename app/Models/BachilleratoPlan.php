<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Plan de estudios de Bachillerato.
 *
 * Equivalente "logico" de Carrera para el nivel Bachillerato.
 * Una institucion tipicamente tiene 1 o 2 planes vigentes (BGU, BTI, etc.).
 */
class BachilleratoPlan extends Model
{
    protected $table = 'bachillerato_plan';
    protected $primaryKey = 'id_plan_bachillerato';

    /** Para route-model binding usamos el PK custom. */
    public function getRouteKeyName(): string
    {
        return 'id_plan_bachillerato';
    }

    protected $fillable = [
        'clave_plan', 'nombre_plan', 'num_semestres', 'tipo_periodo',
        'duracion_meses', 'vigente', 'descripcion',
    ];

    protected $casts = [
        'vigente' => 'boolean',
    ];

    /** Modalidades soportadas. */
    public const MODALIDAD_ESCOLARIZADO    = 'escolarizado';
    public const MODALIDAD_NO_ESCOLARIZADO = 'no_escolarizado';

    /**
     * Label del periodo en singular: "Semestre" o "Cuatrimestre".
     */
    public function getLabelPeriodoAttribute(): string
    {
        return $this->tipo_periodo === 'cuatrimestre' ? 'Cuatrimestre' : 'Semestre';
    }

    /**
     * Duración total del plan en meses. Respeta el valor almacenado;
     * si no hay override, lo calcula como num_periodos × meses-por-periodo
     * (semestre = 6 meses, cuatrimestre = 4 meses).
     */
    public function getDuracionMesesCalculadaAttribute(): int
    {
        if (!is_null($this->attributes['duracion_meses'] ?? null)) {
            return (int) $this->attributes['duracion_meses'];
        }
        $mesesPorPeriodo = $this->tipo_periodo === 'cuatrimestre' ? 4 : 6;
        return $this->num_semestres * $mesesPorPeriodo;
    }

    /**
     * Texto legible de duración: "3 años" o "18 meses".
     */
    public function getDuracionTextoAttribute(): string
    {
        $meses = $this->duracion_meses_calculada;
        if ($meses % 12 === 0) {
            $anios = (int) ($meses / 12);
            return $anios . ' ' . ($anios === 1 ? 'año' : 'años');
        }
        return $meses . ' meses';
    }

    public function materias()
    {
        return $this->hasMany(Materia::class, 'id_plan_bachillerato');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_plan_bachillerato');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_plan_bachillerato');
    }

    /** Solo planes vigentes. */
    public function scopeVigente($query)
    {
        return $query->where('vigente', true);
    }
}
