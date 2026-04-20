<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicloEscolar extends Model
{
    protected $table = 'ciclo_escolar';
    protected $primaryKey = 'id_ciclo';
    public $timestamps = false;

    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin'];

    /** Duración fija del ciclo escolar: 3 años 4 meses. */
    public const DURACION_ANIOS = 3;
    public const DURACION_MESES = 4;

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    /** Calcula la fecha de fin a partir de la fecha de inicio (+3 años 4 meses). */
    public static function calcularFechaFin($fechaInicio): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($fechaInicio)
            ->addYears(self::DURACION_ANIOS)
            ->addMonths(self::DURACION_MESES);
    }

    /** Genera el nombre estándar del ciclo como rango de años: "2024–2027". */
    public static function generarNombre($fechaInicio): string
    {
        $inicio = \Carbon\Carbon::parse($fechaInicio);
        $fin    = self::calcularFechaFin($inicio);
        return $inicio->year . '–' . $fin->year;
    }

    /** Rango de años para mostrar en vistas (ej. "2024–2027"). */
    public function getRangoAniosAttribute(): string
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) return '—';
        return $this->fecha_inicio->year . '–' . $this->fecha_fin->year;
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
