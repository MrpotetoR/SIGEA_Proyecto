<?php

namespace App\Models;

use App\Models\Concerns\FiltraPorCarrerasAsignadas;
use App\Models\Scopes\NivelEducativoScope;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use FiltraPorCarrerasAsignadas;

    protected $table = 'alumno';
    protected $primaryKey = 'id_alumno';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'id_carrera', 'id_plan_bachillerato',
        'id_tutor', 'id_alumno_publico',
        'nombre', 'apellidos', 'cuatrimestre_actual', 'estatus',
        'nivel_educativo',
    ];

    public function planBachillerato()
    {
        return $this->belongsTo(BachilleratoPlan::class, 'id_plan_bachillerato');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new NivelEducativoScope());
    }

    /** Bypass deliberado del scope por nivel (uso admin / reportes consolidados). */
    public function scopeSinFiltroNivel($query)
    {
        return $query->withoutGlobalScope(NivelEducativoScope::class);
    }

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

    public function padreTutor()
    {
        return $this->hasOne(PadreTutor::class, 'id_alumno');
    }

    public function pagosCuatrimestre()
    {
        return $this->hasMany(PagoCuatrimestre::class, 'id_alumno');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoAlumno::class, 'id_alumno');
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

    /**
     * Estado consolidado del pago del alumno:
     *  - 'pagado'   : tiene aprobados todos los cuatrimestres hasta el actual y no tiene pendientes
     *  - 'revision' : tiene al menos un váucher pendiente de revisión
     *  - 'sin_pago' : no tiene pendientes y le faltan aprobados
     */
    public function getPagoEstadoActualAttribute(): string
    {
        $pagos = $this->relationLoaded('pagosCuatrimestre')
            ? $this->pagosCuatrimestre
            : $this->pagosCuatrimestre()->get();

        if ($pagos->contains('estatus', 'pendiente')) {
            return 'revision';
        }

        $aprobados = $pagos->where('estatus', 'aprobado')->count();
        if ($aprobados >= $this->cuatrimestre_actual) {
            return 'pagado';
        }

        return 'sin_pago';
    }
}
