<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

class Alumno extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'carrera_id', 'matricula', 'nombre', 'apellidos',
        'cuatrimestre_actual', 'status', 'tutor_id',
    ];

    protected $casts = [
        'cuatrimestre_actual' => 'integer',
    ];

    // ─── Relaciones ────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    public function semaforoAcademico(): HasMany
    {
        return $this->hasMany(SemaforoAcademico::class);
    }

    public function hrsCulturales(): HasMany
    {
        return $this->hasMany(HrsCulturalDeportiva::class);
    }

    public function servicioSocial(): HasOne
    {
        return $this->hasOne(ServicioSocial::class);
    }

    public function evaluacionesDocente(): HasMany
    {
        return $this->hasMany(EvaluacionDocente::class);
    }

    public function constancias(): HasMany
    {
        return $this->hasMany(Constancia::class);
    }

    public function historialBajas(): HasMany
    {
        return $this->hasMany(HistorialBaja::class);
    }

    // ─── Scopes ────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('status', 'activo');
    }

    public function scopeDeCarrera($query, int $carreraId)
    {
        return $query->where('carrera_id', $carreraId);
    }

    public function scopeDeCuatrimestre($query, int $cuatrimestre)
    {
        return $query->where('cuatrimestre_actual', $cuatrimestre);
    }

    // ─── Accessors ─────────────────────────────

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function getPromedioGeneralAttribute(): ?float
    {
        $promedio = $this->calificaciones()
            ->whereNotNull('calificacion')
            ->avg('calificacion');

        return $promedio ? round($promedio, 2) : null;
    }
}
