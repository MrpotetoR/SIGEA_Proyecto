<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicloEscolar extends Model
{
    protected $table = 'ciclo_escolar';
    protected $primaryKey = 'id_ciclo';
    public $timestamps = false;

    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin'];

    /** Duración fija del ciclo escolar cuatrimestral: 3 años 4 meses (10 cuatrimestres × 4 meses). */
    public const DURACION_ANIOS = 3;
    public const DURACION_MESES = 4;

    /** Duración fija del ciclo escolar semestral: 3 años exactos (6 semestres × 6 meses). */
    public const DURACION_ANIOS_SEMESTRAL = 3;
    public const DURACION_MESES_SEMESTRAL = 0;

    /**
     * Bloques cuatrimestrales del año UDEA (3 cohortes/año):
     *   - A: ingresos de enero      (meses 1–4)
     *   - B: ingresos de mayo       (meses 5–8)
     *   - C: ingresos de septiembre (meses 9–12)
     */
    public const FECHAS_DEFAULT_POR_BLOQUE = [
        'A' => ['mes' => 1,  'dia' => 15],
        'B' => ['mes' => 5,  'dia' => 15],
        'C' => ['mes' => 9,  'dia' => 15],
    ];

    /**
     * Bloques semestrales del año UDEA (2 cohortes/año — área salud y psicología):
     *   - 1: ingresos de enero (1er semestre)
     *   - 2: ingresos de agosto (2° semestre)
     */
    public const FECHAS_DEFAULT_POR_BLOQUE_SEMESTRAL = [
        '1' => ['mes' => 1, 'dia' => 15],
        '2' => ['mes' => 8, 'dia' => 15],
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    /**
     * Calcula la fecha de fin a partir de la fecha de inicio según el tipo:
     *   - cuatrimestre: +3 años 4 meses
     *   - semestre    : +3 años exactos
     */
    public static function calcularFechaFin($fechaInicio, string $tipo = 'cuatrimestre'): \Carbon\Carbon
    {
        $base = \Carbon\Carbon::parse($fechaInicio);
        if ($tipo === 'semestre') {
            return $base->copy()
                ->addYears(self::DURACION_ANIOS_SEMESTRAL)
                ->addMonths(self::DURACION_MESES_SEMESTRAL);
        }
        return $base->copy()
            ->addYears(self::DURACION_ANIOS)
            ->addMonths(self::DURACION_MESES);
    }

    /** Devuelve el bloque cuatrimestral (A/B/C) según el mes de una fecha. */
    public static function bloqueDeMes(int $mes): string
    {
        if ($mes <= 4)  return 'A';
        if ($mes <= 8)  return 'B';
        return 'C';
    }

    /** Devuelve el bloque semestral (1/2) según el mes de una fecha. */
    public static function bloqueSemestralDeMes(int $mes): string
    {
        return $mes <= 7 ? '1' : '2';
    }

    /**
     * Genera el nombre estándar del ciclo según el tipo:
     *   - cuatrimestre: "2026A–2029A" (sufijo letra A/B/C)
     *   - semestre    : "2026-1–2029-1" (sufijo número -1/-2)
     */
    public static function generarNombre($fechaInicio, string $tipo = 'cuatrimestre'): string
    {
        $inicio = \Carbon\Carbon::parse($fechaInicio);
        $fin    = self::calcularFechaFin($inicio, $tipo);

        if ($tipo === 'semestre') {
            $bloqueIni = self::bloqueSemestralDeMes($inicio->month);
            $bloqueFin = self::bloqueSemestralDeMes($fin->month);
            return $inicio->year . '-' . $bloqueIni . '–' . $fin->year . '-' . $bloqueFin;
        }

        $bloqueIni = self::bloqueDeMes($inicio->month);
        $bloqueFin = self::bloqueDeMes($fin->month);
        return $inicio->year . $bloqueIni . '–' . $fin->year . $bloqueFin;
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

    // ═══════════════════════════════════════════════════════════════════
    //  Accessors operativos: dado que un "ciclo" modela una generación
    //  completa (3a4m o 3a), estos accessors permiten saber el periodo
    //  actual (cuatrimestre o semestre) dentro de la generación.
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Infiere el tipo de ciclo según la duración real (no requiere columna en BD):
     *   - 'cuatrimestre' si la duración es ~3 años 4 meses (1200+ días)
     *   - 'semestre'     si la duración es ~3 años exactos (~1095 días)
     */
    public function getTipoInferidoAttribute(): string
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) return 'cuatrimestre';
        $dias = $this->fecha_inicio->diffInDays($this->fecha_fin);
        // Punto medio entre 1095 (3a) y 1216 (3a4m) ≈ 1155
        return $dias < 1155 ? 'semestre' : 'cuatrimestre';
    }

    /** Total de periodos académicos en la generación (10 cuatri ó 6 sem). */
    public function getTotalPeriodosAttribute(): int
    {
        return $this->tipo_inferido === 'semestre' ? 6 : 10;
    }

    /** Duración en meses de cada periodo académico (4 cuatri ó 6 sem). */
    public function getMesesPorPeriodoAttribute(): int
    {
        return $this->tipo_inferido === 'semestre' ? 6 : 4;
    }

    /**
     * ¿En qué periodo está la generación actualmente?
     *   - Retorna 1..N donde N = total_periodos
     *   - Si la fecha actual está antes del inicio → 0 (futuro)
     *   - Si está después del fin → N+1 (egresado)
     */
    public function getPeriodoActualAttribute(): int
    {
        if (!$this->fecha_inicio) return 0;
        $ahora = now();
        if ($ahora->lt($this->fecha_inicio)) return 0;
        if ($ahora->gt($this->fecha_fin))   return $this->total_periodos + 1;

        $mesesTranscurridos = $this->fecha_inicio->diffInMonths($ahora);
        $periodo = intdiv((int) $mesesTranscurridos, $this->meses_por_periodo) + 1;
        return min($periodo, $this->total_periodos);
    }

    /** Fecha de fin del periodo académico actual (ej. el cuatri 2 termina el ...). */
    public function getFechaFinPeriodoActualAttribute(): ?\Carbon\Carbon
    {
        if (!$this->fecha_inicio || $this->periodo_actual < 1) return null;
        return $this->fecha_inicio->copy()->addMonths($this->periodo_actual * $this->meses_por_periodo);
    }
}
