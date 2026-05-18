<?php

namespace App\Models\Scopes;

use App\Support\ContextoEducativo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Scope global que filtra los registros segun el contexto educativo activo
 * en sesion (universidad / bachillerato).
 *
 * Se aplica a los modelos Alumno, Docente, Grupo y Materia.
 *
 * Si no hay contexto en sesion (admin sin entrar al panel, jobs en consola,
 * API publica, etc.) el scope NO filtra — se devuelven todos los registros.
 * Esto evita romper migraciones, seeders y comandos artisan.
 *
 * Para bypasear deliberadamente el scope (ej. reportes consolidados de admin):
 *   Modelo::withoutGlobalScope(NivelEducativoScope::class)->get();
 *   Modelo::sinFiltroNivel()->get();   // si el modelo define este scope helper
 */
class NivelEducativoScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Si estamos en consola o no hay sesion, no aplicamos filtro.
        if (app()->runningInConsole()) {
            return;
        }

        $nivel = ContextoEducativo::actual();
        if (!$nivel) {
            return;
        }

        $builder->where(
            $model->getTable() . '.nivel_educativo',
            $nivel,
        );
    }
}
