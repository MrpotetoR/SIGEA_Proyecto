<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Docente extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nombre', 'apellidos', 'especialidad', 'horas_contrato', 'es_titular',
    ];

    protected $casts = [
        'es_titular' => 'boolean',
    ];

    // ─── Relaciones ────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(EvaluacionDocente::class);
    }

    public function gruposTutorados(): HasMany
    {
        return $this->hasMany(Grupo::class, 'tutor_docente_id');
    }

    public function carrerasDirigidas(): HasMany
    {
        return $this->hasMany(Carrera::class, 'director_id');
    }

    // ─── Accessors ─────────────────────────────

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function getPromedioEvaluacionAttribute(): ?float
    {
        $promedio = $this->evaluaciones()->avg('calificacion_promedio');
        return $promedio ? round($promedio, 2) : null;
    }

    /**
     * Grupos donde da clase en el ciclo activo.
     */
    public function gruposDelCicloActivo()
    {
        return $this->horarios()
            ->with('grupo')
            ->whereHas('grupo.ciclo', fn ($q) => $q->where('activo', true))
            ->get()
            ->pluck('grupo')
            ->unique('id');
    }
}
