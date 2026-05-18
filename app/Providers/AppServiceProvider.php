<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Habilita asignar carreras a otros gestores escolares.
        // - Admin: siempre.
        // - Gestor con flag puede_asignar_carreras: si.
        // - Cualquier otro: no.
        Gate::define('asignar-carreras', function (User $user) {
            if ($user->hasRole('admin')) {
                return true;
            }
            return (bool) ($user->gestorEscolar?->puede_asignar_carreras ?? false);
        });
    }
}
