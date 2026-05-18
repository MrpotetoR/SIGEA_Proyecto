<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Filtra docentes para que solo se vean aquellos que imparten al menos
 * una materia en una carrera asignada al usuario de Gestor Escolar.
 */
class DocenteCarrerasAsignadasScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return;
        }

        if (!$user->hasRole('gestor_escolar')) {
            return;
        }

        $ids = $user->carrerasAsignadasIds();

        if (empty($ids)) {
            $builder->whereRaw('1 = 0');
            return;
        }

        $builder->whereExists(function ($sub) use ($ids) {
            $sub->select(\DB::raw(1))
                ->from('docente_carrera as dc')
                ->whereColumn('dc.id_docente', 'docente.id_docente')
                ->whereIn('dc.id_carrera', $ids);
        });
    }
}
