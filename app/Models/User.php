<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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

    public function noticias()
    {
        return $this->hasMany(Noticia::class, 'user_id');
    }

    public function chatbotSesiones()
    {
        return $this->hasMany(ChatbotSesion::class, 'user_id');
    }

    // Helper para redirigir según rol
    public function panelUrl(): string
    {
        if ($this->hasRole('servicios_escolares')) return '/servicios/dashboard';
        if ($this->hasRole('director_carrera')) return '/director/dashboard';
        if ($this->hasRole('docente')) return '/docente/dashboard';
        if ($this->hasRole('alumno')) return '/alumno/dashboard';
        return '/dashboard';
    }
}
