<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carrera';
    protected $primaryKey = 'id_carrera';
    public $timestamps = false;

    protected $fillable = ['id_director', 'nombre_carrera', 'clave_carrera', 'area_academica', 'tipo_periodo', 'duracion_periodos'];

    public const AREAS_ACADEMICAS = [
        'ciencias_salud'       => 'Ciencias de la Salud',
        'ingenierias'          => 'Ingenierías y Tecnología',
        'negocios'             => 'Negocios y Administración',
        'ciencias_sociales'    => 'Ciencias Sociales y Jurídicas',
        'educacion'            => 'Educación y Humanidades',
        'arquitectura_diseno'  => 'Arquitectura y Diseño',
        'gastronomia'          => 'Gastronomía',
    ];

    public const TIPOS_PERIODO = [
        'cuatrimestre' => 'Cuatrimestre',
        'semestre'     => 'Semestre',
    ];

    /**
     * Total de periodos por tipo — reglas de negocio fijas del sistema.
     * Cuatrimestre: 10 periodos | Semestre: 7 periodos
     */
    public const DURACION_POR_TIPO = [
        'cuatrimestre' => 10,
        'semestre'     => 7,
    ];

    /** Máximo de periodos que puede tener un alumno en esta carrera. */
    public function getMaxPeriodosAttribute(): int
    {
        return self::DURACION_POR_TIPO[$this->tipo_periodo] ?? 10;
    }

    /** Etiqueta singular del periodo: "Cuatrimestre" o "Semestre". */
    public function getLabelPeriodoAttribute(): string
    {
        return self::TIPOS_PERIODO[$this->tipo_periodo] ?? 'Cuatrimestre';
    }

    public function getDuracionEstimadaAttribute(): string
    {
        $mesesPorPeriodo = $this->tipo_periodo === 'semestre' ? 6 : 4;
        $meses = $this->max_periodos * $mesesPorPeriodo;
        $anios = intdiv($meses, 12);
        $resto = $meses % 12;

        if ($anios && $resto) {
            return "{$anios} año" . ($anios > 1 ? 's' : '') . " y {$resto} mes" . ($resto > 1 ? 'es' : '');
        }
        return $anios ? "{$anios} año" . ($anios > 1 ? 's' : '') : "{$resto} mes" . ($resto > 1 ? 'es' : '');
    }

    protected static function booted(): void
    {
        static::creating(function (Carrera $carrera) {
            if (empty($carrera->clave_carrera) && !empty($carrera->nombre_carrera)) {
                $carrera->clave_carrera = self::generarClave($carrera->nombre_carrera);
            }
            // Forzar duracion_periodos según el tipo_periodo (regla fija del sistema)
            $carrera->duracion_periodos = self::DURACION_POR_TIPO[$carrera->tipo_periodo] ?? 10;
        });

        static::updating(function (Carrera $carrera) {
            // tipo_periodo es inmutable: revertir si intentan cambiarlo
            if ($carrera->isDirty('tipo_periodo')) {
                $carrera->tipo_periodo = $carrera->getOriginal('tipo_periodo');
            }
            // duracion_periodos siempre sincronizada con tipo_periodo
            $carrera->duracion_periodos = self::DURACION_POR_TIPO[$carrera->tipo_periodo] ?? 10;
        });
    }

    public static function generarClave(string $nombre): string
    {
        $stopWords = ['de', 'del', 'en', 'la', 'las', 'los', 'el', 'y', 'e', 'a'];
        $words = explode(' ', $nombre);
        $initials = '';

        foreach ($words as $word) {
            if (!in_array(mb_strtolower($word), $stopWords) && mb_strlen($word) > 1) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }
        }

        if (mb_strlen($initials) < 2) {
            $initials = mb_strtoupper(mb_substr(preg_replace('/\s+/', '', $nombre), 0, 3));
        }

        return $initials . '-' . date('Y');
    }

    public function director()
    {
        return $this->belongsTo(Docente::class, 'id_director', 'id_docente');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_carrera');
    }

    public function materias()
    {
        return $this->hasMany(Materia::class, 'id_carrera');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_carrera');
    }
}
