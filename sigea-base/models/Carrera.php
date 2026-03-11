<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Carrera extends Model
{
    use HasFactory;

    protected $fillable = ['nombre_carrera', 'clave_carrera', 'director_id'];

    public function director(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'director_id');
    }

    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class);
    }

    public function materias(): HasMany
    {
        return $this->hasMany(Materia::class);
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class);
    }
}
