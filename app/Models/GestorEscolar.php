<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Gestor Escolar — rol unificado que fusiona las funciones de
 * Servicios Escolares y Director de Carrera.
 *
 * La tabla mantiene el PK `id_personal` por compatibilidad con las tablas
 * pivote `personal_carrera` y `documento_personal_se` (renombrar el PK
 * implicaría reconstruir todas las FKs y queda pendiente para otra fase).
 */
class GestorEscolar extends Model
{
    use SoftDeletes;

    protected $table = 'gestores_escolares';
    protected $primaryKey = 'id_personal';

    protected $fillable = [
        'user_id',
        'nombre',
        'apellidos',
        'num_cedula',
        'rfc',
        'especialidad',
        'puede_asignar_carreras',
    ];

    protected function casts(): array
    {
        return ['puede_asignar_carreras' => 'boolean'];
    }

    /** Máximo de carreras que un gestor escolar puede administrar. */
    public const MAX_CARRERAS = 4;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carreras()
    {
        return $this->belongsToMany(
            Carrera::class,
            'personal_carrera',
            'id_personal',
            'id_carrera'
        )->withTimestamps();
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoPersonalSE::class, 'id_personal');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function puedeAgregarCarrera(): bool
    {
        return $this->carreras()->count() < self::MAX_CARRERAS;
    }

    public function carrerasRestantes(): int
    {
        return max(0, self::MAX_CARRERAS - $this->carreras()->count());
    }
}
