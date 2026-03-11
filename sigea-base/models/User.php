<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    // ─── Relaciones ────────────────────────────

    public function alumno()
    {
        return $this->hasOne(Alumno::class);
    }

    public function docente()
    {
        return $this->hasOne(Docente::class);
    }

    public function chatbotSesiones()
    {
        return $this->hasMany(ChatbotSesion::class);
    }

    // ─── Helpers ───────────────────────────────

    public function esAlumno(): bool
    {
        return $this->hasRole('alumno');
    }

    public function esDocente(): bool
    {
        return $this->hasRole('docente');
    }

    public function esDirector(): bool
    {
        return $this->hasRole('director_carrera');
    }

    public function esServiciosEscolares(): bool
    {
        return $this->hasRole('servicios_escolares');
    }

    /**
     * Retorna el panel/dashboard al que debe redirigirse después del login.
     */
    public function dashboardRoute(): string
    {
        return match (true) {
            $this->esServiciosEscolares() => 'admin.dashboard',
            $this->esDirector()           => 'director.dashboard',
            $this->esDocente()            => 'docente.dashboard',
            $this->esAlumno()             => 'alumno.dashboard',
            default                       => 'home',
        };
    }
}
