<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    // Relaciones
    public function alumno()
    {
        return $this->hasOne(Alumno::class, 'user_id');
    }

    public function docente()
    {
        return $this->hasOne(Docente::class, 'user_id');
    }

    public function gestorEscolar()
    {
        return $this->hasOne(GestorEscolar::class, 'user_id');
    }

    /**
     * Devuelve los IDs de carreras asignadas al Gestor Escolar.
     * - Admin: todas las carreras (acceso pleno).
     * - Gestor Escolar: solo las carreras vinculadas a su perfil.
     * - Otros roles: array vacío.
     */
    public function carrerasAsignadasIds(): array
    {
        if ($this->hasRole('admin')) {
            return Carrera::pluck('id_carrera')->all();
        }

        if ($this->hasRole('gestor_escolar')) {
            return $this->gestorEscolar?->carreras()
                ->pluck('carrera.id_carrera')->all() ?? [];
        }

        return [];
    }

    public function tieneTodasLasCarreras(): bool
    {
        return $this->hasRole('admin');
    }

    public function noticias()
    {
        return $this->hasMany(Noticia::class, 'user_id');
    }

    public function chatbotSesiones()
    {
        return $this->hasMany(ChatbotSesion::class, 'user_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'user_id');
    }

    public function notificacionesNoLeidas()
    {
        return $this->notificaciones()->noLeidas();
    }

    // Helper para redirigir según rol
    public function panelUrl(): string
    {
        if ($this->hasRole('admin')) {
            return '/admin/dashboard';
        }
        if ($this->hasRole('gestor_escolar')) {
            return '/gestor-escolar/dashboard';
        }
        if ($this->hasRole('docente')) {
            return '/docente/dashboard';
        }
        if ($this->hasRole('alumno')) {
            return '/alumno/dashboard';
        }

        return '/dashboard';
    }
}
