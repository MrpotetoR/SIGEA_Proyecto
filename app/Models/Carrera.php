<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carrera';
    protected $primaryKey = 'id_carrera';
    public $timestamps = false;

    protected $fillable = ['nombre_carrera', 'clave_carrera', 'rvoe', 'area_academica', 'tipo_periodo', 'duracion_periodos'];

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

        static::created(function (Carrera $carrera) {
            // Notificar a todos los admins que hay una carrera nueva sin asignar.
            $admins = User::role('admin')->where('activo', true)->get();
            if ($admins->isEmpty()) return;

            app(\App\Services\NotificacionService::class)->enviarMasivo(
                $admins,
                'carrera_sin_asignar',
                'Nueva carrera sin personal asignado',
                "Se creó la carrera \"{$carrera->nombre_carrera}\" y aún no tiene personal de Servicios Escolares asignado.",
                [
                    'icono' => 'academic-cap',
                    'color' => 'amber',
                    'url'   => '/admin/asignaciones',
                ]
            );
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

    /**
     * Personal de Servicios Escolares que administra esta carrera.
     * Relación 1:1 (una carrera tiene a lo más un personal asignado).
     */
    public function personalAsignado()
    {
        return $this->belongsToMany(
            GestorEscolar::class,
            'personal_carrera',
            'id_carrera',
            'id_personal'
        )->withTimestamps();
    }

    public function personal()
    {
        return $this->personalAsignado()->first();
    }

    public function tieneAsignacion(): bool
    {
        return $this->personalAsignado()->exists();
    }

    /**
     * Scope que limita carreras a las asignadas al usuario autenticado.
     * - Admin: todas.
     * - Servicios Escolares: solo sus carreras asignadas.
     * - Otros: todas (otros paneles tienen sus propias autorizaciones).
     */
    public function scopeMisCarreras($query)
    {
        if (!auth()->check()) {
            return $query;
        }
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            return $query;
        }
        if (!$user->hasRole('gestor_escolar')) {
            return $query;
        }
        $ids = $user->carrerasAsignadasIds();
        if (empty($ids)) {
            return $query->whereRaw('1 = 0');
        }
        return $query->whereIn('id_carrera', $ids);
    }
}
